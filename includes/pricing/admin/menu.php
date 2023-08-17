<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

function bookedin_pricing_submenu_page() {

    $pricingClass = new BookedInpricings();
    
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

    // Handle form submissions to add new pricings
    if (isset($_POST['add_pricing'])) {
        $pricing_name = sanitize_text_field($_POST['pricing_name']);
        $pricing_description = sanitize_textarea_field($_POST['pricing_description']);

        $pricingClass->add_pricing($pricing_name, $pricing_description);
        
    }

    // Handle pricing deletion
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['pricing_id'])) {
        $pricing_id = intval($_GET['pricing_id']);
        
        $pricingClass->delete_pricing($pricing_id);

    }

    // Fetch all pricings from the database
    $pricings = $pricingClass->get_pricings();
    
    bookedInNavigation('Pricing');
    ?>
    <div class="wrap">

        <!-- Form to add new pricings -->
        <h2>Add New pricing</h2>
        <form method="post" action="">
            <label for="pricing_name">Name:</label>
            <input type="text" name="pricing_name" required>
            <label for="pricing_description">Description:</label>
            <textarea name="pricing_description"></textarea>
            <input type="submit" name="add_pricing" value="Add pricing">
        </form>

        <!-- Display existing pricings -->
        <h2>Existing pricings</h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pricings as $pricing) { ?>
                    <tr>
                        <td><?php echo $pricing['pricing_name']; ?></td>
                        <td><?php echo $pricing['pricing_description']; ?></td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=bookedin_pricing_edit&action=edit&pricing_id=' . $pricing['id']); ?>">Edit</a> |
                            <a href="<?php echo admin_url('admin.php?page=bookedin_pricing_submenu&action=delete&pricing_id=' . $pricing['id']); ?>" onclick="return confirm('Are you sure you want to delete this pricing?')">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php
    bookInFooter();
}