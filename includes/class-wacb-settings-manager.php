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
	 * Supported button positions.
	 *
	 * @var string[]
	 */
	const ALLOWED_POSITIONS = array( 'left', 'right' );

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
			'wacb_enabled'         => 1,
			'wacb_whatsapp_number' => '',
			'wacb_default_message' => '',
			'wacb_button_text'     => 'Chat on WhatsApp',
			'wacb_button_color'    => '#25D366',
			'wacb_button_position' => 'right',
			'wacb_button_delay'    => 0,
			'wacb_routing_rules'   => self::get_default_routing_rules(),
		);
	}

	/**
	 * Returns the default routing rules structure.
	 *
	 * @return array<int, array<string, string>>
	 */
	public static function get_default_routing_rules() {
		return array(
			array(
				'rule_type'  => 'default',
				'match_type' => 'sitewide',
				'label'      => 'All pages',
				'number'     => '',
			),
		);
	}

	/**
	 * Returns normalized saved settings.
	 *
	 * @return array<string, mixed>
	 */
	public static function get_settings() {
		$saved_settings = get_option( self::get_option_name(), array() );

		return self::normalize_settings( $saved_settings );
	}

	/**
	 * Sanitizes incoming settings values.
	 *
	 * @param mixed $settings Raw settings from the Settings API.
	 * @return array<string, mixed>
	 */
	public static function sanitize_settings( $settings ) {
		return self::normalize_settings( $settings );
	}

	/**
	 * Returns normalized settings based on the current schema.
	 *
	 * @param mixed $settings Raw settings array.
	 * @return array<string, mixed>
	 */
	public static function normalize_settings( $settings ) {
		$defaults = self::get_defaults();

		if ( ! is_array( $settings ) ) {
			$settings = array();
		}

		$position = isset( $settings['wacb_button_position'] ) ? sanitize_key( $settings['wacb_button_position'] ) : $defaults['wacb_button_position'];
		$color    = isset( $settings['wacb_button_color'] ) ? sanitize_hex_color( $settings['wacb_button_color'] ) : $defaults['wacb_button_color'];

		if ( ! in_array( $position, self::ALLOWED_POSITIONS, true ) ) {
			$position = $defaults['wacb_button_position'];
		}

		if ( empty( $color ) ) {
			$color = $defaults['wacb_button_color'];
		}

		return array(
			'wacb_enabled'         => empty( $settings['wacb_enabled'] ) ? 0 : 1,
			'wacb_whatsapp_number' => self::sanitize_whatsapp_number( $settings['wacb_whatsapp_number'] ?? $defaults['wacb_whatsapp_number'] ),
			'wacb_default_message' => sanitize_textarea_field( (string) ( $settings['wacb_default_message'] ?? $defaults['wacb_default_message'] ) ),
			'wacb_button_text'     => sanitize_text_field( (string) ( $settings['wacb_button_text'] ?? $defaults['wacb_button_text'] ) ),
			'wacb_button_color'    => $color,
			'wacb_button_position' => $position,
			'wacb_button_delay'    => absint( $settings['wacb_button_delay'] ?? $defaults['wacb_button_delay'] ),
			'wacb_routing_rules'   => self::normalize_routing_rules( $settings['wacb_routing_rules'] ?? $defaults['wacb_routing_rules'] ),
		);
	}

	/**
	 * Normalizes routing rules to the MVP schema.
	 *
	 * @param mixed $routing_rules Raw routing rules.
	 * @return array<int, array<string, string>>
	 */
	private static function normalize_routing_rules( $routing_rules ) {
		$default_rules = self::get_default_routing_rules();
		$default_rule  = $default_rules[0];

		if ( ! is_array( $routing_rules ) || empty( $routing_rules[0] ) || ! is_array( $routing_rules[0] ) ) {
			return $default_rules;
		}

		$rule = $routing_rules[0];

		$label = sanitize_text_field( (string) ( $rule['label'] ?? $default_rule['label'] ) );

		if ( '' === $label ) {
			$label = $default_rule['label'];
		}

		return array(
			array(
				'rule_type'  => $default_rule['rule_type'],
				'match_type' => $default_rule['match_type'],
				'label'      => $label,
				'number'     => self::sanitize_whatsapp_number( $rule['number'] ?? $default_rule['number'] ),
			),
		);
	}

	/**
	 * Sanitizes a WhatsApp number into wa.me-compatible digits.
	 *
	 * @param mixed $number Raw number input.
	 * @return string
	 */
	private static function sanitize_whatsapp_number( $number ) {
		$sanitized_number = preg_replace( '/[^0-9]/', '', (string) $number );

		return is_string( $sanitized_number ) ? $sanitized_number : '';
	}
}
