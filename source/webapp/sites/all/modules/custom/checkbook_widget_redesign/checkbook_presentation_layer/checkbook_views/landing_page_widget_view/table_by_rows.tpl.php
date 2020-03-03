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

require_once(realpath(drupal_get_path('module', 'data_controller')) . '/common/object/converter/handler/PHP2Json.php');?>

<div about="/node/<?php echo widget_unique_identifier($node) ?>" typeof="sioc:Item foaf:Document" class="node node-widget node-teaser node-published node-not-promoted node-not-sticky self-posted author-admin odd clearfix" id="node-widget-<?php echo widget_unique_identifier($node) ?>">
<div class="content clearfix">


<?php 
/* Evaluating Widget titles and sub titles */


    if (isset($node->widgetConfig->table_title)) {
        $widget_title = $node->widgetConfig->table_title;
        if(isset($node->widgetConfig->headerSubTitle)){
            load_widget_controller_data_count($node);
            $headerSubTitle = ' Number of '.$node->widgetConfig->headerSubTitle.':  '.number_format($node->headerCount);
        } 
    }else if(isset($node->widgetConfig->headerTitle)){
        load_widget_controller_data_count($node);
        $headerSubTitle = isset($node->widgetConfig->headerSubTitle) ? $node->widgetConfig->headerSubTitle : $node->widgetConfig->headerTitle;
        $count = $node->headerCount > 4 ? '<span class="hideOnExpand">5 </span>' : '';
        if(Dashboard::isNycha() && isset($node->widgetConfig->nychaTitle)){
            $widget_title = $node->widgetConfig->nychaTitle;
        }
        else{
            $widget_title = 'Top '.$count.' '.$node->widgetConfig->headerTitle;
        }
        $headerSubTitle = ' Number of '.$headerSubTitle.':  '.number_format($node->headerCount);
    }
    
    if(isset($widget_title)){
        print '<div class="tableHeader"><h2>' . $widget_title .(isset($headerSubTitle)?('<span class="contentCount">'.$headerSubTitle.'</span>'):''). '</h2></div>';
    }
?>
    
<table id="table_<?php echo widget_unique_identifier($node) ?>" class="<?php echo $node->widgetConfig->html_class ?>">
  <?php
  if (isset($node->widgetConfig->caption_column)) {
    echo '<caption>' . $node->data[0][$node->widgetConfig->caption_column] . '</caption>';
  }
  else {
    if (isset($node->widgetConfig->caption)) {
      echo '<caption>' . $node->widgetConfig->caption . '</caption>';
    }
  }
  ?>
  <thead>
  <?php
  echo "<tr>";
  foreach ($node->widgetConfig->table_columns as $row) {
	if(check_node_flag_visibilty($row->visibility_flag, $node)){
	    $label = (isset($row->labelAlias))? (WidgetUtil::generateLabelMapping($row->labelAlias)) : $row->label;
	
	    $fn = $row->adjustLabelFunction;
	    if(isset($fn) && function_exists($fn)) {
	      $label = $fn($label);
	    }
	    $headerClass = ($row->headerClass) ? ' class="' . $row->headerClass . '"' : '';
	    echo "<th$headerClass>" . $label . "</th>";
    }
  }
  echo "</tr>\n";
  ?>
  </thead>

  <tbody>

  <?php
  if (isset($node->data) and is_array($node->data)) {
    foreach ($node->data as $datarow) {
      echo "<tr>";
      foreach ($node->widgetConfig->table_columns as $row) {
        echo '<td class="' . $datarow[$row->classColumn] . '">' . $datarow[$row->column] . '</td>';
      }
      echo "</tr>";
    }

  }
  ?>
  </tbody>
</table>

<?php if (!$node->widgetConfig->disableViewAll) { ?>
<a class="view-all popup"
   href="/checkbook/view_all_popup/node/<?= $node->nid ?>?refURL=<?= drupal_get_path_alias($_GET['q']) ?>">View All</a>
<?php
}
if ($node->widgetConfig->deferredRendering == TRUE) {
  widget_data_tables_add_js_setting($node);
}
else {
  widget_data_tables_add_js($node);
}
if (isset($node->widgetConfig->table_footnote_column)) {
  echo $node->data[0][$node->widgetConfig->table_footnote_column];
}
else {
  if (isset($node->widgetConfig->table_footnote)) {
    echo $node->widgetConfig->table_footnote;
  }
}
?>
<div class="tableFooter">
  <?php
    if ($node->widgetConfig->enableExpand == TRUE) {
        if($node->totalDataCount > 5){
            $simultExpandCollapseNodeIds = array();
//            $simultExpandCollapseNodeIds = array('spending_by_agencies_view', 'spending_by_expense_categories_view',
//                                                 'spending_by_departments_view','oge_spending_by_expense_categories_view',
//                                                 'oge_spending_by_departments_view', 'mwbe_spending_by_agencies_view',
//                                                 'mwe_spending_expense_categories_view', 'mwbe_spending_by_departments_view');
            if(in_array($node->nid, $simultExpandCollapseNodeIds)){
                echo '<a  class="simultExpandCollapseWidget"><img src="/' . drupal_get_path('theme',$GLOBALS['theme']) . '/images/open.png"></a>';
            }else{
                echo '<a  class="expandCollapseWidget"><img src="/' . drupal_get_path('theme',$GLOBALS['theme']) . '/images/open.png"></a>';
            }
        }
    }

  if(isset($node->widgetConfig->footerUrl)) {
      if($node->widgetConfig->footerUrl != "") {
          $footerUrl = $node->widgetConfig->footerUrl;
          $node->widgetConfig->footerUrl = eval("return $footerUrl;");
      }
      else $node->widgetConfig->footerUrl = null;
  }
  else {
      $footerUrl = _widget_controller_footer_url($node);
      if(isset($footerUrl)){
          $node->widgetConfig->footerUrl = $footerUrl;
      }else{
          $node->widgetConfig->footerUrl = null;
      }
  }

    if (isset($node->widgetConfig->footerUrl)) {
        if($node->totalDataCount > 5){
            echo '<span class="plus-or">or</span>';
        }
        $url = $node->widgetConfig->footerUrl;
        if($node->totalDataCount > 0) {
            echo '<a class="show-details bottomContainerReload" href="'.$url.'">Details >></a>';
        }
        else {
            echo '<a class="show-details bottomContainerReload" href="'.$url.'" style="display:none;">Details >></a>';
        }
    }
  ?>
</div>

</div>
    <div class="clearfix">
        <div class="links node-links clearfix"></div>
    </div>
</div>




