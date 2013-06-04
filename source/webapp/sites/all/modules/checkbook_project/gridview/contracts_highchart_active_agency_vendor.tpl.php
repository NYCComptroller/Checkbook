<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
?>
<?php
$isAgencyPage = ($node->nid == 452 || $node->nid == 455);

if ($isAgencyPage){
  $rowname = 'agency_agency_agency_name';
} else {
  $rowname = 'vendor_vendor_legal_name';
}

include_once('contracts_title.php');
include_once('page_title.php');
include_once('export_link.php');
?>

<table id="table_<?php echo widget_unique_identifier($node);?>" class="<?php echo $node->widgetConfig->gridConfig->html_class; ?>">
  <thead>
    <tr>

        <?php
        if ($isAgencyPage){
            echo "<th class='text'>" . WidgetUtil::generateLabelMapping("contract_agency") . "</th>";
        } else {
            echo "<th class='text'>" . WidgetUtil::generateLabelMapping("vendor_name") . "</th>";
        }
        ?>
      <th class='number'><?php echo WidgetUtil::generateLabelMapping("no_of_contracts") ;?></th>
      <th class='number'><?php echo WidgetUtil::generateLabelMapping("current_amount") ;?></th>
      <th>&nbsp</th>
    </tr>
  </thead>
  <tbody>
  <?php
        if (isset($node->data) && is_array($node->data)) {
            foreach ($node->data as $datarow) {
              echo "<tr>";
              echo "<td><div>".$datarow[$rowname]."</div></td>";
              echo "<td>". $datarow['total_contracts']."</td>";
              echo "<td>".$datarow['current_amount_sum']."</td>";
              echo '<td>&nbsp</td>';
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
                        "aaSorting":[[2,"desc"]],
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
                                        source.total_contracts = val;
                                        source.total_contracts_display =  "<div>" + addCommas(val) + "</div>";
                                        return;
                                    }else if (type == "display") {
                                        return source.total_contracts_display;
                                    }
                                    return source.total_contracts;
                                },
                                "sClass":"number",
                                "asSorting": [ "desc", "asc" ]
                            },
                            {
                                "aTargets": [2],
                                "aExportFn":"function",
                                "mDataProp": function ( source, type, val ) {
                                    if (type == "set") {
                                        source.budget_ytd = val;
                                        source.budget_ytd_display =  "<div>" + custom_number_format(val) + "</div>";
                                        return;
                                    }else if (type == "display") {
                                        return source.budget_ytd_display;
                                    }
                                    return source.budget_ytd;
                                },
                                "sClass":"number",
                                "asSorting": [ "desc", "asc" ]
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