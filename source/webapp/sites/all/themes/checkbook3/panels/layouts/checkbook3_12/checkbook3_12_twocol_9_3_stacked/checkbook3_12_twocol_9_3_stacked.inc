<?php

/**
 * Implements hook_panels_layouts().
 */
function checkbook3_checkbook3_12_twocol_9_3_stacked_panels_layouts() {
  $items['checkbook3_12_twocol_9_3_stacked'] = array(
    'title' => t('Two Column 9-3 Stacked'),
    'category' => t('checkbook3: 12 Column Grid'),
    'icon' => 'checkbook3_12_twocol_9_3_stacked.png',
    'theme' => 'checkbook3_12_twocol_9_3_stacked',
    'admin css' => '../checkbook3_panels_admin_12.css',
    'regions' => array(
      'top' => t('Top'),
      'left' => t('Left side'),
      'right' => t('Right side')
    ),
  );

  return $items;
}
