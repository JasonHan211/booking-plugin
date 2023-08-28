<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

require_once (BI_PLUGIN_PATH . '/includes/pricings/pricing.php');

class BookedInAddons {

    private $db;
    private $charset_collate;
    private $pricingClass;
    public $addons_table = 'bookedin_addons';
    public $table_name;

    public function __construct() {
        global $wpdb;
        $this->db = $wpdb;
        $this->charset_collate = $this->db->get_charset_collate();
        $this->pricingClass = new BookedInPricings();
        $this->table_name = $this->db->prefix . $this->addons_table;
    }

    public function add_addon($addon_name, $addon_price, $addon_description, $addon_perday, $addon_activeFlag ) {

        $this->db->insert($this->table_name, array(
            'addon_name' => $addon_name,
            'addon_price' => $addon_price,
            'addon_description' => $addon_description,
            'addon_perday' => $addon_perday,
            'activeFlag' => $addon_activeFlag
        ));

        return $this->db->insert_id;
    }

    public function update_addon($addon_id, $addon_name, $addon_price, $addon_description, $addon_perday, $addon_activeFlag ) {

        $this->db->update($this->table_name, array(
            'addon_name' => $addon_name,
            'addon_price' => $addon_price,
            'addon_description' => $addon_description,
            'addon_perday' => $addon_perday,
            'activeFlag' => $addon_activeFlag
        ), array('id' => $addon_id));

    }

    public function delete_addon($addon_id) {

        $this->db->delete($this->table_name, array('id' => $addon_id));

    }

    public function get_addons($addon_id = null, $activeFlag = 'A') {

        $pricingTable = $this->pricingClass->table_name;

        if ($addon_id === null) {

            if ($activeFlag != 'A') {
                
                $addon = $this->db->get_results(
                    "SELECT 
                    atn.id as 'id', 
                    atn.addon_name as 'addon_name', 
                    atn.addon_price as 'addon_price', 
                    atn.addon_description as 'addon_description', 
                    atn.addon_perday as 'addon_perday', 
                    atn.activeFlag as 'activeFlag', 
                    ptn.pricing_name as 'pricing_name',
                    ptn.pricing_structure as 'price_structure'
                    FROM $this->table_name atn 
                    LEFT JOIN $pricingTable ptn ON ptn.id = atn.addon_price 
                    WHERE activeFlag = '$activeFlag'"
                    , ARRAY_A);
                return $addon;
            }

            $addon = $this->db->get_results(
                "SELECT 
                atn.id as 'id', 
                atn.addon_name as 'addon_name', 
                atn.addon_price as 'addon_price', 
                atn.addon_description as 'addon_description', 
                atn.addon_perday as 'addon_perday', 
                atn.activeFlag as 'activeFlag', 
                ptn.pricing_name as 'pricing_name',
                ptn.pricing_structure as 'price_structure'
                FROM $this->table_name atn 
                LEFT JOIN $pricingTable ptn ON ptn.id = atn.addon_price"
                , ARRAY_A);
            return $addon;
        }
        
        $addon = $this->db->get_row("SELECT * FROM $this->table_name WHERE id = $addon_id", ARRAY_A);

        return $addon;
    }

    public function createDB() {

        $sql = "CREATE TABLE IF NOT EXISTS $this->table_name (
            id INT NOT NULL AUTO_INCREMENT,
            addon_name VARCHAR(255) NOT NULL,
            addon_price VARCHAR(255) NOT NULL,
            addon_description TEXT,
            addon_perday CHAR(1) NOT NULL DEFAULT 'N',
            activeFlag CHAR(1) NOT NULL DEFAULT 'Y',
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            edited_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $this->charset_collate;";

        dbDelta($sql);

    }

    public function deleteDB() {

        $this->db->query("DROP TABLE IF EXISTS $this->table_name");

    }

    public function addons_activate(){
        
        $this->createDB();

    }

    public function addons_deactivate(){
        
        $this->deleteDB();
        
    }
}



