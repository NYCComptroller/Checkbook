<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
?>
<?php if(count($node->data) > 0){/*
    $contract = $node->data[0];
    $magid = _get_master_agreement_id();
    $document_codes = _get_document_codes();
    $magdoctype = $document_codes[$contract['document_code_id@checkbook:history_master_agreement/master_agreement_id@checkbook:history_agreement']];

    ?>
<div id="contListContainerNew">
    <h2>Associated Contracts</h2>
    <a href="/panel_html/contract_transactions/contract_details/magid/<?php echo $magid; ?>/doctype/<?php echo $magdoctype; ?>" class="bottomContainerReload">
        <?php echo $contract['contract_number@checkbook:history_master_agreement/master_agreement_id@checkbook:history_agreement']; ?></a>


    <ul>
        <?php
        $count = 0;
        foreach($node->data as $contract){
            if($count % 10 == 0 & $count > 0){
                echo "</ul><ul>";
            }
            ?>
            <li>
                <a href="/panel_html/contract_transactions/contract_details/agid/<?php echo $contract['original_agreement_id']; ?>/doctype/<?php echo $contract['document_code@checkbook:ref_document_code']; ?>" class="ui-button bottomContainerReload"><?php echo $contract['contract_number']; ?></a>
            </li>
            <?php $count +=1; }

        $origUrl = $_GET['q'];
        $_GET['q']='nodedisplay/node/'.widget_unique_identifier($node).'/agid/'._getRequestParamValue('agid');
        pager_default_initialize($node->totalDataCount, 10);
        $output = theme('pager', array('quantity' => 5));
        print "<div class='customPager'>". $output . "</div>";
        $_GET['q'] = $origUrl;

        ?>
    </ul>
</div>
<?php }else{
    echo "No Associated Contracts";
*/}
 