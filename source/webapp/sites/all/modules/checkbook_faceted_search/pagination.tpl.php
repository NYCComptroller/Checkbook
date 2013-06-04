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
 * Date: 3/6/13
 * Time: 3:22 PM
 * To change this template use File | Settings | File Templates.
 */
?>
<div class="rows">
  <?php
  foreach ($unchecked as $row) {
    echo '<div class="row">';
    echo '<div class="checkbox"><input class="styled" name="' . $autocomplete_id . '" type="checkbox" value="' . $row[0] . '" onClick="return applyTableListFilters();"></div>';
    echo '<div class="name">' . $row[1] . '</div>';
    echo '<div class="number"><span>' . number_format($row[2]) . '</span></div>';
    echo '</div>';
  }
  ?>
</div>