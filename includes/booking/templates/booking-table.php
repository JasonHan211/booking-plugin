<?php

function bookingTable($bookings) {
    ?>

        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Date From</th>
                    <th>Date To</th>
                    <th>Resource</th>
                    <th>Description</th>
                    <th>Paid</th>
                    <th>Adults</th>
                    <th>Children</th>
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
                        <td><?php echo $booking['booking_paid']; ?></td>
                        <td><?php echo $booking['booking_adults']; ?></td>
                        <td><?php echo $booking['booking_children']; ?></td>
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

    <?php
}