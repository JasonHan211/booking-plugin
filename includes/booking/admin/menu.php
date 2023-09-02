<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

// Function to display the option page and bookings
function my_booking_plugin_option_page() {

    $bookingClass = new BookedInBookings();
    $addonClass = new BookedInAddons();
    $pricingClass = new BookedInPricings();

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
        $booking_discount = sanitize_text_field($_POST['booking_discount']);
        $booking_adults = sanitize_text_field($_POST['booking_adults']);
        $booking_children = sanitize_text_field($_POST['booking_children']);    
        $booking_user = sanitize_text_field($_POST['booking_user']);
        $booking_email = sanitize_text_field($_POST['booking_email']);
        $booking_phone = sanitize_text_field($_POST['booking_phone']);

        
        $selectedAddons = array();
        $booking_addon = array();
        if (isset($_POST['booking_addon']) && !empty($_POST['booking_addon'])) {
            $booking_addon = array_map('sanitize_text_field', $_POST['booking_addon']);
            $allAddon = $addonClass->get_addons(null,'Y');

            // Get all addon that are selected
            foreach ($allAddon as $addon) {
                if (in_array($addon['id'], $booking_addon)) {
                    $selectedAddons[] = $addon;
                }
            }
        }

        // Check if its available
        $available = $bookingClass->get_available($booking_date_from, $booking_date_to);
        if (count($available) == 0) {
            echo '<script>alert("Sorry, this date is not available. Please try another date.");location.reload(); </script>';
            return;
        }

        [$price, $_, $_] = $bookingClass->calculate_price($booking_date_from, $booking_date_to, $booking_resource, $booking_addon, $booking_adults, $booking_children, $booking_discount);
        
        
        $booking_header_id = $bookingClass->add_booking_header($booking_date_from, $booking_date_to, $booking_resource, $booking_notes, $booking_description, $booking_paid, $booking_discount, $booking_price, $booking_adults, $booking_children, $booking_user, $booking_email, $booking_phone);

        // Add booking for selected addon with charge once
        foreach ($selectedAddons as $addon) {
            if ($addon['addon_perday'] == 'N') {
                $bookingClass->add_booking_addon($booking_header_id, $booking_date_from, $addon['id'], $booking_paid, $booking_discount);
            }
        }

        $nights = $bookingClass->get_nights($booking_date_from, $booking_date_to);

        for ($i = 0; $i < $nights; $i++) {
            $booking_date = date('Y-m-d', strtotime("$booking_date_from + $i days"));           
            $bookingClass->add_booking($booking_header_id, $booking_date, $booking_resource, $booking_paid);
            
            // Add booking for selected addon with charge per day
            foreach ($selectedAddons as $addon) {
                if ($addon['addon_perday'] == 'Y') {
                    $bookingClass->add_booking_addon($booking_header_id, $booking_date, $addon['id'], $booking_paid, $booking_discount);
                }
            }
        }

        // Update discount quantity
        $discount = $pricingClass->get_discount_by_code($booking_discount);
        $new_quantity = $discount['discount_quantity'] - 1; 
        $pricingClass->update_discount($discount['id'],$discount['discount_name'],$discount['discount_description'],$discount['discount_code'],$new_quantity,$discount['discount_type'],$discount['discount_amount'],$discount['discount_start_date'],$discount['discount_end_date'],$discount['discount_on_type'],$discount['discount_on_id'],$discount['discount_condition'],$discount['discount_condition_start'],$discount['discount_condition_end'],$discount['discount_auto_apply'],$discount['discount_active']);

            
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