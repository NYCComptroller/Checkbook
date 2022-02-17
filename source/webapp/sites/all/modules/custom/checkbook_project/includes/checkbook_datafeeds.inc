<?php
/**
 * Function to get connection to data feeds DB
 *
 * @param string $db_name
 * @param string $data_source
 * @return DatabaseConnection|void DB connection
 */
function get_datafeed_connection($db_name = "datafeed", $data_source = Datasource::CITYWIDE){
  try {
    return Database::getConnection($db_name, $data_source);
  } catch (Exception $e) {
    LogHelper::log_error("Exception getting connection for datafeed. Exception is :" . $e);
    return;
  }
}

/**
 * Function to generate the path to save the file for data feeds
 * @return string
 * @throws Exception
 */
function _checkbook_project_prepare_data_feeds_file_output_dir(){
  global $conf;

  $dir = variable_get('file_public_path', 'sites/default/files') . '/' . $conf['check_book']['data_feeds']['output_file_dir'];
  _checkbook_project_prepare_data_feeds_dir($dir);

  $dir .= '/' . $conf['check_book']['export_data_dir'];
  _checkbook_project_prepare_data_feeds_dir($dir);

  try {
    //delete files older than 2 hours
    _checkbook_project_clean_files($dir);
  } catch (Exception $e) {
    LogHelper::log_error($e);
  }
  return $dir;
}

/**
 * Function to generate the path to save the file for data feeds
 * @param $dir
 * @throws Exception
 */
function _checkbook_project_prepare_data_feeds_dir($dir){
  if (!file_prepare_directory($dir, FILE_CREATE_DIRECTORY)) {
    LogHelper::log_error("Could not prepare file output directory $dir.Should check if this directory is writable.");
    throw new Exception("Could not prepare file. Please contact Support team.");
  }
}

/**
 *
 */
function _checkbook_project_clean_files(){
  $dir = _checkbook_export_prepareFileOutputDir();
  file_scan_directory($dir, '/.*/', array('callback' => '_checkbook_project_delete_file_if_stale'));
}

/**
 * Callback to delete files modified more than a set time ago.
 *
 * @param $path
 */
function _checkbook_project_delete_file_if_stale($path){
  $timeDiff = intval(floor((REQUEST_TIME - filemtime($path)) / (3600)));
  if ($timeDiff > 2) {//More than 2 hours old
    file_unmanaged_delete($path);
  }
}
