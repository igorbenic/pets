<?php
/**
 * The missing form for pets.
 */

$pet_fields_db = new \Pets\DB\Fields();
$pet_fields    = $pet_fields_db->get_all();
$pet_fields    = array_filter( $pet_fields, array( '\Pets\Fields', 'only_forms' ) );

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

	<?php
	if ( $pet_fields ) {
		?>
		<input type="hidden" name="missing_pets_fields" value="1" />
		<?php
		foreach( $pet_fields as $field ) {

			\Pets\Fields::render_field($field);
		}

	}
	?>

	<button type="submit" class="button"><?php esc_html_e( 'Add Missing Pet', 'pets' ); ?></button>
</form>
