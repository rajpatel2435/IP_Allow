<?php


function ip_allow_activate()
{
    // Set default options or perform other activation tasks
    $default_allowed_ips = array();
    add_option('allowed_ips', $default_allowed_ips);
}