<?php

/**
 * Implements hook_panels_layouts().
 */
function checkbook3_checkbook3_12_threecol_4_4_4_panels_layouts() {
  $items['checkbook3_12_threecol_4_4_4'] = array(
    'title' => t('Three column 4-4-4'),
    'category' => t('checkbook3: 12 Column Grid'),
    'icon' => 'checkbook3_12_threecol_4_4_4.png',
    'theme' => 'checkbook3_12_threecol_4_4_4',
    'admin css' => 'checkbook3_12_threecol_4_4_4.css',
    'regions' => array(
      'left' => t('Left side'),
      'middle' => t('Middle column'),
      'right' => t('Right side')
    ),
  );

  return $items;
}
