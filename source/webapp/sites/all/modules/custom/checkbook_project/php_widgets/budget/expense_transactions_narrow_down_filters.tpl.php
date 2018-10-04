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
    $agencies = $departments = $expenditureCategories = $curAmounts = array();

    //request criteria
    $reqAgencyId = $node->widgetConfig->requestParams['agency_id'];
    $reqDeptId = $node->widgetConfig->requestParams['department_id'];
    $reqCategoryId = $node->widgetConfig->requestParams['object_class_id'];

    if(count($node->data) > 0){
        foreach($node->data as $data){
            if(array_key_exists('g100m_count',$data)){
                $curAmounts = $data;
            }else if (array_key_exists('agency_agency_agency_name',$data)){
                $agencies[] = $data;
            }else if (array_key_exists('dept_dept_department_short_name',$data)){
                $departments[] = $data;
            }else if (array_key_exists('object_class_object_class_object_class_name',$data)){
                $expenditureCategories[] = $data;
            }
        }
    }

    $output .= "<div class='title'>Narrow down your search:</div><div class='content clearfix'>";

     if(isset($reqAgencyId)){
        $output .= "<input type='hidden' id='fHideAgencyId' name='fHideAgencyId' value='{$reqAgencyId}'/>";
     }else if(count($agencies) > 0){
        $output .= "<table id='agencyfilter'>";
        $output .= "<th colspan='3'>By Agency</th>";
        $i=0;
        $displayRows = NULL;
        $hiddenRows = NULL;
        foreach($agencies as $key=>$value){
            $checked = ($reqAgencyId == $value['agency_agency']) ? 'checked' : '';
            $agencyName = _get_shortened_text($value['agency_agency_agency_name']);
            if($i<5){
                $displayRows .= "<tr><td><input class='styled' type='checkbox' name='fagencyId' {$checked} value='{$value['agency_agency']}' onClick='javascript:applyTableListFilters(this.checked);'/></td>  <td>{$agencyName}</td> <td>{$value['txcount']}</td> </tr>";
            }else{
                $hiddenRows .= "<tr><td><input class='styled' type='checkbox' name='fagencyId' {$checked} value='{$value['agency_agency']}' onClick='javascript:applyTableListFilters(this.checked);'/></td>  <td>{$agencyName}</td> <td>{$value['txcount']}</td> </tr>";
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

    if(isset($reqAgencyId) && count($departments) > 0){
        $output .= "<table id='deptfilter'>";
        $output .= "<th colspan='3'>By Department</th>";
        $i=0;
        $displayRows = NULL;
        $hiddenRows = NULL;
        foreach($departments as $key=>$value){
            $checked = ($reqDeptId == $value['dept_dept']) ? 'checked' : '';
            $deptName = _get_shortened_text($value['dept_dept_department_short_name']);
            if($i<5){
                $displayRows .= "<tr><td><input type='checkbox' name='fdeptId' {$checked} value='{$value['dept_dept']}' onClick='javascript:applyTableListFilters(this.checked);'/></td>  <td>{$deptName}</td> <td>{$value['txcount']}</td> </tr>";
            }else{
                $hiddenRows .= "<tr><td><input type='checkbox' name='fdeptId' {$checked} value='{$value['dept_dept']}' onClick='javascript:applyTableListFilters(this.checked);'/></td>  <td>{$deptName}</td> <td>{$value['txcount']}</td> </tr>";
            }
            $i++;
        }

        if($displayRows){
            $displayRows = "<tbody>" .$displayRows."</tbody>" ;
        }

        if($hiddenRows){
            $hiddenRows = "<tbody id='hiddendept' style='display: none;'>" .$hiddenRows."</tbody>";
        }

        $output .= $displayRows.$hiddenRows."</table>";

        if(count($departments) > 5){
            $output .= "<div class='nd-more-link'><a href=\"javascript:toggleDisplay('hiddendept');changeLinkText('showmorelinkAg','Departments')\"><div id='showmorelinkAg'>Show more Departments &#187;</div></a></div>";
        }
    }

    if(count($expenditureCategories) > 0){
        $output .= "<table id='expcatfilter'>";
        $output .= "<th colspan='3'>By Expense Category</th>";
        $i=0;
        $displayRows = NULL;
        $hiddenRows = NULL;
        foreach($expenditureCategories as $key=>$value){
            $checked = ($reqCategoryId == $value['object_class_object_class']) ? 'checked' : '';
            $categoryName = _get_shortened_text($value['object_class_object_class_object_class_name']);
            if($i<5){
                $displayRows .= "<tr><td><input type='checkbox' name='fExpCategoryId' {$checked}  value='{$value['object_class_object_class']}' onClick='javascript:applyTableListFilters(this.checked);'/></td>  <td>{$categoryName}</td> <td>{$value['txcount']}</td> </tr>";
            }else{
                $hiddenRows .= "<tr><td><input type='checkbox' name='fExpCategoryId' {$checked}  value='{$value['object_class_object_class']}' onClick='javascript:applyTableListFilters(this.checked);'/></td>  <td>{$categoryName}</td> <td>{$value['txcount']}</td> </tr>";
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

        if(count($expenditureCategories) > 5){
            $output .= "<div class='nd-more-link'><a href=\"javascript:toggleDisplay('hiddencat');changeLinkText('showmorelinkCat','Expense Categories')\"><div id='showmorelinkCat'>Show more Expense Categories &#187;</div></a></div>";
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
