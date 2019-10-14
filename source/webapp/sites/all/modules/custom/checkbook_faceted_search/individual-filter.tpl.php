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
/**
 * Created by JetBrains PhpStorm.
 * User: jrobertson
 * Date: 2/27/13
 * Time: 4:00 PM
 * To change this template use File | Settings | File Templates.
 * $node
 * $name
 */

//Facets that have Url parameters that match the current Url will be disabled from de-selecting by default.
//To enable the user to de-select the default criteria (for advanced search), set "allowFacetDeselect":true in the config
$disableFacet = !(isset($node->widgetConfig->allowFacetDeselect) ? $node->widgetConfig->allowFacetDeselect : false);
$urlParameter = $node->widgetConfig->urlParameterName;
if($disableFacet) { //only URL parameters count and can be disabled
    $query_string = $_GET['q'];
    $is_new_window = preg_match('/newwindow/i',$query_string);
    $currentPath = current_path();
    //To disable the user to de-select the default criteria (for advanced search)-NYCHA Contracts
    if (preg_match('/nycha_contracts\/all\/transactions/', $currentPath) ||
        preg_match('/nycha_contracts\/search\/transactions/', $currentPath)) {
        $url_ref = $_GET['q'];
        $disableFacet = preg_match('"/' . $node->widgetConfig->urlParameterName . '/"', $url_ref);
    }
    else {
        $url_ref = $is_new_window ? $_GET['q'] : $_SERVER['HTTP_REFERER'];
        $disableFacet = preg_match('"/' . $urlParameter . '/"', $url_ref);
    }
}
if(isset($node->widgetConfig->maxSelect) && !$disableFacet){
  $tooltip = 'title="Select upto ' . $node->widgetConfig->maxSelect . '"';
}
else{
$tooltip = "";
}

//Amount Filter
if($node->widgetConfig->filterName == 'Amount') {
    $showAllRecords = isset($node->widgetConfig->showAllRecords) ? $node->widgetConfig->showAllRecords : false;
    if(!$showAllRecords) {
        $params = explode('~', RequestUtilities::get($urlParameter));
        if($params[0]) {
            $unchecked = null;
        }
    }
}

//Payroll Range Filter
$is_payroll_range_filter =
    ($node->widgetConfig->filterName == 'Gross Pay YTD') ||
    ($node->widgetConfig->filterName == 'Annual Salary') ||
    ($node->widgetConfig->filterName == 'Overtime Payment');
if($is_payroll_range_filter) {
    $showAllRecords = isset($node->widgetConfig->showAllRecords) ? $node->widgetConfig->showAllRecords : false;
    if(!$showAllRecords) {
        $params = explode('~', RequestUtilities::get($urlParameter));
        if($params[0]) {
            $unchecked = null;
        }
    }
}
//donot show annual in ratetype facet
if($node->widgetConfig->filterName == 'Rate Type'){
    if ($unchecked && $unchecked)
        foreach($unchecked as $key => $value) {
            if($value[1] == 'ANNUAL') {
                $unchecked[$key] = 0;
            }
        }
}
//Contract Includes Sub Vendors Facet
//For N/A value, some values are null, this needs to be handled
if($node->widgetConfig->filterName == 'Contract Includes Sub Vendors') {
    if (isset($unchecked) && $unchecked)
    foreach($unchecked as $key => $value) {
        if($value[1] == null) {
            $unchecked[$key][0] = 5;
            $unchecked[$key][1] = "N/A";
        }
    }
    if (isset($checked) && $checked)
    foreach($checked as $key => $value) {
        if($value[1] == null) {
            $checked[$key][0] = 5;
            $checked[$key][1] = "N/A";
        }
    }
}

//Sub Vendor Status in PIP
//For N/A value, some values are null, this needs to be handled
if($node->widgetConfig->filterName == 'Sub Vendor Status in PIP') {
    if ($unchecked && $unchecked)
    foreach($unchecked as $key => $value) {
        if($value[1] == null) {
            $unchecked[$key][0] = 0;
            $unchecked[$key][1] = "N/A";
        }
    }
    if (isset($checked) && $checked)
    foreach($checked as $key => $value) {
        if($value[1] == null) {
            $checked[$key][0] = 0;
            $checked[$key][1] = "N/A";
        }
    }
}

