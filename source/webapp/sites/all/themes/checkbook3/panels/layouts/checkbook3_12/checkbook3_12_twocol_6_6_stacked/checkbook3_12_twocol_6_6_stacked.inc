<?php

/**
 * Implements hook_panels_layouts().
 */
function checkbook3_checkbook3_12_twocol_6_6_stacked_panels_layouts() {
  $items['checkbook3_12_twocol_6_6_stacked'] = array(
    'title' => t('Two Column 6-6 stacked'),
    'category' => t('checkbook3: 12 Column Grid'),
    'icon' => 'checkbook3_12_twocol_6_6_stacked.png',
    'theme' => 'checkbook3_12_twocol_6_6_stacked',
    'admin css' => '../checkbook3_panels_admin_12.css',
    'regions' => array(
      'top' => t('Top'),
      'left' => t('Left'),
      'right' => t('Right'),
      'bottom' => t('Bottom'),
    ),
  );

  return $items;
}
