<?php
/**
 * Single Pet in Archive
 */
?>
<article id="pet-<?php echo get_the_ID(); ?>" <?php post_class(); ?>>
	<header class="pet-header">
        <div class="pet-image">
		<?php
		if ( has_post_thumbnail() ) {
			the_post_thumbnail( 'pets-thumbnail' );
		} else {
		    echo pets_get_logo();
        }
		?>
        </div>
		<?php the_title( '<a href="' . get_permalink() . '"><h3 class="pet-title">', '</h3></a>' ); ?>
	</header>
	<section class="pet-content">
		<?php the_content(); ?>
	</section>
</article>
