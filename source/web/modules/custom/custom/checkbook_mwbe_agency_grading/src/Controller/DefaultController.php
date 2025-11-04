<?php /**
 * @file
 * Contains \Drupal\checkbook_mwbe_agency_grading\Controller\DefaultController.
 */

namespace Drupal\checkbook_mwbe_agency_grading\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Render\Markup;

/**
 * Default controller for the checkbook_mwbe_agency_grading module.
 */
class DefaultController extends ControllerBase {

  public function _checkbook_mwbe_agency_grading($filters) {
    $params['data_type'] = _checkbook_mwbe_agency_grading_param_update();
    $agencies_data = _checkbook_mwbe_agency_grading_getdata();
    $left_content = _checkbook_mwbe_agency_grading_left($agencies_data);
    $nyc_data = _checkbook_mwbe_agency_grading_right($agencies_data);

    return [
      '#theme' => 'mwbe_agency_grading_main',
      '#left_content' => $left_content,
      '#nyc_data' => $nyc_data,
      '#params' => $params,
      '#attached' => [
        'html_head' => [
          
        ]
      ]
    ];
  }

  public function _checkbook_mwbe_agency_grading_csv($filters) {

    $params['data_type'] = _checkbook_mwbe_agency_grading_param_update();
    $agencies_data = _checkbook_mwbe_agency_grading_getdata();
    $data = _mwbe_agency_grading_current_csv_header();
    $mwbe_cats = _mwbe_agency_grading_current_cats();

    foreach ($agencies_data as $row) {
      $total = 0;
      $csv_data = $row['agency_name'] . ",";
      foreach ($mwbe_cats as $mwbe_cat) {
        $csv_data .= $row[$mwbe_cat] . ",";
        $total += $row[$mwbe_cat];
      }
      $csv_data .= $row['total_mwbe'] . "\n";
      if ($total > 0) {
        $data .= $csv_data;
      }
    }

    $response = new Response();
    $response->headers->set('Content-Type','text/csv');
    $response->headers->set('Content-Disposition','attachment; filename=TransactionsData.csv');
    $response->headers->set('Content-Length',strlen($data));
    $response->headers->set('Expires','cache');
    $response->headers->set('Expires','-1');

    $response->setContent($data);

    return $response;
  }

}
