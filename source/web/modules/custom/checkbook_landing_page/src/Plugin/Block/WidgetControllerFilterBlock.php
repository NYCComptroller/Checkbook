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

namespace Drupal\checkbook_landing_page\Plugin\Block;

use Drupal\checkbook_log\LogHelper;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use ParseError;

/**
 * Provides a 'Widget Controller Filter' Block.
 *
 * @Block(
 *   id = "widget_controller_filter_block",
 *   admin_label = @Translation("Widget Controller Filter Block"),
 *   category = @Translation("Custom"),
 * )
 */
class WidgetControllerFilterBlock extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
    $expandBottomContURL = \Drupal::request()->get('expandBottomContURL');
    $current_route = \Drupal::routeMatch()->getRouteName();
    if(!isset($expandBottomContURL)) {
      $widget_id = $this->configuration['widget_id'] ?? NULL;
      $php_setting = $this->configuration['php_setting'] ?? NULL;
      if (isset($widget_id) && !empty($widget_id)) {
        $result = _widget_controller_node_view($widget_id);
      } else if (isset($php_setting) && !empty($php_setting)) {
        $result = _widget_controller_node_view($this->runWidgetControllerWidgetPhp($php_setting));
      }
      if (isset($result) && !empty($result)) {
        return [
          '#markup' => $result,
          '#cache' => ['contexts' => ['url.path', 'url.query_args']]
          ];
      } else if (str_contains($current_route, 'layout_builder.')) {
        return [
          '#markup' => "Widget Controller Block for <br> $widget_id $php_setting",
        ];
      } else {
        return [
          '#markup' => "",
        ];
      }
    } else if (str_contains($current_route, 'layout_builder.')) {
      return [
        '#markup' => "expandBottomContURL is set, so no display",
      ];
    } else {
      return [
        '#markup' => "",
      ];
    }
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
      '#title' => $this->t('Widget Controller ID'),
      '#description' => $this->t("Id of widget to load"),
      '#maxlength' => 100,
      '#size' => 100,
      '#default_value' => $this->configuration['widget_id'] ?? '',
    );
    $form['php_setting'] = array(
      '#type' => 'textarea',
      '#title' => $this->t('PHP setting'),
      '#default_value' => $this->configuration['php_setting'],
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['widget_id']  = $form_state->getValue('widget_id');
    $this->configuration['php_setting']  = $form_state->getValue('php_setting');
  }

  /**
   * @param $php_code
   * @return bool|mixed
   */
  public function runWidgetControllerWidgetPhp($php_code) {
    if (isset($php_code) && !empty($php_code)) {
      try {
        return eval($php_code);
      } catch (ParseError $e) {
        LogHelper::log_error("Error while running WidgetControllerWidgetPHP code in : " . $e);
        return false;
      }
    } else {
      return true;
    }
  }
}
