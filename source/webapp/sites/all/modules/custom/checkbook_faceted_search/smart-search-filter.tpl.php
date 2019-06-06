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
 * @file
 * Template file to output the "Narrow Down Your Search" sidebar for Smart Search
 *
 * Available variables:
 * $facets
 * $active_contracts
 * $theme_hook_suggestions
 * $zebra
 * $id
 * $directory
 * $classes_array
 * $attributes_array
 * $title_attributes_array
 * $content_attributes_array
 * $title_prefix
 * $title_suffix
 * $user
 * $db_is_active
 * $is_admin
 * $logged_in
 * $is_front
 * $render_array
 */
?>

<div class="narrow-down-filter">
  <div class="narrow-down-title">Narrow Down Your Search:</div>
<?php
foreach ($facets_render as $facet_name => $facet) {

  //    $id_title = str_replace(" ", "_", $title);
//    if ($title == 'Type of Data' || $title == 'Spending Category' || $title == 'Category' || $title == 'Status'){
//        $count =0;
//        foreach ($value as $v) {
//            if(is_array($v) && in_array('checked', $v)) {
//              $count++;
//            }
//        }
//
//        if($count == 0){
//            $display_facet = "none";
//            $span = "";
//        }else{
//            $display_facet = "block";
//            $span = "open";
//        }
//    }else{
//        if(!empty($value['checked'])){
//            $display_facet = "block";
//            $span = "open";
//        }else{
//            $display_facet = "none";
//            $span = "";
//        }
//    }

    $span='';
    $display_facet = 'none';

    if ($facet->selected) {
      $span = 'open';
      $display_facet = 'block';
    }

    echo '<div class="filter-content-' . $facet_name . ' filter-content">';
    echo '  <div class="filter-title"><span class="'.$span.'">By ' . htmlentities($facet->title) . '</span></div>';
  echo '    <div class="facet-content" style="display:'.$display_facet.'" ><div class="progress"></div>';

    if ($facet->autocomplete) {
      $autocomplete_id = "autocomplete_" . $facet->input_name;
  //      $disabled = ($value['checked'] && count($value['checked']) >= 5) ? "disabled" : '';
      /**
       * @TODO
       */
      $disabled = '';

      echo '<div class="autocomplete"><input id="' . $autocomplete_id . '" ' . $disabled . ' type="text" /></div>';
    }

    /*
    if ($title == 'Type of Data' || $title == 'Spending Category' || $title == 'Category' || $title == 'Status') {
        foreach ($value as $v) {
            $name = $value['name'];
            $id = $id_title.$index;
            if (is_array($v)) {
                $checked = (in_array('checked', $v)) ? ' checked="checked" ' : '';
                $active = ($checked) ? ' class="active"' : '';
                $id .= (in_array('checked', $v)) ? '_checked' : '_unchecked';
                echo '<div class="row">';
                echo '<div class="checkbox">';
                if ($v[0]) {
                    echo '<input id="'.$id.'" name="' . $name . '" type="checkbox"' . $checked . 'value="' . $v[0] . '" onClick="javascript:applySearchFilters();">';
                }
                echo '<label for="'.$id.'"></label></div>';
                echo '<div class="name">' . htmlentities($v[1]) . '</div>';
                echo '<div class="number"><span' . $active . '>' . number_format($v[2]) . '</span></div>';
                if ($sub_cat_array && count($sub_cat_array[$v[1]]) > 0) {
                    $sub_index = 0;
                    foreach ($sub_cat_array[$v[1]] as $a => $b) {
                        $sub_id_title = str_replace(" ", "_", $a);
                        $id = $id_title.$sub_id_title.$index.$sub_index;
                        echo '<div class="sub-category">';
                        echo '<div class="subcat-filter-title">By ' . $a . '</div>';
                        echo '<div class="progress"></div>';
                        echo '<div class="options">';
                        echo '<div class="rows">';
                        foreach ($b as $sub_cat) {
                            $name = $b['name'];
                            if (is_array($sub_cat)) {
                                $checked = (in_array('checked', $sub_cat)) ? ' checked="checked" ' : '';
                                $active = ($checked) ? ' class="active"' : '';
                                $id .= (in_array('checked', $v)) ? '_checked' : '_unchecked';
                                echo '<div class="row">';
                                echo '<div class="checkbox">';
                                if ($sub_cat[0]) {
                                    if($a == 'Status'){
                                        echo '<input id="'.$id.'" name="' . $name . '" type="radio"' . $checked . 'value="' . $sub_cat[0] . '" onClick="javascript:applySearchFilters();" class="radio-blue">';
                                    }
                                    else{
                                        echo '<input id="'.$id.'" name="' . $name . '" type="checkbox"' . $checked . 'value="' . $sub_cat[0] . '" onClick="javascript:applySearchFilters();">';
                                    }
                                }
                                echo '<label for="'.$id.'"></label></div>';
                                echo '<div class="name"><label for="'.$id.'">' . htmlentities($sub_cat[1]) . '</label></div>';
                                echo '<div class="number"><span' . $active . '><label for="'.$id.'">' . number_format($sub_cat[2]) . '</label></span></div>';
                                echo '</div>';
                            }
                        }
                        $sub_index++;
                        echo '</div></div></div>';
                    }
                }
                echo '</div>';
            }
            $index++;
        }
//        echo '</div></div>';
    }
    else {
        if($title == 'Vendor Type'){
            $disabled = ($value['checked'] && count($value['checked']) >= 5) ? "disabled" : '';
            echo '<div class="checked-items">';
            if ($value['checked']) {
                $prime_vendor = array();
                $sub_vendor = array();
                $mwbe_vendor = array();
                $prime_count = 0;
                $sub_count = 0;
                $mwbe_count = 0;
                $prime_flag = false;
                $sub_flag = false;
                $mwbe_flag = false;
                $url = request_uri();
                $type = explode('*|*', $url);
                for($i=0; $i<sizeof($type); $i++){
                    if(preg_match('/^vendor_type/', $type[$i])){
                        $val = explode('=', $type[$i]);
                    }
                }
                $filter = explode('~', $val[1]);
                foreach ($value['checked'] as $row) {
                    foreach($filter as $vtype){
                        if($vtype == 'pv'){
                          if($row[1] == 'p' || $row[1] == 'pm'){
                            $prime_flag = true;
                            $prime_count = $prime_count + $row[2];
                            $prime_vendor = array(
                                'name' => 'Prime Vendor',
                                'value' => 'pv',
                                'count' => $prime_count
                            );
                          }
                        }
                        if($vtype == 'sv'){
                          if($row[1] == 's' || $row[1] == 'sm'){
                            $sub_flag = true;
                            $sub_count = $sub_count + $row[2];
                            $sub_vendor = array(
                                'name' => 'Sub Vendor',
                                'value' => 'sv',
                                'count' => $sub_count
                            );
                          }
                        }

                        if($vtype == 'mv'){
                          if($row[1] == 'sm' || $row[1] == 'pm'){
                            $mwbe_flag = true;
                            $mwbe_count = $mwbe_count + $row[2];
                            $mwbe_vendor = array(
                                'name' => 'M/WBE Vendor',
                                'value' => 'mv',
                                'count' => $mwbe_count
                            );
                          }
                        }
                    }
                }
                if($prime_flag){
                    $id = "vendor_type_pv_checked";
                    echo '<div class="row">';
                    echo '<div class="checkbox"><input type="checkbox" id="'.$id.'" value="' . $prime_vendor['value'] . '"  name="' . $value['name'] . '" checked="checked" onClick="javascript:applySearchFilters();"><label for="'.$id.'"></label></div>';
                    echo '<div class="name"><label for="'.$id.'">' . htmlentities($prime_vendor['name']) . '</label></div>';
                    echo '<div class="number"><span><label for="'.$id.'">' . htmlentities(number_format($prime_vendor['count'])) . '</label></span></div>';
                    echo '</div>';
                }
                if($sub_flag){
                    $id = "vendor_type_sv_checked";
                    echo '<div class="row">';
                    echo '<div class="checkbox"><input type="checkbox" id="'.$id.'" value="' . $sub_vendor['value'] . '" name="' . $value['name'] . '" checked="checked" onClick="javascript:applySearchFilters();"><label for="'.$id.'"></label></div>';
                    echo '<div class="name"><label for="'.$id.'">' . htmlentities($sub_vendor['name']) . '</label></div>';
                    echo '<div class="number"><span><label for="'.$id.'">' . htmlentities(number_format($sub_vendor['count'])) . '</label></span></div>';
                    echo '</div>';
                }
                if($mwbe_flag){
                    $id = "vendor_type_mv_checked";
                    echo '<div class="row">';
                    echo '<div class="checkbox"><input type="checkbox" id="'.$id.'" value="' . $mwbe_vendor['value'] . '"  name="' . $value['name'] . '" checked="checked" onClick="javascript:applySearchFilters();"><label for="'.$id.'"></label></div>';
                    echo '<div class="name"><label for="'.$id.'">' . htmlentities($mwbe_vendor['name']) . '</label></div>';
                    echo '<div class="number"><span><label for="'.$id.'">' . htmlentities(number_format($mwbe_vendor['count'])) . '</label></span></div>';
                    echo '</div>';
                }
            }
            echo '</div>';
            echo '<div class="options">';
            echo '<div class="rows">';
            if ($value['unchecked']) {
                $prime_vendor = array();
                $sub_vendor = array();
                $mwbe_vendor = array();
                $prime_count = 0;
                $sub_count = 0;
                $mwbe_count = 0;
                foreach ($value['unchecked'] as $row) {
                    if($row[1] == 'p' || $row[1] == 'pm'){
                        $prime_count = $prime_count + $row[2];
                        $prime_vendor = array(
                            'name' => 'Prime Vendor',
                            'value' => 'pv',
                            'count' => $prime_count
                        );
                    }
                    if($row[1] == 's' || $row[1] == 'sm'){
                        $sub_count = $sub_count + $row[2];
                        $sub_vendor = array(
                            'name' => 'Sub Vendor',
                            'value' => 'sv',
                            'count' => $sub_count
                        );
                    }
                    if($row[1] == 'pm' || $row[1] == 'sm'){
                        $mwbe_count = $mwbe_count + $row[2];
                        $mwbe_vendor = array(
                            'name' => 'M/WBE Vendor',
                            'value' => 'mv',
                            'count' => $mwbe_count
                        );
                    }
                }
                if($prime_count){
                    $id = "vendor_type_pv_unchecked";
                    echo '<div class="row">';
                    echo '<div class="checkbox"><input type="checkbox" id="'.$id.'" value="' . $prime_vendor['value'] . '" ' . $disabled . ' name="' . $value['name'] . '" onClick="javascript:applySearchFilters();"><label for="'.$id.'"></label></div>';
                    echo '<div class="name"><label for="'.$id.'">' . htmlentities($prime_vendor['name']) . '</label></div>';
                    echo '<div class="number"><span><label for="'.$id.'">' . htmlentities(number_format($prime_vendor['count'])) . '</label></span></div>';
                    echo '</div>';
                }
                if($sub_count){
                    $id = "vendor_type_sv_unchecked";
                    echo '<div class="row">';
                    echo '<div class="checkbox"><input type="checkbox" id="'.$id.'" value="' . $sub_vendor['value'] . '" ' . $disabled . ' name="' . $value['name'] . '" onClick="javascript:applySearchFilters();"><label for="'.$id.'"></label></div>';
                    echo '<div class="name"><label for="'.$id.'">' . htmlentities($sub_vendor['name']) . '</label></div>';
                    echo '<div class="number"><span><label for="'.$id.'">' . htmlentities(number_format($sub_vendor['count'])) . '</label></span></div>';
                    echo '</div>';
                }
                if($mwbe_count){
                    $id = "vendor_type_mvv_unchecked";
                    echo '<div class="row">';
                    echo '<div class="checkbox"><input type="checkbox" id="'.$id.'" value="' . $mwbe_vendor['value'] . '" ' . $disabled . ' name="' . $value['name'] . '" onClick="javascript:applySearchFilters();"><label for="'.$id.'"></label></div>';
                    echo '<div class="name"><label for="'.$id.'">' . htmlentities($mwbe_vendor['name']) . '</label></div>';
                    echo '<div class="number"><span><label for="'.$id.'">' . htmlentities(number_format($mwbe_vendor['count'])) . '</label></span></div>';
                    echo '</div>';
                }
            }
            echo '</div>';
            echo '</div>';
        }
        else{
            $autocomplete_id = "autocomplete_" . $value['name'];
            $disabled = ($value['checked'] && count($value['checked']) >= 5) ? "disabled" : '';
            if($title != 'M/WBE Category' && $title != 'Vendor Type' && $title != 'Payroll Type'){
                if(empty($value['checked']) && empty($value['unchecked'])){
                    echo '<div class="disable_autocomplete"><input type="text" disabled="disabled"></div>';
                }else{
                    echo '<div class="autocomplete"><input id="' . $autocomplete_id . '" ' . $disabled . ' type="text"></div>';
                }
            }
            echo '<div class="checked-items">';
            if ($value['checked']) {
                $checked_index = 0;
                foreach ($value['checked'] as $row) {
                    $id = $id_title.$checked_index.'_checked';
                    $yearID = _getYearIDFromValue($row[0]);
                    if($title == 'Fiscal Year'){
                        if($yearID <= _getFiscalYearID()){
                            echo '<div class="row">';
                            echo '<div class="checkbox"><input type="checkbox" id="'.$id.'" value="' . $row[0] . '" name="' . $value['name'] . '" checked="checked" onClick="javascript:applySearchFilters();"><label for="'.$id.'"></label></div>';
                            echo '<div class="name"><label for="'.$id.'">' . htmlentities($row[1]) . '</label></div>';
                            echo '<div class="number"><span class="active"><label for="'.$id.'">' . number_format($row[2]) . '</label></span></div>';
                            echo '</div>';
                        }
                    }else{
                        echo '<div class="row">';
                        echo '<div class="checkbox"><input type="checkbox" id="'.$id.'" value="' . $row[0] . '" name="' . $value['name'] . '" checked="checked" onClick="javascript:applySearchFilters();"><label for="'.$id.'"></label></div>';
                        echo '<div class="name"><label for="'.$id.'">' . htmlentities($row[1]) . '</label></div>';
                        echo '<div class="number"><span class="active"><label for="'.$id.'">' . number_format($row[2]) . '</label></span></div>';
                        echo '</div>';
                    }
                    $checked_index++;
                }
            }
            echo '</div>';
            echo '<div class="options">';
            echo '<div class="rows">';
            if ($value['unchecked']) {
                $unchecked_index = 0;
                foreach ($value['unchecked'] as $row) {
                    $id = $id_title.$unchecked_index.'_unchecked';
                    $yearID = _getYearIDFromValue($row[0]);
                    if($title == 'Fiscal Year'){
                        if($yearID <= _getFiscalYearID()){
                            echo '<div class="row">';
                            echo '<div class="checkbox"><input type="checkbox" id="'.$id.'" value="' . $row[0] . '" ' . $disabled . ' name="' . $value['name'] . '" onClick="javascript:applySearchFilters();"><label for="'.$id.'"></label></div>';
                            echo '<div class="name"><label for="'.$id.'">' . htmlentities($row[1]) . '</label></div>';
                            echo '<div class="number"><span><label for="'.$id.'">' . htmlentities(number_format($row[2])) . '</label></span></div>';
                            echo '</div>';
                        }
                    }else{
                        echo '<div class="row">';
                        echo '<div class="checkbox"><input type="checkbox" id="'.$id.'" value="' . $row[0] . '" ' . $disabled . ' name="' . $value['name'] . '" onClick="javascript:applySearchFilters();"><label for="'.$id.'"></label></div>';
                        echo '<div class="name"><label for="'.$id.'">' . htmlentities($row[1]) . '</label></div>';
                        echo '<div class="number"><span><label for="'.$id.'">' . htmlentities(number_format($row[2])) . '</label></span></div>';
                        echo '</div>';
                    }
                    $unchecked_index++;
                }
            }
            echo '</div>';
            echo '</div>';
        }
    }
    /* */

    echo '<div class="options">';
    echo '<div class="rows">';
    $index = 0;

    foreach($facet->results as $facet_value => $count) {

      $facet_result_title = $facet_value;
      if (is_array($count)) {
        // vendor_type prepare_results()
        list($facet_result_title, $count) = $count;
      }

      $id = 'facet_'.$facet_name.$index;
      $active='';
      echo '<div class="row">';
      echo '<div class="checkbox">';

      $checked = '';
      if ($facet->selected) {
        $checked = in_array($facet_value, $facet->selected);
        $checked = $checked ? ' checked="checked" ' : '';
      }

      echo '<input type="checkbox" id="'.$id.'" '.$checked . ' name="'.$facet->input_name.'" value="'.
        htmlentities($facet_value).'" onClick="javascript:applySearchFilters();" />';
      echo '<label for="'.$id.'">';
      echo '</label>';
      echo '</div>';

      echo '<div class="name">' . htmlentities($facet_result_title) . '</div>';
      echo '<div class="number"><span' . $active . '>' . number_format($count) . '</span></div>';
      echo '</div>';
      $index++;
    }

    echo '</div></div>';

    echo '</div></div>';
}
?>
</div>
<script type="text/javascript">
    jQuery('.filter-title > .open').each(function(){
        jQuery('div.filter-content-fagencyName .options').mCustomScrollbar("destroy");
        jQuery('div.filter-content-fyear .options').mCustomScrollbar("destroy");
        jQuery('div.filter-content-regfyear .options').mCustomScrollbar("destroy");
        jQuery('div.filter-content-fvendorName .options').mCustomScrollbar("destroy");
        jQuery('div.filter-content-fexpenseCategoryName .options').mCustomScrollbar("destroy");
        jQuery('div.filter-content-fmwbeCategory .options').mCustomScrollbar("destroy");
        jQuery('div.filter-content-fpayrollTypeName .options').mCustomScrollbar("destroy");
        jQuery('div.filter-content-findustryTypeName .options').mCustomScrollbar("destroy");
        scroll_facet();
    });

    jQuery('.filter-title').unbind('click');
    jQuery('.filter-title').click(function(){
        if(jQuery(this).next().css('display') == 'block'){
            jQuery(this).next().css('display','none');
            jQuery(this).children('span').removeClass('open');

        } else {
            jQuery(this).next().css('display','block');
            jQuery(this).children('span').addClass('open');

            jQuery('div.filter-content-fagencyName .options').mCustomScrollbar("destroy");
            jQuery('div.filter-content-fyear .options').mCustomScrollbar("destroy");
            jQuery('div.filter-content-regfyear .options').mCustomScrollbar("destroy");
            jQuery('div.filter-content-fvendorName .options').mCustomScrollbar("destroy");
            jQuery('div.filter-content-fexpenseCategoryName .options').mCustomScrollbar("destroy");
            jQuery('div.filter-content-fmwbeCategory .options').mCustomScrollbar("destroy");
            jQuery('div.filter-content-fpayrollTypeName .options').mCustomScrollbar("destroy");
            jQuery('div.filter-content-findustryTypeName .options').mCustomScrollbar("destroy");
            scroll_facet();
        }
    });
    function scroll_facet(){
        var opts = {
            horizontalScroll:false,
            scrollButtons:{
                enable:false
            },
            theme:'dark'
        };
        jQuery('div.filter-content-fagencyName .options').mCustomScrollbar(opts);
        jQuery('div.filter-content-fmwbeCategory .options').mCustomScrollbar(opts);
        jQuery('div.filter-content-fpayrollTypeName .options').mCustomScrollbar(opts);
        jQuery('div.filter-content-fyear .options').mCustomScrollbar(opts);
        jQuery('div.filter-content-regfyear .options').mCustomScrollbar(opts);
        jQuery('div.filter-content-findustryTypeName .options').mCustomScrollbar(opts);

        var vendorpage = 0;
        var vpagelimit = Drupal.settings.checkbook_smart_search.vendor_pages;
        jQuery('div.filter-content-fvendorName .options').mCustomScrollbar({
            horizontalScroll:false,
            scrollButtons:{
                enable:false
            },
            theme:'dark',
            callbacks:{
                onTotalScroll:function () {
                    if (vendorpage < vpagelimit) {
                        vendorpage++;
                        smartSearchPaginateVendor(vendorpage);
                    }
                },
                onTotalScrollBack:function () {
                    if (vendorpage > 0) {
                        vendorpage--;
                        smartSearchPaginateVendor(vendorpage);
                    }
                }
            }
        });
        var expcatpg = 0;
        var ecpagelimit = Drupal.settings.checkbook_smart_search.expense_category_pages;
        jQuery('div.filter-content-fexpenseCategoryName .options').mCustomScrollbar({
            horizontalScroll:false,
            scrollButtons:{
                enable:false
            },
            theme:'dark',
            callbacks:{
                onTotalScroll:function () {
                    if (expcatpg < ecpagelimit) {
                        expcatpg++;
                        smartSearchPaginateExpcat(expcatpg);
                    }
                },
                onTotalScrollBack:function () {
                    if (expcatpg > 0) {
                        expcatpg--;
                        smartSearchPaginateExpcat(expcatpg);
                    }
                }
            }
        });
    }
</script>
