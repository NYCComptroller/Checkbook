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
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\checkbook_widget_retroactivity\Config\RetroactivityConfigService;

/**
 * Provides a 'Retroactivity Chart Tiles' Block.
 *
 * @Block(
 * id = "retroactivity_chart_tiles_block",
 * admin_label = @Translation("Retroactivity Chart Tiles"),
 * )
 */
class RetroactivityChartTilesBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The module handler service.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * The config service.
   *
   * @var Drupal\checkbook_widget_retroactivity\Config\RetroactivityConfigService
   */
  protected $configService;

  /**
   * Constructs a new RetroactivityTabsBlock instance.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ModuleHandlerInterface $module_handler, RetroactivityConfigService $config_service) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->moduleHandler = $module_handler;
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
      $container->get('checkbook_widget_retroactivity.retroactivity_config')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config_data = $this->loadConfig();
    if (empty($config_data['tabs'])) {
      return [];
    }
    // Fetch tab ID from ajax URL.
    $tab_id = \Drupal::routeMatch()->getParameter('tab_id');

    $tiles_to_render = [];

    // Validate that the tab exists in config and has tiles
    if ($tab_id && isset($config_data['tabs'][$tab_id]['tiles'])) {
        $allowed_tiles = $config_data['tabs'][$tab_id]['tiles'];
        $all_chart_definitions = $config_data['chart_tiles'] ?? [];

        foreach ($allowed_tiles as $tile_key) {
        if (isset($all_chart_definitions[$tile_key])) {
            $tile_def = $all_chart_definitions[$tile_key];

            // Filter by 'enabled' status
            if (!empty($tile_def['enabled'])) {
            $tiles_to_render[] = [
                'id' => $tile_key, // The internal key
                'chart_id' => $tile_def['chart_id'], // The ID used for Highcharts AJAX
                'title' => $tile_def['label'],
                'thumbnail' => '/' . $this->moduleHandler->getModule('checkbook_widget_retroactivity')->getPath() . '/' . $tile_def['image'],
                'order' => $tile_def['order'] ?? 0,
                'active' => FALSE, // We will handle 'active' state via JS on click
            ];
            }
        }
        }

        // Sort the tiles by the 'order' value
        usort($tiles_to_render, function ($a, $b) {
        return $a['order'] <=> $b['order'];
        });

        // Set the first tile as active by default for the initial load
        if (!empty($tiles_to_render)) {
        $tiles_to_render[0]['active'] = TRUE;
        }
    }

    return [
        '#theme' => 'retroactivity_chart_tiles',
        '#tiles' => $tiles_to_render,
        '#attached' => [
          'library' => ['checkbook_widget_retroactivity/retroactivity-tiles']
        ],
    ];
  }

  /**
   * Loads the tab configuration from the JSON file.
   */
  protected function loadConfig() {
    return $this->configService->getRetroactivityConfig();
  }

}