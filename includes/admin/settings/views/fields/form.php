<?php
/**
 * Field Form
 */

use \Pets\Fields;

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

$update = false;

$form_title = __( 'New Field', 'pets' );
$button     = __( 'Add Field', 'pets' );
$field      = array();
$field_meta = '';
$db_sect    = new Pets\DB\Fields_Sections();
$sections   = $db_sect->get_all();

if ( isset( $_REQUEST['field'] ) && isset( $_REQUEST['action'] ) && 'edit' === $_REQUEST['action'] ) {
	$field_db = new \Pets\DB\Fields();
	$field    = $field_db->get( absint( $_REQUEST['field'] ) );

	if ( $field ) {
		$update = true;
		if ( $field['meta'] && is_array( $field['meta'] ) && isset( $field['meta']['options'] ) ) {
			$field_meta = 'data-options="' . esc_attr( wp_json_encode( $field['meta']['options'] ) ) . '"';
		}
	}
}

if ( $update ) {
	$form_title = __( 'Edit Field', 'pets' );
	$button     = __( 'Save Changes', 'pets' );
}

if ( ! isset( $field['searchable'] ) ) {
    $field['searchable'] = 0;
}

if ( ! isset( $field['forms'] ) ) {
	$field['forms'] = 0;
}

$multiple_search = isset( $field['meta'] ) && isset( $field['meta']['multiple_search'] ) && 'yes' === $field['meta']['multiple_search'] ? 1 : 0;

?>
<form method="post" class="pets-form pets-form-field pets-form-add-field">
	<?php
	wp_nonce_field( 'pets_nonce_form', 'pets_nonce' );
	if ( $update ) {
		?>
        <input type="hidden" id="pets_field_id" name="pets_field_id"
               value="<?php echo esc_attr( absint( $_REQUEST['field'] ) ); ?>"/>
		<?php
	}
	?>
    <h3>
		<?php echo esc_html( $form_title ); ?>
    </h3>
    <fieldset>
        <div class="field">
            <label for="pets_field_title"><?php esc_html_e( 'Title', 'pets' ); ?></label>
            <input class="widefat" type="text" name="pets_field_title" id="pets_field_title"
                   value="<?php echo isset( $field['title'] ) ? esc_attr( $field['title'] ) : ''; ?>"/>
        </div>
        <div class="field">
            <label for="pets_field_slug"><?php esc_html_e( 'Slug', 'pets' ); ?></label>
            <input class="widefat" type="text" name="pets_field_slug" id="pets_field_slug"
                   value="<?php echo isset( $field['slug'] ) ? esc_attr( $field['slug'] ) : ''; ?>"/>
            <p class="description"><?php esc_html_e( 'If empty, it will be generated from the title.', 'pets' ); ?></p>
        </div>
        <div class="field">
            <label for="pets_field_searchable"><?php esc_html_e( 'Searchable?', 'pets' ); ?></label>
            <input <?php checked( $field['searchable'], 1, true ); ?> class="widefat" type="checkbox" name="pets_field_searchable" id="pets_field_searchable"
                   value="yes" />
            <p class="description"><?php esc_html_e( 'If checked, it will appear in the search form.', 'pets' ); ?></p>
            <label for="pets_field_multiple_search">
                <input <?php checked( $multiple_search, 1, true ); ?> class="widefat" type="checkbox" name="pets_field_meta[multiple_search]" id="pets_field_multiple_search" value="yes" />
	            <?php esc_html_e( 'If searchable, allow multiple selection in the search form. Works for fields with options (Radio, Dropdown).', 'pets' ); ?>
            </label>
        </div>
		<div class="field">
			<label for="pets_field_forms"><?php esc_html_e( 'Forms?', 'pets' ); ?></label>
			<input <?php checked( $field['forms'], 1, true ); ?> class="widefat" type="checkbox" name="pets_field_forms" id="pets_field_forms"
																	  value="yes" />
			<p class="description"><?php esc_html_e( 'If checked, it will appear in the forms (Add & Add Missing widgets).', 'pets' ); ?></p>

		</div>
        <div class="field">
            <label for="pets_field_sections"><?php esc_html_e( 'Sections', 'pets' ); ?></label>
            <select class="widefat" name="pets_field_sections" id="pets_field_sections">
                <option value="0"><?php esc_html_e( 'Information', 'pets' ); ?></option>
				<?php
				if ( $sections ) {
					foreach ( $sections as $section ) {
						echo '<option value="' . $section['id'] . '">' . $section['title'] . '</option>';
					}
				}
				?>
            </select>
            <p class="description"><?php esc_html_e( 'If empty, it will be generated from the title.', 'pets' ); ?></p>
        </div>
        <div class="field">
            <label for="pets_field_type"><?php esc_html_e( 'Type', 'pets' ); ?></label>
            <select class="widefat" name="pets_field_type" id="pets_field_type">
                <option value="0"><?php esc_html_e( 'Choose a Type', 'pets' ); ?></option>
				<?php
				$field_type = isset( $field['type'] ) ? esc_attr( $field['type'] ) : '';
				$types      = Fields::get_field_types();
				if ( $types && is_array( $types ) ) {
					foreach ( $types as $type => $data ) {
						echo '<option ' . selected( $field_type, $type, false ) . ' value="' . esc_attr( $type ) . '">' . $data['text'] . '</option>';
					}
				}
				?>
            </select>
            <div class="field field-meta" <?php echo $field_meta; ?>></div>
        </div>

    </fieldset>
    <button type="submit" class="button button-primary pets-submit"
            name="pets_new_field_submit"><?php echo esc_html( $button ); ?></button>
	<?php if ( $update ) { ?>
        <a class="button button-secondary pets-submit"
           href="<?php echo esc_attr( admin_url( 'edit.php?post_type=pets&page=pets-fields' ) ); ?>"><?php esc_html_e( 'New Field', 'pets' ); ?></a>
	<?php } ?>
</form>

<?php
\Pets\Admin\Settings_Fields::field_templates();
?>

