jQuery(document).ready(function ($) {

  $('.expandCollapseWidget').each(function () {
    let $control = $(this);
    // Get heading text excluding contentCount.
    let $header = $control
      .closest('.node-widget')
      .find('.tableHeader h2')
      .first()
      .clone();

    $header.find('.contentCount').remove();

    let headerText = $header.text()
      .replace(/\s+/g, ' ')
      .trim();

    // Initial collapsed state.
    $control.attr('aria-expanded', 'false');
    $control.attr('aria-label', headerText);
  });
  // Mouse/click handling.
  $('.expandCollapseWidget').on('click', function (event) {

    event.preventDefault();
    let $control = $(this);
    let toggled = $control.data('toggled');
    let oTable = "";

    $control.data('toggled', !toggled);
    oTable = $control.parent().prev().find('.dataTable').dataTable();
    // Get Widget title text excluding the content count.
    let $header = $control
      .closest('.node-widget')
      .find('.tableHeader h2')
      .first()
      .clone();

    $header.find('.contentCount').remove();

    let headerText = $header.text()
      .replace(/\s+/g, ' ')
      .trim();

    let text = '';
    let aria_label = 'Expand';
    if (!toggled) {
      // Expanded.
      oTable.fnSettings().oInit.expandto150 = true;
      oTable.fnSettings().oInit.expandto5 = false;

      
      text = "<img src='/themes/custom/nyccheckbook/images/close.png' alt='Collapse'>";
      $control.parent().parent().find('.hideOnExpand').hide();
      // Adjust aria-label of h2 tag to remove the count.
      aria_label = $control.parent().parent().find('h2').attr('aria-label');
      if (aria_label.includes('Top')) {
        let words = aria_label.split(" ");
        words.splice(1, 1);
        aria_label = words.join(" ");
        $control.parent().parent().find('h2').html(aria_label);
        $control.parent().parent().find('h2').attr('aria-label', aria_label);
        $control.attr('aria-label', 'Collapse ' + aria_label);
      }
      else {
         $control.attr('aria-label', 'Collapse ' + $header.text()
          .replace(/\s+/g, ' ')
          .trim());
      }
      $control.attr('aria-expanded', 'true');
      oTable.fnDraw();
      $control.html(text);
      $control.find('img').attr('alt', 'Collapse ' + aria_label);
    }
    else {
      // Collapsed.
      oTable.fnSettings().oInit.expandto5 = true;
      oTable.fnSettings().oInit.expandto150 = false;

      $control.attr('aria-expanded', 'false');
      
      text = "<img src='/themes/custom/nyccheckbook/images/open.png' alt='Expand'>";

      let place = $('#' + oTable.fnSettings().sInstance + '_wrapper')
        .parent()
        .parent()
        .attr('id');
      document.getElementById(place).scrollIntoView();
      $control.parent().parent().find('.hideOnExpand').show();

      // Adjust aria-label of h2 tag to include the count.
      aria_label = $control.parent().parent().find('h2').attr('aria-label');
      if (aria_label.includes('Top')) {
        let words = aria_label.split(" ");
        words.splice(1, 0, '5');
        aria_label = words.join(" ");
        $control.parent().parent().find('h2').html(aria_label);
        $control.parent().parent().find('h2').attr('aria-label', aria_label);
        $control.attr('aria-label', 'Expand ' + aria_label);
      }
      else {
         $control.attr('aria-label', 'Expand ' + $header.text()
          .replace(/\s+/g, ' ')
          .trim());
      }
      $control.attr('aria-expanded', 'false');
      oTable.fnDraw();
      $control.html(text);
      $control.find('img').attr('alt', 'Expand ' + aria_label);
    }
   
  });

  // Keyboard support for <a role="button">.
  $('.expandCollapseWidget').on('keydown', function (event) {
    if (event.key === 'Enter' || event.key === ' ') {
      event.preventDefault();
      $(this).trigger('click');
    }
  });

});
