<?php


class NychaBudgetUrlService {

    static function getFooterUrl($parameters = null) {
        // = '/panel_html/nycha_budget_transactions/nycha_budget/transactions'
          //  .RequestUtilities::buildUrlFromParam('year')
            //.RequestUtilities::buildUrlFromParam('expcategory');
        //return $url;

      $url = "/panel_html/nycha_budget_transactions/nycha_budget/transactions"
        . RequestUtilities::buildUrlFromParam('year')
        . RequestUtilities::buildUrlFromParam('fundsrc')
        . RequestUtilities::buildUrlFromParam('datasource');
      return $url;
    }

}
