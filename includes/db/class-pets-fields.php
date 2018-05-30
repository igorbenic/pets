<?php
/**
 * Fields Database Class Definition.
 */

Namespace Pets\DB;

use Pets\Pets_Cache;

class Fields {

    public static function install() {
        global $wpdb;

        $wpdb->hide_errors();

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        dbDelta( self::get_schema() );
    }

    private static function get_schema() {
    
        global $wpdb;

        $collate = '';

        if ( $wpdb->has_cap( 'collation' ) ) {
            $collate = $wpdb->get_charset_collate();
        }

        $tables = "
  CREATE TABLE {$wpdb->prefix}pets_fields (
  id BIGINT UNSIGNED NOT NULL auto_increment,
  title varchar(200) NOT NULL,
  slug  varchar(200) NOT NULL,
  type  varchar(200) NOT NULL,
  meta TEXT,
  PRIMARY KEY  (id)
) $collate;";

        return $tables;
    }

	/**
	 * Get Table Name
	 * @return string
	 */
    public function get_table_name() {
    	global $wpdb;

	    return $wpdb->prefix . 'pets_fields';
    }

	/**
	 * Creating a Field.
	 *
	 * @param $title
	 * @param $slug
	 * @param $type
	 */
    public function create( $title, $slug, $type, $meta ) {
		global $wpdb;

		Pets_Cache::delete_cache('fields');

		return $wpdb->insert(
			$this->get_table_name(),
			array(
				'title' => $title,
				'slug'  => $slug,
				'type'  => $type,
				'meta'  => maybe_serialize( $meta ),
			),
			array( '%s', '%s', '%s', '%s' )
		);
    }

	/**
	 * Creating a Field.
	 *
	 * @param $title
	 * @param $slug
	 * @param $type
	 */
	public function update( $id, $title, $slug, $type, $meta ) {
		global $wpdb;

		Pets_Cache::delete_cache('fields');

		return $wpdb->update(
			$this->get_table_name(),
			array(
				'title' => $title,
				'slug'  => $slug,
				'type'  => $type,
				'meta'  => maybe_serialize( $meta ),
			),
			array( 'id' => $id ),
			array( '%s', '%s', '%s', '%s' ),
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
