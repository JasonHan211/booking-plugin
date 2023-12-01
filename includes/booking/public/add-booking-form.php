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

$bookingClass = new BookedInBookings();

// Booking Page
function show_booking_form() {

    require_once (BI_PLUGIN_PATH . '/includes/booking/public/templates/booking-calendar.php');
    require_once (BI_PLUGIN_PATH . '/includes/booking/public/templates/new-booking-form.php');

    bookedInHeader();
    newBookingForm();
}

// Payment Page
function show_payment_page() {
    require_once (BI_PLUGIN_PATH . '/includes/booking/public/templates/payment-page.php');

    // Payment Option
    bankTransferWhatsapp();

}

// Create New Booking
function create_new_booking_rest_endpoint() {
    register_rest_route('v1/new_booking', 'submit', array(
          'methods' => 'POST',
          'callback' => 'new_booking_public_callback'
    ));
}

function new_booking_public_callback($request) {
    
    $bookingClass = new BookedInBookings();
    
    if (!wp_verify_nonce($request['_wpnonce'], 'wp_rest')) {
          return new WP_Error('invalid_nonce', 'Invalid nonce', array('status' => 401));
    }

    $start = $request->get_param('booking_date_from');
    $end = $request->get_param('booking_date_to');
    $bookings = json_decode($request->get_param('bookings'));
    $booking_notes = $request->get_param('booking_notes');
    $booking_description = $request->get_param('booking_description');
    $booking_name = $request->get_param('booking_name');
    $booking_email = $request->get_param('booking_email');
    $booking_phone = $request->get_param('booking_phone');
    $booking_discount = $request->get_param('booking_discount');
    $booking_price = $request->get_param('booking_price');
    $booking_paid = $request->get_param('booking_paid');
    $booking_deposit_refund = $request->get_param('booking_deposit_refund');
    
    [$booking_number,$total] = $bookingClass->new_booking(false, $start, $end, $bookings, $booking_notes, $booking_description, $booking_name, $booking_email, $booking_phone, $booking_discount, $booking_price, $booking_paid, $booking_deposit_refund);

    $confirmation_message = 'Successfully add booking';

    $redirect_url = home_url('/payment/?booking_number=' . $booking_number . '&amount=' . $total);

    return new WP_Rest_Response(array('success' => true, 'redirect_url' => $redirect_url, 'booking_number' => $booking_number, 'message' => $confirmation_message), 200);
};

