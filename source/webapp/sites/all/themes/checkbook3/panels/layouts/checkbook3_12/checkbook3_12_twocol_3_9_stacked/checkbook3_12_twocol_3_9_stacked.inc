<?php

/**
 * Implements hook_panels_layouts().
 */
function checkbook3_checkbook3_12_twocol_3_9_stacked_panels_layouts() {
  $items['checkbook3_12_twocol_3_9_stacked'] = array(
    'title' => t('Two Column 3-9 Stacked'),
    'category' => t('checkbook3: 12 Column Grid'),
    'icon' => 'checkbook3_12_twocol_3_9_stacked.png',
    'theme' => 'checkbook3_12_twocol_3_9_stacked',
    'admin css' => '../checkbook3_panels_admin_12.css',
    'regions' => array(
      'top' => t('Top'),
      'left' => t('Left side'),
      'right' => t('Right side')
    ),
  );

  return $items;
}
