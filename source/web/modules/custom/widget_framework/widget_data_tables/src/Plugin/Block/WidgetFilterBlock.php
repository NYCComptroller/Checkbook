<?php
/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (c) 2012 – 2023 New York City
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Drupal\widget_data_tables\Plugin\Block;

use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_log\LogHelper;
use Drupal\checkbook_project\WidgetUtilities\WidgetUtil;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use ParseError;

/**
 * Provides a 'Widget Filter' Block.
 *
 * @Block(
 *   id = "widget_filter_block",
 *   admin_label = @Translation("Widget Filter block"),
 *   category = @Translation("Custom"),
 * )
 */

class WidgetFilterBlock extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
    $current_route = \Drupal::routeMatch()->getRouteName();
    $disable_block = $this->configuration['disable_block'] ?? NULL;
    if ($disable_block) {
      if (str_contains($current_route, 'layout_builder.')) {
        return [
          '#markup' => "<h4>Block is Disabled.<br>Widget Filter Block for " . $this->configuration['widget_id'] . ". " . $this->getVisibilityRuleSettingString() . "</h4>",
        ];
      }
      else {
        return $this->emptyReturn();
      }
    }
    elseif (str_contains($current_route, 'layout_builder.')) {
      $widget_id = $this->configuration['widget_id'] ?? NULL;
      return [
        '#markup' => "Widget Filter Block for $widget_id. " . $this->getVisibilityRuleSettingString(),
      ];
    }
    elseif ($this->checkVisibility()) {
      $id = $this->configuration['widget_id'];
      if (!(WidgetUtil::isWidgetJsonValid($id))) {
        return [
          '#markup' => $id . ' is not a valid Widget Id.',
        ];
      }
      $request_params= \Drupal::request()->attributes->get('params');

      //need to add widget_id infront of request as _widget_node_view_page expects it
      $request_params = $id . ':' . $request_params;

      $contentController = new \Drupal\widget\Controller\DefaultController();
      $result = $contentController->_widget_node_view_page($request_params);
      if (isset($result) && !empty($result)) {
        //adding class name for widgets so that css rule can be applied at individual widget levels
        $result['#attributes']['class'][] = 'node-widget-' . $id;
        $result['#cache']['contexts'] = ['url.path', 'url.query_args'];
        return $result;
      }
      else {
        LogHelper::log_warn("Widget ". $id . " did not return result.");
        return $this->emptyReturn();
      }
    }
    else  {
      LogHelper::log_debug("Visibility Rule for widget " . $this->configuration['widget_id'] . " was not met.");
      return $this->emptyReturn();
    }
  }

  private function emptyReturn(): array {
    return [
      '#markup' => '',
    ];
  }
  private function getVisibilityRuleSettingString() {
    $return = '';
    $paths = $this->configuration['paths'];
    $visibility_setting = $this->configuration['visibility_setting'];
    $not = $this->configuration['not'];
    $paths2 = $this->configuration['paths_2nd'];
    $visibility_setting2 = $this->configuration['visibility_setting_2nd'];
    $not2 = $this->configuration['not_2nd'];
    $phpVisibility = $this->configuration['php_visibility'];
    $php_visibility_not = $this->configuration['php_visibility_not'];

    $ruleNo = 0;

    if ((isset($paths) && !empty($paths)) || isset($paths2) && !empty($paths2) || isset($phpVisibility) && !empty($phpVisibility)) {
      $return .= "<br>Visibility rules<br/>";
    }

    if (isset($paths) && !empty($paths)) {
      $ruleNo++;
      $return .= "Rule $ruleNo: ";
      if ((isset($visibility_setting) && !$visibility_setting) xor (isset($not) && $not)) {
        $return .= "NOT ";
      }
      $return .= "one of $paths<br/>";
    }

    if (isset($paths2) && !empty($paths2)) {
      $ruleNo++;
      $return .= "Rule $ruleNo: ";
      if ((isset($visibility_setting2) && !$visibility_setting2) xor (isset($not2) && $not2)) {
        $return .= "NOT ";
      }
      $return .= "one of $paths2<br/>";
    }

    if (isset($phpVisibility) && !empty($phpVisibility)) {
      $return .= "PHP Rule: ";
      if (isset($php_visibility_not) && $php_visibility_not) {
        $return .= "NOT ";
      }
      $return .= "$phpVisibility<br/>";
    }
    return $return;
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['widget_id'] = array(
      '#type' => 'textfield',
      '#attributes' => array(
        'type' => 'number',
        ),
      '#title' => $this->t('Widget ID'),
      '#description' => $this->t("Id of widget to load"),
      '#maxlength' => 50,
      '#size' => 50,
      '#default_value' => $this->configuration['widget_id'],
    );
    $disable_block = (isset($this->configuration['disable_block'])) ? $this->configuration['disable_block'] : '0';
    $form['disable_block'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Disable this block.'),
      '#default_value' => $disable_block,
    );
    $dont_use_bottom_container = (isset($this->configuration['dont_use_bottom_container'])) ? $this->configuration['dont_use_bottom_container'] : '0';
    $form['dont_use_bottom_container'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Use Current URL.'),
      '#description' => $this->t("If checked, then will not use BottomContainerURL for parameters."),
      '#default_value' => $dont_use_bottom_container,
    );
    $visibility_setting_value = (isset($this->configuration['visibility_setting'])) ? $this->configuration['visibility_setting'] : '1';
    $form['visibility_setting'] = array(
      '#type' => 'radios',
      '#title' => $this->t('Path visibility Setting'),
      '#default_value' => $visibility_setting_value,
      '#options' => array(
        1 => $this->t('Allow access on the following pages'),
        0 => $this->t('Allow access on all pages except the following pages'),
      ),
    );
    $form['paths'] = array(
      '#type' => 'textarea',
      '#title' => $this->t('Paths'),
      '#default_value' => $this->configuration['paths'],
    );
    $not_setting_value = (isset($this->configuration['not'])) ? $this->configuration['not'] : '0';
    $form['not'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Reverse (NOT)'),
      '#default_value' => $not_setting_value,
    );

    $visibility_setting_value_2nd = (isset($this->configuration['visibility_setting_2nd'])) ? $this->configuration['visibility_setting_2nd'] : '1';
    $form['visibility_setting_2nd'] = array(
      '#type' => 'radios',
      '#title' => $this->t('Path visibility Setting (2nd)'),
      '#default_value' => $visibility_setting_value_2nd,
      '#options' => array(
        1 => $this->t('Allow access on the following pages'),
        0 => $this->t('Allow access on all pages except the following pages'),
      ),
    );
    $form['paths_2nd'] = array(
      '#type' => 'textarea',
      '#title' => $this->t('Paths'),
      '#default_value' => $this->configuration['paths_2nd'],
    );
    $not_setting_value = (isset($this->configuration['not_2nd'])) ? $this->configuration['not_2nd'] : '0';
    $form['not_2nd'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Reverse (NOT)'),
      '#default_value' => $not_setting_value,
    );
    $form['php_visibility'] = array(
      '#type' => 'textarea',
      '#title' => $this->t('PHP visibility setting'),
      '#default_value' => $this->configuration['php_visibility'],
    );
    $not_setting_value = (isset($this->configuration['php_visibility_not'])) ? $this->configuration['php_visibility_not'] : '0';
    $form['php_visibility_not'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Reverse (NOT)'),
      '#default_value' => $not_setting_value,
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['widget_id']  = $form_state->getValue('widget_id');
    $this->configuration['disable_block']  = $form_state->getValue('disable_block');
    $this->configuration['dont_use_bottom_container']  = $form_state->getValue('dont_use_bottom_container');
    $this->configuration['visibility_setting']  = $form_state->getValue('visibility_setting');
    $this->configuration['paths']  = $form_state->getValue('paths');
    $this->configuration['not']  = $form_state->getValue('not');
    $this->configuration['visibility_setting_2nd']  = $form_state->getValue('visibility_setting_2nd');
    $this->configuration['paths_2nd']  = $form_state->getValue('paths_2nd');
    $this->configuration['not_2nd']  = $form_state->getValue('not_2nd');
    $this->configuration['php_visibility']  = $form_state->getValue('php_visibility');
    $this->configuration['php_visibility_not']  = $form_state->getValue('php_visibility_not');
  }

  public function checkVisibility() {
    $first_visible = true;
    $second_visible = true;
    $php_visible = true;
    //check first path visibility rule
    if (isset($this->configuration['paths']) && !empty($this->configuration['paths'])) {
      $first_visible = $this->checkVisbibilityPath($this->configuration['paths'] );
      if (isset($this->configuration['visibility_setting'])) {
        $first_visible = ($this->configuration['visibility_setting']) ? $first_visible : !$first_visible;
      }

      if (isset($this->configuration['not'])) {
        $first_visible = ($this->configuration['not']) ? !$first_visible : $first_visible;
      }
    }

    //check second path visibility rule
    if (isset($this->configuration['paths_2nd']) && !empty($this->configuration['paths_2nd'])) {
      $second_visible = $this->checkVisbibilityPath($this->configuration['paths_2nd'] );
      if (isset($this->configuration['visibility_setting_2nd'])) {
        $second_visible = ($this->configuration['visibility_setting_2nd']) ? $second_visible : !$second_visible;
      }

      if (isset($this->configuration['not_2nd'])) {
        $second_visible = ($this->configuration['not_2nd']) ? !$second_visible : $second_visible;
      }
    }

    //check PHP visibility rule
    if (isset($this->configuration['php_visibility']) && !empty($this->configuration['php_visibility'])) {
      $php_visible = $this->checkVisibilityPhp($this->configuration['php_visibility'] );

      if (isset($this->configuration['php_visibility_not'])) {
        $php_visible = ($this->configuration['php_visibility_not']) ? !$php_visible : $php_visible;
      }
    }

    return $first_visible && $second_visible && $php_visible;
  }

  public function checkVisbibilityPath($paths) {
    if (isset($paths) && !empty($paths)) {
      //split the values of paths by endline
      $path_values = preg_split('/\r\n|\r|\n/', $paths);

      //get current path, this block will check parameters from BottomContainerURL if that exists
      //otherwise from current path
      $dont_use_bottom_container = (isset($this->configuration['dont_use_bottom_container'])) ? $this->configuration['dont_use_bottom_container'] : '0';
      if ((!($dont_use_bottom_container)) && RequestUtilities::getBottomContUrl()) {
        $current_path = RequestUtilities::getBottomContUrl();
      } else {
        $current_path = \Drupal::service('path.current')->getPath();
      }
      $current_path = str_replace(':','/', $current_path);

      $path_matches = false;
      foreach($path_values as $path_value) {
        if (preg_match($path_value, $current_path)) {
          $path_matches = true;
          break;
        }
      }

      return $path_matches;
    } else {
      return true;
    }
  }

  public function blockValidate($form, FormStateInterface $form_state) {
    $php_value = $form_state->getValue('php_visibility');
    if (!empty($php_value)) {
      try{
        $result = eval($php_value);
      } catch (ParseError $e) {
        $form_state->setErrorByName('php_visibility', $this->t('Error in PHP Visibility code'));
      }
    }
    return $form_state;
  }

  public function checkVisibilityPhp($php_code) {
    if (isset($php_code) && !empty($php_code)) {
      try {
        $result = eval($php_code);
        return $result;
      } catch (ParseError $e) {
        LogHelper::log_error("Error while doing checkVisibilityPhp for WidgetFilterBlock: " . $e);
        return false;
      }
    } else {
      return true;
    }
  }
}
