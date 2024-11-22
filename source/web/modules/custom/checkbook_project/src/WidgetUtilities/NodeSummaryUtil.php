<?php
namespace Drupal\checkbook_project\WidgetUtilities;

use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Exception;

class NodeSummaryUtil
{
  /**
   * @param null $nid
   * @return mixed|string|null
   * @throws Exception
   */
    public static function getInitNodeSummaryContent($nid = null){
        $nid = $nid ?? self::getNodeId();
        if(empty($nid)){
            return NULL;
        }
      if (!WidgetUtil::isWidgetJsonValid($nid)) {
        return NULL;
      }

        $node = _widget_node_load_file($nid);
        widget_config($node);
        $node->widgetConfig->limit = 1;
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
        else if(isset($node->widgetConfig->preprocess_data)){
          eval($node->widgetConfig->preprocess_data);
        }

        $themekey = $node->widgetConfig->summaryView->template ?? $node->widgetConfig->template;
        $twigFilePath = \Drupal\widget_services\Common\CommonService::getTemplatePath($themekey);
        $twigService = \Drupal::service('twig');
        $templateClass = $twigService->getTemplateClass($twigFilePath);
        $template = $twigService->loadTemplate($templateClass, $twigFilePath);
        $markup = [
        '#markup' => $template->render([ 'node' => $node])
        ];
        return \Drupal::service('renderer')->render($markup);
    }

  /**
   * @param null $nid
   * @return mixed|null
   */
    public static function getInitNodeSummaryTitle($nid = null){
      $nid = $nid ?? self::getNodeId();
      if(empty($nid)){
        return NULL;
      }
      $smnid = RequestUtilities::get('smnid');
        $node = _widget_node_load_file($nid);
        widget_config($node);
        $title = NULL;
            if($smnid){
                if(isset($node->widgetConfig->summaryView->templateTitleEval)){
                    $title = eval($node->widgetConfig->summaryView->templateTitleEval);
                }
                else if(isset($node->widgetConfig->templateTitleEval)){
                  $title = eval($node->widgetConfig->templateTitleEval);
                }
                else if(isset($node->widgetConfig->widgetTitle)){
                    $title = $node->widgetConfig->widgetTitle;
                }else if(isset($node->widgetConfig->WidgetTitleEval)){
                    $title = eval($node->widgetConfig->WidgetTitleEval);
                }else if(isset($node->widgetConfig->table_title)){
                    $title = $node->widgetConfig->table_title;
                }
            } else{
              if(isset($node->widgetConfig->summaryView->templateTitleEval)){
                $title = eval($node->widgetConfig->summaryView->templateTitleEval);
              }
              else if(isset($node->widgetConfig->WidgetTitleEval)){
                $title = eval($node->widgetConfig->WidgetTitleEval);
              }
            else if(isset($node->widgetConfig->widgetTitle)){
                $title = $node->widgetConfig->widgetTitle;
            }else if(isset($node->widgetConfig->table_title)){
                $title = $node->widgetConfig->table_title;
            }
        }
        return $title;
    }

  /**
   * @param null $nid
   * @return null
   */
    public static function getInitNodeSummaryTemplateTitle($nid = null){
        $nid = $nid ?? self::getNodeId();
        if(empty($nid)){
            return NULL;
        }

        $node = _widget_node_load_file($nid);
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
      if(isset($node->widgetConfig->preprocess_data)){
        eval($node->widgetConfig->preprocess_data);
      }
        return $node->widgetConfig->summaryView->templateTitle ?? $node->widgetConfig->templateTitle;
    }

  /**
   * @return array|string|null
   */
    static function getNodeId(){
      $sumnid = RequestUtilities::_getRequestParamValueBottomURL('smnid');
      $sumnid = $sumnid ?? RequestUtilities::get('smnid');
      return $sumnid;
    }

}
