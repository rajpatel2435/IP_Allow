<?php
/**
 * Plugin Name: IP Allow
 * Description: A plugin that adds capabilities to allow access from specified IPs and block all others.
 * Version: 1.0.0
 * Author: Raj Patel
 */

//load the file

if (!defined('MY_PLUGIN_PATH')) {
    define('MY_PLUGIN_PATH', plugin_dir_path(__FILE__));
}

// Prevent direct access to the file
if (!defined('ABSPATH')) {
    exit;
}

require_once MY_PLUGIN_PATH . 'base/Admin_menu.php';
require_once MY_PLUGIN_PATH . 'base/Activation.php';
require_once MY_PLUGIN_PATH . 'base/Uninstall.php';
require_once MY_PLUGIN_PATH . 'base/Actions.php';
require_once MY_PLUGIN_PATH . 'base/Allow_ip.php';

// Activation script
register_activation_hook(__FILE__, 'ip_allow_activate');

// Uninstall plugin and delete options
register_uninstall_hook(__FILE__, 'ip_allow_uninstall');

// Register settings for admin section
add_action('admin_init', 'ip_allow_settings_init');

// Add settings page to the admin menu
add_action('admin_menu', 'ip_allow_add_admin_menu');
