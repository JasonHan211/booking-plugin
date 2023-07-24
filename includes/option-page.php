<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

class BookedInMenuPage {

    public function initialize() {
        
        // Main Menu
            //Booking
            require_once (BI_PLUGIN_PATH . '/includes/booking/booking.php');
            add_action('admin_menu', array($this, 'bookedin_add_menu_page'));
            add_action( 'admin_menu', array($this,'bookedin_booking_edit_submenu'));

        // Submenu
            // Resources
            require_once (BI_PLUGIN_PATH . '/includes/resources/resources.php');
            add_action( 'admin_menu', array($this,'bookedin_resources_submenu'));
            add_action( 'admin_menu', array($this,'bookedin_resources_edit_submenu'));

            // Pricing
            // require_once (BI_PLUGIN_PATH . '/includes/pricing/pricing.php');
            // add_action( 'admin_menu', array($this,'bookedin_pricing_submenu'));
            // add_action( 'admin_menu', array($this,'bookedin_pricing_edit_submenu'));
        
        // Optional
            // Contact Form
            if (file_exists(BI_PLUGIN_PATH . '/includes/contact-form/contact-form.php')) { 
                require_once (BI_PLUGIN_PATH . '/includes/contact-form/contact-form.php');
                add_action( 'admin_menu', array($this,'bookedin_contact_form_submenu'));
            }
                
        // Settings
            // add_action( 'admin_menu', array($this,'bookedin_setting_submenu'));
    
    }

    // Main Menu (Bookings)
    public function bookedin_add_menu_page() {

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

    public function bookedin_booking_edit_submenu() {

        require_once (BI_PLUGIN_PATH . '/includes/booking/edit-menu.php');
        add_submenu_page(
            null,                         
            'Edit Booking',         
            'Edit Booking',               
            'manage_options',              
            'bookedin_booking_edit',        
            'booking_edit_page'    
        );
    }

    // Submenu (Resources)
    public function bookedin_resources_submenu() {
    
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

    // Submenu (Resources Edit Page)
    public function bookedin_resources_edit_submenu() {

        require_once (BI_PLUGIN_PATH . '/includes/resources/edit-menu.php');
        add_submenu_page(
            null,                         
            'Edit Resource',         
            'Edit Resource',               
            'manage_options',              
            'bookedin_resources_edit',        
            'resources_edit_page'    
        );
    }

    // Submenu (pricing)
    public function bookedin_pricing_submenu() {

        require_once (BI_PLUGIN_PATH . '/includes/pricing/menu.php');
        add_submenu_page(
            'bookedin_main_menu',       
            'Pricing',       
            'Pricing',           
            'manage_options',         
            'bookedin_pricing_submenu',       
            'bookedin_pricing_submenu_page',  
            3
        );
    }

    // Submenu (pricing Edit Page)
    public function bookedin_pricing_edit_submenu() {

        require_once (BI_PLUGIN_PATH . '/includes/pricing/edit-menu.php');
        add_submenu_page(
            null,                         
            'Edit Pricing',         
            'Edit Pricing',               
            'manage_options',              
            'bookedin_pricing_edit',        
            'pricing_edit_page'    
        );
    }

    // Submenu (Contact Form)
    public function bookedin_contact_form_submenu() {

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
    public function bookedin_setting_submenu() {

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

$menuPage = new BookedInMenuPage();
$menuPage->initialize();










