<?php
/**
 * Fields Database Class Definition.
 */

Namespace Pets\DB;

/**
 * Class DB
 *
 * @package Pets\DB
 */
abstract class DB {

	protected $table_slug = '';

	/**
	 * Install.
	 */
	public static function install() {
		global $wpdb;

		$wpdb->hide_errors();

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( static::get_schema() );
	}

	/**
	 * Table Schema
	 */
	protected static function get_schema() { return ''; }

	/**
	 * Get Table Name
	 * @return string
	 */
	public function get_table_name() {
		global $wpdb;

		return $wpdb->prefix . $this->table_slug;
	}

	/**
	 * Creating a Field.
	 *
	 * @param array $data
	 * @param array $format
	 */
	public function create( $data, $format = array() ) {
		global $wpdb;

		return $wpdb->insert(
			$this->get_table_name(),
			$data,
			$format
		);
	}

	/**
	 * Creating a Field.
	 *
	 * @param $title
	 * @param $slug
	 * @param $type
	 */
	public function update( $id, $data, $format = array() ) {
		global $wpdb;

		return $wpdb->update(
			$this->get_table_name(),
			$data,
			array( 'id' => $id ),
			$format,
			array( '%d' )
		);
	}

	/**
	 * Get All Fields
	 */
	public function get_all() {
		global $wpdb;
		$results = $wpdb->get_results( "SELECT * FROM " . $this->get_table_name(), ARRAY_A );

		if ( $results ) {
			foreach ( $results as $order => $result ) {
				$results[ $order ] = array_map( 'maybe_unserialize', $result );
			}
		}

		return $results;
	}

	/**
	 * Returning a single Field.
	 *
	 * @param $id
	 */
	public function get( $id ) {
		global $wpdb;
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . $this->get_table_name() . " WHERE id=%d LIMIT 1", $id ), ARRAY_A );

		if ( $row ) {
			$row = array_map( 'maybe_unserialize', $row );
		}

		return $row;
	}

}
