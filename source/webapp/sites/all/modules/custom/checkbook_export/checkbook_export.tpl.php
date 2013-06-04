<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
?>
<?php
if($totalRecords > 0){
?>
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
                <td colspan="2"><input type='radio' name='dc' value='all'/>&nbsp;All Pages</td>
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
