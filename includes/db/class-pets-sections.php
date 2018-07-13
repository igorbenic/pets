<?php
/**
 * Field Sections Database Class Definition.
 */

Namespace Pets\DB;

/**
 * Class Fields_Sections
 *
 * @package Pets\DB
 */
class Fields_Sections extends DB {

	protected $table_slug = 'pets_fields_sections';
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
  CREATE TABLE {$wpdb->prefix}pets_fields_sections (
  id BIGINT UNSIGNED NOT NULL auto_increment,
  title varchar(200) NOT NULL,
  slug  varchar(200) NOT NULL,
  icon  varchar(200),
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

		if ( ! $format ) {
			$format = array( '%s', '%s', '%s' );
		}
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

		if ( ! $format ) {
			$format = array( '%s', '%s', '%s' );
		}
		return parent::update( $id, $data, $format );
	}
}
