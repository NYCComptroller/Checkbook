<?php

/**
 * Implements hook_panels_layouts().
 */
function checkbook_checkbook_12_threecol_4_4_4_stacked_panels_layouts() {
  $items['checkbook_12_threecol_4_4_4_stacked'] = array(
    'title' => t('Three column 4-4-4 stacked'),
    'category' => t('Checkbook: 12 Column Grid'),
    'icon' => 'checkbook_12_threecol_4_4_4_stacked.png',
    'theme' => 'checkbook_12_threecol_4_4_4_stacked',
    'admin css' => '../checkbook_panels_admin_12.css',
    'regions' => array(
      'top' => t('Top'),
      'left' => t('Left side'),
      'middle' => t('Middle column'),
      'right' => t('Right side'),
      'bottom' => t('Bottom'),
    ),
  );

  return $items;
}
