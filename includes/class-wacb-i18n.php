<?php
/**
 * Internationalization functionality.
 *
 * @package WhatsApp_Chat_Button
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Text domain loader.
 */
class WACB_I18n {

	/**
	 * Loads the plugin text domain.
	 *
	 * @return void
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			'whatsapp-chat-button',
			false,
			dirname( WACB_PLUGIN_BASENAME ) . '/languages/'
		);
	}
}
