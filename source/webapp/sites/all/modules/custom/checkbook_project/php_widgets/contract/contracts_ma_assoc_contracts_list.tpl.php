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

<?php if(count($node->data) > 0){/*?>
<div id="contListContainerNew">
    <h2>Associated Contracts</h2>
    <ul>
        <?php
        //echo 'totalDataCount:'.$node->totalDataCount;
        $count = 0;
        foreach($node->data as $contract){
            if($count % 10 == 0 & $count > 0){
                echo "</ul><ul>";
            }
            ?>
            <li><a href="/panel_html/contract_transactions/contract_details/agid/<?php echo $contract['original_agreement_id']; ?>/doctype/<?php echo $contract['document_code@checkbook:ref_document_code']; ?>" class="ui-button bottomContainerReload"><?php echo $contract['contract_number']; ?></a>
            </li>
            <?php $count +=1; }

        $origUrl = $_GET['q'];
        $_GET['q']='nodedisplay/node/'.widget_unique_identifier($node).'/magid/'.RequestUtilities::getRequestParamValue('magid');
        pager_default_initialize($node->totalDataCount, 10);
        $output = theme('pager', array('quantity' => 5));
        print "<div class='customPager'>". $output . "</div>";
        $_GET['q'] = $origUrl;
        ?>
    </ul>
</div>
<?php }
else{
    echo "No Associated Contracts";
*/}
