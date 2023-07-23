<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

// Function to display the option page and bookings
function my_booking_plugin_option_page() {
    
    // Check if the user has the required permissions to view the option page
    if (!current_user_can('manage_options')) {
        return;
    }

    // Get all bookings from the database
    global $wpdb;
    $table_name = $wpdb->prefix . 'bookings';
    $bookings = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);

    // Display the bookings on the option page
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <table class="wp-list-table widefat striped">
            <thead>
                <tr>
                    <th><?php esc_html_e('ID', 'my-booking-plugin'); ?></th>
                    <th><?php esc_html_e('Booking Date', 'my-booking-plugin'); ?></th>
                    <th><?php esc_html_e('Name', 'my-booking-plugin'); ?></th>
                    <th><?php esc_html_e('Email', 'my-booking-plugin'); ?></th>
                    <th><?php esc_html_e('Details', 'my-booking-plugin'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($bookings as $booking) {
                    echo '<tr>';
                    echo '<td>' . esc_html($booking['id']) . '</td>';
                    echo '<td>' . esc_html($booking['booking_date']) . '</td>';
                    echo '<td>' . esc_html($booking['booking_name']) . '</td>';
                    echo '<td>' . esc_html($booking['booking_email']) . '</td>';
                    echo '<td>' . esc_html($booking['booking_details']) . '</td>';
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php
}