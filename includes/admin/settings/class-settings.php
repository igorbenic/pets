<?php

/**
 * Admin Settings Class
 */

namespace Pets\Admin;

class Settings {

    /**
     * Including the Settings Page
     * @return void 
     */
    public function page() {
        include 'views/main.php';
    }

    /**
     * Loading Settings
     * @return void 
     */
    public function load() {
        include 'class-settings-fields.php';
        add_action( 'pets_admin_page_pets-settings', array( $this, 'settings_page' ) );
        add_action( 'pets_settings_form_fields', array( $this, 'render_fields' ) );
    }

	/**
	 * Save the settings for the current tab.
	 */
    public function save_settings() {
		if ( ! isset( $_POST['pets_settings_save'] ) ) {
			return;
		}

		$active_tab = $_POST['pets_settings_save'];

		if ( ! isset( $_POST['pets_' . $active_tab ] ) ) {
			return;
		}

		update_option( 'pets_' . $active_tab, $_POST['pets_' . $active_tab ] );

		do_action( 'pets_settings_updated', $active_tab );
    }

    /**
     * Main Settings Page
     * @return void 
     */
    public function settings_page() {
    	$this->save_settings();
    	$active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'general';
    	$tabs       = $this->get_tabs();

    	if ( $tabs ) {
		    echo '<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">';
		    foreach ( $tabs as $tab => $title ) {
				    echo '<a href="' . admin_url( 'edit.php?post_type=pets&page=pets-settings&tab=' . $tab ) . '" class="nav-tab ' .  ( ( $tab === $active_tab ) ? 'nav-tab-active' : '' ) . ' ">' . $title . '</a>';
		    }
		    echo '</h2>';
		    echo '<br/>';
	    }

	    ?>
		<form name="pets_settings" method="POST">
			<?php do_action( 'pets_settings_form_fields', $active_tab ); ?>
			<button type="submit" class="button button-primary" name="pets_settings_save" value="<?php echo $active_tab; ?>"><?php esc_html_e( 'Save Settings', 'pets' ); ?></button>
		</form>
		<?php
    }

	/**
	 * Get Settings Tabs.
	 */
    public function get_tabs() {
		return apply_filters( 'pets_settings_tabs', array(
			'general' => __( 'General', 'pets' ),
		));
    }

	/**
	 * Render the fields for the provided tab.
	 * @param $tab
	 */
    public function render_fields( $tab ) {
		$fields = $this->get_fields( $tab );
		if ( $fields ) {
			echo '<table class="form-table">';
			$options = get_option( 'pets_' . $tab, array() );
			foreach ( $fields as $name => $field ) {
				if ( ! isset( $field['name'] ) ) {
					$field['name'] = $name;
				}
				$field['value'] = isset( $options[ $field['name'] ] ) ? $options[ $field['name'] ] : ( isset( $field['default'] ) ? $field['default'] : '' );
				$this->render_field( $field, $tab );
			}
			echo '</table>';
		}
    }

	/**
	 * @param $field
	 * @param $tab
	 *
	 * @return string
	 */
    public function get_field_name( $field, $tab ) {
    	return 'pets_' . $tab . '[' . $field['name'] . ']';
    }

