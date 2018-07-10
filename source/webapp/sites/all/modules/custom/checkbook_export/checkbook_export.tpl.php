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

if($totalRecords > 0){
?>
    <div id="loading_gif" style="display: none"></div>
    <div id='dialog' class="ckbk-export-dialog">

        <div id='errorMessages'></div>
        <table>
            <tr>
                <th>
                  <span class="bold">Format:</span>
                  <input type='hidden' name='frmt' checked="true" value='csv' id='export-frmt-csv'/>
                  <label for="export-frmt-csv">&nbsp;CSV (Comma-separated values)</label>
                </th>
            </tr>
            <tr>
              <th colspan="2"><span class="bold">Data Selection</span></th>
            </tr>
            <tr>
                <td colspan="2">
                  <input type='radio' name='dc' value='cp' id='export-dc-cp'/>
                  <label for='export-dc-cp'>&nbsp;Current Page</label>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                  <input type='radio' name='dc' checked="true" value='all' id="export-dc-all"/>
                  <label for="export-dc-all">&nbsp;All Pages</label>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <input type='radio' name='dc' value='range' id="export-dc-range"/>
                  <label for="export-dc-range">
                    &nbsp;Pages from&nbsp;<input type='text' class="export-range-input" name='rangefrom' size="4"/>
                    &nbspto&nbsp;<input class="export-range-input" type='text' name='rangeto' size="4"/><?php
                      echo '<span id="export-message">(Max allowed pages: '. number_format($maxPages) .')</span>';
                    ?></label>
                </td>
            </tr>
        </table>
    </div>
    <span id="export-message">
        <?php
        if($totalRecords <= $displayRecords){
            echo "Total ".number_format($displayRecords)." records available for download.";
        }else{
            echo "Maximum of ".number_format($displayRecords)." records available for download from ".number_format($totalRecords)." available records.";
        }
        ?>
    </span>
<?php
}else{
?>
    <div id='dialog'>
        <table class="no-records clearfix">
            <tr>
                <td>No records are available for download.</td>
            </tr>
        </table>
    </div>
<?php
}
?>
