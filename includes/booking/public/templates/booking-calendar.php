<?php

function bookingCalendar($display=true) {

    $resourceClass = new BookedInResources();
    $bookingClass = new BookedInBookings();
    $datesClass = new BookedInDates();

    $totalResources = $resourceClass->get_total_resources();
    $bookingSlots = $bookingClass->get_booking_slots();
    $holiday = $datesClass->get_all_holiday();

    ?>
        <div class="container" id="calendarContainer"></div>
        <div class="mt-3 d-flex justify-content-between align-items-center" id="calendarStatus"></div>

        <script>
            // Parse the PHP values into JavaScript variables
            var display = <?php echo $display ? 'true' : 'false'; ?>;
            var totalResources = <?php echo $totalResources; ?>;
            var bookingSlots = <?php echo json_encode($bookingSlots); ?>;
            var holiday = <?php echo json_encode($holiday); ?>;
            console.log(bookingSlots);
            // Get the current month and year
            var currentDate = new Date();
            var currentMonth = currentDate.getMonth() + 1; // Adding 1 to get 1-12 range
            var actualCurrentMonth = currentMonth;
            var currentYear = currentDate.getFullYear();
            var actualCurrentYear = currentYear;

            // Selected dates
            var selectedDates = [];
            var selectedStartDate = null;
            var selectedEndDate = null;

            // Function to format the date in "YYYY-MM-DD" format
            function formatDate(date) {
                var d = new Date(date);
                var month = (d.getMonth() + 1);
                var day = d.getDate();

                if (month < 10) {
                    month = '0' + month;
                }

                if (day < 10) {
                    day = '0' + day;
                }

                return d.getFullYear() + '-' + month + '-' + day;
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
                
                if (currentMonth === actualCurrentMonth && currentYear === actualCurrentYear) {
                    return;
                } 

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
                calendarHTML += '<h3 class="text-center mb-0">' + currentMonth + '-' + currentYear + '</h3>';
                calendarHTML += '<button onclick="goToNextMonth()" class="btn btn-secondary">Next Month &gt;</button>';
                calendarHTML += '</div>';
                calendarHTML += '<thead><tr><th colspan="7"></th></tr><tr><th>Sun</th><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th></tr></thead><tbody>';

                // Get the first day of the month and the number of days in the month
                var firstDay = new Date(currentYear, currentMonth - 1, 1).getDay();

                var numDays = new Date(currentYear, currentMonth, 0).getDate();

                // Get the last day of the previous month
                var prevMonthLastDay = new Date(currentYear, currentMonth - 1, 0).getDate();
                var prevMonthStartDay = prevMonthLastDay - firstDay + 1;
            
                // Get first day date
                var day = (firstDay === 0)? 1 : prevMonthStartDay;

                // Marking for next month
                var nextMonthFlag = false;

                for (var i = 0; i < 6; i++) {

                    calendarHTML += '<tr>';
                    
                    for (var j = 0; j < 7; j++) {

                        calendarHTML += '<td>';

                        var currentDate = new Date(currentYear, currentMonth - 1, day); 
                        
                        // Days from the previous month to first day of the month
                        if (i === 0 && j < firstDay) {
                            let currentDate = new Date(currentYear, currentMonth - 2, day);
                        } 
                        
                        var isAvailable = currentDate >= new Date();

                        if (isAvailable) {
                            
                            // Fill in the date and available slots
                            var formattedDate = formatDate(currentDate);

                            var slot = bookingSlots.find(function(slot) {
                                return slot.date === formattedDate;
                            });
                            
                            var availableSlots = slot ? slot.availableSlots : totalResources;
                            
                            if (nextMonthFlag || (i === 0 && j < firstDay)) {
                                calendarHTML += '<div data-slots="' + availableSlots + '" data-date="' + currentDate + '" onclick="selectDate(this)"  class="text-muted">';
                            } else {
                                calendarHTML += '<div data-slots="' + availableSlots + '" data-date="' + currentDate + '" onclick="selectDate(this)">';
                            }
    
                            calendarHTML +=  day;

                            if (availableSlots <= 0) {

                                // Days with no available slots
                                calendarHTML += '<br>' + 'Full';

                            } else {

                                // Days with available slots
                                calendarHTML += '<br>' + availableSlots + ' slots';

                            }
                            
                            calendarHTML += '</div>';
                        } else {

                            calendarHTML += '<div data-slots="0" data-date="' + currentDate + '" class="text-muted">';
                            calendarHTML += '<span>' + day + '</span>';
                            calendarHTML += '<br>' + '&#8203';
                            calendarHTML += '</div>';

                        }
                        

                        day++;

                        // From prev month transition to this month
                        if (i === 0 && day == prevMonthLastDay+1) {
                            day = 1;
                        }

                        // From this month transition to next month
                        if (day > numDays && (i !== 0)) {
                            day = 1;
                            nextMonthFlag = true;
                            nextMonth();
                        }

                        calendarHTML += '</td>';
                    }

                    calendarHTML += '</tr>';
                }
                
                calendarHTML += '</tbody></table>';

                

                calendarContainer.innerHTML = calendarHTML;

                previousMonth();
                if (!display) {
                    addHoverEvent();
                    highlightSelectedRange();
                }
            }

            // Function to generate the selected dates
            function generateSelectedDate() {
                let calendarStatus = document.getElementById('calendarStatus');
                let calendarHTML = '';

                // Output the calendar HTML
                calendarHTML += '<span id="selectedDatesOutput"></span>';
                calendarHTML += '<input type="hidden" name="booking_date_from" id="booking_date_from">';
                calendarHTML += '<input type="hidden" name="booking_date_to" id="booking_date_to">';

                calendarStatus.innerHTML = calendarHTML;
            }

            generateCalendar();
            generateSelectedDate();


            // Function to handle date selection
            function selectDate(cell) {

                if (display) {
                    return;
                }

                if (cell.getAttribute('data-slots') === '0' && selectedStartDate == null) {
                    return;
                }

                var selectedDate = new Date(cell.getAttribute('data-date'));

                // Reset
                if (selectedDate === selectedStartDate || selectDate === selectedEndDate || (selectedStartDate !== null && selectedEndDate !== null)) {
                    removeSelectedRange();
                }

                if (selectedStartDate === null) {

                    selectedStartDate = selectedDate;
                    selectedEndDate = null;
                    selectedDates.push(selectedDate);
                    cell.classList.add('selected-start');
                    outputSelectedDates();

                } else if (selectedStartDate !== null && selectedEndDate === null) {

                    if (selectedDate <= selectedStartDate) {

                        removeSelectedRange();
                        selectedStartDate = selectedDate;
                        selectedEndDate = null;
                        selectedDates.push(selectedDate);
                        cell.classList.add('selected-start');
                        outputSelectedDates();

                    } else {

                        selectedEndDate = selectedDate;
                        selectedDates.push(selectedDate);
                        cell.classList.add('selected-end');
                        
                        // Add all dates in between to the array
                        var currentDate = new Date(selectedStartDate);
                        var oneDayBeforeLastDate = new Date(selectedEndDate);
                        oneDayBeforeLastDate.setDate(oneDayBeforeLastDate.getDate() - 1);

                        while (currentDate.getTime() !== oneDayBeforeLastDate.getTime()) {
                            currentDate.setDate(currentDate.getDate() + 1);
                            selectedDates.push(new Date(currentDate));
                        }

                        // Sort the selected dates array
                        selectedDates.sort(function(a, b) {
                            return a.getTime() - b.getTime();
                        });

                        highlightSelectedRange();
                    }
                    
                }

            }

            // Function that add listener when mouse over the date
            function addHoverEvent() {

                var allCells = document.querySelectorAll('#availability-table div[data-slots]');
                
                allCells.forEach(function(cell) {
                    
                    cell.addEventListener('mouseover', function(elem) {
                        
                        // Get the date of the cell
                        var selectedDate = new Date(cell.getAttribute('data-date'));
                        
                        // Check if the cell date that is hovering on is after selected start date
                        if (selectedStartDate !== null && selectedEndDate === null && selectedDate > selectedStartDate) {
                            
                            // Highlight all the cell from the selected start date until on hover date
                            var currentDate = new Date(selectedStartDate);
                            var hoverOnDate = new Date(selectedDate);

                            while (currentDate.getTime() !== hoverOnDate.getTime()) {
                                currentDate.setDate(currentDate.getDate() + 1);
                                
                                // Get the cell of the current date
                                var currentCell = document.querySelector('#availability-table div[data-date="' + currentDate + '"]');
                                if (currentCell) {
                                    currentCell.classList.add('selected-middle');
                                }
                            }

                        } else if (selectedStartDate !== null && selectedEndDate === null && selectedDate < selectedStartDate) {
                            
                            // Highlight only the cell that is being hovered on
                            cell.classList.add('selected-middle');

                        } else if (selectedStartDate == null && selectedEndDate == null) {
                            
                            if (selectedDate >= new Date()) {
                                cell.classList.add('selected-middle');
                            }
                        }

                    });

                    cell.addEventListener('mouseout', function(elem) {
                        
                        // Get the date of the cell
                        var selectedDate = new Date(cell.getAttribute('data-date'));

                        if (selectedStartDate !== null && selectedEndDate === null) {
                            var allCells = document.querySelectorAll('#availability-table div[data-slots].selected-middle');
                            allCells.forEach(function(cell) {
                                cell.classList.remove('selected-middle');
                            });
                        } else if (selectedStartDate == null && selectedEndDate == null) {

                            if (selectedDate >= new Date()) {
                                cell.classList.remove('selected-middle');
                            }
                        }
                    });
                });


            }

            // Function to remove all selected range
            function removeSelectedRange() {
                selectedStartDate = null;
                selectedEndDate = null;
                selectedDates = [];

                // Remove the selected range
                var allCells = document.querySelectorAll('#availability-table div[data-slots]');
                allCells.forEach(function(cell) {
                    cell.classList.remove('selected-start');
                    cell.classList.remove('selected-middle');
                    cell.classList.remove('selected-end');
                });
            }

            // Function to highlight the selected date range
            function highlightSelectedRange() {
                
                if (selectedStartDate === null) {
                    return;
                }

                for (let i = 0; i < selectedDates.length; i++) {
                    
                    var date = selectedDates[i];
                    var cell = document.querySelector('#availability-table div[data-date="' + date + '"]');
                    
                    if (cell) {

                        if (cell.getAttribute('data-slots') === '0' && i !== selectedDates.length-1) {
                            removeSelectedRange();
                            return;
                        }

                        if (i === 0) {
                            cell.classList.add('selected-start');
                        } else if (i === selectedDates.length - 1) {
                            cell.classList.add('selected-end');
                        } else {
                            cell.classList.add('selected-middle');
                        }

                    }      
                }

                outputSelectedDates();
            }

            // Function to format the date in yyyy-mm-dd format
            function formatDateToYYYYMMDD(date) {
                if (date === null) {
                    return null;
                }
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            }

            // Function to output the selected dates
            function outputSelectedDates() {
                var outputElement = document.getElementById('selectedDatesOutput');
                var outputStartElement = document.getElementById('booking_date_from');
                var outputEndElement = document.getElementById('booking_date_to');
                if (selectedStartDate && selectedEndDate) {
                    var startDateString = selectedStartDate.toLocaleDateString();
                    var endDateString = selectedEndDate.toLocaleDateString();
                    outputElement.innerHTML = 'Selected Dates: ' + startDateString + ' to ' + endDateString;

                    outputStartElement.value = formatDateToYYYYMMDD(selectedStartDate);
                    outputEndElement.value = formatDateToYYYYMMDD(selectedEndDate);

                } else {
                    outputElement.innerHTML = 'Select a date range';
                    outputStartElement.value = formatDateToYYYYMMDD(selectedStartDate);
                    outputEndElement.value = formatDateToYYYYMMDD(selectedEndDate);

                }
            }

        </script>

    <?php

}
