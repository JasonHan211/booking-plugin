<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

function bookedin_resources_submenu_page() {
    
    $resourcesClass = new BookedInResources();
    $pricingClass = new BookedInPricings();

    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

    // Handle form submissions to add new resources
    if (isset($_POST['add_resource'])) {
        $resource_name = sanitize_text_field($_POST['resource_name']);
        $resource_price = sanitize_text_field($_POST['resource_price']);
        $resource_description = sanitize_textarea_field($_POST['resource_description']);
        $resource_activeFlag = sanitize_text_field($_POST['resource_activeFlag']);

        $resourcesClass->add_resource($resource_name, $resource_price, $resource_description, $resource_activeFlag);
    }

    // Handle resource deletion
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['resource_id'])) {
        $resource_id = intval($_GET['resource_id']);
        
        $resourcesClass->delete_resource($resource_id);
    }

    bookedInNavigation('Resources');
    ?>
    <div class="wrap">
        <br>
        <div class="container">
        <!-- Form to add new resources -->
        <h2>Add New Resource</h2>
            <form method="post" action="">
                <div class="row">
                    <div class="col">

                        <label class="form-label" for="resource_name">Name:</label>
                        <input class="form-control" type="text" name="resource_name" required>
                        
                        <div class="row mt-3">
                            <div class="col">
                                <label class="form-label" for="resource_price">Price:</label>
                                <select class="form-control" name="resource_price">
                                    <?php
                                        $pricings = $pricingClass->get_pricings();
                                        foreach ($pricings as $pricing) { ?>
                                        <option value="<?php echo $pricing['id']; ?>"><?php echo $pricing['pricing_name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col">
                                <label class="form-label" for="resource_activeFlag">Active:</label>
                                <select class="form-control" name="resource_activeFlag">
                                    <option value="Y">Yes</option>
                                    <option value="N">No</option>
                                </select>
                            </div>
                        </div>
                        
                    </div>
                    <div class="col">
                        <label class="form-label" for="resource_description">Description:</label>
                        <textarea class="form-control" name="resource_description"></textarea>
                    </div>
                </div>
                
                <br>
                <input class="btn btn-primary" type="submit" name="add_resource" value="Add Resource">
                
                
                
            </form>
        </div>
        
        <br>
        <div class="container">
        <!-- Display existing resources -->
        <h2>Existing Resources</h2>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Active</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $resources = $resourcesClass->get_resources();
                        foreach ($resources as $resource) { ?>
                        <tr>
                            <td><?php echo $resource['resource_name']; ?></td>
                            <td><?php echo $resource['resource_description']; ?></td>
                            <td><?php echo $resource['pricing_name']; ?></td>
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
        
    </div>
    <?php
    bookInFooter();
}