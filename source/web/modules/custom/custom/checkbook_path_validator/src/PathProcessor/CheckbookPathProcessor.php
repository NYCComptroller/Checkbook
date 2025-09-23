<?php

namespace Drupal\checkbook_path_validator\PathProcessor;

use Drupal\Core\PathProcessor\InboundPathProcessorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CheckbookPathProcessor implements InboundPathProcessorInterface {

  /**
   * @param $path
   * @param Request $request
   *
   * @return string
   */
  public function processInbound($path, Request $request): string {
    // Get config.
    $config = \Drupal::config('checkbook_path_validator.settings')->get();

    if (!empty($config['status'])) {
      static $validated = FALSE;

      if (!$validated) {
        // Get elements.
        $path_elements = explode('/', trim($path, '/'));
        $query = $request->query->all();

        // Check all path elements.
        foreach ($path_elements as $path_key => $path_value) {
          foreach ($config['items'] as $config_value) {
            // Check path name.
            if ($config_value['parameter'] == $path_value) {
              // Get pattern form config.
              $pattern = $config['regex_delimiter'] . $config_value['regex'] . $config['regex_delimiter'];

              // Check path value.
              if (!preg_match($pattern, $path_elements[$path_key + 1] ?? '')) {
                // Return 404.
                throw new NotFoundHttpException();
              }
            }
          }
        }

        // Check all query elements.
        foreach ($query as $query_key => $query_value) {
          foreach ($config['items'] as $config_value) {
            // Check query name.
            if ($config_value['parameter'] == $query_key) {
              // Get pattern form config.
              $pattern = $config['regex_delimiter'] . $config_value['regex'] . $config['regex_delimiter'];

              // Check query value.
              if (!preg_match($pattern, $query_value)) {
                // Return 404.
                throw new NotFoundHttpException();
              }
            }
          }
        }
      }

      $validated = TRUE;
    }

    return $path;
  }

}
