<?php
/**
 * Settings defaults and accessors.
 *
 * @package WhatsApp_Chat_Button
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings manager.
 */
class WACB_Settings_Manager {

	/**
	 * Primary settings option name.
	 *
	 * @var string
	 */
	const OPTION_NAME = 'wacb_settings';

	/**
	 * Returns the settings option name.
	 *
	 * @return string
	 */
	public static function get_option_name() {
		return self::OPTION_NAME;
	}

	/**
	 * Returns default plugin settings.
	 *
	 * @return array<string, mixed>
	 */
	public static function get_defaults() {
		return array(
			'wacb_enabled'         => 0,
			'wacb_whatsapp_number' => '',
			'wacb_default_message' => '',
			'wacb_button_text'     => '',
			'wacb_button_color'    => '#25D366',
			'wacb_button_position' => 'right',
			'wacb_button_delay'    => 0,
		);
	}

	/**
	 * Returns normalized saved settings.
	 *
	 * @return array<string, mixed>
	 */
	public static function get_settings() {
		$saved_settings = get_option( self::get_option_name(), array() );

		if ( ! is_array( $saved_settings ) ) {
			$saved_settings = array();
		}

		return wp_parse_args( $saved_settings, self::get_defaults() );
	}
}
