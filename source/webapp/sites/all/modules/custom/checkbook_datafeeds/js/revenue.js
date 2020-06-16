(function ($) {
  Drupal.behaviors.budgetDataFeeds = {
    attach: function (context, settings) {
      //DataSource Filter Formatter
      $.fn.formatDatafeedsDatasourceRadio();

      //Sets up jQuery UI autocompletes and autocomplete filtering functionality
      let year = ($('#edit-fiscal-year', context).val() === 'All Years') ? 0 : $('#edit-fiscal-year', context).val();
      let fundclass = emptyToZero($('#edit-fund-class', context).val());
      let agency = emptyToZero($('#edit-agency', context).val());
      let budgetyear = ($('#edit-budget-fiscal-year', context).val() === 'All Years') ? 0 : $('#edit-budget-fiscal-year', context).val();
      let revcat = emptyToZero($('#edit-revenue-category', context).val());
      let revclass = emptyToZero($('#edit-revenue-class', context).val());
      let revsrc = emptyToZero($('#edit-revenue-source', context).val());
      let fundingsrc = emptyToZero($('#edit-funding-class', context).val());
      $('#edit-revenue-class', context).autocomplete({source:'/autocomplete/revenue/revenueclass/' + year + '/' + fundclass + '/' + agency + '/' + budgetyear + '/' + revcat + '/' + revsrc + '/' + fundingsrc});
      $('#edit-revenue-source', context).autocomplete({source:'/autocomplete/revenue/revenuesource/' + year + '/' + fundclass + '/' + agency + '/' + budgetyear + '/' + revcat + '/' + revclass + '/' + fundingsrc});
      $('.watch:input', context).each(function () {
        $(this).focusin(function () {
          year = ($('#edit-fiscal-year', context).val() === 'All Years') ? 0 : $('#edit-fiscal-year', context).val();
          fundclass = emptyToZero($('#edit-fund-class', context).val());
          agency = emptyToZero($('#edit-agency', context).val());
          budgetyear = ($('#edit-budget-fiscal-year', context).val() === 'All Years') ? 0 : $('#edit-budget-fiscal-year', context).val();
          revcat = emptyToZero($('#edit-revenue-category', context).val());
          revclass = emptyToZero($('#edit-revenue-class', context).val());
          revsrc = emptyToZero($('#edit-revenue-source', context).val());
          fundingsrc = emptyToZero($('#edit-funding-class', context).val());
          $("#edit-revenue-class").autocomplete("option", "source", '/autocomplete/revenue/revenueclass/' + year + '/' + fundclass + '/' + agency + '/' + budgetyear + '/' + revcat + '/' + revsrc + '/' + fundingsrc);
          $("#edit-revenue-source").autocomplete("option", "source", '/autocomplete/revenue/revenuesource/' + year + '/' + fundclass + '/' + agency + '/' + budgetyear + '/' + revcat + '/' + revclass + '/' + fundingsrc);
        });
      });

      // Sets up multi-select/option transfer for CityWide
      $('#edit-column-select',context).multiSelect();
      $('#ms-edit-column-select .ms-selectable .ms-list',context).after('<a class="select">Add All</a>');
      $('#ms-edit-column-select .ms-selection .ms-list',context).after('<a class="deselect">Remove All</a>');
      $('#ms-edit-column-select a.select',context).click(function(){
        $('#edit-column-select',context).multiSelect('select_all');
      });
      $('#ms-edit-column-select a.deselect',context).click(function(){
        $('#edit-column-select',context).multiSelect('deselect_all');
      });
    }
  }

  //Function to retrieve values enclosed in brackets or return zero if none
  function emptyToZero(input) {
    const p = /\[(.*?)]$/;
    const code = p.exec(input.trim());
    if (code) {
      return code[1];
    }
    return 0;
  }
}(jQuery));
