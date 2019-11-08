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
 * Constructs the URL for Revenue transactions based on the input by the users
 *
 * @param $form
 * @param $form_state
 * @param null $button
 * @return string
 */
function _checkbook_advanced_search_revenue_submit($form, &$form_state, $button = NULL)
{
  $revenue_fund_class = trim($form['revenue']['revenue_fund_class']['#value']);
  $revenue_agency = trim($form['revenue']['revenue_agencies']['#value']);
  $revenue_fiscal_year = trim($form['revenue']['revenue_fiscal_year']['#value']);
  $revenue_budget_fiscal_year = trim($form['revenue']['revenue_budget_fiscal_year']['#value']);
  $revenue_revenue_category = trim($form['revenue']['revenue_revenue_category']['#value']);
  $revenue_revenue_class = trim($form['revenue']['revenue_revenue_class']['#value']);
  $revenue_revenue_class_exact = trim($form['revenue']['revenue_revenue_class_exact']['#value']);
  $revenue_revenue_source = trim($form['revenue']['revenue_revenue_source']['#value']);
  $revenue_revenue_source_exact = trim($form['revenue']['revenue_revenue_source_exact']['#value']);
  $revenue_funding_source = trim($form['revenue']['revenue_funding_source']['#value']);
  $revenue_adopted_from = trim($form['revenue']['revenue_adopted_from']['#value']);
  $revenue_adopted_to = trim($form['revenue']['revenue_adopted_to']['#value']);
  $revenue_recognized_from = trim($form['revenue']['revenue_recognized_from']['#value']);
  $revenue_recognized_to = trim($form['revenue']['revenue_recognized_to']['#value']);
  $revenue_modified_from = trim($form['revenue']['revenue_modified_from']['#value']);
  $revenue_modified_to = trim($form['revenue']['revenue_modified_to']['#value']);

  $redirect_url = "revenue/transactions";

  if (!empty($revenue_agency)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($revenue_agency, 'agency');
  }
  if (!empty($revenue_fiscal_year)) {
    if (strtolower($revenue_fiscal_year) != 'all fiscal years')
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($revenue_fiscal_year, 'fy');
  }
  if (!empty($revenue_budget_fiscal_year)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($revenue_budget_fiscal_year, 'year');
  }
  if (!empty($revenue_fund_class)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($revenue_fund_class, 'fundcls');
  }
  if (!empty($revenue_revenue_category)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($revenue_revenue_category, 'revcat');
  }
  if (!empty($revenue_revenue_class)) {
    if ($revenue_revenue_class == $revenue_revenue_class_exact) {
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($revenue_revenue_class, 'revclsnm_exact');
    } else {
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($revenue_revenue_class, 'revclsnm');
    }
  }

  if (!empty($revenue_revenue_source)) {
    if ($revenue_revenue_source == $revenue_revenue_source_exact) {
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($revenue_revenue_source, 'revsrcnm_exact');
    } else {
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($revenue_revenue_source, 'revsrcnm');
    }
  }
  if (!empty($revenue_funding_source)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($revenue_funding_source, 'fundsrccode');
  }

  //URL for ranges
  $revenue_adopted_range_array = array('from' => $revenue_adopted_from, 'to' => $revenue_adopted_to, 'type' => 'amount');
  $redirect_url .= _checkbook_advanced_search_generate_redirect_url($revenue_adopted_range_array, 'adopted_amt', TRUE);

  $revenue_recognized_range_array = array('from' => $revenue_recognized_from, 'to' => $revenue_recognized_to, 'type' => 'amount');
  $redirect_url .= _checkbook_advanced_search_generate_redirect_url($revenue_recognized_range_array, 'amt', TRUE);

  $revenue_modified_range_array = array('from' => $revenue_modified_from, 'to' => $revenue_modified_to, 'type' => 'amount');
  $redirect_url .= _checkbook_advanced_search_generate_redirect_url($revenue_modified_range_array, 'modified_amt', TRUE);


  return $redirect_url;
}
