<?php
/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (c) 2012 – 2023 New York City
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Drupal\checkbook_widget_retroactivity\Service;

use Dom\Node;
use Drupal\checkbook_widget_retroactivity\Config\RetroactivityConfigService;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\checkbook_project\ContractsUtilities\ContractUtil;
use Drupal\checkbook_project\CommonUtilities\CheckbookDateUtil;
/**
 * Service to process the filter processing logic in Retroactivity Dashboard.
 */
class RetroactivityFilterProcessorService {

  /**
   * The config service.
   *
   * @var Drupal\checkbook_widget_retroactivity\Config\RetroactivityConfigService
   */
  protected $configService;

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */

  protected $moduleHandler;
  /**
   * The Contract Utility Class.
   *
   * @var \Drupal\checkbook_project\ContractsUtilities\ContractUtil
   */
  protected $contractUtil;

  /**
   * The Date Utility Class.
   *
   * @var \Drupal\checkbook_project\CommonUtilities\CheckbookDateUtil
   */
  protected $datetUtil;

  /**
   * Constructs a RetroactivityFilterProcessorService object.
   */

  public function __construct(ModuleHandlerInterface $moduleHandler, RetroactivityConfigService $configService) {
    $this->moduleHandler = $moduleHandler;
    $this->configService = $configService;
    $this->contractUtil = new ContractUtil();
    $this->datetUtil = new CheckbookDateUtil();
  }
  /**
   * Returns the dashboard configuration as an array.
   */
  public function adjustParametersFor(array $parameters, string $chartId) {
    $default_year_interval = $this->configService->getConfigVariable('defaultYearInterval');
    $current_fy = $this->datetUtil::_getFiscalYearID();
    $year_start_enabled = in_array($chartId, ['contract-volume-chart-1', 'contract-volume-chart-2', 'value-LateVsOntime', 'average-days-late-1']);
    //TBD
    $year_end_enabled = in_array($chartId, [
      'contract-volume-chart-1',
      'contract-volume-chart-2',
      'value-LateVsOntime',
      'average-days-late-1',
      'avg-days-late-by-agency',
      'payment-chart-1',
      'volume-HowLate',
      'ytd-lateness-rate-comparison']);
    if (empty($parameters)) {
      $parameters['year-end.year-end'][] = $current_fy;
      if ($year_start_enabled) {
        $parameters['year-start.year-start'][] = $current_fy - $default_year_interval;
      }
    }
    $adjustedParameters = $parameters;
    if (isset($adjustedParameters) && count($adjustedParameters) > 0) {
      foreach ($adjustedParameters as $key => $value) {
        switch ($key) {
          case 'year-start.year-start':
            $year_start = $value[0] < 112 ? 112 : $value[0];
            break;
          case 'year-end.year-end':
          case 'year.year':
            $year_end = $value[0] >  $current_fy ?  $current_fy : $value[0];
            break;
          case 'nonprofit_status.nonprofit_status':
            if (FALSE === in_array($value[0], [0,1])) {
              unset($adjustedParameters[$key]);
            }
            break;
          case 'vendor_customer_code.vendor_customer_code':
          case 'minority_type_id.minority_type_id':
          case 'agency_code.agency_code':
          case 'industry_type_id.industry_type_id':
            if (empty($value[0])) {
              unset($adjustedParameters[$key]);
            }
            break;
          case 'sort-order':
            unset($adjustedParameters[$key]);
            break;
        }
      }
      if ($year_start_enabled && empty($year_start)) {
        $year_start = empty($year_end) ? $current_fy - $default_year_interval : $year_end - $default_year_interval;
      }
      if ($year_end_enabled && empty($year_end)) {
        $year_end = $current_fy;
      }
      if ($chartId == 'ytd-lateness-rate-comparison') {
        $year_start = $year_end - 1;
        $adjustedParameters['year.year'] = array(data_controller_get_operator_factory_instance()->initiateHandler(\Drupal\data_controller\Datasource\Operator\Handler\RangeOperatorHandler::$OPERATOR__NAME, $year_start, $year_end));
        // Only apply YTD date filter for current fiscal year
        if ($year_end == $current_fy) {
          $today = new \DateTime();
          $fy_start_month = 7;
          $fy_start_day = 1;
          $selected_fy_year = $this->datetUtil::_getYearValueFromID($year_end);
          $ytd_start = new \DateTime($selected_fy_year . '-' . $fy_start_month . '-' . $fy_start_day);
          $adjustedParameters['registration_date.registration_date'] = array(data_controller_get_operator_factory_instance()->initiateHandler(\Drupal\data_controller\Datasource\Operator\Handler\RangeOperatorHandler::$OPERATOR__NAME, $ytd_start->format('Y-m-d'), $today->format('Y-m-d')));
        }
      }
      else {
        // Convert year-id to year.
        if ($year_end_enabled && $chartId == 'payment-chart-1') {
          $year_end = $this->datetUtil::_getYearValueFromID($year_end);
        }
        // Search by an year range only if Start Year filter is enabled.
        if ($year_start_enabled || $year_end_enabled) {
          $adjustedParameters['year.year'] = $year_start_enabled ?
            array(data_controller_get_operator_factory_instance()->initiateHandler(\Drupal\data_controller\Datasource\Operator\Handler\RangeOperatorHandler::$OPERATOR__NAME, $year_start, $year_end))
            : $year_end;
        }
      }
      // Cleanup temp variables.
      unset($adjustedParameters['year-start.year-start']);
      unset($adjustedParameters['year-end.year-end']);
    }

    return $adjustedParameters;
  }

  /**
   * Alter transformationPHP code for a specific chart.
   *
   */
  public function transformationPHPFor(string $chartId, $node) {
    switch ($chartId) {
      case 'contract-volume-chart-1':
        return $this->transformationPHPContractVolume1($node);
        break;

      case 'contract-volume-chart-2':
        return $this->transformationPHPContractVolume2($node);
        break;

      case 'volume-HowLate':
        return $this->transformationPHPContractVolume3($node);
        break;

      case 'ytd-lateness-rate-comparison':
        return $this->transformationPHPContractVolume4($node);
        break;

      case 'value-LateVsOntime':
        return $this->transformationPHPContractValue1($node);
        break;

      case 'average-days-late-1':
        return $this->transformationPHPAvgDaysLate1($node);
        break;

      case 'average-days-late-by-agency':
        return $this->transformationPHPAvgDaysLate2($node);
        break;

      case 'payment-chart-1':
        return $this->transformationPHPPaymentChart1($node);
        break;
    }

    return NULL;
  }

