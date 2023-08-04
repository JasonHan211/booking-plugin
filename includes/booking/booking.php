<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

class BookedInBookings {

    private $db;
    private $charset_collate;
    public $booking_header_table = 'bookedin_booking_header';
    public $booking_table = 'bookedin_bookings';
    public $booking_addons_table = 'bookedin_booking_addons';
    public $booking_header_table_name;
    public $booking_table_name;
    public $booking_addons_table_name;

    public function __construct() {
        global $wpdb;
        $this->db = $wpdb;
        $this->charset_collate = $this->db->get_charset_collate();
        $this->booking_header_table_name = $this->db->prefix . $this->booking_header_table;
        $this->booking_table_name = $this->db->prefix . $this->booking_table;
        $this->booking_addons_table_name = $this->db->prefix . $this->booking_addons_table;
    }

    public function add_booking_header($booking_date_from, $booking_date_to, $booking_notes, $booking_description, $booking_paid, $booking_adults, $booking_children, $booking_user, $booking_email, $booking_phone ) {

        $this->db->insert($this->booking_header_table_name, array(
            'booking_date_from' => $booking_date_from,
            'booking_date_to' => $booking_date_to,
            'booking_notes' => $booking_notes,
            'booking_description' => $booking_description,
            'booking_paid' => $booking_paid,
            'booking_adults' => $booking_adults,
            'booking_children' => $booking_children,
            'booking_user' => $booking_user,
            'booking_email' => $booking_email,
            'booking_phone' => $booking_phone,
        ));

        $id = $this->db->insert_id;

        return $id;

    }

    public function add_booking($booking_header_id, $booking_date, $booking_resource, $booking_paid) {

        $this->db->insert($this->booking_table_name, array(
            'booking_header_id' => $booking_header_id,
            'booking_date' => $booking_date,
            'booking_resource' => $booking_resource,
            'booking_paid' => $booking_paid
        ));

        $id = $this->db->insert_id;

        return $id;

    }

    public function add_booking_addon($booking_header_id, $booking_date, $booking_addon, $booking_paid) {

        $this->db->insert($this->booking_addons_table_name, array(
            'booking_header_id' => $booking_header_id,
            'booking_date' => $booking_date,
            'booking_addon' => $booking_addon,
            'booking_paid' => $booking_paid
        ));

        $id = $this->db->insert_id;

        return $id;

    }

    public function createBookingHeaderDB() {

        $sql = "CREATE TABLE IF NOT EXISTS $this->booking_header_table_name (
            id INT NOT NULL AUTO_INCREMENT,
            booking_date_from DATE NOT NULL,
            booking_date_to DATE NOT NULL,
            booking_notes TEXT,
            booking_description TEXT,
            booking_paid CHAR(1) NOT NULL DEFAULT 'N',
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


