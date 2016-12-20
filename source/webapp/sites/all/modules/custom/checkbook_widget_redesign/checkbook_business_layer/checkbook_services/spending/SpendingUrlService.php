<?php
/**
 * Created by PhpStorm.
 * User: pshirodkar
 * Date: 12/7/16
 * Time: 1:36 PM
 */

class SpendingUrlService {


    static $landingPageParams = array("category"=>"category","industry"=>"industry","mwbe"=>"mwbe","dashboard"=>"dashboard","agency"=>"agency","vendor"=>"vendor","subvendor"=>"subvendor");
    /**
     * @param $agency_id
     * @return string
     */
    static function agencyUrl($agency_id, $legacy_node_id){
        if($legacy_node_id == 501) {
            $url = '/spending_landing'
                .RequestUtilities::_getUrlParamString('vendor')
                .RequestUtilities::_getUrlParamString('category')
                .RequestUtilities::_getUrlParamString('industry')
                .RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
                ._checkbook_project_get_year_url_param_string()
                . '/category/2/agency/'. $agency_id;
        } else {
            $url = '/spending_landing'
                .RequestUtilities::_getUrlParamString('vendor')
                .RequestUtilities::_getUrlParamString('category')
                .RequestUtilities::_getUrlParamString('industry')
                .RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
                ._checkbook_project_get_year_url_param_string()
                . '/agency/'. $agency_id;
        }
        return $url;
    }


    /**
     * @param $contractId
     * @return string
     */
    static function contractIdUrl($row){
        $contractType = self::getContractType($row['document_id']);
        if(strtolower($contractType) == 'mma1' || strtolower($contractType) == 'ma1'){
            $contractUrl = '/magid/'.$row['agreement_id'].'/doctype/'.$contractType;
        }else{
            $contractUrl = '/agid/'.$row['agreement_id'].'/doctype/'.$contractType;
        }
        $url = '/contract_details' .$contractUrl
               .RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
               .'/newwindow' ; 

        return $url;
    } 
    
     /**
     * @param $param - Widget Name to be used in the URL
     * @param $value - value of @param to be used in the URL
     * @param null $legacy_node_id
     * @return string
     */
    static function ytdSpendindUrl($param, $value, $legacy_node_id = null){
        $smnid_param = isset($legacy_node_id) ? '/smnid/'.$legacy_node_id : '';
        if($legacy_node_id == 501) {
            $url = '/panel_html/spending_transactions/spending/transactions'
                .RequestUtilities::_getUrlParamString('vendor')
                .RequestUtilities::_getUrlParamString('agency')
                .RequestUtilities::_getUrlParamString('category')
                .RequestUtilities::_getUrlParamString('industry')
                .RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
                ._checkbook_project_get_year_url_param_string()
                .$smnid_param
                . '/category/2/'.$param.'/'. $value;
        } else {
            $url = '/panel_html/spending_transactions/spending/transactions'
                .RequestUtilities::_getUrlParamString('vendor')
                .RequestUtilities::_getUrlParamString('agency')
                .RequestUtilities::_getUrlParamString('category')
                .RequestUtilities::_getUrlParamString('industry')
                .RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
                ._checkbook_project_get_year_url_param_string()
                .$smnid_param
                . '/'.$param.'/'. $value;
        }
        return $url;
    }


    /**
     * @param $param - Widget Name to be used in the URL
     * @param $value - value of @param to be used in the URL
     * @param null $legacy_node_id
     * @return string
     */
    static function contractAmountUrl($row, $legacy_node_id = null){
        $smnidParam = isset($legacy_node_id) ? '/smnid/'.$legacy_node_id : '';
        $contractType = self::getContractType($row['document_id']);
        if(strtolower($contractType) == 'mma1' || strtolower($contractType) == 'ma1'){
            $contractUrl = '/magid/'.$row['agreement_id'].'/doctype/'.$contractType;
        }else{
            $contractUrl = '/agid/'.$row['agreement_id'].'/doctype/'.$contractType;
        }
        
        $url = '/panel_html/spending_transactions/spending/transactions'
               .RequestUtilities::_getUrlParamString('vendor')
               .RequestUtilities::_getUrlParamString('agency')
               .RequestUtilities::_getUrlParamString('category')
               .RequestUtilities::_getUrlParamString('industry')
               .RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
               ._checkbook_project_get_year_url_param_string()
               .$smnidParam
               .$contractUrl;
        return $url;
    }



