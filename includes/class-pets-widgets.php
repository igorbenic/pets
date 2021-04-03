<?php
/**
 * Widgets for Pets.
 */

namespace Pets;

/**
 * Class Widgets
 *
 * @package Pets
 */
class Widgets {

	/**
	 * Register Widgets
	 */
	public function register_widgets() {
		$widgets = $this->get_widgets();
		if ( $widgets ) {
			foreach ( $widgets as $widget ) {
				if ( ! class_exists( $widget ) ) {
					continue;
				}
				register_widget( $widget );
			}
		}
	}

	/**
	 * Pets Widgets.
	 */
	public function get_widgets() {
		return apply_filters( 'pets_get_widgets', array(
			'\Pets\Widgets\Single_Pet',
			'\Pets\Widgets\Search',
			'\Pets\Widgets\Add_Missing',
			'\Pets\Widgets\Add',
		) );
	}
}
