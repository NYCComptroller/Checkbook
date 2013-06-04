<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
?>
<?php

include_once('spending_title.php');
include_once('page_title.php');
include_once('export_link.php');
?>

<table id="table_<?php echo widget_unique_identifier($node) ?>" class="<?php echo $node->widgetConfig->gridConfig->html_class ?>">
    <thead>

        <tr><th class='text'><?php echo WidgetUtil::generateLabelMapping("contract_id") ;?></th>
        <th class='number'><?php echo WidgetUtil::generateLabelMapping("spending_amount") ;?></th>
        <th class='text'><?php echo WidgetUtil::generateLabelMapping("vendor_name") ;?></th>
        <th class='text'><?php echo WidgetUtil::generateLabelMapping("contract_agency") ;?></th>
        <th>&nbsp</th>
        </tr>

    </thead>

    <tbody>
    <?php
        if (isset($node->data) && is_array($node->data)) {
            foreach ($node->data as $datarow) {
                echo '<tr>
                <td><div>' . $datarow['document_id'] . '</div></td>
                <td>' . $datarow['total_spending_amount'] . '</td>
                <td><div>' . $datarow['legal_name@checkbook:vendor'] . '</div></td>
                <td><div>' . $datarow['agency_name@checkbook:agency'] . '</div></td>
                <td>&nbsp</td>
                </tr>';
            }
        }
    ?>
    </tbody>
</table>
<?php
$dataTableOptions ='
                    {
                        "bFilter":false,
                        "bInfo":false,
                        "bLengthChange":false,
                        "iDisplayLength":12,
                        "aaSorting":[[1,"desc"]],
                        "bPaginate": false,
                        "sAltAjaxSource":"'. $_GET['q'] .'",
            			"fnDrawCallback"  :  function( oSettings ) {
            			addPaddingToDataCells(this);
            			},                                                
                        "aoColumnDefs": [
                            {
                                "aTargets": [0,2,3],
                                "sClass":"text",
                                "asSorting": [ "asc","desc" ]
                            },
                            {"aTargets":[0],"sWidth":"140px"},
                            {
                                "aTargets": [1],
                                "aExportFn":"function",
                                "mDataProp": function ( source, type, val ) {
                                    if (type == "set") {
                                        source.contract_amount = val;
                                        source.contract_amount_display =  "<div>" + custom_number_format(val) +"</div>" ;
                                        return;
                                    }else if (type == "display") {
                                        return source.contract_amount_display;
                                    }
                                    return source.contract_amount;
                                },
                                "sClass":"number",
                                "asSorting": [ "desc", "asc" ],
                                "sWidth":"75px"
                            },
                            {
                              "aTargets": [2]
                            },
                            {
                              "aTargets":[4],
                              "sWidth":"15px"
                            }
                        ]
                    }
                    ';
$node->widgetConfig->gridConfig->dataTableOptions = $dataTableOptions;
widget_highcharts_add_datatable_js($node);
