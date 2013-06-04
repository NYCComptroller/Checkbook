<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
?>
<div class="nyc_totals_links">
  <table>
    <tbody>
    <tr>
      <?php
      $class = "";
      if (preg_match("/^contracts_landing/", $_GET['q']) & _getRequestParamValue("status") == "A") {
        $class = ' class="active"';
      }
      $active_link = ContractURLHelper::prepareActRegContractsSliderFilter('contracts_landing', 'A');
      $count = "<span class='count'>" . number_format($node->data[0]['total_contracts']) . "</span>";
      $dollars = "<span class='dollars'>" . custom_number_formatter_format($node->data[0]['current_amount_sum'],1,'$') . "</span>";      
      ?>
      <td<?php echo $class; ?>>
        <div class="positioning">
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
      if (preg_match("/^contracts_landing/", $_GET['q']) & _getRequestParamValue("status") == "R") {
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
      if (preg_match("/^contracts_revenue_landing/", $_GET['q']) & _getRequestParamValue("status") == "A") {
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
      if (preg_match("/^contracts_revenue_landing/", $_GET['q']) & _getRequestParamValue("status") == "R") {
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


