<?php


namespace Pets;


class Pets_Emails {

	public function __construct() {
		add_action( 'pets_missing_pet_added', '\Pets\Pets_Emails::notify_on_missing_pet' );
		add_action( 'pets_new_pet_added', '\Pets\Pets_Emails::notify_on_new_pet' );
	}

	/**
	 * Notify user on new pet
	 *
	 * @param $pet_id
	 */
	public static function notify_on_new_pet( $pet_id ) {

		$emails = pets_get_setting( 'notify_on_new_emails', 'general', '' );

		if ( ! $emails ) {
			return;
		}

		$message = __( 'Hi, there is a new pet that was added on your site.', 'pets' );
		$message .= "\n" . sprintf( __( 'You can view the pet information at: %s ', 'pets' ), self::get_edit_link( $pet_id ) );

		wp_mail( $emails, __( 'New Pet Added', 'pets' ), $message );

	}

	public static function notify_on_missing_pet( $pet_id ) {
		$emails = pets_get_setting( 'notify_on_missing_emails', 'general', '' );

		if ( ! $emails ) {
			return;
		}

		$message = __( 'Hi, there is a new missing pet that was added on your site.', 'pets' );
		$message .= "\n" . sprintf( __( 'You can view the pet information at: %s ', 'pets' ), self::get_edit_link( $pet_id ) );

		wp_mail( $emails, __( 'Missing Pet Added', 'pets' ), $message );
	}

	public static function get_edit_link( $id, $context = 'display' ) {
		$post = get_post( $id );
		if ( ! $post ) {
			return '';
		}

		return admin_url( 'post.php?post=' . $id . '&action=edit');
	}
}
