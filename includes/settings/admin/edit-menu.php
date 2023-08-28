<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

function setting_edit_page() {

    $settingsClass = new BookedInSettings();

    // Update setting
    if (isset($_POST['update_setting'])) {
        $setting_id = intval($_GET['setting_id']);
        $setting_name = sanitize_text_field($_POST['setting_name']);
        $setting_description = sanitize_text_field($_POST['setting_description']);
        $setting_value = sanitize_text_field($_POST['setting_value']);

        $settingsClass->update_settings($setting_id, $setting_name, $setting_description, $setting_value);

        wp_redirect(admin_url('admin.php?page=bookedin_setting_submenu'));
        exit;

    }

    // Get setting
    if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['setting_id'])) {
        $setting_id = intval($_GET['setting_id']);

        $setting = $settingsClass->get_settings($setting_id);
    }


    bookedInNavigation('Settings');
    ?>
    <div class="wrap">

        <!-- Form to add new addons -->
        <h2>Edit Setting</h2>
        <form method="post" action="">
            <label for="setting_name">Name:</label>
            <input type="text" name="setting_name" value="<?php echo $setting[0]['setting_name']; ?>" required>
            <label for="setting_description">Description:</label>
            <input type="text" name="setting_description" value="<?php echo $setting[0]['setting_description']; ?>" required>
            <label for="setting_value">Value:</label>
            <input type="text" name="setting_value" value="<?php echo $setting[0]['setting_value']; ?>" required>
            <input type="submit" name="update_setting" value="Update Setting">
        </form>
    <?php
    bookInFooter();
}