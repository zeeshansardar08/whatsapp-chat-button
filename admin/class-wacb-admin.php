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
		if ( ! $this->is_settings_screen( $hook_suffix ) ) {
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
	 * Registers admin scripts when the settings UI is introduced.
	 *
	 * @return void
	 */
	public function enqueue_scripts( $hook_suffix = '' ) {
		if ( ! $this->is_settings_screen( $hook_suffix ) ) {
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
			__( 'Routing Rules', 'whatsapp-chat-button' ),
			array( $this, 'render_routing_section' ),
			self::SETTINGS_PAGE
		);

		add_settings_field(
			'wacb_routing_rules',
			__( 'Routing rules', 'whatsapp-chat-button' ),
			array( $this, 'render_routing_rules_field' ),
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
		echo '<p>' . esc_html__( 'Configure the primary WhatsApp number and the default message template used when no routing rule overrides it.', 'whatsapp-chat-button' ) . '</p>';
	}

	/**
	 * Renders the button design section description.
	 *
	 * @return void
	 */
	public function render_button_design_section() {
		echo '<p>' . esc_html__( 'These settings control how the floating button looks and when it appears on the frontend.', 'whatsapp-chat-button' ) . '</p>';
	}

	/**
	 * Renders the variables section description.
	 *
	 * @return void
	 */
	public function render_variables_section() {
		echo '<p>' . esc_html__( 'Use placeholders in the saved message template to personalize chats without hard-coding page details.', 'whatsapp-chat-button' ) . '</p>';
	}

	/**
	 * Renders the routing section description.
	 *
	 * @return void
	 */
	public function render_routing_section() {
		echo '<p>' . esc_html__( 'Rules are evaluated in this order: page, post, category, then default fallback. The first matching rule wins, and the default rule is always checked last.', 'whatsapp-chat-button' ) . '</p>';
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
		<p><?php echo esc_html__( 'You can use the following placeholders in the default pre-filled message:', 'whatsapp-chat-button' ); ?></p>
		<ul>
			<li><code>{page_title}</code></li>
			<li><code>{url}</code></li>
			<li><code>{site_name}</code></li>
		</ul>
		<p class="description">
			<?php echo esc_html__( 'They are stored in the saved message template and replaced only when the button is rendered.', 'whatsapp-chat-button' ); ?>
		</p>
		<?php
	}

	/**
	 * Renders routing rules.
	 *
	 * @return void
	 */
	public function render_routing_rules_field() {
		$routing_rules     = WACB_Settings_Manager::get_routing_rules();
		$default_rule      = WACB_Settings_Manager::get_default_routing_rules()[0];
		$specific_rules    = array();
		$page_options      = $this->get_page_options();
		$post_options      = $this->get_post_options();
		$category_options  = $this->get_category_options();
		$rule_type_options = WACB_Settings_Manager::get_rule_type_labels();
		$template_rule     = WACB_Settings_Manager::get_empty_routing_rule();
		$settings_key      = WACB_Settings_Manager::get_option_name();

		unset( $rule_type_options['default'] );

		foreach ( $routing_rules as $routing_rule ) {
			if ( 'default' === $routing_rule['rule_type'] ) {
				$default_rule = $routing_rule;
				continue;
			}

			$specific_rules[] = $routing_rule;
		}
		?>
		<div class="wacb-routing-rules" data-wacb-routing-rules data-wacb-next-index="<?php echo esc_attr( (string) count( $specific_rules ) ); ?>">
			<p class="description">
				<?php echo esc_html__( 'Add specific rules for pages, posts, and categories. Evaluation priority is fixed as page, post, category, then default fallback.', 'whatsapp-chat-button' ); ?>
			</p>
			<?php if ( empty( $page_options ) || empty( $post_options ) || empty( $category_options ) ) : ?>
				<p class="description">
					<?php echo esc_html__( 'Some selector lists are currently empty. Only existing published pages, published posts, and categories can be targeted.', 'whatsapp-chat-button' ); ?>
				</p>
			<?php endif; ?>

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

			<p>
				<button type="button" class="button" data-wacb-add-rule>
					<?php echo esc_html__( 'Add routing rule', 'whatsapp-chat-button' ); ?>
				</button>
			</p>

			<div class="wacb-routing-default-rule">
				<h4><?php echo esc_html__( 'Default fallback', 'whatsapp-chat-button' ); ?></h4>
				<input type="hidden" name="<?php echo esc_attr( $settings_key ); ?>[wacb_routing_rules][default][rule_type]" value="default" />
				<input type="hidden" name="<?php echo esc_attr( $settings_key ); ?>[wacb_routing_rules][default][target_id]" value="0" />
				<p>
					<label for="wacb_default_rule_label"><?php echo esc_html__( 'Label', 'whatsapp-chat-button' ); ?></label>
					<br />
					<input
						type="text"
						class="regular-text"
						id="wacb_default_rule_label"
						name="<?php echo esc_attr( $settings_key ); ?>[wacb_routing_rules][default][label]"
						value="<?php echo esc_attr( (string) $default_rule['label'] ); ?>"
					/>
				</p>
				<p>
					<label for="wacb_default_rule_number"><?php echo esc_html__( 'Fallback number override', 'whatsapp-chat-button' ); ?></label>
					<br />
					<input
						type="text"
						class="regular-text"
						inputmode="numeric"
						id="wacb_default_rule_number"
						name="<?php echo esc_attr( $settings_key ); ?>[wacb_routing_rules][default][number]"
						value="<?php echo esc_attr( (string) $default_rule['number'] ); ?>"
						placeholder="<?php echo esc_attr__( 'Leave empty to use the primary number', 'whatsapp-chat-button' ); ?>"
					/>
				</p>
				<p class="description">
					<?php echo esc_html__( 'Keep this empty to fall back to the primary WhatsApp number from the General section.', 'whatsapp-chat-button' ); ?>
				</p>
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
	 * Returns analytics summary data for the admin tab.
	 *
	 * @return array<string, mixed>
	 */
	private function get_analytics_summary() {
		$table_name         = WACB_Tracking_Engine::get_table_name();
		$table_exists       = WACB_Tracking_Engine::table_exists();
		$has_click_data     = WACB_Tracking_Engine::has_click_data();
		$table_exists_label = $table_exists ? __( 'Ready', 'whatsapp-chat-button' ) : __( 'Not available yet', 'whatsapp-chat-button' );
		$empty_state_message = '';

		if ( ! $table_exists ) {
			$empty_state_message = __( 'The analytics table is not available. Reactivate the plugin if tracking does not start after activation.', 'whatsapp-chat-button' );
		} elseif ( ! $has_click_data ) {
			$empty_state_message = __( 'No click data has been recorded yet. Analytics will start appearing after visitors click the frontend button.', 'whatsapp-chat-button' );
		}

		return array(
			'table_name'         => $table_name,
			'table_exists'       => $table_exists,
			'has_click_data'     => $has_click_data,
			'table_exists_label' => $table_exists_label,
			'empty_state_message' => $empty_state_message,
			'total_clicks'       => WACB_Tracking_Engine::get_total_clicks(),
			'clicks_today'       => WACB_Tracking_Engine::get_clicks_today(),
			'top_pages'          => WACB_Tracking_Engine::get_top_pages( 5 ),
			'device_breakdown'   => WACB_Tracking_Engine::get_device_breakdown(),
		);
	}

	/**
	 * Returns whether the current screen is the plugin settings page.
	 *
	 * @param string $hook_suffix Hook suffix.
	 * @return bool
	 */
	private function is_settings_screen( $hook_suffix ) {
		return '' !== $this->settings_page_hook_suffix && $this->settings_page_hook_suffix === $hook_suffix;
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
