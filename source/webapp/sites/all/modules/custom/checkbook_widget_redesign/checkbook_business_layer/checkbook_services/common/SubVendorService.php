<?php

class SubVendorService extends VendorService {

    /**
     * Returns the Latest Minority Type for the given Vendor and the current selected year
     * @param $vendor_id
     * @param null $agency_id
     * @param string $domain
     * @return mixed
     */
    public static function getLatestMinorityType($vendor_id, $agency_id = null, $domain = null) {
        return parent::getLatestMinorityType($vendor_id, $agency_id, 'S', $domain);
    }

    /**
     *  Returns the Latest Minority Type for the given Vendor and the current provided transaction year
     * @param $vendor_id
     * @param $year_id
     * @param $type_of_year
     * @param string $domain
     * @return bool
     */
    public static function getLatestMinorityTypeByYear($vendor_id, $year_id, $type_of_year, $domain = null) {
        return parent::getLatestMinorityTypeByYear($vendor_id, $year_id, $type_of_year,'S',$domain);
    }

    /**
     * Given the sub vendor id, returns the corresponding sub vendor customer code
     * @param $vendor_id
     * @return null
     */
    public static function getVendorCode($vendor_id){
        $result = NULL;
        $query = "SELECT v.vendor_customer_code
                FROM subvendor v
                JOIN (SELECT vendor_id, MAX(vendor_history_id) AS vendor_history_id
                FROM subvendor_history GROUP BY 1) vh ON v.vendor_id = vh.vendor_id
                WHERE v.vendor_id = ".$vendor_id;

        $vendor = _checkbook_project_execute_sql_by_data_source($query,'checkbook');
        return $vendor[0] ? $vendor[0]['vendor_customer_code'] : null;
    }
} 