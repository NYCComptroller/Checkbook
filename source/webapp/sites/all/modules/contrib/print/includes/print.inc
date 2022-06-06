<?php

/**
 * @file
 * Common functions used by several of the print modules.
 *
 * @ingroup print
 */

/**
 * Auxiliary function to scan all module directories for a given library.
 *
 * @param string $lib
 *   The machine name of a library to return the path for.
 * @param string $mask
 *   The preg_match() regular expression of the files to find.
 *
 * @return array
 *   An array of the filenames matching the provided mask.
 */
function _print_scan_libs($lib, $mask) {
  $tools = array_keys(file_scan_directory(drupal_get_path('module', 'print'), $mask));
  $tools = array_merge($tools, array_keys(file_scan_directory(PRINT_LIB_PATH, $mask)));
  if (module_exists('libraries')) {
    $tools = array_merge($tools, array_keys(file_scan_directory(libraries_get_path($lib), $mask)));
  }

  return array_unique($tools);
}

/**
 * Callback function for the preg_replace_callback replacing spaces with %20.
 *
 * Replace spaces in URLs with %20
 *
 * @param array $matches
 *   Array with the matched tag patterns, usually <a...>+text+</a>.
 *
 * @return string
 *   tag with re-written URL
 */
function _print_replace_spaces($matches) {
  // Split the html into the different tag attributes.
  $pattern = '!\s*(\w+\s*=\s*"(?:\\\"|[^"])*")\s*|\s*(\w+\s*=\s*\'(?:\\\\\'|[^\'])*\')\s*|\s*(\w+\s*=\s*\w+)\s*|\s+!';
  $attribs = preg_split($pattern, $matches[1], -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
  foreach ($attribs as $key => $value) {
    $attribs[$key] = preg_replace('!(\w)\s*=\s*(.*)!', '$1=$2', $value);
  }

  $size = count($attribs);
  for ($i = 1; $i < $size; $i++) {
    // If the attribute is href or src, we need to rewrite the URL in the value.
    if (preg_match('!^(?:href|src)\s*?=(.*)!i', $attribs[$i], $urls) > 0) {
      $url = trim($urls[1], " \t\n\r\0\x0B\"'");
      $new_url = str_replace(' ', '%20', $url);
      $matches[1] = str_replace($url, $new_url, $matches[1]);
    }
  }

  $ret = '<' . $matches[1] . '>';
  if (count($matches) == 4) {
    $ret .= $matches[2] . $matches[3];
  }

  return $ret;
}

/**
 * Convert image paths to the file:// protocol.
 *
 * In some Drupal setups, the use of the 'private' filesystem or Apache's
 * configuration prevent access to the images of the page. This function
 * tries to circumnvent those problems by accessing files in the local
 * filesystem.
 *
 * @param string $html
 *   Contents of the post-processed template already with the node data.
 * @param bool $images_via_file
 *   If TRUE, convert also files in the 'public' filesystem to local paths.
 *
 * @return string
 *   converted file names
 */
function _print_access_images_via_file($html, $images_via_file) {
  global $base_url, $language;

  $dom = new DOMDocument();
  // Suppress warnings for invalid HTML.
  @$dom->loadHTML($html);

  $image_tags = $dom->getElementsByTagName('img');
  foreach ($image_tags as $image_tag) {
    $image_relative_tag_src = str_replace($base_url, '', $image_tag->getAttribute('src'));
    $file_private_path = variable_get('file_private_path', '');
    $is_private_file = (strpos($image_relative_tag_src, '/system/files') === 0);

    if ($is_private_file) {
      // Reconstruct private file URI.
      $image_uri = str_replace('/system/files/', 'private://', $image_relative_tag_src);

      // The URI to check access against.
      $check_uri = $image_uri;

      if (strpos($image_uri, 'private://styles') === 0) {
        // This is a private image derivative. Find the original image to check
        // access.
        $style_parts = explode('/', file_uri_target($image_uri));
        // We strip the same args as image_style_deliver().
        $style_parts = array_slice($style_parts, 3);
        $original_target = implode('/', $style_parts);
        // Update the URI to check access to the original image.
        $check_uri = preg_replace('/\?itok=.*$/', '', 'private://' . $original_target);
      }

      if (file_download_headers($check_uri)) {
        // Access granted to check URI. Find the real path of the file.
        $image_src = drupal_realpath($image_uri);
        $image_tag->setAttribute('src', 'file://' . $image_src);
      }
      else {
        // Access to this private file is denied.
        $image_tag->setAttribute('src', '');
      }
    }
    elseif ($images_via_file) {
      // Reconstruct public file URL.
      $public_directory = variable_get('file_public_path', conf_path() . '/files');
      if (strpos($image_relative_tag_src, '/' . $public_directory) === 0) {
        $image_src = str_replace('/' . $public_directory, 'public://', $image_relative_tag_src);
        // Prevent directory traversal.
        $image_src = drupal_realpath($image_src);
        $image_tag->setAttribute('src', 'file://' . $image_src);
      }
    }
  }

  return $dom->saveHTML();
}
