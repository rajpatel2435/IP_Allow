<?php



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


   

    load_template(MY_PLUGIN_PATH.'template/form.php', true);


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

    $atts = array(
        'allowed_ips' => $allowed_ips,
    );

  
    load_template(MY_PLUGIN_PATH.'/template/input_form.php', true ,$atts);
    

}
