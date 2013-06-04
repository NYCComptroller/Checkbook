<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
?>
<?php

if(!isset($message)){
    $message = "There are no records found.";
}

echo "<div id='no-records' class='clearfix'>" . $message . "</div>";