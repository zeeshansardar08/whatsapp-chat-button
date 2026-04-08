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
		<div>
			<h1 class="wacb-admin-page__title"><?php echo esc_html__( 'Analytics', 'whatsapp-chat-button' ); ?></h1>
			<p class="wacb-admin-page__intro">
				<?php echo esc_html__( 'Review the lightweight click analytics collected when visitors use the WhatsApp button. The plugin stores only page URL, click time, and a simple device label.', 'whatsapp-chat-button' ); ?>
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
		</section>

		<section class="wacb-admin-card wacb-admin-metric-card">
			<p class="wacb-admin-metric-card__label"><?php echo esc_html__( 'Tracked devices', 'whatsapp-chat-button' ); ?></p>
			<p class="wacb-admin-metric-card__value"><?php echo esc_html( number_format_i18n( $analytics_summary['tracked_devices'] ) ); ?></p>
		</section>
	</div>

	<div class="wacb-admin-grid wacb-admin-grid--two-column">
		<section class="wacb-admin-card">
			<div class="wacb-admin-card__header">
				<h2 class="wacb-admin-card__title"><?php echo esc_html__( 'Top Clicked Pages', 'whatsapp-chat-button' ); ?></h2>
				<p class="wacb-admin-card__description-text"><?php echo esc_html__( 'The most-clicked frontend URLs recorded by the plugin.', 'whatsapp-chat-button' ); ?></p>
			</div>

			<?php if ( ! empty( $analytics_summary['top_pages'] ) ) : ?>
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
								<td>
									<a href="<?php echo esc_url( $top_page['page_url'] ); ?>" target="_blank" rel="noopener noreferrer">
										<?php echo esc_html( $top_page['page_url'] ); ?>
									</a>
								</td>
								<td><?php echo esc_html( number_format_i18n( $top_page['click_count'] ) ); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php else : ?>
				<p class="wacb-admin-empty-state"><?php echo esc_html__( 'No page-level click data is available yet.', 'whatsapp-chat-button' ); ?></p>
			<?php endif; ?>
		</section>

		<section class="wacb-admin-card">
			<div class="wacb-admin-card__header">
				<h2 class="wacb-admin-card__title"><?php echo esc_html__( 'Device Breakdown', 'whatsapp-chat-button' ); ?></h2>
				<p class="wacb-admin-card__description-text"><?php echo esc_html__( 'Device detection uses WordPress core mobile detection and stores only a simple mobile or desktop label.', 'whatsapp-chat-button' ); ?></p>
			</div>

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
		</section>
	</div>

	<section class="wacb-admin-card">
		<div class="wacb-admin-card__header">
			<h2 class="wacb-admin-card__title"><?php echo esc_html__( 'Tracking Table', 'whatsapp-chat-button' ); ?></h2>
			<p class="wacb-admin-card__description-text"><?php echo esc_html__( 'The analytics summary below reflects the plugin’s local tracking table only.', 'whatsapp-chat-button' ); ?></p>
		</div>

		<table class="widefat striped">
			<tbody>
				<tr>
					<th scope="row"><?php echo esc_html__( 'Tracking table', 'whatsapp-chat-button' ); ?></th>
					<td><code><?php echo esc_html( $analytics_summary['table_name'] ); ?></code></td>
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
	</section>
