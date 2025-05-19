(function ($) {
$(document).ready(function(){
    // Year Dropdown
    $('#year_list').chosen({disable_search_threshold: 50});

    // Fiscal Year Dropdown
    $('#fiscal_year_list').chosen({disable_search_threshold: 50});

    $('#year_list,#fiscal_year_list').change(function(){
      let link = jQuery('#year_list :selected').attr("link");
      window.location = link;
    });

    $('#year_list_chosen,#fiscal_year_list_chosen').click(function(){
      // Close agencies dropdown
      $(".all-agency-list-content").slideUp(0);
      $("#all-agency-list-open").removeClass("open");

      $(".other-agency-list-content").slideUp(0);
      $("#other-agency-list-open").removeClass("open");
    });
})
})(jQuery);
