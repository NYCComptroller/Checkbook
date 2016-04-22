<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 3/29/16
 * Time: 11:08 AM
 */

class NodeViewModel extends AbstractNodeViewModel {

    function __construct()
    {
        $this->widgetConfig = new WidgetConfigModel();
    }
}


abstract class AbstractNodeViewModel {
    public $widgetConfig;
    public $data;
}

