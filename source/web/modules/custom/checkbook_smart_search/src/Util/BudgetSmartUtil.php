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


namespace Drupal\checkbook_smart_search\Util;

use Drupal\checkbook_infrastructure_layer\Utilities\FormattingUtilities;
use Drupal\checkbook_project\CommonUtilities\CheckbookDateUtil;
use Drupal\checkbook_solr\CheckbookSolr;

class BudgetSmartUtil {

  public static function displayBudgetResult($budget_results, $solr_datasource) {
    $budget_parameter_mapping = CheckbookSolr::getSearchFields($solr_datasource, 'budget');

    $current_year_id = CheckbookDateUtil::getCurrentFiscalYearId();
    $linkable_fields = [
      "agency_name" => "/budget/year/" . $current_year_id . "/yeartype/B/agency/" . $budget_results["agency_id"],
      "expense_category_name" => "/budget/year/" . $current_year_id . "/yeartype/B/expcategory/" . $budget_results["object_class_id"],
    ];
    if ($budget_results['fiscal_year'][0] < 2010) {

      $linkable_fields = [];

    }

    $amount_fields = [
      "adopted_amount",
      "current_budget_amount",
      "total_expenditure",
      "pre_encumbered_amount",
      "encumbered_amount",
      "accrued_expense_amount",
      "cash_expense_amount",
      "post_closing_adjustment_amount",
      "committed"
    ];

    $count = 1;
    $row = [];
    $rows = [];
    $out = "<div class=\"search-result-fields\"><div class=\"grid-row\">";
    foreach ($budget_parameter_mapping as $key => $title) {
      $value = $budget_results[$key];
      if ($key == 'expenditure_object_name') {
        $value = $budget_results[$key][0];
      }
      if ($key == 'fiscal_year') {
        $value = $budget_results[$key][0];
      }

      if ($searchTerm) {
        $temp = substr($value, strpos(strtoupper($value), strtoupper($searchTerm)), strlen($searchTerm));
        $value = str_ireplace($searchTerm, '<em>' . $temp . '</em>', $value);
      }

      $value = _checkbook_smart_search_str_html_entities($value);

      if (in_array($key, $amount_fields)) {
        $value = FormattingUtilities::custom_number_formatter_format($value, 2, '$');
      }
      else {
        if (array_key_exists($key, $linkable_fields)) {
          $value = "<a href='" . $linkable_fields[$key] . "'>" . $value . "</a>";
        }
      }
      if ($count % 2 == 0) {
        $out .= "<div class=\"grid-col-6\">";
        if ($title) {
          $out .= '<div class="field-label">' . $title . ':</div><div class="field-content">' . $value . '</div>';
        }
        $rows[] = $row;
        $row = [];
      }
      else {
        $out .= "<div class=\"grid-col-6\">";
        if ($title) {
          $out .= '<div class="field-label">' . $title . ':</div><div class="field-content">' . $value . '</div>';
        }
      }
      $count++;
      $out .= "</div>";
    }
    $out .= "</div></div>";
    return $out;
  }

}
