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
    static function getInitNodeSummaryContent($nid = null){
        $nid = isset($nid) ? $nid : self::getNodeId();

        if(empty($nid)){
            return NULL;
        }

        $node = node_load($nid);
        //For custom widgets
        if(!$node) $node = _widget_node_load_file($nid);
        widget_config($node);
        $node->widgetConfig->limit = 1;
        $node->widgetConfig->getTotalDataCount = false;
        if($node->widgetConfig->summaryView->disable_limit){
           unset($node->widgetConfig->limit);
        }

        //prepare anything we'll need before loading
        widget_prepare($node);
        //invoke widget specific prepare
        widget_invoke($node, 'widget_prepare');
        widget_data($node);

        if(isset($node->widgetConfig->summaryView->preprocess_data)){
            eval($node->widgetConfig->summaryView->preprocess_data);
        }

        $themekey = $node->widgetConfig->summaryView->template;
        return theme($themekey, array('node'=>$node));
    }

    static function getInitNodeSummaryTitle($nid = null){
        $bottomURL = $_REQUEST['expandBottomContURL'];
        $smnid = RequestUtil::getRequestKeyValueFromURL("smnid",$bottomURL);

        if($nid == null)
          $nid = self::getNodeId();

        if(empty($nid)){
            return NULL;
        }

        $node = node_load($nid);
        //For custom widgets
        if(!$node) $node = _widget_node_load_file($nid);
        widget_config($node);
        $title = NULL;
            if($smnid){
                if(isset($node->widgetConfig->summaryView->templateTitleEval)){
                    $title = eval($node->widgetConfig->summaryView->templateTitleEval);
                }
                else if(isset($node->widgetConfig->widgetTitle)){
                    $title = $node->widgetConfig->widgetTitle;
                }else if(isset($node->widgetConfig->WidgetTitleEval)){
                    $title = eval($node->widgetConfig->WidgetTitleEval);
                }else if(isset($node->widgetConfig->table_title)){
                    $title = $node->widgetConfig->table_title;
                }
            }
       else{
            if(isset($node->widgetConfig->widgetTitle)){
                $title = $node->widgetConfig->widgetTitle;
            }else if(isset($node->widgetConfig->WidgetTitleEval)){
                $title = eval($node->widgetConfig->WidgetTitleEval);
            }else if(isset($node->widgetConfig->table_title)){
                $title = $node->widgetConfig->table_title;
            }
        }
        return $title;
    }

    static function getInitNodeSummaryTemplateTitle($nid = null){
        $nid = isset($nid) ? $nid : self::getNodeId();

        if(empty($nid)){
            return NULL;
        }

        $node = node_load($nid);
        widget_config($node);
        $node->widgetConfig->limit = 1;
        $node->widgetConfig->getTotalDataCount = false;
        if($node->widgetConfig->summaryView->disable_limit){
            unset($node->widgetConfig->limit);
        }

        //prepare anything we'll need before loading
        widget_prepare($node);
        //invoke widget specific prepare
        widget_invoke($node, 'widget_prepare');
        widget_data($node);

        if(isset($node->widgetConfig->summaryView->preprocess_data)){
            eval($node->widgetConfig->summaryView->preprocess_data);
        }

        $title = $node->widgetConfig->summaryView->templateTitle;
        return $title;
    }

    static function getNodeId(){
        return RequestUtilities::getRequestParamValue('smnid');//summary node id
    }

}
