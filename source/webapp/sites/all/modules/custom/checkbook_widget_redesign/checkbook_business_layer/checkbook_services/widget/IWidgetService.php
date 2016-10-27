<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 4/8/16
 * Time: 3:07 PM
 */

interface IWidgetService {
    public function getLegacyNodeId();
    public function getWidgetData($parameters, $limit, $order_by);
    public function getWidgetDataCount($parameters);
    public function getWidgetHeaderCount($parameters);
    public function implementDerivedColumns($data);
    public function adjustParameters($parameters, $urlPath);
    public function getWidgetFooterUrl($parameters);
} 