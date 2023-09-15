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
        $date_value = sanitize_text_field($_POST['date_value']);

        $datesClass->update_dates($date_id, $date_name, $date_description, $date_value);

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
    <div class="wrap">

        <!-- Form to add new addons -->
        <h2>Edit Date</h2>
        <form method="post" action="">
            <label for="date_name">Name:</label>
            <input type="text" name="date_name" value="<?php echo $date[0]['date_name']; ?>" required>
            <label for="date_description">Description:</label>
            <input type="text" name="date_description" value="<?php echo $date[0]['date_description']; ?>" required>
            <label for="date_value">Value:</label>
            <input type="text" name="date_value" value="<?php echo $date[0]['date_value']; ?>" required>
            <input type="submit" name="update_date" value="Update Date">
        </form>
    <?php
    bookInFooter();
}