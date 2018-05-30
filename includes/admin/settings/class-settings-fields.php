<?php

/**
 * Admin Settings Class
 */

namespace Pets\Admin;

use Pets\DB\Fields;

class Settings_Fields {

	/**
	 * Array of Errors.
	 *
	 * @var array
	 */
	private $errors = array();

    public function __construct() {
        add_action( 'pets_admin_page_pets-fields', array( $this, 'settings_page' ) );
		$this->create_field_on_post();
    }

	/**
	 * Get a Field Admin View
	 * @param $file
	 */
    public function get_view( $file ) {
    	$file_path = PETS_PATH . 'includes/admin/settings/views/fields/' . $file;
    	if ( \file_exists( $file_path ) ) {
    		include $file_path;
	    }
    }

    /**
     * Main Settings Page
     * @return void 
     */
    public function settings_page() {
        include PETS_PATH . 'includes/admin/class-pets-fields-table.php';
        $table = new Fields_Table();
        $table->prepare_items();
        ?>
	    <form method="post">
		    <?php
            $table->display();
            ?>
	    </form>
	    <?php
	    $this->form();
    }

	/**
	 * Fields Form
	 */
    public function form() {
    	if ( $this->errors ) {
    		foreach( $this->errors as $error ) {
    			?>
			    <div class="notice error"><p><?php echo $error; ?></p></div>
				<?php
		    } 
	    }
		$this->get_view( 'form.php' );
    }

	/**
	 * Create Field from Form
	 */
    public function create_field_on_post() {
		if ( isset( $_POST['pets_new_field_submit'] ) ) {

		    if ( ! isset( $_POST['pets_nonce'] )
                || ! wp_verify_nonce( $_POST['pets_nonce'], 'pets_nonce_form')
            ) {
			    wp_die( __( 'Trying to cheat?', 'pets' ) );
			    return;
            }

			$title = isset( $_POST['pets_field_title'] ) && $_POST['pets_field_title'] ? sanitize_text_field( $_POST['pets_field_title'] ) : false;
			if ( ! $title ) {
				$this->errors[] = __( 'Field Title Empty.', 'pets' );
				return;
			}

			$slug = isset( $_POST['pets_field_slug'] ) && $_POST['pets_field_slug'] ? sanitize_text_field( $_POST['pets_field_slug'] ) : false;
			if ( ! $slug ) {
				$slug = sanitize_title( $title );
			}
			$slug = str_replace( '-', '_', $slug );

			$type = isset( $_POST['pets_field_type'] ) && '0' !== $_POST['pets_field_type'] ? sanitize_text_field( $_POST['pets_field_type'] ) : false;
			if ( ! $type ) {
				$this->errors[] = __( 'Select a Field Type.', 'pets' );
				return;
			}

			$meta = isset( $_POST['pets_field_meta'] ) ? $_POST['pets_field_meta'] : array();
			$id   = isset( $_POST['pets_field_id'] ) ? absint( $_POST['pets_field_id'] ) : false;

			$pets_fields_db = new Fields();
			if ( ! $id ) {
				$ret = $pets_fields_db->create( $title, $slug, $type, $meta );
			} else {
			    $ret = $pets_fields_db->update( $id, $title, $slug, $type, $meta );
            }
			if ( false === $ret ) {
				$this->errors[] = __( 'Something went wrong. We could not create the field.', 'pets' );
				return;
			}
			if ( is_wp_error( $ret ) ) {
				$this->errors[] = $ret->get_error_message();
				return;
			}

			echo '<div class="notice updated"><p>' . __( 'Field Created.', 'pets' ) . '</p></div>';
		}
    }

	/**
	 * Get the Field Templates
	 */
    public static function field_templates() {
        ?>
        <script type="text/template" id="tmpl-multi">
            <# var option = ''; if( data.options ) { option = data.options[0]; } console.log(option);#>
            <div class="pets-field-meta-inputs">
                <div class="pets-field-meta-input">
                    <input type="text" class="widefat" name="pets_field_meta[options][]" value="{{ option  }}" placeholder="<?php echo esc_attr( 'Enter an Option', 'pets' ) ?>">
                    <button type="button" class="button button-secondary button-small pets-remove-meta-field" data-type="multi">-</button>
                </div>
                <#
                if ( data.options ) {
                    for( var i = 1; i < data.options.length; i++ ) { #>
                        <div class="pets-field-meta-input">
                            <input type="text" class="widefat" name="pets_field_meta[options][]" value="{{ data.options[ i ] }}" placeholder="<?php echo esc_attr( 'Enter an Option', 'pets' ) ?>">
                            <button type="button" class="button button-secondary button-small pets-remove-meta-field" data-type="multi">-</button>
                        </div>
                    <#
                    }
                }
                #>
            </div>
            <button type="button" class="button button-secondary pets-add-meta-field" data-type="multi">+</button>
        </script>
        <script type="text/template" id="tmpl-multi-input">
            <div class="pets-field-meta-input">
                <input type="text" class="widefat" name="pets_field_meta[options][]" value="{{ data.value || '' }}" placeholder="<?php echo esc_attr( 'Enter an Option', 'pets' ) ?>">
                <button type="button" class="button button-secondary button-small pets-remove-meta-field" data-type="multi">-</button>
            </div>
        </script>
        <?php
        do_action('pets_fields_after_form_templates' );
    }
}

new Settings_Fields();