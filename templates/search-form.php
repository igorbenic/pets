<?php
/**
 * The search form for pets.
 */

use Pets\DB\Fields;

$breeds         = get_terms( array(
	'taxonomy'   => 'breed',
	'hide_empty' => false,
) );
$selected_breed = isset( $_REQUEST['breed'] ) ? sanitize_text_field( $_REQUEST['breed'] ) : '';

$colors         = get_terms( array(
	'taxonomy'   => 'pet-color',
	'hide_empty' => false,
) );
$selected_color = isset( $_REQUEST['pet-color'] ) ? sanitize_text_field( $_REQUEST['pet-color'] ) : '';

$pet_fields_db = new Fields();
$pet_fields    = $pet_fields_db->get_all();
$pet_fields    = array_filter( $pet_fields, array( '\Pets\Fields', 'only_searchable' ) );

?>
<form class="pets-search-form" method="GET" action="<?php echo get_post_type_archive_link( 'pets' ); ?>">
    <div class="fieldset">
        <div class="search-field">
            <label for="pets_breed">
				<?php echo esc_html_e( 'Breed', 'pets' ); ?>
            </label>
            <select id="pets_breed" name="breed">
                <option value="0"><?php esc_html_e( 'All Breeds', 'pets' ); ?></option>
				<?php
				if ( $breeds ) {
					foreach ( $breeds as $breed ) {
						?>
                        <option <?php selected( $selected_breed, $breed->slug, true ); ?>
                                value="<?php echo esc_attr( $breed->slug ); ?>"><?php echo $breed->name; ?></option>
						<?php
					}
				}
				?>
            </select>
        </div>
        <div class="search-field">
            <label for="pets_color">
				<?php echo esc_html_e( 'Color', 'pets' ); ?>
            </label>
            <select id="pets_color" name="pet-color">
                <option value="0"><?php esc_html_e( 'All Colors', 'pets' ); ?></option>
				<?php
				if ( $colors ) {
					foreach ( $colors as $color ) {
						?>
                        <option <?php selected( $selected_color, $color->slug, true ); ?>
                                value="<?php echo esc_attr( $color->slug ); ?>"><?php echo $color->name; ?></option>
						<?php
					}
				}
				?>
            </select>
        </div>
    </div>
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
                            $options = isset( $field['meta'] ) && isset( $field['meta']['options'] ) ? $field['meta']['options'] : array( __( 'No', 'pets' ), __( 'Yes', 'pets' ), );
                            ?>
                            <select id="<?php echo esc_attr( $name ); ?>" name="<?php echo esc_attr( $name ); ?>">
                                <option value="all"><?php esc_html_e( 'All', 'pets' ); ?></option>
		                        <?php
		                        if ( $options ) {
			                        foreach ( $options as $key => $option ) {
			                            $value = 'checkbox' === $field['type'] ? $key : $option;
				                        ?>
                                        <option <?php selected( $selected, $value, true ); ?> value="<?php echo esc_attr( $value ); ?>"><?php echo $option; ?></option>
				                        <?php
			                        }
		                        }
		                        ?>
                            </select>
                            <?php
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
