<?php

include_once PLUGIN_PATH . '/includes/booking/booking.php';

// Add the top-level menu page
function my_booking_plugin_add_menu_page()
{
    add_menu_page(
        __('Booking Management', 'my-booking-plugin'),
        __('Booking Management', 'my-booking-plugin'),
        'manage_options',
        'my-booking-plugin',
        'my_booking_plugin_option_page',
        'dashicons-calendar', // You can change the icon to suit your needs
        30 // Adjust the position to place it among other top-level pages (change 30 to any other number)
    );
}
add_action('admin_menu', 'my_booking_plugin_add_menu_page');