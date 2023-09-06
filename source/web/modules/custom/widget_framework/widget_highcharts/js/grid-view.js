function addPaddingToDataCells(table) {
  (function ($) {
    $(table).find("th").each(function (i, val) {
        if ($(this).hasClass("number")) {
          var colwidth = $(this).find("span").width();
          var maxDataWidth = 0;
          $(table).find("tr td:nth-child(" + (i + 1) + ")").each(
            function () {
              if (maxDataWidth < $(this).find("div").width()) {
                maxDataWidth = $(this).find("div").width();
              }
            }
          );
          if ((colwidth - maxDataWidth) / 2 > 1) {
            $(table).find("tr td:nth-child(" + (i + 1) + ") div").css("margin-right", Math.floor((colwidth - maxDataWidth) / 2) + "px");
          }
        }
      }
    );
    $(".DTFC_LeftHeadWrapper table").find("th").each(function (i, val) {
        if ($(this).hasClass("number")) {
          var colwidth = $(this).find("div").width();
          var maxDataWidth = 0;
          $(".DTFC_LeftBodyWrapper table").find("tr td:nth-child(" + (i + 1) + ")").each(
            function () {
              if (maxDataWidth < $(this).find("div").width()) {
                maxDataWidth = $(this).find("div").width();
              }
            }
          );
          if ((colwidth - maxDataWidth) / 2 > 1) {
            $(".DTFC_LeftBodyWrapper table").find("tr td:nth-child(" + (i + 1) + ") div").css("margin-right", Math.floor((colwidth - maxDataWidth) / 2) + "px");
          }
        }
      }
    );
    $(".dataTables_scrollHeadInner table").find("th").each(function (i, val) {
        if ($(this).hasClass("number")) {
          var colwidth = $(this).find("div").width();
          var maxDataWidth = 0;
          $(".dataTables_scrollBody table").find("tr td:nth-child(" + (i + 1) + ")").each(
            function () {
              if (maxDataWidth < $(this).find("div").width()) {
                maxDataWidth = $(this).find("div").width();
              }
            }
          );
          if ((colwidth - maxDataWidth) / 2 > 1) {
            $(".dataTables_scrollBody table").find("tr td:nth-child(" + (i + 1) + ") div").css("margin-right", Math.floor((colwidth - maxDataWidth) / 2) + "px");
          }
        }
      }
    );
  }(jQuery));
}
function custom_number_format(number) {
  if (number == null || number == '') {
    return '$0.00';
  }

  var decimal_digits = 2;
  var prefix = '$';

  var thousands = 1000;
  var millions = thousands * 1000;
  var billions = millions * 1000;
  var trillions = billions * 1000;
  var formattedNumber = '';

  var absNumber = Math.abs(number);

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

(function ($) {
  Drupal.behaviors.exportGridTransactions = {
    attach: function (context, settings) {
      $('span.grid_export').off().on("click", function () {
        var nodeId = $(this).attr('exportid');
        var oSettings = $('#table_' + nodeId).dataTable().fnSettings();

        var url = '/export/grid/transactions/'+ nodeId;
        var inputs = "<input type='hidden' name='refURL' value='" + (oSettings.sAjaxSource != null ? oSettings.sAjaxSource : oSettings.oInit.sAltAjaxSource) + "'/>"
          + "<input type='hidden' name='iDisplayStart' value='" + oSettings._iDisplayStart + "'/>"
          + "<input type='hidden' name='iDisplayLength' value='" + oSettings._iDisplayLength + "'/>"
          + "<input type='hidden' name='node' value='" + nodeId + "'/>"
        ;

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

      });
    }
  };

  Drupal.behaviors.printGridTransactions = {
    attach: function (context, settings) {
      $(once('span_grid_print', 'span.grid_print')).on("click", function () {
        let url = new URL(window.location.href);
        url.searchParams.set('print', '')
        window.open(decodeURIComponent(url.href.replace(/=$/, '')));
      });
    }
  };

  // Print page on load.
  $(function() {
    let url = new URL(window.location.href);
    if (url.searchParams.has('print')) {
      window.print();
    }
  })

}(jQuery));