    /**
    * determines whether contract is master agreement or not based on the contract number
    * @return string
    */
    static function getContractType($contractNumber){
       $contractNumber_3 = substr($contractNumber, 0, 3); //get first 3 characters from contract number
       $contractNumber_4 = substr($contractNumber, 0, 4); //get first 4 characters from contract number
       $fourLetterContractTypes = array('mma1', 'cta1', 'rct1', 'pcc1', 'cta2', 'mac1');
       if(in_array(strtolower($contractNumber_4), $fourLetterContractTypes)){
           return $contractNumber_4;
       }else{
           return $contractNumber_3;
       }
    }

    /**
     * @param $minority_type_id
     * @return string
     */
    static function mwbeUrl($minority_type_id){
        $minority_type_id = $minority_type_id == 4 || $minority_type_id == 5 ? '4~5' : $minority_type_id;
        $url = '/spending_landing'
            .RequestUtilities::_getUrlParamString('vendor')
            .RequestUtilities::_getUrlParamString('category')
            .RequestUtilities::_getUrlParamString('dashboard')
            ._checkbook_project_get_year_url_param_string()
            . '/mwbe/'. $minority_type_id . '?expandBottomCont=true';
        return $url;
    }


    /**
     * @param $prime_vendor_id
     * @return string
     */
    static function primevendorUrl($vendor_id){
        $url = '/spending_landing'
            .RequestUtilities::_getUrlParamString('agency')
            .RequestUtilities::_getUrlParamString('category')
            .RequestUtilities::_getUrlParamString('industry')
            ._checkbook_project_get_year_url_param_string()
            . '/vendor/'. $vendor_id;
        return $url;
    }

    /**
     * @param $prime_vendor_id
     * @return string
     */
    static function vendorUrl($vendor_id){
        $url = '/spending_landing'
            .RequestUtilities::_getUrlParamString('agency')
            .RequestUtilities::_getUrlParamString('category')
            .RequestUtilities::_getUrlParamString('industry')
            .RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
            ._checkbook_project_get_year_url_param_string()
            . '/vendor/'. $vendor_id;
        return $url;
    }



    /**
     * @param $parameters
     * @param null $legacy_node_id
     * @return string
     */
    static function getFooterUrl($parameters,$legacy_node_id = null) {
        $dtsmnid_param = isset($legacy_node_id) ? '/dtsmnid/'.$legacy_node_id : '';
        $url = '/panel_html/spending_transactions/spending/transactions'
                .RequestUtilities::_getUrlParamString('vendor','fvendor')
                .RequestUtilities::_getUrlParamString('vendor')
                .RequestUtilities::_getUrlParamString('agency')
                .RequestUtilities::_getUrlParamString('category')
                .RequestUtilities::_getUrlParamString('industry')
                .RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
                ._checkbook_project_get_year_url_param_string()
                .$dtsmnid_param;
        
        return $url;
    }

    /**
     * Returns Sub Vendor YTD Spending Link Url based on values from current path & data row
     *
     * @param $node
     * @param $row
     * @return string
     */
    static function getSubVendorYtdSpendingUrl($legacy_node_id, $row){
        $override_params = array(
            'subvendor'=>$row['vendor_id'],
            'fvendor'=>$row['vendor_id'],
            'smnid'=>$legacy_node_id
        );
        return '/' . self::getSpendingTransactionPageUrl($override_params);
    }

    /**
     *  Returns a spending transaction page Url with custom parameters appended but instead of persisted
     *
     * @param array $override_params
     * @return string
     */
    static function getSpendingTransactionPageUrl($override_params = array()) {
        return self::getSpendingUrl('panel_html/spending_transactions/spending/transactions',$override_params);
    }

    /**
     * Function build the url using the path and the current Spending URL parameters.
     * The Url parameters can be overridden by the override parameter array.
     *
     * @param $path
     * @param array $override_params
     * @return string
     */
    static function getSpendingUrl($path, $override_params = array()) {

        $url =  $path . _checkbook_project_get_year_url_param_string();

        $pathParams = explode('/',drupal_get_path_alias($_GET['q']));
        $url_params = self::$landingPageParams;
        $exclude_params = array_keys($override_params);
        if(is_array($url_params)){
            foreach($url_params as $key => $value){
                if(!in_array($key,$exclude_params)){
                    $url .=  CustomURLHelper::get_url_param($pathParams,$key,$value);
                }
            }
        }

        if(is_array($override_params)){
            foreach($override_params as $key => $value){
                if(isset($value)){
                    if($key == 'yeartype' && $value == 'C'){
                        $value = 'B';
                    }
                    $url .= "/$key";
                    $url .= "/$value";
                }
            }
        }

        return $url;
    }

} 