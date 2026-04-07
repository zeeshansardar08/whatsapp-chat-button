<?php
/**
 * Tracking foundation and table helpers.
 *
 * @package WhatsApp_Chat_Button
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Tracking engine.
 */
class WACB_Tracking_Engine {

	/**
	 * Clicks table suffix.
	 *
	 * @var string
	 */
	const TABLE_SUFFIX = 'wacb_clicks';

	/**
	 * Returns the full table name.
	 *
	 * @param wpdb|null $wpdb_instance WordPress database object.
	 * @return string
	 */
	public static function get_table_name( $wpdb_instance = null ) {
		global $wpdb;

		if ( ! $wpdb_instance instanceof wpdb ) {
			$wpdb_instance = $wpdb;
		}

		if ( ! $wpdb_instance instanceof wpdb ) {
			return '';
		}

		return $wpdb_instance->prefix . self::TABLE_SUFFIX;
	}

	/**
	 * Returns the SQL schema for the clicks table.
	 *
	 * @param wpdb|null $wpdb_instance WordPress database object.
	 * @return string
	 */
	public static function get_schema( $wpdb_instance = null ) {
		global $wpdb;

		if ( ! $wpdb_instance instanceof wpdb ) {
			$wpdb_instance = $wpdb;
		}

		if ( ! $wpdb_instance instanceof wpdb ) {
			return '';
		}

		$table_name      = self::get_table_name( $wpdb_instance );
		$charset_collate = $wpdb_instance->get_charset_collate();

		return "CREATE TABLE {$table_name} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			page_url text NOT NULL,
			clicked_at datetime NOT NULL,
			device varchar(20) NOT NULL DEFAULT '',
			PRIMARY KEY  (id),
			KEY clicked_at (clicked_at)
		) {$charset_collate};";
	}

	/**
	 * Determines whether the tracking table exists.
	 *
	 * @param wpdb|null $wpdb_instance WordPress database object.
	 * @return bool
	 */
	public static function table_exists( $wpdb_instance = null ) {
		global $wpdb;

		if ( ! $wpdb_instance instanceof wpdb ) {
			$wpdb_instance = $wpdb;
		}

		if ( ! $wpdb_instance instanceof wpdb ) {
			return false;
		}

		$table_name = self::get_table_name( $wpdb_instance );

		if ( '' === $table_name ) {
			return false;
		}

		$found_table = $wpdb_instance->get_var(
			$wpdb_instance->prepare(
				'SHOW TABLES LIKE %s',
				$table_name
			)
		);

		return $table_name === $found_table;
	}

	/**
	 * Returns the total number of tracked clicks.
	 *
	 * @param wpdb|null $wpdb_instance WordPress database object.
	 * @return int
	 */
	public static function get_total_clicks( $wpdb_instance = null ) {
		global $wpdb;

		if ( ! $wpdb_instance instanceof wpdb ) {
			$wpdb_instance = $wpdb;
		}

		if ( ! $wpdb_instance instanceof wpdb || ! self::table_exists( $wpdb_instance ) ) {
			return 0;
		}

		$table_name = self::get_table_name( $wpdb_instance );

		if ( '' === $table_name ) {
			return 0;
		}

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Table identifiers cannot be parameterized with $wpdb->prepare().
		$total_clicks = $wpdb_instance->get_var( "SELECT COUNT(*) FROM {$table_name}" );

		return absint( $total_clicks );
	}
}
