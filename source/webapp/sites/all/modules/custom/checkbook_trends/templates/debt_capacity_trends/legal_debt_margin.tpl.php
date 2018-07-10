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
<?php
echo eval($node->widgetConfig->header);
$table_rows = array();
$years = array();
foreach( $node->data as $row){
	$length =  $row['indentation_level'];
	$spaceString = '&nbsp;';
	while($length > 0){
		$spaceString .= '&nbsp;';
		$length -=1;
	}
	$table_rows[$row['display_order']]['category'] =  $row['category'];
	$table_rows[$row['display_order']]['highlight_yn'] = $row['highlight_yn'];
	$table_rows[$row['display_order']]['indentation_level'] = $row['indentation_level'];
	$table_rows[$row['display_order']]['amount_display_type'] = $row['amount_display_type'];
	$table_rows[$row['display_order']][$row['fiscal_year']]['amount'] = $row['amount'];
	$years[$row['fiscal_year']] = 	$row['fiscal_year'];
}
rsort($years);
?>

<h3><?php echo $node->widgetConfig->table_title; ?></h3>

<a class="trends-export" href="/export/download/trends_legal_debt_margin_csv?dataUrl=/node/<?php echo $node->nid ?>">Export</a>
<table class="fy-ait">
  <tbody>
  <tr>
    <td style="width:270px;padding:0;"><div></div></td>
    <td class="bb"><div>Fiscal Year<br>(Amounts in Thousands)</div></td>
  </tr>
  </tbody>
</table>
<table id="table_<?php echo widget_unique_identifier($node) ?>" style='display:none' class="trendsShowOnLoad <?php echo $node->widgetConfig->html_class ?>">
    <?php
    if (isset($node->widgetConfig->caption_column)) {
        echo '<caption>' . $node->data[0][$node->widgetConfig->caption_column] . '</caption>';
    }
    else if (isset($node->widgetConfig->caption)) {
        echo '<caption>' . $node->widgetConfig->caption . '</caption>';
    }
    ?>
    <thead>
        <tr class="second-row">
            <th><div><br/></div></th>
            <?php
            foreach ($years as $year){
                echo "<th class='dt'><div>&nbsp;</div></th>";
                echo "<th class='number'><div>" . $year . "</div></th>";
            }
            ?>
            <th>&nbsp;</th>
        </tr>
    </thead>

    <tbody>

    <?php
        $count = 1;
        foreach($table_rows as $row){
            $dollar_sign = ($count == 1 || strtolower($row['category']) == 'legal debt margin') ? '<div class="dollarItem" >$</div>':'';

            $cat_class = "";
            if($row['highlight_yn'] == 'Y')
                $cat_class = "highlight ";

            $cat_class .= "level" . $row['indentation_level'];
            $amount_class = "";
            if($row['category'] == 'Anticipated TSASC debt incurring power')
                $row['category'] = 'Anticipated TSASC debt incurring<br>power';
            if($row['category'] == 'Total net debt applicable to the limit as a percentage of debt limit')
                $row['category'] = 'Total net debt applicable to the limit<br>as a percentage of debt limit';
            if($row['amount_display_type'])
                $amount_class = " amount-" . $row['amount_display_type'];

            $amount_class .= " number ";
            $row['category'] = str_replace('(1)','<sup>(1)</sup>', $row['category']);
            $row['category'] = str_replace('(2)','<sup>(2)</sup>', $row['category']);
            $row['category'] = str_replace('(3)','<sup>(3)</sup>', $row['category']);

            $row['category'] = (isset($row['category'])?$row['category']:'&nbsp;');

            $conditionCategory = $row['category'];
            switch($conditionCategory){
            	case "Debt limit (10% of assessed value)":
            		$conditionCategory = "<div class='" . $cat_class . "'>Debt limit (10% of<br><span style='padding-left:0px;'>assessed value)<span></div>";
            		break;
            	case "Service fund and appropriations for redemption of non-excluded debt":
            		$conditionCategory = "<div class='" . $cat_class ."'>Service fund and<br><span style='padding-left:0px;'>appropriations for</span><br><span style='padding-left:0px;'>redemption of</span><br><span style='padding-left:0px;'>non-excluded debt</span></div>";
            		break;
            	case "Anticipated TSASC debt incurring<br>power":
            		$conditionCategory = "<div class='" .$cat_class . "'>Anticipated TSASC debt<br><span style='padding-left:0px;'>incurring power<span></div>";
            		break;
            	case "Contract, land acquisition and other liabilities":
            		$conditionCategory = "<div class='" .$cat_class . "'>Contract, land acquisition<br><span style='padding-left:0px;'>and other liabilities</span></div>";
            		break;
            	case "Total net debt applicable to limit":
            		$conditionCategory = "<div class='" .$cat_class . "'>Total net debt applicable<br>to limit</div>";
            		break;
            	case "Total net debt applicable to the limit<br>as a percentage of debt limit":
            		$conditionCategory = "<div class='" .$cat_class . "'>Total net debt applicable to<br><span style='padding-left:0px;'>the limit as a percentage</span><br><span style='padding-left:0px;'>of debt limit<span></div>";
            		break;
                case "Anticipated TFA financing<sup>(3)</sup>":
                    $conditionCategory = "<div class='" .$cat_class . "'>Anticipated TFA financing</div>";
                    break;
            	default:
            		$conditionCategory = "<div class='" . $cat_class . "' >" . $row['category'] . "</div>";
            		break;
            }

            echo "<tr><td class='text'>" . $conditionCategory . "</td>";

            foreach ($years as $year){
                if($count == count($table_rows)){
                     $amount = isset($row[$year]['amount']) ? $row[$year]['amount'] : '&nbsp;';
                    echo "<td><div>&nbsp;</div></td>"
                         ."<td class='" . $amount_class . "' ><div>" . $amount . "&nbsp;&nbsp;%</div></td>";
                }
                else{
                    if($row[$year]['amount'] > 0){
                        $amount = isset($row[$year]['amount']) ? number_format($row[$year]['amount']) : '&nbsp;';
                        echo "<td><div>&nbsp;</div></td>"
                             ."<td class='" . $amount_class . "' >" .$dollar_sign. "<div>" . $amount . "</div></td>";
                    }else if($row[$year]['amount'] < 0){
                        $amount = isset($row[$year]['amount']) ? number_format(abs($row[$year]['amount'])) : '&nbsp;';
                        echo "<td><div></div></td>"
                            ."<td class='" . $amount_class . "' >" .$dollar_sign. "<div>(" . $amount . ")</div></td>";
                    }else if($row[$year]['amount'] == 0){
                         if(strpos($row['category'], ':'))
                            echo "<td><div>&nbsp;</div></td>"."<td class='" . $amount_class . "' ><div>" . '&nbsp;' . "</div></td>";
                         else
                            echo "<td><div>&nbsp;</div></td>"."<td class='" . $amount_class . "' ><div>" . '-' . "</div></td>";
                    }
                }
            }
            echo "<td>&nbsp;</td>";
            echo "</tr>";
            $count++;
        }
    ?>

    </tbody>
