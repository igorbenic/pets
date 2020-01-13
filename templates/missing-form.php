<?php
/**
 * The missing form for pets.
 */


?>
<form class="pets-missing-form pets-search-form" method="POST" action="" enctype="multipart/form-data">
	<?php
		do_action( 'pets_missing_form_before_fields' );
		wp_nonce_field( 'wp-add-missing-pets', 'pets_missing_nonce' );
	?>

	<div class="pets-form-field search-field">
		<label for="missing_pets_name"><?php esc_html_e( 'Pet Name', 'pets' ); ?></label>
		<input required id="missing_pets_name" type="text" name="pets_name" placeholder="<?php esc_attr_e( 'Pet Name', 'pets' ); ?>"/>
	</div>

	<div class="pets-form-field search-field">
		<label for="missing_pets_info"><?php esc_html_e( 'Pet Description', 'pets' ); ?></label>
		<textarea required id="missing_pets_info" type="text" name="pets_description" placeholder="<?php esc_attr_e( 'Pet Description', 'pets' ); ?>"></textarea>
	</div>

	<div class="pets-form-field search-field">
		<label for="missing_pets_image"><?php esc_html_e( 'Pet Image', 'pets' ); ?></label>
		<input type="file" name="pets_image" accept="image/*" id="missing_pet_image" />
	</div>

	<button type="submit" class="button"><?php esc_html_e( 'Add Missing Pet', 'pets' ); ?></button>
</form>
