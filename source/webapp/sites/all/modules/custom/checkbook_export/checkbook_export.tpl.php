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
    <div id='dialog'>

        <div id='errorMessages'></div>
        <table>
            <tr>
                <th><span class="bold">Data Selection</span></th>
                <th><span class="bold">Format</span></th>
            </tr>
            <tr>
                <td><input type='radio' name='dc' value='cp'/>&nbsp;Current Page</td>
                <td><input type='radio' name='frmt' checked="true" value='csv'/>&nbsp;Comma Delimited</td>
            </tr>
            <tr>
                <td colspan="2"><input type='radio' name='dc' checked="true" value='all'/>&nbsp;All Pages</td>
            </tr>
            <tr>
                <td colspan="2">
                    <input type='radio' name='dc' value='range'/>
                    &nbsp;Pages from&nbsp;<input type='text' name='rangefrom' size="4"/>
                    &nbspto&nbsp;<input type='text' name='rangeto' size="4"/><?php echo '<span id="export-message">(Max allowed pages: '. number_format($maxPages) .')</span>'?>
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
