<?php
/**
 * Single Pet in Archive
 */
?>
<article id="pet-<?php echo get_the_ID(); ?>" <?php post_class(); ?>>
	<header class="pet-header">
        <a href="<?php echo get_permalink(); ?>">
            <div class="pet-image">
                <?php
                if ( has_post_thumbnail() ) {
                    the_post_thumbnail( 'pets-thumbnail' );
                } else {
                    echo pets_get_logo();
                }
                ?>
            </div>
        </a>
		<?php the_title( '<a href="' . get_permalink() . '"><h3 class="pet-title">', '</h3></a>' ); ?>
        <?php
            if ( get_post_status() === 'missing' ) {
                echo '<span class="pet-missing">' . __( 'Missing', 'pets' ) . '</span>';
            }
        ?>
	</header>
	<section class="pet-content">
		<?php the_content(); ?>
	</section>
</article>
