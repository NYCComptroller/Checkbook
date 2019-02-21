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


$contract = $node->data;

?>
<div class="content clearfix">
    <div class="contract-details-heading cb-ma-details">
        <div class="contract-id">
            <h2 class='contract-title'>Contract ID: <span
                    class="contract-number"><?= htmlentities($contract['contract_id']) ?></span></h2>
        </div>
        <div class="dollar-amounts">
            <div class="spent-to-date">
                <?= custom_number_formatter_format($contract['spend_to_date'], 2, "$"); ?>
                <div class="amount-title">Spent to<br/>Date</div>
            </div>
            <div class="original-amount">
                <?= custom_number_formatter_format($contract['original_amount'], 2, '$'); ?>
                <div class="amount-title">Original Amount</div>
            </div>
            <div class="current-amount">
                <?= custom_number_formatter_format($contract['total_amount'], 2, '$'); ?>
                <div class="amount-title">Current Amount</div>
            </div>
            <?php if ($node->total_associated_releases): ?>
                <div class="total-contracts">
                    <?= intval($node->total_associated_releases) ?>
                    <div class="amount-title">Assoc. Releases</div>
                </div>
            <?php endif; ?>
        </div>
        <div class="contract-information">
            <div class="contract-details">
                <h4>General Information</h4>
                <ul class="left">
                    <li>
                        <span class="gi-list-item">Vendor:</span>
                        &nbsp;<a href="<?= NychaContractsUrlService::vendorUrl($contract['vendor_id']) ?>">
                            <?= htmlentities($contract['vendor_name']) ?></a></li>
                    <li>
                        <span class="gi-list-item">Purpose:</span>
                        &nbsp;<?= htmlentities($contract['purpose']) ?>
                    </li>
                    <li>
                        <span class="gi-list-item">Contract Type:</span>
                        &nbsp;<?= htmlentities($contract['contract_type_descr']) ?>
                    </li>
                    <li>
                        <span class="gi-list-item">Contracting Agency:</span>
                        &nbsp;<a href="<?= NychaContractsUrlService::agencyUrl() ?>">
                            <?= htmlentities($contract['agency_name']) ?>
                        </a>
                    </li>
                    <li><span class="gi-list-item">Award Method:</span>
                        &nbsp;<?= htmlentities($contract['award_method_name']) ?>

                    </li>

                    <li>
                        <span class="gi-list-item">Version Number:</span>
                        &nbsp;<?= htmlentities($contract['revision_number']) ?>
                    </li>
                </ul>
                <ul class="right">
                    <?php if ($contract['start_date']): ?>
                        <li>
                        <span class="gi-list-item">Start Date:</span>
                        &nbsp;<?= format_string_to_date($contract['start_date']); ?>
                        </li><?php endif; ?>
                    <?php if ($contract['end_date']): ?>
                        <li>
                        <span class="gi-list-item">End Date:</span>
                        &nbsp;<?= format_string_to_date($contract['end_date']); ?>
                        </li><?php endif; ?>
                    <li>
                        <span class="gi-list-item">Approved Date:</span>
                        &nbsp;<?= format_string_to_date($node->contractPO ? $contract['release_approved_date'] : $contract['revision_approved_date']); ?>
                    </li>
                    <li>
                        <span class="gi-list-item">Cancelled Date:</span>

                    </li>
                    <li>
                        <span class="gi-list-item">Transaction Status:</span>
                        &nbsp;<?= 'Approved'; ?>
                    </li>
                    <li>
                        <span class="gi-list-item">Commodity Category:</span>
                        &nbsp;<?= htmlentities($contract['category_descr']) ?>
                    </li>
                </ul>
            </div>
            <div class="contract-vendor-details">
                <h4>
                    Vendor Information
                </h4>
                <ul class="left">
                    <li>
                        <span class="gi-list-item">Vendor:</span>
                        &nbsp;<a href="<?= NychaContractsUrlService::vendorUrl($contract['vendor_id']) ?>">
                            <?= htmlentities($contract['vendor_name']) ?></a></li>
                    <li>
                        <span class="gi-list-item">Address:</span>
                        &nbsp;<?= htmlentities($contract['address_line1']) ?> <?= htmlentities($contract['address_line2']) ?>
                        <?= htmlentities($contract['city']) ?> <?= htmlentities($contract['state']) ?> <?= htmlentities($contract['zip']) ?>
                    </li>
                    <li>
                        <span class="gi-list-item">Total Number of NYCHA Contracts:</span>
                        <?= $node->total_number_of_contracts['sum'] ?>

                    </li>
                    <li>
                        <span class="gi-list-item">M/WBE Vendor:</span>

                    </li>
                    <li>
                        <span class="gi-list-item">M/WBE Category:</span>

                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="panel-separator"></div>

