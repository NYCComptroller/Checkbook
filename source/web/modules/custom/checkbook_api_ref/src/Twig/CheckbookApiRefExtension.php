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
    if ($message['error']):
      echo "<tr>
              <td>❌ ".  $message['error'] ." ❌</td>
            </tr>";
    else:
      echo "<tr>
                <th>File</th>
                <th>Sample</th>
            </tr>";
      foreach ($message['files'] as $filename => $info):
        echo "<tr class=" . ((empty($c) || $c == 'even') ? ($c = 'odd') : ($c = 'even')) . "><th><div class=\"ref-filename\">";
      echo $filename . ".csv"."</div><small>";
      echo($info['error'] ? '❌' : '');
      if ($info['old_timestamp']):
        echo "<br /> Old file: " . $info['old_timestamp'];
      endif;
      if ($info['old_filesize']):
        echo "<br /> Old filesize(bytes): " . $info['old_filesize'];
      endif;
      if ($info['new_timestamp'] && ($info['old_timestamp'] !== $info['new_timestamp'])):
        echo "<br /> New file: " . $info['new_timestamp'];
      endif;
      if ($info['new_filesize']):
        echo "<br /> New filesize(bytes): " . $info['new_filesize'];
      endif;
      echo "<br />Updated: " . ($info['updated'] ? '✅' : 'No') . "</small></th><td>";
      if ($info['error']):
        echo "⛔".$info['error'] . "⛔<br />";
      endif;
      //if ($info['warning']):
      //  echo "☢".$info['warning'] . " ☢<br />";
      //endif;
      if ($info['info']):
        echo "⚡".$info['info'] . " ⚡<br />";
      endif;
      if ($info['sample']):
        $headers = array_keys($info['sample'][0]);
        echo "<table class=\"sample-data\"><tr>";
        foreach ($headers as $header):
          echo "<th>" . $header . "</th>";
        endforeach;
        echo "</tr>";
        foreach ($info['sample'] as $row):
          echo "<tr>";
          foreach ($row as $cell):
            echo "<td>".htmlentities($cell)."</td>";
          endforeach;
          echo "</tr>";
        endforeach;
        echo "</table>";
      endif;

      echo "</td></tr>";
      endforeach;
    endif;

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
