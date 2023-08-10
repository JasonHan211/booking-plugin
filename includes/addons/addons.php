<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

class BookedInAddons {

    private $db;
    private $charset_collate;
    public $addons_table = 'bookedin_addons';
    public $table_name;

    public function __construct() {
        global $wpdb;
        $this->db = $wpdb;
        $this->charset_collate = $this->db->get_charset_collate();
        $this->table_name = $this->db->prefix . $this->addons_table;
    }

    public function add_addon($addon_name, $addon_price, $addon_description, $addon_activeFlag ) {

        $this->db->insert($this->table_name, array(
            'addon_name' => $addon_name,
            'addon_price' => $addon_price,
            'addon_description' => $addon_description,
            'activeFlag' => $addon_activeFlag
        ));

        return $this->db->insert_id;
    }

    public function update_addon($addon_id, $addon_name, $addon_price, $addon_description, $addon_activeFlag ) {

        $this->db->update($this->table_name, array(
            'addon_name' => $addon_name,
            'addon_price' => $addon_price,
            'addon_description' => $addon_description,
            'activeFlag' => $addon_activeFlag
        ), array('id' => $addon_id));

    }

    public function delete_addon($addon_id) {

        $this->db->delete($this->table_name, array('id' => $addon_id));

    }

    public function get_addons($addon_id = null, $activeFlag = 'A') {

        if ($addon_id === null) {

            if ($activeFlag != 'A') {
                
                $addon = $this->db->get_results("SELECT * FROM $this->table_name WHERE activeFlag = '$activeFlag'", ARRAY_A);
                echo $this->db->last_error;
                return $addon;
            }

            $addon = $this->db->get_results("SELECT * FROM $this->table_name", ARRAY_A);
            
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



