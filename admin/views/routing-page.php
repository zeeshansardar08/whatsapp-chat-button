<?php
/**
 * Routing rules admin page view.
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
			<h1 class="wacb-admin-page__title"><?php echo esc_html__( 'Routing Rules', 'whatsapp-chat-button' ); ?></h1>
			<p class="wacb-admin-page__intro">
				<?php echo esc_html__( 'Assign different WhatsApp numbers to specific pages, posts, and categories. The first matching rule wins before the default fallback is checked.', 'whatsapp-chat-button' ); ?>
			</p>
		</div>
		<div class="wacb-admin-page__badge">
			<?php
			echo esc_html(
				sprintf(
					/* translators: %d: number of routing rules. */
					_n( '%d active rule', '%d active rules', $routing_count, 'whatsapp-chat-button' ),
					$routing_count
				)
			);
			?>
		</div>
	</div>

	<?php settings_errors( WACB_Settings_Manager::get_option_name() ); ?>

	<form class="wacb-admin-form" method="post" action="options.php">
		<?php settings_fields( $settings_group ); ?>
		<input type="hidden" name="<?php echo esc_attr( WACB_Settings_Manager::get_option_name() ); ?>[wacb_form_context]" value="<?php echo esc_attr( WACB_Settings_Manager::FORM_CONTEXT_ROUTING ); ?>" />

		<div class="wacb-admin-stack">
			<?php $this->render_settings_section_card( $settings_page, 'wacb_routing_section' ); ?>
		</div>

		<div class="wacb-admin-form-actions">
			<?php submit_button( __( 'Save Routing Rules', 'whatsapp-chat-button' ), 'primary large', 'submit', false ); ?>
		</div>
	</form>
</div>
