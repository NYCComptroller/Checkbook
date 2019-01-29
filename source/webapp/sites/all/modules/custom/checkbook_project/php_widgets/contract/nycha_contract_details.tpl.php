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

//var_dump($node->data);


$contract = $node->data[0];

//if ( RequestUtilities::get("datasource") == "checkbook_oge") {
//    $datasource ="/datasource/checkbook_oge";
//    $oge_class = "oge-ma-details";
//}else{
//    $oge_class = "cb-ma-details";
//}
//if(RequestUtilities::get("status")){
//    $status = '/status/'.RequestUtilities::get("status");
//}else{
//    $status = '/status/A';
//}

$vendor_link = '';//_checkbook_vendor_link($contract['vendor_id_checkbook_vendor_history'],TRUE);
$agency_link = '';//_checkbook_agency_link($contract['agency_id_checkbook_agency'],TRUE);

$spending_link = '';//"/spending/transactions/magid/" . RequestUtilities::get("magid") . $datasource . "/newwindow";
$newwindowclass = '';
//if(!preg_match("/newwindow/",current_path())){
//    $newwindowclass= 'class="new_window"';
//}

/*
 * array(1) {
  [0]=>
  array(17) {
    ["contractid"]=>
    string(9) "BA1404493"
    ["vendor_id"]=>
    string(6) "807055"
    ["vendor_name"]=>
    string(41) "APTIM ENVIRONMENTAL & INFRASTRUCTURE, INC"
    ["purpose"]=>
    string(70) "CPD-Program Mgmt. Services Related to Hurricane Sandy Long Term Repair"
    ["award_method_id"]=>
    string(2) "60"
    ["award_method_name"]=>
    string(15) "COMPETETIVE RFP"
    ["number_of_solicitations"]=>
    NULL
    ["response_to_solicitation"]=>
    NULL
    ["spend_to_date"]=>
    string(11) "81011295.84"
    ["award_size_name"]=>
    string(23) "Greater than $1 Million"
    ["award_size_id"]=>
    string(1) "1"
    ["original_amount"]=>
    string(11) "14132443.96"
    ["total_amount"]=>
    string(12) "146618596.34"
    ["start_date"]=>
    string(10) "2014-03-14"
    ["end_date"]=>
    string(10) "2022-06-30"
    ["approved_date"]=>
    string(10) "2018-01-25"
    ["revision_count"]=>
    string(1) "8"
  }
}
 */

