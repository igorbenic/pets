<?php
/**
 * PetFinder Integration
 */

namespace Pets\Integrations;

use Pets\Pets_Cache;

class PetFinder {

	/**
	 * URL to PetFinder API.
	 *
	 * @var string
	 */
	public static $url = 'https://api.petfinder.com/v2/';

	/**
	 * Give constructor.
	 */
	public function __construct() {

		add_filter( 'pets_settings_tabs', array( $this, 'add_tab' ) );
		add_filter( 'pets_settings_fields', array( $this, 'add_settings' ) );
	}

	/**
	 * Get the bearer token.
	 */
	public static function get_bearer_token() {
		$token = get_transient( 'pets_petfinder_token' );
		if ( is_array( $token ) ) {
			if ( ! isset( $token['timestamp'] ) ) {
				$token = false;
			} else {
				if ( time() > $token['timestamp'] ) {
					$token = false;
				}
			}
		}
		if ( false === $token ) {
			$result = wp_remote_post( self::$url . 'oauth2/token', array(
				'body' => array(
					'grant_type'    => 'client_credentials',
					'client_id'     => pets_get_setting( 'petfinder_api_key', 'petfinder' ),
					'client_secret' => pets_get_setting( 'petfinder_api_secret', 'petfinder' ),
				),
			));

			if ( 200 === wp_remote_retrieve_response_code( $result ) ) {
				$token = json_decode( wp_remote_retrieve_body( $result ), true );
				$token['timestamp'] = time() + $token['expires_in'] - 100;
				set_transient( 'pets_petfinder_token', $token, time() + $token['expires_in'] - 100 );
			}
		}

		return $token;
	}

	/**
	 * Get PetFinder
	 * @param $path
	 * @param $params
	 */
	public static function get( $path, $params = array() ) {
		$token = self::get_bearer_token();

		if ( ! $token ) {
			return false;
		}

		$headers = array(
			'Authorization' => 'Bearer ' . $token['access_token'],
		);

		$allowed_params = array(
			'type',
			'breed',
			'size',
			'gender',
			'age',
			'color',
			'coat',
			'status',
			'name',
			'organization',
			'good_with_children',
			'good_with_dogs',
			'good_with_cats',
			'location',
			'distance',
			'before',
			'after',
			'sort',
			'limit',
			'page'
		);

		foreach ( $params as $param => $value ) {
			if ( ! in_array( $param, $allowed_params, true ) ) {
				unset( $params[ $param ] );
			}
		}

		$url = add_query_arg($params,self::$url . $path );
		$result = wp_remote_get( $url, array(
			'headers' => $headers
		));

		if ( 200 !== wp_remote_retrieve_response_code( $result ) ) {
			return new \WP_Error( 'pets-error', wp_remote_retrieve_response_message( $result ) );
		}

		return json_decode( wp_remote_retrieve_body( $result ), true );
	}

	/**
	 * @return bool|mixed|\WP_Error
	 */
	public static function get_types() {
		$types = Pets_Cache::get_cache('types' );
		if ( false === $types ) {
			$types = self::get( 'types' );
			if ( is_wp_error( $types ) ) {
				return $types;
			}
			Pets_Cache::set_cache( 'types', $types, DAY_IN_SECONDS );
		}

		return $types;
	}


	/**
	 * @param array $tabs
	 *
	 * @return mixed
	 */
	public function add_tab( $tabs ) {
		$tabs['petfinder'] = __( 'Petfinder', 'pets' );
		return $tabs;
	}

	/**
	 * @param array $settings
	 */
	public function add_settings( $settings ) {


		$settings['petfinder'] = array(
			'petfinder_api_key'   => array(
				'type'    => 'text',
				'title'   => __( 'Petfinder API Key', 'pets' ),
			),
			'petfinder_api_secret'   => array(
				'type'    => 'text',
				'title'   => __( 'Petfinder API Secret', 'pets' ),
			),
			'petfinder_organization_id'   => array(
				'type'    => 'text',
				'title'   => __( 'Petfinder Organization ID', 'pets' ),
			),
			'petfinder_shortcode_atts'   => array(
				'type'    => 'description',
				'title'   => sprintf( '<a target="_blank" href="https://www.petfinder.com/developers/v2/docs/#get-animals">%s</a>', __( 'To Check all shortcode Atts, visit this link.', 'pets' ) ),
			),
			'petfinder_shortcode_example'   => array(
				'type'    => 'description',
				'title'   => $this->get_field_examples(),
			),
		);
		return $settings;
	}

	public function get_field_examples() {
		ob_start();
		?>
		<p>Please make sure to create the fields under Pets > Fields to match the Petfinder attributes found on <a href="https://www.petfinder.com/developers/v2/docs/#get-animals" target="_blank">https://www.petfinder.com/developers/v2/docs/#get-animals</a>.</p>

		<p>For example, for attribute status, create a field:</p>
		<ul>
			<li>Name: Status (or any other)</li>
			<li>slug: status (Important to be the same as attribute on Petfinder)</li>
			<li>type: dropdown</li>
			<li>options: Adoptable, Found, Adopted</li>
		</ul>
		<?php

		return ob_get_clean();
	}
}

new PetFinder();
