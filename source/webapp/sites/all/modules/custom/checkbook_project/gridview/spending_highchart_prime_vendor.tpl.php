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
include_once('spending_title.php');
include_once('page_title.php');
include_once('export_link.php');
?>

<table id="table_<?php echo widget_unique_identifier($node);?>" class="<?php echo $node->widgetConfig->gridConfig->html_class; ?>">
  <thead>
    <tr>
        <th class='text'><?php echo WidgetUtil::generateLabelMapping("prime_vendor") ;?></th>
        <th class='number'><?php echo WidgetUtil::generateLabelMapping("spending_amount") ;?></th>
        <th>&nbsp;</th>
    </tr>
  </thead>
  <tbody>
  <?php
        if (isset($node->data) && is_array($node->data)) {
            foreach ($node->data as $datarow) {
              echo "<tr>";
              echo "<td><div>".$datarow['prime_vendor_prime_vendor_legal_name']."</div></td>";
              echo "<td>".$datarow['check_amount_sum']."</td>";
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
                                "sClass":"text",
                                "asSorting": [ "asc","desc" ]
                            },
                            {
                                "aTargets": [1],
                                "aExportFn":"function",
                                "mDataProp": function ( source, type, val ) {
                                    if (type == "set") {
                                        source.total_amount = val;
                                        source.total_amount_display = "<div>" + custom_number_format(val) + "</div>";
                                        return;
                                    }else if (type == "display") {
                                        return source.total_amount_display;
                                    }
                                    return source.total_amount;
                                },
                                "sClass":"number",
                                "asSorting": [ "desc","asc" ]
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
