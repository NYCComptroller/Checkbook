<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 4/25/16
 * Time: 10:26 AM
 */

class ContractsUrlService
{
  /**
   * @param $original_agreement_id
   * @param $document_code
   * @return string
   */
    public static function contractIdUrl($original_agreement_id, $document_code): string
    {
        return "/panel_html/contract_transactions/agid/" . $original_agreement_id
            . RequestUtilities::buildUrlFromParam([
                'status',
                'bottom_slider',
            ])
            . RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
            . "/doctype/" . $document_code;
    }

  /**
   * @param $original_agreement_id
   * @param $document_code
   * @return string
   */
    public static function masterContractIdUrl($original_agreement_id, $document_code): string
    {
        return "/panel_html/contract_transactions/contract_details/magid/" . $original_agreement_id
            . RequestUtilities::buildUrlFromParam([
                'status',
                'bottom_slider',
            ])
            . RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
            . "/doctype/" . $document_code;
    }

  /**
   * @param $original_agreement_id
   * @param $doctype
   * @param $fms_contract_number
   * @param null $pending_contract_number
   * @param null $version
   * @param null $linktype
   * @return string
   */
    public static function pendingMasterContractIdUrl($original_agreement_id, $doctype, $fms_contract_number, $pending_contract_number = null, $version = null, $linktype = null): string
    {
        $lower_doctype = strtolower($doctype);
        if ($original_agreement_id) {
            if (($lower_doctype == 'ma1') || ($lower_doctype == 'mma1') || ($lower_doctype == 'rct1')) {
                $url = '/panel_html/contract_transactions/magid/' . $original_agreement_id
                    . RequestUtilities::buildUrlFromParam('status')
                    . RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
                    . "/doctype/" . $doctype;
            } else {
                $url = '/panel_html/contract_transactions/agid/' . $original_agreement_id
                    . RequestUtilities::buildUrlFromParam('status')
                    . RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
                    . "/doctype/" . $doctype;
            }
        } else {
            $url = '/minipanels/pending_contract_transactions/contract/' . $fms_contract_number
                . RequestUtilities::buildUrlFromParam('status')
                . RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
                . '/version/' . $version;
        }
        return $url;
    }

  /**
   * @param $original_agreement_id
   * @param $doctype
   * @param $fms_contract_number
   * @param null $pending_contract_number
   * @param null $version
   * @param null $linktype
   * @return string
   */
    public static function pendingContractIdLink($original_agreement_id, $doctype, $fms_contract_number, $pending_contract_number = null, $version = null, $linktype = null): string
    {
        $lower_doctype = strtolower($doctype);
        if ($original_agreement_id) {
            if (($lower_doctype == 'ma1') || ($lower_doctype == 'mma1') || ($lower_doctype == 'rct1')) {
                $url = '/panel_html/contract_transactions/magid/' . $original_agreement_id . '/doctype/' . $doctype;
            } else {
                $url = '/panel_html/contract_transactions/agid/' . $original_agreement_id . '/doctype/' . $doctype;
            }
        } else {
            $url = '/minipanels/pending_contract_transactions/contract/' . $pending_contract_number . '/version/' . $version;
        }

        //Don't persist M/WBE parameter if there is no dashboard (this could be an advanced search parameter)
        $mwbe_parameter = RequestUtilities::get('dashboard') != null ? RequestUtilities::buildUrlFromParam('mwbe') : '';
        $url .= $mwbe_parameter;

        return $url;
    }

    /**
     * Gets the spent to date link Url for the contract spending
     * @param $spend_type_parameter
     * @param null $legacy_node_id
     * @return string
     */
    public static function spentToDateUrl($spend_type_parameter, $legacy_node_id = null): string
    {
        return "/contract/spending/transactions"
            . $spend_type_parameter
            . RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
            . RequestUtilities::buildUrlFromParam([
                'status',
                'status|contstatus',
                'agency|cagency',
                'vendor|cvendor',
                'awdmethod',
                'cindustry',
                'csize',
            ])
            . _checkbook_project_get_year_url_param_string()
            . RequestUtilities::buildUrlFromParam('year|syear')
            . "/doctype/CT1~CTA1~MA1"
            . "/contcat/" . ContractCategory::getCurrent()
            . (isset($legacy_node_id) ? "/smnid/" . $legacy_node_id . "/newwindow" : "/newwindow");
    }

