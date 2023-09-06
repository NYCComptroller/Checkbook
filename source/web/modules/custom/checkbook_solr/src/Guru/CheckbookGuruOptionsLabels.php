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

namespace Drupal\checkbook_solr\Guru;

/**
 * Class CheckbookGuruOptionsLabels
 */
class CheckbookGuruOptionsLabels extends CheckbookGuru
{
  /**
   * @param string $facet
   * @return array
   */
  public function get_all(string $facet)
  {
    $out = [];
    $facet_results = parent::get_all($facet);
    if(!$facet_results) {
      return $out;
    }
    foreach($facet_results as $facet_value){
      $facet_value = ucwords($facet_value);

      if (strpos($facet_value,']') && ']' == substr($facet_value,-1)) {
        $idx = strrpos($facet_value,'[');
        $title = trim(substr($facet_value,0,$idx));
        $label = _ckbk_excerpt($title);
        $id = substr($facet_value,$idx+1,-1);
        $out[] = [
          'label' => $label,
          'value' => $facet_value,
          'code' => $id,
        ];
      } else {
        $out[] = $facet_value;
      }
    }
    return $out;
  }
}