  /**
   * Dynamically calculate the width of an individual bar in a chart.
   * @param int $barCount
   */
  protected function getChartBarWidth($barCount) {
    $config = $this->configService->getRetroactivityConfig();
    $min_bar_width = $config['minBarWidth'];
    $max_bar_width = $config['maxBarWidth'];
    $yearly_progression_rate = $config['yearlyProgressionRateForBar'];
    $current_bar_width = $min_bar_width + ($config['defaultYearCount'] - $barCount) * $yearly_progression_rate;
    // Set default values.
    $bar_width = $current_bar_width < $min_bar_width ? $min_bar_width : ($current_bar_width > $max_bar_width ? $max_bar_width : $current_bar_width );

    return $bar_width;
  }

  /**
   * Processes Node object for transformationPHP in Contract Volume Chart 1.
   */
  protected function transformationPHPContractVolume1($node) {
    $yearStart = (int) ($node->widgetConfig->originalRequestParams['year-start'] ?? 0);
    $yearEnd = (int) ($node->widgetConfig->originalRequestParams['year-end'] ?? 0);
    // Find the year range for the submitted request.
    if ($yearStart > 0 && $yearEnd > 0 && $yearStart <= $yearEnd) {
      $yearRange = range($yearStart, $yearEnd);
    }
    else {
      $yearRange = [];
    }
    $yearData = array_fill_keys($yearRange, []);
    if (!empty($node->data)) {
      foreach ($node->data as $row) {
        $yearData[$row['year_year']] = $row;
      }
    }
    foreach ($yearData as $yearId => $row) {
      $year_label = $year = $this->datetUtil::_getYearValueFromID($yearId);
      // Convert 2022 to FY22.
      $year_label = 'FY' . substr($year_label, -2);
      $categories[] = $year_label;
      $ontime_data[] = [
        'name' => $year_label,
        'y' => isset($row['count_ontime_contracts']) && $row['count_ontime_contracts'] > 0 ? (int)$row['count_ontime_contracts']: NULL,
        'year' => 'FY ' . $year,
        'count_total' => (int)$row['total_contracts_sum']
        ];
      $retro_data[] = [
        'name' => $year_label,
        'y' => isset($row['count_retroactive_contracts']) && $row['count_retroactive_contracts'] > 0 ? (int)$row['count_retroactive_contracts']: NULL,
        'year' => 'FY ' . $year,
        'count_total' => (int)$row['total_contracts_sum']
      ];
      // Have a separate entry for no-data case.
      $zero_data[] = [
        'name' => $year_label,
        'y' => isset($row['year_year']) ? NULL: 0,
        'year' => 'FY ' . $year,
        'count_total' => 0
      ];
    }
    // Set years for x-axis.
    $node->widgetConfig->chartConfig->xAxis->categories = $categories;
    $node->widgetConfig->chartConfig->series = [
      ['name' => 'Late', 'data' => $retro_data, 'color' => '#d32f2f'],
      ['name' => 'On Time', 'data' => $ontime_data, 'color' => '#10A651'],
      ['name' => 'No Data', 'data' => $zero_data,
        'showInLegend' => FALSE, 'legendIndex' => FALSE,
        'includeInDataExport' => FALSE, 'linkedTo' => NULL,
        'color' => 'transparent',
        'states' => [
          'inactive' => [
            'opacity' => 1
          ],
          'hover' => [
            'enabled' => FALSE
          ]
        ],
        'dataLabels' => [
          'enabled' => TRUE,
          'inside' => FALSE,
          'color' => '#000000',
          'y' => -10,
          'style' => [
            'fontFamily' => 'Roboto, sans-serif',
            'fontWeight' => 'normal',
            'fontSize' => '10px',
            'textOutline' => 'none'
          ]
        ]]
    ];

    // Adjust bar width dynamically.
    $node->widgetConfig->chartConfig->plotOptions->column->pointWidth = $this->getChartBarWidth(count($yearData));

    return $node->data;
  }

