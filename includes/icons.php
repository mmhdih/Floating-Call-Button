<?php
/**
 * Preset icons and icon helpers.
 *
 * @package Tavoos_Floating_Call_Button
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Preset icons, all drawn in a 24x24 viewBox.
 *
 * Add your own with the `tavoos_fcb_icons` filter:
 * $icons['my-icon'] = array( 'label' => 'My icon', 'path' => 'M...' );
 * For a multicolor icon, use an `svg` key (the markup inside <svg>) instead of `path`.
 *
 * @return array<string, array{label: string, path?: string, rule?: string, svg?: string}>
 */
function tavoos_fcb_get_icons() {
	static $icons = null;

	if ( null === $icons ) {
		$icons = apply_filters(
			'tavoos_fcb_icons',
			array(
				'phone'     => array(
					'label' => __( 'Phone', 'tavoos-floating-call-button' ),
					'path'  => 'M6.6 10.8a15.1 15.1 0 0 0 6.6 6.6l2.2-2.2a1 1 0 0 1 1-.25 11.4 11.4 0 0 0 3.6.57 1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.25.2 2.45.57 3.57a1 1 0 0 1-.25 1z',
				),
				'whatsapp'  => array(
					'label' => __( 'WhatsApp', 'tavoos-floating-call-button' ),
					'path'  => 'M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2zm5.3 14.1c-.2.6-1.3 1.2-1.8 1.2-.5.1-1 .2-3.3-.7-2.8-1.1-4.5-3.9-4.7-4.1-.1-.2-1.1-1.5-1.1-2.9s.7-2 1-2.3c.3-.3.6-.3.8-.3h.6c.2 0 .4 0 .6.5l.9 2.1c.1.2.1.4 0 .5l-.3.5-.4.4c-.1.1-.3.3-.1.6.2.3.7 1.2 1.6 2 1.1 1 2 1.3 2.3 1.4.3.1.4.1.6-.1l.9-1c.2-.3.4-.2.6-.1l2 1c.3.1.5.2.5.3.1.2.1.7-.1 1.4z',
				),
				'telegram'  => array(
					'label' => __( 'Telegram', 'tavoos-floating-call-button' ),
					'path'  => 'M9.78 18.65l.28-4.23 7.68-6.92c.34-.31-.07-.46-.52-.19L7.74 13.3 3.64 12c-.88-.25-.89-.86.2-1.3l15.97-6.16c.73-.33 1.43.18 1.15 1.3l-2.72 12.81c-.19.91-.74 1.13-1.5.71L12.6 16.3l-1.99 1.93c-.23.23-.42.42-.83.42z',
				),
				'instagram' => array(
					'label' => __( 'Instagram', 'tavoos-floating-call-button' ),
					'path'  => 'M7.8 2h8.4C19.4 2 22 4.6 22 7.8v8.4a5.8 5.8 0 0 1-5.8 5.8H7.8C4.6 22 2 19.4 2 16.2V7.8A5.8 5.8 0 0 1 7.8 2m-.2 2A3.6 3.6 0 0 0 4 7.6v8.8C4 18.39 5.61 20 7.6 20h8.8a3.6 3.6 0 0 0 3.6-3.6V7.6C20 5.61 18.39 4 16.4 4H7.6m9.65 1.5a1.25 1.25 0 0 1 1.25 1.25A1.25 1.25 0 0 1 17.25 8 1.25 1.25 0 0 1 16 6.75a1.25 1.25 0 0 1 1.25-1.25M12 7a5 5 0 0 1 5 5 5 5 0 0 1-5 5 5 5 0 0 1-5-5 5 5 0 0 1 5-5m0 2a3 3 0 0 0-3 3 3 3 0 0 0 3 3 3 3 0 0 0 3-3 3 3 0 0 0-3-3z',
				),
				'linkedin'  => array(
					'label' => __( 'LinkedIn', 'tavoos-floating-call-button' ),
					'path'  => 'M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.32 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.79M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z',
				),
				'eitaa'     => array(
					'label' => __( 'Eitaa', 'tavoos-floating-call-button' ),
					'path'  => 'M5.968 23.942a6.624 6.624 0 0 1-2.332-.83c-1.62-.929-2.829-2.593-3.217-4.426-.151-.717-.17-1.623-.15-7.207C.288 5.47.274 5.78.56 4.79c.142-.493.537-1.34.823-1.767C2.438 1.453 3.99.445 5.913.08c.384-.073.94-.08 6.056-.08 6.251 0 6.045-.009 7.066.314a6.807 6.807 0 0 1 4.314 4.184c.33.937.346 1.087.369 3.555l.02 2.23-.391.268c-.558.381-1.29 1.06-2.316 2.15-1.182 1.256-2.376 2.42-2.982 2.907-1.309 1.051-2.508 1.651-3.726 1.864-.634.11-1.682.067-2.302-.095-.553-.144-.517-.168-.726.464a6.355 6.355 0 0 0-.318 1.546l-.031.407-.146-.03c-1.215-.241-2.419-1.285-2.884-2.5a3.583 3.583 0 0 1-.26-1.219l-.016-.34-.309-.284c-.644-.59-1.063-1.312-1.195-2.061-.212-1.193.34-2.542 1.538-3.756 1.264-1.283 3.127-2.29 4.953-2.68.658-.14 1.818-.177 2.403-.075 1.138.198 2.067.773 2.645 1.639.182.271.195.31.177.555a.812.812 0 0 1-.183.493c-.465.651-1.848 1.348-3.336 1.68-2.625.585-4.294-.142-4.033-1.759.026-.163.04-.304.031-.313-.032-.032-.293.104-.575.3-.479.334-.903.984-1.05 1.607-.036.156-.05.406-.034.65.02.331.053.454.192.736.092.186.275.45.408.589l.24.251-.096.122a4.845 4.845 0 0 0-.677 1.217 3.635 3.635 0 0 0-.105 1.815c.103.461.421 1.095.739 1.468.242.285.797.764.886.764.024 0 .044-.048.044-.106.001-.23.184-.973.326-1.327.423-1.058 1.351-1.96 2.82-2.74.245-.13.952-.47 1.572-.757 1.36-.63 2.103-1.015 2.511-1.305 1.176-.833 1.903-2.065 2.14-3.625.086-.57.086-1.634 0-2.207-.368-2.438-2.195-4.096-4.818-4.37-2.925-.307-6.648 1.953-8.942 5.427-1.116 1.69-1.87 3.565-2.187 5.443-.123.728-.169 2.08-.093 2.75.193 1.704.822 3.078 1.903 4.156a6.531 6.531 0 0 0 1.87 1.313c2.368 1.13 4.99 1.155 7.295.071.996-.469 1.974-1.196 3.023-2.25 1.02-1.025 1.71-1.88 3.592-4.458 1.04-1.423 1.864-2.368 2.272-2.605l.15-.086-.019 3.091c-.018 2.993-.022 3.107-.123 3.561-.6 2.678-2.54 4.636-5.195 5.242l-.468.107-5.775.01c-4.734.008-5.85-.002-6.19-.056z',
				),
				'bale'      => array(
					'label' => __( 'Bale', 'tavoos-floating-call-button' ),
					'path'  => 'M12.2 .3A11.8 11.8 0 1 1 .4 12.1V1.6C.4.6 1.4.1 2.2.6l3 2A11.7 11.7 0 0 1 12.2.3zM7.16 10.01L10.29 13.07L16.63 6.73A1.8 1.8 0 0 1 19.17 9.27L11.57 16.87A1.8 1.8 0 0 1 9.04 16.89L4.64 12.59A1.8 1.8 0 0 1 7.16 10.01z',
					'rule'  => 'evenodd',
				),
				'rubika'    => array(
					'label' => __( 'Rubika', 'tavoos-floating-call-button' ),
					// Multicolor logo: the icon color setting does not apply to it.
					'svg'   => '<path fill="#7CB342" d="M12 .8l9.7 5.6-4.24 2.45z"/><path fill="#26B99A" d="M12 .8l5.46 8.05L12 5.7z"/><path fill="#5DD6B4" d="M21.7 6.4v11.2l-4.24-2.45z"/><path fill="#8E44AD" d="M21.7 6.4l-4.24 8.75v-6.3z"/><path fill="#E8445A" d="M21.7 17.6L12 23.2v-4.9z"/><path fill="#F7941D" d="M21.7 17.6l-9.7.7 5.46-3.15z"/><path fill="#FDBB2D" d="M12 23.2l-9.7-5.6 4.24-2.45z"/><path fill="#A6C83B" d="M12 23.2l-5.46-8.05L12 18.3z"/><path fill="#1E88E5" d="M2.3 17.6V6.4l4.24 2.45z"/><path fill="#6A3FA0" d="M2.3 17.6l4.24-8.75v6.3z"/><path fill="#E8404F" d="M2.3 6.4L12 .8v4.9z"/><path fill="#F57C20" d="M2.3 6.4l9.7-.7-5.46 3.15z"/>',
				),
				'email'     => array(
					'label' => __( 'Email', 'tavoos-floating-call-button' ),
					'path'  => 'M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z',
				),
				'sms'       => array(
					'label' => __( 'SMS', 'tavoos-floating-call-button' ),
					'path'  => 'M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM9 11H7V9h2v2zm4 0h-2V9h2v2zm4 0h-2V9h2v2z',
				),
				'chat'      => array(
					'label' => __( 'Chat', 'tavoos-floating-call-button' ),
					'path'  => 'M21 6h-2v9H6v2c0 .55.45 1 1 1h11l4 4V7c0-.55-.45-1-1-1zm-4 6V3c0-.55-.45-1-1-1H3c-.55 0-1 .45-1 1v14l4-4h10c.55 0 1-.45 1-1z',
				),
				'send'      => array(
					'label' => __( 'Send', 'tavoos-floating-call-button' ),
					'path'  => 'M2.01 21L23 12 2.01 3 2 10l15 2-15 2z',
				),
				'support'   => array(
					'label' => __( 'Support', 'tavoos-floating-call-button' ),
					'path'  => 'M12 1a9 9 0 0 0-9 9v7c0 1.66 1.34 3 3 3h3v-8H5v-2c0-3.87 3.13-7 7-7s7 3.13 7 7v2h-4v8h4v1h-7v2h6c1.66 0 3-1.34 3-3V10a9 9 0 0 0-9-9z',
				),
				'location'  => array(
					'label' => __( 'Location / map', 'tavoos-floating-call-button' ),
					'path'  => 'M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z',
				),
				'link'      => array(
					'label' => __( 'Link', 'tavoos-floating-call-button' ),
					'path'  => 'M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z',
				),
				'globe'     => array(
					'label' => __( 'Website', 'tavoos-floating-call-button' ),
					'path'  => 'M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm6.93 6h-2.95a15.65 15.65 0 0 0-1.38-3.56A8.03 8.03 0 0 1 18.93 8zM12 4.04c.83 1.2 1.48 2.53 1.91 3.96h-3.82c.43-1.43 1.08-2.76 1.91-3.96zM4.26 14A8.2 8.2 0 0 1 4 12c0-.69.1-1.36.26-2h3.38a16.5 16.5 0 0 0 0 4H4.26zm.82 2h2.95c.32 1.25.78 2.45 1.38 3.56A7.99 7.99 0 0 1 5.08 16zm2.95-8H5.08a7.99 7.99 0 0 1 4.33-3.56A15.65 15.65 0 0 0 8.03 8zM12 19.96c-.83-1.2-1.48-2.53-1.91-3.96h3.82c-.43 1.43-1.08 2.76-1.91 3.96zM14.34 14H9.66a14.7 14.7 0 0 1 0-4h4.68a14.7 14.7 0 0 1 0 4zm.25 5.56c.6-1.11 1.06-2.31 1.38-3.56h2.95a8.03 8.03 0 0 1-4.33 3.56zM16.36 14a16.5 16.5 0 0 0 0-4h3.38a8.2 8.2 0 0 1 0 4h-3.38z',
				),
				'user'      => array(
					'label' => __( 'User / agent', 'tavoos-floating-call-button' ),
					'path'  => 'M12 12a5 5 0 1 0 0-10 5 5 0 0 0 0 10zm0 2c-3.34 0-10 1.67-10 5v3h20v-3c0-3.33-6.66-5-10-5z',
				),
				'question'  => array(
					'label' => __( 'Question', 'tavoos-floating-call-button' ),
					'path'  => 'M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26A1.99 1.99 0 0 0 12 7a2 2 0 0 0-2 2H8a4 4 0 1 1 8 0c0 .88-.36 1.68-.93 2.25z',
				),
				'close'     => array(
					'label' => __( 'Close', 'tavoos-floating-call-button' ),
					'path'  => 'M18.3 5.7a1 1 0 0 0-1.4 0L12 10.6 7.1 5.7a1 1 0 0 0-1.4 1.4l4.9 4.9-4.9 4.9a1 1 0 1 0 1.4 1.4l4.9-4.9 4.9 4.9a1 1 0 0 0 1.4-1.4L13.4 12l4.9-4.9a1 1 0 0 0 0-1.4z',
				),
			)
		);
	}

	return $icons;
}

