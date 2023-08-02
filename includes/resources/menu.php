<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

function bookedin_resources_submenu_page() {
    
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'bookedin_resources';

    // Handle form submissions to add new resources
    if (isset($_POST['add_resource'])) {
        $resource_name = sanitize_text_field($_POST['resource_name']);
        $resource_price = sanitize_text_field($_POST['resource_price']);
        $resource_description = sanitize_textarea_field($_POST['resource_description']);
        $resource_activeFlag = sanitize_text_field($_POST['resource_activeFlag']);

        $wpdb->insert($table_name, array(
            'resource_name' => $resource_name,
            'resource_price' => $resource_price,
            'resource_description' => $resource_description,
            'activeFlag' => $resource_activeFlag
        ));
    }

    // Handle resource deletion
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['resource_id'])) {
        $resource_id = intval($_GET['resource_id']);
        $wpdb->delete($table_name, array('id' => $resource_id));
    }

    // Fetch all resources from the database
    $resources = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
    
    bookedInNavigation('Resources');
    ?>
    <div class="wrap">

        <!-- Form to add new resources -->
        <h2>Add New Resource</h2>
        <form method="post" action="">
            <label for="resource_name">Name:</label>
            <input type="text" name="resource_name" required>
            <label for="resource_price">Price:</label>
            <input type="text" name="resource_price" required>
            <label for="resource_description">Description:</label>
            <textarea name="resource_description"></textarea>
            <label for="resource_activeFlag">Active:</label>
            <select name="resource_activeFlag">
                <option value="Y">Yes</option>
                <option value="N">No</option>
            <input type="submit" name="add_resource" value="Add Resource">
        </form>

        <!-- Display existing resources -->
        <h2>Existing Resources</h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Description</th>
                    <th>Active</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($resources as $resource) { ?>
                    <tr>
                        <td><?php echo $resource['resource_name']; ?></td>
                        <td><?php echo $resource['resource_price']; ?></td>
                        <td><?php echo $resource['resource_description']; ?></td>
                        <td><?php echo $resource['activeFlag']; ?></td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=bookedin_resources_edit&action=edit&resource_id=' . $resource['id']); ?>">Edit</a> |
                            <a href="<?php echo admin_url('admin.php?page=bookedin_resources_submenu&action=delete&resource_id=' . $resource['id']); ?>" onclick="return confirm('Are you sure you want to delete this resource?')">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php
    bookInFooter();
}