  /**
   * Processes Node object for transformationPHP in Contract Volume Chart 2 .
   */
  protected function transformationPHPContractVolume2($node) {
    $all_zero = TRUE;
    $categories = [];
    $total_data = [];
    $late_pct_data = [];
    $data_by_year = [];
    $max_total = 0;
    $default_year_interval = 4;
    $current_fy = $this->datetUtil::_getFiscalYearID();
    $selected_start_id = drupal_static('retro_trend_selected_year_start');
    $selected_end_id = drupal_static('retro_trend_selected_year_end');

    $extract_year_id = function($raw_value) {
      if ($raw_value === NULL || $raw_value === '') {
        return NULL;
      }
      if (is_array($raw_value)) {
        $raw_value = reset($raw_value);
      }
      $raw_value = (string) $raw_value;
      $digits = preg_replace('/[^0-9]/', '', $raw_value);
      if ($digits === '') {
        return NULL;
      }
      $year_value = (int) $digits;
      return ($year_value > 1000) ? (100 + ($year_value % 100)) : $year_value;
    };

    if ($selected_start_id === NULL || $selected_end_id === NULL) {
      $request = \Drupal::request();
      $query_start = $request->query->get('year-start');
      $query_end = $request->query->get('year-end');
      $query_start_alt = $request->query->get('year_start');
      $query_end_alt = $request->query->get('year_end');

      $selected_start_id = $selected_start_id !== NULL
        ? $selected_start_id
        : ($extract_year_id($query_start) !== NULL ? $extract_year_id($query_start) : $extract_year_id($query_start_alt));

      $selected_end_id = $selected_end_id !== NULL
        ? $selected_end_id
        : ($extract_year_id($query_end) !== NULL ? $extract_year_id($query_end) : $extract_year_id($query_end_alt));

      $request_uri = $request->getRequestUri();

      if ($selected_start_id === NULL && preg_match('/year-start[^0-9]*([0-9]{3,4})/i', $request_uri, $matches)) {
        $selected_start_id = $extract_year_id($matches[1]);
      }

      if ($selected_end_id === NULL && preg_match('/year-end[^0-9]*([0-9]{3,4})/i', $request_uri, $matches)) {
        $selected_end_id = $extract_year_id($matches[1]);
      }
    }

    foreach ($node->data as $row) {
      $year_id = (int) $row['year_year'];
      $ontime = isset($row['count_ontime_contracts']) ? (float) $row['count_ontime_contracts'] : 0;
      $retro = isset($row['count_retroactive_contracts']) ? (float) $row['count_retroactive_contracts'] : 0;
      $total = $ontime + $retro;

      $data_by_year[$year_id] = [
        'total' => $total,
        'retro' => $retro,
      ];

      if ($total > 0) {
        $all_zero = FALSE;
      }

      if ($total > $max_total) {
        $max_total = $total;
      }
    }

    if ($selected_start_id === NULL) {
      $selected_start_id = $current_fy - $default_year_interval;
    }

    if ($selected_end_id === NULL) {
      $selected_end_id = $current_fy;
    }

    if ($selected_start_id < 112) {
      $selected_start_id = 112;
    }

    if ($selected_end_id > $current_fy) {
      $selected_end_id = $current_fy;
    }

    if ($selected_start_id > $selected_end_id) {
      $tmp_year_id = $selected_start_id;
      $selected_start_id = $selected_end_id;
      $selected_end_id = $tmp_year_id;
    }

    $yearRange = range($selected_start_id, $selected_end_id);
    $yearData = array_fill_keys($yearRange, []);

    foreach ($yearData as $year_id => $year_data) {
      $year = $this->datetUtil::_getYearValueFromID($year_id);
      $year_label = 'FY' . substr($year, -2);

      $total = isset($data_by_year[$year_id]) ? $data_by_year[$year_id]['total'] : 0;
      $retro = isset($data_by_year[$year_id]) ? $data_by_year[$year_id]['retro'] : 0;

      $late_pct = $total > 0 ? ($retro / $total) * 100 : 0;
      $display_late_pct = round($late_pct, 2);
      $plot_late_pct = ($display_late_pct == 0) ? 0.5 : $display_late_pct;

      $categories[] = $year_label;

      $total_data[] = [
        'name' => $year_label,
        'y' => $total > 0 ? $total : NULL,
      ];

      $late_pct_data[] = [
        'name' => $year_label,
        'y' => $plot_late_pct,
        'tableValue' => $display_late_pct,
        'style' => [
          'fontFamily' => 'Roboto, sans-serif',
          'fontWeight' => 'normal',
          'textOutline' => 'none',
          'color' => '#333',
          'fontSize' => '12px',
        ],
      ];
    }

    if ($all_zero) {
      $node->totalDataCount = 0;
    }

    $primary_tick_interval = 4;

    if ($max_total > 0) {
      $raw_interval = ceil(($max_total * 1.05) / 5);
      $nice_intervals = [
        1, 2, 5, 10, 20, 25, 50, 75, 100, 125,
        150, 175, 200, 250, 300, 400, 500, 750,
        1000, 1250, 1500, 1750, 2000, 2500, 3000,
        4000, 5000, 7500, 10000, 12500, 15000,
        20000, 25000, 50000, 100000,
      ];

      foreach ($nice_intervals as $nice_interval) {
        if ($raw_interval <= $nice_interval) {
          $primary_tick_interval = $nice_interval;
          break;
        }
      }
    }

    $primary_axis_max = $max_total > 0 ? $primary_tick_interval * 5 : 20;

    $node->widgetConfig->chartConfig->xAxis->categories = $categories;

    $node->widgetConfig->chartConfig->yAxis[0]->min = 0;
    $node->widgetConfig->chartConfig->yAxis[0]->max = $primary_axis_max;
    $node->widgetConfig->chartConfig->yAxis[0]->tickInterval = $primary_tick_interval;
    $node->widgetConfig->chartConfig->yAxis[0]->startOnTick = false;
    $node->widgetConfig->chartConfig->yAxis[0]->endOnTick = false;

    $node->widgetConfig->chartConfig->series = [
      [
        'name' => '# of contracts',
        'type' => 'column',
        'data' => $total_data,
        'color' => '#1BA1C2',
        'yAxis' => 0,
        'zIndex' => 1,
        'style' => [
          'fontFamily' => 'Roboto, sans-serif',
          'fontWeight' => 'normal',
          'textOutline' => 'none',
          'color' => '#333',
          'fontSize' => '12px',
        ],
      ],
      [
        'name' => '% late',
        'type' => 'line',
        'data' => $late_pct_data,
        'color' => '#C44D4D',
        'yAxis' => 1,
        'zIndex' => 4,
        'style' => [
          'fontFamily' => 'Roboto, sans-serif',
          'fontWeight' => 'normal',
          'textOutline' => 'none',
          'color' => '#333',
          'fontSize' => '12px',
        ],
      ],
    ];

    // Adjust bar width dynamically based on the full selected fiscal year range,
    // not based on returned DB rows. Returned rows can be fewer when filters only
    // have data for some years, which makes bars too wide.
    $node->widgetConfig->chartConfig->plotOptions->column->pointWidth = $this->getChartBarWidth(count($yearData));

    return $node->data;
  }
  /**
   * Processes Node object for transformationPHP in Contract Volume Chart 3.
   */
  protected function transformationPHPContractVolume3($node) {
    $series_data = [];
    if(count($node->data) > 0){
      // Fixed colors by category
      // 1 - On Time or Early (green), 2 - Late – Within 30 Days, 3 - Later – 31–180, 4 - Very Late – 181–365, 5 - Latest – >1 Year (red)
      $category_colors = [1 => '#8BC34A', 2 => '#D7A98F', 3 => '#D28C6B', 4 => '#8B3F1F', 5 => '#d32f2f'];
      foreach ($node->data as $row) {
        $category_id = $row['lateness_category_lateness_category'];
        $series_data [] = array(
          'name' => $row['lateness_category_lateness_category_lateness_category'],
          'y' => (int) $row['total_contracts_sum'],
          'color' => isset($category_colors[$category_id]) ? $category_colors[$category_id]: '#CCCCCC'
        );
      }
      // Set series
      $node->widgetConfig->chartConfig->series = [[
        'name' => 'Count of Doc_ID',
        'colorByPoint' => true,
        'data' => $series_data
      ]];
      return $node;
    }else{
      return $node->data;
    }
  }

