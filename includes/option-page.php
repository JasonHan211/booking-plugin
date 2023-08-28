<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

class BookedInMenuPage {

    public function initialize() {
        
        // Main Menu
            //Booking
            require_once (BI_PLUGIN_PATH . '/includes/booking/booking.php');

            if (class_exists('BookedInBookings')) {
                $bookings = new BookedInBookings();
                register_activation_hook(BI_FILE, array($bookings,'activate'));
                // register_deactivation_hook(BI_FILE, array($bookings,'deactivate')); 
                
                add_action('admin_menu', array($this, 'bookedin_add_menu_page'));
                add_action( 'admin_menu', array($this,'bookedin_booking_edit_submenu'));
            
            }


        // Submenu
            // Pricing
            require_once (BI_PLUGIN_PATH . '/includes/pricings/pricing.php');

            if (class_exists('BookedInPricings')) {
                $pricing = new BookedInPricings();
                register_activation_hook(BI_FILE, array($pricing,'pricings_activate'));
                register_deactivation_hook(BI_FILE, array($pricing,'pricings_deactivate'));    
            
                add_action( 'admin_menu', array($this,'bookedin_pricings_submenu'));
                add_action( 'admin_menu', array($this,'bookedin_pricings_edit_submenu'));
    
            }

            // Resources
            require_once (BI_PLUGIN_PATH . '/includes/resources/resources.php');

            if (class_exists('BookedInResources')) {
                $resources = new BookedInResources();
                register_activation_hook(BI_FILE, array($resources,'resources_activate'));
                // register_deactivation_hook(BI_FILE, array($resources,'resources_deactivate'));    
            
                add_action( 'admin_menu', array($this,'bookedin_resources_submenu'));
                add_action( 'admin_menu', array($this,'bookedin_resources_edit_submenu'));
    
            }
                    
            // Addons
            require_once (BI_PLUGIN_PATH . '/includes/addons/addons.php');

            if (class_exists('BookedInAddons')) {
                $addons = new BookedInAddons();
                register_activation_hook(BI_FILE, array($addons,'addons_activate'));
                // register_deactivation_hook(BI_FILE, array($addons,'addons_deactivate'));    
            
                add_action( 'admin_menu', array($this,'bookedin_addons_submenu'));
                add_action( 'admin_menu', array($this,'bookedin_addons_edit_submenu'));
    
            }
      

        
        // Optional
            // Contact Form
            if (file_exists(BI_PLUGIN_PATH . '/includes/contact-form/contact-form.php')) { 
                require_once (BI_PLUGIN_PATH . '/includes/contact-form/contact-form.php');
                add_action( 'admin_menu', array($this,'bookedin_contact_form_submenu'));
            }
                
        // Settings
            require_once (BI_PLUGIN_PATH . '/includes/settings/settings.php');
            $settings = new BookedInSettings();
            register_activation_hook(BI_FILE, array($settings,'settings_activate'));
            // register_deactivation_hook(BI_FILE, array($settings,'settings_deactivate'));
            add_action( 'admin_menu', array($this,'bookedin_setting_submenu'));
            add_action( 'admin_menu', array($this,'bookedin_setting_edit_submenu'));
    
    }

    // Main Menu (Bookings)
    public function bookedin_add_menu_page() {

        require_once (BI_PLUGIN_PATH . '/includes/booking/admin/menu.php');
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

        require_once (BI_PLUGIN_PATH . '/includes/booking/admin/edit-menu.php');
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
    
        require_once (BI_PLUGIN_PATH . '/includes/resources/admin/menu.php');
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

        require_once (BI_PLUGIN_PATH . '/includes/resources/admin/edit-menu.php');
        add_submenu_page(
            null,                         
            'Edit Resource',         
            'Edit Resource',               
            'manage_options',              
            'bookedin_resources_edit',        
            'resources_edit_page'    
        );
    }

    // Submenu (Addons)
    public function bookedin_addons_submenu() {

        require_once (BI_PLUGIN_PATH . '/includes/addons/admin/menu.php');
        add_submenu_page(
            'bookedin_main_menu',       
            'Addons',       
            'Addons',           
            'manage_options',         
            'bookedin_addons_submenu',       
            'bookedin_addons_submenu_page',  
            3
        );
    }

    // Submenu (addons Edit Page)
    public function bookedin_addons_edit_submenu() {

        require_once (BI_PLUGIN_PATH . '/includes/addons/admin/edit-menu.php');
        add_submenu_page(
            null,                         
            'Edit addon',         
            'Edit addon',               
            'manage_options',              
            'bookedin_addons_edit',        
            'addons_edit_page'    
        );
    }

    // Submenu (pricing)
    public function bookedin_pricings_submenu() {

        require_once (BI_PLUGIN_PATH . '/includes/pricings/admin/menu.php');
        add_submenu_page(
            'bookedin_main_menu',       
            'Pricing',       
            'Pricing',           
            'manage_options',         
            'bookedin_pricings_submenu',       
            'bookedin_pricings_submenu_page',  
            3
        );
    }

    // Submenu (pricing Edit Page)
    public function bookedin_pricings_edit_submenu() {

        require_once (BI_PLUGIN_PATH . '/includes/pricings/admin/edit-menu.php');
        add_submenu_page(
            null,                         
            'Edit Pricing',         
            'Edit Pricing',               
            'manage_options',              
            'bookedin_pricings_edit',        
            'pricings_edit_page'    
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

        require_once (BI_PLUGIN_PATH . '/includes/settings/admin/menu.php');
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

    // Submenu (Settings Edit Page)
    public function bookedin_setting_edit_submenu() {

        require_once (BI_PLUGIN_PATH . '/includes/settings/admin/edit-menu.php');
        add_submenu_page(
            null,                         
            'Edit Setting',         
            'Edit Setting',               
            'manage_options',              
            'bookedin_setting_edit',        
            'setting_edit_page'    
        );
    }

}

$menuPage = new BookedInMenuPage();
$menuPage->initialize();










