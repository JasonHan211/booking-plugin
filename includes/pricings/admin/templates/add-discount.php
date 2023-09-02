<?php

function addDiscountForm() {

    $pricingClass = new BookedInpricings();

    // Handle discount addition
    if (isset($_POST['add_discount'])) {
        $discount_name = sanitize_text_field($_POST['discount_name']);
        $discount_description = sanitize_text_field($_POST['discount_description']);
        $discount_code = sanitize_text_field($_POST['discount_code']);
        $discount_quantity = sanitize_text_field($_POST['discount_quantity']);
        $discount_type = sanitize_text_field($_POST['discount_type']);
        $discount_amount = sanitize_text_field($_POST['discount_amount']);
        $discount_start_date = sanitize_text_field($_POST['discount_start_date']);
        $discount_end_date = sanitize_text_field($_POST['discount_end_date']);
        $discount_on_type = sanitize_text_field($_POST['discount_on_type']);
        if ($discount_on_type == 'ALL') {
            $discount_on_id = null;
        } else {
            $discount_on_id = sanitize_text_field($_POST['discount_on_id']);
        }
        $discount_condition = sanitize_text_field($_POST['discount_condition']);
        $discount_condition_start = sanitize_text_field($_POST['discount_condition_date_from']);
        $discount_condition_end = sanitize_text_field($_POST['discount_condition_date_to']);
        $discount_auto_apply = sanitize_text_field($_POST['discount_auto_apply']);
        $discount_active = sanitize_text_field($_POST['discount_active']);

        $pricingClass->add_discount($discount_name, $discount_description, $discount_code, $discount_quantity, $discount_type, $discount_amount, $discount_start_date, $discount_end_date, $discount_on_type, $discount_on_id, $discount_condition, $discount_condition_start, $discount_condition_end, $discount_auto_apply, $discount_active);
        
    }

    ?>
    <!-- Form to add new discounts -->
        <h2>Add New discount</h2>
        <form method="post" action="">
            <label for="discount_name">Name:</label>
            <input type="text" name="discount_name" required>
            <label for="discount_description">Description:</label>
            <textarea name="discount_description"></textarea>
            <label for="discount_code">Code:</label>
            <input type="text" name="discount_code" class="all-cap" required>
            <br>
            <label for="discount_quantity">Quantity:</label>
            <input type="number" name="discount_quantity" min=0 step="1" required>
            <label for="discount_type">Type:</label>
            <select name="discount_type">
                <option value="Percentage">Percentage</option>
                <option value="Fixed">Fixed</option>
            </select>
            <label for="discount_amount">Amount:</label>
            <input type="number" name="discount_amount" min=0 step="1" required>
            <br>
            <label for="discount_start_date">Start Date:</label>
            <input type="date" name="discount_start_date">
            <label for="discount_end_date">End Date:</label>
            <input type="date" name="discount_end_date">
            <br>
            <label for="discount_on_type">On Type:</label>
            <select name="discount_on_type">
                <option value="ALL">All</option>
                <option value="Resources">Resources</option>
                <option value="Addon">Addon</option>
            </select>
            <label for="discount_on_id">On ID:</label>
            <select name="discount_on_id" disabled>
                <option value="All">N/A</option>
            </select>
            <label for="discount_condition">Condition:</label>
            <select name="discount_condition">
                <option value="None">None</option>
                <option value="Weekdays">Weekdays</option>
                <option value="Weekends">Weekends</option>
                <option value="Off-Peak">Off Peak (Weekdays & Not Holiday)</option>
            </select>
            <br>
            <div class="discount-condition-date-range">
                <label for="discount_condition_date_fom">Date From:</label>
                <input type="date" name="discount_condition_date_from">
                <label for="discount_condition_date_to">Date To:</label>
                <input type="date" name="discount_condition_date_to">
            </div>
            <label for="discount_auto_apply">Auto Apply:</label>
            <select name="discount_auto_apply">
                <option value="Y">Yes</option>
                <option value="N">No</option>
            </select>
            <label for="discount_active">Active:</label>
            <select name="discount_active">
                <option value="Y">Yes</option>
                <option value="N">No</option>
            </select>
            <input type="submit" name="add_discount" value="Add discount">
        </form>

        <script>
            jQuery(document).ready(function($) {
                $('select[name="discount_on_type"]').change(function() {
                    var discount_on_type = $(this).val();
                    var discount_on_id = $('select[name="discount_on_id"]');
                    if (discount_on_type == 'Resources') {

                        $.ajax({
                            url: '<?php echo get_rest_url(null, 'v1/resources/get_resources');?>',
                            type: 'POST',
                            data: {
                                action: 'get_resources',
                            },
                            success: function (data) {
                                
                                discount_on_id.prop('disabled', false);
                                discount_on_id.empty();

                                resources = data.resources;

                                discount_on_id.append('<option value="All"> All </option>');
                                resources.forEach(function(resource) {
                                    discount_on_id.append('<option value="' + resource.id + '">' + resource.resource_name + '</option>');
                                });

                            }
                        });
                        
                    } else if (discount_on_type == 'Addon') {

                        $.ajax({
                            url: '<?php echo get_rest_url(null, 'v1/addons/get_addons');?>',
                            type: 'POST',
                            data: {
                                action: 'get_addons',
                            },
                            success: function (data) {
                                
                                discount_on_id.prop('disabled', false);
                                discount_on_id.empty();

                                addons = data.addons;

                                discount_on_id.append('<option value="All"> All </option>');
                                addons.forEach(function(addon) {
                                    discount_on_id.append('<option value="' + addon.id + '">' + addon.addon_name + '</option>');
                                });

                            }
                        });

                    } else {
                        discount_on_id.prop('disabled', true);
                        discount_on_id.empty();
                        discount_on_id.append('<option value="null">N/A</option>');
                    }
                });

                $('input[name="discount_code"]').keyup(function() {
                    $(this).val($(this).val().toUpperCase());
                });

            });
        </script>

    <?php
}