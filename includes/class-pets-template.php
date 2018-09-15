<?php
/**
 * This class is used for various templating functions.
 */

namespace Pets;

/**
 * Class Pets_Template
 *
 * @package Pets
 */
class Pets_Template {

	/**
	 * Initialize the templating.
	 */
	public function init() {
		add_filter( 'the_content', array( $this, 'the_content' ) );
		add_filter( 'template_include', array( $this, 'include_template' ) );
	}

	/**
	 * Template
	 * @param $template
	 */
	public function include_template( $template ) {
		if ( is_post_type_archive( 'pets' ) ) {
			$template = pets_get_locate_template( 'archive/loop.php' );
		}

		return $template;
	}

	/**
	 * @param $content
	 *
	 * @return mixed
	 */
	public function the_content( $content ) {
		if ( ! is_singular( 'pets' ) ) {
			return $content;
		}

		$pet         = new Pet( get_the_ID() );
		$information = $pet->get_formatted_information();
		if ( $information ) {
			$position = pets_get_setting( 'info_position', 'general', 'after' );

			if ( 'after' === $position ) {
				$content .= $information;
			} else {
				$information .= $content;
				$content      = $information;
			}
		}

		$show_sponsors = absint( pets_get_setting( 'show_sponsors', 'sponsors', '1' ) );
		if ( $show_sponsors ) {
			$content .= $pet->get_formatted_sponsors();
		}

		return $content;
	}

}