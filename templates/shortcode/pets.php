<?php
/**
 * Petfinder Search template
 */

$form_action = get_permalink();
$show_statuses = false;

$breed       = isset( $_GET['breed'] ) ? $_GET['breed'] : '';
$pets_search = isset( $_GET['pets_search'] ) ? $_GET['pets_search'] : array();
$location    = isset( $_GET['pet-locations'] ) ? $_GET['pet-locations'] : 0;
$search_page = isset( $_GET['search-page'] ) ? $_GET['search-page'] : 1;

$pets_search = wp_parse_args( $pets_search, $atts );

if ( $location ) {
	$pets_search['location'] = $location;
}

if ( $breed ) {
	$types = \Pets\Integrations\PetFinder::get_types();
	$is_type    = false;

	if ( ! is_wp_error( $types ) ) {
		$type_names = wp_list_pluck( $types['types'], 'name' );
		$is_type    = false;
		foreach ( $type_names as $type_name ) {
			if ( strtolower( $breed ) === strtolower( $type_name ) ) {
				$pets_search['type'] = $breed;
				$is_type             = true;
				break;
			}
		}
	}
	if ( ! $is_type ) {
		$pets_search['breed'] = $breed;
	}
}

$pets_search['page'] = $search_page;

foreach ( $pets_search as $slug => $value ) {
	if ( 'adoptable' === $slug ) {
		if ( absint( $value ) ) {
			$pets_search[ $slug ] = 'adoptable';
		}
		continue;
	}

	if ( is_array( $value ) ) {
		$pets_search[ $slug ] = implode( ',', $value );
	}

	if ( 'name' !== $slug ) {
		$pets_search[ $slug ] = strtolower( $pets_search[ $slug ] );
	}
}

$pets_search = array_filter( $pets_search );

$pets_query = new WP_Query();
$pets_query->parse_query(array(
	'post_type' => 'pets',
	'post_status' => 'publish',
	'paged' => $search_page
));

if ( isset( $pets_search['orderby'] ) && $pets_search['orderby'] ) {
	$pets_query->set( 'orderby', $pets_search['orderby'] );
}

$show_pagination = true;

if ( isset( $pets_search['hide_nav'] ) && $pets_search['hide_nav'] ) {
	$show_pagination = false;
}

\Pets\Search::add_pets_per_page_to_query($pets_query);
\Pets\Search::add_search_fields_to_query($pets_query);

if ( $pets_search['filter'] ) {
	$filters       = explode( ',', $pets_search['filter'] );
	$filter_values = ! empty( $pets_search['filter_value'] ) ? explode( ',', $pets_search['filter_value'] ) : array();
	$filter_data   = array();

	foreach ( $filters as $index => $field_filter ) {
		$value = isset( $filter_values[ $index ] ) ? $filter_values[ $index ] : '';
		$filter_data[ $field_filter ] = $value;
	}

	\Pets\Search::filter_fields_query( $filter_data, $pets_query );
}

$pets_query->get_posts();

$total_pages  = $pets_query->max_num_pages;
$current_page = absint( $search_page );
?>
<div class="post-type-archive-pets">
	<?php

	include pets_locate_template( 'search-form.php', false );

	do_action( 'pets_before_loop_while' );

	while ( $pets_query->have_posts() ) {
		$pets_query->the_post();
		pets_get_template_part( 'archive/single' );
	}

	wp_reset_postdata();

	do_action( 'pets_after_loop_while' );

	if ( $total_pages > 1 && $show_pagination ) {
		$query_args = $_GET;
		if ( isset( $_GET['search-page'] ) ) {
			unset( $_GET['search-page'] );
		}
		$lowest = $current_page - 2;
		$max    = $current_page + 2;

		if ( $lowest < 1 ) {
			$lowest = 1;
		}

		if ( $max - $lowest < 5 ) {
			$max = $lowest + 5;
		}

		if ( $max > $total_pages ) {
			$max = $total_pages;
		}
		?>
		<nav class="navigation pagination" role="navigation">
			<h2 class="screen-reader-text">Posts navigation</h2>
			<div class="nav-links">
				<?php
				for( $p = $lowest; $p <= $max; $p++ ) {

					if ( $current_page === $p ) {
						?>
						<span aria-current="page" class="page-numbers current"><?php echo esc_html( $p ); ?></span>
						<?php
					} else {
						$url = $form_action;
						$url = add_query_arg( $_GET, $url );
						$url = add_query_arg( 'search-page', $p, $url );
						?>
						<a class="page-numbers" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $p ); ?></a>
						<?php
					}
				}

				?>
				<span class="page-numbers"><?php esc_html_e( 'Total Pages:', 'pets' ); ?> <?php echo $total_pages; ?></span>
			</div>
		</nav>
		<?php
	}
	?>
</div>
