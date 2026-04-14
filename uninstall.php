<?php
/**
 * Fired during plugin uninstall.
 *
 * @package WhatsApp_Chat_Button
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

require_once dirname( __FILE__ ) . '/includes/class-wacb-settings-manager.php';
require_once dirname( __FILE__ ) . '/includes/class-wacb-tracking-engine.php';

/**
 * Removes plugin data for a single site.
 *
 * @return void
 */
function wacb_uninstall_site_data() {
	global $wpdb;

	delete_option( WACB_Settings_Manager::get_option_name() );
	delete_option( 'wacb_version' );
	delete_option( 'wacb_db_version' );
	delete_transient( 'wacb_admin_notices' );

	$table_name = '';

	if ( $wpdb instanceof wpdb ) {
		$table_name = str_replace( '`', '``', WACB_Tracking_Engine::get_table_name( $wpdb ) );
	}

	if ( '' !== $table_name && $wpdb instanceof wpdb ) {
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Table identifiers cannot be passed through $wpdb->prepare().
		$wpdb->query( "DROP TABLE IF EXISTS `{$table_name}`" );
	}
}

if ( is_multisite() ) {
	$site_ids = get_sites(
		array(
			'fields' => 'ids',
		)
	);

	foreach ( $site_ids as $site_id ) {
		switch_to_blog( (int) $site_id );
		wacb_uninstall_site_data();
		restore_current_blog();
	}
} else {
	wacb_uninstall_site_data();
}
