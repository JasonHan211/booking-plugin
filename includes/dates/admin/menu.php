<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

function bookedin_date_submenu_page() {
    
    $datesClass = new BookedInDates();

    // Add date
    if (isset($_POST['add_date'])) {
        $date_name = sanitize_text_field($_POST['date_name']);
        $date_description = sanitize_text_field($_POST['date_description']);
        $date_time = sanitize_text_field($_POST['date_time']);
        $date_type = sanitize_text_field($_POST['date_type']);
        $activeFlag = sanitize_text_field($_POST['activeFlag']);

        $datesClass->add_dates($date_name, $date_description, $date_time, $date_type, $activeFlag);

    }

    // Delete date
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['date_id'])) {
        $date_id = intval($_GET['date_id']);

        $datesClass->delete_dates($date_id);
    }

    bookedInNavigation('Dates');
    ?>
    <div class="container">
        <br>
        <!-- Form to add new addons -->
        <h2>Add New Dates</h2>
        <form method="post" action="">

            <div class="row">

                <div class="col">
                    <label class="form-label" for="date_name">Name:</label>
                    <input class="form-control" type="text" name="date_name" value="" required>
                </div>
                <div class="col">
                    <label class="form-label" for="date_description">Description:</label>
                    <input class="form-control" type="text" name="date_description" value="">
                </div>
                <div class="col">
                    <label class="form-label" for="date_time">Date:</label>
                    <input class="form-control" type="date" name="date_time" value="" required>
                </div>

            </div>
            <div class="row mt-2">

                <div class="col">
                    <label class="form-label" for="date_type">Type:</label>
                    <select class="form-control" name="date_type">
                        <option value="Holiday">Holiday</option>
                        <option value="Break">Break</option>
                    </select>
                </div>
                <div class="col">
                    <label class="form-label" for="activeFlag">Active:</label>
                    <select class="form-control" name="activeFlag">
                        <option value="Y">Yes</option>
                        <option value="N">No</option>
                    </select>
                </div>

            </div>

            <br>

            <input class="btn btn-primary" type="submit" name="add_date" value="Add Date">
        </form>

        <br><br>
        <!-- Display existing addons -->
        <h2>Existing Dates</h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Active</th>
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
                        <td><?php echo $date['date_time']; ?></td>
                        <td><?php echo $date['date_type']; ?></td>
                        <td><?php echo $date['activeFlag']; ?></td>
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