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
?>
<h3>Spending by Expense Category</h3>
  <?php
  
  if ( _getRequestParamValue("datasource") == "checkbook_oge") {
  	$datasource ="/datasource/checkbook_oge";
  }
echo "<table class='dataTable spending-exp-cat outerTable'>";
echo "<thead>
        <tr>
          <th class='text'>" . WidgetUtil::generateLabelMapping("expense_category") ."</th>
          <th class='number'>" . WidgetUtil::generateLabelMapping("encumbered_amount") ."</th>
          <th class='number endCol'>" . WidgetUtil::generateLabelMapping("amount_spent") ."</th>
        </tr>
      </thead>
      <tbody>";
$count = 0;
if (count($node->data) > 0) {
  foreach ($node->data as $row) {
    if ($count % 2 == 0) {
      $class = "odd";
    }
    else {
      $class = "even";
    }
    $spending_link = "/spending/transactions/expcategorycode/" . $row['expenditure_object_code'] . "/contnum/" .
      $row['contract_number@checkbook:history_agreement/original_agreement_id@checkbook:aggregateon_contracts_expense']
      . $datasource . "/newwindow";
    echo "<tr class='outer " . $class . "'>";
    echo "<td class='text'><div>" . $row['expenditure_object_name'] . "</div></td>";
    echo "<td class='number'><div>" . custom_number_formatter_format($row['encumbered_amount'], 2, '$') . "</div></td>";
    if ($row['is_disbursements_exist'] == 'Y' && !preg_match("/newwindow/",current_path())) {
      echo "<td class='number endCol'><div><a class=\"new_window\" href='" . $spending_link . "'>" . custom_number_formatter_format($row['spending_amount'], 2, '$') . "</a></div></td>";
    }
    else {
      echo "<td class='number endCol'><div>" . custom_number_formatter_format($row['spending_amount'], 2, '$') . "</div></td>";
    }
    echo "</tr>";
    $count += 1;
  }
}
else {
  echo "<tr class='odd'>";
  echo '<td class="dataTables_empty" valign="top" colspan="3">' .
           '<div id="no-records-datatable" class="clearfix">
                 <span>No Matching Records Found</span>
           </div>' . '</td>';
  echo "</tr>";
}
echo "</tbody></table>";