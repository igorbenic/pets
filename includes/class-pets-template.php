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

		$fields = Fields::get_fields( get_the_ID() );

		$information = array();

		$breed = wp_get_post_terms( get_the_ID(), 'breed' );

		if ( $breed ) {
			$information[ __( 'Breed', 'pets' ) ] = implode( ', ', wp_list_pluck( $breed, 'name' ));
		}

		$color = wp_get_post_terms( get_the_ID(), 'pet-color' );

		if ( $color ) {
			$information[ __( 'Color(s)', 'pets' ) ] = implode( ', ', wp_list_pluck( $color, 'name' ));
		}

		if ( $fields ) {
			foreach ( $fields as $field ) {
				if ( null === $field['value'] || '' === $field['value'] ) {
					continue;
				}

				$information[ $field['title'] ] = $field['value'];
			}
		}

		if ( $information ) {
			ob_start();
			echo '<h3>' . __( 'Information', 'pets' ) . '</h3>';
			echo '<ul class="pets-fields pets-information">';
			foreach ( $information as $title => $value ) {
				echo '<li>';
				echo '<strong class="pets-field-title">' . $title . ':</strong>';
				echo $value;
				echo '</li>';
			}
			echo '</ul>';
			$info = ob_get_clean();
			$position = pets_get_setting( 'info_position', 'general', 'after' );

			if ( 'after' === $position ) {
				$content .= $info;
			} else {
				$info .= $content;
				$content = $info;
			}
		}

		return $content;
	}

}