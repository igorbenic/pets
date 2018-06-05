<?php
/**
 * A class to manage a Single Pet
 */

namespace Pets;

/**
 * Class Pet
 *
 * @package Pets
 */
class Pet {

	/**
	 * @var int
	 */
	protected $id = 0;

	/**
	 * @var null|WP_Post
	 */
	protected $post = null;

	/**
	 * Pet constructor.
	 *
	 * @param int|WP_Post $post
	 * @param bool        $load If true, it will load the post object
	 */
	public function __construct( $post, $load = false ) {

		if ( is_numeric( $post ) ) {
			$this->set_id( $post );
			if ( $load ) {
				$this->set_post( get_post( $post ) );
			}
		} elseif ( is_a( $post, 'WP_Post' ) ) {
			$this->set_id( $post->ID );
			$this->set_post( $post );
		}
	}

	/**
	 * Set the ID.
	 * @param $id
	 */
	public function set_id( $id ) {
		$this->id = $id;
	}

	/**
	 * Set the Post.
	 *
	 * @param $post
	 */
	public function set_post( $post ) {
		$this->post = $post;
	}

	/**
	 * @return int
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * @return array
	 */
	public function get_information() {
		$pet_id = $this->get_id();
		if ( ! $pet_id ) {
			return array();
		}

		$fields = Fields::get_fields( $pet_id );

		$information = array();

		$breed = wp_get_post_terms( $pet_id, 'breed' );

		if ( $breed ) {
			$information[ __( 'Breed', 'pets' ) ] = implode( ', ', wp_list_pluck( $breed, 'name' ) );
		}

		$color = wp_get_post_terms( $pet_id, 'pet-color' );

		if ( $color ) {
			$information[ __( 'Color(s)', 'pets' ) ] = implode( ', ', wp_list_pluck( $color, 'name' ) );
		}

		if ( $fields ) {
			foreach ( $fields as $field ) {
				if ( null === $field['value'] || '' === $field['value'] ) {
					continue;
				}

				$information[ $field['title'] ] = $field['value'];
			}
		}

		return $information;
	}

	/**
	 * Get formatted information with HTML.
	 */
	public function get_formatted_information() {
		$information = $this->get_information();

		ob_start();
		pets_get_template( 'single/information', array( 'information' => $information ) );
		return ob_get_clean();
	}
}