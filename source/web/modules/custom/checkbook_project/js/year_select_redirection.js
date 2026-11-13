(function ($) {
  $(document).ready(function () {
    // Initialize Chosen only when needed for Year and Fiscal year dropdowns
    /*
    ['year_list', 'fiscal_year_list'].forEach(function (id) {
      const $select = $('#' + id);

      if (!$select.length) {
        return;
      }

      if (!$select.data('chosen')) {
        $select.chosen({
          disable_search_threshold: 50
        });
      }

    });
    */
    //return;
    /**
     * Ensure the Year dropdown has an associated label.
     */
    function ensureYearLabel(selectId, labelText) {
      const $select = $('#' + selectId);

      if (!$select.length) {
        return;
      }

      const labelId = selectId + '_label';

      if (!$('#' + labelId).length) {
        $('<label>', {
          id: labelId,
          for: selectId,
          class: 'filter',
          text: labelText
        }).insertBefore($select);
      }

      $select.attr('aria-labelledby', labelId);
    }
    /**
     * Add accessibility support to Chosen dropdowns.
     */
    function makeChosenAccessible(selectId, label) {
      const $select = $('#' + selectId);
      const $chosen = $('#' + selectId + '_chosen');

      if (!$select.length || !$chosen.length) {
        return;
      }

      let $trigger = $chosen.find('.chosen-single');
      let $results = $chosen.find('.chosen-results');
      const $searchInput = $chosen.find('.chosen-search-input');
      const resultsId = selectId + '_results';

      /**
       * Configure the Chosen trigger.
       */
      function updateTrigger() {
        $trigger = $chosen.find('.chosen-single');

        if (!$trigger.length) {
          return;
        }

        const expanded = $chosen.hasClass('chosen-with-drop');

        $trigger.attr({
          'tabindex': '0',
          'role': 'combobox',
          'aria-label': 'Year Filter',
          'aria-haspopup': 'listbox',
          'aria-expanded': String(expanded),
          'aria-controls': resultsId
        });
      }

      /**
       * Configure the results list and options.
       */
      function updateOptions() {
        $results = $chosen.find('.chosen-results');

        $results.attr({
          'id': resultsId,
          'role': 'listbox'
        });

        $results.find('li.active-result, li.result-selected')
          .each(function () {
            const $option = $(this);

            $option.attr({
              'role': 'option',
              'aria-selected': $option.hasClass('result-selected')
                ? 'true'
                : 'false'
            });
          });
      }

      // Initialize accessibility attributes.
      updateTrigger();
      updateOptions();
      // Give the Chosen search input an accessible name.
      $searchInput.attr({
        'aria-label': 'Search ' + label
      });

      /**
       * Dropdown opened.
       */
      $select.on('chosen:showing_dropdown.a11y', function () {
        setTimeout(function () {
          updateTrigger();
          updateOptions();
        }, 0);
      });

      /**
       * Dropdown closed.
       */
      $select.on('chosen:hiding_dropdown.a11y', function () {
        $trigger.attr('aria-expanded', 'false');
      });

      /**
       * Selection changed or Chosen updated.
       */
      $select.on('change.a11y chosen:updated.a11y', function () {
        setTimeout(function () {
          updateTrigger();
          updateOptions();
        }, 0);
      });
    }

    ensureYearLabel('year_list', 'Year Filter:');
    ensureYearLabel('fiscal_year_list', 'Year Filter:');

    // Apply accessibility fixes to both dropdowns.
    makeChosenAccessible('year_list', 'Year Filter');
    makeChosenAccessible('fiscal_year_list', 'Year Filter');

    /**
     * Redirect when year selection changes.
     */
    $('#year_list, #fiscal_year_list').change(function () {
      window.location = $(this).find(':selected').attr('link');
    });

    /**
     * Close agency dropdowns.
     */
    $('#year_list_chosen, #fiscal_year_list_chosen').click(function () {
      $('.all-agency-list-content').slideUp(0);
      $('#all-agency-list-open').removeClass('open');

      $('.other-agency-list-content').slideUp(0);
      $('#other-agency-list-open').removeClass('open');
    });

  });
})(jQuery);
