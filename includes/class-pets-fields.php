<?php
/**
 * Class Fields is used for operating and managing fields.
 */

namespace Pets;

if( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Fields
 *
 * @package Pets
 */
class Fields {

	/**
	 * Metabox
	 * @param $post
	 */
	public static function metabox( $post ) {
		$post_id   = $post->ID;
		$fields_db = new \Pets\DB\Fields();
		$fields    = $fields_db->get_all();

		echo '<table class="form-table">';
		foreach ( $fields as $field ) {
			self::render_field( $field, $post_id );
		}
		echo '</table>';
	}

	/**
	 * @param $post_id
	 */
	public static function get_fields( $post_id ) {
		$fields = Pets_Cache::get_cache( 'fields' );

		if ( false === $fields ) {
			$fields_db = new \Pets\DB\Fields();
			$fields    = $fields_db->get_all();
			Pets_Cache::set_cache( 'fields', $fields );
		}

		if ( $fields ) {
			foreach ( $fields as $order => $field ) {
				$value = get_post_meta( $post_id, '_' . $field['slug'], true );
				if ( 'checkbox' === $field['type'] ) {
					if ( $value ) {
						$field['value'] = __( 'Yes', 'pets' );
					} else {
						$field['value'] = __( 'No', 'pets' );
					}
				} else {
					$field['value'] = get_post_meta( $post_id, '_' . $field['slug'], true );
				}
				$fields[ $order ] = $field;
			}
			$fields = apply_filters( 'pets_get_fields', $fields, $post_id );
		}

		return $fields;
	}

	/**
	 * @param $post_id
	 */
	public static function save_fields( $post_id ) {
		$fields_db = new \Pets\DB\Fields();
		$fields    = $fields_db->get_all();

		foreach ( $fields as $field ) {
			$name = 'pets_field_' . $field['slug'];
			if ( isset( $_POST[ $name ] ) ) {
				update_post_meta( $post_id, '_' . $field['slug'], $_POST[ $name ] );
			} else {
				if ( 'checkbox' === $field['type'] ) {
					delete_post_meta( $post_id, '_' . $field['slug'] );
				}
			}
		}

		do_action( 'pets_save_fields', $post_id, $_POST );
	}

	/**
	 * Get the field types.
	 * @return array
	 */
	public static function get_field_types() {
		return apply_filters( 'pets_field_types', array(
			'text'     => array(
				'text'     => __( 'Text', 'pets' ),
				'template' => 'single',
			),
			'select'   => array(
				'text'     => __( 'Dropdown', 'pets' ),
				'template' => 'multi',
			),
			'checkbox' => array(
				'text'     => __( 'Checkbox', 'pets' ),
				'template' => 'single',
			),
			'radio'    => array(
				'text'     => __( 'Radio List', 'pets' ),
				'template' => 'multi',
			),
			'textarea' => array(
				'text'     => __( 'Text Area', 'pets' ),
				'template' => 'single',
			),
		));
	}

	/**
	 * Render the Field
	 */
	public static function render_field( $field, $post_id = 0 ) {
		$defaults = array(
			'title' => '',
			'type'  => '',
			'slug'  => '',
			'meta'  => array(),
		);

		$args = wp_parse_args( $field, $defaults );

		if ( ! isset( $args['value'] ) ) {
			$args['value'] = get_post_meta( $post_id, '_' . $args['slug'], true );
		}

		switch ( $args['type'] ) {
			case 'text':
				$html  = '<tr class="pets-field">';
				$html .= '<th>';
				$html .= '<label for="pets-field-' . $args['slug'] . '">';
				$html .= $args['title'];
				$html .= '</label>';
				$html .= '</th>';
				$html .= '<td>';
				$html .= '<input class="widefat" name="pets_field_' . $args['slug'] . '" id="pets-field-' . $args['slug'] . '" type="text" value="' . esc_attr( $args['value'] ) . '"/>';
				$html .= '</td>';
				$html .= '</tr>';
				echo $html;
				break;
			case 'textarea':
				$html  = '<tr class="pets-field">';
				$html .= '<th>';
				$html .= '<label for="pets-field-' . $args['slug'] . '">';
				$html .= $args['title'];
				$html .= '</label>';
				$html .= '</th>';
				$html .= '<td>';
				$html .= '<textarea class="widefat" name="pets_field_' . $args['slug'] . '" rows="5" id="pets-field-' . $args['slug'] . '">' . $args['value'] . '</textarea>';
				$html .= '</td>';
				$html .= '</tr>';
				echo $html;
				break;
			case 'select':
				$html  = '<tr class="pets-field">';
				$html .= '<th>';
				$html .= '<label for="pets-field-' . $args['slug'] . '">';
				$html .= $args['title'];
				$html .= '</label>';
				$html .= '</th>';
				$html .= '<td>';
				$html .= '<select name="pets_field_' . $args['slug'] . '" class="widefat" id="pets-field-' . $args['slug'] . '">';
				$html .= '<option value="">' . __( 'Select an option', 'pets' ) . '</option>';
				if ( isset( $args['meta']['options'] ) ) {
					foreach ( $args['meta']['options'] as $index => $option ) {
						$html .= '<option value="' . esc_attr( $option ) . '" ' . selected( $option, $args['value'], false ) . '>' . $option . '</option>';
					}
				}
				$html .= '</select>';
				$html .= '</td>';
				$html .= '</tr>';
				echo $html;
				break;
			case 'radio':
				$html  = '<tr class="pets-field">';
				$html .= '<th>';
				$html .= '<label for="pets-field-' . $args['slug'] . '">';
				$html .= $args['title'];
				$html .= '</label>';
				$html .= '</th>';
				$html .= '<td>';
				if ( isset( $args['meta']['options'] ) ) {
					foreach ( $args['meta']['options'] as $index => $option ) {
						$html .= '<label>';
						$html .= '<input name="pets_field_' . $args['slug'] . '" type="radio" value="' . esc_attr( $option ) . '" ' . checked( $option, $args['value'], false ) . '>' . $option;
						$html .= '<br/></label>';
					}
				}
				$html .= '</td>';
				$html .= '</tr>';
				echo $html;
				break;
			case 'checkbox':
				$html  = '<tr class="pets-field">';
				$html .= '<th>';
				$html .= '<label for="pets-field-' . $args['slug'] . '">';
				$html .= $args['title'];
				$html .= '</label>';
				$html .= '</th>';
				$html .= '<td>';


				$html .= '<input id="pets-field-' . $args['slug'] . '" name="pets_field_' . $args['slug'] . '" type="checkbox" value="1" ' . checked( '1', $args['value'], false ) . '>';


				$html .= '</td>';
				$html .= '</tr>';
				echo $html;
				break;
			default:
				do_action( 'pets_fields_render_field_' . $args['type'], $args );
				break;
		}
	}
}