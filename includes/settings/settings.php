<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

class BookedInSettings {

    private $db;
    private $charset_collate;
    public $settings_table = 'bookedin_settings';
    public $table_name;

    public function __construct() {
        global $wpdb;
        $this->db = $wpdb;
        $this->charset_collate = $this->db->get_charset_collate();
        $this->table_name = $this->db->prefix . $this->settings_table;
    }

    public function add_settings($setting_code, $setting_name, $setting_description, $setting_value, $activeFlag) {

        $this->db->insert($this->table_name, array(
            'setting_code' => $setting_code,
            'setting_name' => $setting_name,
            'setting_description' => $setting_description,
            'setting_value' => $setting_value,
            'activeFlag' => $activeFlag
        ));

        return $this->db->insert_id;
    }

    public function update_settings($setting_id, $setting_code,  $setting_name, $setting_description, $setting_value, $activeFlag) {

        $this->db->update($this->table_name, array(
            'setting_code' => $setting_code,
            'setting_name' => $setting_name,
            'setting_description' => $setting_description,
            'setting_value' => $setting_value,
            'activeFlag' => $activeFlag
        ), array('id' => $setting_id));

        return $this->db->insert_id;
    }

    public function get_settings($setting_id=null) {

        if ($setting_id) {
            $sql = "SELECT * FROM $this->table_name WHERE id = $setting_id";
        } else {
            $sql = "SELECT * FROM $this->table_name";
        }

        $result = $this->db->get_results($sql, 'ARRAY_A');

        return $result;
    }

    public function delete_settings($setting_id) {

        $this->db->delete($this->table_name, array('id' => $setting_id));

    }

    public function createSettingsDB() {
        $sql = "CREATE TABLE IF NOT EXISTS $this->table_name (
            id INT NOT NULL AUTO_INCREMENT,
            setting_code VARCHAR(255) NOT NULL,
            setting_name VARCHAR(255) NOT NULL,
            setting_description VARCHAR(255) NOT NULL,
            setting_value VARCHAR(255) NOT NULL,
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

    public function settings_activate(){
        
        $this->createSettingsDB();

    }

    public function settings_deactivate(){
        
        $this->deleteDB($this->table_name);

    }

}