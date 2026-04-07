<?php
/**
 * Core plugin bootstrap class.
 *
 * @package WhatsApp_Chat_Button
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Core plugin class.
 */
class WACB {

	/**
	 * Hook loader.
	 *
	 * @var WACB_Loader|null
	 */
	protected $loader;

	/**
	 * Plugin slug.
	 *
	 * @var string
	 */
	protected $plugin_name;

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	protected $version;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->plugin_name = 'whatsapp-chat-button';
		$this->version     = defined( 'WACB_VERSION' ) ? WACB_VERSION : '0.1.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Loads required class files.
	 *
	 * @return void
	 */
	private function load_dependencies() {
		$required_files = array(
			'includes/class-wacb-loader.php',
			'includes/class-wacb-i18n.php',
			'includes/class-wacb-settings-manager.php',
			'includes/class-wacb-routing-engine.php',
			'includes/class-wacb-tracking-engine.php',
			'admin/class-wacb-admin.php',
			'public/class-wacb-public.php',
		);

		foreach ( $required_files as $required_file ) {
			$file_path = WACB_PLUGIN_DIR . $required_file;

			if ( ! is_readable( $file_path ) ) {
				return;
			}

			require_once $file_path;
		}

		$this->loader = new WACB_Loader();
	}

	/**
	 * Registers localization hooks.
	 *
	 * @return void
	 */
	private function set_locale() {
		if ( ! $this->loader instanceof WACB_Loader ) {
			return;
		}

		$plugin_i18n = new WACB_I18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Registers admin hooks.
	 *
	 * @return void
	 */
	private function define_admin_hooks() {
		if ( ! $this->loader instanceof WACB_Loader ) {
			return;
		}

		$plugin_admin = new WACB_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
	}

	/**
	 * Registers public hooks.
	 *
	 * @return void
	 */
	private function define_public_hooks() {
		if ( ! $this->loader instanceof WACB_Loader ) {
			return;
		}

		$plugin_public = new WACB_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
	}

	/**
	 * Runs the plugin.
	 *
	 * @return void
	 */
	public function run() {
		if ( $this->loader instanceof WACB_Loader ) {
			$this->loader->run();
		}
	}

	/**
	 * Returns the plugin slug.
	 *
	 * @return string
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Returns the current plugin version.
	 *
	 * @return string
	 */
	public function get_version() {
		return $this->version;
	}
}
