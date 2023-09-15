<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

function bookedin_setting_submenu_page() {
    
    $settingsClass = new BookedInSettings();

    // Add setting
    if (isset($_POST['add_setting'])) {
        $setting_code = sanitize_text_field($_POST['setting_code']);
        $setting_name = sanitize_text_field($_POST['setting_name']);
        $setting_description = sanitize_text_field($_POST['setting_description']);
        $setting_value = sanitize_text_field($_POST['setting_value']);
        $activeFlag = sanitize_text_field($_POST['activeFlag']);

        $settingsClass->add_settings($setting_code,$setting_name, $setting_description, $setting_value, $activeFlag);

    }

    // Delete setting
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['setting_id'])) {
        $setting_id = intval($_GET['setting_id']);

        $settingsClass->delete_settings($setting_id);
    }

    bookedInNavigation('Settings');
    ?>
    <div class="container">
        <br>
        <!-- Form to add new addons -->
        <h2>Add New Settings</h2>
        <form method="post" action="">
            <div class="row">
                <div class="col">
                    <label for="setting_code">Code:</label>
                    <input class="form-control" type="text" name="setting_code" value="" required>
                </div>
                <div class="col">
                    <label class="form-label" for="setting_name">Name:</label>
                    <input class="form-control" type="text" name="setting_name" value="" required>
                </div>
                <div class="col">
                    <label class="form-label" for="setting_description">Description:</label>
                    <input class="form-control" type="text" name="setting_description" value="" required>
                </div>
                <div class="col">
                    <label class="form-label" for="setting_value">Value:</label>
                    <input class="form-control" type="text" name="setting_value" value="" required>
                </div>
                <div class="col">
                    <label class="form-label" for="activeFlag">Active:</label>
                    <select class="form-control" name="activeFlag">
                        <option value="Y">Yes</option>
                        <option value="N">No</option>
                    </select>
                </div>
            </div>
            <br>
            <input class="btn btn-primary" type="submit" name="add_setting" value="Add Setting">
        </form>

        <br><br>
        <!-- Display existing addons -->
        <h2>Existing Settings</h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Value</th>
                    <th>Active</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $settings = $settingsClass->get_settings();
                foreach ($settings as $setting) {
                    ?>
                    <tr>
                        <td><?php echo $setting['setting_name']; ?></td>
                        <td><?php echo $setting['setting_description']; ?></td>
                        <td><?php echo $setting['setting_value']; ?></td>
                        <td><?php echo $setting['activeFlag']; ?></td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=bookedin_setting_edit&action=edit&setting_id=' . $setting['id']); ?>">Edit</a> |
                            <a href="<?php echo admin_url('admin.php?page=bookedin_setting_submenu&action=delete&setting_id=' . $setting['id']); ?>">Delete</a>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php
    bookInFooter();
}