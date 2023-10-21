<?php

function bookingTable() {

    $resourceClass = new BookedInResources();

    // Build Filter Form
    $resources = $resourceClass->get_resources();

?>
    <!-- Booking Filter -->
    <div class="container">
        <br>
        <div class="row mb-3">
            <div class="col-md-3">
                <label for="filterBooking" class="form-label">Booking Number:</label>
                <input type="text" id="filterBooking" name="filterBooking" class="form-control" value="">
            </div>
            <div class="col-md-3">
                <label for="filterDate" class="form-label">Date From:</label>
                <input type="date" id="filterDate" name="filterDate" class="form-control" value="">
            </div>
            <div class="col-md-3">
                <label for="filterRange" class="form-label">Range:</label>
                <select id="filterRange" name="filterRange" class="form-select">
                    <option value="">All</option>
                    <option value="0" selected>Today</option>
                    <option value="1">1 Weeks</option>
                    <option value="2">2 Weeks</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="filterResource" class="form-label">Resource:</label>
                <select id="filterResource" name="filterResource" class="form-select">
                    <option value="" selected>All Resources</option>
                    <?php foreach ($resources as $resource) { ?>
                        <option value="<?php echo $resource["id"]; ?>">
                            <?php echo $resource["resource_name"]; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-1">
                <label for="filterPaid" class="form-label">Paid Status:</label>
                <select id="filterPaid" name="filterPaid" class="form-select">
                    <option value="" selected>All</option>
                    <option value="Y">Paid</option>
                    <option value="N">Not Paid</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="filterDepositRefund" class="form-label">Deposit Refund:</label>
                <select id="filterDepositRefund" name="filterDepositRefund" class="form-select">
                    <option value="" selected>All</option>
                    <option value="Y">Paid</option>
                    <option value="N">Not Paid</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="filterName" class="form-label">Name:</label>
                <input type="text" id="filterName" name="filterName" class="form-control" value="">
            </div>
            <div class="col-md-3">
                <label for="filterEmail" class="form-label">Email:</label>
                <input type="email" id="filterEmail" name="filterEmail" class="form-control" value="">
            </div>
            <div class="col-md-3">
                <label for="filterPhone" class="form-label">Phone:</label>
                <input type="tel" id="filterPhone" name="filterPhone" class="form-control" value="">
            </div>
        </div>

        <button id="applyFilterButton" class="btn btn-primary" onclick="getBookingTable()">Apply Filter</button>
    </div>

    <br>

    <!-- Booking Table -->
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
        <tbody id="bookingTable"></tbody>
    </table>
    
    <!-- Pagination -->
    <div class="pagination" id="pagination"></div>

    <!-- Page Search -->
    <div class="page-search">
        <label for="recordPage">Go to Page:</label>
        <input type="number" id="recordPage" value="1" min="1" max="1">
        <button onclick="searchPage()">Go</button>
    </div>

    <script>

        // Build pagination
        let recordsPerPage = 5;
        let totalPages = 1;
        document.getElementById('recordPage').max = totalPages;
        let currentPage = 1;
        let prevPage = currentPage - 1;
        let nextPage = currentPage + 1;
        getBookingTable();
        
        function searchPage() {
            let page = document.getElementById('recordPage').value;
            currentPage = page;
            getBookingTable();
        }

        function goToPage(e) {
            let page = e.getAttribute('data-page');
            currentPage = page;
            getBookingTable();
        }

        function updatePagination() {

            prevPage = Number(currentPage) - 1;
            nextPage = Number(currentPage) + 1;

            document.getElementById('recordPage').value = currentPage;

            let paginationDiv = document.getElementById('pagination');
            paginationDiv.innerHTML = '';

            let content = '';

            // Display prev page buttons
            if (prevPage > 0) {
                content += `<a class="pagination" data-page="${prevPage}" onclick="goToPage(this)">&laquo; Previous</a>`;
            } else {
                content += `<a class="pagination">&laquo; Previous</a>`;
            }

            // Calculate start and end page numbers
            let startPage = Math.max(currentPage - 3, 1);
            let endPage = Math.min(currentPage + 3, totalPages);

            // Display page range
            for (let i = startPage; i <= endPage; i++) {
                let activeClass = (i == currentPage) ? ' active' : '';
                content += `<a class="pagination${activeClass}" data-page="${i}" onclick="goToPage(this)">${i}</a>`;
            }

            //Display next page buttons
            if (currentPage != totalPages) {
                content += `<a class="pagination" data-page="${nextPage}" onclick="goToPage(this)">Next &raquo;</a>`;
            } else {
                content += `<a class="pagination">Next &raquo;</a>`;
            }

            paginationDiv.innerHTML = content;

        }

        function getBookingTable(page = currentPage) {

            // Get data
            let filterBooking = document.getElementById('filterBooking').value;
            let filterDate = document.getElementById('filterDate').value;
            let filterResource = document.getElementById('filterResource').value;
            let filterPaid = document.getElementById('filterPaid').value;
            let filterDepositRefund = document.getElementById('filterDepositRefund').value;
            let filterRange = document.getElementById('filterRange').value;
            let filterName = document.getElementById('filterName').value;
            let filterEmail = document.getElementById('filterEmail').value;
            let filterPhone = document.getElementById('filterPhone').value;

            $.ajax({
                url: '<?php echo get_rest_url(null, 'v1/booking/get_booking_table');?>',
                type: 'POST',
                data: {
                    action: 'get_booking_table',
                    bookingID: filterBooking,
                    date: filterDate,
                    resource: filterResource,
                    paid: filterPaid,
                    depositRefund: filterDepositRefund,
                    range: filterRange,
                    name: filterName,
                    email: filterEmail,
                    phone: filterPhone,
                    page: page,
                    recordsPerPage: recordsPerPage
                },
                success: function (data) {
                    
                    let bookingTable = document.getElementById('bookingTable');
                    bookingTable.innerHTML = '';

                    let bookings = data.bookings;
                    let addons = data.addons;
                    let content = '';

                    for (let i = 0; i < bookings.length; i++) {
                        
                        let booking = bookings[i];

                        let bookingID = booking.booking_number;
                        let dateFrom = booking.booking_date_from;
                        let dateTo = booking.booking_date_to;
                        let resource = booking.resource_name;
                        let notes = booking.booking_notes;
                        let description = booking.booking_description;
                        let paid = booking.booking_paid;
                        let depositRefund = booking.booking_deposit_refund;
                        let discountCode = booking.booking_discount;
                        let price = booking.booking_price;
                        let adults = booking.booking_adults;
                        let children = booking.booking_children;
                        let name = booking.booking_user;
                        let email = booking.booking_email;
                        let phone = booking.booking_phone;

                        // Check if booking is tomorrow

                        if (isTomorrow(dateFrom)){
                            content += '<tr class="tomorrow">';
                        } else {
                            content += '<tr>';
                        }

                        // Add header
                        content += `
                                <td>${bookingID}</td>
                                <td>${dateFrom}</td>
                                <td>${dateTo}</td>
                                <td>${resource}</td>
                                <td>${notes}</td>
                                <td>${description}</td>
                                <td>${paid}</td>
                                <td>${depositRefund}</td>
                                <td>${discountCode}</td>
                                <td>${price}</td>
                                <td>${adults}</td>
                                <td>${children}</td>
                                <td>${name}</td>
                                <td>${email}</td>
                                <td>${phone}</td>
                                <td>
                                    <a href="<?php echo get_admin_url(); ?>admin.php?page=bookedin_booking_edit&action=edit&booking_id=${booking.id}">Edit</a>
                                    <a href="<?php echo get_admin_url(); ?>admin.php?page=bookedin_main_menu&action=delete&booking_id=${bookingID}" onclick="return confirm('Are you sure you want to delete this booking?')">Delete</a>
                                </td>
                            </tr>
                        `;

                        if (addons[i].length > 0) {
                            let thisAddons = addons[i];

                            thisAddons.forEach(addon => {
                                content += `
                                    <tr>
                                        <td colspan="1"></td>
                                        <td>${addon.booking_date}</td>
                                        <td colspan="1"></td>
                                        <td>${addon.addon_name}</td>
                                        <td colspan="2"></td>
                                        <td>${addon.booking_paid}</td>
                                        <td colspan="8"></td>
                                    </tr>`;
                            });
                        }

                    }

                    bookingTable.innerHTML = content;

                    let totalRow = data.totalCount;
                    totalPages = Math.ceil(totalRow / recordsPerPage);
                    console.log(totalPages);
                    updatePagination();
                }    
                
            });


        }

        // Function to check if a date is tomorrow
        function isTomorrow(date) {
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            const dateToCheck = new Date(date);
            return (
                tomorrow.getDate() === dateToCheck.getDate() &&
                tomorrow.getMonth() === dateToCheck.getMonth() &&
                tomorrow.getFullYear() === dateToCheck.getFullYear()
            );
        }

    </script>
    
<?php
}