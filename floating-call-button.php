<?php
/**
 * Plugin Name:       Floating Call Button
 * Plugin URI:        https://github.com/mmhdih/Floating-Call-Button
 * Description:       Adds a floating contact button to all or selected pages. Clicking it opens your contact channels: phone, WhatsApp, Telegram, Instagram, email and more.
 * Version:           1.2.0
 * Requires at least: 5.6
 * Requires PHP:      7.2
 * Author:            Mahdi Habibi | Tavoos Web
 * Author URI:        https://tavoosweb.ir/
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       floating-call-button
 * Domain Path:       /languages
 *
 * @package Floating_Call_Button
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TAVOOS_FCB_VERSION', '1.2.0' );
define( 'TAVOOS_FCB_FILE', __FILE__ );
define( 'TAVOOS_FCB_DIR', plugin_dir_path( __FILE__ ) );
define( 'TAVOOS_FCB_URL', plugin_dir_url( __FILE__ ) );
define( 'TAVOOS_FCB_OPTION', 'tavoos_fcb_settings' );

require_once TAVOOS_FCB_DIR . 'includes/icons.php';
require_once TAVOOS_FCB_DIR . 'includes/class-tavoos-fcb-options.php';
require_once TAVOOS_FCB_DIR . 'includes/class-tavoos-fcb-frontend.php';

/**
 * Load the bundled translations (used until a language pack is available).
 */
function tavoos_fcb_load_textdomain() {
	load_plugin_textdomain( 'floating-call-button', false, dirname( plugin_basename( TAVOOS_FCB_FILE ) ) . '/languages' );
}
add_action( 'init', 'tavoos_fcb_load_textdomain' );

if ( is_admin() ) {
	require_once TAVOOS_FCB_DIR . 'includes/class-tavoos-fcb-admin.php';
	Tavoos_FCB_Admin::init();
}

Tavoos_FCB_Frontend::init();

add_action( 'plugins_loaded', array( 'Tavoos_FCB_Options', 'maybe_migrate' ) );
register_activation_hook( __FILE__, array( 'Tavoos_FCB_Options', 'activate' ) );
