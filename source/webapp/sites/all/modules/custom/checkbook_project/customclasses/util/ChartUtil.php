<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class ChartUtil
{
    static function generateGridViewLink($node){
        return '<a class="chart-grid-view gridpopup" style="display:none"
                href="/gridview/popup/node/' . $node->nid . '?refURL='. drupal_get_path_alias($_GET['q']) .'">Grid View</a>';
    }

    /*static function isGridView($node){
        return ($node->widgetConfig->displayType == 'gridview');
    }*/

}
