<?php

/**
 * 
 * Plugin Name: BookedIn
 * Description: A booking management system for WordPress
 * Version: 1.0.0
 * Author: Jason Han
 * 
 */

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

// Define constants
if (!defined('BI_VERSION_NUM')) define('BI_VERSION_NUM', '1.0.0');
if (!defined('BI_PLUGIN_PATH')) define('BI_PLUGIN_PATH', plugin_dir_path(__FILE__));
if (!defined('BI_PLUGIN_URL')) define('BI_PLUGIN_URL', plugin_dir_url( __FILE__ ));

require_once BI_PLUGIN_PATH . 'includes/bookedin.php';

?>