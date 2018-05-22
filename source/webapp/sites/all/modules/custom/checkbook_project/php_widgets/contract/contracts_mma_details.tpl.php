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


if ( RequestUtilities::getRequestParamValue("datasource") == "checkbook_oge") {
    $datasource ="/datasource/checkbook_oge";
    $oge_class = "oge-ma-details";
}else{
    $oge_class = "cb-ma-details";
}
if(RequestUtilities::getRequestParamValue("status")){
    $status = '/status/'.RequestUtilities::getRequestParamValue("status");
}else{
    $status = '/status/A';
}

$vendor_link = _checkbook_vendor_link($node->data[0]['vendor_id_checkbook_vendor_history'],TRUE);
$agency_link = _checkbook_agency_link($node->data[0]['agency_id_checkbook_agency'],TRUE);

$spending_link = "/spending/transactions/magid/" . RequestUtilities::getRequestParamValue("magid") . $datasource . "/newwindow";
if(!preg_match("/newwindow/",current_path())){
    $newwindowclass= 'class="new_window"';
}

if (RequestUtilities::getRequestParamValue("datasource") != "checkbook_oge") {
    $contract_number = $node->data[0]['contract_number'];
    $querySubVendorCount = "SELECT  COUNT(DISTINCT vendor_id) AS sub_vendor_count  FROM sub_agreement_snapshot
                            WHERE contract_number = '". $contract_number . "'
                            AND latest_flag = 'Y'
                            LIMIT 1";

    $results3 = _checkbook_project_execute_sql_by_data_source($querySubVendorCount,_get_current_datasource());
    if (!isset($res))
        $res = new stdClass();
    $res->data = $results3;
    $total_subvendor_count = $res->data[0]['sub_vendor_count'];
}
?>
<div class="contract-details-heading <?php echo $oge_class ;?>">
    <div class="contract-id">
        <h2 class='contract-title'>Contract ID: <span
                class="contract-number"><?php echo $node->data[0]['contract_number'];?></span></h2>
        <?php
        //$oge_agency_id = _checkbook_get_oge_agency_id($node->data[0]['vendor_id_checkbook_vendor_history']);



        if ( RequestUtilities::getRequestParamValue("datasource") == "checkbook_oge" && !preg_match('/newwindow/',$_GET['q']) && $node->data_source_amounts_differ) {
            $alt_txt = "This master agreement has additional information as a prime vendor.<br><br> Click this icon to view this contract as a prime vendor. ";
            $url="/contract_details/magid/" .  RequestUtilities::getRequestParamValue("magid") . "/doctype/MMA1/newwindow";
            echo "<div class='contractLinkNote contractIcon'><a class='new_window' href='". $url ."' alt='" . $alt_txt . "' >Open in New Window</a></div>";
        }elseif( !preg_match('/newwindow/',$_GET['q']) && _checkbook_is_oge_parent_contract($node->data[0]['contract_number'])  && $node->data_source_amounts_differ){
            $alt_txt = "This master agreement has additional information as an agency <br><br> Click this icon to view this contract as an agency ";
            $url="/contract_details/magid/" .  RequestUtilities::getRequestParamValue("magid") . "/doctype/MMA1/datasource/checkbook_oge/newwindow";
            echo "<div class='contractLinkNote contractIcon'><a class='new_window' href='". $url ."' alt='" . $alt_txt . "' >Open in New Window</a></div>";
        }
        ?>
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
            <?php echo custom_number_formatter_format($node->original_contract_amount, 2, '$');?>
            <div class="amount-title">Original Amount</div>
        </div>
        <div class="current-amount">
            <?php echo custom_number_formatter_format($node->maximum_spending_limit, 2, '$');?>
            <div class="amount-title">Current Amount</div>
        </div>
        <div class="total-contracts">
            <?php echo $node->total_child_contracts;?>
            <div class="amount-title">Assoc. Contracts</div>
        </div>
    </div>
    <div class="contract-information <?php echo $oge_class ;?>">
        <div class="contract-details">
            <h4>General Information</h4>
            <ul class="left">
                <?php if($datasource ==  null){?>
                    <li><span class="gi-list-item">Prime Vendor:</span> <a
                            href="<?php echo $vendor_link;?>"><?php echo $node->data[0]['legal_name_checkbook_vendor'];?></a></li>
                <?php }else{ ?>
                    <li><span class="gi-list-item">Prime Vendor:</span> <?php echo $node->data[0]['legal_name_checkbook_vendor'];?></li>
                <?php }?>
                <li><span class="gi-list-item">Purpose:</span> <?php echo $node->data[0]['description'];?></li>
                <li><span class="gi-list-item">Contract Type:</span> <?php echo $node->data[0]['agreement_type_name'];?></li>
                <?php if($datasource ==  null){?>
                    <li><span class="gi-list-item">Contracting Agency:</span> <a
                            href="<?php echo $agency_link;?>"><?php echo $node->data[0]['agency_name_checkbook_agency'];?></a>
                    </li>
                <?php }else{ ?>
                    <li><span class="gi-list-item">Contracting Agency:</span>
                        <?php echo $node->data[0]['agency_name_checkbook_agency'];?>
                    </li>
                <?php } ?>
                <li><span
                        class="gi-list-item">Award Method:</span> <?php echo $node->data[0]['award_method_name_checkbook_award_method'];?>
                </li>
                <?php
                if ( RequestUtilities::getRequestParamValue("datasource") != "checkbook_oge") {
                    ?>
                    <li><span class="gi-list-item">Version Number:</span> <?php echo $node->data[0]['document_version'];?></li>
                <?php
                }
                ?>
                <li><span
                        class="gi-list-item">FMS Document:</span> <?php echo $node->data[0]['document_code_checkbook_ref_document_code'];?>
                </li>
                <?php
                if ( RequestUtilities::getRequestParamValue("datasource") != "checkbook_oge") {
                    ?>
                    <li><span class="gi-list-item">Total Number of Sub Vendors:</span> <?php if($total_subvendor_count > 0) { echo $total_subvendor_count; } else { echo 'N/A'; } ?>
                    </li>
                <?php } ?>
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
                <?php
                if ( RequestUtilities::getRequestParamValue("datasource") != "checkbook_oge") {
                    ?>
                    <li><span
                            class="gi-list-item">Registration Date:</span> <?php echo format_string_to_date($node->data[0]['date_chckbk_date_id_rgstrd_date_id_chckbk_histr_master_agrmnt_2']);?></span>
                    </li>
                <?php
                }
                ?>
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
