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
		<div class="wacb-admin-page__header-content">
			<h1 class="wacb-admin-page__title"><?php echo esc_html__( 'Dashboard', 'whatsapp-chat-button' ); ?></h1>
			<p class="wacb-admin-page__intro">
				<?php echo esc_html__( 'Monitor the current health of your WhatsApp button setup and review a quick summary of click activity.', 'whatsapp-chat-button' ); ?>
			</p>
		</div>
		<div class="wacb-page-badges wacb-admin-page__header-meta">
			<span class="wacb-status-badge <?php echo $plugin_summary['is_enabled'] ? 'is-positive' : 'is-neutral'; ?>">
				<?php echo esc_html( $plugin_summary['enabled_badge_label'] ); ?>
			</span>
			<span class="wacb-status-badge <?php echo $plugin_summary['is_tracking_ready'] ? 'is-positive' : 'is-warning'; ?>">
				<?php echo esc_html( $plugin_summary['tracking_badge_label'] ); ?>
			</span>
		</div>
	</div>

	<?php if ( ! empty( $analytics_summary['empty_state_message'] ) ) : ?>
		<div class="wacb-empty-state wacb-empty-state--notice">
			<h2 class="wacb-empty-state__title"><?php echo esc_html__( 'Analytics are waiting for the first click', 'whatsapp-chat-button' ); ?></h2>
			<p class="wacb-empty-state__text"><?php echo esc_html( $analytics_summary['empty_state_message'] ); ?></p>
			<ul class="wacb-empty-state__list">
				<li><?php echo esc_html__( 'Enable the plugin in Settings.', 'whatsapp-chat-button' ); ?></li>
				<li><?php echo esc_html__( 'Verify the primary WhatsApp number is configured.', 'whatsapp-chat-button' ); ?></li>
				<li><?php echo esc_html__( 'Visit a frontend page where the button should appear.', 'whatsapp-chat-button' ); ?></li>
				<li><?php echo esc_html__( 'Click the WhatsApp button once to confirm tracking.', 'whatsapp-chat-button' ); ?></li>
			</ul>
		</div>
	<?php endif; ?>

	<div class="wacb-admin-stack">
		<div class="wacb-admin-grid wacb-admin-grid--metrics">
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
				<div class="wacb-admin-metric-card__status">
					<span class="wacb-status-badge <?php echo $analytics_summary['table_exists'] ? 'is-positive' : 'is-warning'; ?>">
						<?php echo esc_html( $analytics_summary['table_exists_label'] ); ?>
					</span>
				</div>
				<p class="wacb-admin-metric-card__meta">
					<span class="wacb-code-chip"><?php echo esc_html( $analytics_summary['table_name'] ); ?></span>
				</p>
			</section>

			<section class="wacb-admin-card wacb-admin-metric-card">
				<p class="wacb-admin-metric-card__label"><?php echo esc_html__( 'Active routing rules', 'whatsapp-chat-button' ); ?></p>
				<p class="wacb-admin-metric-card__value"><?php echo esc_html( number_format_i18n( $plugin_summary['active_routing_rules'] ) ); ?></p>
				<p class="wacb-admin-metric-card__meta"><?php echo esc_html__( 'Specific page, post, and category rules.', 'whatsapp-chat-button' ); ?></p>
			</section>
		</div>

		<div class="wacb-admin-grid wacb-admin-grid--two-column">
			<section class="wacb-admin-card wacb-admin-card--compact">
			<div class="wacb-admin-card__header">
				<h2 class="wacb-admin-card__title"><?php echo esc_html__( 'Device breakdown', 'whatsapp-chat-button' ); ?></h2>
				<p class="wacb-admin-card__description-text"><?php echo esc_html__( 'A quick view of how tracked clicks are split across mobile and desktop visitors.', 'whatsapp-chat-button' ); ?></p>
			</div>

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

			<section class="wacb-admin-card wacb-admin-card--compact">
			<div class="wacb-admin-card__header">
				<h2 class="wacb-admin-card__title"><?php echo esc_html__( 'Quick plugin status', 'whatsapp-chat-button' ); ?></h2>
				<p class="wacb-admin-card__description-text"><?php echo esc_html__( 'Review the core setup details that directly affect frontend availability and routing behavior.', 'whatsapp-chat-button' ); ?></p>
			</div>

			<div class="wacb-admin-metric-list">
				<div class="wacb-admin-metric-list__item">
					<span><?php echo esc_html__( 'Primary WhatsApp number', 'whatsapp-chat-button' ); ?></span>
					<strong><?php echo esc_html( $plugin_summary['primary_number_label'] ); ?></strong>
				</div>
				<div class="wacb-admin-metric-list__item">
					<span><?php echo esc_html__( 'Default fallback', 'whatsapp-chat-button' ); ?></span>
					<strong><?php echo esc_html( $plugin_summary['default_fallback_label'] ); ?></strong>
				</div>
				<div class="wacb-admin-metric-list__item">
					<span><?php echo esc_html__( 'Button layout', 'whatsapp-chat-button' ); ?></span>
					<strong><?php echo esc_html( $plugin_summary['button_position_label'] . ' / ' . $plugin_summary['button_delay_label'] ); ?></strong>
				</div>
			</div>
			</section>
		</div>
	</div>
</div>
