<?php

class PrimeVendorService extends VendorService {

    /**
     * Returns the Latest Minority Type for the given Vendor and the current selected year
     * @param $vendor_id
     * @param null $agency_id
     * @param string $domain
     * @return mixed
     */
    public static function getLatestMinorityType($vendor_id, $agency_id = null, $domain = null) {
        return parent::getLatestMinorityType($vendor_id, $agency_id, 'P', $domain);
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
        return parent::getLatestMinorityTypeByYear($vendor_id, $year_id, $type_of_year,'P',$domain);
    }

    /**
     * Given the vendor id, returns the corresponding vendor customer code
     * @param $vendor_id
     * @return null
     */
    public static function getVendorCode($vendor_id){
        $vendor = _checkbook_project_querydataset("checkbook:vendor","vendor_customer_code",array("vendor_id"=>$vendor_id));
        return $vendor[0] ? $vendor[0]['vendor_customer_code'] : null;
    }
} 