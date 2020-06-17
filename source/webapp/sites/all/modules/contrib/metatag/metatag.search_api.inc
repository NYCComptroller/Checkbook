<?php

/**
 * @file
 * Contains MetatagSearchAlterCallback.
 */

/**
 * Implements hook_search_api_alter_callback_info().
 */
function metatag_search_api_alter_callback_info() {
  return array(
    'search_api_metatag_alter_callback' => array(
      'name' => t('Meta tags'),
      'description' => t("Adds the item's meta tags to the indexed data."),
      'class' => 'MetatagSearchAlterCallback',
    ),
  );
}

/**
 * Only add the class if Search API is installed.
 */
if (class_exists('SearchApiAbstractAlterCallback')) {

  /**
   * Adds meta tag values to the indexed items.
   */
  class MetatagSearchAlterCallback extends SearchApiAbstractAlterCallback {

    /**
     * {@inheritdoc}
     */
    public function supportsIndex(SearchApiIndex $index) {
      // Check for multiple datasources.
      if (isset($index->item_type) && $index->item_type == 'multiple') {
        // Verify that atleast one datasource type is entity.
        foreach ($index->options['datasource']['types'] as $datasource_type) {
          if (!is_null(entity_get_info($datasource_type))) {
            return TRUE;
          }
        }
      }
      else {
        return (bool) $index->getEntityType();
      }
    }

    /**
     * {@inheritdoc}
     */
    public function alterItems(array &$items) {
      $tags = metatag_get_info('tags');
      if (isset($this->index->item_type) && $this->index->item_type == 'multiple') {
        foreach ($items as $id => $item) {
          $item_type = $item->item_type;
          if (isset($item->{$item_type})) {
            $entity_item = $item->{$item_type};
            if (isset($entity_item->metatags)) {
              foreach (array_keys($tags) as $tag) {
                $items[$id]->{'metatag_' . $tag} = NULL;
                if (isset($entity_item->language) && isset($entity_item->metatags[$entity_item->language][$tag])) {
                  $instance = metatag_get_instance($tag, $entity_item->metatags[$entity_item->language][$tag]);
                  $items[$id]->{'metatag_' . $tag} = $instance->getValue(array('token data' => array($item_type => $entity_item)));
                }
              }
            }
          }
        }
      }
      else {
        $entity_type = $this->index->getEntityType();
        foreach ($items as $id => $item) {
          if (isset($item->metatags)) {
            foreach (array_keys($tags) as $tag) {
              $items[$id]->{'metatag_' . $tag} = NULL;
              if (isset($item->language) && isset($item->metatags[$item->language][$tag])) {
                $instance = metatag_get_instance($tag, $item->metatags[$item->language][$tag]);
                $items[$id]->{'metatag_' . $tag} = $instance->getValue(array('token data' => array($entity_type => $item)));
              }
            }
          }
        }
      }
    }

    /**
     * {@inheritdoc}
     */
    public function propertyInfo() {
      $properties = array();

      // Get available meta tags.
      $tags = metatag_get_info('tags');
      foreach ($tags as $id => $tag) {
        switch ($tag['class']) {
          case 'DrupalLinkMetaTag':
            $type = 'uri';
            break;

          default:
            $type = 'text';
            break;
        }
        $properties['metatag_' . $id] = array(
          'label' => t('Meta tag: @label', array('@label' => $tag['label'])),
          'description' => t('@label meta tag attached to an item.', array('@label' => $tag['label'])),
          'type' => $type,
        );
      }

      return $properties;
    }

  }
}