$checkedCount = (isset($checked) && $checked) ? sizeof($checked) : 0;
$uncheckedCount = (isset($unchecked) && $unchecked) ? sizeof($unchecked) : 0;

//Payroll Type Filter
$count = 0;
if($node->widgetConfig->filterName == 'Payroll Type') {

    switch($node->nid) {
        case 898:
        case 899:
        //Advanced Search Payroll Type Facets
//        if ($checked && is_array($checked))
        if (isset($checked) && $checked)
        foreach($checked as $key => $value) {
            if($value[0] == 2 || $value[0] == 3) {
                $count = $count + $value[2];
                $id = "2~3";
                unset($checked[$key]);
            }
            else {
                array_push($checked,array($value[0],PayrollType::$SALARIED,$value[2]));
                unset($checked[$key]);
            }
        }
        if($count > 0) {
            array_push($checked,array($id,PayrollType::$NON_SALARIED,$count));
        }
        break;
    }
}

//Modified Expense Budget Filter
if($node->widgetConfig->filterName == 'Modified Expense Budget') {
    $showAllRecords = isset($node->widgetConfig->showAllRecords) ? $node->widgetConfig->showAllRecords : false;
    if(!$showAllRecords) {
        $params = explode('~', RequestUtilities::get($urlParameter));
        if($params[0]) {
            $unchecked = null;
        }
    }
}

//Revenue Recognized Filter
if($node->widgetConfig->filterName == 'Revenue Recognized') {
    $showAllRecords = isset($node->widgetConfig->showAllRecords) ? $node->widgetConfig->showAllRecords : false;
    if(!$showAllRecords) {
        $params = explode('~', RequestUtilities::get($urlParameter));
        if($params[0]) {
            $unchecked = null;
        }
    }
}

$logicalOrFacet = $node->widgetConfig->logicalOrFacet;
if(isset($logicalOrFacet) && $logicalOrFacet) {
    foreach($unchecked as $key => $value){
        //Remove N/A from facet
        if($value[1] == null) {
            unset($unchecked[$key]);
        }
    }
    foreach($checked as $key=>$value){
        //Remove N/A from facet
        if($value[1] == null) {
            unset($checked[$key]);
        }
    }
}

//Remove N/A from Prime/Sub Industry facets
if($node->widgetConfig->filterName == 'Prime Industry' || $node->widgetConfig->filterName == 'Sub Industry'){
    foreach($unchecked as $key => $value){
        if($value[1] == null) {
            unset($unchecked[$key]);
        }
    }
    foreach($checked as $key=>$value){
        if($value[1] == null) {
            unset($checked[$key]);
        }
    }
}

//Checking 'Asian-American' filter in Prime/Sub MWBE Category Facet
$is_prime_filter = $node->widgetConfig->filterName == 'Prime M/WBE Category';
$is_sub_filter = $node->widgetConfig->filterName == 'Sub M/WBE Category';
$is_prime_sub_filter = $node->widgetConfig->filterName == 'M/WBE Category';
if($is_prime_filter || $is_sub_filter || ($is_prime_sub_filter && $node->widgetConfig->parentNid == 939)){

    $asian_american_count = 0;
    $show_only_prime_certified = $is_prime_filter && ContractUtil::showPrimeMwbeData();
    $show_only_sub_certified = $is_sub_filter && ContractUtil::showSubMwbeData();

    foreach($unchecked as $key => $value){
        $id = $value[0];
        $name = $value[1];
        $count = $value[2];
        if($id == 4 || $id == 5){
            $asian_american_count = $asian_american_count + $count;
            unset($unchecked[$key]);
        }
        else if($id == 7 || $id == 11){
            if($show_only_prime_certified || $show_only_sub_certified) {
                unset($unchecked[$key]);
            }
        }
        else if(!isset($name)) {
            unset($unchecked[$key]);
        }
    }

    if($asian_american_count > 0) {
        array_push($unchecked,array("4~5","Asian American",$asian_american_count));
        usort($unchecked,
            function($a, $b)
            {
                if ($a[2] == $b[2]) {
                    return 0;
                }
                return ($a[2] > $b[2]) ? -1 : 1;
            }
        );
    }
    $asian_american_count = 0;


//    if (isset($checked) && is_array($checked))
    if (isset($checked) && $checked)
    foreach($checked as $key => $value){
        $id = $value[0];
        $name = $value[1];
        $count = $value[2];
        if($id == 4 || $id == 5){
            $asian_american_count = $asian_american_count + $count;
            unset($checked[$key]);
        }
        else if($id == 7 || $id == 11){
            if($show_only_prime_certified || $show_only_sub_certified) {
                unset($checked[$key]);
            }
        }
        else if(!isset($name)) {
            unset($checked[$key]);
        }
    }

    if($asian_american_count > 0) {
        array_push($checked,array("4~5","Asian American",$asian_american_count));
        usort($checked,
            function($a, $b)
            {
                if ($a[2] == $b[2]) {
                    return 0;
                }
                return ($a[2] > $b[2]) ? -1 : 1;
            }
        );
    }
}

