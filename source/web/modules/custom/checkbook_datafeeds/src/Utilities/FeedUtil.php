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

namespace Drupal\checkbook_datafeeds\Utilities;

use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;
use Drupal\checkbook_log\LogHelper;
use Drupal\Core\Database\Database;
use Drupal\Core\File\FileSystemInterface;
use Exception;

class FeedUtil
{

  /**
   * Function to get connection to data feeds DB
   *
   * @param string $db_name
   * @param string $data_source
   *
   * @return \Drupal\Core\Database\Connection | void DB connection
   */
  public static function get_datafeed_connection(string $db_name = "datafeed", string $data_source = Datasource::CITYWIDE) {
    try {
      return Database::getConnection($db_name, $data_source);
    } catch (Exception $e) {
      LogHelper::log_error("Exception getting connection for datafeed. Exception is :" . $e);
      return;
    }
  }

  /**
   * Function to generate the path to save the file for data feeds
   *
   * @return string
   * @throws Exception
   */
  public static function _checkbook_project_prepare_data_feeds_file_output_dir() {
    $dir = \Drupal::state()->get('file_public_path','sites/default/files') . '/' . \Drupal::config('check_book')->get('data_feeds')['output_file_dir'] ;
    self::_checkbook_project_prepare_data_feeds_dir($dir);

    $dir .= '/' . \Drupal::config('check_book')->get('export_data_dir');
    self::_checkbook_project_prepare_data_feeds_dir($dir);

    try {
      //delete files older than 2 hours
      self::_checkbook_project_clean_files($dir);
    } catch (Exception $e) {
      LogHelper::log_error($e);
    }
    return $dir;
  }

  /**
   * Function to generate the path to save the file for data feeds
   *
   * @param $dir
   *
   * @throws Exception
   */
  public static function _checkbook_project_prepare_data_feeds_dir($dir) {
    if (!\Drupal::service('file_system')->preparedirectory($dir, FileSystemInterface::CREATE_DIRECTORY)) {
      LogHelper::log_error("Could not prepare file output directory $dir.Should check if this directory is writable.");
      throw new Exception("Could not prepare file. Please contact Support team.");
    }
  }

  /**
   *
   */
  public static function _checkbook_project_clean_files() {
    $dir = _checkbook_export_prepareFileOutputDir();
    \Drupal::service('file_system')->scanDirectory($dir, '/.*/', ['callback' => '\Drupal\checkbook_datafeeds\Utilities\FeedUtil::_checkbook_project_delete_file_if_stale']);
  }

  /**
   * Callback to delete files modified more than a set time ago.
   *
   * @param $path
   */
  public static function _checkbook_project_delete_file_if_stale($path) {
    $request_time = \Drupal::time()->getRequestTime();
    $timeDiff = intval(floor(($request_time - filemtime($path)) / (3600)));
    if ($timeDiff > 2) {//More than 2 hours old
      \Drupal::service('file_system')->delete($path);
    }
  }

  /**
   * Gets the created date for records within a domain to display at the top of Data Feeds page.
   *
   * @param string $domain
   *   Domain
   *
   * @return string
   *   Formatted date from domain's created_date column
   */
  public static function getDataFeedsUpdatedDate($domain)
  {
    switch ($domain) {
      case 'spending':
        $query = ('SELECT MAX(COALESCE(created_date)) FROM {disbursement}');
        break;

      case 'contracts':
        $query = ('SELECT MAX(COALESCE(created_date)) FROM {history_agreement}');
        break;

      case 'payroll':
        $query = ('SELECT MAX(COALESCE(created_date)) FROM {payroll}');
        break;
      case 'budget':
        $query = ('SELECT MAX(COALESCE(updated_date, created_date)) FROM {budget}');
        break;
      case 'revenue':
        $query = ('SELECT MAX(COALESCE(last_modified_date)) FROM {revenue_details}');
        break;
    }

    $results = _checkbook_project_execute_sql($query);
    $max = $results[0]['max'];
    $date = date('F j, Y h:ia', strtotime($max));
    return $date;
  }
}
