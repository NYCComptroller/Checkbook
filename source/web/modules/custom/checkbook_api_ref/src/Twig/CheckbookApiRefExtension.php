<?php

namespace Drupal\checkbook_api_ref\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
class CheckbookApiRefExtension extends AbstractExtension
{
  public function getFunctions()
  {
    return [
      'CheckbookApiRefTabel' => new TwigFunction('CheckbookApiRefTabel', [
        $this,
        'CheckbookApiRefTabel',
      ]),
      'CheckbookApiRefFooter' => new TwigFunction('CheckbookApiRefFooter', [
        $this,
        'CheckbookApiRefFooter',
      ])
    ];
  }

  public function CheckbookApiRefTabel($message)
  {
    $output = '';
    if ($message['error']):
      $output .= "<tr>
              <td>❌ ".  $message['error'] ." ❌</td>
            </tr>";
    else:
      $output .= "<tr>
                <th>File</th>
                <th>Sample</th>
            </tr>";
      foreach ($message['files'] as $filename => $info):
        $output .= "<tr class=" . ((empty($c) || $c == 'even') ? ($c = 'odd') : ($c = 'even')) . "><th><div class=\"ref-filename\">";
      $output .= $filename . ".csv"."</div><small>";
      $output .=($info['error'] ? '❌' : '');
      if ($info['old_timestamp']):
        $output .= "<br /> Old file: " . $info['old_timestamp'];
      endif;
      if ($info['old_filesize']):
        $output .= "<br /> Old filesize(bytes): " . $info['old_filesize'];
      endif;
      if ($info['new_timestamp'] && ($info['old_timestamp'] !== $info['new_timestamp'])):
        $output .= "<br /> New file: " . $info['new_timestamp'];
      endif;
      if ($info['new_filesize']):
        $output .= "<br /> New filesize(bytes): " . $info['new_filesize'];
      endif;
      $output .= "<br />Updated: " . ($info['updated'] ? '✅' : 'No') . "</small></th><td>";
      if ($info['error']):
        $output .= "⛔".$info['error'] . "⛔<br />";
      endif;
      //if ($info['warning']):
      //  $output .= "☢".$info['warning'] . " ☢<br />";
      //endif;
      if ($info['info']):
        $output .= "⚡".$info['info'] . " ⚡<br />";
      endif;
      if ($info['sample']):
        $headers = array_keys($info['sample'][0]);
        $output .= "<table class=\"sample-data\"><tr>";
        foreach ($headers as $header):
          $output .= "<th>" . $header . "</th>";
        endforeach;
        $output .= "</tr>";
        foreach ($info['sample'] as $row):
          $output .= "<tr>";
          foreach ($row as $cell):
            $output .= "<td>".htmlentities($cell)."</td>";
          endforeach;
          $output .= "</tr>";
        endforeach;
        $output .= "</table>";
      endif;

      $output .= "</td></tr>";
      endforeach;
    endif;

    return $output;
  }

  public function CheckbookApiRefFooter()
  {
    $return_val = '';
    $return_val .= '<table width="100%">';
    $return_val .= '<tr align="center">';
    $return_val .= '<td>';
    $return_val .=  "© " . date('Y') . ", Checkbook NYC<br/>";
    $return_val .= "</td>";
    $return_val .= "</tr>";
    $return_val .= "</table>";
    return $return_val;
  }

}
