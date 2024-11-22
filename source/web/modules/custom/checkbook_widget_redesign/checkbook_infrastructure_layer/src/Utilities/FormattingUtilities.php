<?php

namespace Drupal\checkbook_infrastructure_layer\Utilities;

use DateTime;

class FormattingUtilities
{
  /**
   * If the length of the text passed exceeds the specified length,
   * replaces the remaining characters with "..." and add a tooltip
   * @param $text
   * @param int $length
   * @return string
   */
  public static function _shorten_word_with_tooltip($text, $length = 20)
  {
    if (strlen($text) > $length) {
      return "<span title='" . htmlentities($text, ENT_QUOTES) . "'>" . substr(htmlentities($text), 0, $length - 3) . "...</span>";
    }
    return $text;
  }

  /**
   * return replacment patter for title ...
   * @param string $text
   * @param integer $length
   * @param int $no_of_lines
   * @return string
   */
  public static function _get_tooltip_markup($text, $length = 20, $no_of_lines = 2)
  {
    return self::_break_text_custom(html_entity_decode($text, ENT_QUOTES), $length, $no_of_lines);
  }

  /**
   * return replacment patter for title ...
   * @param string $text
   * @param integer $length
   * @param int $no_of_lines
   * @return string
   */
  public static function _break_text_custom($text, $length = 20, $no_of_lines = 2)
  {
    $text_array = explode(" ", $text);
    $offset = 0;
    $remaining_original = $remaining = ($length % 2 == 0) ? ($length / $no_of_lines) : (round($length / $no_of_lines) - 1);
    $first_line = true;

    $index = 0;

    if (strlen($text_array[0]) >= 2 * $remaining_original) {
      return "<span title='" . htmlentities($text, ENT_QUOTES) . "'>" . substr(htmlentities($text), 0, $length - 3) . "...</span>";
    } else {
      foreach ($text_array as $key => $value) {
        if (isset($first_line) && $first_line) {
          if (strlen($value) >= $remaining && $index == 0) {
            $first_line = false;
            $second_line = true;
            $remaining = 2 * $remaining_original - strlen($value) - 1;
            $offset = strlen($value) + 1;
          } else {
            $remaining = $remaining - (strlen($value) + 1);
            if ($remaining <= 0) {
              $first_line = false;
              $second_line = true;
              $remaining = $remaining_original;
            } else {
              $offset += strlen($value) + 1;
            }
          }
        }
        if (isset($second_line) && $second_line) {
          $prev_remaining = $remaining;
          $remaining = $remaining - (strlen($value) + 1);
          if ($remaining >= 0) {
            $offset = $offset + strlen($value) + 1;
          } else {
            $offset = $offset + ($prev_remaining - 3);
            $second_line = false;
          }
        }
        $index += 1;
      }

      if ($offset < strlen($text))
        return "<span title='" . htmlentities($text, ENT_QUOTES) . "'>" . htmlentities(substr($text, 0, $offset)) . "...</span>";
      else
        return "<span title='" . htmlentities($text, ENT_QUOTES) . "'>" . htmlentities(substr($text, 0, $offset)) . "</span>";
    }
  }

  /**
   * return replacment patter for title ...
   * @param string $text
   * @param integer $length
   * @return string
   */
  public static function _break_text_custom2($text, $length = 18)
  {
    $text = html_entity_decode($text, ENT_QUOTES);
    $text_array = explode(" ", $text);
    $breaked_text = "";
    foreach ($text_array as $key => $value) {
      if (strlen($value) >= $length)
        $value = implode(" ", str_split($value, $length));
      $breaked_text = $breaked_text . " " . $value;
    }
    return htmlentities($breaked_text);
  }

  /**
   * @param string $text
   * @param int $len
   * @return string
   */
  public static function _ckbk_excerpt($text = '', $len = 20): string
  {
    if (!$text) {
      return '';
    }
    return (strlen($text) > $len) ? substr($text, 0, $len) . '...' : $text;
  }

