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
include_once('revenue_title.php');
include_once('page_title.php');
include_once('export_link.php');

?>



<table id="table_<?php echo widget_unique_identifier($node) ?>" class="<?php echo $node->widgetConfig->gridConfig->html_class ?>">
    <thead>
    <?php
        echo "<tr><th class='number'>" . WidgetUtil::generateLabelMapping("year") . "</th>
        <th class='number'>"  .WidgetUtil::generateLabelMapping("recognized")  .  " </th>
        <th class='number'>" . WidgetUtil::generateLabelMapping("remaining"). "</th>
        <th>&nbsp</th>        
        </tr>\n";
    ?>
    </thead>

    <tbody>
    <?php
            foreach ($node->data as $datarow) {                
                echo "<tr>";
                   echo '<td>' . $datarow['year_year_year_value'] . '</td>';
                   echo '<td>' . $datarow['revenue_amount_sum'] . '</td>';
                   echo '<td>' . $datarow['remaining']   . '</td>';
                   echo "<td>&nbsp</td>";                   
                echo "</tr>";
            }
    ?>
    </tbody>
</table>
<?php
echo $node->widgetConfig->gridConfig->footer;

 $dataTableOptions ='
                    {
                        "bFilter":false,
                        "bInfo":false,
                        "bLengthChange":false,
                        "iDisplayLength":1,
                        "aaSorting":[[0,"asc"]],
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
                                        source.year_year_year_value = val;
                                        source.year_year_year_value_display = "<div>" + val + "</div>" ;
                                        return;
                                    }else if (type == "display") {
                                        return source.year_year_year_value_display;
                                    }else if (type == "sort") {
                                        return source.year_year_year_value;
                                    }
                                    return source.year_year_year_value;
                                },
                                "asSorting": [ "asc","desc" ],
                                "sWidth":"15px",
                                "sClass":"number"
                            },
                            {
                                "aTargets": [1],
                                "aExportFn":"function",
                                "mDataProp": function ( source, type, val ) {
                                    if (type == "set") {
                                        source.revenue_amount_sum = val;
                                        source.revenue_amount_sum_display = "<div>" + custom_number_format(val) + "</div>" ;
                                        return;
                                    }else if (type == "display") {
                                        return source.revenue_amount_sum_display;
                                    }else if (type == "sort") {
                                        return source.revenue_amount_sum;
                                    }
                                    return source.revenue_amount_sum;
                                },
                                "asSorting": [ "desc","asc" ],
                                "sClass":"number"
                            },
                            {
                                "aTargets": [2],
                                "aExportFn":"function",
                                "mDataProp": function ( source, type, val ) {
                                    if (type == "set") {
                                        source.remaining = val;
                                        source.remaining_display = "<div>" + custom_number_format(val) + "</div>" ;
                                        return;
                                    }else if (type == "display") {
                                        return source.remaining_display;
                                    }else if (type == "sort") {
                                        return source.remaining;
                                    }
                                    return source.remaining;
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