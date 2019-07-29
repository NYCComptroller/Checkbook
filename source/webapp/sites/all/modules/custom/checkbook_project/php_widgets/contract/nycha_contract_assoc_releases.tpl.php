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
<!-----------------------------    Contract Details of PAs and BAs     ------------------->
<table class="nycha_assoc_contracts_list">
    <tbody>
<?php
$first = (bool)!$node->page;
foreach ($node->assocReleases as $release): ?>
        <tr>
            <td class="assoc_item">
                <div class="contract-title clearfix">
                    <div class="assoc-release-link">
                        <div><a class="contract-title-text showHide <?= ($first ? '' : 'open') ?>"></a>
                            <span>Release Spending for
                                <strong><?= htmlentities($release['release_id']) ?></strong></span>
                        </div>
                    </div>
                    <div class="dollar-amounts">
                        <div
                            class="spent-to-date"><?= custom_number_formatter_format($release['release_spend_to_date'], 2, "$"); ?>
                            <div class="amount-title">
                                <?= str_replace('<br/>', ' ', WidgetUtil::getLabel('invoiced_amount')) ?>
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
                                                <th class="text">
                                                    <?= WidgetUtil::getLabelDiv('vendor_name') ?>
                                                </th>
                                                <th>
                                                    <?= WidgetUtil::getLabelDiv('current_amount') ?>
                                                </th>
                                                <th>
                                                    <?= WidgetUtil::getLabelDiv('original_amount') ?>
                                                </th>
                                                <th>
                                                    <?= WidgetUtil::getLabelDiv('invoiced_amount') ?>
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr class="even outer">
                                                <td>
                                                    <div>
                                                        <a class="showHide  expandTwo"></a><?= htmlentities($release['vendor_name']) ?>
                                                    </div>

                                                </td>
                                                <td class="number-center">
                                                    <div>
                                                        <?= custom_number_formatter_format($release['release_total_amount'], 2, '$'); ?>
                                                    </div>
                                                </td>
                                                <td class="number-center">
                                                    <div>
                                                        <?= custom_number_formatter_format($release['release_original_amount'], 2, '$'); ?>
                                                    </div>
                                                </td>
                                                <td class="number-center">
                                                    <div>
                                                        <?= custom_number_formatter_format($release['release_spend_to_date'], 2, "$"); ?>
                                                    </div>
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
                                                                            <th class="text item_category">
                                                                                <?= WidgetUtil::getLabelDiv('item_category') ?>
                                                                            </th>
                                                                            <th class="text item_description">
                                                                              <?= WidgetUtil::getLabelDiv('item_description') ?>
                                                                            </th>
                                                                            <th class="number shipment_number">
                                                                                <?= WidgetUtil::getLabelDiv('shipment_number') ?>
                                                                            </th>
                                                                            <th class="number">
                                                                                <?= WidgetUtil::getLabelDiv('current_amount') ?>
                                                                            </th>
                                                                            <th class="number endCol">
                                                                                <?= WidgetUtil::getLabelDiv('original_amount') ?>
                                                                            </th>
                                                                            <th class="number endCol">
                                                                                <?= WidgetUtil::getLabelDiv('invoiced_amount') ?>
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
                                                                            <tr class="outer n_assoc_rel_shipments <?= ($z % 2 ? 'even' : 'odd') ?>">
                                                                                <td class="text item_category_descr">
                                                                                    <div><?= _get_tooltip_markup(htmlentities($shipment['commodity_category_descr']), 40) ?></div>
                                                                                </td>
                                                                                <td class="text item_description">
                                                                                  <div><?= _get_tooltip_markup(htmlentities($shipment['item_description']), 20) ?></div>
                                                                                </td>
                                                                                <td class="number-center shipment_number">
                                                                                    <div><?= htmlentities($shipment['shipment_number']) ?></div>
                                                                                </td>
                                                                                <td class="number total_amount">
                                                                                    <div><?= custom_number_formatter_format($shipment['release_line_total_amount'], 2, "$"); ?></div>
                                                                                </td>
                                                                                <td class="number endCol original_amount">
                                                                                    <div><?= custom_number_formatter_format($shipment['release_line_original_amount'], 2, "$"); ?></div>
                                                                                </td>
                                                                                <td class="number endCol spend_to_date">
                                                                                    <div><?= custom_number_formatter_format($shipment['release_line_spend_to_date'], 2, "$"); ?></div>
                                                                                </td>
                                                                                <td class="text responsibility_center_descr">
                                                                                    <div><?= _get_tooltip_markup(htmlentities($shipment['responsibility_center_descr']), 24) ?></div>
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
                                                                <div><?= custom_number_formatter_format($revisions[key($revisions)]['revised_total_amount'], 2, "$") ?></div>
                                                            </td>
                                                            <td class="number endCol">
                                                                <div><?= custom_number_formatter_format($revisions[key($revisions)]['release_original_amount'], 2, "$") ?></div>
                                                            </td>
                                                        </tr>
                                                        <tr id="showHidectaspe<?= $year ?>"
                                                            class="showHide <?= ($year_cnt % 2 ? 'even' : 'odd') ?>"
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
                                                                                    <div><?= custom_number_formatter_format($revision['revised_total_amount'], 2, '$') ?></div>
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
                                                                <div>$3.38M</div>
                                                            </td>
                                                        </tr>
                                                        <tr id="showHidectaspe<?= $year ?>" class="showHide odd cta-r-data"
                                                            style="<?= ($year_cnt ? 'display:none' : '') ?>">
                                                            <td colspan="3">
                                                                <div class="scroll">
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
                                    <table class="dataTable outerTable">
                                        <thead>
                                        <tr>
                                            <th >
                                                <?= WidgetUtil::getLabelDiv('expense_category'); ?>
                                            </th>
                                            <th >
                                                <?= WidgetUtil::getLabelDiv('category_type'); ?>
                                            </th>
                                            <th >
                                                <?= WidgetUtil::getLabelDiv('encumbered_amount'); ?>
                                            </th>
                                            <th >
                                                <?= WidgetUtil::getLabelDiv('invoiced_amount'); ?>
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr class="even outer">
                                            <td class="center">
                                                    <div>
                                                        CONSTRUCTION-BUILDINGS
                                                    </div>
                                            </td>
                                            <td class="center">
                                                <div>
                                                    OTHER
                                                </div>
                                            </td>
                                            <td class="number-center">
                                                <div>
                                                    $14.17M
                                                </div>
                                            </td>
                                            <td class="number-center">
                                                <div>
                                                    $12.74M
                                                </div>
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
    $first = false;
endforeach; ?>
    </tbody>
</table>
