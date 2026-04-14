<?php
/**
 * Analytics admin page view.
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
			<h1 class="wacb-admin-page__title"><?php echo esc_html__( 'Analytics', 'whatsapp-chat-button' ); ?></h1>
			<p class="wacb-admin-page__intro">
				<?php echo esc_html__( 'Review the lightweight click analytics collected when visitors use the WhatsApp button. The plugin stores only page URL, click time, and a simple device label.', 'whatsapp-chat-button' ); ?>
			</p>
		</div>
		<div class="wacb-page-badges wacb-admin-page__header-meta">
			<span class="wacb-status-badge <?php echo $plugin_summary['is_tracking_ready'] ? 'is-positive' : 'is-warning'; ?>">
				<?php echo esc_html( $plugin_summary['tracking_badge_label'] ); ?>
			</span>
		</div>
	</div>

	<div class="wacb-admin-stack">
		<?php if ( ! empty( $analytics_summary['empty_state_message'] ) ) : ?>
			<div class="wacb-empty-state wacb-empty-state--notice">
				<h2 class="wacb-empty-state__title"><?php echo esc_html__( 'No analytics data yet', 'whatsapp-chat-button' ); ?></h2>
				<p class="wacb-empty-state__text"><?php echo esc_html( $analytics_summary['empty_state_message'] ); ?></p>
				<p class="wacb-empty-state__text"><?php echo esc_html__( 'As soon as a visitor clicks the frontend WhatsApp button, these reports will begin populating automatically.', 'whatsapp-chat-button' ); ?></p>
			</div>
		<?php endif; ?>

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
				<p class="wacb-admin-metric-card__label"><?php echo esc_html__( 'Device types seen', 'whatsapp-chat-button' ); ?></p>
				<p class="wacb-admin-metric-card__value"><?php echo esc_html( number_format_i18n( $analytics_summary['tracked_device_types'] ) ); ?></p>
				<p class="wacb-admin-metric-card__meta"><?php echo esc_html__( 'Mobile and desktop categories with at least one recorded click.', 'whatsapp-chat-button' ); ?></p>
			</section>
		</div>

		<div class="wacb-admin-grid wacb-admin-grid--two-column">
			<section class="wacb-admin-card wacb-admin-card--compact">
				<div class="wacb-admin-card__header">
					<h2 class="wacb-admin-card__title"><?php echo esc_html__( 'Top Clicked Pages', 'whatsapp-chat-button' ); ?></h2>
					<p class="wacb-admin-card__description-text"><?php echo esc_html__( 'The most-clicked frontend URLs recorded by the plugin.', 'whatsapp-chat-button' ); ?></p>
				</div>

				<?php if ( ! empty( $analytics_summary['top_pages'] ) ) : ?>
					<div class="wacb-table-card">
						<table class="widefat striped">
							<thead>
								<tr>
									<th scope="col"><?php echo esc_html__( 'Page URL', 'whatsapp-chat-button' ); ?></th>
									<th scope="col"><?php echo esc_html__( 'Clicks', 'whatsapp-chat-button' ); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $analytics_summary['top_pages'] as $top_page ) : ?>
									<tr>
										<td class="wacb-table-cell-break wacb-analytics-url">
											<a href="<?php echo esc_url( $top_page['page_url'] ); ?>" target="_blank" rel="noopener noreferrer">
												<?php echo esc_html( $top_page['page_url'] ); ?>
											</a>
										</td>
										<td><?php echo esc_html( number_format_i18n( $top_page['click_count'] ) ); ?></td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				<?php else : ?>
					<div class="wacb-empty-state">
						<h3 class="wacb-empty-state__title"><?php echo esc_html__( 'No page-level click data yet', 'whatsapp-chat-button' ); ?></h3>
						<p class="wacb-empty-state__text"><?php echo esc_html__( 'Top clicked pages will appear here after visitors use the frontend button.', 'whatsapp-chat-button' ); ?></p>
					</div>
				<?php endif; ?>
			</section>

			<section class="wacb-admin-card wacb-admin-card--compact">
				<div class="wacb-admin-card__header">
					<h2 class="wacb-admin-card__title"><?php echo esc_html__( 'Device Breakdown', 'whatsapp-chat-button' ); ?></h2>
					<p class="wacb-admin-card__description-text"><?php echo esc_html__( 'Device detection uses WordPress core mobile detection and stores only a simple mobile or desktop label.', 'whatsapp-chat-button' ); ?></p>
				</div>

				<div class="wacb-table-card">
					<table class="widefat striped">
						<thead>
							<tr>
								<th scope="col"><?php echo esc_html__( 'Device', 'whatsapp-chat-button' ); ?></th>
								<th scope="col"><?php echo esc_html__( 'Clicks', 'whatsapp-chat-button' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><?php echo esc_html__( 'Mobile', 'whatsapp-chat-button' ); ?></td>
								<td><?php echo esc_html( number_format_i18n( $analytics_summary['device_breakdown']['mobile'] ) ); ?></td>
							</tr>
							<tr>
								<td><?php echo esc_html__( 'Desktop', 'whatsapp-chat-button' ); ?></td>
								<td><?php echo esc_html( number_format_i18n( $analytics_summary['device_breakdown']['desktop'] ) ); ?></td>
							</tr>
						</tbody>
					</table>
				</div>
			</section>
		</div>

		<section class="wacb-admin-card wacb-admin-card--compact">
			<div class="wacb-admin-card__header">
				<h2 class="wacb-admin-card__title"><?php echo esc_html__( 'Tracking Table', 'whatsapp-chat-button' ); ?></h2>
				<p class="wacb-admin-card__description-text"><?php echo esc_html__( 'The analytics summary below reflects the plugin\'s local tracking table only.', 'whatsapp-chat-button' ); ?></p>
			</div>

			<div class="wacb-table-card">
				<table class="widefat striped">
					<tbody>
						<tr>
							<th scope="row"><?php echo esc_html__( 'Tracking table', 'whatsapp-chat-button' ); ?></th>
							<td><span class="wacb-code-chip"><?php echo esc_html( $analytics_summary['table_name'] ); ?></span></td>
						</tr>
						<tr>
							<th scope="row"><?php echo esc_html__( 'Table status', 'whatsapp-chat-button' ); ?></th>
							<td><?php echo esc_html( $analytics_summary['table_exists_label'] ); ?></td>
						</tr>
						<tr>
							<th scope="row"><?php echo esc_html__( 'Total recorded clicks', 'whatsapp-chat-button' ); ?></th>
							<td><?php echo esc_html( number_format_i18n( $analytics_summary['total_clicks'] ) ); ?></td>
						</tr>
						<tr>
							<th scope="row"><?php echo esc_html__( 'Clicks today', 'whatsapp-chat-button' ); ?></th>
							<td><?php echo esc_html( number_format_i18n( $analytics_summary['clicks_today'] ) ); ?></td>
						</tr>
					</tbody>
				</table>
			</div>
		</section>
	</div>
</div>