  /**
   * Processes Node object for transformationPHP in Contract Volume Chart 4.
   */
  protected function transformationPHPContractVolume4($node) {
    $all_zero = TRUE;
    $year_data = [];
    $categories = [];
    $late_rate_data = [];

    // Get year_end from request parameters for YTD label logic
    $params = $node->widgetConfig->requestParams ?? [];
    $year_end = NULL;

    foreach ($node->data as $row) {
      // Track the maximum year_id for YTD logic
      if ($year_end === NULL || $row['year_year'] > $year_end) {
        $year_end = $row['year_year'];
      }

      $total_contracts = $row['count_ontime_contracts'] + $row['count_retroactive_contracts'];
      if ($total_contracts > 0) {
        $all_zero = FALSE;
        $late_rate = ($row['count_retroactive_contracts'] / $total_contracts) * 100;
        $late_rate = round($late_rate, 2);
      } else {
        $late_rate = 0;
      }
      $year = $this->datetUtil::_getYearValueFromID($row['year_year']);
      $year_label = 'FY' . substr($year, -2);
      $year_data[$year] = [
        'year' => $year,
        'year_id' => $row['year_year'],
        'total_contracts' => $total_contracts,
        'late_contracts' => $row['count_retroactive_contracts'],
        'ontime_contracts' => $row['count_ontime_contracts'],
        'late_rate' => $late_rate
      ];
      $categories[] = $year_label;
      $late_rate_data[] = array('name' => $year_label, 'y' => $late_rate);
    }
    if ($all_zero) {
      $node->totalDataCount = 0;
    }
    krsort($year_data);
    $year_data = array_values($year_data);

    $neutral_min = $node->widgetConfig->neutralRange->min ?? -0.0001;
    $neutral_max = $node->widgetConfig->neutralRange->max ?? 0.0001;

    $comparison_data = [
      'has_data' => false,
      'current_year' => null,
      'prior_year' => null,
      'percentage_diff' => 0,
      'is_improvement' => false,
      'is_neutral' => false,
      'is_degradation' => false,
      'neutral_range' => [
        'min' => $neutral_min,
        'max' => $neutral_max
      ]
    ];
    if (count($year_data) >= 2) {
      $comparison_data['current_year'] = $year_data[0];
      $comparison_data['prior_year'] = $year_data[1];

      // Check if either year has no contracts
      // Allow comparison even if late rate is 0% (all contracts on-time) since we're showing percentage point changes
      $current_has_data = isset($comparison_data['current_year']['total_contracts']) && $comparison_data['current_year']['total_contracts'] > 0;
      $prior_has_data = isset($comparison_data['prior_year']['total_contracts']) && $comparison_data['prior_year']['total_contracts'] > 0;

      if ($current_has_data && $prior_has_data) {
        $comparison_data['has_data'] = true;
        $comparison_data['percentage_diff'] = $comparison_data['current_year']['late_rate'] - $comparison_data['prior_year']['late_rate'];

        if ($comparison_data['percentage_diff'] < $neutral_min) {
          $comparison_data['is_improvement'] = true;
          $comparison_data['is_neutral'] = false;
          $comparison_data['is_degradation'] = false;
        } elseif ($comparison_data['percentage_diff'] >= $neutral_min && $comparison_data['percentage_diff'] <= $neutral_max) {
          $comparison_data['is_improvement'] = false;
          $comparison_data['is_neutral'] = true;
          $comparison_data['is_degradation'] = false;
        } else {
          $comparison_data['is_improvement'] = false;
          $comparison_data['is_neutral'] = false;
          $comparison_data['is_degradation'] = true;
        }
      }
    }

    $node->widgetConfig->comparisonData = $comparison_data;
    $node->widgetConfig->yearData = $year_data;
    $node->widgetConfig->currentFyId = $this->datetUtil::_getFiscalYearID();

    $card_context = 'Citywide';
    if (!empty($params['agency_id.agency_id'][0])) {
      $card_context = 'Agency';
    } elseif (!empty($params['vendor_id.vendor_id'][0])) {
      $card_context = 'Vendor';
    }
    $node->widgetConfig->cardContext = $card_context;
    $node->widgetConfig->selectedFyId = $year_end;

    $node->widgetConfig->chartConfig->xAxis->categories = $categories;
    $node->widgetConfig->chartConfig->series = array(array('name' => 'Late Registration Rate %', 'data' => $late_rate_data, 'color' => 'rgb(196, 77, 77)'));
    return $node;
  }

  /**
   * Processes Node object for transformationPHP in Contract Value Chart 1.
   */
  protected function transformationPHPContractValue1($node) {
    $ontime_value = [];
    $retro_value = [];
    $zero_value = [];
    $yearStart = (int) ($node->widgetConfig->originalRequestParams['year-start'] ?? 0);
    $yearEnd = (int) ($node->widgetConfig->originalRequestParams['year-end'] ?? 0);

    if ($yearStart > 0 && $yearEnd > 0 && $yearStart <= $yearEnd) {
      $yearRange = range($yearStart, $yearEnd);
    }
    else {
      $yearRange = [];
    }
    $yearData = array_fill_keys($yearRange, []);

    foreach ($node->data as $row) {
      $year = $row['year_year'];
      if (isset($yearData[$year])) {
        $yearData[$year][] = $row;
      }
    }

    foreach ($yearData as $year_id => $year_data) {
      $year = $this->datetUtil::_getYearValueFromID($year_id);
      $year_label = 'FY' . substr($year, -2);
      $row = $year_data[0];

      /*if (abs($row['total_ontime_amount_sum']) > 0) {
        $all_zero = FALSE;
      }

      if (abs($row['total_retroactive_amount_sum']) > 0) {
        $all_zero = FALSE;
      }*/
      $ontime = (float) $row['total_ontime_amount_sum'];
      $retro = (float) $row['total_retroactive_amount_sum'];

      $ontime_value[] = [
        'name' => $year_label,
        'y' => $ontime > 0 ? $ontime : null,
        'fiscal_year' => $year,
        'total_amount' => (float)$row['total_contract_amount_sum']
      ];
      $retro_value[] = [
        'name' => $year_label,
        'y' => $retro > 0 ? $retro : null,
        'fiscal_year' => $year,
        'total_amount' => (float)$row['total_contract_amount_sum']
      ];
      $zero_value[] = [
        'name' => $year_label,
        'y' => ($ontime == 0 && $retro == 0) ? 0 : null,
        'fiscal_year' => $year,
        'total_amount' => 0
      ];
    }

    /*if ($all_zero) {
      $node->totalDataCount = 0;
    }*/
    // Remove categories
    unset($node->widgetConfig->chartConfig->xAxis->categories);

    // Use category axis
    $node->widgetConfig->chartConfig->xAxis->type = 'category';

    // Set series
    $node->widgetConfig->chartConfig->series = [
      [
        'name' => 'Late',
        'data' => $retro_value,
        'color' => '#d32f2f'
      ],
      [
        'name' => 'On Time',
        'data' => $ontime_value,
        'color' => '#10A651'
      ],
      /** Set Data Label styling when the amounts are zero*/
      [
        'name' => 'No Data',
        'data' => $zero_value,
        'color' => 'transparent',
        'showInLegend' => FALSE,
        'enableMouseTracking' => FALSE,
        'linkedTo' => NULL,
        'includeInDataExport' => FALSE,
        'states' => [
          'inactive' => [
            'opacity' => 1
          ],
          'hover' => [
            'enabled' => FALSE
          ]
        ],
        'dataLabels' => [
          'enabled' => TRUE,
          'inside' => FALSE,
          'y' => -10,
          'style' => [
            'color' => '#000000',
            'fontWeight' => 'normal',
            'textOutline' => 'none'
          ]
        ]
      ]
    ];

    // Adjust bar width dynamically.
    // load configuration details.
    $node->widgetConfig->chartConfig->plotOptions->column->pointWidth = $this->getChartBarWidth(count($yearData));

    return $node->data;
  }

