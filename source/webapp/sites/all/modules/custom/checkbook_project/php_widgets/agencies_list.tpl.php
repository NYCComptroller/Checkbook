<?php
/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (C) 2019 New York City
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

//145 = {array} [3]
// agency_id = {int} 9000
// agency_name = "NEW YORK CITY ECONOMIC DEVELOPMENT CORPORATION"
// is_oge_agency = "Y"

$city_agencies = $node->data[Datasource::CITYWIDE];
$edc_agencies = $node->data[Datasource::OGE];
$nycha_agencies= $node->data[Datasource::NYCHA];

$oge_filter_highlight = (_checkbook_check_isEDCPage() || _checkbook_check_isNYCHAPage()) ? 'agency_filter_highlight' : '';
$city_filter_highlight = (!(_checkbook_check_isEDCPage() || _checkbook_check_isNYCHAPage())) ? 'agency_filter_highlight' : '';

$current_fy_year = (RequestUtilities::get('year')) ? RequestUtilities::get('year') :  _getFiscalYearID() ;
$current_cal_year = (RequestUtilities::get('year'))
  ? min(RequestUtilities::get('year'), CheckbookDateUtil::getCurrentCalendarYearId())
  : CheckbookDateUtil::getCurrentCalendarYearId();

$current_url = explode('/',request_uri());
$url = $current_url[1];
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

foreach($city_agencies as $key => $agency){
    if($agency['id'] == $agency_id_value){
        $selected_text = $agency['title'];
    }
}

$agencies = array_chunk($city_agencies, 10);

$agency_list = "<div id='agency-list' class='agency-nav-dropdowns'>";
$agency_list .= "<div class='agency-list-open'><span id='all-agency-list-open' class='".$city_filter_highlight."'>$selected_text</span></div>";
$agency_list .= "<div class='agency-list-content all-agency-list-content'>";
$agency_list .= "<div class='listContainer1' id='allAgenciesList'>";

foreach($agencies as $key => $agencies_chunck){
    $agency_list .= ((($key+1)%2 == 0)? "" : "<div class='agency-slide'>");
    $agency_list .= "<ul class='listCol".($key+1)."'>";
    foreach($agencies_chunck as $a => $agency){
        $agency_url ="";

        $agency_url = ($current_url[1] == 'payroll')?'payroll/agency_landing/agency/'.$agency['id'].'/yeartype/C/year/'.$current_cal_year
            : $url.'/agency/'.$agency['id'];

        $agency_list .= "<li id=agency-list-id-".$agency['id'].">
                            <a href='/".$agency_url. "'>".$agency['title']."</a>
                        </li>";
    }
    $agency_list .= "</ul>";
    $agency_list .= (($key%2 == 1)? "</div>" : "");
}

$agency_list .= "</div>";
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

//NYCHA Agencies: Set NYCHA default URL to Contracts
$nycha_url = "nycha_spending/year/".$current_cal_year."/datasource/checkbook_nycha";

$agency_list_other = "<div id='agency-list-other' class='agency-nav-dropdowns'>
  <div class='agency-list-open'><span id='other-agency-list-open' class='".$oge_filter_highlight."'>Other Government Entities</span></div>
  <div class='agency-list-content other-agency-list-content'>
    <div class='listContainer1' id='otherAgenciesList'>
        <div class='agency-slide'>
          <ul class='listCol'>";
foreach($edc_agencies as $key => $edc_agency){
    $agency_list_other .= "<li><a href='/". $edc_url .'/yeartype/B/year/'.$current_fy_year."/datasource/checkbook_oge/agency/".$edc_agency['id']. "'>". $edc_agency['title'] ."</a></li>";
}
foreach($nycha_agencies as $key => $nycha_agency){
    $agency_list_other .= "<li><a href='/". $nycha_url .'/agency/'.$nycha_agency['id'] ."'>". $nycha_agency['title'] ."</a></li>";
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



print "<div class='agency-nav-dropdowns-parent'>";
print $agency_list;
print $agency_list_other;
print "</div>";
