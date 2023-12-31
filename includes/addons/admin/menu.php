<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;


function bookedin_addons_submenu_page() {

    $addonsClass = new BookedInAddons();
    $pricingClass = new BookedInPricings();
    
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

    // Handle form submissions to add new addons
    if (isset($_POST['add_addon'])) {
        
        $addon_name = sanitize_text_field($_POST['addon_name']);
        $addon_price = sanitize_text_field($_POST['addon_price']);
        $addon_description = sanitize_textarea_field($_POST['addon_description']);
        $addon_perday = sanitize_text_field($_POST['addon_perday']);
        $addon_activeFlag = sanitize_text_field($_POST['addon_activeFlag']);

        $addonsClass->add_addon($addon_name, $addon_price, $addon_description, $addon_perday, $addon_activeFlag);
    }

    // Handle addon deletion
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['addon_id'])) {
        
        $addon_id = intval($_GET['addon_id']);

        $addonsClass->delete_addon($addon_id);
    }

    bookedInNavigation('Addons');
    ?>
    <div class="wrap">
        <br>
        <div class="container">
            <!-- Form to add new addons -->
            <h2>Add New addon</h2>
            <form method="post" action="">

                <div class="row">
                    <div class="col">
                        <label class="form-label" for="addon_name">Name:</label>
                        <input class="form-control" type="text" name="addon_name" required>

                        <div class="row mt-3">
                            <div class="col">
                                <label class="form-label" for="addon_price">Price:</label>
                                <select class="form-control" name="addon_price">
                                    <?php
                                        $pricings = $pricingClass->get_pricings();
                                        foreach ($pricings as $pricing) { ?>
                                        <option value="<?php echo $pricing['id']; ?>"><?php echo $pricing['pricing_name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col">
                                <label class="form-label" for="addon_perday">Per Day:</label>
                                <select class="form-control" name="addon_perday">
                                    <option value="Y">Yes</option>
                                    <option value="N">No</option>
                                </select>
                            </div>
                            <div class="col">
                                <label class="form-label" for="addon_activeFlag">Active:</label>
                                <select class="form-control" name="addon_activeFlag">
                                    <option value="Y">Yes</option>
                                    <option value="N">No</option>
                                </select>
                            </div>
                        </div>
                    
                    </div>
                    <div class="col">
                        <label class="form-label" for="addon_description">Description:</label>
                        <textarea class="form-control" name="addon_description"></textarea>
                    </div>
                </div>

                
                
                
                
                
                <br>
                <input class="btn btn-primary" type="submit" name="add_addon" value="Add addon">
            </form>
        </div>
        
        
        <br>
        <div class="container">
            <!-- Display existing addons -->
            <h2>Existing addons</h2>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Per Day</th>
                        <th>Active</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $addons = $addonsClass->get_addons();
                    foreach ($addons as $addon) { ?>
                        <tr>
                            <td><?php echo $addon['addon_name']; ?></td>
                            <td><?php echo $addon['addon_description']; ?></td>
                            <td><?php echo $addon['pricing_name']; ?></td>
                            <td><?php echo $addon['addon_perday']; ?></td>
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
        
    </div>
    <?php
    bookInFooter();
}