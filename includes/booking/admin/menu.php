<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

// Function to display the option page and bookings
function my_booking_plugin_option_page() {

    $bookingClass = new BookedInBookings();

    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

    // Include the template files
    include_once('templates/new-booking-form.php');
    include_once('templates/booking-table.php');

    global $wpdb;
    $bookings_table_name = $wpdb->prefix . 'bookedin_bookings';
    $resources_table_name = $wpdb->prefix . 'bookedin_resources';

    // Handle form submissions to add new bookings
    if (isset($_POST['add_booking'])) {
        $booking_date_from = sanitize_text_field($_POST['booking_date_from']);
        $booking_date_to = sanitize_text_field($_POST['booking_date_to']);
        $booking_resource = sanitize_text_field($_POST['booking_resource']);
        $booking_description = sanitize_textarea_field($_POST['booking_description']);
        $booking_paid = sanitize_text_field($_POST['booking_paid']);
        $booking_adults = sanitize_text_field($_POST['booking_adults']);
        $booking_children = sanitize_text_field($_POST['booking_children']);
        
        $booking_user = sanitize_text_field($_POST['booking_user']);
        $booking_email = sanitize_text_field($_POST['booking_email']);
        $booking_phone = sanitize_text_field($_POST['booking_phone']);

        $wpdb->insert($bookings_table_name, array(
            'booking_date_from' => $booking_date_from,
            'booking_date_to' => $booking_date_to,
            'booking_resource' => $booking_resource,
            'booking_description' => $booking_description,
            'booking_paid' => $booking_paid,
            'booking_adults' => $booking_adults,
            'booking_children' => $booking_children,
            'booking_user' => $booking_user,
            'booking_email' => $booking_email,
            'booking_phone' => $booking_phone,
        ));

        // echo $wpdb->insert_id;
            
    }

    // Handle booking deletion
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['booking_id'])) {
        $booking_id = intval($_GET['booking_id']);
        $wpdb->delete($bookings_table_name, array('id' => $booking_id));
    }

    // Fetch all bookings from the database
    $bookings = $wpdb->get_results("SELECT * FROM $bookings_table_name LEFT JOIN $resources_table_name on $resources_table_name.id = $bookings_table_name.booking_resource", ARRAY_A);

    $resources = $wpdb->get_results("SELECT * FROM $resources_table_name", ARRAY_A);
    $totalResources = count($resources);

    $bookingSlots = $wpdb->get_results("SELECT DATE(booking_date_from) as 'date', 
    ($totalResources-COUNT(*)) AS 'availableSlots'
    from wp_bookedin_bookings wbb 
    GROUP BY DATE(booking_date_from)", ARRAY_A);

    // Get the current month and year
    $currentMonth = date('n'); // n returns month without leading zeros
    $currentYear = date('Y');

    // Function to format the date in "YYYY-MM-DD" format
    function formatDate($date) {
        return date('Y-m-d', strtotime($date));
    }

    // Function to check if a date is in the current month
    function isInCurrentMonth($date, $currentMonth, $currentYear) {
        $dateMonth = date('n', strtotime($date));
        $dateYear = date('Y', strtotime($date));
        return ($dateMonth === $currentMonth) && ($dateYear === $currentYear);
    }

    // Get the current month and year from URL parameters if available, else use the current date
    if (isset($_GET['month']) && isset($_GET['year'])) {
        $currentMonth = $_GET['month'];
        $currentYear = $_GET['year'];
    } else {
        $currentMonth = date('n'); // n returns month without leading zeros
        $currentYear = date('Y');
    }

    bookedInNavigation('Dashboard');
    ?>
    <div class="wrap">

        <br>

        <h2>Add New booking</h2>
        
        <?php newBookingForm($resources) ?>

        <br>
        <br>

        <h2>Availability Calendar</h2>

        <div class="container">
            <div class="mb-3 d-flex justify-content-between align-items-center">
                <a href="?page=bookedin_main_menu&month=<?php echo ($currentMonth - 1); ?>&year=<?php echo $currentYear; ?>" class="btn btn-secondary">&lt; Previous Month</a>
                <h3 class="text-center"><?php echo date('F Y', strtotime("$currentYear-$currentMonth-01")); ?></h3>
                <a href="?page=bookedin_main_menu&month=<?php echo ($currentMonth + 1); ?>&year=<?php echo $currentYear; ?>" class="btn btn-secondary">Next Month &gt;</a>
            </div>
            <table id="availability-table" class="table table-bordered">
                <thead>
                    <tr>
                        <th colspan="7"></th>
                    </tr>
                    <tr>
                        <th>Sun</th>
                        <th>Mon</th>
                        <th>Tue</th>
                        <th>Wed</th>
                        <th>Thu</th>
                        <th>Fri</th>
                        <th>Sat</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $firstDay = date('N', strtotime("$currentYear-$currentMonth-01")); // Day of the week of the first day of the month
                    $numDays = date('t', strtotime("$currentYear-$currentMonth-01")); // Number of days in the month

                    $day = 1;
                    for ($i = 0; $i < 6; $i++) {
                        echo '<tr>';
                        for ($j = 0; $j < 7; $j++) {
                            echo '<td>';
                            if ($i === 0 && $j < $firstDay) {
                                // Empty cell before the first day of the month
                                echo '';
                            } elseif ($day <= $numDays) {
                                // Fill in the date and available slots
                                $currentDate = date("$currentYear-$currentMonth-$day");
                                $formattedDate = formatDate($currentDate);
                                $slot = array_filter($bookingSlots, function ($slot) use ($formattedDate) {
                                    return $slot['date'] === $formattedDate;
                                });

                                $availableSlots = count($slot) ? reset($slot)['availableSlots'] : $totalResources;
                                $isAvailable = strtotime($currentDate) >= strtotime('now');
                                
                                echo $day;

                                if(!$isAvailable) {
                                    echo '<br>'. ' - ';
                                } else {
                                    echo '<br>' .  $availableSlots . ' slots';
                                }
                                
                                $day++;
                            } else {
                                // Empty cell after the last day of the month
                                echo '';
                            }
                            echo '</td>';
                        }
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <br>
        <br>

        <!-- Display existing bookings -->
        <h2>Existing bookings</h2>
        
        <?php bookingTable($bookings) ?>

    </div>
    <?php
    bookInFooter();
}