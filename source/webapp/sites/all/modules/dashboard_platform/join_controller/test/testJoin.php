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


$keyColumnName1 = 'agency_id';
$table1 = array(
    array($keyColumnName1 => 1, 'name' => 'Energy'),
    array($keyColumnName1 => 2, 'name' => 'DHS'),
    array($keyColumnName1 => 3, 'name' => 'FEMA')
);
$source1 = new JoinController_SourceConfiguration($table1, $keyColumnName1);

$keyColumnName2 = 'agency_identifier';
$table2 = array(
    array($keyColumnName2 => 1, 'amount' =>   30000),
    array($keyColumnName2 => 1, 'amount' =>  450000),
    array($keyColumnName2 => 2, 'amount' => 5500000),
    array($keyColumnName2 => 2, 'amount' =>    5000),
    array($keyColumnName2 => 4, 'amount' =>      10)
);
$source2 = new JoinController_SourceConfiguration($table2, $keyColumnName2, 'sourceB_', '_2');


function joinTables($joinMethod, $source1, $source2, $expectedRecordCount, $isOutputShown) {
    echo "----- '$joinMethod' join method ----------------------------------------\n";

    $joinController = join_controller_get_instance($joinMethod);

    $result = $joinController->join($source1, $source2);
    $isTestFailed = ($count = count($result->tableData)) != $expectedRecordCount;
    if ($isOutputShown || $isTestFailed) {
        var_dump($result);
    }

    if ($isTestFailed) {
        throw new IllegalStateException("Record count is $count instead of $expectedRecordCount");
    }

    if ($isOutputShown) {
        echo "\n";
    }
    else {
        echo "..... Ok\n";
    }
}

$showJoinResult = FALSE;

echo 'Supported methods : ' . implode(', ', join_controller_get_supported_methods()). "\n";

joinTables(JoinControllerFactory::$METHOD__INNER, $source1, $source2, 4, $showJoinResult);
joinTables(JoinControllerFactory::$METHOD__INNER, $source1, $source2, 4, $showJoinResult);
joinTables(JoinControllerFactory::$METHOD__LEFT_OUTER, $source1, $source2, 5, $showJoinResult);
joinTables(JoinControllerFactory::$METHOD__RIGHT_OUTER, $source1, $source2, 5, $showJoinResult);
joinTables(JoinControllerFactory::$METHOD__FULL, $source1, $source2, 6, $showJoinResult);
joinTables(JoinControllerFactory::$METHOD__CROSS, $source1, $source2, 15, $showJoinResult);
joinTables(JoinControllerFactory::$METHOD__UNION, $source1, $source2, 8, $showJoinResult);
