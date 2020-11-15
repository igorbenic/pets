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
class Missing {

	/**
	 * Errors
	 *
	 * @var array
	 */
	public $errors = array();


	/**
	 * Notices
	 *
	 * @var array
	 */
	public $notices = array();

	public function __construct() {
		add_action( 'pets_missing_form_before_fields', array( $this, 'show_errors' ) );
		add_action( 'pets_missing_form_before_fields', array( $this, 'show_notices' ) );

	    add_action( 'init', array( $this, 'maybe_add_missing_pet' ) );
	}

	/**
	 * Show errors if any
	 */
	public function show_errors() {
		if ( ! $this->errors ) {
			return;
		}

		echo '<ul class="pets-errors">';
		foreach ( $this->errors as $error ) {
			?>
			<li class="pets-error">
				<?php echo esc_html( $error ); ?>
			</li>
			<?php
		}
		echo '</ul>';
	}

	/**
	 * Show errors if any
	 */
	public function show_notices() {
		if ( ! $this->notices ) {
			return;
		}

		echo '<ul class="pets-notices">';
		foreach ( $this->notices as $notice ) {
			?>
			<li class="pets-notice">
				<?php echo esc_html( $notice ); ?>
			</li>
			<?php
		}
		echo '</ul>';
	}

	/**
	 * Maybe submit a missing Pet.
	 */
	public function maybe_add_missing_pet() {
		if ( ! isset( $_POST['pets_missing_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['pets_missing_nonce'], 'wp-add-missing-pets' ) ) {
			die();
		}

		$name = isset( $_POST['pets_name'] ) ? sanitize_text_field( $_POST['pets_name'] ) : '';

		if ( ! $name ) {
			$this->errors[] = __( 'Pet Name is required', 'pets' );
			return;
		}

		$info = isset( $_POST['pets_description'] ) ? sanitize_text_field( $_POST['pets_description'] ) : '';

		if ( ! $info ) {
			$this->errors[] = __( 'Pet Description is required', 'pets' );
			return;
		}

		$pet_id = wp_insert_post(array(
			'post_type'    => 'pets',
			'post_title'   => $name,
			'post_content' => $info,
			'post_status'  => 'missing'
		));

		if ( is_wp_error( $pet_id ) ) {
			$this->errors[] = $pet_id->get_error_message();
			return;
		}

		$this->notices[] = __( 'Missing Pet was added', 'pets' );

		if ( isset( $_FILES['pets_image'] ) && $_FILES['pets_image']['name'] ) {
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
			require_once( ABSPATH . 'wp-admin/includes/media.php' );

			$attachment_id = media_handle_upload( 'pets_image', $pet_id );

			if ( is_wp_error( $attachment_id ) ) {
				$this->errors[] = sprintf( __( 'Pet image could not be uploaded. Reason: %s' ), $attachment_id->get_error_message() );
			} else {
				update_post_meta( $pet_id, '_thumbnail_id', $attachment_id );
			}
		}

		if ( isset( $_POST['missing_pets_fields'] ) ) {
			Fields::save_fields($pet_id, $_POST );
		}

		do_action( 'pets_missing_pet_added', $pet_id, $_POST );
	}
}
