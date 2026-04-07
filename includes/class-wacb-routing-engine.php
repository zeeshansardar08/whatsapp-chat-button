<?php
/**
 * Routing logic foundation.
 *
 * @package WhatsApp_Chat_Button
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolves the WhatsApp destination number.
 */
class WACB_Routing_Engine {

	/**
	 * Returns the best available number for the current request.
	 *
	 * @param string $default_number Default configured number.
	 * @param array  $routing_rules  Stored routing rules.
	 * @param int    $object_id      Optional object ID for future routing logic.
	 * @return string
	 */
	public function get_number_for_request( $default_number, $routing_rules = array(), $object_id = 0 ) {
		unset( $object_id );

		$route_number = $this->get_default_route_number( $routing_rules );

		if ( '' !== $route_number ) {
			return $route_number;
		}

		return $this->sanitize_number( $default_number );
	}

	/**
	 * Returns the saved default route number when available.
	 *
	 * @param array $routing_rules Stored routing rules.
	 * @return string
	 */
	private function get_default_route_number( $routing_rules ) {
		if ( ! is_array( $routing_rules ) || empty( $routing_rules[0] ) || ! is_array( $routing_rules[0] ) ) {
			return '';
		}

		$default_rule = $routing_rules[0];
		$rule_type    = isset( $default_rule['rule_type'] ) ? sanitize_key( $default_rule['rule_type'] ) : '';
		$match_type   = isset( $default_rule['match_type'] ) ? sanitize_key( $default_rule['match_type'] ) : '';

		if ( 'default' !== $rule_type || 'sitewide' !== $match_type ) {
			return '';
		}

		return $this->sanitize_number( $default_rule['number'] ?? '' );
	}

	/**
	 * Sanitizes a WhatsApp number into digits only.
	 *
	 * @param mixed $number Raw number input.
	 * @return string
	 */
	private function sanitize_number( $number ) {
		$sanitized_number = preg_replace( '/[^0-9]/', '', (string) $number );

		return is_string( $sanitized_number ) ? $sanitized_number : '';
	}
}
