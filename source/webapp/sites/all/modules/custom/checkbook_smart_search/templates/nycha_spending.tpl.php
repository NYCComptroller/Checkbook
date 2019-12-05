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
$spendingParameterMapping = CheckbookSolr::getSearchFields($solr_datasource, 'spending');

//Display hyphen for the following fields based on spending category
$hyphenFields = array(1 => array("agreement_type_name", "contract_number", "release_number", "contract_purpose", "industry_type_name", "department_name"),
                      4 => array("agreement_type_name", "contract_number", "release_number", "contract_purpose", "industry_type_name", "department_name"),
                      2 => array("agreement_type_name", "contract_number", "release_number", "invoice_number", "contract_spent_amount", "contract_purpose",
                                 "industry_type_name", "funding_source_name", "responsibility_center_name", "program_phase_name", "gl_project_name"));

//Amount Fields and decimals to be displayed
$amountFields = array("check_amount" => 2, "contract_spent_amount" => 4);
$dateFields = array("check_issue_date");

$yearId = isset($spending_results['fiscal_year_id']) ? isset($spending_results['fiscal_year_id']) : CheckbookDateUtil::getCurrentFiscalYear(Datasource::NYCHA);
$contractIdLink = NychaSpendingUrlService::generateContractIdLink($spending_results['contract_number'], $yearId);
$vendorLink = "<a href='/nycha_spending/datasource/checkbook_nycha/year/".$yearId."/vendor/".$spending_results['vendor_id']."'>".$spending_results['vendor_name']."</a>";
$linkableFields = array("contract_number" => $contractIdLink, "vendor_name" => $vendorLink);

$count = 1;
foreach ($spendingParameterMapping as $key => $title) {
  if(in_array($key, $hyphenFields[$spending_results['spending_category_id']])){
    $value = "-";
  }else {
    $value = $spending_results[$key];
    //Date Fields
    if (in_array($key, $dateFields)) {
      $value = date("F j, Y", strtotime(substr($value, 0, 10)));
    }
    //Amount Fields
    if (array_key_exists($key, $amountFields)) {
      $value = custom_number_formatter_basic_format($value, $amountFields[$key]);
    }
    //Hyperlink Fields
    if (array_key_exists($key, $linkableFields)) {
      $value = $linkableFields[$key];
    }
  }

  if ($count % 2 == 0) {
    if ($title) {
      $row[] = '<div class="field-label">' . $title . ':</div><div class="field-content">' . $value . '</div>';
    }
    $rows[] = $row;
    $row = array();
  } else {
    if ($title) {
      $row[] = '<div class="field-label">' . $title . ':</div><div class="field-content">' . $value . '</div>';
    }
  }
  $count++;
}

print theme('table', array('rows' => $rows, 'attributes' => array('class' => array('search-result-fields'))));
