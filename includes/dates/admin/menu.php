<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

function bookedin_date_submenu_page() {
    
    $datesClass = new BookedInDates();

    // Add date
    if (isset($_POST['add_date'])) {
        $date_name = sanitize_text_field($_POST['date_name']);
        $date_description = sanitize_text_field($_POST['date_description']);
        $date_value = sanitize_text_field($_POST['date_value']);

        $datesClass->add_dates($date_name, $date_description, $date_value);

    }

    // Delete date
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['date_id'])) {
        $date_id = intval($_GET['date_id']);

        $datesClass->delete_dates($date_id);
    }

    bookedInNavigation('Dates');
    ?>
    <div class="wrap">

        <!-- Form to add new addons -->
        <h2>Add New Dates</h2>
        <form method="post" action="">
            <label for="date_name">Name:</label>
            <input type="text" name="date_name" value="" required>
            <label for="date_description">Description:</label>
            <input type="text" name="date_description" value="" required>
            <label for="date_value">Value:</label>
            <input type="text" name="date_value" value="" required>
            <input type="submit" name="add_date" value="Add Date">

        <!-- Display existing addons -->
        <h2>Existing Dates</h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Value</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $dates = $datesClass->get_dates();
                foreach ($dates as $date) {
                    ?>
                    <tr>
                        <td><?php echo $date['date_name']; ?></td>
                        <td><?php echo $date['date_description']; ?></td>
                        <td><?php echo $date['date_value']; ?></td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=bookedin_date_edit&action=edit&date_id=' . $date['id']); ?>">Edit</a> |
                            <a href="<?php echo admin_url('admin.php?page=bookedin_date_submenu&action=delete&date_id=' . $date['id']); ?>">Delete</a>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php
    bookInFooter();
}