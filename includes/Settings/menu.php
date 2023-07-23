<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

function bookedin_setting_submenu_page() {
    // Add your submenu page content here
    bookedInNavigation('Settings');
    ?>

    <div class="wrap">
        <h1>Settings</h1>
        <p>Settings page content goes here</p>
    </div>

    <?php
}