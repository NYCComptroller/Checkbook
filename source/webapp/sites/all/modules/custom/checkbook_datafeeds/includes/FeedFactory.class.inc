<?php
/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (C) 2019 New York City
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

namespace checkbook_datafeeds;


/**
 * Class FeedFactory
 * @package checkbook_datafeeds
 */
class FeedFactory
{
  private static $datasource_map = [
    \Datasource::CITYWIDE => 'Citywide',
    \Datasource::OGE => 'Nycedc',
    \Datasource::NYCHA => 'Nycha'
  ];

  /**
   * @param $data_source
   * @param $domain
   * @return SpendingFeed|bool
   */
  public static function getFeed($data_source, $domain)
  {
    $ds = self::$datasource_map[$data_source];
    $class = 'checkbook_datafeeds\\' . ucfirst($domain) . 'Feed' . $ds;
    spl_autoload_call($class);
    if (!class_exists($class)) {
      \LogHelper::log_error("DataFeeds class not found: '{$class}' ");
      return FALSE;
    }
    return new $class();
  }
}
