<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
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
        echo "<tr><th class='text'>" . WidgetUtil::generateLabelMapping("funding_class") . "</th>
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
                   echo '<td>' . $datarow['funding_funding_funding_class_name'] . '</td>';
                   echo '<td>' . $datarow['revenue_amount_sum'] . '</td>';
                   echo '<td>' . $datarow['remaining']   . '</td>';
                   echo "<td>&nbsp</td>";                   
                echo "</tr>";
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
                        "iDisplayLength":1,
                        "aaSorting":[[1,"desc"]],
                        "bPaginate": false,
                        "sAltAjaxSource":"'. $_GET['q'] .'",
            			"fnDrawCallback"  :  function( oSettings ) {
            			addPaddingToDataCells(this);
            			},
                        "aoColumnDefs": [
                            {
                                "aTargets": [0],
                                "mDataProp": function ( source, type, val ) {
                                    if (type == "set") {                                        
                                        source.funding_funding_funding_class_name = val;
                                        source.funding_funding_funding_class_name_display = "<div>" + val + "</div>" ;
                                        return;
                                    }else if (type == "display") {
                                        return source.funding_funding_funding_class_name_display;
                                    }else if (type == "sort") {
                                        return source.funding_funding_funding_class_name;
                                    }
                                    return source.funding_funding_funding_class_name;
                                },
                                "asSorting": [ "desc","asc" ],
                                "sClass":"text"
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