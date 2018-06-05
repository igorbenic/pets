<?php
/**
 * Information for a Pet
 */

if ( ! $information ) {
	return;
}
?>
<h3><?php esc_html_e( 'Information', 'pets' ); ?></h3>
<ul class="pets-fields pets-information">
	<?php
	foreach ( $information as $title => $value ) {
		echo '<li>';
		echo '<strong class="pets-field-title">' . $title . ':</strong>';
		echo $value;
		echo '</li>';
	}
	?>
</ul>
