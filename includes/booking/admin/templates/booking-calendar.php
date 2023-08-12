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
                   
            // Get the current month and year
            var currentDate = new Date();
            var currentMonth = currentDate.getMonth() + 1; // Adding 1 to get 1-12 range
            var currentYear = currentDate.getFullYear();

            // Function to format the date in "YYYY-MM-DD" format
            function formatDate(date) {
                var d = new Date(date);
                var month = (d.getMonth() + 1);

                if (month < 10) {
                    month = '0' + month;
                }

                return d.getFullYear() + '-' + month + '-' + d.getDate();
            }

            // Previous month
            function previousMonth() {
                currentMonth--;
                if (currentMonth < 1) {
                    currentMonth = 12;
                    currentYear--;
                }
            }

             // Function to navigate to the previous month
            function goToPreviousMonth() {
                previousMonth();
                generateCalendar();
            }

            // Next month
            function nextMonth() {
                currentMonth++;
                if (currentMonth > 12) {
                    currentMonth = 1;
                    currentYear++;
                }
            }

            // Function to navigate to the next month
            function goToNextMonth() {
                nextMonth();
                generateCalendar();
            }

            // Function to generate the calendar
            function generateCalendar() {
            
                // Get the calendar container
                var calendarContainer = document.getElementById('calendarContainer');
                
                // Generate the calendar HTML
                var calendarHTML = '<table id="availability-table" class="table table-bordered">';
                calendarHTML += '<div class="mb-3 d-flex justify-content-between align-items-center">';
                calendarHTML += '<button onclick="goToPreviousMonth()" class="btn btn-secondary">&lt; Previous Month</button>';
                calendarHTML += '<h3 class="text-center">' + currentMonth + '-' + currentYear + '</h3>';
                calendarHTML += '<button onclick="goToNextMonth()" class="btn btn-secondary">Next Month &gt;</button>';
                calendarHTML += '</div>';
                calendarHTML += '<thead><tr><th colspan="7"></th></tr><tr><th>Sun</th><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th></tr></thead><tbody>';

                // Get the first day of the month and the number of days in the month
                var firstDay = new Date(currentYear, currentMonth - 1, 1).getDay();
                var numDays = new Date(currentYear, currentMonth, 0).getDate();

                // Get the last day of the previous month
                var prevMonthLastDay = new Date(currentYear, currentMonth - 1, 0).getDate();
                var prevMonthStartDay = prevMonthLastDay - firstDay + 1;
                
                var day = 1;
                
                for (var i = 0; i < 6; i++) {

                    calendarHTML += '<tr>';
                    
                    for (var j = 0; j < 7; j++) {

                        calendarHTML += '<td>';

                        var currentDate = new Date(currentYear, currentMonth - 1, day);
                        var isAvailable = currentDate >= new Date();

                        // Check if the current date is available
                        if (isAvailable) {

                            // Fill in the date and available slots
                            var formattedDate = formatDate(currentDate);

                            var slot = bookingSlots.find(function(slot) {
                                return slot.date === formattedDate;
                            });

                            var availableSlots = slot ? slot.availableSlots : totalResources;
                            
                            calendarHTML += '<div data-slots="' + availableSlots + '" data-date="' + currentDate + '" onclick="selectDate(this)">';
                            calendarHTML +=  day;

                            if (availableSlots <= 0) {

                                // Days with no available slots
                                calendarHTML += '<br>' + '&#8203';

                            } else {

                                // Days with available slots
                                calendarHTML += '<br>' + availableSlots + ' slots';

                            }
                            
                            calendarHTML += '</div>';

                            day++;

                            if (day > numDays) {
                                day = 1;
                                
                                nextMonth();
                            }

                        } else {

                            if (i === 0 && j < firstDay) {

                                // Days from the previous month to first day of the month
                                let prevMonthDate = new Date(currentYear, currentMonth - 2, prevMonthStartDay);
                                calendarHTML += '<div data-slots="0" data-date="' + prevMonthDate + '" class="text-muted">';
                                calendarHTML += '<span>' + prevMonthStartDay + '</span>';
                                prevMonthStartDay++;

                            } else {

                                // Days from first day to current day
                                calendarHTML += '<div data-slots="0" data-date="' + currentDate + '" class="text-muted">';
                                calendarHTML += '<span>' + day + '</span>';
                                day++;

                            }
                            
                            calendarHTML += '<br>' + '&#8203';
                            calendarHTML += '</div>';

                        }

                        calendarHTML += '</td>';
                    }

                    calendarHTML += '</tr>';
                }
                
                calendarHTML += '</tbody></table>';

                // Output the calendar HTML
                calendarHTML += '<div class="mt-3 d-flex justify-content-between align-items-center">';
                calendarHTML += '<span id="selectedDatesOutput"></span>';
                calendarHTML += '</div>'; 

                calendarContainer.innerHTML = calendarHTML;

                previousMonth();
            }

            generateCalendar();


            // Selected dates
            var selectedDates = [];
            var selectedStartDate = null;
            var selectedEndDate = null;

            // Function to handle date selection
            function selectDate(cell) {

                var selectedDate = new Date(cell.getAttribute('data-date'));
                
                // Check if the selectedDate is in array
                var dateIndex = selectedDates.findIndex(function(date) {
                    return date.getTime() === selectedDate.getTime();
                });

                if (dateIndex > -1) {
                    // Remove the date from the array
                    selectedDates.splice(dateIndex, 1);
                    cell.classList.remove('selected');



                } else {
                    // Add the date to the array
                    selectedDates.push(selectedDate);
                    cell.classList.add('selected');

                    if (selectedDates.length >= 2) {
                    
                        // Sort the selected dates
                        selectedDates.sort(function(a, b) {
                            return a.getTime() - b.getTime();
                        });

                        // Add all dates in between to the array
                        var currentDate = new Date(selectedDates[0]);
                        var oneDayBeforeLastDate = new Date(selectedDates[selectedDates.length - 1]);
                        oneDayBeforeLastDate.setDate(oneDayBeforeLastDate.getDate() - 1);

                        while (currentDate.getTime() !== oneDayBeforeLastDate.getTime()) {
                            currentDate.setDate(currentDate.getDate() + 1);
                            selectedDates.push(new Date(currentDate));
                        }

                        // Sort the selected dates
                        selectedDates.sort(function(a, b) {
                            return a.getTime() - b.getTime();
                        });

                        selectedStartDate = selectedDates[0];
                        selectedEndDate = selectedDates[selectedDates.length - 1];

                        highlightSelectedRange();
                        outputSelectedDates();
                    
                    }
                }
            }

            // Function to remove all selected range
            function removeSelectedRange() {
                selectedStartDate = null;
                selectedEndDate = null;
                selectedDates = [];

                // Remove the selected range
                var allCells = document.querySelectorAll('#availability-table div[data-slots].selected');
                allCells.forEach(function(cell) {
                    cell.classList.remove('selected');
                });
            }

            // Function to highlight the selected date range
            function highlightSelectedRange() {
                
                selectedDates.forEach(function(date) {
                    var cell = document.querySelector('#availability-table div[data-date="' + date + '"]');
                    cell.classList.add('selected');
                });

            }

            // Function to output the selected dates
            function outputSelectedDates() {
                var outputElement = document.getElementById('selectedDatesOutput');
                if (selectedStartDate && selectedEndDate) {
                    var startDateString = selectedStartDate.toLocaleDateString();
                    var endDateString = selectedEndDate.toLocaleDateString();
                    outputElement.innerHTML = 'Selected Dates: ' + startDateString + ' to ' + endDateString;
                } else {
                    outputElement.innerHTML = 'Select a date range';
                }
            }

        </script>

    <?php

}
