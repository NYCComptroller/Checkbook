<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
?>
<?php
echo eval($node->widgetConfig->header);  
$table_rows = array();

$url_path = drupal_get_path_alias($_GET['q']);
$path_params = explode('/', $url_path);
$yr_index = array_search("year",$path_params);
$req_year_id = $path_params[$yr_index+1];

$years = array();
foreach( $node->data as $row){
    $table_rows[$row['funding_funding']]['id'] = $row['funding_funding'];
    $table_rows[$row['funding_funding']]['name'] = $row['funding_funding_funding_class_name'];
    $table_rows[$row['funding_funding']]['adopted_budget'] = $row['adopted_budget'];
    $table_rows[$row['funding_funding']]['current_modified_budget'] = $row['current_modified_budget'];

    $table_rows[$row['funding_funding']]['revenue_collected'][$row['year_year_year_value']] = $row['revenue_amount_sum'];
    $years[$row['year_year_year_value']] = 	$row['year_year_year_value'];
}
asort($years);
?>
<h3><?php echo $node->widgetConfig->table_title; ?></h3>

<table id="table_<?php echo widget_unique_identifier($node) ?>" class="<?php echo $node->widgetConfig->html_class ?>">
    <?php
    if (isset($node->widgetConfig->caption_column)) {
        echo '<caption>' . $node->data[0][$node->widgetConfig->caption_column] . '</caption>';
    }
    else if (isset($node->widgetConfig->caption)) {
        echo '<caption>' . $node->widgetConfig->caption . '</caption>';
    }
    ?>
    <thead>
    <tr>
    <th>Name</th>
    <th>Adopted Budget</th>
    <th>Current Modified Budget</th>
    <?php
    foreach ($years as $year){
        echo "<th>Revenue Collected <div align='center'> FY " . $year . "</div></th>";
    }
    ?>
    <th>Total Revenue Collected <div align='center'> To Date</div></th>
    </tr>
    </thead>

    <tbody>

    <?php
        $i = 0;
        foreach($table_rows as $row){
            echo "<tr>
                    <td class='" . $cat_class . "' ><a href='/revenue/year/". $req_year_id ."/fndsrc/". $row['id'] ."'>" . $row['name'] . "</a></td>
                    <td class='" . $cat_class . "' >" . custom_number_formatter_format($row['adopted_budget'],2,'$') . "</td>
                    <td class='" . $cat_class . "' >" . custom_number_formatter_format($row['current_modified_budget'],2,'$') . "</td>";
            foreach ($years as $year){
                $amount_link = "<a href='/revenue/transactions/fundsrc/" .$row['id']. "/year/" . _getYearIDFromValue ($year). "'>"
                               .custom_number_formatter_format($row['revenue_collected'][$year],2,'$')."</a>";
                echo "<td class='" . $amount_class . "' >" . $amount_link . "</td>";
            }
            //echo "<td class='" . $amount_class . "' >" . custom_number_formatter_format(array_sum($row['revenue_collected']),2,'$') ."</td>";

            $widgetNode = node_load(285);
            widget_set_uid($widgetNode,$i);
            $additionalParams = array();
            $additionalParams["funding.funding"] = $row['id'];
            widget_add_additional_parameters($widgetNode,$additionalParams);
            $widgetChart = node_build_content($widgetNode);
            $widgetChart = drupal_render($widgetNode->content);

            echo "<td class='" . $amount_class . "' ><table><tr><td><a href='/revenue/transactions/fundsrc/". $row['id'] ."'>"
                  .custom_number_formatter_format(array_sum($row['revenue_collected']),2,'$').
                 "</a></td><td>". $widgetChart ."</td></tr></table></td>";

            echo "</tr>";
            $i++;
        }
    ?>
    </tbody>
</table>
    
<?php  widget_data_tables_add_js($node); ?>