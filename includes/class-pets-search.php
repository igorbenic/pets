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
}