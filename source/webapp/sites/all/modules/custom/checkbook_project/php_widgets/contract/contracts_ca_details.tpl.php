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


if (RequestUtilities::getRequestParamValue("datasource") == "checkbook_oge") {
  $oge_class = "oge-ca-details";
} else {
  $oge_class = "cb-ca-details";
}

$vendor_link = _checkbook_vendor_link($node->data[0]['vendor_id_checkbook_vendor_history']);
$agency_link = _checkbook_agency_link($node->data[0]['agency_id_checkbook_agency']);

$datasource = '';
if ('checkbook_oge' == RequestUtilities::getRequestParamValue("datasource")) {
  $datasource = "/datasource/checkbook_oge";
}

$spending_link = "/spending/transactions/agid/" . RequestUtilities::getRequestParamValue("agid") . $datasource.  "/newwindow";
?>
<div class="contract-details-heading <?php echo $oge_class ;?>">
  <div class="contract-id">
    <h2 class="contract-title">Contract ID: <span
      class="contract-number"><?php echo $node->data[0]['contract_number'];?></span></h2>
	<?php
    if ( RequestUtilities::getRequestParamValue("datasource") == "checkbook_oge" && !preg_match('/newwindow/',$_GET['q']) && $node->data_source_amounts_differ) {
			$alt_txt = "This contract agreement has addtional information as a prime vendor. <br><br> Click this icon to view this contract as a prime vendor. ";
			$url="/contract_details/agid/" .  RequestUtilities::getRequestParamValue("agid") . "/doctype/CTA1/newwindow";
			echo "<div class='contractLinkNote contractIcon'><a class='new_window' href='". $url ."' alt='" . $alt_txt . "'  >View as Vendor</a></div>";
    }elseif( !preg_match('/newwindow/',$_GET['q']) && _checkbook_is_oge_contract($node->data[0]['contract_number']) && $node->data_source_amounts_differ){
			$alt_txt = "This contract agreement has addtional information as agency <br><br> Click this icon to view this contract as an agency ";
			$url="/contract_details/agid/" .  RequestUtilities::getRequestParamValue("agid") . "/doctype/CTA1/datasource/checkbook_oge/newwindow";
			echo "<div class='contractLinkNote contractIcon'><a class='new_window' href='". $url ."' alt='" . $alt_txt . "'  >View as agency</a></div>";
		}
	?>
<?php

if(isset($node->magid)){
if(!preg_match("/newwindow/",current_path())){
    $master_link_html = '<span class="master-contract-link">Parent Contract: <a class="bottomContainerReload" href=/panel_html/contract_transactions/contract_details/magid/' .  $node->magid . '/doctype/' . $node->document_code. $datasource . ' class=\"bottomContainerReload\">' .  $node->contract_number . '</a></span>';
}
else
{
    $master_link_html = '<span class="master-contract-link">Parent Contract: '.  $node->contract_number . '</span>';
}
  echo  $master_link_html;
}


if(!preg_match("/newwindow/",current_path())){
  $newwindowclass= 'class="new_window"';
}

$original_contract_amount =  ( RequestUtilities::getRequestParamValue("datasource") == "checkbook_oge") ? $node->original_contract_amount:$node->data[0]['original_contract_amount'];
$maximum_contract_amount =  ( RequestUtilities::getRequestParamValue("datasource") == "checkbook_oge") ? $node->maximum_contract_amount:$node->data[0]['maximum_contract_amount'];

?>

  </div>
  <div class="dollar-amounts">
    <div class="spent-to-date">
      <?php if(!preg_match('/newwindow/',$_GET['q'])){ ?>
      <a <?php echo $newwindowclass ?>
         href="<?php echo $spending_link; ?>"><?php echo custom_number_formatter_format($node->spent_amount, 2, "$");?></a>
     <?php } else { ?>
        <?php echo custom_number_formatter_format($node->spent_amount, 2, "$");?>
        <?php } ?>
      <div class="amount-title">Spent
        to Date
      </div>
    </div>
    <div
      class="original-amount"><?php echo custom_number_formatter_format($original_contract_amount, 2, '$');?>
      <div class="amount-title">Original
        Amount
      </div>
    </div>
    <div
      class="current-amount"><?php echo custom_number_formatter_format($maximum_contract_amount, 2, '$');?>
      <div class="amount-title">Current
        Amount
      </div>
    </div>
