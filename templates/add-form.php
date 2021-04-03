<?php
/**
 * The add form for pets.
 */

$pet_fields_db = new \Pets\DB\Fields();
$pet_fields    = $pet_fields_db->get_all();
$pet_fields    = array_filter( $pet_fields, array( '\Pets\Fields', 'only_forms' ) );

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

	<?php
		if ( $pet_fields ) {
			?>
			<input type="hidden" name="new_pets_fields" value="1" />
			<?php
			foreach( $pet_fields as $field ) {

				\Pets\Fields::render_field($field);
			}

		}
	?>

	<button type="submit" class="button"><?php esc_html_e( 'Add a Pet', 'pets' ); ?></button>
</form>
