<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

function bookedin_pricings_submenu_page() {

    $pricingClass = new BookedInpricings();
    
    include_once('templates/add-pricing.php');

    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
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

        <?php addPricingForm(); ?>

        <!-- Display existing pricings -->
        <h2>Existing pricings</h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Structure</th>
                    <th>Active</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pricings as $pricing) { ?>
                    <tr>
                        <td><?php echo $pricing['pricing_name']; ?></td>
                        <td><?php echo $pricing['pricing_description']; ?></td>
                        <td><?php echo $pricing['pricing_structure']; ?></td>
                        <td><?php echo $pricing['pricing_active']; ?></td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=bookedin_pricings_edit&action=edit&pricing_id=' . $pricing['id']); ?>">Edit</a> |
                            <a href="<?php echo admin_url('admin.php?page=bookedin_pricings_submenu&action=delete&pricing_id=' . $pricing['id']); ?>" onclick="return confirm('Are you sure you want to delete this pricing?')">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php
    bookInFooter();
}