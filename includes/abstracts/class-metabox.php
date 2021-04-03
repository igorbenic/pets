<?php
/**
 * Created by PhpStorm.
 * User: igor
 * Date: 06/09/18
 * Time: 13:17
 */

namespace Pets\Admin;

/**
 * Class Pets_Metabox
 *
 * @package Pets\Admin
 */
class Pets_Metabox {

	/**
	 * Pets_Metabox constructor.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'metaboxes' ) );
		add_action( 'save_post', array( $this, 'save_post' ), 20, 2 );
	}

	/**
	 * Include the $file.
	 *
	 * @param string $file
	 * @param array  $data
	 */
	public function view( $file, $data ) {
		include PETS_PATH . 'includes/admin/post-types/meta-boxes/' . $file;
	}

	/**
	 * Registering metaboxes
	 */
	public function metaboxes() {}

	/**
	 * Save the post
	 *
	 * @param integer  $post_id
	 * @param \WP_Post $post
	 */
	public function save_post( $post_id, $post ) {}
}