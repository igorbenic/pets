<?php
/**
 * Created by PhpStorm.
 * User: igor
 * Date: 27/05/18
 * Time: 17:46
 */

namespace Pets\Integrations;


class Give {

	/**
	 * Give constructor.
	 */
	public function __construct() {

		add_action( 'give_donation_form_top', array( $this, 'pet_selector' ), 30, 3 );
		add_action( 'give_insert_payment', array( $this, 'save_selected_pet' ), 20, 2 );
		add_action( 'give_donation_details_thead_before', array( $this, 'show_pet_in_donation_details' ) );

		add_filter( 'give_donation_receipt_args', array( $this, 'donation_receipt_args' ), 20, 2 );
		add_filter( 'pets_settings_tabs', array( $this, 'add_tab' ) );
		add_filter( 'pets_settings_fields', array( $this, 'add_settings' ) );
		add_filter( 'the_content', array( $this, 'add_pet_donation_link' ), 99 );
	}

	/**
	 * @param $content
	 *
	 * @return mixed
	 */
	public function add_pet_donation_link( $content ) {
		remove_filter( 'the_content', array( $this, 'add_pet_donation_link' ), 99 );
		if ( is_singular( 'pets' ) ) {
			$form_id = pets_get_setting( 'form_id', 'give', 0 );

			if ( ! $form_id ) {
				return $content;
			}
			$form_type = pets_get_setting( 'form_type', 'give', 'form' );

			if ( 'form' === $form_type ) {
				$atts = array( 'form_id' => $form_id, 'pet' => get_the_ID() );
				ob_start();
				give_get_donation_form( $atts );
				$content .= ob_get_clean();
			} else {

				$link = get_permalink( $form_id );
				$link = add_query_arg( 'pet', get_the_ID(), $link );

				$content .= '<a href="' . $link . '" class="button">' . sprintf( __( 'Donate for %s', 'pets' ), get_the_title() ) . '</a>';
			}
		}
		add_filter( 'the_content', array( $this, 'add_pet_donation_link' ), 99 );
		return $content;
	}

	/**
	 * @param $payment_id
	 */
	public function show_pet_in_donation_details( $payment_id ) {
		$pet = give_get_payment_meta( $payment_id, 'selected_pet', true );

		if ( is_numeric( $pet ) ) {
			?>
			<strong><?php esc_html_e( 'Donation for Pet(s)', 'pets' ); ?></strong><br/>
			<?php
			if ( 0 === absint( $pet ) ) {
				echo __( 'All', 'pets' );
			} else {
				echo '<a href="' . get_permalink( $pet ) . '" target="_blank">' . get_the_title( $pet ) . '</a>';
			}
		}
	}

	/**
	 * Adding Receipt Args.
	 *
	 * @param $args
	 * @param $donation_id
	 */
	public function donation_receipt_args( $args, $donation_id ) {
		$pet = give_get_payment_meta( $donation_id, 'selected_pet', true );

		if ( is_numeric( $pet ) ) {
			if ( 0 === absint( $pet ) ) {
				$title = __( 'All', 'pets' );
			} else {
				$title = get_the_title( $pet );
			}

			if ( $title ) {
				$args[] = array(
					'name'    => __( 'Donation for Pet(s)', 'pets' ),
					'display' => true,
					'value'   => $title,
				);
			}
		}

		return $args;
	}

	/**
	 * Save the selected Pet.
	 *
	 * @param $payment_id
	 * @param $data
	 */
	public function save_selected_pet( $payment_id, $data ) {
		if ( isset( $_POST['give_form_select_pet'] ) ) {

		    if ( ! $payment_id ) {
		        return;
            }

			$payment = new \Give_Payment( $payment_id );
			$payment->update_meta( 'selected_pet', absint( $_POST['give_form_select_pet'] ) );

			$sponsor_id = 0;
			$donor_id = $payment->donor_id;
			$sponsors = get_terms( array(
                'taxonomy'   => 'sponsors',
                'hide_empty' => false,
                'meta_query' => array (
                    array(
                        'key'   => '_give_donor_id',
                        'value' => $donor_id,
                        'type'  => 'NUMERIC'
                    )
                )
            ));

			if ( is_wp_error( $sponsors ) ) {
			    return;
            }

			if ( ! $sponsors ) {
			    // This Donor is not connected with any Sponsor. Let's create it.
                $name    = trim( $data['user_info']['first_name'] . ' ' . $data['user_info']['last_name'] );
				$sponsor = wp_insert_term( $name, 'sponsors' );
				if ( is_wp_error( $sponsor ) ) {
				    return;
                }

                $sponsor_id = $sponsor['term_id'];
				add_term_meta( $sponsor_id, '_give_donor_id', absint( $donor_id ) );

				if ( $data['user_info']['id'] ) {
					add_term_meta( $sponsor_id, '_user_id', absint( $data['user_info']['id'] ) );
                }
            } else {
			    $sponsor_id = $sponsors[0]->term_id;
            }

            if ( $sponsor_id ) {
	            wp_set_post_terms( absint( $_POST['give_form_select_pet'] ), array( $sponsor_id ), 'sponsors', true );
            }
		}
	}

