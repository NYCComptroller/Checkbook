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


if ( RequestUtilities::get("datasource") == "checkbook_oge") {
    $datasource ="/datasource/checkbook_oge";
}

//Main table header
$tbl['header']['title'] = "<h3>Spending by Expense Category</h3>";
$tbl['header']['columns'] = array(
    array('value' => WidgetUtil::generateLabelMappingNoDiv("expense_category"), 'type' => 'text'),
    array('value' => WidgetUtil::generateLabelMappingNoDiv("encumbered_amount"), 'type' => 'number'),
    array('value' => WidgetUtil::generateLabelMappingNoDiv("spent_to_date"), 'type' => 'number')
    );
$count = 0;
if (count($node->data) > 0) {
    foreach ($node->data as $row) {

        $spent_to_date_value = custom_number_formatter_format($row['spending_amount'], 2, '$');
        $spent_to_date = custom_number_formatter_format($row['spending_amount'], 2, '$');

        //Main table columns
        $tbl['body']['rows'][$count]['columns'] = array(
            array('value' => $row['expenditure_object_name'], 'type' => 'text'),
            array('value' => custom_number_formatter_format($row['encumbered_amount'], 2, '$'), 'type' => 'number'),
            array('value' => $spent_to_date_value, 'type' => 'number_link', 'link_value' => $spent_to_date)
        );
        $count += 1;
    }
}

$html = WidgetUtil::generateTable($tbl);
echo $html;

