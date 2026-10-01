(function ($, Drupal, once) {
  'use strict';

  Drupal.behaviors.payrollEmployee = {
    attach: function (context, settings) {
      once('payroll-employee-init', '.payroll-emp-wrapper', context).forEach(function (wrapper) {
        const $wrapper = $(wrapper);
        const defaultView = $wrapper.data('default-view');
        const salariedCount = parseInt($wrapper.data('salaried-count'), 10);
        const nonSalariedCount = parseInt($wrapper.data('non-salaried-count'), 10);

        // Initialize cycle for salaried if needed
        if (salariedCount > 1) {
          const $salariedRecords = $('#emp-agency-detail-records-salaried', context);
          if ($salariedRecords.length > 0) {
            $salariedRecords.cycle({
              slideExpr: '.emp-agency-detail-record',
              prev: '#prev-emp-salaried',
              next: '#next-emp-salaried',
              fx: 'scrollVert',
              speed: 0,
              width: '640px',
              timeout: 0
            });
          }
        }

        // Initialize cycle for non-salaried if needed
        if (nonSalariedCount > 1) {
          const $nonSalariedRecords = $('#emp-agency-detail-records-non-salaried', context);
          if ($nonSalariedRecords.length > 0) {
            $nonSalariedRecords.cycle({
              slideExpr: '.emp-agency-detail-record',
              prev: '#prev-emp-non-salaried',
              next: '#next-emp-non-salaried',
              fx: 'scrollVert',
              speed: 0,
              width: '640px',
              timeout: 0
            });
          }
        }

        // Toggle functionality
        once('payroll-toggle-click', '.toggleEmployee', context).forEach(function (toggleElement) {
          $(toggleElement).on('click', function (e) {
            e.preventDefault();
            $('.emp-record-salaried').toggle();
            $('.emp-record-non-salaried').toggle();
          });
        });

        // Set default view - show/hide based on default
        if (defaultView === 'Salaried') {
          $('.emp-record-salaried', wrapper).show();
          $('.emp-record-non-salaried', wrapper).hide();
        } else {
          $('.emp-record-salaried', wrapper).hide();
          $('.emp-record-non-salaried', wrapper).show();
        }
      });
    }
  };

})(jQuery, Drupal, once);
