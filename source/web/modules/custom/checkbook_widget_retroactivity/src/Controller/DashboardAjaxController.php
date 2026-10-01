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

namespace Drupal\checkbook_widget_retroactivity\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Symfony\Component\HttpFoundation\Request;

class DashboardAjaxController extends ControllerBase {


  /**
   * Returns the dynamic headings for a specific tab.
   */
  public function loadTabHeader($tab_id) {
    $config = $this->getDashboardConfig();
    $tab_data = $config['tabs'][$tab_id] ?? NULL;

    if (!$tab_data) {
      return new AjaxResponse(); // Return empty if tab doesn't exist
    }

    // Build the heading HTML
    $build = [
      '#theme' => 'retroactivity_tab_header', // You'll need to define this in hook_theme
      '#main_title' => $this->t('RETROACTIVITY DASHBOARD'),
      '#sub_title' => $this->t($tab_data['label']),
    ];

    $response = new AjaxResponse();
    $response->addCommand(new HtmlCommand('.dashboard-headings-container', $build));
    return $response;
  }

  /**
   * Returns the tiles block for a specific tab.
   */
  public function loadTiles($tab_id) {
    $response = new AjaxResponse();
    
    $block_manager = \Drupal::service('plugin.manager.block');
    // Pass the tab_id into the block configuration so it knows which tiles to show
    $plugin_block = $block_manager->createInstance('retroactivity_chart_tiles_block', [
      'tab_id' => $tab_id
    ]);
    
    $render_array = $plugin_block->build();
    
    $response->addCommand(new HtmlCommand('.retroactivity-tiles-container', $render_array));
    return $response;
  }


  /**
   * Helper to load the JSON config.
   */
  protected function getDashboardConfig() {
    $module_path = \Drupal::service('extension.list.module')->getPath('checkbook_widget_retroactivity');
    $file_path = $module_path . '/config/dashboard_config.json';
    if (file_exists($file_path)) {
      return json_decode(file_get_contents($file_path), TRUE);
    }
    return [];
  }
}