<?php
/**
 * Field Form
 */

use \Pets\Fields;

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

$update = false;

if ( isset( $_REQUEST['field'] ) && isset( $_REQUEST['action'] ) && 'edit' === $_REQUEST['action'] ) {
    $update = true;
}

$form_title = __( 'New Field', 'pets' );
$button     = __( 'Add Field', 'pets' );
$field      = array();
$field_meta = '';

if ( $update ) {
    $form_title = __( 'Edit Field', 'pets' );
    $field_db   = new \Pets\DB\Fields();
    $field      = $field_db->get( absint( $_REQUEST['field'] ) );
    if ( $field['meta'] && is_array( $field['meta'] ) ) {
        $field_meta = 'data-options="' . esc_attr( wp_json_encode( $field['meta'] ) ) . '"';
    }
    $button = __( 'Save Changes', 'pets' );
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
            <label for="pets_field_type"><?php esc_html_e( 'Type', 'pets' ); ?></label>
            <select name="pets_field_type" id="pets_field_type">
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
        </div>
        <div class="field field-meta" <?php echo $field_meta; ?>></div>
    </fieldset>
	<button type="submit" class="button button-primary pets-submit" name="pets_new_field_submit"><?php echo esc_html( $button ); ?></button>
	<?php if ( $update ) { ?>
        <a class="button button-secondary pets-submit" href="<?php echo esc_attr( admin_url( 'edit.php?post_type=pets&page=pets-fields' ) ); ?>"><?php esc_html_e( 'New Field', 'pets' ); ?></a>
	<?php } ?>
</form>

<?php
    \Pets\Admin\Settings_Fields::field_templates();
?>

