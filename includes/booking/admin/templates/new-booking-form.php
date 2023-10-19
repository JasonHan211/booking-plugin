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
                            
                            <?php bookingCalendar($display=false) ?>

                        </div>
                        <div class="mb-3">
                            <div class="row">
                                <div class="col">
                                    <label for="booking_adults" class="form-label">Adults:</label>
                                    <input type="number" class="form-control" name="booking_adults" value="1" min="1" onchange="updatePrice()" required>
                                </div>
                                <div class="col">
                                    <label for="booking_children" class="form-label">Children:</label>
                                    <input type="number" class="form-control" name="booking_children" value="0" min="0" onchange="updatePrice()" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="booking_resource" class="form-label">Resource:</label>
                            <select class="form-select" name="booking_resource" onchange="updatePrice()" disabled>
                                    <option value="">Please select dates</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="booking_addons" class="form-label">Addons:</label>
                            <br>

                            <div class="addon-container">
                                <?php foreach($addons as $addon) { ?>
                                    
                                    <div class="addon-section border p-4 m-2">
                                        <div class="row">
                                            <div class="col-11"><label class="form-label"><?php echo $addon['addon_name']; ?></label></div>
                                            <div class="col-1"><input type="checkbox" class="form-control" name="booking_addon[]" value="<?php echo $addon['id']; ?>" data-price='<?php echo $addon['addon_price']; ?>' onclick="updatePrice()"></div>
                                        </div>
                                        <p class="mb-0"><?php echo $addon['addon_description']; ?></p>
                                    </div>

                                <?php } ?>
                            </div>

                        </div>
                        <div class="mb-3">
                            <label for="booking_notes" class="form-label">Customer Notes:</label>
                            <textarea class="form-control" name="booking_notes"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="booking_description" class="form-label">Hidden Description:</label>
                            <textarea class="form-control" name="booking_description"></textarea>
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
                        <br>
                        <h3>Pricing Details</h3>
                        <div class="mb-3">
                            <label for="booking_discount" class="form-label">Discount Code:</label>
                            <input type="text" class="form-control" name="booking_discount" onchange="updatePrice()">
                        </div>
                        <!-- To add price breakdown -->

                        <div class="mt-4 mb-4" id="priceBreakdown" hidden>
                            <div class="card mw-100">
                                <div class="card-header">
                                    Price Breakdown
                                </div>
                                <div class="card-body">
                                    <ul class="list-group">
                                        <li class="list-group-item d-flex justify-content-between">
                                            <span>Price of Stay:</span>
                                            <span id="stayPrice"></span>
                                        </li>
                                        <li class="list-group-item justify-content-between" id="addonContainer" hidden>
                                            <span>Addons:</span>
                                            <ul class="list-group mt-3" id="addonList"></ul>
                                        </li>
                                   
                                        <li class="list-group-item d-flex justify-content-between">
                                            <span>Deposit Amount:</span>
                                            <span id="depositPrice"></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between">
                                            <span>Discount Applied:</span>
                                            <span id="discountPrice"></span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col-8">
                                            <strong>Total Price:</strong>
                                        </div>
                                        <div class="col-4 ">
                                            <div class="input-group">
                                                <span class="input-group-text">RM</span>
                                                <input type="text" class="form-control text-end pe-3" name="booking_price" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="booking_paid" class="form-label">Paid:</label>
                            <select class="form-select" name="booking_paid">
                                <option value="N">No</option>
                                <option value="Y">Yes</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="booking_deposit_refund" class="form-label">Deposit Refund:</label>
                            <select class="form-select" name="booking_deposit_refund">
                                <option value="N">No</option>
                                <option value="Y">Yes</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" name="add_booking" class="btn btn-primary">Add Booking</button>
            </form>
        </div>

        <script>

            let totalPrice = 0;

            function updatePrice() {
                
                let startDate = document.getElementById('booking_date_from').value;
                let endDate = document.getElementById('booking_date_to').value;
                let resource = document.getElementsByName('booking_resource')[0].value;
                let adult = document.getElementsByName('booking_adults')[0].value;
                let children = document.getElementsByName('booking_children')[0].value;
                let discount = document.getElementsByName('booking_discount')[0].value;    
                let addons = [];
                const checkboxes = document.querySelectorAll('[name="booking_addon[]"]');
            
                checkboxes.forEach(checkbox => {
                    if (checkbox.checked) {
                        addons.push(checkbox.value);
                    }
                });
    
                if (startDate == '' || endDate == '' || resource == '') {
                    return;
                }
                
                $.ajax({
                    url: '<?php echo get_rest_url(null, 'v1/booking/calculate_price');?>',
                    type: 'POST',
                    data: {
                        action: 'calculate_price',
                        booking_date_from: startDate,
                        booking_date_to: endDate,
                        booking_resource: resource,
                        booking_addon: addons,
                        booking_adults: adult,
                        booking_children: children,
                        booking_discount: discount
                    },
                    success: function (data) {
                        
                        let nights = data.resource.resource.length;

                        document.getElementById('priceBreakdown').hidden = false;
                        document.getElementById('stayPrice').innerHTML = `RM ${Number(data.resource.resource[0].resource_price).toFixed(2)} x ${nights} nights`;

                        if (data.addons.length > 0) {
                            document.getElementById('addonContainer').hidden = false;
                            let addonList = document.getElementById('addonList');
                            addonList.innerHTML = '';
                            data.addons.forEach(addon => {

                                let price = (addon.addon_perday == 'Y') ? addon.addon_price/nights : addon.addon_price;
                                let addonHtml = `
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>${addon.addon.addon_name}:</span>
                                    <span>RM ${Number(price).toFixed(2)} x ${nights} nights</span>
                                </li>`;

                                addonList.insertAdjacentHTML('beforeend', addonHtml);

                            })
                        } else {
                            document.getElementById('addonContainer').hidden = true;
                        }
                        

                        document.getElementById('depositPrice').innerHTML = `RM ${data.total.deposit.toFixed(2)}`;
                        
                        let discount = 0
                        discount = data.total.raw_total - data.total.total_after_final_discounted
                        document.getElementById('discountPrice').innerHTML = `- RM ${discount.toFixed(2)}`;

                        totalPrice = data.total.total_after_final_discounted;
                        document.getElementsByName('booking_price')[0].value = totalPrice.toFixed(2);
                    }
                });

            }

            // Function to format the date range as a string
            function formatDateRange(startDate, endDate) {
                const start = startDate.toLocaleDateString('en-US', { year: 'numeric', month: '2-digit', day: '2-digit' });
                const end = endDate.toLocaleDateString('en-US', { year: 'numeric', month: '2-digit', day: '2-digit' });
                return `${start} - ${end}`;
            }

            // Function to handle mutations
            function handleMutations(mutationsList, observer) {
                for (var mutation of mutationsList) {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
                        getResources();
                    }
                }
            }

            // Create a new MutationObserver instance
            var observer = new MutationObserver(handleMutations);

            // Observe changes in childList of the target element
            observer.observe(document.getElementById('booking_date_to'), { attributes: true, attributeFilter: ['value'] });
    
            // Function to get the available resources
            function getResources() {
                
                // Get the select element
                var selectElement = document.getElementsByName('booking_resource')[0];

                startDate = document.getElementById('booking_date_from').value;
                endDate = document.getElementById('booking_date_to').value;
                
                // If the start date or end date is empty, disable the select element and return
                if (startDate == '' || endDate == '') {
                    selectElement.disabled = true;
                    selectElement.innerHTML = '<option value="">Please select dates</option>';
                    return;
                }
                selectElement.innerHTML = '<option value="">Loading Please Wait</option>';
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
                            option.value = "";
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
                        updatePrice();
                    }
                });
            }

            // Set the selected date range into the hidden input fields before form submission
            $('form').submit(function (e) {

                startDate = document.getElementById('booking_date_from').value;
                endDate = document.getElementById('booking_date_to').value;

                if (startDate === '' || endDate === '') {
                    alert('Please select a date range.');
                    e.preventDefault();
                }
                
            });

        </script>

    <?php
}
