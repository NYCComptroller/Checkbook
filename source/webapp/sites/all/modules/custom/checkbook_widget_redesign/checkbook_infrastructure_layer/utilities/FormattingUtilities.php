<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class FormattingUtilities{
  /**
   * @param $number
   *
   * @return mixed|string Returns negative number wrapped in parenthesis
   */
  public static function trendsNumberDisplay($number){
    return ($number > 0) ? number_format($number) : "(" . number_format(abs($number)).")";
  }
}

