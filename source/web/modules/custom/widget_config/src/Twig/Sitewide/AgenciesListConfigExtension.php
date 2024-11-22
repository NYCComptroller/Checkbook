<?php
namespace Drupal\widget_config\Twig\Sitewide;

use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_project\CommonUtilities\CheckbookDateUtil;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AgenciesListConfigExtension extends AbstractExtension {
  public function getFunctions() {
    return [
    'generateAgenciesList' => new TwigFunction('generateAgenciesList', [
      $this,
      'generateAgenciesList',
        ])
      ];
  }

  public function generateAgenciesList($node) {
    $city_agencies = array();
    $edc_agencies = array();
    $nycha_agencies= array();

    if (!empty($node->data)) {
      foreach ($node->data as $key => $value) {
        if (isset($value['is_oge_agency']) && $value['is_oge_agency'] == 'Y') {
          $edc_agencies[$key] = $value;
        }
        elseif (isset($value['is_oge_agency']) && $value['is_oge_agency'] == 'N') {
          $city_agencies[$key] = $value;
        }
        else {
          $nycha_agencies[$key] = $value;
        }
      }
    }

    $oge_filter_highlight = (Datasource::isOGE() || Datasource::isNYCHA()) ? 'agency_filter_highlight' : '';
    $city_filter_highlight = (!(Datasource::isOGE() || Datasource::isNYCHA())) ? 'agency_filter_highlight' : '';

    $yearUrlValue = RequestUtilities::get('year');
    $current_fy_year = $yearUrlValue ?? CheckbookDateUtil::getCurrentFiscalYearId(); ;
    $current_cal_year = $yearUrlValue ? min($yearUrlValue, CheckbookDateUtil::getCurrentCalendarYearId()) : CheckbookDateUtil::getCurrentCalendarYearId();

    $current_url = explode('/',request_uri());
    //$url = $current_url[1];
    if($current_url[1] == 'contracts_landing' || $current_url[1] == 'contracts_revenue_landing' || $current_url[1] == 'contracts' ||
      $current_url[1] == 'contracts_pending_exp_landing' || $current_url[1] == 'contracts_pending_rev_landing'){

      $all_agency_url = $url = 'contracts_landing/status/A/yeartype/B/year/'.$current_fy_year;
    }else if($current_url[1] == 'payroll'){
      $all_agency_url = $url = 'payroll/yeartype/B/year/'.$current_fy_year;
    }else if($current_url[1] == 'budget'){
      $all_agency_url = $url = 'budget/yeartype/B/year/'.$current_fy_year;
    }else if($current_url[1] == 'revenue'){
      $all_agency_url = $url = 'revenue/yeartype/B/year/'.$current_fy_year;
    }else{
      $all_agency_url = $url = 'spending_landing/yeartype/B/year/'.$current_fy_year;
    }


    $selected_text = 'Citywide Agencies';

    /*foreach($city_agencies as $key => $value){
      if($value['agency_id'] == $agency_id_value){
        $selected_text = $value['agency_name'];
      }
    }*/

    $agencies = array_chunk($city_agencies, 10);

    $agency_list = "<div id='agency-list' class='agency-nav-dropdowns'>";
    $agency_list .= "<div class='agency-list-open'><span id='all-agency-list-open' class='".$city_filter_highlight."'>$selected_text</span></div>";
    $agency_list .= "<div class='agency-list-content all-agency-list-content'>";
    $agency_list .= "<div class='listContainer1' id='allAgenciesList'>";

    if ($agencies) {
      foreach($agencies as $key => $agencies_chunck){
        $agency_list .= ((($key+1)%2 == 0)? "" : "<div class='agency-slide'>");
        $agency_list .= "<ul class='listCol".($key+1)."'>";
        foreach($agencies_chunck as $a => $agency){
          $agency_url ="";

          $agency_url = ($current_url[1] == 'payroll')?'payroll/agency_landing/agency/'.$agency['agency_id'].'/yeartype/C/year/'.$current_cal_year
            : $url.'/agency/'.$agency['agency_id'];

          $agency_list .= "<li id=agency-list-id-".$agency['agency_id'].">
                            <a href='/".$agency_url. "'>".$agency['agency_name']."</a>
                        </li>";
        }
        $agency_list .= "</ul>";
        $agency_list .= (($key%2 == 1)? "</div>" : "");
      }
      $agency_list .= "</div>";
    }

    $agency_list .= "</div>";
    $agency_list .= "<div class='agency-list-nav'><a id='prev1'>Prev</a><a  id='next1'>Next</a>";
    $agency_list .= "<a href='/".$all_agency_url."' id='citywide_all_agencies'>CITYWIDE ALL AGENCIES</a></div>";
    $agency_list .= "<div class='agency-list-close'><a>x Close</a></div>";
    $agency_list .= "</div></div>";

//$edc_agencies
    if($current_url[1] == 'contracts_landing')
      $edc_url = "contracts_landing/status/A";
    else
      $edc_url = "spending_landing";

//NYCHA Agencies: Set NYCHA default URL to Spending
    $nychaCurrentFY = CheckbookDateUtil::getCurrentFiscalYearId(Datasource::NYCHA);
    $nychaFY = (isset($yearUrlValue) && $yearUrlValue <= $nychaCurrentFY) ? $yearUrlValue : $nychaCurrentFY;
    $nychaUrl = "nycha_spending/year/". $nychaFY ."/datasource/checkbook_nycha";

    $agency_list_other = "<div id='agency-list-other' class='agency-nav-dropdowns'>
  <div class='agency-list-open'><span id='other-agency-list-open' class='".$oge_filter_highlight."'>Other Government Entities</span></div>
  <div class='agency-list-content other-agency-list-content'>
    <div class='listContainer1' id='otherAgenciesList'>
        <div class='agency-slide'>
          <ul class='listCol'>";
    foreach($edc_agencies as $key => $edc_agency){
      $agency_list_other .= "<li><a href='/". $edc_url .'/yeartype/B/year/'.$current_fy_year."/datasource/checkbook_oge/agency/".$edc_agency['agency_id']. "'>". $edc_agency['agency_name'] ."</a></li>";
    }
    foreach($nycha_agencies as $key => $nycha_agency){
      $agency_list_other .= "<li><a href='/". $nychaUrl .'/agency/'.$nycha_agency['agency_id'] ."'>". $nycha_agency['agency_name'] ."</a></li>";
    }
    $agency_list_other .= "</ul>
        </div>
    </div>
        <div class='agency-list-nav'><a id='prev2'>Prev</a><a  id='next2'>Next</a>
        <a href='/spending_landing" . '/yeartype/B/year/'.$current_fy_year ."/datasource/checkbook_oge/agency/9000"."' id='citywide_all_agencies'>OTHER GOVERNMENT ENTITIES</a>
        </div>
    <div class='agency-list-close'><a>x Close</a></div>
  </div>
</div>";

    return "<div class='agency-nav-dropdowns-parent'>" . $agency_list . $agency_list_other . "</div>";
  }
}