  /**
   * Processes Node object for transformationPHP in Average Days Late chart 1.
   *
   * Real DB 0 value => display 0.
   * Missing year in selected fiscal year range => display N/A.
   */
  protected function transformationPHPAvgDaysLate1($node) {
    $categories = [];
    $raw_values_by_year = [];
    $max_positive_value = 0;
    $max_negative_value = 0;
    $default_year_interval = 4;
    $current_fy = $this->datetUtil::_getFiscalYearID();
    $selected_start_id = drupal_static('avg_days_late_selected_year_start');
    $selected_end_id = drupal_static('avg_days_late_selected_year_end');

    $extract_year_id = function($raw_value) {
      if ($raw_value === NULL || $raw_value === '') {
        return NULL;
      }

      if (is_array($raw_value)) {
        $raw_value = reset($raw_value);
      }

      $raw_value = (string) $raw_value;
      $digits = preg_replace('/[^0-9]/', '', $raw_value);

      if ($digits === '') {
        return NULL;
      }

      $year_value = (int) $digits;

      return ($year_value > 1000) ? (100 + ($year_value % 100)) : $year_value;
    };

    if ($selected_start_id === NULL || $selected_end_id === NULL) {
      $request = \Drupal::request();

      $query_start = $request->query->get('year-start');
      $query_end = $request->query->get('year-end');
      $query_start_alt = $request->query->get('year_start');
      $query_end_alt = $request->query->get('year_end');

      $selected_start_id = $selected_start_id !== NULL
        ? $selected_start_id
        : ($extract_year_id($query_start) !== NULL ? $extract_year_id($query_start) : $extract_year_id($query_start_alt));

      $selected_end_id = $selected_end_id !== NULL
        ? $selected_end_id
        : ($extract_year_id($query_end) !== NULL ? $extract_year_id($query_end) : $extract_year_id($query_end_alt));

      $request_uri = $request->getRequestUri();

      if ($selected_start_id === NULL && preg_match('/year-start[^0-9]*([0-9]{3,4})/i', $request_uri, $matches)) {
        $selected_start_id = $extract_year_id($matches[1]);
      }

      if ($selected_end_id === NULL && preg_match('/year-end[^0-9]*([0-9]{3,4})/i', $request_uri, $matches)) {
        $selected_end_id = $extract_year_id($matches[1]);
      }
    }

    foreach ($node->data as $row) {
      if (!isset($row['year_year'])) {
        continue;
      }

      if (!array_key_exists('avg_days_late', $row) || $row['avg_days_late'] === NULL || $row['avg_days_late'] === '') {
        continue;
      }

      $year_id = (int) $row['year_year'];
      $rounded_value = round((float) $row['avg_days_late'], 0);

      if ($rounded_value > $max_positive_value) {
        $max_positive_value = $rounded_value;
      }

      if ($rounded_value < 0 && abs($rounded_value) > $max_negative_value) {
        $max_negative_value = abs($rounded_value);
      }

      $raw_values_by_year[$year_id] = $rounded_value;
    }

    if ($selected_start_id === NULL) {
      $selected_start_id = $current_fy - $default_year_interval;
    }

    if ($selected_end_id === NULL) {
      $selected_end_id = $current_fy;
    }

    if ($selected_start_id < 112) {
      $selected_start_id = 112;
    }

    if ($selected_end_id > $current_fy) {
      $selected_end_id = $current_fy;
    }

    if ($selected_start_id > $selected_end_id) {
      $tmp_year_id = $selected_start_id;
      $selected_start_id = $selected_end_id;
      $selected_end_id = $tmp_year_id;
    }

    $calculate_axis = function($max_value) {
      if ($max_value <= 0) {
        return [0, 0];
      }

      $raw_interval = ceil(($max_value * 1.10) / 5);
      $intervals = [1, 2, 5, 10, 12, 15, 20, 25, 30, 40, 50, 75, 100, 125, 150, 200, 250, 500, 1000, 2000, 5000];
      $selected_interval = end($intervals);

      foreach ($intervals as $interval) {
        if ($raw_interval <= $interval) {
          $selected_interval = $interval;
          break;
        }
      }

      return [$selected_interval * 5, $selected_interval];
    };

    list($positive_axis_limit, $positive_tick_interval) = $calculate_axis($max_positive_value);
    list($negative_axis_limit, $negative_tick_interval) = $calculate_axis($max_negative_value);

    if ($positive_axis_limit == 0) {
      $positive_axis_limit = 100;
      $positive_tick_interval = 20;
    }

    $positive_visual_max = 100;
    $negative_visual_min = $negative_axis_limit > 0 ? -100 : 0;

    $avg_days_data_positive = [];
    $avg_days_data_positive_small = [];
    $avg_days_data_negative = [];
    $avg_days_data_negative_small = [];
    $zero_label_data = [];
    $missing_label_data = [];
    $transformed_grid_data = [];

    for ($year_id = $selected_start_id; $year_id <= $selected_end_id; $year_id++) {
      $year_full = $this->datetUtil::_getYearValueFromID($year_id);
      $year_label = 'FY' . substr((string) $year_full, -2);
      $categories[] = $year_label;

      $has_data_for_year = array_key_exists($year_id, $raw_values_by_year);

      if (!$has_data_for_year) {
        $avg_days_data_positive[] = NULL;
        $avg_days_data_positive_small[] = NULL;
        $avg_days_data_negative[] = NULL;
        $avg_days_data_negative_small[] = NULL;
        $zero_label_data[] = NULL;

        $missing_label_data[] = [
          'name' => $year_label,
          'y' => 0,
          'isMissingYear' => TRUE,
          'noData' => TRUE,
        ];

        $transformed_grid_data[] = [
          'year' => $year_label,
          'avg_days_late' => 'N/A',
        ];

        continue;
      }

      $actual_value = (float) $raw_values_by_year[$year_id];
      $missing_label_data[] = NULL;

      if ($actual_value < 0) {
        $scaled_value = $negative_axis_limit > 0 ? ($actual_value / $negative_axis_limit) * 100 : 0;

        $avg_days_data_positive[] = NULL;
        $avg_days_data_positive_small[] = NULL;
        $zero_label_data[] = NULL;

        if (abs($actual_value) < 10) {
          $avg_days_data_negative[] = NULL;
          $avg_days_data_negative_small[] = [
            'name' => $year_label,
            'y' => $scaled_value,
            'actualValue' => $actual_value,
            'color' => '#10A651',
          ];
        }
        else {
          $avg_days_data_negative[] = [
            'name' => $year_label,
            'y' => $scaled_value,
            'actualValue' => $actual_value,
            'color' => '#10A651',
          ];
          $avg_days_data_negative_small[] = NULL;
        }
      }
      elseif ($actual_value > 0) {
        $scaled_value = $positive_axis_limit > 0 ? ($actual_value / $positive_axis_limit) * 100 : 0;

        $avg_days_data_negative[] = NULL;
        $avg_days_data_negative_small[] = NULL;
        $zero_label_data[] = NULL;

        if (abs($actual_value) < 10) {
          $avg_days_data_positive[] = NULL;
          $avg_days_data_positive_small[] = [
            'name' => $year_label,
            'y' => $scaled_value,
            'actualValue' => $actual_value,
            'color' => '#d32f2f',
          ];
        }
        else {
          $avg_days_data_positive[] = [
            'name' => $year_label,
            'y' => $scaled_value,
            'actualValue' => $actual_value,
            'color' => '#d32f2f',
          ];
          $avg_days_data_positive_small[] = NULL;
        }
      }
      else {
        $avg_days_data_positive[] = NULL;
        $avg_days_data_positive_small[] = NULL;
        $avg_days_data_negative[] = NULL;
        $avg_days_data_negative_small[] = NULL;

        $zero_label_data[] = [
          'name' => $year_label,
          'y' => 0,
          'actualValue' => 0,
        ];
      }

      $transformed_grid_data[] = [
        'year' => $year_label,
        'avg_days_late' => number_format($actual_value, 0, '.', ','),
      ];
    }

    $tick_positions = [];
    $axis_label_map = [];

    if ($negative_axis_limit > 0 && $negative_tick_interval > 0) {
      for ($i = 5; $i >= 1; $i--) {
        $visual_tick = -20 * $i;
        $actual_tick = -1 * $negative_tick_interval * $i;
        $tick_positions[] = $visual_tick;
        $axis_label_map[(string) $visual_tick] = $actual_tick;
      }
    }

    $tick_positions[] = 0;
    $axis_label_map['0'] = 0;

    if ($positive_axis_limit > 0 && $positive_tick_interval > 0) {
      for ($i = 1; $i <= 5; $i++) {
        $visual_tick = 20 * $i;
        $actual_tick = $positive_tick_interval * $i;
        $tick_positions[] = $visual_tick;
        $axis_label_map[(string) $visual_tick] = $actual_tick;
      }
    }

    $year_count = count($categories);

    $node->widgetConfig->chartConfig->xAxis->categories = $categories;
    $node->widgetConfig->chartConfig->yAxis->min = $negative_visual_min;
    $node->widgetConfig->chartConfig->yAxis->max = $positive_visual_max;
    $node->widgetConfig->chartConfig->yAxis->startOnTick = false;
    $node->widgetConfig->chartConfig->yAxis->endOnTick = false;
    $node->widgetConfig->chartConfig->yAxis->tickPositions = $tick_positions;
    $node->widgetConfig->chartConfig->yAxis->axisLabelMap = $axis_label_map;

    unset($node->widgetConfig->chartConfig->yAxis->tickInterval);

    $node->widgetConfig->chartConfig->series = [
      [
        'name' => 'Late Bars',
        'type' => 'column',
        'data' => $avg_days_data_positive,
        'showInLegend' => false,
        'minPointLength' => 0,
      ],
      [
        'name' => 'Late Bars Small',
        'type' => 'column',
        'data' => $avg_days_data_positive_small,
        'showInLegend' => false,
        'minPointLength' => 6,
      ],
      [
        'name' => 'On Time Bars',
        'type' => 'column',
        'data' => $avg_days_data_negative,
        'showInLegend' => false,
        'minPointLength' => 0,
      ],
      [
        'name' => 'On Time Bars Small',
        'type' => 'column',
        'data' => $avg_days_data_negative_small,
        'showInLegend' => false,
        'minPointLength' => 6,
      ],
      [
        'name' => 'Zero Labels',
        'type' => 'scatter',
        'data' => $zero_label_data,
        'showInLegend' => false,
        'enableMouseTracking' => false,
        'marker' => [
          'enabled' => false,
        ],
        'dataLabels' => [
          'enabled' => true,
          'inside' => false,
          'crop' => false,
          'overflow' => 'allow',
          'y' => -8,
          'style' => [
            'fontFamily' => 'Roboto, sans-serif',
            'fontSize' => '12px',
            'fontWeight' => 'normal',
            'color' => '#222222',
            'textOutline' => 'none',
          ],
          'function' => 'avgDaysLateZeroDataLabelFormatter',
        ],
      ],
      [
        'name' => 'Missing Labels',
        'type' => 'scatter',
        'data' => $missing_label_data,
        'showInLegend' => false,
        'enableMouseTracking' => false,
        'marker' => [
          'enabled' => false,
        ],
        'dataLabels' => [
          'enabled' => true,
          'inside' => false,
          'crop' => false,
          'overflow' => 'allow',
          'y' => -8,
          'style' => [
            'fontFamily' => 'Roboto, sans-serif',
            'fontSize' => '12px',
            'fontWeight' => 'normal',
            'color' => '#222222',
            'textOutline' => 'none',
          ],
          'function' => 'avgDaysLateMissingDataLabelFormatter',
        ],
      ],
      [
        'name' => 'On Time',
        'type' => 'line',
        'color' => '#10A651',
        'data' => [],
        'lineWidth' => 0,
        'showInLegend' => true,
        'enableMouseTracking' => false,
        'marker' => [
          'enabled' => true,
          'symbol' => 'square',
          'radius' => 8,
        ],
      ],
      [
        'name' => 'Late',
        'type' => 'line',
        'color' => '#d32f2f',
        'data' => [],
        'lineWidth' => 0,
        'showInLegend' => true,
        'enableMouseTracking' => false,
        'marker' => [
          'enabled' => true,
          'symbol' => 'square',
          'radius' => 8,
        ],
      ],
    ];

    $node->widgetConfig->chartConfig->plotOptions->column->pointWidth = $this->getChartBarWidth($year_count);
    $node->data = $transformed_grid_data;

    return $node->data;
  }

