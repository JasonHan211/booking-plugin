<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

class MenuPage {
    public function initialize() {
        
        // Main Menu
        add_action('admin_menu', array($this, 'bookedIn_add_menu_page'));

        // Submenu
        add_action( 'admin_menu', array($this,'bookedin_resources_add_submenu'));
        add_action( 'admin_menu', array($this,'bookedin_contact_form_add_submenu'));
        add_action( 'admin_menu', array($this,'bookedin_setting_add_submenu'));
    }

    // Main Menu (Bookings)
    public function bookedIn_add_menu_page() {

        require_once (BI_PLUGIN_PATH . '/includes/booking/menu.php');
        add_menu_page(
            'BookedIn',
            'BookedIn',
            'manage_options',
            'bookedin_main_menu',
            'my_booking_plugin_option_page',
            'dashicons-calendar', 
            2
        );
    }

    // Submenu (Resources)
    public function bookedin_resources_add_submenu() {
    
        require_once (BI_PLUGIN_PATH . '/includes/resources/menu.php');
        add_submenu_page(
            'bookedin_main_menu',       
            'Resources',       
            'Resources',           
            'manage_options',         
            'bookedin_resources_submenu',       
            'bookedin_resources_submenu_page',  
            3
        );
    }

    // Submenu (Contact Form)
    public function bookedin_contact_form_add_submenu() {

        require_once (BI_PLUGIN_PATH . '/includes/contact-form/menu.php');
        add_submenu_page(
            'bookedin_main_menu',       
            'Contact Form',         
            'Contact Form',            
            'manage_options',          
            'bookedin_contact_form_submenu',       
            'bookedin_contact_form_submenu_page',   
            5
        );
    }

    // Submenu (Settings)
    public function bookedin_setting_add_submenu() {

        require_once (BI_PLUGIN_PATH . '/includes/Settings/menu.php');
        add_submenu_page(
            'bookedin_main_menu',       
            'Settings',         
            'Settings',            
            'manage_options',          
            'bookedin_setting_submenu',       
            'bookedin_setting_submenu_page',   
            4
        );
    }

}

$menuPage = new MenuPage();
$menuPage->initialize();










