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
				'template' => 'multi',
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
}