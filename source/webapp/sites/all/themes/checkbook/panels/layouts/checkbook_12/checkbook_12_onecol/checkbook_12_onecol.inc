<?php

/**
 * Implements hook_panels_layouts()
 */
function checkbook_checkbook_12_onecol_panels_layouts() {
  $items['checkbook_12_onecol'] = array(
    'title' => t('Single column 12'),
    'category' => t('Checkbook: 12 Column Grid'),
    'icon' => 'checkbook_12_onecol.png',
    'theme' => 'checkbook_12_onecol',
    'admin css' => '../checkbook_panels_admin_12.css',
    'regions' => array('middle' => t('Middle column')),
  );

  return $items;
}

