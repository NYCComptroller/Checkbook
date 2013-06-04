<?php

/**
 * Implements hook_panels_layouts().
 */
function checkbook_checkbook_12_twocol_3_9_stacked_panels_layouts() {
  $items['checkbook_12_twocol_3_9_stacked'] = array(
    'title' => t('Two Column 3-9 Stacked'),
    'category' => t('Checkbook: 12 Column Grid'),
    'icon' => 'checkbook_12_twocol_3_9_stacked.png',
    'theme' => 'checkbook_12_twocol_3_9_stacked',
    'admin css' => '../checkbook_panels_admin_12.css',
    'regions' => array(
      'top' => t('Top'),
      'left' => t('Left side'),
      'right' => t('Right side')
    ),
  );

  return $items;
}
