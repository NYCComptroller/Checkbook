jQuery(document).ready(function ($) {
  $('.expandCollapseWidget').on("click",
    function (event) {
      var toggled = $(this).data('toggled');
      $(this).data('toggled', !toggled);

      event.preventDefault();
      oTable = $(this).parent().prev().find('.dataTable').dataTable();
      var text = "";
      if (!toggled) {
        oTable.fnSettings().oInit.expandto150 = true;
        oTable.fnSettings().oInit.expandto5 = false;
        text = "<img src='/themes/custom/nyccheckbook/images/close.png'>";
        $(this).parent().parent().find('.hideOnExpand').hide();

      }
      else {
        oTable.fnSettings().oInit.expandto5 = true;
        oTable.fnSettings().oInit.expandto150 = false;
        text = "<img src='/themes/custom/nyccheckbook/images/open.png'>";
        var place = $('#' + oTable.fnSettings().sInstance + '_wrapper').parent().parent().attr('id');
        document.getElementById(place).scrollIntoView();
        $(this).parent().parent().find('.hideOnExpand').show();
      }
      oTable.fnDraw();
      $(this).html(text);
    }
  );
});
