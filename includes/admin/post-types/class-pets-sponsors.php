<?php
/**
 * Sponsors Post Type
 */
namespace Pets\Admin;

/**
 * Class Pets_Sponsors
 *
 * @package Pets\Admin
 */
class Pets_Sponsors extends Pets_Metabox {

	/**
	 * Register the metaboxes.
	 */
	public function metaboxes() {
		add_meta_box( 'pets-sponsors', __( 'Pets', 'pets' ), array( $this, 'metabox' ), 'sponsors' );
	}

	/**
	 * @param $post
	 */
	public function metabox( $post ) {
		$this->view( 'sponsors-pets.php', $post );
	}
}

new Pets_Sponsors();