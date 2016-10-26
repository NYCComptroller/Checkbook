<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 10/19/16
 * Time: 2:37 PM
 */

class VendorService {

    /**
     * Given the vendor id, returns the corresponding vendor customer code
     * @param $vendor_id
     * @return null
     */
    static function getVendorCode($vendor_id){
        $vendor = _checkbook_project_querydataset("checkbook:vendor","vendor_customer_code",array("vendor_id"=>$vendor_id));
        return $vendor[0] ? $vendor[0]['vendor_customer_code'] : null;
    }

    /**
     * Given the sub vendor id, returns the corresponding sub vendor customer code
     * @param $subvendor_id
     * @return null
     */
    static function getSubVendorCode($subvendor_id){
        $result = NULL;
        $query = "SELECT v.vendor_customer_code
                FROM subvendor v
                JOIN (SELECT vendor_id, MAX(vendor_history_id) AS vendor_history_id FROM subvendor_history GROUP BY 1) vh
                  ON v.vendor_id = vh.vendor_id
                WHERE v.vendor_id = ".$subvendor_id;

        $subvendor = _checkbook_project_execute_sql_by_data_source($query,'checkbook');
        return $subvendor[0] ? $subvendor[0]['vendor_customer_code'] : null;
    }
}