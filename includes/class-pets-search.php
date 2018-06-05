<?php
/**
 * Created by PhpStorm.
 * User: igor
 * Date: 29/05/18
 * Time: 22:30
 */

namespace Pets;

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
}