<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
?>
<h3>Spending by Expense Category</h3>
  <?php
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
      . "/newwindow";
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