<div class="content clearfix nycha-contract-history">

    <div>
        <h3>
            Contract History
        </h3>

        <table class="outerTable nycha-c-history">
            <?php
            if ($node->contract_history_by_years && sizeof($node->contract_history_by_years)):
            ?>
            <thead>
            <tr>
                <th class="text">
                    <div><span>Fiscal<br>Year</span></div>
                </th>
                <th class="text">
                    <div><span>Number Of<br>Modifications</span></div>
                </th>
                <th class="number">
                    <div style="margin-right: 86px;"><span>Current<br>Amount</span></div>
                </th>
                <th class="number">
                    <div style="margin-right: 86px;"><span>Original<br>Amount</span></div>
                </th>
            </tr>
            </thead>
            <tbody><?php
            $hidden = 0;
            $yi = 0;
            foreach ($node->contract_history_by_years as $year => $contract_history_by_year):
                ?>
                <tr class="outer <?= ($yi % 2 ? 'odd' : 'even') ?>">
                    <td class="text">
                        <div><a class="showHide <?= ($hidden ? 'open' : '') ?>"></a> FY <?= $year ?></div>
                    </td>
                    <td class="text">
                        <div><?= sizeof($contract_history_by_year) ?> Modifications</div>
                    </td>
                    <td class="number">
                        <div
                            style="margin-right: 86px;"><?= custom_number_formatter_format($contract_history_by_year[key($contract_history_by_year)]['revision_total_amount'], 2, '$') ?></div>
                    </td>
                    <td class="number">
                        <div
                            style="margin-right: 86px;"><?= custom_number_formatter_format($contract['original_amount'], 2, '$') ?></div>
                    </td>
                </tr>
                <tr id="showHideNychaOrderRevisions<?= $year ?>"
                    class="showHide <?= ($yi % 2 ? 'odd' : 'even') ?>" <?= ($hidden ? 'style="display:none"' : '') ?>>
                    <td colspan="4">
                        <div class="scroll">
                            <table class="dataTable outerTable">
                                <thead>
                                <tr>
                                    <th class="number thVNum">
                                        <?= WidgetUtil::generateLabelMapping("version_number") ?>
                                    </th>
                                    <?php if ($node->contractBAPA): ?>
                                        <th class="text thStartDate">
                                            <?= WidgetUtil::generateLabelMapping("start_date") ?>
                                        </th>
                                        <th class="text thEndDate">
                                            <?= WidgetUtil::generateLabelMapping("end_date") ?>
                                        </th>
                                    <?php endif; ?>
                                    <th class="text thRegDate">
                                        <?= WidgetUtil::generateLabelMapping("release_approved_date") ?>
                                    </th>
                                    <th class="text thLastMDate">
                                        <?= WidgetUtil::generateLabelMapping("last_mod_date") ?>
                                    </th>
                                    <th class="number thOrigAmt">
                                        <?= WidgetUtil::generateLabelMapping("original_amount") ?>
                                    </th>
                                    <th class="number thCurAmt">
                                        <?= WidgetUtil::generateLabelMapping("current_amount") ?>
                                    </th>
                                    <th class="text thVerStat">
                                        <?= WidgetUtil::generateLabelMapping("transaction_status") ?>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $even = 0;
                                foreach ($contract_history_by_year as $revision): ?>
                                    <tr class="inner <?= ($even++ % 2 ? 'even' : 'odd') ?>">
                                        <td class="number thVNum">
                                            <div><?= (int)$revision['revision_number'] ?></div>
                                        </td>
                                        <?php if ($node->contractBAPA): ?>
                                            <td class="text thStartDate">
                                                <div><?= format_string_to_date($contract['start_date']) ?></div>
                                            </td>
                                            <td class="text thEndDate">
                                                <div><?= format_string_to_date($contract['end_date']) ?></div>
                                            </td>
                                        <?php endif; ?>
                                        <td class="text thRegDate">
                                            <div><?= format_string_to_date($revision['revision_approved_date']) ?></div>
                                        </td>
                                        <td class="text thLastMDate">
                                            <div><?= format_string_to_date($revision['revision_approved_date']) ?></div>
                                        </td>
                                        <td class="number thOrigAmt">
                                            <div><?= custom_number_formatter_format($contract['original_amount'], 2, '$') ?></div>
                                        </td>
                                        <td class="number thCurAmt">
                                            <div><?= custom_number_formatter_format($revision['revision_total_amount'], 2, '$') ?></div>
                                        </td>
                                        <td class="text thVerStat">
                                            <div>Approved</div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
                <?php
                $yi++;
                $hidden++;
            endforeach;
            else: ?>
            <tbody>
            <tr class="odd">
                <td class="dataTables_empty" valign="top" colspan="4">
                    <div id="no-records-datatable" class="clearfix">
                        <span>No Matching Records Found</span>
                    </div>
                </td>
            </tr>
            <?php endif; ?>
            </tbody>
        </table>

    </div>
    <?php if ($node->contract_history_by_years && sizeof($node->contract_history_by_years)): ?>
        <script type="text/javascript">
            contractsAddPadding(jQuery('div.nycha-contract-history'));
        </script>
    <?php endif; ?>
