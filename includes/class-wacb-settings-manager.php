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
	 * Supported routing rule types.
	 *
	 * @var string[]
	 */
	const ALLOWED_RULE_TYPES = array( 'page', 'post', 'category', 'default' );

	/**
	 * Form context for the settings page.
	 *
	 * @var string
	 */
	const FORM_CONTEXT_SETTINGS = 'settings';

	/**
	 * Form context for the routing rules page.
	 *
	 * @var string
	 */
	const FORM_CONTEXT_ROUTING = 'routing';

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
	 * @return array<int, array<string, int|string>>
	 */
	public static function get_default_routing_rules() {
		return array(
			array(
				'rule_type' => 'default',
				'target_id' => 0,
				'label'     => 'Default fallback',
				'number'    => '',
			),
		);
	}

	/**
	 * Returns a blank routing rule shape.
	 *
	 * @return array<string, int|string>
	 */
	public static function get_empty_routing_rule() {
		return array(
			'rule_type' => 'page',
			'target_id' => 0,
			'label'     => '',
			'number'    => '',
		);
	}

	/**
	 * Returns the supported routing rule type labels.
	 *
	 * @return array<string, string>
	 */
	public static function get_rule_type_labels() {
		return array(
			'page'     => __( 'Page', 'whatsapp-chat-button' ),
			'post'     => __( 'Post', 'whatsapp-chat-button' ),
			'category' => __( 'Category', 'whatsapp-chat-button' ),
			'default'  => __( 'Default', 'whatsapp-chat-button' ),
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
	 * Returns normalized routing rules for downstream use.
	 *
	 * @return array<int, array<string, int|string>>
	 */
	public static function get_routing_rules() {
		$settings = self::get_settings();

		return self::normalize_routing_rules( $settings['wacb_routing_rules'] );
	}

	/**
	 * Returns only the specific routing rules.
	 *
	 * @param array<int, array<string, int|string>>|null $routing_rules Optional routing rules array.
	 * @return array<int, array<string, int|string>>
	 */
	public static function get_specific_routing_rules( $routing_rules = null ) {
		if ( null === $routing_rules ) {
			$routing_rules = self::get_routing_rules();
		}

		$specific_rules = array();

		foreach ( self::normalize_routing_rules( $routing_rules ) as $rule ) {
			if ( 'default' === $rule['rule_type'] ) {
				continue;
			}

			$specific_rules[] = $rule;
		}

		return $specific_rules;
	}

	/**
	 * Returns the default fallback routing rule.
	 *
	 * @param array<int, array<string, int|string>>|null $routing_rules Optional routing rules array.
	 * @return array<string, int|string>
	 */
	public static function get_default_routing_rule( $routing_rules = null ) {
		$default_rule = self::get_default_routing_rules()[0];

		if ( null === $routing_rules ) {
			$routing_rules = self::get_routing_rules();
		}

		foreach ( self::normalize_routing_rules( $routing_rules ) as $rule ) {
			if ( 'default' === $rule['rule_type'] ) {
				$default_rule = $rule;
				break;
			}
		}

		return $default_rule;
	}

	/**
	 * Sanitizes incoming settings values.
	 *
	 * @param mixed $settings Raw settings from the Settings API.
	 * @return array<string, mixed>
	 */
	public static function sanitize_settings( $settings ) {
		return self::normalize_settings( self::merge_partial_settings( $settings ) );
	}

	/**
	 * Returns whether a routing rule type is supported.
	 *
	 * @param string $rule_type Rule type.
	 * @return bool
	 */
	public static function is_allowed_rule_type( $rule_type ) {
		return in_array( $rule_type, self::ALLOWED_RULE_TYPES, true );
	}

	/**
	 * Returns a normalized routing rule.
	 *
	 * @param mixed $rule Raw routing rule.
	 * @return array<string, int|string>
	 */
	public static function normalize_routing_rule( $rule ) {
		$defaults = self::get_empty_routing_rule();

		if ( ! is_array( $rule ) ) {
			$rule = array();
		}

		$rule_type = isset( $rule['rule_type'] ) ? sanitize_key( (string) $rule['rule_type'] ) : $defaults['rule_type'];

		if ( ! self::is_allowed_rule_type( $rule_type ) ) {
			$rule_type = $defaults['rule_type'];
		}

		$target_id = 0;

		if ( 'page' === $rule_type ) {
			$target_id = absint( $rule['target_id_page'] ?? $rule['target_id'] ?? 0 );
		} elseif ( 'post' === $rule_type ) {
			$target_id = absint( $rule['target_id_post'] ?? $rule['target_id'] ?? 0 );
		} elseif ( 'category' === $rule_type ) {
			$target_id = absint( $rule['target_id_category'] ?? $rule['target_id'] ?? 0 );
		}

		$normalized_rule = array(
			'rule_type' => $rule_type,
			'target_id' => $target_id,
			'label'     => sanitize_text_field( (string) ( $rule['label'] ?? '' ) ),
			'number'    => self::sanitize_whatsapp_number( $rule['number'] ?? '' ),
		);

		if ( 'default' === $rule_type ) {
			$normalized_rule['target_id'] = 0;
		}

		if ( '' === $normalized_rule['label'] ) {
			$normalized_rule['label'] = self::get_default_rule_label( $normalized_rule['rule_type'] );
		}

		return $normalized_rule;
	}

	/**
	 * Returns the default label for a rule type.
	 *
	 * @param string $rule_type Rule type.
	 * @return string
	 */
	public static function get_default_rule_label( $rule_type ) {
		$labels = self::get_rule_type_labels();

		if ( isset( $labels[ $rule_type ] ) ) {
			if ( 'default' === $rule_type ) {
				return __( 'Default fallback', 'whatsapp-chat-button' );
			}

			return sprintf(
				/* translators: %s: rule type label. */
				__( '%s rule', 'whatsapp-chat-button' ),
				$labels[ $rule_type ]
			);
		}

		return __( 'Routing rule', 'whatsapp-chat-button' );
	}

	/**
	 * Groups routing rules by type for evaluation.
	 *
	 * @param array<int, array<string, int|string>> $routing_rules Routing rules.
	 * @return array<string, array<int, array<string, int|string>>>
	 */
	public static function group_routing_rules_by_type( $routing_rules ) {
		$grouped_rules = array(
			'page'     => array(),
			'post'     => array(),
			'category' => array(),
			'default'  => array(),
		);

		foreach ( self::normalize_routing_rules( $routing_rules ) as $rule ) {
			$grouped_rules[ $rule['rule_type'] ][] = $rule;
		}

		return $grouped_rules;
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
	 * @return array<int, array<string, int|string>>
	 */
	public static function normalize_routing_rules( $routing_rules ) {
		$normalized_rules = array();
		$default_rule     = self::get_default_routing_rules()[0];

		if ( is_array( $routing_rules ) ) {
			foreach ( $routing_rules as $rule ) {
				$normalized_rule = self::normalize_routing_rule( $rule );

				if ( 'default' === $normalized_rule['rule_type'] ) {
					$default_rule = $normalized_rule;
					continue;
				}

				if ( empty( $normalized_rule['target_id'] ) || '' === $normalized_rule['number'] ) {
					continue;
				}

				$normalized_rules[] = $normalized_rule;
			}
		}

		$normalized_rules[] = $default_rule;

		return array_values( $normalized_rules );
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

	/**
	 * Merges a partial settings submission with the saved option.
	 *
	 * @param mixed $settings Raw submitted settings.
	 * @return array<string, mixed>
	 */
	private static function merge_partial_settings( $settings ) {
		$current_settings = self::get_settings();

		if ( ! is_array( $settings ) ) {
			return $current_settings;
		}

		$form_context = isset( $settings['wacb_form_context'] ) ? sanitize_key( (string) $settings['wacb_form_context'] ) : '';
		$merged       = $current_settings;
		$setting_keys = array_keys( self::get_defaults() );

		foreach ( $setting_keys as $setting_key ) {
			if ( 'wacb_routing_rules' === $setting_key ) {
				continue;
			}

			if ( array_key_exists( $setting_key, $settings ) ) {
				$merged[ $setting_key ] = $settings[ $setting_key ];
			}
		}

		if ( self::FORM_CONTEXT_SETTINGS === $form_context ) {
			$merged['wacb_routing_rules'] = self::merge_settings_page_routing_rules(
				$current_settings['wacb_routing_rules'],
				$settings['wacb_routing_rules'] ?? array()
			);

			return $merged;
		}

		if ( self::FORM_CONTEXT_ROUTING === $form_context ) {
			$merged['wacb_routing_rules'] = self::merge_routing_page_rules(
				$current_settings['wacb_routing_rules'],
				$settings['wacb_routing_rules'] ?? array()
			);

			return $merged;
		}

		if ( array_key_exists( 'wacb_routing_rules', $settings ) ) {
			$merged['wacb_routing_rules'] = $settings['wacb_routing_rules'];
		}

		return $merged;
	}

	/**
	 * Merges the settings page default fallback submission.
	 *
	 * @param array<int, array<string, int|string>> $existing_rules Current routing rules.
	 * @param mixed                                 $submitted_rules Submitted routing rules.
	 * @return array<int, array<string, int|string>>
	 */
	private static function merge_settings_page_routing_rules( $existing_rules, $submitted_rules ) {
		$specific_rules = self::get_specific_routing_rules( $existing_rules );
		$default_rule   = self::get_default_routing_rule( $submitted_rules );

		$specific_rules[] = $default_rule;

		return array_values( $specific_rules );
	}

	/**
	 * Merges the routing page submission while preserving the default fallback rule.
	 *
	 * @param array<int, array<string, int|string>> $existing_rules Current routing rules.
	 * @param mixed                                 $submitted_rules Submitted routing rules.
	 * @return array<int, array<string, int|string>>
	 */
	private static function merge_routing_page_rules( $existing_rules, $submitted_rules ) {
		$normalized_rules = array();
		$default_rule     = self::get_default_routing_rule( $existing_rules );

		if ( is_array( $submitted_rules ) ) {
			foreach ( $submitted_rules as $rule ) {
				$normalized_rule = self::normalize_routing_rule( $rule );

				if ( 'default' === $normalized_rule['rule_type'] ) {
					continue;
				}

				if ( empty( $normalized_rule['target_id'] ) || '' === $normalized_rule['number'] ) {
					continue;
				}

				$normalized_rules[] = $normalized_rule;
			}
		}

		$normalized_rules[] = $default_rule;

		return array_values( $normalized_rules );
	}
}
