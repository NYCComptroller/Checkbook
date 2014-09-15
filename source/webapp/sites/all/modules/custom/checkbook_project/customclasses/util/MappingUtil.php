<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 8/6/14
 * Time: 5:07 PM
 */

class MappingUtil {

    private static $vendor_type_value_map = array(
        'pv' => 'P~PM',
        'sv' => 'S~SM',
        'mv' => 'SM~PM',
    );

    private static $vendor_type_name_map = array(
        'sv' => 'Sub Vendor',
        'pv' => 'Prime Vendor',
        'mv' => 'M/WBE Vendor',
    );

    /** Returns the vendor type value based on the vendor_type mapping */
    static function getVendorTypeValue($vendor_type) {
        return self::$vendor_type_value_map[$vendor_type];
    }

    /** Returns the vendor type name based on the vendor_type mapping */
    static function getVendorTypeName($vendor_type) {
        return self::$vendor_type_name_map[$vendor_type];
    }

    private static $minority_type_category_map = array(
        2 => 'Black American',
        3 => 'Hispanic American',
        4 => 'Asian American',
        5 => 'Asian American',
        7 => 'Non-M/WBE',
        9 => 'Women',
        11 => 'Individuals and Others',
//        array('?') =>'Emerging'
    );
    private static $minority_type_category_map_multi = array(
        'Total M/WBE' => array(2,3,4,5,9),
        'Asian American' => array(4,5),
        'Black American' => array(2),
        'Women' => array(9),
        'Hispanic American' => array(3),
//        array('?') =>'Emerging'
        'Non-M/WBE' => array(7),
        'Individuals and Others' => array(11),
    );
    
    
    static $mwbe_prefix = "M/WBE" ;
    
    private static $minority_type_category_map_multi_chart = array(
    		'Black American' => array(2),
    		'Hispanic American' => array(3),
    		'Asian American' => array(4,5),
    		'Non-M/WBE' => array(7),
    		'Women' => array(9),
    		'Individuals and Others' => array(11),
    		'M/WBE' => array(2,3,4,5,9),
    		//        array('?') =>'Emerging'
    );

    
    static function isMWBECertified($mwbe_cats){    	
		if(count(array_intersect($mwbe_cats, self::$minority_type_category_map_multi_chart[self::$mwbe_prefix])) > 0){
    		return true;
    	}else{
    		return false;
    	}
    } 

    /** Returns the M/WBE category name based on the minority_type_id mapping */
    static function getMinorityCategoryById($minority_type_id) {
        return self::$minority_type_category_map[$minority_type_id];
    }

    /** Returns the M/WBE category name and it's minority_type_id mapping as an array */
    static function getMinorityCategoryMappings() {
        return self::$minority_type_category_map_multi;
    }

    /**
     * @param null $minority_type_ids
     * @return int|string
     */
    static function getCurrenEhtnicityName($minority_type_ids = null) {
    	$mwbe_url_params = isset($minority_type_ids) ? $minority_type_ids : explode('~',_getRequestParamValue('mwbe'));
    	
    	foreach(self::$minority_type_category_map_multi_chart as $key=>$values){
    		if(count(array_diff($mwbe_url_params, $values)) == 0){
    			return $key;
    		}
    	}
    }
    
    
    
