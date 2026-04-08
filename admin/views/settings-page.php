<?php
/**
 * Admin settings page view.
 *
 * @package WhatsApp_Chat_Button
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap">
	<h1><?php echo esc_html__( 'WhatsApp Chat Button', 'whatsapp-chat-button' ); ?></h1>

	<nav class="nav-tab-wrapper" aria-label="<?php echo esc_attr__( 'Settings tabs', 'whatsapp-chat-button' ); ?>">
		<?php foreach ( $tabs as $tab_slug => $tab_label ) : ?>
			<?php
			$tab_url     = add_query_arg(
				array(
					'page' => $page_slug,
					'tab'  => $tab_slug,
				),
				admin_url( 'options-general.php' )
			);
			$tab_classes = 'nav-tab';

			if ( $active_tab === $tab_slug ) {
				$tab_classes .= ' nav-tab-active';
			}
			?>
			<a class="<?php echo esc_attr( $tab_classes ); ?>" href="<?php echo esc_url( $tab_url ); ?>">
				<?php echo esc_html( $tab_label ); ?>
			</a>
		<?php endforeach; ?>
	</nav>

	<?php if ( 'analytics' === $active_tab ) : ?>
		<div class="card">
			<h2><?php echo esc_html__( 'Analytics', 'whatsapp-chat-button' ); ?></h2>
			<p>
				<?php echo esc_html__( 'This screen shows lightweight click analytics collected when visitors use the WhatsApp button. The plugin stores only page URL, click time, and a simple device label.', 'whatsapp-chat-button' ); ?>
			</p>
			<?php if ( ! empty( $analytics_summary['empty_state_message'] ) ) : ?>
				<div class="notice notice-info inline">
					<p><?php echo esc_html( $analytics_summary['empty_state_message'] ); ?></p>
				</div>
			<?php endif; ?>
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

			<h3><?php echo esc_html__( 'Top Clicked Pages', 'whatsapp-chat-button' ); ?></h3>
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
				<p><?php echo esc_html__( 'No page-level click data is available yet.', 'whatsapp-chat-button' ); ?></p>
			<?php endif; ?>

			<h3><?php echo esc_html__( 'Device Breakdown', 'whatsapp-chat-button' ); ?></h3>
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
			<p class="description">
				<?php echo esc_html__( 'Device detection uses WordPress core mobile detection and stores only a simple mobile or desktop label.', 'whatsapp-chat-button' ); ?>
			</p>
		</div>
	<?php else : ?>
		<?php settings_errors( WACB_Settings_Manager::get_option_name() ); ?>
		<form method="post" action="options.php">
			<?php
			settings_fields( $settings_group );
			do_settings_sections( $settings_page );
			submit_button( __( 'Save Settings', 'whatsapp-chat-button' ) );
			?>
		</form>
	<?php endif; ?>
</div>
