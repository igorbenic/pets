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
class Pets_Type {

	/**
	 * Pets_Type constructor.
	 */
	public function __construct() {
		add_filter( 'bulk_actions-edit-pets', array( $this, 'add_bulk_actions' ) );
		add_filter( 'handle_bulk_actions-edit-pets', array( $this, 'handle_bulk_actions' ), 10, 3 );
		add_action( 'admin_notices', array( $this, 'add_notices' ) );
		add_filter( 'display_post_states', array( $this, 'add_pet_state'), 20, 2 );

	}

	/**
	 * Add notices for Pets changes
	 */
	public function add_notices() {

		if( ! empty( $_REQUEST['pets_set_missing_status'] ) ) {

			printf( '<div id="message" class="updated notice is-dismissible"><p>' .
			        _n( '%s pet set to missing.',
				        '%s pets are set to missing.',
				        intval( $_REQUEST['pets_set_missing_status'] )
			        ) . '</p></div>', intval( $_REQUEST['pets_set_missing_status'] ) );

		}

		if( ! empty( $_REQUEST['pets_set_publish_status'] ) ) {

			printf( '<div id="message" class="updated notice is-dismissible"><p>' .
			        _n( '%s pet set to published.',
				        '%s pets are set to published.',
				        intval( $_REQUEST['pets_set_publish_status'] )
			        ) . '</p></div>', intval( $_REQUEST['pets_set_publish_status'] ) );

		}


	}
	/**
	 * Adding your own post state (label)
	 *
	 * @param array   $states Array of all registered states.
	 * @param WP_Post $post   Post object that we can use.
	 */
	public function add_pet_state( $states, $post ) {
		if ( 'missing' === $post->post_status ) {

			$states['pet-missing'] = __( 'Missing Pet', 'pets' );

		}
		return $states;
	}

	/**
	 * Handle the bulk actions
	 *
	 * @param $redirect
	 * @param $doaction
	 * @param $object_ids
	 */
	public function handle_bulk_actions( $redirect, $doaction, $object_ids ) {

		$redirect = remove_query_arg( array( 'pets_set_missing_status', 'pets_set_publish_status' ), $redirect );

		// do something for "Make Draft" bulk action
		if ( $doaction == 'set-missing' ) {

			foreach ( $object_ids as $post_id ) {
				wp_update_post( array(
					'ID' => $post_id,
					'post_status' => 'missing' // set status
				) );
			}

			// do not forget to add query args to URL because we will show notices later
			$redirect = add_query_arg(
				'pets_set_missing_status', // just a parameter for URL (we will use $_GET['misha_make_draft_done'] )
				count( $object_ids ), // parameter value - how much posts have been affected
				$redirect );

		}

		if ( $doaction == 'set-publish' ) {

			foreach ( $object_ids as $post_id ) {
				wp_update_post( array(
					'ID' => $post_id,
					'post_status' => 'publish' // set status
				) );
			}

			// do not forget to add query args to URL because we will show notices later
			$redirect = add_query_arg(
				'pets_set_publish_status', // just a parameter for URL (we will use $_GET['misha_make_draft_done'] )
				count( $object_ids ), // parameter value - how much posts have been affected
				$redirect );

		}


		return $redirect;
	}

		/**
	 * Add Bulk Actions
	 * @param $actions
	 */
	public function add_bulk_actions( $actions ) {

		$actions['set-missing'] = __( 'Set to Missing', 'pets' );
		$actions['set-publish'] = __( 'Set to Published', 'pets' );

		return $actions;
	}

}

new Pets_Type();