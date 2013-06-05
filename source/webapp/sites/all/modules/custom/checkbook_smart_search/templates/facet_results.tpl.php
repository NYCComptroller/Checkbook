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