    public static function masterAgreementSpentToDateUrl($spend_type_parameter, $legacy_node_id = null)
    {
        return "/contract/spending/transactions"
            . $spend_type_parameter
            . RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
            . RequestUtilities::buildUrlFromParam('status|contstatus')
            . (!Datasource::isOGE() ? RequestUtilities::buildUrlFromParam('agency|sagency') : "")
            . (!Datasource::isOGE() ? RequestUtilities::buildUrlFromParam('vendor|svendor') : RequestUtilities::buildUrlFromParam('vendor'))
            . RequestUtilities::buildUrlFromParam('cindustry|sindustry')
                .RequestUtilities::buildUrlFromParam([
                'awdmethod',
                'csize',
            ])
            . _checkbook_project_get_year_url_param_string()
            . RequestUtilities::buildUrlFromParam('year|syear')
            . "/contcat/" . ContractCategory::getCurrent()
            . (isset($legacy_node_id) ? "/smnid/" . $legacy_node_id . "/newwindow" : "/newwindow");
    }

    /**
     * Gets the Minority Type Name link for the given minority type id
     * @param $minority_type_id
     * @return NULL or string
     */
    public static function primeMinorityTypeUrl($minority_type_id): ?string
    {
        $showLink = Dashboard::isPrimeDashboard() && MinorityTypeService::isMWBECertified($minority_type_id);
        $dashboard = DashboardParameter::MWBE;
        return $showLink ? self::minorityTypeUrl($minority_type_id, $dashboard) : null;
    }

    /**
     * Get the minority type link url for a sub vendor.
     *
     * Rules:
     *
     * 1. Sub M/WBE category is only a link from Sub Dashboards
     * 2. Must be certified to be linkable
     * 3. If current dashboard is "Sub Vendors", redirect to "Sub Vendors (M/WBE)" dashboard
     *
     * @param $minority_type_id
     * @return string
     */
    public static function subMinorityTypeUrl($minority_type_id): ?string
    {
        $showLink = Dashboard::isSubDashboard() && MinorityTypeService::isMWBECertified($minority_type_id);
        $dashboard = DashboardParameter::getCurrent();
        $dashboard = $dashboard == DashboardParameter::SUB_VENDORS ? DashboardParameter::SUB_VENDORS_MWBE : $dashboard;
        return $showLink ? self::minorityTypeUrl($minority_type_id, $dashboard) : null;
    }

    /**
     * Gets the Minority Type Name link for the given minority type id
     * @param $minority_type_id
     * @param $dashboard
     * @return NULL or string
     */
    public static function minorityTypeUrl($minority_type_id, $dashboard): ?string
    {
        $url = NULL;
        if (MinorityTypeService::isMWBECertified($minority_type_id)) {
            $currentUrl = RequestUtilities::_getCurrentPage();
            $minority_type_id = ($minority_type_id == 4 || $minority_type_id == 5) ? '4~5' : $minority_type_id;
            $url = $currentUrl
                . RequestUtilities::buildUrlFromParam('syear|year')
                . _checkbook_project_get_year_url_param_string()
                . RequestUtilities::buildUrlFromParam([
                    'agency',
                    'cindustry',
                    'csize',
                    'awdmethod',
                    'contstatus|status',
                    'vendor',
                    'subvendor',
                ])
                . '/dashboard/' . $dashboard
                . '/mwbe/' . $minority_type_id;
        }
        return $url;
    }

  /**
   * @param $agency_id
   * @param null $original_agreement_id
   * @return string
   */
    public static function agencyUrl($agency_id, $original_agreement_id = null): string
    {
        $currentUrl = RequestUtilities::_getCurrentPage();
        return $currentUrl
            . (isset($original_agreement_id) ? ("/magid/" . $original_agreement_id) : '')
            . RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
            . RequestUtilities::buildUrlFromParam([
                'vendor',
                'cindustry',
                'csize',
                'awdmethod',
                'status',
                'bottom_slider',
            ])
            . _checkbook_project_get_year_url_param_string()
            . "/agency/" . $agency_id
            . "?expandBottomCont=true";
    }

  /**
   * @param $award_method_id
   * @return string
   */
    public static function awardmethodUrl($award_method_id): string
    {
        $currentUrl = RequestUtilities::_getCurrentPage();
        return $currentUrl
            . RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
            . RequestUtilities::buildUrlFromParam([
                'vendor',
                'cindustry',
                'csize',
                'agency',
                'status',
            ])
            . _checkbook_project_get_year_url_param_string()
            . "/awdmethod/" . $award_method_id
            . "?expandBottomCont=true";
    }

