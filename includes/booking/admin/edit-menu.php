<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

function booking_edit_page() {

    $bookingClass = new BookedInBookings();
    $addonClass = new BookedInAddons();
    $resourceClass = new BookedInResources();

    include_once('templates/booking-calendar.php');

    if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['booking_id'])) {
        $booking_id = intval($_GET['booking_id']);
        $booking_header = $bookingClass->get_booking_header($booking_id);
        $booking_addons = $bookingClass->get_booking_addons($booking_id);

        $addons = $addonClass->get_addons(null,'Y');
        $resources = $resourceClass->get_resources(null,'Y');
    }

    if (isset($_POST['update_booking'])) {

        $booking_date_from = sanitize_text_field($_POST['booking_date_from']);
        $booking_date_to = sanitize_text_field($_POST['booking_date_to']);
        $booking_resource = sanitize_text_field($_POST['booking_resource']);
        $booking_notes = sanitize_text_field($_POST['booking_notes']);
        $booking_description = sanitize_textarea_field($_POST['booking_description']);
        $booking_paid = sanitize_text_field($_POST['booking_paid']);
        $booking_deposit_refund = sanitize_text_field($_POST['booking_deposit_refund']);
        $booking_price = sanitize_text_field($_POST['booking_price']);
        $booking_discount = sanitize_text_field($_POST['booking_discount']);
        $booking_adults = sanitize_text_field($_POST['booking_adults']);
        $booking_children = sanitize_text_field($_POST['booking_children']);    
        $booking_user = sanitize_text_field($_POST['booking_user']);
        $booking_email = sanitize_text_field($_POST['booking_email']);
        $booking_phone = sanitize_text_field($_POST['booking_phone']);

        
        $selectedAddons = array();
        $booking_addon = array();
        if (isset($_POST['booking_addon']) && !empty($_POST['booking_addon'])) {
            $booking_addon = array_map('sanitize_text_field', $_POST['booking_addon']);
            $allAddon = $addonClass->get_addons(null,'Y');

            // Get all addon that are selected
            foreach ($allAddon as $addon) {
                if (in_array($addon['id'], $booking_addon)) {
                    $selectedAddons[] = $addon;
                }
            }
        }

        $bookingClass->delete_booking_and_addons($booking_id);

        [$booking_header_id, $booking_number] = $bookingClass->update_booking_header($booking_id, $booking_header['booking_number'], $booking_date_from, $booking_date_to, $booking_resource, $booking_notes, $booking_description, $booking_paid, $booking_deposit_refund, $booking_discount, $booking_price, $booking_adults, $booking_children, $booking_user, $booking_email, $booking_phone);

        // Add booking for selected addon with charge once
        foreach ($selectedAddons as $addon) {
            if ($addon['addon_perday'] == 'N') {
                $bookingClass->add_booking_addon($booking_header_id, $booking_date_from, $addon['id'], $booking_paid, $booking_discount);
            }
        }

        $nights = $bookingClass->get_nights($booking_date_from, $booking_date_to);

        for ($i = 0; $i < $nights; $i++) {
            $booking_date = date('Y-m-d', strtotime("$booking_date_from + $i days"));           
            $bookingClass->add_booking($booking_header_id, $booking_date, $booking_resource, $booking_paid);
            
            // Add booking for selected addon with charge per day
            foreach ($selectedAddons as $addon) {
                if ($addon['addon_perday'] == 'Y') {
                    $bookingClass->add_booking_addon($booking_header_id, $booking_date, $addon['id'], $booking_paid, $booking_discount);
                }
            }
        }

        wp_redirect(admin_url('admin.php?page=bookedin_main_menu'));
        exit;
    }

    bookedInNavigation('booking');
    ?>
    <div class="container">
        <br>
        <h1>Edit Booking</h1>
        <form method="post" action="">
            <div class="row">
                <!-- Left Column -->
                <div class="col-md-6">
                    <h3>Booking Details</h3>
                    <div class="mb-3">
                        <label for="booking_number" class="form-label">Booking Number:</label>
                        <input type="text" class="form-control" name="booking_number" value="<?php echo esc_attr($booking_header['booking_number']); ?>" readonly>
                    </div>
                    <div class="mb-3">  
                        <div class="row">

                            <div class="col">
                                <label for="booking_date_from" class="form-label">Date From:</label>
                                <input type="date" id="booking_date_from"  class="form-control" name="booking_date_from" value="<?php echo esc_attr($booking_header['booking_date_from']); ?>" required>
                            </div>
                            <div class="col">
                                <label for="booking_date_to" class="form-label">Date To:</label>
                                <input type="date" id="booking_date_to" class="form-control" name="booking_date_to" value="<?php echo esc_attr($booking_header['booking_date_to']); ?>" required>
                            </div>

                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="row">
                            <div class="col">
                                <label for="booking_adults" class="form-label">Adults:</label>
                                <input type="number" class="form-control" name="booking_adults" value="<?php echo esc_attr($booking_header['booking_adults']); ?>" min="1" onchange="updatePrice()" required>
                            </div>
                            <div class="col">
                                <label for="booking_children" class="form-label">Children:</label>
                                <input type="number" class="form-control" name="booking_children" value="<?php echo esc_attr($booking_header['booking_children']); ?>" min="0" onchange="updatePrice()" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="booking_resource" class="form-label">Resource:</label>
                        <select class="form-select" name="booking_resource" onchange="updatePrice()">
                        <?php foreach($resources as $resource) { ?>

                            <option value="<?php echo $resource['id']; ?>" <?php if ($resource['id'] == $booking_header['booking_resource']) { echo 'selected'; } ?>><?php echo $resource['resource_name']; ?></option>

                        <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="booking_addons" class="form-label">Addons:</label>
                        
                        <?php 
                            foreach($addons as $addon) { 
                        ?>

                            <label class="form-label"><?php echo $addon['addon_name']; ?></label>
                            <input type="checkbox" class="form-input" name="booking_addon[]" value="<?php echo $addon['id']; ?>" data-price='<?php echo $addon['addon_price']; ?>' onclick="updatePrice()" 
                            <?php 

                                foreach($booking_addons as $booking_addon) {
                                    if ($addon['id'] == $booking_addon['booking_addon']) { 
                                        echo 'checked'; 
                                    } 
                                }
                                
                            ?>
                            >
                        <?php 
                            } 
                        ?>

                    </div>
                    <div class="mb-3">
                        <label for="booking_notes" class="form-label">Customer Notes:</label>
                        <textarea class="form-control" name="booking_notes"><?php echo esc_attr($booking_header['booking_notes']); ?> </textarea>
                    </div>
                    <div class="mb-3">
                        <label for="booking_description" class="form-label">Hidden Description:</label>
                        <textarea class="form-control" name="booking_description"><?php echo esc_attr($booking_header['booking_description']); ?> </textarea>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-md-6">

                    <h3>User Details</h3>

                    <div class="mb-3">
                        <label for="booking_user" class="form-label">Name:</label>
                        <input type="text" class="form-control" name="booking_user" value="<?php echo esc_attr($booking_header['booking_user']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="booking_email" class="form-label">Email:</label>
                        <input type="email" class="form-control" name="booking_email" value="<?php echo esc_attr($booking_header['booking_email']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="booking_phone" class="form-label">Phone:</label>
                        <input type="text" class="form-control" name="booking_phone" value="<?php echo esc_attr($booking_header['booking_phone']); ?>" required>
                    </div>
                    <br>
                    <h3>Pricing Details</h3>
                    <div class="mb-3">
                        <label for="booking_price" class="form-label">Price:</label>
                        <input type="text" class="form-control" name="booking_price" value="<?php echo esc_attr($booking_header['booking_price']); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="booking_discount" class="form-label">Discount Code:</label>
                        <input type="text" class="form-control" name="booking_discount" onchange="updatePrice()" value="<?php echo esc_attr($booking_header['booking_discount']); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="booking_paid" class="form-label">Paid:</label>
                        <select class="form-select" name="booking_paid">
                            <option value="N" <?php if ($booking_header['booking_paid'] === 'N') echo 'selected'; ?>>No</option>
                            <option value="Y" <?php if ($booking_header['booking_paid'] === 'Y') echo 'selected'; ?>>Yes</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="booking_deposit_refund" class="form-label">Deposit Refund:</label>
                        <select class="form-select" name="booking_deposit_refund">
                            <option value="N" <?php if ($booking_header['booking_deposit_refund'] === 'N') echo 'selected'; ?>>No</option>
                            <option value="Y" <?php if ($booking_header['booking_deposit_refund'] === 'Y') echo 'selected'; ?>>Yes</option>
                        </select>
                    </div>
                    
                </div>
            </div>
            <div>
                <input class="btn btn-primary" type="submit" name="update_booking" value="Update booking">
            </div>
            
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
                    
                    totalPrice = data.total.total_after_final_discounted;
                    document.getElementsByName('booking_price')[0].value = totalPrice;
                }
            });

        }

        </script>


    <?php
    bookInFooter();
}