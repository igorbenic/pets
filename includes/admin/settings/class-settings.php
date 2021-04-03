<?php

/**
 * Admin Settings Class
 */

namespace Pets\Admin;

use Pets\Fields;

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
        add_action( 'pets_settings_updated', array( $this, 'settings_updated' ) );
    }

	/**
	 * Pets settings updated.
	 */
    public function settings_updated() {
        ?>
        <div class="notice updated">
            <p><?php _e( 'Settings Updated', 'pets' ); ?></p>
        </div>
        <?php
    }

	/**
	 * Save the settings for the current tab.
	 */
    public function save_settings() {
		if ( ! isset( $_POST['pets_settings_save'] ) ) {
			return;
		}

		$active_tab = $_POST['pets_settings_save'];

		$validated_settings = $this->validate_settings( $active_tab );

		update_option( 'pets_' . $active_tab, $validated_settings );

		do_action( 'pets_settings_updated', $active_tab );
    }

	/**
     * Validate Settings
     *
	 * @param string $tab Tab for which we validate settings.
	 *
	 * @return array
	 */
    public function validate_settings( $tab ) {
        $posted_fields     = isset( $_POST['pets_' . $tab ] ) ? $_POST['pets_' . $tab ] : array();
        $registered_fields = $this->get_fields( $tab );

        foreach ( $registered_fields as $name => $fields ) {
            if ( ! isset( $posted_fields[ $name ] ) && $fields['type'] === 'checkbox' ) {
                $posted_fields[ $name ] = '0';
            }
        }

        return $posted_fields;
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
            'sponsors' => __( 'Sponsors', 'pets' ),
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
			'desc'    => '',
		);
	    $args = wp_parse_args( $field, $defaults );

	    if ( ! $args['id'] ) {
	    	$args['id'] = $field['name'];
	    }

    	switch ( $field['type'] ) {
		    case 'description':
			    $html  = '<tr class="pets-field">';
			    $html .= '<td colspan="2">';
			    $html .= '<p class="description""><strong>';
			    $html .= $args['title'];
			    $html .= '</strong></p>';
			    $html .= '</td>';;
			    $html .= '</tr>';
			    echo $html;
			    break;
		    case 'text':
			    $html  = '<tr class="pets-field">';
			    $html .= '<th>';
			    $html .= '<label for="' . $args['id'] . '">';
			    $html .= $args['title'];
			    $html .= '</label>';
			    $html .= '</th>';
			    $html .= '<td>';
			    $html .= '<input class="widefat" name="' . $this->get_field_name( $field, $tab ) . '" id="' . $args['id'] . '" type="text" value="' . esc_attr( $args['value'] ) . '"/>';
			    if ( $args['desc'] ) {
			    	$html .= '<p class="description">' . $args['desc'] . '</p>';
				}
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
				if ( $args['desc'] ) {
					$html .= '<p class="description">' . $args['desc'] . '</p>';
				}
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
				if ( $args['desc'] ) {
					$html .= '<p class="description">' . $args['desc'] . '</p>';
				}
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
				if ( $args['desc'] ) {
					$html .= '<p class="description">' . $args['desc'] . '</p>';
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
				if ( $args['desc'] ) {
					$html .= '<p class="description">' . $args['desc'] . '</p>';
				}
				$html .= '</td>';
			    $html .= '</tr>';
			    echo $html;
			    break;
			case 'fields_filter':
				$fields = Fields::get_cached_fields();
				$name   = $this->get_field_name( $field, $tab );

				$html  = '<tr class="pets-field">';
				$html .= '<th>';
				$html .= '<label for="' . $args['id'] . '">';
				$html .= $args['title'];
				$html .= '</label>';
				$html .= '</th>';
				$html .= '<td>';
				$html .= '<table class="form-table">';
				foreach ( $fields as $field_index => $pets_field ) {
					if ( isset( $pets_field['searchable'] ) && $pets_field['searchable'] ) {
						continue;
					}

					$pets_field['name'] = $name . '[' . $pets_field['slug'] . ']';
					$pets_field['value'] = isset( $args['value'][ $pets_field['slug'] ] ) ? $args['value'][ $pets_field['slug'] ]: '';
					if ( 'text' !== $pets_field['type'] && 'textarea' !== $pets_field['type'] ) {
						$multiple =  isset( $pets_field['meta'] ) && isset( $pets_field['meta']['multiple_search'] ) && 'yes' === $pets_field['meta']['multiple_search'] ? true : false;
						$options  = isset( $pets_field['meta'] ) && isset( $pets_field['meta']['options'] ) ? $pets_field['meta']['options'] : array( __( 'No', 'pets' ), __( 'Yes', 'pets' ), );

						$html .= '<tr class="pets-field">';
						$html .= '<th>';
						$html .= '<label for="' . $pets_field['id'] . '">';
						$html .= $pets_field['title'];
						$html .= '</label>';
						$html .= '</th>';
						$html .= '<td>';
						ob_start();
						if ( $multiple ) {
							foreach ( $options as $option ) {
								$checked = false;
								if ( is_array( $pets_field['value']  ) ) {
									$checked = in_array( $option, $pets_field['value'] , true );
								}

								?>
								<br/>
								<label for="<?php echo esc_attr( $pets_field['name'] ); ?>_<?php echo sanitize_title( $option ); ?>">
									<input type="checkbox" name="<?php echo esc_attr( $pets_field['name'] ); ?>[]" <?php checked( $checked, true, true ); ?> id="<?php echo esc_attr( $pets_field['name'] ); ?>_<?php echo sanitize_title( $option ); ?>" value="<?php echo esc_attr( $option ); ?>" />
									<?php echo esc_html( $option ); ?>
								</label>
								<?php

							}


						} else {
							?>
							<select id="<?php echo esc_attr( $pets_field['name'] ); ?>" name="<?php echo esc_attr( $pets_field['name'] ); ?>">
								<option value="all"><?php esc_html_e( 'All', 'pets' ); ?></option>
								<?php
								if ( $options ) {
									foreach ( $options as $key => $option ) {
										$value = 'checkbox' === $pets_field['type'] ? $key : $option;
										?>
										<option <?php selected( $pets_field['value'], $value, true ); ?>
											value="<?php echo esc_attr( $value ); ?>"><?php echo $option; ?></option>
										<?php
									}
								}
								?>
							</select>
							<?php
						}
						$html .= ob_get_clean();
						$html .= '</td>';
						$html .= '</tr>';
					} else {
						ob_start();
						Fields::render_field( $pets_field, 0 );
						$html .= ob_get_clean();
					}
				}
				$html .= '</table>';
				if ( $args['desc'] ) {
					$html .= '<p class="description">' . $args['desc'] . '</p>';
				}
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
                'pets_per_page' => array(
                    'title' => __( 'Pets per Page', 'pets' ),
                    'type'  => 'text',
                    'default' => '6',
                ),
				'info_position' => array(
					'title' => __( 'Information Position', 'pets' ),
					'type'  => 'radio',
					'options' => array(
						'after' => __( 'After the content', 'pets' ),
						'before' => __( 'Before the content', 'pets' ),
					),
					'default' => 'after'
				),
                'location_search' => array(
	                'title' => __( 'Location in Search?', 'pets' ),
	                'type'  => 'checkbox',
	                'default' => '0'
                ),
                'show_missing' => array(
	                'title' => __( 'Show Missing Pets in Search?', 'pets' ),
	                'type'  => 'checkbox',
	                'default' => '0'
                ),
				'notify_on_missing_emails' => array(
					'title' => __( 'Notify on a new Missing Pet', 'pets' ),
					'type'  => 'text',
					'default' => '',
					'desc'    => __( 'Enter emails separated by comma to notify', 'pets' ),
				),
				'notify_on_new_emails' => array(
					'title' => __( 'Notify on a new Pet', 'pets' ),
					'type'  => 'text',
					'default' => '',
					'desc'    => __( 'Enter emails separated by comma to notify', 'pets' ),
				),
				'new_pet_status' => array(
					'title' => __( 'New Pet Status', 'pets' ),
					'desc'  => __( 'Status to be assigned when a new pet is added through form', 'pets' ),
					'type'  => 'select',
					'options' => array(
						'publish' => __( 'Publish', 'pets' ),
						'draft'   => __( 'Draft', 'pets' ),
					),
					'default' => 'draft'
				),
				'search_filter' => array(
					'title' => __( 'Global Search', 'pets' ),
					'type'  => 'description',
				),
				'fields_filter' => array(
					'title' => __( 'Search Filter', 'pets' ),
					'desc'  => __( 'Which Fields and which values should be included in search. This is not applied to [pets_archive]. Only on global search. Displayed fields here are not searchable.', 'pets' ),
					'type'  => 'fields_filter',
				),
			),
            'sponsors' => array(
                'show_sponsors' => array(
                    'title' => __( 'Show Sponsors', 'pets' ),
                    'type'  => 'checkbox',
                    'default' => '1',
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

	    return apply_filters( 'pets_get_setting_' . $tab . '_' . $id, $default );
    }
}
