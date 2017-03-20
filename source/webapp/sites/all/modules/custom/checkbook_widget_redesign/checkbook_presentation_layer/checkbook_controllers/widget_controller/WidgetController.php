<?php

class WidgetViewConfigModel {

    public $widget_config;
    public $legacy_node_id;
    public $visibility_parameters;

    function __construct($config) {
        if(isset($config->legacy_node_id))
            $this->legacy_node_id = $config->legacy_node_id;
        if(isset($config->visibility_parameters))
            $this->visibility_parameters = $config->visibility_parameters;
        if(isset($config->widget_config))
            $this->widget_config = $config->widget_config;
    }
}

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
     * returns the legacy node id based on the configuration
     * @param $widget
     * @return mixed
     */
    public function getWidgetLegacyNodeId($widget) {

        $config = self::_getCurrentWidgetViewConfig($widget);
        $legacy_node_id = $config->legacy_node_id;
        return $legacy_node_id;
    }

    /**
     * @param $widget
     * @return null|WidgetViewConfigModel
     */
    private function _getCurrentWidgetViewConfig($widget) {

        $domain = CheckbookDomain::getCurrent();log_error($domain);
        $dashboard = Dashboard::getCurrent();
        $config = null;

        $config = $this->widgetViewConfigs[$domain][$dashboard][$widget];
        if(!isset($config)) {
            $config = self::_loadWidgetViewConfig($domain,$dashboard,$widget);
            $this->widgetViewConfigs[$domain][$dashboard][$widget] = $config;
        }
        return $config;
    }

    /**
     * @param $domain
     * @param $dashboard
     * @param $widget
     * @return null|WidgetViewConfigModel
     */
    private function _loadWidgetViewConfig($domain,$dashboard,$widget) {

        $config_str = file_get_contents(realpath(drupal_get_path('module', 'checkbook_view_configs')) . "/{$domain}.json");
        $converter = new Json2PHPObject();
        $configuration = $converter->convert($config_str);

        switch($domain) {
            case Domain::$CONTRACTS:
                $status = ContractStatus::getCurrent();
                $category = ContractCategory::getCurrent();
                $dimension = "{$status}_{$category}";
                $config = $configuration->$dashboard->$dimension->landing_page_widgets->$widget;
                break;
            case Domain::$PAYROLL:
                $dimension = PayrollLandingPage::getCurrent();
                $config = $configuration->$dashboard->$dimension->landing_page_widgets->$widget;
                break;
            case Domain::$SPENDING:
            case Domain::$REVENUE:
            case Domain::$BUDGET:
                $config = $configuration->$dashboard->landing_page_widgets->$widget;
                break;
        }
        return isset($config) ? new WidgetViewConfigModel($config) : null;
    }

    /**
     * Function will read the domain specific widget configuration to determine
     * whether or not to show the widget and return the name of the widget view config file to use
     *
     * @param $widget
     * @return null
     */
    public function getWidgetViewConfigName($widget) {

        $view = null;

        $config = $this->_getCurrentWidgetViewConfig($widget);
        $widget_config = $config->widget_config;

        if(isset($widget_config)) {
            $visibility_parameters = $config->visibility_parameters;
            if(isset($visibility_parameters)) {

                foreach($visibility_parameters as $value) {
                    if(isset($value)) {

                        //Don't show the widget if this parameter is in the URL
                        if(substr($value, 0, 1 ) == "-") {
                            $value = ltrim($value, "-");
                            $param_value = explode(':', $value);
                            if((count($param_value) == 1 && RequestUtilities::getRequestParamValue($param_value[0])) || 
                               (count($param_value) == 2 && $param_value[1] == RequestUtilities::getRequestParamValue($param_value[0]))){
                                return null;
                            }
                        }
                        //Don't show the widget if this parameter is not in the URL
                        else{
                            $param_value = explode(':', $value);
                            if(!((count($param_value) == 1 && RequestUtilities::getRequestParamValue($param_value[0])) || 
                               (count($param_value) == 2 && $param_value[1] == RequestUtilities::getRequestParamValue($param_value[0]))))
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