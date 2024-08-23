<?php
/**
 * Plugin Name: IP Allow
 * Description: A plugin that adds capabilities to allow access from specified IPs and block all others.
 * Version: 1.0.0
 * Author: Raj Patel
 */

// Prevent direct access to the file
if (!defined('ABSPATH')) {
    exit;
}

// Activation script
register_activation_hook(__FILE__, 'ip_allow_activate');

function ip_allow_activate()
{
    // Set default options or perform other activation tasks
    $default_allowed_ips = array();
    add_option('allowed_ips', $default_allowed_ips);
}

// Uninstall plugin and delete options
register_uninstall_hook(__FILE__, 'ip_allow_uninstall');

function ip_allow_uninstall()
{
    // Remove options or perform other uninstallation tasks
    delete_option('allowed_ips');
}

// Register settings for admin section
add_action('admin_init', 'ip_allow_settings_init');
function ip_allow_settings_init()
{
    // Register a setting for allowed IPs
    // blocked ips will be registerd under setting
    // 1) 1st add option for setting
    // section and then field
    // Register a setting for allowed IPs

    register_setting('ip_allow_settings', 'allowed_ips', 'sanitize_allowed_ips');

    // / set ips in option table inside allow_ips
    add_settings_section(
        'ip_allow_section',
        'IP Allow Settings',
        'ip_allow_section_callback',
        'ip_allow'
    );

    // Add a settings field
    // add_setting_field('unique_id', label, callabck (render html),appear field  ,and again id where do we need tp store
    add_settings_field(
        'ip_allow_field',
        'Allowed IP Addresses',
        'ip_allow_field_callback',
        'ip_allow',
        'ip_allow_section'
    );
}

// Sanitize the allowed IP addresses input
function sanitize_allowed_ips($input)
{
    if (is_string($input)) {
        // Convert a single IP address string into an array
        $input = array_map('trim', explode("\n", $input));
    }

    // Remove empty values and validate IPs
    $input = array_filter(array_map('sanitize_text_field', $input), function ($ip) {
        return filter_var($ip, FILTER_VALIDATE_IP) !== false;
    });

    return $input;
}

// Section callback function
function ip_allow_section_callback()
{
    echo '<p>Enter the IP addresses you want to allow access to. All other IPs will be blocked.</p>';
}

// Field callback function
function ip_allow_field_callback()
{
    $allowed_ips = get_option('allowed_ips', array());

    // Ensure $allowed_ips is always an array
    if (is_string($allowed_ips)) {
        $allowed_ips = array_map('trim', explode("\n", $allowed_ips));
    } else {
        $allowed_ips = is_array($allowed_ips) ? $allowed_ips : array();
    }

    ?>
    <p class="notice">Enter one IP address per line.</p>

       <!-- diplay the value from get options and they can also add new -->

    <textarea name="allowed_ips" rows="10" cols="50" class="large-text"><?php echo esc_textarea(implode("\n", $allowed_ips)); ?></textarea>
    <?php
}

// Add settings page to the admin menu
add_action('admin_menu', 'ip_allow_add_admin_menu');
function ip_allow_add_admin_menu()
{
    add_options_page(
        'IP Allow Settings',
        'IP Allow',
        'manage_options',
        'ip_allow',
        'ip_allow_options_page'
    );
}

// Display settings page
function ip_allow_options_page()
{
    ?>
    <div class="ip_allow_waper">
        <h1>IP Allow Settings</h1>
        <!-- Post the data to options file -->
        <form action="options.php" method="post">
            <?php
    settings_fields('ip_allow_settings');
    do_settings_sections('ip_allow');
    submit_button();
    ?>
        </form>
    </div>
    <?php
}

// Check if IP is in allowed listadd_action('template_redirect', 'ip_allow_check_ip');
function ip_allow_check_ip()
{
    // Get the current site URL
    $site_url = get_site_url();

    // Check if the site URL contains 'hstreetmedia.net' or 'cloudwaysapps.com'
    if (strpos($site_url, 'hstreetmedia.net') !== false || strpos($site_url, 'cloudwaysapps.com') !== false) {

        // Define the list of allowed IP addresses
        $white_listed_ips = array(
            '24.202.251.227',
            '70.25.44.209',
            '96.22.143.15',
            '142.119.3.5',
            '173.246.80.69',
            '174.112.14.206',
            '184.163.112.176',
            '185.213.220.2',
            '185.213.221.5',
            '190.5.212.31',
            '190.14.202.2',
            '190.171.98.66',
            '190.171.98.68',
            '190.242.68.34',
            '201.218.220.5',
            '201.218.220.15',
            '207.81.214.196',
            '207.81.214.197',
            '207.81.214.220',
            '64.18.81.0/24',
        );

        // Get the current IP address
        $current_ip = $_SERVER['REMOTE_ADDR'];

        // Fetch the allowed IPs from options table
        $allowed_ips = get_option('allowed_ips', array());

        // Convert to array
        if (is_string($allowed_ips)) {
            $allowed_ips = array_map('trim', explode("\n", $allowed_ips));
        } else {
            $allowed_ips = array_map('trim', (array) $allowed_ips);
        }

        // Check if the visitor's IP address is in the allowed list
        $allowed = false;
        foreach ($allowed_ips as $ip) {
            if (strpos($ip, '/') !== false) {
                if (ip_in_range($current_ip, $ip)) {
                    $allowed = true;
                    break;
                }
            } elseif ($ip === $current_ip) {
                $allowed = true;
                break;
            }
        }

        // Allow access if the IP is in the allowed list or in the white-listed IPs
        if ($allowed || in_array($current_ip, $white_listed_ips)) {
            return;
        }

        // Display 403 Forbidden error if IP is not allowed
        wp_die('403 Forbidden', 'Access Denied', array('response' => 403));
    }
}

// Function to check if an IP is in a given range
// need to check if it si working or not
function ip_in_range($ip, $range)
{
    // check if given the ip start with / to find subnet domain
    list($range, $mask) = explode('/', $range);
    // check for range
    // ip2long converts ip address frim standar dotted
    $range = ip2long($range);
    $ip = ip2long($ip);
    $mask = 0xffffffff << (32 - $mask);
    $range &= $mask;
    return ($ip & $mask) == $range;
}
