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


$url = '  ' . RequestUtil::getCurrentPageUrl();

    if($node->data[0]['document_code_checkbook_ref_document_code'] == 'RCT1'){
        $agency_link = '/contracts_pending_rev_landing/year/' . CheckbookDateUtil::getCurrentFiscalYearId() . '/yeartype/B/agency/'. $node->data[0]['agency_id_checkbook_agency'] . '?expandBottomCont=true';
        $vendor_link = '/contracts_pending_rev_landing/year/' . CheckbookDateUtil::getCurrentFiscalYearId() . '/yeartype/B/vendor/'.$node->data[0]['vendor_vendor'] .'?expandBottomCont=true';
    }else{
        if (_is_mwbe_vendor(RequestUtilities::get("agid")) || _is_mwbe_vendor(RequestUtilities::get("magid")) || stripos($url,'/dashboard/mp')) {
            $mwbe = RequestUtilities::_appendMWBESubVendorDatasourceUrlParams().'/dashboard/mp';
        }
        else{
            $mwbe='';
        }
        $agency_link = '/contracts_pending_exp_landing/year/' . CheckbookDateUtil::getCurrentFiscalYearId() . '/yeartype/B/agency/'. $node->data[0]['agency_id_checkbook_agency'] .$mwbe. '?expandBottomCont=true';
        $vendor_link = '/contracts_pending_exp_landing/year/' . CheckbookDateUtil::getCurrentFiscalYearId() . '/yeartype/B/vendor/'.$node->data[0]['vendor_vendor'] .$mwbe.'?expandBottomCont=true';
    }

if(isset($node->original_master_agreement_id)){
    if(!preg_match("/newwindow/",current_path())){
        $master_link_html = '<span class="master-contract-link">Parent Contract: <a class="bottomContainerReload" href=/panel_html/contract_transactions/contract_details/magid/' .  $node->original_master_agreement_id . '/doctype/' . $node->document_code. $datasource . ' class=\"bottomContainerReload\">' .  $node->contract_number . '</a></span>';
    }
    else
    {
        $master_link_html = '<span class="master-contract-link">Parent Contract: '.  $node->contract_number . '</span>';
    }
}

?>
<div class="contract-details-heading" style="margin-bottom: 10px;">
  <div class="contract-id" >
    <h2 class='contract-title' style="margin-bottom: 10px;">Contract ID: <span
      class="contract-number"><?php echo $node->data[0]['contract_number'];?></span></h2>
    <?php if($node->data[0]['parent_contract_number']){
        echo $master_link_html;
    } ?>
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
      <li><span class="gi-list-item">Prime Vendor:</span> <a
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
