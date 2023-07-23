<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;


class BookedIn {

    public function __construct() {

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    }

    public function initialize() {

        // Include the required files
        require_once (BI_PLUGIN_PATH . '/includes/includes.php');
        
        // Register activation and deactivation hooks
        register_activation_hook(__FILE__, array($this,'activate'));
        register_deactivation_hook(__FILE__, array($this,'deactivate'));

    }

    // Function to create the custom database table on plugin activation
    public function activate() {

        global $wpdb;
        $table_name = $wpdb->prefix . 'bookings';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id INT NOT NULL AUTO_INCREMENT,
            booking_date DATE NOT NULL,
            booking_name VARCHAR(255) NOT NULL,
            booking_email VARCHAR(255) NOT NULL,
            booking_details TEXT,
            PRIMARY KEY (id)
        ) $charset_collate;";

        dbDelta($sql);
    }

    // Function to remove the custom database table on plugin deactivation
    public function deactivate() {
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'bookings';
        $wpdb->query("DROP TABLE IF EXISTS $table_name");
    }


}

if (class_exists('BookedIn')) {
    $bookedIn = new BookedIn();
    $bookedIn->initialize();
}
