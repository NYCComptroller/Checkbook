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
<div class="nyc_totals_links">
    <table>
        <tbody>
        <tr>
            <?php

                $total +=  0;
                $dollars =0;

            $class = "";
            if ( RequestUtilities::get("category") == "") {
                $class = ' class="active"';
            }
            $link = RequestUtil::preparePayrollBottomNavFilter("spending_landing",null);
            $dollars = "<span class='dollars'>" . custom_number_formatter_format($total,1,'$') . "</span>";
            ?>
            <td<?php echo $class; ?>>
                <div class="positioning">
                    <?php if($total != 0 ){?>
                        <a href="/<?php echo $link; ?>?expandBottomCont=true"><?php echo $count; ?>Total<br>Spending<br><?php echo $dollars; ?></a>
                    <?php }else{?>
                        <?php echo $count; ?><a>Total<br>Spending</a><?php echo $dollars; ?>
                    <?php }?>
                </div>
                <div class="indicator"></div>
            </td>
            <?php
            $class = "";
            if (RequestUtilities::get("category") == 2) {
                $class = ' class="active"';
            }
            $link = RequestUtil::preparePayrollBottomNavFilter("spending_landing",2);
            $dollars = "<span class='dollars'>" . custom_number_formatter_format(0,1,'$') . "</span>";
            ?>
            <td<?php echo $class; ?>>
                <div class="positioning">
                    <?php if($dollars != 0 ){?>
                        <a href="/<?php echo $link; ?>?expandBottomCont=true"><?php echo $count; ?>Payroll<br>Spending<br><?php echo $dollars; ?></a>
                    <?php }else{?>
                        <?php echo $count; ?>Payroll<br>Spending<br><?php echo $dollars; ?>
                    <?php }?>
                </div>
                <div class="indicator"></div>
            </td>
            <?php
            $class = "";
            if (RequestUtilities::get("category") == 1) {
                $class = ' class="active"';
            }
            $link = RequestUtil::preparePayrollBottomNavFilter("spending_landing",1);
            $dollars = "<span class='dollars'>" . custom_number_formatter_format(0,1,'$') . "</span>";

            ?>
            <td<?php echo $class; ?>>
                <div class="positioning">
                    <?php if($dollars!= 0 ){?>
                        <a href="/<?php echo $link; ?>?expandBottomCont=true"><?php echo $count; ?>Contract<br>Spending<br><?php echo $dollars; ?></a>
                    <?php }else{?>
                        <?php echo $count; ?>Contract<br>Spending<br><?php echo $dollars; ?>
                    <?php }?>
                </div>
                <div class="indicator"></div>
            </td>
        </tr>
        </tbody>
    </table>
</div>
