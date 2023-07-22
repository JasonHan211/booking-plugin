<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

require_once (BI_PLUGIN_PATH . '/includes/booking/option-page.php');

// Add the top-level menu page
function bookedIn_add_menu_page()
{
    add_menu_page(
        'BookedIn',
        'BookedIn',
        'manage_options',
        'bookedin_main_slug',
        'my_booking_plugin_option_page',
        'dashicons-calendar', // You can change the icon to suit your needs
        2 // Adjust the position to place it among other top-level pages (change 30 to any other number)
    );
}
add_action('admin_menu', 'bookedIn_add_menu_page');