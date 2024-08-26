<?php

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
