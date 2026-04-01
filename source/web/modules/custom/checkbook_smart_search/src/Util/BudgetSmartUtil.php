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

    // Safely resolve reused fields once
    $agency_id       = $budget_results['agency_id']      ?? '';
    $object_class_id = $budget_results['object_class_id'] ?? '';
    $fiscal_year     = $budget_results['fiscal_year'][0]  ?? 0;

    if ($fiscal_year >= 2010) {
      $linkable_fields = [
        'agency_name'           => '/budget/year/' . $current_year_id . '/yeartype/B/agency/'      . $agency_id,
        'expense_category_name' => '/budget/year/' . $current_year_id . '/yeartype/B/expcategory/' . $object_class_id,
      ];
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

    // --- Build structured fields array ---
    $fields = [];
    foreach ($budget_parameter_mapping as $key => $title) {
      if (!$title) {
        continue;
      }

      // Safely access sparse Solr fields with ?? ''
      if ($key === 'expenditure_object_name' || $key === 'fiscal_year') {
        $value = $budget_results[$key][0] ?? '';
      }
      else {
        $value = $budget_results[$key] ?? '';
      }

      if (is_array($value)) {
        $value = implode(', ', $value);
      }

      // Format amounts or resolve link
      $value_link = NULL;
      if (in_array($key, $amount_fields)) {
        $value = FormattingUtilities::custom_number_formatter_format($value, 2, '$');
      }
      elseif (array_key_exists($key, $linkable_fields)) {
        $value_link = $linkable_fields[$key];
      }

      $fields[] = [
        'key'           => $key,
        'title'         => $title,
        'title_link'    => NULL,
        'value'         => $value,
        'value_link'    => $value_link,
        'is_new_window' => FALSE,
        'minority_link' => NULL,
      ];
    }

    return [
      'domain' => 'budget',
      'fields' => $fields,
    ];
  }

}
