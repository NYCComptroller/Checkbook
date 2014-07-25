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
?>
<?php
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

foreach ($render_array as $title => $value) {
    if ($title == 'Type of Data' || $title == 'Spending Category' || $title == 'Category' || $title == 'Status'){
        $count =0;
        foreach ($value as $v) {
            if(in_array('checked', $v))
                $count++;
        }
        $display_facet = ($count == 0) ? "display:none" :"display:block";
    }else{
        $display_facet = (count($value['checked']) > 0) ? "display:block" :"display:none";
    }
  echo '<div class="filter-content-' . $value['name'] . ' filter-content">';
  echo '<div class="filter-title">By ' . $title . '</div>';
  echo '<div class="facet-content" style="'.$display_facet.'" ><div class="progress"></div>';
  if ($title == 'Type of Data' || $title == 'Spending Category' || $title == 'Category' || $title == 'Status') {
    echo '<div class="options">';
    echo '<div class="rows">';
    foreach ($value as $v) {
      $name = $value['name'];
      if (is_array($v)) {
        $checked = (in_array('checked', $v)) ? ' checked="checked" ' : '';
        $active = ($checked) ? ' class="active"' : '';
        echo '<div class="row">';
        echo '<div class="checkbox">';
        if ($v[0]) {
          echo '<input name="' . $name . '" type="checkbox"' . $checked . 'value="' . $v[0] . '" onClick="javascript:applySearchFilters();">';
        }
        echo '</div>';
        echo '<div class="name">' . htmlentities($v[1]) . '</div>';
        echo '<div class="number"><span' . $active . '>' . number_format($v[2]) . '</span></div>';
        if (count($sub_cat_array[$v[1]]) > 0) {
          foreach ($sub_cat_array[$v[1]] as $a => $b) {
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
                echo '<div class="row">';
                echo '<div class="checkbox">';
                if ($sub_cat[0]) {
                  echo '<input name="' . $name . '" type="checkbox"' . $checked . 'value="' . $sub_cat[0] . '" onClick="javascript:applySearchFilters();">';
                }
                echo '</div>';
                echo '<div class="name">' . htmlentities($sub_cat[1]) . '</div>';
                echo '<div class="number"><span' . $active . '>' . number_format($sub_cat[2]) . '</span></div>';
                echo '</div>';
              }
            }
            echo '</div></div></div>';
          }
        }
        echo '</div>';
      }
    }
    echo '</div></div>';
  }
  else {
    $autocomplete_id = "autocomplete_" . $value['name'];
    $disabled = ($value['checked'] && count($value['checked']) >= 5) ? "disabled" : '';
    echo '<div class="autocomplete"><input id="' . $autocomplete_id . '" ' . $disabled . ' type="text"></div>';
    echo '<div class="checked-items">';
    if ($value['checked']) {
      foreach ($value['checked'] as $row) {
        echo '<div class="row">';
        echo '<div class="checkbox"><input type="checkbox" value="' . $row[0] . '" name="' . $value['name'] . '" checked="checked" onClick="javascript:applySearchFilters();"></div>';
        echo '<div class="name">' . htmlentities($row[1]) . '</div>';
        echo '<div class="number"><span class="active">' . number_format($row[2]) . '</span></div>';
        echo '</div>';
      }
    }
    echo '</div>';
    echo '<div class="options">';
    echo '<div class="rows">';
    if ($value['unchecked']) {
      foreach ($value['unchecked'] as $row) {
        echo '<div class="row">';
        echo '<div class="checkbox"><input type="checkbox" value="' . $row[0] . '" ' . $disabled . ' name="' . $value['name'] . '" onClick="javascript:applySearchFilters();"></div>';
        echo '<div class="name">' . htmlentities($row[1]) . '</div>';
        echo '<div class="number"><span>' . htmlentities(number_format($row[2])) . '</span></div>';
        echo '</div>';
      }
    }
    echo '</div>';
    echo '</div>';
  }
  echo '</div></div>';
}
  ?>
</div>
<script type="text/javascript">
  var opts = {
    horizontalScroll:false,
    scrollButtons:{
      enable:false
    },
    theme:'dark'
  };
  jQuery('div.filter-content-fagencyName .options').mCustomScrollbar(opts);
  jQuery('div.filter-content-fyear .options').mCustomScrollbar(opts);
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
</script>