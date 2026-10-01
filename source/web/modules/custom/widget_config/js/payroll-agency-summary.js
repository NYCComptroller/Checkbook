(function ($, Drupal, once) {
  'use strict';

  Drupal.behaviors.payrollAgencySummary = {
    attach: function (context, settings) {
      once('payroll-agency-init', '.payroll-emp-wrapper', context).forEach(function (wrapper) {
        const $wrapper = $(wrapper);
        const defaultView = $wrapper.data('default-view');
        const salariedCount = parseInt($wrapper.data('salaried-count'), 10) || 0;
        const nonSalariedCount = parseInt($wrapper.data('non-salaried-count'), 10) || 0;

        // Initialize cycle for salaried records if more than 1 slide
        if (salariedCount > 1) {
          const $salariedRecords = $('#emp-agency-detail-records-salaried', wrapper).filter(':first');
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

        // Initialize cycle for non-salaried records if more than 1 slide
        if (nonSalariedCount > 1) {
          const $nonSalariedRecords = $('#emp-agency-detail-records-non-salaried', wrapper).filter(':first');
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
        once('payroll-toggle-click', '.toggleEmployee', wrapper).forEach(function (toggleElement) {
          $(toggleElement).on('click', function (e) {
            e.preventDefault();
            $('.emp-record-salaried', wrapper).toggle();
            $('.emp-record-non-salaried', wrapper).toggle();
          });
        });

        // Set default view
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
