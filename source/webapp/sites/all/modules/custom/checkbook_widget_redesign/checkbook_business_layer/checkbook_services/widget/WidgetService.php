<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 12/22/16
 * Time: 12:39 PM
 */

abstract class WidgetService implements IWidgetService {

    /**
     * Legacy node id configured for backwards compatibility
     * @var
     */
    protected $legacy_node_id;
    /**
     * Param configured to support transactions
     * @var
     */
    protected $param_config;


    /**
     * Variable to hold the configuration to get widget data
     * @var
     */
    protected $widgetConfig;

    /**
     * @param $widgetConfig
     */
    function __construct($widgetConfig) {
        $this->widgetConfig = $widgetConfig;
        if(isset($this->widgetConfig->legacy_node_id)) {
            $this->legacy_node_id = $this->widgetConfig->legacy_node_id;
        }
        if(isset($this->widgetConfig->param_config)) {
            $this->param_config = $this->widgetConfig->param_config;
        }
    }

    /**
     * Returns the legacy node id
     * @return mixed
     */
    public function getLegacyNodeId() {
        return $this->legacy_node_id ?: RequestUtilities::get("legacy_node_id");
    }

    public function getParamName(){
        return $this->param_config?: RequestUtilities::get("param_name");
    }
    /**
     * Returns the widget data
     * @param $parameters
     * @param $limit
     * @param $orderBy
     * @return mixed
     */
    abstract function getWidgetData($parameters, $limit, $orderBy);

    /**
     * Returns records from specified configured datasource
     * Returns total number of records for the widget
     * @param $parameters
     * @return mixed
     */
    abstract function getWidgetDataCount($parameters);

    /**
     * Returns count for widget header using specified datasource or default row count
     * @param $parameters
     * @return mixed
     */
    abstract function getWidgetHeaderCount($parameters);

    /**
    * Function to be overridden by implementing class to apply customized formatting to the data
    * @param $column_name
    * @param $row
    * @return mixed
    */
    abstract function implementDerivedColumn($column_name,$row);

    /**
     * Function will convert the order by to a SQL clause
     * @return string
     */
    function prepareOrderBy() {
        $orderBy = "";
        if (isset($this->widgetConfig->orderBy)) {
            foreach ($this->widgetConfig->orderBy as $value) {
                if(substr($value, 0, 1 ) == "-") {
                    $value = ltrim($value, "-");
                    $orderBy .= $orderBy == "" ? "{$value} DESC" : ",{$value} DESC";
                }
                else {
                    $orderBy .= $orderBy == "" ? $value : ",{$value}";
                }
            }
        }
        return $orderBy;
    }

    /**
     * Function will populate the parameter array with values that
     * exist in both the list of validUrlParameters and the query string
     * @return array
     */
    function prepareInputParameters() {
        $parameters = array();

        if (isset($this->widgetConfig->defaultParameters)) {
            foreach ($this->widgetConfig->defaultParameters as $key => $value) {
                $values_array = explode('~',$value);
                $value = count($values_array) > 1 ? "(".implode(",", $values_array).")" : $value;
                $parameters[$key] = htmlspecialchars_decode(_replace_special_characters_decode($value));
            }
        }
        if (isset($this->widgetConfig->validUrlParameters)) {
            $urlParams = $this->widgetConfig->validUrlParameters;
            $urlPath = drupal_get_path_alias($_GET['q']);
            $pathParams = explode('/', $urlPath);
            for($i = 0; $i < count($pathParams); $i = $i + 1) {
                $key = $pathParams[$i];
                $value = $pathParams[$i+1];
                if(in_array($key,$urlParams)) {
                    $values_array = explode('~',$value);
                    $value = count($values_array) > 1 ? "(".implode(",", $values_array).")" : $value;
                    $parameters[$key] = htmlspecialchars_decode(_replace_special_characters_decode($value));
                }
            }
        }
        return $parameters;
    }
}
