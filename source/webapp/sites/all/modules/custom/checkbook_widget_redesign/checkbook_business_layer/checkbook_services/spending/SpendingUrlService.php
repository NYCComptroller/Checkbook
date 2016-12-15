<?php
/**
 * Created by PhpStorm.
 * User: pshirodkar
 * Date: 12/7/16
 * Time: 1:36 PM
 */

class SpendingUrlService {
    
    /**
     * @param $agency_id
     * @return string
     */
    static function agencyUrl($agency_id){
        $url = '/spending_landing'
               .RequestUtilities::_getUrlParamString('vendor')
               .RequestUtilities::_getUrlParamString('category')
               .RequestUtilities::_getUrlParamString('industry')
               .RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
               ._checkbook_project_get_year_url_param_string()
               . '/agency/'. $agency_id;
        return $url;
    }

    /**
     * @param $agency_id
     * @return string
     */
    static function payrollagencyUrl($agency_id){
        $url = '/spending_landing'
            .RequestUtilities::_getUrlParamString('vendor')
            .RequestUtilities::_getUrlParamString('category')
            .RequestUtilities::_getUrlParamString('industry')
            .RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
            ._checkbook_project_get_year_url_param_string()
            . '/category/2/agency/'. $agency_id;
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
        $url = '/panel_html/spending_transactions/spending/transactions'
               .RequestUtilities::_getUrlParamString('vendor')
               .RequestUtilities::_getUrlParamString('agency')
               .RequestUtilities::_getUrlParamString('category')
               .RequestUtilities::_getUrlParamString('industry')
               .RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
               ._checkbook_project_get_year_url_param_string()
               .$smnid_param
               . '/'.$param.'/'. $value;
        return $url;
    }

    /**
     * @param $param - Widget Name to be used in the URL
     * @param $value - value of @param to be used in the URL
     * @param null $legacy_node_id
     * @return string
     */
    static function payrollagenciesytdSpendindUrl($param, $value, $legacy_node_id = null){
        $smnid_param = isset($legacy_node_id) ? '/smnid/'.$legacy_node_id : '';
        $url = '/panel_html/spending_transactions/spending/transactions'
            .RequestUtilities::_getUrlParamString('vendor')
            .RequestUtilities::_getUrlParamString('agency')
            .RequestUtilities::_getUrlParamString('category')
            .RequestUtilities::_getUrlParamString('industry')
            .RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
            ._checkbook_project_get_year_url_param_string()
            .$smnid_param
            . '/category/2/'.$param.'/'. $value;
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
                .RequestUtilities::_getUrlParamString('vendor')
                .RequestUtilities::_getUrlParamString('fvendor')
                .RequestUtilities::_getUrlParamString('agency')
                .RequestUtilities::_getUrlParamString('category')
                .RequestUtilities::_getUrlParamString('industry')
                .RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
                ._checkbook_project_get_year_url_param_string()
                .$dtsmnid_param;
        
        return $url;
    }
} 