<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

// Function to display the option page and bookings
function my_booking_plugin_option_page() {

    $bookingClass = new BookedInBookings();
    $addonClass = new BookedInAddons();
    $pricingClass = new BookedInPricings();
    $resourcesClass = new BookedInResources();

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
        $booking_deposit_refund = sanitize_text_field($_POST['booking_deposit_refund']);
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

        $resource = $resourcesClass->get_resources($booking_resource);

        [$resource_output, $addon_output, $total, $discount_used] = $pricingClass->get_price_after_discount($booking_discount, $booking_date_from, $booking_date_to, $resource, $selectedAddons, $booking_adults, $booking_children);
        
        

        $booking_discount_used = array();
        foreach ($discount_used as $discount) {
            $booking_discount_used[] = $discount['discount_name'];
            $pricingClass->use_discount($discount);
        }

        // For public
        $booking_price_total = $total['total_after_final_discounted'];
        
        [$booking_header_id, $booking_number] = $bookingClass->add_booking_header($booking_date_from, $booking_date_to, $booking_resource, $booking_notes, $booking_description, $booking_paid, $booking_deposit_refund, json_encode($booking_discount_used), $booking_price, $booking_adults, $booking_children, $booking_user, $booking_email, $booking_phone);

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