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
	 * Returns the default number until routing rules are introduced.
	 *
	 * @param string $default_number Default configured number.
	 * @param int    $object_id      Optional object ID for future routing logic.
	 * @return string
	 */
	public function get_number_for_request( $default_number, $object_id = 0 ) {
		$sanitized_number = preg_replace( '/[^0-9]/', '', (string) $default_number );

		unset( $object_id );

		return is_string( $sanitized_number ) ? $sanitized_number : '';
	}
}
