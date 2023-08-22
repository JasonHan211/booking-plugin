<?php

require_once (BI_PLUGIN_PATH . '/includes/pricings/pricing.php');

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

class BookedInResources {

    private $db;
    private $charset_collate;
    private $pricingClass;
    public $resources_table = 'bookedin_resources';
    public $table_name;

    public function __construct() {
        global $wpdb;
        $this->db = $wpdb;
        $this->charset_collate = $this->db->get_charset_collate();
        $this->pricingClass = new BookedInPricings();
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

    public function get_resources($resource_id = null, $activeFlag = 'A') {      

        $pricingTable = $this->pricingClass->table_name;

        if ($resource_id === null) {

            if ($activeFlag != 'A') {
                
                $resources = $this->db->get_results(
                    "SELECT
                    rtn.id as 'id',
                    rtn.resource_name as 'resource_name',
                    rtn.resource_price as 'resource_price',
                    rtn.resource_description as 'resource_description',
                    rtn.activeFlag as 'activeFlag',
                    ptn.pricing_name as 'pricing_name'
                    FROM $this->table_name rtn
                    LEFT JOIN $pricingTable ptn ON ptn.id = rtn.resource_price 
                    WHERE activeFlag = '$activeFlag'", ARRAY_A);
                echo $this->db->last_error;
                return $resources;
            }

            $resources = $this->db->get_results(
                "SELECT
                rtn.id as 'id',
                rtn.resource_name as 'resource_name',
                rtn.resource_price as 'resource_price',
                rtn.resource_description as 'resource_description',
                rtn.activeFlag as 'activeFlag',
                ptn.pricing_name as 'pricing_name'
                FROM $this->table_name rtn
                LEFT JOIN $pricingTable ptn ON ptn.id = rtn.resource_price", ARRAY_A);
            
            return $resources;
        }
        
        $resource = $this->db->get_row("SELECT * FROM $this->table_name WHERE id = $resource_id", ARRAY_A);

        return $resource;
    }

    public function get_total_resources() {

        $total_resources = $this->db->get_var("SELECT COUNT(*) FROM $this->table_name WHERE activeFlag = 'Y'");

        return $total_resources;

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



