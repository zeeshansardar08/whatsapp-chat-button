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
	 * Settings page slug.
	 *
	 * @var string
	 */
	const PAGE_SLUG = 'wacb-settings';

	/**
	 * Settings page identifier.
	 *
	 * @var string
	 */
	const SETTINGS_PAGE = 'wacb-settings';

	/**
	 * Settings group identifier.
	 *
	 * @var string
	 */
	const SETTINGS_GROUP = 'wacb_settings_group';

	/**
	 * Settings tab identifier.
	 *
	 * @var string
	 */
	const TAB_SETTINGS = 'settings';

	/**
	 * Analytics tab identifier.
	 *
	 * @var string
	 */
	const TAB_ANALYTICS = 'analytics';

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
	 * Settings screen hook suffix.
	 *
	 * @var string
	 */
	private $settings_page_hook_suffix = '';

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
	public function enqueue_styles( $hook_suffix = '' ) {
		if ( $this->settings_page_hook_suffix !== $hook_suffix ) {
			return;
		}
	}

	/**
	 * Registers admin scripts when the settings UI is introduced.
	 *
	 * @return void
	 */
	public function enqueue_scripts( $hook_suffix = '' ) {
		if ( $this->settings_page_hook_suffix !== $hook_suffix ) {
			return;
		}
	}

	/**
	 * Registers the plugin settings page.
	 *
	 * @return void
	 */
	public function register_admin_menu() {
		$this->settings_page_hook_suffix = add_options_page(
			__( 'WhatsApp Chat Button', 'whatsapp-chat-button' ),
			__( 'WhatsApp Chat Button', 'whatsapp-chat-button' ),
			'manage_options',
			self::PAGE_SLUG,
			array( $this, 'display_plugin_page' )
		);
	}

	/**
	 * Registers settings, sections, and fields.
	 *
	 * @return void
	 */
	public function register_settings() {
		register_setting(
			self::SETTINGS_GROUP,
			WACB_Settings_Manager::get_option_name(),
			array(
				'type'              => 'array',
				'sanitize_callback' => array( 'WACB_Settings_Manager', 'sanitize_settings' ),
				'default'           => WACB_Settings_Manager::get_defaults(),
			)
		);

		add_settings_section(
			'wacb_general_section',
			__( 'General', 'whatsapp-chat-button' ),
			array( $this, 'render_general_section' ),
			self::SETTINGS_PAGE
		);

		add_settings_field(
			'wacb_enabled',
			__( 'Enable plugin', 'whatsapp-chat-button' ),
			array( $this, 'render_enabled_field' ),
			self::SETTINGS_PAGE,
			'wacb_general_section'
		);

		add_settings_field(
			'wacb_whatsapp_number',
			__( 'Primary WhatsApp number', 'whatsapp-chat-button' ),
			array( $this, 'render_whatsapp_number_field' ),
			self::SETTINGS_PAGE,
			'wacb_general_section'
		);

		add_settings_field(
			'wacb_default_message',
			__( 'Default pre-filled message', 'whatsapp-chat-button' ),
			array( $this, 'render_default_message_field' ),
			self::SETTINGS_PAGE,
			'wacb_general_section'
		);

		add_settings_section(
			'wacb_button_design_section',
			__( 'Button Design', 'whatsapp-chat-button' ),
			array( $this, 'render_button_design_section' ),
			self::SETTINGS_PAGE
		);

		add_settings_field(
			'wacb_button_text',
			__( 'Button text', 'whatsapp-chat-button' ),
			array( $this, 'render_button_text_field' ),
			self::SETTINGS_PAGE,
			'wacb_button_design_section'
		);

		add_settings_field(
			'wacb_button_position',
			__( 'Position', 'whatsapp-chat-button' ),
			array( $this, 'render_button_position_field' ),
			self::SETTINGS_PAGE,
			'wacb_button_design_section'
		);

		add_settings_field(
			'wacb_button_color',
			__( 'Button color', 'whatsapp-chat-button' ),
			array( $this, 'render_button_color_field' ),
			self::SETTINGS_PAGE,
			'wacb_button_design_section'
		);

		add_settings_field(
			'wacb_button_delay',
			__( 'Show delay', 'whatsapp-chat-button' ),
			array( $this, 'render_button_delay_field' ),
			self::SETTINGS_PAGE,
			'wacb_button_design_section'
		);

		add_settings_section(
			'wacb_variables_section',
			__( 'Smart Message Variables', 'whatsapp-chat-button' ),
			array( $this, 'render_variables_section' ),
			self::SETTINGS_PAGE
		);

		add_settings_field(
			'wacb_supported_variables',
			__( 'Supported placeholders', 'whatsapp-chat-button' ),
			array( $this, 'render_supported_variables_field' ),
			self::SETTINGS_PAGE,
			'wacb_variables_section'
		);

		add_settings_section(
			'wacb_routing_section',
			__( 'Routing Foundation', 'whatsapp-chat-button' ),
			array( $this, 'render_routing_section' ),
			self::SETTINGS_PAGE
		);

		add_settings_field(
			'wacb_default_route',
			__( 'Default route', 'whatsapp-chat-button' ),
			array( $this, 'render_default_route_field' ),
			self::SETTINGS_PAGE,
			'wacb_routing_section'
		);
	}

	/**
	 * Renders the plugin admin page.
	 *
	 * @return void
	 */
	public function display_plugin_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'whatsapp-chat-button' ) );
		}

		$active_tab        = $this->get_active_tab();
		$tabs              = $this->get_tabs();
		$page_slug         = self::PAGE_SLUG;
		$settings_group    = self::SETTINGS_GROUP;
		$settings_page     = self::SETTINGS_PAGE;
		$analytics_summary = $this->get_analytics_summary();
		$view_path         = WACB_PLUGIN_DIR . 'admin/views/settings-page.php';

		if ( is_readable( $view_path ) ) {
			require $view_path;
		}
	}

	/**
	 * Renders the general section description.
	 *
	 * @return void
	 */
	public function render_general_section() {
		echo '<p>' . esc_html__( 'Configure the primary WhatsApp number and the default message template used by the plugin.', 'whatsapp-chat-button' ) . '</p>';
	}

	/**
	 * Renders the button design section description.
	 *
	 * @return void
	 */
	public function render_button_design_section() {
		echo '<p>' . esc_html__( 'These design settings will be used by the frontend button in a later phase.', 'whatsapp-chat-button' ) . '</p>';
	}

	/**
	 * Renders the variables section description.
	 *
	 * @return void
	 */
	public function render_variables_section() {
		echo '<p>' . esc_html__( 'Use placeholders in the default message to personalize chats without hard-coding page details.', 'whatsapp-chat-button' ) . '</p>';
	}

	/**
	 * Renders the routing section description.
	 *
	 * @return void
	 */
	public function render_routing_section() {
		echo '<p>' . esc_html__( 'This phase stores a stable default routing structure so page-based and taxonomy-based rules can be added later without changing the option schema.', 'whatsapp-chat-button' ) . '</p>';
	}

	/**
	 * Renders the enabled field.
	 *
	 * @return void
	 */
	public function render_enabled_field() {
		$settings = WACB_Settings_Manager::get_settings();
		?>
		<label for="wacb_enabled">
			<input
				name="<?php echo esc_attr( WACB_Settings_Manager::get_option_name() ); ?>[wacb_enabled]"
				type="checkbox"
				id="wacb_enabled"
				value="1"
				<?php checked( 1, (int) $settings['wacb_enabled'] ); ?>
			/>
			<?php echo esc_html__( 'Allow the plugin to render its frontend output once that phase is enabled.', 'whatsapp-chat-button' ); ?>
		</label>
		<?php
	}

	/**
	 * Renders the WhatsApp number field.
	 *
	 * @return void
	 */
	public function render_whatsapp_number_field() {
		$settings = WACB_Settings_Manager::get_settings();
		?>
		<input
			name="<?php echo esc_attr( WACB_Settings_Manager::get_option_name() ); ?>[wacb_whatsapp_number]"
			type="text"
			id="wacb_whatsapp_number"
			class="regular-text"
			inputmode="numeric"
			value="<?php echo esc_attr( $settings['wacb_whatsapp_number'] ); ?>"
			placeholder="15551234567"
		/>
		<p class="description">
			<?php echo esc_html__( 'Use the international format required by wa.me. Only digits are stored.', 'whatsapp-chat-button' ); ?>
		</p>
		<?php
	}

	/**
	 * Renders the default message field.
	 *
	 * @return void
	 */
	public function render_default_message_field() {
		$settings = WACB_Settings_Manager::get_settings();
		?>
		<textarea
			name="<?php echo esc_attr( WACB_Settings_Manager::get_option_name() ); ?>[wacb_default_message]"
			id="wacb_default_message"
			class="large-text"
			rows="5"
		><?php echo esc_textarea( $settings['wacb_default_message'] ); ?></textarea>
		<p class="description">
			<?php echo esc_html__( 'Supported placeholders can be included in this saved template and will be documented below.', 'whatsapp-chat-button' ); ?>
		</p>
		<?php
	}

	/**
	 * Renders the button text field.
	 *
	 * @return void
	 */
	public function render_button_text_field() {
		$settings = WACB_Settings_Manager::get_settings();
		?>
		<input
			name="<?php echo esc_attr( WACB_Settings_Manager::get_option_name() ); ?>[wacb_button_text]"
			type="text"
			id="wacb_button_text"
			class="regular-text"
			value="<?php echo esc_attr( $settings['wacb_button_text'] ); ?>"
		/>
		<?php
	}

	/**
	 * Renders the button position field.
	 *
	 * @return void
	 */
	public function render_button_position_field() {
		$settings = WACB_Settings_Manager::get_settings();
		?>
		<select name="<?php echo esc_attr( WACB_Settings_Manager::get_option_name() ); ?>[wacb_button_position]" id="wacb_button_position">
			<option value="left" <?php selected( $settings['wacb_button_position'], 'left' ); ?>>
				<?php echo esc_html__( 'Left', 'whatsapp-chat-button' ); ?>
			</option>
			<option value="right" <?php selected( $settings['wacb_button_position'], 'right' ); ?>>
				<?php echo esc_html__( 'Right', 'whatsapp-chat-button' ); ?>
			</option>
		</select>
		<?php
	}

	/**
	 * Renders the button color field.
	 *
	 * @return void
	 */
	public function render_button_color_field() {
		$settings = WACB_Settings_Manager::get_settings();
		?>
		<input
			name="<?php echo esc_attr( WACB_Settings_Manager::get_option_name() ); ?>[wacb_button_color]"
			type="color"
			id="wacb_button_color"
			value="<?php echo esc_attr( $settings['wacb_button_color'] ); ?>"
		/>
		<p class="description">
			<?php echo esc_html__( 'Stored as a sanitized hexadecimal color value.', 'whatsapp-chat-button' ); ?>
		</p>
		<?php
	}

	/**
	 * Renders the button delay field.
	 *
	 * @return void
	 */
	public function render_button_delay_field() {
		$settings = WACB_Settings_Manager::get_settings();
		?>
		<input
			name="<?php echo esc_attr( WACB_Settings_Manager::get_option_name() ); ?>[wacb_button_delay]"
			type="number"
			id="wacb_button_delay"
			class="small-text"
			min="0"
			step="1"
			value="<?php echo esc_attr( (string) $settings['wacb_button_delay'] ); ?>"
		/>
		<span><?php echo esc_html__( 'seconds', 'whatsapp-chat-button' ); ?></span>
		<?php
	}

	/**
	 * Renders the supported variables field.
	 *
	 * @return void
	 */
	public function render_supported_variables_field() {
		?>
		<p><?php echo esc_html__( 'You can use the following placeholders in the default pre-filled message:', 'whatsapp-chat-button' ); ?></p>
		<ul>
			<li><code>{page_title}</code></li>
			<li><code>{url}</code></li>
			<li><code>{site_name}</code></li>
		</ul>
		<p class="description">
			<?php echo esc_html__( 'These values are stored as part of the message template in this phase. Replacement logic will be introduced when frontend output is built.', 'whatsapp-chat-button' ); ?>
		</p>
		<?php
	}

	/**
	 * Renders the default route field.
	 *
	 * @return void
	 */
	public function render_default_route_field() {
		$settings = WACB_Settings_Manager::get_settings();
		$rule     = $settings['wacb_routing_rules'][0];
		?>
		<input
			name="<?php echo esc_attr( WACB_Settings_Manager::get_option_name() ); ?>[wacb_routing_rules][0][rule_type]"
			type="hidden"
			value="default"
		/>
		<input
			name="<?php echo esc_attr( WACB_Settings_Manager::get_option_name() ); ?>[wacb_routing_rules][0][match_type]"
			type="hidden"
			value="sitewide"
		/>
		<p>
			<label for="wacb_default_route_label">
				<?php echo esc_html__( 'Label', 'whatsapp-chat-button' ); ?>
			</label>
			<br />
			<input
				name="<?php echo esc_attr( WACB_Settings_Manager::get_option_name() ); ?>[wacb_routing_rules][0][label]"
				type="text"
				id="wacb_default_route_label"
				class="regular-text"
				value="<?php echo esc_attr( $rule['label'] ); ?>"
			/>
		</p>
		<p>
			<label for="wacb_default_route_number">
				<?php echo esc_html__( 'Fallback number override', 'whatsapp-chat-button' ); ?>
			</label>
			<br />
			<input
				name="<?php echo esc_attr( WACB_Settings_Manager::get_option_name() ); ?>[wacb_routing_rules][0][number]"
				type="text"
				id="wacb_default_route_number"
				class="regular-text"
				inputmode="numeric"
				value="<?php echo esc_attr( $rule['number'] ); ?>"
				placeholder="<?php echo esc_attr__( 'Leave empty to use the primary number', 'whatsapp-chat-button' ); ?>"
			/>
		</p>
		<p class="description">
			<?php echo esc_html__( 'This stores the first routing rule in a stable array format. More advanced page, post, and taxonomy rules will be added in a later phase.', 'whatsapp-chat-button' ); ?>
		</p>
		<?php
	}

	/**
	 * Returns the active admin tab.
	 *
	 * @return string
	 */
	private function get_active_tab() {
		$active_tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : self::TAB_SETTINGS;
		$tabs       = array_keys( $this->get_tabs() );

		if ( ! in_array( $active_tab, $tabs, true ) ) {
			return self::TAB_SETTINGS;
		}

		return $active_tab;
	}

	/**
	 * Returns available admin tabs.
	 *
	 * @return array<string, string>
	 */
	private function get_tabs() {
		return array(
			self::TAB_SETTINGS  => __( 'Settings', 'whatsapp-chat-button' ),
			self::TAB_ANALYTICS => __( 'Analytics', 'whatsapp-chat-button' ),
		);
	}

	/**
	 * Returns analytics placeholder data.
	 *
	 * @return array<string, int|string>
	 */
	private function get_analytics_summary() {
		$table_name         = WACB_Tracking_Engine::get_table_name();
		$table_exists       = WACB_Tracking_Engine::table_exists();
		$table_exists_label = $table_exists ? __( 'Ready', 'whatsapp-chat-button' ) : __( 'Not available yet', 'whatsapp-chat-button' );

		return array(
			'table_name'         => $table_name,
			'table_exists_label' => $table_exists_label,
			'total_clicks'       => WACB_Tracking_Engine::get_total_clicks(),
		);
	}
}
