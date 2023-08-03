<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;


function bookedin_addons_submenu_page() {

    $addonsClass = new BookedInAddons();
    
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

    // Handle form submissions to add new addons
    if (isset($_POST['add_addon'])) {
        
        $addon_name = sanitize_text_field($_POST['addon_name']);
        $addon_price = sanitize_text_field($_POST['addon_price']);
        $addon_description = sanitize_textarea_field($_POST['addon_description']);
        $addon_activeFlag = sanitize_text_field($_POST['addon_activeFlag']);

        $addonsClass->add_addon($addon_name, $addon_price, $addon_description, $addon_activeFlag);
    }

    // Handle addon deletion
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['addon_id'])) {
        
        $addon_id = intval($_GET['addon_id']);

        $addonsClass->delete_addon($addon_id);
    }

    // Fetch all addons from the database
    $addons = $addonsClass->get_addon();
    
    bookedInNavigation('Addons');
    ?>
    <div class="wrap">

        <!-- Form to add new addons -->
        <h2>Add New addon</h2>
        <form method="post" action="">
            <label for="addon_name">Name:</label>
            <input type="text" name="addon_name" required>
            <label for="addon_price">Price:</label>
            <input type="text" name="addon_price" required>
            <label for="addon_description">Description:</label>
            <textarea name="addon_description"></textarea>
            <label for="addon_activeFlag">Active:</label>
            <select name="addon_activeFlag">
                <option value="Y">Yes</option>
                <option value="N">No</option>
            <input type="submit" name="add_addon" value="Add addon">
        </form>

        <!-- Display existing addons -->
        <h2>Existing addons</h2>
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
                <?php foreach ($addons as $addon) { ?>
                    <tr>
                        <td><?php echo $addon['addon_name']; ?></td>
                        <td><?php echo $addon['addon_price']; ?></td>
                        <td><?php echo $addon['addon_description']; ?></td>
                        <td><?php echo $addon['activeFlag']; ?></td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=bookedin_addons_edit&action=edit&addon_id=' . $addon['id']); ?>">Edit</a> |
                            <a href="<?php echo admin_url('admin.php?page=bookedin_addons_submenu&action=delete&addon_id=' . $addon['id']); ?>" onclick="return confirm('Are you sure you want to delete this addon?')">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php
    bookInFooter();
}