</div>

<div class="clearfix">
    <div class="links node-links clearfix"></div>
</div>


<div class="panel-separator"></div>

<?php if ($node->contractPO): ?>
    <div class="contracts-spending-top"><h3>SPENDING BY VENDOR</h3>
        <table class="dataTable outerTable" style="border: 1px solid #CACACA;">
            <thead>
            <tr>
                <th style="text-align: left !important; vertical-align: middle;">
                                                        <span
                                                            style="margin:8px 0 8px 15px!important; display:inline-block; text-align: center !important;">Vendor<br>Name</span>
                </th>
                <th style="text-align: center !important; vertical-align: middle; padding-right:6% !important">
                                                        <span
                                                            style="margin:8px 0 8px 0 !important;display:inline-block; text-align: center !important;">Current<br>Amount</span>
                </th>
                <th style="text-align: center !important; vertical-align: middle; padding-right:6% !important">
                                                        <span
                                                            style="margin:8px 0 8px 0 !important;display:inline-block; text-align: center !important;">Original<br>Amount</span>
                </th>
                <th style="text-align: center !important; vertical-align: middle; padding-right:6% !important">
                                                        <span
                                                            style="margin:8px 0 8px 0 !important;display:inline-block; text-align: center !important;">Spent To<br>Date</span>
                </th>
            </tr>
            </thead>
            <tbody>
            <tr class="even outer">
                <td style="text-align: left !important; vertical-align: middle; padding: 10px 5px !important;">
                                                        <span
                                                            style="margin:8px 0 8px 15px!important; display:inline-block; text-align: left !important;"><a
                                                                class="showHide  expandTwo"></a><?= htmlentities($contract['vendor_name']) ?></span>
                </td>
                <td style="text-align: center !important; vertical-align: middle; padding-right:6% !important">
                                                        <span
                                                            style="display:inline-block; text-align: right !important;">
                                                            <?= custom_number_formatter_format($contract['total_amount'], 2, '$'); ?>
                                                        </span>
                </td>
                <td style="text-align: center !important; vertical-align: middle; padding-right:6% !important">
                                                        <span
                                                            style="display:inline-block; text-align: right !important;">
                                                            <?= custom_number_formatter_format($contract['original_amount'], 2, '$'); ?>
                                                        </span>
                </td>
                <td style="text-align: center !important; vertical-align: middle; padding-right:6% !important">
                                                        <span
                                                            style="display:inline-block; text-align: right !important;">
                                                            <?= custom_number_formatter_format($contract['spend_to_date'], 2, "$"); ?>
                                                        </span>
                </td>
            </tr>
            <tr class="showHide">
                <td colspan="4">
                    <div>
                        <div>
                            <div>
                                <h3>
                                    Shipment and Distribution Details <!-- (<?= sizeof($node->shipments) ?>) -->
                                </h3>
                                <div class="scroll">
                                    <table
                                        class="dataTable cta-history outerTable">
                                        <thead>
                                        <tr>
                                            <th class="text">
                                                <div>
                                                    <span>Shipment<br/>Number</span>
                                                </div>
                                            </th>
                                            <th class="text">
                                                <div>
                                                    <span>Distribution <br/>Number</span>
                                                </div>
                                            </th>
                                            <th class="number">
                                                <div
                                                    style="margin-right: 82px;">
                                                    <span>Current<br>Amount</span>
                                                </div>
                                            </th>
                                            <th class="number endCol">
                                                <div
                                                    style="margin-right: 81px;">
                                                    <span>Original<br>Amount</span>
                                                </div>
                                            </th>
                                            <th class="number endCol">
                                                <div
                                                    style="margin-right: 81px;">
                                                    <span>Spend to date</span>
                                                </div>
                                            </th>
                                            <th class="number endCol">
                                                <div
                                                    style="margin-right: 81px;">
                                                    <span>Responsibility center</span>
                                                </div>
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $z = 0;
                                        foreach ($node->shipments as $shipment):?>
                                            <tr class="outer <?= ($z % 2 ? 'even' : 'odd') ?>">
                                                <td class="text">
                                                    <div><?= htmlentities($shipment['shipment_number']) ?></div>
                                                </td>
                                                <td class="text">
                                                    <div><?= htmlentities($shipment['distribution_number']) ?></div>
                                                </td>
                                                <td class="number">
                                                    <div
                                                        style="margin-right: 82px;">
                                                        <div
                                                            class="spent-to-date"><?= custom_number_formatter_format($shipment['release_line_total_amount'], 2, "$"); ?>
                                                        </div>
                                                </td>
                                                <td class="number endCol">
                                                    <div
                                                        style="margin-right: 81px;">
                                                        <div
                                                            class="spent-to-date"><?= custom_number_formatter_format($shipment['release_line_original_amount'], 2, "$"); ?>
                                                        </div>
                                                </td>
                                                <td class="number endCol">
                                                    <div
                                                        style="margin-right: 81px;">
                                                        <div
                                                            class="spent-to-date"><?= custom_number_formatter_format($shipment['release_line_spend_to_date'], 2, "$"); ?>
                                                        </div>
                                                </td>
                                                <td class="text">
                                                    <div><?= htmlentities($shipment['responsibility_center_descr']) ?></div>
                                                </td>
                                            </tr>
                                            <?php
                                            $z++;
                                        endforeach; ?>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <div class="panel-separator"></div>
    <div>
        <h3>
            Spending Transactions by Vendor
        </h3>

        <table class="dataTable cta-spending-history outerTable">
            <thead>
            <tr>
                <th class="text">
                    <div><span>Fiscal<br>Year</span></div>
                </th>
                <th class="text">
                    <div><span>Number Of<br>Transactions</span></div>
                </th>
                <th class="number endCol">
                    <div style="margin-right: 119px;">
                        <span>Amount<br>Spent</span></div>
                </th>
            </tr>
            </thead>
            <tbody>
            <?php
            $year_cnt = 0;
            foreach ([2019, 2018, 2017, 2016, 2015] as $year): ?>
                <tr class="outer <?= ($year_cnt % 2 ? 'even' : 'odd') ?>">
                    <td class="text">
                        <div>
                            <a class="showHide <?= ($year_cnt ? 'open' : '') ?>"></a>
                            FY <?= $year ?></div>
                    </td>
                    <td class="text">
                        <div>7 Transactions</div>
                    </td>
                    <td class="number endCol">
                        <div style="margin-right: 119px;">$3.38M</div>
                    </td>
                </tr>
                <tr id="showHidectaspe<?= $year ?>" class="showHide odd"
                    style="<?= ($year_cnt ? 'display:none' : '') ?>">
                    <td colspan="3">
                        <div class="scroll" style="padding-left:20px">
                            <table class="dataTable outerTable">
                                <thead>
                                <tr>
                                    <th class="text th1">
                                        <div><span>Date</span></div>
                                    </th>
                                    <th class="text th2">
                                        <div><span>Document id</span></div>
                                    </th>
                                    <th class="number th3">
                                        <div><span>Check<br>Amount</span></div>
                                    </th>
                                    <th class="text th4">
                                        <div><span>Expence<br>Category</span>
                                        </div>
                                    </th>
                                    <th class="text th5">
                                        <div><span>Nycha<br/>Payment</span>
                                        </div>
                                    </th>
                                    <th class="text th6">
                                        <div><span>Agency</span></div>
                                    </th>
                                    <th class="text th7">
                                        <div><span>Department</span></div>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $revision_cnt = 0;
                                for ($i = 1; $i < 11; $i++): ?>
                                    <tr class="<?= ($i % 2 ? 'odd' : 'even') ?>">
                                        <td class="text td1">
                                            <div>07/18/<?= $year ?></div>
                                        </td>
                                        <td class="text td2">
                                            <div>428758926</div>
                                        </td>
                                        <td class="number td3">
                                            <div>$3.14B</div>
                                        </td>
                                        <td class="text td4">
                                            <div>CONSTRUCTION BUILDINGS</div>
                                        </td>
                                        <td class="text td5">
                                            <div>$2.58M</div>
                                        </td>
                                        <td class="text td6">
                                            <div>HEALTH AND HOSPITALS</div>
                                        </td>
                                        <td class="text td7">
                                            <div>400-819-303</div>
                                        </td>
                                    </tr>
                                <?php endfor; ?>
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
                <?php
                $year_cnt++;
            endforeach;
            ?>
            </tbody>
        </table>
    </div>

    <div class="panel-separator"></div>

    <h3>Spending by Expense Category</h3>
    <table class="dataTable outerTable"
           style="border: 1px solid #CACACA;">
        <thead>
        <tr>
            <th style="text-align: left !important; vertical-align: middle;"><span
                    style="margin:8px 0 8px 15px!important; display:inline-block; text-align: center !important;">Expense<br>Category</span>
            </th>
            <th style="text-align: left !important; vertical-align: middle;"><span
                    style="margin:8px 0 8px 15px!important; display:inline-block; text-align: center !important;">Category<br>Type</span>
            </th>
            <th style="text-align: center !important; vertical-align: middle; padding-right:6% !important">
                            <span
                                style="margin:8px 0 8px 0 !important;display:inline-block; text-align: center !important;">Encumbered<br>Amount</span>
            </th>
            <th style="text-align: center !important; vertical-align: middle; padding-right:6% !important">
                            <span
                                style="margin:8px 0 8px 0 !important;display:inline-block; text-align: center !important;">Spent To<br>Date</span>
            </th>
        </tr>
        </thead>
        <tbody>
        <tr class="even outer">
            <td style="text-align: left !important; vertical-align: middle; padding: 10px 5px !important;">
                            <span
                                style="margin:8px 0 8px 15px!important; display:inline-block; text-align: left !important;">CONSTRUCTION-BUILDINGS</span>
            </td>
            <td style="text-align: left !important; vertical-align: middle; padding: 10px 5px !important;">
                            <span
                                style="margin:8px 0 8px 15px!important; display:inline-block; text-align: left !important;">OTHER</span>
            </td>
            <td style="text-align: center !important; vertical-align: middle; padding-right:6% !important">
                                                                <span
                                                                    style="display:inline-block; text-align: right !important;">$14.17M</span>
            </td>
            <td style="text-align: center !important; vertical-align: middle; padding-right:6% !important">
                                                                <span
                                                                    style="display:inline-block; text-align: right !important;">$12.74M</span>
            </td>
        </tr>
        </tbody>
    </table>

