<?php

interface IWidgetRepository {
    public function getWidgetData($parameters, $limit, $order_by);
    public function getTotalRowCount($parameters);
    public function getHeaderCount($parameters);
}
