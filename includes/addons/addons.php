<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

class BookedInAddons {

    public function addons_activate(){
        global $wpdb;
        $table_name = $wpdb->prefix . 'bookedin_addons';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id INT NOT NULL AUTO_INCREMENT,
            addon_name VARCHAR(255) NOT NULL,
            addon_price VARCHAR(255) NOT NULL,
            addon_description TEXT,
            activeFlag char(1) NOT NULL DEFAULT 'Y',
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            edited_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";

        dbDelta($sql);
    }

    public function addons_deactivate(){
        global $wpdb;
        $table_name = $wpdb->prefix . 'bookedin_addons';
        $wpdb->query("DROP TABLE IF EXISTS $table_name");
    }
}

if (class_exists('BookedInAddons')) {
    $addons = new BookedInAddons();
    register_activation_hook(BI_FILE, array($addons,'addons_activate'));
    register_deactivation_hook(BI_FILE, array($addons,'addons_deactivate'));    
}

