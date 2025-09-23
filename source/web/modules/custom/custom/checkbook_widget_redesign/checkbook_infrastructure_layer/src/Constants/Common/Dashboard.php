<?php

namespace Drupal\checkbook_infrastructure_layer\Constants\Common;

use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_project\CommonUtilities\CheckbookDateUtil;

abstract class Dashboard {
    const CITYWIDE = "citywide";
    const OGE = "oge";
    const NYCHA = "nycha";
    const SUB_VENDORS = "sub_vendors";
    const SUB_VENDORS_MWBE = "sub_vendors_mwbe";
    const MWBE_SUB_VENDORS = "mwbe_sub_vendors";
    const MWBE = "mwbe";
    const CURRENT = "current_year";
    const PREVIOUS = "previous_year";

  /**
   * @return string
   */
     public static function getCurrent(): string
     {
        $domain = CheckbookDomain::getCurrent();
        $year = RequestUtilities::get(UrlParameter::YEAR);

        if($domain == CheckbookDomain::$REVENUE){
            if($year >= CheckbookDateUtil::getCurrentFiscalYearId()) {
              return self::CURRENT;
            }else {
              return self::PREVIOUS;
            }
        }else{
            $dashboard = DashboardParameter::getCurrent();
            switch($dashboard) {
                case DashboardParameter::SUB_VENDORS: return self::SUB_VENDORS;
                case DashboardParameter::SUB_VENDORS_MWBE: return self::SUB_VENDORS_MWBE;
                case DashboardParameter::MWBE_SUB_VENDORS: return self::MWBE_SUB_VENDORS;
                case DashboardParameter::MWBE: return self::MWBE;
                default:
                    if(Datasource::isOGE()) {
                      return self::OGE;
                    }else if(Datasource::isNYCHA()) {
                      return self::NYCHA;
                    }else {
                      return self::CITYWIDE;
                    }
            }
        }
     }

  /**
   * @return bool
   */
    public static function isOGE(): bool
    {
        return self::getCurrent() == self::OGE;
    }

  /**
   * @return bool
   */
    public static function isNYCHA(): bool
    {
        return self::getCurrent() == self::NYCHA;
    }

  /**
   * @return bool
   */
     public static function isMWBE(): bool
     {
        $dashboard = self::getCurrent();
        return $dashboard == self::MWBE || $dashboard == self::SUB_VENDORS_MWBE || $dashboard == self::MWBE_SUB_VENDORS;
     }

  /**
   * @return bool
   */
     public static function isSubDashboard(): bool
     {
        $dashboard = self::getCurrent();
        return $dashboard == self::SUB_VENDORS || $dashboard == self::SUB_VENDORS_MWBE || $dashboard == self::MWBE_SUB_VENDORS;
     }

  /**
   * @return bool
   */
      public static function isPrimeDashboard(): bool
      {
        $dashboard = self::getCurrent();
        return $dashboard == self::MWBE || $dashboard == self::CITYWIDE || $dashboard == self::OGE || $dashboard == self::NYCHA;
      }
}
