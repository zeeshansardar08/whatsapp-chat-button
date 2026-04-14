<?php
/**
 * Settings admin page view.
 *
 * @package WhatsApp_Chat_Button
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap wacb-admin-page">
	<div class="wacb-admin-page__header">
		<div class="wacb-admin-page__header-content">
			<h1 class="wacb-admin-page__title"><?php echo esc_html__( 'Settings', 'whatsapp-chat-button' ); ?></h1>
			<p class="wacb-admin-page__intro">
				<?php echo esc_html__( 'Manage the core plugin behavior, button design, supported smart message variables, and the default fallback configuration.', 'whatsapp-chat-button' ); ?>
			</p>
		</div>
		<div class="wacb-page-badges wacb-admin-page__header-meta">
			<span class="wacb-status-badge <?php echo $plugin_summary['is_enabled'] ? 'is-positive' : 'is-neutral'; ?>">
				<?php echo esc_html( $plugin_summary['enabled_badge_label'] ); ?>
			</span>
			<span class="wacb-status-badge <?php echo $plugin_summary['has_primary_number'] ? 'is-positive' : 'is-warning'; ?>">
				<?php echo esc_html( $plugin_summary['primary_number_label'] ); ?>
			</span>
		</div>
	</div>

	<div class="wacb-notice-stack">
		<?php settings_errors(); ?>
	</div>

	<form class="wacb-admin-form" method="post" action="options.php">
		<?php settings_fields( $settings_group ); ?>
		<input type="hidden" name="<?php echo esc_attr( WACB_Settings_Manager::get_option_name() ); ?>[wacb_form_context]" value="<?php echo esc_attr( WACB_Settings_Manager::FORM_CONTEXT_SETTINGS ); ?>" />

		<div class="wacb-admin-stack">
			<?php $this->render_settings_section_card( $settings_page, 'wacb_general_section' ); ?>
			<?php $this->render_settings_section_card( $settings_page, 'wacb_button_design_section' ); ?>
			<?php $this->render_settings_section_card( $settings_page, 'wacb_variables_section' ); ?>
			<?php $this->render_settings_section_card( $settings_page, 'wacb_default_fallback_section' ); ?>
		</div>

		<div class="wacb-save-zone">
			<div class="wacb-save-zone__content">
				<h2 class="wacb-save-zone__title"><?php echo esc_html__( 'Save your settings', 'whatsapp-chat-button' ); ?></h2>
				<p class="wacb-save-zone__text"><?php echo esc_html__( 'Changes are applied to the existing plugin configuration without altering your saved routing rules or analytics data.', 'whatsapp-chat-button' ); ?></p>
			</div>
			<div class="wacb-save-zone__action">
				<?php submit_button( __( 'Save Settings', 'whatsapp-chat-button' ), 'primary large', 'submit', false ); ?>
			</div>
		</div>
	</form>
</div>
