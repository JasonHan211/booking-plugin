<?php

function bookingTable() {

    $bookingClass = new BookedInBookings();

    $bookings = $bookingClass->get_booking_header();
    
    ?>

        <table class="wp-list-table widefat striped">
            <thead>
                <tr>
                    <th>Date From</th>
                    <th>Date To</th>
                    <th>Resource</th>
                    <th>Notes</th>
                    <th>Description</th>
                    <th>Paid</th>
                    <th>Price</th>
                    <th>Adults</th>
                    <th>Children</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $booking) { ?>
                    <tr>
                        <td><?php echo $booking['booking_date_from']; ?></td>
                        <td><?php echo $booking['booking_date_to']; ?></td>
                        <td><?php echo $booking['resource_name']; ?></td>
                        <td><?php echo $booking['booking_notes']; ?></td>
                        <td><?php echo $booking['booking_description']; ?></td>
                        <td><?php echo $booking['booking_paid']; ?></td>
                        <td><?php echo $booking['booking_price']; ?></td>
                        <td><?php echo $booking['booking_adults']; ?></td>
                        <td><?php echo $booking['booking_children']; ?></td>
                        <td><?php echo $booking['booking_user']; ?></td>
                        <td><?php echo $booking['booking_email']; ?></td>
                        <td><?php echo $booking['booking_phone']; ?></td>
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