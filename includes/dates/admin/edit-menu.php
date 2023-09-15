<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

function date_edit_page() {

    $datesClass = new BookedInDates();

    // Update date
    if (isset($_POST['update_date'])) {
        $date_id = intval($_GET['date_id']);
        $date_name = sanitize_text_field($_POST['date_name']);
        $date_description = sanitize_text_field($_POST['date_description']);
        $date_time = sanitize_text_field($_POST['date_time']);
        $date_type = sanitize_text_field($_POST['date_type']);
        $activeFlag = sanitize_text_field($_POST['activeFlag']);

        $datesClass->update_dates($date_id, $date_name, $date_description, $date_time, $date_type, $activeFlag);

        wp_redirect(admin_url('admin.php?page=bookedin_date_submenu'));
        exit;

    }

    // Get date
    if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['date_id'])) {
        $date_id = intval($_GET['date_id']);

        $date = $datesClass->get_dates($date_id);
    }


    bookedInNavigation('Dates');
    ?>
    <div class="container">
        <br>
        <!-- Form to add new addons -->
        <h2>Edit Date</h2>
        <form method="post" action="">
            <div class="row">

                <div class="col">
                    <label class="form-label" for="date_name">Name:</label>
                    <input class="form-control" type="text" name="date_name" value="<?php echo $date[0]['date_name']; ?>" required>
                </div>
                <div class="col">
                    <label class="form-label" for="date_description">Description:</label>
                    <input class="form-control" type="text" name="date_description" value="<?php echo $date[0]['date_description']; ?>">
                </div>
                <div class="col">
                    <label class="form-label" for="date_time">Date:</label>
                    <input class="form-control" type="date" name="date_time" value="<?php echo $date[0]['date_time']; ?>" required>
                </div>

                </div>
                <div class="row mt-2">

                <div class="col">
                    <label class="form-label" for="date_type">Type:</label>
                    <select class="form-control" name="date_type">
                        <option value="Holiday" <?php if ($date[0]['date_type'] === 'Holiday') echo 'selected'; ?>>Holiday</option>
                        <option value="Break" <?php if ($date[0]['date_type'] === 'Break') echo 'selected'; ?>>Break</option>
                    </select>
                </div>
                <div class="col">
                    <label class="form-label" for="activeFlag">Active:</label>
                    <select class="form-control" name="activeFlag">
                        <option value="Y" <?php if ($date[0]['activeFlag'] === 'Y') echo 'selected'; ?>>Yes</option>
                        <option value="N" <?php if ($date[0]['activeFlag'] === 'N') echo 'selected'; ?>>No</option>
                    </select>
                </div>

            </div>
            <br>
            <input class="btn btn-primary" type="submit" name="update_date" value="Update Date">
        </form>
    <?php
    bookInFooter();
}