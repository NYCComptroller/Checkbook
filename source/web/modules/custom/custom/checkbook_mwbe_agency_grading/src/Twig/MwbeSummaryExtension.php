<?php

namespace Drupal\checkbook_mwbe_agency_grading\Twig;

use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_project\MwbeUtilities\MappingUtil;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MwbeSummaryExtension extends AbstractExtension
{

  /**
   * Generates a list of all Twig functions that this extension defines.
   *
   * @return array
   *   A key/value array that defines custom Twig functions. The key denotes the
   *   function name used in the tag, e.g.:
   * @code
   *   {{ testfunc() }}
   * @endcode
   *
   *   The value is a standard PHP callback that defines what the function does.
   */
  public function getFunctions()
  {
    return [
      'leftContent' => new TwigFunction('leftContent', [$this, 'leftContent']),
      'rightContent' => new TwigFunction('rightContent', [$this, 'rightContent']),
      'jsChart' => new TwigFunction('jsChart', [$this, 'jsChart']),
      'jsRightContent' => new TwigFunction('jsRightContent', [$this, 'jsRightContent']),
      'checkbook_mwbe_agency_grading_js' => new TwigFunction('checkbook_mwbe_agency_grading_js', [$this, 'checkbook_mwbe_agency_grading_js'])
    ];
  }

  public function leftContent($left_agencies_data, $data_type)
  {
    $left_data='';
    $id = 1;
    foreach ($left_agencies_data as $row) {
      $agency = $row['agency_name'];
      $chart = "<div id=chart_container_".$id." style=\"width: 300px; height: 50px\"></div>";
      $js = $this->jsChart($row['data_row'], $id);
      $chart .= "<script>".$js."</script>";
      if ($row['spending_amount'] > 0) {
        if ($data_type == 'sub_vendor_data') {
          $link = "/spending_landing/year/" . RequestUtilities::get("year") .
            "/yeartype/" .  RequestUtilities::get("yeartype") . "/agency/" . $row["agency_id"] . "/dashboard/ms/mwbe/" . MappingUtil::$total_mwbe_cats;
        } else {
          $link = "/spending_landing/year/" . RequestUtilities::get("year") .
            "/yeartype/" .  RequestUtilities::get("yeartype") . "/agency/" . $row["agency_id"] . "/dashboard/mp/mwbe/" . MappingUtil::$total_mwbe_cats;
        }

        $left_data .= "<tr>
						          <td><div><a href=" . $link . ">" . $agency . "</a></div></td>
						          <td>" . $chart . "  </td>
						          <td>" . $row['spending_amount'] . "  </td>
						          <td></td>
		                  </tr>";
      }
      $id += 1;
    }
   // var_dump($left_data);
    return $left_data;
  }

  public function rightContent($mwbe_val)
  {
    $mwbe_cats =  _mwbe_agency_grading_current_cats();
    return in_array($mwbe_val, $mwbe_cats) ? 'checked=""' : '';
  }

  public function jsChart($data_row,$id)
  {
    //$val = \Drupal::currentUser()->isAuthenticated() ? 66 : 0;
    $io_mwbe = $data_row['io_mwbe'] > 0 ? $data_row['io_mwbe'] : null;
    $n_mwbe= $data_row['n_mwbe'] > 0 ? $data_row['n_mwbe'] : null;
    $w_mwbe = $data_row['w_mwbe'] > 0 ? $data_row['w_mwbe'] : null;
    $ha_mwbe = $data_row['ha_mwbe'] > 0 ? $data_row['ha_mwbe'] :null;
    $ba_mwbe =  $data_row['ba_mwbe'] > 0 ? $data_row['ba_mwbe'] : null;
    $aa_mwbe = $data_row['aa_mwbe'] > 0 ? $data_row['aa_mwbe'] : null;
    $na_mwbe = $data_row['na_mwbe'] > 0  ? $data_row['na_mwbe']: null;
    $em_mwbe = $data_row['em_mwbe'] > 0 ? $data_row['em_mwbe'] : null;

    $js = "
    var "."\$k"." = jQuery.noConflict();".
    //"\$k"."(document).ready(function () {
    "(function ("."\$k".", Drupal, drupalSettings){
    var chart = new Highcharts.Chart({
      chart: {
         renderTo: 'chart_container_" . $id . "',
         defaultSeriesType: 'bar',
         backgroundColor:'rgba(255, 255, 255, 0.002)',
         height:50    ,
         animation: false
      },
      title: {
         text: null
      },
      exporting: {
         enabled: false
      },
      legend: {
          enabled: false
      },
      credits: {
          enabled: false
      },
      xAxis: {
        categories: ['Ethnicity'],
        lineWidth :0,
        tickWidth: 0,
        labels: {
          enabled: false
      	}
      },
      yAxis: {
         min: 0,
         gridLineWidth :0,
         title: {
            text: null
         },
         labels: {
             enabled: false
       	}

      },
      tooltip: {
         formatter: function() {
            return this.series.name +' - $'+this.y+' ('+ Math.round(this.percentage) +'%)';
         },
         animation:false,
         shadow:false
      },
      plotOptions: {
         series: {
            stacking: 'percent',
            borderWidth: 1,
            minPointLength: 3,
            pointWidth: 18,
            animation: false,
            shadow: false
         }
      },
      series: [
         {name: 'Individuals & Other',
          data: [" . $io_mwbe . "],
          color: '#858f9b'
         },
         {name: 'Non-M/WBE',
          data: [" . $n_mwbe. "],
          color: '#2e5a8b'
         },
         {name: 'Women (Non-Minority)',
          data: [" . $w_mwbe . "],
          color: '#eb8e27'
         },
         {name: 'Hispanic American',
          data: [" . $ha_mwbe."],
          color: '#9ab46a'
         },
         {name: 'Black American',
          data: [" . $ba_mwbe . "],
          color: '#7db7e5'
         },
        {name: 'Asian American',
          data: [" . $aa_mwbe. "],
          color: '#b8d8ef'
         },
        {name: 'Native American',
          data: [" . $na_mwbe. "],
          color: '#F3F386'
        },
        {name: 'Emerging (Non-Minority)',
          data: [" . $em_mwbe. "],
          color: '#D2B8EF'
        }
      ]
 });
})("."\$k".", Drupal, drupalSettings);";
    return $js;
  }

  public function jsRightContent()
  {
   if(RequestUtilities::get('mwbe_agency_grading') == 'sub_vendor_data'){
     $window_location = "/mwbe_agency_grading/sub_vendor_data/year/". RequestUtilities::get('year'). "/yeartype/" . RequestUtilities::get('yeartype') . "/mwbe_filter/";
   }
   else{
     $window_location = "/mwbe_agency_grading/prime_vendor_data/year/". RequestUtilities::get('year'). "/yeartype/" . RequestUtilities::get('yeartype') . "/mwbe_filter/";
   }

    $rjs =  "(function ($, Drupal, drupalSettings) {
	    Drupal.behaviors.agency_grading = {
	            attach:function (context, settings) {
	            	$('.checkbox-grading-legend .legend_entry').click(function () {
	                    var filter = getNamedFilterCriteria('mwbe_right_filter');
                      window.location = \"".$window_location ."\" + filter
                });
              }
	        };
	}(jQuery, Drupal, drupalSettings));";
	return $rjs;
	}

  public function checkbook_mwbe_agency_grading_js() {

    $val = \Drupal::currentUser()->isAuthenticated() ? 66 : 0;
    $jsContent = "function fnCustomInitComplete() {
          var topSpacing = ".$val.";
          var tableOffsetTop = "."\$j"."('#grading_table').offset().top;
          var tableHeight = "."\$j"."('#grading_table').height();
          var docHeight = "."\$j"."(document).height();
          var bottomSpacing = docHeight - (tableOffsetTop + tableHeight) ;
          "."\$j"."('.dataTables_scrollHead').sticky({ getWidthFrom:'#scroll_wrapper_head',topSpacing:topSpacing, bottomSpacing:bottomSpacing});
      }";


    $js = '
          var oTable;
          var $j = jQuery.noConflict();
          $j(document).ready(function() {
            $j(".hidden_body").toggle();

            oTable = $j("#grading_table").dataTable(
              {
                "bFilter": false,
                "bPaginate": true,
                "iDisplayLength":25,
                "sPaginationType":"full_numbers",
                "bLengthChange": false,
                "sDom":"<pr><t><ip>",
                "oLanguage": {
                "sInfo": "Displaying transactions _START_ - _END_ of _TOTAL_",
                  "sProcessing":"<img src=\'/themes/custom/nyccheckbook/images/loading_large.gif\' title=\'Processing...\'/>"
                },
                "bInfo": true,
                "aaSorting":[[2,"desc"]],
                "fnInitComplete":function () { fnCustomInitComplete();},
                "sScrollX": "100%",
                "aoColumnDefs": [
                  {
                    "aTargets": [0],
                    "sClass":"text",
                    "sWidth":"270px"
                  },
                  {
                    "aTargets": [1],
                    "asSorting": [  ],
                    "sClass":"text"
                  },
                  {
                    "aTargets": [2],
                    "sClass":"number",
                    "aExportFn":"function",
                    "mDataProp": function ( source, type, val ) {
                    if (type === "set") {
                      source.total_contracts = val;
                      source.total_contracts_display =  "<div>" + custom_number_format(val) + "</div>";
                      return;
                    }else if (type === "display") {
                      return source.total_contracts_display;
                    }
                    return source.total_contracts;
                  }
                  }
                ]
              }
            );
          });
          ' . $jsContent;
    return $js;
  }

 }
