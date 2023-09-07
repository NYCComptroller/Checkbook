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

namespace Drupal\checkbook_datafeeds\Payroll;

use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;

class PayrollFeed extends PayrollFeedBase
{
  protected string $data_source = 'citywide';
  protected string $type_of_data = 'Payroll';
  protected $filtered_columns_container = 'column_select_expense';

  public function __construct($data_source = 'citywide')
  {
    $this->data_source = $data_source;
    $this->type_of_data = $this->data_source == Datasource::NYCHA ? 'Payroll_NYCHA' : 'Payroll';
  }

  protected function _process_user_criteria_by_datasource()
  {
    if ($this->data_source == Datasource::NYCHA) {
      $other_government_entity = 'NEW YORK CITY HOUSING AUTHORITY[996]';
      //$other_government_entity = ($values['other_government_entity'] == 'Select One') ? 'All' : $values['other_government_entity'];
      $this->form['filter']['other_government_entity'] = array(
        '#markup' => '<div><strong>Other Government Entities:</strong> ' . $other_government_entity . '</div>',
      );
      $this->user_criteria['Other Government Entity'] = $other_government_entity;
      $this->formatted_search_criteria['Other Government Entities'] = $other_government_entity;
    } else {
      if ($this->form_state->getValue('agency')) {
        $this->form['filter']['agency'] = array(
          '#markup' => '<div><strong>Agency:</strong> ' . $this->form_state->getValue('agency')  . '</div>',
        );
        $this->user_criteria['Agency'] = $this->form_state->getValue('agency');
        $this->formatted_search_criteria['Agency'] = $this->form_state->getValue('agency');
      }
    }

    // PAYROLL STARTS
    if (($this->form_state->getValue('otherfrom') || $this->form_state->getValue('otherfrom') === "0") && ($this->form_state->getValue('otherto') || $this->form_state->getValue('otherto') === "0")) {
      $this->form['filter']['other_pay'] = array('#markup' => '<div><strong>Other Payments:</strong> Greater Than Equal to: $' . $this->form_state->getValue('otherfrom') . ' and Less Than Equal to: $' . $this->form_state->getValue('otherto') . '</div>');
      $this->user_criteria['Other Pay Greater Than'] = $this->form_state->getValue('otherfrom');
      $this->user_criteria['Other Pay Less Than'] = $this->form_state->getValue('otherto');
      $this->formatted_search_criteria['Other Payments'] = 'Greater Than Equal to: $' . $this->form_state->getValue('otherfrom') . ' and Less Than Equal to: $' . $this->form_state->getValue('otherto');
    } elseif (!$this->form_state->getValue('otherfrom') && ($this->form_state->getValue('otherto') || $this->form_state->getValue('otherto') === "0")) {
      $this->form['filter']['other_pay'] = array('#markup' => '<div><strong>Other Payments:</strong> Less Than Equal to: $' . $this->form_state->getValue('otherto') . '</div>');
      $this->user_criteria['Other Pay Less Than'] = $this->form_state->getValue('otherto');
      $this->formatted_search_criteria['Other Payments'] = 'Less Than Equal to: $' . $this->form_state->getValue('otherto');
    } elseif (($this->form_state->getValue('otherfrom') || $this->form_state->getValue('otherfrom') === "0") && !$this->form_state->getValue('otherto')) {
      $this->form['filter']['other_pay'] = array('#markup' => '<div><strong>Other Payments:</strong> Greater Than Equal to: $' . $this->form_state->getValue('otherfrom') . '</div>');
      $this->user_criteria['Other Pay Greater Than'] = $this->form_state->getValue('otherfrom');
      $this->formatted_search_criteria['Other Payments'] = 'Greater Than Equal to: $' . $this->form_state->getValue('otherfrom');
    }
    if (($this->form_state->getValue('grossfrom') || $this->form_state->getValue('grossfrom') === "0") && ($this->form_state->getValue('grossto') || $this->form_state->getValue('grossto') === "0")) {
      $this->form['filter']['gross_pay'] = array('#markup' => '<div><strong>Gross Pay:</strong> Greater Than Equal to: $' . $this->form_state->getValue('grossfrom') . ' and Less Than Equal to: $' . $this->form_state->getValue('grossto') . '</div>');
      $this->user_criteria['Gross Pay Greater Than'] = $this->form_state->getValue('grossfrom');
      $this->user_criteria['Gross Pay Less Than'] = $this->form_state->getValue('grossto');
      $this->formatted_search_criteria['Gross Pay'] = 'Greater Than Equal to: $' . $this->form_state->getValue('grossfrom') . ' and Less Than Equal to: $' . $this->form_state->getValue('grossto');
    } elseif (!$this->form_state->getValue('grossfrom') && ($this->form_state->getValue('grossto') || $this->form_state->getValue('grossto') === "0")) {
      $this->form['filter']['gross_pay'] = array('#markup' => '<div><strong>Gross Pay:</strong> Less Than Equal to: $' . $this->form_state->getValue('grossto') . '</div>');
      $this->user_criteria['Gross Pay Less Than'] = $this->form_state->getValue('grossto');
      $this->formatted_search_criteria['Gross Pay'] = 'Less Than Equal to: $' . $this->form_state->getValue('grossto');
    } elseif (($this->form_state->getValue('grossfrom') || $this->form_state->getValue('grossfrom') === "0") && !$this->form_state->getValue('grossto')) {
      $this->form['filter']['gross_pay'] = array('#markup' => '<div><strong>Gross Pay:</strong> Greater Than Equal to: $' . $this->form_state->getValue('grossfrom') . '</div>');
      $this->user_criteria['Gross Pay Greater Than'] = $this->form_state->getValue('grossfrom');
      $this->formatted_search_criteria['Gross Pay'] = 'Greater Than Equal to: $' . $this->form_state->getValue('grossfrom');
    }
    if (($this->form_state->getValue('totalgrossfrom') || $this->form_state->getValue('totalgrossfrom') === "0") && ($this->form_state->getValue('totalgrossto') || $this->form_state->getValue('totalgrossto') === "0")) {
      $this->form['filter']['total_gross'] = array('#markup' => '<div><strong>Gross Pay YTD:</strong> Greater Than Equal to: $' . $this->form_state->getValue('totalgrossfrom') . ' and Less Than Equal to: $' . $this->form_state->getValue('totalgrossto') . '</div>');
      $this->user_criteria['Gross Pay YTD Greater Than'] = $this->form_state->getValue('totalgrossfrom');
      $this->user_criteria['Gross Pay YTD Less Than'] = $this->form_state->getValue('totalgrossto');
      $this->formatted_search_criteria['Gross Pay YTD'] = 'Greater Than Equal to: $' . $this->form_state->getValue('totalgrossfrom') . ' and Less Than Equal to: $' . $this->form_state->getValue('totalgrossto');
    } elseif (!$this->form_state->getValue('totalgrossfrom') && ($this->form_state->getValue('totalgrossto') || $this->form_state->getValue('totalgrossto') === "0")) {
      $this->form['filter']['total_gross'] = array('#markup' => '<div><strong>Gross Pay YTD:</strong> Less Than Equal to: $' . $this->form_state->getValue('totalgrossto') . '</div>');
      $this->user_criteria['Gross Pay YTD Less Than'] = $this->form_state->getValue('totalgrossto');
      $this->formatted_search_criteria['Gross Pay YTD'] = 'Less Than Equal to: $' . $this->form_state->getValue('totalgrossto');
    } elseif (($this->form_state->getValue('totalgrossfrom') || $this->form_state->getValue('totalgrossfrom') === "0") && !$this->form_state->getValue('totalgrossto')) {
      $this->form['filter']['total_gross'] = array('#markup' => '<div><strong>Gross Pay YTD:</strong> Greater Than Equal to: $' . $this->form_state->getValue('totalgrossfrom') . '</div>');
      $this->user_criteria['Gross Pay YTD Greater Than'] = $this->form_state->getValue('totalgrossfrom');
      $this->formatted_search_criteria['Gross Pay YTD'] = 'Greater Than Equal to: $' . $this->form_state->getValue('totalgrossfrom');
    }
    if ($this->form_state->getValue('salary_type')) {
      $this->form['filter']['salary_type'] = array(
        '#markup' => '<div><strong>Salary Type:</strong> ' . $this->form_state->getValue('salary_type') . '</div>'
      );
      $this->user_criteria['Salary Type'] = $this->form_state->getValue('salary_type');
      $this->formatted_search_criteria['Salary Type'] = $this->form_state->getValue('salary_type');
    }

    if (($this->form_state->getValue('salaryfrom') || $this->form_state->getValue('salaryfrom') === "0") && ($this->form_state->getValue('salaryto') || $this->form_state->getValue('salaryto') === "0")) {
      $this->form['filter']['annual_salary'] = array('#markup' => '<div><strong>Amount:</strong> Greater Than Equal to: $' . $this->form_state->getValue('salaryfrom') . ' and Less Than Equal to: $' . $this->form_state->getValue('salaryto') . '</div>');
      $this->user_criteria['Amount Greater Than'] = $this->form_state->getValue('salaryfrom');
      $this->user_criteria['Amount Less Than'] = $this->form_state->getValue('salaryto');
      $this->formatted_search_criteria['Amount'] = 'Greater Than Equal to: $' . $this->form_state->getValue('salaryfrom') . ' and Less Than Equal to: $' . $this->form_state->getValue('salaryto');
    } elseif (!$this->form_state->getValue('salaryfrom') && ($this->form_state->getValue('salaryto') || $this->form_state->getValue('salaryto') === "0")) {
      $this->form['filter']['annual_salary'] = array('#markup' => '<div><strong>Amount:</strong> Less Than Equal to: $' . $this->form_state->getValue('salaryto') . '</div>');
      $this->user_criteria['Amount Less Than'] = $this->form_state->getValue('salaryto');
      $this->formatted_search_criteria['Amount'] = 'Less Than Equal to: $' . $this->form_state->getValue('salaryto');
    } elseif (($this->form_state->getValue('salaryfrom') || $this->form_state->getValue('salaryfrom') === "0") && !$this->form_state->getValue('salaryto')) {
      $this->form['filter']['annual_salary'] = array('#markup' => '<div><strong>Amount:</strong> Greater Than Equal to: $' . $this->form_state->getValue('salaryfrom') . '</div>');
      $this->user_criteria['Amount Greater Than'] = $this->form_state->getValue('salaryfrom');
      $this->formatted_search_criteria['Amount'] = 'Greater Than Equal to: $' . $this->form_state->getValue('salaryfrom');
    }
    if (($this->form_state->getValue('basepayfrom') || $this->form_state->getValue('basepayfrom') === "0") && ($this->form_state->getValue('basepayto') || $this->form_state->getValue('basepayto') === "0")) {
      $this->form['filter']['base_pay'] = array('#markup' => '<div><strong>Base Pay:</strong> Greater Than Equal to: $' . $this->form_state->getValue('basepayfrom') . ' and Less Than Equal to: $' . $this->form_state->getValue('basepayto') . '</div>');
      $this->user_criteria['Base Pay Greater Than'] = $this->form_state->getValue('basepayfrom');
      $this->user_criteria['Base Pay Less Than'] = $this->form_state->getValue('basepayto');
      $this->formatted_search_criteria['Base Pay'] = 'Greater Than Equal to: $' . $this->form_state->getValue('basepayfrom') . ' and Less Than Equal to: $' . $this->form_state->getValue('basepayto');
    } elseif (!$this->form_state->getValue('basepayfrom') && ($this->form_state->getValue('basepayto') || $this->form_state->getValue('basepayto') === "0")) {
      $this->form['filter']['base_pay'] = array('#markup' => '<div><strong>Base Pay:</strong> Less Than Equal to: $' . $this->form_state->getValue('basepayto') . '</div>');
      $this->user_criteria['Base Pay Less Than'] = $this->form_state->getValue('basepayto');
      $this->formatted_search_criteria['Base Pay'] = 'Less Than Equal to: $' . $this->form_state->getValue('basepayto');
    } elseif (($this->form_state->getValue('basepayfrom') || $this->form_state->getValue('basepayfrom') === "0") && !$this->form_state->getValue('basepayto')) {
      $this->form['filter']['base_pay'] = array('#markup' => '<div><strong>Base Pay:</strong> Greater Than Equal to: $' . $this->form_state->getValue('basepayfrom') . '</div>');
      $this->user_criteria['Base Pay Greater Than'] = $this->form_state->getValue('basepayfrom');
      $this->formatted_search_criteria['Base Pay'] = 'Greater Than Equal to: $' . $this->form_state->getValue('basepayfrom');
    }
    if (($this->form_state->getValue('overtimefrom') || $this->form_state->getValue('overtimefrom') === "0") && ($this->form_state->getValue('overtimeto') || $this->form_state->getValue('overtimeto') === "0")) {
      $this->form['filter']['overtime_pay'] = array('#markup' => '<div><strong>Overtime Payments:</strong> Greater Than Equal to: $' . $this->form_state->getValue('overtimefrom') . ' and Less Than Equal to: $' . $this->form_state->getValue('overtimeto') . '</div>');
      $this->user_criteria['Overtime Pay Greater Than'] = $this->form_state->getValue('overtimefrom');
      $this->user_criteria['Overtime Pay Less Than'] = $this->form_state->getValue('overtimeto');
      $this->formatted_search_criteria['Overtime Payments'] = 'Greater Than Equal to: $' . $this->form_state->getValue('overtimefrom') . ' and Less Than Equal to: $' . $this->form_state->getValue('overtimeto');
    } elseif (!$this->form_state->getValue('overtimefrom') && ($this->form_state->getValue('overtimeto') || $this->form_state->getValue('overtimeto') === "0")) {
      $this->form['filter']['overtime_pay'] = array('#markup' => '<div><strong>Overtime Payments:</strong> Less Than Equal to: $' . $this->form_state->getValue('overtimeto') . '</div>');
      $this->user_criteria['Overtime Pay Less Than'] = $this->form_state->getValue('overtimeto');
      $this->formatted_search_criteria['Overtime Payments'] = 'Less Than Equal to: $' . $this->form_state->getValue('overtimeto');
    } elseif (($this->form_state->getValue('overtimefrom') || $this->form_state->getValue('overtimefrom') === "0") && !$this->form_state->getValue('overtimeto')) {
      $this->form['filter']['overtime_pay'] = array('#markup' => '<div><strong>Overtime Payments:</strong> Greater Than Equal to: $' . $this->form_state->getValue('overtimefrom') . '</div>');
      $this->user_criteria['Overtime Pay Greater Than'] = $this->form_state->getValue('overtimefrom');
      $this->formatted_search_criteria['Overtime Payments'] = 'Greater Than Equal to: $' . $this->form_state->getValue('overtimefrom');
    }
    if ($this->form_state->getValue('payfrequency')) {
      $this->form['filter']['payfrequency'] = array(
        '#markup' => '<div><strong>Pay Frequency:</strong> ' . $this->form_state->getValue('payfrequency') . '</div>',
      );
      $this->user_criteria['Pay Frequency'] = $this->form_state->getValue('payfrequency');
      $this->formatted_search_criteria['Pay Frequency'] = $this->form_state->getValue('payfrequency');
    }

    if (startsWith($this->form_state->getValue('year'), 'F')) {
      $this->form['filter']['year'] = array(
        '#markup' => '<div><strong>Year:</strong> ' . $this->form_state->getValue('year') . '</div>',
      );
      $this->user_criteria['Fiscal Year'] = $this->form_state->getValue('year');
      $this->formatted_search_criteria['Year'] = $this->form_state->getValue('year');
      $year_type = 'fiscal_year';
    } elseif (startsWith($this->form_state->getValue('year'), 'C')) {
      $this->form['filter']['year'] = array(
        '#markup' => '<div><strong>Year:</strong> ' . $this->form_state->getValue('year') . '</div>',
      );
      $this->user_criteria['Calendar Year'] = $this->form_state->getValue('year');
      $this->formatted_search_criteria['Year'] = $this->form_state->getValue('year');
      $year_type = 'calendar_year';
    }
    if ($this->form_state->getValue('paydatefrom') && $this->form_state->getValue('paydateto')) {
      $this->form['filter']['pay_date'] = array('#markup' => '<div><strong>Pay Date:</strong> From: ' . $this->form_state->getValue('paydatefrom') . ' To: ' . $this->form_state->getValue('paydateto') . '</div>');
      $this->user_criteria['Pay Date After'] = $this->form_state->getValue('paydatefrom');
      $this->user_criteria['Pay Date Before'] = $this->form_state->getValue('paydateto');
      $this->formatted_search_criteria['Pay Date'] = 'From: ' . $this->form_state->getValue('paydatefrom') . ' To: ' . $this->form_state->getValue('paydateto');
    } elseif (!$this->form_state->getValue('paydatefrom') && $this->form_state->getValue('paydateto')) {
      $this->form['filter']['pay_date'] = array('#markup' => '<div><strong>Pay Date:</strong> To: ' . $this->form_state->getValue('paydateto') . '</div>');
      $this->user_criteria['Pay Date Before'] = $this->form_state->getValue('paydateto');
      $this->formatted_search_criteria['Pay Date'] = 'To: ' . $this->form_state->getValue('paydateto');
    } elseif ($this->form_state->getValue('paydatefrom') && !$this->form_state->getValue('paydateto')) {
      $this->form['filter']['pay_date'] = array('#markup' => '<div><strong>Pay Date:</strong> From: ' . $this->form_state->getValue('paydatefrom') . '</div>');
      $this->user_criteria['Pay Date After'] = $this->form_state->getValue('paydatefrom');
      $this->formatted_search_criteria['Pay Date'] = 'Pay Date:</strong> From: ' . $this->form_state->getValue('paydatefrom');
    }
    if ($this->form_state->getValue('payratefrom') && $this->form_state->getValue('payrateto')) {
      $this->form['filter']['pay_rate'] = array('#markup' => '<div><strong>Pay Rate:</strong> Greater Than Equal to: $' . $this->form_state->getValue('payratefrom') . ' and Less Than Equal to: $' . $this->form_state->getValue('payrateto') . '</div>');
      $this->user_criteria['Pay Rate Greater Than'] = $this->form_state->getValue('payratefrom');
      $this->user_criteria['Pay Rate Less Than'] = $this->form_state->getValue('payrateto');
      $this->formatted_search_criteria['Pay Rate'] = 'Greater Than Equal to: $' . $this->form_state->getValue('payratefrom') . ' and Less Than Equal to: $' . $this->form_state->getValue('payrateto');
    } elseif (!$this->form_state->getValue('payratefrom') && $this->form_state->getValue('payrateto')) {
      $this->form['filter']['pay_rate'] = array('#markup' => '<div><strong>Pay Rate:</strong> Less Than Equal to: $' . $this->form_state->getValue('payrateto') . '</div>');
      $this->user_criteria['Pay Rate Less Than'] = $this->form_state->getValue('payrateto');
      $this->formatted_search_criteria['Pay Rate'] = 'Less Than Equal to: $' . $this->form_state->getValue('payrateto');
    } elseif ($this->form_state->getValue('payratefrom') && !$this->form_state->getValue('payrateto')) {
      $this->form['filter']['pay_rate'] = array('#markup' => '<div><strong>Pay Rate:</strong> Greater Than Equal to: $' . $this->form_state->getValue('payratefrom') . '</div>');
      $this->user_criteria['Pay Rate Greater Than'] = $this->form_state->getValue('payratefrom');
      $this->formatted_search_criteria['Pay Rate'] = 'Greater Than Equal to: $' . $this->form_state->getValue('payratefrom');
    }


    //PAYROLL ENDS

    if ($this->form_state->getValue('dept') && $this->form_state->getValue('dept') != 'Select Department') {
      $this->form['filter']['department'] = array('#markup' => '<div><strong>Department:</strong> ' . $this->form_state->getValue('dept') . '</div>');
      $this->user_criteria['Department'] = $this->form_state->getValue('dept');
      $this->formatted_search_criteria['Department'] = $this->form_state->getValue('dept');
    }
    if ($this->form_state->getValue('expense_category') && $this->form_state->getValue('expense_category') != 'Select Expense Category') {
      $this->form['filter']['expense_category'] = array('#markup' => '<div><strong>Expense Category:</strong> ' . $this->form_state->getValue('expense_category') . '</div>');
      $this->user_criteria['Expense Category'] = $this->form_state->getValue('expense_category');
      $this->formatted_search_criteria['Expense Category'] = $this->form_state->getValue('expense_category');
    }

    if ($this->form_state->getValue('budget_code')) {
      $this->form['filter']['budget_code'] = array('#markup' => '<div><strong>Budget Code:</strong> ' . $this->form_state->getValue('budget_code') . '</div>');
      $this->user_criteria['Budget Code'] = $this->form_state->getValue('budget_code');
      $this->formatted_search_criteria['Budget Code'] = $this->form_state->getValue('budget_code');
    }
    if ($this->form_state->getValue('catastrophic_event') !== "0" && $this->form_state->getValue('catastrophic_event') >= 2020) {
      $this->form['filter']['catastrophic_event'] = array('#markup' => '<div><strong>Catastrophic Event:</strong> ' . $this->form_state->getValue('catastrophic_event') . '</div>');
      $this->user_criteria['Catastrophic Event'] = $this->form_state->getValue('catastrophic_event');
      $this->formatted_search_criteria['Catastrophic Event'] = $this->form_state->getValue('catastrophic_event');
    }
    if ($this->form_state->getValue('fiscal_year')) {
      $this->form['filter']['fiscal_year'] = array('#markup' => '<div><strong>Year:</strong> ' . $this->form_state->getValue('fiscal_year') . '</div>');
//      kint($this->form['filter']['fiscal_year'] );
      $this->user_criteria['Year'] = $this->form_state->getValue('fiscal_year');
      $this->formatted_search_criteria['Year'] = $this->form_state->getValue('fiscal_year');
    }
    if (($this->form_state->getValue('currentfrom') || $this->form_state->getValue('currentfrom') === "0") && ($this->form_state->getValue('currentto') || $this->form_state->getValue('currentto') === "0")) {
      $this->form['filter']['current_budget'] = array('#markup' => '<div><strong>Modified Budget:</strong> Greater Than Equal to: $' . $this->form_state->getValue('currentfrom') . ' and Less Than Equal to: $' . $this->form_state->getValue('currentto') . '</div>');
      $this->user_criteria['Modified Budget Greater Than'] = $this->form_state->getValue('currentfrom');
      $this->user_criteria['Modified Budget Less Than'] = $this->form_state->getValue('currentto');
      $this->formatted_search_criteria['Modified Budget'] = 'Greater Than Equal to: $' . $this->form_state->getValue('currentfrom') . ' and Less Than Equal to: $' . $this->form_state->getValue('currentto');
    } elseif (!$this->form_state->getValue('currentfrom') && $this->form_state->getValue('currentfrom')) {
      $this->form['filter']['current_budget'] = array('#markup' => '<div><strong>Modified Budget:</strong> Less Than Equal to: $' . $this->form_state->getValue('currentto') . '</div>');
      $this->user_criteria['Modified Budget Less Than'] = $this->values['currentto'];
      $this->formatted_search_criteria['Modified Budget'] = 'Less Than Equal to: $' . $this->values['currentto'];
    } elseif ($this->form_state->getValue('currentfrom') && !$this->values['currentto']) {
      $this->form['filter']['current_budget'] = array('#markup' => '<div><strong>Modified Budget:</strong> Greater Than Equal to: $' . $this->form_state->getValue('currentfrom') . '</div>');
      $this->user_criteria['Modified Budget Greater Than'] = $this->form_state->getValue('currentfrom');
      $this->formatted_search_criteria['Modified Budget'] = 'Greater Than Equal to: $' . $this->form_state->getValue('currentfrom');
    }
    if (($this->form_state->getValue('adoptedfrom') || $this->form_state->getValue('adoptedfrom') === "0") && ($this->form_state->getValue('adoptedto') || $this->form_state->getValue('adoptedto') === "0")) {
      $this->form['filter']['adopted_budget'] = array('#markup' => '<div><strong>Adopted Budget:</strong> Greater Than Equal to: $' . $this->form_state->getValue('adoptedfrom') . ' and Less Than Equal to: $' . $this->form_state->getValue('adoptedto') . '</div>');
      $this->user_criteria['Adopted Budget Greater Than'] = $this->form_state->getValue('adoptedfrom');
      $this->user_criteria['Adopted Budget Less Than'] = $this->form_state->getValue('adoptedto');
      $this->formatted_search_criteria['Adopted Budget'] = 'Greater Than Equal to: $' . $this->form_state->getValue('adoptedfrom') . ' and Less Than Equal to: $' . $this->form_state->getValue('adoptedto');
    } elseif (!$this->form_state->getValue('adoptedfrom') && ($this->form_state->getValue('adoptedto') || $this->form_state->getValue('adoptedto') === "0")) {
      $this->form['filter']['adopted_budget'] = array('#markup' => '<div><strong>Adopted Budget:</strong> Less Than Equal to: $' . $this->form_state->getValue('adoptedto') . '</div>');
      $this->user_criteria['Adopted Budget Less Than'] = $this->values['adoptedto'];
      $this->formatted_search_criteria['Adopted Budget'] = 'Less Than Equal to: $' . $this->form_state->getValue('adoptedto');
    } elseif (($this->form_state->getValue('adoptedfrom') || $this->form_state->getValue('adoptedfrom') === "0") && !$this->form_state->getValue('adoptedto')) {
      $this->form['filter']['adopted_budget'] = array('#markup' => '<div><strong>Adopted Budget:</strong> Greater Than Equal to: $' . $this->form_state->getValue('adoptedfrom') . '</div>');
      $this->user_criteria['Adopted Budget Greater Than'] = $this->values['adoptedfrom'];
      $this->formatted_search_criteria['Adopted Budget'] = 'Greater Than Equal to: $' . $this->values['adoptedfrom'];
    }
    if (($this->form_state->getValue('preencumberedfrom') || $this->form_state->getValue('preencumberedfrom') === "0") && ($this->form_state->getValue('preencumberedto') || $this->form_state->getValue('preencumberedto') === "0")) {
      $this->form['filter']['preencumbered'] = array('#markup' => '<div><strong>Pre-encumbered:</strong> Greater Than Equal to: $' . $this->form_state->getValue('preencumberedfrom') . ' and Less Than Equal to: $' . $this->form_state->getValue('preencumberedto') . '</div>');
      $this->user_criteria['Pre-encumbered Greater Than'] = $this->form_state->getValue('preencumberedfrom');
      $this->user_criteria['Pre-encumbered Less Than'] = $this->form_state->getValue('preencumberedto');
      $this->formatted_search_criteria['Pre-encumbered'] = 'Greater Than Equal to: $' . $this->form_state->getValue('preencumberedfrom') . ' and Less Than Equal to: $' . $this->form_state->getValue('preencumberedfrom');
    } elseif (!$this->values['preencumberedfrom'] && ($this->form_state->getValue('preencumberedto') || $this->form_state->getValue('preencumberedto') === "0")) {
      $this->form['filter']['preencumbered'] = array('#markup' => '<div><strong>Pre-encumbered:</strong> Less Than Equal to: $' . $this->form_state->getValue('preencumberedto') . '</div>');
      $this->user_criteria['Pre-encumbered Less Than'] = $this->form_state->getValue('preencumberedto');
      $this->formatted_search_criteria['Pre-encumbered'] = 'Less Than Equal to: $' . $this->form_state->getValue('preencumberedto');
    } elseif (($this->form_state->getValue('preencumberedfrom') || $this->form_state->getValue('preencumberedfrom') === "0") && !$this->form_state->getValue('preencumberedto')) {
      $this->form['filter']['preencumbered'] = array('#markup' => '<div><strong>Pre-encumbered:</strong> Greater Than Equal to: $' . $this->form_state->getValue('preencumberedfrom') . '</div>');
      $this->user_criteria['Pre-encumbered Greater Than'] = $this->form_state->getValue('preencumberedfrom');
      $this->formatted_search_criteria['Pre-encumbered'] = 'Greater Than Equal to: $' . $this->form_state->getValue('preencumberedfrom');
    }
    if (($this->form_state->getValue('encumberedfrom') || $this->form_state->getValue('encumberedfrom') === "0") && ($this->form_state->getValue('encumberedto') || $this->form_state->getValue('encumberedto') === "0")) {
      $this->form['filter']['encumbered'] = array('#markup' => '<div><strong>Encumbered:</strong> Greater Than Equal to: $' . $this->form_state->getValue('encumberedfrom') . ' and Less Than Equal to: $' . $this->form_state->getValue('encumberedto') . '</div>');
      $this->user_criteria['Encumbered Greater Than'] = $this->form_state->getValue('encumberedfrom');
      $this->user_criteria['Encumbered Less Than'] = $this->form_state->getValue('encumberedto');
      $this->formatted_search_criteria['Encumbered'] = 'Greater Than Equal to: $' . $this->form_state->getValue('encumberedfrom') . ' and Less Than Equal to: $' . $this->form_state->getValue('encumberedto');
    } elseif (!$this->form_state->getValue('encumberedfrom') && $this->form_state->getValue('encumberedto')) {
      $this->form['filter']['encumbered'] = array('#markup' => '<div><strong>Encumbered:</strong> Less Than Equal to: $' . $this->form_state->getValue('encumberedto') . '</div>');
      $this->user_criteria['Encumbered Less Than'] = $this->form_state->getValue('encumberedto');
      $this->formatted_search_criteria['Encumbered'] = 'Less Than Equal to: $' . $this->form_state->getValue('encumberedto');
    } elseif ($this->form_state->getValue('encumberedfrom') && !$this->form_state->getValue('encumberedto')) {
      $this->form['filter']['encumbered'] = array('#markup' => '<div><strong>Encumbered:</strong> Greater Than Equal to: $' . $this->form_state->getValue('encumberedfrom') . '</div>');
      $this->user_criteria['Encumbered Greater Than'] = $this->form_state->getValue('encumberedfrom');
      $this->formatted_search_criteria['Encumbered'] = 'Greater Than Equal to: $' . $this->form_state->getValue('encumberedfrom');
    }
    if (($this->form_state->getValue('accruedexpensefrom') || $this->form_state->getValue('accruedexpensefrom') === "0") && ($this->form_state->getValue('accruedexpenseto') || $this->form_state->getValue('accruedexpenseto') === "0")) {
      $this->form['filter']['accrued_expense'] = array('#markup' => '<div><strong>Accrued Expense:</strong> Greater Than Equal to: $' . $this->form_state->getValue('accruedexpensefrom') . ' and Less Than Equal to: $' . $this->form_state->getValue('accruedexpenseto') . '</div>');
      $this->user_criteria['Accrued Expense Greater Than'] = $this->form_state->getValue('accruedexpensefrom');
      $this->user_criteria['Accrued Expense Less Than'] = $this->form_state->getValue('accruedexpenseto');
      $this->formatted_search_criteria['Accrued Expense'] = 'Greater Than Equal to: $' . $this->form_state->getValue('accruedexpensefrom') . ' and Less Than Equal to: $' . $this->form_state->getValue('accruedexpenseto');
    } elseif (!$this->form_state->getValue('accruedexpensefrom') && ($this->form_state->getValue('accruedexpenseto') || $this->form_state->getValue('accruedexpenseto') === "0")) {
      $this->form['filter']['accrued_expense'] = array('#markup' => '<div><strong>Accrued Expense:</strong> Less Than Equal to: $' . $this->form_state->getValue('accruedexpenseto') . '</div>');
      $this->user_criteria['Accrued Expense Less Than'] = $this->form_state->getValue('accruedexpenseto');
      $this->formatted_search_criteria['Accrued Expense'] = 'Less Than Equal to: $' . $this->form_state->getValue('accruedexpenseto');
    } elseif (($this->form_state->getValue('accruedexpensefrom') || $this->form_state->getValue('accruedexpensefrom') === "0") && !$this->form_state->getValue('accruedexpenseto')) {
      $this->form['filter']['accrued_expense'] = array('#markup' => '<div><strong>Accrued Expense:</strong> Greater Than Equal to: $' . $this->form_state->getValue('accruedexpensefrom') . '</div>');
      $this->user_criteria['Accrued Expense Greater Than'] = $this->form_state->getValue('accruedexpensefrom');
      $this->formatted_search_criteria['Accrued Expense'] = 'Greater Than Equal to: $' . $this->form_state->getValue('accruedexpensefrom');
    }
    if (($this->form_state->getValue('cashfrom') || $this->form_state->getValue('cashfrom') === "0") && ($this->form_state->getValue('cashto') || $this->form_state->getValue('cashto') === "0")) {
      $this->form['filter']['cash_amount'] = array('#markup' => '<div><strong>Cash Expense:</strong> Greater Than Equal to: $' . $this->form_state->getValue('cashfrom') . ' and Less Than Equal to: $' . $this->form_state->getValue('cashto') . '</div>');
      $this->user_criteria['Cash Expense Greater Than'] = $this->form_state->getValue('cashfrom');
      $this->user_criteria['Cash Expense Less Than'] = $this->form_state->getValue('cashto');
      $this->formatted_search_criteria['Cash Expense'] = 'Greater Than Equal to: $' . $this->form_state->getValue('cashfrom') . ' and Less Than Equal to: $' . $this->form_state->getValue('cashto');
    } elseif (!$this->form_state->getValue('cashfrom') && ($this->form_state->getValue('cashto') || $this->form_state->getValue('cashto') === "0")) {
      $this->form['filter']['cash_amount'] = array('#markup' => '<div><strong>Cash Expense:</strong> Less Than Equal to: $' . $this->form_state->getValue('cashto') . '</div>');
      $this->user_criteria['Cash Expense Less Than'] = $this->form_state->getValue('cashto');
      $this->formatted_search_criteria['Cash Expense'] = 'Less Than Equal to: $' . $this->form_state->getValue('cashto');
    } elseif (($this->form_state->getValue('cashfrom') || $this->form_state->getValue('cashfrom') === "0") && !$this->form_state->getValue('cashto')) {
      $this->form['filter']['cash_amount'] = array('#markup' => '<div><strong>Cash Expense:</strong> Greater Than Equal to: $' . $this->form_state->getValue('cashfrom') . '</div>');
      $this->user_criteria['Cash Expense Greater Than'] = $this->form_state->getValue('cashfrom');
      $this->formatted_search_criteria['Cash Expense'] = 'Greater Than Equal to: $' . $this->form_state->getValue('cashfrom');
    }
    if (($this->form_state->getValue('postadjustmentsfrom') || $this->form_state->getValue('postadjustmentsfrom') === "0") && ($this->form_state->getValue('postadjustmentsto') || $this->form_state->getValue('postadjustmentsto') === "0")) {
      $this->form['filter']['post_adjustments'] = array('#markup' => '<div><strong>Post Adjustments:</strong> Greater Than Equal to: $' . $this->form_state->getValue('postadjustmentsfrom') . ' and Less Than Equal to: $' . $this->form_state->getValue('postadjustmentsto') . '</div>');
      $this->user_criteria['Post Adjustments Greater Than'] = $this->form_state->getValue('postadjustmentsfrom');
      $this->user_criteria['Post Adjustments Less Than'] = $this->form_state->getValue('postadjustmentsto');
      $this->formatted_search_criteria['Post Adjustments'] = 'Greater Than Equal to: $' . $this->form_state->getValue('postadjustmentsfrom') . ' and Less Than Equal to: $' . $this->form_state->getValue('postadjustmentsto');
    } elseif (!$this->form_state->getValue('postadjustmentsfrom') && ($this->form_state->getValue('postadjustmentsto') || $this->form_state->getValue('postadjustmentsto') === "0")) {
      $this->form['filter']['post_adjustments'] = array('#markup' => '<div><strong>Post Adjustments:</strong> Less Than Equal to: $' . $this->form_state->getValue('postadjustmentsto') . '</div>');
      $this->user_criteria['Post Adjustments Less Than'] = $this->form_state->getValue('postadjustmentsto');
      $this->formatted_search_criteria['Post Adjustments'] = 'Less Than Equal to: $' . $this->form_state->getValue('postadjustmentsto');
    } elseif (($this->form_state->getValue('postadjustmentsfrom') || $this->form_state->getValue('postadjustmentsfrom') === "0") && !$this->form_state->getValue('postadjustmentsto')) {
      $this->form['filter']['post_adjustments'] = array('#markup' => '<div><strong>Post Adjustments:</strong> Greater Than Equal to: $' . $this->form_state->getValue('postadjustmentsfrom') . '</div>');
      $this->user_criteria['Post Adjustments Greater Than'] = $this->form_state->getValue('postadjustmentsfrom');
      $this->formatted_search_criteria['Post Adjustments'] = 'Greater Than Equal to: $' . $this->form_state->getValue('postadjustmentsfrom');
    }
  }

