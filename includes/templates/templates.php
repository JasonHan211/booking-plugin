<?php

function bookedInHeader() {
    ?>
    <head>  
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
        
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
                <a href="?page=bookedin_addons_submenu" class="nav-tab <?php echo ($activeNavTab === 'Addons') ? 'active' : ''; ?>">Addons</a>
                <a href="?page=bookedin_pricings_submenu" class="nav-tab <?php echo ($activeNavTab === 'Pricing') ? 'active' : ''; ?>">Pricing</a>
                <a href="?page=bookedin_contact_form_submenu" class="nav-tab <?php echo ($activeNavTab === 'Contact Form') ? 'active' : ''; ?>">Contact Form</a>
                <!-- <a href="?page=bookedin_setting_submenu" class="nav-tab <?php echo ($activeNavTab === 'Settings') ? 'active' : ''; ?>">Settings</a> -->
            </h2>
        </div>

    <?php
}

function bookInFooter() {
    ?>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
        <script src="<?php echo BI_PLUGIN_URL . 'includes/templates/assets/js/script.js'; ?>"></script>
    <?php
}