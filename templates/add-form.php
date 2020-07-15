<?php
/**
 * The add form for pets.
 */


?>
<form class="pets-missing-form pets-search-form" method="POST" action="" enctype="multipart/form-data">
	<?php
	do_action( 'pets_add_form_before_fields' );
	wp_nonce_field( 'wp-add-new-pets', 'pets_add_nonce' );
	?>

	<div class="pets-form-field search-field">
		<label for="new_pets_name"><?php esc_html_e( 'Pet Name', 'pets' ); ?></label>
		<input required id="new_pets_name" type="text" name="pets_name" placeholder="<?php esc_attr_e( 'Pet Name', 'pets' ); ?>"/>
	</div>

	<div class="pets-form-field search-field">
		<label for="new_pets_info"><?php esc_html_e( 'Pet Description', 'pets' ); ?></label>
		<textarea required id="new_pets_info" type="text" name="pets_description" placeholder="<?php esc_attr_e( 'Pet Description', 'pets' ); ?>"></textarea>
	</div>

	<div class="pets-form-field search-field">
		<label for="new_pets_image"><?php esc_html_e( 'Pet Image', 'pets' ); ?></label>
		<input type="file" name="new_pets_image" accept="image/*" id="new_pets_image" />
	</div>

	<button type="submit" class="button"><?php esc_html_e( 'Add a Pet', 'pets' ); ?></button>
</form>
