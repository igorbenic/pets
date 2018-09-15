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
	 * Is the Post Loaded?
	 * @return bool
	 */
	private function is_loaded() {
		return null !== $this->post;
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
	 * Return the title of the Pet.
	 */
	public function get_title() {
		if ( $this->is_loaded() ) {
			return $this->post->post_title;
		}

		return get_the_title( $this->get_id() );
	}

	/**
	 * Get the Pet Link.
	 *
	 * @return string
	 */
	public function get_link() {
		if ( $this->is_loaded() ) {
			return get_permalink( $this->post );
		}

		return get_permalink( $this->get_id() );
	}

	/**
	 * Return a short description of the pet.
	 *
	 * @return string
	 */
	public function get_short_description() {
		$text = '';

		if ( $this->is_loaded() ) {
			$text = $this->post->post_content;
		}

		if ( ! $text ) {
			$this->post = get_post( $this->get_id() );
			$text       = $this->post->post_content;
		}

		if ( ! $text ) {
			return '';
		}

		return wp_trim_excerpt( $text );
	}

	/**
	 * Get the Pet Image.
	 *
	 * @param  string $size Size of the image.
	 *
	 * @return string
	 */
	public function get_image( $size = 'post-thumbnail' ) {
		return get_the_post_thumbnail( $this->get_id(), $size );
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

		$breed = wp_get_post_terms( $pet_id, 'breed' );
		$color = wp_get_post_terms( $pet_id, 'pet-color' );

		if ( $breed || $color ) {
			if ( ! isset( $fields[0] ) ) {
				$fields[0] = array(
					'title'  => __( 'Information', 'pets' ),
					'id'     => 0,
					'icon'   => '',
					'slug'   => 'information',
					'fields' => array(),
				);
			} elseif ( ! isset( $fields[0]['fields'] ) ) {
				$fields[0]['fields'] = array();
			}

			if ( $breed ) {
				$fields[0]['fields'][] = array(
					'value' => implode( ', ', wp_list_pluck( $breed, 'name' ) ),
					'title' => __( 'Breed', 'pets' ),
				);
			}

			if ( $color ) {
				$fields[0]['fields'][] = array(
					'value' => implode( ', ', wp_list_pluck( $color, 'name' ) ),
					'title' => __( 'Color(s)', 'pets' ),
				);
			}
		}

		return $fields;
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

	/**
	 * Getting Sponsors
	 *
	 * @return array
	 */
	public function get_sponsors() {
		$sponsors = get_the_terms( $this->get_id(), 'sponsors' );
		if ( ! $sponsors || is_wp_error( $sponsors ) ) {
			return array();
		}

		return $sponsors;
	}

	/**
	 * Get the formatted sponsors.
	 *
	 * @return string
	 */
	public function get_formatted_sponsors() {
		$sponsors = $this->get_sponsors();

		if ( ! $sponsors ) {
			return '';
		}

		ob_start();
		pets_get_template( 'single/sponsors', array( 'sponsors' => $sponsors ) );
		return ob_get_clean();
	}
}