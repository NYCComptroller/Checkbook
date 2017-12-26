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
$isAgencyPage = ($node->nid == 457 || $node->nid == 458);

if ($isAgencyPage){
  $rowname = 'document_agency_name_document_agency_name';
} else {
  $rowname = 'vendor_legal_name_vendor_legal_name';
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
            echo "<th class='text'>" . WidgetUtil::generateLabelMapping("prime_vendor") . "</th>";
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
    	if ($datarow['total_num_rev_pending_contracts']){
    		$total = $datarow['total_num_rev_pending_contracts'];
    	} else {
    		$total = $datarow['total_contracts'];
    	}
      echo "<tr>";
      echo "<td><div>".$datarow[$rowname]."</div></td>";
      echo "<td>".$total."</td>";
      echo "<td>".$datarow['total_contract_amount']."</td>";
      echo '<td>&nbsp</td>';
      echo "</tr>";
    }
  }
  ?>
  </tbody>
</table>
<?php
echo  $node->widgetConfig->gridConfig->footer;
    $dataTableOptions ='
                    {
                        "bFilter":false,
                        "bInfo":false,
                        "bLengthChange":false,
                        "iDisplayLength":10,
                        "aaSorting":[[2,"desc"]],
                        "bPaginate": false,
                        "sAltAjaxSource":"'. check_plain($_GET['q']) .'",
            			"fnDrawCallback"  :  function( oSettings ) {
            			addPaddingToDataCells(this);
            			},                                                
                        "aoColumnDefs": [
                            {
                                "aTargets": [0],
                                "sClass":"text  text-sort2",
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
