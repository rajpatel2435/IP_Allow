<?php


function ip_allow_uninstall()
{
    // Remove options or perform other uninstallation tasks
    delete_option('allowed_ips');
}