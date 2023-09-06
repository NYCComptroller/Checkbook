<?php
namespace Drupal\widget_controller;

use Drupal\checkbook_infrastructure_layer\Constants\Common\CheckbookDomain;
use Drupal\checkbook_infrastructure_layer\Constants\Common\Dashboard;
use Drupal\checkbook_infrastructure_layer\Constants\Contract\ContractCategory;
use Drupal\checkbook_infrastructure_layer\Constants\Contract\ContractStatus;
use Drupal\checkbook_infrastructure_layer\Constants\Payroll\PayrollLandingPage;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\data_controller\Common\Object\Converter\Handler\Json2PHPObject;

class WidgetController {

    public $widgetViewConfigs;

    protected static $instance = NULL;

  /**
   * WidgetController constructor.
   */
    protected function __construct() {
        $this->widgetViewConfigs = array();
    }

  /**
   * @return null
   */
    public static function getInstance() {
        if (!isset(static::$instance)) {
            static::$instance = new WidgetController();
        }
        return static::$instance;
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
    public function getWidgetParamConfig($widget) {
        $config = self::_getCurrentWidgetViewConfig($widget);
        return $config->param_config;
    }

    /**
     * @param $widget
     * @return null|WidgetViewConfigModel
     */
    private function _getCurrentWidgetViewConfig($widget):?WidgetViewConfigModel
    {
        $domain = CheckbookDomain::getCurrent();
        $dashboard = Dashboard::getCurrent();
        $config = $this->widgetViewConfigs[$domain][$dashboard][$widget] ?? null;
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
    private function _loadWidgetViewConfig($domain,$dashboard,$widget):?WidgetViewConfigModel
    {
        $config_str = file_get_contents(realpath(\Drupal::service('extension.list.module')->getPath('checkbook_view_configs')) . "/src/config/".$domain.".json");
        $converter = new Json2PHPObject();
        $configuration = $converter->convert($config_str);

        switch($domain) {
            case CheckbookDomain::$CONTRACTS:
                $status = ContractStatus::getCurrent();
                $category = ContractCategory::getCurrent();
                $dimension = "{$status}_{$category}";
                $config = $configuration->$dashboard->$dimension->landing_page_widgets->$widget ?? null;
                break;
            case CheckbookDomain::$PAYROLL:
                $dimension = PayrollLandingPage::getCurrent();
                $config = $configuration->$dashboard->$dimension->landing_page_widgets->$widget ?? null;
                break;
            case CheckbookDomain::$SPENDING:
            case CheckbookDomain::$REVENUE:
            case CheckbookDomain::$BUDGET:
            case CheckbookDomain::$NYCHA_BUDGET:
            case CheckbookDomain::$NYCHA_CONTRACTS:
            case CheckbookDomain::$NYCHA_REVENUE:
            case CheckbookDomain::$NYCHA_SPENDING:
                $config = $configuration->$dashboard->landing_page_widgets->$widget ?? null;
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
        $config = $this->_getCurrentWidgetViewConfig($widget);
        $widget_config = $config->widget_config ?? null;

        if(isset($widget_config)) {
            $visibility_parameters = $config->visibility_parameters;
            if(isset($visibility_parameters)) {

                foreach($visibility_parameters as $value) {
                    if(isset($value)) {

                        //Don't show the widget if this parameter is in the URL
                        if(str_starts_with($value, "-")) {
                            $value = ltrim($value, "-");
                            $param_value = explode(':', $value);
                            if((count($param_value) == 1 && RequestUtilities::get($param_value[0])) ||
                               (count($param_value) == 2 && $param_value[1] == RequestUtilities::get($param_value[0]))){
                                return null;
                            }
                        }
                        //Don't show the widget if this parameter greater than the set value in url
                        elseif(str_starts_with($value, "<")) {
                          //value from json file
                          $value = ltrim($value, "<");
                          $param_value = explode(':', $value);
                          $param_val_num  = (int)$param_value[1];
                          //value from url
                          $val_num = (int)RequestUtilities::get($param_value[0]);

                          //return null if url value more than or equal to visibility value
                          if($val_num  >= $param_val_num){
                            return null;
                          }
                        }
                        //Only show if URL value is more than visibility rule value
                        elseif(str_starts_with($value, ">")) {
                          //value from json file
                          $value = ltrim($value, ">");
                          $param_value = explode(':', $value);
                          $param_val_num  = (int)$param_value[1];
                          //value from url
                          $val_num = (int)RequestUtilities::get($param_value[0]);

                          //return null if url value less than or equal to visibility value
                          if($val_num  <= $param_val_num){
                            return null;
                          }
                        }
                        //Don't show the widget if this parameter is not in the URL
                        else{
                            $param_value = explode(':', $value);
                            if(!((count($param_value) == 1 && RequestUtilities::get($param_value[0])) ||
                               (count($param_value) == 2 && $param_value[1] == RequestUtilities::get($param_value[0])))) {
                              return null;
                            }
                        }

                    }
                }
            }
            return $widget_config;
        }
        return null;
    }
}
