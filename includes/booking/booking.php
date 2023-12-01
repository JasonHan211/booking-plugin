<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

require_once (BI_PLUGIN_PATH . '/includes/resources/resources.php');
require_once (BI_PLUGIN_PATH . '/includes/addons/addons.php');
require_once (BI_PLUGIN_PATH . '/includes/pricings/pricing.php');
require_once (BI_PLUGIN_PATH . '/includes/dates/date.php');

class BookedInBookings {

    private $db;
    private $charset_collate;
    private $resourcesClass;
    private $addonsClass;
    private $pricingClass;
    private $datesClass;
    public $booking_header_table = 'bookedin_booking_header';
    public $booking_table = 'bookedin_bookings';
    public $booking_addons_table = 'bookedin_booking_addons';
    public $booking_invoice_table = 'bookedin_booking_invoice';
    public $booking_header_table_name;
    public $booking_table_name;
    public $booking_addons_table_name;
    public $booking_invoice_table_name;
    public $addon_table_name;


    public function __construct() {
        global $wpdb;
        $this->db = $wpdb;
        $this->resourcesClass = new BookedInResources();
        $this->addonsClass = new BookedInAddons();
        $this->pricingClass = new BookedInPricings();
        $this->datesClass = new BookedInDates();
        $this->addon_table_name = $this->addonsClass->table_name;
        $this->booking_header_table_name = $this->db->prefix . $this->booking_header_table;
        $this->booking_table_name = $this->db->prefix . $this->booking_table;
        $this->booking_addons_table_name = $this->db->prefix . $this->booking_addons_table;
        $this->booking_invoice_table_name = $this->db->prefix . $this->booking_invoice_table;
        $this->charset_collate = $this->db->get_charset_collate();
    }

