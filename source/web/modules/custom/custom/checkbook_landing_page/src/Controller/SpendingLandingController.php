<?php
namespace Drupal\checkbook_landing_page\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;

class SpendingLandingController extends ControllerBase {

  /**
   * @param $params
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function nychaSpending($params) {
    $path = \Drupal::service('path_alias.manager')->getPathByAlias('/nycha_spending');
    if (preg_match('/node\/(\d+)/', $path, $matches)) {
      $node = Node::load($matches[1]);
    }
    if (!$node) {
      //if node not found by alias, then load harcoded value
      $node = \Drupal::entityTypeManager()->getStorage('node')->load(1143);
    }

    return \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');
  }

  /**
   * @param $params
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function nycSpending($params) {
    $path = \Drupal::service('path_alias.manager')->getPathByAlias('/spending_landing');
    if (preg_match('/node\/(\d+)/', $path, $matches)) {
      $node = Node::load($matches[1]);
    }
    if (!$node) {
      //if node not found by alias, then load harcoded value
      $node = \Drupal::entityTypeManager()->getStorage('node')->load(1144);
    }
    return \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');
  }

  /**
   * @param $params
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function subvendorSpending($params) {
    $path = \Drupal::service('path_alias.manager')->getPathByAlias('/spending_landing/dashboard/s*');

    if (preg_match('/node\/(\d+)/', $path, $matches)) {
      $node = Node::load($matches[1]);
    }
    if (!$node) {
      //if node not found by alias, then load harcoded value
      $node = \Drupal::entityTypeManager()->getStorage('node')->load(1146);
    }
    return \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');
  }

  /**
   * @param $params
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function mwbeSpending($params) {
    $path = \Drupal::service('path_alias.manager')->getPathByAlias('/spending_landing/dashboard/mp*');

    if (preg_match('/node\/(\d+)/', $path, $matches)) {
      $node = Node::load($matches[1]);
    }
    if (!$node) {
      //if node not found by alias, then load harcoded value
      $node = \Drupal::entityTypeManager()->getStorage('node')->load(1147);
    }
    return \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');
  }

  /**
   * @param $params
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function mwbesubSpending($params) {
    $path = \Drupal::service('path_alias.manager')->getPathByAlias('/spending_landing/dashboard/ms*');

    if (preg_match('/node\/(\d+)/', $path, $matches)) {
      $node = Node::load($matches[1]);
    }
    if (!$node) {
      //if node not found by alias, then load harcoded value
      $node = \Drupal::entityTypeManager()->getStorage('node')->load(1148);
    }
    return \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');
  }
}
