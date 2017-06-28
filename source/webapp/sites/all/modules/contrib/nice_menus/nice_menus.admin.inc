<?php

/**
 * @file
 * Functionality for Nice Menus administration.
 */

/**
 * Settings form as implemented by hook_menu.
 */
function nice_menus_admin_settings($form, &$form_state) {
  $form['nice_menus_number'] = array(
    '#type' => 'textfield',
    '#description' => t('The total number of independent Nice menus blocks you want. Enter a number between 0 and 99. If you set this to 0, you will have no blocks created but you can still use the Nice menus theme functions directly in your theme.'),
    '#default_value' => variable_get('nice_menus_number', '2'),
    '#size' => 2,
  );
  $form['nice_menus_js'] = array(
    '#type' => 'checkbox',
    '#title' => t('Use JavaScript'),
    '#description' => t('This will add Superfish jQuery to Nice menus. This is required for Nice menus to work properly in Internet Explorer.'),
    '#default_value' => variable_get('nice_menus_js', 1),
  );
  $form['nice_menus_sf_options'] = array(
    '#type' => 'fieldset',
    '#title' => t('Advanced: Superfish options'),
    '#description' => t('You can change the default Superfish options by filling out the desired values here. These only take effect if the Use JavaScript box above is checked.'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );
  $form['nice_menus_sf_options']['nice_menus_sf_delay'] = array(
    '#type' => 'textfield',
    '#title' => t('Mouse delay'),
    '#description' => t('The delay in milliseconds that the mouse can remain outside a submenu without it closing.'),
    '#default_value' => variable_get('nice_menus_sf_delay', 800),
    '#size' => 5,
  );
  $form['nice_menus_sf_options']['nice_menus_sf_speed'] = array(
    '#type' => 'select',
    '#title' => t('Animation speed'),
    '#multiple' => FALSE,
    '#description' => t('Speed of the menu open/close animation.'),
    '#options' => array(
      'slow' => t('slow'),
      'normal' => t('normal'),
      'fast' => t('fast'),
    ),
    '#default_value' => variable_get('nice_menus_sf_speed', 1),
  );

  // Custom validation to make sure the user is entering numbers.
  $form['#validate'][] = 'nice_menus_settings_validate';

  return system_settings_form($form);
}

/**
 * Custom validation for the settings form.
 */
function nice_menus_settings_validate($form, &$form_state) {
  $number = $form_state['values']['nice_menus_number'];
  // Check to make sure it is a number and that is a maximum of 2 digits.
  if (!is_numeric($number) || strlen($number) > 2) {
    form_set_error('nice_menus_number', t('You must enter a number from 0 to 99.'));
  }
}
