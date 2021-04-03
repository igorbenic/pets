<?php
/**
 * The search form for pets.
 */

use Pets\DB\Fields;

$form_action   = isset( $form_action ) ? $form_action : get_post_type_archive_link( 'pets' );
$show_statuses = isset( $show_statuses ) ? $show_statuses : true; // It will be used to show statuses of pets (published|Missing)

$breeds         = get_terms( array(
	'taxonomy'   => 'breed',
	'hide_empty' => false,
) );
$breeds_hierarchy = array();
$selected_breed = isset( $_REQUEST['breed'] ) ? sanitize_text_field( $_REQUEST['breed'] ) : '';

$colors         = get_terms( array(
	'taxonomy'   => 'pet-color',
	'hide_empty' => false,
) );
$selected_color = isset( $_REQUEST['pet-color'] ) ? sanitize_text_field( $_REQUEST['pet-color'] ) : '';

$location_search = absint( pets_get_setting( 'location_search', 'general', '0' ) ) ? true : false;
$locations = array();
if ( $location_search ) {
	$locations = get_terms( array(
		'taxonomy'   => 'pet-locations',
		'hide_empty' => true,
	) );
	$location_search = $locations ? true : false;
}
$pet_fields_db = new Fields();
$pet_fields    = $pet_fields_db->get_all();
$pet_fields    = array_filter( $pet_fields, array( '\Pets\Fields', 'only_searchable' ) );
$show_form     = apply_filters( 'pets_show_search_form', $breeds || $colors || $location_search || $pet_fields );

if ( ! $show_form ) {
	return;
}
?>
<form class="pets-search-form" method="GET" action="<?php echo $form_action; ?>">
    <?php
    if ( $breeds || $colors || $location_search ) {
        ?>
        <div class="fieldset">
        <?php
		if ( $breeds ) {

			pets_sort_terms_hierarchically( $breeds, $breeds_hierarchy );
        	?>
        <div class="search-field">
            <label for="pets_breed">
				<?php echo esc_html_e( 'Breed', 'pets' ); ?>
            </label>
            <select id="pets_breed" name="breed">
                <option value="0"><?php esc_html_e( 'All Breeds', 'pets' ); ?></option>
				<?php
					foreach ( $breeds_hierarchy as $breed_item ) {
						pets_recursive_terms_as_options( $breed_item, $selected_breed );
					}

				?>
            </select>
        </div>
        <?php } ?>
        <?php
        if ( $colors ) {
            ?>
            <div class="search-field">
                <label for="pets_color">
                    <?php echo esc_html_e( 'Color', 'pets' ); ?>
                </label>
                <select id="pets_color" name="pet-color">
                    <option value="0"><?php esc_html_e( 'All Colors', 'pets' ); ?></option>
                    <?php

                        foreach ( $colors as $color ) {
                            ?>
                            <option <?php selected( $selected_color, $color->slug, true ); ?>
                                    value="<?php echo esc_attr( $color->slug ); ?>"><?php echo $color->name; ?></option>
                            <?php
                        }
                    ?>
                </select>
            </div>
        <?php } ?>
        <?php
        if ( $location_search ) {

	        $selected_location = isset( $_REQUEST['pet-locations'] ) ? sanitize_text_field( $_REQUEST['pet-locations'] ) : '';

	        ?>
            <div class="search-field">
                <label for="pets_locations">
			        <?php esc_html_e( 'Location', 'pets' ); ?>
                </label>
                <select id="pets_locations" name="pet-locations">
                    <option value="0"><?php esc_html_e( 'All Locations', 'pets' ); ?></option>
			        <?php
			        if ( $locations ) {
				        foreach ( $locations as $location ) {
					        ?>
                            <option <?php selected( $selected_location, $location->slug, true ); ?>
                                    value="<?php echo esc_attr( $location->slug ); ?>"><?php echo $location->name; ?></option>
					        <?php
				        }
			        }
			        ?>
                </select>
            </div>
	        <?php
        }
        ?>
        </div>
        <?php
    }
    ?>
    <?php
    if ( $pet_fields ) {
        $searched_fields = isset( $_GET['pets_search'] ) ? $_GET['pets_search'] : array();
        ?>
        <div class="fieldset">
            <?php
                foreach( $pet_fields as $field ) {
                    $name = 'pets_search[' .esc_attr( $field['slug'] ) . ']';
                    $selected = isset( $searched_fields[ $field['slug'] ] ) ? $searched_fields[ $field['slug'] ] : '';
                    ?>
                    <div class="search-field">
                        <label for="<?php echo esc_attr( $name ); ?>">
			                <?php echo esc_html( $field['title'] ); ?>
                        </label>
                        <?php
                        if ( 'text' === $field['type'] || 'textarea' === $field['type'] ) {
                            ?>
                            <input type="text" name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $selected ); ?>" />
                            <?php
                        } else {
                            $multiple =  isset( $field['meta'] ) && isset( $field['meta']['multiple_search'] ) && 'yes' === $field['meta']['multiple_search'] ? true : false;
                            $options = isset( $field['meta'] ) && isset( $field['meta']['options'] ) ? $field['meta']['options'] : array( __( 'No', 'pets' ), __( 'Yes', 'pets' ), );
                            if ( $multiple ) {
                                foreach ( $options as $option ) {
                                    $checked = false;
                                    if ( is_array( $selected ) ) {
	                                    $checked = in_array( $option, $selected, true );
                                    }
	                                ?>
                                    <br/>
                                    <label for="<?php echo esc_attr( $name ); ?>_<?php echo sanitize_title( $option ); ?>">
                                        <input type="checkbox" name="<?php echo esc_attr( $name ); ?>[]" <?php checked( $checked, true, true ); ?> id="<?php echo esc_attr( $name ); ?>_<?php echo sanitize_title( $option ); ?>" value="<?php echo esc_attr( $option ); ?>" />
                                        <?php echo esc_html( $option ); ?>
                                    </label>
	                                <?php
                                }

                            } else {
	                            ?>
                                <select id="<?php echo esc_attr( $name ); ?>" name="<?php echo esc_attr( $name ); ?>">
                                    <option value="all"><?php esc_html_e( 'All', 'pets' ); ?></option>
		                            <?php
		                            if ( $options ) {
			                            foreach ( $options as $key => $option ) {
				                            $value = 'checkbox' === $field['type'] ? $key : $option;
				                            ?>
                                            <option <?php selected( $selected, $value, true ); ?>
                                                    value="<?php echo esc_attr( $value ); ?>"><?php echo $option; ?></option>
				                            <?php
			                            }
		                            }
		                            ?>
                                </select>
	                            <?php
                            }
                        }
                        ?>
                    </div>
                    <?php
                }
            ?>
        </div>
        <?php
    }

    ?>
    <button type="submit" class="button"><?php esc_html_e( 'Search Pets', 'pets' ); ?></button>
</form>
