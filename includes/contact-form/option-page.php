<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;


use Carbon_Fields\Container;
use Carbon_Fields\Field;

add_action('after_setup_theme', 'load_carbon_fields');
add_action('carbon_fields_register_fields', 'init_contact_form');

// Load Carbon Fields
function load_carbon_fields()
{
      \Carbon_Fields\Carbon_Fields::boot();
}


// Contact Form
function init_contact_form()
{
      Container::make('theme_options', __('Contact Form'))
            ->set_page_parent('bookedin_main_slug')
            ->add_fields(array(

            Field::make('checkbox', 'contact_plugin_active', __('Active')),

            Field::make('text', 'contact_plugin_recipients', __('Recipient Email'))->set_attribute('placeholder', 'eg. your@email.com')->set_help_text('The email that the form is submitted to'),

            Field::make('textarea', 'contact_plugin_message', __('Confirmation Message'))->set_attribute('placeholder', 'Enter confirmation message')->set_help_text('Type the message you want the submitted to receive'),

        ));

}

function get_plugin_options($name)
{
      return carbon_get_theme_option( $name );
}

?>