<?php

namespace Drupal\widget_controller\PathProcessor;

use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\Core\PathProcessor\InboundPathProcessorInterface;
use Symfony\Component\HttpFoundation\Request;

class WidgetControllerPathProcessor implements InboundPathProcessorInterface
{
  private const Separator =':';

  /**
   * @param $path
   * @param Request $request
   * @return string
   */
  public function processInbound($path, Request $request): string {
    if (str_starts_with($path, '/widget_controller/')) {
      $urlParams = preg_replace('|^\/widget_controller\/|', '', $path);
      $urlParams = RequestUtilities::replaceSlash($urlParams);
      return "/widget_controller/$urlParams";
    }
    elseif (str_starts_with($path, '/checkbook_views/data_tables/ajax_data/node/')) {
      $urlParams = preg_replace('|^\/checkbook_views/data_tables/ajax_data/node\/|', '', $path);
      $urlParams = RequestUtilities::replaceSlash($urlParams);
      return "/checkbook_views/data_tables/ajax_data/node/$urlParams";
    }
    elseif (str_starts_with($path, '/widget/')) {
      $urlParams = preg_replace('|^\/widget\/|', '', $path);
      $urlParams = RequestUtilities::replaceSlash($urlParams);
      return "/widget/$urlParams";
    }
    elseif(str_starts_with($path, '/mwbe_agency_grading/prime_vendor_data/year/')){
      $urlParams = preg_replace('|^\/mwbe_agency_grading/prime_vendor_data/year\/|', '', $path);
      $urlParams = RequestUtilities::replaceSlash($urlParams);
      return "/mwbe_agency_grading/prime_vendor_data/year/$urlParams";
    }
    elseif(str_starts_with($path, '/mwbe_agency_grading/sub_vendor_data/year/')){
      $urlParams = preg_replace('|^\/mwbe_agency_grading/sub_vendor_data/year\/|', '', $path);
      $urlParams = RequestUtilities::replaceSlash($urlParams);
      return "/mwbe_agency_grading/sub_vendor_data/year/$urlParams";
    }
    elseif(str_starts_with($path, '/mwbe_agency_grading_csv/')){
      $urlParams = preg_replace('|^\/mwbe_agency_grading_csv\/|', '', $path);
      $urlParams = RequestUtilities::replaceSlash($urlParams);
      return "/mwbe_agency_grading_csv/$urlParams";
    }
    elseif (str_starts_with($path, '/faceted-search/ajax/widget/')) {
      $urlParams = preg_replace('|^\/faceted-search/ajax/widget\/|', '', $path);
      $urlParams = RequestUtilities::replaceSlash($urlParams);
      return "/faceted-search/ajax/widget/$urlParams";
    }
    elseif (str_starts_with($path, '/faceted-search/ajax/autocomplete/node/')) {
      $urlParams = preg_replace('|^\/faceted-search/ajax/autocomplete/node\/|', '', $path);
      $urlParams = RequestUtilities::replaceSlash($urlParams);
      return "/faceted-search/ajax/autocomplete/node/$urlParams";
    }
    elseif(str_starts_with($path, '/dashboard_platform/data_tables/ajax_data/node/')){
      $urlParams = preg_replace('|^\/dashboard_platform/data_tables/ajax_data/node\/|', '', $path);
      $urlParams = RequestUtilities::replaceSlash($urlParams);
      return "/dashboard_platform/data_tables/ajax_data/node/$urlParams";
    }
    elseif(str_starts_with($path, '/dashboard_platform/data_tables_list/ajax_data/node/')){
      $urlParams = preg_replace('|^\/dashboard_platform/data_tables_list/ajax_data/node\/|', '', $path);
      $urlParams = RequestUtilities::replaceSlash($urlParams);
      return "/dashboard_platform/data_tables_list/ajax_data/node/$urlParams";
    }
    return $path;
  }
}
