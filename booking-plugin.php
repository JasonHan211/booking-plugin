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
        include_once PLUGIN_PATH . '/includes/utilities.php';
        include_once PLUGIN_PATH . '/includes/option-page.php';
        include_once PLUGIN_PATH . '/includes/contact-form.php';
    }

}

if (class_exists('BookingPlugin')) {
    $bookingPlugin = new BookingPlugin();
    $bookingPlugin->initialize();
}

?>