  protected function _process_datasource_values() {
    $values = $this->form_state->get(['step_information', 'payroll', 'stored_values']);

    if (startsWith($values['year'], 'F')) {
      $this->criteria['value']['fiscal_year'] = ltrim($values['year'], 'FY ');
    } elseif (startsWith($values['year'], 'C')) {
      $this->criteria['value']['calendar_year'] = ltrim($values['year'], 'CY ');
    }

    if ($this->data_source !== Datasource::NYCHA) {
      if ($values['agency'] != 'Citywide (All Agencies)') {
        preg_match($this->bracket_value_pattern, $values['agency'], $amatches);
        $this->criteria['value']['agency_code'] = trim($amatches[1], '[ ]');
      }
    }

    /*if ($values['title']) {
      $this->criteria['value']['title'] = $values['title'];
    }*/
    if ($values['title']) {
      $title_exact = $_POST['title_exact'];//$values['title_exact'];
      $title = $values['title'];
     // var_dump($title_exact);die();
      if ($title_exact && $title_exact == $title)
        $this->criteria['value']['title_exact'] = $title_exact;
      else
        $this->criteria['value']['title'] = $title;
    }

    if ($values['payfrequency'] != '') {
      $this->criteria['value']['pay_frequency'] = $values['payfrequency'];
    }

    if ($values['basepayfrom'] !== '' || $values['basepayto'] !== '') {
      $this->criteria['range']['base_pay'] = array(
        checknull($values['basepayfrom']),
        checknull($values['basepayto']),
      );
    }

    if ($values['overtimefrom'] !== '' || $values['overtimeto'] !== '') {
      $this->criteria['range']['overtime_payments'] = array(
        checknull($values['overtimefrom']),
        checknull($values['overtimeto']),
      );
    }

    if ($values['otherfrom'] !== '' || $values['otherto'] !== '') {
      $this->criteria['range']['other_payments'] = array(
        checknull($values['otherfrom']),
        checknull($values['otherto']),
      );
    }

    if ($values['grossfrom'] !== '' || $values['grossto'] !== '') {
      $this->criteria['range']['gross_pay'] = array(
        checknull($values['grossfrom']),
        checknull($values['grossto']),
      );
    }

    if ($values['paydatefrom'] !== '' || $values['paydateto'] !== '') {
      $this->criteria['range']['pay_date'] = array(
        checknull($values['paydatefrom']),
        checknull($values['paydateto']),
      );
    }

    if ($values['totalgrossfrom'] !== '' || $values['totalgrossto'] !== '') {
      $this->criteria['range']['gross_pay_ytd'] = array(
        checknull($values['totalgrossfrom']),
        checknull($values['totalgrossto']),
      );
    }

    if ($values['salary_type']) {
      $this->criteria['value']['amount_type'] = $values['salary_type'];
    }

    if ($values['salaryfrom'] !== '' || $values['salaryto'] !== '') {
      $this->criteria['range']['amount'] = array(
        checknull($values['salaryfrom']),
        checknull($values['salaryto']),
      );
    }

    /*if ($this->form_state->getValue('dept')) {
      preg_match($this->bracket_value_pattern, $this->form_state->getValue('dept'), $dcmatches);
      if ($dcmatches) {
        $this->criteria['value']['department_code'] = trim($dcmatches[1], '[ ]');
      }
    }
    if ($this->form_state->getValue('expense_category')) {
      preg_match($this->bracket_value_pattern, $this->form_state->getValue('expense_category'), $ecmatches);
      if ($ecmatches) {
        $this->criteria['value']['expense_category'] = trim($ecmatches[1], '[ ]');
      }
    }

    if ($this->form_state->getValue('budget_code')) {
      preg_match($this->bracket_value_pattern, $this->form_state->getValue('budget_code'), $bcmatches);
      if ($bcmatches) {
        $this->criteria['value']['budget_code'] = trim($bcmatches[1], '[ ]');
        $this->criteria['value']['budget_code_name'] = str_replace($bcmatches[1], "", $this->form_state->getValue('budget_code'));
      } else {
        $this->criteria['value']['budget_code_name'] = $this->form_state->getValue('budget_code');
      }
    }
    if ($this->form_state->getValue('catastrophic_event') !== '0' && $this->form_state->getValue('fiscal_year') >= 2020) {
      preg_match($this->bracket_value_pattern, $this->form_state->getValue('catastrophic_event'), $evcmatches);
      if ($evcmatches) {
        $this->criteria['value']['catastrophic_event'] = trim($evcmatches[1], '[ ]');
      }
    }
    if ($this->form_state->getValue('adoptedfrom') !== '' || $this->form_state->getValue('adoptedto') !== '') {
      $this->criteria['range']['adopted'] = array(
        checknull($this->form_state->getValue('adoptedfrom')),
        checknull($this->form_state->getValue('adoptedto')),
      );
    }
    if ($this->form_state->getValue('currentfrom') !== '' || $this->form_state->getValue('currentto') !== '') {
      $this->criteria['range']['modified'] = array(
        checknull($this->form_state->getValue('currentfrom')),
        checknull($this->form_state->getValue('currentto')),
      );
    }
    if ($this->form_state->getValue('preencumberedfrom') !== '' || $this->form_state->getValue('preencumberedto') !== '') {
      $this->criteria['range']['pre_encumbered'] = array(
        checknull($this->form_state->getValue('preencumberedfrom')),
        checknull($this->form_state->getValue('preencumberedto')),
      );
    }
    if ($this->form_state->getValue('encumberedfrom') !== '' || $this->form_state->getValue('encumberedto') !== '') {
      $this->criteria['range']['encumbered'] = array(
        checknull($this->form_state->getValue('encumberedfrom')),
        checknull($this->form_state->getValue('encumberedto')),
      );
    }
    if ($this->form_state->getValue('cashfrom') !== '' || $this->form_state->getValue('cashto') !== '') {
      $this->criteria['range']['cash_expense'] = array(
        checknull($this->form_state->getValue('cashfrom')),
        checknull($this->form_state->getValue('cashto')),
      );
    }
    if ($this->form_state->getValue('postadjustmentsfrom') !== '' || $this->form_state->getValue('postadjustmentsto') !== '') {
      $this->criteria['range']['post_adjustment'] = array(
        checknull($this->form_state->getValue('postadjustmentsfrom')),
        checknull($this->form_state->getValue('postadjustmentsto')),
      );
    }
    if ($this->form_state->getValue('accruedexpensefrom') !== '' || $this->form_state->getValue('accruedexpenseto') !== '') {
      $this->criteria['range']['accrued_expense'] = array(
        checknull($this->form_state->getValue('accruedexpensefrom')),
        checknull($this->form_state->getValue('accruedexpenseto')),
      );
    }*/
  }

