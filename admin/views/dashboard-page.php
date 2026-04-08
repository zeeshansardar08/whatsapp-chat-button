<?php
/**
 * Dashboard admin page view.
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
			<h1 class="wacb-admin-page__title"><?php echo esc_html__( 'Dashboard', 'whatsapp-chat-button' ); ?></h1>
			<p class="wacb-admin-page__intro">
				<?php echo esc_html__( 'Monitor the current health of your WhatsApp button setup and review a quick summary of click activity.', 'whatsapp-chat-button' ); ?>
			</p>
		</div>
	</div>

	<?php if ( ! empty( $analytics_summary['empty_state_message'] ) ) : ?>
		<div class="wacb-admin-card wacb-admin-card--notice">
			<p class="wacb-admin-card__notice"><?php echo esc_html( $analytics_summary['empty_state_message'] ); ?></p>
		</div>
	<?php endif; ?>

	<div class="wacb-admin-grid">
		<section class="wacb-admin-card wacb-admin-metric-card">
			<p class="wacb-admin-metric-card__label"><?php echo esc_html__( 'Total clicks', 'whatsapp-chat-button' ); ?></p>
			<p class="wacb-admin-metric-card__value"><?php echo esc_html( number_format_i18n( $analytics_summary['total_clicks'] ) ); ?></p>
		</section>

		<section class="wacb-admin-card wacb-admin-metric-card">
			<p class="wacb-admin-metric-card__label"><?php echo esc_html__( 'Clicks today', 'whatsapp-chat-button' ); ?></p>
			<p class="wacb-admin-metric-card__value"><?php echo esc_html( number_format_i18n( $analytics_summary['clicks_today'] ) ); ?></p>
		</section>

		<section class="wacb-admin-card wacb-admin-metric-card">
			<p class="wacb-admin-metric-card__label"><?php echo esc_html__( 'Table status', 'whatsapp-chat-button' ); ?></p>
			<p class="wacb-admin-metric-card__value"><?php echo esc_html( $analytics_summary['table_exists_label'] ); ?></p>
			<p class="wacb-admin-metric-card__meta"><code><?php echo esc_html( $analytics_summary['table_name'] ); ?></code></p>
		</section>

		<section class="wacb-admin-card wacb-admin-metric-card">
			<p class="wacb-admin-metric-card__label"><?php echo esc_html__( 'Device breakdown', 'whatsapp-chat-button' ); ?></p>
			<div class="wacb-admin-metric-list">
				<div class="wacb-admin-metric-list__item">
					<span><?php echo esc_html__( 'Mobile', 'whatsapp-chat-button' ); ?></span>
					<strong><?php echo esc_html( number_format_i18n( $analytics_summary['device_breakdown']['mobile'] ) ); ?></strong>
				</div>
				<div class="wacb-admin-metric-list__item">
					<span><?php echo esc_html__( 'Desktop', 'whatsapp-chat-button' ); ?></span>
					<strong><?php echo esc_html( number_format_i18n( $analytics_summary['device_breakdown']['desktop'] ) ); ?></strong>
				</div>
			</div>
		</section>
	</div>
</div>
