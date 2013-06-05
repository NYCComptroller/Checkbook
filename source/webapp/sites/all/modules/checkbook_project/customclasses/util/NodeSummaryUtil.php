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


class NodeSummaryUtil
{
    /** Prepare node summary content for the given template */
    static function getInitNodeSummaryContent(){
        $nid = self::getNodeId();

        if(empty($nid)){
            return NULL;
        }

        $node = node_load($nid);
        widget_config($node);

        $node->widgetConfig->getTotalDataCount = false;
        $node->widgetConfig->limit = 1;

        //prepare anything we'll need before loading
        widget_prepare($node);
        //invoke widget specific prepare
        widget_invoke($node, 'widget_prepare');
        widget_data($node);

        $themekey = $node->widgetConfig->summaryView->template;
        return theme($themekey, array('node'=>$node));
    }

    /** Prepare node summary Title for the given template */
    static function getInitNodeSummaryTitle($nid = null){
      
        if($nid == null)
          $nid = self::getNodeId();

        if(empty($nid)){
            return NULL;
        }

        $node = node_load($nid);
        widget_config($node);

        $title = NULL;
        if(isset($node->widgetConfig->widgetTitle)){
            $title = $node->widgetConfig->widgetTitle;
        }else if(isset($node->widgetConfig->WidgetTitleEval)){
            $title = eval($node->widgetConfig->WidgetTitleEval);
        }else if(isset($node->widgetConfig->table_title)){
            $title = $node->widgetConfig->table_title;
        }

        return $title;
    }

    /** Returns node id from url */
    static function getNodeId(){
        return _getRequestParamValue('smnid');//summary node id
    }

}
