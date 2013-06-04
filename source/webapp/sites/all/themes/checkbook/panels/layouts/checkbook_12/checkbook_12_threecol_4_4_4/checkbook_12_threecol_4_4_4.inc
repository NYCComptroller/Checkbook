<?php

/**
 * Implements hook_panels_layouts().
 */
function checkbook_checkbook_12_threecol_4_4_4_panels_layouts() {
  $items['checkbook_12_threecol_4_4_4'] = array(
    'title' => t('Three column 4-4-4'),
    'category' => t('Checkbook: 12 Column Grid'),
    'icon' => 'checkbook_12_threecol_4_4_4.png',
    'theme' => 'checkbook_12_threecol_4_4_4',
    'admin css' => 'checkbook_12_threecol_4_4_4.css',
    'regions' => array(
      'left' => t('Left side'),
      'middle' => t('Middle column'),
      'right' => t('Right side')
    ),
  );

  return $items;
}
