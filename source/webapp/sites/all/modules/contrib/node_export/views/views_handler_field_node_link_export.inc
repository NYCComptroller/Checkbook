<?php

/**
 * @file
 * The Node export views field handler.
 */

/**
 * Field handler to present a export node link.
 *
 * Closely modeled after views/modules/node/views_handler_field_node_link_edit.inc
 */
class views_handler_field_node_link_export extends views_handler_field_node_link {
  function construct() {
    parent::construct();
    $this->additional_fields['nid'] = 'nid';
  }

  function render($values) {
    // Ensure that user has access to export this node.
    $node = new stdClass();
    $node->nid = $values->{$this->aliases['nid']};
    if (!node_export_access_export($node->nid)) {
      return;
    }

    $text = !empty($this->options['text']) ? $this->options['text'] : t('Node export');
    return l($text, "node/$node->nid/node_export", array('query' => drupal_get_destination()));
  }
}
