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

include_once('budget_title.php');
include_once('page_title.php');
include_once('export_link.php');


?>

<table id="table_<?php echo widget_unique_identifier($node) ?>" class="<?php echo $node->widgetConfig->gridConfig->html_class ?>">
    <thead>
    <?php
        echo "<tr><th class='text'>" . WidgetUtil::generateLabelMapping("expense_category") . "</th>
        <th class='number'>"  .WidgetUtil::generateLabelMapping("committed")  .  " </th>
        <th class='number'>" . WidgetUtil::generateLabelMapping("remaining"). "</th>
        <th>&nbsp;</th>        
        </tr>\n";
    ?>
    </thead>

    <tbody>
<?php
        if (isset($node->data) && is_array($node->data)) {
            foreach ($node->data as $datarow) {
                echo "<tr>";
                   echo '<td>' . $datarow['object_class_name_object_class_name'] . '</td>';
                   echo '<td>' . $datarow['budget_committed'] . '</td>';
                   echo '<td>' . $datarow['budget_remaining'] . '</td>';
                   echo "<td>&nbsp;</td>";
                echo "</tr>";
            }
        }
    ?>
    </tbody>
</table>
<?php
echo eval($node->widgetConfig->gridConfig->footer);

 $dataTableOptions ='
                    {
                        "bFilter":false,
                        "bInfo":false,
                        "bLengthChange":false,
                        "iDisplayLength":10,
                        "aaSorting":[[1,"desc"]],
                        "bPaginate": false,
                        "sAltAjaxSource":"'. check_plain($_GET['q']) .'",
            			"fnDrawCallback"  :  function( oSettings ) {
            			addPaddingToDataCells(this);
            			},
                        "aoColumnDefs": [
                            {
                                "aTargets": [0],
                                "mDataProp": function ( source, type, val ) {
                                    if (type == "set") {
                                        source.object_class_name_object_class_name = val;
                                        source.object_class_name_object_class_name_display = "<div>" + val + "</div>";
                                        return;
                                    }else if (type == "display") {
                                        return source.object_class_name_object_class_name_display;
                                    }else if (type == "sort") {
                                        return source.object_class_name_object_class_name;
                                    }
                                    return source.object_class_name_object_class_name;
                                },
                                "asSorting": [ "desc","asc" ],
                                "sClass":"text"
                            },
                            {
                                "aTargets": [1],
                                "aExportFn":"function",
                                "mDataProp": function ( source, type, val ) {
                                    if (type == "set") {
                                        source.budget_committed = val;
                                        source.budget_committed_display = "<div>" + custom_number_format(val) + "</div>";
                                        return;
                                    }else if (type == "display") {
                                        return source.budget_committed_display;
                                    }else if (type == "sort") {
                                        return source.budget_committed;
                                    }
                                    return source.budget_committed;
                                },
                                "asSorting": [ "desc","asc" ],
                                "sClass":"number"
                            },
                            {
                                "aTargets": [2],
                                "aExportFn":"function",
                                "mDataProp": function ( source, type, val ) {
                                    if (type == "set") {
                                        source.budget_remaining = val;
                                        source.budget_remaining_display =  "<div>" + custom_number_format(val) + "</div>";
                                        return;
                                    }else if (type == "display") {
                                        return source.budget_remaining_display;
                                    }else if (type == "sort") {
                                        return source.budget_remaining;
                                    }
                                    return source.budget_remaining;
                                },
                                "asSorting": [ "desc","asc" ],
                                "sClass":"number"
                            },
                            {
                              "aTargets": [3],
                              "sWidth":"15px"
                            }

                        ]
                    }
                    ';

$node->widgetConfig->gridConfig->dataTableOptions = $dataTableOptions;
widget_highcharts_add_datatable_js($node);
