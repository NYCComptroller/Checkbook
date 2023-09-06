<?php

namespace Drupal\widget_config\Twig\Spending;

use Drupal\checkbook_custom_breadcrumbs\SpendingBreadcrumbs;
use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_project\CommonUtilities\CheckbookDateUtil;
use Drupal\checkbook_project\MwbeUtilities\MappingUtil;
use Drupal\checkbook_project\SpendingUtilities\SpendingUtil;
use Drupal\checkbook_project\WidgetUtilities\WidgetProcessor;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SpendingConfigExtension extends AbstractExtension
{
  public function getFunctions()
  {
    return [
      'spendingSummaryTitle' => new TwigFunction('spendingSummaryTitle', [
        $this,
        'spendingSummaryTitle',
      ]),
      'spendingMwbeCatName' => new TwigFunction('spendingMwbeCatName', [
        $this,
        'spendingMwbeCatName',
      ]),
      'spendingContractGrid' => new TwigFunction('spendingContractGrid', [
        $this,
        'spendingContractGrid',
      ]),
      'spendingTitle' => new TwigFunction('spendingTitle', [
        $this,
        'spendingTitle',
      ]),
      'spendingFyGrid' => new TwigFunction('spendingFyGrid', [
        $this,
        'spendingFyGrid',
      ]),
      'spendingDateAmount' => new TwigFunction('spendingDateAmount', [
       $this,
       'spendingDateAmount',
      ]),
      'spendingMonth' => new TwigFunction('spendingMonth', [
        $this,
        'spendingMonth',
      ]),
      'spendingDeptName'=> new TwigFunction('spendingDeptName', [
        $this,
        'spendingDeptName',
      ]),
    ];
  }

  public function spendingSummaryTitle($totalAggregateColumns, $row)
  {
    return SpendingUtil::getPercentYtdSpending($totalAggregateColumns, $row);
  }

  public function spendingMwbeCatName($mwbeId)
  {
    return MappingUtil::getMinorityCategoryById($mwbeId);
  }

  public static function spendingTitle()
  {
    return SpendingBreadcrumbs::getSpendingPageTitle();
  }

  public function spendingContractGrid($node)
  {
    if (isset($node->data) && is_array($node->data)) {
      $output = '';
      foreach ($node->data as $datarow) {
        $vendor_name = (isset($datarow['legal_name@checkbook:vendor']))? $datarow['legal_name@checkbook:vendor']:$datarow['legal_name@checkbook:prime_vendor'] ;
        $output .= '<tr>
                <td><div>' . $datarow['document_id'] . '</div></td>
                <td>' . $datarow['total_spending_amount'] . '</td>
                <td>&nbsp;&nbsp;</td>
                <td><div>' . $vendor_name . '</div></td>
                <td><div>' . $datarow['agency_name@checkbook:agency'] . '</div></td>
                <td>&nbsp;</td>
                </tr>';
      }
    }
    return $output;
  }

  public function spendingDateAmount($node)
  {
    $amount = WidgetProcessor::_checkbook_project_pre_process_aggregation($node,'check_amount_sum');
    return $amount;
  }

  public function spendingMonth()
  {
    $monthDetails = CheckbookDateUtil::getMonthDetails(RequestUtilities::_getRequestParamValueBottomURL('month'));
    return $monthDetails;
  }

  public function spendingFyGrid($node)
  {

    $hidePrevLabel = (isset($node->widgetConfig->chartConfig->series[0]->showInLegend) && ($node->widgetConfig->chartConfig->series[0]->showInLegend == false));

    $SeriesPreviousYearLabel = $node->widgetConfig->chartConfig->series[0]->name;
    $SeriesCurrentYearLabel = $node->widgetConfig->chartConfig->series[1]->name;

    $output = "<thead>";
    $output.="<tr><th class='text'><div><span>Month</span></div></th>"
    . ($hidePrevLabel ? "" : "<th class='number'><div><span>$SeriesPreviousYearLabel</span></div></th>");
    $output .= "<th class='number'><div><span>$SeriesCurrentYearLabel</span></div></th>
               <th>&nbsp;</th></tr>\n" . "</thead><tbody>";
    $months = array();
    $data = $node->widgetConfig->gridConfig->data ?? null;

    if (Datasource::isNYCHA()) {
      if (isset($data) && is_array($data)) {
        $cnt = 1;
        foreach ($data as $datarow) {
          $months[] = $datarow[0];
          $output .= "<tr>";
          $output .= '<td>' . $cnt . '</td>';
          $output .= ($hidePrevLabel ? '' : ('<td>' . $datarow[1] . '</td>'));
          $output .= '<td>' . $datarow[2] . '</td>';
          $output .= "<td>&nbsp;</td>";
          $output .= "</tr>";
          $cnt++;
        }
      } else {
        if (isset($node->data) && is_array($node->data)) {
          $cnt = 1;
          foreach ($node->data as $datarow) {
            $months[] = $datarow['month_month_month_name'];
            $output .= "<tr>";
            $output .= '<td>' . $cnt . '</td>' .
              ($hidePrevLabel ? '' : ('<td>' . $datarow['previous_spending'] . '</td>'));
            $output .= '<td>' . $datarow['current_spending'] . '</td>';
            $output .= "<td>&nbsp;</td>";
            $output .= "</tr>";
            $cnt++;
          }
        }
      }
    }
    else{
      $months = array();
      if (isset($node->data) && is_array($node->data)) {
        $cnt =1;
        foreach ($node->data as $datarow) {
          $months[] = $datarow['month_month_month_name'];
          echo "<tr>";
          echo '<td>' . $cnt . '</td>';
          echo ( $hidePrevLabel ? '' : ('<td>' . $datarow['previous_spending'] . '</td>') );
          echo '<td>' . $datarow['current_spending'] . '</td>';
          echo "<td>&nbsp;</td>";
          echo "</tr>";
          $cnt++;
        }
      }
    }


    $output .=  "</tbody ></table >\n";

if (!$hidePrevLabel) {
  $dataTableOptions = '
                    {
                        "bFilter":false,
                        "bInfo":false,
                        "bLengthChange":false,
                        "iDisplayLength":12,
                        "aaSorting":[[0,"asc"]],
                        "bPaginate": false,
                        "sAltAjaxSource":"' . \Drupal::request()->query->get('q') . '",
            			"fnDrawCallback"  :  function( oSettings ) {
            			addPaddingToDataCells(this);
            			},
                        "aoColumnDefs": [
                            {
                                "aTargets": [0],
                                "mDataProp": function ( source, type, val ) {
                                    if (type == "set") {
                                        var monthList = ["' . implode("\",\"", $months) . '"];
                                        source.month = val;
                                        source.month_display = "<div>" + monthList[(val-1)] + "</div>";
                                        return;
                                    }else if (type == "display") {
                                        return source.month_display;
                                    }else if (type == "sort") {
                                        return source.month;
                                    }
                                    return source.month;
                                },
                                "sClass":"text",
                                "asSorting": [ "asc","desc" ],
                                "sWidth":"180px"
                            },
                            {
                                "aTargets": [1],
                                "aExportFn":"function",
                                "mDataProp": function ( source, type, val ) {
                                    if (type == "set") {
                                        source.previous_spending = val;
                                        source.previous_spending_display =  "<div>" + custom_number_format(val) + "</div>";
                                        return;
                                    }else if (type == "display") {
                                        return source.previous_spending_display;
                                    }
                                    return source.previous_spending;
                                },
                                "sClass":"number",
                                "asSorting": [ "desc", "asc" ],
                                "sWidth":"300px"
                            },
                            {
                                "aTargets": [2],
                                "aExportFn":"function",
                                "mDataProp": function ( source, type, val ) {
                                    if (type == "set") {
                                        source.current_spending = val;
                                        source.current_spending_display =  "<div>" + custom_number_format(val) + "</div>";
                                        return;
                                    }else if (type == "display") {
                                        return source.current_spending_display;
                                    }
                                    return source.current_spending;
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
} else {
  $dataTableOptions = '
                    {
                        "bFilter":false,
                        "bInfo":false,
                        "bLengthChange":false,
                        "iDisplayLength":12,
                        "aaSorting":[[0,"asc"]],
                        "bPaginate": false,
                        "sAltAjaxSource":"' . \Drupal::request()->query->get('q') . '",
            			"fnDrawCallback"  :  function( oSettings ) {
            			addPaddingToDataCells(this);
            			},
                        "aoColumnDefs": [
                            {
                                "aTargets": [0],
                                "mDataProp": function ( source, type, val ) {
                                    if (type == "set") {
                                        var monthList = ["' . implode("\",\"", $months) . '"];
                                        source.month = val;
                                        source.month_display = "<div>" + monthList[(val-1)] + "</div>";
                                        return;
                                    }else if (type == "display") {
                                        return source.month_display;
                                    }else if (type == "sort") {
                                        return source.month;
                                    }
                                    return source.month;
                                },
                                "sClass":"text",
                                "asSorting": [ "asc","desc" ],
                                "sWidth":"180px"
                            },
                            {
                                "aTargets": [1],
                                "aExportFn":"function",
                                "mDataProp": function ( source, type, val ) {
                                    if (type == "set") {
                                        source.current_spending = val;
                                        source.current_spending_display =  "<div>" + custom_number_format(val) + "</div>";
                                        return;
                                    }else if (type == "display") {
                                        return source.current_spending_display;
                                    }
                                    return source.current_spending;
                                },
                                "sClass":"number",
                                "asSorting": [ "desc", "asc" ],
                                "sWidth":"300px"
                            },
                            {
                              "aTargets": [2],
                              "sWidth":"15px"
                            }

                        ]
                    }
                    ';
  }
$node->widgetConfig->gridConfig->dataTableOptions = $dataTableOptions;
return $output;
}
  public function spendingDeptName($agency_id,$spending_category_value,$year_id,$deptcode,$datasource)
  {

    $dept = "'".$deptcode."'";
    $spending_category = isset($spending_category_value) ? ' AND s0.spending_category_id ='.$spending_category_value : '';
    $query = "SELECT  j.agency_agency, j.department_department,j1.department_name AS department_department_department_name
                  FROM (SELECT s0.agency_id AS agency_agency,s0.department_code AS department_department,s0.department_id
                        FROM aggregateon_spending_coa_entities s0
                        WHERE s0.agency_id = ".$agency_id. $spending_category ."
                        AND s0.year_id = ".$year_id." AND s0.department_code = ".$dept."
                        GROUP BY s0.agency_id, s0.department_code, s0.year_id,s0.department_id) j
                 LEFT OUTER JOIN ref_department j1 ON j1.department_code = j.department_department and j1.department_id = j.department_id
                  LIMIT 1";
    if($datasource=='checkbook_oge'){
      $result = _checkbook_project_execute_sql_by_data_source($query, Datasource::OGE);
    }
    else {
      $result = _checkbook_project_execute_sql_by_data_source($query);
    }
    //var_dump($query);
   return htmlentities($result[0]['department_department_department_name']);
  }
}
