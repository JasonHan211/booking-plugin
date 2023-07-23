<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

function resources_edit_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'bookedin_resources';

    if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['resource_id'])) {
        $resource_id = intval($_GET['resource_id']);
        $resource = $wpdb->get_row("SELECT * FROM $table_name WHERE id = $resource_id", ARRAY_A);
    }

    if (isset($_POST['update_resource'])) {
        $resource_name = sanitize_text_field($_POST['resource_name']);
        $resource_description = sanitize_textarea_field($_POST['resource_description']);

        $wpdb->update($table_name, array(
            'resource_name' => $resource_name,
            'resource_description' => $resource_description
        ), array('id' => $resource_id));

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
            <label for="resource_description">resource_description:</label>
            <textarea name="resource_description"><?php echo esc_textarea($resource['resource_description']); ?></textarea>
            <input type="submit" name="update_resource" value="Update Resource">
        </form>
    </div>
    <?php
    bookInFooter();
}