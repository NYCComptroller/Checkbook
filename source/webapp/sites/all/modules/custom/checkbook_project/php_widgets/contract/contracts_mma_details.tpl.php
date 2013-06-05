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
<?php


if (_getRequestParamValue("doctype") == "RCT1") {
  $vendor_link = '/contracts_revenue_landing/status/A/year/' . _getCurrentYearID() . '/yeartype/B/vendor/'
    . $node->data[0]['vendor_id_checkbook_vendor_history'] . '?expandBottomCont=true';
  $agency_link = '/contracts_revenue_landing/status/A/year/' . _getCurrentYearID() . '/yeartype/B/agency/'
    . $node->data[0]['agency_id_checkbook_agency'] . '?expandBottomCont=true';
}
else {
  $vendor_link = '/contracts_landing/status/A/year/' . _getCurrentYearID() . '/yeartype/B/vendor/'
    . $node->data[0]['vendor_id_checkbook_vendor_history'] . '?expandBottomCont=true';
  $agency_link = '/contracts_landing/status/A/year/' . _getCurrentYearID() . '/yeartype/B/agency/'
    . $node->data[0]['agency_id_checkbook_agency'] . '?expandBottomCont=true';
}

$spending_link = "/spending/transactions/magid/" . _getRequestParamValue("magid") . "/newwindow";
if(!preg_match("/newwindow/",current_path())){
  $newwindowclass= 'class="new_window"';
}
?>
<div class="contract-details-heading">
  <div class="contract-id">
    <h2 class='contract-title'>Contract ID: <span
      class="contract-number"><?php echo $node->data[0]['contract_number'];?></span></h2>

  </div>
  <div class="dollar-amounts">
    <?php if(!preg_match('/RCT1/',$node->data[0]['contract_number'])){?>
    <div class="spent-to-date">
      <a <?php echo $newwindowclass ?>
         href="<?php echo $spending_link; ?>"><?php echo custom_number_formatter_format($node->spent_amount, 2, "$");?></a>

      <div class="amount-title">Spent to<br/>Date</div>
    </div>
    <?php }?>
    <div class="original-amount">
      <?php echo custom_number_formatter_format($node->data[0]['original_contract_amount'], 2, '$');?>
      <div class="amount-title">Original Amount</div>
    </div>
    <div class="current-amount">
      <?php echo custom_number_formatter_format($node->data[0]['maximum_spending_limit'], 2, '$');?>
      <div class="amount-title">Current Amount</div>
    </div>
    <div class="total-contracts">
      <?php echo $node->total_child_contracts;?>
      <div class="amount-title">Assoc. Contracts</div>
    </div>
  </div>
</div>
<div class="contract-information">
  <div class="contract-details">
    <h4>General Information</h4>
    <ul class="left">
      <li><span class="gi-list-item">Vendor:</span> <a
        href="<?php echo $vendor_link;?>"><?php echo $node->data[0]['legal_name_checkbook_vendor'];?></a></li>
      <li><span class="gi-list-item">Purpose:</span> <?php echo $node->data[0]['description'];?></li>
      <li><span class="gi-list-item">Contract Type:</span> <?php echo $node->data[0]['agreement_type_name'];?></li>
      <li><span class="gi-list-item">Contracting Agency:</span> <a
        href="<?php echo $agency_link;?>"><?php echo $node->data[0]['agency_name_checkbook_agency'];?></a>
      </li>
      <li><span
        class="gi-list-item">Award Method:</span> <?php echo $node->data[0]['award_method_name_checkbook_award_method'];?>
      </li>
      <li><span class="gi-list-item">Version Number:</span> <?php echo $node->data[0]['document_version'];?></li>
      <li><span
        class="gi-list-item">FMS Document:</span> <?php echo $node->data[0]['document_code_checkbook_ref_document_code'];?>
      </li>
    </ul>
    <ul class="right">
      <li><span
        class="gi-list-item">Number of Solicitations per Contract:</span> <?php echo $node->data[0]['number_solicitation'];?>
      </li>
      <li><span
        class="gi-list-item">Number of Responses per Solicitation:</span> <?php echo $node->data[0]['number_responses'];?>
      </li>
      <li><span
        class="gi-list-item">Start Date:</span> <?php echo format_string_to_date($node->data[0]['date_chckbk_dat_id_effctv_bgn_date_id_chckbk_hstr_mstr_agrmnt_0']);?>
      </li>
      <li><span
        class="gi-list-item">End Date:</span> <?php echo format_string_to_date($node->data[0]['date_chckbk_date_id_effctv_end_dat_id_chckbk_hstr_mstr_agrmnt_1']);?>
      </li>
      <li><span
        class="gi-list-item">Registration Date:</span> <?php echo format_string_to_date($node->data[0]['date_chckbk_date_id_rgstrd_date_id_chckbk_histr_master_agrmnt_2']);?></span>
      </li>
      <li><span class="gi-list-item">APT PIN:</span> <?php echo $node->data[0]["board_approved_award_no"];?></li>
      <li><span class="gi-list-item">PIN:</span> <?php echo $node->data[0]['tracking_number'];?></li>
    </ul>
  </div>
  <div class="contract-vendor-details">
    <?php
    $nid = 425;
    $node = node_load($nid);
    node_build_content($node);
    print drupal_render($node->content);
    ?>
  </div>
</div>
<!--<div class="associated-contracts">
  <?php
/*  $nid = 437;
  $node = node_load($nid);
  node_build_content($node);
  print drupal_render($node->content);
  */?>
</div>-->