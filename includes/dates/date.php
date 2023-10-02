<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

class BookedInDates {

    private $db;
    private $charset_collate;
    public $dates_table = 'bookedin_dates';
    public $table_name;

    public function __construct() {
        global $wpdb;
        $this->db = $wpdb;
        $this->charset_collate = $this->db->get_charset_collate();
        $this->table_name = $this->db->prefix . $this->dates_table;
    }

    public function add_dates($date_name, $date_description, $date_time, $date_type, $activeFlag) {
        
        $this->db->insert($this->table_name, array(
            'date_name' => $date_name,
            'date_description' => $date_description,
            'date_time' => $date_time,
            'date_type' => $date_type,
            'activeFlag' => $activeFlag
        ));

        return $this->db->insert_id;
    }

    public function update_dates($date_id, $date_name, $date_description, $date_time, $date_type, $activeFlag) {

        $this->db->update($this->table_name, array(
            'date_name' => $date_name,
            'date_description' => $date_description,
            'date_time' => $date_time,
            'date_type' => $date_type,
            'activeFlag' => $activeFlag
        ), array('id' => $date_id));

        return $this->db->insert_id;
    }

    public function get_dates($date_id=null) {

        if ($date_id) {
            $sql = "SELECT * FROM $this->table_name WHERE id = $date_id";
        } else {
            $sql = "SELECT * FROM $this->table_name ORDER BY date_time";
        }

        $result = $this->db->get_results($sql, 'ARRAY_A');

        return $result;
    }

    public function get_all_holiday() {

        $result = $this->db->get_results(
            "SELECT
            DATE(date_time) as 'date',
            date_name as 'day'
            FROM $this->table_name
            WHERE date_type = 'Holiday'
            AND activeFlag = 'Y'
            ", 'ARRAY_A');

        return $result;

    }

    public function get_all_break() {

        $result = $this->db->get_results(
            "SELECT 
            DATE(date_time) as 'date', 
            0 AS 'availableSlots',
            date_name as 'day'
            FROM $this->table_name
            WHERE date_type = 'Break'
            AND activeFlag = 'Y'
            ", 'ARRAY_A');

        return $result;

    }

    public function check_is_holiday($date_time) {
        
        $result = $this->db->get_results(
            "SELECT * 
            FROM $this->table_name 
            WHERE date_time = '$date_time'
            AND date_type = 'Holiday'
            AND activeFlag = 'Y'
            ", 'ARRAY_A');

        $output = $result? true : false;

        return $output;

    }

    public function check_is_break($date_time) {

        $result = $this->db->get_results(
            "SELECT * 
            FROM $this->table_name 
            WHERE date_time = '$date_time'
            AND date_type = 'Break'
            AND activeFlag = 'Y'
            ", 'ARRAY_A');

        $output = $result? true : false;

        return $output;

    }

    public function delete_dates($date_id) {

        $this->db->delete($this->table_name, array('id' => $date_id));

    }

    public function createDatesDB() {
        $sql = "CREATE TABLE IF NOT EXISTS $this->table_name (
            id INT NOT NULL AUTO_INCREMENT,
            date_name VARCHAR(255) NOT NULL,
            date_description VARCHAR(255) NOT NULL,
            date_time DATE NOT NULL,
            date_type VARCHAR(255) NOT NULL,
            activeFlag CHAR(1) NOT NULL DEFAULT 'Y',
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            edited_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $this->charset_collate;";

        dbDelta($sql);
    }

    public function deleteDB($table_name) {

        $this->db->query("DROP TABLE IF EXISTS $table_name");

    }

    public function dates_activate(){
        
        $this->createDatesDB();

    }

    public function dates_deactivate(){
        
        $this->deleteDB($this->table_name);

    }

}