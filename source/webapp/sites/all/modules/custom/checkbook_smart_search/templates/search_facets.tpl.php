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

    drupal_add_js(drupal_get_path('module', 'checkbook_smart_search') .'/js/smart_search.js');
    $output = NULL;
    $oge=$agencies = $vendors = $expenseCategories = $revenueCategories
        = $fiscalYears = $domains = $contractCategory = $contractStatus = $spendingCategory = array();
    $reqOge=$reqAgencies = $reqFiscalYears = $reqDomains = $reqVendors =
        $reqExpenseCategories = $reqRevenueCategories = $reqSpendCategories = array();

    $searchTerms = explode('*|*', $_REQUEST['search_term']);

//arrays for the selected facet values from the URL
    for($i=1;$i < count($searchTerms);$i++){
        $filters = explode('=', $searchTerms[$i]);
        $filters[1] = urldecode($filters[1]);
        switch($filters[0]){
           case 'agency_names':
                $reqAgencies = explode("~", $filters[1]);
                break;
           case 'fiscal_years':
                $reqFiscalYears = explode("~", $filters[1]);
                break;
           case 'domains':
                $reqDomains = explode("~", $filters[1]);
                break;
           case 'vendor_names':
                $reqVendors = explode("~", $filters[1]);
                break;
           case 'expense_categories':
                $reqExpenseCategories = explode("~", $filters[1]);
                break;
           case 'revenue_categories':
                $reqRevenueCategories = explode("~", $filters[1]);
                break;
           case 'spending_categories':
                $reqSpendCategories = explode("~", $filters[1]);
                break;
           case 'contract_categories':
                $reqContCategories = explode("~", $filters[1]);
                break;
           case 'contract_status':
                $reqContStatus = explode("~", $filters[1]);
                break;
        }
    }

//arrays for the facet values
    foreach($facets as $key => $value){
        switch($key){
            case 'agency_name':
                $agencies[] = $value[$key];
                break;
            case 'fiscal_year':
                $fiscalYears[] = $value[$key];
                break;
            case 'domain':
                $domains[] = $value[$key];
                break;
            case 'vendor_name':
                $vendors[] = $value[$key];
                break;
            case 'expenditure_object_name':
                $expenseCategories[] = $value[$key];
                break;
            /*case 'revenue_category_name':
                $revenueCategories[] = $value;
                break;*/
            case 'spending_category_name':
                $spendingCategory[] = $value[$key];
                break;
            case 'contract_category_name':
                $contractCategory[] = $value[$key];
                break;
            case 'contract_status':
                $contractStatus[] = $value[$key];
                break;
            case 'oge':
                $oge[] = $value[$key];
                break;
        }

    }

    $output .= "<div class='title'>Narrow down your search:</div><div class='content'>";

