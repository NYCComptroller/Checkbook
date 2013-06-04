<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
?>
<?php

  if($node->data[0]['vendor_id']){
    $vendor_link = '/contracts_landing/status/A/year/' . _getCurrentYearID() . '/yeartype/B/vendor/'
    . $node->data[0]['vendor_id'] . '?expandBottomCont=true';
  }
  else{
      if($node->data[0]['document_code_checkbook_ref_document_code'] == 'RCT1')
          $vendor_link = '/contracts_pending_rev_landing/year/' . _getCurrentYearID() . '/yeartype/B/vendor/'.$node->data[0]['vendor_vendor'] .'?expandBottomCont=true';
      else
          $vendor_link = '/contracts_pending_exp_landing/year/' . _getCurrentYearID() . '/yeartype/B/vendor/'.$node->data[0]['vendor_vendor'] .'?expandBottomCont=true';

  }
  $agency_link = '/contracts_landing/status/A/year/' . _getCurrentYearID() . '/yeartype/B/agency/'
    . $node->data[0]['agency_id_checkbook_agency'] . '?expandBottomCont=true';
?>
<div class="contract-details-heading">
  <div class="contract-id">
    <h2 class='contract-title'>Contract ID: <span
      class="contract-number"><?php echo $node->data[0]['contract_number'];?></span></h2>
    <?php if($node->data[0]['parent_contract_number']){ ?>
        <h3>Parent Contract: 
            <span><?php echo $node->data[0]['parent_contract_number'];?></span></h3>
    <?php } ?>
  </div>
  <div class="dollar-amounts">
    <div class="original-amount">
      <?php echo custom_number_formatter_format($node->data[0]['original_contract_amount'], 2, '$');?>
      <div class="amount-title">Original Amount</div>
    </div>
    <div class="current-amount">
      <?php echo custom_number_formatter_format($node->data[0]['maximum_spending_limit'], 2, '$');?>
      <div class="amount-title">Current Amount</div>
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
        class="gi-list-item">Start Date:</span> <?php echo format_string_to_date($node->data[0]['date_chckbk_dat_id_effctv_bgn_date_id_chckbk_hstr_mstr_agrmnt_0']);?>
      </li>
      <li><span
        class="gi-list-item">End Date:</span> <?php echo format_string_to_date($node->data[0]['date_chckbk_date_id_effctv_end_dat_id_chckbk_hstr_mstr_agrmnt_1']);?>
      </li>
      <li><span class="gi-list-item">APT PIN:</span> <?php echo $node->data[0]["board_approved_award_no"];?></li>
      <li><span class="gi-list-item">PIN:</span> <?php echo $node->data[0]['tracking_number'];?></li>
    </ul>
  </div>
  <div class="contract-vendor-details">
    <?php
    $nid = 546;
    $node = node_load($nid);
    node_build_content($node);
    print drupal_render($node->content);
    ?>
  </div>
</div>
