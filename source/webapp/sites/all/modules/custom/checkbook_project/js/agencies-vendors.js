/**
 * Moves inline script tags from inside a DataTable to end of document so they are evaluated
 *
 * Used as a callback function after a DataTable is redrawn
 */

function movescripts() {
    jQuery('[id^="table_"] script').each(
        function () {
            var script = document.createElement('script');
            script.type = 'text/javascript';
            script.text = jQuery(this).html();
            document.body.appendChild(script);
        }).remove();
}

/**
 * Moves inline script tags from inside a DataTable to end of document so they are evaluated
 *
 * Used as a callback function after a DataTable is redrawn
 */

function movetablescripts() {
    var i=0;
    var scripts = jQuery('[id^="table_"] script');
    for (i; i < scripts.length; i++){
        var scriptid = 'movescript-'+jQuery(scripts[i]).parents('table').attr('id')+'-'+i;
        if (jQuery('#'+scriptid).length === 0){
            var script = document.createElement('script');
            script.type = 'text/javascript';
            script.text = jQuery(scripts[i]).html();
            script.id = scriptid;
            document.body.appendChild(script);
            jQuery(scripts[i]).remove();
        }
    }
}

/**
 * Examines a set of checkboxes and toggles Highcharts series based on state of checkbox
 *
 * Used as a callback function after a DataTable is redrawn
 *
 * @param id
 *  The jQuery selector string of the form containing the checkboxes
 *
 * @param charts
 *  An array containing Highcharts objects
 */

function stickycheckboxes(id, charts){
    jQuery(document).ready(function(){
        jQuery(id).each(function(){
            toggleSeries(jQuery(this),charts);
            for (i = 0; i < charts.length; i++) {
            for (j = 0; j < charts[i].series.length; j++) {
                charts[i].redraw();
            }
        }
        })
    })
}

/**
 * Iterates through an array containing Highcharts objects and shows or hides a series
 * based on the state of a checkbox and that checkbox's name attribute
 *
 * @param input
 *  A jQuery object containing a checkbox input
 *
 * @param chartArray
 *  An array of Highcharts objects
 */

function toggleSeries(input, chartArray) {
    if (input.is(':checked')) {
        var name = jQuery(input).attr('name');
        for (i = 0; i < chartArray.length; i++) {
            for (j = 0; j < chartArray[i].series.length; j++) {
                if (chartArray[i].series[j].name === name)
                    chartArray[i].series[j].setVisible(true, false);
            }
        }
    }
    else {
        var name = jQuery(input).attr('name');
        for (i = 0; i < chartArray.length; i++) {
            for (j = 0; j < chartArray[i].series.length; j++) {
                if (chartArray[i].series[j].name === name)
                    chartArray[i].series[j].setVisible(false, false);
            }
        }
    }
}

/**
 *  Reloads the data table based on the selection of category checkboxes on 'Agencies Landing' page.
 * @param filterName
 *  The jQuery selector string of the form containing the checkboxes
 */
function submitAgencyCategories(filterName){
    var filterId = '';
    var oFilterIds = document.forms['form_spending_categories'].elements[filterName.name];
    if(eval(oFilterIds)){
        for(var i = 0; i < oFilterIds.length; i++)
        {
            if(oFilterIds[i].checked)
            {
                if(filterId.length>0){
                    filterId = filterId  +'~'+ oFilterIds[i].value;
                }else{
                    filterId = oFilterIds[i].value;
                }
            }
        }
    }
    
    if(filterId.length <= 0){
        filterId = "1~2~3";
        for(var i = 0; i < oFilterIds.length-1; i++)
        {
           oFilterIds[i].checked = true;
        }
    }

    var oTable = jQuery('.dataTable').dataTable();
    var oldFilterId = 'spending_category_id/' + oTable.dataTableSettings[0].sAjaxSource.split("/").pop();
    var newFilter = 'spending_category_id/' + filterId;
    oTable.dataTableSettings[0].sAjaxSource = oTable.dataTableSettings[0].sAjaxSource.replace(oldFilterId, newFilter);

    oTable.fnDraw();
}