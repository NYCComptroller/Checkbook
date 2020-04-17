<?php


class NychaBudgetUrlService {

    static function getFooterUrl($parameters = null) {
        $url = '/panel_html/nycha_budget_transactions/nycha_budget/transactions'
            .RequestUtilities::buildUrlFromParam('year')
            .RequestUtilities::buildUrlFromParam('expcategory');
        return $url;
    }
}
