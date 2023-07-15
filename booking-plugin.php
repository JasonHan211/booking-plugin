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
        add_action('init', array($this, 'custom_post_type'));
    }

    public function initialize()
    {
        include_once PLUGIN_PATH . '/includes/utilities.php';
        include_once PLUGIN_PATH . '/includes/option-page.php';
    }

}

if (!class_exists('BookingPlugin')) {
    $bookingPlugin = new BookingPlugin();
    $bookingPlugin->initialize();
}

?>