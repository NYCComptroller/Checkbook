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

namespace Drupal\checkbook_widget_retroactivity\Config;

use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Service to handle dashboard configuration loading.
 */
class RetroactivityConfigService {

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * Constructs a RetroactivityConfigService object.
   */
  public function __construct(ModuleHandlerInterface $module_handler) {
    $this->moduleHandler = $module_handler;
  }

  /**
   * Returns the dashboard configuration as an array.
   */
  public function getRetroactivityConfig() {
    $module_path = $this->moduleHandler->getModule('checkbook_widget_retroactivity')->getPath();
    $file_path = $module_path . '/src/Config/retroactivity-dashboard-mapping.json';

    if (file_exists($file_path)) {
      $json_string = file_get_contents($file_path);
      return json_decode($json_string, TRUE);
    }

    return NULL;
  }

  /**
   * Returns a configuration value from config.
   * @param string $item
   */
  public function getConfigVariable($item) {
    $config = $this->getRetroactivityConfig();

    return isset($config[$item]) ? $config[$item] : NULL; 
  }

}