  /**
   * @param $industry_type_id
   * @return string
   */
   public static function industryUrl($industry_type_id): string
   {
        $currentUrl = RequestUtilities::_getCurrentPage();
        return $currentUrl
            . RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
            . RequestUtilities::buildUrlFromParam([
                'vendor',
                'agency',
                'csize',
                'awdmethod',
                'status',
                'bottom_slider',
            ])
            . _checkbook_project_get_year_url_param_string()
            . "/cindustry/" . $industry_type_id
            . "?expandBottomCont=true";
    }

  /**
   * @param $award_size_id
   * @return string
   */
    public static function contractSizeUrl($award_size_id): string
    {
        $currentUrl = RequestUtilities::_getCurrentPage();
        return $currentUrl
            . RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
            . RequestUtilities::buildUrlFromParam([
                'vendor',
                'subvendor',
                'agency',
                'csize',
                'awdmethod',
                'status',
            ])
            . _checkbook_project_get_year_url_param_string()
            . "/csize/" . $award_size_id
            . "?expandBottomCont=true";
    }

    /**
     * @param $parameters
     * @param null $legacy_node_id
     * @return string
     */
    public static function getFooterUrl($parameters, $legacy_node_id = null): string
    {
        list($subvendor, $vendor, $mwbe, $industry) = RequestUtilities::get(['subvendor', 'vendor', 'mwbe', 'cindustry']);
        $category = ContractCategory::getCurrent();

        $subvendor_code = $subvendor ? SubVendorService::getVendorCode($subvendor) : null;
        $vendor_code = $vendor ? PrimeVendorService::getVendorCode($vendor) : null;

        $subvendor_param = isset($subvendor_code) ? '/vendorcode/' . $subvendor_code : '';
        $vendor_param = isset($vendor_code) ? '/vendorcode/' . $vendor_code : '';
        $mwbe_param = isset($mwbe) ? (Dashboard::isSubDashboard() || $legacy_node_id == 720 ? '/smwbe/' . $mwbe : '/pmwbe/' . $mwbe) : '';



        if (Datasource::isOGE()) {
            $industry_param = isset($industry) ? '/cindustry/' . $industry : '';
        } else {
            $industry_param = isset($industry) ? (Dashboard::isSubDashboard() || $legacy_node_id == 720 ? '/scindustry/' . $industry : '/pcindustry/' . $industry) : '';
        }
        //Handle 3rd bottom navigation
        $bottom_slider = RequestUtilities::get('bottom_slider');
        if ($bottom_slider == "sub_vendor") {
            $mwbe_param = isset($mwbe) ? '/pmwbe/' . $mwbe : "";
        }

        $category_param = '/contcat/' . (isset($category) ? $category : ContractCategory::EXPENSE);
        $smnid_param = isset($legacy_node_id) ? '/smnid/' . $legacy_node_id : '';
        $contract_status = RequestUtilities::buildUrlFromParam('status|contstatus');
        $contract_status = isset($contract_status) && $contract_status != '' ? $contract_status : "/contstatus/P";
        // Add mwbe url parameter for pending contracts facet and transactions filtering
        if ($contract_status == '/contstatus/P' || $category == ContractCategory::REVENUE){
          $mwbe_param = isset($mwbe) ? "/mwbe/".$mwbe : '';
        }

      $contract_type = ContractType::getCurrent();
        if ($contract_type == 'registered_revenue' || $contract_type == 'pending_expense' || $contract_type == 'active_revenue' ){
          $mwbe_param = isset($mwbe) ? '/mwbe/' . $mwbe : "";
        }
        $path = Dashboard::isSubDashboard() && subVendorContractsByPrimeVendor::getCurrent() == ContractCategory::EXPENSE
            ? '/panel_html/sub_contracts_transactions/subcontract/transactions'
            : '/panel_html/contract_details/contract/transactions';

        return $path . $category_param
            . $contract_status
            . _checkbook_append_url_params()
            . RequestUtilities::buildUrlFromParam([
                'agency',
                'vendor',
                'subvendor',
                'vendor|fvendor',
                'awdmethod',
                'csize',
                'bottom_slider',
            ])
            . RequestUtilities::buildUrlFromParam('dashboard')
            . $mwbe_param . $subvendor_param . $vendor_param . $industry_param
            . _checkbook_project_get_year_url_param_string()
            . self::getDocumentCodeUrlString($parameters)
            . $smnid_param;
    }

