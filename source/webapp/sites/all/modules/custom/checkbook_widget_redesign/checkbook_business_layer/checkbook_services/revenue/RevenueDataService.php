<?php

class RevenueDataService extends AbstractDataService {

    const sqlConfigPath = "revenue/revenue";

    function __construct() {
        parent::__construct(self::sqlConfigPath);
    }

} 