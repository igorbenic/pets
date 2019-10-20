<?php
/**
 * Registering Shortcodes.
 */

namespace Pets;

/**
 * Class Shortcodes
 *
 * @package Pets
 */
class Shortcodes {

	/**
	 * Shortcodes constructor.
	 */
	public function __construct() {
		$shortcodes = $this->get_shortcodes();
		if ( $shortcodes ) {
			foreach ( $shortcodes as $shortcode => $shortcode_cb ) {
				add_shortcode( 'pets_' . $shortcode, $shortcode_cb );
			}
		}
	}

	/**
	 * Return all registered shortcodes.
	 * @return array {
	 *      $key: string Shortcode slug without 'pets_' => $value: function to be called.
	 * }
	 */
	public function get_shortcodes() {
		return apply_filters( 'pets_shortcodes', array(
			'single' => array( $this, 'single_pet' ),
			'petfinder' => array( $this, 'petfinder' ),
		));
	}

	/**
	 * Showing a single pet
	 */
	public function petfinder( $args ) {

		ob_start();

		pets_locate_template( 'shortcode/petfinder.php', true );

		return ob_get_clean();
	}

	/**
	 * Showing a single pet
	 */
	public function single_pet( $args ) {
		$atts = shortcode_atts(
			array(
				'id'    => 0,
				'info'  => true,
				'image' => true,
			),
			$args,
			'pets_single'
		);

		$wp_args = array(
			'post_type'   => 'pets',
			'post_status' => 'publish',
			'posts_per_page' => 1,
		);

		if ( ! $atts['id'] ) {
			$wp_args['orderby'] = 'rand';
		} else {
			$wp_args['p'] = absint( $atts['id'] );
		}

		ob_start();
		$query = new \WP_Query( $wp_args );
		if ( $query->have_posts() ) {
			while( $query->have_posts() ) {
				$query->the_post();
				$pet = new Pet( get_the_ID(), true );
				if ( 'false' !== strtolower( $atts['image'] ) ) {
					$image = $pet->get_image();
					if ( $image ) {
						echo $image;
					}
				}
				echo '<h3><a href="' . $pet->get_link() . '">' . $pet->get_title() . '</a></h3>';
				echo $pet->get_short_description();
				if ( 'false' !== strtolower( $atts['info'] ) ) {
					echo $pet->get_formatted_information();
				}
			}
			wp_reset_postdata();
		}
		return ob_get_clean();
	}
}