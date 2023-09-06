(function ($) {
  // Document is ready.
  $(function () {
    // MWBE Agency Summary export sorting.
    $('body')
      .on('click', 'span.summary_export', function () {
        var oSettings = $('#grading_table').dataTable().fnSettings();
        var url = '';
        var url_path = location.pathname.split("/");
        for (var i = 0; i < url_path.length; i++) {
          if (url_path[i] === 'mwbe_agency_grading') {
            url += 'mwbe_agency_grading_csv/';
          }else {
            url += url_path[i] + '/';
          }
        }
        var iDisplayLength = oSettings._iDisplayLength;
        var iDisplayStart = oSettings._iDisplayStart;

        let inputs = "<input type='hidden' name='iDisplayStart' value='" + oSettings._iDisplayStart + "'/>"
            + "<input type='hidden' name='iDisplayLength' value='" + oSettings._iDisplayLength + "'/>";

        if (oSettings.oFeatures.bSort !== false) {
          var iCounter = 0;

          aaSort = (oSettings.aaSortingFixed !== null) ?
              oSettings.aaSortingFixed.concat(oSettings.aaSorting) :
              oSettings.aaSorting.slice();

          for (i = 0; i < aaSort.length; i++) {
            aDataSort = oSettings.aoColumns[aaSort[i][0]].aDataSort;

            for (j = 0; j < aDataSort.length; j++) {
              inputs = inputs + "<input type='hidden' name='iSortCol_" + iCounter + "' value='" + aDataSort[j] + "'/>";
              inputs = inputs + "<input type='hidden' name='sSortDir_" + iCounter + "' value='" + aaSort[i][1] + "'/>";
              iCounter++;
            }
          }
          inputs = inputs + "<input type='hidden' name='iSortingCols' value='" + iCounter + "'/>";
        }
        $('<form action="' + url + '" method="get">' + inputs + '</form>').appendTo('body').submit().remove();
      })
      .on('click', '.tabs li a', function () {
        // Active state for tabs
        $(".tabs li a").removeClass("active");
        $(this).addClass("active");

        // Active state for Tabs Content
        //$(".tab_content_container > .tab_content_active").removeClass("tab_content_active").fadeOut(200);
        //$(this.rel).fadeIn(500).addClass("tab_content_active");
      });
  })
})(jQuery);

//@TO DO: Refactor duplicate functions
function custom_number_format(number) {
  if (number == null || number === '') {
    return '$0.00';
  }

  let decimal_digits = 2;
  let prefix = '$';

  let thousands = 1000;
  let millions = thousands * 1000;
  let billions = millions * 1000;
  let trillions = billions * 1000;
  let formattedNumber = '';

  let absNumber = Math.abs(number);

  if (absNumber >= trillions) {
    formattedNumber = prefix + addCommas((absNumber / trillions).toFixed(decimal_digits)) + 'T';
  }
  else if (absNumber >= billions) {
    formattedNumber = prefix + addCommas((absNumber / billions).toFixed(decimal_digits)) + 'B';
  }
  else if (absNumber >= millions) {
    formattedNumber = prefix + addCommas((absNumber / millions).toFixed(decimal_digits)) + 'M';
  }
  else if (absNumber >= thousands) {
    formattedNumber = prefix + addCommas((absNumber / thousands).toFixed(decimal_digits)) + 'K';
  }
  else {
    formattedNumber = prefix + addCommas(absNumber.toFixed(decimal_digits));
  }
  return (number < 0) ? ('-' + formattedNumber) : formattedNumber;
}

function addCommas(nStr) {
  nStr += '';
  c = nStr.split(',');
  nStr = c.join('');
  x = nStr.split('.');
  x1 = x[0];
  x2 = x.length > 1 ? '.' + x[1] : '';
  var rgx = /(\d+)(\d{3})/;
  while (rgx.test(x1)) {
    x1 = x1.replace(rgx, '$1' + ',' + '$2');
  }
  return x1 + x2;
}

