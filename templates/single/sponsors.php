<?php
/**
 * Sponsors for a Pet
 */

if ( ! $sponsors ) {
	return;
}
?>
	<h3 class="pets-section-title"><?php echo \Pets\Fields::get_section_icon_html( 'fas fa-life-ring' ); ?><?php esc_html_e( 'Sponsors', 'pets' ); ?></h3>
	<ul class="pets-fields pets-information pets-sponsors">
	<?php
	foreach ( $sponsors as $sponsor ) {
		echo '<li>';
		echo '<strong class="pets-field-title">' . esc_html( $sponsor->name ) . '</strong>';
		echo '<a class="button pets-button" href="' . get_term_link( $sponsor ) . '">' . sprintf( esc_html__( 'Sponsored: %d', 'pets' ), $sponsor->count ) . '</a>';
		echo '</li>';
	}
	?>
	</ul>
