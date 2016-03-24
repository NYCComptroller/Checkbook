<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 3/24/16
 * Time: 2:34 PM
 */

require_once('WidgetView.php');

class LandingPageWidgetView extends WidgetView {

    function viewPrepare()
    {
        // TODO: Implement viewPrepare() method.
    }
    function viewGetData()
    {
        // TODO: Implement viewGetData() method.
    }

    function viewDisplay()
    {
        $sAjaxSource = '/checkbook_views/data_tables/ajax_data/node/' . $this->configKey;
        $sAjaxSource .= $this->getUrlFromRequest();
//        $node->widgetConfig->dataTableOptions->sAjaxSource =  _escape_special_characters(html_entity_decode($sAjaxSource,ENT_QUOTES));

        $node->widgetConfig->dataTableOptions->fnServerData= "##function ( sSource, aoData, fnCallback ) {
			aoData.push( {
			  'name': 'data_type', 'value': 'json' } );
			  jQuery.ajax( {
			    'dataType': 'json',
			    'type': 'GET',
			    'url': sSource,
			    'data': aoData,
			    'success': fnCallback
			  } );
			}##";

        return theme('landing_page_widget_view_by_rows_theme', array('node'=> $node));
    }
}