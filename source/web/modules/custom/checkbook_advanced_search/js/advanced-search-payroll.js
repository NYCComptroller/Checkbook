(function ($, Drupal, drupalSettings) {
  Drupal.advancedSearchAndAlertsPayroll = {
    'advanced_search_payroll_init': function () {
      advanced_search_payroll_init_autocomplete();

      $('#payroll-advanced-search').each(function () {
        $(this).focusout(function () {
          advanced_search_payroll_init_autocomplete();
        });
      });

      //On change of data source
      ///checkbook_advanced_search_clear_button.js sets this value by default
      $('input:radio[name=payroll_advanced_search_domain_filter]').click(function () {
        let dataSource = $('input[name=payroll_advanced_search_domain_filter]:checked').val();
        onChangeDataSource(dataSource);
      });

      $('input:submit[id^="edit-payroll-clear"]').click(function (e) {
        let dataSource = 'checkbook';
        let href = window.location.href;
        if (href.indexOf('datasource/checkbook_nycha') !== -1) {
          dataSource = 'checkbook_nycha';
        }
        $(':radio[name="payroll_advanced_search_domain_filter"][value="' + dataSource + '"]').click();
        e.preventDefault();
      });

      function onChangeDataSource(dataSource) {
        /** Hide Fiscal Year values for NYCHA **/
        if (dataSource === 'checkbook_nycha') {
          $(".form-item-checkbook-payroll-agencies").hide();
        } else {
          $(".form-item-checkbook-payroll-agencies").show();
        }

        //Payroll Years
        $("select[name='payroll_year'] option").each(function() {
          if (dataSource === 'checkbook_nycha') {
            if (!(/^cy/.test(this.value))) {
              // Hide Calendar Years for NYCHA Payroll
              $("select[name='payroll_year'] option[value='" + this.value + "']").attr('disabled','disabled').hide();
            }
            else {
              this.text = this.text.replace('CY', 'FY');
            }
          }
          else{
            if (!(/^cy/.test(this.value))) {
              // Show Calendar Years
              $("select[name='payroll_year'] option[value='" + this.value + "']").removeAttr('disabled').show();
            }
            else {
              this.text = this.text.replace('FY', 'CY');
            }
          }
        });
        clearInputFields("#payroll-advanced-search", 'payroll', dataSource);
      }

      function advanced_search_payroll_init_autocomplete() {
        let pay_frequency = $('select[name=payroll_pay_frequency]').val() || 0;
        let year = $('select[name=payroll_year]').val() || 0;
        let data_source = $('input[name=payroll_advanced_search_domain_filter]:checked').val();
        let agency_id = 0;

        let solr_datasource = data_source;
        if ('checkbook_nycha' === data_source) {
          solr_datasource = 'nycha';
        } else {
          agency_id = $('select[name=checkbook_payroll_agencies]').val() || 0;
        }

        let filters = {
          agency_id: agency_id,
          year: year,
          pay_frequency: pay_frequency
        };

         $("input[name='payroll_employee_name']").autocomplete({source: $.fn.autoCompleteSourceUrl(solr_datasource,'civil_service_title',filters)});
      }
      //display form depending on domain filter radiobox
      let dataSource = $('input:radio[name=payroll_advanced_search_domain_filter]:checked').val() ? $('input:radio[name=payroll_advanced_search_domain_filter]:checked').val() : "checkbook";
      onChangeDataSource(dataSource);
    }
  };
}(jQuery, Drupal, drupalSettings));
