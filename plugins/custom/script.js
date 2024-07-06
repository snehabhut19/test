jQuery(document).ready(function($) {
    // Initialize Flatpickr for date and time picker
    $("#pill-datetime").flatpickr({
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        minDate: "today" // Optionally, restrict selection to today and onwards
    });

    // Handle form submission
    $('#pill-reminder-form').on('submit', function(event) {
        event.preventDefault();

        var formData = {
            'action': 'add_pill_reminder',
            'pill_name': $('#pill-name').val(),
            'pill_datetime': $('#pill-datetime').val()
        };

        // AJAX POST request
        $.post(pill_reminder_ajax.ajaxurl, formData, function(response) {
            $('#pill-reminder-message').text(response);
            $('#pill-reminder-form')[0].reset(); // Reset form after successful submission
        });
    });
});
