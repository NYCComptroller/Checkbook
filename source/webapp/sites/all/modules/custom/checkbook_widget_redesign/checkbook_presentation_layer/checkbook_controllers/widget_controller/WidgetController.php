<?php

//todo: make abstract class to share custom implementation for other domains
class WidgetController {

//    public $node;
//    public $key;
//    public $service;
//    public $parameters;
    public $widgetViewConfigs;

    protected static $instance = NULL;

    protected function __construct() {
        $this->widgetViewConfigs = array();
    }

    public static function getInstance() {
        if (!isset(self::$instance)) {
            self::$instance = new WidgetController();
        }
        return self::$instance;
    }

    /**
     * returns the name of the view to be displayed based on the configuration
     * @param string $widget
     * @return view name to be displayed
     */
    public function getWidgetViewConfigName($widget) {

        $domain = CheckbookDomain::getCurrent();
        $view = null;

        switch($domain) {
            case Domain::$CONTRACTS;
//                $view = ContractsWidgetVisibilityService::getWidgetVisibility($widget);
                $view = $this->_getContractWidgetView($widget);
                break;
        }
        return $view;
    }

    /**
     * returns the legacy node id based on the configuration
     * @param string $widget
     * @return view name to be displayed
     */
    public function getWidgetLegacyNodeId($widget) {

        $config = self::_getCurrentWidgetViewConfig($widget);
        $legacy_node_id = $config->legacy_node_id;
        return $legacy_node_id;
    }

    private function _getCurrentWidgetViewConfig($widget) {

        $domain = CheckbookDomain::getCurrent();
        $dashboard = Dashboard::getCurrent();
        $config = null;

        $config = $config = $this->widgetViewConfigs[$domain][$dashboard][$widget];
        if(!isset($config)) {
            $config = self::_loadWidgetViewConfig($domain,$dashboard,$widget);
            $this->widgetViewConfigs[$domain][$dashboard][$widget] = $config;
        }
        return $config;
    }

    private function _loadWidgetViewConfig($domain,$dashboard,$widget) {

        $dimension = "";

        switch($domain) {
            case Domain::$CONTRACTS:
                $status = ContractStatus::getCurrent();
                $category = ContractCategory::getCurrent();
                $nostatusExpenseContracts = noStatusExpenseContracts::getCurrent();
                $dimension = isset($nostatusExpenseContracts)? "{$category}": "{$status}_{$category}";
                break;
        }
        $config_str = file_get_contents(realpath(drupal_get_path('module', 'checkbook_view_configs')) . "/{$domain}.json");
        $converter = new Json2PHPObject();
        $configuration = $converter->convert($config_str);
        $config = $configuration->$dashboard->$dimension->landing_page_widgets->$widget;

        return $config;
    }

    /**
     * Function will read the domain specific widget configuration to determine
     * whether or not to show the widget and return the name of the widget view config file to use
     *
     * @param $widget
     * @return null
     */
    private function _getContractWidgetView($widget) {

        $view = null;

        $config = $this->_getCurrentWidgetViewConfig($widget);
        $widget_config = $config->widget_config;
        $show = true;

        if(isset($widget_config)) {
            $visibility_parameters = $config->visibility_parameters;
            if(isset($visibility_parameters)) {

                foreach($visibility_parameters as $value) {
                    if(isset($value)) {

                        //Don't show widget if this parameter is in the URL
                        if(substr($value, 0, 1 ) == "-") {
                            $value = ltrim($value, "-");
                            if(RequestUtilities::getRequestParamValue($value))
                                return null;
                        }
                        //Don't show widget if this parameter is not in the URL
                        elseif(!RequestUtilities::getRequestParamValue($value)) {
                                return null;
                        }
                    }
                }
            }
            return $widget_config;
        }
        return null;
    }

