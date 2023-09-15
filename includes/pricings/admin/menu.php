<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

function bookedin_pricings_submenu_page() {

    $pricingClass = new BookedInpricings();
    
    include_once('templates/add-pricing.php');
    include_once('templates/add-discount.php');

    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

    // Handle pricing deletion
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['pricing_id'])) {
        $pricing_id = intval($_GET['pricing_id']);
        
        $pricingClass->delete_pricing($pricing_id);

    }

    // Handle discount deletion
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['discount_id'])) {
        $discount_id = intval($_GET['discount_id']);
        
        $pricingClass->delete_discount($discount_id);

    }

    bookedInNavigation('Pricing');
    ?>
    <div class="wrap">
        <div class="row">
            <div class="col">
                <?php addPricingForm(); ?>
            </div>
            <div class="col">
                <?php addDiscountForm(); ?>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col m-1 table-responsive">
                <!-- Display existing pricings -->
                <h2>Existing Pricings</h2>
                <table class="wp-list-table half-page-table widefat fixed striped">
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
                        <?php
                            $pricings = $pricingClass->get_pricings();
                            foreach ($pricings as $pricing) { ?>
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
            <div class="col m-1 table-responsive">
                <!-- Display existing pricings -->
                <h2>Existing Discounts</h2>
                <table class="wp-list-table half-page-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Code</th>
                            <th>Quantity</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>On Type</th>
                            <th>On ID</th>
                            <th>Condition</th>
                            <th>Condition Start</th>
                            <th>Condition End</th>
                            <th>Auto Apply</th>
                            <th>Active</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $discounts = $pricingClass->get_discounts();
                        foreach ($discounts as $discount) { ?>
                            <tr>
                                <td><?php echo $discount['discount_name']; ?></td>
                                <td><?php echo $discount['discount_description']; ?></td>
                                <td><?php echo $discount['discount_code']; ?></td>
                                <td><?php echo $discount['discount_quantity']; ?></td>
                                <td><?php echo $discount['discount_type']; ?></td>
                                <td><?php echo $discount['discount_amount']; ?></td>
                                <td><?php echo $discount['discount_start_date']; ?></td>
                                <td><?php echo $discount['discount_end_date']; ?></td>
                                <td><?php echo $discount['discount_on_type']; ?></td>
                                <td><?php echo $discount['discount_on_id']; ?></td>
                                <td><?php echo $discount['discount_condition']; ?></td>
                                <td><?php echo $discount['discount_condition_start']; ?></td>
                                <td><?php echo $discount['discount_condition_end']; ?></td>
                                <td><?php echo $discount['discount_auto_apply']; ?></td>
                                <td><?php echo $discount['discount_active']; ?></td>
                                <td>
                                    <a href="<?php echo admin_url('admin.php?page=bookedin_pricings_edit&action=edit&discount_id=' . $discount['id']); ?>">Edit</a> |
                                    <a href="<?php echo admin_url('admin.php?page=bookedin_pricings_submenu&action=delete&discount_id=' . $discount['id']); ?>" onclick="return confirm('Are you sure you want to delete this pricing?')">Delete</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php
    bookInFooter();
}