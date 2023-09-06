<?php
namespace Drupal\widget_services\Revenue;

use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_project\WidgetUtilities\WidgetProcessor;

class RevenueService  {

    /**
     * Function to adjustDataTableOptions
     * @return mixed
     */
    public static function adjustDataTableOptions($node) {
      $revenuetype = RequestUtilities::get('revenuetype');
      if(isset($revenuetype) && $revenuetype == 'remaining') {
        $node->widgetConfig->dataTableOptions->aaSorting[0][0] = 3;
        return($node->widgetConfig);
      }
    }

  /**
   * Function to allow adjustParameters
   * @return mixed
   */
    public static function adjustParameters($node,$parameters)
    {
        return WidgetProcessor::_checkbook_project_applyParameterFilters($node,$parameters);
    }

}