  /**
   * @param $parameters
   * @return string
   */
    public static function getDocumentCodeUrlString($parameters): string
    {
        $doc_type = $parameters['doctype'];
        if (isset($doc_type)) {
            $doc_type = explode(",", $doc_type);
            $doc_type = implode("~", str_replace("'", "", $doc_type));
            $doc_type = str_replace("(", "", str_replace(")", "", $doc_type));
        } else {
            //contract category or doc type is derived from the page path
            $status = ContractStatus::getCurrent();
            $category = ContractCategory::getCurrent();
            switch ($status) {
                case ContractStatus::PENDING:
                    switch ($category) {
                        case ContractCategory::REVENUE:
                            $doc_type = "RCT1";
                            break;
                        default:
                            if ($parameters['contract_type'] == 'master_agreement') {
                              $doc_type = "MMA1~MA1~MAR";
                            }else if ($parameters['contract_type'] == 'child_contract') {
                              $doc_type = "CT1~CTA1~CTR";
                            }else {
                              $doc_type = "MMA1~MA1~MAR~CT1~CTA1~CTR";
                            }
                            break;
                    }
                    break;
                default:
                    switch ($category) {
                        case ContractCategory::REVENUE:
                            $doc_type = "RCT1";
                            break;
                        default:
                            $doc_type = "MA1~CTA1~CT1";
                            break;
                    }
                    break;
            }
        }

        return isset($doc_type) ? '/doctype/' . $doc_type : '';
    }

    /**
     * @param $blnIsMasterAgreement
     * @param $primeOrSub - Used to set prime or sub dollar difference parameter for
     *        Active/Registered Expense Contracts Modifications details links
     * @return string
     */
    public static function getAmtModificationUrlString($blnIsMasterAgreement = false, $primeOrSub = NULL): string
    {
        // Set modification parameter for Active/Registered Expense Contracts Modifications details links
        if ($primeOrSub == 'P') {
            return '/modamt/0/pmodamt/0';
        } else if ($primeOrSub == 'S') {
            return '/modamt/0/smodamt/0';
        }

        if ($blnIsMasterAgreement) {
          $url = "/modamt/0" . (ContractUtil::showSubVendorData() ? '/smodamt/0' : '/pmodamt/0');
        }else {
          $url = "/modamt/0/pmodamt/0/smodamt/0";
        }

        return $url;
    }

  /**
   * @return string
   */
  public static function getMocUrlString(): string
  {
     $url = '/mocs/Yes';
     return $url;
  }
    /**
     * Returns Contracts Prime Vendor Landing page URL for the given prime vendor id, year and year type
     * @param $vendor_id
     * @param $year_id
     * @param $effective_end_year_id
     * @param bool $current
     * @return string
     */
    public static function primeVendorUrl($vendor_id,$year_id = null, $effective_end_year_id=null,bool $current = true): string
    {
        $url = RequestUtilities::buildUrlFromParam([
                'agency',
                'contstatus|status',
                'csize',
            ])
            . _checkbook_project_get_year_url_param_string();

        list($year_type, $agency_id) = RequestUtilities::get(['yeartype', 'agency']);

        // For advanced search, if the year value is not set, get the latest minority type category for current Fiscal Year
        if (RequestUtil::isAdvancedSearchPage()){
          $year_url = self::applyYearParameter($effective_end_year_id);
          $year_id = $effective_end_year_id;
          $url = preg_replace("/\/year\/\d+/", $year_url, $url);
        }

        $latest_minority_id = $year_id
            ? PrimeVendorService::getLatestMinorityTypeByYear($vendor_id, $year_id, $year_type)
            : PrimeVendorService::getLatestMinorityType($vendor_id, $agency_id);

        $is_mwbe_certified = MinorityTypeService::isMWBECertified($latest_minority_id);
        $mwbe_amount = VendorService::getMwbeAmount($vendor_id,$year_id);
        $subven_amount = VendorService::getSubVendorAmount($vendor_id,$year_id);

        $urlPath = drupal_get_path_alias($_GET['q']);
        if (!preg_match('/pending/', $urlPath)) {
            if (!RequestUtilities::get('status')) {
                $url .= "/status/A";
            }
        }

        $minority_id = MappingUtil::getTotalMinorityIds('url');
        if ($is_mwbe_certified && isset($mwbe_amount)) {
                $url .= "/dashboard/mp/mwbe/".$minority_id."/vendor/" . $vendor_id;
        }
        else if ( $mwbe_amount == 0 && $subven_amount>0 || !isset($mwbe_amount)&&$subven_amount>0){
         // if prime is zero and sub amount is not zero. change dashboard to ms
          $url .= "/dashboard/ms/mwbe/".$minority_id."/vendor/" . $vendor_id;
        }
        else if($is_mwbe_certified){
            $url .= "/dashboard/mp/mwbe/".$minority_id."/vendor/" . $vendor_id;
        }
        else {
            $url .= RequestUtilities::buildUrlFromParam('datasource') . "/vendor/" . $vendor_id;
         }

        $currentUrl = RequestUtilities::_getCurrentPage();
        return ($current) ? $currentUrl . $url : $url;
    }

