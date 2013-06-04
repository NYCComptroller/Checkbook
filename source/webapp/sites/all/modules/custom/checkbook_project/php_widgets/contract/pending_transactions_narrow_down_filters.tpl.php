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
    $agencies = $vendors = $curAmount = $awardMethods = $industryTypes = array();

    //request criteria
    $reqAgencyId = $node->widgetConfig->requestParams['document_agency_id'];
    $reqVendorCode = $node->widgetConfig->requestParams['vendor_vendor'];
    $reqAwardMethodId = $node->widgetConfig->requestParams['award_method_id'];
    $reqIndustryTypeId = $node->widgetConfig->requestParams['industry_type_id'];

    if(count($node->data) > 0){
        foreach($node->data as $data){
            if(array_key_exists('g100m_count',$data)){
                $curAmount = $data;
            }else if (array_key_exists('agency_agency_agency_name',$data)){
                $agencies[] = $data;
            }else if (array_key_exists('vendor_legal_name_vendor_legal_name',$data)){
                $vendors[] = $data;
            }else if (array_key_exists('award_method_award_method_award_method_name',$data)){
                $awardMethods[] = $data;
            }else if (array_key_exists('industry_industry_industry_type_name',$data)){
                $industryTypes[] = $data;
            }
        }
    }

    $output .= "<div class='title'>Narrow down your search:</div><div class='content clearfix'>";

      if(count($agencies) > 0){
        $output .= "<table id='agencyfilter'>";
        $output .= "<thead><th colspan='3'>By Agency</th></thead>";
        $i=0;
        $displayRows = NULL;
        $hiddenRows = NULL;
        foreach($agencies as $key=>$value){
                $checked = ($reqAgencyId == $value['agency_agency']) ? 'checked' : '';
            if($i<5){
                $displayRows .= "<tr><td><input class='styled' type='checkbox' name='fagencyId' {$checked} value='{$value['agency_agency']}' onClick='javascript:applyTableListFilters();'/></td>  <td>{$value['agency_agency_agency_name']}</td> <td><span class=\"results\">".number_format($value['txcount'])."</span></td> </tr>";
            }else{
                $hiddenRows .= "<tr><td><input class='styled' type='checkbox' name='fagencyId' {$checked} value='{$value['agency_agency']}' onClick='javascript:applyTableListFilters();'/></td>  <td>{$value['agency_agency_agency_name']}</td> <td><span class=\"results\">".number_format($value['txcount'])."</span></td> </tr>";
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

    if(count($vendors) > 0){
        $output .= "<table id='payeefilter'>";
        $output .= "<thead><th colspan='3'>By Vendor</th></thead>";
        $i=0;
        $displayRows = NULL;
        $hiddenRows = NULL;
        foreach($vendors as $key=>$value){
            $checked = ($reqVendorCode == $value['vendor_vendor']) ? 'checked' : '';
            if($i<5){
                $displayRows .= "<tr><td><input class='styled' type='checkbox' name='fvendorId' {$checked}  value='{$value['vendor_vendor']}' onClick='javascript:applyTableListFilters();'/></td>  <td>{$value['vendor_legal_name_vendor_legal_name']}</td> <td><span class=\"results\">".number_format($value['txcount'])."</span></td> </tr>";
            }else{
                $hiddenRows .= "<tr><td><input class='styled' type='checkbox' name='fvendorId' {$checked}  value='{$value['vendor_customer_code_vendor_customer_code']}' onClick='javascript:applyTableListFilters();'/></td>  <td>{$value['vendor_legal_name_vendor_legal_name']}</td> <td><span class=\"results\">".number_format($value['txcount'])."</span></td> </tr>";
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
            $output .= "<div class='nd-more-link'><a href=\"javascript:toggleDisplay('hiddenpayees');changeLinkText('showmorelinkPy','Vendors')\"><div id='showmorelinkPy'>Show more Vendors &#187;</div></a></div>";
        }
    }

    if(count($curAmount) > 0){
        $output .= "<table id='chkamtfilter'>";
        $output .= "<thead><th colspan='3'>By Current Amount</th></thead>";

        $displayRows = NULL;
        $displayRows .= "<tr><td><input class='styled' type='checkbox' name='fCurAmount' value='6' onClick='javascript:applyTableListFilters();'/></td>  <td>Greater than $100M</td> <td><span class=\"results\">".number_format($curAmount['g100m_count'])."</span></td> </tr>";
        $displayRows .= "<tr><td><input class='styled' type='checkbox' name='fCurAmount' value='5' onClick='javascript:applyTableListFilters();'/></td>  <td>$51M - $100M</td> <td><span class=\"results\">".number_format($curAmount['g50m_le100m_count'])."</span></td> </tr>";
        $displayRows .= "<tr><td><input class='styled' type='checkbox' name='fCurAmount' value='4' onClick='javascript:applyTableListFilters();'/></td>  <td>$26M - $50M</td> <td><span class=\"results\">".number_format($curAmount['g25m_le50m_count'])."</span></td> </tr>";
        $displayRows .= "<tr><td><input class='styled' type='checkbox' name='fCurAmount' value='3' onClick='javascript:applyTableListFilters();'/></td>  <td>$11M - $25M</td> <td><span class=\"results\">".number_format($curAmount['g10m_le25m_count'])."</span></td> </tr>";
        $displayRows .= "<tr><td><input class='styled' type='checkbox' name='fCurAmount' value='2' onClick='javascript:applyTableListFilters();'/></td>  <td>$1M - $10M</td> <td><span class=\"results\">".number_format($curAmount['ge1m_le10m_count'])."</span></td> </tr>";
        $displayRows .= "<tr><td><input class='styled' type='checkbox' name='fCurAmount' value='1' onClick='javascript:applyTableListFilters();'/></td>  <td>Less than $1M</td> <td><span class=\"results\">".number_format($curAmount['less1m_count'])."</span></td> </tr>";

        $displayRows = "<tbody>" .$displayRows."</tbody>" ;
        $output .= $displayRows."</table>";
    }

    if(count($awardMethods) > 0){
        $output .= "<table id='chkamtfilter'>";
        $output .= "<thead><th colspan='3'>By Award Method</th></thead>";
        $i=0;
        $displayRows = NULL;
        $hiddenRows = NULL;
        foreach($awardMethods as $key=>$value){
            $checked = ($reqAwardMethodId == $value['award_method_award_method']) ? 'checked' : '';
            if($i<5){
                $displayRows .= "<tr><td><input class='styled' type='checkbox' name='fAwdMethodId' {$checked}  value='{$value['award_method_award_method']}' onClick='javascript:applyTableListFilters();'/></td>  <td>{$value['award_method_award_method_award_method_name']}</td> <td><span class=\"results\">".number_format($value['txcount'])."</span></td> </tr>";
            }else{
                $hiddenRows .= "<tr><td><input class='styled' type='checkbox' name='fAwdMethodId' {$checked}  value='{$value['award_method_award_method']}' onClick='javascript:applyTableListFilters();'/></td>  <td>{$value['award_method_award_method_award_method_name']}</td> <td><span class=\"results\">".number_format($value['txcount'])."</span></td> </tr>";
            }
            $i++;
        }

        if($displayRows){
            $displayRows = "<tbody>" .$displayRows."</tbody>" ;
        }

        if($hiddenRows){
            $hiddenRows = "<tbody id='hiddenawdmethod' style='display: none;'>" .$hiddenRows."</tbody>" ;
        }

        $output .= $displayRows.$hiddenRows."</table>";

        if(count($awardMethods) > 5){
            $output .= "<div class='nd-more-link'><a href=\"javascript:toggleDisplay('hiddenawdmethod');changeLinkText('showmorelinkAwdMethod','Award Methods')\"><div id='showmorelinkAwdMethod'>Show more Award Methods &#187;</div></a></div>";
        }
    }

    if(count($industryTypes) > 0){
        $output .= "<table id='chkamtfilter'>";
        $output .= "<thead><th colspan='3'>By Contract Industry</th></thead>";
        $i=0;
        $displayRows = NULL;
        $hiddenRows = NULL;
        foreach($industryTypes as $key=>$value){
            $checked = ($reqIndustryTypeId == $value['industry_industry']) ? 'checked' : '';
            if($i<5){
                $displayRows .= "<tr><td><input class='styled' type='checkbox' name='fIndustryTypeId' {$checked}  value='{$value['industry_industry']}' onClick='javascript:applyTableListFilters();'/></td>  <td>{$value['industry_industry_industry_type_name']}</td> <td><span class=\"results\">".number_format($value['txcount'])."</span></td> </tr>";
            }else{
                $hiddenRows .= "<tr><td><input class='styled' type='checkbox' name='fIndustryTypeId' {$checked}  value='{$value['industry_industry']}' onClick='javascript:applyTableListFilters();'/></td>  <td>{$value['industry_industry_industry_type_name']}</td> <td><span class=\"results\">".number_format($value['txcount'])."</span></td> </tr>";
            }
            $i++;
        }

        if($displayRows){
            $displayRows = "<tbody>" .$displayRows."</tbody>" ;
        }

        if($hiddenRows){
            $hiddenRows = "<tbody id='hiddenIndustry' style='display: none;'>" .$hiddenRows."</tbody>" ;
        }

        $output .= $displayRows.$hiddenRows."</table>";

        if(count($industryTypes) > 5){
            $output .= "<div class='nd-more-link'><a href=\"javascript:toggleDisplay('hiddenIndustry');changeLinkText('showmorelinkIndustry','Industries')\"><div id='showmorelinkIndustry'>Show more Industries &#187;</div></a></div>";
        }
    }

    $output .= "</div>";

	print $output;
