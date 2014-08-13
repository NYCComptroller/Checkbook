<?php
/**
* This file is part of the Checkbook NYC financial transparency software.
* 
* Copyright (C) 2012, 2013 New York City
* 
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Affero General Public License as
* published by the Free Software Foundation, either version 3 of the
* License, or (at your option) any later version.
* 
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU Affero General Public License for more details.
* 
* You should have received a copy of the GNU Affero General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


class ChartUtil
{
    static function generateGridViewLink($node){
        return '<a class="chart-grid-view gridpopup" style="display:none"
                href="/gridview/popup/node/' . $node->nid . '?refURL='. check_plain(drupal_get_path_alias($_GET['q'])) .'">Grid View</a>';
    }
    
    static function generateWidgetGridViewLink($node){
    	return '<a class="chart-grid-view gridpopup" style="display:none"
                href="/gridview/popup/widget/' . $node->nid . '?refURL='. check_plain(drupal_get_path_alias($_GET['q'])) .'">Grid View</a>';
    }

    /*static function isGridView($node){
        return ($node->widgetConfig->displayType == 'gridview');
    }*/

}
