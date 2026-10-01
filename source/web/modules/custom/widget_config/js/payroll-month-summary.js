(function ($, Drupal, once, drupalSettings) {
  'use strict';

  Drupal.behaviors.payrollMonthSummary = {
    attach: function (context, settings) {
      const payrollSettings = settings.payroll || {};

      // Toggle functionality
      once('month-toggle', '.toggleEmployee', context).forEach(function (element) {
        $(element).on('click', function () {
          $('.emp-record-salaried').toggle();
          $('.emp-record-non-salaried').toggle();
        });
      });

      // Set default view
      if (payrollSettings.defaultView === 'Salaried') {
        $('.emp-record-salaried').show();
        $('.emp-record-non-salaried').hide();
      } else {
        $('.emp-record-salaried').hide();
        $('.emp-record-non-salaried').show();
      }
    }
  };

})(jQuery, Drupal, once, drupalSettings);
