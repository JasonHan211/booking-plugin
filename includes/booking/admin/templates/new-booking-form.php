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
                            <label for="booking_resource_count" class="form-label">Number of Resource:</label>
                            <input type="number" class="form-control" name="booking_resource_count" onchange="updateResources()" value="0" min="1" max="" required disabled>
                        </div>
                        <div id="resourceDetails" hidden></div>
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
                                <div class="card-body" id="priceBreakdownContent"></div>
                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col-7">
                                            <strong>Total Price:</strong>
                                        </div>
                                        <div class="col-5 ">
                                            <div class="input-group">
                                                <span class="input-group-text">RM</span>
                                                <input type="text" class="form-control text-end pe-4" name="booking_price" required>
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

            let addons = <?php echo json_encode($addons); ?>;
            let totalPrice = 0;
            let resourcesAvailable = [];
            let resourceCount = 0;

            function updatePrice() {
                
                let startDate = document.getElementById('booking_date_from').value;
                let endDate = document.getElementById('booking_date_to').value;
                let discount = document.getElementsByName('booking_discount')[0].value; 
                
                if (startDate == '' || endDate == '') {
                    return;
                }

                let bookings = [];

                let resourceForms = document.getElementsByName('resourcesForm'); 

                Array.from(resourceForms).forEach((form, index) => {
                    let resource = form.querySelectorAll('[name="booking_resource"]')[0].value; 
                    let adult = form.querySelectorAll('[name="booking_adults"]')[0].value; 
                    let children = form.querySelectorAll('[name="booking_children"]')[0].value;   
                    let addons = [];
                    let checkboxes = form.querySelectorAll('[name="booking_addon[]"]');
                    checkboxes.forEach(checkbox => {
                        if (checkbox.checked) {
                            addons.push(checkbox.value);
                        }
                    });

                    let eachResourceForm = {
                        resource: resource,
                        adult: adult,
                        children: children,
                        addons: addons
                    };

                    bookings.push(eachResourceForm);

                });

                console.log(bookings);
                
                $.ajax({
                    url: '<?php echo get_rest_url(null, 'v1/booking/calculate_price');?>',
                    type: 'POST',
                    data: {
                        action: 'calculate_price',
                        booking_date_from: startDate,
                        booking_date_to: endDate,
                        bookings: JSON.stringify(bookings),
                        booking_discount: discount
                    },
                    success: function (data) {
                        
                        let nights = data.total.nights;

                        document.getElementById('priceBreakdown').hidden = false;

                        let bookings = data.bookings;
                        let priceBreakdownContent = document.getElementById('priceBreakdownContent');
                        let stayHtml = '';

                        bookings.forEach((booking,index) => {
                            
                            stayHtml += `
                            <ul class="list-group">
                                <h6>Tent ${index + 1}</h6>
                                <li class="list-group-item justify-content-between">
                                    <span>Price of Stay:</span>
                                    <ul class="list-group mt-3">`;
                                    booking.resource.forEach(night => {

                                        stayHtml += `
                                        <li class="list-group-item d-flex justify-content-between">
                                            <span>${night.booking_date}</span>
                                            <span>RM ${Number(booking.resource[0].resource_price).toFixed(2)}</span>
                                        </li>`;

                                    });

                            stayHtml += `
                                    </ul>
                                </li>`;  

                            if (booking.addon.length >0) {
                            
                            stayHtml += `     
                                <li class="list-group-item justify-content-between">
                                    <span>Addons:</span>
                                    <ul class="list-group mt-3">`;
                                    booking.addon.forEach(addon => {
                                        stayHtml += `
                                        <li class="list-group-item d-flex justify-content-between">
                                            <span>${addon.addon.addon_name}:</span>
                                            <span>RM ${Number(addon.addon_price).toFixed(2)} for ${nights} nights</span>
                                        </li>`;

                                    })
                            stayHtml +=`        
                                    </ul>
                                </li>`;

                            }
                            stayHtml +=`
                            </ul>`;


                        });

                        let discount = 0;
                        discount = data.total.original - data.total.total_after_final_discounted;

                        stayHtml +=`
                        <ul class="list-group">
                            <h6>Others</h6>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Deposit Amount:</span>
                                <span>RM ${data.total.deposit.toFixed(2)}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Discount Applied:</span>
                                <span>- RM ${discount.toFixed(2)}</span>
                            </li>
                        </ul>`;

                        priceBreakdownContent.innerHTML = stayHtml;

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
                var selectElement = document.getElementsByName('booking_resource_count')[0];

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

                        resourcesAvailable = data.availables;
                        
                        document.getElementsByName('booking_resource_count')[0].max = resourcesAvailable.length;
                        
                        if (resourcesAvailable.length == 0) {
                            document.getElementsByName('booking_resource_count')[0].min = 0;
                            document.getElementsByName('booking_resource_count')[0].value = 0;
                            document.getElementsByName('booking_resource_count')[0].disabled = true;
                        } else {
                            document.getElementsByName('booking_resource_count')[0].min = 1;
                            document.getElementsByName('booking_resource_count')[0].value = 1;
                            document.getElementsByName('booking_resource_count')[0].disabled = false;
                        }

                        updateResources();
                    }
                });
            }

            function updateResources() {
                let newResourceCount = document.getElementsByName('booking_resource_count')[0].value;

                let change = newResourceCount - resourceCount;
                console.log("change: ",change);
                
                let resourceDetails = document.getElementById('resourceDetails');

                // Add resources
                if (change > 0) {

                    for (let i = 0; i < Math.abs(change); i++) {
                        let resourceForm = ``;
                        resourceForm += `
                            <div class="mb-3 p-4 border" name="resourcesForm">
                                <h5>Tent ${Number(resourceCount) + 1}</h5>
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
                                        <select class="form-select" name="booking_resource" onchange="updatePrice()">`;
                                    
                                    if (resourcesAvailable.length == 0) {
                                        resourceForm += `<option value="">No available resources</option>`;
                                    }

                                    // Add the available resources as options to the select element
                                    resourcesAvailable.forEach(function (resource) {
                                        resourceForm += `<option value="${resource.id}" data-price="${resource.price}">${resource.name}</option>`;
                                    });

                        resourceForm += `
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="booking_addons" class="form-label">Addons:</label>
                                        <br>
                                        <div class="addon-container">`;

                                    addons.forEach(function (addon) {
                                        resourceForm += `
                                            <div class="addon-section border p-4 m-2">
                                                <div class="row">
                                                    <div class="col-11"><label class="form-label">${addon.addon_name}</label></div>
                                                    <div class="col-1"><input type="checkbox" class="form-control" name="booking_addon[]" value="${addon.id}" data-price='${addon.addon_price}' onclick="updatePrice()"></div>
                                                </div>
                                                <p class="mb-0">${addon.addon_description}</p>
                                            </div>`;
                                    })

                        resourceForm += `
                                        </div>
                                    </div>
                                </div>`;

                        resourceDetails.innerHTML += resourceForm;
                        resourceCount++;
                    }

                // Remove resources
                } else if (change < 0) {

                    for (let i = 0; i < Math.abs(change); i++) {
                        // Find the last child element with the name 'resourcesForm' and remove it
                        const resourceForms = document.getElementsByName('resourcesForm');
                        if (resourceForms.length > 0) {
                            resourceDetails.removeChild(resourceForms[resourceForms.length - 1]);
                        }
                    }
                    
                }

                resourceCount = newResourceCount;
                updatePrice();

                if (resourceCount != 0) {
                    resourceDetails.hidden = false;
                } else {
                    resourceDetails.hidden = true;
                }

            }

            const numberInput = document.getElementsByName('booking_resource_count')[0];

            numberInput.addEventListener('input', function () {
                const inputValue = parseFloat(numberInput.value);
                const max = parseFloat(numberInput.getAttribute('max'));

                if (!isNaN(inputValue) && inputValue > max) {
                    numberInput.value = max; // Reset the input value to the maximum allowed value
                }
            });

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