	/**
	 * @param array $tabs
	 *
	 * @return mixed
	 */
	public function add_tab( $tabs ) {
		$tabs['give'] = __( 'Give', 'pets' );
		return $tabs;
	}

	/**
	 * @param array $settings
	 */
	public function add_settings( $settings ) {
		$args  = array( 'post_type' => 'give_forms', 'post_status' => 'publish', 'posts_per_page' => -1 );
		$forms = get_posts( $args );
		$form_ids = array();
		foreach ( $forms as $form ) {
			$form_ids[ $form->ID ] = $form->post_title;
		}
		$settings['give'] = array(
			'form_id'   => array(
				'type'    => 'select',
				'title'   => __( 'Give Donation Form', 'pets' ),
				'options' => $form_ids,
			),
            'form_type' => array(
                'type'    => 'select',
                'title'   => __( 'Show form on a Pet Page as', 'pets' ),
                'options' => array(
                    'form' => __( 'Form', 'pets' ),
                    'link' => __( 'Link to donation form', 'pets' ),
                )
            ),
		);
		return $settings;
	}

	/**
	 * @param $donation_form_id
	 * @param $args
	 * @param $form
	 */
	public function pet_selector( $donation_form_id, $args, $form ) {
		$form_id = pets_get_setting( 'form_id', 'give', 0 );

		if ( ! $form_id ) {
			return;
		}

		if ( absint( $donation_form_id ) !== absint( $form_id ) ) {
			return;
		}

		$single_pet = isset( $_GET['pet'] ) ? absint( $_GET['pet'] ) : ( isset( $args['pet'] ) ? absint( $args['pet'] ) : 0);
		if ( ! $single_pet ) {
			$args = array( 'post_type' => 'pets', 'post_status' => 'publish', 'posts_per_page' => -1 );
			$pets = get_posts( $args );
			?>
			<fieldset id="give_form_select_a_pet">
				<legend>
					<label for="give_form_select_pet">
					<?php esc_html_e( 'Select a Pet', 'pets' ); ?>
					</label>
				</legend>
				<p class="form-row form-row-wide">
					<select name="give_form_select_pet" id="give_form_select_pet">
						<option value="0"><?php esc_html_e( 'Donate for all', 'pets' ); ?></option>
						<?php
						if ( $pets ) {
							foreach ( $pets as $pet ) {
								?>
								<option value="<?php echo esc_attr( $pet->ID ); ?>"><?php echo $pet->post_title; ?></option>
								<?php
							}
						}
						?>
					</select>
				</p>
			</fieldset>
			<?php
		} else {
			?>
			<fieldset id="give_form_select_a_pet">
				<legend>
					<label for="give_form_select_pet">
						<?php echo sprintf( __( 'Donating for %s', 'pets' ), get_the_title( $single_pet ) ); ?>
					</label>
				</legend>
				<input type="hidden" name="give_form_select_pet" value="<?php echo $single_pet ?>" />
				<?php
					if ( has_post_thumbnail( $single_pet ) ) {
				?>
				<p class="form-row form-row-first form-row-responsive">
					<?php echo get_the_post_thumbnail( $single_pet, 'full' ); ?>
				</p>
				<p class="form-row form-row-last form-row-responsive">
					<strong><?php echo get_the_title( $single_pet ); ?></strong><br/>
					<?php
						echo get_the_excerpt( $single_pet );
					?>
				</p>
				<?php } ?>
			</fieldset>
			<?php
		}
	}
}

if ( class_exists( 'Give' ) ) {
	new Give();
}