/**
 * SVG markup of a preset icon.
 *
 * @param string $key   Icon key.
 * @param string $class Optional CSS class.
 * @return string
 */
function tavoos_fcb_preset_icon_svg( $key, $class = '' ) {
	$icons = tavoos_fcb_get_icons();
	if ( ! isset( $icons[ $key ] ) ) {
		$key = 'phone';
	}
	$icon = $icons[ $key ];

	if ( ! empty( $icon['svg'] ) ) {
		$body = $icon['svg'];
	} else {
		$body = sprintf(
			'<path fill="currentColor"%s d="%s"></path>',
			empty( $icon['rule'] ) ? '' : ' fill-rule="' . esc_attr( $icon['rule'] ) . '"',
			esc_attr( $icon['path'] )
		);
	}

	$svg = sprintf(
		'<svg%s viewBox="0 0 24 24" aria-hidden="true" focusable="false">%s</svg>',
		$class ? ' class="' . esc_attr( $class ) . '"' : '',
		$body
	);

	return wp_kses( $svg, tavoos_fcb_icon_allowed_html() );
}

/**
 * Icon markup for an item, based on its icon source (preset, custom SVG or image).
 *
 * @param array $item Array with icon_type, icon, icon_svg and icon_img.
 * @return string Safe HTML.
 */
