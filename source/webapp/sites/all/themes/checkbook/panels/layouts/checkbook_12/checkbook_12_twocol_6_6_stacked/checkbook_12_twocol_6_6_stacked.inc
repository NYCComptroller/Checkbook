<?php

/**
 * Implements hook_panels_layouts().
 */
function checkbook_checkbook_12_twocol_6_6_stacked_panels_layouts() {
  $items['checkbook_12_twocol_6_6_stacked'] = array(
    'title' => t('Two Column 6-6 stacked'),
    'category' => t('Checkbook: 12 Column Grid'),
    'icon' => 'checkbook_12_twocol_6_6_stacked.png',
    'theme' => 'checkbook_12_twocol_6_6_stacked',
    'admin css' => '../checkbook_panels_admin_12.css',
    'regions' => array(
      'top' => t('Top'),
      'left' => t('Left'),
      'right' => t('Right'),
      'bottom' => t('Bottom'),
    ),
  );

  return $items;
}
