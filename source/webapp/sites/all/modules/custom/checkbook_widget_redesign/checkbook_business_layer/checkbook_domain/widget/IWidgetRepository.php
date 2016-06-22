<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 4/8/16
 * Time: 3:14 PM
 */


interface IWidgetRepository {

    public function getWidgetData($parameters, $limit, $order_by);
    public function getTotalRowCount($parameters);
    public function getHeaderCount($parameters);
}