//Checking 'Asian-American' filter in MWBE Category Facet
$count =0;
if($node->widgetConfig->filterName == 'M/WBE Category' && $node->widgetConfig->parentNid != 939){
    $dashboard = RequestUtilities::get('dashboard');
    foreach($unchecked as $key => $value){
        if(isset($dashboard) && $dashboard != 'ss'){
            if($value[0] == 7 || $value[0] == 11){
                unset($unchecked[$key]);
            }
        }
        //Remove N/A from facet
        if($value[1] == null) {
            unset($unchecked[$key]);
        }
    }
    if(isset($checked) && $checked) {
        foreach ($checked as $key => $value) {
            if ($value[0] == 4 || $value[0] == 5) {
                $count = $count + $value[2];
                $id = "4~5";
                unset($checked[$key]);
            } else {
                array_push($checked, array($value[0], MappingUtil::getMinorityCategoryById($value[0]), $value[2]));
                unset($checked[$key]);
            }
            //Remove N/A from facet
            if ($value[1] == null) {
                unset($checked[$key]);
            }
        }
    }
    if($count > 0 )array_push($checked,array($id,'Asian American',$count));
}

//Data alteration for Vendor Type Facet
//Vendor Type facet for parentNid == 932/939 is a different implementation and should be ignored
if($node->widgetConfig->filterName == 'Vendor Type'){
    if($node->widgetConfig->parentNid == 932 || $node->widgetConfig->parentNid == 939) {
        $vendor_counts = array();
        // To fix the issue with PM counts are getting added twice to PM~SM
//      if (is_array($checked)) {
      if (isset($checked) && $checked) {
        foreach($checked as $row){
          $checked_vendor_types[$row[0]] = $row[2];
        }
      }
//        if (is_array($checked_vendor_types)) {
        if (isset($checked_vendor_types) && $checked_vendor_types) {
          foreach($checked_vendor_types as $key=>$value){
            if(in_array($key,array('P'))){
              $vendor_counts['P~PM'] = $vendor_counts['P~PM']+ $value;
            }
            if(in_array($key,array('S'))){
              $vendor_counts['S~SM'] = $vendor_counts['S~SM']+ $value;
            }
            if(in_array($key,array('PM'))){
              $vendor_counts['PM~SM'] = $vendor_counts['PM~SM']+ $value;
            }
            if(in_array($key,array('SM'))){
              $vendor_counts['PM~SM'] = $vendor_counts['PM~SM']+ $value;
            }
          }
        }
//        if (is_array($unchecked)) {
        if (isset($unchecked) && $unchecked) {
          foreach($unchecked as $row){
            if(in_array($row[0],array('P'))){
              $vendor_counts['P~PM'] = $vendor_counts['P~PM']+ $row[2];
            }
            if(in_array($row[0],array('S'))){
              $vendor_counts['S~SM'] = $vendor_counts['S~SM']+ $row[2];
            }
            if(in_array($row[0],array('PM'))){
              $vendor_counts['PM~SM'] = $vendor_counts['PM~SM']+ $row[2];
            }
            if(in_array($row[0],array('SM'))){
              $vendor_counts['PM~SM'] = $vendor_counts['PM~SM']+ $row[2];
            }
          }
        }
        $checked = $unchecked = array();
        $selected_vendor_types =  RequestUtilities::get('vendortype');
//        if (is_array($vendor_counts)) {
        if (isset($vendor_counts) && $vendor_counts) {
          foreach($vendor_counts as $key=>$value){
            if (strpos($selected_vendor_types, $key) !== false) {
              array_push($checked, array($key, MappingUtil::getMixedVendorTypeNames($key),$value));
            }
            else {
              array_push($unchecked, array($key, MappingUtil::getMixedVendorTypeNames($key),$value));
            }
          }
        }
    }
    else {
        $vendor_types = RequestUtilities::get('vendortype');
        $vendor_type_data = MappingUtil::getVendorTypes($checked, $vendor_types);
        $vendor_type_data = MappingUtil::getVendorTypes($unchecked, $vendor_types);
        $checked = $vendor_type_data['checked'];
        $unchecked = $vendor_type_data['unchecked'];
    }
}

