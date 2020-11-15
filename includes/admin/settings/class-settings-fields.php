<?php

/**
 * Admin Settings Class
 */

namespace Pets\Admin;

use Pets\DB\Fields;
use Pets\DB\Fields_Sections;

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
		$this->create_field_section_on_post();
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
        $active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'fields';

	    echo '<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">';

        echo '<a href="' . admin_url( 'edit.php?post_type=pets&page=pets-fields&tab=fields' ) . '" class="nav-tab ' .  ( ( 'fields' === $active_tab ) ? 'nav-tab-active' : '' ) . ' ">' . __( 'Fields', 'pets' ) . '</a>';
	    echo '<a href="' . admin_url( 'edit.php?post_type=pets&page=pets-fields&tab=sections' ) . '" class="nav-tab ' .  ( ( 'sections' === $active_tab ) ? 'nav-tab-active' : '' ) . ' ">' . __( 'Sections', 'pets' ) . '</a>';

	    echo '</h2>';
	    echo '<br/>';

	    switch ( $active_tab ) {
            case 'sections':
                $this->page_sections();
                break;
            default:
                $this->page_fields();
                break;
        }
    }

	/**
	 * Fields Sections Page
	 */
	public function page_sections() {
		include PETS_PATH . 'includes/admin/class-pets-fields-sections-table.php';
		$table = new Fields_Sections_Table();
		$table->prepare_items();
		?>
        <form method="post">
			<?php
			$table->display();
			?>
        </form>
		<?php
		$this->form_sections();
	}

	/**
	 * Fields Page
	 */
    public function page_fields() {
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
	 * Fields Form Sections
	 */
	public function form_sections() {
		if ( $this->errors ) {
			foreach( $this->errors as $error ) {
				?>
                <div class="notice error"><p><?php echo $error; ?></p></div>
				<?php
			}
		}
		$this->get_view( 'form-sections.php' );
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

			$meta    = isset( $_POST['pets_field_meta'] ) ? $_POST['pets_field_meta'] : array();
			$id      = isset( $_POST['pets_field_id'] ) ? absint( $_POST['pets_field_id'] ) : false;
			$section = isset( $_POST['pets_field_sections'] ) ? absint( $_POST['pets_field_sections'] ) : 0;
			$search  = isset( $_POST['pets_field_searchable'] ) ? 1 : 0;
			$forms   = isset( $_POST['pets_field_forms'] ) ? 1 : 0;

			$data = array(
                'title'         => $title,
                'slug'          => $slug,
                'type'          => $type,
                'meta'          => $meta,
                'field_section' => $section,
                'searchable'    => $search,
				'forms'         => $forms
            );

			$pets_fields_db = new Fields();
			if ( ! $id ) {
				$ret = $pets_fields_db->create( $data );
			} else {
			    $ret = $pets_fields_db->update( $id, $data );
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
	 * Create Field Section from Form
	 */
	public function create_field_section_on_post() {
		if ( isset( $_POST['pets_new_field_section_submit'] ) ) {

			if ( ! isset( $_POST['pets_nonce'] )
			     || ! wp_verify_nonce( $_POST['pets_nonce'], 'pets_nonce_form')
			) {
				wp_die( __( 'Trying to cheat?', 'pets' ) );
				return;
			}

			$title = isset( $_POST['pets_field_title'] ) && $_POST['pets_field_title'] ? sanitize_text_field( $_POST['pets_field_title'] ) : false;
			if ( ! $title ) {
				$this->errors[] = __( 'Section Title Empty.', 'pets' );
				return;
			}

			$slug = isset( $_POST['pets_field_slug'] ) && $_POST['pets_field_slug'] ? sanitize_text_field( $_POST['pets_field_slug'] ) : false;
			if ( ! $slug ) {
				$slug = sanitize_title( $title );
			}
			$slug = str_replace( '-', '_', $slug );

			$icon = isset( $_POST['pets_field_icon'] ) ? $_POST['pets_field_icon'] : '';
			if ( ! $icon ) {
			    $icon = isset( $_POST['pets_field_icon_string'] ) ? $_POST['pets_field_icon_string'] : '';
            }
			$id   = isset( $_POST['pets_field_id'] ) ? absint( $_POST['pets_field_id'] ) : false;

			$data = array(
				'title' => $title,
				'slug'  => $slug,
				'icon'  => $icon,
			);
			$pets_fields_db = new Fields_Sections();
			if ( ! $id ) {
				$ret = $pets_fields_db->create( $data );
			} else {
				$ret = $pets_fields_db->update( $id, $data );
			}
			if ( false === $ret ) {
				$this->errors[] = __( 'Something went wrong. We could not create the field.', 'pets' );
				return;
			}
			if ( is_wp_error( $ret ) ) {
				$this->errors[] = $ret->get_error_message();
				return;
			}

			echo '<div class="notice updated"><p>' . __( 'Field Section Created.', 'pets' ) . '</p></div>';
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
