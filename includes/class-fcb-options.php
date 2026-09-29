<?php
/**
 * مدیریت تنظیمات افزونه: مقادیر پیش‌فرض، خواندن و پاک‌سازی.
 *
 * @package Floating_Call_Button
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FCB_Options {

	/**
	 * موقعیت‌های آماده دکمه.
	 *
	 * @return array<string, string>
	 */
	public static function positions() {
		return array(
			'bottom-right'  => 'پایین راست',
			'bottom-left'   => 'پایین چپ',
			'bottom-center' => 'پایین وسط',
			'middle-right'  => 'وسط راست',
			'middle-left'   => 'وسط چپ',
			'top-right'     => 'بالا راست',
			'top-left'      => 'بالا چپ',
			'top-center'    => 'بالا وسط',
			'custom'        => 'دلخواه (درصدی)',
		);
	}

	/**
	 * انواع کانال‌های ارتباطی و مقادیر پیش‌فرض هر کدام.
	 *
	 * با فیلتر `fcb_channel_types` قابل گسترش است.
	 *
	 * @return array<string, array>
	 */
	public static function channel_types() {
		return apply_filters(
			'fcb_channel_types',
			array(
				'phone'     => array(
					'label'       => 'تماس تلفنی',
					'icon'        => 'phone',
					'bg'          => '#FFF1C2',
					'color'       => '#2B2118',
					'placeholder' => '+989121234567',
					'hint'        => 'شماره تلفن (ترجیحاً با کد کشور).',
					'message_label' => '',
					'new_tab'     => 0,
				),
				'whatsapp'  => array(
					'label'       => 'پیام در واتساپ',
					'icon'        => 'whatsapp',
					'bg'          => '#25D366',
					'color'       => '#FFFFFF',
					'placeholder' => '989121234567',
					'hint'        => 'شماره واتساپ با کد کشور و بدون + و صفر اول (مثل 989121234567).',
					'message_label' => 'متن پیش‌فرض پیام',
					'new_tab'     => 1,
				),
				'telegram'  => array(
					'label'       => 'تلگرام',
					'icon'        => 'telegram',
					'bg'          => '#229ED9',
					'color'       => '#FFFFFF',
					'placeholder' => 'username',
					'hint'        => 'نام کاربری تلگرام (بدون @) یا لینک کامل.',
					'message_label' => '',
					'new_tab'     => 1,
				),
				'instagram' => array(
					'label'       => 'اینستاگرام',
					'icon'        => 'instagram',
					'bg'          => '#E1306C',
					'color'       => '#FFFFFF',
					'placeholder' => 'username',
					'hint'        => 'نام کاربری اینستاگرام یا لینک کامل.',
					'message_label' => '',
					'new_tab'     => 1,
				),
				'email'     => array(
					'label'       => 'ایمیل',
					'icon'        => 'email',
					'bg'          => '#EA4335',
					'color'       => '#FFFFFF',
					'placeholder' => 'info@example.com',
					'hint'        => 'آدرس ایمیل.',
					'message_label' => 'موضوع پیش‌فرض ایمیل',
					'new_tab'     => 0,
				),
				'sms'       => array(
					'label'       => 'پیامک',
					'icon'        => 'sms',
					'bg'          => '#4CAF50',
					'color'       => '#FFFFFF',
					'placeholder' => '+989121234567',
					'hint'        => 'شماره دریافت پیامک.',
					'message_label' => 'متن پیش‌فرض پیامک',
					'new_tab'     => 0,
				),
				'eitaa'     => array(
					'label'       => 'ایتا',
					'icon'        => 'eitaa',
					'bg'          => '#EE7D23',
					'color'       => '#FFFFFF',
					'placeholder' => 'username',
					'hint'        => 'نام کاربری ایتا یا لینک کامل.',
					'message_label' => '',
					'new_tab'     => 1,
				),
				'bale'      => array(
					'label'       => 'بله',
					'icon'        => 'bale',
					'bg'          => '#35A99A',
					'color'       => '#FFFFFF',
					'placeholder' => 'username',
					'hint'        => 'نام کاربری بله یا لینک کامل.',
					'message_label' => '',
					'new_tab'     => 1,
				),
				'rubika'    => array(
					'label'       => 'روبیکا',
					'icon'        => 'rubika',
					'bg'          => '#F4F1FA',
					'color'       => '#6A3FA0',
					'placeholder' => 'username',
					'hint'        => 'نام کاربری روبیکا یا لینک کامل.',
					'message_label' => '',
					'new_tab'     => 1,
				),
				'linkedin'  => array(
					'label'       => 'لینکدین',
					'icon'        => 'linkedin',
					'bg'          => '#0A66C2',
					'color'       => '#FFFFFF',
					'placeholder' => 'https://www.linkedin.com/company/...',
					'hint'        => 'لینک کامل صفحه لینکدین.',
					'message_label' => '',
					'new_tab'     => 1,
				),
				'location'  => array(
					'label'       => 'آدرس روی نقشه',
					'icon'        => 'location',
					'bg'          => '#34A853',
					'color'       => '#FFFFFF',
					'placeholder' => 'https://maps.google.com/...',
					'hint'        => 'لینک گوگل‌مپ، نشان یا بلد.',
					'message_label' => '',
					'new_tab'     => 1,
				),
				'custom'    => array(
					'label'       => 'لینک دلخواه',
					'icon'        => 'link',
					'bg'          => '#607D8B',
					'color'       => '#FFFFFF',
					'placeholder' => 'https://example.com',
					'hint'        => 'هر لینکی (https:، tel:، mailto: و ...).',
					'message_label' => '',
					'new_tab'     => 1,
				),
			)
		);
	}

	/**
	 * یک کانال با مقادیر پیش‌فرض نوع مشخص.
	 *
	 * @param string $type نوع کانال.
	 * @param array  $args مقادیر جایگزین.
	 * @return array
	 */
	public static function channel_defaults( $type = 'custom', $args = array() ) {
		$types = self::channel_types();
		$t     = isset( $types[ $type ] ) ? $types[ $type ] : $types['custom'];

		return wp_parse_args(
			$args,
			array(
				'enabled'    => 1,
				'type'       => isset( $types[ $type ] ) ? $type : 'custom',
				'title'      => $t['label'],
				'subtitle'   => '',
				'value'      => '',
				'message'    => '',
				'new_tab'    => $t['new_tab'],
				'icon_type'  => 'preset',
				'icon'       => $t['icon'],
				'icon_svg'   => '',
				'icon_img'   => '',
				'icon_bg'    => $t['bg'],
				'icon_color' => $t['color'],
			)
		);
	}

	/**
	 * مقادیر پیش‌فرض تنظیمات.
	 *
	 * @return array
	 */
	public static function defaults() {
		return array(
			// عمومی.
			'enabled'           => 1,
			'single_direct'     => 0,
			'aria_label'        => 'تماس با ما',
			'direction'         => 'auto',

			// نمایش.
			'display_mode'      => 'all',
			'pages'             => array(),
			'front_page'        => 0,
			'post_ids'          => '',
			'show_desktop'      => 1,
			'show_mobile'       => 1,

			// موقعیت.
			'position'          => 'bottom-right',
			'offset_x'          => 22,
			'offset_y'          => 24,
			'custom_x'          => 95,
			'custom_y'          => 90,
			'mobile_breakpoint' => 1024,
			'mobile_offset_x'   => 14,
			'mobile_offset_y'   => 78,
			'z_index'           => 9990,

			// ظاهر دکمه اصلی.
			'size'              => 60,
			'mobile_size'       => 54,
			'icon_type'         => 'preset',
			'icon'              => 'phone',
			'icon_svg'          => '',
			'icon_img'          => '',
			'bg_color'          => '#F5B301',
			'bg_color2'         => '#FFD34D',
			'icon_color'        => '#2B2118',
			'pulse'             => 1,

			// ظاهر منو.
			'card_bg'           => '#FFFFFF',
			'card_text'         => '#2B2118',
			'card_subtext'      => '#8A7D70',
			'card_border'       => '#F5EAD0',

			// کانال‌ها.
			'channels'          => array(
				self::channel_defaults( 'phone', array( 'subtitle' => 'همین حالا تماس بگیرید' ) ),
				self::channel_defaults(
					'whatsapp',
					array(
						'subtitle' => 'پاسخ سریع کارشناسان',
						'message'  => 'سلام، از سایت پیام می‌دهم.',
					)
				),
				self::channel_defaults( 'telegram', array( 'enabled' => 0 ) ),
			),
		);
	}

	/**
	 * تنظیمات فعلی ادغام‌شده با پیش‌فرض‌ها.
	 *
	 * @return array
	 */
	public static function get() {
		$saved = get_option( FCB_OPTION );
		if ( ! is_array( $saved ) ) {
			return self::defaults();
		}
		return array_merge( self::defaults(), $saved );
	}

	/**
	 * هنگام فعال‌سازی، تنظیمات پیش‌فرض ذخیره شود.
	 */
	public static function activate() {
		if ( false === get_option( FCB_OPTION ) ) {
			add_option( FCB_OPTION, self::defaults() );
		}
	}

	/**
	 * پاک‌سازی ورودی فرم تنظیمات.
	 *
	 * @param mixed $input ورودی خام.
	 * @return array
	 */
	public static function sanitize( $input ) {
		$d   = self::defaults();
		$in  = is_array( $input ) ? wp_unslash( $input ) : array();
		$out = array();

		// چک‌باکس‌ها.
		foreach ( array( 'enabled', 'single_direct', 'front_page', 'show_desktop', 'show_mobile', 'pulse' ) as $key ) {
			$out[ $key ] = empty( $in[ $key ] ) ? 0 : 1;
		}

		$out['aria_label'] = isset( $in['aria_label'] ) ? sanitize_text_field( $in['aria_label'] ) : $d['aria_label'];
		$out['direction']  = self::choice( $in, 'direction', array( 'auto', 'rtl', 'ltr' ), $d['direction'] );

		$out['display_mode'] = self::choice( $in, 'display_mode', array( 'all', 'include', 'exclude' ), 'all' );
		$out['pages']        = isset( $in['pages'] ) && is_array( $in['pages'] ) ? array_values( array_filter( array_map( 'absint', $in['pages'] ) ) ) : array();
		$out['post_ids']     = isset( $in['post_ids'] ) ? implode( ', ', self::parse_ids( $in['post_ids'] ) ) : '';

		$out['position'] = self::choice( $in, 'position', array_keys( self::positions() ), $d['position'] );

		$numbers = array(
			'offset_x'          => array( 0, 500 ),
			'offset_y'          => array( 0, 500 ),
			'custom_x'          => array( 0, 100 ),
			'custom_y'          => array( 0, 100 ),
			'mobile_breakpoint' => array( 0, 3000 ),
			'mobile_offset_x'   => array( 0, 500 ),
			'mobile_offset_y'   => array( 0, 500 ),
			'z_index'           => array( 0, 2147483647 ),
			'size'              => array( 30, 150 ),
			'mobile_size'       => array( 30, 150 ),
		);
		foreach ( $numbers as $key => $range ) {
			$out[ $key ] = self::number( $in, $key, $range[0], $range[1], $d[ $key ] );
		}

		$out = array_merge( $out, self::sanitize_icon( $in, $d['icon'] ) );

		$colors = array( 'bg_color', 'icon_color', 'card_bg', 'card_text', 'card_subtext', 'card_border' );
		foreach ( $colors as $key ) {
			$color       = isset( $in[ $key ] ) ? sanitize_hex_color( $in[ $key ] ) : '';
			$out[ $key ] = $color ? $color : $d[ $key ];
		}
		// رنگ دوم گرادیان اختیاری است (خالی = رنگ ثابت).
		$out['bg_color2'] = isset( $in['bg_color2'] ) ? (string) sanitize_hex_color( $in['bg_color2'] ) : '';

		$out['channels'] = array();
		if ( isset( $in['channels'] ) && is_array( $in['channels'] ) ) {
			foreach ( $in['channels'] as $channel ) {
				if ( is_array( $channel ) ) {
					$out['channels'][] = self::sanitize_channel( $channel );
				}
			}
		}

		return $out;
	}

	/**
	 * پاک‌سازی یک کانال.
	 *
	 * @param array $c ورودی خام کانال.
	 * @return array
	 */
	protected static function sanitize_channel( $c ) {
		$types = self::channel_types();
		$type  = isset( $c['type'], $types[ $c['type'] ] ) ? $c['type'] : 'custom';
		$def   = self::channel_defaults( $type );

		$icon_bg    = isset( $c['icon_bg'] ) ? sanitize_hex_color( $c['icon_bg'] ) : '';
		$icon_color = isset( $c['icon_color'] ) ? sanitize_hex_color( $c['icon_color'] ) : '';

		return array_merge(
			array(
				'enabled'    => empty( $c['enabled'] ) ? 0 : 1,
				'type'       => $type,
				'title'      => isset( $c['title'] ) ? sanitize_text_field( $c['title'] ) : '',
				'subtitle'   => isset( $c['subtitle'] ) ? sanitize_text_field( $c['subtitle'] ) : '',
				// sanitize_text_field کاراکترهای %xx لینک‌ها را حذف می‌کند، پس فقط تگ‌ها و فاصله‌ها حذف می‌شوند.
				'value'      => isset( $c['value'] ) ? trim( preg_replace( '/[\r\n\t ]+/', ' ', wp_strip_all_tags( $c['value'] ) ) ) : '',
				'message'    => isset( $c['message'] ) ? sanitize_textarea_field( $c['message'] ) : '',
				'new_tab'    => empty( $c['new_tab'] ) ? 0 : 1,
				'icon_bg'    => $icon_bg ? $icon_bg : $def['icon_bg'],
				'icon_color' => $icon_color ? $icon_color : $def['icon_color'],
			),
			self::sanitize_icon( $c, $def['icon'] )
		);
	}

	/**
	 * پاک‌سازی فیلدهای آیکون (مشترک بین دکمه اصلی و کانال‌ها).
	 *
	 * @param array  $in           ورودی.
	 * @param string $default_icon آیکون پیش‌فرض.
	 * @return array
	 */
	protected static function sanitize_icon( $in, $default_icon ) {
		$icons = fcb_get_icons();

		return array(
			'icon_type' => self::choice( $in, 'icon_type', array( 'preset', 'svg', 'image' ), 'preset' ),
			'icon'      => isset( $in['icon'], $icons[ $in['icon'] ] ) ? $in['icon'] : $default_icon,
			'icon_svg'  => isset( $in['icon_svg'] ) ? fcb_sanitize_svg( $in['icon_svg'] ) : '',
			'icon_img'  => isset( $in['icon_img'] ) ? esc_url_raw( trim( $in['icon_img'] ) ) : '',
		);
	}

	/**
	 * تبدیل رشته شناسه‌ها به آرایه عددی.
	 *
	 * @param string|array $ids شناسه‌ها.
	 * @return int[]
	 */
	public static function parse_ids( $ids ) {
		if ( is_array( $ids ) ) {
			$ids = implode( ',', $ids );
		}
		$ids = self::latin_digits( (string) $ids );
		return array_values( array_unique( array_filter( array_map( 'absint', preg_split( '/[\s,،]+/u', $ids ) ) ) ) );
	}

	/**
	 * تبدیل ارقام فارسی و عربی به انگلیسی.
	 *
	 * @param string $str رشته.
	 * @return string
	 */
	public static function latin_digits( $str ) {
		return strtr(
			(string) $str,
			array(
				'۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4', // phpcs:ignore WordPress.Arrays.ArrayDeclarationSpacing
				'۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
				'٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
				'٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
			)
		);
	}

	/**
	 * مقدار انتخابی از بین گزینه‌های مجاز.
	 */
	protected static function choice( $in, $key, $allowed, $default ) {
		return isset( $in[ $key ] ) && in_array( $in[ $key ], $allowed, true ) ? $in[ $key ] : $default;
	}

	/**
	 * عدد محدود به بازه.
	 */
	protected static function number( $in, $key, $min, $max, $default ) {
		if ( ! isset( $in[ $key ] ) || '' === trim( (string) $in[ $key ] ) ) {
			return $default;
		}
		$value = (float) self::latin_digits( $in[ $key ] );
		$value = max( $min, min( $max, $value ) );
		return ( floor( $value ) == $value ) ? (int) $value : round( $value, 2 ); // phpcs:ignore Universal.Operators.StrictComparisons
	}
}
