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

/**
 * Class CheckbookGuruOptionsAttributes
 */
class CheckbookGuruOptionsAttributes extends CheckbookGuru
{
  /**
   * @param string $facet
   * @return array
   */
  public function get_all(string $facet)
  {
    $out = [];
    $attributes = [];
    $facet_results = parent::get_all($facet);
    if(!$facet_results) {
      return $out;
    }
    foreach($facet_results as $facet_value){
      $facet_value = ucwords($facet_value);

      if (strpos($facet_value,']') && ']' == substr($facet_value,-1)) {
        //$idx = strrpos($facet_value,'[');
        //$title = trim(substr($facet_value,0,$idx));
        $title = $facet_value;
        $label = _ckbk_excerpt($title);
        $out[$facet_value] = $label;
        $attributes[$facet_value] = ['title' => $title];
      } else {
        $out[$facet_value] = $facet_value;
        $attributes[$facet_value] = $facet_value;
      }
    }
    return [
      'options' => $out,
      'options_attributes' => $attributes
    ];
  }
}
