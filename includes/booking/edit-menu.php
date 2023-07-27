<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

function booking_edit_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'bookedin_bookings';

    if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['booking_id'])) {
        $booking_id = intval($_GET['booking_id']);
        $booking = $wpdb->get_row("SELECT * FROM $table_name WHERE id = $booking_id", ARRAY_A);
    }

    if (isset($_POST['update_booking'])) {

        $booking_date_from = sanitize_text_field($_POST['booking_date_from']);
        $booking_date_to = sanitize_text_field($_POST['booking_date_to']);
        $booking_resource = sanitize_text_field($_POST['booking_resource']);
        $booking_description = sanitize_textarea_field($_POST['booking_description']);
        $booking_paid = sanitize_text_field($_POST['booking_paid']);
        $booking_adults = sanitize_text_field($_POST['booking_adults']);
        $booking_children = sanitize_text_field($_POST['booking_children']);
        
        $booking_user = sanitize_text_field($_POST['booking_user']);
        $booking_email = sanitize_text_field($_POST['booking_email']);
        $booking_phone = sanitize_text_field($_POST['booking_phone']);

        $wpdb->update($table_name, array(
            'booking_date_from' => $booking_date_from,
            'booking_date_to' => $booking_date_to,
            'booking_resource' => $booking_resource,
            'booking_description' => $booking_description,
            'booking_paid' => $booking_paid,
            'booking_adults' => $booking_adults,
            'booking_children' => $booking_children,

            'booking_user' => $booking_user,
            'booking_email' => $booking_email,
            'booking_phone' => $booking_phone,

        ), array('id' => $booking_id));

        wp_redirect(admin_url('admin.php?page=bookedin_main_menu'));
        exit;
    }

    bookedInNavigation('booking');
    ?>
    <div class="wrap">
        <h1>Edit booking</h1>
        <form method="post" action="">
            <label for="booking_date_from">Date From:</label>
            <input type="date" name="booking_date_from" value="<?php echo esc_attr($booking['booking_date_from']); ?>" required>
            <label for="booking_date_to">Date To:</label>
            <input type="date" name="booking_date_to" value="<?php echo esc_attr($booking['booking_date_to']); ?>" required>
            <label for="booking_resource">Resource:</label>
            <input type="text" name="booking_resource" value="<?php echo esc_attr($booking['booking_resource']); ?>" required>
            <label for="booking_details">Description:</label>
            <textarea name="booking_description"><?php echo esc_textarea($booking['booking_description']); ?></textarea>
            <label for="booking_paid">Paid:</label>
            <select name="booking_paid">
                <option value="YES" <?php if ($booking['booking_paid'] === 'YES') echo 'selected'; ?>>YES</option>
                <option value="NO" <?php if ($booking['booking_paid'] === 'NO') echo 'selected'; ?>>NO</option>
            </select>
            <label for="booking_adults">Adults:</label>
            <input type="number" name="booking_adults" value="<?php echo esc_attr($booking['booking_adults']); ?>" required>
            <label for="booking_children">Children:</label>
            <input type="number" name="booking_children" value="<?php echo esc_attr($booking['booking_children']); ?>" required>
            <label for="booking_user">Name:</label>
            <input type="text" name="booking_user" value="<?php echo esc_attr($booking['booking_user']); ?>" required>
            <label for="booking_email">Email:</label>
            <input type="text" name="booking_email" value="<?php echo esc_attr($booking['booking_email']); ?>" required>
            <label for="booking_phone">Phone:</label>
            <input type="text" name="booking_phone" value="<?php echo esc_attr($booking['booking_phone']); ?>" required>
            <input type="submit" name="update_booking" value="Update booking">
        </form>
    </div>
    <?php
    bookInFooter();
}