  /**
   * formats the number for display purposes, eg:1000000 will be displayed as 1M
   * @param int,float $number
   * @param int $decimal_digits
   * @param string $prefix
   * @param string $suffix
   * @return string formattedNumber
   */
  public static function custom_number_formatter_format($number, int $decimal_digits = 0, $prefix = null, string $suffix = '') : string
  {
    $number = (float)$number;
    $thousands = 1000;
    $millions = $thousands * 1000;
    $billions = $millions * 1000;
    $trillions = $billions * 1000;
    $formattedNumber = '';
    if ($number < 0) {
      $formattedNumber = '-';
    }

    if (abs($number) >= $trillions) {
      $formattedNumber = $formattedNumber . $prefix . number_format((abs($number) / $trillions), $decimal_digits, '.', ',') . 'T' . $suffix;
    } else if (abs($number) >= $billions) {
      $formattedNumber = $formattedNumber . $prefix . number_format((abs($number) / $billions), $decimal_digits, '.', ',') . 'B' . $suffix;
    } else if (abs($number) >= $millions) {
      $formattedNumber = $formattedNumber . $prefix . number_format((abs($number) / $millions), $decimal_digits, '.', ',') . 'M' . $suffix;
    } else if (abs($number) >= $thousands) {
      $formattedNumber = $formattedNumber . $prefix . number_format((abs($number) / $thousands), $decimal_digits, '.', ',') . 'K' . $suffix;
    } else {
      $formattedNumber = $formattedNumber . $prefix . number_format(abs($number), $decimal_digits, '.', ',') . $suffix;
    }
    return $formattedNumber;
  }

  /**
   * formats the date for display purposes, eg:2016-01-08 will be displayed as 01/08/2016
   * @param string $date
   * @return string formattedDate
   * @throws \Exception
   */
  public static function custom_date_format(string $date): string
  {
    $raw_date = new DateTime($date);
    return $raw_date->format('m/d/Y');
  }

  /**
   * formats the number for display purposes, eg:1000000 will be displayed as 1M
   * @param int,float $number
   * @param int $decimal_digits
   * @param string $prefix
   * @return string formattedNumber
   */
  public static function custom_number_formatter_basic_format($number, int $decimal_digits = 2, string $prefix = '$')
  {
    if ($number == null) {
      $number = 0;
    }
    if (!is_numeric($number)) {
      return $number;
    }

    $formattedNumber = NULL;
    if ($number < 0) {
      $formattedNumber = '-';
    }
    $formattedNumber .= $prefix . number_format(abs($number), $decimal_digits);
    return $formattedNumber;
  }

  /**
   * @param $string
   * @return false|string
   */
  public static function format_string_to_date($string){

    if ($string == null or trim($string) == '')
      return "";

    return date("m/d/Y", strtotime($string));
  }

  /**
   * @param $string
   * @return int
   */
  public static function _get_num_from_string($string){
    return (int)preg_replace('/[^\-\d]*(\-?\d*).*/', '$1', $string);
  }

  /**
   * @param $string
   *
   * @return mixed
   */
  public static function _checkbook_regex_replace_pattern($string){
    $search = [
      '.',
      '^',
      '$',
      '*',
      '+',
      '(',
      ')',
      '[',
      ']',
      '{',
      '}',
    ];
    $replace = [
      '\.',
      '\^',
      '\$',
      '\*',
      '\+',
      '\(',
      '\)',
      '\[',
      '\]',
      '\{',
      '\}',
    ];
    $string = str_replace($search, $replace, $string);
    return $string;
  }

  /**
   * Strips a value from brackets using RegEx else returns zero.
   * @param string $input
   * @return int|string
   */
  public static function emptyToZero($input){
    if (is_numeric($input)) {
      return $input;
    }
    $p = "/.*?(\\[.*?\\])/is";
    $matches = array();
    if ($input) {
      preg_match($p, $input, $matches);
      $output = trim($matches[1] ?? '', '[ ]');
    } else {
      $output = 0;
    }
    return $output;
  }

  /**
   * @param mixed $string
   * @return mixed
   */
  public static function checkbook_replaceSlash($string)
  {
    return str_replace('/', '__', $string);
  }

  /**
   * @param $number
   * @param $decimals
   *
   * @return mixed|string Returns negative number wrapped in parenthesis
   */
  public static function trendsNumberDisplay($number, $decimals = 0, $prefix = '', $suffix = ''){
    return $number >= 0 ? $prefix . number_format($number, $decimals) . $suffix : "(" . $prefix . number_format(abs($number), $decimals) . $suffix . ")";
  }

}
