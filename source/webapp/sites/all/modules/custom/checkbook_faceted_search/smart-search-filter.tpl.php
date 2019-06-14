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
 * $facets_render
 */
?>

<div class="narrow-down-filter">
  <div class="narrow-down-title">Narrow Down Your Search:</div>
<?php
foreach ($facets_render??[] as $facet_name => $facet) {

  // skipping children (sub facets)
  if ($facet->child??false){
    continue;
  }

    $span='';
    $display_facet = 'none';

  $just_one_result = (1 === sizeof($facet->results));

    if ($just_one_result||$facet->selected) {
      $span = 'open';
      $display_facet = 'block';
    }

    echo '<div class="filter-content-' . $facet_name . ' filter-content">';
    echo '  <div class="filter-title"><span class="'.$span.'">By ' . htmlentities($facet->title) . '</span></div>';
    echo '    <div class="facet-content" style="display:'.$display_facet.'" ><div class="progress"></div>';

    if ($facet->autocomplete && sizeof($facet->results)>9) {
      $autocomplete_id = "autocomplete_" . $facet_name;
      $disabled = '';

      echo '<div class="autocomplete"><input id="' . $autocomplete_id . '" ' . $disabled . ' type="text" class="solr_autocomplete" facet="'.$facet_name.'" /></div>';
    }

    echo '<div class="options">';
    echo '<div class="rows">';
    $index = 0;

    foreach($facet->results as $facet_value => $count) {

      $facet_result_title = $facet_value;
      if (is_array($count)) {
        list($facet_result_title, $count) = $count;
      }

      $id = 'facet_'.$facet_name.$index;
      $active='';
      echo '<div class="row">';
      echo '<div class="checkbox">';

      $checked = '';
      if ($facet->selected||$just_one_result) {
        $checked = in_array($facet_value, $facet->selected);
        $checked = $checked||$just_one_result ? ' checked="checked" ' : '';
      }
      echo '<input type="checkbox" id="'.$id.'" '.$checked . ' facet="'.$facet_name.'" value="'.
        htmlentities($facet_value).'" onClick="javascript:applySearchFilters();" />';
      echo '<label for="'.$id.'">';
      echo '</label>';
      echo '</div>';

      echo '<div class="name">' . htmlentities($facet_result_title) . '</div>';
      echo '<div class="number"><span' . $active . '>' . number_format($count) . '</span></div>';

      if (($checked||$just_one_result) && ($children = $facet->sub->$facet_value??false)){
        $sub_index=0;
        foreach($children as $child){
          $sub_facet = $facets_render[$child];
          if (!$sub_facet) {
            continue;
          }

          $sub_facet_name = $child;
          echo '<div class="sub-category">';
          echo '<div class="subcat-filter-title">By '.htmlentities($sub_facet->title).'</div>';
          foreach($sub_facet->results as $sub_facet_value => $sub_count){

            $facet_result_title = $sub_facet_value;
            if (is_array($sub_count)) {
              list($facet_result_title, $sub_count) = $sub_count;
            }

            $id = 'facet_'.$sub_facet_name.$sub_index;
            $active='';
            echo '<div class="row">';
            echo '<div class="checkbox">';

            $checked = '';
            if ($sub_facet->selected) {
              $checked = in_array($sub_facet_value, $sub_facet->selected);
              $checked = $checked ? ' checked="checked" ' : '';
            }
            echo '<input type="checkbox" id="'.$id.'" '.$checked . ' facet="'.$sub_facet_name.'" value="'.
              htmlentities($sub_facet_value).'" onClick="javascript:applySearchFilters();" />';
            echo '<label for="'.$id.'"></label>';
            echo '</div>';

            echo '<div class="name">' . htmlentities($facet_result_title) . '</div>';
            echo '<div class="number"><span' . $active . '>' . number_format($sub_count) . '</span></div>';

            echo '</div>';
          }
          $sub_index++;
          echo '</div>';
        }
      }

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
