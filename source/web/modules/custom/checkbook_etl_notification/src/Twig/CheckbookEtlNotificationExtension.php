<?php

namespace Drupal\checkbook_etl_notification\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CheckbookEtlNotificationExtension extends AbstractExtension
{
  public function getFunctions()
  {
    return [
      'CheckbookEtlNotficationFooter' => new TwigFunction('CheckbookEtlNotficationFooter', [
        $this,
        'CheckbookEtlNotficationFooter',
      ])
    ];
  }

  public function CheckbookEtlNotficationFooter()
  {
    $return_val = '';
    $return_val .= '<table width="100%">';
    $return_val .= '<tr align="center">';
    $return_val .= '<td>';
    $return_val .=  "© " . date('Y') . ", Checkbook NYC<br/>";
    /*if(isset($dev_mode) && $dev_mode):
      $return_val .= "<small>";
      $etl_status_footer = \Drupal::config('check_book')->get('etl-status-footer');
      if (!empty($etl_status_footer)):
        $out = '';
        $arr = [];
        foreach ($etl_status_footer as $line) {
          foreach ($line as $text => $url):
            $arr[] = "<a target=\"_blank\" href=\"$url\">$text</a>";
          endforeach;
          $out .= join(' | ', $arr) . '<br />';
          $arr = [];
        }
        $return_val .= $out;
        endif;
      $return_val .= "</small>";
      endif;*/
    $return_val .= "</td>";
    $return_val .= "</tr>";
    $return_val .= "</table>";

    return $return_val;
  }

}
