<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 3/29/16
 * Time: 11:08 AM
 */

class WidgetConfigModel extends AbstractWidgetConfigModel {
}

abstract class AbstractWidgetConfigModel {
    public $keepOriginalDatasource;
    public $noDataInitialLoad;
}