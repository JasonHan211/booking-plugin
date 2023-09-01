<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

require_once (BI_PLUGIN_PATH . '/includes/addons/addons.php');
require_once (BI_PLUGIN_PATH . '/includes/booking/booking.php');

class BookedInpricings {

    private $db;
    private $charset_collate;
    public $pricing_table = 'bookedin_pricings';
    public $discount_table = 'bookedin_discounts';
    public $table_name;
    public $discount_table_name;

    public function __construct() {
        global $wpdb;
        $this->db = $wpdb;
        $this->charset_collate = $this->db->get_charset_collate();
        $this->table_name = $this->db->prefix . $this->pricing_table;
        $this->discount_table_name = $this->db->prefix . $this->discount_table;
    }

    public function calculatePrice($pricing_id, $adult=0, $children=0) {

        $total_price = 0;
        $pricing = $this->get_pricings($pricing_id);

        $pricing_structure = json_decode($pricing['pricing_structure'], true);

        if (sizeof($pricing_structure[0]) == 1 || sizeof($pricing_structure) == 1) {
            $total_price = $pricing_structure[0][0];
        } else {
            $adult = (int)$adult;
            $children = (int)$children;
    
            $total_price = $pricing_structure[$adult][$children];
        }

        return $total_price;
    }

    public function add_pricing($pricing_name, $pricing_description, $pricing_structure, $pricing_active = 'Y') {

        $this->db->insert($this->table_name, array(
            'pricing_name' => $pricing_name,
            'pricing_description' => $pricing_description,
            'pricing_structure' => $pricing_structure,
            'pricing_active' => $pricing_active
        ));

        return $this->db->insert_id;
    }

    public function update_pricing($pricing_id, $pricing_name, $pricing_description, $pricing_structure, $pricing_active) {

        $this->db->update($this->table_name, array(
            'pricing_name' => $pricing_name,
            'pricing_description' => $pricing_description,
            'pricing_structure' => $pricing_structure,
            'pricing_active' => $pricing_active
        ), array('id' => $pricing_id));

    }

    public function delete_pricing($pricing_id) {

        $this->db->delete($this->table_name, array('id' => $pricing_id));

    }

    public function get_pricings($pricing_id = null) {

        if ($pricing_id === null) {

            $pricing = $this->db->get_results("SELECT * FROM $this->table_name", ARRAY_A);
            echo $this->db->last_error;
            return $pricing;

        }

        $pricing = $this->db->get_row("SELECT * FROM $this->table_name WHERE id = $pricing_id", ARRAY_A);
        echo $this->db->last_error;
        return $pricing;

    }

    public function add_discount($discount_name, $discount_description, $discount_code, $discount_quantity, $discount_type, $discount_amount, $discount_start_date, $discount_end_date, $discount_on_type, $discount_on_id, $discount_condition, $discount_condition_start, $discount_condition_end, $discount_auto_apply, $discount_active) {

        $this->db->insert($this->discount_table_name, array(
            'discount_name' => $discount_name,
            'discount_description' => $discount_description,
            'discount_code' => $discount_code,
            'discount_quantity' => $discount_quantity,
            'discount_type' => $discount_type,
            'discount_amount' => $discount_amount,
            'discount_start_date' => $discount_start_date,
            'discount_end_date' => $discount_end_date,
            'discount_on_type' => $discount_on_type,
            'discount_on_id' => $discount_on_id,
            'discount_condition' => $discount_condition,
            'discount_condition_start' => $discount_condition_start,
            'discount_condition_end' => $discount_condition_end,
            'discount_auto_apply' => $discount_auto_apply,
            'discount_active' => $discount_active
        ));
        echo $this->db->last_error;
        return $this->db->insert_id;

    }

    public function get_discounts($discount_id = null) {

        if ($discount_id === null) {

            $discount = $this->db->get_results("SELECT * FROM $this->discount_table_name", ARRAY_A);
            echo $this->db->last_error;
            return $discount;

        }

        $discount = $this->db->get_row("SELECT * FROM $this->discount_table_name WHERE id = $discount_id", ARRAY_A);
        echo $this->db->last_error;
        return $discount;

    }

