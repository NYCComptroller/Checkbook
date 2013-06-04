<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
?>
<?php
$current_fy_year = _getFiscalYearID();
$current_cal_year = _getCalendarYearID();

$current_url = explode('/',$_SERVER['REQUEST_URI']);
$url = $current_url[1];
if($current_url[1] == 'contracts_landing' || $current_url[1] == 'contracts_revenue_landing' || $current_url[1] == 'contracts' ||
   $current_url[1] == 'contracts_pending_exp_landing' || $current_url[1] == 'contracts_pending_rev_landing'){

   $all_agency_url = $url = 'contracts_landing/status/A/yeartype/B/year/'.$current_fy_year;
}else if($current_url[1] == 'payroll'){
    $all_agency_url = $url = 'payroll/yeartype/B/year/'.$current_fy_year;
}else{
    $all_agency_url = $url = 'spending_landing/yeartype/B/year/'.$current_fy_year;
}


$selected_text = 'Citywide (All Agencies)';

foreach($node->data as $key => $value){
	if($value['agency_id'] == $agency_id_value){
		$selected_text = $value['agency_name'];
	}
}

$agencies = array_chunk($node->data, 10);

$agency_list = "<div id='agency-list'>";
$agency_list .= "<div class='agency-list-open'><span>$selected_text</span></div>";
$agency_list .= "<div class='agency-list-content'>";
$agency_list .= "<div class='listContainer1'>";

foreach($agencies as $key => $agencies_chunck){
    $agency_list .= ((($key+1)%2 == 0)? "" : "<div class='agency-slide'>");
    $agency_list .= "<ul class='listCol".($key+1)."'>";
    foreach($agencies_chunck as $a => $agency){
        $agency_url ="";
        $agency_url = ($current_url[1] == 'payroll')?'payroll/agency/'.$agency['agency_id'].'/yeartype/B/year/'.$current_fy_year
                                              : $url.'/agency/'.$agency['agency_id'];
        
        $agency_list .= "<li id=agency-list-id-".$agency['agency_id'].">
                            <a href='/".$agency_url. "'>".$agency['agency_name']."</a>
                        </li>";
    }
    $agency_list .= "</ul>";
    $agency_list .= (($key%2 == 1)? "</div>" : "");
}

$agency_list .= "</div>";
$agency_list .= "</div>";
$agency_list .= "<div class='agency-list-nav'><a href='#' id='prev'>Prev</a><a href='#' id='next'>Next</a>";
$agency_list .= "<a href='/".$all_agency_url."' id='citywide_all_agencies'>CITYWIDE ALL AGENCIES</a></div>";
$agency_list .= "<div class='agency-list-close'><a href='#'>x Close</a></div>";
$agency_list .= "</div></div>";
print $agency_list;