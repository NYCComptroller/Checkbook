<?php
/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (C) 2012, 2013 New York City
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


class NychaSpendingUtil
{
  static $widget_titles = array('checks' => 'Checks', 'ytd_check' => 'Check','vendors' => 'Vendors', 'ytd_vendor' => 'Vendor',
    'contracts' => 'Contracts', 'ytd_contract' => 'Contract', 'expense_categories' => 'Expense Categories',
    'ytd_expense_category' => 'Expense Category', 'industries' => 'Industries', 'ytd_industry' => 'Industry',
    'funding_sources' => 'Funding Sources', 'ytd_funding_source' => 'Funding Source', 'departments' => 'Departments',
    'ytd_department' => 'Department');

  static public function getTransactionsTitle(){
    $categories = array(3 => 'Contracts', 2 => 'Payroll', 1 => 'Section 8', 4 => 'Others', null => 'Total');
    $url = $_SERVER['HTTP_REFERER'];
    $widget = RequestUtil::getRequestKeyValueFromURL("widget", $url);
    $widget_titles = self::$widget_titles;
    $title = isset($widget) ? $widget_titles[$widget]: "";
    $title = $title .' '.$categories[ RequestUtilities::get('category')]. " Spending Transactions";
    return $title ;
  }
}
