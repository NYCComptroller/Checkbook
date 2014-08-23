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
 * Created by JetBrains PhpStorm.
 * User: jrobertson
 * Date: 2/27/13
 * Time: 4:00 PM
 * To change this template use File | Settings | File Templates.
 * $node
 * $name
 */
if(isset($node->widgetConfig->maxSelect)){
  $tooltip = 'title="Select upto ' . $node->widgetConfig->maxSelect . '"';
}
else{
$tooltip = "";
}
//Checking 'Asian-American' filter in MWBE Category Facet
$count =0;
if($node->widgetConfig->filterName == 'M/WBE Category'){
    foreach($checked as $key=>$value){
        if($value[0] == 4 || $value[0] == 5){
            $count = $count + $value[2];
            $id = "4~5";
            unset($checked[$key]);
        }else{
            array_push($checked,array($value[0],MappingUtil::getMinorityCategoryById($value[0]),$value[2]));
            unset($checked[$key]);
        }
    }
    if($count > 0 )array_push($checked,array($id,'Asian American',$count));
}

if(count($checked) == 0){
    $display_facet ="none";
    $span = "";
}else{
    $display_facet ="block";
    $span = "open";
}

if(strtolower($filter_name) == 'agency'){
    if(_checkbook_check_isEDCPage()){
        $filter_name = 'Other Government Entity';
    }else{
        $filter_name = 'Citywide Agency';
    }
}
if(strtolower($filter_name) == 'vendor'){
    $filter_name = "Prime Vendor";
}
?>
<div class="filter-content <?php if( $hide_filter != "") print "disabled"; ?>"><div <?php print $hide_filter; ?>>
  <div class="filter-title" <?php print $tooltip ?>><span class="<?php print $span;?>">By <?php print $filter_name;?></span></div>
  <div class="facet-content" style="display:<?php echo $display_facet; ?>">
  <div class="progress"></div>
  <?php  
    $pages = ceil($node->totalDataCount/$node->widgetConfig->limit);   
    if((isset($checked) && $node->widgetConfig->maxSelect == count($checked)) || count($checked) + count($unchecked) == 0 ){
      $disabled = " DISABLED='true' " ;
    }else{
      $disabled = "" ;
    }   
    if( !isset($node->widgetConfig->autocomplete) || $node->widgetConfig->autocomplete == true  ){ ?>
  <div class="autocomplete"><input class="autocomplete" <?php print $disabled; ?> pages="<?php print $pages ?>" type="text" name="<?php print $autocomplete_field_name ?>" 
            autocomplete_param_name="<?php print $autocomplete_param_name ?>" nodeid="<?php print $node->nid ;?>" id="<?php print $autocomplete_id ?>"></div>
        <?php } ?>
  <div class="checked-items">
    <?php
    foreach ($checked as $row) {
      $row[0] = str_replace('__','/', $row[0]);      
      $row[1] = str_replace('__','/', $row[1]);      
      echo '<div class="row">';
      echo '<div class="checkbox"><input class="styled" name="' . $autocomplete_id . '" type="checkbox" checked="checked" value="' . urlencode(html_entity_decode($row[0],ENT_QUOTES)) . '" onClick="return applyTableListFilters();"></div>';
      echo '<div class="name">' . _break_text_custom2($row[1],15) . '</div>';
      echo '<div class="number"><span class="active">' . number_format($row[2]) . '</span></div>';
      echo '</div>';
    }
    ?>
  </div>
  <div class="options">
    <div class="rows">
    <?php
    foreach ($unchecked as $row) {
      $row[0] = str_replace('__','/', $row[0]);      
      $row[1] = str_replace('__','/', $row[1]);      
      echo '<div class="row">';
      echo '<div class="checkbox"><input class="styled" name="' . $autocomplete_id . '" type="checkbox" '  .  $disabled .  'value="' . urlencode($row[0]) . '" onClick="return applyTableListFilters();"></div>';
      echo '<div class="name">' . _break_text_custom2($row[1],15) . '</div>';
      echo '<div class="number"><span>' . number_format($row[2]) . '</span></div>';
      echo '</div>';
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
            onTotalScroll: function (){   
				var pages = $(this).next().find('input.autocomplete').attr('pages');
				if(pages == 1) return false;
				if(page" . $node->nid ."  >= pages ) {
					return false;
				}
				page" . $node->nid ."++;                      
                paginateScroll(" . $node->nid .", page" . $node->nid .")
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
?>