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
        $discount_on = sanitize_text_field($_POST['discount_on']);
        $discount_condition = sanitize_text_field($_POST['discount_condition']);
        $discount_auto_apply = sanitize_text_field($_POST['discount_auto_apply']);
        $discount_active = sanitize_text_field($_POST['discount_active']);

        $pricingClass->add_discount($discount_name, $discount_description, $discount_code, $discount_type, $discount_amount, $discount_start_date, $discount_end_date, $discount_on, $discount_condition, $discount_auto_apply, $discount_active);

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
                <option value="P">Percentage</option>
                <option value="F">Fixed</option>
            </select>
            <label for="discount_amount">Amount:</label>
            <input type="text" name="discount_amount" required>
            <br>
            <label for="discount_start_date">Start Date:</label>
            <input type="date" name="discount_start_date">
            <label for="discount_end_date">End Date:</label>
            <input type="date" name="discount_end_date">
            <br>
            <label for="discount_on">On:</label>
            <select name="discount_on">
                <option value="ALL">All</option>
                <option value="Resources">Resources</option>
                <option value="Addon">Addon</option>
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
    <?php
}