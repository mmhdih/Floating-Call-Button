<?php
/**
 * نمایش دکمه شناور در سایت.
 *
 * @package Floating_Call_Button
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FCB_Frontend {

	/**
	 * نتیجه بررسی نمایش (کش در طول درخواست).
	 *
	 * @var bool|null
	 */
	protected static $display = null;

	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue' ) );
		add_action( 'wp_footer', array( __CLASS__, 'render' ), 99 );
	}

	/**
	 * آیا دکمه در صفحه فعلی نمایش داده شود؟
	 *
	 * @return bool
	 */
	public static function should_display() {
		if ( null !== self::$display ) {
			return self::$display;
		}

		$s    = FCB_Options::get();
		$show = ! empty( $s['enabled'] ) && ( ! empty( $s['show_desktop'] ) || ! empty( $s['show_mobile'] ) ) && self::get_channels( $s );

		if ( $show && 'all' !== $s['display_mode'] ) {
			$match = self::matches_target( $s );
			$show  = ( 'include' === $s['display_mode'] ) ? $match : ! $match;
		}

		/**
		 * فیلتر نهایی نمایش دکمه.
		 *
		 * @param bool  $show نمایش داده شود یا نه.
		 * @param array $s    تنظیمات.
		 */
		self::$display = (bool) apply_filters( 'fcb_should_display', $show, $s );

		return self::$display;
	}

	/**
	 * آیا صفحه فعلی جزو صفحات انتخاب‌شده است؟
	 *
	 * @param array $s تنظیمات.
	 * @return bool
	 */
	protected static function matches_target( $s ) {
		if ( ! empty( $s['front_page'] ) && is_front_page() ) {
			return true;
		}

		$ids = array_unique( array_merge( array_map( 'absint', (array) $s['pages'] ), FCB_Options::parse_ids( $s['post_ids'] ) ) );
		if ( ! $ids ) {
			return false;
		}

		$current = 0;
		if ( function_exists( 'is_shop' ) && is_shop() && function_exists( 'wc_get_page_id' ) ) {
			$current = (int) wc_get_page_id( 'shop' );
		} elseif ( is_singular() || ( is_home() && ! is_front_page() ) ) {
			$current = (int) get_queried_object_id();
		} elseif ( is_front_page() && 'page' === get_option( 'show_on_front' ) ) {
			$current = (int) get_option( 'page_on_front' );
		}

		return $current && in_array( $current, $ids, true );
	}

	/**
	 * کانال‌های فعال و معتبر همراه با لینک نهایی.
	 *
	 * @param array $s تنظیمات.
	 * @return array
	 */
	public static function get_channels( $s ) {
		$list = array();
		foreach ( (array) $s['channels'] as $channel ) {
			if ( empty( $channel['enabled'] ) ) {
				continue;
			}
			$url = self::build_url( $channel );
			if ( '' === $url ) {
				continue;
			}
			$channel['url'] = $url;
			$list[]         = $channel;
		}
		return apply_filters( 'fcb_channels', $list, $s );
	}

	/**
	 * ساخت لینک هر کانال بر اساس نوع آن.
	 *
	 * @param array $c کانال.
	 * @return string
	 */
	public static function build_url( $c ) {
		$value = trim( FCB_Options::latin_digits( isset( $c['value'] ) ? $c['value'] : '' ) );
		if ( '' === $value ) {
			return '';
		}

		$msg  = isset( $c['message'] ) ? trim( $c['message'] ) : '';
		$type = isset( $c['type'] ) ? $c['type'] : 'custom';
		$user = ltrim( $value, '@/' );

		// اگر کاربر خودش لینک کامل وارد کرده، همان را استفاده کن.
		if ( preg_match( '#^[a-z][a-z0-9+.\-]*:#i', $value ) ) {
			$url = $value;
		} else {
			switch ( $type ) {
				case 'phone':
					$url = 'tel:' . preg_replace( '/[^0-9+]/', '', $value );
					break;
				case 'sms':
					$url = 'sms:' . preg_replace( '/[^0-9+]/', '', $value ) . ( $msg ? '?body=' . rawurlencode( $msg ) : '' );
					break;
				case 'whatsapp':
					$number = preg_replace( '/\D/', '', $value );
					$number = preg_replace( '/^00/', '', $number );
					$url    = 'https://wa.me/' . $number . ( $msg ? '?text=' . rawurlencode( $msg ) : '' );
					break;
				case 'email':
					$url = 'mailto:' . $value . ( $msg ? '?subject=' . rawurlencode( $msg ) : '' );
					break;
				case 'telegram':
					$url = 'https://t.me/' . $user;
					break;
				case 'instagram':
					$url = 'https://instagram.com/' . $user;
					break;
				case 'eitaa':
					$url = 'https://eitaa.com/' . $user;
					break;
				case 'bale':
					$url = 'https://ble.ir/' . $user;
					break;
				case 'rubika':
					$url = 'https://rubika.ir/' . $user;
					break;
				case 'linkedin':
					$url = 'https://www.linkedin.com/in/' . $user;
					break;
				default:
					// مثل example.com یا www.example.com.
					$url = 'https://' . $user;
			}
		}

		return (string) apply_filters( 'fcb_channel_url', $url, $c );
	}

	/**
	 * پروتکل‌های مجاز در لینک‌ها.
	 *
	 * @return string[]
	 */
	protected static function protocols() {
		return array_unique( array_merge( wp_allowed_protocols(), array( 'tel', 'sms', 'mailto', 'tg', 'whatsapp', 'viber', 'skype', 'geo', 'intent' ) ) );
	}

	/**
	 * بارگذاری CSS و JS فقط در صورت نمایش.
	 */
	public static function enqueue() {
		if ( ! self::should_display() ) {
			return;
		}

		wp_enqueue_style( 'fcb-frontend', FCB_URL . 'assets/css/frontend.css', array(), FCB_VERSION );
		wp_add_inline_style( 'fcb-frontend', self::dynamic_css( FCB_Options::get() ) );

		wp_enqueue_script( 'fcb-frontend', FCB_URL . 'assets/js/frontend.js', array(), FCB_VERSION, true );
	}

	/**
	 * CSS پویا بر اساس تنظیمات.
	 *
	 * @param array $s تنظیمات.
	 * @return string
	 */
	protected static function dynamic_css( $s ) {
		$bg2  = $s['bg_color2'] ? $s['bg_color2'] : $s['bg_color'];
		$vars = array(
			'--fcb-z'        => (int) $s['z_index'],
			'--fcb-ox'       => $s['offset_x'] . 'px',
			'--fcb-oy'       => $s['offset_y'] . 'px',
			'--fcb-mox'      => $s['mobile_offset_x'] . 'px',
			'--fcb-moy'      => $s['mobile_offset_y'] . 'px',
			'--fcb-cx'       => $s['custom_x'] . '%',
			'--fcb-cy'       => $s['custom_y'] . '%',
			'--fcb-size'     => $s['size'] . 'px',
			'--fcb-msize'    => $s['mobile_size'] . 'px',
			'--fcb-bg'       => $s['bg_color'],
			'--fcb-bg2'      => $bg2,
			'--fcb-color'    => $s['icon_color'],
			'--fcb-card-bg'  => $s['card_bg'],
			'--fcb-card-txt' => $s['card_text'],
			'--fcb-card-sub' => $s['card_subtext'],
			'--fcb-card-bd'  => $s['card_border'],
		);

		$css = '#fcb{';
		foreach ( $vars as $name => $value ) {
			$css .= $name . ':' . $value . ';';
		}
		$css .= '}';

		$bp = (int) $s['mobile_breakpoint'];
		if ( $bp > 0 ) {
			$css .= '@media (max-width:' . $bp . 'px){#fcb{--fcb-x:var(--fcb-mox);--fcb-y:var(--fcb-moy);--fcb-s:var(--fcb-msize)}#fcb.fcb--hide-mobile{display:none!important}}';
			$css .= '@media (min-width:' . ( $bp + 1 ) . 'px){#fcb.fcb--hide-desktop{display:none!important}}';
		} else {
			// بدون نقطه شکست، همه دستگاه‌ها «دسکتاپ» حساب می‌شوند.
			$css .= '#fcb.fcb--hide-desktop{display:none!important}';
		}

		return $css;
	}

	/**
	 * محاسبه جهت باز شدن منو و تراز آن.
	 *
	 * @param array $s تنظیمات.
	 * @return array{0: string, 1: string} [جهت, تراز]
	 */
	protected static function menu_layout( $s ) {
		$pos = $s['position'];

		if ( 'custom' === $pos ) {
			$x = (float) $s['custom_x'];
			$y = (float) $s['custom_y'];
			if ( $y > 35 && $y < 65 ) {
				// نزدیک وسط صفحه: منو کنار دکمه باز شود.
				return array( $x > 50 ? 'left' : 'right', 'center' );
			}
			return array( $y <= 35 ? 'down' : 'up', $x > 66 ? 'right' : ( $x < 34 ? 'left' : 'center' ) );
		}

		list( $v, $h ) = explode( '-', $pos );

		if ( 'middle' === $v ) {
			// کنار دکمه باز شود: دکمه راست ← منو سمت چپش.
			return array( 'right' === $h ? 'left' : 'right', 'center' );
		}

		return array( 'top' === $v ? 'down' : 'up', $h );
	}

	/**
	 * چاپ HTML دکمه.
	 */
	public static function render() {
		if ( ! self::should_display() ) {
			return;
		}

		$s        = FCB_Options::get();
		$channels = self::get_channels( $s );
		$dir      = 'auto' === $s['direction'] ? ( is_rtl() ? 'rtl' : 'ltr' ) : $s['direction'];

		list( $open, $align ) = self::menu_layout( $s );

		$classes = array(
			'fcb',
			'fcb--pos-' . $s['position'],
			'fcb--open-' . $open,
			'fcb--align-' . $align,
		);
		if ( ! empty( $s['pulse'] ) ) {
			$classes[] = 'fcb--pulse';
		}
		if ( empty( $s['show_mobile'] ) ) {
			$classes[] = 'fcb--hide-mobile';
		}
		if ( empty( $s['show_desktop'] ) ) {
			$classes[] = 'fcb--hide-desktop';
		}
		if ( 'svg' !== $s['icon_type'] && 'image' !== $s['icon_type'] ) {
			$classes[] = 'fcb--preset-icon';
		}

		$direct = ! empty( $s['single_direct'] ) && 1 === count( $channels );
		$label  = $s['aria_label'] ? $s['aria_label'] : 'تماس با ما';
		$icon   = '<span class="fcb__i1">' . fcb_render_icon( $s ) . '</span>';

		echo "\n<!-- Floating Call Button by Tavoos Web - https://tavoosweb.ir/ -->\n";
		echo '<div id="fcb" class="' . esc_attr( implode( ' ', $classes ) ) . '" dir="' . esc_attr( $dir ) . '">';

		if ( $direct ) {
			$c = $channels[0];
			printf(
				'<a class="fcb__btn" href="%1$s" aria-label="%2$s"%3$s>%4$s</a>',
				esc_url( $c['url'], self::protocols() ),
				esc_attr( $c['title'] ? $c['title'] : $label ),
				$c['new_tab'] ? ' target="_blank" rel="nofollow noopener"' : '',
				$icon // phpcs:ignore WordPress.Security.EscapeOutput -- پاک‌سازی شده در fcb_render_icon.
			);
		} else {
			echo '<div class="fcb__menu" id="fcb-menu" hidden>';
			foreach ( $channels as $c ) {
				self::render_channel( $c );
			}
			echo '</div>';

			printf(
				'<button type="button" class="fcb__btn fcb__toggle" aria-label="%1$s" aria-expanded="false" aria-controls="fcb-menu">%2$s<span class="fcb__i2">%3$s</span></button>',
				esc_attr( $label ),
				$icon, // phpcs:ignore WordPress.Security.EscapeOutput
				fcb_preset_icon_svg( 'close' ) // phpcs:ignore WordPress.Security.EscapeOutput
			);
		}

		echo "</div>\n";
	}

	/**
	 * چاپ یک گزینه منو.
	 *
	 * @param array $c کانال.
	 */
	protected static function render_channel( $c ) {
		$style = sprintf( '--fcb-ic-bg:%s;--fcb-ic-color:%s', $c['icon_bg'], $c['icon_color'] );

		printf(
			'<a class="fcb__opt fcb__opt--%1$s" href="%2$s"%3$s><span class="fcb__ic" style="%4$s">%5$s</span><span class="fcb__txt"><b>%6$s</b>%7$s</span></a>',
			esc_attr( $c['type'] ),
			esc_url( $c['url'], self::protocols() ),
			$c['new_tab'] ? ' target="_blank" rel="nofollow noopener"' : '',
			esc_attr( $style ),
			fcb_render_icon( $c ), // phpcs:ignore WordPress.Security.EscapeOutput
			esc_html( $c['title'] ),
			$c['subtitle'] ? '<small>' . esc_html( $c['subtitle'] ) . '</small>' : ''
		);
	}
}
