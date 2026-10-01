(function ($) {
  $(document).ready(function () {

    // Year Dropdown.
    $('#year_list').chosen({
      disable_search_threshold: 50
    });

    // Fiscal Year Dropdown.
    $('#fiscal_year_list').chosen({
      disable_search_threshold: 50
    });

    /**
     * Add accessibility support to Chosen dropdowns.
     */
    function makeChosenAccessible(selectId, label) {
      let $select = $('#' + selectId);
      let $chosen = $('#' + selectId + '_chosen');
      let $trigger = $chosen.find('.chosen-single');
      let $results = $chosen.find('.chosen-results');
      let $searchInput = $chosen.find('.chosen-search-input');
      let resultsId = selectId + '_results';

      // Configure results list.
      $results.attr({
        'id': resultsId,
        'role': 'listbox'
      });

      // Configure visible Chosen control.
      $trigger.attr({
        'role': 'combobox',
        'aria-label': label,
        'aria-haspopup': 'listbox',
        'aria-expanded': 'false',
        'aria-controls': resultsId
      });

      // Give the Chosen search input an accessible name.
      $searchInput.attr({
        'aria-label': 'Search ' + label
      });

      /**
       * Update option roles and selected state.
       */
      function updateOptions() {
        $results.find('li.active-result').each(function () {
          $(this).attr({
            'role': 'option',
            'aria-selected': $(this).hasClass('result-selected')
              ? 'true'
              : 'false'
          });
        });
      }

      updateOptions();

      // Dropdown opened.
      $chosen.on('chosen:showing_dropdown', function () {
        $trigger.attr('aria-expanded', 'true');
        updateOptions();
      });

      // Dropdown closed.
      $chosen.on('chosen:hiding_dropdown', function () {
        $trigger.attr('aria-expanded', 'false');
      });

      // Selection changed.
      $select.on('change', function () {
        updateOptions();
      });
    }

    // Apply accessibility fixes.
    makeChosenAccessible('year_list', 'Fiscal Year');
    makeChosenAccessible('fiscal_year_list', 'Fiscal Year');

    // Redirect when year selection changes.
    $('#year_list,#fiscal_year_list').change(function () {
      window.location = $(this).find(':selected').attr('link');
    });

    // Close agency dropdowns.
    $('#year_list_chosen,#fiscal_year_list_chosen').click(function () {
      $('.all-agency-list-content').slideUp(0);
      $('#all-agency-list-open').removeClass('open');

      $('.other-agency-list-content').slideUp(0);
      $('#other-agency-list-open').removeClass('open');
    });

  });
})(jQuery);
