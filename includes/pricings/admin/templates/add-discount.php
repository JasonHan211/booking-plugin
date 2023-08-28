<?php

function addDiscountForm() {

    $pricingClass = new BookedInpricings();

    // Handle discount addition
    if (isset($_POST['add_discount'])) {
        $discount_name = sanitize_text_field($_POST['discount_name']);
        $discount_description = sanitize_text_field($_POST['discount_description']);
        $discount_code = sanitize_text_field($_POST['discount_code']);
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
        $discount_auto_apply = sanitize_text_field($_POST['discount_auto_apply']);
        $discount_active = sanitize_text_field($_POST['discount_active']);

        $pricingClass->add_discount($discount_name, $discount_description, $discount_code, $discount_type, $discount_amount, $discount_start_date, $discount_end_date, $discount_on_type, $discount_on_id, $discount_condition, $discount_auto_apply, $discount_active);
        
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
            <input type="text" name="discount_code" required>
            <br>
            <label for="discount_type">Type:</label>
            <select name="discount_type">
                <option value="Percentage">Percentage</option>
                <option value="Fixed">Fixed</option>
            </select>
            <label for="discount_amount">Amount:</label>
            <input type="text" name="discount_amount" required>
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
                <option value="null">N/A</option>
            </select>
            <label for="discount_condition">Condition:</label>
            <select name="discount_condition">
                <option value="all">All</option>
                <option value="specific">Specific</option>
            </select>
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
                        discount_on_id.prop('disabled', false);
                        discount_on_id.empty();
                        discount_on_id.append('<option value="1">Resource 1</option>');
                        discount_on_id.append('<option value="2">Resource 2</option>');
                        discount_on_id.append('<option value="3">Resource 3</option>');
                    } else if (discount_on_type == 'Addon') {
                        discount_on_id.prop('disabled', false);
                        discount_on_id.empty();
                        discount_on_id.append('<option value="1">Addon 1</option>');
                        discount_on_id.append('<option value="2">Addon 2</option>');
                        discount_on_id.append('<option value="3">Addon 3</option>');
                    } else {
                        discount_on_id.prop('disabled', true);
                        discount_on_id.empty();
                        discount_on_id.append('<option value="null">N/A</option>');
                    }
                });
            });
        </script>

    <?php
}