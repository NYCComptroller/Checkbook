<?php

namespace Drupal\widget_config\Twig\Trends;


use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CommonTrendsConfigExtension extends AbstractExtension
{
  public function getFunctions()
  {
    return [
      'addJs' => new TwigFunction('addJs', [
        $this,
        'addJs',
      ]),
      'addCaption' => new TwigFunction('addCaption', [
        $this,
        'addCaption',
      ])
    ];
  }

  public static function addJs($node){
    return widget_data_tables_add_js($node);
  }

  public static function addCaption($node)
  {
    if (isset($node->widgetConfig->caption_column)) {
      echo '<caption>' . $node->data[0][$node->widgetConfig->caption_column] . '</caption>';
    } else
      if (isset($node->widgetConfig->caption)) {
        echo '<caption>' . $node->widgetConfig->caption . '</caption>';
      }
  }
}