function tavoos_fcb_render_icon( $item ) {
	$type = isset( $item['icon_type'] ) ? $item['icon_type'] : 'preset';

	if ( 'svg' === $type && ! empty( $item['icon_svg'] ) ) {
		return tavoos_fcb_sanitize_svg( $item['icon_svg'] );
	}

	if ( 'image' === $type && ! empty( $item['icon_img'] ) ) {
		return '<img src="' . esc_url( $item['icon_img'] ) . '" alt="" loading="lazy" decoding="async">';
	}

	return tavoos_fcb_preset_icon_svg( isset( $item['icon'] ) ? $item['icon'] : 'phone' );
}

/**
 * Echo icon markup, escaped against the icon allowlist.
 *
 * @param string $html Icon markup.
 */
function tavoos_fcb_echo_icon( $html ) {
	echo wp_kses( $html, tavoos_fcb_icon_allowed_html() );
}

/**
 * Tags and attributes allowed in icon markup (SVG and img).
 *
 * @return array
 */
function tavoos_fcb_icon_allowed_html() {
	static $allowed = null;
	if ( null !== $allowed ) {
		return $allowed;
	}

	$common = array(
		'class'             => true,
		'id'                => true,
		'fill'              => true,
		'fill-rule'         => true,
		'fill-opacity'      => true,
		'clip-rule'         => true,
		'clip-path'         => true,
		'stroke'            => true,
		'stroke-width'      => true,
		'stroke-linecap'    => true,
		'stroke-linejoin'   => true,
		'stroke-miterlimit' => true,
		'stroke-dasharray'  => true,
		'stroke-opacity'    => true,
		'opacity'           => true,
		'transform'         => true,
		'style'             => true,
	);

	$allowed = array(
		'svg'            => $common + array(
			'xmlns'               => true,
			'xmlns:xlink'         => true,
			'viewbox'             => true,
			'width'               => true,
			'height'              => true,
			'preserveaspectratio' => true,
			'aria-hidden'         => true,
			'focusable'           => true,
			'role'                => true,
			'version'             => true,
		),
		'g'              => $common,
		'path'           => $common + array( 'd' => true ),
		'circle'         => $common + array(
			'cx' => true,
			'cy' => true,
			'r'  => true,
		),
		'ellipse'        => $common + array(
			'cx' => true,
			'cy' => true,
			'rx' => true,
			'ry' => true,
		),
		'rect'           => $common + array(
			'x'      => true,
			'y'      => true,
			'width'  => true,
			'height' => true,
			'rx'     => true,
			'ry'     => true,
		),
		'line'           => $common + array(
			'x1' => true,
			'y1' => true,
			'x2' => true,
			'y2' => true,
		),
		'polyline'       => $common + array( 'points' => true ),
		'polygon'        => $common + array( 'points' => true ),
		'defs'           => array(),
		'clippath'       => array(
			'id'            => true,
			'clippathunits' => true,
		),
		'lineargradient' => array(
			'id'                => true,
			'x1'                => true,
			'y1'                => true,
			'x2'                => true,
			'y2'                => true,
			'gradientunits'     => true,
			'gradienttransform' => true,
		),
		'radialgradient' => array(
			'id'                => true,
			'cx'                => true,
			'cy'                => true,
			'r'                 => true,
			'fx'                => true,
			'fy'                => true,
			'gradientunits'     => true,
			'gradienttransform' => true,
		),
		'stop'           => array(
			'offset'       => true,
			'stop-color'   => true,
			'stop-opacity' => true,
			'style'        => true,
		),
		'title'          => array(),
		'img'            => array(
			'src'      => true,
			'alt'      => true,
			'loading'  => true,
			'decoding' => true,
			'class'    => true,
		),
	);

	return $allowed;
}

/**
 * Sanitize user-supplied SVG code (removes scripts, event handlers, etc.).
 *
 * @param string $svg SVG code.
 * @return string
 */
function tavoos_fcb_sanitize_svg( $svg ) {
	$svg = trim( (string) $svg );
	if ( '' === $svg || false === stripos( $svg, '<svg' ) ) {
		return '';
	}

	// Drop the contents of dangerous tags too (wp_kses only strips the tags themselves).
	$svg = preg_replace( '#<(script|style|foreignObject)\b[^>]*>.*?</\1\s*>#is', '', $svg );

	$allowed = tavoos_fcb_icon_allowed_html();
	unset( $allowed['img'] );

	return trim( wp_kses( $svg, $allowed ) );
}
