<?php

/**
 * Implements hook_panels_layouts().
 */
function checkbook_checkbook_12_twocol_10_2_stacked_panels_layouts() {
  $items['checkbook_12_twocol_10_2_stacked'] = array(
    'title' => t('Two Column 10-2 Stacked'),
    'category' => t('Checkbook: 12 Column Grid'),
    'icon' => 'checkbook_12_twocol_10_2_stacked.png',
    'theme' => 'checkbook-12-twocol-10-2-stacked',
    'admin css' => '../checkbook_panels_admin_12.css',
    'regions' => array(
      'top' => t('Top'),
      'left' => t('Left side'),
      'right' => t('Right side')
    ),
  );

  return $items;
}
