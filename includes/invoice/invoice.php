<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

require_once (BI_PLUGIN_PATH . 'includes/invoice/template/template.php');

class BookedInInvoice {
    
    private $db;
    private $charset_collate;
    public $booking_invoice_table = 'bookedin_booking_invoice';
    public $booking_invoice_table_name;

    public function __construct() {
        global $wpdb;
        $this->db = $wpdb;
        $this->booking_invoice_table_name = $this->db->prefix . $this->booking_invoice_table;
        $this->charset_collate = $this->db->get_charset_collate();
    }

    public function get_invoice_by_id($booking_id) {
        
        $booking = $this->db->get_results(
            "SELECT *
            FROM $this->booking_invoice_table_name
            WHERE booking_number = '$booking_id'", ARRAY_A);
        return $booking;
    }

    public function get_invoice_html($booking_ids) {

        $totalItems = count($booking_ids);
        $currentItem = 0;
        $outputString = '';

        $outputString .= heading();

        foreach($booking_ids as $booking_id) {
            $currentItem++;

            $data = $this->get_invoice_by_id($booking_id);

            // Invoice Header
            $outputString .= bodyTemplate($data);

            if ($currentItem < $totalItems) {
                $outputString .= nextPageTemplate();
            }
        }

        return $outputString;
    }
  
    public function add_booking_invoice($booking_number, $bookingDetails, $contact, $total) {
        $this->db->insert($this->booking_invoice_table_name, array(
            'booking_number' => $booking_number,
            'booking_info' => $bookingDetails,
            'contact_info' => $contact,
            'total_info' => $total,
        ));

        $id = $this->db->insert_id;
        
        return $id;
    }

    public function createInvoiceDB() {

        $sql = "CREATE TABLE IF NOT EXISTS $this->booking_invoice_table_name (
            id INT NOT NULL AUTO_INCREMENT,
            booking_number VARCHAR(255) NOT NULL,
            booking_info JSON,
            contact_info JSON,
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

        $this->createInvoiceDB();

    }

    // Function to remove the custom database table on plugin deactivation
    public function deactivate() {

        $this->deleteDB($this->booking_invoice_table_name);

    }

}