?>
<div class="content clearfix">
    <div class="contract-id">
        <h2 class='contract-title'>Contract ID: <span
                class="contract-number"><?= $contract['contract_id']; ?></span></h2>
        <?php
        //$oge_agency_id = _checkbook_get_oge_agency_id($contract['vendor_id_checkbook_vendor_history']);


        if (RequestUtilities::get("datasource") == "checkbook_oge" && !preg_match('/newwindow/', $_GET['q']) && $node->data_source_amounts_differ) {
            $alt_txt = "This master agreement has additional information as a prime vendor.<br><br> Click this icon to view this contract as a prime vendor. ";
            $url = "/contract_details/magid/" . RequestUtilities::get("magid") . "/doctype/MMA1/newwindow";
            echo "<div class='contractLinkNote contractIcon'><a class='new_window' href='" . $url . "' alt='" . $alt_txt . "' >Open in New Window</a></div>";
        } elseif (!preg_match('/newwindow/', $_GET['q']) && _checkbook_is_oge_parent_contract($contract['contract_number']) && $node->data_source_amounts_differ) {
            $alt_txt = "This master agreement has additional information as an agency <br><br> Click this icon to view this contract as an agency ";
            $url = "/contract_details/magid/" . RequestUtilities::get("magid") . "/doctype/MMA1/datasource/checkbook_oge/newwindow";
            echo "<div class='contractLinkNote contractIcon'><a class='new_window' href='" . $url . "' alt='" . $alt_txt . "' >Open in New Window</a></div>";
        }
        ?>
    </div>
    <div class="dollar-amounts">
        <div class="spent-to-date">
            <a <?= $newwindowclass ?>
                href="<?= $spending_link; ?>"><?= custom_number_formatter_format($contract['spend_to_date'], 2, "$"); ?></a>
            <div class="amount-title">Spent to<br/>Date</div>
        </div>
        <div class="original-amount">
            <?= custom_number_formatter_format($contract['original_amount'], 2, '$'); ?>
            <div class="amount-title">Original Amount</div>
        </div>
        <div class="current-amount">
            <?= custom_number_formatter_format($contract['spend_to_date'], 2, '$'); ?>
            <div class="amount-title">Current Amount</div>
        </div>
    </div>
    <div class="contract-information <?= $oge_class; ?>">
        <div class="contract-details">
            <h4>General Information</h4>
            <ul class="left">
                <li>
                    <span class="gi-list-item">Vendor:</span>
                    &nbsp;<a href="<?= $vendor_link; ?>"><?= $contract['vendor_name']; ?></a></li>
                <li>
                    <span class="gi-list-item">Purpose:</span>
                    &nbsp;<?= $contract['purpose']; ?>
                </li>
                <li>
                    <span class="gi-list-item">Revision Number:</span>
                    &nbsp;<?= $contract['revision_number'] ?>
                </li>
            </ul>
            <ul class="right">
                <li>
                    <span class="gi-list-item">Start Date:</span>
                    &nbsp;<?= format_string_to_date($contract['start_date']); ?>
                </li>
                <li>
                    <span class="gi-list-item">End Date:</span>
                    &nbsp;<?= format_string_to_date($contract['end_date']); ?>
                </li>
                <li>
                    <span class="gi-list-item">Registration Date:</span>
                    &nbsp;<?= format_string_to_date($contract['approved_date']); ?>
                </li>
            </ul>
        </div>
        <div class="contract-vendor-details">
            <h4>
                Vendor Information
            </h4>
            <ul class="left">
                <li><span class="gi-list-item">Vendor:</span>&nbsp;<a
                        href="<?= $vendor_link; ?>"><?= $contract['vendor_name'] ?></a></li>
                <li><span class="gi-list-item">Address:</span>
                    &nbsp;<?= $contract['address_line1'] ?>
                    <br/><?= $contract['address_line2'] ?>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="content clearfix nycha-contract-history">

    <div>
        <h3>
            Contract History
        </h3>

        <table class="outerTable nycha-c-history">
            <thead>
            <tr>
                <th class='number thVNum'>
                    <div><span>Revision<br/>Number</span></div>
                </th>
                <th class='text thLastMDate'>
                    <div><span>Revision<br />Approved&nbsp;Date</span></div>
                </th>
                <th class='number thTotalAmt'>
                    <div><span>Revision <br />Total&nbsp;Amount</span></div>
                </th>
                <th class='number thOrigAmt'>
                    <div><span>Original<br/>Amount</span></div>
                </th>
            </tr>
            </thead>
            <tbody>
            <?php
            $i = 0;
            foreach ($node->contract_history as $contract_revision):?>
                <tr class="inner">
                    <td class='number thVNum'>
                        <div><?= (int)$contract_revision['revision_number'] ?></div>
                    </td>
                    <td class='text thEndDate'>
                        <div><?= format_string_to_date($contract_revision['revision_approved_date']) ?></div>
                    </td>
                    <td class='number thTotalAmt'>
                        <?= custom_number_formatter_format($contract_revision['revision_total_amount'], 2, '$'); ?>
                    </td>
                    <td class='number thOrigAmt'>
                        <?= custom_number_formatter_format($contract_revision['original_amount'], 2, '$'); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script type="text/javascript">
        contractsAddPadding(jQuery('div.nycha-contract-history'));
        jQuery('table.nycha-c-history').dataTable({
            "bFilter": false,
            "bPaginate": false,
            "bInfo": false
        });
    </script>
    <!-- END OUTPUT from 'sites/all/modules/custom/checkbook_project/php_widgets/contract/contracts_ma_history.tpl.php' -->

</div>

<div class="clearfix">
    <div class="links node-links clearfix"></div>

</div>


<?php /*
        <div class="contract-vendor-details">

            $nid = 425;
            $node = node_load($nid);
            node_build_content($node);
            print drupal_render($node->content);
        </div>
        */ ?>
<!--<div class="associated-contracts">
  <?php
/*  $nid = 437;
  $node = node_load($nid);
  node_build_content($node);
  print drupal_render($node->content);
  */ ?>
</div>
