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

      foreach($node->data as $key=>$row){
        $dollars_by_cat[$row['category_category']] = $row['check_amount_sum'];
        $total +=  $row['check_amount_sum'];
      }
      $class = "";
      if ( RequestUtilities::get("category") == "") {
        $class = ' class="active"';
      }
      $link = RequestUtil::prepareSpendingBottomNavFilter("spending_landing",null);
      $dollars = "<span class='dollars'>" . custom_number_formatter_format($total,1,'$') . "</span>";
      ?>
      <td<?php echo $class; ?>>
        <div class="positioning">
      <?php if($total != 0 ){?>
          <a href="/<?php echo $link; ?>?expandBottomCont=true"><?php echo $count; ?>Total<br>Spending<br><?php echo $dollars; ?></a>
        <?php }else{?>
        <?php echo $count; ?><a>Total<br>Spending</a><?php echo $dollars; ?>
        <?php }?>
        </div>
        <div class="indicator"></div>
      </td>
      <?php
      $class = "";
      if (RequestUtilities::get("category") == 2) {
        $class = ' class="active"';
      }
      $link = RequestUtil::prepareSpendingBottomNavFilter("spending_landing",2);
      $dollars = "<span class='dollars'>" . custom_number_formatter_format($dollars_by_cat[2],1,'$') . "</span>";
      ?>
      <td<?php echo $class; ?>>
        <div class="positioning">
      <?php if($dollars_by_cat[2] != 0 ){?>
        <a href="/<?php echo $link; ?>?expandBottomCont=true"><?php echo $count; ?>Payroll<br>Spending<br><?php echo $dollars; ?></a>
        <?php }else{?>
        <?php echo $count; ?>Payroll<br>Spending<br><?php echo $dollars; ?>
        <?php }?>
        </div>
        <div class="indicator"></div>
      </td>
      <?php
      $class = "";
      if (RequestUtilities::get("category") == 3) {
        $class = ' class="active"';
      }
      $link = RequestUtil::prepareSpendingBottomNavFilter("spending_landing",3);
      $dollars = "<span class='dollars'>" . custom_number_formatter_format($dollars_by_cat[3],1,'$') . "</span>";

      ?>
      <td<?php echo $class; ?>>
        <div class="positioning">
        <?php if($dollars_by_cat[3] != 0 ){?>
          <a href="/<?php echo $link; ?>?expandBottomCont=true"><?php echo $count; ?>Capital<br>Spending<br><?php echo $dollars; ?></a>
        <?php }else{?>
        <?php echo $count; ?>Capital<br>Spending<br><?php echo $dollars; ?>
        <?php }?>
        </div>
        <div class="indicator"></div>
      </td>
      <?php
      $class = "";
      if (RequestUtilities::get("category") == 1) {
        $class = ' class="active"';
      }
      $link = RequestUtil::prepareSpendingBottomNavFilter("spending_landing",1);
      $dollars = "<span class='dollars'>" . custom_number_formatter_format($dollars_by_cat[1],1,'$') . "</span>";

      ?>
      <td<?php echo $class; ?>>
        <div class="positioning">
      <?php if($dollars_by_cat[1] != 0 ){?>
          <a href="/<?php echo $link; ?>?expandBottomCont=true"><?php echo $count; ?>Contract<br>Spending<br><?php echo $dollars; ?></a>
        <?php }else{?>
        <?php echo $count; ?>Contract<br>Spending<br><?php echo $dollars; ?>
        <?php }?>
        </div>
        <div class="indicator"></div>
      </td>
      <?php
      $class = "";
      if (RequestUtilities::get("category") == 5) {
        $class = ' class="active"';
      }
      $link = RequestUtil::prepareSpendingBottomNavFilter("spending_landing",5);
      $dollars = "<span class='dollars'>" . custom_number_formatter_format($dollars_by_cat[5],1,'$') . "</span>";

      ?>
      <td<?php echo $class;?>>
        <div class="positioning">
      <?php if($dollars_by_cat[5] != 0 ){?>
          <a href="/<?php echo $link; ?>?expandBottomCont=true"><?php echo $count; ?>Trust & Agency<br>Spending<br><?php echo $dollars; ?></a>
        <?php }else{?>
        <?php echo $count; ?>Trust & Agency<br>Spending<br><?php echo $dollars; ?>
        <?php }?>
        </div>
        <div class="indicator"></div>
      </td>
      <?php
      $class = "";
      if (RequestUtilities::get("category") == 4) {
        $class = ' active';
      }
      $link = RequestUtil::prepareSpendingBottomNavFilter("spending_landing",4);
      $dollars = "<span class='dollars'>" . custom_number_formatter_format($dollars_by_cat[4],1,'$') . "</span>";

      ?>
      <td class="last<?php echo $class;?>">
        <div class="positioning">
        <?php if($dollars_by_cat[4] != 0 ){?>
          <a href="/<?php echo $link; ?>?expandBottomCont=true"><?php echo $count; ?>Other<br>Spending<br><?php echo $dollars; ?></a>
        <?php }else{?>
        <?php echo $count; ?>Other<br>Spending<br><?php echo $dollars; ?>
        <?php }?>
        </div>
        <div class="indicator"></div>
      </td>
    </tr>
    </tbody>
  </table>
</div>


