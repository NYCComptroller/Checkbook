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

use Drupal\checkbook_solr\CheckbookSolrQuery;

/**
 * Get search term from $_REQUEST.
 *
 * @return string
 *   Search term
 */
function _get_autocomplete_search_term()
{
  $term = trim(\Drupal::request()->query->get('term'));
  if (strlen($term) > 0) {
    $autocomplete_text = trim(htmlspecialchars_decode($term, ENT_QUOTES));
    $autocomplete_terms = explode(' ', $autocomplete_text);
    $autocomplete_text = CheckbookSolrQuery::escape(trim(htmlspecialchars_decode($term), ENT_QUOTES));

    if (count($autocomplete_terms) > 0) {
      $autocomplete_text = "";
      foreach ($autocomplete_terms as $key => $value) {
        if (strlen($value) > 0) {
          $autocomplete_text .= CheckbookSolrQuery::escape($value) . "*%20AND%20";
        }
      }
      $search_term = "(" . substr($autocomplete_text, 0, -9) . ")";
    } else {
      $search_term = $autocomplete_text . "*";
    }

    return $search_term;
  } else {
    return '';
  }
}