  protected function _validate_by_datasource(&$form, &$form_state)
  {
    //Copied from payroll.inc
    //  $basepayfrom = $form_state['values']['basepayfrom'];
    $basepayfrom = $form_state->getValue('basepayfrom');
//  $basepayto = $form_state['values']['basepayto'];
    $basepayto = $form_state->getValue('basepayto');
//  $overtimefrom = $form_state['values']['overtimefrom'];
    $overtimefrom = $form_state->getValue('overtimefrom');
//  $overtimeto = $form_state['values']['overtimeto'];
    $overtimeto = $form_state->getValue('overtimeto');
//  $otherfrom = $form_state['values']['otherfrom'];
    $otherfrom = $form_state->getValue('otherfrom');
//  $otherto = $form_state['values']['otherto'];
    $otherto = $form_state->getValue('otherto');
//  $grossfrom = $form_state['values']['grossfrom'];
    $grossfrom = $form_state->getValue('grossfrom');
//  $grossto = $form_state['values']['grossto'];
    $grossto = $form_state->getValue('grossto');
//  $paydatefrom = $form_state['values']['paydatefrom'];
    $paydatefrom = $form_state->getValue('paydatefrom');
//  $paydateto = $form_state['values']['paydateto'];
    $paydateto = $form_state->getValue('paydateto');
//  $totalgrossfrom = $form_state['values']['totalgrossfrom'];
    $totalgrossfrom = $form_state->getValue('totalgrossfrom');
//  $totalgrossto = $form_state['values']['totalgrossto'];
    $totalgrossto = $form_state->getValue('totalgrossto');
//  $salaryfrom = $form_state['values']['salaryfrom'];
    $salaryfrom = $form_state->getValue('salaryfrom');
//  $salaryto = $form_state['values']['salaryto'];
    $salaryto = $form_state->getValue('salaryto');
//  $data_source = $form_state['values']['datafeeds-payroll-domain-filter'];
    $data_source = $form_state->getValue('datafeeds-payroll-domain-filter');
//  $oge_response_columns = $form_state['values']['oge_column_select'];
    $oge_response_columns = $form_state->getValue('oge_column_select');
//  $response_columns = $form_state['values']['column_select'];
    $response_columns = $form_state->getValue('column_select');
//    kint($oge_response_columns);
//    kint($response_columns);
    // Base Pay:
    if ($basepayfrom && !is_numeric($basepayfrom)) {
//    form_set_error('basepayfrom', t('Base Pay From value must be a number.'));
      $form_state->setErrorByName('basepayfrom', t('Base Pay From value must be a number.'));
    }
    if ($basepayto && !is_numeric($basepayto)) {
//    form_set_error('basepayto', t('Base Pay To value must be a number.'));
      $form_state->setErrorByName('basepayto', t('Base Pay To value must be a number.'));
    }
    if (is_numeric($basepayfrom) && is_numeric($basepayto) && $basepayto < $basepayfrom) {
//    form_set_error('basepayto', t('Invalid range for Base Pay.'));
      $form_state->setErrorByName('basepayto', t('Invalid range for Base Pay.'));
    }
    // Overtime Pay:
    if ($overtimefrom && !is_numeric($overtimefrom)) {
//    form_set_error('overtimefrom', t('Overtime Payments From value must be a number.'));
      $form_state->setErrorByName('overtimefrom', t('Overtime Payments From value must be a number.'));
    }
    if ($overtimeto && !is_numeric($overtimeto)) {
//    form_set_error('overtimeto', t('Overtime Payments To value must be a number.'));
      $form_state->setErrorByName('overtimeto', t('Overtime Payments To value must be a number.'));
    }
    if (is_numeric($overtimefrom) && is_numeric($overtimeto) && $overtimeto < $overtimefrom) {
//    form_set_error('overtimeto', t('Invalid range for Overtime Payments.'));
      $form_state->setErrorByName('overtimeto', t('Invalid range for Overtime Payments.'));
    }
    // Other Pay:
    if ($otherfrom && !is_numeric($otherfrom)) {
//    form_set_error('otherfrom', t('Other Payments From value must be a number.'));
      $form_state->setErrorByName('otherfrom', t('Other Payments From value must be a number.'));
    }
    if ($otherto && !is_numeric($otherto)) {
//    form_set_error('otherto', t('Other Payments To value must be a number.'));
      $form_state->setErrorByName('otherto', t('Other Payments To value must be a number.'));
    }
    if (is_numeric($otherfrom) && is_numeric($otherto) && $otherto < $otherfrom) {
//    form_set_error('otherto', t('Invalid range for Other Payments.'));
      $form_state->setErrorByName('otherto', t('Invalid range for Other Payments.'));
    }
    // Gross Pay:
    if ($grossfrom && !is_numeric($grossfrom)) {
//    form_set_error('grossfrom', t('Gross Pay From value must be a number.'));
      $form_state->setErrorByName('grossfrom', t('Gross Pay From value must be a number.'));
    }
    if ($grossto && !is_numeric($grossto)) {
//    form_set_error('grossto', t('Gross Pay To value must be a number.'));
      $form_state->setErrorByName('grossto', t('Gross Pay To value must be a number.'));
    }//
    if (is_numeric($grossfrom) && is_numeric($grossto) && $grossto < $grossfrom) {
//    form_set_error('grossto', t('Invalid range for Gross Pay'));
      $form_state->setErrorByName('grossto', t('Invalid range for Gross Pay.'));
    }
    // Pay Date:
    if ($paydatefrom && !checkDateFormat($paydatefrom)) {
//    form_set_error('paydatefrom', t('Pay Date From must be a valid date (YYYY-MM-DD).'));
      $form_state->setErrorByName('paydatefrom', t('Pay Date From must be a valid date (YYYY-MM-DD).'));
    }
//    kint($paydateto);
    if ($paydateto && !checkDateFormat($paydateto)) {
//    form_set_error('paydateto', t('Pay Date To must be a valid date (YYYY-MM-DD).'));
      $form_state->setErrorByName('paydateto', t('Pay Date To must be a valid date (YYYY-MM-DD).'));
    }
    if ($paydatefrom && $paydateto && strtotime($paydateto) < strtotime($paydatefrom)) {
//    form_set_error('paydateto', t('Pay Date To must be a valid date (YYYY-MM-DD).'));
      $form_state->setErrorByName('paydateto', t('Invalid range for Pay Date.'));
    }
    // Total Gross YTD:
    if ($totalgrossfrom && !is_numeric($totalgrossfrom)) {
//    form_set_error('totalgrossfrom', t('Gross Pay YTD From value must be a number.'));
      $form_state->setErrorByName('totalgrossfrom', t('Gross Pay YTD From value must be a number.'));
    }
    if ($totalgrossto && !is_numeric($totalgrossto)) {
//    form_set_error('totalgrossto', t('Gross Pay YTD To value must be a number.'));
      $form_state->setErrorByName('totalgrossto', t('Gross Pay YTD To value must be a number.'));
    }
    if (is_numeric($totalgrossfrom) && is_numeric($totalgrossto) && $totalgrossto < $totalgrossfrom) {
//    form_set_error('totalgrossto', t('Invalid range for Gross Pay YTD.'));
      $form_state->setErrorByName('totalgrossto', t('Invalid range for Gross Pay YTD.'));
    }
    // Annual Salary:
    if ($salaryfrom && !is_numeric($salaryfrom)) {
//    form_set_error('salaryfrom', t('Amount From value must be a number.'));
      $form_state->setErrorByName('salaryfrom', t('Amount From value must be a number.'));
    }
    if ($salaryto && !is_numeric($salaryto)) {
//    form_set_error('salaryto', t('Amount To value must be a number.'));
      $form_state->setErrorByName('salaryto', t('Amount To value must be a number.'));
    }
    if (is_numeric($salaryfrom) && is_numeric($salaryto) && $salaryto < $salaryfrom) {
//    form_set_error('salaryto', t('Invalid range for Amount.'));
      $form_state->setErrorByName('salaryto', t('Invalid range for Amount.'));
    }
    //Validate response columns
//   if (Datasource::NYCHA == $data_source && !$oge_response_columns) {
//    if (Datasource::NYCHA == $data_source && (array_key_first($og_response_columns) === null && count($oge_response_columns) < 2)) {
//    if ( Datasource::NYCHA == $data_source && empty(array_filter($oge_response_columns)) ){
      if ( Datasource::NYCHA == $data_source && (array_key_first($oge_response_columns) == '' || array_key_first($oge_response_columns) == null)){
//    form_set_error('oge_column_select', t('You must select at least one column.'));
      $form_state->setErrorByName('oge_column_select', t('You must select at least one column.'));
//   } else if (Datasource::CITYWIDE == $data_source && !$response_columns) {
    } else if (Datasource::CITYWIDE == $data_source && (array_key_first($response_columns) == '' || array_key_first($response_columns) == null)) {
//    } else if (Datasource::CITYWIDE == $data_source && $form_state->getValue('column_select')->isEmpty()) {
//    form_set_error('column_select', t('You must select at least one column.'));
      $form_state->setErrorByName('column_select', t('You must select at least one column.'));
    }

    //Set the hidden filed values on Spending form
    // $form_state['complete form']['data_source']['#value'] = $data_source;
    $form_state->setValue(['complete form', 'data_source', '#value'], $data_source);
    //Hidden Field for multi-select
    if ($data_source == Datasource::NYCHA) {
      //$multi_select_hidden = isset($form_state['input']['oge_column_select']) ? '|' . implode('||', $form_state['input']['oge_column_select']) . '|' : '';
      $multi_select_hidden = $form_state->hasValue(['input', 'oge_column_select']) ? '|' . implode('||', $form_state->getValue(['input', 'oge_column_select'])) . '|' : '';

    } else {
      //$multi_select_hidden = isset($form_state['input']['column_select']) ? '|' . implode('||', $form_state['input']['column_select']) . '|' : '';
      $multi_select_hidden = $form_state->hasValue(['input', 'column_select']) ? '|' . implode('||', $form_state->getValue(['input', 'column_select'])) . '|' : '';

    }
    //$form_state['complete form']['data_source']['#value'] = $data_source;
    $form_state->set(['complete form', 'data_source', '#value'], $data_source);
    //$form_state['complete form']['hidden_multiple_value']['#value'] = $multi_select_hidden;
    $form_state->set(['complete form', 'hidden_multiple_value', '#value'], $multi_select_hidden);

  }

}
