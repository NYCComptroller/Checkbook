<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


/**
 * Class to load configuration used by API
 */
class ConfigUtil {
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
  static function getDomainConfiguration($domain) {
    $config_str = file_get_contents(realpath(drupal_get_path('module', 'checkbook_api')) . "/config/" . strtolower($domain) . ".json");

    $converter = new Json2PHPObject();
    $configuration = $converter->convert($config_str);

    return $configuration;
  }

  /**
   * Get configuration.
   *
   * @static
   *
   * @param string $domain
   *   domain
   * @param string $config_key
   *   config key
   *
   * @return mixed
   *   config key
   */
  static function getConfiguration($domain, $config_key) {
    $config_str = file_get_contents(realpath(drupal_get_path('module', 'checkbook_api')) . "/config/" . strtolower($domain) . ".json");

    $converter = new Json2PHPObject();
    $configuration = $converter->convert($config_str);

    return $configuration->$config_key;
  }
}
