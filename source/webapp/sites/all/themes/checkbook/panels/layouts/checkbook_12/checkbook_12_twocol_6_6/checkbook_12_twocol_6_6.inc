<?php

/**
 * Implements hook_panels_layouts().
 */
function checkbook_checkbook_12_twocol_6_6_panels_layouts() {
  $items['checkbook_12_twocol_6_6'] = array(
    'title' => t('Two Column 6-6'),
    'category' => t('Checkbook: 12 Column Grid'),
    'icon' => 'checkbook_12_twocol_6_6.png',
    'theme' => 'checkbook_12_twocol_6_6',
    'admin css' => '../checkbook_panels_admin_12.css',
    'regions' => array(
      'left' => t('Left side'),
      'right' => t('Right side')
    ),
  );

  return $items;
}
