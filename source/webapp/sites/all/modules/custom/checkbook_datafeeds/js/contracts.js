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
        $('.default-fields .datafield.sub_vendor_status_in_pip_id').hide();
        $('.default-fields .datafield.industry').hide();

        $("#edit-df-contract-status").children("option[value='pending']").hide();
        $("#edit-category").children("option[value='all']").hide();
        $("#edit-category").children("option[value='revenue']").hide();
        $("#edit-apt-pin").attr('disabled', 'disabled');

        //Moving fields to match with Advanced search form order
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
      default:
        $('.default-fields').show();
        $('.nycha-fields').hide();

        $('.default-fields .datafield.commodityline').hide();
        $('.default-fields .datafield.entity_contract_number').hide();
        $('.default-fields .datafield.budgetname').hide();

        $('.default-fields .datafield.mwbecategory').show();
        $('.default-fields .datafield.scntrc_status').show();
        $('.default-fields .datafield.regdate').show();
        $('.default-fields .datafield.sub_vendor_status_in_pip_id').show();
        $('.default-fields .datafield.industry').show();

        $("#edit-df-contract-status").children("option[value='pending']").show();
        $("#edit-category").children("option[value='all']").show();
        $("#edit-category").children("option[value='revenue']").show();
        $("#edit-apt-pin").removeAttr('disabled');

        //Moving fields to match with Advanced search form order
        $('.default-fields .datafield.pin').insertBefore('.default-fields .datafield.currentamt.datarange');
        $('.default-fields .datafield.enddate.datarange').insertAfter('.default-fields .datafield.currentamt.datarange');
        $('.default-fields .datafield.receiveddate.datarange').insertAfter('.default-fields .datafield.startdate.datarange');
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
    const sub_vendor_status_in_pip = jQuery(".sub_vendor_status_in_pip_id");
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
    $.fn.removePrimeAndSubIcon(sub_vendor_status_in_pip);
    $.fn.removePrimeAndSubIcon(purpose);
    $.fn.removePrimeAndSubIcon(industry);
    $.fn.removePrimeAndSubIcon(year);

    const contract_status_val = jQuery("select[name=df_contract_status]").val();
    const category_val = jQuery("select[name=category]").val();
    if ($("input[name='datafeeds-contracts-domain-filter']:checked").val() === 'checkbook') {
      // Add asterisk fields & note
      if ((contract_status_val === 'active' || contract_status_val === 'registered')
        && (category_val === 'expense' || category_val === 'all')) {

        jQuery("<div class='prime-and-sub-note-datafeeds'><p>All Fields are searchable by Prime data, unless designated as Prime & Sub (<img src='/sites/all/themes/checkbook3/images/prime-and-sub.png' />).</p><br/></div>").insertAfter(jQuery("p.required-message"));

        $.fn.addPrimeAndSubIcon(contract_status);
        $.fn.addPrimeAndSubIcon(vendor);
        $.fn.addPrimeAndSubIcon(mwbe_category);
        $.fn.addPrimeAndSubIcon(current_amt_from);
        $.fn.addPrimeAndSubIcon(category);
        $.fn.addPrimeAndSubIcon(sub_vendor_status_in_pip);
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
    const primeAndSubIcon = "<img class='prime-and-sub-datafeeds' src='/sites/all/themes/checkbook3/images/prime-and-sub.png' />";
    jQuery(ele).find('label').first().prepend(primeAndSubIcon);
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

    if (dataSource !== 'checkbook_nycha') {
      //Change the Agency drop-down label
      const vendor_label = (dataSource === 'checkbook_oge') ? 'Prime Vendor:' : 'Vendor:';
      $("label[for = edit-vendor]").text(vendor_label);

      //Clear text fields and drop-downs
      $.fn.clearInputFields(dataSource);
      //Reset 'sub-vendor status in PIP' and 'contracts include sub-vendors' drop-downs
      $.fn.subVendorStatusInPipChange(0, 0);

      //reset the selected columns
      $.fn.resetSelectedColumns();

      $('#edit-column-select-expense option[value="Year"]').attr('disabled', 'disabled');
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

      $('#edit-column-select-oge-expense option[value="Year"]').attr('disabled', 'disabled');
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

      $('#edit-column-select-revenue option[value="Year"]').attr('disabled', 'disabled');
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

      $('#edit-column-select-all option[value="Year"]').attr('disabled', 'disabled');
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
      $('#edit-column-select-nycha option[value="Year"]').attr('disabled', 'disabled');
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
            $('.form-item-column-select-expense label').html('Columns (Active Expense)<span class="form-required">*</span>');
            $expense.show();
            $revenue.hide();
            $pending.hide();
            $all.hide();
            $pending_all.hide();
            $oge_expense.hide();
            $nycha.hide();
          } else if (catval === 'revenue') {
            $('.form-item-column-select-revenue label').html('Columns (Active Revenue)<span class="form-required">*</span>');
            $expense.hide();
            $revenue.show();
            $pending.hide();
            $all.hide();
            $pending_all.hide();
            $oge_expense.hide();
            $nycha.hide();
          } else {
            $('.form-item-column-select-all label').html('Columns (All Active)<span class="form-required">*</span>');
            $all.show();
            $expense.hide();
            $revenue.hide();
            $pending.hide();
            $pending_all.hide();
            $oge_expense.hide();
            $nycha.hide();
          }
        } else if (datasource === 'checkbook_oge') {
          $('.form-item-column-select-oge-expense label').html('Columns (Active Expense)<span class="form-required">*</span>');
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
            $('.form-item-column-select-expense label').html('Columns (Registered Expense)<span class="form-required">*</span>');
            $expense.show();
            $revenue.hide();
            $pending.hide();
            $all.hide();
            $pending_all.hide();
            $oge_expense.hide();
            $nycha.hide();
          } else if (catval === 'revenue') {
            $('.form-item-column-select-revenue label').html('Columns (Registered Revenue)<span class="form-required">*</span>');
            $expense.hide();
            $revenue.show();
            $pending.hide();
            $all.hide();
            $pending_all.hide();
            $oge_expense.hide();
            $nycha.hide();
          } else {
            $('.form-item-column-select-all label').html('Columns (All Registered)<span class="form-required">*</span>');
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
            $('.form-item-column-select-oge-expense label').html('Columns (Registered Expense)<span class="form-required">*</span>');
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
          $('.form-item-column-select-pending label').html('Columns (Pending Expense)<span class="form-required">*</span>');
          $expense.hide();
          $revenue.hide();
          $pending.show();
          $all.hide();
          $pending_all.hide();
          $oge_expense.hide();
          $nycha.hide();
        } else if (catval === 'revenue') {
          $('.form-item-column-select-pending label').html('Columns (Pending Revenue)<span class="form-required">*</span>');
          $expense.hide();
          $revenue.hide();
          $pending.show();
          $all.hide();
          $pending_all.hide();
          $oge_expense.hide();
          $nycha.hide();
        } else {
          $('.form-item-column-select-pending-all label').html('Columns (All Pending)<span class="form-required">*</span>');
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
    const valid_status = [6,1,4,3,2,5];
    sub_vendor_status = parseInt(sub_vendor_status);
    includes_sub_vendors = parseInt(includes_sub_vendors);
    if($.inArray(sub_vendor_status, valid_status)) {
      if(includes_sub_vendors === 2){
        $('#edit-contract_includes_sub_vendors_id').html('<option value="0">Select Status</option>' +
          '<option value="2" selected>Yes[2]</option>');
      } else {
        $('#edit-contract_includes_sub_vendors_id').html('<option value="0" selected>Select Status</option>' +
          '<option value="2">Yes[2]</option>');
      }
    }

    if(sub_vendor_status === 0) {
      if(includes_sub_vendors === 2){
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

      $.fn.formatDatafeedsDatasourceRadio();

      //This is to reset the radio button to citywide if the user refreshes browser
      let datasource = $('input[name="datafeeds-contracts-domain-filter"]:checked', context).val();
      const $contractStatus = $('select[name="df_contract_status"]', context);
      const $category = $('#edit-category', context);
      let csval = $('select[name="df_contract_status"]', context).val();
      let catval = $('#edit-category', context).val();

      $.fn.reloadAgencies(datasource);

      //Show or hide fields based on data source selection
      $.fn.showHideFields(datasource);

      //Show Sub or Prime vendor icon
      $.fn.showHidePrimeAndSubIcon();

      // Display multi-select
      $.fn.hideShow(csval, catval, datasource);
      // Enable/disable and add/remove options in 'Contracts Include SubVendors' and 'SubVendor Status in PIP' drop-downs
      $.fn.subVendorStatusInPipChange($('#edit-sub_vendor_status_in_pip_id', context).val(), $('#edit-contract_includes_sub_vendors_id', context).val());

      //On change of "Sub Vendor Status in PIP" status
      $('#edit-sub_vendor_status_in_pip_id', context).change(function () {
        const sub_vendor_status = $('#edit-sub_vendor_status_in_pip_id', context).val();
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
        datasource = $('input[name="datafeeds-contracts-domain-filter"]:checked',context).val();
        $.fn.resetSelectedColumns();
        $.fn.hideShow(csval, catval, datasource);
        $.fn.showHidePrimeAndSubIcon();
        if (csval == 'pending') {
          $('#edit-year option:selected').removeAttr('selected');
        }
      });

      //Contract Category Drop-down
      $category.change(function () {
        csval = $('select[name="df_contract_status"]', context).val();
        catval = $('#edit-category', context).val();
        datasource = $('input[name="datafeeds-contracts-domain-filter"]:checked',context).val();
        $.fn.resetSelectedColumns();
        $.fn.hideShow(csval, catval, datasource);
        $.fn.showHidePrimeAndSubIcon();
      });

      //Set up jQuery datepickers
      const currentYear = new Date().getFullYear();
      $('.datepicker', context).datepicker({
        dateFormat: "yy-mm-dd",
        changeMonth: true,
        changeYear: true,
        yearRange: '-' + (currentYear - 1900) + ':+' + (2500 - currentYear)
      });

      //Disable Year option for All Years
      if ($('#edit-year', context).val() === '0') {
        $('#edit-column-select-expense option[value="Year"]', context).attr('disabled', 'disabled');
        $('#edit-column-select-oge-expense option[value="Year"]', context).attr('disabled', 'disabled');
        $('#edit-column-select-revenue option[value="Year"]', context).attr('disabled', 'disabled');
        $('#edit-column-select-all option[value="Year"]', context).attr('disabled', 'disabled');
      } else {
        $('#edit-column-select-expense option[value="Year"]', context).attr('disabled', '');
        $('#edit-column-select-oge-expense option[value="Year"]', context).attr('disabled', '');
        $('#edit-column-select-revenue option[value="Year"]', context).attr('disabled', '');
        $('#edit-column-select-all option[value="Year"]', context).attr('disabled', '');
      }
      //Disable Year option for All Years - for NYCHA
      if ($('#edit-nycha-year', context).val() === '0') {
        $('#edit-column-select-nycha option[value="Year"]').attr('disabled', 'disabled');
      } else {
        $('#edit-column-select-nycha option[value="Year"]').attr('disabled', '');
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
      const contract_type = $.fn.emptyToZero($('#edit-contract-type', context).val());
      const agency = $.fn.emptyToZero($('#edit-agency', context).val());
      const award_method = $.fn.emptyToZero($('#edit-award-method', context).val());
      const year = ($('#edit-year', context).attr('disabled')) ? 0 : $('#edit-year', context).val();
      const mwbecat = ($('#edit-mwbe-category').val()) ? $('#edit-mwbe-category').val() : 0;
      const industry = $.fn.emptyToZero($('#edit-industry', context).val());
      const includes_sub_vendors = $.fn.emptyToZero($('#edit-contract_includes_sub_vendors_id', context).val());
      const sub_vendor_status = $.fn.emptyToZero($('#edit-sub_vendor_status_in_pip_id', context).val());

      $('#edit-vendor', context).autocomplete({source: '/autocomplete/contracts/vendor/' + status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + mwbecat + '/' + industry + '/' + includes_sub_vendors + '/' + sub_vendor_status + '/' + datasource});
      $('#edit-contractno', context).autocomplete({source: '/autocomplete/contracts/contract_number/' + status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + mwbecat + '/' + industry + '/' + includes_sub_vendors + '/' + sub_vendor_status + '/' + datasource});
      $('#edit-apt-pin', context).autocomplete({source: '/autocomplete/contracts/apt_pin/' + status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + mwbecat + '/' + industry + '/' + includes_sub_vendors + '/' + sub_vendor_status + '/' + datasource});
      $('#edit-pin', context).autocomplete({source: '/autocomplete/contracts/pin/' + status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + mwbecat + '/' + industry + '/' + includes_sub_vendors + '/' + sub_vendor_status + '/' + datasource});
      $('#edit-entity-contract-number', context).autocomplete({source: '/autocomplete/contracts/entitycontractnum/' + status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + mwbecat + '/' + industry + '/' + includes_sub_vendors + '/' + sub_vendor_status + '/' + datasource});
      $('#edit-commodity-line', context).autocomplete({source: '/autocomplete/contracts/commodityline/' + status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + mwbecat + '/' + industry + '/' + includes_sub_vendors + '/' + sub_vendor_status + '/' + datasource});
      $('#edit-budget-name', context).autocomplete({source: '/autocomplete/contracts/budgetname/' + status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + mwbecat + '/' + industry + '/' + includes_sub_vendors + '/' + sub_vendor_status + '/' + datasource});

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

      $('#edit-nycha-vendor').autocomplete({source: $.fn.autoCompleteSourceUrl(datasource,'vendor_name_code',nycha_filters)});
      $('#edit-nycha-contract-id').autocomplete({source: $.fn.autoCompleteSourceUrl(datasource,'contract_number',nycha_filters)});
      $('#edit-nycha-apt-pin').autocomplete({source: $.fn.autoCompleteSourceUrl(datasource,'pin',nycha_filters)});

      $('.watch:input', context).each(function () {
        $(this).focusin(function () {
          const status = $('select[name="df_contract_status"]', context).val();
          const category = $('#edit-category', context).val();
          const contract_type = $.fn.emptyToZero($('#edit-contract-type', context).val());
          const agency = $.fn.emptyToZero($('#edit-agency', context).val());
          const award_method = $.fn.emptyToZero($('#edit-award-method', context).val());
          const year = ($('#edit-year', context).attr('disabled')) ? 0 : $('#edit-year', context).val();
          let mwbecat = ($('#edit-mwbe-category').val()) ? $('#edit-mwbe-category').val() : 0;
          mwbecat = mwbecat == null ? 0 : mwbecat;
          const industry = $.fn.emptyToZero($('#edit-industry', context).val());
          const datasource = $('input:radio[name=datafeeds-contracts-domain-filter]:checked').val();
          const includes_sub_vendors = $.fn.emptyToZero($('#edit-contract_includes_sub_vendors_id', context).val());
          const sub_vendor_status = $.fn.emptyToZero($('#edit-sub_vendor_status_in_pip_id', context).val());

          $('#edit-vendor', context).autocomplete('option', 'source', '/autocomplete/contracts/vendor/' + status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + mwbecat + '/' + industry + '/' + includes_sub_vendors + '/' + sub_vendor_status + '/' + datasource);
          $('#edit-contractno', context).autocomplete('option', 'source', '/autocomplete/contracts/contract_number/' + status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + mwbecat + '/' + industry + '/' + includes_sub_vendors + '/' + sub_vendor_status + '/' + datasource);
          $('#edit-apt-pin', context).autocomplete('option', 'source', '/autocomplete/contracts/apt_pin/' + status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + mwbecat + '/' + industry + '/' + includes_sub_vendors + '/' + sub_vendor_status + '/' + datasource);
          $('#edit-pin', context).autocomplete('option', 'source', '/autocomplete/contracts/pin/' + status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + mwbecat + '/' + industry + '/' + includes_sub_vendors + '/' + sub_vendor_status + '/' + datasource);
          $('#edit-entity-contract-number', context).autocomplete('option', 'source', '/autocomplete/contracts/entitycontractnum/' + status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + mwbecat + '/' + industry + '/' + includes_sub_vendors + '/' + sub_vendor_status + '/' + datasource);
          $('#edit-commodity-line', context).autocomplete('option', 'source', '/autocomplete/contracts/commodityline/' + status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + mwbecat + '/' + industry + '/' + includes_sub_vendors + '/' + sub_vendor_status + '/' + datasource);
          $('#edit-budget-name', context).autocomplete('option', 'source', '/autocomplete/contracts/budgetname/' + status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + mwbecat + '/' + industry + '/' + includes_sub_vendors + '/' + sub_vendor_status + '/' + datasource);

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

          $('#edit-nycha-vendor').autocomplete({source: $.fn.autoCompleteSourceUrl(datasource,'vendor_name_code',nycha_filters)});
          $('#edit-nycha-contract-id').autocomplete({source: $.fn.autoCompleteSourceUrl(datasource,'contract_number',nycha_filters)});
          $('#edit-nycha-apt-pin').autocomplete({source: $.fn.autoCompleteSourceUrl(datasource,'pin',nycha_filters)});
        });
      });
      //Year Drop-down
      $('#edit-nycha-year', context).change(function () {
        if ($(this).val() === '0') {
          $('#edit-column-select-nycha option[value="Year"]', context).attr('disabled', 'disabled');
        } else {
          $('#edit-column-select-nycha option[value="Year"]', context).attr('disabled', '');
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
        if ($(this).val() === '0') {
          $('#edit-column-select-expense option[value="Year"]', context).attr('disabled', 'disabled');
          $('#edit-column-select-oge-expense option[value="Year"]', context).attr('disabled', 'disabled');
          $('#edit-column-select-revenue option[value="Year"]', context).attr('disabled', 'disabled');
          $('#edit-column-select-all option[value="Year"]', context).attr('disabled', 'disabled');
        } else {
          $('#edit-column-select-expense option[value="Year"]', context).attr('disabled', '');
          $('#edit-column-select-oge-expense option[value="Year"]', context).attr('disabled', '');
          $('#edit-column-select-revenue option[value="Year"]', context).attr('disabled', '');
          $('#edit-column-select-all option[value="Year"]', context).attr('disabled', '');
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

      $.fn.fixAutoCompleteWrapping($("#dynamic-filter-data-wrapper").children());
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

  //Prevent the auto-complete from wrapping un-necessarily
  $.fn.fixAutoCompleteWrapping = function (divWrapper) {
    jQuery(divWrapper.children()).find('input.ui-autocomplete-input:text').each(function () {
      $(this).data("autocomplete")._resizeMenu = function () {
        (this.menu.element).outerWidth('100%');
      }
    });
  };

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
    var year_value = input.split('FY');
    return year_value[1];
  }

  //Function to clear text fields and drop-downs
  $.fn.clearInputFields = function (dataSource) {
    $('.fieldset-wrapper').find(':input').each(function () {
      switch (this.type) {
        case 'select-one':
          const default_option = $(this).attr('default_selected_value');
          if (default_option) {
            $(this).find('option[value=' + default_option + ']').attr("selected", "selected");
          } else {
            $(this).find('option:first').attr("selected", "selected");
          }
          break;
        case 'text':
          $(this).val('');
          break;
      }
    });

    //Enable "Contract includes sub vendors" and "Sub Vendor Status in PIP" drop-downs
    if (dataSource !== 'checkbook_oge') {
      $('#edit-contract_includes_sub_vendors_id').removeAttr('disabled');
      $('#edit-sub_vendor_status_in_pip_id').removeAttr('disabled');
    }

    //For OGE set 'Expense' as default category
    if (dataSource === 'checkbook_oge') {
      $('select[name="category"]').val('expense');
    }
  }

}(jQuery));
