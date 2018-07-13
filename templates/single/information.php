<?php
/**
 * Information for a Pet
 */

if ( ! $information ) {
	return;
}
foreach ( $information as $section ) {
	?>
    <h3 class="pets-section-title"><?php echo \Pets\Fields::get_section_icon_html( $section['icon'] ) . esc_html( $section['title'] ); ?></h3>
    <ul class="pets-fields pets-information">
		<?php
		foreach ( $section['fields'] as $field ) {
			if ( ! $field['value'] ) {
				continue;
			}
			echo '<li>';
			echo '<strong class="pets-field-title">' . $field['title'] . ':</strong>';
			echo $field['value'];
			echo '</li>';
		}
		?>
    </ul>
<?php }