    static function getCurrentMWBETopNavFilters($active_domain_link, $domain){    	
    	
    	
    	$applicable_minority_types = self::getCurrentMWBEApplicableFilters($domain);
    	 
    	
    	$filters_html =  "<div class='main-nav-drop-down' style='display:none'>
  		<ul>
  			<li class='no-click title'>M/WBE Category</li>
  			<li class='no-click'><a href='/" . RequestUtil::getLandingPageUrl($domain) . "/mwbe/2~3~4~5~9" . "'>Total M/WBE</a></li>";
    	
    	
    	if(array_intersect($applicable_minority_types,array(4,5))){
    		$filters_html .=  "<li class='no-click'><a href='/" . $active_domain_link . "/mwbe/4~5" . "'>Asian American</a></li>";
    	}
    	if(array_intersect($applicable_minority_types,array(2))){
    		$filters_html .=  "<li class='no-click'><a href='/" . $active_domain_link . "/mwbe/2" . "'>Black American</a></li>";
    	}
    	if(array_intersect($applicable_minority_types,array(9))){
    		$filters_html .=  "<li class='no-click'><a href='/" . $active_domain_link . "/mwbe/9" . "'>Women</a></li>";
    	}
    	if(array_intersect($applicable_minority_types,array(3))){
    		$filters_html .=  "<li class='no-click'><a href='/" . $active_domain_link . "/mwbe/3" . "'>Hispanic American</a></li>";
    	}

    	if(array_intersect($applicable_minority_types,array(7,11))){
    		$filters_html .=  "<li class='no-click title'>Other</li>";
    		if(array_intersect($applicable_minority_types,array(7))){
    			$filters_html .=  "<li class='no-click'><a href='/" . $active_domain_link . "/mwbe/7" . "'>Non-M/WBE</a></li>";
    		}
    		if(array_intersect($applicable_minority_types,array(11))){
    			$filters_html .=  "<li class='no-click'><a href='/" . $active_domain_link . "/mwbe/11" . "'>Individuals & Others</a></li>";
    		}    		
    	}
		
  		$filters_html .=  "</ul>
  		</div>";
    	
    	return $filters_html;
    	
    	
    }
    
    
    static function getCurrentMWBEApplicableFilters($domain){
    	
    	switch($domain){
    		case "spending":
    			$urlParamMap = array("year"=>"year_id","yeartype"=>"type_of_year","agency"=>"agency_id","vendor"=>"vendor_id","category"=>"spending_category_id");
    			$where_filters = array();
    			
    			foreach($urlParamMap as $param=>$value){
    				if(_getRequestParamValue($param) != null){
    					$where_filters[] = _widget_build_sql_condition(' a1.' . $value, _getRequestParamValue($param));
    				}
    			}
    			
    			if(count($where_filters) > 0){
    				$where_filter = ' where ' . implode(' and ' , $where_filters);
    			}
    			
    			$sql = 'select a1.minority_type_id
				    from aggregateon_mwbe_spending_coa_entities a1
				   ' . $where_filter . '
				    group by a1.minority_type_id  ';
    			
    			
    			$data = _checkbook_project_execute_sql($sql);    			
    			
	    	break;	
	    	case "contracts":
	    		$urlParamMap = array("year"=>"fiscal_year_id","agency"=>"agency_id","yeartype"=>"type_of_year","awdmethod"=>"award_method_id","vendor"=>"vendor_id",
									"status"=>"status_flag","csize"=>"award_size_id","cindustry"=>"industry_type_id");
	    		$where_filters = array();
	    		foreach($urlParamMap as $param=>$value){
	    			if(_getRequestParamValue($param) != null){
    					$where_filters[] = _widget_build_sql_condition(' a1.' . $value, _getRequestParamValue($param));
    				}
	    		}
	    		
	    		if(count($where_filters) > 0){
	    			$where_filter = ' where ' . implode(' and ' , $where_filters);
	    		}
	    		
	    		$where_filter .= ' and rd.document_code in (' . ContractUtil::getCurrentPageDocumentIds() . ') ';
	    		
	    		
	    		$sql = 'select a1.minority_type_id
				    from {aggregateon_mwbe_contracts_cumulative_spending} a1
	    				join {ref_document_code} rd on a1.document_code_id = rd.document_code_id
				   ' . $where_filter . '
				    group by a1.minority_type_id';
	    		$data  = _checkbook_project_execute_sql($sql);	 
	    	break;
    	}
    	$applicable_minority_types = array();
    	foreach($data as $row){
    		$applicable_minority_types[] = $row['minority_type_id'];
    	}
    	return $applicable_minority_types;
    }

    static function getCurrentSubVendorTopNavFilters($active_domain_link){
    	 
    	 
    	 
    	 
    }
    
    
    
} 