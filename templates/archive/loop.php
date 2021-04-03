<?php
/**
 * Archive Loop for Pets.
 */

get_header();

do_action( 'pets_before_loop'  );

if ( have_posts() ) {

	do_action( 'pets_before_loop_while' );

	while ( have_posts() ) {
		the_post();
		pets_get_template_part( 'archive/single' );
	}

	do_action( 'pets_after_loop_while' );

	if ( function_exists( 'the_posts_pagination' ) ) {
		the_posts_pagination();
	} elseif ( function_exists( 'paginate_links' ) ) {
		echo paginate_links();
	} else {
		posts_nav_link();
	}
} else {
	pets_get_template_part( 'archive/not-found' );
}

do_action( 'pets_after_loop' );

get_footer();