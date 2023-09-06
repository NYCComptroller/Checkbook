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

namespace Drupal\checkbook_datafeeds;

use Drupal\checkbook_infrastructure_layer\Constants\Common\CheckbookDomain;
use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;
use Drupal\data_controller\Common\Object\Converter\Handler\Json2PHPArray;

/**
 * Class to load configuration used by datafeeds
 */
class DatafeedsConfigUtil{
  public static function dataSourceRadio($data_source, $domain){
    $options = [
      Datasource::CITYWIDE => 'Citywide Agencies',
      Datasource::OGE => 'New York City Economic Development Corporation',
      Datasource::NYCHA => 'New York City Housing Authority'
    ];
    if (CheckbookDomain::$PAYROLL == $domain || CheckbookDomain::$BUDGET == $domain || CheckbookDomain::$REVENUE == $domain) {
      unset($options['checkbook_oge']);
    }
    return [
      '#type' => 'radios',
      '#title' => 'Data source',
      '#options' => $options,
      '#default_value' => !isset($data_source) ? Datasource::CITYWIDE : $data_source,
      '#prefix' => '<div id="div_data_source" class="clearfix">',
      '#suffix' => '</div>',
    ];
  }

  /**
   * Get domain configuration.
   *
   * @static
   *
   * @param string $domain
   *   domain
   *
   * @return mixed
   *   configuration
   */
  static function getConfig($domain){
    $checkbook_datafeeds_path = \Drupal::service('extension.list.module')->getPath('checkbook_datafeeds');
    $config_str = file_get_contents(realpath($checkbook_datafeeds_path) . "/config/checkbook_datafeeds_" . strtolower($domain) . "_column_options.json");
    $converter = new Json2PHPArray();
    $configuration = $converter->convert($config_str);
    return $configuration;
  }
}
