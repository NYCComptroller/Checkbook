<?php
/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (C) 2012, 2013 New York City
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */


/**
 * Class to load configuration used by datafeeds
 */
class DatafeedsConfigUtil
{
  public static function dataSourceRadio($data_source, $domain)
  {
    $options = [
      'checkbook' => 'Citywide Agencies',
      'checkbook_oge' => 'NYCEDC (New York City Economic Development Corporation)',
      'checkbook_nycha' => 'NYCHA (New York City Housing Authority)'
    ];
    if ('payroll' == $domain) {
      unset($options['checkbook_oge']);
    }
    return [
      '#type' => 'radios',
      '#title' => 'Data source',
      '#options' => $options,
      '#default_value' => !isset($data_source) ? 'checkbook' : $data_source,
      '#prefix' => '<div id="div_data_source">',
      '#suffix' => '</div><br/>',
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
  static function getConfig($domain)
  {
    $config_str = file_get_contents(realpath(drupal_get_path('module', 'checkbook_datafeeds')) . "/config/checkbook_datafeeds_" . strtolower($domain) . "_column_options.json");

    $converter = new Json2PHPArray();
    $configuration = $converter->convert($config_str);

    return $configuration;
  }
}
