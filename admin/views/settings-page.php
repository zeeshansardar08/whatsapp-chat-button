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
		<div>
			<h1 class="wacb-admin-page__title"><?php echo esc_html__( 'Settings', 'whatsapp-chat-button' ); ?></h1>
			<p class="wacb-admin-page__intro">
				<?php echo esc_html__( 'Manage the core plugin behavior, button design, supported smart message variables, and the default fallback configuration.', 'whatsapp-chat-button' ); ?>
			</p>
		</div>
	</div>

	<?php settings_errors( WACB_Settings_Manager::get_option_name() ); ?>

	<form class="wacb-admin-form" method="post" action="options.php">
		<?php settings_fields( $settings_group ); ?>
		<input type="hidden" name="<?php echo esc_attr( WACB_Settings_Manager::get_option_name() ); ?>[wacb_form_context]" value="<?php echo esc_attr( WACB_Settings_Manager::FORM_CONTEXT_SETTINGS ); ?>" />

		<div class="wacb-admin-stack">
			<?php $this->render_settings_section_card( $settings_page, 'wacb_general_section' ); ?>
			<?php $this->render_settings_section_card( $settings_page, 'wacb_button_design_section' ); ?>
			<?php $this->render_settings_section_card( $settings_page, 'wacb_variables_section' ); ?>
			<?php $this->render_settings_section_card( $settings_page, 'wacb_default_fallback_section' ); ?>
		</div>

		<div class="wacb-admin-form-actions">
			<?php submit_button( __( 'Save Settings', 'whatsapp-chat-button' ), 'primary large', 'submit', false ); ?>
		</div>
	</form>
</div>
