<?php
/**
 * Message variable replacement and chat link generation.
 *
 * @package WhatsApp_Chat_Button
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolves message variables for frontend use.
 */
class WACB_Message_Resolver {

	/**
	 * Resolves supported placeholders inside a message template.
	 *
	 * @param string $message_template Saved message template.
	 * @param int    $object_id        Current queried object ID.
	 * @return string
	 */
	public function resolve_template( $message_template, $object_id = 0 ) {
		$replacements = array(
			'{page_title}' => $this->get_page_title( $object_id ),
			'{url}'        => $this->get_current_url(),
			'{site_name}'  => $this->get_site_name(),
		);

		return strtr( (string) $message_template, $replacements );
	}

	/**
	 * Builds the final wa.me URL.
	 *
	 * @param string $whatsapp_number Resolved WhatsApp number.
	 * @param string $message         Resolved message content.
	 * @return string
	 */
	public function build_chat_url( $whatsapp_number, $message ) {
		$number = preg_replace( '/[^0-9]/', '', (string) $whatsapp_number );

		if ( ! is_string( $number ) || '' === $number ) {
			return '';
		}

		$chat_url = 'https://wa.me/' . $number;
		$message  = (string) $message;

		if ( '' !== $message ) {
			$chat_url = add_query_arg(
				array(
					'text' => $message,
				),
				$chat_url
			);
		}

		return esc_url_raw( $chat_url );
	}

	/**
	 * Returns the current page title when available.
	 *
	 * @param int $object_id Current queried object ID.
	 * @return string
	 */
	private function get_page_title( $object_id ) {
		$object_id = absint( $object_id );

		if ( $object_id > 0 && ( is_singular() || is_page() ) ) {
			return wp_strip_all_tags( get_the_title( $object_id ) );
		}

		if ( is_home() && (int) get_option( 'page_for_posts' ) > 0 ) {
			return wp_strip_all_tags( get_the_title( (int) get_option( 'page_for_posts' ) ) );
		}

		if ( is_category() || is_tag() || is_tax() ) {
			return wp_strip_all_tags( single_term_title( '', false ) );
		}

		if ( is_post_type_archive() ) {
			return wp_strip_all_tags( post_type_archive_title( '', false ) );
		}

		return '';
	}

	/**
	 * Returns the current request URL.
	 *
	 * @return string
	 */
	private function get_current_url() {
		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '/';

		if ( ! is_string( $request_uri ) || '' === $request_uri ) {
			$request_uri = '/';
		}

		return esc_url_raw( home_url( '/' . ltrim( $request_uri, '/' ) ) );
	}

	/**
	 * Returns the site name.
	 *
	 * @return string
	 */
	private function get_site_name() {
		return sanitize_text_field( get_bloginfo( 'name', 'display' ) );
	}
}
