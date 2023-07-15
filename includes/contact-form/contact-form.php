<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
      exit;
}

add_shortcode('contact', 'show_contact_form');
add_action('rest_api_init', 'create_rest_endpoint');

function show_contact_form()
{
      include PLUGIN_PATH . 'includes/contact-form/templates/contact-form.php';
}

function create_rest_endpoint()
{
      register_rest_route('v1/contact-form', 'submit', array(
            'methods' => 'POST',
            'callback' => 'contact_form_callback'
      ));
}

function contact_form_callback($data)
{
      $params = $data->get_params();

      if (!wp_verify_nonce($params['_wpnonce'], 'wp_rest')) {
            return new WP_Error('invalid_nonce', 'Invalid nonce', array('status' => 401));
      }

      $name = sanitize_text_field($params['name']);
      $phone = sanitize_text_field($params['phone']);
      $email = sanitize_email($params['email']);
      $message = sanitize_textarea_field($params['message']);

      if (empty($name) || empty($phone) || empty($email) || empty($message)) {
            return new WP_Error('fields_required', 'All fields are required', array('status' => 400));
      }

      $to = get_option('admin_email');
      $subject = 'New enquiry from ' . $name;
      $body = 'Name: ' . $name . "\r\n";
      $body .= 'Phone: ' . $phone . "\r\n";
      $body .= 'Email: ' . $email . "\r\n";
      $body .= 'Message: ' . $message . "\r\n";

      $headers = array('Content-Type: text/html; charset=UTF-8');

      $sent = true; // wp_mail($to, $subject, $body, $headers);

      if ($sent) {
            return array('message' => "Thanks for your enquiry. We will get back to you shortly.{$body}");
      } else {
            return new WP_Error('email_failed', 'Your message could not be sent', array('status' => 500));
      }
}