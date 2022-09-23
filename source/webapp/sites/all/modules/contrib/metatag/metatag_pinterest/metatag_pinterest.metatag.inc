<?php

/**
 * @file
 * Metatag integration for the Metatag:Pinterest module.
 */

/**
 * Implements hook_metatag_info().
 */
function metatag_pinterest_metatag_info() {
  $info['groups']['pinterest'] = array(
    'label' => t('Pinterest'),
    'description' => t("A set of meta tags used to control how the site's content is consumed by <a href='https://pinterest.com/'>Pinterest</a>."),
    'form' => array(
      '#weight' => 56,
    ),
  );

  // Pinterest meta tags stack later.
  $weight = 30;

  // Default values for each meta tag.
  $tag_info_defaults = array(
    'description' => '',
    'class' => 'DrupalTextMetaTag',
    'group' => 'pinterest',
  );

  $info['tags']['pin:id'] = array(
    'label' => t('Pinterest ID'),
    'description' => t('The Canonical Pinterest object to pin.'),
    'weight' => ++$weight,
    'devel_generate' => array(
      'type' => 'integer',
    ),
  ) + $tag_info_defaults;

  $info['tags']['pin:description'] = array(
    'label' => t('Description'),
    'description' => t('A one to two sentence description of the content.'),
    'weight' => ++$weight,
  ) + $tag_info_defaults;

  $info['tags']['pin:media'] = array(
    'label' => t(''),
    'description' => t('The URL of media which should represent the content.'),
    'weight' => ++$weight,
  ) + $tag_info_defaults;

  $info['tags']['pin:url'] = array(
    'label' => t('URL'),
    'description' => t('The URL which should represent the content.'),
    'weight' => ++$weight,
  ) + $tag_info_defaults;

  $info['tags']['pinterest'] = array(
    'label' => t('Pinterest'),
    'description' => t("These control some advanced options for content sharing."),
    'class' => 'DrupalListMetaTag',
    'weight' => ++$weight,
    'form' => array(
      '#options' => array(
        'nohover' => t('nohover - prevents the <em>pin</em> hover button from appearing on content, but doesn\'t stop the content itself from being "pinned" by a Pinterest user.'),
        'nopin' => t('nopin - prevents the page from being "pinned" by a Pinterest user.'),
        'nosearch' => t('nosearch - prevents the page from showing on Pinterest\'s search, but doesn\'t shop it from show on someone\'s Pinterest page.'),
      ),
    ),
  ) + $tag_info_defaults;

  return $info;
}
