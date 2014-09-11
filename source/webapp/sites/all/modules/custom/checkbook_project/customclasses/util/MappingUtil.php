<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 8/6/14
 * Time: 5:07 PM
 */

class MappingUtil {

    private static $minority_type_category_map = array(
        2 => 'Black American',
        3 => 'Hispanic American',
        4 => 'Asian American',
        5 => 'Asian American',
        7 => 'Non-M/WBE',
        9 => 'Women',
        11 => 'Individuals and Others',
    );
    private static $minority_type_category_map_multi = array(
        'Black American' => array(2),
        'Hispanic American' => array(3),
        'Asian American' => array(4,5),
        'Non-M/WBE' => array(7),
        'Women' => array(9),
        'Individuals and Others' => array(11),
        'Total M/WBE' => array(2,3,4,5,9),
    );

    private static $minority_type_category_map_multi_chart = array(
    		'Black American' => array(2),
    		'Hispanic American' => array(3),
    		'Asian American' => array(4,5),
    		'Non-M/WBE' => array(7),
    		'Women' => array(9),
    		'Individuals and Others' => array(11),
    		'M/WBE' => array(2,3,4,5,9),
    );

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

    /** Returns the M/WBE category name based on the minority_type_id mapping */
    static function getMinorityCategoryById($minority_type_id) {
        return self::$minority_type_category_map[$minority_type_id];
    }

    /** Returns the M/WBE category name and it's minority_type_id mapping as an array */
    static function getMinorityCategoryMappings() {
        return self::$minority_type_category_map_multi;
    }

    static function getCurrenEhtnicityName() {
    	$mwbe_url_params = explode('~',_getRequestParamValue('mwbe'));
    	
    	foreach(self::$minority_type_category_map_multi_chart as $key=>$values){
    		if(count(array_diff($mwbe_url_params, $values)) == 0){
    			return $key;
    		}
    	}
    }
    
    
} 