<?php endif; ?>


<?php if ($node->contractBAPA && $node->assocReleases && sizeof($node->assocReleases)): ?>
    <div class="content clearfix nycha-assoc-releases">

        <h3>Associated Releases</h3>
        <table id="assoc_contracts_list">
            <tbody>
            <?php
            $ri = 0;
            foreach ($node->assocReleases as $release): ?>
                <tr>
                    <td class="assoc_item">
                        <div class="contract-title clearfix">
                            <div>
                                <div><a class="contract-title-text showHide <?= ($ri ? 'open' : '') ?>"></a>
                                    <a>Release Spending for
                                        <strong><?= htmlentities($release['release_id']) ?></strong></a>
                                </div>
                            </div>

                            <div class="dollar-amounts">
                                <div
                                    class="spent-to-date"><?= custom_number_formatter_format($release['release_spend_to_date'], 2, "$"); ?>
                                    <div class="amount-title">Spent to Date
                                    </div>
                                </div>
                                <div
                                    class="original-amount"><?= custom_number_formatter_format($release['release_original_amount'], 2, '$'); ?>
                                    <div class="amount-title">Original Amount
                                    </div>
                                </div>
                                <div
                                    class="current-amount"><?= custom_number_formatter_format($release['release_total_amount'], 2, '$'); ?>
                                    <div class="amount-title">Current Amount
                                    </div>
                                </div>
                            </div>


                        </div>

                        <div class="showHide" style="display: <?= $ri ? 'none' : 'block' ?>;">
                            <div class="panel-display omega-grid omega-12-onecol">
                                <div class="panel-panel">
                                    <div class="inside">

                                        <div class="panel-pane pane-custom pane-1">


                                            <div class="contracts-spending-top"><h3>SPENDING BY VENDOR</h3>
                                                <table class="dataTable outerTable" style="border: 1px solid #CACACA;">
                                                    <thead>
                                                    <tr>
                                                        <th style="text-align: left !important; vertical-align: middle;">
                                                        <span
                                                            style="margin:8px 0 8px 15px!important; display:inline-block; text-align: center !important;">Vendor<br>Name</span>
                                                        </th>
                                                        <th style="text-align: center !important; vertical-align: middle; padding-right:6% !important">
                                                        <span
                                                            style="margin:8px 0 8px 0 !important;display:inline-block; text-align: center !important;">Current<br>Amount</span>
                                                        </th>
                                                        <th style="text-align: center !important; vertical-align: middle; padding-right:6% !important">
                                                        <span>
                                                            style="margin:8px 0 8px 0 !important;display:inline-block; text-align: center !important;">Original<br>Amount</span>
                                                        </th>
                                                        <th style="text-align: center !important; vertical-align: middle; padding-right:6% !important">
                                                        <span
                                                            style="margin:8px 0 8px 0 !important;display:inline-block; text-align: center !important;">Spent To<br>Date</span>
                                                        </th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr class="even outer">
                                                        <td style="text-align: left !important; vertical-align: middle; padding: 10px 5px !important;">
                                                        <span
                                                            style="margin:8px 0 8px 15px!important; display:inline-block; text-align: left !important;"><a
                                                                class="showHide  expandTwo"></a><?= htmlentities($release['vendor_name']) ?></span>
                                                        </td>
                                                        <td style="text-align: center !important; vertical-align: middle; padding-right:6% !important">
                                                        <span
                                                            style="display:inline-block; text-align: right !important;">
                                                            <?= custom_number_formatter_format($release['release_total_amount'], 2, '$'); ?>
                                                        </span>
                                                        </td>
                                                        <td style="text-align: center !important; vertical-align: middle; padding-right:6% !important">
                                                        <span
                                                            style="display:inline-block; text-align: right !important;">
                                                            <?= custom_number_formatter_format($release['release_original_amount'], 2, '$'); ?>
                                                        </span>
                                                        </td>
                                                        <td style="text-align: center !important; vertical-align: middle; padding-right:6% !important">
                                                        <span
                                                            style="display:inline-block; text-align: right !important;">
                                                            <?= custom_number_formatter_format($release['release_spend_to_date'], 2, "$"); ?>
                                                        </span>
                                                        </td>
                                                    </tr>
                                                    <tr class="showHide" style="">
                                                        <td colspan="4">
                                                            <div>
                                                                <div id="contract_history">

                                                                    <div>
                                                                        <h3>
                                                                            Shipment and Distribution Details
                                                                        </h3>
                                                                        <div class="scroll">
                                                                            <table
                                                                                class="dataTable cta-history outerTable">
                                                                                <thead>
                                                                                <tr>
                                                                                    <th class="text">
                                                                                        <div>
                                                                                            <span>Shipment<br/>Number</span>
                                                                                        </div>
                                                                                    </th>
                                                                                    <th class="text">
                                                                                        <div>
                                                                                            <span>Distribution <br/>Number</span>
                                                                                        </div>
                                                                                    </th>
                                                                                    <th class="number">
                                                                                        <div
                                                                                            style="margin-right: 82px;">
                                                                                            <span>Current<br>Amount</span>
                                                                                        </div>
                                                                                    </th>
                                                                                    <th class="number endCol">
                                                                                        <div
                                                                                            style="margin-right: 81px;">
                                                                                            <span>Original<br>Amount</span>
                                                                                        </div>
                                                                                    </th>
                                                                                    <th class="number endCol">
                                                                                        <div
                                                                                            style="margin-right: 81px;">
                                                                                            <span>Spend to date</span>
                                                                                        </div>
                                                                                    </th>
                                                                                    <th class="number endCol">
                                                                                        <div
                                                                                            style="margin-right: 81px;">
                                                                                            <span>Responsibility center</span>
                                                                                        </div>
                                                                                    </th>
                                                                                </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                <?php
                                                                                $z = 0;
                                                                                foreach ($release['shipments'] as $shipment):?>
                                                                                    <tr class="outer <?= ($z % 2 ? 'even' : 'odd') ?>">
                                                                                        <td class="text">
                                                                                            <div><?= htmlentities($shipment['shipment_number']) ?></div>
                                                                                        </td>
                                                                                        <td class="text">
                                                                                            <div><?= htmlentities($shipment['distribution_number']) ?></div>
                                                                                        </td>
                                                                                        <td class="number">
                                                                                            <div
                                                                                                style="margin-right: 82px;">
                                                                                                <div
                                                                                                    class="spent-to-date"><?= custom_number_formatter_format($shipment['release_line_total_amount'], 2, "$"); ?>
                                                                                                </div>
                                                                                        </td>
                                                                                        <td class="number endCol">
                                                                                            <div
                                                                                                style="margin-right: 81px;">
                                                                                                <div
                                                                                                    class="spent-to-date"><?= custom_number_formatter_format($shipment['release_line_original_amount'], 2, "$"); ?>
                                                                                                </div>
                                                                                        </td>
                                                                                        <td class="number endCol">
                                                                                            <div
                                                                                                style="margin-right: 81px;">
                                                                                                <div
                                                                                                    class="spent-to-date"><?= custom_number_formatter_format($shipment['release_line_spend_to_date'], 2, "$"); ?>
                                                                                                </div>
                                                                                        </td>
                                                                                        <td class="text">
                                                                                            <div><?= htmlentities($shipment['responsibility_center_descr']) ?></div>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <?php
                                                                                    $z++;
                                                                                endforeach; ?>
                                                                            </table>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="panel-separator"></div>
                                            <div id="spending_transactions">
                                                <div>
                                                    <h3>
                                                        Release History
                                                    </h3>

                                                    <table class="dataTable cta-spending-history outerTable">
                                                        <thead>
                                                        <tr>
                                                            <th class="text">
                                                                <div><span>Fiscal<br>Year</span></div>
                                                            </th>
                                                            <th class="text">
                                                                <div><span>Number Of<br>Modifications</span></div>
                                                            </th>
                                                            <th class="number endCol">
                                                                <div style="margin-right: 119px;">
                                                                    <span>Current<br>Amount</span></div>
                                                            </th>
                                                            <th class="number endCol">
                                                                <div style="margin-right: 119px;">
                                                                    <span>Original<br>Amount</span></div>
                                                            </th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php
                                                        $year_cnt = 0;
                                                        foreach ($release['history'] as $year => $revisions): ?>
                                                            <tr class="outer <?= ($year_cnt % 2 ? 'even' : 'odd') ?>">
                                                                <td class="text">
                                                                    <div>
                                                                        <a class="showHide <?= ($year_cnt ? 'open' : '') ?>"></a>
                                                                        FY <?= $year ?></div>
                                                                </td>
                                                                <td class="text">
                                                                    <div><?= sizeof($revisions) ?></div>
                                                                </td>
                                                                <td class="number endCol">
                                                                    <div
                                                                        style="margin-right: 119px;"><?= custom_number_formatter_format($revisions[key($revisions)]['revision_total_amount'], 2, "$") ?></div>
                                                                </td>
                                                                <td class="number endCol">
                                                                    <div
                                                                        style="margin-right: 119px;"><?= custom_number_formatter_format($revisions[key($revisions)]['release_original_amount'], 2, "$") ?></div>
                                                                </td>
                                                            </tr>
                                                            <tr id="showHidectaspe<?= $year ?>" class="showHide odd"
                                                                style="<?= ($year_cnt ? 'display:none' : '') ?>">
                                                                <td colspan="4">
                                                                    <div class="scroll" style="padding-left:20px">
                                                                        <table class="dataTable outerTable">
                                                                            <thead>
                                                                            <tr>
                                                                                <th class="text th1">
                                                                                    <div><span>Version Number</span>
                                                                                    </div>
                                                                                </th>
                                                                                <th class="text th2">
                                                                                    <div><span>Approved<br>Date</span>
                                                                                    </div>
                                                                                </th>
                                                                                <th class="text th3">
                                                                                    <div>
                                                                                        <span>Last<br/>Modified&nbsp;Date</span>
                                                                                    </div>
                                                                                </th>
                                                                                <th class="text th4">
                                                                                    <div><span>Current<br/>Amount</span>
                                                                                    </div>
                                                                                </th>
                                                                                <th class="text th5">
                                                                                    <div>
                                                                                        <span>Original<br/>Amount</span>
                                                                                    </div>
                                                                                </th>
                                                                                <th class="text th6">
                                                                                    <div>
                                                                                        <span>Increase/<br/>Decrease</span>
                                                                                    </div>
                                                                                </th>
                                                                                <th class="text th7">
                                                                                    <div>
                                                                                        <span>Transaction<br/>Status</span>
                                                                                    </div>
                                                                                </th>
                                                                            </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                            <?php
                                                                            $revision_cnt = 0;
                                                                            foreach ($revisions as $revision): ?>
                                                                                <tr class="<?= ($revision_cnt % 2 ? 'odd' : 'even') ?>">
                                                                                    <td class="text td1">
                                                                                        <div><?= htmlentities($revision['revision_number']) ?></div>
                                                                                    </td>
                                                                                    <td class="text td2">
                                                                                        <div><?= format_string_to_date($revision['revision_approved_date']) ?></div>
                                                                                    </td>
                                                                                    <td class="text td3">
                                                                                        <div><?= format_string_to_date($revision['revision_approved_date']) ?></div>
                                                                                    </td>
                                                                                    <td class="text td4">
                                                                                        <div><?= custom_number_formatter_format($revision['revision_total_amount'], 2, '$') ?></div>
                                                                                    </td>
                                                                                    <td class="text td5">
                                                                                        <div><?= custom_number_formatter_format($revision['release_original_amount'], 2, '$') ?></div>
                                                                                    </td>
                                                                                    <td class="text td6">
                                                                                        <div><?= custom_number_formatter_format(($revision['revision_total_amount'] - $revision['release_original_amount']), 2, '$') ?></div>
                                                                                    </td>
                                                                                    <td class="text td7">
                                                                                        <div><?= htmlentities($revision['transaction_status_name']) ?></div>
                                                                                    </td>
                                                                                </tr>
                                                                                <?php
                                                                                $revision_cnt++;
                                                                            endforeach; ?>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <?php
                                                            $year_cnt++;
                                                        endforeach;
                                                        ?>
                                                        </tbody>
                                                    </table>
                                                    <div class="panel-separator"></div>
                                                    <div>
                                                        <h3>
                                                            Spending Transactions by Release
                                                        </h3>

                                                        <table class="dataTable cta-spending-history outerTable">
                                                            <thead>
                                                            <tr>
                                                                <th class="text">
                                                                    <div><span>Fiscal<br>Year</span></div>
                                                                </th>
                                                                <th class="text">
                                                                    <div><span>Number Of<br>Transactions</span></div>
                                                                </th>
                                                                <th class="number endCol">
                                                                    <div style="margin-right: 119px;">
                                                                        <span>Amount<br>Spent</span></div>
                                                                </th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <?php
                                                            $year_cnt = 0;
                                                            foreach ([2019, 2018, 2017, 2016, 2015] as $year): ?>
                                                                <tr class="outer <?= ($year_cnt % 2 ? 'even' : 'odd') ?>">
                                                                    <td class="text">
                                                                        <div>
                                                                            <a class="showHide <?= ($year_cnt ? 'open' : '') ?>"></a>
                                                                            FY <?= $year ?></div>
                                                                    </td>
                                                                    <td class="text">
                                                                        <div>7 Transactions</div>
                                                                    </td>
                                                                    <td class="number endCol">
                                                                        <div style="margin-right: 119px;">$3.38M</div>
                                                                    </td>
                                                                </tr>
                                                                <tr id="showHidectaspe<?= $year ?>" class="showHide odd"
                                                                    style="<?= ($year_cnt ? 'display:none' : '') ?>">
                                                                    <td colspan="3">
                                                                        <div class="scroll" style="padding-left:20px">
                                                                            <table class="dataTable outerTable">
                                                                                <thead>
                                                                                <tr>
                                                                                    <th class="text th1">
                                                                                        <div><span>Date</span></div>
                                                                                    </th>
                                                                                    <th class="text th2">
                                                                                        <div><span>Document id</span>
                                                                                        </div>
                                                                                    </th>
                                                                                    <th class="number th3">
                                                                                        <div>
                                                                                            <span>Check<br>Amount</span>
                                                                                        </div>
                                                                                    </th>
                                                                                    <th class="text th4">
                                                                                        <div>
                                                                                            <span>Expence<br>Category</span>
                                                                                        </div>
                                                                                    </th>
                                                                                    <th class="text th5">
                                                                                        <div>
                                                                                            <span>Nycha<br/>Payment</span>
                                                                                        </div>
                                                                                    </th>
                                                                                    <th class="text th6">
                                                                                        <div><span>Agency</span></div>
                                                                                    </th>
                                                                                    <th class="text th7">
                                                                                        <div><span>Department</span>
                                                                                        </div>
                                                                                    </th>
                                                                                </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                <?php $revision_cnt = 0;
                                                                                for ($i = 1; $i < 11; $i++): ?>
                                                                                    <tr class="<?= ($i % 2 ? 'odd' : 'even') ?>">
                                                                                        <td class="text td1">
                                                                                            <div>
                                                                                                07/18/<?= $year ?></div>
                                                                                        </td>
                                                                                        <td class="text td2">
                                                                                            <div>428758926</div>
                                                                                        </td>
                                                                                        <td class="number td3">
                                                                                            <div>$3.14B</div>
                                                                                        </td>
                                                                                        <td class="text td4">
                                                                                            <div>CONSTRUCTION
                                                                                                BUILDINGS
                                                                                            </div>
                                                                                        </td>
                                                                                        <td class="text td5">
                                                                                            <div>$2.58M</div>
                                                                                        </td>
                                                                                        <td class="text td6">
                                                                                            <div>HEALTH AND HOSPITALS
                                                                                            </div>
                                                                                        </td>
                                                                                        <td class="text td7">
                                                                                            <div>400-819-303</div>
                                                                                        </td>
                                                                                    </tr>
                                                                                <?php endfor; ?>
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <?php
                                                                $year_cnt++;
                                                            endforeach;
                                                            ?>
                                                            </tbody>
                                                        </table>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="panel-separator"></div>


                                            <h3>Spending by Expense Category</h3>
                                            <table class="dataTable outerTable"
                                                   style="border: 1px solid #CACACA;">
                                                <thead>
                                                <tr>
                                                    <th style="text-align: left !important; vertical-align: middle;"><span
                                                            style="margin:8px 0 8px 15px!important; display:inline-block; text-align: center !important;">Expense<br>Category</span>
                                                    </th>
                                                    <th style="text-align: left !important; vertical-align: middle;"><span
                                                            style="margin:8px 0 8px 15px!important; display:inline-block; text-align: center !important;">Category<br>Type</span>
                                                    </th>
                                                    <th style="text-align: center !important; vertical-align: middle; padding-right:6% !important">
                            <span
                                style="margin:8px 0 8px 0 !important;display:inline-block; text-align: center !important;">Encumbered<br>Amount</span>
                                                    </th>
                                                    <th style="text-align: center !important; vertical-align: middle; padding-right:6% !important">
                            <span
                                style="margin:8px 0 8px 0 !important;display:inline-block; text-align: center !important;">Spent To<br>Date</span>
                                                    </th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr class="even outer">
                                                    <td style="text-align: left !important; vertical-align: middle; padding: 10px 5px !important;">
                            <span
                                style="margin:8px 0 8px 15px!important; display:inline-block; text-align: left !important;">CONSTRUCTION-BUILDINGS</span>
                                                    </td>
                                                    <td style="text-align: left !important; vertical-align: middle; padding: 10px 5px !important;">
                            <span
                                style="margin:8px 0 8px 15px!important; display:inline-block; text-align: left !important;">OTHER</span>
                                                    </td>
                                                    <td style="text-align: center !important; vertical-align: middle; padding-right:6% !important">
                                                                <span
                                                                    style="display:inline-block; text-align: right !important;">$14.17M</span>
                                                    </td>
                                                    <td style="text-align: center !important; vertical-align: middle; padding-right:6% !important">
                                                                <span
                                                                    style="display:inline-block; text-align: right !important;">$12.74M</span>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>

                                        </div>

                                    </div>

                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php
                $ri++;
            endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
