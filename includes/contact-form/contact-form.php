<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;


add_shortcode('contact', 'show_contact_form');
add_action('rest_api_init', 'create_rest_endpoint');
add_action('init', 'create_submissions_page');
add_action('add_meta_boxes', 'create_meta_box');
add_filter('manage_submission_posts_columns', 'custom_submission_columns');
add_action('manage_submission_posts_custom_column', 'fill_submission_columns', 10, 2);
add_action('admin_init', 'setup_search');


function setup_search() {

      // Only apply filter to submissions page

      global $typenow;

      if ($typenow === 'submission') {

            add_filter('posts_search', 'submission_search_override', 10, 2);
      }
}

function submission_search_override($search, $query) {

      // Override the submissions page search to include custom meta data

      global $wpdb;

      if ($query->is_main_query() && !empty($query->query['s'])) {
            $sql    = "
              or exists (
                  select * from {$wpdb->postmeta} where post_id={$wpdb->posts}.ID
                  and meta_key in ('name','email','phone')
                  and meta_value like %s
              )
          ";
            $like   = '%' . $wpdb->esc_like($query->query['s']) . '%';
            $search = preg_replace(
                  "#\({$wpdb->posts}.post_title LIKE [^)]+\)\K#",
                  $wpdb->prepare($sql, $like),
                  $search
            );
      }

      return $search;
}

function fill_submission_columns($column, $post_id) {
      // Return meta data for individual posts on table

      switch ($column) {

            case 'name':
                  echo esc_html(get_post_meta($post_id, 'name', true));
                  break;

            case 'email':
                  echo esc_html(get_post_meta($post_id, 'email', true));
                  break;

            case 'phone':
                  echo esc_html(get_post_meta($post_id, 'phone', true));
                  break;

            case 'message':
                  echo esc_html(get_post_meta($post_id, 'message', true));
                  break;
      }
}

function custom_submission_columns($columns) {
      // Edit the columns for the submission table

      $columns = array(

            'cb' => $columns['cb'],
            'name' => __('Name', 'contact-plugin'),
            'email' => __('Email', 'contact-plugin'),
            'phone' => __('Phone', 'contact-plugin'),
            'message' => __('Message', 'contact-plugin'),
            'date' => 'Date',

      );

      return $columns;
}

function create_meta_box() {
      // Create custom meta box to display submission

      add_meta_box('custom_contact_form', 'Submission', 'display_submission', 'submission');
}

function display_submission() {
      // Display individual submission data on it's page

      // $postmetas = get_post_meta( get_the_ID() );

      // echo '<ul>';

      // foreach($postmetas as $key => $value)
      // {

      //       echo '<li><strong>' . $key . ':</strong> ' . $value[0] . '</li>';

      // }

      // echo '</ul>';


      echo '<ul>';

      echo '<li><strong>Name:</strong><br /> ' . esc_html(get_post_meta(get_the_ID(), 'name', true)) . '</li>';
      echo '<li><strong>Email:</strong><br /> ' . esc_html(get_post_meta(get_the_ID(), 'email', true)) . '</li>';
      echo '<li><strong>Phone:</strong><br /> ' . esc_html(get_post_meta(get_the_ID(), 'phone', true)) . '</li>';
      echo '<li><strong>Message:</strong><br /> ' . esc_html(get_post_meta(get_the_ID(), 'message', true)) . '</li>';

      echo '</ul>';
}

function create_submissions_page() {

      // Create the submissions post type to store form submissions

      $args = [

            'public' => true,
            'has_archive' => true,
            'menu_position' => 3,
            'publicly_queryable' => false,
            'labels' => [

                  'name' => 'Submissions',
                  'singular_name' => 'Submission',
                  'edit_item' => 'View Submission'

            ],
            'supports' => false,
            'capability_type' => 'post',
            'capabilities' => array(
                  'create_posts' => false,
            ),
            'map_meta_cap' => true
      ];

      register_post_type('submission', $args);
}

function show_contact_form() {
      include BI_PLUGIN_PATH . 'includes/contact-form/templates/contact-form.php';
}

function create_rest_endpoint() {
      register_rest_route('v1/contact-form', 'submit', array(
            'methods' => 'POST',
            'callback' => 'contact_form_callback'
      ));
}

function contact_form_callback($data) {
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
      $body = 'Name: ' . $name . "<br>";
      $body .= 'Phone: ' . $phone . "<br>";
      $body .= 'Email: ' . $email . "<br>";
      $body .= 'Message: ' . $message . "<br>";

      // Remove unneeded data from paramaters
      unset($params['_wpnonce']);
      unset($params['_wp_http_referer']);

      // Send the email message
      $headers = [];

      $admin_email = get_bloginfo('admin_email');
      $admin_name = get_bloginfo('name');

      // Set recipient email
      $recipient_email = get_option('contact_plugin_recipients');

      if (!$recipient_email) {
            // Make all lower case and trim out white space
            $recipient_email = strtolower(trim($recipient_email));
      } else {

            // Set admin email as recipient email if no option has been set
            $recipient_email = $admin_email;
      }


      $headers[] = "From: {$admin_name} <{$admin_email}>";
      $headers[] = "Reply-to: {$name} <{$email}>";
      $headers[] = "Content-Type: text/html";

      $subject = "New enquiry from {$name}";

      $message = '';
      $message = "<h1>Message has been sent from {$name}</h1>";


      $postarr = [

            'post_title' => $params['name'],
            'post_type' => 'submission',
            'post_status' => 'publish'

      ];

      $post_id = wp_insert_post($postarr);

      // Loop through each field posted and sanitize it
      foreach ($params as $label => $value) {

            switch ($label) {

                  case 'message':

                        $value = sanitize_textarea_field($value);
                        break;

                  case 'email':

                        $value = sanitize_email($value);
                        break;

                  default:

                        $value = sanitize_text_field($value);
            }

            add_post_meta($post_id, sanitize_text_field($label), $value);

            $message .= '<strong>' . sanitize_text_field(ucfirst($label)) . ':</strong> ' . $value . '<br />';
      }


      wp_mail($recipient_email, $subject, $message, $headers);

      $confirmation_message = "The message was sent successfully!!";

      if (get_option('contact_plugin_message')) {

            $confirmation_message = get_option('contact_plugin_message');

            $confirmation_message = str_replace('{name}', $name, $confirmation_message);
      }

      return new WP_Rest_Response(array('message' => $confirmation_message), 200);
}