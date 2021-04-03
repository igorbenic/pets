<?php
/**
 * Globally available functions for templating.
 */

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * @param        $template_name
 * @param        $args
 * @param string $template_path
 * @param string $default_path
 */
function pets_get_template( $template_name, $args, $template_path = '', $default_path = '' ) {
	if ( ! empty( $args ) && is_array( $args ) ) {
		extract( $args );
	}

	$template_names = "{$template_name}.php";

	$located = pets_get_locate_template( $template_names, $template_path, $default_path );

	if ( ! file_exists( $located ) ) {
		return;
	}

	include $located;
}

/**
 * @param        $template_name
 * @param string $template_path
 * @param string $default_path
 *
 * @return mixed|void
 */
function pets_get_locate_template( $template_name, $template_path = '', $default_path = '' ) {
	if ( ! $template_path ) {
		$template_path = 'pets/';
	}

	if ( ! $default_path ) {
		$default_path = PETS_PATH . '/templates/';
	}

	// Look within passed path within the theme - this is priority.
	$template = locate_template(
		array(
			trailingslashit( $template_path ) . $template_name,
			$template_name,
		)
	);

	// Get default template/
	if ( ! $template ) {
		$template = $default_path . $template_name;
	}

	return apply_filters( 'pets_get_locate_template', $template, $template_name, $template_path );
}

/**
 * @param      $slug
 * @param null $name
 * @param bool $load
 *
 * @return string
 */
function pets_get_template_part( $slug, $name = null, $load = true ) {

	/**
	 * Fires in give template part, before the template part is retrieved.
	 *
	 * Allows you to execute code before retrieving the template part.
	 *
	 * @since 1.0
	 *
	 * @param string $slug Template part file slug {slug}.php.
	 * @param string $name Template part file name {slug}-{name}.php.
	 */
	do_action( "get_template_part_{$slug}", $slug, $name );

	// Setup possible parts
	$templates = array();
	if ( isset( $name ) ) {
		$templates[] = $slug . '-' . $name . '.php';
	}
	$templates[] = $slug . '.php';

	// Allow template parts to be filtered
	$templates = apply_filters( 'pets_get_template_part', $templates, $slug, $name );

	// Return the part that is found
	return pets_locate_template( $templates, $load, false );
}

/**
 * @param      $template_names
 * @param bool $load
 * @param bool $require_once
 *
 * @return bool|string
 */
function pets_locate_template( $template_names, $load = false, $require_once = true ) {
	// No file found yet
	$located = false;

	// Try to find a template file
	foreach ( (array) $template_names as $template_name ) {

		// Continue if template is empty
		if ( empty( $template_name ) ) {
			continue;
		}

		// Trim off any slashes from the template name
		$template_name = ltrim( $template_name, '/' );

		// try locating this template file by looping through the template paths
		foreach ( pets_get_theme_template_paths() as $template_path ) {

			if ( file_exists( $template_path . $template_name ) ) {
				$located = $template_path . $template_name;
				break;
			}
		}

		if ( $located ) {
			break;
		}
	}

	if ( ( true == $load ) && ! empty( $located ) ) {
		load_template( $located, $require_once );
	}

	return $located;
}

/**
 * Returns a list of paths to check for template locations
 *
 * @since 1.0
 * @return array
 */
function pets_get_theme_template_paths() {

	$template_dir = 'pets/';

	$file_paths = array(
		1   => trailingslashit( get_stylesheet_directory() ) . $template_dir,
		10  => trailingslashit( get_template_directory() ) . $template_dir,
		100 => trailingslashit(PETS_PATH ) . 'templates',
	);

	$file_paths = apply_filters( 'pets_template_paths', $file_paths );

	// sort the file paths based on priority
	ksort( $file_paths, SORT_NUMERIC );

	return array_map( 'trailingslashit', $file_paths );
}

/**
 * Pets Logo.
 */
