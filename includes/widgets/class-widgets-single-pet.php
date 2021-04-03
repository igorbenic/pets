<?php
/**
 * Widget for showing a single pet.
 */

namespace Pets\Widgets;
use \Pets\Pet;

/**
 * Class Single_Pet
 *
 * @package Pets\Widgets
 */
class Single_Pet extends \WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'pets_single_pet', // Base ID
			esc_html__( 'Single Pet', 'pets' ), // Name
			array( 'description' => esc_html__( 'Show a single pet.', 'pets' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		$pet = ! empty( $instance['pet'] ) ? absint( $instance['pet'] ) : 0;
		if ( 0 === $pet ) {
			$wp_args = array(
				'post_type'      => 'pets',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'       => 'rand',
				'suppress_filters' => false,
			);
			$query = new \WP_Query( $wp_args );
			if ( $query->have_posts() ) {
				$pet   = $query->posts[0];
			}
		}

		if ( ! $pet ) {
		    return;
        }

		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}

        $the_pet = new Pet( $pet, true );

		$excerpt   = $the_pet->get_short_description();
        $pet_image = $the_pet->get_image();
        if ( $pet_image ) {
            echo $pet_image;
        }
		echo '<a href="' . $the_pet->get_link() . '"><strong>' . $the_pet->get_title() . '</strong></a>';
        if ( $excerpt ) {
            echo '<p>' . $excerpt . '</p>';
        }

		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'A Pet', 'pets' );
		$pet   = ! empty( $instance['pet'] ) ? absint( $instance['pet'] ) : 0;
		$pets  = get_posts( array(
			'post_type'      => 'pets',
			'post_status'    => 'publish',
			'posts_per_page' => - 1,
		) );
		?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'pets' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
                   name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text"
                   value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'pet' ) ); ?>"><?php esc_attr_e( 'Pet:', 'pets' ); ?></label>
            <select id="<?php echo esc_attr( $this->get_field_id( 'pet' ) ); ?>"
                    name="<?php echo esc_attr( $this->get_field_name( 'pet' ) ); ?>">
                <option <?php selected( $pet, 0, true ); ?> value="0"><?php esc_html_e( 'Random Pet', 'pets' ); ?></option>
				<?php
				if ( $pets ) {
					foreach( $pets as $_pet ) {
						?>
                        <option <?php selected( $pet, $_pet->ID, true ); ?> value="<?php echo esc_attr( $_pet->ID ); ?>"><?php echo $_pet->post_title; ?></option>
						<?php
					}
				}
				?>
            </select>

        </p>
		<?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance          = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : esc_html__( 'A Pet', 'pets' );;
		$instance['pet']   = ( ! empty( $new_instance['pet'] ) ) ? absint( $new_instance['pet'] ) : 0;

		return $instance;
	}

}