    public function update_discount($discount_id, $discount_name, $discount_description, $discount_code, $discount_quantity, $discount_type, $discount_amount, $discount_start_date, $discount_end_date, $discount_on_type, $discount_on_id, $discount_condition, $discount_auto_apply, $discount_active) {

        $this->db->update($this->discount_table_name, array(
            'discount_name' => $discount_name,
            'discount_description' => $discount_description,
            'discount_code' => $discount_code,
            'discount_quantity' => $discount_quantity,
            'discount_type' => $discount_type,
            'discount_amount' => $discount_amount,
            'discount_start_date' => $discount_start_date,
            'discount_end_date' => $discount_end_date,
            'discount_on_type' => $discount_on_type,
            'discount_on_id' => $discount_on_id,
            'discount_condition' => $discount_condition,
            'discount_auto_apply' => $discount_auto_apply,
            'discount_active' => $discount_active
        ), array('id' => $discount_id));

    }

    public function delete_discount($discount_id) {

        $this->db->delete($this->discount_table_name, array('id' => $discount_id));

    }

    public function createPricingDB() {
        $sql = "CREATE TABLE IF NOT EXISTS $this->table_name (
            id INT NOT NULL AUTO_INCREMENT,
            pricing_name VARCHAR(255) NOT NULL,
            pricing_description TEXT,
            pricing_structure TEXT,
            pricing_active CHAR(1) NOT NULL DEFAULT 'N',
            PRIMARY KEY (id)
        ) $this->charset_collate;";

        dbDelta($sql);
    }

    public function createDiscoundDB() {
        $sql = "CREATE TABLE IF NOT EXISTS $this->discount_table_name (
            id INT NOT NULL AUTO_INCREMENT,
            discount_name VARCHAR(255) NOT NULL,
            discount_description TEXT,
            discount_code VARCHAR(255) NOT NULL,
            discount_quantity INT,
            discount_type VARCHAR(255) NOT NULL,  -- percentage or amount,     -- percentage or amount
            discount_on_type VARCHAR(255) NOT NULL,
            discount_on_id VARCHAR(255) NOT NULL,      
            discount_amount VARCHAR(255) NOT NULL,
            discount_start_date VARCHAR(255),
            discount_end_date VARCHAR(255),
            discount_condition VARCHAR(255) NOT NULL,
            discount_condition_start DATE,
            discount_condition_end DATE,
            discount_auto_apply CHAR(1) NOT NULL DEFAULT 'N',
            discount_active CHAR(1) NOT NULL DEFAULT 'N',
            PRIMARY KEY (id)
        ) $this->charset_collate;";

        dbDelta($sql);
    }

    public function deleteDB($table_name) {

        $this->db->query("DROP TABLE IF EXISTS $table_name");

    }

    public function pricings_activate(){
        
        $this->createPricingDB();
        $this->createDiscoundDB();

    }

    public function pricings_deactivate(){
        
        // $this->deleteDB($this->table_name);
        $this->deleteDB($this->discount_table_name);

    }
}

// REST API ENDPOINTS
add_action('rest_api_init', 'register_add_pricing');

function register_add_pricing() {
    register_rest_route('v1/pricing', 'add_pricing', array(
          'methods' => 'POST',
          'callback' => 'add_pricing_callback'
    ));
} 

function add_pricing_callback($request) {

    $pricing_name = $request->get_param('pricing_name');
    $pricing_description = $request->get_param('pricing_description');
    $pricing_structure = $request->get_param('pricing_structure');
    $pricing_active = $request->get_param('pricing_active');

    $pricing = new BookedInpricings();
    $last_id = $pricing->add_pricing($pricing_name, $pricing_description, $pricing_structure, $pricing_active);

    return new WP_REST_Response(array('availables'=>$last_id,'message'=>'Success'), 200);
}

// REST API ENDPOINTS
add_action('rest_api_init', 'register_update_pricing');

function register_update_pricing() {
    register_rest_route('v1/pricing', 'update_pricing', array(
          'methods' => 'POST',
          'callback' => 'update_pricing_callback'
    ));
} 

function update_pricing_callback($request) {

    $pricing_id = $request->get_param('pricing_id');
    $pricing_name = $request->get_param('pricing_name');
    $pricing_description = $request->get_param('pricing_description');
    $pricing_structure = $request->get_param('pricing_structure');
    $pricing_active = $request->get_param('pricing_active');

    $pricing = new BookedInpricings();
    $last_id = $pricing->update_pricing($pricing_id, $pricing_name, $pricing_description, $pricing_structure, $pricing_active);

    return new WP_REST_Response(array('availables'=>$last_id,'message'=>'Success'), 200);
}




