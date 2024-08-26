<?php




// Function to get the client's IP address
function get_client_ip()
{
    $ip_keys = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR');
    foreach ($ip_keys as $key) {
        if (array_key_exists($key, $_SERVER)) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
    }
    return 'UNKNOWN';
}

// Check if IP is in allowed list

add_action('template_redirect', 'ip_allow_check_ip');
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

        $current_ip = get_client_ip();

        // Fetch the allowed IPs from options table
        $allowed_ips = get_option('allowed_ips', array());

        if (is_string($allowed_ips)) {
            $allowed_ips = array_map('trim', explode("\n", $allowed_ips));
        } else {
            $allowed_ips = array_map('trim', (array) $allowed_ips);
        }

        // combined ip

        $all_allowed_ips = array_merge($allowed_ips, $white_listed_ips);

        // Check if the visitor's IP address is in the allowed list
        $allowed = false;
        foreach ($all_allowed_ips as $ip) {
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
        if ($allowed) {
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

    if (strpos($range, '/') === false) {
        return false; // Invalid range
    }

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
