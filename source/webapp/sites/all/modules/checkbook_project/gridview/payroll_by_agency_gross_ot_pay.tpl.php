<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
?>
<?php
$isByGrossPay = ($node->nid == 494);

include_once('payroll_title.php');
include_once('page_title.php');
include_once('export_link.php');
?>

<table id="table_<?php echo widget_unique_identifier($node);?>" class="<?php echo $node->widgetConfig->gridConfig->html_class; ?>">
  <thead>
    <tr>
        <th class='text'><?php echo WidgetUtil::generateLabelMapping("agency_name");?></th>
        <th class='number'><?php echo WidgetUtil::generateLabelMapping("amount");?></th>
        <th>&nbsp</th>
    </tr>
  </thead>
  <tbody>
  <?php
        if (isset($node->data) && is_array($node->data)) {
            foreach ($node->data as $datarow) {
              echo "<tr>";
              echo "<td><div>".$datarow['agency_agency_agency_name']."</div></td>";
              echo "<td>".( $isByGrossPay ? $datarow['total_gross_pay'] : $datarow['total_overtime_pay'])."</td>";
              echo "<td>&nbsp</td>";
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
                        "sAltAjaxSource":"'. $_GET['q'] .'",
                        "fnDrawCallback"  :  function( oSettings ) {
            			    addPaddingToDataCells(this);
            			},
                        "aoColumnDefs": [
                            {
                                "aTargets": [0],
                                "sClass":"text",
                                "asSorting": [ "asc","desc" ]
                            },
                            {
                                "aTargets": [1],
                                "aExportFn":"function",
                                "mDataProp": function ( source, type, val ) {
                                    if (type == "set") {
                                        source.pay_amount = val;
                                        source.pay_amount_display = "<div>" + custom_number_format(val) + "</div>";
                                        return;
                                    }else if (type == "display") {
                                        return source.pay_amount_display;
                                    }
                                    return source.pay_amount;
                                },
                                "sClass":"number",
                                "asSorting": [ "desc", "asc" ]
                            },
                            {
                              "aTargets": [2],
                              "sWidth":"15px"
                            }
                        ]
                    }
                    ';
$node->widgetConfig->gridConfig->dataTableOptions = $dataTableOptions;
widget_highcharts_add_datatable_js($node);