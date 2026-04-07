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
			<h2><?php echo esc_html__( 'Analytics Foundation', 'whatsapp-chat-button' ); ?></h2>
			<p>
				<?php echo esc_html__( 'This screen is connected to the tracking engine foundation. Advanced reports, filters, and visualizations are intentionally deferred to a later phase.', 'whatsapp-chat-button' ); ?>
			</p>
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
				</tbody>
			</table>
			<p class="description">
				<?php echo esc_html__( 'Click logging, reporting filters, and page/device breakdowns will be added in a later phase once frontend event collection is in place.', 'whatsapp-chat-button' ); ?>
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
