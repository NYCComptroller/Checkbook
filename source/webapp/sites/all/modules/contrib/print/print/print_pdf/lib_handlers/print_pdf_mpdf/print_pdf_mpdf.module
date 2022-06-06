<?php

/**
 * @file
 * Generate a PDF for the print_pdf module using the mPDF library.
 *
 * @ingroup print
 */

/**
 * Implements hook_pdf_tool_info().
 */
function print_pdf_mpdf_pdf_tool_info() {
  $info = array(
    'name' => 'mPDF',
    'min_version' => '7.1.0',
    'url' => 'https://github.com/mpdf/mpdf/releases/latest',
    'expand_css' => FALSE,
    'public_dirs' => array(
      'ttfontdata',
      'tmp',
    ),
  );

  $pdf_tool = explode('|', variable_get('print_pdf_pdf_tool', PRINT_PDF_PDF_TOOL_DEFAULT));
  // Only for versions less than 7.0.0, without calling the version() function.
  if (isset($pdf_tool[1]) && strpos($pdf_tool[1], 'mpdf.php')) {
    $info['tool_dirs'] = array(
      'graph_cache',
    );
  }
  return $info;
}

/**
 * Implements hook_pdf_tool_version().
 */
function print_pdf_mpdf_pdf_tool_version($pdf_tool) {
  if (file_exists(DRUPAL_ROOT . '/' . $pdf_tool)) {
    include_once DRUPAL_ROOT . '/' . $pdf_tool;
  }

  // Version 5.x and 6.x
  if (defined('mPDF_VERSION')) {
    return mPDF_VERSION;
  }
  // Version >= 7.0
  else {
    if (class_exists('\Mpdf\Mpdf')) {
      $version = \Mpdf\Mpdf::VERSION;
      if (!empty($version)) {
        return $version;
      }
    }
  }

  return 'unknown';
}

/**
 * Implements hook_print_pdf_available_libs_alter().
 */
function print_pdf_mpdf_print_pdf_available_libs_alter(&$pdf_tools) {
  module_load_include('inc', 'print', 'includes/print');
  $tools = _print_scan_libs('mpdf', '!^mpdf.php$!');

  foreach ($tools as $tool) {
    $pdf_tools['print_pdf_mpdf|' . $tool] = 'mPDF (' . dirname($tool) . ')';
  }

  // mPDF >= 7.0 uses a composer autoloader.
  $tools = _print_scan_libs('mpdf', '!^autoload.php$!');
  foreach ($tools as $tool) {
    if (preg_match('!mpdf.*?/vendor/autoload.php$!', $tool)) {
      $pdf_tools['print_pdf_mpdf|' . $tool] = 'mPDF (' . dirname(dirname($tool)) . ')';
    }
  }
}
