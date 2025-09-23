<?php

namespace Drupal\landing_page_widget_view\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LandingPageExtension extends AbstractExtension {
  public function getFunctions() {
    return [
      'landing_page_widget_view_add_js_twig' => new TwigFunction('landing_page_widget_view_add_js_twig', [$this, 'landing_page_widget_view_add_js_twig',])
    ];
  }

  public function landing_page_widget_view_add_js_twig($node) {
    print landing_page_widget_view_add_js($node);
  }

}
