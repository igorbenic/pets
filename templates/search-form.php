<?php
/**
 * The search form for pets.
 */

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
    <button type="submit" class="button"><?php esc_html_e( 'Search Pets', 'pets' ); ?></button>
</form>
