<?php
namespace Drupal\checkbook_api\config;

use Drupal\checkbook_log\LogHelper;
use Drupal\data_controller\Common\Object\Converter\Handler\Json2PHPObject;

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
  static public function getDomainConfiguration($domain) {
    $config_path = realpath(\Drupal::service('extension.list.module')->getPath('checkbook_api')) . "/src/config/" . strtolower($domain) . ".json";

    LogHelper::log_info("Loading config from {$config_path}");
    $config_str = file_get_contents($config_path);
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
  static public function getConfiguration($domain, $config_key) {
    $config_str = file_get_contents(realpath(\Drupal::service('extension.list.module')->getPath('checkbook_api')) . "/src/config/" . strtolower($domain) . ".json");

    $converter = new Json2PHPObject();
    $configuration = $converter->convert($config_str);

    return $configuration->$config_key;
  }
}
