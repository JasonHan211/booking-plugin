<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

add_shortcode('booking-form', 'show_booking_form');
add_action('rest_api_init', 'create_new_booking_rest_endpoint');

add_shortcode('payment-page', 'show_payment_page');

require_once (BI_PLUGIN_PATH . '/includes/booking/booking.php');
require_once (BI_PLUGIN_PATH . '/includes/pricings/pricing.php');
require_once (BI_PLUGIN_PATH . '/includes/resources/resources.php');
require_once (BI_PLUGIN_PATH . '/includes/addons/addons.php');



function show_booking_form() {

    require_once (BI_PLUGIN_PATH . '/includes/booking/public/templates/booking-calendar.php');
    require_once (BI_PLUGIN_PATH . '/includes/booking/public/templates/new-booking-form.php');

    bookedInHeader();
    newBookingForm();
}

function create_new_booking_rest_endpoint() {
    register_rest_route('v1/new_booking', 'submit', array(
          'methods' => 'POST',
          'callback' => 'new_booking_callback'
    ));
}

function new_booking_callback($data) {

    $bookingClass = new BookedInBookings();
    $addonClass = new BookedInAddons();
    $pricingClass = new BookedInPricings();
    $resourcesClass = new BookedInResources();

    $params = $data->get_params();
    
    if (!wp_verify_nonce($params['_wpnonce'], 'wp_rest')) {
          return new WP_Error('invalid_nonce', 'Invalid nonce', array('status' => 401));
    }

    $booking_date_from = sanitize_text_field($params['booking_date_from']);
    $booking_date_to = sanitize_text_field($params['booking_date_to']);
    $booking_resource = sanitize_text_field($params['booking_resource']);
    $booking_notes = sanitize_text_field($params['booking_notes']);
    $booking_discount = sanitize_text_field($params['booking_discount']);
    $booking_adults = sanitize_text_field($params['booking_adults']);
    $booking_children = sanitize_text_field($params['booking_children']);    
    $booking_user = sanitize_text_field($params['booking_user']);
    $booking_email = sanitize_text_field($params['booking_email']);
    $booking_phone = sanitize_text_field($params['booking_phone']);

    // if (empty($name) || empty($phone) || empty($email) || empty($message)) {
    //       return new WP_Error('fields_required', 'All fields are required', array('status' => 400));
    // }

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

        $allow = false;
        foreach ($available as $slot) {
            if ($slot['id'] == $booking_resource) {
                $allow = true;
                break;
            }
        }

        // Check if its available
        if (!$allow) {
            $confirmation_message = 'Sorry, this resource is not available on this date.';
            return new WP_Rest_Response(array('success' => false, 'message' => $confirmation_message), 200);
        }

        $resource = $resourcesClass->get_resources($booking_resource);

        [$resource_output, $addon_output, $total, $discount_used] = $pricingClass->get_price_after_discount($booking_discount, $booking_date_from, $booking_date_to, $resource, $selectedAddons, $booking_adults, $booking_children);
        
        $booking_discount_used = array();
        foreach ($discount_used as $discount) {
            $booking_discount_used[] = $discount['discount_name'];
            $pricingClass->use_discount($discount);
        }

        $booking_price_total = $total['total_after_final_discounted'];

        [$booking_header_id, $booking_number] = $bookingClass->add_booking_header($booking_date_from, $booking_date_to, $booking_resource, $booking_notes, 'From Website', 'N', json_encode($booking_discount_used), $booking_price_total, $booking_adults, $booking_children, $booking_user, $booking_email, $booking_phone);

        // Add booking for selected addon with charge once
        foreach ($selectedAddons as $addon) {
            if ($addon['addon_perday'] == 'N') {
                $bookingClass->add_booking_addon($booking_header_id, $booking_date_from, $addon['id'], 'N', $booking_discount);
            }
        }

        $nights = $bookingClass->get_nights($booking_date_from, $booking_date_to);

        for ($i = 0; $i < $nights; $i++) {
            $booking_date = date('Y-m-d', strtotime("$booking_date_from + $i days"));           
            $bookingClass->add_booking($booking_header_id, $booking_date, $booking_resource, 'N');
            
            // Add booking for selected addon with charge per day
            foreach ($selectedAddons as $addon) {
                if ($addon['addon_perday'] == 'Y') {
                    $bookingClass->add_booking_addon($booking_header_id, $booking_date, $addon['id'], 'N', $booking_discount);
                }
            }
        }


    // Remove unneeded data from paramaters
    unset($params['_wpnonce']);
    unset($params['_wp_http_referer']);

    $confirmation_message = 'Successfully add booking';

    $redirect_url = home_url('/payment/?booking_number=' . $booking_number);

    return new WP_Rest_Response(array('success' => true, 'redirect_url' => $redirect_url, 'booking_number' => $booking_number, 'message' => $confirmation_message), 200);
}

// Payment

function show_payment_page() {
    require_once (BI_PLUGIN_PATH . '/includes/booking/public/templates/payment-page.php');

    // Payment Option
    bankTransferWhatsapp();

}
