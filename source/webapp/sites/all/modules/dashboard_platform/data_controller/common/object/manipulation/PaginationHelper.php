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




function paginate_records(array &$records = NULL, $start_with = 0, $limit = NULL) {
    if (!isset($records)) {
        return $records;
    }

    if ((!isset($start_with) || ($start_with == 0)) && !isset($limit)) {
        return $records;
    }

    return array_slice($records, (isset($start_with) ? $start_with : 0), $limit);
}