//Begin of Narrow down search by Domain/Type of Data

    if(count($domains) > 0){
        $output .= "<table id='domainfilter'>";
        $output .= "<tr><th colspan='3'>By Type of Data</th></tr>";
        $displayRows = NULL;
        $contractFilters = NULL;
        $spendingFilters = NULL;

        foreach($domains[0] as $domainName=>$count){
            if($count > 0){
                $checked = (in_array($domainName, $reqDomains)) ? 'checked' : '';
                $displayRows .= "<tr>
                                    <td><input type='checkbox' name='fdomainName' {$checked} value='{$domainName}' onClick='javascript:applySearchFilters();'/></td>
                                    <td>{$domainName}</td>
                                    <td class=\"results\"><span class='results'>".number_format($count)."</span></td>
                                </tr>";

                //Spending Category Filter
                if($domainName == 'spending' && $checked == 'checked'){
                    $spendingFilters .= "<tr><td colspan='3' style='padding:0;'><table id='spendfilter'>";
                    $spendingFilters .= "<tr><th colspan=\"3\">By Spending Category</th></tr>";
                    foreach($spendingCategory[0] as $spendingName => $spendingCount){
                        $spendingValue = urlencode($spendingName);
                        if($spendingCount > 0){
                           // if(in_array('spending',$reqDomains) && count($reqSpendCategories) == 0){
                             //   $checked = 'checked';
                            //}else{
                                $checked = (in_array($spendingName, $reqSpendCategories)) ? 'checked' : '';
                            //}
                            $spendingFilters .= "<tr>
                                                    <td><input type='checkbox' name='fspendingCatName' {$checked} value='{$spendingValue}' onClick='javascript:applySearchFilters();'/></td>
                                                    <td>{$spendingName}</td>
                                                    <td class='results'><span class='results'>{$spendingCount}</span></td>
                                                 </tr>";
                        }
                    }
                    $spendingFilters .= "</table></td></tr>";

                    $displayRows .= $spendingFilters;
                }


                //Contract Category filter
                if($domainName == 'contracts' && $checked == 'checked'){
                    if(count($contractCategory[0]) > 0){
                        $contractFilters .= "<tr><td colspan='3' style='padding:0;'><table id='catfilter'>";
                        $contractFilters .= "<tr><th colspan=\"3\">By Category</th></tr>";
                        foreach($contractCategory[0] as $catName => $catCount){
                            if($catCount > 0){
                                //if(in_array('contracts',$reqDomains) && count($reqContCategories) == 0){
                                  //  $checked = 'checked';
                                //}else{
                                    $checked = (in_array($catName, $reqContCategories)) ? 'checked' : '';
                                //}
                                $contractFilters .= "<tr><td><input type='checkbox' name='fcontractCatName' {$checked} value='{$catName}' onClick='javascript:applySearchFilters();'/></td>
                                                         <td>{$catName}</td>
                                                         <td class='results'><span class='results'>{$catCount}</span></td>
                                                     </tr>";
                            }
                        }
                        $contractFilters .= "</table></td></tr>";
                    }

                     //Contract Status filter
                    if(count($contractStatus) > 0){
                        $contractFilters .= "<tr><td colspan='3' style='padding:0'><table id='statusfilter'>";
                        $contractFilters .= "<tr><th colspan=\"3\">By Status</th></tr>";
                        foreach($contractStatus as $key => $value){
                            foreach($value as $status => $statusCount){
                                //if(in_array('contracts',$reqDomains) && count($reqContStatus) == 0){
                                 //   $checked = 'checked';
                                //}else{
                                    $checked = (in_array($status, $reqContStatus)) ? 'checked' : '';
                                //}
                                //if($checked == 'checked'){
                                    $contractFilters .= "<tr><td><input type='checkbox' name='fcontractStatus' {$checked} value='{$status}' onClick='javascript:applySearchFilters();'/></td>
                                                             <td>{$status}</td>
                                                             <td class='results'><span class='results'>{$statusCount}</span></td>
                                                         </tr>";
                                    if(strtolower($status) == 'registered'){
                                       $contractFilters .= "<tr><td></td>
                                                            <td>Active</td><td>".$active_contracts['response']['numFound']."</td> </tr>";
                                    }
                                //}
                            }
                        }
                        $contractFilters .= "</table></td></tr>";
                    }

                    $displayRows .= $contractFilters;
                }
            }
        }
        
        $output .= $displayRows."</table>";
    }

//End of Narrow down search by Domain/Type of Data


//Begin of Narrow down search by OGE

if(count($oge) > 0){
    $output .= "<table id='ogefilter'>";
    $output .= "<th colspan='3'>By Other Government Entity</th>";
    $i=0;
    $displayRows = NULL;
    $hiddenRows = NULL;
    foreach($reqOge as $ $key => $value){
        $checked = "checked";$count = $oge[0][$value];
        if($i<5){
            $displayRows .= "<tr><td><input type='checkbox' name='fogeName' {$checked} value='{$value}' onClick='javascript:applySearchFilters();'/></td>
                                     <td>". htmlentities($value). "</td> <td class='results'><span class='results'>".number_format($count)."</span></td>
                                 </tr>";
        }else{
            $hiddenRows .= "<tr><td><input type='checkbox' name='fogeName' {$checked} value='{$value}' onClick='javascript:applySearchFilters();'/></td>
                                    <td>". htmlentities($value) ."</td> <td class='results'><span class='results'>".number_format($count)."</span></td>
                                </tr>";
        }
        $i++;
    }
    foreach($oge[0] as $ogeName=>$count){
        if($count > 0 && !in_array($ogeName, $reqOge)){
            $ogeValue = urlencode($ogeName);

            if($i<5){
                $displayRows .= "<tr><td><input type='checkbox' name='fagencyName' value='{$ogeValue}' onClick='javascript:applySearchFilters();'/></td>
                                         <td>". htmlentities($ogeName). "</td> <td class='results'><span class='results'>".number_format($count)."</span></td>
                                     </tr>";
            }else{
                $hiddenRows .= "<tr><td><input type='checkbox' name='fagencyName' value='{$ogeValue}' onClick='javascript:applySearchFilters();'/></td>
                                        <td>". htmlentities($ogeName) ."</td> <td class='results'><span class='results'>".number_format($count)."</span></td>
                                    </tr>";
            }
            $i++;
        }
    }

    if($displayRows){
        $displayRows = "<tbody>" .$displayRows."</tbody>" ;
    }

    if($hiddenRows){
        $hiddenRows = "<tbody id='hiddenagencies' style='display: none;'>" .$hiddenRows."</tbody>";
    }

    $output .= $displayRows.$hiddenRows."</table>";

    if($i > 5){
        $output .= "<div style='margin-bottom: 20px'><a href=\"javascript:toggleDisplay('hiddenoge');changeLinkText('showmorelinkAg','Oge')\">
                        <div id='showmorelinkAg'>Show more Entities &#187</div></a></div>";
    }
}