  /**
   * Processes Node object for transformationPHP in Average Days Late chart 2.
   */
  protected function transformationPHPAvgDaysLate2($node) {
    if (!count($node->data)) {
      $node->totalDataCount = 0;
      return $node->data;
    }

    $categories = [];
    $label_series = [];
    $data_series = [];

    foreach ($node->data as $row) {
      $agency_name = $row['agency_name_agency_name'];
      $agency_shortname = $row['bca_agency_short_name_bca_agency_short_name'] ?? $row['agency_short_name_agency_short_name'];
      $val = ROUND((float) $row['avg_days_late'], 0);

      $is_ontime = (strtolower(trim($row['lateness_label'])) === 'ontime');
      $display_val = $val;
      $categories[] = $agency_shortname;

      // Series 0: Labels anchored at Zero.
      $label_series[] = [
        'y' => 0,
        'agency' => $agency_shortname,
        'dataLabels' => [
          'enabled' => true,
          'align' => $is_ontime ? 'left' : 'right',
          'verticalAlign' => 'middle',
          'x' => $is_ontime ? 12 : -12,
          'y' => 1,
          'format' => '{point.agency}',
          'style' => ['fontFamily' => 'Roboto, sans-serif', 'fontWeight' => 'normal', 'textOutline' => 'none', 'color' => '#333', 'fontSize' => '12px'],
          'chart_custom_type' => $is_ontime ? 'ontime' : 'late'
        ],
        'legendType' => $is_ontime ? 'ontime' : 'late'
      ];

      // Series 1: Bars
      $data_series[] = [
        'y' => $display_val,
        'dataLabels' => [
          'align' => $is_ontime ? 'right' : 'left',
        ]
      ];

      //Ontime data
      if ($is_ontime) {
        $data_series_ontime[] = [
          'y' => $display_val,
          'color' => '#10A651',
          'agency_name' => $agency_name,
          'agency' => $agency_shortname,
          'is_truncated' => false,
          'real_value' => $val,
          'abs_value' => abs($val),
          'dataLabels' => [
            'enabled' => true,
            'align' => 'right',
            'x' => -5,
            'format' => '{point.real_value}',
            'style' => ['fontFamily' => 'Roboto, sans-serif', 'fontWeight' => 'normal', 'textOutline' => 'none', 'color' => '#333', 'fontSize' => '12px']
          ],
          'legendType' => 'ontime'
        ];
      }
      else {
        $data_series_ontime[] = [];
      }
      //Late data
      if (!$is_ontime) {
        $data_series_late[] = [
          'y' => $display_val,
          'color' => '#d32f2f',
          'agency_name' => $agency_name,
          'agency' => $agency_shortname,
          'is_truncated' => false,
          'real_value' => $val,
          'abs_value' => abs($val),
          'dataLabels' => [
            'enabled' => true,
            'align' => 'left',
            'x' => 5,
            'format' => '{point.real_value}',
            'style' => ['fontFamily' => 'Roboto, sans-serif', 'fontWeight' => 'normal', 'textOutline' => 'none', 'color' => '#333', 'fontSize' => '12px']
          ],
          'legend_type' => 'late'
        ];
      }
      else {
        $data_series_late[] = [];
      }
    }

    $node->widgetConfig->chartConfig->xAxis->categories = $categories;
    $node->widgetConfig->chartConfig->series = [
      [
        'name' => 'Labels',
        'data' => $label_series,
        'grouping' => false,
        'showInLegend' => false,
        'stacking' => null,
        'linkedTo' => ':main',
        'enableMouseTracking' => false,
        'color' => 'transparent',
        'zIndex' => 5
      ],
      [
        'name' => 'On Time',
        'color' => '#10A651',
        'type' => 'bar',
        'data' => $data_series_ontime,
        'stacking' => null,
        'zIndex' => 1,
        'grouping' => false,
        'showInLegend' => true,
        'enableMouseTracking' => true
      ],
      [
        'name' => 'Late',
        'color' => '#d32f2f',
        'type' => 'bar',
        'enableMouseTracking' => false,

        'data' => $data_series_late,
        'stacking' => null,
        'zIndex' => 1,
        'grouping' => false,
        'showInLegend' => true,
        'enableMouseTracking' => true
      ]
    ];

    // FIX: Loop over $data_series to find the limit.
    $max_val_left = 0;
    $max_val_right = 0;
    foreach ($data_series as $point) {
      if (abs($point['y']) > $max_val_left && $point['dataLabels']['align'] == 'right') {
        $max_val_left = abs($point['y']);
      }
      if (abs($point['y']) > $max_val_right && $point['dataLabels']['align'] == 'left') {
        $max_val_right = abs($point['y']);
      }
    }
    // Min scale for x axis.
    // There is no way to find the final tickPoint, so go for the minimum value.
    $min_data_point = 1;
    $limit_left = $max_val_left > $min_data_point ? $max_val_left * 1 : $min_data_point;
    $limit_right = $max_val_right > $min_data_point ? $max_val_right * 1 : $min_data_point;
    // Dynamically set left and right values for x axis.
    $node->widgetConfig->chartConfig->yAxis->min = -$limit_left;
    $node->widgetConfig->chartConfig->yAxis->max = $limit_right;

    // Find a ratio between bar width and character width to decide the cut off length of agency name label,
    // in case there is not enough room.
    $plot_width = $node->widgetConfig->chartConfig->chart->width - ($node->widgetConfig->chartConfig->chart->marginLeft + $node->widgetConfig->chartConfig->chart->marginRight);
    $total_range = abs($limit_left) + abs($limit_right);
    $pixels_per_day = $plot_width / $total_range;

    // Define a value to adjust the character to pixel size.
    $avg_char_width = 6;

    $available_pixels_left = abs($limit_left) * $pixels_per_day;
    // Maximum characters allowed on the left side of x axis.
    $max_chars_left = floor(($available_pixels_left) / $avg_char_width);

    $available_pixels_right = abs($limit_right) * $pixels_per_day;
    // Maximum characters allowed on the left side of y axis.
    $max_chars_right = floor(($available_pixels_right) / $avg_char_width);

    foreach ($node->widgetConfig->chartConfig->series[0]['data'] as $key => $arr_series_label) {
      $agency_name = $arr_series_label['agency'];
      if ($arr_series_label['dataLabels']['align'] == 'right') {
        if (strlen($agency_name) > $max_chars_left) {
          $agency_substr = substr($agency_name, 0, max(3, $max_chars_left));
          // Just ensure that the agency name is really cut off.
          if(strlen($agency_substr) != strlen($agency_name)) {
            $agency_name = $agency_substr  . '...';
          }
          $node->widgetConfig->chartConfig->series[0]['data'][$key]['agency'] = $agency_name;
        }
      }
      if ($arr_series_label['dataLabels']['align'] == 'left') {
        if (strlen($agency_name) > $max_chars_right) {
          $agency_substr = substr($agency_name, 0, max(3, $max_chars_right));
           if(strlen($agency_substr) != strlen($agency_name)) {
            $agency_name = $agency_substr  . '...';
          }
          $node->widgetConfig->chartConfig->series[0]['data'][$key]['agency'] = $agency_name;
        }
      }
    }
    // Set the sorting order.
    $sort_order = (int) ($node->widgetConfig->originalRequestParams['sort-order'] ?? 0);
    $node->widgetConfig->chartConfig->xAxis->reversed = $sort_order ? false : true;
    //Dynmically adjust the chart height based on data.
    if (count($node->data) <= 10) {
      $node->widgetConfig->chartConfig->chart->height = 420 ;
    }
    else {
      $node->widgetConfig->chartConfig->chart->height = count($node->data) * 35;
    }

    return $node->data;
  }

