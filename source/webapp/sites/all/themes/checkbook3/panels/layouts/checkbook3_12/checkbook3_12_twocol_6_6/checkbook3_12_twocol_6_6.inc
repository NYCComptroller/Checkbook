<?php

/**
 * Implements hook_panels_layouts().
 */
function checkbook3_checkbook3_12_twocol_6_6_panels_layouts() {
  $items['checkbook3_12_twocol_6_6'] = array(
    'title' => t('Two Column 6-6'),
    'category' => t('checkbook3: 12 Column Grid'),
    'icon' => 'checkbook3_12_twocol_6_6.png',
    'theme' => 'checkbook3_12_twocol_6_6',
    'admin css' => '../checkbook3_panels_admin_12.css',
    'regions' => array(
      'left' => t('Left side'),
      'right' => t('Right side')
    ),
  );

  return $items;
}
