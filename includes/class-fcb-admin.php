<?php
/**
 * صفحه تنظیمات افزونه در پیشخوان وردپرس.
 *
 * @package Floating_Call_Button
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FCB_Admin {

	const SLUG  = 'floating-call-button';
	const GROUP = 'fcb_settings_group';

	/**
	 * شناسه صفحه در پیشخوان.
	 *
	 * @var string
	 */
	protected static $hook = '';

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'register' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'assets' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( FCB_FILE ), array( __CLASS__, 'action_links' ) );
		add_filter( 'plugin_row_meta', array( __CLASS__, 'row_meta' ), 10, 2 );
	}

	public static function menu() {
		self::$hook = add_menu_page(
			'دکمه شناور تماس',
			'دکمه تماس',
			'manage_options',
			self::SLUG,
			array( __CLASS__, 'render_page' ),
			'dashicons-phone',
			81
		);
	}

	public static function register() {
		register_setting(
			self::GROUP,
			FCB_OPTION,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( 'FCB_Options', 'sanitize' ),
				'default'           => FCB_Options::defaults(),
			)
		);
	}

	public static function action_links( $links ) {
		array_unshift( $links, '<a href="' . esc_url( admin_url( 'admin.php?page=' . self::SLUG ) ) . '">تنظیمات</a>' );
		return $links;
	}

	public static function row_meta( $links, $file ) {
		if ( plugin_basename( FCB_FILE ) === $file ) {
			$links[] = '<a href="https://tavoosweb.ir/" target="_blank" rel="noopener">طراحی شده توسط مهدی حبیبی | طاووس وب</a>';
		}
		return $links;
	}

	public static function assets( $hook ) {
		if ( $hook !== self::$hook ) {
			return;
		}

		wp_enqueue_media();
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'fcb-admin', FCB_URL . 'assets/css/admin.css', array(), FCB_VERSION );
		wp_enqueue_script( 'fcb-admin', FCB_URL . 'assets/js/admin.js', array( 'jquery', 'jquery-ui-sortable', 'wp-color-picker' ), FCB_VERSION, true );

		$icons = array();
		foreach ( array_keys( fcb_get_icons() ) as $key ) {
			$icons[ $key ] = fcb_preset_icon_svg( $key );
		}

		wp_localize_script(
			'fcb-admin',
			'fcbAdmin',
			array(
				'icons' => $icons,
				'types' => FCB_Options::channel_types(),
				'i18n'  => array(
					'confirmRemove' => 'این کانال حذف شود؟',
					'mediaTitle'    => 'انتخاب آیکون',
					'mediaButton'   => 'استفاده به عنوان آیکون',
					'untitled'      => 'بدون عنوان',
					'noValue'       => 'مقدار وارد نشده',
				),
			)
		);
	}

	/* ------------------------------------------------------------------ */
	/* Helpers                                                             */
	/* ------------------------------------------------------------------ */

	protected static function name( $key ) {
		return FCB_OPTION . '[' . $key . ']';
	}

	protected static function checkbox( $name, $checked, $label, $class = '' ) {
		printf(
			'<label class="fcb-switch %4$s"><input type="checkbox" name="%1$s" value="1" %2$s><span class="fcb-switch__ui" aria-hidden="true"></span><span>%3$s</span></label>',
			esc_attr( $name ),
			checked( ! empty( $checked ), true, false ),
			esc_html( $label ),
			esc_attr( $class )
		);
	}

	protected static function number( $s, $key, $unit, $min, $max, $step = 1 ) {
		printf(
			'<span class="fcb-num"><input type="number" id="fcb-%1$s" name="%2$s" value="%3$s" min="%4$s" max="%5$s" step="%6$s" class="small-text"> <span class="fcb-num__unit">%7$s</span></span>',
			esc_attr( $key ),
			esc_attr( self::name( $key ) ),
			esc_attr( $s[ $key ] ),
			esc_attr( $min ),
			esc_attr( $max ),
			esc_attr( $step ),
			esc_html( $unit )
		);
	}

	protected static function color( $name, $value, $default = '', $id = '' ) {
		printf(
			'<input type="text" class="fcb-color" name="%1$s" value="%2$s" data-default-color="%3$s"%4$s>',
			esc_attr( $name ),
			esc_attr( $value ),
			esc_attr( $default ),
			$id ? ' id="' . esc_attr( $id ) . '"' : ''
		);
	}

	/**
	 * فیلد انتخاب آیکون (آماده / SVG / تصویر).
	 *
	 * @param string $prefix نام پایه فیلد، مثل fcb_settings یا fcb_settings[channels][0].
	 * @param array  $item   مقادیر فعلی.
	 */
	protected static function icon_field( $prefix, $item ) {
		$source  = isset( $item['icon_type'] ) ? $item['icon_type'] : 'preset';
		$sources = array(
			'preset' => 'آیکون آماده',
			'svg'    => 'کد SVG دلخواه',
			'image'  => 'تصویر (آپلود)',
		);
		?>
		<div class="fcb-icon-field" data-source="<?php echo esc_attr( $source ); ?>">
			<div class="fcb-seg">
				<?php foreach ( $sources as $key => $label ) : ?>
					<label><input type="radio" class="fcb-icon-source" name="<?php echo esc_attr( $prefix . '[icon_type]' ); ?>" value="<?php echo esc_attr( $key ); ?>" <?php checked( $source, $key ); ?>><span><?php echo esc_html( $label ); ?></span></label>
				<?php endforeach; ?>
			</div>

			<div class="fcb-icon-panel fcb-icon-panel--preset">
				<div class="fcb-icon-grid">
					<?php foreach ( fcb_get_icons() as $key => $icon ) : ?>
						<?php
						if ( 'close' === $key ) {
							continue;
						}
						?>
						<label class="fcb-icon-opt" title="<?php echo esc_attr( $icon['label'] ); ?>">
							<input type="radio" class="fcb-icon-preset" name="<?php echo esc_attr( $prefix . '[icon]' ); ?>" value="<?php echo esc_attr( $key ); ?>" <?php checked( $item['icon'], $key ); ?>>
							<span><?php echo fcb_preset_icon_svg( $key ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						</label>
					<?php endforeach; ?>
				</div>
			</div>

			<div class="fcb-icon-panel fcb-icon-panel--svg">
				<textarea class="large-text code fcb-icon-svg" rows="4" dir="ltr" name="<?php echo esc_attr( $prefix . '[icon_svg]' ); ?>" placeholder="&lt;svg viewBox=&quot;0 0 24 24&quot;&gt;...&lt;/svg&gt;"><?php echo esc_textarea( $item['icon_svg'] ); ?></textarea>
				<p class="description">کد کامل SVG را بچسبانید. برای هم‌رنگ شدن با «رنگ آیکون»، از <code>fill="currentColor"</code> استفاده کنید.</p>
			</div>

			<div class="fcb-icon-panel fcb-icon-panel--image">
				<div class="fcb-media">
					<input type="url" class="regular-text fcb-icon-img" dir="ltr" name="<?php echo esc_attr( $prefix . '[icon_img]' ); ?>" value="<?php echo esc_attr( $item['icon_img'] ); ?>" placeholder="https://">
					<button type="button" class="button fcb-upload">انتخاب از کتابخانه رسانه</button>
				</div>
				<p class="description">فرمت‌های PNG، SVG یا WebP با پس‌زمینه شفاف پیشنهاد می‌شود.</p>
			</div>
		</div>
		<?php
	}

	/**
	 * یک ردیف کانال در تکرارشونده.
	 *
	 * @param string|int $index اندیس.
	 * @param array      $c     کانال.
	 * @param bool       $open  باز باشد؟
	 */
	protected static function channel_row( $index, $c, $open = false ) {
		$types  = FCB_Options::channel_types();
		$type   = isset( $types[ $c['type'] ] ) ? $types[ $c['type'] ] : $types['custom'];
		$prefix = FCB_OPTION . '[channels][' . $index . ']';
		$id     = 'fcb-ch-' . $index;
		?>
		<div class="fcb-ch<?php echo $open ? ' is-open' : ''; ?><?php echo empty( $c['enabled'] ) ? ' is-disabled' : ''; ?>">
			<div class="fcb-ch__head">
				<span class="fcb-ch__handle dashicons dashicons-menu" title="برای جابجایی بکشید"></span>
				<span class="fcb-ch__preview" style="<?php echo esc_attr( '--ic-bg:' . $c['icon_bg'] . ';--ic-color:' . $c['icon_color'] ); ?>"><?php echo fcb_render_icon( $c ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				<button type="button" class="fcb-ch__summary" aria-expanded="<?php echo $open ? 'true' : 'false'; ?>">
					<strong class="fcb-ch__title"><?php echo esc_html( $c['title'] ? $c['title'] : 'بدون عنوان' ); ?></strong>
					<span class="fcb-ch__meta"><span class="fcb-ch__type"><?php echo esc_html( $type['label'] ); ?></span> · <span class="fcb-ch__value" dir="ltr"><?php echo esc_html( $c['value'] ? $c['value'] : 'مقدار وارد نشده' ); ?></span></span>
				</button>
				<?php self::checkbox( $prefix . '[enabled]', $c['enabled'], 'فعال', 'fcb-ch__enabled' ); ?>
				<button type="button" class="button-link fcb-ch__toggle" aria-label="باز/بستن"><span class="dashicons dashicons-arrow-down-alt2"></span></button>
				<button type="button" class="button-link fcb-ch__remove" aria-label="حذف کانال" title="حذف"><span class="dashicons dashicons-trash"></span></button>
			</div>

			<div class="fcb-ch__body">
				<div class="fcb-grid">
					<div class="fcb-field">
						<label for="<?php echo esc_attr( $id ); ?>-type">نوع کانال</label>
						<select id="<?php echo esc_attr( $id ); ?>-type" class="fcb-ch__type-select" name="<?php echo esc_attr( $prefix . '[type]' ); ?>">
							<?php foreach ( $types as $key => $t ) : ?>
								<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $c['type'], $key ); ?>><?php echo esc_html( $t['label'] ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="fcb-field">
						<label for="<?php echo esc_attr( $id ); ?>-value">شماره / نام کاربری / لینک</label>
						<input id="<?php echo esc_attr( $id ); ?>-value" type="text" dir="ltr" class="regular-text fcb-ch__value-input" name="<?php echo esc_attr( $prefix . '[value]' ); ?>" value="<?php echo esc_attr( $c['value'] ); ?>" placeholder="<?php echo esc_attr( $type['placeholder'] ); ?>">
						<p class="description fcb-ch__hint"><?php echo esc_html( $type['hint'] ); ?></p>
					</div>
					<div class="fcb-field">
						<label for="<?php echo esc_attr( $id ); ?>-title">عنوان</label>
						<input id="<?php echo esc_attr( $id ); ?>-title" type="text" class="regular-text fcb-ch__title-input" name="<?php echo esc_attr( $prefix . '[title]' ); ?>" value="<?php echo esc_attr( $c['title'] ); ?>">
					</div>
					<div class="fcb-field">
						<label for="<?php echo esc_attr( $id ); ?>-subtitle">زیرعنوان (اختیاری)</label>
						<input id="<?php echo esc_attr( $id ); ?>-subtitle" type="text" class="regular-text" name="<?php echo esc_attr( $prefix . '[subtitle]' ); ?>" value="<?php echo esc_attr( $c['subtitle'] ); ?>" placeholder="مثلاً: پاسخ سریع کارشناسان">
					</div>
					<div class="fcb-field fcb-field--full fcb-ch__msg"<?php echo $type['message_label'] ? '' : ' hidden'; ?>>
						<label for="<?php echo esc_attr( $id ); ?>-message" class="fcb-ch__msg-label"><?php echo esc_html( $type['message_label'] ? $type['message_label'] : 'متن پیش‌فرض' ); ?></label>
						<textarea id="<?php echo esc_attr( $id ); ?>-message" class="large-text" rows="2" name="<?php echo esc_attr( $prefix . '[message]' ); ?>"><?php echo esc_textarea( $c['message'] ); ?></textarea>
					</div>
					<div class="fcb-field fcb-field--full">
						<?php self::checkbox( $prefix . '[new_tab]', $c['new_tab'], 'باز شدن لینک در تب جدید', 'fcb-ch__newtab' ); ?>
					</div>
					<div class="fcb-field fcb-field--full">
						<span class="fcb-label">آیکون</span>
						<?php self::icon_field( $prefix, $c ); ?>
					</div>
					<div class="fcb-field">
						<span class="fcb-label">رنگ پس‌زمینه آیکون</span>
						<?php self::color( $prefix . '[icon_bg]', $c['icon_bg'], $type['bg'] ); ?>
					</div>
					<div class="fcb-field">
						<span class="fcb-label">رنگ آیکون</span>
						<?php self::color( $prefix . '[icon_color]', $c['icon_color'], $type['color'] ); ?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/* ------------------------------------------------------------------ */
	/* Page                                                                */
	/* ------------------------------------------------------------------ */

	public static function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$s     = FCB_Options::get();
		$types = FCB_Options::channel_types();
		$tabs  = array(
			'channels'   => array( 'dashicons-share', 'کانال‌های ارتباطی' ),
			'appearance' => array( 'dashicons-art', 'ظاهر دکمه' ),
			'position'   => array( 'dashicons-move', 'موقعیت' ),
			'display'    => array( 'dashicons-visibility', 'نمایش و عمومی' ),
		);
		?>
		<div class="wrap fcb-wrap">
			<div class="fcb-header">
				<div class="fcb-header__logo"><?php echo fcb_preset_icon_svg( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
				<div>
					<h1>دکمه شناور تماس</h1>
					<p class="fcb-header__credit">طراحی شده توسط <a href="https://tavoosweb.ir/" target="_blank" rel="noopener">مهدی حبیبی | طاووس وب</a> · نسخه <?php echo esc_html( FCB_VERSION ); ?></p>
				</div>
			</div>

			<?php settings_errors(); ?>

			<form method="post" action="options.php" class="fcb-form">
				<?php settings_fields( self::GROUP ); ?>

				<nav class="nav-tab-wrapper fcb-tabs">
					<?php foreach ( $tabs as $key => $tab ) : ?>
						<a href="#<?php echo esc_attr( $key ); ?>" class="nav-tab" data-tab="<?php echo esc_attr( $key ); ?>"><span class="dashicons <?php echo esc_attr( $tab[0] ); ?>"></span> <?php echo esc_html( $tab[1] ); ?></a>
					<?php endforeach; ?>
				</nav>

				<?php /* ======================= کانال‌ها ======================= */ ?>
				<section class="fcb-tab" data-tab="channels">
					<div class="fcb-card">
						<h2>کانال‌های ارتباطی</h2>
						<p class="description">راه‌های ارتباطی که با کلیک روی دکمه نمایش داده می‌شوند. ترتیب را با کشیدن <span class="dashicons dashicons-menu"></span> تغییر دهید. کانال‌هایی که «مقدار» ندارند در سایت نمایش داده نمی‌شوند.</p>

						<div id="fcb-channels" class="fcb-channels">
							<?php
							foreach ( array_values( (array) $s['channels'] ) as $i => $channel ) {
								self::channel_row( $i, FCB_Options::channel_defaults( $channel['type'], $channel ) );
							}
							?>
						</div>

						<p class="fcb-empty" <?php echo $s['channels'] ? 'hidden' : ''; ?>>هنوز کانالی اضافه نشده است.</p>

						<div class="fcb-add">
							<label for="fcb-add-type">افزودن کانال جدید:</label>
							<select id="fcb-add-type">
								<?php foreach ( $types as $key => $t ) : ?>
									<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $t['label'] ); ?></option>
								<?php endforeach; ?>
							</select>
							<button type="button" class="button button-secondary" id="fcb-add-channel"><span class="dashicons dashicons-plus-alt2"></span> افزودن</button>
						</div>
					</div>

					<script type="text/html" id="tmpl-fcb-channel">
						<?php self::channel_row( '__INDEX__', FCB_Options::channel_defaults( 'custom' ), true ); ?>
					</script>
				</section>

				<?php /* ======================= ظاهر ======================= */ ?>
				<section class="fcb-tab" data-tab="appearance">
					<div class="fcb-card fcb-card--split">
						<div>
							<h2>دکمه اصلی</h2>
							<table class="form-table" role="presentation">
								<tr>
									<th scope="row">آیکون دکمه</th>
									<td><?php self::icon_field( FCB_OPTION, $s ); ?></td>
								</tr>
								<tr>
									<th scope="row">رنگ پس‌زمینه</th>
									<td><?php self::color( self::name( 'bg_color' ), $s['bg_color'], '#F5B301', 'fcb-bg-color' ); ?></td>
								</tr>
								<tr>
									<th scope="row">رنگ دوم (گرادیان)</th>
									<td>
										<?php self::color( self::name( 'bg_color2' ), $s['bg_color2'], '#FFD34D', 'fcb-bg-color2' ); ?>
										<p class="description">برای رنگ ثابت (بدون گرادیان) این فیلد را خالی کنید.</p>
									</td>
								</tr>
								<tr>
									<th scope="row">رنگ آیکون</th>
									<td><?php self::color( self::name( 'icon_color' ), $s['icon_color'], '#2B2118', 'fcb-icon-color' ); ?></td>
								</tr>
								<tr>
									<th scope="row">اندازه دکمه</th>
									<td>
										<label>دسکتاپ: <?php self::number( $s, 'size', 'px', 30, 150 ); ?></label>
										&nbsp; <label>موبایل: <?php self::number( $s, 'mobile_size', 'px', 30, 150 ); ?></label>
									</td>
								</tr>
								<tr>
									<th scope="row">انیمیشن</th>
									<td><?php self::checkbox( self::name( 'pulse' ), $s['pulse'], 'حلقه پالس دور دکمه' ); ?></td>
								</tr>
							</table>
						</div>
						<div class="fcb-preview-box">
							<span class="fcb-label">پیش‌نمایش</span>
							<div class="fcb-preview" id="fcb-preview" dir="ltr">
								<div class="fcb-preview__anchor" id="fcb-preview-anchor">
									<div class="fcb-preview__menu" id="fcb-preview-menu" dir="<?php echo esc_attr( is_rtl() ? 'rtl' : 'ltr' ); ?>"></div>
									<span class="fcb-preview__btn" id="fcb-preview-btn"></span>
								</div>
							</div>
							<p class="description">پیش‌نمایش با موقعیت انتخاب‌شده در تب «موقعیت» هماهنگ است.</p>
						</div>
					</div>

					<div class="fcb-card">
						<h2>ظاهر منوی کانال‌ها</h2>
						<table class="form-table" role="presentation">
							<tr>
								<th scope="row">پس‌زمینه کارت‌ها</th>
								<td><?php self::color( self::name( 'card_bg' ), $s['card_bg'], '#FFFFFF', 'fcb-card-bg' ); ?></td>
							</tr>
							<tr>
								<th scope="row">رنگ عنوان</th>
								<td><?php self::color( self::name( 'card_text' ), $s['card_text'], '#2B2118', 'fcb-card-text' ); ?></td>
							</tr>
							<tr>
								<th scope="row">رنگ زیرعنوان</th>
								<td><?php self::color( self::name( 'card_subtext' ), $s['card_subtext'], '#8A7D70', 'fcb-card-subtext' ); ?></td>
							</tr>
							<tr>
								<th scope="row">رنگ حاشیه کارت‌ها</th>
								<td><?php self::color( self::name( 'card_border' ), $s['card_border'], '#F5EAD0', 'fcb-card-border' ); ?></td>
							</tr>
						</table>
					</div>
				</section>

				<?php /* ======================= موقعیت ======================= */ ?>
				<section class="fcb-tab" data-tab="position">
					<div class="fcb-card">
						<h2>موقعیت دکمه</h2>
						<div class="fcb-positions">
							<?php foreach ( FCB_Options::positions() as $key => $label ) : ?>
								<label class="fcb-pos">
									<input type="radio" name="<?php echo esc_attr( self::name( 'position' ) ); ?>" value="<?php echo esc_attr( $key ); ?>" <?php checked( $s['position'], $key ); ?>>
									<span class="fcb-pos__screen fcb-pos__screen--<?php echo esc_attr( $key ); ?>"><i></i></span>
									<span class="fcb-pos__label"><?php echo esc_html( $label ); ?></span>
								</label>
							<?php endforeach; ?>
						</div>

						<div class="fcb-pos-custom" data-show-when="custom">
							<h3>موقعیت دلخواه (درصدی)</h3>
							<p class="description">روی صفحه زیر کلیک کنید یا نقطه را بکشید، یا مقدارها را دستی وارد کنید. ۰٪ یعنی لبه چپ/بالا و ۱۰۰٪ یعنی لبه راست/پایین صفحه.</p>
							<div class="fcb-pos-custom__wrap">
								<div class="fcb-pos-custom__screen" id="fcb-custom-screen" dir="ltr"><span id="fcb-custom-dot"></span></div>
								<div>
									<p><label>افقی (W) از چپ: <?php self::number( $s, 'custom_x', '%', 0, 100, 0.5 ); ?></label></p>
									<p><label>عمودی (H) از بالا: <?php self::number( $s, 'custom_y', '%', 0, 100, 0.5 ); ?></label></p>
								</div>
							</div>
						</div>

						<table class="form-table" role="presentation" data-hide-when="custom">
							<tr>
								<th scope="row">فاصله از لبه (دسکتاپ)</th>
								<td>
									<label>افقی: <?php self::number( $s, 'offset_x', 'px', 0, 500 ); ?></label>
									&nbsp; <label>عمودی: <?php self::number( $s, 'offset_y', 'px', 0, 500 ); ?></label>
								</td>
							</tr>
							<tr>
								<th scope="row">فاصله از لبه (موبایل)</th>
								<td>
									<label>افقی: <?php self::number( $s, 'mobile_offset_x', 'px', 0, 500 ); ?></label>
									&nbsp; <label>عمودی: <?php self::number( $s, 'mobile_offset_y', 'px', 0, 500 ); ?></label>
									<p class="description">مثلاً اگر قالب شما در موبایل نوار پایینی دارد، فاصله عمودی را بیشتر کنید.</p>
								</td>
							</tr>
						</table>

						<table class="form-table" role="presentation">
							<tr>
								<th scope="row">عرض نقطه شکست موبایل</th>
								<td>
									<?php self::number( $s, 'mobile_breakpoint', 'px', 0, 3000 ); ?>
									<p class="description">صفحه‌هایی با عرض کمتر یا مساوی این مقدار «موبایل» حساب می‌شوند.</p>
								</td>
							</tr>
							<tr>
								<th scope="row">z-index</th>
								<td>
									<?php self::number( $s, 'z_index', '', 0, 2147483647 ); ?>
									<p class="description">اگر دکمه زیر بخش‌های دیگر سایت می‌رود، این عدد را بیشتر کنید.</p>
								</td>
							</tr>
						</table>
					</div>
				</section>

				<?php /* ======================= نمایش و عمومی ======================= */ ?>
				<section class="fcb-tab" data-tab="display">
					<div class="fcb-card">
						<h2>تنظیمات عمومی</h2>
						<table class="form-table" role="presentation">
							<tr>
								<th scope="row">وضعیت</th>
								<td><?php self::checkbox( self::name( 'enabled' ), $s['enabled'], 'نمایش دکمه در سایت' ); ?></td>
							</tr>
							<tr>
								<th scope="row">دستگاه‌ها</th>
								<td>
									<?php self::checkbox( self::name( 'show_desktop' ), $s['show_desktop'], 'دسکتاپ' ); ?>
									<?php self::checkbox( self::name( 'show_mobile' ), $s['show_mobile'], 'موبایل و تبلت' ); ?>
								</td>
							</tr>
							<tr>
								<th scope="row">یک کانال</th>
								<td><?php self::checkbox( self::name( 'single_direct' ), $s['single_direct'], 'اگر فقط یک کانال فعال بود، دکمه مستقیم به همان لینک برود (بدون منو)' ); ?></td>
							</tr>
							<tr>
								<th scope="row"><label for="fcb-aria">متن دسترسی‌پذیری دکمه</label></th>
								<td><input id="fcb-aria" type="text" class="regular-text" name="<?php echo esc_attr( self::name( 'aria_label' ) ); ?>" value="<?php echo esc_attr( $s['aria_label'] ); ?>"></td>
							</tr>
							<tr>
								<th scope="row"><label for="fcb-direction">جهت متن منو</label></th>
								<td>
									<select id="fcb-direction" name="<?php echo esc_attr( self::name( 'direction' ) ); ?>">
										<option value="auto" <?php selected( $s['direction'], 'auto' ); ?>>خودکار (بر اساس زبان سایت)</option>
										<option value="rtl" <?php selected( $s['direction'], 'rtl' ); ?>>راست به چپ</option>
										<option value="ltr" <?php selected( $s['direction'], 'ltr' ); ?>>چپ به راست</option>
									</select>
								</td>
							</tr>
						</table>
					</div>

					<div class="fcb-card">
						<h2>نمایش در صفحات</h2>
						<div class="fcb-seg fcb-seg--block">
							<?php
							$modes = array(
								'all'     => 'همه صفحات',
								'include' => 'فقط صفحات انتخاب‌شده',
								'exclude' => 'همه صفحات به جز صفحات انتخاب‌شده',
							);
							foreach ( $modes as $key => $label ) :
								?>
								<label><input type="radio" name="<?php echo esc_attr( self::name( 'display_mode' ) ); ?>" value="<?php echo esc_attr( $key ); ?>" <?php checked( $s['display_mode'], $key ); ?>><span><?php echo esc_html( $label ); ?></span></label>
							<?php endforeach; ?>
						</div>

						<div class="fcb-targets" data-hide-mode="all">
							<p><?php self::checkbox( self::name( 'front_page' ), $s['front_page'], 'صفحه اصلی سایت' ); ?></p>

							<span class="fcb-label">برگه‌ها</span>
							<input type="search" class="regular-text fcb-page-search" placeholder="جستجوی برگه...">
							<div class="fcb-pages">
								<?php
								$pages = get_pages(
									array(
										'sort_column' => 'menu_order,post_title',
										'post_status' => array( 'publish', 'private', 'draft' ),
									)
								);
								if ( ! $pages ) {
									echo '<p class="description">برگه‌ای وجود ندارد.</p>';
								}
								foreach ( $pages as $page ) :
									$depth = count( get_post_ancestors( $page ) );
									$title = $page->post_title ? $page->post_title : '(بدون عنوان)';
									?>
									<label style="<?php echo esc_attr( '--depth:' . $depth ); ?>">
										<input type="checkbox" name="<?php echo esc_attr( self::name( 'pages' ) ); ?>[]" value="<?php echo esc_attr( $page->ID ); ?>" <?php checked( in_array( (int) $page->ID, array_map( 'intval', (array) $s['pages'] ), true ) ); ?>>
										<span><?php echo esc_html( $title ); ?></span>
										<?php if ( 'publish' !== $page->post_status ) : ?>
											<em>(<?php echo esc_html( get_post_status_object( $page->post_status )->label ); ?>)</em>
										<?php endif; ?>
									</label>
								<?php endforeach; ?>
							</div>

							<p>
								<label for="fcb-post-ids" class="fcb-label">شناسه نوشته‌ها، محصولات یا هر نوع محتوای دیگر</label>
								<input id="fcb-post-ids" type="text" dir="ltr" class="regular-text" name="<?php echo esc_attr( self::name( 'post_ids' ) ); ?>" value="<?php echo esc_attr( $s['post_ids'] ); ?>" placeholder="12, 45, 108">
								<span class="description">شناسه‌ها را با کاما جدا کنید.</span>
							</p>
						</div>
					</div>
				</section>

				<div class="fcb-submit">
					<?php submit_button( 'ذخیره تنظیمات', 'primary large', 'submit', false ); ?>
				</div>
			</form>
		</div>
		<?php
	}
}
