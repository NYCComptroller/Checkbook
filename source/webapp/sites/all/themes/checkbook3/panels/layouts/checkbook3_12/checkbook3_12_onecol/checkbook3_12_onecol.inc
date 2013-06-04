<?php

/**
 * Implements hook_panels_layouts()
 */
function checkbook3_checkbook3_12_onecol_panels_layouts() {
  $items['checkbook3_12_onecol'] = array(
    'title' => t('Single column 12'),
    'category' => t('checkbook3: 12 Column Grid'),
    'icon' => 'checkbook3_12_onecol.png',
    'theme' => 'checkbook3_12_onecol',
    'admin css' => '../checkbook3_panels_admin_12.css',
    'regions' => array('middle' => t('Middle column')),
  );

  return $items;
}

