<?php
/**
 * Plugin Name:     Pets
 * Plugin URI:      http://petswp.com
 * Description:     Manage Pets and Animal Shelters
 * Author:          ibenic
 * Author URI:      https://ibenic.com
 * Text Domain:     pets
 * Domain Path:     /languages
 * Version:         0.2.1
 *
 * @package         Pets
 */
namespace Pets;

if( ! defined( 'ABSPATH' ) ) {
    return;
}

// Create a helper function for easy SDK access.
function pet_fs() {
	global $pet_fs;

	if ( ! isset( $pet_fs ) ) {
		// Include Freemius SDK.
		require_once dirname(__FILE__) . '/freemius/start.php';

		$pet_fs = fs_dynamic_init( array(
			'id'                  => '2120',
			'slug'                => 'pets',
			'type'                => 'plugin',
			'public_key'          => 'pk_d54c88070fcd447603456014d42ba',
			'is_premium'          => true,
			'has_addons'          => false,
			'has_paid_plans'      => true,
			'menu'                => array(
				'slug'           => 'edit.php?post_type=pets',
				'contact'        => false,
				'support'        => false,
			),
		) );
	}


	return $pet_fs;
}

// Init Freemius.
pet_fs();
// Signal that SDK was initiated.
do_action( 'pet_fs_loaded' );

final class Pets {

    /**
     * Version
     * @var string
     */
    public $version = '0.2.1';

    /**
     * Run everything
     * @return void 
     */
    public function run() {
        $this->define_constants();
        $this->includes();
        $this->init();
        $this->hooks();
    }

    public function define_constants() {
        define( 'PETS_PATH', plugin_dir_path( __FILE__ ) );
        define( 'PETS_URL', plugin_dir_url( __FILE__ ) );
    }

    /**
     * Including files
     * @return void 
     */
    private function includes() {
        include_once 'includes/class-pets-cpt.php';
	    include_once 'includes/class-pets-fields.php';
	    include_once 'includes/class-pets-cache.php';
	    include_once 'includes/class-pets-search.php';
	    include_once 'includes/class-pets-shortcodes.php';
	    include_once 'includes/class-pets-pet.php';
	    include_once 'includes/class-pets-widgets.php';
	    // Settings Class.
	    include_once 'includes/admin/settings/class-settings.php';
	    include_once 'includes/functions-settings.php';

	    include_once 'includes/widgets/class-widgets-single-pet.php';

	    include_once 'includes/functions-templates.php';

        /**
         * Database Classes
         */
        include_once 'includes/db/class-pets-fields.php';

        if( is_admin() ) {
            include_once 'includes/admin/class-admin.php';
        } else {
        	include_once 'includes/class-pets-template.php';
        }
    }

	/**
	 * Include integrations
	 */
	public function include_integrations() {
    	include_once 'includes/integrations/class-give.php';
	}

    /**
     * Initialize classes, settings.
     * @return void
     */
    public function init() {
        \register_activation_hook( __FILE__, array( '\Pets\DB\Fields', 'install' ) );

        if( is_admin() ) {
            $admin = new Admin\Admin();
            $admin->load();
        } else {
        	$template = new Pets_Template();
        	$template->init();
        	new Shortcodes();
        }
    }

    /**
     * Action/Filters Hooks
     * @return void 
     */
    private function hooks() {
        $cpt     = new CPT();
        $widgets = new Widgets();

        /**
         * Actions
         */
        add_action( 'init', array( $cpt, 'init' ) );
	    add_action( 'init', array( $this, 'add_image_sizes' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
        add_action( 'plugins_loaded', array( $this, 'include_integrations') );
        add_action( 'pets_before_loop', array( '\Pets\Search', 'add_form' ) );
	    add_action( 'pre_get_posts', array( '\Pets\Search', 'pets_per_page' ) );
	    add_action( 'widgets_init', array( $widgets, 'register_widgets' ) );

	    /**
         * Filters
         */
        add_filter( 'post_updated_messages', array( $cpt, 'update_messages' ) );
    }

	/**
	 * Enqueueing Scripts and Styles for Public.
	 */
    public function enqueue() {
		wp_enqueue_style( 'pets-css', PETS_URL . '/assets/css/public/pets.css', '', '' );
    }

	/**
	 * Define Image sizes.
	 */
    public function add_image_sizes() {
		if ( ! \has_image_size( 'pets-thumbnail' ) ) {
			\add_image_size( 'pets-thumbnail', 360 );
		}
    }
}

/**
 * Starting our plugin
 * @return void 
 */
function run() {
    $pets = new Pets();
    $pets->run();
}
run();