//End of Narrow down search by OGE

//Begin of Narrow down search by Agency

    if(count($agencies) > 0){
        $output .= "<table id='agencyfilter'>";
        $output .= "<th colspan='3'>By Agency</th>";
        $i=0;
        $displayRows = NULL;
        $hiddenRows = NULL;
        foreach($reqAgencies as $ $key => $value){
            $checked = "checked";$count = $agencies[0][$value];
            if($i<5){
                $displayRows .= "<tr><td><input type='checkbox' name='fagencyName' {$checked} value='{$value}' onClick='javascript:applySearchFilters();'/></td>
                                     <td>". htmlentities($value). "</td> <td class='results'><span class='results'>".number_format($count)."</span></td>
                                 </tr>";
            }else{
                $hiddenRows .= "<tr><td><input type='checkbox' name='fagencyName' {$checked} value='{$value}' onClick='javascript:applySearchFilters();'/></td>
                                    <td>". htmlentities($value) ."</td> <td class='results'><span class='results'>".number_format($count)."</span></td>
                                </tr>";
            }
            $i++;
        }
        foreach($agencies[0] as $agencyName=>$count){
            if($count > 0 && !in_array($agencyName, $reqAgencies)){
                $agencyValue = urlencode($agencyName);

                if($i<5){
                    $displayRows .= "<tr><td><input type='checkbox' name='fagencyName' value='{$agencyValue}' onClick='javascript:applySearchFilters();'/></td>
                                         <td>". htmlentities($agencyName). "</td> <td class='results'><span class='results'>".number_format($count)."</span></td>
                                     </tr>";
                }else{
                    $hiddenRows .= "<tr><td><input type='checkbox' name='fagencyName' value='{$agencyValue}' onClick='javascript:applySearchFilters();'/></td>
                                        <td>". htmlentities($agencyName) ."</td> <td class='results'><span class='results'>".number_format($count)."</span></td>
                                    </tr>";
                }
                $i++;
            }
        }

        if($displayRows){
            $displayRows = "<tbody>" .$displayRows."</tbody>" ;
        }

        if($hiddenRows){
            $hiddenRows = "<tbody id='hiddenagencies' style='display: none;'>" .$hiddenRows."</tbody>";
        }

        $output .= $displayRows.$hiddenRows."</table>";

        if($i > 5){
            $output .= "<div style='margin-bottom: 20px'><a href=\"javascript:toggleDisplay('hiddenagencies');changeLinkText('showmorelinkAg','Agencies')\">
                        <div id='showmorelinkAg'>Show more Agencies &#187</div></a></div>";
        }
    }

//End of Narrow down search by Agency

