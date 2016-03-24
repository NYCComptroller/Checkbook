<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 3/24/16
 * Time: 2:32 PM
 */

abstract class WidgetView {

    protected $configKey;
    protected $type;
    protected $json;
    protected $jsFunctions;
    public $config;
    public $data;
    public $totalDataCount;

    function __construct($configKey)
    {
        //load config
        $this->configKey = $configKey;
        $this::loadJson();
        $this->config = new ViewConfig($this->json);

        //Some dummy values
        $this->totalDataCount = 100;
    }

    function prepare() {

        foreach ($_REQUEST as $key => $val) {
            $_REQUEST[$key] = $val;
        }

        if (isset($_REQUEST['data'])) {
            $this->config->dataOnly = TRUE;
        }

        //Initialize request parameters
        $urlPath = drupal_get_path_alias($_GET['q']);
        $pathParams = explode('/', $urlPath);
        for($i = 0; $i < count($pathParams); $i = $i + 1) {
            $key = $pathParams[$i];
            $value = $pathParams[$i + 1];
            $this->config->requestParameters[$key] = htmlspecialchars_decode($value);
        }

        //Apply default parameters
        if (isset($this->config->defaultParameters)) {
            foreach ($this->config->defaultParameters as $key => $value) {
                if (!isset($this->config->requestParameters[$key])) {
                    $this->config->requestParameters[$key] = $value;
                }
            }
        }

        //View Specific
        $this->viewPrepare();
    }

    function getData() {
        //View Specific
        $this->viewGetData();
    }

    function getHeader() {
        $header = $this->config->headerTitle;

        $count = $this->totalDataCount > 4 ? "<span class=\"hideOnExpand\">5 </span>" : "";
        $header = "<div class=\"tableHeader\"><h2>Top {$count} {$header}</h2><span class=\"contCount\"> Number of {$header}: {number_format($this->totalDataCount)}</span></div>";
        return $header;
    }

    function getFooter() {
        $footerUrl = $this->config->footerUrl;
        $footer = $this->totalDataCount > 0
            ? "<a class=\"show-details bottomContainerReload\" href=\"{$footerUrl}\">Details >></a>"
            : "<a class=\"show-details bottomContainerReload\" href=\"{$footerUrl}\" style=\"display:none;\">Details >></a>";
        return $footer;
    }

    abstract function viewPrepare();
    abstract function viewGetData();
    abstract function viewDisplay();

    protected function getUrlFromRequest()
    {
        $url = "";
        if(is_array($this->config->requestParameters))  {
            foreach($this->config->requestParameters as $key=>$value) {
                $url .= "/$key/$value" ;
            }
        }
        $url .= _checkbook_append_url_params();
        return $url;
    }

    /**
     * Function will scan the checkbook_project directory to load the json configuration for the widget view
     */
    private function loadJson() {
        $this->type = "widget";
        $files = file_scan_directory( drupal_get_path('module','checkbook_project') , '/^'.$this->configKey.'\.json$/');
        if(count($files) > 0){
            $file_names = array_keys($files);
            $json = file_get_contents($file_names[0]);
            $this->json =  $json;
        }
        $FUNCTION_START = '<function>';
        $FUNCTION_END = '</function>';
        $FUNCTION_DELIMITER = '##';
        $FUNCTION_NAME_DELIMITER = '^^';

        $functionStart = strpos($this->json,$FUNCTION_START);
        if ($functionStart != false) {
            $functionEnd = strpos($this->json, $FUNCTION_END);
            $functions = substr($this->json, $functionStart+ drupal_strlen($FUNCTION_START), $functionEnd - $functionStart - drupal_strlen($FUNCTION_START));
            $funcList = explode($FUNCTION_DELIMITER, $functions);
            $functionMap = array();
            foreach ($funcList as $jsFunction) {
                $keyVal = explode($FUNCTION_NAME_DELIMITER, $jsFunction);
                $functionMap[str_replace("\r\n", "", $keyVal[0])] = $keyVal[1];
            }
            unset($jsFunction);
            $this->jsFunctions = $functionMap;
            $this->json = str_replace($FUNCTION_START.$functions.$FUNCTION_END, '', $this->json);
        }
    }
}

class ViewConfig extends JsonConvertible {
    public $uid;
    public $requestParameters;
    public $defaultParameters;
    public $widgetType;
    public $widgetSubType;
    public $headerTitle;
    public $footerUrl;
    public $dataService;
    public $dataServiceFunction;
    public $widgetTitleEval;
    public $dataOnly;

    function __construct($json)
    {
        $data = json_decode($json, true);
        foreach ($data as $key => $value)
            $this->{$key} = $value;
    }
}

abstract class JsonConvertible {
    static function fromJson($json) {
        $result = new static();
        $objJson = json_decode($json);
        $class = new \ReflectionClass($result);
        $publicProps = $class->getProperties(\ReflectionProperty::IS_PUBLIC);
        foreach ($publicProps as $prop) {
            $propName = $prop->name;
            if (isset($objJson->$propName)) {
                $prop->setValue($result, $objJson->$propName);
            }
            else {
                $prop->setValue($result, null);
            }
        }
        return $result;
    }

    function toJson() {
        return json_encode($this);
    }
}

interface IWidgetView {

}