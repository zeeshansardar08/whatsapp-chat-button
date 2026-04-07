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
	 * @param int    $object_id      Current queried object ID.
	 * @return string
	 */
	public function get_number_for_request( $default_number, $routing_rules = array(), $object_id = 0 ) {
		$grouped_rules = WACB_Settings_Manager::group_routing_rules_by_type( $routing_rules );
		$object_id     = absint( $object_id );

		$page_number = $this->get_page_rule_number( $grouped_rules['page'], $object_id );

		if ( '' !== $page_number ) {
			return $page_number;
		}

		$post_number = $this->get_post_rule_number( $grouped_rules['post'], $object_id );

		if ( '' !== $post_number ) {
			return $post_number;
		}

		$category_number = $this->get_category_rule_number( $grouped_rules['category'], $object_id );

		if ( '' !== $category_number ) {
			return $category_number;
		}

		$default_route_number = $this->get_default_rule_number( $grouped_rules['default'] );

		if ( '' !== $default_route_number ) {
			return $default_route_number;
		}

		return $this->sanitize_number( $default_number );
	}

	/**
	 * Resolves a matching page rule.
	 *
	 * @param array<int, array<string, int|string>> $rules Page rules.
	 * @param int                                   $object_id Current object ID.
	 * @return string
	 */
	private function get_page_rule_number( $rules, $object_id ) {
		if ( ! is_page() || $object_id <= 0 ) {
			return '';
		}

		foreach ( $rules as $rule ) {
			if ( $object_id === absint( $rule['target_id'] ) ) {
				return $this->sanitize_number( $rule['number'] ?? '' );
			}
		}

		return '';
	}

	/**
	 * Resolves a matching post rule.
	 *
	 * @param array<int, array<string, int|string>> $rules Post rules.
	 * @param int                                   $object_id Current object ID.
	 * @return string
	 */
	private function get_post_rule_number( $rules, $object_id ) {
		if ( ! is_singular( 'post' ) || $object_id <= 0 ) {
			return '';
		}

		foreach ( $rules as $rule ) {
			if ( $object_id === absint( $rule['target_id'] ) ) {
				return $this->sanitize_number( $rule['number'] ?? '' );
			}
		}

		return '';
	}

	/**
	 * Resolves a matching category rule.
	 *
	 * @param array<int, array<string, int|string>> $rules Category rules.
	 * @param int                                   $object_id Current object ID.
	 * @return string
	 */
	private function get_category_rule_number( $rules, $object_id ) {
		foreach ( $rules as $rule ) {
			$target_id = absint( $rule['target_id'] );

			if ( $target_id <= 0 ) {
				continue;
			}

			if ( is_category( $target_id ) ) {
				return $this->sanitize_number( $rule['number'] ?? '' );
			}

			if ( is_singular( 'post' ) && $object_id > 0 && has_category( $target_id, $object_id ) ) {
				return $this->sanitize_number( $rule['number'] ?? '' );
			}
		}

		return '';
	}

	/**
	 * Returns the default route number when available.
	 *
	 * @param array<int, array<string, int|string>> $rules Default rules.
	 * @return string
	 */
	private function get_default_rule_number( $rules ) {
		if ( empty( $rules[0] ) || ! is_array( $rules[0] ) ) {
			return '';
		}

		return $this->sanitize_number( $rules[0]['number'] ?? '' );
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
