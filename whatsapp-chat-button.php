<?php
/**
 * Plugin Name:       WhatsApp Chat Button
 * Description:       Convert visitors into WhatsApp leads with a customizable floating chat button.
 * Version:           0.1.0
 * Author:            Zignites
 * Text Domain:       whatsapp-chat-button
 * Domain Path:       /languages
 *
 * @package WhatsApp_Chat_Button
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'wacb_define_constants' ) ) {
	/**
	 * Defines plugin-wide constants.
	 *
	 * @return void
	 */
	function wacb_define_constants() {
		if ( ! defined( 'WACB_VERSION' ) ) {
			define( 'WACB_VERSION', '0.1.0' );
		}

		if ( ! defined( 'WACB_PLUGIN_FILE' ) ) {
			define( 'WACB_PLUGIN_FILE', __FILE__ );
		}

		if ( ! defined( 'WACB_PLUGIN_BASENAME' ) ) {
			define( 'WACB_PLUGIN_BASENAME', plugin_basename( WACB_PLUGIN_FILE ) );
		}

		if ( ! defined( 'WACB_PLUGIN_DIR' ) ) {
			define( 'WACB_PLUGIN_DIR', plugin_dir_path( WACB_PLUGIN_FILE ) );
		}

		if ( ! defined( 'WACB_PLUGIN_URL' ) ) {
			define( 'WACB_PLUGIN_URL', plugin_dir_url( WACB_PLUGIN_FILE ) );
		}
	}
}

if ( ! function_exists( 'wacb_require_file' ) ) {
	/**
	 * Safely loads a plugin file if it exists and is readable.
	 *
	 * @param string $relative_path Plugin-relative file path.
	 * @return bool
	 */
	function wacb_require_file( $relative_path ) {
		$file_path = WACB_PLUGIN_DIR . ltrim( $relative_path, '/\\' );

		if ( ! is_readable( $file_path ) ) {
			return false;
		}

		require_once $file_path;

		return true;
	}
}

if ( ! function_exists( 'wacb_activate' ) ) {
	/**
	 * Runs plugin activation logic.
	 *
	 * @param bool $network_wide Whether the plugin is being network-activated.
	 * @return void
	 */
	function wacb_activate( $network_wide = false ) {
		wacb_define_constants();

		if ( ! wacb_require_file( 'includes/class-wacb-activator.php' ) ) {
			return;
		}

		WACB_Activator::activate( $network_wide );
	}
}

if ( ! function_exists( 'wacb_run' ) ) {
	/**
	 * Boots the core plugin class.
	 *
	 * @return void
	 */
	function wacb_run() {
		wacb_define_constants();

		if ( ! wacb_require_file( 'includes/class-wacb.php' ) || ! class_exists( 'WACB' ) ) {
			return;
		}

		$plugin = new WACB();
		$plugin->run();
	}
}

register_activation_hook( __FILE__, 'wacb_activate' );

wacb_run();
