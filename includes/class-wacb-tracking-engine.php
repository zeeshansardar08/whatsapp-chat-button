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
	 * Maximum stored page URL length.
	 *
	 * @var int
	 */
	const MAX_PAGE_URL_LENGTH = 2000;

	/**
	 * Supported device labels.
	 *
	 * @var string[]
	 */
	const ALLOWED_DEVICES = array( 'mobile', 'desktop' );

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

	/**
	 * Returns whether any click records exist.
	 *
	 * @param wpdb|null $wpdb_instance WordPress database object.
	 * @return bool
	 */
	public static function has_click_data( $wpdb_instance = null ) {
		return self::get_total_clicks( $wpdb_instance ) > 0;
	}

	/**
	 * Inserts a tracked click.
	 *
	 * @param string    $page_url      Clicked page URL.
	 * @param string    $device        Device label.
	 * @param wpdb|null $wpdb_instance WordPress database object.
	 * @return bool
	 */
	public static function insert_click( $page_url, $device = '', $wpdb_instance = null ) {
		global $wpdb;

		if ( ! $wpdb_instance instanceof wpdb ) {
			$wpdb_instance = $wpdb;
		}

		if ( ! $wpdb_instance instanceof wpdb || ! self::table_exists( $wpdb_instance ) ) {
			return false;
		}

		$sanitized_page_url = self::sanitize_page_url( $page_url );

		if ( '' === $sanitized_page_url ) {
			return false;
		}

		$inserted = $wpdb_instance->insert(
			self::get_table_name( $wpdb_instance ),
			array(
				'page_url'   => $sanitized_page_url,
				'clicked_at' => current_time( 'mysql' ),
				'device'     => self::sanitize_device_for_storage( $device ),
			),
			array( '%s', '%s', '%s' )
		);

		return false !== $inserted;
	}

	/**
	 * Returns the number of clicks recorded today in the site timezone.
	 *
	 * @param wpdb|null $wpdb_instance WordPress database object.
	 * @return int
	 */
	public static function get_clicks_today( $wpdb_instance = null ) {
		global $wpdb;

		if ( ! $wpdb_instance instanceof wpdb ) {
			$wpdb_instance = $wpdb;
		}

		if ( ! $wpdb_instance instanceof wpdb || ! self::table_exists( $wpdb_instance ) ) {
			return 0;
		}

		$table_name = self::get_table_name( $wpdb_instance );
		$start      = wp_date( 'Y-m-d 00:00:00' );
		$end        = wp_date( 'Y-m-d 23:59:59' );

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Table identifiers cannot be parameterized with $wpdb->prepare().
		$total_clicks = $wpdb_instance->get_var(
			$wpdb_instance->prepare(
				"SELECT COUNT(*) FROM {$table_name} WHERE clicked_at BETWEEN %s AND %s",
				$start,
				$end
			)
		);

		return absint( $total_clicks );
	}

	/**
	 * Returns the top clicked pages.
	 *
	 * @param int       $limit         Maximum number of pages to return.
	 * @param wpdb|null $wpdb_instance WordPress database object.
	 * @return array<int, array{page_url: string, click_count: int}>
	 */
	public static function get_top_pages( $limit = 5, $wpdb_instance = null ) {
		global $wpdb;

		if ( ! $wpdb_instance instanceof wpdb ) {
			$wpdb_instance = $wpdb;
		}

		if ( ! $wpdb_instance instanceof wpdb || ! self::table_exists( $wpdb_instance ) ) {
			return array();
		}

		$table_name = self::get_table_name( $wpdb_instance );
		$limit      = max( 1, absint( $limit ) );

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Table identifiers cannot be parameterized with $wpdb->prepare().
		$results = $wpdb_instance->get_results(
			"SELECT page_url, COUNT(*) AS click_count
			FROM {$table_name}
			GROUP BY page_url
			ORDER BY click_count DESC, page_url ASC
			LIMIT {$limit}",
			ARRAY_A
		);

		if ( ! is_array( $results ) ) {
			return array();
		}

		$normalized_results = array_map(
			array( __CLASS__, 'normalize_top_page_row' ),
			$results
		);

		return array_values(
			array_filter(
				$normalized_results,
				static function ( $result ) {
					return ! empty( $result['page_url'] );
				}
			)
		);
	}

	/**
	 * Returns click counts by device.
	 *
	 * @param wpdb|null $wpdb_instance WordPress database object.
	 * @return array<string, int>
	 */
	public static function get_device_breakdown( $wpdb_instance = null ) {
		global $wpdb;

		if ( ! $wpdb_instance instanceof wpdb ) {
			$wpdb_instance = $wpdb;
		}

		$breakdown = array(
			'mobile'  => 0,
			'desktop' => 0,
		);

		if ( ! $wpdb_instance instanceof wpdb || ! self::table_exists( $wpdb_instance ) ) {
			return $breakdown;
		}

		$table_name = self::get_table_name( $wpdb_instance );

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Table identifiers cannot be parameterized with $wpdb->prepare().
		$results = $wpdb_instance->get_results(
			"SELECT device, COUNT(*) AS click_count
			FROM {$table_name}
			GROUP BY device",
			ARRAY_A
		);

		if ( ! is_array( $results ) ) {
			return $breakdown;
		}

		foreach ( $results as $result ) {
			if ( ! is_array( $result ) || empty( $result['device'] ) ) {
				continue;
			}

			$device = sanitize_key( (string) $result['device'] );

			if ( in_array( $device, self::ALLOWED_DEVICES, true ) && isset( $breakdown[ $device ] ) ) {
				$breakdown[ $device ] = absint( $result['click_count'] );
			}
		}

		return $breakdown;
	}

	/**
	 * Detects the current request device type.
	 *
	 * @return string
	 */
	public static function detect_device() {
		return wp_is_mobile() ? 'mobile' : 'desktop';
	}

	/**
	 * Returns whether a page URL can be stored by the tracking engine.
	 *
	 * @param mixed $page_url Raw page URL.
	 * @return bool
	 */
	public static function is_valid_page_url( $page_url ) {
		return '' !== self::sanitize_page_url( $page_url );
	}

	/**
	 * Normalizes a top pages query row.
	 *
	 * @param array<string, mixed> $row Query row.
	 * @return array{page_url: string, click_count: int}
	 */
	private static function normalize_top_page_row( $row ) {
		return array(
			'page_url'    => self::sanitize_page_url( $row['page_url'] ?? '' ),
			'click_count' => absint( $row['click_count'] ?? 0 ),
		);
	}

	/**
	 * Sanitizes a tracked page URL.
	 *
	 * @param mixed $page_url Raw page URL.
	 * @return string
	 */
	private static function sanitize_page_url( $page_url ) {
		$page_url = esc_url_raw( (string) $page_url );
		$site_url = wp_parse_url( home_url( '/' ) );

		if ( ! is_string( $page_url ) || '' === $page_url || strlen( $page_url ) > self::MAX_PAGE_URL_LENGTH ) {
			return '';
		}

		if ( ! wp_http_validate_url( $page_url ) ) {
			return '';
		}

		$parsed_url = wp_parse_url( $page_url );

		if ( ! is_array( $parsed_url ) || ! is_array( $site_url ) ) {
			return '';
		}

		if ( empty( $parsed_url['scheme'] ) || ! in_array( $parsed_url['scheme'], array( 'http', 'https' ), true ) ) {
			return '';
		}

		if ( empty( $parsed_url['host'] ) || empty( $site_url['host'] ) ) {
			return '';
		}

		if ( strtolower( $parsed_url['host'] ) !== strtolower( $site_url['host'] ) ) {
			return '';
		}

		if ( self::get_normalized_url_port( $parsed_url ) !== self::get_normalized_url_port( $site_url ) ) {
			return '';
		}

		return is_string( $page_url ) ? $page_url : '';
	}

	/**
	 * Sanitizes the tracked device label before insert.
	 *
	 * @param mixed $device Raw device label.
	 * @return string
	 */
	private static function sanitize_device_for_storage( $device ) {
		$device = sanitize_key( (string) $device );

		if ( ! in_array( $device, self::ALLOWED_DEVICES, true ) ) {
			return self::detect_device();
		}

		return $device;
	}

	/**
	 * Returns the effective URL port, including the scheme default when omitted.
	 *
	 * @param array<string, mixed> $parsed_url Parsed URL array.
	 * @return int
	 */
	private static function get_normalized_url_port( $parsed_url ) {
		if ( isset( $parsed_url['port'] ) ) {
			return absint( $parsed_url['port'] );
		}

		$scheme = isset( $parsed_url['scheme'] ) ? strtolower( (string) $parsed_url['scheme'] ) : '';

		if ( 'https' === $scheme ) {
			return 443;
		}

		return 80;
	}
}
