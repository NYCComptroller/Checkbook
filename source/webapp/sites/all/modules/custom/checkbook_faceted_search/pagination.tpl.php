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
    $value = urlencode(html_entity_decode($row[0], ENT_QUOTES)) ;
    echo '<div class="row">';
    echo '<div class="checkbox"><input class="styled" name="' . $autocomplete_id . '" type="checkbox" value="' . $value . '" onClick="return applyTableListFilters();"></div>';
    echo '<div class="name">' . $row[1] . '</div>';
    echo '<div class="number"><span>' . number_format($row[2]) . '</span></div>';
    echo '</div>';
  }
  ?>
</div>
