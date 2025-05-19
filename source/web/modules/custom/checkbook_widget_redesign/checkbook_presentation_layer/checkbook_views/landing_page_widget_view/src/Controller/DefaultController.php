<?php /**
 * @file
 * Contains \Drupal\landing_page_widget_view\Controller\DefaultController.
 */

namespace Drupal\landing_page_widget_view\Controller;

use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_project\CommonUtilities\RequestUtil;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Default controller for the landing_page_widget_view module.
 */
class DefaultController extends ControllerBase
{
  public function _landing_page_widget_view_ajaxdata($key) {
    $id = RequestUtil::_getnodeid($key);
    RequestUtilities::resetUrl();
    $node = landing_page_widget_config_load_data($id);
    $node = landing_page_widget_view_get_data($node);
    __landing_page_widget_add_padding_data($node);
    $result = landing_page_widget_view_ajaxdata_json($node);
    $response = new Response();
    $response->setContent(json_encode($result));
    $response->headers->set('Content-Type', 'application/json');
    return $response;
  }
}
