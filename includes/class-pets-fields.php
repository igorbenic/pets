<?php
/**
 * Class Fields is used for operating and managing fields.
 */

namespace Pets;

use Pets\DB\Fields_Sections;

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
		$sections  = self::get_fields( $post_id, true, true );

		echo '<table class="form-table">';
		foreach ( $sections as $section ) {
			$icon = '';
			if ( $section['icon'] ) {
				if ( is_numeric( $section['icon'] ) ) {
					$icon = '<img width="50" src="' . wp_get_attachment_thumb_url( $section['icon'] ) . '">';
				} else {
					$icon = '<span class="' . $section['icon'] . '"></span>';
				}
			}
			echo '<tr><th class="pets-section-title" colspan="2">' . $icon . $section['title'] . '</th></tr>';
			foreach ( $section['fields'] as $field ) {
				self::render_field( $field, $post_id );
			}
		}
		echo '</table>';
	}

	/**
	 * Get all sections for Fields.
	 *
	 * @return array
	 */
	public static function get_sections() {
		$sections_db = new Fields_Sections();
		$_sections   = $sections_db->get_all();
		$sections    = array(
			array(
				'title' => __( 'Information', 'pets' ),
				'id'    => 0,
				'icon'  => '',
				'slug'  => 'information',
			),
		);
		if ( $_sections ) {
			foreach ( $_sections as $section ) {
				$sections[ $section['id'] ] = $section;
			}
		}
		return apply_filters( 'pets_fields_sections', $sections );
	}

	/**
	 * @param integer $post_id       Post ID.
	 * @param boolean $with_sections (Optional).
	 * @param boolean $admin         Is it for admin usage.
	 *
	 * @return array
	 */
	public static function get_fields( $post_id, $with_sections = true, $admin = false ) {
		$fields = self::get_cached_fields();

		$sections = array();
		if ( $with_sections ) {
			$sections = self::get_sections();
		}

		if ( $fields ) {
			foreach ( $fields as $order => $field ) {
				$value = get_post_meta( $post_id, '_' . $field['slug'], true );
				if ( ! $admin && 'checkbox' === $field['type'] ) {
					if ( $value ) {
						$field['value'] = __( 'Yes', 'pets' );
					} else {
						$field['value'] = __( 'No', 'pets' );
					}
				} else {
					$field['value'] = $value;
				}
				if ( $with_sections ) {
					$section = isset( $field['field_section'] ) ? absint( $field['field_section'] ) : 0;
					if ( ! isset( $sections[ $section ] ) ) {
						$section = 0;
					}
					if ( ! isset( $sections[ $section ]['fields'] ) ) {
						$sections[ $section ]['fields'] = array();
					}
					$sections[ $section ]['fields'][] = $field;
				}
				$fields[ $order ] = $field;
			}
			if ( $with_sections ) {
				$sections = array_filter( $sections, array( __CLASS__, 'remove_empty_sections' ) );
				return apply_filters( 'pets_get_fields_sections', $sections, $post_id );
			}
			$fields = apply_filters( 'pets_get_fields', $fields, $post_id );
		}

		return $fields;
	}

	public static function remove_empty_sections( $section ) {
		if ( ! isset( $section['fields'] ) ) { return false; }
		if ( ! $section['fields'] ) { return false; }
		return true;
	}

	/**
	 * @param $post_id
	 */
	public static function save_fields( $post_id, $data = array() ) {
		$fields_db = new \Pets\DB\Fields();
		$fields    = $fields_db->get_all();

		foreach ( $fields as $field ) {
			$name = 'pets_field_' . $field['slug'];
			if ( isset( $data[ $name ] ) ) {
				update_post_meta( $post_id, '_' . $field['slug'], $data[ $name ] );
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

		$name = 'pets_field_' . $args['slug'];

		if ( isset( $args['name'] ) ) {
			$name = $args['name'];
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
				$html .= '<input class="widefat" name="' . $name . '" id="pets-field-' . $args['slug'] . '" type="text" value="' . esc_attr( $args['value'] ) . '"/>';
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
				$html .= '<textarea class="widefat" name="' . $name . '" rows="5" id="pets-field-' . $args['slug'] . '">' . $args['value'] . '</textarea>';
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
				$html .= '<select name="' . $name . '" class="widefat" id="pets-field-' . $args['slug'] . '">';
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
						$html .= '<input name="' . $name . '" type="radio" value="' . esc_attr( $option ) . '" ' . checked( $option, $args['value'], false ) . '>' . $option;
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

				$html .= '<input id="pets-field-' . $args['slug'] . '" name="' . $name . '" type="checkbox" value="1" ' . checked( '1', $args['value'], false ) . '>';

				$html .= '</td>';
				$html .= '</tr>';
				echo $html;
				break;
			default:
				do_action( 'pets_fields_render_field_' . $args['type'], $args );
				break;
		}
	}

	/**
	 * @param $icon
	 *
	 * @return string
	 */
	public static function get_section_icon_html( $icon ) {
		if ( ! $icon ) { return ''; }
		if ( is_numeric( $icon ) ) {
			return wp_get_attachment_image( $icon );
		} else {
			return '<span class="' . $icon . '"></span>';
		}
	}

	/**
	 * Returning true only for searchable items. Used in array_filter.
	 *
	 * @param $item
	 *
	 * @return bool
	 */
	public static function only_searchable( $item ) {
		return absint( $item['searchable'] ) === 1;
	}

	/**
	 * Returning true only for items to show in forms. Used in array_filter.
	 *
	 * @param $item
	 *
	 * @return bool
	 */
	public static function only_forms( $item ) {
		return absint( $item['forms'] ) === 1;
	}


	/**
	 * Get Cached Fields from DB.
	 */
	public static function get_cached_fields() {
		$fields = Pets_Cache::get_cache( 'fields' );

		if ( false === $fields ) {
			$fields_db = new \Pets\DB\Fields();
			$fields    = $fields_db->get_all();
			Pets_Cache::set_cache( 'fields', $fields );
		}

		return $fields;
	}
}
