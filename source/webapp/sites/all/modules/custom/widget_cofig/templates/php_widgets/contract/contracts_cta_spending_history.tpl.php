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
<div>
  <h3>
    Spending Transactions By Prime Vendor
  </h3>

  <table class="dataTable cta-spending-history outerTable">
    <thead>
    <tr>
      <th class="text"><?php echo WidgetUtil::generateLabelMapping("fiscal_year") ?></th>
      <th class="text"><?php echo WidgetUtil::generateLabelMapping("no_of_transactions") ?></th>
      <th class="number endCol"><?php echo WidgetUtil::generateLabelMapping("amount_spent") ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
//1064400//29642
    $sortedArray = array();
    $currentFY = $node->data[0]['fiscal_year'];
    foreach ($node->data as $row) {
      $sortedArray[$row['fiscal_year']][] = $row;
    }
    if (count($sortedArray) > 0) {
//  dsm($sortedArray);
      $showCondition = "";
      //$showClass = 'close';
      $count1 = 0;
      foreach ($sortedArray as $key => $items) {
        if ($key != null) {
          if ($count1 % 2 == 0) {
            $class1 = "odd";
          }
          else {
            $class1 = "even";
          }
          echo "<tr class='outer " . $class1 . "'>";
          echo "<td class='text'><div><a class=\"showHide $showClass\"></a> FY " . $key . "</div></td>";
          echo "<td class='text'><div>" . count($items) . " Transactions</div></td>";
          $showClass = 'open';
          $check_amount_sum = 0;
          foreach ($items as $item) {
            $check_amount_sum += $item['check_amount'];
          }
          echo "<td class='number endCol'><div>" . custom_number_formatter_format($check_amount_sum, 2, '$') . "</div></td>";
          echo "</tr>";
          $count1 += 1;
          echo "<tr id='showHidectaspe" . $key . "' class='showHide " . $class1 . "' " . $showCondition . ">";
          $showCondition = "style='display:none'";
          echo "<td colspan='3' >";
          echo "<div class='scroll'>";
          echo "<table class='sub-table col6'>";
          echo "<thead><tr><th class='text th1'><div><span>Date</span></div></th>
                           <th class='text th2'>". WidgetUtil::generateLabelMapping("document_id")."</th>
                           <th class='number th3'>". WidgetUtil::generateLabelMapping("check_amount")."</th>
                           <th class='text th4'>". WidgetUtil::generateLabelMapping("expense_category")."</th>
                           <th class='text th5'>". WidgetUtil::generateLabelMapping("agency_name")."</th>
                           <th class='text th6'>". WidgetUtil::generateLabelMapping("dept_name")."</th></tr></thead><tbody>";
          $count = 0;
          foreach ($items as $item) {
            if ($count % 2 == 0) {
              $class = "class=\"odd\"";
            }
            else {
              $class = "class=\"even\"";
            }
            echo "<tr " . $class . ">";
            echo "<td class='text td1'><div>" . $item['date@checkbook:date_id/check_eft_issued_date_id@checkbook:disbursement_line_item_details'] . "</div></td>";
            echo "<td class='text td2'><div>" . $item['document_id'] . "</div></td>";
            echo "<td class='number td3'><div>" . custom_number_formatter_format($item['check_amount'], 2, '$') . "</div></td>";
            echo "<td class='text td4'><div>" . $item['expenditure_object_name'] . "</div></td>";
            echo "<td class='text td5'><div>" . $item['agency_name'] . "</div></td>";
            echo "<td class='text td6'><div>" . $item['department_name'] . "</div></td>";
            echo "</tr>";
            $count += 1;
          }
          echo "</tbody>";
          echo "</table></div>";
          echo "</td>";
          echo "</tr>";
        }
      }
    }
    else {
      echo '<tr class="odd">';
     echo '<td class="dataTables_empty" valign="top" colspan="3">' .
           '<div id="no-records-datatable" class="clearfix">
                 <span>No Matching Records Found</span>
           </div>' . '</td>';
      echo '</tr>';
    }
    ?>
    </tbody>
  </table>
</div>