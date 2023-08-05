


// Function to format the date range as a string
function formatDateRange(startDate, endDate) {
    const start = startDate.toLocaleDateString('en-US', { year: 'numeric', month: '2-digit', day: '2-digit' });
    const end = endDate.toLocaleDateString('en-US', { year: 'numeric', month: '2-digit', day: '2-digit' });
    return `${start} - ${end}`;
}

// Initialize the Bootstrap Datepicker
$(document).ready(function () {
    $('#booking_dates').datepicker({
        format: 'yyyy-mm-dd',
        multidate: 2,
        startDate: new Date(),
        showOnFocus: true, // Calendar won't close on first selection
        autoclose: false,
        multidateSeparator: ' - ',
    });

    // Show the datepicker when the input field is focused
    $('#booking_dates').focus(function () {
        $(this).datepicker('show');
    });
});

// Function to format the date in yyyy-mm-dd format
function formatDateToYYYYMMDD(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

// Set the selected date range into the hidden input fields before form submission
$('form').submit(function (e) {
    const selectedDates = $('#booking_dates').datepicker('getDates');
    if (selectedDates.length === 2) {
        
        if (selectedDates[0] > selectedDates[1]) {
            const temp = selectedDates[0];
            selectedDates[0] = selectedDates[1];
            selectedDates[1] = temp;
        }

        const startDate = formatDateToYYYYMMDD(selectedDates[0]);
        const endDate = formatDateToYYYYMMDD(selectedDates[1]);
        $('[name="booking_date_from"]').val(startDate);
        $('[name="booking_date_to"]').val(endDate);
    }
    else {
        alert('Please select a date range.');
        e.preventDefault();
    }
});