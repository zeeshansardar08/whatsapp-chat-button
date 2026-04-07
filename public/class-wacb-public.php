<?php
/**
 * Public-facing functionality.
 *
 * @package WhatsApp_Chat_Button
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Public controller.
 */
class WACB_Public {

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
	 * Registers public styles when frontend assets are added.
	 *
	 * @return void
	 */
	public function enqueue_styles() {
	}

	/**
	 * Registers public scripts when frontend assets are added.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
	}
}
