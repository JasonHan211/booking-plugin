<?php


function newBookingForm() {

    $addonClass = new BookedInAddons();
    $addons = $addonClass->get_addons(null,'Y');

    ?>
        
        <div class="container">
            <form method="post" action="" id="add_booking">
                <div class="row">
                    <!-- Left Column -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div id="booking_dates" data-date=""></div>
                            <input type="hidden" name="booking_date_from">
                            <input type="hidden" name="booking_date_to">
                        </div>
                        <div class="mb-3">
                            <label for="booking_resource" class="form-label">Resource:</label>
                            <select class="form-select" name="booking_resource" onclick="updatePriceField(this)" disabled>
                                    <option value="">Please select dates</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="booking_addons" class="form-label">Addons:</label>
                            
                            <?php foreach($addons as $addon) { ?>

                                <label class="form-label"><?php echo $addon['addon_name']; ?></label>
                                <input type="checkbox" class="form-control" name="booking_addon" value="<?php echo $addon['id']; ?>" data-price="<?php echo $addon['addon_price']; ?>" onclick="updatePriceField(this)">

                             <?php } ?>

                        </div>
                        <div class="mb-3">
                            <label for="booking_notes" class="form-label">Customer Notes:</label>
                            <textarea class="form-control" name="booking_notes"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="booking_description" class="form-label">Hidden Description:</label>
                            <textarea class="form-control" name="booking_description"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="booking_paid" class="form-label">Paid:</label>
                            <select class="form-select" name="booking_paid">
                                <option value="N">No</option>
                                <option value="Y">Yes</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="booking_adults" class="form-label">Adults:</label>
                            <input type="number" class="form-control" name="booking_adults" value="0" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label for="booking_children" class="form-label">Children:</label>
                            <input type="number" class="form-control" name="booking_children" value="0" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label for="booking_price" class="form-label">Price:</label>
                            <input type="text" class="form-control" name="booking_price" required>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-md-6">

                        <h3>User Details</h3>

                        <div class="mb-3">
                            <label for="booking_user" class="form-label">Name:</label>
                            <input type="text" class="form-control" name="booking_user" required>
                        </div>
                        <div class="mb-3">
                            <label for="booking_email" class="form-label">Email:</label>
                            <input type="email" class="form-control" name="booking_email" required>
                        </div>
                        <div class="mb-3">
                            <label for="booking_phone" class="form-label">Phone:</label>
                            <input type="text" class="form-control" name="booking_phone" required>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" name="add_booking" class="btn btn-primary">Add Booking</button>
            </form>
        </div>

        <script>
            function updatePriceField(selectElement) {
                // Get the selected option element
                if (selectElement.selectedIndex == -1) {
                    return;
                }
                var selectedOption = selectElement.options[selectElement.selectedIndex];

                // Get the price from the data-price attribute of the selected option
                var price = selectedOption.getAttribute('data-price');

                // Update the price_input value
                document.getElementsByName('booking_price')[0].value = price;
            }

            // Function to format the date range as a string
            function formatDateRange(startDate, endDate) {
                const start = startDate.toLocaleDateString('en-US', { year: 'numeric', month: '2-digit', day: '2-digit' });
                const end = endDate.toLocaleDateString('en-US', { year: 'numeric', month: '2-digit', day: '2-digit' });
                return `${start} - ${end}`;
            }

            // Initialize the Bootstrap Datepicker
            $(document).ready(function () {
                $('#booking_dates').datepicker({
                    format: 'yyyy-mm-dd',
                    multidate: 2,
                    startDate: new Date(),
                    showOnFocus: true, // Calendar won't close on first selection
                    autoclose: false,
                    multidateSeparator: ' - ',
                });

                // Show the datepicker when the input field is focused
                $('#booking_dates').focus(function () {
                    $(this).datepicker('show');
                });

                // Do something when 2 dates is selected
                $('#booking_dates').datepicker().on('changeDate', function (e) {
                    const selectedDates = $(this).datepicker('getDates');

                    var selectElement = document.querySelector('select[name="booking_resource"]');
                    selectElement.disabled = true;

                    if (selectedDates.length === 2) {

                        if (selectedDates[0] > selectedDates[1]) {
                            const temp = selectedDates[0];
                            selectedDates[0] = selectedDates[1];
                            selectedDates[1] = temp;
                        }

                        // Get the start and end dates
                        let startDate = formatDateToYYYYMMDD(selectedDates[0]);
                        let endDate = formatDateToYYYYMMDD(selectedDates[1]);

                        // Update the input fields with the selected dates
                        $('input[name="booking_date_from"]').val(startDate);
                        $('input[name="booking_date_to"]').val(endDate);
                        
                        // Update the input field with the formatted date range string
                        $(this).val(formatDateRange(selectedDates[0], selectedDates[1]));

                        // Query for available resources
                        $.ajax({
                            url: '<?php echo get_rest_url(null, 'v1/resources/get_available');?>',
                            type: 'POST',
                            data: {
                                action: 'get_available',
                                booking_date_from: startDate,
                                booking_date_to: endDate
                            },
                            success: function (data) {

                                selectElement.disabled = false;

                                // Remove all options from the select element
                                selectElement.innerHTML = '';

                                let availableResources = data.availables;

                                if (availableResources.length == 0) {
                                    let option = document.createElement('option');
                                    option.value = '';
                                    option.text = 'No available resources';
                                    selectElement.appendChild(option);
                                }

                                // Add the available resources as options to the select element
                                availableResources.forEach(function (resource) {
                                    var option = document.createElement('option');
                                    option.value = resource.id;
                                    option.text = resource.name;
                                    option.setAttribute('data-price', resource.price);
                                    selectElement.appendChild(option);
                                });
                            }
                        });

                    }
                });

            });

            // Function to format the date in yyyy-mm-dd format
            function formatDateToYYYYMMDD(date) {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            }

            // Set the selected date range into the hidden input fields before form submission
            $('form').submit(function (e) {

                // Check if the form is the booking form
                if ($(this).attr('id') === 'booking_form') {
                    const selectedDates = $('#booking_dates').datepicker('getDates');
                    if (selectedDates.length === 0) {
                        alert('Please select a date range.');
                        e.preventDefault();
                    }
                }
            });

        </script>

    <?php
}
