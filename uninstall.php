<?php
/**
 * حذف تنظیمات افزونه هنگام پاک کردن آن.
 *
 * @package Floating_Call_Button
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'fcb_settings' );
