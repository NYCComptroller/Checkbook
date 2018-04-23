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
<div class="activeExpenseContractNote toolTip">Includes all multiyear contracts whose end date is greater than today's date or completed in the current fiscal year</div>
<div class="nyc_totals_links">
  <table>
    <tbody>
    <tr>
      <?php
      $class = "";
      $is_active_expense_contracts = false;
      $is_active_expense_contracts = preg_match("/^contracts_landing/", $_GET['q']) & RequestUtilities::getRequestParamValue("status") == "A";
      if ($is_active_expense_contracts) {
        $class = ' class="active"';

      }
      $active_link = ContractURLHelper::prepareActRegContractsSliderFilter('contracts_landing', 'A');
      $count = "<span class='count'>" . number_format($node->data[0]['total_contracts']) . "</span>";
      $dollars = "<span class='dollars'>" . custom_number_formatter_format($node->data[0]['current_amount_sum'],1,'$') . "</span>";
      ?>
      <td<?php echo $class; ?>>
          <?php
          $class = ' class="positioning"';
          $is_edc_prime_vendor = RequestUtilities::getRequestParamValue("vendor") == "5616";
          if ($is_active_expense_contracts && $is_edc_prime_vendor) {
              $class = ' class="positioning activeExpenseContract"';
          }
          ?>
        <div<?php echo $class; ?>>
      <?php if($node->data[0]['total_contracts'] > 0 ){?>
          <a href="/<?php echo $active_link; ?>?expandBottomCont=true"><?php echo $count; ?><br>Active<br>Expense Contracts<br><?php echo $dollars; ?></a>
        <?php }else{?>
        <?php echo $count; ?><br>Active<br>Expense Contracts<br><?php echo $dollars; ?>
        <?php }?>
        </div>
        <div class="indicator"></div>
      </td>
      <?php
      $class = "";
      if (preg_match("/^contracts_landing/", $_GET['q']) & RequestUtilities::getRequestParamValue("status") == "R") {
        $class = ' class="active"';
      }
      $reg_link = ContractURLHelper::prepareActRegContractsSliderFilter('contracts_landing', 'R');
      $count = "<span class='count'>" . number_format($node->data[1]['total_contracts']) . "</span>";
      $dollars = "<span class='dollars'>" . custom_number_formatter_format($node->data[1]['current_amount_sum'],1,'$') . "</span>";
      ?>
      <td<?php echo $class; ?>>
        <div class="positioning">
      <?php if($node->data[1]['total_contracts'] > 0 ){?>
        <a href="/<?php echo $reg_link; ?>?expandBottomCont=true"><?php echo $count; ?><br>Registered<br>Expense Contracts<br><?php echo $dollars; ?></a>
        <?php }else{?>
        <?php echo $count; ?><br>Registered<br>Expense Contracts<br><?php echo $dollars; ?>
        <?php }?>
        </div>
        <div class="indicator"></div>
      </td>
      <?php
      $class = "";
      if (preg_match("/^contracts_revenue_landing/", $_GET['q']) & RequestUtilities::getRequestParamValue("status") == "A") {
        $class = ' class="active"';
      }
      $active_link = ContractURLHelper::prepareActRegContractsSliderFilter('contracts_revenue_landing', 'A');
      $count = "<span class='count'>" . number_format($node->data[2]['total_contracts']) . "</span>";
      $dollars = "<span class='dollars'>" . custom_number_formatter_format($node->data[2]['current_amount_sum'],1,'$') . "</span>";

      ?>
      <td<?php echo $class; ?>>
        <div class="positioning">
        <?php if($node->data[2]['total_contracts'] > 0 ){?>
          <a href="/<?php echo $active_link; ?>?expandBottomCont=true"><?php echo $count; ?><br>Active<br>Revenue Contracts<br><?php echo $dollars; ?></a>
        <?php }else{?>
        <?php echo $count; ?><br>Active<br>Revenue Contracts<br><?php echo $dollars; ?>
        <?php }?>
        </div>
        <div class="indicator"></div>
      </td>
      <?php
      $class = "";
      if (preg_match("/^contracts_revenue_landing/", $_GET['q']) & RequestUtilities::getRequestParamValue("status") == "R") {
        $class = ' class="active"';
      }
      $reg_link = ContractURLHelper::prepareActRegContractsSliderFilter('contracts_revenue_landing', 'R');
      $count = "<span class='count'>" . number_format($node->data[3]['total_contracts']) . "</span>";
      $dollars = "<span class='dollars'>" . custom_number_formatter_format($node->data[3]['current_amount_sum'],1,'$') . "</span>";

      ?>
      <td<?php echo $class; ?>>
        <div class="positioning">
      <?php if($node->data[3]['total_contracts'] > 0 ){?>
          <a href="/<?php echo $reg_link; ?>?expandBottomCont=true"><?php echo $count; ?><br>Registered<br>Revenue Contracts<br><?php echo $dollars; ?></a>
        <?php }else{?>
        <?php echo $count; ?><br>Registered<br>Revenue Contracts<br><?php echo $dollars; ?>
        <?php }?>
        </div>
        <div class="indicator"></div>
      </td>
      <?php
      $class = "";
      if (preg_match("/^contracts_pending_exp_landing/", $_GET['q'])) {
        $class = ' class="active"';
      }
      $pending_exp_link = ContractURLHelper::preparePendingContractsSliderFilter('contracts_pending_exp_landing');
      $count = "<span class='count'>" . number_format($node->data[5]['total_contracts']) . "</span>";
      $dollars = "<span class='dollars'>" . custom_number_formatter_format($node->data[4]['total_contract_amount'],1,'$') . "</span>";
      ?>
      <td<?php echo $class;?>>
        <div class="positioning">
      <?php if($node->data[5]['total_contracts'] > 0 ){?>
          <a href="/<?php echo $pending_exp_link; ?>?expandBottomCont=true"><?php echo $count; ?><br>Pending<br>Expense Contracts<br><?php echo $dollars; ?></a>
        <?php }else{?>
        <?php echo $count; ?><br>Pending<br>Expense Contracts<br><?php echo $dollars; ?>
        <?php }?>
        </div>
        <div class="indicator"></div>
      </td>
      <?php
      $class = "";
      if (preg_match("/^contracts_pending_rev_landing/", $_GET['q'])) {
        $class = ' active';
      }
      $pending_rev_link = ContractURLHelper::preparePendingContractsSliderFilter('contracts_pending_rev_landing');
      $count = "<span class='count'>" . number_format($node->data[7]['total_contracts']) . "</span>";
      $dollars = "<span class='dollars'>" . custom_number_formatter_format($node->data[6]['total_contract_amount'],1,'$') . "</span>";

      ?>
      <td class="last<?php echo $class;?>">
        <div class="positioning">
        <?php if($node->data[7]['total_contracts'] > 0 ){?>
          <a href="/<?php echo $pending_rev_link; ?>?expandBottomCont=true"><?php echo $count; ?><br>Pending<br>Revenue Contracts<br><?php echo $dollars; ?></a>
        <?php }else{?>
        <?php echo $count; ?><br>Pending<br>Revenue Contracts<br><?php echo $dollars; ?>
        <?php }?>
        </div>
        <div class="indicator"></div>
      </td>
    </tr>
    </tbody>
  </table>
</div>



