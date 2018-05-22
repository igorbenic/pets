<?php

/**
 * Main Admin Class
 *
 * @package  Pets\Admin
 */

namespace Pets\Admin;
use \Pets\Fields;

/**
 * Admin
 */
class Admin {

    /**
     * Loading the Admin
     * @return void 
     */
    public function load() {
        add_action( 'admin_menu', array( $this, 'menus' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
    }

    /**
     * Admin Menus
     * @return void
     */
    public function menus() {
        add_submenu_page( 
            'edit.php?post_type=pets', 
            __( 'Settings', 'pets' ), 
            __( 'Settings', 'pets' ), 
            'manage_options', 
            'pets-settings',
            array( $this, 'settings_page' )
        );

        add_submenu_page( 
            'edit.php?post_type=pets', 
            __( 'Fields', 'pets' ), 
            __( 'Fields', 'pets' ), 
            'manage_options', 
            'pets-fields',
            array( $this, 'settings_page' )
        );
    }

    /**
     * Main Settings
     * @return void 
     */
    public function settings_page() {
        include_once 'settings/class-settings.php';
        $settings = new Settings();
        $settings->load();
        $settings->page();
    }

	/**
	 * @param string $hook
	 */
    public function enqueue( $hook ) {
		wp_enqueue_style( 'pets-admin', PETS_URL . '/assets/css/admin/admin.css' );

		if ( 'pets_page_pets-fields' === $hook ) {
			wp_enqueue_script( 'pets-fields', PETS_URL . '/assets/js/admin/fields.js', array( 'jquery', 'wp-util' ), '', true );
			wp_localize_script( 'pets-fields', 'pets_fields', array(
				'types' => Fields::get_field_types()
			));
		}
    }
 }