<?php
/**
 * Front-end output of the floating button.
 *
 * @package Floating_Call_Button
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tavoos_FCB_Frontend {

	/**
	 * Cached display decision for the current request.
	 *
	 * @var bool|null
	 */
	protected static $display = null;

	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue' ) );
		add_action( 'wp_footer', array( __CLASS__, 'render' ), 99 );
	}

	/**
	 * Whether the button is shown on the current page.
	 *
	 * @return bool
	 */
	public static function should_display() {
		if ( null !== self::$display ) {
			return self::$display;
		}

		$s    = Tavoos_FCB_Options::get();
		$show = ! empty( $s['enabled'] ) && ( ! empty( $s['show_desktop'] ) || ! empty( $s['show_mobile'] ) ) && self::get_channels( $s );

		if ( $show && 'all' !== $s['display_mode'] ) {
			$match = self::matches_target( $s );
			$show  = ( 'include' === $s['display_mode'] ) ? $match : ! $match;
		}

		/**
		 * Filters whether the button is shown on the current page.
		 *
		 * @param bool  $show Whether to show the button.
		 * @param array $s    Settings.
		 */
		self::$display = (bool) apply_filters( 'tavoos_fcb_should_display', $show, $s );

		return self::$display;
	}

	/**
	 * Whether the current page is one of the selected pages.
	 *
	 * @param array $s Settings.
	 * @return bool
	 */
	protected static function matches_target( $s ) {
		if ( ! empty( $s['front_page'] ) && is_front_page() ) {
			return true;
		}

		$ids = array_unique( array_merge( array_map( 'absint', (array) $s['pages'] ), Tavoos_FCB_Options::parse_ids( $s['post_ids'] ) ) );
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
	 * Enabled channels that have a value, with their final link.
	 *
	 * @param array $s Settings.
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
		return apply_filters( 'tavoos_fcb_channels', $list, $s );
	}

	/**
	 * Build a channel's link from its type and value.
	 *
	 * @param array $c Channel.
	 * @return string
	 */
	public static function build_url( $c ) {
		$value = trim( Tavoos_FCB_Options::latin_digits( isset( $c['value'] ) ? $c['value'] : '' ) );
		if ( '' === $value ) {
			return '';
		}

		$msg  = isset( $c['message'] ) ? trim( $c['message'] ) : '';
		$type = isset( $c['type'] ) ? $c['type'] : 'custom';
		$user = ltrim( $value, '@/' );

		// A full link entered by the user is used as is.
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
					// For example example.com or www.example.com.
					$url = 'https://' . $user;
			}
		}

		return (string) apply_filters( 'tavoos_fcb_channel_url', $url, $c );
	}

	/**
	 * Protocols allowed in channel links.
	 *
	 * @return string[]
	 */
	protected static function protocols() {
		return array_unique( array_merge( wp_allowed_protocols(), array( 'tel', 'sms', 'mailto', 'tg', 'whatsapp', 'viber', 'skype', 'geo', 'intent' ) ) );
	}

	/**
	 * Enqueue CSS and JS only where the button is shown.
	 */
	public static function enqueue() {
		if ( ! self::should_display() ) {
			return;
		}

		wp_enqueue_style( 'tavoos-fcb-frontend', TAVOOS_FCB_URL . 'assets/css/frontend.css', array(), TAVOOS_FCB_VERSION );
		wp_add_inline_style( 'tavoos-fcb-frontend', self::dynamic_css( Tavoos_FCB_Options::get() ) );

		wp_enqueue_script(
			'tavoos-fcb-frontend',
			TAVOOS_FCB_URL . 'assets/js/frontend.js',
			array(),
			TAVOOS_FCB_VERSION,
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);
	}

	/**
	 * Dynamic CSS built from the settings.
	 *
	 * @param array $s Settings.
	 * @return string
	 */
	protected static function dynamic_css( $s ) {
		$bg2  = $s['bg_color2'] ? $s['bg_color2'] : $s['bg_color'];
		$vars = array(
			'--tavoos-fcb-z'        => (int) $s['z_index'],
			'--tavoos-fcb-ox'       => $s['offset_x'] . 'px',
			'--tavoos-fcb-oy'       => $s['offset_y'] . 'px',
			'--tavoos-fcb-mox'      => $s['mobile_offset_x'] . 'px',
			'--tavoos-fcb-moy'      => $s['mobile_offset_y'] . 'px',
			'--tavoos-fcb-cx'       => $s['custom_x'] . '%',
			'--tavoos-fcb-cy'       => $s['custom_y'] . '%',
			'--tavoos-fcb-size'     => $s['size'] . 'px',
			'--tavoos-fcb-msize'    => $s['mobile_size'] . 'px',
			'--tavoos-fcb-bg'       => $s['bg_color'],
			'--tavoos-fcb-bg2'      => $bg2,
			'--tavoos-fcb-color'    => $s['icon_color'],
			'--tavoos-fcb-card-bg'  => $s['card_bg'],
			'--tavoos-fcb-card-txt' => $s['card_text'],
			'--tavoos-fcb-card-sub' => $s['card_subtext'],
			'--tavoos-fcb-card-bd'  => $s['card_border'],
		);

		$css = '#tavoos-fcb{';
		foreach ( $vars as $name => $value ) {
			$css .= $name . ':' . $value . ';';
		}
		$css .= '}';

		$bp = (int) $s['mobile_breakpoint'];
		if ( $bp > 0 ) {
			$css .= '@media (max-width:' . $bp . 'px){#tavoos-fcb{--tavoos-fcb-x:var(--tavoos-fcb-mox);--tavoos-fcb-y:var(--tavoos-fcb-moy);--tavoos-fcb-s:var(--tavoos-fcb-msize)}#tavoos-fcb.tavoos-fcb--hide-mobile{display:none!important}}';
			$css .= '@media (min-width:' . ( $bp + 1 ) . 'px){#tavoos-fcb.tavoos-fcb--hide-desktop{display:none!important}}';
		} else {
			// Without a breakpoint every device counts as desktop.
			$css .= '#tavoos-fcb.tavoos-fcb--hide-desktop{display:none!important}';
		}

		return $css;
	}

	/**
	 * Direction in which the menu opens, and its alignment.
	 *
	 * @param array $s Settings.
	 * @return array{0: string, 1: string} [direction, alignment]
	 */
	protected static function menu_layout( $s ) {
		$pos = $s['position'];

		if ( 'custom' === $pos ) {
			$x = (float) $s['custom_x'];
			$y = (float) $s['custom_y'];
			if ( $y > 35 && $y < 65 ) {
				// Near the vertical middle: open the menu beside the button.
				return array( $x > 50 ? 'left' : 'right', 'center' );
			}
			return array( $y <= 35 ? 'down' : 'up', $x > 66 ? 'right' : ( $x < 34 ? 'left' : 'center' ) );
		}

		list( $v, $h ) = explode( '-', $pos );

		if ( 'middle' === $v ) {
			// Open beside the button: a right-hand button gets the menu on its left.
			return array( 'right' === $h ? 'left' : 'right', 'center' );
		}

		return array( 'top' === $v ? 'down' : 'up', $h );
	}

	/**
	 * Print the button markup.
	 */
	public static function render() {
		if ( ! self::should_display() ) {
			return;
		}

		$s        = Tavoos_FCB_Options::get();
		$channels = self::get_channels( $s );
		$dir      = 'auto' === $s['direction'] ? ( is_rtl() ? 'rtl' : 'ltr' ) : $s['direction'];

		list( $open, $align ) = self::menu_layout( $s );

		$classes = array(
			'tavoos-fcb',
			'tavoos-fcb--pos-' . $s['position'],
			'tavoos-fcb--open-' . $open,
			'tavoos-fcb--align-' . $align,
		);
		if ( ! empty( $s['pulse'] ) ) {
			$classes[] = 'tavoos-fcb--pulse';
		}
		if ( empty( $s['show_mobile'] ) ) {
			$classes[] = 'tavoos-fcb--hide-mobile';
		}
		if ( empty( $s['show_desktop'] ) ) {
			$classes[] = 'tavoos-fcb--hide-desktop';
		}
		if ( 'svg' !== $s['icon_type'] && 'image' !== $s['icon_type'] ) {
			$classes[] = 'tavoos-fcb--preset-icon';
		}

		$direct = ! empty( $s['single_direct'] ) && 1 === count( $channels );
		$label  = $s['aria_label'] ? $s['aria_label'] : __( 'Contact us', 'floating-call-button' );
		$icon   = tavoos_fcb_render_icon( $s );
		?>
		<div id="tavoos-fcb" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" dir="<?php echo esc_attr( $dir ); ?>">
			<?php if ( $direct ) : ?>
				<?php $c = $channels[0]; ?>
				<a class="tavoos-fcb__btn" href="<?php echo esc_url( $c['url'], self::protocols() ); ?>" aria-label="<?php echo esc_attr( $c['title'] ? $c['title'] : $label ); ?>"<?php echo $c['new_tab'] ? ' target="_blank" rel="nofollow noopener"' : ''; ?>>
					<span class="tavoos-fcb__i1"><?php tavoos_fcb_echo_icon( $icon ); ?></span>
				</a>
			<?php else : ?>
				<div class="tavoos-fcb__menu" id="tavoos-fcb-menu" hidden>
					<?php
					foreach ( $channels as $c ) {
						self::render_channel( $c );
					}
					?>
				</div>
				<button type="button" class="tavoos-fcb__btn tavoos-fcb__toggle" aria-label="<?php echo esc_attr( $label ); ?>" aria-expanded="false" aria-controls="tavoos-fcb-menu">
					<span class="tavoos-fcb__i1"><?php tavoos_fcb_echo_icon( $icon ); ?></span>
					<span class="tavoos-fcb__i2"><?php tavoos_fcb_echo_icon( tavoos_fcb_preset_icon_svg( 'close' ) ); ?></span>
				</button>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Print one menu item.
	 *
	 * @param array $c Channel.
	 */
	protected static function render_channel( $c ) {
		$style = sprintf( '--tavoos-fcb-ic-bg:%s;--tavoos-fcb-ic-color:%s', $c['icon_bg'], $c['icon_color'] );
		?>
		<a class="tavoos-fcb__opt tavoos-fcb__opt--<?php echo esc_attr( $c['type'] ); ?>" href="<?php echo esc_url( $c['url'], self::protocols() ); ?>"<?php echo $c['new_tab'] ? ' target="_blank" rel="nofollow noopener"' : ''; ?>>
			<span class="tavoos-fcb__ic" style="<?php echo esc_attr( $style ); ?>"><?php tavoos_fcb_echo_icon( tavoos_fcb_render_icon( $c ) ); ?></span>
			<span class="tavoos-fcb__txt">
				<b><?php echo esc_html( $c['title'] ); ?></b>
				<?php if ( $c['subtitle'] ) : ?>
					<small><?php echo esc_html( $c['subtitle'] ); ?></small>
				<?php endif; ?>
			</span>
		</a>
		<?php
	}
}