//Begin of Narrow down search by Vendor

    if(count($vendors) > 0){
        $output .= "<table id='vendorfilter'>";
        $output .= "<th colspan='3'>By Prime Vendor</th>";
        $i=0;
        $displayRows = NULL;
        $hiddenRows = NULL;
        foreach($reqVendors as $ $key => $value){
            $checked = "checked";$count = $vendors[0][$value];
            if($i<5){
                $displayRows .= "<tr><td><input type='checkbox' name='fvendorName' {$checked} value='{$value}' onClick='javascript:applySearchFilters();'/></td>
                                     <td>". htmlentities($value). "</td> <td class='results'><span class='results'>".number_format($count)."</span></td>
                                 </tr>";
            }else{
                $hiddenRows .= "<tr><td><input type='checkbox' name='fvendorName' {$checked} value='{$value}' onClick='javascript:applySearchFilters();'/></td>
                                    <td>". htmlentities($value) ."</td> <td class='results'><span class='results'>".number_format($count)."</span></td>
                                </tr>";
            }
            $i++;
        }
        foreach($vendors[0] as $vendorName=>$count){
            if($count > 0 && !in_array($vendorName, $reqVendors)){
                
                $vendorValue = urlencode($vendorName);
                if($i<5){
                    $displayRows .= "<tr><td><input type='checkbox' name='fvendorName' value='{$vendorValue}' onClick='javascript:applySearchFilters();'/></td>
                                         <td>{$vendorName}</td> <td class='results'><span class='results'>".number_format($count)."</span></td> </tr>";
                }else{
                    $hiddenRows .= "<tr><td><input type='checkbox' name='fvendorName' value='{$vendorValue}' onClick='javascript:applySearchFilters();'/></td>
                                        <td>{$vendorName}</td> <td class='results'><span class='results'>".number_format($count)."</span></td> </tr>";
                }
                $i++;
            }
        }

        if($displayRows){
            $displayRows = "<tbody>" .$displayRows."</tbody>" ;
        }

        if($hiddenRows){
            $hiddenRows = "<tbody id='hiddenvendors' style='display: none;'>" .$hiddenRows."</tbody>";
        }

        $output .= $displayRows.$hiddenRows."</table>";

        if($i > 5){
            $output .= "<div style='margin-bottom: 20px'><a href=\"javascript:toggleDisplay('hiddenvendors');changeLinkText('showmorelinkVd','Vendors')\"><div id='showmorelinkVd'>Show more Vendors</div></a></div>";
        }
    }

//End of Narrow down search by Vendor

//Begin of Narrow down search by Expense Category

    if(count($expenseCategories) > 0){
        $output .= "<table id='expensecategoryfilter'>";
        $output .= "<th colspan='3'>By Expense Category</th>";
        $i=0;
        $displayRows = NULL;
        $hiddenRows = NULL;
        
        foreach($reqExpenseCategories as $ $key => $value){
            $checked = "checked";$count = $expenseCategories[0][$value];
            if($i<5){
                $displayRows .= "<tr><td><input type='checkbox' name='fexpenseCategoryName' {$checked} value='{$value}' onClick='javascript:applySearchFilters();'/></td>
                                     <td>". htmlentities($value). "</td> <td class='results'><span class='results'>".number_format($count)."</span></td>
                                 </tr>";
            }else{
                $hiddenRows .= "<tr><td><input type='checkbox' name='fexpenseCategoryName' {$checked} value='{$value}' onClick='javascript:applySearchFilters();'/></td>
                                    <td>". htmlentities($value) ."</td> <td class='results'><span class='results'>".number_format($count)."</span></td>
                                </tr>";
            }
            $i++;
        }
        foreach($expenseCategories[0] as $expenseCategoryName=>$count){
            if($count > 0 && !in_array($expenseCategoryName, $reqExpenseCategories)){

                $expenseCategoryValue = urlencode($expenseCategoryName);
                if($i<5){
                    $displayRows .= "<tr><td><input type='checkbox' name='fexpenseCategoryName' value='{$expenseCategoryValue}' onClick='javascript:applySearchFilters();'/></td>
                                         <td>{$expenseCategoryName}</td> <td class='results'><span class='results'>".number_format($count)."</span></td>
                                     </tr>";
                }else{
                    $hiddenRows .= "<tr><td><input type='checkbox' name='fexpenseCategoryName' value='{$expenseCategoryValue}' onClick='javascript:applySearchFilters();'/></td>
                                        <td>{$expenseCategoryName}</td> <td class='results'><span class='results'>".number_format($count)."</span></td>
                                    </tr>";
                }
                $i++;
            }
        }

        if($displayRows){
            $displayRows = "<tbody>" .$displayRows."</tbody>" ;
        }

        if($hiddenRows){
            $hiddenRows = "<tbody id='hiddenexpensecategories' style='display: none;'>" .$hiddenRows."</tbody>";
        }

        $output .= $displayRows.$hiddenRows."</table>";

        if($i > 5){
            $output .= "<div style='margin-bottom: 20px'><a href=\"javascript:toggleDisplay('hiddenexpensecategories');changeLinkText('showmorelinkEc','Expense Categories')\"><div id='showmorelinkEc'>Show more Expense Categories</div></a></div>";
        }
    }

//End of Narrow down search by Expense Category

