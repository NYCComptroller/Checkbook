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
            <h2 class='contract-title'><?= WidgetUtil::getLabel('contract_id') ?>: <span
                    class="contract-number"><?= htmlentities($contract['contract_id']) ?></span></h2>
        </div>
        <div class="dollar-amounts">
            <div class="spent-to-date">
                <?= custom_number_formatter_format($contract['spend_to_date'], 2, "$"); ?>
                <div class="amount-title"><?= WidgetUtil::getLabel('spent_to_date') ?></div>
            </div>
            <div class="original-amount">
                <?= custom_number_formatter_format($contract['original_amount'], 2, '$'); ?>
                <div class="amount-title"><?= WidgetUtil::getLabel('original_amount') ?></div>
            </div>
            <div class="current-amount">
                <?= custom_number_formatter_format($contract['current_amount'], 2, '$'); ?>
                <div class="amount-title"><?= WidgetUtil::getLabel('current_amount') ?></div>
            </div>
            <?php if ($node->total_associated_releases): ?>
                <div class="total-contracts">
                    <?= intval($node->total_associated_releases) ?>
                    <div class="amount-title"><?= WidgetUtil::getLabel('assoc_releases') ?></div>
                </div>
            <?php endif; ?>
        </div>
        <div class="contract-information">
            <div class="contract-details">
                <h4>General Information</h4>
                <ul class="left">
                    <li>
                        <span class="gi-list-item"><?= WidgetUtil::getLabel('vendor_name') ?>:</span>
                        &nbsp;<a
                            href="<?= NychaContractsUrlService::vendorUrl( $contract['vendor_id']) ?>">
                            <?= htmlentities($contract['vendor_name']) ?></a></li>
                    <li>
                        <span class="gi-list-item"><?= WidgetUtil::getLabel('contract_purpose') ?>:</span>
                        &nbsp;<?= htmlentities($contract['purpose']) ?>
                    </li>
                    <li>
                        <span class="gi-list-item"><?= WidgetUtil::getLabel('contract_type') ?>:</span>
                        &nbsp;<?= htmlentities($contract['contract_type_descr']) ?>
                    </li>
                    <li>
                        <span class="gi-list-item"><?= WidgetUtil::getLabel('contract_agency') ?>:</span>
                        &nbsp;<a href="<?= NychaContractsUrlService::agencyUrl() ?>">
                            <?= htmlentities($contract['agency_name']) ?>
                        </a>
                    </li>
                    <li><span class="gi-list-item"><?= WidgetUtil::getLabel('award_method') ?>:</span>
                        &nbsp;<?= htmlentities($contract['award_method_name']) ?>

                    </li>

                    <li>
                        <span class="gi-list-item"><?= WidgetUtil::getLabel('version_number') ?>:</span>
                        &nbsp;<?= htmlentities($contract['revision_count'] ?? $contract['release_revision_count']) ?>
                    </li>
                </ul>
                <ul class="right">
                    <?php if ($contract['start_date']): ?>
                        <li>
                        <span class="gi-list-item"><?= WidgetUtil::getLabel('start_date') ?>:</span>
                        &nbsp;<?= format_string_to_date($contract['start_date']); ?>
                        </li><?php endif; ?>
                    <?php if ($contract['end_date']): ?>
                        <li>
                        <span class="gi-list-item"><?= WidgetUtil::getLabel('end_date') ?>:</span>
                        &nbsp;<?= format_string_to_date($contract['end_date']); ?>
                        </li><?php endif; ?>
                    <li>
                        <span class="gi-list-item"><?= WidgetUtil::getLabel('approved_date') ?>:</span>
                        &nbsp;<?= format_string_to_date($contract['release_approved_date'] ?? $contract['approved_date']); ?>
                    </li>
                    <li>
                        <span class="gi-list-item"><?= WidgetUtil::getLabel('canceled_date') ?>:</span>

                    </li>
                    <li>
                        <span class="gi-list-item"><?= WidgetUtil::getLabel('transaction_status') ?>:</span>
                        &nbsp;<?= 'Approved'; ?>
                    </li>
                    <li>
                        <span class="gi-list-item"><?= WidgetUtil::getLabel('commodity_category') ?>:</span>
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
                        <span class="gi-list-item"><?= WidgetUtil::getLabel('vendor_name') ?>:</span>
                        &nbsp;<a
                            href="<?= NychaContractsUrlService::vendorUrl($contract['vendor_id']) ?>">
                            <?= htmlentities($contract['vendor_name']) ?></a></li>
                    <li>
                        <span class="gi-list-item"><?= WidgetUtil::getLabel('address') ?>:</span>
                        &nbsp;<?= htmlentities($contract['address_line1']) ?> <?= htmlentities($contract['address_line2']) ?>
                        <?= htmlentities($contract['city']) ?> <?= htmlentities($contract['state']) ?> <?= htmlentities($contract['zip']) ?>
                    </li>
                    <li>
                        <span class="gi-list-item"><?= WidgetUtil::getLabel('total_number_nycha_contracts') ?>:</span>
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
            <thead>
            <tr>
                <th class="text">
                    <?= WidgetUtil::getLabelDiv('fiscal_year') ?>
                </th>
                <th class="text">
                    <?= WidgetUtil::getLabelDiv('no_of_modifications') ?>
                </th>
                <th class="number">
                    <div><span><?= WidgetUtil::getLabel('current_amount') ?></span></div>
                </th>
                <th class="number">
                    <div><span><?= WidgetUtil::getLabel('original_amount') ?></span></div>
                </th>
            </tr>
            </thead>
            <tbody>
            <?php
            if ($node->contract_history_by_years && sizeof($node->contract_history_by_years)):
                $hidden = 0;
                $yi = 0;
                foreach ($node->contract_history_by_years as $year => $contract_history_by_year):
                    ?>
                    <tr class="outer <?= ($yi % 2 ? 'odd' : '') ?>">
                        <td class="text">
                            <div><a class="showHide <?= ($hidden ? 'open' : '') ?>"></a> FY <?= $year ?></div>
                        </td>
                        <td class="text">
                            <div><?= sizeof($contract_history_by_year) ?> Modifications</div>
                        </td>
                        <td class="number">
                            <div><?= custom_number_formatter_format($contract_history_by_year[key($contract_history_by_year)]['current_amount'], 2, '$') ?></div>
                        </td>
                        <td class="number">
                            <div><?= custom_number_formatter_format($contract['original_amount'], 2, '$') ?></div>
                        </td>
                    </tr>
                    <tr id="showHideNychaOrderRevisions<?= $year ?>"
                        class="showHide <?= ($yi % 2 ? 'odd' : '') ?>" <?= ($hidden ? 'style="display:none"' : '') ?>>
                        <td colspan="4">
                            <div class="scroll">
                                <table class="dataTable outerTable">
                                    <thead>
                                    <tr>
                                        <th class="number thVNum">
                                            <?= WidgetUtil::getLabelDiv("version_number") ?>
                                        </th>
                                        <?php if ($node->contractBAPA): ?>
                                            <th class="text thStartDate">
                                                <?= WidgetUtil::getLabelDiv("start_date") ?>
                                            </th>
                                            <th class="text thEndDate">
                                                <?= WidgetUtil::getLabelDiv("end_date") ?>
                                            </th>
                                        <?php endif; ?>
                                        <th class="text thRegDate">
                                            <?= WidgetUtil::getLabelDiv("approved_date") ?>
                                        </th>
                                        <th class="text thLastMDate">
                                            <?= WidgetUtil::getLabelDiv("last_mod_date") ?>
                                        </th>
                                        <th class="number thOrigAmt">
                                            <?= WidgetUtil::getLabelDiv("original_amount") ?>
                                        </th>
                                        <th class="number thCurAmt">
                                            <?= WidgetUtil::getLabelDiv("current_amount") ?>
                                        </th>
                                        <th class="text thVerStat">
                                            <?= WidgetUtil::getLabelDiv("transaction_status") ?>
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
                                                <div><?= format_string_to_date(($revision['revision_approved_date'] ?? $revision['approved_date'])) ?></div>
                                            </td>
                                            <td class="text thLastMDate">
                                                <div><?= format_string_to_date(($revision['revision_approved_date'] ?? $revision['approved_date'])) ?></div>
                                            </td>
                                            <td class="number thOrigAmt">
                                                <div><?= custom_number_formatter_format($contract['original_amount'], 2, '$') ?></div>
                                            </td>
                                            <td class="number thCurAmt">
                                                <div><?= custom_number_formatter_format($revision['current_amount'], 2, '$') ?></div>
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
        <table class="dataTable nycha-ct-spending-by-vendor outerTable">
            <thead>
            <tr>
                <th>
                  <?= WidgetUtil::getLabelDiv('vendor_name') ?>
                </th>
                <th>
                  <?= WidgetUtil::getLabelDiv('current_amount') ?>
                </th>
                <th>
                  <?= WidgetUtil::getLabelDiv('original_amount') ?>
                </th>
                <th>
                  <?= WidgetUtil::getLabelDiv('spent_to_date') ?>
                </th>
            </tr>
            </thead>
            <tbody>
            <tr class="even outer">
                <td>
                    <div>
                      <span>
                    <a class="showHide  expandTwo"></a><?= htmlentities($contract['vendor_name']) ?></span>
                    </div>
                </td>
                <td class="number-center">
                    <div>
                      <span>
                    <?= custom_number_formatter_format($contract['current_amount'], 2, '$'); ?>
                    </span>
                    </div>
                </td>
                <td class="number-center">
                    <div>
                      <span>
                    <?= custom_number_formatter_format($contract['original_amount'], 2, '$'); ?>
                    </span>
                    </div>
                </td>
                <td class="number-center">
                    <div>
                      <span>
                    <?= custom_number_formatter_format($contract['spend_to_date'], 2, "$"); ?>
                    </span>
                    </div>
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
                                            <th class="text nycc-line_number">
                                                <?= WidgetUtil::getLabelDiv('line_number') ?>
                                            </th>
                                            <th class="text nycc-shipment_number">
                                                <?= WidgetUtil::getLabelDiv('shipment_number') ?>
                                            </th>
                                            <th class="text nycc-distribution_number">
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
                                        foreach ($node->shipments as $shipment):?>
                                            <tr class="outer <?= ($z % 2 ? 'even' : 'odd') ?>">
                                                <td class="center">
                                                  <div>
                                                    <?= htmlentities($shipment['line_number']) ?>
                                                  </div>
                                                </td>
                                                <td class="center">
                                                  <div>
                                                    <?= htmlentities($shipment['shipment_number']) ?>
                                                  </div>
                                                </td>
                                                <td class="center">
                                                  <div>
                                                    <?= htmlentities($shipment['distribution_number']) ?>
                                                  </div>
                                                </td>
                                                <td class="number">
                                                  <div>
                                                    <?= custom_number_formatter_format($shipment['release_line_total_amount'], 2, "$"); ?>
                                                  </div>
                                                </td>
                                                <td class="number endCol">
                                                  <div>
                                                    <?= custom_number_formatter_format($shipment['release_line_original_amount'], 2, "$"); ?>
                                                  </div>
                                                </td>
                                                <td class="number endCol">
                                                  <div>
                                                    <?= custom_number_formatter_format($shipment['release_line_spend_to_date'], 2, "$"); ?>
                                                  </div>
                                                </td>
                                                <td class="center">
                                                  <div>
                                                    <?= htmlentities($shipment['responsibility_center_descr']) ?>
                                                  </div>
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
                <tr id="showHidectaspe<?= $year ?>" class="showHide odd"
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
                                        <?= WidgetUtil::getLabelDiv('check_amount') ?>
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
    <table class="dataTable outerTable">
        <thead>
        <tr>
            <th>
                <?= WidgetUtil::getLabelDiv('expense_category'); ?>
            </th>
            <th>
                <?= WidgetUtil::getLabelDiv('category_type'); ?>
            </th>
            <th>
                <?= WidgetUtil::getLabelDiv('encumbered_amount'); ?>
            </th>
            <th>
                <?= WidgetUtil::getLabelDiv('spend_to_date'); ?>
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

