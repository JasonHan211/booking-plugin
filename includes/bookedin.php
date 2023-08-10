<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;


class BookedIn {

    public function __construct() {

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    }

    public function initialize() {
        
        function enqueue_admin_scripts() {
            wp_enqueue_script('jquery'); // Enqueue jQuery
        }
        
        add_action('admin_enqueue_scripts', 'enqueue_admin_scripts');

        // Compulsory files
        require_once (BI_PLUGIN_PATH . '/includes/utilities/utilities.php');

        // Templates
        require_once (BI_PLUGIN_PATH . '/includes/templates/templates.php');

        // Option pages
        require_once (BI_PLUGIN_PATH . '/includes/option-page.php');

    }

}

if (class_exists('BookedIn')) {
    $bookedIn = new BookedIn();
    $bookedIn->initialize();
}