    /**
     * Returns Sub Vendor Landing page URL for the given sub vendor id in the given year and year type for
     * Active/Registered Contracts Landing Pages
     * @param $vendor_id
     * @param $year_id
     * @return string
     */
    public static function subVendorUrl($vendor_id, $year_id = null): string
    {
        list($year_type, $agency_id) = RequestUtilities::get(['yeartype', 'agency']);
        $currentUrl = RequestUtilities::_getCurrentPage();

        $latest_minority_id = !(isset($year_id))
            ? SubVendorService::getLatestMinorityType($vendor_id, $agency_id)
            : SubVendorService::getLatestMinorityTypeByYear($vendor_id, $year_id, $year_type);

        $url = RequestUtilities::buildUrlFromParam(['agency', 'contstatus|status'])
            . _checkbook_project_get_year_url_param_string();

        $current_dashboard = RequestUtilities::get("dashboard");
        $is_mwbe_certified = MappingUtil::isMWBECertified(array($latest_minority_id));

        //if M/WBE certified, go to M/WBE (Sub Vendor) else if NOT M/WBE certified, go to Sub Vendor dashboard
        $new_dashboard = $is_mwbe_certified ? "ms" : "ss";
        $status = strlen(RequestUtilities::buildUrlFromParam('contstatus|status')) == 0 ? "/status/A" : "";

        if ($current_dashboard != $new_dashboard) {
            return $currentUrl . $url . $status . "/dashboard/" . $new_dashboard . ($is_mwbe_certified ? "/mwbe/".MappingUtil::getTotalMinorityIds('url') : "") . "/subvendor/" . $vendor_id;
        } else {
            $url .= $status
                . RequestUtilities::buildUrlFromParam([
                    'cindustry',
                    'csize',
                    'awdmethod',
                ])
                . "/dashboard/" . $new_dashboard
                . ($is_mwbe_certified ? "/mwbe/".MappingUtil::getTotalMinorityIds('url') : "")
                . "/subvendor/" . $vendor_id;
            return $currentUrl . $url;
        }
    }

  /**
   * @param $docType
   * @return string
   */
    public static function applyLandingParameter($docType): string
    {
        if ($docType == "RCT1") {
            $page = "/contracts_revenue_landing";
        } else {
            $page = "/contracts_landing";
        }
        return $page;
    }

  /**
   * @param $effective_end_year_id
   * @return string
   */
  public static function applyYearParameter($effective_end_year_id = null): string
  {
    $year_id = RequestUtilities::get("year");
    if (RequestUtil::isAdvancedSearchPage() && !isset($year_id)){
     if($effective_end_year_id != '' && $effective_end_year_id < CheckbookDateUtil::getCurrentFiscalYearId()){
       $year_id = $effective_end_year_id;
     }
     else{
       $year_id = CheckbookDateUtil::getCurrentFiscalYearId();
     }
    }
    $url = '/year/' . $year_id;
    return $url;
  }

  /**
   * @param $effective_end_year_id
   * @return string
   */
  public static function adjustYeartypeParameter($effective_end_year_id = null): string
  {
    $url = _checkbook_project_get_year_url_param_string();
    $year = self::applyYearParameter($effective_end_year_id);
    $url =  preg_replace("/\/year\/\d+/",$year, $url) ;
    return $url;
  }
}
