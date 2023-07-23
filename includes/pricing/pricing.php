<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

class BookedInpricings {

    public function pricings_activate(){
        global $wpdb;
        $table_name = $wpdb->prefix . 'bookedin_pricings';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id INT NOT NULL AUTO_INCREMENT,
            pricing_name VARCHAR(255) NOT NULL,
            pricing_description TEXT,
            PRIMARY KEY (id)
        ) $charset_collate;";

        dbDelta($sql);
    }

    public function pricings_deactivate(){
        global $wpdb;
        $table_name = $wpdb->prefix . 'bookedin_pricings';
        $wpdb->query("DROP TABLE IF EXISTS $table_name");
    }
}

if (class_exists('BookedInpricings')) {
    $pricings = new BookedInpricings();
    register_activation_hook(BI_FILE, array($pricings,'pricings_activate'));
    // register_deactivation_hook(BI_FILE, array($pricings,'pricings_deactivate'));    
}

