<?php

/**
 * Implements hook_panels_layouts().
 */
function checkbook_checkbook_12_twocol_9_3_panels_layouts() {
  $items['checkbook_12_twocol_9_3'] = array(
    'title' => t('Two Column 9-3'),
    'category' => t('Checkbook: 12 Column Grid'),
    'icon' => 'checkbook_12_twocol_9_3.png',
    'theme' => 'checkbook_12_twocol_9_3',
    'admin css' => '../checkbook_panels_admin_12.css',
    'regions' => array(
      'left' => t('Left side'),
      'right' => t('Right side')
    ),
  );

  return $items;
}
