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
	 * Top-level dashboard menu slug.
	 *
	 * @var string
	 */
	const MENU_SLUG = 'wacb-dashboard';

	/**
	 * Settings submenu slug.
	 *
	 * @var string
	 */
	const SETTINGS_SLUG = 'wacb-settings';

	/**
	 * Routing rules submenu slug.
	 *
	 * @var string
	 */
	const ROUTING_SLUG = 'wacb-routing-rules';

	/**
	 * Analytics submenu slug.
	 *
	 * @var string
	 */
	const ANALYTICS_SLUG = 'wacb-analytics';

	/**
	 * Settings page identifier.
	 *
	 * @var string
	 */
	const SETTINGS_PAGE = 'wacb-settings-page';

	/**
	 * Routing page identifier.
	 *
	 * @var string
	 */
	const ROUTING_PAGE = 'wacb-routing-page';

	/**
	 * Settings group identifier.
	 *
	 * @var string
	 */
	const SETTINGS_GROUP = 'wacb_settings_group';

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
	 * Registered plugin admin screen hooks.
	 *
	 * @var array<string, string>
	 */
	private $page_hooks = array();

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
	 * Registers admin styles on plugin pages.
	 *
	 * @param string $hook_suffix Current admin hook suffix.
	 * @return void
	 */
	public function enqueue_styles( $hook_suffix = '' ) {
		if ( ! $this->is_plugin_screen( $hook_suffix ) ) {
			return;
		}

		wp_enqueue_style(
			'wacb-admin',
			WACB_PLUGIN_URL . 'assets/css/wacb-admin.css',
			array(),
			$this->version
		);
	}

	/**
	 * Registers admin scripts on the routing rules page.
	 *
	 * @param string $hook_suffix Current admin hook suffix.
	 * @return void
	 */
	public function enqueue_scripts( $hook_suffix = '' ) {
		if ( ! $this->is_routing_screen( $hook_suffix ) ) {
			return;
		}

		wp_enqueue_script(
			'wacb-admin',
			WACB_PLUGIN_URL . 'assets/js/wacb-admin.js',
			array(),
			$this->version,
			true
		);
	}

	/**
	 * Registers the plugin admin menu and submenus.
	 *
	 * @return void
	 */
	public function register_admin_menu() {
		$this->page_hooks[ self::MENU_SLUG ] = add_menu_page(
			__( 'WhatsApp Chat Button', 'whatsapp-chat-button' ),
			__( 'WhatsApp Chat Button', 'whatsapp-chat-button' ),
			'manage_options',
			self::MENU_SLUG,
			array( $this, 'display_dashboard_page' ),
			'dashicons-format-chat',
			58
		);

		add_submenu_page(
			self::MENU_SLUG,
			__( 'Dashboard', 'whatsapp-chat-button' ),
			__( 'Dashboard', 'whatsapp-chat-button' ),
			'manage_options',
			self::MENU_SLUG,
			array( $this, 'display_dashboard_page' )
		);

		$this->page_hooks[ self::SETTINGS_SLUG ] = add_submenu_page(
			self::MENU_SLUG,
			__( 'Settings', 'whatsapp-chat-button' ),
			__( 'Settings', 'whatsapp-chat-button' ),
			'manage_options',
			self::SETTINGS_SLUG,
			array( $this, 'display_settings_page' )
		);

		$this->page_hooks[ self::ROUTING_SLUG ] = add_submenu_page(
			self::MENU_SLUG,
			__( 'Routing Rules', 'whatsapp-chat-button' ),
			__( 'Routing Rules', 'whatsapp-chat-button' ),
			'manage_options',
			self::ROUTING_SLUG,
			array( $this, 'display_routing_page' )
		);

		$this->page_hooks[ self::ANALYTICS_SLUG ] = add_submenu_page(
			self::MENU_SLUG,
			__( 'Analytics', 'whatsapp-chat-button' ),
			__( 'Analytics', 'whatsapp-chat-button' ),
			'manage_options',
			self::ANALYTICS_SLUG,
			array( $this, 'display_analytics_page' )
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
			'wacb_default_fallback_section',
			__( 'Default Fallback', 'whatsapp-chat-button' ),
			array( $this, 'render_default_fallback_section' ),
			self::SETTINGS_PAGE
		);

		add_settings_field(
			'wacb_default_fallback',
			__( 'Fallback rule', 'whatsapp-chat-button' ),
			array( $this, 'render_default_fallback_field' ),
			self::SETTINGS_PAGE,
			'wacb_default_fallback_section'
		);

		add_settings_section(
			'wacb_routing_section',
			__( 'Routing Rules', 'whatsapp-chat-button' ),
			array( $this, 'render_routing_section' ),
			self::ROUTING_PAGE
		);

		add_settings_field(
			'wacb_routing_rules',
			__( 'Routing rules', 'whatsapp-chat-button' ),
			array( $this, 'render_routing_rules_field' ),
			self::ROUTING_PAGE,
			'wacb_routing_section'
		);
	}

	/**
	 * Renders the dashboard page.
	 *
	 * @return void
	 */
	public function display_dashboard_page() {
		$this->abort_if_no_permissions();

		$analytics_summary = $this->get_analytics_summary();
		$plugin_summary    = $this->get_plugin_summary();
		$view_path         = WACB_PLUGIN_DIR . 'admin/views/dashboard-page.php';

		if ( is_readable( $view_path ) ) {
			require $view_path;
		}
	}

	/**
	 * Renders the settings page.
	 *
	 * @return void
	 */
	public function display_settings_page() {
		$this->abort_if_no_permissions();

		$settings_group = self::SETTINGS_GROUP;
		$settings_page  = self::SETTINGS_PAGE;
		$plugin_summary = $this->get_plugin_summary();
		$view_path      = WACB_PLUGIN_DIR . 'admin/views/settings-page.php';

		if ( is_readable( $view_path ) ) {
			require $view_path;
		}
	}

	/**
	 * Renders the routing rules page.
	 *
	 * @return void
	 */
	public function display_routing_page() {
		$this->abort_if_no_permissions();

		$settings_group = self::SETTINGS_GROUP;
		$settings_page  = self::ROUTING_PAGE;
		$routing_rules  = WACB_Settings_Manager::get_specific_routing_rules();
		$routing_count  = count( $routing_rules );
		$plugin_summary = $this->get_plugin_summary();
		$view_path      = WACB_PLUGIN_DIR . 'admin/views/routing-page.php';

		if ( is_readable( $view_path ) ) {
			require $view_path;
		}
	}

	/**
	 * Renders the analytics page.
	 *
	 * @return void
	 */
	public function display_analytics_page() {
		$this->abort_if_no_permissions();

		$analytics_summary = $this->get_analytics_summary();
		$plugin_summary    = $this->get_plugin_summary();
		$view_path         = WACB_PLUGIN_DIR . 'admin/views/analytics-page.php';

		if ( is_readable( $view_path ) ) {
			require $view_path;
		}
	}

	/**
	 * Renders a section card using the Settings API globals.
	 *
	 * @param string $page       Settings page identifier.
	 * @param string $section_id Section identifier.
	 * @return void
	 */
	public function render_settings_section_card( $page, $section_id ) {
		global $wp_settings_fields, $wp_settings_sections;

		if ( empty( $wp_settings_sections[ $page ][ $section_id ] ) ) {
			return;
		}

		$section = $wp_settings_sections[ $page ][ $section_id ];
		?>
		<section class="wacb-admin-card wacb-admin-card--section">
			<div class="wacb-admin-card__header">
				<?php if ( ! empty( $section['title'] ) ) : ?>
					<h2 class="wacb-admin-card__title"><?php echo esc_html( $section['title'] ); ?></h2>
				<?php endif; ?>
				<?php if ( ! empty( $section['callback'] ) && is_callable( $section['callback'] ) ) : ?>
					<div class="wacb-admin-card__description">
						<?php call_user_func( $section['callback'], $section ); ?>
					</div>
				<?php endif; ?>
			</div>

			<?php if ( ! empty( $wp_settings_fields[ $page ][ $section_id ] ) ) : ?>
				<table class="form-table wacb-admin-form-table" role="presentation">
					<?php do_settings_fields( $page, $section_id ); ?>
				</table>
			<?php endif; ?>
		</section>
		<?php
	}

	/**
	 * Renders the general section description.
	 *
	 * @return void
	 */
	public function render_general_section( $section = array() ) {
		echo '<p>' . esc_html__( 'Configure the primary WhatsApp number and the default message template used when no routing rule overrides it.', 'whatsapp-chat-button' ) . '</p>';
	}

	/**
	 * Renders the button design section description.
	 *
	 * @return void
	 */
	public function render_button_design_section( $section = array() ) {
		echo '<p>' . esc_html__( 'These settings control how the floating button looks and when it appears on the frontend.', 'whatsapp-chat-button' ) . '</p>';
	}

	/**
	 * Renders the variables section description.
	 *
	 * @return void
	 */
	public function render_variables_section( $section = array() ) {
		echo '<p>' . esc_html__( 'Use placeholders in the saved message template to personalize chats without hard-coding page details.', 'whatsapp-chat-button' ) . '</p>';
	}

	/**
	 * Renders the default fallback section description.
	 *
	 * @return void
	 */
	public function render_default_fallback_section( $section = array() ) {
		echo '<p>' . esc_html__( 'This fallback rule is used when no page, post, or category routing rule matches the current request.', 'whatsapp-chat-button' ) . '</p>';
	}

	/**
	 * Renders the routing section description.
	 *
	 * @return void
	 */
	public function render_routing_section( $section = array() ) {
		echo '<p>' . esc_html__( 'Rules are evaluated in this order: page, post, category, then default fallback. The first matching rule wins.', 'whatsapp-chat-button' ) . '</p>';
	}

	/**
	 * Renders the enabled field.
	 *
	 * @return void
	 */
	public function render_enabled_field() {
		$settings = WACB_Settings_Manager::get_settings();
		?>
		<input
			name="<?php echo esc_attr( WACB_Settings_Manager::get_option_name() ); ?>[wacb_enabled]"
			type="hidden"
			value="0"
		/>
		<label for="wacb_enabled">
			<input
				name="<?php echo esc_attr( WACB_Settings_Manager::get_option_name() ); ?>[wacb_enabled]"
				type="checkbox"
				id="wacb_enabled"
				value="1"
				<?php checked( 1, (int) $settings['wacb_enabled'] ); ?>
			/>
			<?php echo esc_html__( 'Render the floating WhatsApp button on the frontend when the plugin has a valid number to use.', 'whatsapp-chat-button' ); ?>
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
			<?php echo esc_html__( 'Use the international format required by wa.me, for example 15551234567. Spaces, plus signs, and punctuation are removed before saving.', 'whatsapp-chat-button' ); ?>
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
			<?php echo esc_html__( 'Supported placeholders can be included in this template. They are replaced at runtime using the current page context.', 'whatsapp-chat-button' ); ?>
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
		<p class="description">
			<?php echo esc_html__( 'Keep the label short so it stays readable on smaller screens.', 'whatsapp-chat-button' ); ?>
		</p>
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
		<p class="description">
			<?php echo esc_html__( 'Choose which side of the screen the floating button should attach to.', 'whatsapp-chat-button' ); ?>
		</p>
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
		<p class="description">
			<?php echo esc_html__( 'Set to 0 to show the button immediately after page load.', 'whatsapp-chat-button' ); ?>
		</p>
		<?php
	}

	/**
	 * Renders the supported variables field.
	 *
	 * @return void
	 */
	public function render_supported_variables_field() {
		?>
		<div class="wacb-token-panel">
			<p class="wacb-token-panel__title"><?php echo esc_html__( 'Available placeholders', 'whatsapp-chat-button' ); ?></p>
			<div class="wacb-token-list">
				<span class="wacb-token">{page_title}</span>
				<span class="wacb-token">{url}</span>
				<span class="wacb-token">{site_name}</span>
			</div>
			<p class="description">
				<?php echo esc_html__( 'They are stored in the saved message template and replaced only when the button is rendered.', 'whatsapp-chat-button' ); ?>
			</p>
		</div>
		<?php
	}

	/**
	 * Renders the default fallback rule field.
	 *
	 * @return void
	 */
	public function render_default_fallback_field() {
		$default_rule = WACB_Settings_Manager::get_default_routing_rule();
		$settings_key = WACB_Settings_Manager::get_option_name();
		?>
		<div class="wacb-default-fallback wacb-form-grid">
			<input type="hidden" name="<?php echo esc_attr( $settings_key ); ?>[wacb_routing_rules][default][rule_type]" value="default" />
			<input type="hidden" name="<?php echo esc_attr( $settings_key ); ?>[wacb_routing_rules][default][target_id]" value="0" />
			<div class="wacb-form-grid__item">
				<label for="wacb_default_rule_label"><?php echo esc_html__( 'Label', 'whatsapp-chat-button' ); ?></label>
				<input
					type="text"
					class="regular-text"
					id="wacb_default_rule_label"
					name="<?php echo esc_attr( $settings_key ); ?>[wacb_routing_rules][default][label]"
					value="<?php echo esc_attr( (string) $default_rule['label'] ); ?>"
				/>
			</div>
			<div class="wacb-form-grid__item">
				<label for="wacb_default_rule_number"><?php echo esc_html__( 'Fallback number override', 'whatsapp-chat-button' ); ?></label>
				<input
					type="text"
					class="regular-text"
					inputmode="numeric"
					id="wacb_default_rule_number"
					name="<?php echo esc_attr( $settings_key ); ?>[wacb_routing_rules][default][number]"
					value="<?php echo esc_attr( (string) $default_rule['number'] ); ?>"
					placeholder="<?php echo esc_attr__( 'Leave empty to use the primary number', 'whatsapp-chat-button' ); ?>"
				/>
			</div>
			<p class="description wacb-form-grid__footer">
				<?php echo esc_html__( 'Leave the override empty to fall back to the primary WhatsApp number from the General section.', 'whatsapp-chat-button' ); ?>
			</p>
		</div>
		<?php
	}

	/**
	 * Renders routing rules.
	 *
	 * @return void
	 */
	public function render_routing_rules_field() {
		$specific_rules    = WACB_Settings_Manager::get_specific_routing_rules();
		$default_rule      = WACB_Settings_Manager::get_default_routing_rule();
		$page_options      = $this->get_page_options();
		$post_options      = $this->get_post_options();
		$category_options  = $this->get_category_options();
		$rule_type_options = WACB_Settings_Manager::get_rule_type_labels();
		$template_rule     = WACB_Settings_Manager::get_empty_routing_rule();
		$settings_key      = WACB_Settings_Manager::get_option_name();

		unset( $rule_type_options['default'] );
		?>
		<div class="wacb-routing-rules" data-wacb-routing-rules data-wacb-next-index="<?php echo esc_attr( (string) count( $specific_rules ) ); ?>">
			<div class="wacb-toolbar">
				<div class="wacb-toolbar__content">
					<p class="wacb-toolbar__title"><?php echo esc_html__( 'Rules workspace', 'whatsapp-chat-button' ); ?></p>
					<p class="wacb-toolbar__text">
						<?php echo esc_html__( 'Add specific rules for pages, posts, and categories. Page rules are checked first, followed by post rules, then category rules.', 'whatsapp-chat-button' ); ?>
					</p>
					<p class="wacb-toolbar__text">
						<?php
						echo esc_html(
							sprintf(
								/* translators: %s: fallback rule description. */
								__( 'Default fallback: %s', 'whatsapp-chat-button' ),
								'' !== (string) $default_rule['number'] ? __( 'Custom fallback number configured', 'whatsapp-chat-button' ) : __( 'Uses the primary WhatsApp number', 'whatsapp-chat-button' )
							)
						);
						?>
					</p>
					<?php if ( empty( $page_options ) || empty( $post_options ) || empty( $category_options ) ) : ?>
						<p class="wacb-toolbar__text">
							<?php echo esc_html__( 'Some selector lists are currently empty. Only existing published pages, published posts, and categories can be targeted.', 'whatsapp-chat-button' ); ?>
						</p>
					<?php endif; ?>
				</div>
				<div class="wacb-toolbar__actions">
					<button type="button" class="button button-secondary" data-wacb-add-rule>
						<?php echo esc_html__( 'Add routing rule', 'whatsapp-chat-button' ); ?>
					</button>
				</div>
			</div>

			<div class="wacb-empty-state<?php echo empty( $specific_rules ) ? '' : ' is-hidden'; ?>" data-wacb-empty-state<?php echo empty( $specific_rules ) ? '' : ' hidden'; ?>>
				<h3 class="wacb-empty-state__title"><?php echo esc_html__( 'No specific rules added yet', 'whatsapp-chat-button' ); ?></h3>
				<p class="wacb-empty-state__text">
					<?php echo esc_html__( 'Create your first routing rule to send pages, posts, or categories to a dedicated WhatsApp number while keeping the default fallback in place.', 'whatsapp-chat-button' ); ?>
				</p>
			</div>

			<div class="wacb-table-card">
				<table class="widefat striped wacb-routing-rules-table">
					<thead>
						<tr>
							<th scope="col"><?php echo esc_html__( 'Rule label', 'whatsapp-chat-button' ); ?></th>
							<th scope="col"><?php echo esc_html__( 'Rule type', 'whatsapp-chat-button' ); ?></th>
							<th scope="col"><?php echo esc_html__( 'Target', 'whatsapp-chat-button' ); ?></th>
							<th scope="col"><?php echo esc_html__( 'WhatsApp number', 'whatsapp-chat-button' ); ?></th>
							<th scope="col"><?php echo esc_html__( 'Action', 'whatsapp-chat-button' ); ?></th>
						</tr>
					</thead>
					<tbody data-wacb-rules-body>
						<?php foreach ( $specific_rules as $index => $rule ) : ?>
							<?php
							$this->render_routing_rule_row(
								$settings_key . '[wacb_routing_rules][' . (string) $index . ']',
								$rule,
								$rule_type_options,
								$page_options,
								$post_options,
								$category_options
							);
							?>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>

			<?php
			ob_start();
			$this->render_routing_rule_row(
				$settings_key . '[wacb_routing_rules][__index__]',
				$template_rule,
				$rule_type_options,
				$page_options,
				$post_options,
				$category_options
			);
			$template_row_html = ob_get_clean();
			?>
			<script type="text/html" data-wacb-rule-template><?php echo $template_row_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></script>
		</div>
		<?php
	}

	/**
	 * Returns analytics summary data for the admin pages.
	 *
	 * @return array<string, mixed>
	 */
	private function get_analytics_summary() {
		$table_name          = WACB_Tracking_Engine::get_table_name();
		$table_exists        = WACB_Tracking_Engine::table_exists();
		$has_click_data      = WACB_Tracking_Engine::has_click_data();
		$device_breakdown    = WACB_Tracking_Engine::get_device_breakdown();
		$table_exists_label  = $table_exists ? __( 'Ready', 'whatsapp-chat-button' ) : __( 'Not available yet', 'whatsapp-chat-button' );
		$empty_state_message = '';
		$tracked_device_types = count(
			array_filter(
				$device_breakdown,
				static function ( $click_count ) {
					return absint( $click_count ) > 0;
				}
			)
		);

		if ( ! $table_exists ) {
			$empty_state_message = __( 'The analytics table is not available. Reactivate the plugin if tracking does not start after activation.', 'whatsapp-chat-button' );
		} elseif ( ! $has_click_data ) {
			$empty_state_message = __( 'No click data has been recorded yet. Analytics will start appearing after visitors click the frontend button.', 'whatsapp-chat-button' );
		}

		return array(
			'table_name'          => $table_name,
			'table_exists'        => $table_exists,
			'has_click_data'      => $has_click_data,
			'table_exists_label'  => $table_exists_label,
			'empty_state_message' => $empty_state_message,
			'total_clicks'        => WACB_Tracking_Engine::get_total_clicks(),
			'clicks_today'        => WACB_Tracking_Engine::get_clicks_today(),
			'top_pages'           => WACB_Tracking_Engine::get_top_pages( 5 ),
			'device_breakdown'    => $device_breakdown,
			'tracked_device_types' => $tracked_device_types,
		);
	}

	/**
	 * Returns plugin setup and health summary data for admin pages.
	 *
	 * @return array<string, mixed>
	 */
	private function get_plugin_summary() {
		$settings             = WACB_Settings_Manager::get_settings();
		$default_rule         = WACB_Settings_Manager::get_default_routing_rule();
		$active_routing_rules = count( WACB_Settings_Manager::get_specific_routing_rules() );
		$table_ready          = WACB_Tracking_Engine::table_exists();
		$primary_number       = (string) $settings['wacb_whatsapp_number'];
		$fallback_number      = (string) $default_rule['number'];
		$button_position      = 'left' === $settings['wacb_button_position'] ? __( 'Left', 'whatsapp-chat-button' ) : __( 'Right', 'whatsapp-chat-button' );
		$button_delay         = absint( $settings['wacb_button_delay'] );

		return array(
			'is_enabled'                => ! empty( $settings['wacb_enabled'] ),
			'has_primary_number'        => '' !== $primary_number,
			'has_default_fallback'      => '' !== $fallback_number,
			'active_routing_rules'      => $active_routing_rules,
			'is_tracking_ready'         => $table_ready,
			'primary_number_label'      => '' !== $primary_number ? __( 'Configured', 'whatsapp-chat-button' ) : __( 'Missing', 'whatsapp-chat-button' ),
			'default_fallback_label'    => '' !== $fallback_number ? __( 'Override configured', 'whatsapp-chat-button' ) : __( 'Uses primary number', 'whatsapp-chat-button' ),
			'button_position_label'     => $button_position,
			'button_delay_label'        => $button_delay > 0 ? sprintf(
				/* translators: %d: delay in seconds. */
				_n( '%d second delay', '%d seconds delay', $button_delay, 'whatsapp-chat-button' ),
				$button_delay
			) : __( 'Shows immediately', 'whatsapp-chat-button' ),
			'enabled_badge_label'       => ! empty( $settings['wacb_enabled'] ) ? __( 'Enabled', 'whatsapp-chat-button' ) : __( 'Disabled', 'whatsapp-chat-button' ),
			'tracking_badge_label'      => $table_ready ? __( 'Tracking ready', 'whatsapp-chat-button' ) : __( 'Tracking issue', 'whatsapp-chat-button' ),
			'primary_number_value'      => $primary_number,
			'default_fallback_value'    => $fallback_number,
		);
	}

	/**
	 * Returns whether the current screen is a plugin admin page.
	 *
	 * @param string $hook_suffix Hook suffix.
	 * @return bool
	 */
	private function is_plugin_screen( $hook_suffix ) {
		return '' !== $hook_suffix && in_array( $hook_suffix, $this->page_hooks, true );
	}

	/**
	 * Returns whether the current screen is the routing rules page.
	 *
	 * @param string $hook_suffix Hook suffix.
	 * @return bool
	 */
	private function is_routing_screen( $hook_suffix ) {
		return isset( $this->page_hooks[ self::ROUTING_SLUG ] ) && $this->page_hooks[ self::ROUTING_SLUG ] === $hook_suffix;
	}

	/**
	 * Halts execution if the current user cannot manage plugin settings.
	 *
	 * @return void
	 */
	private function abort_if_no_permissions() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'whatsapp-chat-button' ) );
		}
	}

	/**
	 * Renders a routing rule row view.
	 *
	 * @param string                                 $field_name_prefix Field name prefix.
	 * @param array<string, int|string>              $rule Rule data.
	 * @param array<string, string>                  $rule_type_options Rule type options.
	 * @param array<int, array{id:int,title:string}> $page_options Page options.
	 * @param array<int, array{id:int,title:string}> $post_options Post options.
	 * @param array<int, array{id:int,title:string}> $category_options Category options.
	 * @return void
	 */
	private function render_routing_rule_row( $field_name_prefix, $rule, $rule_type_options, $page_options, $post_options, $category_options ) {
		$view_path = WACB_PLUGIN_DIR . 'admin/views/routing-rule-row.php';

		if ( is_readable( $view_path ) ) {
			require $view_path;
		}
	}

	/**
	 * Returns published pages for routing.
	 *
	 * @return array<int, array{id:int,title:string}>
	 */
	private function get_page_options() {
		$page_options = array();
		$pages        = get_pages(
			array(
				'sort_column' => 'post_title',
				'sort_order'  => 'ASC',
			)
		);

		foreach ( $pages as $page ) {
			$title = get_the_title( $page );

			if ( '' === $title ) {
				$title = __( '(no title)', 'whatsapp-chat-button' );
			}

			$page_options[] = array(
				'id'    => (int) $page->ID,
				'title' => $title,
			);
		}

		return $page_options;
	}

	/**
	 * Returns published posts for routing.
	 *
	 * @return array<int, array{id:int,title:string}>
	 */
	private function get_post_options() {
		$post_options = array();
		$posts        = get_posts(
			array(
				'post_type'        => 'post',
				'post_status'      => 'publish',
				'numberposts'      => -1,
				'orderby'          => 'title',
				'order'            => 'ASC',
				'suppress_filters' => false,
			)
		);

		foreach ( $posts as $post ) {
			$title = get_the_title( $post );

			if ( '' === $title ) {
				$title = __( '(no title)', 'whatsapp-chat-button' );
			}

			$post_options[] = array(
				'id'    => (int) $post->ID,
				'title' => $title,
			);
		}

		return $post_options;
	}

	/**
	 * Returns categories for routing.
	 *
	 * @return array<int, array{id:int,title:string}>
	 */
	private function get_category_options() {
		$category_options = array();
		$categories       = get_categories(
			array(
				'hide_empty' => false,
				'orderby'    => 'name',
				'order'      => 'ASC',
			)
		);

		foreach ( $categories as $category ) {
			$category_options[] = array(
				'id'    => (int) $category->term_id,
				'title' => $category->name,
			);
		}

		return $category_options;
	}
}
