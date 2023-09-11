<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

require_once (BI_PLUGIN_PATH . '/includes/resources/resources.php');
require_once (BI_PLUGIN_PATH . '/includes/addons/addons.php');
require_once (BI_PLUGIN_PATH . '/includes/pricings/pricing.php');

class BookedInBookings {

    private $db;
    private $charset_collate;
    private $resourcesClass;
    private $addonsClass;
    private $pricingClass;
    public $booking_header_table = 'bookedin_booking_header';
    public $booking_table = 'bookedin_bookings';
    public $booking_addons_table = 'bookedin_booking_addons';
    public $booking_header_table_name;
    public $booking_table_name;
    public $booking_addons_table_name;
    public $addon_table_name;


    public function __construct() {
        global $wpdb;
        $this->db = $wpdb;
        $this->resourcesClass = new BookedInResources();
        $this->addonsClass = new BookedInAddons();
        $this->pricingClass = new BookedInPricings();
        $this->addon_table_name = $this->addonsClass->table_name;
        $this->charset_collate = $this->db->get_charset_collate();
        $this->booking_header_table_name = $this->db->prefix . $this->booking_header_table;
        $this->booking_table_name = $this->db->prefix . $this->booking_table;
        $this->booking_addons_table_name = $this->db->prefix . $this->booking_addons_table;
    }

    public function get_available($booking_date_from, $booking_date_to) {
        
        // booking to - 1 day
        $booking_date_to = date('Y-m-d', strtotime("$booking_date_to - 1 days"));

        // SQL get all bookings between dates
        $bookings = $this->db->get_results(
            "SELECT * FROM $this->booking_table_name WHERE booking_date BETWEEN '$booking_date_from' AND '$booking_date_to'"
            , ARRAY_A);    

        // SQL get all resources
        $resources = $this->resourcesClass->get_resources(null,'Y');

        // Get available resources thats not in bookings
        $availableResources = array();

        foreach ($resources as $resource) {
            $resourceId = $resource['id'];
            $resourceName = $resource['resource_name'];
            $resourceDescription = $resource['resource_description'];
            $resourcePrice = $resource['resource_price'];
            $resourceAvailable = true;

            foreach ($bookings as $booking) {
                if ($booking['booking_resource'] == $resourceId) {
                    $resourceAvailable = false;
                }
            }

            if ($resourceAvailable) {
                $availableResources[] = array(
                    'id' => $resourceId,
                    'name' => $resourceName,
                    'description' => $resourceDescription,
                    'price' => $resourcePrice
                );
            }
        }

        return $availableResources;

    }

    public function calculate_resource_price($booking_date_start, $booking_date_end, $booking_resource, $booking_adult, $booking_children, $booking_discount) {
       
        // Get resource price
        $resource = $this->resourcesClass->get_resources($booking_resource);

        // Get discount
        $response = $this->pricingClass->apply_auto_discount('Resources', $resource['id'], $resource['resource_price'], $booking_discount, $booking_date_start, $booking_date_end,  $booking_adult, $booking_children);

        $total_price = $response['discounted_price'];
        $ori_price = $response['original_price'];
        $applied_discount = $response['applied_discount'];
        
        return [$total_price, $ori_price, $applied_discount];
    }

    public function calculate_addon_price($booking_date_start, $booking_date_end, $booking_addon, $booking_adult, $booking_children, $booking_discount) {

        $total_price = 0;
        $ori_price = 0;
        $applied_discount = array();

        // Get addon price
        if ($booking_addon != null) {
            foreach ($booking_addon as $addonid) {
                
                $addon = $this->addonsClass->get_addons($addonid);
                
                // Get discount 
                $response = $this->pricingClass->apply_auto_discount('Addon', $addon['id'], $addon['addon_price'], $booking_discount, $booking_date_start, $booking_date_end, $booking_adult, $booking_children, $addon['addon_perday']);   
                
                $total_price = $response['discounted_price'];
                $ori_price = $response['original_price'];
                $applied_discount = $response['applied_discount'];
            }
        }

        return [$total_price, $ori_price, $applied_discount];
    }

    public function calculate_price($booking_date_from, $booking_date_to, $booking_resource, $booking_addon, $booking_adult, $booking_children, $booking_discount) {

        // Get the resource
        $resource = $this->resourcesClass->get_resources($booking_resource);

        // Get all the addons
        $addons = array();
        if ($booking_addon != null) {
            foreach ($booking_addon as $addonid) {
                $addon = $this->addonsClass->get_addons($addonid);
                $addons[] = $addon;
            }
        }

        [$resource_output, $addon_output, $total, $discount_used] = $this->pricingClass->get_price_after_discount($booking_discount, $booking_date_from, $booking_date_to, $resource, $addons, $booking_adult, $booking_children);

        return [$resource_output, $addon_output, $total, $discount_used];

    }

