<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

class BookedInResources {

    public function resources_activate(){
        global $wpdb;
        $table_name = $wpdb->prefix . 'bookedin_resources';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id INT NOT NULL AUTO_INCREMENT,
            resource_name VARCHAR(255) NOT NULL,
            resource_price VARCHAR(255) NOT NULL,
            resource_description TEXT,
            activeFlag char(1) NOT NULL DEFAULT 'Y',
            PRIMARY KEY (id)
        ) $charset_collate;";

        dbDelta($sql);
    }

    public function resources_deactivate(){
        global $wpdb;
        $table_name = $wpdb->prefix . 'bookedin_resources';
        $wpdb->query("DROP TABLE IF EXISTS $table_name");
    }
}

if (class_exists('BookedInResources')) {
    $resources = new BookedInResources();
    register_activation_hook(BI_FILE, array($resources,'resources_activate'));
    // register_deactivation_hook(BI_FILE, array($resources,'resources_deactivate'));    
}

