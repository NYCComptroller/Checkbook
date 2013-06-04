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
 
      foreach($node->data as $key=>$row){
        $dollars_by_cat[$row['category_category']] = $row['check_amount_sum'];
        $total +=  $row['check_amount_sum'];
      }
      $class = "";
      if ( _getRequestParamValue("category") == "") {
        $class = ' class="active"';
      }
      $link = RequestUtil::preparePayrollBottomNavFilter("spending_landing",null);
      $dollars = "<span class='dollars'>" . custom_number_formatter_format($total,1,'$') . "</span>";      
      ?>
      <td<?php echo $class; ?>>
        <div class="positioning">
      <?php if($total != 0 ){?>        
          <a href="/<?php echo $link; ?>?expandBottomCont=true"><?php echo $count; ?>Total<br>Spending<br><?php echo $dollars; ?></a>
        <?php }else{?>
        <?php echo $count; ?>Total<br>Spending<br><?php echo $dollars; ?>
        <?php }?>           
        </div>
        <div class="indicator"></div>
      </td>
      <?php
      $class = "";
      if (_getRequestParamValue("category") == 2) {
        $class = ' class="active"';
      }
      $link = RequestUtil::preparePayrollBottomNavFilter("spending_landing",2);
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
      if (_getRequestParamValue("category") == 3) {
        $class = ' class="active"';
      }
      $link = RequestUtil::preparePayrollBottomNavFilter("spending_landing",3);      
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
      if (_getRequestParamValue("category") == 1) {
        $class = ' class="active"';
      }
      $link = RequestUtil::preparePayrollBottomNavFilter("spending_landing",1);
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
      if (_getRequestParamValue("category") == 5) {
        $class = ' class="active"';
      }
      $link = RequestUtil::preparePayrollBottomNavFilter("spending_landing",5);      
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
      if (_getRequestParamValue("category") == 4) {
        $class = ' active';
      }
      $link = RequestUtil::preparePayrollBottomNavFilter("spending_landing",4);
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


