<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

function setting_edit_page() {

    $settingsClass = new BookedInSettings();

    // Update setting
    if (isset($_POST['update_setting'])) {
        $setting_id = intval($_GET['setting_id']);
        $setting_code = sanitize_text_field($_POST['setting_code']);
        $setting_name = sanitize_text_field($_POST['setting_name']);
        $setting_description = sanitize_text_field($_POST['setting_description']);
        $setting_value = sanitize_text_field($_POST['setting_value']);
        $activeFlag = sanitize_text_field($_POST['activeFlag']);

        $settingsClass->update_settings($setting_id, $setting_code, $setting_name, $setting_description, $setting_value, $activeFlag);

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
    <div class="container">
        <br>
        <!-- Form to add new addons -->
        <h2>Edit Setting</h2>
        <form method="post" action="">
            <div class="row">
                <div class="col">
                    <label for="setting_code">Code:</label>
                    <input class="form-control" type="text" name="setting_code" value="<?php echo $setting[0]['setting_code']; ?>" required>
                </div>
                <div class="col">
                    <label class="form-label" for="setting_name">Name:</label>
                    <input class="form-control" type="text" name="setting_name" value="<?php echo $setting[0]['setting_name']; ?>" required>
                </div>
                <div class="col">
                    <label class="form-label" for="setting_description">Description:</label>
                    <input class="form-control" type="text" name="setting_description" value="<?php echo $setting[0]['setting_description']; ?>" required>
                </div>
                <div class="col">
                    <label class="form-label" for="setting_value">Value:</label>
                    <input class="form-control" type="text" name="setting_value" value="<?php echo $setting[0]['setting_value']; ?>" required>
                </div>
                <div class="col">
                    <label class="form-label" for="activeFlag">Active:</label>
                    <select class="form-control" name="activeFlag">
                        <option value="Y" <?php if ($setting[0]['activeFlag'] === 'Y') echo 'selected'; ?>>Yes</option>
                        <option value="N" <?php if ($setting[0]['activeFlag'] === 'N') echo 'selected'; ?>>No</option>
                    </select>
                </div>
            </div>

            <br>
            <input class="btn btn-primary" type="submit" name="update_setting" value="Update Setting">
        </form>
    <?php
    bookInFooter();
}