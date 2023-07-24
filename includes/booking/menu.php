<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

// Function to display the option page and bookings
function my_booking_plugin_option_page() {
    
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

    global $wpdb;
    $bookings_table_name = $wpdb->prefix . 'bookedin_bookings';
    $resources_table_name = $wpdb->prefix . 'bookedin_resources';

    // Handle form submissions to add new bookings
    if (isset($_POST['add_booking'])) {
        $booking_date_from = sanitize_text_field($_POST['booking_date_from']);
        $booking_date_to = sanitize_text_field($_POST['booking_date_to']);
        $booking_resource = sanitize_text_field($_POST['booking_resource']);
        $booking_description = sanitize_textarea_field($_POST['booking_description']);
        
        $booking_user = sanitize_text_field($_POST['booking_user']);
        $booking_email = sanitize_text_field($_POST['booking_email']);
        $booking_phone = sanitize_text_field($_POST['booking_phone']);
        $booking_user_details = sanitize_textarea_field($_POST['booking_user_details']);

        $wpdb->insert($bookings_table_name, array(
            'booking_date_from' => $booking_date_from,
            'booking_date_to' => $booking_date_to,
            'booking_resource' => $booking_resource,
            'booking_description' => $booking_description,
            
            'booking_user' => $booking_user,
            'booking_email' => $booking_email,
            'booking_phone' => $booking_phone,
            'booking_user_details' => $booking_user_details
        ));
            
    }

    // Handle booking deletion
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['booking_id'])) {
        $booking_id = intval($_GET['booking_id']);
        $wpdb->delete($bookings_table_name, array('id' => $booking_id));
    }

    // Fetch all bookings from the database
    $bookings = $wpdb->get_results("SELECT * FROM $bookings_table_name LEFT JOIN $resources_table_name on $resources_table_name.id = $bookings_table_name.booking_resource", ARRAY_A);

    $resources = $wpdb->get_results("SELECT id, resource_name FROM $resources_table_name", ARRAY_A);
    
    bookedInNavigation('Dashboard');
    ?>
    <div class="wrap">

        <!-- Form to add new bookings -->
        <h2>Add New booking</h2>
        <form method="post" action="">
            <label for="booking_date_from">Date From:</label>
            <input type="date" name="booking_date_from" required>
            <label for="booking_date_to">Date To:</label>
            <input type="date" name="booking_date_to" required>
            <label for="booking_resource">Resource:</label>
            <select name="booking_resource" required>
                <?php if( sizeof($resources) == 0) {?>
                <option value="">Please create a resource</option>
                <?php } else { foreach($resources as $resource) {?>
                <option value="<?php echo $resource['id']; ?>"><?php echo $resource['resource_name']; ?></option>
                <?php }} ?>
            </select>
            <label for="booking_description">Description:</label>
            <textarea name="booking_description"></textarea>
            <br>
            <br>
            <label for="booking_user">Name:</label>
            <input type="text" name="booking_user" required>
            <label for="booking_email">Email:</label>
            <input type="email" name="booking_email" required>
            <label for="booking_phone">Phone:</label>
            <input type="text" name="booking_phone" required>
            <label for="booking_user_details">User details:</label>
            <textarea name="booking_user_details"></textarea>
            
            <input type="submit" name="add_booking" value="Add booking">


        </form>

        <!-- Display existing bookings -->
        <h2>Existing bookings</h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Date From</th>
                    <th>Date To</th>
                    <th>Resource</th>
                    <th>Description</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>User details</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $booking) { ?>
                    <tr>
                        <td><?php echo $booking['booking_date_from']; ?></td>
                        <td><?php echo $booking['booking_date_to']; ?></td>
                        <td><?php echo $booking['resource_name']; ?></td>
                        <td><?php echo $booking['booking_description']; ?></td>
                        <td><?php echo $booking['booking_user']; ?></td>
                        <td><?php echo $booking['booking_email']; ?></td>
                        <td><?php echo $booking['booking_phone']; ?></td>
                        <td><?php echo $booking['booking_user_details']; ?></td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=bookedin_booking_edit&action=edit&booking_id=' . $booking['id']); ?>">Edit</a> |
                            <a href="<?php echo admin_url('admin.php?page=bookedin_main_menu&action=delete&booking_id=' . $booking['id']); ?>" onclick="return confirm('Are you sure you want to delete this booking?')">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php
    bookInFooter();
}