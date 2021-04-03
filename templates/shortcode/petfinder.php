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

$organization_id = pets_get_setting( 'petfinder_organization_id', 'petfinder', '' );

if ( $organization_id ) {
	$pets_search['organization'] = $organization_id;
}

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

$animals      = Pets\Integrations\PetFinder::get( 'animals', $pets_search );
$total_pages  = 1;
$current_page = 1;
?>
<div class="pets-petfinder-search post-type-archive-pets">
	<?php

	include pets_locate_template( 'search-form.php', false );

	do_action( 'pets_before_loop_while' );

	if ( ! is_wp_error( $animals ) && $animals['animals'] ) {
		$pagination   = $animals['pagination'];
		$total_pages  = $pagination['total_pages'];
		$current_page = $pagination['current_page'];
		foreach ( $animals['animals'] as $animal ) {
			?>
			<article id="pet-<?php echo esc_attr( $animal['id'] ); ?>" class="pets type-pets status-publish hentry">
				<header class="pet-header">
					<a target="_blank" href="<?php echo esc_url( $animal['url'] ); ?>">
						<div class="pet-image">
							<?php
							if ( $animal['photos'] ) {
								$photo = $animal['photos'][0];

								echo '<img src="' . end( $photo ) . '" />';
							} else {
								echo pets_get_logo();
							}
							?>
						</div>
					</a>
					<a target="_blank" href="<?php echo esc_url( $animal['url'] ); ?>">
						<h3 class="pet-title">
							<?php echo wp_kses_post( $animal['name'] ); ?>
						</h3>
					</a>
				</header>
				<section class="pet-content">
					<?php echo wpautop( $animal['description'] ); ?>
				</section>
			</article>
			<?php
		}
	}


	do_action( 'pets_after_loop_while' );

	if ( $total_pages > 1 ) {
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