</table>
    <div class="footnote">
    <p>Notes:</p>
    <p>    (1) The Legal Debt Margin and the Net Debt Applicable to the Debt Limit as a Percentage of the Debt Limit are recalculated on July 1, the first day of each City fiscal year, based on the new assessed value
        in accordance with the new year's enacted tax fixing resolution. Hence, the amounts applicable to the succeeding fiscal year differ from these June 30th fiscal year end amounts.  The extent and direction of the change
        in debt limit dependson those of the change in assessed value from year to year, smoothed by the five year averaging. For fiscal year 2018, beginning July 1, 2017, the Legal Debt Margin and the Net Debt Applicable
        to the Debt Limit as a Percentage of the Debt Limit are $34,211,662 and 65.18%, respectively.</p>
    <p>(2) A five-year average of full valuations of taxable real estate from the Resolutions of the Council Fixing the Tax Rates for the fiscal year beginning on July 1, 2016 and ending on June 30, 2017.</p>
    <p>(3) The Constitution of the State of New York limits the general debt-incurring power of The City of New York to ten percent of the five-year average of full valuations of taxable real estate.</p>
    <p>(4) Includes adjustments for Business Improvement Districts, Original Issue Discount, Capital Appreciation Bonds Discounts and cash on hand for defeasance.</p>
    <p>(5) Transitional Finance Authority (TFA) Debt Outstanding above $13.5 billion (Excludes TFA Building Aid Revenue bonds).</p>
    <p>(6) To provide for the City's capital program, State legislation was enacted which created TFA. The new authorization as of July  2009 provide that TFA debt above $13.5 billion is subject to the general
        debt limit of the City.</p>
    <p>(7) Obligations for water supply and certain obligations for rapid transit are excluded pursuant to the State Constitution and in accordance with provisions of the State Local Finance Law. Resources of the General
        Debt Service Fund applicable to non-excluded debt and debt service appropriations for the redemption of such debt are deducted from the non-excluded funded debt to arrive at the funded debt within the debt limit.</p>
    </div>
<?php
	widget_data_tables_add_js($node);
