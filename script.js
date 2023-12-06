$(function () {
  $('#datefilter').daterangepicker(
    {
      minDate: '01/01/2024',
      maxDate: '01/31/2024',
      autoUpdateInput: false,
      locale: {
        cancelLabel: 'Clear',
      },
      isInvalidDate: function (date) {
        // Add logic to disable specific dates
        var disabledDates = ['2024-01-01', '2024-01-31']; // Example disabled dates

        //INSERT ARRAY HERE WITH DATES THAT ARE DISABLED

        // Convert the date to a string in the format YYYY-MM-DD
        var dateString = date.format('YYYY-MM-DD');

        // Check if the date is in the array of disabled dates
        return disabledDates.includes(dateString);
      },
    },
    function (start, end, label) {
      console.log(
        'New date range selected: ' +
          start.format('YYYY-MM-DD') +
          ' to ' +
          end.format('YYYY-MM-DD') +
          ' (predefined range: ' +
          label +
          ')'
      );
    }
  );

  $('input[name="datefilter"]').on(
    'apply.daterangepicker',
    function (ev, picker) {
      $(this).val(
        picker.startDate.format('MM/DD/YYYY') +
          ' - ' +
          picker.endDate.format('MM/DD/YYYY')
      );
    }
  );

  $('input[name="datefilter"]').on(
    'cancel.daterangepicker',
    function (ev, picker) {
      $(this).val('');
    }
  );
});
