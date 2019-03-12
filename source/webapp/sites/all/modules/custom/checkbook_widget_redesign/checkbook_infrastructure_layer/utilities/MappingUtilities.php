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
        7 => 'Non-M/WBE',
        9 => 'Women',
        11 => 'Individuals and Others',
//        array('?') =>'Emerging'
    );

    static $mwbe_prefix = "M/WBE" ;

    static $total_mwbe_cats = "2~3~4~5~9";

    public static $minority_type_category_map_multi_chart = array(
        'Black American' => array(2),
        'Hispanic American' => array(3),
        'Asian American' => array(4,5),
        'Non-M/WBE' => array(7),
        'Women' => array(9),
        'Individuals and Others' => array(11),
        'M/WBE' => array(2,3,4,5,9),
        //        array('?') =>'Emerging'
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
