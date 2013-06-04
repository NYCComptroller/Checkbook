<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
?>
<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jrobertson
 * Date: 3/26/13
 * Time: 2:35 PM
 * To change this template use File | Settings | File Templates.
 */
?>
<div class="rows">
  <?php
  foreach ($results as $key => $value){
    echo '<div class="row">';
    echo '<div class="checkbox"><input type="checkbox" value="'.urlencode($key).'" name="'.$name.'" onClick="javascript:applySearchFilters();"></div>';
    echo '<div class="name">'.$key.'</div>';
    echo '<div class="number"><span>'.number_format($value).'</span></div>';
    echo '</div>';
  }
  ?>
</div>