    public function render_field( $field, $tab ) {
		$defaults = array(
			'options' => array(),
			'class'   => '',
			'value'   => '',
			'name'    => '',
			'id'      => '',
		);
	    $args = wp_parse_args( $field, $defaults );

	    if ( ! $args['id'] ) {
	    	$args['id'] = $field['name'];
	    }

    	switch ( $field['type'] ) {
		    case 'text':
			    $html  = '<tr class="pets-field">';
			    $html .= '<th>';
			    $html .= '<label for="' . $args['id'] . '">';
			    $html .= $args['title'];
			    $html .= '</label>';
			    $html .= '</th>';
			    $html .= '<td>';
			    $html .= '<input class="widefat" name="' . $this->get_field_name( $field, $tab ) . '" id="' . $args['id'] . '" type="text" value="' . esc_attr( $args['value'] ) . '"/>';
			    $html .= '</td>';
			    $html .= '</tr>';
			    echo $html;
			    break;
		    case 'textarea':
			    $html  = '<tr class="pets-field">';
			    $html .= '<th>';
			    $html .= '<label for="' . $args['id'] . '">';
			    $html .= $args['title'];
			    $html .= '</label>';
			    $html .= '</th>';
			    $html .= '<td>';
			    $html .= '<textarea class="widefat" name="' . $this->get_field_name( $field, $tab ) . '" rows="5" id="' . $args['id'] . '">' . $args['value'] . '</textarea>';
			    $html .= '</td>';
			    $html .= '</tr>';
			    echo $html;
			    break;
		    case 'select':
			    $html  = '<tr class="pets-field">';
			    $html .= '<th>';
			    $html .= '<label for="' . $args['id'] . '">';
			    $html .= $args['title'];
			    $html .= '</label>';
			    $html .= '</th>';
			    $html .= '<td>';
			    $html .= '<select name="' . $this->get_field_name( $field, $tab ) . '" class="widefat" id="' . $args['id'] . '">';
			    if ( isset( $args['options'] ) ) {
				    foreach ( $args['options'] as $value => $option ) {
					    $html .= '<option value="' . esc_attr( $value ) . '" ' . selected( $value, $args['value'], false ) . '>' . $option . '</option>';
				    }
			    }
			    $html .= '</select>';
			    $html .= '</td>';
			    $html .= '</tr>';
			    echo $html;
			    break;
		    case 'radio':
			    $html  = '<tr class="pets-field">';
			    $html .= '<th>';
			    $html .= '<label for="' . $args['id'] . '">';
			    $html .= $args['title'];
			    $html .= '</label>';
			    $html .= '</th>';
			    $html .= '<td>';
			    if ( isset( $args['options'] ) ) {
				    foreach ( $args['options'] as $value => $option ) {
					    $html .= '<label>';
					    $html .= '<input name="' . $this->get_field_name( $field, $tab ) . '" type="radio" value="' . esc_attr( $value ) . '" ' . checked( $value, $args['value'], false ) . '>' . $option;
					    $html .= '<br/></label>';
				    }
			    }
			    $html .= '</td>';
			    $html .= '</tr>';
			    echo $html;
			    break;
		    case 'checkbox':
			    $html  = '<tr class="pets-field">';
			    $html .= '<th>';
			    $html .= '<label for="' . $args['id'] . '">';
			    $html .= $args['title'];
			    $html .= '</label>';
			    $html .= '</th>';
			    $html .= '<td>';
			    $html .= '<input id="' . $args['id'] . '" name="' . $this->get_field_name( $field, $tab ) . '" type="checkbox" value="1" ' . checked( '1', $args['value'], false ) . '>';
			    $html .= '</td>';
			    $html .= '</tr>';
			    echo $html;
			    break;
		    default:
		    	do_action( 'pets_settings_field_' . $field['type'], $field, $tab, $this );
		    	break;
	    }
    }

	/**
	 * @param $tab
	 *
	 * @return array
	 */
    public function get_fields( $tab ) {
    	$fields = $this->settings_fields();
    	return isset( $fields[ $tab ] ) ? $fields[ $tab ] : array();
    }

	/**
	 * Settings fields.
	 */
    public function settings_fields() {
		return apply_filters( 'pets_settings_fields', array(
			'general' => array(
				'info_position' => array(
					'title' => __( 'Information Position', 'pets' ),
					'type'  => 'radio',
					'options' => array(
						'after' => __( 'After the content', 'pets' ),
						'before' => __( 'Before the content', 'pets' ),
					),
					'default' => 'after'
				)
			)
		));
    }

	/**
	 * Get a setting.
	 * @param        $id
	 * @param string $tab
	 */
    public function get_setting( $id, $tab = '', $default = '' ) {
		// No tab? Let's find the setting.
    	if ( ! $tab ) {
			$fields = $this->settings_fields();
			foreach ( $fields as $tab_id => $settings ) {
				if ( in_array( $id, array_keys( $settings ) ) ) {
					$tab = $tab_id;
					break;
				}
			}
		}

		// Still no tab? Let's try a classic approach
		if ( ! $tab ) {
			return get_option( $id, false );
		}

		$option = get_option( 'pets_' . $tab, array() );
    	if ( is_array( $option ) && isset( $option[ $id ] ) ) {
			return $option[ $id ];
	    }

	    return $default;
    }
}