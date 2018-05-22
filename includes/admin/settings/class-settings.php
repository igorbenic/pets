<?php

/**
 * Admin Settings Class
 */

namespace Pets\Admin;

class Settings {

    /**
     * Including the Settings Page
     * @return void 
     */
    public function page() {
        include 'views/main.php';
    }

    /**
     * Loading Settings
     * @return void 
     */
    public function load() {
        include 'class-settings-fields.php';
        add_action( 'pets_admin_page_pets-settings', array( $this, 'settings_page' ) );
    }

    /**
     * Main Settings Page
     * @return void 
     */
    public function settings_page() {
        echo 'Only Setting';
    }
}