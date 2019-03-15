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
<table>
    <tbody>
<?php
$first = (bool)!$node->page;
foreach ($node->assocReleases as $release): ?>
    <tr>
        <td class="assoc_item">
            <div class="contract-title clearfix">
                <div>
                    <div><a class="contract-title-text showHide <?= ($first ? '' : 'open') ?>"></a>
                        <a>Release Spending for
                            <strong><?= htmlentities($release['release_id']) ?></strong></a>
                    </div>
                </div>

                <div class="dollar-amounts">
                    <div
                        class="spent-to-date"><?= custom_number_formatter_format($release['release_spend_to_date'], 2, "$"); ?>
                        <div class="amount-title">
                            <?= str_replace('<br/>', ' ', WidgetUtil::getLabel('spent_to_date')) ?>
                        </div>
                    </div>
                    <div
                        class="original-amount"><?= custom_number_formatter_format($release['release_original_amount'], 2, '$'); ?>
                        <div class="amount-title">
                            <?= str_replace('<br/>', ' ', WidgetUtil::getLabel('original_amount')) ?>
                        </div>
                    </div>
                    <div
                        class="current-amount"><?= custom_number_formatter_format($release['release_total_amount'], 2, '$'); ?>
                        <div class="amount-title">
                            <?= str_replace('<br/>', ' ', WidgetUtil::getLabel('current_amount')) ?>
                        </div>
                    </div>
                    <div
                        class="approved-date">
                        <?= format_string_to_date($release['release_approved_date']); ?>
                        <div class="amount-title">
                            <?= str_replace('<br/>', ' ', WidgetUtil::getLabel('approved_date')) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="showHide" style="display: <?= $first ? 'block' : 'none' ?>;">
                <div class="panel-display omega-grid omega-12-onecol">
                    <div class="panel-panel">
                        <div class="inside">

                            <div class="panel-pane pane-custom pane-1">
                                <div class="contracts-spending-top"><h3>SPENDING BY VENDOR</h3>
                                    <table class="dataTable cta-spending-history outerTable">
                                        <thead>
                                        <tr>
                                            <th><span
                                                    style="margin:8px 0px 8px 15px!important; display:inline-block; text-align: center !important;">
                                                            <?= WidgetUtil::getLabel('vendor_name') ?>
                                                            </span>
                                            </th>
                                            <th>
                                                            <span
                                                                style="text-align: center !important; vertical-align: middle; padding-right:6% !important">
                                                                <?= WidgetUtil::getLabel('current_amount') ?>
                                                            </span>
                                            </th>
                                            <th>
                                                            <span
                                                                style="text-align: center !important; vertical-align: middle; padding-right:6% !important">
                                                                <?= WidgetUtil::getLabel('original_amount') ?>
                                                                </span>
                                            </th>
                                            <th>
                                                            <span
                                                                style="text-align: center !important; vertical-align: middle; padding-right:6% !important">
                                                                <?= WidgetUtil::getLabel('spent_to_date') ?>
                                                            </span></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr class="even outer">
                                            <td style="text-align: left !important; vertical-align: middle; padding: 10px 5px !important;">
                                                        <span
                                                            style="margin:8px 0px 8px 15px!important; display:inline-block; text-align: left !important;">
                                                            <a class="showHide  expandTwo"></a><?= htmlentities($release['vendor_name']) ?></span>
                                            </td>
                                            <td style="text-align: left !important; vertical-align: middle; padding: 10px 5px !important;">
                                                        <span
                                                            style="text-align: center !important; vertical-align: middle; padding-right:6% !important">
                                                            <?= custom_number_formatter_format($release['release_total_amount'], 2, '$'); ?>
                                                        </span>
                                            </td>
                                            <td style="text-align: left !important; vertical-align: middle; padding: 10px 5px !important;">
                                                        <span
                                                            style="text-align: center !important; vertical-align: middle; padding-right:6% !important">
                                                            <?= custom_number_formatter_format($release['release_original_amount'], 2, '$'); ?>
                                                        </span>
                                            </td>
                                            <td style="text-align: left !important; vertical-align: middle; padding: 10px 5px !important;">
                                                        <span
                                                            style="text-align: center !important; vertical-align: middle; padding-right:6% !important">
                                                            <?= custom_number_formatter_format($release['release_spend_to_date'], 2, "$"); ?>
                                                        </span>
                                            </td>
                                        </tr>
                                        <tr class="showHide">
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
                                                                            <?= WidgetUtil::getLabelDiv('line_number') ?>
                                                                        </th>
                                                                        <th class="text">
                                                                            <?= WidgetUtil::getLabelDiv('shipment_number') ?>
                                                                        </th>
                                                                        <th class="text">
                                                                            <?= WidgetUtil::getLabelDiv('distribution_number') ?>
                                                                        </th>
                                                                        <th class="number">
                                                                            <?= WidgetUtil::getLabelDiv('current_amount') ?>
                                                                        </th>
                                                                        <th class="number endCol">
                                                                            <?= WidgetUtil::getLabelDiv('original_amount') ?>
                                                                        </th>
                                                                        <th class="number endCol">
                                                                            <?= WidgetUtil::getLabelDiv('spent_to_date') ?>
                                                                        </th>
                                                                        <th class="number endCol">
                                                                            <?= WidgetUtil::getLabelDiv('responsibility_center') ?>
                                                                        </th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    <?php
                                                                    $z = 0;
                                                                    foreach ($release['shipments'] as $shipment):?>
                                                                        <tr class="outer <?= ($z % 2 ? 'even' : 'odd') ?>">
                                                                            <td class="text">
                                                                                <div><?= htmlentities($shipment['line_number']) ?></div>
                                                                            </td>
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
                                                    <?= WidgetUtil::getLabelDiv('fiscal_year') ?>
                                                </th>
                                                <th class="text">
                                                    <?= WidgetUtil::getLabelDiv('no_of_mod') ?>
                                                </th>
                                                <th class="number endCol">
                                                    <?= WidgetUtil::getLabelDiv('current_amount') ?>
                                                </th>
                                                <th class="number endCol">
                                                    <?= WidgetUtil::getLabelDiv('original_amount') ?>
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php if ($release['history']):
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
                                                    <tr id="showHidectaspe<?= $year ?>" class="showHide <?= ($year_cnt % 2 ? 'even' : 'odd') ?>"
                                                        style="<?= ($year_cnt ? 'display:none' : '') ?>">
                                                        <td colspan="4">
                                                            <div class="scroll" style="padding-left:20px">
                                                                <table class="dataTable outerTable">
                                                                    <thead>
                                                                    <tr>
                                                                        <th class="text th1">
                                                                            <?= WidgetUtil::getLabelDiv('version_number') ?>
                                                                        </th>
                                                                        <th class="text th2">
                                                                            <?= WidgetUtil::getLabelDiv('approved_date') ?>
                                                                        </th>
                                                                        <th class="text th3">
                                                                            <?= WidgetUtil::getLabelDiv('last_mod_date') ?>
                                                                        </th>
                                                                        <th class="text th4">
                                                                            <?= WidgetUtil::getLabelDiv('current_amount') ?>
                                                                        </th>
                                                                        <th class="text th5">
                                                                            <?= WidgetUtil::getLabelDiv('original_amount') ?>
                                                                        </th>
                                                                        <th class="text th6">
                                                                            <?= WidgetUtil::getLabelDiv('increase_decrease') ?>
                                                                        </th>
                                                                        <th class="text th7">
                                                                            <?= WidgetUtil::getLabelDiv('transaction_status') ?>
                                                                        </th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    <?php
                                                                    $revision_cnt = 0;
                                                                    foreach ($revisions as $revision): ?>
                                                                        <tr class="<?= ($revision_cnt % 2 ? 'even' : 'odd') ?>">
                                                                            <td class="text td1">
                                                                                <div><?= htmlentities($revision['revision_number']) ?></div>
                                                                            </td>
                                                                            <td class="text td2">
                                                                                <div><?= format_string_to_date($revision['revision_approved_date'] ?? $revision['approved_date']) ?></div>
                                                                            </td>
                                                                            <td class="text td3">
                                                                                <div><?= format_string_to_date($revision['revision_approved_date'] ?? $revision['approved_date']) ?></div>
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
                                            <?php else: ?>
                                                <tr>
                                                    <td class="dataTables_empty" valign="top" colspan="4">
                                                        <div id="no-records-datatable" class="clearfix">
                                                            <span>No Matching Records Found</span>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
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
                                                        <?= WidgetUtil::getLabelDiv('fiscal_year') ?>
                                                    </th>
                                                    <th class="text">
                                                        <?= WidgetUtil::getLabelDiv('no_of_transactions') ?>
                                                    </th>
                                                    <th class="number endCol">
                                                        <?= WidgetUtil::getLabelDiv('amount_spent') ?>
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
                                                                            <?= WidgetUtil::getLabelDiv('date') ?>
                                                                        </th>
                                                                        <th class="text th2">
                                                                            <?= WidgetUtil::getLabelDiv('document_id') ?>
                                                                        </th>
                                                                        <th class="number th3">
                                                                            <?= WidgetUtil::getLabelDiv('current_amount') ?>
                                                                        </th>
                                                                        <th class="text th4">
                                                                            <?= WidgetUtil::getLabelDiv('expense_category') ?>
                                                                        </th>
                                                                        <th class="text th5">
                                                                            <?= WidgetUtil::getLabelDiv('nycha_payment') ?>
                                                                        </th>
                                                                        <th class="text th6">
                                                                            <?= WidgetUtil::getLabelDiv('agency_name') ?>
                                                                        </th>
                                                                        <th class="text th7">
                                                                            <?= WidgetUtil::getLabelDiv('dept_name') ?>
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
                                        <th style="text-align: left !important; vertical-align: middle;">
                                                        <span
                                                            style="margin:8px 0 8px 15px!important; display:inline-block; text-align: center !important;">Expense<br>Category</span>
                                        </th>
                                        <th style="text-align: left !important; vertical-align: middle;">
                                                        <span
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
    $first=false;
endforeach; ?>
    </tbody>
</table>