<?php endif; ?>

<?php if ($node->contractBAPA && $node->assoc_releases_count): ?>
    <div class="content clearfix nycha-assoc-releases">

        <h3>Associated Releases</h3>
        <div class="nycha_contract_assoc_releases"></div>
        <div class="pager-center">
            <div class="nycha_assoc_rel_pager"></div>
        </div>
    </div>

    <script type="text/javascript">
        jQuery(document).ready(function () {
            var callBackURL = "/panel_html/nycha_contract_assoc_releases/contract/<?= htmlentities($contract['contract_id']) ?>/page/";

            function loadAssocReleases(page) {
                jQuery(".nycha_contract_assoc_releases").html("<img class='assoc-loading' src='/sites/all/themes/checkbook/images/loading_large.gif' title='Loading Data...'/>");
                jQuery.ajax({
                    url: callBackURL + page,
                    success: function (data) {
                        jQuery(".nycha_contract_assoc_releases").html(data);
                    },
                    complete: function () {
                        jQuery(".assoc-loading").remove();
                        if (jQuery('.assoc_item').length < <?= $node->assoc_releases_count ?>) {
                        }
                        jQuery(".nycha_assoc_rel_pager").show();
                    }
                });
            }

            jQuery(".nycha_assoc_rel_pager").hide();
            loadAssocReleases(0);

            jQuery('.nycha_assoc_rel_pager').pagination({
                items: <?= $node->assoc_releases_count ?>,
                prevText: 'Previous',
                itemsOnPage: 10,
                onPageClick: function (page) {
                    loadAssocReleases(page - 1)
                }
            });
        });
    </script>
<?php endif; ?>
