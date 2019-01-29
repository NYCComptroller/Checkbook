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


$contract = $node->data[0];

?>
<div class="content clearfix">
    <div class="contract-id">
        <h2 class='contract-title'>Contract ID: <span
                class="contract-number"><?= $contract['contract_id']; ?></span></h2>
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
            <?= custom_number_formatter_format($contract['spend_to_date'], 2, '$'); ?>
            <div class="amount-title">Current Amount</div>
        </div>
    </div>
    <div class="contract-information">
        <div class="contract-details">
            <h4>General Information</h4>
            <ul class="left">
                <li>
                    <span class="gi-list-item">Vendor:</span>
                    &nbsp;<?= $contract['vendor_name']; ?></li>
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
                <li>
                    <span class="gi-list-item">Vendor:</span>
                    &nbsp;<?= $contract['vendor_name'] ?></li>
                <li>
                    <span class="gi-list-item">Address:</span>
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
                    <div><span>Revision<br/>Approved&nbsp;Date</span></div>
                </th>
                <th class='number thTotalAmt'>
                    <div><span>Revision <br/>Total&nbsp;Amount</span></div>
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
</div>

<div class="clearfix">
    <div class="links node-links clearfix"></div>

</div>
