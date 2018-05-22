<?php
/**
 * Plugin Name:     Pets
 * Plugin URI:      http://petswp.com
 * Description:     Manage Pets and Animal Shelters
 * Author:          ibenic
 * Author URI:      https://ibenic.com
 * Text Domain:     pets
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Pets
 */
namespace Pets;

if( ! defined( 'ABSPATH' ) ) {
    return;
}

final class Pets {

    /**
     * Version
     * @var string
     */
    public $version = '0.1.0';

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

        /**
         * Database Classes
         */
        include_once 'includes/db/class-pets-fields.php';

        if( is_admin() ) {
            include_once 'includes/admin/class-admin.php';
        }
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
        }
    }

    /**
     * Action/Filters Hooks
     * @return void 
     */
    private function hooks() {
        $cpt = new CPT();

        /**
         * Actions
         */
        add_action( 'init', array( $cpt, 'init' ) );

        /**
         * Filters
         */
        add_filter( 'post_updated_messages', array( $cpt, 'update_messages' ) );
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