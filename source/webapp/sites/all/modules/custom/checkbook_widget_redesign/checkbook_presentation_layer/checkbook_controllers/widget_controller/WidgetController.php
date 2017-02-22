<?php

//todo: make abstract class to share custom implementation for other domains
class WidgetController {

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
                $view = $this->_getContractWidgetView($widget);
                break;
            case Domain::$SPENDING;
                $view = $this->_getSpendingWidgetView($widget);
                break;
            case Domain::$REVENUE;
                $view = $this->_getRevenueWidgetView($widget);
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

        $config_str = file_get_contents(realpath(drupal_get_path('module', 'checkbook_view_configs')) . "/{$domain}.json");
        $converter = new Json2PHPObject();
        $configuration = $converter->convert($config_str);

        switch($domain) {
            case Domain::$CONTRACTS:
                $status = ContractStatus::getCurrent();
                $category = ContractCategory::getCurrent();
                $dimension = ($category == ContractCategory::NONE) ? "{$status}" : "{$status}_{$category}";
                $config = $configuration->$dashboard->$dimension->landing_page_widgets->$widget;
                break;
            case Domain::$SPENDING:
                $config = $configuration->$dashboard->landing_page_widgets->$widget;
                break;
            case Domain::$REVENUE:
                $config = $configuration->$dashboard->landing_page_widgets->$widget;
                break;
        }
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

    /**
     * Function will read the domain specific widget configuration to determine
     * whether or not to show the widget and return the name of the widget view config file to use
     *
     * @param $widget
     * @return null
     */
    private function _getSpendingWidgetView($widget) {

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
                            $param_value = explode(':', $value);
                            if((count($param_value) == 1 && RequestUtilities::getRequestParamValue($param_value[0])) || 
                               (count($param_value) == 2 && $param_value[1] == RequestUtilities::getRequestParamValue($param_value[0]))){
                                return null;
                            }
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
    
     /**
     * Function will read the domain specific widget configuration to determine
     * whether or not to show the widget and return the name of the widget view config file to use
     *
     * @param $widget
     * @return null
     */
    private function _getRevenueWidgetView($widget) {

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
}