  /**
   * Return the configuration item for filter exclude entries.
   *
   */
  public function getFilterAutoCompleteExcludeValues(string $item) {
    return $this->configService->getConfigVariable($item);
  }

  /**
   * Processes Node object for transformationPHP in Payment chart 1.
   */
  protected function transformationPHPPaymentChart1($node) {
    // Pre-define the categories to make sure the bars exist even if data is not present.
    // Make sure the index matches the lateness_category_id in DB.
    $arr_lateness_categories = [
      1 => ['label' => 'On Time for Start', 'color' => '#10A651'],
      2 => ['label' => 'Within 30 Days of Start', 'color' => '#D0A28E'],
      3 => ['label' => 'Between 31 - 180 Days', 'color' => '#B66D4F'],
      4 => ['label' => 'Between 181 - 365 Days', 'color' => '#9C3D1B'],
      5 => ['label' => 'More than 1 Year after Start', 'color' => '#d32f2f'],
    ];

    $data = [];
    $categories = [];
    foreach ($arr_lateness_categories as $category_id => $arr_lateness) {
      // Set labels for x-axis.
      $categories[] = $arr_lateness['label'];
      $data = [];
      $data[0] = [
        'name' => $arr_lateness['label'],
        'lateness_category_id' => $category_id,
        'y' => 0,
        'total_check_amount_sum' => 0,
        'unique_check_count_sum' => 0,
        'showInLegend' => FALSE, 'legendIndex' => $category_id-1,
        'includeInDataExport' => FALSE, 'linkedTo' => NULL,
        'states' => [
          'inactive' => [
            'opacity' => 1
          ],
          'hover' => [
            'enabled' => FALSE
          ]
        ],
        'dataLabels' => [
          'enabled' => TRUE,
          'inside' => FALSE,
          'color' => '#000000',
          'y' => -23,
          'style' => [
            'fontFamily' => 'Roboto, sans-serif',
            'fontWeight' => 'normal',
            'fontSize' => '12px',
            'textOutline' => 'none'
          ]
        ]
      ];

      if (!empty($node->data)) {
        foreach ($node->data as $row) {
          if ($row['lateness_category_lateness_category'] == $category_id ) {
            // Unique check count will be equal to unique contracts.
            // So treat unique_check_count_sum as unique contracts.
            $data[0]['y'] = $row['unique_check_count_sum'] ? (int)$row['unique_check_count_sum']: 0;
            $data[0]['total_check_amount_sum'] = $row['total_check_amount_sum'] ? (int)$row['total_check_amount_sum']: 0;
            $data[0]['unique_check_count_sum'] = $row['unique_check_count_sum'] ? (int)$row['unique_check_count_sum']: 0;
            $data[0]['unique_vendor_count'] = $row['unique_vendor_count'] ? (int)$row['unique_vendor_count']: 0;
            break;
          }
        }
      }
      $node->widgetConfig->chartConfig->series[$category_id-1] = ['name' => $arr_lateness['label'], 'data' => $data, 'color' => $arr_lateness['color']];
    }
    // Set labels for x-axis.
    $node->widgetConfig->chartConfig->xAxis->categories = $categories;

    return $node->data;
  }
}
