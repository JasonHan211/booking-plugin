<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

function addons_edit_page() {

    $addonsClass = new BookedInAddons();

    global $wpdb;
    $table_name = $wpdb->prefix . 'bookedin_addons';

    if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['addon_id'])) {
        $addon_id = intval($_GET['addon_id']);

        $addon = $addonsClass->get_addon($addon_id);
    }

    if (isset($_POST['update_addon'])) {
        $addon_name = sanitize_text_field($_POST['addon_name']);
        $addon_price = sanitize_text_field($_POST['addon_price']);
        $addon_description = sanitize_textarea_field($_POST['addon_description']);
        $addon_activeFlag = sanitize_text_field($_POST['addon_activeFlag']);

        $addonsClass->update_addon($addon_id, $addon_name, $addon_price, $addon_description, $addon_activeFlag);

        wp_redirect(admin_url('admin.php?page=bookedin_addons_submenu'));
        exit;
    }

    bookedInNavigation('Addons');
    ?>
    <div class="wrap">
        <h1>Edit addon</h1>
        <form method="post" action="">
            <label for="addon_name">addon Name:</label>
            <input type="text" name="addon_name" value="<?php echo esc_attr($addon['addon_name']); ?>" required>
            <label for="addon_price">addon Price:</label>
            <input type="text" name="addon_price" value="<?php echo esc_attr($addon['addon_price']); ?>" required>
            <label for="addon_description">addon_description:</label>
            <textarea name="addon_description"><?php echo esc_textarea($addon['addon_description']); ?></textarea>
            <label for="addon_activeFlag">Active:</label>
            <select name="addon_activeFlag">
                <option value="Y" <?php selected($addon['activeFlag'], 'Y'); ?>>Yes</option>
                <option value="N" <?php selected($addon['activeFlag'], 'N'); ?>>No</option>
            <input type="submit" name="update_addon" value="Update addon">
        </form>
    </div>
    <?php
    bookInFooter();
}