function pets_get_logo() {
	ob_start();
	?>
	<svg class="pet-logo" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	     width="100px" height="100px" viewBox="200 320 200 100">
		<path fill-rule="evenodd" clip-rule="evenodd" fill="black" d="M236.649,319.693c1.968-1.876,4.756-3.869,7.075-4.437
	c13.011-3.191,36.336,21.904,30.264,37.486c-1.276,3.272-4.959,6.783-9.843,8.13C249.851,364.813,222.243,333.424,236.649,319.693z"
		/>
		<path fill-rule="evenodd" clip-rule="evenodd" fill="black" d="M279.17,297.144c24.304-17.792,40.412,30.084,25.503,42.218
	C282.868,357.106,263.266,308.416,279.17,297.144z"/>
		<path fill-rule="evenodd" clip-rule="evenodd" fill="black" d="M225.792,365.372c10.116-9.704,27.559-1.127,34.11,8.893
	c5.249,8.026,5.415,18.295-1.569,22.575c-12.223,7.49-35.28-4.71-36.553-21.657C221.569,372.361,223.027,368.022,225.792,365.372z"
		/>
		<path fill-rule="evenodd" clip-rule="evenodd" fill="black" d="M323.499,314.513c19.818-16.472,29.574,20.112,23.275,32.438
	c-1.956,3.826-7.63,9.242-12.46,10.031c-13.843,2.263-19.81-19.095-18.172-29.439C316.765,323.607,318.769,318.444,323.499,314.513z
	"/>
		<path fill-rule="evenodd" clip-rule="evenodd" fill="black" d="M284.958,360.467c1.862-1.316,8.935-5.513,13.302-6.139
	c12.462-1.784,36.095,5.719,47.878,15.583c15.257,12.77,15.107,27.353-2.571,33.928c-9.317,3.465-18.989,1.449-27.77,5.729
	c-11.263,5.493-36.01,44.516-48.213,14c-7.261-18.158,1.743-47.539,10.945-58.162C279.912,363.812,282.725,362.045,284.958,360.467z
	"/>
		<path fill-rule="evenodd" clip-rule="evenodd" fill="black" d="M308.858,424.946c2.151,4.129,4.304,8.258,6.456,12.388
	c-3.976,1.667-8.586,5.344-12.105,5.559c-1.667-3.976-5.344-8.585-5.559-12.105C301.387,428.839,305.122,426.892,308.858,424.946z"
		/>
		<path fill-rule="evenodd" clip-rule="evenodd" fill="black" d="M333.654,412.071c3.736-1.947,7.471-3.894,11.208-5.841
	c2.869,2.374,4.637,8.248,7.047,12.081c-3.933,2.049-7.865,4.099-11.799,6.149C337.958,420.33,335.807,416.2,333.654,412.071z"/>
	</svg>
	<?php
	return ob_get_clean();
}

add_action( 'pets_before_loop', 'pets_before_loop' );
add_action( 'pets_after_loop', 'pets_after_loop' );
add_action( 'pets_before_loop_while', 'pets_before_loop_while' );
add_action( 'pets_after_loop_while', 'pets_after_loop_while' );

/**
 * The before loop of pets.
 */
function pets_before_loop() {
	pets_locate_template( 'before-loop.php', true );
}

/**
 * Closing the divider at the end of the loop.
 */
function pets_after_loop() {
	pets_locate_template( 'after-loop.php', true );
}

/**
 * The before loop of pets.
 */
function pets_before_loop_while() {
	pets_locate_template( 'before-loop-while.php', true );
}

/**
 * Closing the divider at the end of the loop.
 */
function pets_after_loop_while() {
	pets_locate_template( 'after-loop-while.php', true );
}

/**
 * Sort Terms with children
 *
 * @param array $cats
 * @param array $into
 * @param int   $parent
 */
function pets_sort_terms_hierarchically(&$cats, &$into, $parent = 0) {
	foreach ($cats as $i => $cat) {
		if ($cat->parent == $parent) {
			$into[$cat->term_id] = $cat;
			unset($cats[$i]);
		}
	}

	foreach ($into as $topCat) {
		$topCat->children = array();
		pets_sort_terms_hierarchically($cats, $topCat->children, $topCat->term_id);
	}
}

/**
 * @param        $term
 * @param        $selected
 * @param string $separator
 */
function pets_recursive_terms_as_options( $term, $selected, $separator = '' ) {
	?>
	<option <?php selected( $selected, $term->slug, true ); ?>
		value="<?php echo esc_attr( $term->slug ); ?>"><?php echo $separator . $term->name; ?></option>
	<?php

	if ( ! empty( $term->children ) ) {
		$separator = trim( $separator ) . '- ';
		foreach( $term->children as $child ) {
			pets_recursive_terms_as_options( $child, $selected, $separator );
		}
	}
}
