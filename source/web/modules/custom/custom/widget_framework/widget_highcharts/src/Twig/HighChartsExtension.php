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

namespace Drupal\widget_highcharts\Twig;

use Drupal\checkbook_project\WidgetUtilities\WidgetUtil;
use Drupal\Core\Render\Markup;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class HighChartsExtension extends AbstractExtension {

  /**
   * Generates a list of all Twig functions that this extension defines.
   *
   * @return array
   *   A key/value array that defines custom Twig functions. The key denotes the
   *   function name used in the tag, e.g.:
   *   @code
   *   {{ testfunc() }}
   *   @endcode
   *
   *   The value is a standard PHP callback that defines what the function does.
   */
  public function getFunctions() {
    return [
      'widget_highcharts_add_js' => new TwigFunction('widget_highcharts_add_js', [$this, 'widget_highcharts_add_js']),
      'widget_highstocks_add_js' => new TwigFunction('widget_highstocks_add_js', [$this, 'widget_highstocks_add_js']),
      'widget_highcharts_add_datatable_js' => new TwigFunction('widget_highcharts_add_datatable_js', [$this, 'widget_highcharts_add_datatable_js']),
      'widget_url' => new TwigFunction('widget_url', [$this, 'widget_url']),
      'widget_get_table_header' => new TwigFunction('widget_get_table_header', [$this, 'widget_get_table_header']),
      'widget_highstocks_header' => new TwigFunction('widget_highstocks_header', [$this, 'widget_highstocks_header']),
      'widget_highstocks_footer' => new TwigFunction('widget_highstocks_footer', [$this, 'widget_highstocks_footer'])
    ];
  }

  public function widget_url() {
    return \Drupal::request()->query->get('q');
  }
  public function widget_get_table_header($rowname) {
    return WidgetUtil::generateLabelMapping($rowname);
  }

  public function widget_highcharts_add_js($node){
    $val = widget_mergeJSFunctions($node, $node->widgetConfig->chartConfig);
    $val2 = (isset($node->widgetConfig->callback)) ? ',function(chart){'.$node->widgetConfig->callback.'}' : "";
    $js = "
		    jQuery(document).ready(function() {
        var chart = new Highcharts.Chart(".$val.$val2 .");
        Highcharts.chartarray.push(chart);
    });"
    ;
    $node->widgetConfig->highchartsjs =  $js;
    return $js;
  }

  function widget_highcharts_add_datatable_js($dataTableOptions,$node) {
    $node->widgetConfig->gridConfig->dataTableOptions = $dataTableOptions;
    $id  = widget_unique_identifier($node);
    $js = "
		    var oTable" . $id  .  ";
		    var "."\$j"."= jQuery.noConflict();".
        "\$j"."(document).ready(function() {
		        oTable" . $id  .  " = "."\$j"."('#table_" . widget_unique_identifier($node) . "')" .
		        ".on('preXhr.dt', function() {\$j(this).addClass('datatable-ajax-started');}).on('xhr.dt', function() {\$j(this).addClass('datatable-ajax-completed');})" .
		        ".dataTable(
		        " . $node->widgetConfig->gridConfig->dataTableOptions . ");
        }
        );"
    ;
    return $js;
  }

  public function widget_highstocks_header($node){
    return eval($node->widgetConfig->header);
  }
  public function widget_highstocks_footer($node){
    return eval($node->widgetConfig->footer);
  }
  public function widget_highstocks_add_js($node){
    $val = widget_mergeJSFunctions($node, $node->widgetConfig->chartConfig);
    $val2 = (isset($node->widgetConfig->callback)) ? ',function(chart){'.$node->widgetConfig->callback.'}' : "";

    $js = "
		    jQuery(document).ready(function() {
        var chart = new Highcharts.StockChart(".$val.$val2 .");
        Highcharts.chartarray.push(chart);
    });"
    ;
    $node->widgetConfig->highchartsjs =  $js;
    return Markup::create($js);
  }
}
