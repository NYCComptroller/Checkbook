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
<div class="nyc_totals_links">
  <table>
    <tbody>
    <tr>
      <?php
          $categories_order = array(null, 2, 3, 1, 4);
          $category_names = NychaSpendingUtil::$categories;
          foreach($node->data as $key=>$row){
            if($row['category_name_category_name'] == 'Payroll'){$row['invoice_amount_sum'] = $row['check_amount_sum'];}
            $categories[$row['category_category']] = array('name' => $row['category_name_category_name'], 'amount' => $row['invoice_amount_sum']);
            $total_spending +=  $row['invoice_amount_sum'];
          }
          $categories[null] = array('name' => 'Total', 'amount' => $total_spending);
          foreach($categories_order as $key => $category_id){
              $active_class = "";
              if (RequestUtilities::get("category") == $category_id) {
                  $active_class = ' class="active"';
              }
              $link = SpendingUtil::prepareSpendingBottomNavFilter("nycha_spending", $category_id);
              $amount = "<span class='dollars'>" . custom_number_formatter_format($categories[$category_id]['amount'],1,'$') . "</span>";
              $category_name = $category_names[$category_id].'<br>Spending<br>';

              echo "<td" . $active_class ."><div class='positioning'>";
              if($categories[$category_id]['amount'] != 0 ){
                echo '<a href="/'.$link.'">' .$category_name .$amount . '</a>';
              }else{
                echo $category_name .$amount;
              }
              echo "</div><div class=\"indicator\"></div></td>";
          }
      ?>
    </tr>
    </tbody>
  </table>
</div>


