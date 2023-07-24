<?php

function bookedInHeader() {
    ?>
    <head>  
        <link rel="stylesheet" href="<?php echo BI_PLUGIN_URL . 'includes/templates/assets/css/style.css'; ?>">
    </head>
    <?php
}

function bookedInNavigation($activeNavTab) {

    bookedInHeader()
        
    ?>
        <div class="wrap">
            <h1>BookedIn</h1>
            <h2 class="nav-tab-wrapper">
                <a href="?page=bookedin_main_menu" class="nav-tab <?php echo ($activeNavTab === 'Dashboard') ? 'active' : ''; ?>">Dashboard</a>
                <a href="?page=bookedin_resources_submenu" class="nav-tab <?php echo ($activeNavTab === 'Resources') ? 'active' : ''; ?>">Resources</a>
                <!-- <a href="?page=bookedin_pricing_submenu" class="nav-tab <?php echo ($activeNavTab === 'Pricing') ? 'active' : ''; ?>">Pricing</a> -->
                <a href="?page=bookedin_contact_form_submenu" class="nav-tab <?php echo ($activeNavTab === 'Contact Form') ? 'active' : ''; ?>">Contact Form</a>
                <!-- <a href="?page=bookedin_setting_submenu" class="nav-tab <?php echo ($activeNavTab === 'Settings') ? 'active' : ''; ?>">Settings</a> -->
            </h2>
        </div>

    <?php
}

function bookInFooter() {
    ?>
        <script src="<?php echo BI_PLUGIN_URL . 'includes/templates/assets/js/script.js'; ?>"></script>
    <?php
}