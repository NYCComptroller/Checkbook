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
    Contract History By Prime Vendor
  </h3>
  <table class="dataTable cta-history outerTable">
    <thead>
    <tr>
      <th class="text"><?php echo WidgetUtil::generateLabelMapping("fiscal_year"); ?></th>
      <th class="text"><?php echo WidgetUtil::generateLabelMapping("no_of_mod"); ?></th>
      <th class="number"><?php echo WidgetUtil::generateLabelMapping("current_amount"); ?></th>
      <th class="number endCol"><?php echo WidgetUtil::generateLabelMapping("original_amount"); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php

    $sortedArray = array();
    $currentFY = $node->data[0]['source_updated_fiscal_year'];
    $reg_date = $node->data[count($node->data) - 1]['date@checkbook:date_id/registered_date_id@checkbook:history_agreement'];
    foreach ($node->data as $row) {
      if (isset($row['original_maximum_amount'])) {
        $row['original_contract_amount'] = $row['original_maximum_amount'];
        $row['maximum_contract_amount'] = $row['revised_maximum_amount'];
      }
      if($row['status'] != 'Registered' || isset($row['source_updated_fiscal_year'])){
        $sortedArray[$row['source_updated_fiscal_year']][] = $row;
        }
    }
    if (count($sortedArray) > 0) {
      //TODO To be clarified
      $keys = array_keys($sortedArray);
      $lastKey = $keys[sizeof($sortedArray) - 1];
      $lastFYArray = $sortedArray[$lastKey];
      //$sortedArray[$lastKey][sizeof($sortedArray[$lastKey])-1]['updated_date'] = $sortedArray[$lastKey][sizeof($sortedArray[$lastKey])-1]['date@checkbook:date_id/registered_date_id@checkbook:history_agreement'];

      $showCondition = "";

      $count1 = 0;
      
//      log_error($sortedArray);
      
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
          echo "<td class='text'><div>" . count($items) . " Modifications</div></td>";
          $showClass = 'open';
          $orig_amount_sum = 0;
          $curr_amount_sum = 0;
          foreach ($items as $item) {
            $curr_amount_sum = $item['maximum_contract_amount'];
            $orig_amount_sum = $item['original_contract_amount'];
            break;
          }
          echo "<td class='number'><div>" . custom_number_formatter_format($curr_amount_sum, 2, '$') . "</div></td>";
          echo "<td class='number endCol'><div>" . custom_number_formatter_format($orig_amount_sum, 2, '$') . "</div></td>";
          echo "</tr>";
          $count1 += 1;
          echo "<tr id='showHidectahis" . $key . "' class='showHide " . $class1 . "' " . $showCondition . ">";
          $showCondition = "style='display:none'";
          echo "<td colspan='4' >";
          echo "<div class='scroll'>";
          echo "<table class='sub-table col9'>";
          echo "<thead>
                  <tr>
                    <th class='number thVNum'>".WidgetUtil::generateLabelMapping("version_number")."</th>
                    <th class='text thStartDate'>".WidgetUtil::generateLabelMapping("start_date")."</th>
                    <th class='text thEndDate'>".WidgetUtil::generateLabelMapping("end_date")."</th>
                    <th class='text thRegDate'>".WidgetUtil::generateLabelMapping("reg_date")."</th>
                    <th class='text thLastMDate'>".WidgetUtil::generateLabelMapping("last_mod_date")."</th>
                    <th class='number thCurAmt'>".WidgetUtil::generateLabelMapping("current_amount")."</th>
                    <th class='number thOrigAmt'>".WidgetUtil::generateLabelMapping("original_amount")."</th>
                    <th class='number thIncDec'>".WidgetUtil::generateLabelMapping("increase_decrease")."</th>
                    <th class='text thVerStat status'>".WidgetUtil::generateLabelMapping("version_status")."</th>
                  </tr></thead><tbody>";
          $count = 0;
          
          foreach ($items as $item) {
            if ($count % 2 == 0) {
              $class = "class=\"odd\"";
            }
            else {
              $class = "class=\"even\"";
            }
            echo "<tr " . $class . ">";
            echo "<td class='number thVNum' ><div>" . $item['document_version'] . "</div></td>";
            echo "<td class='text thStartDate'><div>" . $item['start_date'] . "</div></td>";
            echo "<td class='text thEndDate'><div>" . $item['end_date'] . "</div></td>";
            echo "<td class='text thRegDate'><div>" . $reg_date . "</div></td>";
            if (isset($item['cif_received_date'])) {
              echo "<td class='text thLastMDate'><div>" . $item['cif_received_date'] . "</div></td>";
            }
            elseif (trim($item['document_version']) == "1") {
              echo "<td class='thLastMDate'><div></div></td>";
            }
            else {
              echo "<td class='text thLastMDate'><div>" . $item['date@checkbook:date_id/source_updated_date_id@checkbook:history_agreement'] . "</div></td>";
            }
            echo "<td class='number thCurAmt' ><div>" . custom_number_formatter_format($item['maximum_contract_amount'], 2, '$') . "</div></td>";
            echo "<td class='number thOrigAmt' ><div>" . custom_number_formatter_format($item['original_contract_amount'], 2, '$') . "</div></td>";
            echo "<td class='number thIncDec' ><div>" . custom_number_formatter_format(($item['maximum_contract_amount'] - $item['original_contract_amount']), 2, '$') . "</div></td>";
            echo "<td class='text thVerStat'><div>" . $item['status'] . "</div></td>";
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
      echo '<td class="dataTables_empty" valign="top" colspan="4">' .
           '<div id="no-records-datatable" class="clearfix">
                 <span>No Matching Records Found</span>
           </div>' . '</td>';
      echo '</tr>';
    }
    ?>
    </tbody>
  </table>
</div>