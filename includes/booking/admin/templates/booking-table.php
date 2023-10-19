<?php

function bookingTable() {

    $bookingClass = new BookedInBookings();


    $recordsPerPage = 10; // Number of records to display per page
    $page = isset($_GET['recordPage']) ? intval($_GET['recordPage']) : 1; // Current page

    // Calculate OFFSET for pagination
    $offset = ($page - 1) * $recordsPerPage;

    // Get total number of records
    $rowCount = $bookingClass->get_booking_header_count();
    // Calculate total number of pages
    $totalPages = ceil($rowCount / $recordsPerPage);

    $bookings = $bookingClass->get_booking_header(null, $recordsPerPage, $offset);
    
    ?>

<div class="container">
        <br>
        <div class="row mb-3">
            <div class="col-md-3">
                <label for="filterBooking" class="form-label">Booking Number:</label>
                <input type="text" id="filterBooking" name="filterBooking" class="form-control">
            </div>
            <div class="col-md-3">
                <label for="filterDate" class="form-label">Date:</label>
                <input type="date" id="filterDate" name="filterDate" class="form-control">
            </div>
            <div class="col-md-3">
                <label for="filterResource" class="form-label">Resource:</label>
                <select id="filterResource" name="filterResource" class="form-select">
                    <option value="">All Resources</option>
                    <option value="resource1">Resource 1</option>
                    <option value="resource2">Resource 2</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="filterBirthday" class="form-label">Birthday:</label>
                <select id="filterBirthday" name="filterBirthday" class="form-select">
                    <option value="">All</option>
                    <option value="1">Birthday</option>
                    <option value="0">No Birthday</option>
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                <label for="filterPaid" class="form-label">Paid Status:</label>
                <select id="filterPaid" name="filterPaid" class="form-select">
                    <option value="">All</option>
                    <option value="1">Paid</option>
                    <option value="0">Not Paid</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="filterName" class="form-label">Name:</label>
                <input type="text" id="filterName" name="filterName" class="form-control">
            </div>
            <div class="col-md-3">
                <label for="filterEmail" class="form-label">Email:</label>
                <input type="email" id="filterEmail" name="filterEmail" class="form-control">
            </div>
            <div class="col-md-3">
                <label for="filterPhone" class="form-label">Phone:</label>
                <input type="tel" id="filterPhone" name="filterPhone" class="form-control">
            </div>
        </div>

        <button id="applyFilterButton" class="btn btn-primary">Apply Filter</button>

    </div>

        <br>

        <table class="wp-list-table widefat striped">
            <thead>
                <tr>
                    <th>Booking Number</th>
                    <th>Date From</th>
                    <th>Date To</th>
                    <th>Resource</th>
                    <th>Notes</th>
                    <th>Description</th>
                    <th>Paid</th>
                    <th>Deposit Refund</th>
                    <th>Discount Code</th>
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

                    <?php 
                    
                        // Check if booking is tomorrow
                        $tomorrow = date('Y-m-d', strtotime('+1 day'));
                        $bookingDateFrom = date('Y-m-d', strtotime($booking['booking_date_from']));

                        if ($tomorrow === $bookingDateFrom) {
                            echo '<tr class="tomorrow">';
                        } else {
                            echo '<tr>';
                        }


                    ?>
                        <td><?php echo $booking['booking_number']; ?></td>
                        <td><?php echo $booking['booking_date_from']; ?></td>
                        <td><?php echo $booking['booking_date_to']; ?></td>
                        <td><?php echo $booking['resource_name']; ?></td>
                        <td><?php echo $booking['booking_notes']; ?></td>
                        <td><?php echo $booking['booking_description']; ?></td>
                        <td><?php echo $booking['booking_paid']; ?></td>
                        <td><?php echo $booking['booking_deposit_refund']; ?></td>
                        <td><?php echo $booking['booking_discount']; ?></td>
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

                    <?php 
                    
                    $bookedAddons = $bookingClass->get_booking_addons($booking['id']);
                    foreach ($bookedAddons as $addon) { ?>
                            <tr>
                                <td colspan="1"></td>
                                <td><?php echo $addon['booking_date']; ?></td>
                                <td colspan="1"></td>
                                <td><?php echo $addon['addon_name']; ?></td>
                                <td colspan="2"></td>
                                <td><?php echo $addon['booking_paid']; ?></td>
                                <td colspan="8"></td>
                            </tr>
                    <?php } ?>

                <?php } ?>
            </tbody>
        </table>
        <!-- Display page links -->
        <div class="pagination">
            <?php
            $prevPage = $page - 1;
            $nextPage = $page + 1;
            
            if ($prevPage > 0) {
                echo '<a href="?page=bookedin_main_menu&recordPage=' . $prevPage . '" class="pagination">&laquo; Previous</a>';
            }
            
            // Calculate start and end page numbers
            $startPage = max($page - 3, 1);
            $endPage = min($page + 3, $totalPages);
            
            // Display page links
            for ($i = $startPage; $i <= $endPage; $i++) {
                $activeClass = ($i === $page) ? ' active' : '';
                echo '<a href="?page=bookedin_main_menu&recordPage=' . $i . '" class="pagination' . $activeClass . '">' . $i . '</a>';
            }
            
            if ($nextPage <= $totalPages) {
                echo '<a href="?page=bookedin_main_menu&recordPage=' . $nextPage . '" class="pagination">Next &raquo;</a>';
            }
            ?>
        </div>
        <div class="page-search">
            <label for="recordPage">Go to Page:</label>
            <input type="number" id="recordPage" value="<?php echo $page; ?>" min="1" max="<?php echo $totalPages; ?>">
            <button id="goToPageLink">Go</button>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var goToPageLink = document.getElementById('goToPageLink');
                var recordPageInput = document.getElementById('recordPage');
                
                goToPageLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    var pageNumber = parseInt(recordPageInput.value);
                    if (pageNumber >= 1 && pageNumber <= <?php echo $totalPages; ?>) {
                        window.location.href = '?page=bookedin_main_menu&recordPage=' + pageNumber;
                    }
                });

                recordPageInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        var pageNumber = parseInt(recordPageInput.value);
                        if (pageNumber >= 1 && pageNumber <= <?php echo $totalPages; ?>) {
                            window.location.href = '?page=bookedin_main_menu&recordPage=' + pageNumber;
                        }
                    }
                });
            });
        </script>








    <?php
}