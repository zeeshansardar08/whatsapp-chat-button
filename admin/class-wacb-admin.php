<?php
/**
 * Admin-facing functionality.
 *
 * @package WhatsApp_Chat_Button
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin controller.
 */
class WACB_Admin {

	/**
	 * Plugin slug.
	 *
	 * @var string
	 */
	private $plugin_name;

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	private $version;

	/**
	 * Constructor.
	 *
	 * @param string $plugin_name Plugin slug.
	 * @param string $version     Plugin version.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Registers admin styles when the settings UI is introduced.
	 *
	 * @return void
	 */
	public function enqueue_styles() {
	}

	/**
	 * Registers admin scripts when the settings UI is introduced.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
	}
}
