<?php
/**
 * Activation logic.
 *
 * @package WhatsApp_Chat_Button
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( is_readable( WACB_PLUGIN_DIR . 'includes/class-wacb-settings-manager.php' ) ) {
	require_once WACB_PLUGIN_DIR . 'includes/class-wacb-settings-manager.php';
}

if ( is_readable( WACB_PLUGIN_DIR . 'includes/class-wacb-tracking-engine.php' ) ) {
	require_once WACB_PLUGIN_DIR . 'includes/class-wacb-tracking-engine.php';
}

/**
 * Runs activation tasks.
 */
class WACB_Activator {

	/**
	 * Activates the plugin.
	 *
	 * @return void
	 */
	public static function activate() {
		self::maybe_seed_settings();
		self::maybe_create_clicks_table();

		update_option( 'wacb_version', WACB_VERSION );
		update_option( 'wacb_db_version', '0.1.0' );
	}

	/**
	 * Creates the base settings option if needed.
	 *
	 * @return void
	 */
	private static function maybe_seed_settings() {
		if ( ! class_exists( 'WACB_Settings_Manager' ) ) {
			return;
		}

		$current_settings = get_option( WACB_Settings_Manager::get_option_name(), array() );

		if ( ! is_array( $current_settings ) ) {
			$current_settings = array();
		}

		update_option(
			WACB_Settings_Manager::get_option_name(),
			WACB_Settings_Manager::normalize_settings( $current_settings )
		);
	}

	/**
	 * Creates the clicks table for analytics foundations.
	 *
	 * @return void
	 */
	private static function maybe_create_clicks_table() {
		global $wpdb;

		if ( ! class_exists( 'WACB_Tracking_Engine' ) || ! $wpdb instanceof wpdb ) {
			return;
		}

		$schema = WACB_Tracking_Engine::get_schema( $wpdb );

		if ( '' === $schema ) {
			return;
		}

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $schema );
	}
}