//Begin of Narrow down search by Revenue Category

    if(count($revenueCategories) > 0){
        $output .= "<table id='revenuecategoryfilter'>";
        $output .= "<th colspan='3'>By Revenue Category</th>";
        $i=0;
        $displayRows = NULL;
        $hiddenRows = NULL;
        foreach($revenueCategories[0] as $revenueCategoryName => $count){
            if($count > 0){
                $checked = (in_array($revenueCategoryName, $reqRevenueCategories)) ? 'checked' : '';
                $revenueCategoryValue = urlencode($revenueCategoryName);

                if($i<5){
                    $displayRows .= "<tr><td><input type='checkbox' name='frevenueCategoryName' {$checked} value='{$revenueCategoryValue}' onClick='javascript:applySearchFilters();'/></td>  <td>{$revenueCategoryName}</td> <td class='results'><span class='results'>".number_format($count)."</span></td> </tr>";
                }else{
                    $hiddenRows .= "<tr><td><input type='checkbox' name='frevenueCategoryName' {$checked} value='{$revenueCategoryValue}' onClick='javascript:applySearchFilters();'/></td>  <td>{$revenueCategoryName}</td> <td class='results'><span class='results'>".number_format($count)."</span></td> </tr>";
                }
                $i++;
            }
        }

        if($displayRows){
            $displayRows = "<tbody>" .$displayRows."</tbody>" ;
        }

        if($hiddenRows){
            $hiddenRows = "<tbody id='hiddenrevenuecategories' style='display: none;'>" .$hiddenRows."</tbody>";
        }

        $output .= $displayRows.$hiddenRows."</table>";

        if($i > 5){
            $output .= "<div style='margin-bottom: 20px'><a href=\"javascript:toggleDisplay('hiddenrevenuecategories');changeLinkText('showmorelinkRc','Revenue Categories')\"><div id='showmorelinkRc'>Show more Revenue Categories</div></a></div>";
        }
    }

//End of Narrow down search by Revenue Category

//Begin of Narrow down search by Fiscal Year

    if(count($fiscalYears) > 0){
        krsort($fiscalYears[0]);
        $output .= "<table id='fiscalyearfilter'>";
        $output .= "<th colspan='3'>By Fiscal Year</th>";
        $i=0;
        $displayRows = NULL;
        $hiddenRows = NULL;
        foreach($reqFiscalYears as $ $key => $value){
            $checked = "checked";$count = $fiscalYears[0][$value];
            if($i<5){
                $displayRows .= "<tr><td><input type='checkbox' name='fyear' {$checked} value='{$value}' onClick='javascript:applySearchFilters();'/></td>
                                     <td>". htmlentities($value). "</td> <td class='results'><span class='results'>".number_format($count)."</span></td>
                                 </tr>";
            }else{
                $hiddenRows .= "<tr><td><input type='checkbox' name='fyear' {$checked} value='{$value}' onClick='javascript:applySearchFilters();'/></td>
                                    <td>". htmlentities($value) ."</td> <td class='results'><span class='results'>".number_format($count)."</span></td>
                                </tr>";
            }
            $i++;
        }
        foreach($fiscalYears[0] as $fiscalYear=>$count){
            if($count > 0 && !in_array($fiscalYear, $reqFiscalYears)){

                if($i<5){
                    $displayRows .= "<tr><td><input type='checkbox' name='fyear' value='{$fiscalYear}' onClick='javascript:applySearchFilters();'/></td>
                                         <td>{$fiscalYear}</td> <td class='results'><span class='results'>".number_format($count)."</span></td>
                                     </tr>";
                }else{
                    $hiddenRows .= "<tr><td><input type='checkbox' name='fyear' value='{$fiscalYear}' onClick='javascript:applySearchFilters();'/></td>
                                        <td>{$fiscalYear}</td> <td class='results'><span class='results'>".number_format($count)."</span></td>
                                    </tr>";
                }
                $i++;
            }
        }

        if($displayRows){
            $displayRows = "<tbody>" .$displayRows."</tbody>" ;
        }

        if($hiddenRows){
            $hiddenRows = "<tbody id='hiddenfiscalyears' style='display: none;'>" .$hiddenRows."</tbody>";
        }

        $output .= $displayRows.$hiddenRows."</table>";

        if($i > 5){
            $output .= "<div style='margin-bottom: 20px'><a href=\"javascript:toggleDisplay('hiddenfiscalyears');changeLinkText('showmorelinkFY','Fiscal Years')\"><div id='showmorelinkFY'>Show more Fiscal Years</div></a></div></div>";
        }
    }

//End of Narrow down search by Fiscal Year

print $output;