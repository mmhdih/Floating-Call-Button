<?php
/**
 * Remove the plugin settings when the plugin is deleted.
 *
 * @package Floating_Call_Button
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'tavoos_fcb_settings' );
delete_option( 'fcb_settings' );
