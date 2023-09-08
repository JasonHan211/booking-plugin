<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

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

    public function check_availability($discount, $date){
        
        // Check if discount date is within apply discount date
        $today = date('Y-m-d');
        if ($discount['discount_start_date'] > $today) {   
            return null;
        } else if ($discount['discount_end_date'] < $today) {
            $this->update_discount($discount['id'],$discount['discount_name'],$discount['discount_description'],$discount['discount_code'],$discount['discount_quantity'],$discount['discount_type'],$discount['discount_amount'],$discount['discount_start_date'],$discount['discount_end_date'],$discount['discount_on_type'],$discount['discount_on_id'],$discount['discount_condition'],$discount['discount_condition_start'],$discount['discount_condition_end'],$discount['discount_auto_apply'],'N');
            return null;
        }

        // Check discount condition
        $discount_condition = $discount['discount_condition'];
        $discount_condition_start = $discount['discount_condition_start'];
        $discount_condition_end = $discount['discount_condition_end'];
        
        if ($discount_condition_start > $date || $discount_condition_end < $date) return null;

        if ($discount_condition == 'None') {
            return $discount;
        } else if ($discount_condition == 'Off-Peak') {

            // To be added after having holiday table
            // Check if date is a weekday
            $date_obj = new DateTime($date);
            $day = $date_obj->format('l');
            if ($day == 'Friday' || $day == 'Saturday' || $day == 'Sunday') {
                return null;
            }

        } else if ($discount_condition == 'Weekdays') {

            // Check if date is a weekday
            $date_obj = new DateTime($date);
            $day = $date_obj->format('l');
            if ($day == 'Friday' || $day == 'Saturday' || $day == 'Sunday') {
                return null;
            }

        } else if ($discount_condition == 'Weekends') {

            // Check if date is a weekends
            $date_obj = new DateTime($date);
            $day = $date_obj->format('l');
            if ($day == 'Monday' || $day == 'Tuesday' || $day == 'Wednesday' || $day == 'Thursday') {
                return null;
            }

        }

        // Check discount quantity left
        if ($discount['discount_quantity'] == 0) {
            return null;
        } 

        return $discount;
    }

    public function get_price_after_discount($discount_code = null, $start, $end, $resource, $addons, $adults, $children) {

        // Condition string
        $condition = "((discount_on_type = 'Resources' AND (discount_on_id = 'All' OR discount_on_id = ".$resource['id']."))";
        $condition = $condition." OR discount_on_type = 'All'";
        $condition = $condition." OR (discount_on_type = 'Addon' AND discount_on_id = 'All')";

        // Resource Price
        $resource_price = $this->calculatePrice($resource['resource_price'], $adults, $children);
        $resource_discounted_price = $resource_price;
        
        // Addons Price
        $addonsObjs = array();
        foreach ($addons as $addon) {

            $addon_price = $this->calculatePrice($addon['addon_price'], $adults, $children);
            $condition = $condition." OR (discount_on_type = 'Addon' AND discount_on_id = ".$addon['id'].")";
            $addonsObjs[] = array('addon'=>$addon, 'addon_price'=>$addon_price, 'addon_discounted_price'=>$addon_price);

        }

        // Get all auto apply discount
        $discounts = $this->db->get_results(
            "SELECT * 
            FROM $this->discount_table_name 
            WHERE $condition)
            AND discount_auto_apply = 'Y'
            AND discount_active = 'Y'", ARRAY_A);
        echo $this->db->last_error;

        $discount_with_code = $this->db->get_results(
            "SELECT *
            FROM $this->discount_table_name
            WHERE discount_code = '$discount_code'
            AND discount_active = 'Y'", ARRAY_A);
        echo $this->db->last_error;

        $discounts = array_merge($discounts, $discount_with_code);

        $all_type_discount = array();
        $applied_discount = array();

        foreach ($discounts as $discount) {

            if ($discount['discount_on_type'] == 'Resources') {
                
                $amount_eligible = 0;
                $amount_not_eligible = 0;
                
                // Loop per day to check on each day
                $booking_date = date('Y-m-d', strtotime($start));
                
                while (strtotime($booking_date) < strtotime($end)) {
                    $discount = $this->check_availability($discount, $booking_date);

                    if ($discount !== null) {

                        $amount_eligible += $resource_discounted_price;

                        // If discount code not in array
                        if (!in_array($discount, $applied_discount)) {
                            $applied_discount[] = $discount;
                        }

                    } else {

                        $amount_not_eligible += $resource_discounted_price;

                    }
                    // To fix algorithm to calc discount
                    $booking_date = date('Y-m-d', strtotime($booking_date . ' +1 day'));
    
                }

                // Update discounted price
                if ($discount !== null) {
                    $resource_discounted_price = $this->apply_discount($discount, $amount_eligible);
                }
                // To fix bug
                $resource_discounted_price += $amount_not_eligible;
                
            
            } else if ($discount['discount_on_type'] == 'Addon') {

 
                foreach ($addonsObjs as $addon) {
                    
                    if ($addon['addon']['id'] != $discount['discount_on_id']) continue;

                    if ($addon['addon_perday'] == 'Y') {

                        $amount_eligible = 0;
                        $amount_not_eligible = 0;
                        
                        // Loop per day to check on each day
                        $booking_date = date('Y-m-d', strtotime($start));
                        while (strtotime($booking_date) < strtotime($end)) {

                            $discount = $this->check_availability($discount, $booking_date);

                            if ($discount !== null) {

                                $amount_eligible += $addon['addon_discounted_price'];
        
                                // If discount code not in array
                                if (!in_array($discount, $applied_discount)) {
                                    $applied_discount[] = $discount;
                                }
        
                            } else {
        
                                $amount_not_eligible += $addon['addon_discounted_price'];
        
                            }

                            $booking_date = date('Y-m-d', strtotime($booking_date . ' +1 day'));
                        }

                        // Update discounted price
                        if ($discount !== null) {
                            $addon['addon_discounted_price'] = $this->apply_discount($discount, $amount_eligible);
                        }
                        if ($amount_eligible !== 0) {
                            $addon['addon_discounted_price'] += $amount_not_eligible;
                        }

                    } else {

                        $discount = $this->check_availability($discount, $start);

                        if ($discount !== null) {

                            $addon['addon_discounted_price'] = $this->apply_discount($discount, $addon['addon_discounted_price']);
    
                            // If discount code not in array
                            if (!in_array($discount, $applied_discount)) {
                                $applied_discount[] = $discount;
                            }
    
                        }

                    }

                }


            } else if ($discount['discount_on_type'] == 'All') {
                $all_type_discount[] = $discount;
            }

        }

        // Handle All type discount
        $overall_discount = 0;
        if (!empty($all_type_discount)) {

            $discount = $this->check_availability($discount, $start);

            if ($discount !== null) {

                $temp = $resource_discounted_price;
                $resource_discounted_price = $this->apply_discount($discount, $resource_discounted_price);
                $overall_discount = $temp - $resource_discounted_price;

                // If discount code not in array
                if (!in_array($discount, $applied_discount)) {
                    $applied_discount[] = $discount;
                }

            }
        }

        $date1 = new DateTime($start);
        $date2 = new DateTime($end);
        $interval = $date1->diff($date2);

        $days = $interval->format('%a');
        $resource_total_price = $resource_price * $days;
        $resourceObj = array('resource'=>$resource, 'resource_price'=>$resource_total_price, 'resource_discounted_price'=>$resource_discounted_price, 'overall_discount'=>$overall_discount);
        $discount_used = $applied_discount;

        return [$resourceObj, $addonsObjs, $discount_used];

    }

    public function apply_discount($discount, $price) {

        $discount_type = $discount['discount_type'];
        $discount_amount = $discount['discount_amount'];

        if ($discount_type == 'Fixed') {

            $price = $price - $discount_amount;

        } else if ($discount_type == 'Percentage') {

            $price = $price - ($price * ($discount_amount / 100));

        }

        return round($price,2);

    }

    public function apply_auto_discount($type, $id, $price_id, $discount, $start, $end,  $booking_adult, $booking_children, $addon_perday='N') {

        // Get price for the product
        $original_price = $this->calculatePrice($price_id, $booking_adult, $booking_children);
        $discounted_price = 0;
        $undiscounted_price = 0;

        // Get available discounts
        $applied_discount = array();
        $auto_discounts = $this->db->get_results(
            "SELECT * 
            FROM $this->discount_table_name 
            WHERE (discount_on_type = '$type' OR discount_on_type = 'All') 
            AND (discount_on_id = $id OR discount_on_id = 'All')
            AND discount_auto_apply = 'Y'
            AND discount_active = 'Y'", ARRAY_A);
        echo $this->db->last_error;
        
        $code_discount = $this->db->get_results(
            "SELECT *
            FROM $this->discount_table_name
            WHERE discount_code = '$discount'
            AND discount_active = 'Y'", ARRAY_A);
        echo $this->db->last_error;

        // Resources
        if ($type == 'Resources') {

            $booking_date = date('Y-m-d', strtotime($start));

            // Each discount
            foreach ($auto_discounts as $discount) {

                $amount_eligible = 0;
                $amount_not_eligible = 0;
                $days = 1;
                
                $booking_date = date('Y-m-d', strtotime($start));

                while (strtotime($booking_date) < strtotime($end)) {

                    $discount = $this->check_availability($discount, $booking_date);

                    if ($discount !== null) {

                        $amount_eligible += $original_price;

                        // If discount code not in array
                        if (!in_array($discount['discount_code'], $applied_discount)) {
                            $applied_discount[] = $discount['discount_code'];
                        }

                    } else {

                        $amount_not_eligible += $original_price;

                    }

                    $days++;
                    $booking_date = date('Y-m-d', strtotime($booking_date . ' +1 day'));
                
                }

                $price = $this->apply_discount($discount, $amount_eligible);
                $price += $amount_not_eligible;

            }

            



            // Apply discount to each day
            while (strtotime($booking_date) < strtotime($end)) {

                $day_price = $original_price;

                // Check auto apply discount 
                foreach ($auto_discounts as $discount) {

                    $discount = $this->check_availability($discount, $booking_date);
                    
                    if ($discount !== null) {
                        $day_price = $this->apply_discount($discount, $day_price);
                        $applied_discount[] = $discount['discount_code'];
                    } 
        
                }

                // Check code apply discount
                foreach ($code_discount as $discount) {
                    $discount = $this->check_availability($discount, $booking_date);
                    
                    if ($discount !== null) {
                        $day_price = $this->apply_discount($discount, $day_price);
                        $applied_discount[] = $discount['discount_code'];
                    } 
                }

                $discounted_price += $day_price;
                $undiscounted_price += $original_price;

                $booking_date = date('Y-m-d', strtotime($booking_date . ' +1 day'));

            }

            return array('discounted_price' => $discounted_price, 'applied_discount' => $applied_discount, 'original_price' => $undiscounted_price);
            

        } else if ($type == 'Addon') {

            $booking_date = date('Y-m-d', strtotime($start));

            // Per day
            if ($addon_perday == 'Y') {

                // Apply discount to each day
                while (strtotime($booking_date) < strtotime($end)) {

                    $day_price = $original_price;

                    foreach ($auto_discounts as $discount) {

                        $discount = $this->check_availability($discount, $booking_date);
                        
                        if ($discount !== null) {
                            $day_price = $this->apply_discount($discount, $day_price);
                            $applied_discount[] = $discount['discount_code'];
                        } 

                    }

                    foreach ($code_discount as $discount) {

                        $discount = $this->check_availability($discount, $booking_date);
                        
                        if ($discount !== null) {
                            $day_price = $this->apply_discount($discount, $day_price);
                            $applied_discount[] = $discount['discount_code'];
                        } 

                    }

                    $discounted_price += $day_price;
                    $undiscounted_price += $original_price;

                    $booking_date = date('Y-m-d', strtotime($booking_date . ' +1 day'));

                }

                return array('discounted_price' => $discounted_price, 'applied_discount' => $applied_discount, 'original_price' => $undiscounted_price);

            }

            // One time
            $day_price = $original_price;

            foreach ($auto_discounts as $discount) {

                $discount = $this->check_availability($discount, $booking_date);
                
                if ($discount !== null) {
                    $day_price += $this->apply_discount($discount, $original_price);
                    $applied_discount[] = $discount['discount_name'];
                }
    
            }

            foreach ($code_discount as $discount) {

                $discount = $this->check_availability($discount, $booking_date);
                
                if ($discount !== null) {
                    $day_price += $this->apply_discount($discount, $original_price);
                    $applied_discount[] = $discount['discount_name'];
                }
    
            }

            $discounted_price = $day_price;
            $undiscounted_price = $original_price;

            return array('discounted_price' => $discounted_price, 'applied_discount' => $applied_discount, 'original_price' => $undiscounted_price);

        }


        return array('discounted_price' => null, 'applied_discount' => null, 'original_price' => null);

    }

    public function get_discount_by_code($discount_code) {

        $discount = $this->db->get_row("SELECT * FROM $this->discount_table_name WHERE discount_code = '$discount_code' AND discount_active = 'Y'", ARRAY_A);
        echo $this->db->last_error;

        return $discount;

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

    public function update_discount($discount_id, $discount_name, $discount_description, $discount_code, $discount_quantity, $discount_type, $discount_amount, $discount_start_date, $discount_end_date, $discount_on_type, $discount_on_id, $discount_condition, $discount_condition_start, $discount_condition_end, $discount_auto_apply, $discount_active) {

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
            'discount_condition_start' => $discount_condition_start,
            'discount_condition_end' => $discount_condition_end,
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
            discount_on_id VARCHAR(255),      
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
        
        $this->deleteDB($this->table_name);
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




