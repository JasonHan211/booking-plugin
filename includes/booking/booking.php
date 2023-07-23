<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

class BookedInBookings {

    // Function to create the custom database table on plugin activation
    public function activate() {

        global $wpdb;
        $table_name = $wpdb->prefix . 'bookedin_bookings';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
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
        $table_name = $wpdb->prefix . 'bookedin_bookings';
        $wpdb->query("DROP TABLE IF EXISTS $table_name");
    }

}

if (class_exists('BookedInBookings')) {
    $bookings = new BookedInBookings();
    register_activation_hook(BI_FILE, array($bookings,'activate'));
    // register_deactivation_hook(BI_FILE, array($bookings,'deactivate'));    
}
