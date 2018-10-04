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

    drupal_add_js(drupal_get_path('module', 'checkbook_project') .'/js/transactions.js');

    $output = NULL;
    $agencies = $revenueCategories = $revenueSources = $curAmounts = array();

    //request criteria
    $reqAgencyId = $node->widgetConfig->requestParams['agency_id'];
    $reqRevCategoryId = $node->widgetConfig->requestParams['revenue_category_id'];
    $reqRevSourceId = $node->widgetConfig->requestParams['revenue_source_id'];

    if(count($node->data) > 0){
        foreach($node->data as $data){
            if(array_key_exists('g100m_count',$data)){
                $curAmounts = $data;
            }else if (array_key_exists('agency_agency_agency_name',$data)){
                $agencies[] = $data;
            }else if (array_key_exists('category_category_revenue_category_name',$data)){
                $revenueCategories[] = $data;
            }else if (array_key_exists('revenue_source_revenue_source_revenue_source_name',$data)){
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
                $displayRows .= "<tr><td><input type='checkbox' name='fagencyId' {$checked} value='{$value['agency_agency']}' onClick='javascript:applyTableListFilters(this.checked);'/></td>  <td>{$agencyName}</td> <td>{$value['txcount']}</td> </tr>";
            }else{
                $hiddenRows .= "<tr><td><input type='checkbox' name='fagencyId' {$checked} value='{$value['agency_agency']}' onClick='javascript:applyTableListFilters(this.checked);'/></td>  <td>{$agencyName}</td> <td>{$value['txcount']}</td> </tr>";
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
        $output .= "<table id='revcatfilter'>";
        $output .= "<th colspan='3'>By Revenue Category</th>";
        $i=0;
        $displayRows = NULL;
        $hiddenRows = NULL;
        foreach($revenueCategories as $key=>$value){
            $checked = ($reqRevCategoryId == $value['category_category']) ? 'checked' : '';
            $categoryName = _get_shortened_text($value['category_category_revenue_category_name']);
            if($i<5){
                $displayRows .= "<tr><td><input type='checkbox' name='frevenueCategoryId' {$checked}  value='{$value['category_category']}' onClick='javascript:applyTableListFilters(this.checked);'/></td>  <td>{$categoryName}</td> <td>{$value['txcount']}</td> </tr>";
            }else{
                $hiddenRows .= "<tr><td><input type='checkbox' name='frevenueCategoryId' {$checked}  value='{$value['category_category']}' onClick='javascript:applyTableListFilters(this.checked);'/></td>  <td>{$categoryName}</td> <td>{$value['txcount']}</td> </tr>";
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
            $output .= "<div class='nd-more-link'><a href=\"javascript:toggleDisplay('hiddencat');changeLinkText('showmorelinkCat','Revenue Categories')\"><div id='showmorelinkCat'>Show more Revenue Categories &#187;</div></a></div>";
        }
    }

    if(count($revenueSources) > 0){
        $output .= "<table id='revSourcefilter'>";
        $output .= "<th colspan='3'>By Revenue Source</th>";
        $i=0;
        $displayRows = NULL;
        $hiddenRows = NULL;
        foreach($revenueSources as $key=>$value){
            $checked = ($reqRevSourceId == $value['revenue_source_revenue_source']) ? 'checked' : '';
            $revSourceName = _get_shortened_text($value['revenue_source_revenue_source_revenue_source_name']);
            if($i<5){
                $displayRows .= "<tr><td><input type='checkbox' name='frevenueSourceId' {$checked}  value='{$value['revenue_source_revenue_source']}' onClick='javascript:applyTableListFilters(this.checked);'/></td>  <td>{$revSourceName}</td> <td>{$value['txcount']}</td> </tr>";
            }else{
                $hiddenRows .= "<tr><td><input type='checkbox' name='frevenueSourceId' {$checked}  value='{$value['revenue_source_revenue_source']}' onClick='javascript:applyTableListFilters(this.checked);'/></td>  <td>{$revSourceName}</td> <td>{$value['txcount']}</td> </tr>";
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

        if(count($revenueSources) > 5){
            $output .= "<div class='nd-more-link'><a href=\"javascript:toggleDisplay('hiddencat');changeLinkText('showmorelinkSource','Revenue Sources')\"><div id='showmorelinkSource'>Show more Revenue Sources &#187;</div></a></div>";
        }
    }


    if(count($curAmounts) > 0){
        $output .= "<table id='chkamtfilter'>";
        $output .= "<th colspan='3'>By Current Modified Amount</th>";

        $displayRows = NULL;

        $displayRows .= "<tr><td><input class='styled' type='checkbox' name='fCurAmount' value='6' onClick='javascript:applyTableListFilters(this.checked);'/></td>  <td>Greater than $100M<td>{$curAmounts['g100m_count']}</td> </tr>";
        $displayRows .= "<tr><td><input class='styled' type='checkbox' name='fCurAmount' value='5' onClick='javascript:applyTableListFilters(this.checked);'/></td>  <td>$51M - $100M</td> <td>{$curAmounts['g50m_le100m_count']}</td> </tr>";
        $displayRows .= "<tr><td><input class='styled' type='checkbox' name='fCurAmount' value='4' onClick='javascript:applyTableListFilters(this.checked);'/></td>  <td>$26M - $50M</td> <td>{$curAmounts['g25m_le50m_count']}</td> </tr>";
        $displayRows .= "<tr><td><input class='styled' type='checkbox' name='fCurAmount' value='3' onClick='javascript:applyTableListFilters(this.checked);'/></td>  <td>$11M - $25M</td> <td>{$curAmounts['g10m_le25m_count']}</td> </tr>";
        $displayRows .= "<tr><td><input class='styled' type='checkbox' name='fCurAmount' value='2' onClick='javascript:applyTableListFilters(this.checked);'/></td>  <td>$1M - $10M</td> <td>{$curAmounts['ge1m_le10m_count']}</td> </tr>";
        $displayRows .= "<tr><td><input class='styled' type='checkbox' name='fCurAmount' value='1' onClick='javascript:applyTableListFilters(this.checked);'/></td>  <td>Less than $1M</td> <td>{$curAmounts['less1m_count']}</td> </tr>";

        $displayRows = "<tbody>" .$displayRows."</tbody>" ;
        $output .= $displayRows. "</table>";
    }

    $output .= "</div>";

	print $output;
