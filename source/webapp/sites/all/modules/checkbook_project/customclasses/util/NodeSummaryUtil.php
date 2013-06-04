<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
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
