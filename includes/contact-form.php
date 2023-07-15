<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
      exit;
}

add_shortcode('contact', 'show_contact_form');

function show_contact_form()
{
      include PLUGIN_PATH . '/includes/templates/contact-form.php';
}