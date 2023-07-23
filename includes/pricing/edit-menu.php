<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

function pricing_edit_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'bookedin_pricings';

    if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['pricing_id'])) {
        $pricing_id = intval($_GET['pricing_id']);
        $pricing = $wpdb->get_row("SELECT * FROM $table_name WHERE id = $pricing_id", ARRAY_A);
    }

    if (isset($_POST['update_pricing'])) {
        $pricing_name = sanitize_text_field($_POST['pricing_name']);
        $pricing_description = sanitize_textarea_field($_POST['pricing_description']);

        $wpdb->update($table_name, array(
            'pricing_name' => $pricing_name,
            'pricing_description' => $pricing_description
        ), array('id' => $pricing_id));

        wp_redirect(admin_url('admin.php?page=bookedin_pricing_submenu'));
        exit;
    }

    bookedInNavigation('Pricing');
    ?>
    <div class="wrap">
        <h1>Edit pricing</h1>
        <form method="post" action="">
            <label for="pricing_name">pricing Name:</label>
            <input type="text" name="pricing_name" value="<?php echo esc_attr($pricing['pricing_name']); ?>" required>
            <label for="pricing_description">pricing_description:</label>
            <textarea name="pricing_description"><?php echo esc_textarea($pricing['pricing_description']); ?></textarea>
            <input type="submit" name="update_pricing" value="Update pricing">
        </form>
    </div>
    <?php
    bookInFooter();
}