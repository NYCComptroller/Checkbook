<?php /**
 * @file
 * Contains \Drupal\widget_controller\Controller\DefaultController.
 */

namespace Drupal\widget_controller\Controller;

use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_project\CommonUtilities\RequestUtil;
use Drupal\Core\Controller\ControllerBase;

/**
 * Default controller for the widget_controller module.
 */
class DefaultController extends ControllerBase {

  public function _widget_controller_node_view_page($key) {
      $id = RequestUtil::_getnodeid($key);
      RequestUtilities::resetUrl();
     $node = _widget_controller_node_load_file($id);
     $node = widget_merge_default_settings($node);
     $node = widget_controller_node_view($node);
     return $node->content['body'];//return $node->content['body']['#markup'];
  }
}