    public function add_booking_header($booking_date_from, $booking_date_to,$booking_resource, $booking_notes, $booking_description, $booking_paid, $booking_discount, $booking_price, $booking_adults, $booking_children, $booking_user, $booking_email, $booking_phone ) {

        // Generate unique booking number
        $booking_number = '';
        

        $this->db->insert($this->booking_header_table_name, array(
            'booking_number' => $booking_number,
            'booking_date_from' => $booking_date_from,
            'booking_date_to' => $booking_date_to,
            'booking_resource' => $booking_resource,
            'booking_notes' => $booking_notes,
            'booking_description' => $booking_description,
            'booking_paid' => $booking_paid,
            'booking_discount' => $booking_discount,
            'booking_price' => $booking_price,
            'booking_adults' => $booking_adults,
            'booking_children' => $booking_children,
            'booking_user' => $booking_user,
            'booking_email' => $booking_email,
            'booking_phone' => $booking_phone,
        ));
        echo $this->db->last_error;
        $id = $this->db->insert_id;

        return $id;

    }

    public function add_booking($booking_header_id, $booking_date, $booking_resource) {

        $this->db->insert($this->booking_table_name, array(
            'booking_header_id' => $booking_header_id,
            'booking_date' => $booking_date,
            'booking_resource' => $booking_resource,
        ));

        $id = $this->db->insert_id;

        return $id;

    }

    public function add_booking_addon($booking_header_id, $booking_date, $booking_addon, $booking_paid, $booking_discount) {

        $this->db->insert($this->booking_addons_table_name, array(
            'booking_header_id' => $booking_header_id,
            'booking_date' => $booking_date,
            'booking_addon' => $booking_addon,
            'booking_paid' => $booking_paid,
            'booking_discount' => $booking_discount,
        ));

        $id = $this->db->insert_id;

        return $id;

    }

