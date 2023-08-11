<?php

function bookingCalendar() {

    $resourceClass = new BookedInResources();
    $bookingClass = new BookedInBookings();

    $totalResources = $resourceClass->get_total_resources();
    $bookingSlots = $bookingClass->get_booking_slots();

    ?>
        <div class="container" id="calendarContainer"></div>


        <script>
            // Parse the PHP values into JavaScript variables
            var totalResources = <?php echo $totalResources; ?>;
            var bookingSlots = <?php echo json_encode($bookingSlots); ?>;
            
            // Function to format the date in "YYYY-MM-DD" format
            function formatDate(date) {
                var d = new Date(date);
                var month = (d.getMonth() + 1);

                if (month < 10) {
                    month = '0' + month;
                }

                return d.getFullYear() + '-' + month + '-' + d.getDate();
            }
            
            // Get the current month and year
            var currentDate = new Date();
            var currentMonth = currentDate.getMonth() + 1; // Adding 1 to get 1-12 range
            var currentYear = currentDate.getFullYear();

             // Function to navigate to the previous month
            function goToPreviousMonth() {
                currentMonth--;
                if (currentMonth < 1) {
                    currentMonth = 12;
                    currentYear--;
                }
                generateCalendar();
            }

            // Function to navigate to the next month
            function goToNextMonth() {
                currentMonth++;
                if (currentMonth > 12) {
                    currentMonth = 1;
                    currentYear++;
                }
                generateCalendar();
            }

            function generateCalendar() {
            
                var calendarContainer = document.getElementById('calendarContainer');
                var calendarHTML = '<table id="availability-table" class="table table-bordered">';
                calendarHTML += '<div class="mb-3 d-flex justify-content-between align-items-center">';
                calendarHTML += '<button onclick="goToPreviousMonth()" class="btn btn-secondary">&lt; Previous Month</button>';
                calendarHTML += '<h3 class="text-center">' + currentMonth + '-' + currentYear + '</h3>';
                calendarHTML += '<button onclick="goToNextMonth()" class="btn btn-secondary">Next Month &gt;</button>';
                calendarHTML += '</div>';
                calendarHTML += '<thead><tr><th colspan="7"></th></tr><tr><th>Sun</th><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th></tr></thead><tbody>';

                var firstDay = new Date(currentYear, currentMonth - 1, 1).getDay();
                var numDays = new Date(currentYear, currentMonth, 0).getDate();

                var prevMonthLastDay = new Date(currentYear, currentMonth - 1, 0).getDate();
                var prevMonthStartDay = prevMonthLastDay - firstDay + 1;
                
                var day = 1;
                
                for (var i = 0; i < 6; i++) {

                    calendarHTML += '<tr>';
                    
                    for (var j = 0; j < 7; j++) {

                        calendarHTML += '<td>';

                        if (i === 0 && j < firstDay) {
                            // Days from the previous month
                            calendarHTML += '<span class="text-muted">' + prevMonthStartDay + '</span>';
                            prevMonthStartDay++;
                        } else if (day <= numDays) {
                            // Fill in the date and available slots
                            var currentDate = new Date(currentYear, currentMonth - 1, day);
                            var formattedDate = formatDate(currentDate);

                            var slot = bookingSlots.find(function(slot) {
                                return slot.date === formattedDate;
                            });

                            var availableSlots = slot ? slot.availableSlots : totalResources;
                            var isAvailable = currentDate >= new Date();

                            if (!isAvailable || availableSlots <= 0) {
                                calendarHTML += '<span class="text-muted">' + day + '</span>';
                                calendarHTML += '<br>' + '&#8203';
                            } else {
                                calendarHTML += day;
                                calendarHTML += '<br>' + availableSlots + ' slots';
                            }

                            day++;

                            if (day > numDays) {
                                day = 1;
                                
                                currentMonth++;
                                if (currentMonth > 12) {
                                    currentMonth = 1;
                                    currentYear++;
                                }
                            }

                        }

                        calendarHTML += '</td>';
                    }
                    calendarHTML += '</tr>';
                }

                calendarHTML += '</tbody></table>';

                calendarContainer.innerHTML = calendarHTML;

                currentMonth--;
                if (currentMonth < 1) {
                    currentMonth = 12;
                    currentYear--;
                }
            }

            generateCalendar();

        </script>

    <?php

}
