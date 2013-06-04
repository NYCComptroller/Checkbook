<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
?>
<div class="grid">
  <?php if(!preg_match("/print/",$_GET['original_q'])){?>
    <span class="grid_export" exportid="<?= $node->nid ?>">Export</span>
  <?php }?>
</div>