    public function get_booking_header($booking_id = null, $recordsPerPage = 10, $offset = 0) {

        if ($booking_id === null) {
            $offset = intval($offset);
            $resourceTable = $this->resourcesClass->table_name;
            $booking_header = $this->db->get_results("SELECT 
            bh.id as 'id',
            bh.booking_date_from as 'booking_date_from',
            bh.booking_date_to as 'booking_date_to',
            bh.booking_notes as 'booking_notes',
            bh.booking_description as 'booking_description',
            bh.booking_paid as 'booking_paid',
            bh.booking_discount as 'booking_discount',
            bh.booking_price as 'booking_price',
            bh.booking_adults as 'booking_adults',
            bh.booking_children as 'booking_children',
            bh.booking_user as 'booking_user',
            bh.booking_email as 'booking_email',
            bh.booking_phone as 'booking_phone',
            br.resource_name as 'resource_name'
            FROM $this->booking_header_table_name bh 
            LEFT JOIN $resourceTable br on br.id = bh.booking_resource
            ORDER BY bh.booking_date_from DESC
            LIMIT $recordsPerPage
            OFFSET $offset", ARRAY_A);
            return $booking_header;
        }

        $booking_header = $this->db->get_row("SELECT * FROM $this->booking_header_table_name WHERE id = $booking_id", ARRAY_A);

        return $booking_header;

    }

    public function get_booking_header_count() {
            
            $booking_header = $this->db->get_row("SELECT COUNT(*) as 'count' FROM $this->booking_header_table_name", ARRAY_A);
    
            return $booking_header['count'];
    
    }

    public function get_booking_addons($booking_id = null) {

        if ($booking_id === null) {
            $booking_addons = $this->db->get_results(
                "SELECT * 
                FROM $this->booking_addons_table_name 
                LEFT JOIN $this->addon_table_name ON $this->booking_addons_table_name.booking_addon = $this->addon_table_name.id
                ORDER BY 'booking_addon' ASC", ARRAY_A);
            return $booking_addons;
        }

        $booking_addons = $this->db->get_results(
            "SELECT * 
            FROM $this->booking_addons_table_name 
            LEFT JOIN $this->addon_table_name ON $this->booking_addons_table_name.booking_addon = $this->addon_table_name.id
            WHERE booking_header_id = $booking_id
            ORDER BY 'booking_addon' ASC", ARRAY_A);
        return $booking_addons;


    }

    public function get_nights($booking_date_from, $booking_date_to) {

        $date1 = new DateTime($booking_date_from);
        $date2 = new DateTime($booking_date_to);
        $interval = $date1->diff($date2);
        $nights = $interval->format('%a');

        return $nights;

    }

    public function get_booking_slots() {
            
            $totalResources = $this->resourcesClass->get_total_resources();
    
            $bookingSlots = $this->db->get_results(
                "SELECT DATE(booking_date) as 'date', 
                ($totalResources-COUNT(*)) AS 'availableSlots'
                from $this->booking_table_name wbb 
                GROUP BY DATE(booking_date)"
                , ARRAY_A);
    
            return $bookingSlots;
    
    }

    public function delete_booking($booking_id) {
            
            $this->db->delete($this->booking_header_table_name, array('id' => $booking_id));
            $this->db->delete($this->booking_table_name, array('booking_header_id' => $booking_id));
            $this->db->delete($this->booking_addons_table_name, array('booking_header_id' => $booking_id));
    
    }

    public function createBookingHeaderDB() {

        $sql = "CREATE TABLE IF NOT EXISTS $this->booking_header_table_name (
            id INT NOT NULL AUTO_INCREMENT,
            booking_number VARCHAR(255) NOT NULL,
            booking_date_from DATE NOT NULL,
            booking_date_to DATE NOT NULL,
            booking_resource VARCHAR(255),
            booking_resource_qty INT DEFAULT 1,
            booking_notes TEXT,
            booking_description TEXT,
            booking_paid CHAR(1) NOT NULL DEFAULT 'N',
            booking_discount VARCHAR(255),
            booking_price VARCHAR(255) NOT NULL DEFAULT 0,
            booking_adults INT NOT NULL DEFAULT 0,
            booking_children INT NOT NULL DEFAULT 0,
            booking_user VARCHAR(255) NOT NULL,
            booking_email VARCHAR(255) NOT NULL,
            booking_phone VARCHAR(255) NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            edited_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $this->charset_collate;";

        dbDelta($sql);

    }

    public function createBookingsDB() {

        $sql = "CREATE TABLE IF NOT EXISTS $this->booking_table_name (
            id INT NOT NULL AUTO_INCREMENT,
            booking_header_id INT NOT NULL,
            booking_date DATE NOT NULL,
            booking_resource VARCHAR(255),
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            edited_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $this->charset_collate;";

        dbDelta($sql);
    
    }

    public function createBookingAddonsDB() {

        $sql = "CREATE TABLE IF NOT EXISTS $this->booking_addons_table_name (
            id INT NOT NULL AUTO_INCREMENT,
            booking_header_id INT NOT NULL,
            booking_date DATE NOT NULL,
            booking_addon VARCHAR(255),
            booking_paid CHAR(1) NOT NULL DEFAULT 'N',
            booking_discount VARCHAR(255),
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            edited_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $this->charset_collate;";

        dbDelta($sql);
    
    }

    public function deleteDB($table_name) {

        $this->db->query("DROP TABLE IF EXISTS $table_name");
    }

    // Function to create the custom database table on plugin activation
    public function activate() {

        $this->createBookingHeaderDB();    
        $this->createBookingsDB();
        $this->createBookingAddonsDB();

    }

    // Function to remove the custom database table on plugin deactivation
    public function deactivate() {

        $this->deleteDB($this->booking_header_table_name);
        $this->deleteDB($this->booking_table_name);
        $this->deleteDB($this->booking_addons_table_name);

    }
    
}

// REST API ENDPOINTS
add_action('rest_api_init', 'register_get_available');

function register_get_available() {
    register_rest_route('v1/resources', 'get_available', array(
          'methods' => 'POST',
          'callback' => 'get_available_callback'
    ));
} 

function get_available_callback($request) {

    $date_from = $request->get_param('booking_date_from');
    $date_to = $request->get_param('booking_date_to');

    $bookingsClass = new BookedInBookings();
    $available = $bookingsClass->get_available($date_from, $date_to);
    return new WP_REST_Response(array('availables'=>$available,'message'=>'Success'), 200);
}

// REST API ENDPOINTS
add_action('rest_api_init', 'register_calculate_price');

function register_calculate_price() {
    register_rest_route('v1/booking', 'calculate_price', array(
          'methods' => 'POST',
          'callback' => 'calculate_price_callback'
    ));
} 

function calculate_price_callback($request) {

    $start = $request->get_param('booking_date_from');
    $end = $request->get_param('booking_date_to');
    $resource = $request->get_param('booking_resource');
    $addon = $request->get_param('booking_addon');
    $adults = $request->get_param('booking_adults');
    $children = $request->get_param('booking_children');
    $discount = $request->get_param('booking_discount');

    $booking = new BookedInBookings();
    [$resource_output, $addon_output, $total, $discount] = $booking->calculate_price($start, $end, $resource, $addon, $adults, $children, $discount);

    return new WP_REST_Response(array('resource'=>$resource_output, 'addons'=>$addon_output, 'total'=>$total, 'discount'=>$discount, 'message'=>'Success'), 200);
}