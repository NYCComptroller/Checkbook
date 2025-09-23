(function ($) {
  /**
   * Function will show or hide fields based on datasource selection
   * @param datasource
   */
  $.fn.showHideFields = function (datasource) {
    switch (datasource) {
      case 'checkbook_oge':
        $('.default-fields').show();
        $('.nycha-fields').hide();

        $('.default-fields .datafield.commodityline').show();
        $('.default-fields .datafield.entity_contract_number').show();
        $('.default-fields .datafield.budgetname').show();

        $('.default-fields .datafield.mwbecategory').hide();
        $('.default-fields .datafield.scntrc_status').hide();
        $('.default-fields .datafield.regdate').hide();

        $('.default-fields .datafield.sub_contract_status_id').hide();
        $('.default-fields .datafield.conditional_category').hide();
        $('.default-fields .datafield.industry').hide();

        $("#edit-df-contract-status").children("option[value='pending']").hide();
        $("#edit-category").children("option[value='all']").hide();
        $("#edit-category").children("option[value='revenue']").hide();
        $("#edit-apt-pin").attr('disabled', 'disabled');

        // Moving fields to match with Advanced search form order.
        $('.default-fields .datafield.pin').insertAfter('.default-fields .datafield.purpose');
        $('.default-fields .datafield.receiveddate.datarange').insertAfter('.default-fields .datafield.currentamt.datarange');
        $('.default-fields .datafield.enddate.datarange').insertAfter('.default-fields .datafield.startdate.datarange');
        $('.datafield.agency').hide();

        break;

      case 'checkbook_nycha':
        $('.default-fields').hide();
        $('.nycha-fields').show();
        $('.datafield.agency').hide();
        break;

      case 'checkbook':
      default:
        $('.default-fields').show();
        $('.nycha-fields').hide();

        $('.default-fields .datafield.commodityline').hide();
        $('.default-fields .datafield.entity_contract_number').hide();
        $('.default-fields .datafield.budgetname').hide();

        $('.default-fields .datafield.mwbecategory').show();
        $('.default-fields .datafield.scntrc_status').show();
        $('.default-fields .datafield.regdate').show();
        $('.default-fields .datafield.sub_contract_status_id').show();
        $('.default-fields .datafield.conditional_category').show();

        $('.default-fields .datafield.industry').show();

        $("#edit-df-contract-status").children("option[value='pending']").show();
        $("#edit-category").children("option[value='all']").show();
        $("#edit-category").children("option[value='revenue']").show();
        $("#edit-apt-pin").removeAttr('disabled');

        // Moving fields to match with Advanced search form order.
        $('.default-fields .datafield.pin').insertAfter('.default-fields .datafield.sub_contract_status_id');
        $('.default-fields .datafield.receiveddate.datarange').insertAfter('.default-fields .datafield.startdate.datarange');
        $('.default-fields .datafield.enddate.datarange').insertAfter('.default-fields .datafield.currentamt.datarange');
        $('.datafield.agency').show();
    }
  };

  /**
   * Function will add the asterisk icon css from a field
   */
  $.fn.showHidePrimeAndSubIcon = function () {
    const note = jQuery(".prime-and-sub-note-datafeeds");
    const contract_status = jQuery(".contractstatus");
    const vendor = jQuery(".vendor");
    const mwbe_category = jQuery(".mwbecategory");
    const current_amt_from = jQuery(".currentamt");
    const category = jQuery(".category");
    const sub_contract_status = jQuery(".sub_contract_status_id");
    const purpose = jQuery(".purpose");
    const industry = jQuery(".industry");
    const year = jQuery(".year");

    // Remove all asterisk fields & note
    note.remove();
    $.fn.removePrimeAndSubIcon(contract_status);
    $.fn.removePrimeAndSubIcon(vendor);
    $.fn.removePrimeAndSubIcon(mwbe_category);
    $.fn.removePrimeAndSubIcon(current_amt_from);
    $.fn.removePrimeAndSubIcon(category);
    $.fn.removePrimeAndSubIcon(sub_contract_status);
    $.fn.removePrimeAndSubIcon(purpose);
    $.fn.removePrimeAndSubIcon(industry);
    $.fn.removePrimeAndSubIcon(year);

    const contract_status_val = jQuery("select[name=df_contract_status]").val();
    const category_val = jQuery("select[name=category]").val();
    if ($("input[name='datafeeds-contracts-domain-filter']:checked").val() === 'checkbook') {
      // Add asterisk fields & note
      if ((contract_status_val === 'active' || contract_status_val === 'registered')
        && (category_val === 'expense' || category_val === 'all')) {

        jQuery("<div class='prime-and-sub-note-datafeeds'><p>All Fields are searchable by Prime data, unless designated as Prime & Sub (<img src='/themes/custom/nyccheckbook/images/prime-and-sub.png' />).</p><br/></div>").insertAfter(jQuery("p.required-message"));

        $.fn.addPrimeAndSubIcon(contract_status);
        $.fn.addPrimeAndSubIcon(vendor);
        $.fn.addPrimeAndSubIcon(mwbe_category);
        $.fn.addPrimeAndSubIcon(current_amt_from);
        $.fn.addPrimeAndSubIcon(category);
        $.fn.addPrimeAndSubIcon(sub_contract_status);
        $.fn.addPrimeAndSubIcon(purpose);
        $.fn.addPrimeAndSubIcon(industry);
        $.fn.addPrimeAndSubIcon(year);
      }
    }
  };

  /**
   * Function will remove the asterisk icon css from a field
   * @param ele
   */
  $.fn.removePrimeAndSubIcon = function (ele) {
    ele.find('.prime-and-sub-datafeeds').remove();
    ele.removeClass('asterisk-style');
  };

  /**
   * Function will add the asterisk icon css to a field
   * @param ele
   */
  $.fn.addPrimeAndSubIcon = function (ele) {
    const primeAndSubIcon = "<img class='prime-and-sub-datafeeds' src='/themes/custom/nyccheckbook/images/prime-and-sub.png' />";
    jQuery(ele).find('label').first().prepend(primeAndSubIcon);
    jQuery(ele).find('.span-label').first().prepend(primeAndSubIcon);
    ele.addClass('asterisk-style');
  };

  /**
   * Function will make changes to the form based on data-souce selection
   * @param dataSource
   */
  $.fn.onDataSourceChange = function (dataSource) {
    //Remove all the validation errors when data source is changed
    $('div.messages').remove();
    $('.error').removeClass('error');

    //Show or hide fields based on data-source selection
    $.fn.showHideFields(dataSource);

    //Agency drop-down options
    $.fn.reloadAgencies(dataSource);

    // Reset year drop downs
    $('#edit-year').removeAttr('disabled');
    $('#edit-nycha-year').removeAttr('disabled');

    // Reset covid filter
    $("#edit-conditional_category").removeAttr('disabled');
    updateYearValue('0');

    if (dataSource !== 'checkbook_nycha') {
      //Change the Agency drop-down label
      const vendor_label = (dataSource === 'checkbook_oge') ? 'Prime Vendor:' : 'Vendor:';
      $("label[for = edit-vendor]").text(vendor_label);

      //Clear text fields and drop-downs
      $.fn.clearInputFields(dataSource);
      //Reset 'sub-contract status' and 'contracts include sub-vendors' drop-downs
      $.fn.subVendorStatusInPipChange(0, 0);

      //reset the selected columns
      $.fn.resetSelectedColumns();

      if ($('#edit-year').val() === '0') {
        $('#edit-column-select-expense option[value="Year"]').attr('disabled', 'disabled');
        $('#edit-column-select-expense option[value="year"]').attr('disabled', 'disabled');
      }
      $('#edit-column-select-expense').multiSelect('refresh');
      if (!$('#ms-edit-column-select-expense .ms-selection').next().is("a")) {
        $('#ms-edit-column-select-expense .ms-selection').after('<a class="deselect">Remove All</a>');
        $('#ms-edit-column-select-expense .ms-selection').after('<a class="select">Add All</a>');
      }
      $('#ms-edit-column-select-expense a.select').click(function () {
        $('#edit-column-select-expense').multiSelect('select_all');
      });
      $('#ms-edit-column-select-expense a.deselect').click(function () {
        $('#edit-column-select-expense').multiSelect('deselect_all');
      });

      if ($('#edit-year').val() === '0') {
        $('#edit-column-select-oge-expense option[value="Year"]').attr('disabled', 'disabled');
        $('#edit-column-select-oge-expense option[value="year"]').attr('disabled', 'disabled');
      }
      $('#edit-column-select-oge-expense').multiSelect('refresh');
      if (!$('#ms-edit-column-select-oge-expense .ms-selection').next().is("a")) {
        $('#ms-edit-column-select-oge-expense .ms-selection').after('<a class="deselect">Remove All</a>');
        $('#ms-edit-column-select-oge-expense .ms-selection').after('<a class="select">Add All</a>');
      }
      $('#ms-edit-column-select-oge-expense a.select').click(function () {
        $('#edit-column-select-oge-expense').multiSelect('select_all');
      });
      $('#ms-edit-column-select-oge-expense a.deselect').click(function () {
        $('#edit-column-select-oge-expense').multiSelect('deselect_all');
      });

      if ($('#edit-year').val() === '0') {
        $('#edit-column-select-revenue option[value="Year"]').attr('disabled', 'disabled');
        $('#edit-column-select-revenue option[value="year"]').attr('disabled', 'disabled');
      }
      $('#edit-column-select-revenue').multiSelect('refresh');
      if (!$('#ms-edit-column-select-revenue .ms-selection').next().is("a")) {
        $('#ms-edit-column-select-revenue .ms-selection').after('<a class="deselect">Remove All</a>');
        $('#ms-edit-column-select-revenue .ms-selection').after('<a class="select">Add All</a>');
      }
      $('#ms-edit-column-select-revenue a.select').click(function () {
        $('#edit-column-select-revenue').multiSelect('select_all');
      });
      $('#ms-edit-column-select-revenue a.deselect').click(function () {
        $('#edit-column-select-revenue').multiSelect('deselect_all');
      });

      if ($('#edit-year').val() === '0') {
        $('#edit-column-select-all option[value="Year"]').attr('disabled', 'disabled');
        $('#edit-column-select-all option[value="year"]').attr('disabled', 'disabled');
      }

      $('#edit-column-select-all').multiSelect('refresh');
      if (!$('#ms-edit-column-select-all .ms-selection').next().is("a")) {
        $('#ms-edit-column-select-all .ms-selection').after('<a class="deselect">Remove All</a>');
        $('#ms-edit-column-select-all .ms-selection').after('<a class="select">Add All</a>');
      }
      $('#ms-edit-column-select-all a.select').click(function () {
        $('#edit-column-select-all').multiSelect('select_all');
      });
      $('#ms-edit-column-select-all a.deselect').click(function () {
        $('#edit-column-select-all').multiSelect('deselect_all');
      });
    } else {
      if ($('#edit-nycha-year').val() === '0') {
        $('#edit-column-select-nycha option[value="Year"]').attr('disabled', 'disabled');
        $('#edit-column-select-nycha option[value="year"]').attr('disabled', 'disabled');
      }

      $('#edit-column-select-nycha').multiSelect('refresh');
      if (!$('#ms-edit-column-select-nycha .ms-selection').next().is("a")) {
        $('#ms-edit-column-select-nycha .ms-selection').after('<a class="deselect">Remove All</a>');
        $('#ms-edit-column-select-nycha .ms-selection').after('<a class="select">Add All</a>');
      }
      $('#ms-edit-column-select-nycha a.select').click(function () {
        $('#edit-column-select-nycha').multiSelect('select_all');
      });
      $('#ms-edit-column-select-nycha a.deselect').click(function () {
        $('#edit-column-select-nycha').multiSelect('deselect_all');
      });
    }

    const csval = $('select[name="df_contract_status"]').val();
    const catval = $('#edit-category').val();
    // Display multi-select
    $.fn.hideShow(csval, catval, dataSource);
    $.fn.showHidePrimeAndSubIcon();
  };

  /**
   * Function will make changes to the form based on dataSource selection
   * @param dataSource
   */
  $.fn.reloadAgencies = function (dataSource) {
    $.ajax({
      url: '/datafeeds/spending/agency/' + dataSource + '/json'
      , success: function (data) {
        let html = '';
        if (data[0]) {
          if (data[0].label !== 'No Matches Found') {
            for (let i = 0; i < data.length; i++) {
              html = html + '<option title="' + data[i].value + '" value="' + data[i].value + '">' + data[i].label + '</option>';
            }
          }
        }
        $('#edit-agency').html(html);
        $('#edit-agency').trigger('change');
      }
    });
  };

  /**
   * Function will remove the asterisk icon css from a field
   * @param csval  -- Contract Status
   * @param catval  -- Contract Category
   * @param datasource  -- Datasource
   */
  $.fn.hideShow = function (csval, catval, datasource) {
    const $expense = $('.form-item-column-select-expense');
    const $oge_expense = $('.form-item-column-select-oge-expense');
    const $revenue = $('.form-item-column-select-revenue');
    const $pending = $('.form-item-column-select-pending');
    const $all = $('.form-item-column-select-all');
    const $pending_all = $('.form-item-column-select-pending-all');
    const $nycha = $('.form-item-column-select-nycha');
    //rewire the condistion of datasource categories

    if (datasource === 'checkbook_nycha') {
      $expense.hide();
      $revenue.hide();
      $pending.hide();
      $all.hide();
      $pending_all.hide();
      $oge_expense.hide();
      $nycha.show();
    } else {
      if (csval === 'active') {
        if (datasource === 'checkbook') {
          if (catval === 'expense') {
            $('.form-item-column-select-expense label').html('Columns (Active Expense)<span class="form-required"></span>');
            $expense.show();
            $revenue.hide();
            $pending.hide();
            $all.hide();
            $pending_all.hide();
            $oge_expense.hide();
            $nycha.hide();
          } else if (catval === 'revenue') {
            $('.form-item-column-select-revenue label').html('Columns (Active Revenue)<span class="form-required"></span>');
            $expense.hide();
            $revenue.show();
            $pending.hide();
            $all.hide();
            $pending_all.hide();
            $oge_expense.hide();
            $nycha.hide();
          } else {
            $('.form-item-column-select-all label').html('Columns (All Active)<span class="form-required"></span>');
            $all.show();
            $expense.hide();
            $revenue.hide();
            $pending.hide();
            $pending_all.hide();
            $oge_expense.hide();
            $nycha.hide();
          }
        } else if (datasource === 'checkbook_oge') {
          $('.form-item-column-select-oge-expense label').html('Columns (Active Expense)<span class="form-required"></span>');
          $expense.hide();
          $revenue.hide();
          $pending.hide();
          $all.hide();
          $pending_all.hide();
          $oge_expense.show();
          $nycha.hide();
        }
      } else if (csval === 'registered') {
        if (datasource === 'checkbook') {
          if (catval === 'expense') {
            $('.form-item-column-select-expense label').html('Columns (Registered Expense)<span class="form-required"></span>');
            $expense.show();
            $revenue.hide();
            $pending.hide();
            $all.hide();
            $pending_all.hide();
            $oge_expense.hide();
            $nycha.hide();
          } else if (catval === 'revenue') {
            $('.form-item-column-select-revenue label').html('Columns (Registered Revenue)<span class="form-required"></span>');
            $expense.hide();
            $revenue.show();
            $pending.hide();
            $all.hide();
            $pending_all.hide();
            $oge_expense.hide();
            $nycha.hide();
          } else {
            $('.form-item-column-select-all label').html('Columns (All Registered)<span class="form-required"></span>');
            $expense.hide();
            $revenue.hide();
            $pending.hide();
            $all.show();
            $pending_all.hide();
            $oge_expense.hide();
            $nycha.hide();
          }
        } else if (datasource === 'checkbook_oge') {
          if (catval === 'expense') {
            $('.form-item-column-select-oge-expense label').html('Columns (Registered Expense)<span class="form-required"></span>');
            $expense.hide();
            $revenue.hide();
            $pending.hide();
            $all.hide();
            $pending_all.hide();
            $oge_expense.show();
            $nycha.hide();
          }
        }
      } else {
        if (catval === 'expense') {
          $('.form-item-column-select-pending label').html('Columns (Pending Expense)<span class="form-required"></span>');
          $expense.hide();
          $revenue.hide();
          $pending.show();
          $all.hide();
          $pending_all.hide();
          $oge_expense.hide();
          $nycha.hide();
        } else if (catval === 'revenue') {
          $('.form-item-column-select-pending label').html('Columns (Pending Revenue)<span class="form-required"></span>');
          $expense.hide();
          $revenue.hide();
          $pending.show();
          $all.hide();
          $pending_all.hide();
          $oge_expense.hide();
          $nycha.hide();
        } else {
          $('.form-item-column-select-pending-all label').html('Columns (All Pending)<span class="form-required"></span>');
          $expense.hide();
          $revenue.hide();
          $pending.hide();
          $all.hide();
          $pending_all.show();
          $oge_expense.hide();
          $nycha.hide();
        }
      }
    }
  };

  $.fn.subVendorStatusInPipChange = function (sub_vendor_status, includes_sub_vendors) {
    const valid_status = [6, 1, 4, 3, 2, 5];
    sub_vendor_status = parseInt(sub_vendor_status);
    includes_sub_vendors = parseInt(includes_sub_vendors);
    if ($.inArray(sub_vendor_status, valid_status)) {
      if (includes_sub_vendors === 2) {
        $('#edit-contract_includes_sub_vendors_id').html('<option value="0">Select Status</option>' +
          '<option value="2" selected>Yes[2]</option>');
      } else {
        $('#edit-contract_includes_sub_vendors_id').html('<option value="0" selected>Select Status</option>' +
          '<option value="2">Yes[2]</option>');
      }
    }

    if (sub_vendor_status === 0) {
      if (includes_sub_vendors === 2) {
        $('#edit-contract_includes_sub_vendors_id').html('<option value="0">Select Status</option>' +
          '<option value="2" selected>Yes[2]</option>' +
          '<option value="3">No[3]</option>' +
          '<option value="1">No Data Entered[1]</option>' +
          '<option value="4">Not Required[4]</option>');
      } else {
        $('#edit-contract_includes_sub_vendors_id').html('<option value="0" selected>Select Status</option>' +
          '<option value="2">Yes[2]</option>' +
          '<option value="3">No[3]</option>' +
          '<option value="1">No Data Entered[1]</option>' +
          '<option value="4">Not Required[4]</option>');
      }
    }
    $('#edit-contract_includes_sub_vendors_id').val(includes_sub_vendors);
  };

  Drupal.behaviors.contractsDataFeeds = {
    attach: function (context) {

      //This is to reset the radio button to citywide if the user refreshes browser
      let datasource = $('input[name="datafeeds-contracts-domain-filter"]:checked').val();
      const $contractStatus = $('select[name="df_contract_status"]', context);
      const $category = $('#edit-category', context);
      let csval = $('select[name="df_contract_status"]', context).val();

      if(csval === "pending") {
        $('#edit-year option:selected').removeAttr('selected');
        $('#edit-year').attr('disabled','disabled');

        $("#edit-sub_contract_status_id").attr('disabled', 'disabled');
        $("#edit-sub_contract_status_id").prop("selectedIndex", 0);

        $("#edit-conditional_category").attr('disabled', 'disabled');
        $("#edit-conditional_category").prop("selectedIndex", 0);

        $("#edit-contract_includes_sub_vendors_id").attr('disabled', 'disabled');
        $("#edit-contract_includes_sub_vendors_id").prop("selectedIndex", 0);
        $.fn.subVendorStatusInPipChange(0, 0);
      }

      let catval = $('#edit-category', context).val();
      let year_value = getYearValue($('#edit-year', context).val());

      $.fn.reloadAgencies(datasource);

      //Show or hide fields based on data source selection
      $.fn.showHideFields(datasource);

      //Show Sub or Prime vendor icon
      $.fn.showHidePrimeAndSubIcon();

      // disable if covid is not selected
      if (year_value < 2020 || catval === 'revenue') {
        $("#edit-conditional_category").attr('disabled', 'disabled');
        $("#edit-conditional_category").val('0');
      }

      //Reload year based on the event value
      let cevent = $('#edit-conditional_category', context).val();
      updateYearValue(cevent);

      $(once('contracts_window_load',document)).ready(function () {
        $.fn.formatDatafeedsDatasourceRadio('edit-datafeeds-contracts-domain-filter');
        // Display multi-select. Run on document load
        $.fn.hideShow(csval, catval, datasource);
        let cevent = $('#edit-conditional_category', context).val();
        $.fn.calculateMocsRegister(cevent, csval, catval);
      });

      // Enable/disable and add/remove options in 'Contracts Include SubVendors' and 'Sub Contract' drop-downs
      $.fn.subVendorStatusInPipChange($('#edit-sub_contract_status_id', context).val(), $('#edit-contract_includes_sub_vendors_id', context).val());

      //On change of "Subcontract Status" status
      $('#edit-sub_contract_status_id', context).change(function () {
        const sub_vendor_status = $('#edit-sub_contract_status_id', context).val();
        const includes_sub_vendors = $('#edit-contract_includes_sub_vendors_id', context).val();
        $.fn.subVendorStatusInPipChange(sub_vendor_status, includes_sub_vendors);
      });

      //Data Source change event
      $('input:radio[name=datafeeds-contracts-domain-filter]', context).change(function () {
        $.fn.onDataSourceChange($(this).val());
      });

      //Contract Status Drop-down
      $contractStatus.change(function () {
        csval = $('select[name="df_contract_status"]', context).val();
        catval = $('#edit-category', context).val();
        datasource = $('input[name="datafeeds-contracts-domain-filter"]:checked', context).val();
        $.fn.resetSelectedColumns();
        $.fn.hideShow(csval, catval, datasource);
        $.fn.showHidePrimeAndSubIcon();
        if (csval === 'pending') {
          $('#edit-year option:selected').removeAttr('selected');
          $('#edit-year').attr('disabled', 'disabled');

          $('#edit-sub_contract_status_id option:selected').removeAttr('selected');
          $('#edit-sub_contract_status_id').prop("selectedIndex", 0);

          $('#edit-conditional_category option:selected').removeAttr('selected');
          $('#edit-conditional_category').prop("selectedIndex", 0).attr('disabled', 'disabled');

          $('#edit-contract_includes_sub_vendors_id option:selected').removeAttr('selected');
          $('#edit-contract_includes_sub_vendors_id').prop("selectedIndex", 0);
        } else if (catval === 'revenue') {
          $('#edit-conditional_category option:selected').removeAttr('selected');
          $('#edit-conditional_category').prop("selectedIndex", 0).attr('disabled', 'disabled');
        } else {
          $('#edit-conditional_category').removeAttr('disabled');
        }
      });

      //Contract Category Drop-down
      $category.change(function () {
        csval = $('select[name="df_contract_status"]', context).val();
        catval = $('#edit-category', context).val();
        var year_value = getYearValue($('#edit-year', context).val());
        datasource = $('input[name="datafeeds-contracts-domain-filter"]:checked', context).val();
        // disable event filed when category is all
        if (catval === 'revenue' || (year_value < 2020 && year_value !== 0)) {
          $('#edit-conditional_category option:selected').removeAttr('selected');
          $('#edit-conditional_category').prop("selectedIndex", 0).attr('disabled', 'disabled');
          let cevent = $('#edit-conditional_category', context).val();
          updateYearValue(cevent);
        } else if (csval === 'pending') {
          $('#edit-conditional_category option:selected').removeAttr('selected');
          $('#edit-conditional_category').prop("selectedIndex", 0).attr('disabled', 'disabled');
        } else {
          $('#edit-conditional_category').removeAttr('disabled');
        }
        $.fn.resetSelectedColumns();
        $.fn.hideShow(csval, catval, datasource);
        $.fn.showHidePrimeAndSubIcon();
        let cevent = $('#edit-conditional_category', context).val();
        $.fn.calculateMocsRegister(cevent, csval, catval);
      })

      // On Conditional Category change reload year drop down.
      $('#edit-conditional_category', context).change(function () {
        let cevent = $('#edit-conditional_category', context).val();
        csval = $('select[name="df_contract_status"]', context).val();
        catval = $('#edit-category', context).val();
        updateYearValue(cevent);

        $.fn.calculateMocsRegister(cevent, csval, catval);
      });

      //Set up jQuery datepickers
      const currentYear = new Date().getFullYear();
      /*
      $('.datepicker', context).datepicker({
        dateFormat: "yy-mm-dd",
        changeMonth: true,
        changeYear: true,
        yearRange: '-' + (currentYear - 1900) + ':+' + (2500 - currentYear)
      });
      */
      //Disable Year option for All Years
      if ($('#edit-year', context).val() === '0') {
        $('#edit-column-select-expense option[value="Year"]', context).attr('disabled', 'disabled');
        $('#edit-column-select-oge-expense option[value="Year"]', context).attr('disabled', 'disabled');
        $('#edit-column-select-revenue option[value="Year"]', context).attr('disabled', 'disabled');
        $('#edit-column-select-all option[value="Year"]', context).attr('disabled', 'disabled');
        $('#edit-column-select-expense option[value="year"]', context).attr('disabled', 'disabled');
        $('#edit-column-select-oge-expense option[value="year"]', context).attr('disabled', 'disabled');
        $('#edit-column-select-revenue option[value="year"]', context).attr('disabled', 'disabled');
        $('#edit-column-select-all option[value="year"]', context).attr('disabled', 'disabled');

      } else {
        $('#edit-column-select-expense option[value="Year"]', context).removeAttr('disabled');
        $('#edit-column-select-oge-expense option[value="Year"]', context).removeAttr('disabled');
        $('#edit-column-select-revenue option[value="Year"]', context).removeAttr('disabled');
        $('#edit-column-select-all option[value="Year"]', context).removeAttr('disabled');
        $('#edit-column-select-expense option[value="year"]', context).removeAttr('disabled');
        $('#edit-column-select-oge-expense option[value="year"]', context).removeAttr('disabled');
        $('#edit-column-select-revenue option[value="year"]', context).removeAttr('disabled');
        $('#edit-column-select-all option[value="year"]', context).removeAttr('disabled');
      }
      //Disable Year option for All Years - for NYCHA
      if ($('#edit-nycha-year', context).val() === '0') {
        $('#edit-column-select-nycha option[value="Year"]').attr('disabled', 'disabled');
        $('#edit-column-select-nycha option[value="year"]').attr('disabled', 'disabled');
      } else {
        $('#edit-column-select-nycha option[value="Year"]').removeAttr('disabled');
        $('#edit-column-select-nycha option[value="year"]').removeAttr('disabled');
      }

      //Set up multiselects/option transfers
      //Active/Registered Expense -- CityWide
      $('#edit-column-select-expense', context).multiSelect();
      if (!$('#ms-edit-column-select-expense .ms-selection', context).next().is("a")) {
        $('#ms-edit-column-select-expense .ms-selection', context).after('<a class="deselect">Remove All</a>');
        $('#ms-edit-column-select-expense .ms-selection', context).after('<a class="select">Add All</a>');
      }
      $('#ms-edit-column-select-expense a.select', context).click(function () {
        $('#edit-column-select-expense', context).multiSelect('select_all');
      });
      $('#ms-edit-column-select-expense a.deselect', context).click(function () {
        $('#edit-column-select-expense', context).multiSelect('deselect_all');
      });

      //OGE Active/Registered Expense
      $('#edit-column-select-oge-expense', context).multiSelect();
      if (!$('#ms-edit-column-select-oge-expense .ms-selection', context).next().is("a")) {
        $('#ms-edit-column-select-oge-expense .ms-selection', context).after('<a class="deselect">Remove All</a>');
        $('#ms-edit-column-select-oge-expense .ms-selection', context).after('<a class="select">Add All</a>');
      }
      $('#ms-edit-column-select-oge-expense a.select', context).click(function () {
        $('#edit-column-select-oge-expense', context).multiSelect('select_all');
      });
      $('#ms-edit-column-select-oge-expense a.deselect', context).click(function () {
        $('#edit-column-select-oge-expense', context).multiSelect('deselect_all');
      });
      //Active/Registered Revenue
      $('#edit-column-select-revenue', context).multiSelect();
      if (!$('#ms-edit-column-select-revenue .ms-selection', context).next().is("a")) {
        $('#ms-edit-column-select-revenue .ms-selection', context).after('<a class="deselect">Remove All</a>');
        $('#ms-edit-column-select-revenue .ms-selection', context).after('<a class="select">Add All</a>');
      }
      $('#ms-edit-column-select-revenue a.select', context).click(function () {
        $('#edit-column-select-revenue', context).multiSelect('select_all');
      });
      $('#ms-edit-column-select-revenue a.deselect', context).click(function () {
        $('#edit-column-select-revenue', context).multiSelect('deselect_all');
      });
      //Pending
      $('#edit-column-select-pending', context).multiSelect();
      if (!$('#ms-edit-column-select-pending .ms-selection', context).next().is("a")) {
        $('#ms-edit-column-select-pending .ms-selection', context).after('<a class="deselect">Remove All</a>');
        $('#ms-edit-column-select-pending .ms-selection', context).after('<a class="select">Add All</a>');
      }
      $('#ms-edit-column-select-pending a.select', context).click(function () {
        $('#edit-column-select-pending', context).multiSelect('select_all');
      });
      $('#ms-edit-column-select-pending a.deselect', context).click(function () {
        $('#edit-column-select-pending', context).multiSelect('deselect_all');
      });
      // All Pending
      $('#edit-column-select-pending-all', context).multiSelect();
      if (!$('#ms-edit-column-select-pending-all .ms-selection', context).next().is("a")) {
        $('#ms-edit-column-select-pending-all .ms-selection', context).after('<a class="deselect">Remove All</a>');
        $('#ms-edit-column-select-pending-all .ms-selection', context).after('<a class="select">Add All</a>');
      }
      $('#ms-edit-column-select-pending-all a.select', context).click(function () {
        $('#edit-column-select-pending-all', context).multiSelect('select_all');
      });
      $('#ms-edit-column-select-pending-all a.deselect', context).click(function () {
        $('#edit-column-select-pending-all', context).multiSelect('deselect_all');
      });
      // Active all (expense/revenue)
      $('#edit-column-select-all', context).multiSelect();
      if (!$('#ms-edit-column-select-all .ms-selection', context).next().is("a")) {
        $('#ms-edit-column-select-all .ms-selection', context).after('<a class="deselect">Remove All</a>');
        $('#ms-edit-column-select-all .ms-selection', context).after('<a class="select">Add All</a>');
      }
      $('#ms-edit-column-select-all a.select', context).click(function () {
        $('#edit-column-select-all', context).multiSelect('select_all');
      });
      $('#ms-edit-column-select-all a.deselect', context).click(function () {
        $('#edit-column-select-all', context).multiSelect('deselect_all');
      });

      //NYCHA
      $('#edit-column-select-nycha', context).multiSelect();
      if (!$('#ms-edit-column-select-nycha .ms-selection', context).next().is("a")) {
        $('#ms-edit-column-select-nycha .ms-selection', context).after('<a class="deselect">Remove All</a>');
        $('#ms-edit-column-select-nycha .ms-selection', context).after('<a class="select">Add All</a>');
      }
      $('#ms-edit-column-select-nycha a.select', context).click(function () {
        $('#edit-column-select-nycha', context).multiSelect('select_all');
      });
      $('#ms-edit-column-select-nycha a.deselect', context).click(function () {
        $('#edit-column-select-nycha', context).multiSelect('deselect_all');
      });

      const status = $('select[name="df_contract_status"]', context).val();
      const category = $('#edit-category', context).val();
      const conditional_category = $.fn.emptyToZero($('#edit-conditional_category', context).val());
      const contract_type = $.fn.emptyToZero($('#edit-contract-type', context).val());
      const agency = $.fn.emptyToZero($('#edit-agency', context).val());
      const award_method = $.fn.emptyToZero($('#edit-award-method', context).val());
      const year = ($('#edit-year', context).attr('disabled')) ? 0 : getYearValue($('#edit-year', context).val());
      const mwbecat = ($('#edit-mwbe-category').val()) ? $('#edit-mwbe-category').val() : 0;
      const industry = $.fn.emptyToZero($('#edit-industry', context).val());
      const includes_sub_vendors = $.fn.emptyToZero($('#edit-contract_includes_sub_vendors_id', context).val());
      const sub_vendor_status = $.fn.emptyToZero($('#edit-sub_contract_status_id', context).val());
      // Refactoring autocomplete for citywide similar to Nycha to use common autocomplete function
      let filters = {
        "contract_status": status,
        "contract_category_name": category,
        "contract_type_id": contract_type,
        "event_id": conditional_category,
        "agency_code": agency,
        "award_method_id": award_method,
        "fiscal_year": year,
        "minority_type_id": mwbecat,
        "scntrc_status": includes_sub_vendors,
        "aprv_sta": sub_vendor_status,
        "industry_type_id": industry,
      };
      if (datasource === 'checkbook_oge') {
        $('#edit-vendor').autocomplete({
          source: $.fn.autoCompleteSourceUrl(datasource, 'vendor_name', filters),
          select: function (event, ui) {
            $.fn.preventSelectionDefault(event, ui, "No Matches Found");
          }
        });
      } else {
        $('#edit-vendor').autocomplete({
          source: $.fn.autoCompleteSourceUrl(datasource, 'vendor_name_code', filters),
          select: function (event, ui) {
            $.fn.preventSelectionDefault(event, ui, "No Matches Found");
          }
        });
      }
      $('#edit-contractno').autocomplete({
        source: $.fn.autoCompleteSourceUrl(datasource, 'contract_number', filters),
        select: function (event, ui) {
          $.fn.preventSelectionDefault(event, ui, "No Matches Found");
        }
      });
      $('#edit-apt-pin').autocomplete({
        source: $.fn.autoCompleteSourceUrl(datasource, 'apt_pin', filters),
        select: function (event, ui) {
          $.fn.preventSelectionDefault(event, ui, "No Matches Found");
        }
      });
      $('#edit-pin').autocomplete({
        source: $.fn.autoCompleteSourceUrl(datasource, 'pin', filters),
        select: function (event, ui) {
          $.fn.preventSelectionDefault(event, ui, "No Matches Found");
        }
      });
      $('#edit-entity-contract-number').autocomplete({
        source: $.fn.autoCompleteSourceUrl(datasource, 'contract_entity_contract_number', filters),
        select: function (event, ui) {
          $.fn.preventSelectionDefault(event, ui, "No Matches Found");
        }
      });
      $('#edit-commodity-line').autocomplete({
        source: $.fn.autoCompleteSourceUrl(datasource, 'contract_commodity_line', filters),
        select: function (event, ui) {
          $.fn.preventSelectionDefault(event, ui, "No Matches Found");
        }
      });
      $('#edit-budget-name').autocomplete({
        source: $.fn.autoCompleteSourceUrl(datasource, 'contract_budget_name', filters),
        select: function (event, ui) {
          $.fn.preventSelectionDefault(event, ui, "No Matches Found");
        }
      });

      const purchase_order = $.fn.emptyToZero($('select[name="purchase_order_type"]', context).val());
      const responsibility_center = $.fn.emptyToZero($('select[name="resp_center"]', context).val());
      const nycha_contract_type = $.fn.emptyToZero($('select[name="nycha_contract_type"]', context).val());
      const nycha_award_method = $.fn.emptyToZero($('select[name="nycha_awd_method"]', context).val());
      const nycha_industry = $.fn.emptyToZero($('select[name="nycha_industry"]', context).val());
      const nycha_year = getYearValue($('#edit-nycha-year').val());
      let nycha_filters = {
        agreement_start_year: nycha_year,
        contract_type_code: nycha_contract_type,
        award_method_code: nycha_award_method,
        industry_type_code: nycha_industry,
        agency_code: agency,
        agreement_type_code: purchase_order,
        responsibility_center_code: responsibility_center,
      };

      $('#edit-nycha-vendor').autocomplete({
        source: $.fn.autoCompleteSourceUrl(datasource, 'vendor_name_code', nycha_filters),
        select: function (event, ui) {
          $.fn.preventSelectionDefault(event, ui, "No Matches Found");
        }
      });
      $('#edit-nycha-contract-id').autocomplete({
        source: $.fn.autoCompleteSourceUrl(datasource, 'contract_number', nycha_filters),
        select: function (event, ui) {
          $.fn.preventSelectionDefault(event, ui, "No Matches Found");
        }
      });
      $('#edit-nycha-apt-pin').autocomplete({
        source: $.fn.autoCompleteSourceUrl(datasource, 'pin', nycha_filters),
        select: function (event, ui) {
          $.fn.preventSelectionDefault(event, ui, "No Matches Found");
        }
      });

      $('.watch:input', context).each(function () {
        $(this).focusin(function () {
          const status = $('select[name="df_contract_status"]', context).val();
          const category = $('#edit-category', context).val();
          const contract_type = $.fn.emptyToZero($('#edit-contract-type', context).val());
          const conditional_category = $.fn.emptyToZero($('#edit-conditional_category', context).val());
          const agency = $.fn.emptyToZero($('#edit-agency', context).val());
          const award_method = $.fn.emptyToZero($('#edit-award-method', context).val());
          const year = ($('#edit-year', context).attr('disabled')) ? 0 : getYearValue($('#edit-year', context).val());
          let mwbecat = ($('#edit-mwbe-category').val()) ? $('#edit-mwbe-category').val() : 0;
          mwbecat = mwbecat == null ? 0 : mwbecat;
          const industry = $.fn.emptyToZero($('#edit-industry', context).val());
          const datasource = $('input:radio[name=datafeeds-contracts-domain-filter]:checked').val();
          const includes_sub_vendors = $.fn.emptyToZero($('#edit-contract_includes_sub_vendors_id', context).val());
          const sub_vendor_status = $.fn.emptyToZero($('#edit-sub_contract_status_id', context).val());

          let filters = {
            "contract_status": status,
            "contract_category_name": category,
            "contract_type_id": contract_type,
            "event_id": conditional_category,
            "agency_code": agency,
            "award_method_id": award_method,
            "fiscal_year": year,
            "minority_type_id": mwbecat,
            "scntrc_status": includes_sub_vendors,
            "aprv_sta": sub_vendor_status,
            "industry_type_id": industry,
          };
          if (datasource === 'checkbook_oge') {
            $('#edit-vendor').autocomplete({
              source: $.fn.autoCompleteSourceUrl(datasource, 'vendor_name', filters),
              select: function (event, ui) {
                $.fn.preventSelectionDefault(event, ui, "No Matches Found");
              }
            });
          } else {
            $('#edit-vendor').autocomplete({
              source: $.fn.autoCompleteSourceUrl(datasource, 'vendor_name_code', filters),
              select: function (event, ui) {
                $.fn.preventSelectionDefault(event, ui, "No Matches Found");
              }
            });
          }
          $('#edit-contractno').autocomplete({
            source: $.fn.autoCompleteSourceUrl(datasource, 'contract_number', filters),
            select: function (event, ui) {
              $.fn.preventSelectionDefault(event, ui, "No Matches Found");
            }
          });
          $('#edit-apt-pin').autocomplete({
            source: $.fn.autoCompleteSourceUrl(datasource, 'apt_pin', filters),
            select: function (event, ui) {
              $.fn.preventSelectionDefault(event, ui, "No Matches Found");
            }
          });
          $('#edit-pin').autocomplete({
            source: $.fn.autoCompleteSourceUrl(datasource, 'pin', filters),
            select: function (event, ui) {
              $.fn.preventSelectionDefault(event, ui, "No Matches Found");
            }
          });
          $('#edit-entity-contract-number').autocomplete({
            source: $.fn.autoCompleteSourceUrl(datasource, 'contract_entity_contract_number', filters),
            select: function (event, ui) {
              $.fn.preventSelectionDefault(event, ui, "No Matches Found");
            }
          });
          $('#edit-commodity-line').autocomplete({
            source: $.fn.autoCompleteSourceUrl(datasource, 'contract_commodity_line', filters),
            select: function (event, ui) {
              $.fn.preventSelectionDefault(event, ui, "No Matches Found");
            }
          });
          $('#edit-budget-name').autocomplete({
            source: $.fn.autoCompleteSourceUrl(datasource, 'contract_budget_name', filters),
            select: function (event, ui) {
              $.fn.preventSelectionDefault(event, ui, "No Matches Found");
            }
          });

          const purchase_order = $.fn.emptyToZero($('select[name="purchase_order_type"]', context).val());
          const responsibility_center = $.fn.emptyToZero($('select[name="resp_center"]', context).val());
          const nycha_contract_type = $.fn.emptyToZero($('select[name="nycha_contract_type"]', context).val());
          const nycha_award_method = $.fn.emptyToZero($('select[name="nycha_awd_method"]', context).val());
          const nycha_industry = $.fn.emptyToZero($('select[name="nycha_industry"]', context).val());
          const nycha_year = getYearValue($('#edit-nycha-year').val());
          let nycha_filters = {
            agreement_start_year: nycha_year,
            contract_type_code: nycha_contract_type,
            award_method_code: nycha_award_method,
            industry_type_code: nycha_industry,
            agency_code: agency,
            agreement_type_code: purchase_order,
            responsibility_center_code: responsibility_center,
          };

          $('#edit-nycha-vendor').autocomplete({
            source: $.fn.autoCompleteSourceUrl(datasource, 'vendor_name_code', nycha_filters),
            select: function (event, ui) {
              $.fn.preventSelectionDefault(event, ui, "No Matches Found");
            }
          });
          $('#edit-nycha-contract-id').autocomplete({
            source: $.fn.autoCompleteSourceUrl(datasource, 'contract_number', nycha_filters),
            select: function (event, ui) {
              $.fn.preventSelectionDefault(event, ui, "No Matches Found");
            }
          });
          $('#edit-nycha-apt-pin').autocomplete({
            source: $.fn.autoCompleteSourceUrl(datasource, 'pin', nycha_filters),
            select: function (event, ui) {
              $.fn.preventSelectionDefault(event, ui, "No Matches Found");
            }
          });
        });
      });
      //Year Drop-down
      $('#edit-nycha-year', context).change(function () {
        if ($(this).val() === '0') {
          $('#edit-column-select-nycha').multiSelect('deselect',"Year");
          $('#edit-column-select-nycha option[value="Year"]', context).attr('disabled', 'disabled');
          $('#edit-column-select-nycha option[value="year"]', context).attr('disabled', 'disabled');
        } else {
          $('#edit-column-select-nycha option[value="Year"]', context).removeAttr('disabled');
          $('#edit-column-select-nycha option[value="year"]', context).removeAttr('disabled');
        }
        $('#edit-column-select-nycha', context).multiSelect('refresh');
        if (!$('#ms-edit-column-select-nycha .ms-selection', context).next().is("a")) {
          $('#ms-edit-column-select-nycha .ms-selection', context).after('<a class="deselect">Remove All</a>');
          $('#ms-edit-column-select-nycha .ms-selection', context).after('<a class="select">Add All</a>');
        }
        $('#ms-edit-column-select-nycha a.select', context).click(function () {
          $('#edit-column-select-nycha', context).multiSelect('select_all');
        });
        $('#ms-edit-column-select-nycha a.deselect', context).click(function () {
          $('#edit-column-select-nycha', context).multiSelect('deselect_all');
        });
      });
      //Year Drop-down
      $('#edit-year', context).change(function () {
        let catval = $('#edit-category', context).val();
        if ($(this).val() === '0') {
          $('#edit-column-select-expense').multiSelect('deselect',"Year");
          $('#edit-column-select-expense option[value="Year"]', context).attr('disabled', 'disabled');
          $('#edit-column-select-expense option[value="year"]', context).attr('disabled', 'disabled');

          $('#edit-column-select-oge-expense').multiSelect('deselect',"Year");
          $('#edit-column-select-oge-expense option[value="Year"]', context).attr('disabled', 'disabled');
          $('#edit-column-select-oge-expense option[value="year"]', context).attr('disabled', 'disabled');

          $('#edit-column-select-revenue').multiSelect('deselect',"Year");
          $('#edit-column-select-revenue option[value="Year"]', context).attr('disabled', 'disabled');
          $('#edit-column-select-revenue option[value="year"]', context).attr('disabled', 'disabled');

          $('#edit-column-select-all').multiSelect('deselect',"Year");
          $('#edit-column-select-all option[value="Year"]', context).attr('disabled', 'disabled');
          $('#edit-column-select-all option[value="year"]', context).attr('disabled', 'disabled');

          if (catval === 'revenue') {
            $("#edit-conditional_category").attr('disabled', 'disabled');
            $('select[name="conditional_category"]', context).val('0');
          } else {
            $('select[name="conditional_category"] option[value*="[2]"]').removeAttr('disabled', 'disabled');
            $("#edit-conditional_category").removeAttr('disabled');
          }
        } else {
          let year_value = getYearValue($('#edit-year', context).val());
          $('#edit-column-select-expense option[value="Year"]', context).removeAttr('disabled');
          $('#edit-column-select-oge-expense option[value="Year"]', context).removeAttr('disabled');
          $('#edit-column-select-revenue option[value="Year"]', context).removeAttr('disabled');
          $('#edit-column-select-all option[value="Year"]', context).removeAttr('disabled');
          $('#edit-column-select-expense option[value="year"]', context).removeAttr('disabled');
          $('#edit-column-select-oge-expense option[value="year"]', context).removeAttr('disabled');
          $('#edit-column-select-revenue option[value="year"]', context).removeAttr('disabled');
          $('#edit-column-select-all option[value="year"]', context).removeAttr('disabled');
          //Disable Conditional Categories for the options FY < 2018 and Payroll&Others Spending Categories
          if (catval === 'revenue' || year_value < 2018) {
            $("#edit-conditional_category").attr('disabled', 'disabled');
            $('select[name="conditional_category"]', context).val('0');
          } else {
            $("#edit-conditional_category").removeAttr('disabled');
            //Disable COVID option for FY < 2020
            if(year_value < 2020) {
              $('select[name="conditional_category"] option[value*="[1]"]').attr('disabled', 'disabled');
            }else {
              $('select[name="conditional_category"] option[value*="[1]"]').removeAttr('disabled', 'disabled');
            }
          }
        }
        $('#edit-column-select-expense', context).multiSelect('refresh');
        if (!$('#ms-edit-column-select-expense .ms-selection', context).next().is("a")) {
          $('#ms-edit-column-select-expense .ms-selection', context).after('<a class="deselect">Remove All</a>');
          $('#ms-edit-column-select-expense .ms-selection', context).after('<a class="select">Add All</a>');
        }
        $('#ms-edit-column-select-expense a.select', context).click(function () {
          $('#edit-column-select-expense', context).multiSelect('select_all');
        });
        $('#ms-edit-column-select-expense a.deselect', context).click(function () {
          $('#edit-column-select-expense', context).multiSelect('deselect_all');
        });

        $('#edit-column-select-oge-expense', context).multiSelect('refresh');
        if (!$('#ms-edit-column-select-oge-expense .ms-selection', context).next().is("a")) {
          $('#ms-edit-column-select-oge-expense .ms-selection', context).after('<a class="deselect">Remove All</a>');
          $('#ms-edit-column-select-oge-expense .ms-selection', context).after('<a class="select">Add All</a>');
        }
        $('#ms-edit-column-select-oge-expense a.select', context).click(function () {
          $('#edit-column-select-oge-expense', context).multiSelect('select_all');
        });
        $('#ms-edit-column-select-oge-expense a.deselect', context).click(function () {
          $('#edit-column-select-oge-expense', context).multiSelect('deselect_all');
        });

        $('#edit-column-select-revenue', context).multiSelect('refresh');
        if (!$('#ms-edit-column-select-revenue .ms-selection', context).next().is("a")) {
          $('#ms-edit-column-select-revenue .ms-selection', context).after('<a class="deselect">Remove All</a>');
          $('#ms-edit-column-select-revenue .ms-selection', context).after('<a class="select">Add All</a>');
        }
        $('#ms-edit-column-select-revenue a.select', context).click(function () {
          $('#edit-column-select-revenue', context).multiSelect('select_all');
        });
        $('#ms-edit-column-select-revenue a.deselect', context).click(function () {
          $('#edit-column-select-revenue', context).multiSelect('deselect_all');
        });

        $('#edit-column-select-all', context).multiSelect('refresh');
        if (!$('#ms-edit-column-select-all .ms-selection', context).next().is("a")) {
          $('#ms-edit-column-select-all .ms-selection', context).after('<a class="deselect">Remove All</a>');
          $('#ms-edit-column-select-all .ms-selection', context).after('<a class="select">Add All</a>');
        }
        $('#ms-edit-column-select-all a.select', context).click(function () {
          $('#edit-column-select-all', context).multiSelect('select_all');
        });
        $('#ms-edit-column-select-all a.deselect', context).click(function () {
          $('#edit-column-select-all', context).multiSelect('deselect_all');
        });
      });
    }
  };

  //Reset the selected columns
  $.fn.resetSelectedColumns = function () {
    $('#edit-column-select-expense').multiSelect('deselect_all');
    $('#edit-column-select-oge-expense').multiSelect('deselect_all');
    $('#edit-column-select-revenue').multiSelect('deselect_all');
    $('#edit-column-select-pending').multiSelect('deselect_all');
    $('#edit-column-select-pending-all').multiSelect('deselect_all');
    $('#edit-column-select-all').multiSelect('deselect_all');
    $('#edit-column-select-nycha').multiSelect('deselect_all');
  };

  $.fn.calculateMocsRegister = function(cevent, csval, catval) {
    cevent = $.fn.emptyToZero(cevent);
    if(cevent === 1){
      if (csval === 'active' || csval === 'registered') {
        if (catval === 'expense') {
          $.fn.addColumnSelectMocsRegistered('edit-column-select-expense');
        }
        else if (catval === 'all'){
          $.fn.addColumnSelectMocsRegistered('edit-column-select-all');
        } else {
          $.fn.removeColumnSelectMocsRegistered();
        }
      } else {
        $.fn.removeColumnSelectMocsRegistered();
      }
    } else {
      $.fn.removeColumnSelectMocsRegistered();
    }

    $('#edit-column-select-all').multiSelect('refresh');
    if (!$('#ms-edit-column-select-all .ms-selection').next().is("a")) {
      $('#ms-edit-column-select-all .ms-selection').after('<a class="deselect">Remove All</a>');
      $('#ms-edit-column-select-all .ms-selection').after('<a class="select">Add All</a>');
    }
    $('#ms-edit-column-select-all a.select').click(function () {
      $('#edit-column-select-all').multiSelect('select_all');
    });
    $('#ms-edit-column-select-all a.deselect').click(function () {
      $('#edit-column-select-all').multiSelect('deselect_all');
    });

    $('#edit-column-select-expense').multiSelect('refresh');
    if (!$('#ms-edit-column-select-expense .ms-selection').next().is("a")) {
      $('#ms-edit-column-select-expense .ms-selection').after('<a class="deselect">Remove All</a>');
      $('#ms-edit-column-select-expense .ms-selection').after('<a class="select">Add All</a>');
    }
    $('#ms-edit-column-select-expense a.select').click(function () {
      $('#edit-column-select-expense').multiSelect('select_all');
    });
    $('#ms-edit-column-select-expense a.deselect').click(function () {
      $('#edit-column-select-expense').multiSelect('deselect_all');
    });
  }

  $.fn.addColumnSelectMocsRegistered = function(columnSelectName) {
    let data_format = $('input:hidden[name="hidden_data_format"]').val();
    if (data_format === 'xml') {
      if ($('#' + columnSelectName + ' option[value="mocs_registered"]').length === 0) {
        $('#' + columnSelectName + ' option[value="document_code"]').before('<option value="mocs_registered">mocs_registered</option>');
      }
    } else {
      if ($('#' + columnSelectName + ' option[value="MOCS Registered"]').length === 0) {
        $('#' + columnSelectName + ' option[value="Document Code"]').before('<option value="MOCS Registered">MOCS Registered</option>');
      }
    }
  }

  $.fn.removeColumnSelectMocsRegistered = function () {
    //remove expense columns mocs for csv and xml
    $('#edit-column-select-expense').multiSelect('deselect',"MOCS Registered");
    $('#edit-column-select-expense option[value="MOCS Registered"]').remove();
    $('#edit-column-select-expense').multiSelect('deselect',"mocs_registered");
    $('#edit-column-select-expense option[value="mocs_registered"]').remove();
    //remove select all columns mocs for csv and xml
    $('#edit-column-select-all').multiSelect('deselect',"MOCS Registered");
    $('#edit-column-select-all option[value="MOCS Registered"]').remove();
    $('#edit-column-select-all').multiSelect('deselect',"mocs_registered");
    $('#edit-column-select-all option[value="mocs_registered"]').remove();
  }

  //Function to retrieve values enclosed in brackets or return zero if none
  $.fn.emptyToZero = function (input) {
    const p = /\[(.*?)]$/;
    let inputval, output;
    inputval = p.exec(input);
    if (inputval) {
      output = inputval[1];
    } else {
      output = 0;
    }
    return output;
  };

  //Function to retrieve numeric year value
  function getYearValue(input) {
    let yearValue;
    if(input) {
      if (input.includes('FY')) {
        yearValue = input.split('FY');
        return yearValue[1];
      }
    }
    return 'all';
  }

  // update year drop down when event is chosen
  function updateYearValue(cevent) {
    cevent = $.fn.emptyToZero(cevent);
    $("#edit-year option").each(function () {
      let year = (this.text).split(' ')[1];
      let include = true;
      if(cevent === "1") {
        include = (this.text === "All Years" || year >= 2020);
      } else if(cevent === "2") {
        include = (this.text === "All Years" || year >= 2018);
      }
      this.style.display = include ? '':'none';
    });
  }

  //Function to clear text fields and drop-downs
  $.fn.clearInputFields = function (dataSource) {
    $('.fieldset-wrapper').find(':input').each(function () {
      switch (this.type) {
        case 'select-one':
          $(this).val( $(this).find('option:first').val());
          break;
        case 'text':
          $(this).val('');
          break;
        case 'date':
          $(this).val('');
          $('.date-item-label', $(this).parent()).html('');
          break;
      }
    });

    //Enable "Contract includes sub vendors" and "Subcontract Status" drop-downs
    if (dataSource !== 'checkbook_oge') {
      $('#edit-contract_includes_sub_vendors_id').removeAttr('disabled');
      $('#edit-sub_contract_status_id').removeAttr('disabled');
    }

    //For OGE set 'Expense' as default category
    if (dataSource === 'checkbook_oge') {
      $('select[name="category"]').val('expense');
    }
  }

}(jQuery));
