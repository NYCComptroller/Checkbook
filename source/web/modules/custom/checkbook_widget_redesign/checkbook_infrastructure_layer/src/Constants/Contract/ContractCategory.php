<?php

namespace Drupal\checkbook_infrastructure_layer\Constants\Contract;

use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;

abstract class ContractCategory {

    const EXPENSE = "expense";
    const REVENUE = "revenue";

  /**
   * @return string
   */
    public static function getCurrent(): string
    {
        $urlPath = RequestUtilities::getCurrentPageUrl();
        $ajaxPath = RequestUtilities::getAjaxPath();
        $category = self::EXPENSE;
      // str_contains does not return the correct category
      if(preg_match('/contracts_revenue/',$urlPath) || preg_match('/contracts_revenue/',$ajaxPath)
        || preg_match('/contracts_revenue_landing/',$urlPath) || preg_match('/contracts_revenue_landing/',$ajaxPath)
        || preg_match('/contracts_pending_rev/',$urlPath) || preg_match('/contracts_pending_rev/',$ajaxPath)) {
        $category = self::REVENUE;
      }
        return $category;
    }
}
