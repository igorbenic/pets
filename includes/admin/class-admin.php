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
	    add_action( 'add_meta_boxes', array( $this, 'metaboxes' ) );
	    add_action( 'save_post', array( $this, 'save_post' ), 20, 2 );


	    include PETS_PATH . '/includes/abstracts/class-metabox.php';
	    $this->post_types();
    }

    private function post_types() {
    	include 'post-types/class-pets-sponsors.php';

	    include 'post-types/class-pets-type.php';
    }


	/**
	 * Saving the values.
	 *
	 * @param $post_id
	 * @param $post
	 */
    public function save_post( $post_id, $post ) {
		if ( 'pets' !== get_post_type( $post ) ) {
			return;
		}

		if ( wp_is_post_revision( $post ) ) {
			return;
		}

		if ( wp_doing_ajax() ) {
			return;
		}

		if ( wp_is_post_autosave( $post ) ) {
			return;
		}

		\Pets\Fields::save_fields( $post_id, $_POST );
    }

	/**
	 * Adding Metaboxes
	 */
    public function metaboxes() {
	    add_meta_box( 'pets-fields', __( 'Information', 'pets' ), array( '\Pets\Fields', 'metabox' ), 'pets' );
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
        $settings = new Settings();
        $settings->load();
        $settings->page();
    }

	/**
	 * @param string $hook
	 */
    public function enqueue( $hook ) {
		wp_enqueue_style( 'pets-admin', PETS_URL . '/assets/css/admin/admin.css' );
	    wp_enqueue_style( 'pets-fontawesome', 'https://use.fontawesome.com/releases/v5.1.0/css/all.css' );

		if ( 'pets_page_pets-fields' === $hook ) {
			wp_enqueue_media();
			wp_enqueue_script( 'pets-fields', PETS_URL . '/assets/js/admin/fields.js', array( 'jquery', 'wp-util' ), '', true );
			wp_localize_script( 'pets-fields', 'pets_fields', array(
				'types' => Fields::get_field_types()
			));
		}
    }
 }
