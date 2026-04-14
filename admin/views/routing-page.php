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
		<div class="wacb-admin-page__header-content">
			<h1 class="wacb-admin-page__title"><?php echo esc_html__( 'Routing Rules', 'whatsapp-chat-button' ); ?></h1>
			<p class="wacb-admin-page__intro">
				<?php echo esc_html__( 'Assign different WhatsApp numbers to specific pages, posts, and categories. The first matching rule wins before the default fallback is checked.', 'whatsapp-chat-button' ); ?>
			</p>
		</div>
		<div class="wacb-page-badges wacb-admin-page__header-meta">
			<span class="wacb-status-badge is-positive">
				<?php
				echo esc_html(
					sprintf(
						/* translators: %d: number of routing rules. */
						_n( '%d active rule', '%d active rules', $routing_count, 'whatsapp-chat-button' ),
						$routing_count
					)
				);
				?>
			</span>
		</div>
	</div>

	<div class="wacb-notice-stack">
		<?php settings_errors(); ?>
	</div>

	<div class="wacb-admin-stack">
		<div class="wacb-admin-grid wacb-admin-grid--three-column wacb-admin-grid--summary">
			<section class="wacb-admin-card wacb-admin-summary-card">
				<p class="wacb-admin-summary-card__label"><?php echo esc_html__( 'Active rules', 'whatsapp-chat-button' ); ?></p>
				<p class="wacb-admin-summary-card__value"><?php echo esc_html( number_format_i18n( $routing_count ) ); ?></p>
			</section>
			<section class="wacb-admin-card wacb-admin-summary-card">
				<p class="wacb-admin-summary-card__label"><?php echo esc_html__( 'Evaluation order', 'whatsapp-chat-button' ); ?></p>
				<p class="wacb-admin-summary-card__text"><?php echo esc_html__( 'Page, then post, then category, then the default fallback.', 'whatsapp-chat-button' ); ?></p>
			</section>
			<section class="wacb-admin-card wacb-admin-summary-card">
				<p class="wacb-admin-summary-card__label"><?php echo esc_html__( 'Default fallback', 'whatsapp-chat-button' ); ?></p>
				<p class="wacb-admin-summary-card__text">
					<?php echo esc_html( $plugin_summary['has_default_fallback'] ? __( 'Custom fallback number configured.', 'whatsapp-chat-button' ) : __( 'Currently using the primary WhatsApp number.', 'whatsapp-chat-button' ) ); ?>
				</p>
			</section>
		</div>

		<form class="wacb-admin-form" method="post" action="options.php">
			<?php settings_fields( $settings_group ); ?>
			<input type="hidden" name="<?php echo esc_attr( WACB_Settings_Manager::get_option_name() ); ?>[wacb_form_context]" value="<?php echo esc_attr( WACB_Settings_Manager::FORM_CONTEXT_ROUTING ); ?>" />

			<div class="wacb-admin-stack">
				<?php $this->render_settings_section_card( $settings_page, 'wacb_routing_section' ); ?>
			</div>

			<div class="wacb-save-zone">
				<div class="wacb-save-zone__content">
					<h2 class="wacb-save-zone__title"><?php echo esc_html__( 'Save your routing rules', 'whatsapp-chat-button' ); ?></h2>
					<p class="wacb-save-zone__text"><?php echo esc_html__( 'Rule changes are saved in the existing routing schema. The default fallback remains available even when no specific rules are added.', 'whatsapp-chat-button' ); ?></p>
				</div>
				<div class="wacb-save-zone__action">
					<?php submit_button( __( 'Save Routing Rules', 'whatsapp-chat-button' ), 'primary large', 'submit', false ); ?>
				</div>
			</div>
		</form>
	</div>
</div>
