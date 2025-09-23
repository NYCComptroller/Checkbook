<?php
namespace Drupal\checkbook_api\Utilities;

class APIUtil
{
  /**
   * Function to generate a unique id
   * @return string
   */
  public static function _checkbook_project_generate_uuid()
  {
    $guid = 'export';
    $uid = uniqid("", true);
    $data = '';
    $data .= $_SERVER['REQUEST_TIME'];
    $data .= $_SERVER['HTTP_USER_AGENT'];
    $data .= $_SERVER['LOCAL_ADDR'];
    $data .= $_SERVER['LOCAL_PORT'];
    $data .= $_SERVER['REMOTE_ADDR'];
    $data .= $_SERVER['REMOTE_PORT'];
    $hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));
    $guid =
      substr($hash, 0, 8) .
      '-' .
      substr($hash, 8, 4) .
      '-' .
      substr($hash, 12, 4) .
      '-' .
      substr($hash, 16, 4) .
      '-' .
      substr($hash, 20, 12);

    return strtolower($guid);
  }

  public static function prependToFile($path, $text_to_prepend) {
    set_time_limit(0);

    if (is_file($path) === true)
    {
      $file = fopen($path, 'r');
      $temp = tempnam('./', 'tmp');

      if (is_resource($file) === true)
      {
        file_put_contents($temp, $text_to_prepend, FILE_APPEND);
        while (feof($file) === false)
        {
          file_put_contents($temp, fgets($file), FILE_APPEND);
        }

        fclose($file);
        unlink($path);
        rename($temp, $path);
      } else {
        fclose($file);
      }
    }
  }

  public static function appendToFile($path, $text_to_append) {
    set_time_limit(0);

    if (is_file($path) === true)
    {
      $file = fopen($path, 'r');
      $temp = tempnam('./', 'tmp');

      if (is_resource($file) === true)
      {
        while (feof($file) === false)
        {
          file_put_contents($temp, fgets($file), FILE_APPEND);
        }
        file_put_contents($temp, $text_to_append, FILE_APPEND);

        fclose($file);
        unlink($path);
        rename($temp, $path);
      } else {
        fclose($file);
      }
    }
  }

  public static function replaceInFile($path, $replaceText, $replaceWith) {
    set_time_limit(0);

    if (is_file($path) === true)
    {
      $file = fopen($path, 'r');
      $temp = tempnam('./', 'tmp');

      if (is_resource($file) === true)
      {
        while (feof($file) === false)
        {
          file_put_contents($temp, str_replace($replaceText, $replaceWith, fgets($file)), FILE_APPEND);
        }

        fclose($file);
        unlink($path);
        rename($temp, $path);
      } else {
        fclose($file);
      }
    }
  }
}
