<?php
/**
 * Globally Available functions for getting settings.
 */

/**
 * @param        $id
 * @param string $tab
 * @param string $default
 */
function pets_get_setting( $id, $tab = '', $default = '' ) {
	$settings = new \Pets\Admin\Settings();
	return $settings->get_setting( $id, $tab, $default );
}