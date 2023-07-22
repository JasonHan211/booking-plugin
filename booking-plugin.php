<?php

/**
 * 
 * Plugin Name: Booking Plugin
 * Description: Booking Plugin
 * Version: 1.0.0
 * Author: Jason Han
 * Text Domain: booking-plugin
 * 
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}








class BookingPlugin
{
    public function __construct()
    {
        define('PLUGIN_PATH', plugin_dir_path(__FILE__));
        define('PLUGIN_URL', plugin_dir_url( __FILE__ ));
        require_once(PLUGIN_PATH . '/vendor/autoload.php');
    }

    public function initialize()
    {
        // Utilities
        include_once PLUGIN_PATH . '/includes/utilities.php';
        
        // Register activation and deactivation hooks
        register_activation_hook(__FILE__, 'activate');
        register_deactivation_hook(__FILE__, 'deactivate');

        // Custom Contact Form
        // include_once PLUGIN_PATH . '/includes/option-page.php';

        // Custom Booking 
        include_once PLUGIN_PATH . '/includes/option-page2.php';
    }

    // Function to create the custom database table on plugin activation
    public function activate()
    {
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
    public function deactivate()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'bookings';
        $wpdb->query("DROP TABLE IF EXISTS $table_name");
    }


}

if (class_exists('BookingPlugin')) {
    $bookingPlugin = new BookingPlugin();
    $bookingPlugin->initialize();
}

?>