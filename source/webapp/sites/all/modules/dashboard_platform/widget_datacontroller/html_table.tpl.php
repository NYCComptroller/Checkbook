<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
?>
<div>
<p>Request:</p>
<?php print_r($variables[0]);?>
<p>Response: </p>
    <table>    
        <?php foreach ($variables[1] as $result) { ?>
        <tr>
            <?php foreach ($result as $gridColumn) {?>
            <td>
                <?php print $gridColumn?>
            </td>
            <?php }?>
        </tr>
        <?php }?>
    </table>
</div>