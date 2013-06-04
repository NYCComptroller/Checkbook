<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
?>
<?php
    drupal_add_js(drupal_get_path('module', 'checkbook_project') .'/js/transactions.js');
	$output = NULL;
    $agencies = $revenueCategories = $revenueSources = $checkAmounts = array();

    //request criteria
    $reqAgencyId = $node->widgetConfig->requestParams['agency_id'];
    $reqRevenueCatergoryId = $node->widgetConfig->requestParams['revenue_category_id'];
    $reqRevenueSourceId = $node->widgetConfig->requestParams['revenue_source_id'];

    if(count($node->data) > 0){
        foreach($node->data as $data){
            if(array_key_exists('g100m_count',$data)){
                $checkAmounts = $data;
            }else if (array_key_exists('agency_agency_agency_name',$data)){
                $agencies[] = $data;
            }else if (array_key_exists('category_category_revenue_category_name',$data)){
                $revenueCategories[] = $data;
            }else if (array_key_exists('revsource_revsource_revenue_source_name',$data)){
                $revenueSources[] = $data;
            }
        }
    }

    $output .= "<div class='title'>Narrow down your search:</div><div class='content clearfix'>";

     if(count($agencies) > 0){
       $output .= "<table id='agencyfilter'>";
       $output .= "<th colspan='3'>By Agency</th>";
       $i=0;
       $displayRows = NULL;
       $hiddenRows = NULL;
       foreach($agencies as $key=>$value){
               $checked = ($reqAgencyId == $value['agency_agency']) ? 'checked' : '';
               $agencyName = _get_shortened_text($value['agency_agency_agency_name']);
           if($i<5){
               $displayRows .= "<tr><td><input class='styled' type='checkbox' name='fagencyId' {$checked} value='{$value['agency_agency']}' onClick='javascript:applyTableListFilters();'/></td>  <td>{$agencyName}</td> <td>{$value['txcount']}</td> </tr>";
           }else{
               $hiddenRows .= "<tr><td><input class='styled' type='checkbox' name='fagencyId' {$checked} value='{$value['agency_agency']}' onClick='javascript:applyTableListFilters();'/></td>  <td>{$agencyName}</td> <td>{$value['txcount']}</td> </tr>";
           }
           $i++;
       }

       if($displayRows){
           $displayRows = "<tbody>" .$displayRows."</tbody>" ;
       }

       if($hiddenRows){
           $hiddenRows = "<tbody id='hiddenagencies' style='display: none;'>" .$hiddenRows."</tbody>";
       }

       $output .= $displayRows.$hiddenRows."</table>";

       if(count($agencies) > 5){
           $output .= "<div class='nd-more-link'><a href=\"javascript:toggleDisplay('hiddenagencies');changeLinkText('showmorelinkAg','Agencies')\"><div id='showmorelinkAg'>Show more Agencies &#187;</div></a></div>";
       }
   }

   if(count($revenueCategories) > 0){
       $output .= "<table id='revenueCategoryfilter'>";
       $output .= "<th colspan='3'>By Revenue Category</th>";
       $i=0;
       $displayRows = NULL;
       $hiddenRows = NULL;
       foreach($revenueCategories as $key=>$value){
           $checked = ($reqRevenueCatergoryId == $value['category_category']) ? 'checked' : '';
           $revenueCategoryName = _get_shortened_text($value['category_category_revenue_category_name']);
           if($i<5){
               $displayRows .= "<tr><td><input type='checkbox' name='frevenueCategoryId' {$checked}  value='{$value['category_category']}' onClick='javascript:applyTableListFilters();'/></td>  <td>{$revenueCategoryName}</td> <td>{$value['txcount']}</td> </tr>";
           }else{
               $hiddenRows .= "<tr><td><input type='checkbox' name='frevenueCategoryId' {$checked}  value='{$value['category_category']}' onClick='javascript:applyTableListFilters();'/></td>  <td>{$revenueCategoryName}</td> <td>{$value['txcount']}</td> </tr>";
           }
           $i++;
       }

       if($displayRows){
           $displayRows = "<tbody>" .$displayRows."</tbody>" ;
       }

       if($hiddenRows){
           $hiddenRows = "<tbody id='hiddencat' style='display: none;'>" .$hiddenRows."</tbody>" ;
       }

       $output .= $displayRows.$hiddenRows."</table>";

       if(count($revenueCategories) > 5){
           $output .= "<div class='nd-more-link'><a href=\"javascript:toggleDisplay('hiddencat');changeLinkText('showmorelinkcat','Revenue Categories')\"><div id='showmorelinkcat'>Show more Revenue Categories &#187;</div></a></div>";
       }
   }

    if(count($revenueSources) > 0){
        $output .= "<table id='revenueSourcefilter'>";
        $output .= "<th colspan='3'>By Revenue Source</th>";
        $i=0;
        $displayRows = NULL;
        $hiddenRows = NULL;
        foreach($revenueSources as $key=>$value){
            $checked = ($reqRevenueSourceId == $value['revsource_revsource']) ? 'checked' : '';
            $revenueSourceName = _get_shortened_text($value['revsource_revsource_revenue_source_name']);
            if($i<5){
                $displayRows .= "<tr><td><input type='checkbox' name='frevenueSourceId' {$checked}  value='{$value['revsource_revsource']}' onClick='javascript:applyTableListFilters();'/></td>  <td>{$revenueSourceName}</td> <td>{$value['txcount']}</td> </tr>";
            }else{
                $hiddenRows .= "<tr><td><input type='checkbox' name='frevenueSourceId' {$checked}  value='{$value['revsource_revsource']}' onClick='javascript:applyTableListFilters();'/></td>  <td>{$revenueSourceName}</td> <td>{$value['txcount']}</td> </tr>";
            }
            $i++;
        }

        if($displayRows){
            $displayRows = "<tbody>" .$displayRows."</tbody>" ;
        }

        if($hiddenRows){
            $hiddenRows = "<tbody id='hiddensource' style='display: none;'>" .$hiddenRows."</tbody>" ;
        }

        $output .= $displayRows.$hiddenRows."</table>";

        if(count($revenueSources) > 5){
            $output .= "<div class='nd-more-link'><a href=\"javascript:toggleDisplay('hiddensource');changeLinkText('showmorelinksrc','Revenue Sources')\"><div id='showmorelinksrc'>Show more Revenue Sources &#187;</div></a></div>";
        }
    }

   if(count($checkAmounts) > 0){
       $output .= "<table id='revAmtfilter'>";
       $output .= "<th colspan='3'>By Amount</th>";

       $displayRows = NULL;

       $displayRows .= "<tr><td><input type='checkbox' name='frevenueAmount' value='6' onClick='javascript:applyTableListFilters();'/></td>  <td>Greater than $100M<td>{$checkAmounts['g100m_count']}</td> </tr>";
       $displayRows .= "<tr><td><input type='checkbox' name='frevenueAmount' value='5' onClick='javascript:applyTableListFilters();'/></td>  <td>$51M - $100M</td> <td>{$checkAmounts['g50m_le100m_count']}</td> </tr>";
       $displayRows .= "<tr><td><input type='checkbox' name='frevenueAmount' value='4' onClick='javascript:applyTableListFilters();'/></td>  <td>$26M - $50M</td> <td>{$checkAmounts['g25m_le50m_count']}</td> </tr>";
       $displayRows .= "<tr><td><input type='checkbox' name='frevenueAmount' value='3' onClick='javascript:applyTableListFilters();'/></td>  <td>$11M - $25M</td> <td>{$checkAmounts['g10m_le25m_count']}</td> </tr>";
       $displayRows .= "<tr><td><input type='checkbox' name='frevenueAmount' value='2' onClick='javascript:applyTableListFilters();'/></td>  <td>$1M - $10M</td> <td>{$checkAmounts['ge1m_le10m_count']}</td> </tr>";
       $displayRows .= "<tr><td><input type='checkbox' name='frevenueAmount' value='1' onClick='javascript:applyTableListFilters();'/></td>  <td>Less than $1M</td> <td>{$checkAmounts['less1m_count']}</td> </tr>";

       $displayRows = "<tbody>" .$displayRows."</tbody>" ;
       $output .= $displayRows."</table>";
   }

   $output .= "</div>";

   print $output;