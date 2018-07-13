<?php
/**
 * Field Form
 */

use \Pets\Fields;

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

$update = false;

$form_title = __( 'New Section', 'pets' );
$button     = __( 'Add Section', 'pets' );
$field      = array();

if ( isset( $_REQUEST['field'] ) && isset( $_REQUEST['action'] ) && 'edit' === $_REQUEST['action'] ) {
	$field_db   = new \Pets\DB\Fields_Sections();
	$field      = $field_db->get( absint( $_REQUEST['field'] ) );

	if ( $field ) {
		$update = true;
	}
}

if ( $update ) {
	$form_title = __( 'Edit Section', 'pets' );
	$button     = __( 'Save Changes', 'pets' );
}

?>
<form method="post" class="pets-form pets-form-field pets-form-add-field">
	<?php
	wp_nonce_field( 'pets_nonce_form', 'pets_nonce' );
	if ( $update ) {
		?>
		<input type="hidden" id="pets_field_id" name="pets_field_id" value="<?php echo esc_attr( absint( $_REQUEST['field'] ) ); ?>" />
		<?php
	}
	?>
	<h3>
		<?php echo esc_html( $form_title ); ?>
	</h3>
	<fieldset>
		<div class="field">
			<label for="pets_field_title"><?php esc_html_e( 'Title', 'pets' ); ?></label>
			<input class="widefat" type="text" name="pets_field_title" id="pets_field_title" value="<?php echo isset( $field['title'] ) ? esc_attr( $field['title'] ) : ''; ?>" />
		</div>
		<div class="field">
			<label for="pets_field_slug"><?php esc_html_e( 'Slug', 'pets' ); ?></label>
			<input class="widefat" type="text" name="pets_field_slug" id="pets_field_slug" value="<?php echo isset( $field['slug'] ) ? esc_attr( $field['slug'] ) : ''; ?>"/>
			<p class="description"><?php esc_html_e( 'If empty, it will be generated from the title.', 'pets' ); ?></p>
		</div>
		<div class="field">
			<label for="pets_field_icon_string"><?php esc_html_e( 'Icon', 'pets' ); ?></label>
			<ul id="pets_field_icon" class="pets_wp_gallery">
				<?php
				$icon_string = '';
				$icon_id     = '';
				if ( isset( $field['icon'] ) ) {
					$gallery = explode( ",", $field['icon'] );

					// If there is any ID, create the image for it
					if ( count( $gallery ) > 0 && $gallery[0] !== '' && is_numeric( $gallery[0] ) ) {
						$icon_id = $field['icon'];
						foreach ( $gallery as $attachment_id ) {

							// Create a LI elememnt
							$output = '<li tabindex="0" role="checkbox" aria-label="' . get_the_title( $attachment_id ) . '" aria-checked="true" data-id="' . $attachment_id . '" class="attachment save-ready selected details">';
							// Create a container for the image. (Copied from the WP Media Library Modal to use the same styling)
							$output .= '<div class="attachment-preview js--select-attachment type-image subtype-jpeg portrait">';
							$output .= '<div class="thumbnail">';

							$output .= '<div class="centered">';
							// Get the URL to that image thumbnail
							$output .= '<img src="' . wp_get_attachment_thumb_url( $attachment_id ) . '" draggable="false" alt="">';
							$output .= '</div>';

							$output .= '</div>';

							$output .= '</div>';
							// Add the button to remove this image if wanted (we set the data-gallery to target the correct gallery if there are more than one)
							$output .= '<button type="button" data-gallery="#pets_field_icon" class="button-link check pets-remove-single-image-field" tabindex="0"><span class="media-modal-icon"></span><span class="screen-reader-text">Deselect</span></button>';

							$output .= '</li>';
							echo $output;
						}
					} else {
						$icon_string = $field['icon'];
					}
				}
				?>
			</ul>
			<input type="text" id="pets_field_icon_string" name="pets_field_icon_string" value="<?php echo esc_attr( $icon_string ); ?>" />
            <p class="description"><?php echo sprintf( '<a href="https://fontawesome.com/icons" target="_blank">%s</a>', __( 'Check the Fontawesome FREE Icons and copy the whole class in here to use them.', 'pets' ) ); ?>. <?php esc_html_e( 'Or select an image using the button below.', 'pets' ); ?></p>
			<input type="hidden" id="pets_field_icon_input" name="pets_field_icon" value="<?php echo esc_attr( $icon_id ); ?>" />
			<button type="button" class="button button-primary pets-add-single-image-field" data-gallery="#pets_field_icon"><?php _e( 'Add Image', 'pets' ); ?></button>
		</div>
	</fieldset>
	<button type="submit" class="button button-primary pets-submit" name="pets_new_field_section_submit"><?php echo esc_html( $button ); ?></button>
	<?php if ( $update ) { ?>
		<a class="button button-secondary pets-submit" href="<?php echo esc_attr( admin_url( 'edit.php?post_type=pets&page=pets-fields&tab=sections' ) ); ?>"><?php esc_html_e( 'New Field', 'pets' ); ?></a>
	<?php } ?>
</form>
