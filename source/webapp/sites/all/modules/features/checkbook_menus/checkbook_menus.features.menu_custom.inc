<?php
/**
 * @file
 * checkbook_menus.features.menu_custom.inc
 */

/**
 * Implements hook_menu_default_menu_custom().
 */
function checkbook_menus_menu_default_menu_custom() {
  $menus = array();

  // Exported menu: menu-bureaus
  $menus['menu-bureaus'] = array(
    'menu_name' => 'menu-bureaus',
    'title' => 'Bureaus',
    'description' => 'Bureaus Footer Menu',
  );
  // Exported menu: menu-employment
  $menus['menu-employment'] = array(
    'menu_name' => 'menu-employment',
    'title' => 'Employment',
    'description' => 'Employment Footer Menu',
  );
  // Exported menu: menu-news-room
  $menus['menu-news-room'] = array(
    'menu_name' => 'menu-news-room',
    'title' => 'News Room',
    'description' => 'News Room Footer Menu',
  );
  // Exported menu: menu-reports
  $menus['menu-reports'] = array(
    'menu_name' => 'menu-reports',
    'title' => 'Reports',
    'description' => 'Reports Footer Menu',
  );
  // Exported menu: menu-the-administration
  $menus['menu-the-administration'] = array(
    'menu_name' => 'menu-the-administration',
    'title' => 'The Administration',
    'description' => 'The Administration Footer Menu',
  );
  // Exported menu: menu-the-comptroller
  $menus['menu-the-comptroller'] = array(
    'menu_name' => 'menu-the-comptroller',
    'title' => 'The Comptroller',
    'description' => 'The Comptroller Footer Menu',
  );
  // Translatables
  // Included for use with string extractors like potx.
  t('Bureaus');
  t('Bureaus Footer Menu');
  t('Employment');
  t('Employment Footer Menu');
  t('News Room');
  t('News Room Footer Menu');
  t('Reports');
  t('Reports Footer Menu');
  t('The Administration');
  t('The Administration Footer Menu');
  t('The Comptroller');
  t('The Comptroller Footer Menu');


  return $menus;
}
