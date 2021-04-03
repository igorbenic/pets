<?php
/**
 * Fields Database Class Definition.
 */

Namespace Pets\DB;

use Pets\Pets_Cache;

/**
 * Class Fields
 *
 * @package Pets\DB
 */
class Fields extends DB {

	protected $table_slug = 'pets_fields';
	/**
	 * Field Table Schema
	 * @return string
	 */
    protected static function get_schema() {

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
  field_section BIGINT UNSIGNED,
  searchable TINYINT UNSIGNED,
  forms TINYINT UNSIGNED,
  PRIMARY KEY  (id)
) $collate;";

        return $tables;
    }

	/**
	 * Create a Field.
	 *
	 * @param array $data
	 * @param array $format
	 *
	 * @return false|int
	 */
    public function create( $data, $format = array() ) {

		Pets_Cache::delete_cache('fields');
		if ( ! $format ) {
			$format = array( '%s', '%s', '%s', '%s', '%s', '%s' );
		}
		$data['meta'] = isset( $data['meta'] ) ? maybe_serialize( $data['meta'] ) : '';
		return parent::create( $data, $format );
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

		Pets_Cache::delete_cache('fields');
		if ( ! $format ) {
			$format = array( '%s', '%s', '%s', '%s', '%s', '%s' );
		}
		$data['meta'] = isset( $data['meta'] ) ? maybe_serialize( $data['meta'] ) : '';
		return parent::update( $id, $data, $format );
	}
}
