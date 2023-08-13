<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

// Function to display the option page and bookings
function my_booking_plugin_option_page() {

    $bookingClass = new BookedInBookings();

    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

    // Include the template files
    include_once('templates/new-booking-form.php');
    include_once('templates/booking-table.php');
    include_once('templates/booking-calendar.php');

    // Handle form submissions to add new bookings
    if (isset($_POST['add_booking'])) {
        $booking_date_from = sanitize_text_field($_POST['booking_date_from']);
        $booking_date_to = sanitize_text_field($_POST['booking_date_to']);
        $booking_resource = sanitize_text_field($_POST['booking_resource']);
        $booking_notes = sanitize_text_field($_POST['booking_notes']);
        $booking_description = sanitize_textarea_field($_POST['booking_description']);
        $booking_paid = sanitize_text_field($_POST['booking_paid']);
        $booking_price = sanitize_text_field($_POST['booking_price']);
        $booking_adults = sanitize_text_field($_POST['booking_adults']);
        $booking_children = sanitize_text_field($_POST['booking_children']);    
        $booking_user = sanitize_text_field($_POST['booking_user']);
        $booking_email = sanitize_text_field($_POST['booking_email']);
        $booking_phone = sanitize_text_field($_POST['booking_phone']);

        if (isset($_POST['booking_addon']) && !empty($_POST['booking_addon'])) {
            $booking_addon = array_map('sanitize_text_field', $_POST['booking_addon']);
        }

        // Check if its available
        $available = $bookingClass->get_available($booking_date_from, $booking_date_to);
        if (count($available) == 0) {
            echo '<script>alert("Sorry, this date is not available. Please try another date.");location.reload(); </script>';
            return;
        }

        $booking_header_id = $bookingClass->add_booking_header($booking_date_from, $booking_date_to, $booking_resource, $booking_notes, $booking_description, $booking_paid, $booking_price, $booking_adults, $booking_children, $booking_user, $booking_email, $booking_phone);

        $nights = $bookingClass->get_nights($booking_date_from, $booking_date_to);

        for ($i = 0; $i < $nights; $i++) {
            $booking_date = date('Y-m-d', strtotime("$booking_date_from + $i days"));           
            $bookingClass->add_booking($booking_header_id, $booking_date, $booking_resource, $booking_paid);
            if (isset($booking_addon) && !empty($booking_addon)) {
                foreach ($booking_addon as $addon) {
                    $bookingClass->add_booking_addon($booking_header_id, $booking_date, $addon, $booking_paid);
                }
            }
        }
            
    }

    // Handle booking deletion
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['booking_id'])) {
        $booking_id = intval($_GET['booking_id']);
        $bookingClass->delete_booking($booking_id);
    }

    

    bookedInNavigation('Dashboard');
    ?>
    <div class="wrap">

        <br>

        <h2>Add New booking</h2>
        
        <?php newBookingForm() ?>

        <br>
        <br>

        <!-- Display existing bookings -->
        <h2>Existing bookings</h2>
        
        <?php bookingTable() ?>

    </div>
    <?php
    bookInFooter();
}