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

namespace Drupal\checkbook_widget_retroactivity\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\checkbook_widget_retroactivity\Config\RetroactivityConfigService;

/**
 * Provides a 'Retroactivity Dashboard Tabs' Block.
 *
 * @Block(
 * id = "retroactivity_tabs_block",
 * admin_label = @Translation("Retroactivity Dashboard Tabs"),
 * )
 */
class RetroactivityTabsBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The module handler service.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */

  protected $moduleHandler;
  /**
   * The route match service.
   *
   * @var \Drupal\Core\Extension\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * The config service.
   *
   * @var Drupal\checkbook_widget_retroactivity\Config\RetroactivityConfigService
   */
  protected $configService;

  /**
   * Constructs a new RetroactivityTabsBlock instance.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ModuleHandlerInterface $module_handler, RouteMatchInterface $route_match, RetroactivityConfigService $config_service) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->moduleHandler = $module_handler;
    $this->routeMatch = $route_match;
    $this->configService = $config_service;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('module_handler'),
      $container->get('current_route_match'),
      $container->get('checkbook_widget_retroactivity.retroactivity_config')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $route_name = $this->routeMatch->getRouteName();
    // Do not load tabs for admin layout interface.
    if ($route_name && str_starts_with($route_name, 'layout_builder.')) {
      return [];
    }

    $config_data = $this->loadConfig();
    if (empty($config_data['tabs'])) {
      return [];
    }
    // Filter and Sort Tabs.
    $tabs = array_filter($config_data['tabs'], function ($tab) {
      return !empty($tab['enabled']);
    });

    uasort($tabs, function ($a, $b) {
      return $a['order'] <=> $b['order'];
    });

    $current_path = \Drupal::service('path.current')->getPath();

    return [
      '#theme' => 'retroactivity_tabs',
      '#main_heading' => $this->t('RETROACTIVITY DASHBOARD'),
      // Initially, we'll let the JS handle the active state and sub-headings,
      // but we pass the default active tab (order 1) to the template.
      '#tabs' => $tabs,
      '#active_tab' => 'overview',
      '#attached' => [
        'drupalSettings' => [
          'checkbookRetroactivity' => [
            'config' => $config_data,
          ],
        ],
      ],
    ];
  }

  /**
   * Loads the tab configuration from the config service.
   */
  protected function loadConfig() {
    return $this->configService->getRetroactivityConfig();
  }
}
