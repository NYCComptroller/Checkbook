<?php

/**
 * Implements hook_panels_layouts().
 */
function checkbook_checkbook_12_twocol_6_6_bricks_panels_layouts() {
  $items['checkbook_12_twocol_6_6_bricks'] = array(
    'title' => t('Two Column 6-6 bricks'),
    'category' => t('Checkbook: 12 Column Grid'),
    'icon' => 'checkbook_12_twocol_6_6_bricks.png',
    'theme' => 'checkbook_12_twocol_6_6_bricks',
    'admin css' => '../checkbook_panels_admin_12.css',
    'regions' => array(
      'top' => t('Top'),
      'left_above' => t('Left above'),
      'right_above' => t('Right above'),
      'middle' => t('Middle'),
      'left_below' => t('Left below'),
      'right_below' => t('Right below'),
      'bottom' => t('Bottom'),
    ),
  );

  return $items;
}