    public function get_available($booking_date_from, $booking_date_to) {
        
        // booking to - 1 day
        $booking_date_to = date('Y-m-d', strtotime("$booking_date_to - 1 days"));

        // SQL get all bookings between dates
        $bookings = $this->db->get_results(
            "SELECT * 
            FROM $this->booking_table_name 
            WHERE booking_date BETWEEN '$booking_date_from' AND '$booking_date_to'
            AND booking_paid = 'Y'
            ", ARRAY_A);    

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

    public function calculate_price($booking_date_from, $booking_date_to, $bookings, $booking_discount, $check = true) {

        foreach ($bookings as $booking) {
            $booking->resource = $this->resourcesClass->get_resources($booking->resource);
            
            // Get all the addons
            if (count($booking->addons) > 0) {
                foreach ($booking->addons as &$addonid) {
                    $addonid = $this->addonsClass->get_addons($addonid);
                }
            }
        }

        [$bookings, $total, $discount_used] = $this->pricingClass->get_price_after_discount($booking_discount, $booking_date_from, $booking_date_to, $bookings, $check);

        return [$bookings, $total, $discount_used];

    }

    public function add_booking_header($booking_number = null, $booking_date_from, $booking_date_to,$booking_resource, $booking_notes, $booking_description, $booking_paid, $booking_deposit_refund, $booking_discount, $booking_price, $booking_adults, $booking_children, $booking_user, $booking_email, $booking_phone ) {

        // Generate unique booking number
        if ($booking_number == null) {
            $booking_number = uniqid('BNB-');
            $booking_number = strtoupper($booking_number);
        }

        $this->db->insert($this->booking_header_table_name, array(
            'booking_number' => $booking_number,
            'booking_date_from' => $booking_date_from,
            'booking_date_to' => $booking_date_to,
            'booking_resource' => $booking_resource,
            'booking_notes' => $booking_notes,
            'booking_description' => $booking_description,
            'booking_paid' => $booking_paid,
            'booking_deposit_refund' => $booking_deposit_refund,
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

        return [$id, $booking_number];

    }

    public function add_booking($booking_header_id, $booking_date, $booking_resource, $booking_paid) {

        $this->db->insert($this->booking_table_name, array(
            'booking_header_id' => $booking_header_id,
            'booking_date' => $booking_date,
            'booking_resource' => $booking_resource,
            'booking_paid' => $booking_paid,
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

    public function get_booking_header($booking_id = null, $page = 1, $recordsPerPage = 10, $number='', $date = '', $resource = '', $paid = '', $depositRefund = '', $range = '', $name = '', $email = '', $phone = '') {

        $offset = $recordsPerPage * ($page - 1);
        
        $condition = '1=1';
        if ($number != '') {
            $condition .= " AND bh.booking_number LIKE '%$number%'";
        }
        if ($date != '') {
            $condition .= " AND bh.booking_date_from = '$date'";
        }
        if ($range != '') {
            if ($range == '0') {
                //Get date starting from today onwards
                $today = date('Y-m-d');
                $condition .= " AND bh.booking_date_from >= '$today'";
            } else {
                //Get date from weeks ago
                $weeksAgo = (int)$range;
                $startDate = date('Y-m-d', strtotime("-$weeksAgo weeks"));
                $condition .= " AND bh.booking_date_from >= '$startDate'";
            }
        }
        if ($resource != '') {
            $condition .= " AND br.id = $resource";
        }
        if ($paid != '') {
            $condition .= " AND bh.booking_paid = '$paid'";
        }
        if ($depositRefund != '') {
            $condition .= " AND bh.booking_deposit_refund = '$depositRefund'";
        }
        if ($name != '') {
            $condition .= " AND bh.booking_user LIKE '%$name%'";
        }
        if ($email != '') {
            $condition .= " AND bh.booking_email LIKE '%$email%'";
        }
        if ($phone != '') {
            $condition .= " AND bh.booking_phone LIKE '%$phone%'";
        }

        if ($booking_id === null) {

            $offset = intval($offset);
            $resourceTable = $this->resourcesClass->table_name;

            $booking_header = $this->db->get_results("SELECT 
                bh.id as 'id',
                bh.booking_number as 'booking_number',
                bh.booking_date_from as 'booking_date_from',
                bh.booking_date_to as 'booking_date_to',
                bh.booking_notes as 'booking_notes',
                bh.booking_description as 'booking_description',
                bh.booking_paid as 'booking_paid',
                bh.booking_deposit_refund as 'booking_deposit_refund',
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
                WHERE $condition
                ORDER BY bh.booking_date_from ASC
                LIMIT $recordsPerPage
                OFFSET $offset", ARRAY_A);
                
            $count = $this->get_booking_header_count($condition);

            return array($booking_header,$count);
        }

        $booking_header1 = $this->db->get_row("SELECT * FROM $this->booking_header_table_name WHERE id = $booking_id", ARRAY_A);
        return $booking_header1;

    }

    public function get_booking_header_count($condition) {
            
        $resourceTable = $this->resourcesClass->table_name;

        $booking_header = $this->db->get_row("SELECT 
            COUNT(*) as 'count' 
            FROM $this->booking_header_table_name bh
            LEFT JOIN $resourceTable br on br.id = bh.booking_resource
            WHERE $condition
            ", ARRAY_A);

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

    public function get_booking($booking_id = null) {

        if ($booking_id === null) {
            $booking = $this->db->get_results(
                "SELECT * 
                FROM $this->booking_table_name", ARRAY_A);
            return $booking;
        }

        $booking = $this->db->get_results(
            "SELECT *
            FROM $this->booking_table_name
            WHERE booking_header_id = $booking_id", ARRAY_A);
        return $booking;

    }

    public function update_booking_header($booking_id, $booking_number, $booking_date_from, $booking_date_to, $booking_resource, $booking_notes, $booking_description, $booking_paid, $booking_deposit_refund, $booking_discount, $booking_price, $booking_adults, $booking_children, $booking_user, $booking_email, $booking_phone ) {

        $this->db->update($this->booking_header_table_name, array(
            'booking_number' => $booking_number,
            'booking_date_from' => $booking_date_from,
            'booking_date_to' => $booking_date_to,
            'booking_resource' => $booking_resource,
            'booking_notes' => $booking_notes,
            'booking_description' => $booking_description,
            'booking_paid' => $booking_paid,
            'booking_deposit_refund' => $booking_deposit_refund,
            'booking_discount' => $booking_discount,
            'booking_price' => $booking_price,
            'booking_adults' => $booking_adults,
            'booking_children' => $booking_children,
            'booking_user' => $booking_user,
            'booking_email' => $booking_email,
            'booking_phone' => $booking_phone,
        ), array('id' => $booking_id));

        return [$booking_id, $booking_number];

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
                ($totalResources-COUNT(*)) AS 'availableSlots',
                '' as 'day'
                from $this->booking_table_name wbb 
                where wbb.booking_paid = 'Y'
                GROUP BY DATE(booking_date)"
                , ARRAY_A);
            
            $breaks = $this->datesClass->get_all_break();

            $all = array_merge($bookingSlots, $breaks);

            return $all;
    
    }

    public function new_booking($booking_date_from, $booking_date_to, $bookings, $booking_notes, $booking_description, $booking_name, $booking_email, $booking_phone, $booking_discount, $booking_price, $booking_paid, $booking_deposit_refund) {

        // Check if its available
        $available = $this->get_available($booking_date_from, $booking_date_to);
        if (count($available) == 0) {
            echo '<script>alert("Sorry, this date is not available. Please try another date.");location.reload(); </script>';
            return;
        }

        $mainBookingNumber = null;
        $booking_price_total = 0;

        foreach($bookings as $booking) {

            $booking_resource = $booking->resource;
            $booking_adults = $booking->adult;
            $booking_children = $booking->children;
            // Get all the addons
            if (count($booking->addons) > 0) {
                foreach ($booking->addons as &$addonid) {
                    $addonid = $this->addonsClass->get_addons($addonid);
                }
            }

            $selectedAddons = $booking->addons;

            $booking->resource = $this->resourcesClass->get_resources($booking_resource);

            [$bookings, $total, $discount_used] = $this->pricingClass->get_price_after_discount($booking_discount, $booking_date_from, $booking_date_to, array($booking), true);

            $booking_discount_used = array();
            foreach ($discount_used as $discount) {
                $booking_discount_used[] = $discount['discount_name'];
                $this->pricingClass->use_discount($discount);
            }

            // For public
            $booking_price_total = $total['total_after_final_discounted'];
            
            [$booking_header_id, $booking_number] = $this->add_booking_header($mainBookingNumber, $booking_date_from, $booking_date_to, $booking_resource, $booking_notes, $booking_description, $booking_paid, $booking_deposit_refund, json_encode($booking_discount_used), $booking_price, $booking_adults, $booking_children, $booking_name, $booking_email, $booking_phone);

            if ($mainBookingNumber == null) {
                $mainBookingNumber = $booking_number;
            }

            // Add booking for selected addon with charge once
            foreach ($selectedAddons as $addon) {
                if ($addon['addon_perday'] == 'N') {
                    $this->add_booking_addon($booking_header_id, $booking_date_from, $addon['id'], $booking_paid, $booking_discount);
                }
            }

            $nights = $this->get_nights($booking_date_from, $booking_date_to);
            for ($i = 0; $i < $nights; $i++) {
                $booking_date = date('Y-m-d', strtotime("$booking_date_from + $i days"));           
                $this->add_booking($booking_header_id, $booking_date, $booking_resource, $booking_paid);
                
                // Add booking for selected addon with charge per day
                foreach ($selectedAddons as $addon) {
                    if ($addon['addon_perday'] == 'Y') {
                        $this->add_booking_addon($booking_header_id, $booking_date, $addon['id'], $booking_paid, $booking_discount);
                    }
                }
            }
            
            $this->add_booking_invoice($booking_number, json_encode($booking->resource), json_encode($selectedAddons), json_encode($total));

        }

        return array($mainBookingNumber,$booking_price_total);

    }

    public function add_booking_invoice($booking_number, $resources, $addons, $total) {
        $this->db->insert($this->booking_invoice_table_name, array(
            'booking_number' => $booking_number,
            'resource_info' => $resources,
            'addon_info' => $addons,
            'total_info' => $total,
        ));

        $id = $this->db->insert_id;
        
        return $id;
    }

    public function delete_booking_and_addons($booking_id) {
        $this->db->delete($this->booking_table_name, array('booking_header_id' => $booking_id));
        $this->db->delete($this->booking_addons_table_name, array('booking_header_id' => $booking_id));
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
            booking_deposit_refund CHAR(1) NOT NULL DEFAULT 'N',
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
            booking_paid CHAR(1) NOT NULL DEFAULT 'N',
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

    public function createInvoiceDB() {

        $sql = "CREATE TABLE IF NOT EXISTS $this->booking_invoice_table_name (
            id INT NOT NULL AUTO_INCREMENT,
            booking_number VARCHAR(255) NOT NULL,
            resource_info JSON,
            addon_info JSON,
            total_info JSON,
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
        $this->createInvoiceDB();

    }

    // Function to remove the custom database table on plugin deactivation
    public function deactivate() {

        $this->deleteDB($this->booking_header_table_name);
        $this->deleteDB($this->booking_table_name);
        $this->deleteDB($this->booking_addons_table_name);
        $this->deleteDB($this->booking_invoice_table_name);

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
    $bookings = json_decode($request->get_param('bookings'));
    $discount = $request->get_param('booking_discount');
    $booking = new BookedInBookings();
    [$bookings, $total, $discount] = $booking->calculate_price($start, $end, $bookings, $discount);

    return new WP_REST_Response(array('bookings'=>$bookings, 'total'=>$total, 'discount'=>$discount, 'message'=>'Success'), 200);
}

// REST API ENDPOINTS
add_action('rest_api_init', 'register_get_booking_table');

function register_get_booking_table() {
    register_rest_route('v1/booking', 'get_booking_table', array(
          'methods' => 'POST',
          'callback' => 'get_booking_table_callback'
    ));
} 

function get_booking_table_callback($request) {

    $bookingID = $request->get_param('bookingID');
    $date = $request->get_param('date');
    $resource = $request->get_param('resource');
    $paid = $request->get_param('paid');
    $depositRefund = $request->get_param('depositRefund');
    $range = $request->get_param('range');
    $name = $request->get_param('name');
    $email = $request->get_param('email');
    $phone = $request->get_param('phone');
    $page = $request->get_param('page');
    $recordsPerPage = $request->get_param('recordsPerPage');

    $bookingClass = new BookedInBookings();
    [$bookings,$count] = $bookingClass->get_booking_header(null, $page, $recordsPerPage,$bookingID, $date, $resource, $paid, $depositRefund,$range, $name, $email, $phone);
    $addonArray = array();
    foreach($bookings as $booking) {
        $addon = $bookingClass->get_booking_addons($booking["id"]);
        $addonArray[] = $addon;
    }

    return new WP_REST_Response(array('bookings'=>$bookings,'addons'=>$addonArray,'totalCount'=>$count, 'message'=>'Success'), 200);
}

// REST API ENDPOINTS
add_action('rest_api_init', 'register_get_invoice');

function register_get_invoice() {
    register_rest_route('v1/booking', 'get_invoice', array(
          'methods' => 'POST',
          'callback' => 'get_invoice_callback'
    ));
} 

function get_invoice_callback($request) {

    $bookingIDs = $request->get_param('bookingIDs');

    $booking = new BookedInBookings();
    

    return new WP_REST_Response(array('invoice'=>'', 'debug'=>$bookingIDs, 'message'=>'Success'), 200);
}

// REST API ENDPOINTS
add_action('rest_api_init', 'register_new_booking');

function register_new_booking() {
    register_rest_route('v1/booking', 'new_booking', array(
          'methods' => 'POST',
          'callback' => 'new_booking_callback'
    ));
} 

function new_booking_callback($request) {

    $start = $request->get_param('booking_date_from');
    $end = $request->get_param('booking_date_to');
    $bookings = json_decode($request->get_param('bookings'));
    $booking_notes = $request->get_param('booking_notes');
    $booking_description = $request->get_param('booking_description');
    $booking_name = $request->get_param('booking_name');
    $booking_email = $request->get_param('booking_email');
    $booking_phone = $request->get_param('booking_phone');
    $booking_discount = $request->get_param('booking_discount');
    $booking_price = $request->get_param('booking_price');
    $booking_paid = $request->get_param('booking_paid');
    $booking_deposit_refund = $request->get_param('booking_deposit_refund');

    $booking = new BookedInBookings();
    
    $booking->new_booking($start, $end, $bookings, $booking_notes, $booking_description, $booking_name, $booking_email, $booking_phone, $booking_discount, $booking_price, $booking_paid, $booking_deposit_refund);

    return new WP_REST_Response(array('invoice'=>'', 'debug'=>$bookings, 'message'=>'Success'), 200);
}