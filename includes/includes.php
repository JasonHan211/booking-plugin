<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;  

// Compulsory files
require_once (BI_PLUGIN_PATH . '/includes/utilities/utilities.php');

// Option pages
require_once (BI_PLUGIN_PATH . '/includes/option-page.php');

// Resouces
require_once (BI_PLUGIN_PATH . '/includes/resources/resources.php');

//Booking
require_once (BI_PLUGIN_PATH . '/includes/booking/booking.php');

// Contact Form
if (file_exists(BI_PLUGIN_PATH . '/includes/contact-form/contact-form.php')) { 
    require_once (BI_PLUGIN_PATH . '/includes/contact-form/contact-form.php');
}
