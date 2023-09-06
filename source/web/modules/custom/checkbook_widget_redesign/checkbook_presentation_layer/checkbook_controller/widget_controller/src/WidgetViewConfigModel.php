<?php
namespace Drupal\widget_controller;

class WidgetViewConfigModel {

    public $widget_config;
    public $legacy_node_id;
    public $visibility_parameters;
    public $param_config;

  /**
   * WidgetViewConfigModel constructor.
   * @param $config
   */
    function __construct($config) {
        if(isset($config->legacy_node_id)) {
          $this->legacy_node_id = $config->legacy_node_id;
        }
        if(isset($config->visibility_parameters)) {
          $this->visibility_parameters = $config->visibility_parameters;
        }
        if(isset($config->widget_config)) {
          $this->widget_config = $config->widget_config;
        }
        if(isset($config->param_config)) {
          $this->param_config = $config->param_config;
        }

    }
}
