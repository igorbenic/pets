<div class="wrap">
    <h1><?php echo get_admin_page_title(); ?></h1>

    <?php do_action( 'pets_admin_page_' . ( isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : 'default' )  ); ?>
</div>