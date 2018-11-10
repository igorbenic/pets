<?php
/**
 * Created by PhpStorm.
 * User: igor
 * Date: 29/05/18
 * Time: 22:30
 */

namespace Pets;

use Pets\Fields;

/**
 * Class Search
 *
 * @package Pets
 */
class Search {

	/**
	 * Adding the search form.
	 */
	public static function add_form() {
		if ( ! is_post_type_archive( 'pets' ) ) {
			return;
		}

		pets_locate_template( 'search-form.php', true );
	}

	/**
	 * How many pets we can view on the pets archive page.
	 *
	 * @param WP_Query $query
	 */
	public static function pets_per_page( $query ) {
		if ( is_admin() ) {
			return;
		}

		if ( ! $query->is_main_query() ) {
			return;
		}

		if ( ! is_post_type_archive( 'pets' ) ) {
			return;
		}

		$per_page = pets_get_setting( 'pets_per_page', 'general', 6 );
		$query->set( 'posts_per_page', $per_page );
	}

	/**
	 * How many pets we can view on the pets archive page.
	 *
	 * @param \WP_Query $query
	 */
	public static function pets_search_fields( $query ) {
		if ( is_admin() ) {
			return;
		}

		if ( ! $query->is_main_query() ) {
			return;
		}

		if ( ! is_post_type_archive( 'pets' ) ) {
			return;
		}

		$pets_search = isset( $_GET['pets_search'] ) ? $_GET['pets_search'] : false;

		if ( $pets_search ) {
			$meta_query = array();
			$fields     = Fields::get_cached_fields();
			$fields_slugs = array();
			foreach ( $fields as $field ) {
				$fields_slugs[ $field['slug'] ] = $field;
			}
			foreach ( $pets_search as $field_slug => $field_value ) {

				if ( 'all' === $field_value ) {
					continue;
				}

				if ( ! $field_value ) {
					continue;
				}

				$meta = array(
					'key'   => '_' . $field_slug,
					'value' => $field_value,
				);
				$field_config = $fields_slugs[ $field_slug ];
				if ( 'checkbox' === $field_config['type'] && absint( $field_value ) === 0 ) {
					$meta['value'] = '';
					$meta['compare'] = 'NOT EXISTS';
				}

				$meta_query[] = $meta;
			}
			if ( $meta_query ) {
				$query->set( 'meta_query', $meta_query );
			}
		}
	}
}
