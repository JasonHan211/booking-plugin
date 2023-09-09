<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

add_shortcode('booking-form', 'show_booking_form');

function show_booking_form() {

    require_once (BI_PLUGIN_PATH . '/includes/booking/public/templates/booking-calendar.php');
    require_once (BI_PLUGIN_PATH . '/includes/booking/public/templates/new-booking-form.php');

    bookedInHeader();
    newBookingForm();
}