if(!$checked){
    $display_facet ="none";
    $span = "";
}else{
    $display_facet ="block";
    $span = "open";
}

if(strtolower($filter_name) == 'agency' || strtolower($filter_name) == 'citywide agency'){
    if(_checkbook_check_isEDCPage() || _checkbook_check_isNYCHAPage()){
        $filter_name = 'Other Government Entity';
    }else{
        $filter_name = 'Citywide Agency';
    }
}
if(strtolower($filter_name) == 'vendor'){
    if(_checkbook_check_isEDCPage()){
        $filter_name = 'Prime Vendor';
    }
}
$id_filter_name = str_replace(" ", "_", strtolower($filter_name));
?>
<div name="<?php print $urlParameter; ?>" id="<?php print $autocomplete_id; ?>" class="filter-content <?php if( $hide_filter != "") print "disabled"; ?>">
    <div <?php print $hide_filter; ?>>
  <div class="filter-title" <?php print $tooltip ?>><span class="<?php print $span;?>">By <?php print $filter_name;?></span></div>
  <div class="facet-content" style="display:<?php echo $display_facet; ?>">
  <div class="progress"></div>
  <?php
  $node->widgetConfig->limit = $node->widgetConfig->limit ?: 50;
    $pages = ceil($node->totalDataCount/$node->widgetConfig->limit);
    if((isset($checked) && $node->widgetConfig->maxSelect == $checkedCount) || $checkedCount + $uncheckedCount == 0 || $disableFacet){
      $disabled = " DISABLED='true' " ;
    }
    else{
      $disabled = "" ;
    }
    if( !isset($node->widgetConfig->autocomplete) || $node->widgetConfig->autocomplete == true  ){ ?>
  <div class="autocomplete"><input class="autocomplete" <?php print $disabled; ?> pages="<?php print $pages ?>" type="text" name="<?php print $autocomplete_field_name ?>"
            autocomplete_param_name="<?php print $autocomplete_param_name ?>" nodeid="<?php print $node->nid ;?>" id="<?php print $autocomplete_id ?>"></div>
        <?php } ?>
  <div class="checked-items">
    <?php
    $query_string = $_GET['q'];


    if((isset($checked) && $node->widgetConfig->maxSelect == $checkedCount) || $checkedCount + $uncheckedCount == 0 ){
        $disabled = " DISABLED='true' " ;
    }else{
        $disabled = "" ;
    }
    // Check if links are from ytd(nycha spending) or inv (nycha contracts) and disable facet selection
    if(preg_match('/ytd_contract/',$query_string) || preg_match('/inv_contract/',$query_string)){
      $disableFacet = " DISABLED='true' ";
      $disabled =  " DISABLED='true' ";
      $unchecked = null;
    }
    else{$disableFacet = $disableFacet ? " DISABLED='true' " : "";}
    //$disableFacet = $disableFacet ? " DISABLED='true' " : "";
    $ct = 0;

//    if ($checked && is_array($checked)) {
    if (isset($checked) && $checked) {
      foreach ($checked as $row) {
        if($row[2] > 0) {
            $row[0] = str_replace('__','/', $row[0]);
            $row[1] = str_replace('__','/', $row[1]);
            $id = $id_filter_name."_checked_".$ct;
            echo <<<EOL

            <div class="row">
              <label for="{$id}">
                <div class="checkbox">
                            
EOL;
            echo "  <input class='styled' id='" . $id . "' name= '" . $autocomplete_id . "' type='checkbox' " . $disableFacet . " checked='checked' value='" . urlencode(html_entity_decode($row[0], ENT_QUOTES)) . "' onClick=\"return applyTableListFilters();\" />" .
              "<label for=\"{$id}\" />";
            echo "</div>";
            if($node->widgetConfig->filterName == 'Contract ID') {
              echo '<div class="name"><label for="'.$id.'">' . $row[1] . '</label></div>';
            }
            else {
              echo '<div class="name"><label for="'.$id.'">' . _break_text_custom2($row[1],15) . '</label></div>';
            }
            echo '    <div class="number"><span class="active">' . number_format($row[2]) . '</span></div>';
            echo '  </label>';
            echo '</div>';
            $ct++;
        }
      }
    }

    ?>
  </div>
  <div class="options">
    <div class="rows">
    <?php
    $ct = 0;