</div>

</div>

<div class="contract-information <?php echo $oge_class ;?>">
  <div class="contract-details <?php echo ( RequestUtilities::getRequestParamValue("datasource") == "checkbook_oge")? "oge-cta-contract ":"" ; ?>">
    <h4>General Information</h4>
    <ul class="left">
    <?php
    if ( RequestUtilities::getRequestParamValue("datasource") == "checkbook_oge") {
    ?>
        <li><span class="gi-list-item">Contracting Agency:</span>
            <?php echo $node->data[0]['agency_name_checkbook_agency'];?></li>
        <?php
    }else{
    ?>
      <li><span class="gi-list-item">Prime Vendor:</span>
          <?php if(!preg_match('/newwindow/',$_GET['q'])){ ?>
            <a href="<?php echo $vendor_link;?>"><?php echo $node->data[0]['legal_name_checkbook_vendor'];?></a>
          <?php }else{ echo $node->data[0]['legal_name_checkbook_vendor']; } ?>
          </span></li>
    <?php
	}
    ?>
      <li><span class="gi-list-item">Purpose:</span> <?php echo $node->data[0]['description'];?></li>
      <li><span class="gi-list-item">Contract Type:</span> <?php echo $node->data[0]['agreement_type_name'];?></li>

     <?php
    if ( RequestUtilities::getRequestParamValue("datasource") != "checkbook_oge") {
    ?>
      <li><span class="gi-list-item">Contracting Agency:</span>
          <?php if(!preg_match('/newwindow/',$_GET['q'])){ ?>
            <a href="<?php echo $agency_link;?>"><?php echo $node->data[0]['agency_name_checkbook_agency'];?></a>
          <?php }else{ echo $node->data[0]['agency_name_checkbook_agency'];} ?>
      </li>
        <?php
    }
    ?>
      <li><span
        class="gi-list-item">Award Method:</span> <?php echo $node->data[0]['award_method_name_checkbook_award_method'];?>
      </li>
      <?php if ( RequestUtilities::getRequestParamValue("datasource") != "checkbook_oge") { ?>
      <li><span class="gi-list-item">Version Number:</span> <?php echo $node->data[0]['document_version'];?></li>
      <?php } ?>
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
      <li><span class="gi-list-item">Start
          Date:</span> <?php echo format_string_to_date($node->data[0]['date_chckbk_date_id_effctv_begin_date_id_chckbk_histor_agrmnt_0']);?>
      </li>
      <li><span class="gi-list-item">End
          Date:</span> <?php echo format_string_to_date($node->data[0]['date_checkbk_date_id_effctv_end_date_id_chckbk_history_agrmnt_1']);?>
      </li>
      <?php if ( RequestUtilities::getRequestParamValue("datasource") != "checkbook_oge") { ?>
      <li><span class="gi-list-item">Registration
          Date:</span> <?php echo format_string_to_date($node->data[0]['date_chckbk_date_id_rgstrd_date_id_checkbook_history_agreemnt_2']);?>
      </li>
      <?php } ?>
      <li><span class="gi-list-item">APT PIN:</span> <?php echo $node->data[0]["brd_awd_no"];?></li>
      <li><span class="gi-list-item">PIN:</span> <?php echo $node->data[0]['tracking_number'];?></li>
    </ul>
  </div>
    <?php
    if ( RequestUtilities::getRequestParamValue("datasource") != "checkbook_oge") {
            echo '<div class="contract-vendor-details">';
	    $nid = 439;
	    $node = node_load($nid);
	    node_build_content($node);
	    print drupal_render($node->content);
	    echo '</div>';
    }
    ?>

</div>



  <script type="text/javascript">
  contractsAddPadding(jQuery('.oge-cta-details'));
</script>
