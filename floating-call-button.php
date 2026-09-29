<?php
/**
 * Plugin Name:       دکمه شناور تماس (Floating Call Button)
 * Plugin URI:        https://tavoosweb.ir/
 * Description:       یک دکمه شناور به همه صفحات یا صفحات دلخواه اضافه می‌کند که با کلیک روی آن، کانال‌های ارتباطی (تماس، واتساپ، تلگرام و ...) نمایش داده می‌شوند. طراحی شده توسط <a href="https://tavoosweb.ir/" target="_blank">مهدی حبیبی | طاووس وب</a>
 * Version:           1.0.0
 * Requires at least: 5.6
 * Requires PHP:      7.2
 * Author:            مهدی حبیبی | طاووس وب
 * Author URI:        https://tavoosweb.ir/
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       floating-call-button
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FCB_VERSION', '1.0.0' );
define( 'FCB_FILE', __FILE__ );
define( 'FCB_DIR', plugin_dir_path( __FILE__ ) );
define( 'FCB_URL', plugin_dir_url( __FILE__ ) );
define( 'FCB_OPTION', 'fcb_settings' );

require_once FCB_DIR . 'includes/icons.php';
require_once FCB_DIR . 'includes/class-fcb-options.php';
require_once FCB_DIR . 'includes/class-fcb-frontend.php';

if ( is_admin() ) {
	require_once FCB_DIR . 'includes/class-fcb-admin.php';
	FCB_Admin::init();
}

FCB_Frontend::init();

register_activation_hook( __FILE__, array( 'FCB_Options', 'activate' ) );