//    if (isset($unchecked) && is_array($unchecked))
    if (isset($unchecked) && $unchecked)
    foreach ($unchecked as $row) {
        if($row[2] > 0) {
            $row[0] = str_replace('__', '/', $row[0]);
            $row[1] = str_replace('__', '/', $row[1]);
            $id = $id_filter_name . "_unchecked_" . $ct;
            echo <<<EOL
  
              <div class="row">
                <label for="{$id}">
                  <div class="checkbox">
                              
EOL;
            echo "<input class='styled' id='" . $id . "' name= '" . $autocomplete_id . "' type='checkbox' " . $disabled . "value='" . urlencode(html_entity_decode($row[0], ENT_QUOTES)) . "' onClick=\"return applyTableListFilters();\">" .
              " <label for='" . $id . "' />" .
              "</div>";
            if ($node->widgetConfig->filterName == 'Contract ID') {
                echo '<div class="name">' . $row[1] . '</div>';
            } else {
                echo '<div class="name">' . _break_text_custom2($row[1], 15) . '</div>';
            }
            echo '    <div class="number"><span>' . number_format($row[2]) . '</span></div>';
            echo '  </label>';
            echo '</div>';
            $ct++;
        }
    }
    ?>
    </div>
  </div>
  </div>
  </div>
</div>
<?php
  if($node->widgetConfig->facetPager == true){
    $scroll_facet = "var page" . $node->nid ." = 0;
    $(this).next().find('.options').mCustomScrollbar('destroy');
    $(this).next().find('.options').mCustomScrollbar({
        horizontalScroll:false,
        scrollButtons:{
            enable:false
        },
        callbacks:{
            // this function causing disappearing checkbox issue
            onTotalScroll: function (){
				var pages = $(this).next().find('input.autocomplete').attr('pages');
				if(pages == 1) return false;
				if(page" . $node->nid ."  >= pages ) {
					return false;
				}
				page" . $node->nid ."++;

            },
            onTotalScrollBack: function(){
                var pages = $(this).next().find('input.autocomplete').attr('pages');
                if(pages == 1) return false;
                if (page" . $node->nid ." > 0){
                    page" . $node->nid ."--;
                    paginateScroll(" . $node->nid .", page" . $node->nid .");
                }

            }
        },
        theme:'dark'
    });";

  }elseif($node->widgetConfig->facetNoPager == true){
    $scroll_facet = "
        $(this).next().find('.options').mCustomScrollbar('destroy');
        $(this).next().find('.options').mCustomScrollbar({
            horizontalScroll:false,
            scrollButtons:{
                enable:false
            },
            theme:'dark'
        });
      ";
   }
    $js .= "(function($){
          $('.filter-title').each(function(){
            if($(this).children('span').hasClass('open')){";
    $js .= $scroll_facet . "}});

          $('.filter-title').unbind('click');
          $('.filter-title').click(function(){
                if($(this).next().css('display') == 'block'){
                    $(this).next().css('display','none');
                    $(this).children('span').removeClass('open');
                    $(this).next().find('.options').mCustomScrollbar('destroy');
                } else {
                    $(this).next().css('display','block');
                    $(this).children('span').addClass('open');
                    $(this).next().find('.options').mCustomScrollbar('destroy');
                    $(this).next().find('.options').mCustomScrollbar({
                        horizontalScroll:false,
                        scrollButtons:{
                            enable:false
                        },
                        theme:'dark'
                    });
                }
            });

            })(jQuery);";
  if(isset($js)){
    echo '<script type="text/javascript">' . $js . '</script>';
  }
  if ($node->widgetConfig->addJS1) {
    echo '<script type="text/javascript">' . $node->widgetConfig->addJS . '</script>';
  }
