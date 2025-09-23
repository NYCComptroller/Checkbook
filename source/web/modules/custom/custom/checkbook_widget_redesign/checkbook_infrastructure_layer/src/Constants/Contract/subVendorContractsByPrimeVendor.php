<?php

namespace Drupal\checkbook_infrastructure_layer\Constants\Contract;

use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;

abstract class subVendorContractsByPrimeVendor {

    const EXPENSE = "expense";

    public static function getCurrent() {
        //$urlPath = \Drupal::request()->query->get('q');
        //$ajaxPath = \Drupal::request()->server->get('HTTP_REFERER');
        $urlPath = RequestUtilities::getCurrentPageUrl();
        $ajaxPath = RequestUtilities::getAjaxPath();
        $category = NULL;

        if((str_contains($urlPath, 'contracts_landing') || str_contains('/contracts_landing/',$ajaxPath)) &&
            (str_contains('/bottom_slider/',$urlPath) || str_contains('/bottom_slider/',$ajaxPath))) {
            $category = self::EXPENSE;
        }

        return $category;
    }
}
