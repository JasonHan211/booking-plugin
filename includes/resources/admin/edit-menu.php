<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

function resources_edit_page() {
    
    $resourcesClass = new BookedInResources();

    if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['resource_id'])) {
        $resource_id = intval($_GET['resource_id']);
        $resource = $resourcesClass->get_resource($resource_id);
    }

    if (isset($_POST['update_resource'])) {
        $resource_name = sanitize_text_field($_POST['resource_name']);
        $resource_price = sanitize_text_field($_POST['resource_price']);
        $resource_description = sanitize_textarea_field($_POST['resource_description']);
        $resource_activeFlag = sanitize_text_field($_POST['resource_activeFlag']);

        $resourcesClass->update_resource($resource_id, $resource_name, $resource_price, $resource_description, $resource_activeFlag);

        wp_redirect(admin_url('admin.php?page=bookedin_resources_submenu'));
        exit;
    }

    bookedInNavigation('Resources');
    ?>
    <div class="wrap">
        <h1>Edit Resource</h1>
        <form method="post" action="">
            <label for="resource_name">resource Name:</label>
            <input type="text" name="resource_name" value="<?php echo esc_attr($resource['resource_name']); ?>" required>
            <label for="resource_price">resource Price:</label>
            <input type="text" name="resource_price" value="<?php echo esc_attr($resource['resource_price']); ?>" required>
            <label for="resource_description">resource_description:</label>
            <textarea name="resource_description"><?php echo esc_textarea($resource['resource_description']); ?></textarea>
            <label for="resource_activeFlag">Active:</label>
            <select name="resource_activeFlag">
                <option value="Y" <?php selected($resource['activeFlag'], 'Y'); ?>>Yes</option>
                <option value="N" <?php selected($resource['activeFlag'], 'N'); ?>>No</option>
            <input type="submit" name="update_resource" value="Update Resource">
        </form>
    </div>
    <?php
    bookInFooter();
}