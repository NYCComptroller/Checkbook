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

class RevenueSmartUtil {

  public static function displayRevenueResult($revenue_results, $solr_datasource) {

    $revenue_parameter_mapping = CheckbookSolr::getSearchFields($solr_datasource, 'revenue');
    $current_year_id = CheckbookDateUtil::getCurrentFiscalYearId();

    // Safely resolve reused fields once
    $agency_id   = $revenue_results['agency_id']     ?? '';
    $fiscal_year = $revenue_results['fiscal_year'][0] ?? 0;

    // Disable links for old fiscal years
    $linkable_fields = [];
    if ($fiscal_year >= 2010) {
      $linkable_fields = [
        'agency_name' => '/revenue/year/' . $current_year_id . '/agency/' . $agency_id,
      ];
    }
    $amount_fields = [
      "adopted_amount",
      "current_budget_amount",
      "posting_amount"
    ];

    // --- Build structured fields array ---
    $fields = [];
    foreach ($revenue_parameter_mapping as $key => $title) {
      if (!$title) {
        continue;
      }

      // Safely access sparse Solr fields
      $value = ($key === 'fiscal_year')
        ? ($revenue_results[$key][0] ?? '')
        : ($revenue_results[$key] ?? '');

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
      'domain' => 'revenue',
      'fields' => $fields,
    ];
  }
}
