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
	 * Cached button data.
	 *
	 * @var array<string, mixed>|false|null
	 */
	private $button_data;

	/**
	 * Constructor.
	 *
	 * @param string $plugin_name Plugin slug.
	 * @param string $version     Plugin version.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->button_data = null;
	}

	/**
	 * Registers public styles when frontend assets are added.
	 *
	 * @return void
	 */
	public function enqueue_styles() {
		$button_data = $this->get_button_data();

		if ( false === $button_data ) {
			return;
		}

		wp_enqueue_style(
			'wacb-public',
			WACB_PLUGIN_URL . 'assets/css/wacb-public.css',
			array(),
			$this->version
		);
	}

	/**
	 * Registers public scripts when frontend assets are added.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		$button_data = $this->get_button_data();

		if ( false === $button_data ) {
			return;
		}

		wp_enqueue_script(
			'wacb-public',
			WACB_PLUGIN_URL . 'assets/js/wacb-public.js',
			array(),
			$this->version,
			true
		);

		wp_add_inline_script(
			'wacb-public',
			'window.wacbPublic = ' . wp_json_encode(
				array(
					'ajaxUrl' => admin_url( 'admin-ajax.php' ),
					'action'  => 'wacb_track_click',
					'nonce'   => wp_create_nonce( 'wacb_track_click' ),
				)
			) . ';',
			'before'
		);
	}

	/**
	 * Renders the floating chat button.
	 *
	 * @return void
	 */
	public function render_button() {
		$button_data = $this->get_button_data();

		if ( false === $button_data ) {
			return;
		}

		$view_path = WACB_PLUGIN_DIR . 'public/views/button.php';

		if ( is_readable( $view_path ) ) {
			require $view_path;
		}
	}

	/**
	 * Handles a tracked click request.
	 *
	 * @return void
	 */
	public function handle_track_click() {
		if ( ! check_ajax_referer( 'wacb_track_click', 'nonce', false ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid tracking request.', 'whatsapp-chat-button' ),
				),
				403
			);
		}

		$page_url = isset( $_POST['page_url'] ) ? wp_unslash( $_POST['page_url'] ) : '';

		if ( ! is_string( $page_url ) || '' === $page_url ) {
			wp_send_json_error(
				array(
					'message' => __( 'Missing tracking payload.', 'whatsapp-chat-button' ),
				),
				400
			);
		}

		$inserted = WACB_Tracking_Engine::insert_click( $page_url, WACB_Tracking_Engine::detect_device() );

		if ( ! $inserted ) {
			wp_send_json_error(
				array(
					'message' => __( 'Unable to store the click event.', 'whatsapp-chat-button' ),
				),
				500
			);
		}

		wp_send_json_success();
	}

	/**
	 * Returns resolved button data when the button can be rendered.
	 *
	 * @return array<string, mixed>|false
	 */
	private function get_button_data() {
		if ( null !== $this->button_data ) {
			return $this->button_data;
		}

		$this->button_data = false;

		if ( is_admin() || wp_doing_ajax() || is_feed() || is_embed() || wp_is_json_request() ) {
			return $this->button_data;
		}

		$settings = WACB_Settings_Manager::get_settings();

		if ( empty( $settings['wacb_enabled'] ) ) {
			return $this->button_data;
		}

		$object_id        = get_queried_object_id();
		$routing_engine   = new WACB_Routing_Engine();
		$message_resolver = new WACB_Message_Resolver();
		$number           = $routing_engine->get_number_for_request(
			$settings['wacb_whatsapp_number'],
			$settings['wacb_routing_rules'],
			$object_id
		);

		if ( '' === $number ) {
			return $this->button_data;
		}

		$message  = $message_resolver->resolve_template( $settings['wacb_default_message'], $object_id );
		$chat_url = $message_resolver->build_chat_url( $number, $message );

		if ( '' === $chat_url ) {
			return $this->button_data;
		}

		$defaults    = WACB_Settings_Manager::get_defaults();
		$button_text = (string) $settings['wacb_button_text'];

		if ( '' === $button_text ) {
			$button_text = $defaults['wacb_button_text'];
		}

		$position   = in_array( $settings['wacb_button_position'], WACB_Settings_Manager::ALLOWED_POSITIONS, true ) ? $settings['wacb_button_position'] : $defaults['wacb_button_position'];
		$background = (string) $settings['wacb_button_color'];
		$delay      = absint( $settings['wacb_button_delay'] );

		$this->button_data = array(
			'chat_url'            => $chat_url,
			'button_text'         => $button_text,
			'aria_label'          => sprintf(
				/* translators: %s: button text. */
				__( 'Open WhatsApp chat: %s', 'whatsapp-chat-button' ),
				$button_text
			),
			'position'            => $position,
			'background_color'    => $background,
			'text_color'          => $this->get_contrast_text_color( $background ),
			'delay'               => $delay,
			'page_url'            => $message_resolver->get_current_url(),
			'tracking_data_label' => 'chat-button',
		);

		return $this->button_data;
	}

	/**
	 * Returns a readable text color for the configured button background.
	 *
	 * @param string $background_color Hexadecimal color.
	 * @return string
	 */
	private function get_contrast_text_color( $background_color ) {
		$normalized_color = ltrim( (string) $background_color, '#' );

		if ( 3 === strlen( $normalized_color ) ) {
			$normalized_color = $normalized_color[0] . $normalized_color[0] . $normalized_color[1] . $normalized_color[1] . $normalized_color[2] . $normalized_color[2];
		}

		if ( 6 !== strlen( $normalized_color ) || ! ctype_xdigit( $normalized_color ) ) {
			return '#FFFFFF';
		}

		$red   = hexdec( substr( $normalized_color, 0, 2 ) );
		$green = hexdec( substr( $normalized_color, 2, 2 ) );
		$blue  = hexdec( substr( $normalized_color, 4, 2 ) );

		$luminance = ( ( 0.299 * $red ) + ( 0.587 * $green ) + ( 0.114 * $blue ) ) / 255;

		return $luminance > 0.6 ? '#111111' : '#FFFFFF';
	}
}
