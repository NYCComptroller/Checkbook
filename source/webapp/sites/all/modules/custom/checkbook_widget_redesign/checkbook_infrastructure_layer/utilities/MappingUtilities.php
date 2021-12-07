<?php
/**
 * Created by PhpStorm.
 * User: pshirodkar
 * Date: 12/29/16
 * Time: 11:06 AM
 */

class MappingUtilities {

    private static $minority_type_category_map = array(
        2 => 'Black American',
        3 => 'Hispanic American',
        4 => 'Asian American',
        5 => 'Asian American',
        6 => 'Native American',
        7 => 'Non-M/WBE',
        9 => 'Women (Non-Minority)',
        11 => 'Individuals and Others',
        99 => 'Emerging (Non-Minority)'
    );

    static $mwbe_prefix = "M/WBE" ;

    static $total_mwbe_cats = "2~3~4~5~6~9~99";

    public static $minority_type_category_map_multi_chart = array(
        'Black American' => array(2),
        'Hispanic American' => array(3),
        'Asian American' => array(4,5),
        'Native American' => array(6),
        'Non-M/WBE' => array(7),
        'Women (Non-Minority)' => array(9),
        'Individuals and Others' => array(11),
        'Emerging (Non-Minority)' => array(99),
        'Total M/WBE' => array(2,3,4,5,6,9,99),
    );

    /** Returns the M/WBE category name based on the minority_type_id mapping
     * @param $minority_type_id
     * @return mixed
     */
    static function getMinorityCategoryById($minority_type_id) {
        return static::$minority_type_category_map[$minority_type_id];
    }

    static function isMWBECertified($mwbe_cats){
        if(count(array_intersect($mwbe_cats, static::$minority_type_category_map_multi_chart[static::$mwbe_prefix])) > 0){
            return true;
        }else{
            return false;
        }
    }
}
