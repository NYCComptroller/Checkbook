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
    $agencies = $departments = $departmentnms = $vendors = $expenditureCategories =$expenditureNms= $checkAmounts = $spendingCategories = array();

    //request criteria
    $reqAgencyId = (isset($node->widgetConfig->requestParams['agency_id']) ? $node->widgetConfig->requestParams['agency_id'] : $node->widgetConfig->requestParams['master_child_contract_agency_id']);
    $reqDeptId = $node->widgetConfig->requestParams['department_id'];
    $reqFiscalYearId = $node->widgetConfig->requestParams['check_eft_issued_nyc_year_id'];
    $reqVendorId = (isset($node->widgetConfig->requestParams['vendor_id']) ? $node->widgetConfig->requestParams['vendor_id'] : $node->widgetConfig->requestParams['master_child_contract_vendor_id']);
    $reqExpId = $node->widgetConfig->requestParams['expenditure_object_id'];
    $reqSpendCatId = $node->widgetConfig->requestParams['spending_category_id'];
    $reqDeptNm = $node->widgetConfig->requestParams['department_name'];
    $reqExpNm = $node->widgetConfig->requestParams['expenditure_object_name'];

    if(count($node->data) > 0){
        foreach($node->data as $data){
            if(array_key_exists('g100m_count',$data)){
                $checkAmounts = $data;
            }else if (array_key_exists('agency_agency_agency_name',$data)){
                $agencies[] = $data;
            }else if (array_key_exists('dept_dept_department_short_name',$data)){
                $departments[] = $data;
            }else if (array_key_exists('deptname_deptname',$data)){
                $departmentnms[] = $data;
            }
            else if (array_key_exists('vendor_vendor_legal_name',$data)){
              if(trim($data['vendor_vendor_legal_name']) != '')
                $vendors[] = $data;
            }else if (array_key_exists('expenditure_object_expenditure_object_expenditure_object_name',$data)){
                $expenditureCategories[] = $data;
            }else if (array_key_exists('expobjectname_expobjectname',$data)){
              $expenditureNms[] = $data;
            }
            else if (array_key_exists('category_category_spending_category_name',$data)){
                $spendingCategories[] = $data;
            }
        }
    }

          $output .= "<div class='title'>Narrow down your search:</div><div class='content clearfix'>";

              if(isset($reqAgencyId)){
			$output .= "<input type='hidden' id='fHideAgencyId' name='fHideAgencyId' value='{$reqAgencyId}'/>";
	      }else if(count($agencies) > 0){
			$output .= "<table id='agencyfilter' class='narrow-down'>";
			$output .= "<thead><tr><th colspan='3'>By Agency</th></tr></thead>";
			$i=0;
			$displayRows = NULL;
			$hiddenRows = NULL;
			foreach($agencies as $key=>$value){
			        $checked = ($reqAgencyId == $value['agency_agency']) ? 'checked' : '';
                                $agencyName = _break_text_custom2($value['agency_agency_agency_name']);
				if($i<5){
					$displayRows .= "<tr><td><input class='styled' type='checkbox' name='fagencyId' {$checked} value='{$value['agency_agency']}' onClick='javascript:applyTableListFilters(this.checked);'/></td>  <td>{$agencyName}</td> <td class=\"results\"><span class=\"results\">".number_format($value['txcount'])."</span></td> </tr>";
				}else{
					$hiddenRows .= "<tr><td><input class='styled' type='checkbox' name='fagencyId' {$checked} value='{$value['agency_agency']}' onClick='javascript:applyTableListFilters(this.checked);'/></td>  <td>{$agencyName}</td> <td class=\"results\"><span class=\"results\">".number_format($value['txcount'])."</span></td> </tr>";
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

		if(isset($reqAgencyId) && count($departments) > 0 && preg_match("dept\/",current_path())){
			$output .= "<table id='deptfilter' class='narrow-down'>";
			$output .= "<thead><tr><th colspan='3'>By Department</th></tr></thead>";
			$i=0;
			$displayRows = NULL;
			$hiddenRows = NULL;
			foreach($departments as $key=>$value){
				$checked = ($reqDeptId == $value['dept_dept']) ? 'checked' : '';
				$deptName = _break_text_custom2($value['dept_dept_department_short_name']);
				if($i<5){
					$displayRows .= "<tr><td><input class='styled' type='checkbox' name='fdeptId' {$checked} value='{$value['dept_dept']}' onClick='javascript:applyTableListFilters(this.checked);'/></td>  <td>{$deptName}</td> <td class=\"results\"><span class=\"results\">".number_format($value['txcount'])."</span></td> </tr>";
				}else{
					$hiddenRows .= "<tr><td><input class='styled' type='checkbox' name='fdeptId' {$checked} value='{$value['dept_dept']}' onClick='javascript:applyTableListFilters(this.checked);'/></td>  <td>{$deptName}</td> <td class=\"results\"><span class=\"results\">".number_format($value['txcount'])."</span></td> </tr>";
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
		if(isset($reqAgencyId) && count($departmentnms) > 0){
		  $output .= "<table id='deptfilter' class='narrow-down'>";
		  $output .= "<thead><tr><th colspan='3'>By Department</th></tr></thead>";
		  $i=0;
		  $displayRows = NULL;
		  $hiddenRows = NULL;
		  foreach($departmentnms as $key=>$value){
		    $checked = ($reqDeptNm == $value['deptname_deptname']) ? 'checked' : '';
		    $deptName = _break_text_custom2($value['deptname_deptname']);
		    if($i<5){
		      $displayRows .= "<tr><td><input class='styled' type='checkbox' name='fdeptNm' {$checked} value='{$value['deptname_deptname']}' onClick='javascript:applyTableListFilters(this.checked);'/></td>  <td>{$deptName}</td> <td class=\"results\"><span class=\"results\">".number_format($value['txcount'])."</span></td> </tr>";
		    }else{
		      $hiddenRows .= "<tr><td><input class='styled' type='checkbox' name='fdeptNm' {$checked} value='{$value['deptname_deptname']}' onClick='javascript:applyTableListFilters(this.checked);'/></td>  <td>{$deptName}</td> <td class=\"results\"><span class=\"results\">".number_format($value['txcount'])."</span></td> </tr>";
		    }
		    $i++;
		  }
		  	
		  if($displayRows){
		    $displayRows = "<tbody>" .$displayRows."</tbody>" ;
		  }
		
		  if($hiddenRows){
		    $hiddenRows = "<tbody id='hiddendeptnm' style='display: none;'>" .$hiddenRows."</tbody>";
		  }
		
		  $output .= $displayRows.$hiddenRows."</table>";
		
		  if(count($departmentnms) > 5){
		    $output .= "<div class='nd-more-link'><a href=\"javascript:toggleDisplay('hiddendeptnm');changeLinkText('showmorelinkAg','Departments')\"><div id='showmorelinkAg'>Show more Departments &#187;</div></a></div>";
		  }
		}
		if(count($vendors) > 0){
			$output .= "<table id='payeefilter' class='narrow-down'>";
			$output .= "<thead><tr><th colspan='3'>By Payee</th></tr></thead>";
			$i=0;
			$displayRows = NULL;
			$hiddenRows = NULL;
			foreach($vendors as $key=>$value){
				$checked = ($reqVendorId == $value['vendor_vendor'] && $value['vendor_vendor']  != null) ? 'checked' : '';
				$vendorName = _break_text_custom2($value['vendor_vendor_legal_name']);
				if($i<5){
					$displayRows .= "<tr><td><input class='styled' type='checkbox' name='fvendorId' {$checked}  value='{$value['vendor_vendor']}' onClick='javascript:applyTableListFilters(this.checked);'/></td>  <td>{$vendorName}</td> <td class=\"results\"><span class=\"results\">".number_format($value['txcount'])."</span></td> </tr>";
				}else{
					$hiddenRows .= "<tr><td><input class='styled' type='checkbox' name='fvendorId' {$checked}  value='{$value['vendor_vendor']}' onClick='javascript:applyTableListFilters(this.checked);'/></td>  <td>{$vendorName}</td> <td class=\"results\"><span class=\"results\">".number_format($value['txcount'])."</span></td> </tr>";
				}
				$i++;
			}
			
			if($displayRows){
				$displayRows = "<tbody>" .$displayRows."</tbody>" ;
			}

			if($hiddenRows){
				$hiddenRows = "<tbody id='hiddenpayees' style='display: none;'>" .$hiddenRows."</tbody>" ;
			}

			$output .= $displayRows.$hiddenRows."</table>";

			if(count($vendors) > 5){
				$output .= "<div class='nd-more-link'><a href=\"javascript:toggleDisplay('hiddenpayees');changeLinkText('showmorelinkPy','Payees')\"><div id='showmorelinkPy'>Show more Payees &#187;</div></a></div>";
			}
		}
		
		if(count($checkAmounts) > 0){
			$output .= "<table id='chkamtfilter' class='narrow-down'>";
			$output .= "<thead><tr><th colspan='3'>By Amount</th></tr></thead>";

			$displayRows = NULL;

            $displayRows .= "<tr><td><input class='styled' type='checkbox' name='fCheckAmount' value='6' onClick='javascript:applyTableListFilters(this.checked);'/></td>  <td>Greater than $100M</td> <td class=\"results\"><span class=\"results\">".number_format($checkAmounts['g100m_count'])."</span></td> </tr>";
            $displayRows .= "<tr><td><input class='styled' type='checkbox' name='fCheckAmount' value='5' onClick='javascript:applyTableListFilters(this.checked);'/></td>  <td>$51M - $100M</td> <td class=\"results\"><span class=\"results\">".number_format($checkAmounts['g50m_le100m_count'])."</span></td> </tr>";
            $displayRows .= "<tr><td><input class='styled' type='checkbox' name='fCheckAmount' value='4' onClick='javascript:applyTableListFilters(this.checked);'/></td>  <td>$26M - $50M</td> <td class=\"results\"><span class=\"results\">".number_format($checkAmounts['g25m_le50m_count'])."</span></td> </tr>";
            $displayRows .= "<tr><td><input class='styled' type='checkbox' name='fCheckAmount' value='3' onClick='javascript:applyTableListFilters(this.checked);'/></td>  <td>$11M - $25M</td> <td class=\"results\"><span class=\"results\">".number_format($checkAmounts['g10m_le25m_count'])."</span></td> </tr>";
            $displayRows .= "<tr><td><input class='styled' type='checkbox' name='fCheckAmount' value='2' onClick='javascript:applyTableListFilters(this.checked);'/></td>  <td>$1M - $10M</td> <td class=\"results\"><span class=\"results\">".number_format($checkAmounts['ge1m_le10m_count'])."</span></td> </tr>";
            $displayRows .= "<tr><td><input class='styled' type='checkbox' name='fCheckAmount' value='1' onClick='javascript:applyTableListFilters(this.checked);'/></td>  <td>Less than $1M</td> <td class=\"results\"><span class=\"results\">".number_format($checkAmounts['less1m_count'])."</span></td> </tr>";

			$displayRows = "<tbody>" .$displayRows."</tbody>" ;
			$output .= $displayRows. "</table>";
		}

		if(count($expenditureCategories) > 0 && preg_match("/expcategory\//",current_path())){
		  
			$output .= "<table id='expcatfilter' class='narrow-down'>";
			$output .= "<thead><tr><th colspan='3'>By Expense Category</th></tr></thead>";
			$i=0;
			$displayRows = NULL;
			$hiddenRows = NULL;
			foreach($expenditureCategories as $key=>$value){
				$checked = ($reqExpId == $value['expenditure_object_expenditure_object']) ? 'checked' : '';
				$categoryName = _break_text_custom2($value['expenditure_object_expenditure_object_expenditure_object_name']);
				if($i<5){
					$displayRows .= "<tr><td><input class='styled' type='checkbox' name='fCategoryId' {$checked}  value='{$value['expenditure_object_expenditure_object']}' onClick='javascript:applyTableListFilters(this.checked);'/></td>  <td>{$categoryName}</td> <td class=\"results\"><span class=\"results\">".number_format($value['txcount'])."</span></td> </tr>";
				}else{
					$hiddenRows .= "<tr><td><input class='styled' type='checkbox' name='fCategoryId' {$checked}  value='{$value['expenditure_object_expenditure_object']}' onClick='javascript:applyTableListFilters(this.checked);'/></td>  <td>{$categoryName}</td> <td class=\"results\"><span class=\"results\">".number_format($value['txcount'])."</span></td> </tr>";
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
        else if(count($expenditureNms) > 0){
		  $output .= "<table id='expcatfilter' class='narrow-down'>";
		  $output .= "<thead><tr><th colspan='3'>By Expense Category</th></tr></thead>";
		  $i=0;
		  $displayRows = NULL;
		  $hiddenRows = NULL;
		  foreach($expenditureNms as $key=>$value){
		    $checked = ($reqExpNm == $value['expobjectname_expobjectname']) ? 'checked' : '';
		    $categoryName = _break_text_custom2($value['expobjectname_expobjectname']);
		    $value['expobjectname_expobjectname'] =  urlencode($value['expobjectname_expobjectname']);
		    if($i<5){
		      $displayRows .= "<tr><td><input class='styled' type='checkbox' name='fCategoryNm' {$checked}  value='{$value['expobjectname_expobjectname']}' onClick='javascript:applyTableListFilters(this.checked);'/></td>  <td>{$categoryName}</td> <td class=\"results\"><span class=\"results\">".number_format($value['txcount'])."</span></td> </tr>";
		    }else{
		      $hiddenRows .= "<tr><td><input class='styled' type='checkbox' name='fCategoryNm' {$checked}  value='{$value['expobjectname_expobjectname']}' onClick='javascript:applyTableListFilters(this.checked);'/></td>  <td>{$categoryName}</td> <td class=\"results\"><span class=\"results\">".number_format($value['txcount'])."</span></td> </tr>";
		    }
		    $i++;
		  }
		  	
		  if($displayRows){
		    $displayRows = "<tbody>" .$displayRows."</tbody>" ;
		  }
		
		  if($hiddenRows){
		    $hiddenRows = "<tbody id='hiddencatnm' style='display: none;'>" .$hiddenRows."</tbody>" ;
		  }
		
		  $output .= $displayRows.$hiddenRows."</table>";
		
		  if(count($expenditureNms) > 5){
		    $output .= "<div class='nd-more-link'><a href=\"javascript:toggleDisplay('hiddencatnm');changeLinkText('showmorelinkCat','Expense Categories')\"><div id='showmorelinkCat'>Show more Expense Categories &#187;</div></a></div>";
		  }
		}
        if(count($spendingCategories) > 0){
            $output .= "<table id='spendexpcatfilter' class='narrow-down'>";
            $output .= "<thead><tr><th colspan='3'>By Spending Category</th></thead>";
            $i=0;
            $displayRows = NULL;
            $hiddenRows = NULL;
            foreach($spendingCategories as $key=>$value){
                $checked = ($reqSpendCatId == $value['category_category']) ? 'checked' : '';
                $spendCategoryName = _break_text_custom2($value['category_category_spending_category_name']);
                if($i<5){
                    $displayRows .= "<tr><td><input class='styled' type='checkbox' name='fSpendCategoryId' {$checked}  value='{$value['category_category']}' onClick='javascript:applyTableListFilters(this.checked);'/></td>  <td>{$spendCategoryName}</td> <td class=\"results\"><span class=\"results\">".number_format($value['txcount'])."</span></td> </tr>";
                }else{
                    $hiddenRows .= "<tr><td><input class='styled' type='checkbox' name='fSpendCategoryId' {$checked}  value='{$value['category_category']}' onClick='javascript:applyTableListFilters(this.checked);'/></td>  <td>{$spendCategoryName}</td> <td class=\"results\"><span class=\"results\">".number_format($value['txcount'])."</span></td> </tr>";
                }
                $i++;
            }

            if($displayRows){
                $displayRows = "<tbody>" .$displayRows."</tbody>" ;
            }

            if($hiddenRows){
                $hiddenRows = "<tbody id='hiddenspendcat' style='display: none;'>" .$hiddenRows."</tbody>" ;
            }

            $output .= $displayRows.$hiddenRows."</table>";

            if(count($spendingCategories) > 5){
                $output .= "<div class='nd-more-link'><a href=\"javascript:toggleDisplay('hiddenspendcat');changeLinkText('showmorelinkCat','Expense Types')\"><div id='showmorelinkCat'>Show more Expense Types &#187;</div></a></div>";
            }
        }

        $output .= "</div>";

		print $output;
