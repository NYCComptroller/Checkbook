//Helper function to remove "FY", and remove "~" if present from string
let removeFY = function (year) {
  return year.replace(/fy[~]*/ig, '').trim();
}

function enable_input(selector) {
  if (Array.isArray(selector)) {
    selector.forEach(enable_input);
    return;
  }

  jQuery(selector).each(function () {
    jQuery(this).prop("disabled", false);

    // restore value
    if ('text' === this.type) {
      if (jQuery(this).attr('storedvalue')) {
        jQuery(this).val(jQuery(this).attr('storedvalue'));
      }
      jQuery(this).removeAttr('storedvalue');
    }
  });
}

function disable_input(selector) {
  if (Array.isArray(selector)) {
    selector.forEach(disable_input);
    return;
  }
  jQuery(selector).each(function () {
    jQuery(this).prop("disabled", true);
    jQuery('.date-item-label', jQuery(this).parent()).html('');
    // store value
    if ('text' === this.type) {
      if (jQuery(this).val()) {
        jQuery(this).attr('storedvalue', jQuery(this).val());
      }
      jQuery(this).val('');
    }
    if (this.type === 'select-one') {
      jQuery(this).val(jQuery(this).find('option:first').val());
    }
  });
}

// updateYearValue - change year value display for conditional categories.
function updateEventYearValue(enclosingDiv, cevent, domain = '') {
  jQuery(enclosingDiv).each(function () {
    let year = "";
    if ((this.text).indexOf(' ') > -1) {
      year = parseInt((this.text).split(' ')[1]);
    }
    else{
      year = parseInt(this.text);
    }

    if (year < 2020 && parseInt(cevent) === 1) {
      jQuery(this).hide();
    } else if (year < 2018 && parseInt(cevent) === 2 && (domain !== 'revenue' && domain !== 'budget')) {
      jQuery(this).hide();
    } else {
      jQuery(this).show();
    }
  });
}

// updateConditionalEventValue - change Conditional Category value based on the selected year.
function updateConditionalEventValue(enclosingDiv, year, allDisable, domain = '') {
  year = parseInt(year) ?? 0;
  year = year && year <= 1899 ? year + 1899 : year;

  if(domain === 'revenue' || domain === 'budget') {
    if(year < 2020){
      disable_input(enclosingDiv.find('option[value*=1]'));
    }else{
      enable_input(enclosingDiv.find('option[value*=1]'));
    }
  }else{
    if ((allDisable || (year && year < 2018))) {
      disable_input(enclosingDiv);
      jQuery(enclosingDiv).val('0');
    } else if (year && (year === 2019 || year === 2018)) {
      enable_input(enclosingDiv);
      disable_input(enclosingDiv.find('option[value*=1]'));
    }else{
      enable_input(enclosingDiv);
      enable_input(enclosingDiv.find('option[value*=1]'));
    }
  }
}

// advanced-search clearing/restting input fields
function clearInputFields(enclosingDiv, domain, dataSourceName) {
  jQuery(enclosingDiv).find(':input').each(function () {
    switch (this.type) {
      case 'select-one':
        jQuery(this).val(jQuery(this).find('option:not([disabled]):first').val());
        break;
      case 'date':
        jQuery(this).val('');
        jQuery('.date-item-label', jQuery(this).parent()).html('');
        break;
      case 'text':
        jQuery(this).val('');
        jQuery(this).removeAttr('storedvalue');
        break;
      case 'select-multiple':
      case 'password':
      case 'textarea':
        jQuery(this).val('');
        break;
      case 'checkbox':
      case 'radio':
        switch (domain) {
          case 'payroll':
            jQuery("input[name='payroll_amount_type'][value='0']").prop('checked', true);
            break;
        }
        break;
    }
  });
}