    //todo: Move common functions out of widget_controller module for re-usability in unit tests

//    private static function _getContractsWidgetViewConfigName($widget) {
//
//        $dashboard = Dashboard::getCurrent();
//        $contract_type = ContractType::getCurrent();
//        $category = ContractsParameters::getContractCategory();
//        $view = NULL;
//
//        switch($widget){
//            case 'sample':
//
//                switch($dashboard) {
//
//                    case Dashboard::CITYWIDE:
//
//                        switch($contract_type) {
//
//                            case ContractType::ACTIVE_EXPENSE:
//
//                                break;
//
//                            case ContractType::REGISTERED_EXPENSE:
//
//                                break;
//
//                            case ContractType::ACTIVE_REVENUE:
//
//                                break;
//
//                            case ContractType::REGISTERED_REVENUE:
//
//                                break;
//
//                            case ContractType::PENDING_EXPENSE:
//
//                                break;
//
//                            case ContractType::PENDING_REVENUE:
//
//                                break;
//                        }
//
//                        break;
//
//                    case Dashboard::OGE:
//
//                        switch($contract_type) {
//
//                            case ContractType::ACTIVE_EXPENSE:
//
//                                break;
//
//                            case ContractType::REGISTERED_EXPENSE:
//
//                                break;
//                        }
//
//                        break;
//
//                    case Dashboard::MWBE:
//                        switch($contract_type) {
//
//                            case ContractType::ACTIVE_EXPENSE:
//
//                                break;
//
//                            case ContractType::REGISTERED_EXPENSE:
//
//                                break;
//
//                            case ContractType::ACTIVE_REVENUE:
//
//                                break;
//
//                            case ContractType::REGISTERED_REVENUE:
//
//                                break;
//
//                            case ContractType::PENDING_EXPENSE:
//
//                                break;
//
//                            case ContractType::PENDING_REVENUE:
//
//                                break;
//                        }
//
//                        break;
//
//                    case Dashboard::SUB_VENDORS:
//
//                        switch($contract_type) {
//
//                            case ContractType::ACTIVE_EXPENSE:
//
//                                break;
//
//                            case ContractType::REGISTERED_EXPENSE:
//
//                                break;
//                        }
//
//                        break;
//
//                    case Dashboard::MWBE_SUB_VENDORS:
//
//                        switch($contract_type) {
//
//                            case ContractType::ACTIVE_EXPENSE:
//
//                                break;
//
//                            case ContractType::REGISTERED_EXPENSE:
//
//                                break;
//                        }
//
//                        break;
//
//                    case Dashboard::SUB_VENDORS_MWBE:
//
//                        switch($contract_type) {
//
//                            case ContractType::ACTIVE_EXPENSE:
//
//                                break;
//
//                            case ContractType::REGISTERED_EXPENSE:
//
//                                break;
//                        }
//
//                        break;
//                }
//
//                break;
//            case 'departments':
//                if($category === 'expense'){
//                    if(Dashboard::isOGE()){
//                        if(RequestUtilities::getRequestParamValue('vendor'))
//                            $view = 'contracts_departments_view';
//                        else
//                            $view = 'oge_contracts_departments_view';
//                    }else{
//                        if(($dashboard == NULL || $dashboard == 'mp') && RequestUtilities::getRequestParamValue('agency')){
//                            $view = 'contracts_departments_view';
//                        }
//                    }
//                }
//                break;
//
//            case 'departments':
//
//                switch($dashboard) {
//
//                    case Dashboard::CITYWIDE:
//                    case Dashboard::MWBE:
//
//                        switch($contract_type) {
//
//                            case ContractType::ACTIVE_EXPENSE:
//                            case ContractType::REGISTERED_EXPENSE:
//                                $view = 'contracts_departments_view';
//                                break;
//                        }
//
//                        break;
//                    case Dashboard::OGE:
//
//                        switch($contract_type) {
//
//                            case ContractType::ACTIVE_EXPENSE:
//                            case ContractType::REGISTERED_EXPENSE:
//                                $view = 'oge_contracts_departments_view';
//                                break;
//                        }
//
//                        break;
//                }
//
//                break;
//            case 'industries':
//                if(!RequestUtilities::getRequestParamValue('cindustry')){
//                    switch($category) {
//                        case "expense":
//                            switch($dashboard) {
//                                case "ss":
//                                case "sp":
//                                    $view = 'sub_contracts_by_industries_view';
//                                    break;
//                                case "ms":
//                                    $view = 'mwbe_sub_contracts_by_industries_view';
//                                    break;
//                                default:
//                                    $view = ParameterUtils::isEDCPage() ? 'oge_contracts_by_industries_view' : 'contracts_by_industries_view';
//                                    break;
//                            }
//                            break;
//
//                        case "revenue":
//                            $view = 'revenue_contracts_by_industries_view';
//                            break;
//
//                        case "pending expense":
//                        case "pending revenue":
//                            $view = 'pending_contracts_by_industries_view';
//                            break;
//                    }
//                }
//                break;
//
//            case 'size':
//                if(!RequestUtilities::getRequestParamValue('csize')){
//                    switch($category) {
//                        case "expense":
//                            switch($dashboard) {
//                                case "ss":
//                                case "sp":
//                                    $view = 'sub_contracts_by_size_view';
//                                    break;
//                                case "ms":
//                                    $view = 'mwbe_sub_contracts_by_size_view';
//                                    break;
//                                default:
//                                    $view = ParameterUtils::isEDCPage() ? 'oge_contracts_by_size_view' : 'contracts_by_size_view';
//                                    break;
//                            }
//                            break;
//
//                        case "revenue":
//                            $view = 'revenue_contracts_by_size_view';
//                            break;
//
//                        case "pending expense":
//                        case "pending revenue":
//                            $view = 'pending_contracts_by_size_view';
//                            break;
//                    }
//                }
//                break;
//            case 'award_methods':
//                if(!RequestUtilities::getRequestParamValue('awdmethod')){
//                    switch($category) {
//                        case "expense":
//                            switch($dashboard) {
//                                case "ss":
//                                case "sp":
//                                case "ms":
//                                    $view = 'subvendor_award_methods_view';
//                                    break;
//                                default:
//                                    $view = ParameterUtils::isEDCPage() ? 'oge_award_methods_view' : 'expense_award_methods_view';
//                                    break;
//                            }
//                            break;
//
//                        case "revenue":
//                            $view = 'revenue_award_methods_view';
//                            break;
//
//                        case "pending expense":
//                        case "pending revenue":
//                            $view = 'pending_award_methods_view';
//                            break;
//                    }
//                }
//                break;
//            case 'master_agreements':
//                if(ParameterUtils::isEDCPage()){
//                    $view = 'oge_master_agreements_view';
//                }else{
//                    switch($category) {
//                        case 'expense':
//                            $view = 'master_agreements_view';
//                            break;
//                        case "pending expense":
//                            $view = 'pending_master_agreements_view';
//                            break;
//                        case "revenue":
//                            $view = '';
//                            break;
//                        case "pending revenue":
//                            $view = '';
//                            break;
//                    }
//                }
//                break;
//            case 'vendors':
//                if(!RequestUtilities::getRequestParamValue('vendor')){
//                    switch($category) {
//                        case "expense":
//                            switch($dashboard) {
//                                case "ss":
//                                case "sp":
//                                    $view = 'subcontracts_by_prime_vendors_view';
//                                    break;
//                                case "ms":
//                                case "mp":
//                                    $view = 'mwbe_expense_contracts_by_prime_vendors_view';
//                                    break;
//                                default:
//                                    $view = ParameterUtils::isEDCPage() ? 'oge_contracts_by_prime_vendors_view' : 'expense_contracts_by_prime_vendors_view';
//                                    break;
//                            }
//                            break;
//
//                        case "revenue":
//                            switch($dashboard) {
//                                case "mp":
//                                    $view = 'mwbe_revenue_contracts_by_prime_vendors_view';
//                                    break;
//                                default:
//                                    $view = 'revenue_contracts_by_prime_vendors_view';
//                                    break;
//                            }
//                            break;
//
//                        case "pending expense":
//                        case "pending revenue":
//                            switch($dashboard) {
//                                case "mp":
//                                    $view = 'mwbe_pending_contracts_by_prime_vendors_view';
//                                    break;
//                                default:
//                                    $view = 'pending_contracts_by_prime_vendors_view';
//                                    break;
//                            }
//                            break;
//                    }
//                }
//                break;
//            default :
//                //handle the exception when there is no match
//                $view = NULL;
//                break;
//        }
//        return $view;
//    }
//
//    public function getNode() {
//        if(!isset($this->node)) {
//            $this->node = self::_loadWidgetConfiguration($this->key);
//            $this->node = self::_mergeDefaultSettings($this->key);
//        }
//        return $this->node;
//    }
//
//    public function getService() {
//        if(!isset($this->service)) {
//            if(isset($this->node->widgetConfig->sqlConfig)) {
//                if(!isset($this->node->widgetConfig->getData) || (isset($this->node->widgetConfig->getData) && $this->node->widgetConfig->getData)){
//
//                    $sqlConfig = $this->node->widgetConfig->sqlConfig;
//                    $serviceName = $sqlConfig->serviceName;
//                    //TODO: Use service factory
//                    $this->service = new $serviceName($sqlConfig);
//                }
//            }
//        }
//        return $this->service;
//    }
//
//    //Read config
//    public function loadWidget($key){
//        $this->key = $key;
//        return $this->getNode();
//    }
//
//    private function _loadWidgetConfiguration($key){
//        $node =  new stdClass();
//        $node->type = "widget_controller";
//        $files = file_scan_directory( drupal_get_path('module','checkbook_view_configs') , '/^'.$key.'\.json$/');
//        if(count($files) > 0){
//            $file_names = array_keys($files);
//            $json = file_get_contents($file_names[0]);
//            $node->widget_json =  $json;
//        }
//        if($node->nid == null){
//            $node->nid = $key;
//        }
//        return $node;
//    }
//
//    private function _mergeDefaultSettings($node){
//        $widgetConfig = self::_mergeDefaultSettingsRecursively($node,'default_settings');
//        $node->widget_json = $widgetConfig->widget_json;
//        return $node;
//    }
//
//    private function _mergeDefaultSettingsRecursively($widgetConfig,$defaultConfigKey){
//
//        if(isset($defaultConfigKey)) {
//            $defaultWidgetConfig = _widget_controller_node_load_file($defaultConfigKey);
//            $converter = new Json2PHPObject();
//            $widgetJson =  $converter->convert($widgetConfig->widget_json);
//            $defaultWidgetJson = $converter->convert($defaultWidgetConfig->widget_json);
//            $mergedWidgetJson = drupal_array_merge_deep($defaultWidgetJson, $widgetJson);
//            $widgetConfig->widget_json = json_encode($mergedWidgetJson);
//            $defaultConfigKey = $mergedWidgetJson['defaultConfigKey'] == $defaultConfigKey ? null : $mergedWidgetJson['defaultConfigKey'];
//            return widget_merge_default_settings_recursively($widgetConfig,$defaultConfigKey);
//        }
//        return $widgetConfig;
//    }
//
//    //Load widget data counts
//    function loadWidgetDataCount($parameters) {
//
//        $this->_loadWidgetTotalRowCount($parameters);
//        $this->_loadWidgetHeaderCount($parameters);
//        return $this->node;
//    }
//
//    //Load widget total row count
//    private function _loadWidgetTotalRowCount($parameters) {
//
//        $service = $this->getService();
//        try {
//            $this->node->totalDataCount = $service->getWidgetDataCount($parameters);
//        }
//        catch(Exception $e) {
//            log_error("Error getting total row count: \n" . $e->getMessage()/*, $e*/);
//            $this->node->error = $e;
//        }
//    }
//
//    //Load widget header count
//    private function _loadWidgetHeaderCount($parameters) {
//
//        $service = $this->getService();
//        try {
//            $this->node->headerCount = $service->getWidgetHeaderCount($parameters);
//            if(!isset($this->node->headerCount))
//                $this->node->headerCount = $this->node->totalDataCount;
//        }
//        catch(Exception $e) {
//            log_error("Error getting header count: \n" . $e->getMessage()/*, $e*/);
//            $this->node->error = $e;
//        }
//    }
//
//    //Load widget data
//    function loadWidgetData($parameters) {
//        $results = null;
//        $orderBy = $this->_prepareOrderBy();
//        $limit = $this->node->widgetConfig->limit;
//        $service = $this->getService();
//
//        try {
//            $results = $service->getWidgetData($parameters, $limit, $orderBy);
//        }
//        catch(Exception $e) {
//            log_error("Error getting data from the controller: \n" . $e->getMessage()/*, $e*/);
//            $this->node->error = $e;
//        }
//        $this->node->data = $results;
//        $this->node->nodeAdjustedParamaterConfig = $parameters;
//
//        //Format widget data
//        $this->_formatWidgetData();
//
//        return $this->node;
//    }
//
//    //Format widget data
//    private function _formatWidgetData() {
//        $formatColumns = array_filter($this->node->widgetConfig->table_columns,
//            function($value) {
//                return isset($value->format);
//            });
//        $tooltipColumns = array_filter($this->node->widgetConfig->table_columns,
//            function($value) {
//                return isset($value->tooltip);
//            });
//        $derivedColumns = array_filter($this->node->widgetConfig->table_columns,
//            function($value) {
//                return isset($value->derivedColumn);
//            });
//
//        if(count($formatColumns) > 0 || count($tooltipColumns) > 0 || count($derivedColumns) > 0) {
//            foreach($this->node->data as $key=>$val) {
//                //formatting
//                foreach($formatColumns as $column) {
//                    switch($column->format) {
//                        case "dollar":
//                            $this->node->data[$key][$column->column] = custom_number_formatter_format($this->node->data[$key][$column->column],2,'$');
//                            break;
//                        case "date":
//                            $this->node->data[$key][$column->column] = custom_date_format($this->node->data[$key][$column->column]);
//                            break;
//                        case "number":
//                            $this->node->data[$key][$column->column] = number_format($this->node->data[$key][$column->column]);
//                            break;
//                    }
//                }
//                //tooltip
//                foreach($tooltipColumns as $column) {
//                    $this->node->data[$key][$column->column] = _get_tooltip_markup($this->node->data[$key][$column->column], $column->tooltip);
//                }
//                //derived
//                $original_row = $this->node->data[$key];
//                foreach($derivedColumns as $column) {
//                    $this->node->data[$key][$column->column] = $this->service->implDerivedColumn($column->derivedColumn,$original_row);
//                }
//            }
//        }
//        return $this->node;
//    }
//
//    function prepareInputParameters($path) {
//
//        if(!isset($this->parameters)) {
//            $this->parameters = array();
//            if (isset($this->node->widgetConfig->defaultParameters)) {
//                foreach ($this->node->widgetConfig->defaultParameters as $key => $value) {
//                    $values_array = explode('~',$value);
//                    $value = count($values_array) > 1 ? "(".implode(",", $values_array).")" : $value;
//                    $this->parameters[$key] = htmlspecialchars_decode(_replace_special_characters_decode($value));
//                }
//            }
//            if (isset($this->node->widgetConfig->validUrlParameters)) {
//                $urlParams = $this->node->widgetConfig->validUrlParameters;
//                $urlPath = drupal_get_path_alias($path);
//                $pathParams = explode('/', $urlPath);
//                for($i = 0; $i < count($pathParams); $i = $i + 1) {
//                    $key = $pathParams[$i];
//                    $value = $pathParams[$i+1];
//                    if(in_array($key,$urlParams)) {
//                        $values_array = explode('~',$value);
//                        $value = count($values_array) > 1 ? "(".implode(",", $values_array).")" : $value;
//                        $this->parameters[$key] = htmlspecialchars_decode(_replace_special_characters_decode($value));
//                    }
//                }
//            }
//        }
//
//        return $this->parameters;
//    }
//
//    private function _prepareOrderBy() {
//        $orderBy = "";
//
//        if (isset($this->node->widgetConfig->orderBy)) {
//            foreach ($this->node->widgetConfig->orderBy as $value) {
//                if(substr($value, 0, 1 ) == "-") {
//                    $value = ltrim($value, "-");
//                    $orderBy = $orderBy == "" ? "{$value} DESC" : ",{$value} DESC";
//                }
//                else {
//                    $orderBy = $orderBy == "" ? $value : ",{$value}";
//                }
//            }
//        }
//        return $orderBy;
//    }
} 