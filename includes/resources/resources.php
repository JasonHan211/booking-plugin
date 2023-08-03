<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

class BookedInResources {

    private $db;
    private $charset_collate;
    public $resources_table = 'bookedin_resources';
    public $table_name;

    public function __construct() {
        global $wpdb;
        $this->db = $wpdb;
        $this->charset_collate = $this->db->get_charset_collate();
        $this->table_name = $this->db->prefix . $this->resources_table;
    }

    public function add_resource($resource_name, $resource_price, $resource_description, $resource_activeFlag ) {

        $this->db->insert($this->table_name, array(
            'resource_name' => $resource_name,
            'resource_price' => $resource_price,
            'resource_description' => $resource_description,
            'activeFlag' => $resource_activeFlag
        ));

        return $this->db->insert_id;
    }

    public function update_resource($resource_id, $resource_name, $resource_price, $resource_description, $resource_activeFlag ) {

        $this->db->update($this->table_name, array(
            'resource_name' => $resource_name,
            'resource_price' => $resource_price,
            'resource_description' => $resource_description,
            'activeFlag' => $resource_activeFlag
        ), array('id' => $resource_id));

    }

    public function delete_resource($resource_id) {

        $this->db->delete($this->table_name, array('id' => $resource_id));

    }

    public function get_resource($resource_id = null) {

        if ($resource_id === null) {
            $resource = $this->db->get_results("SELECT * FROM $this->table_name", ARRAY_A);
            
            return $resource;
        }
        
        $resource = $this->db->get_row("SELECT * FROM $this->table_name WHERE id = $resource_id", ARRAY_A);

        return $resource;
    }

    public function createDB() {

        $sql = "CREATE TABLE IF NOT EXISTS $this->table_name (
            id INT NOT NULL AUTO_INCREMENT,
            resource_name VARCHAR(255) NOT NULL,
            resource_price VARCHAR(255) NOT NULL,
            resource_description TEXT,
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

    public function resources_activate(){
        
        $this->createDB();

    }

    public function resources_deactivate(){
        
        $this->deleteDB();

    }
}



