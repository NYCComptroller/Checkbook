<?php

/**
 * @file
 * This file is empty by default because the base theme chain (Alpha & Omega) provides
 * all the basic functionality. However, in case you wish to customize the output that Drupal
 * generates through Alpha & Omega this file is a good place to do so.
 * 
 * Alpha comes with a neat solution for keeping this file as clean as possible while the code
 * for your subtheme grows. Please read the README.txt in the /preprocess and /process subfolders
 * for more information on this topic.
 */

/**
 * Implements hook_preprocess_html
 */
function checkbook3_preprocess_html(&$vars) {
	drupal_add_library('system', 'ui');
	drupal_add_library('system', 'ui.accordion');
	drupal_add_library('system', 'ui.autocomplete');
    drupal_add_css(path_to_theme().'/css/ie-7.css',array('group'=>CSS_THEME,'weight' => 997,'browsers'=>array('IE'=>'IE 7','!IE'=>FALSE)));
    drupal_add_css(path_to_theme().'/css/ie-8.css',array('group'=>CSS_THEME,'weight' => 998,'browsers'=>array('IE'=>'IE 8','!IE'=>FALSE)));
    drupal_add_css(path_to_theme().'/css/ie-9.css',array('group'=>CSS_THEME,'weight' => 999,'browsers'=>array('IE'=>'IE 9','!IE'=>FALSE)));
}

/**
 * Special Menu Items Fix
 */
function checkbook3_nice_menus_menu_item_link($variables) {
  if (empty($variables['element']['#localized_options'])) {
    $variables['element']['#localized_options'] = array();
  }
  if ($variables['element']['#href'] == '<nolink>') {
  return '<a class="nolink" href="#">' . $variables['element']['#title'] . '</a>';
  } else {
  return l($variables['element']['#title'], $variables['element']['#href'], $variables['element']['#localized_options']);
  }
}

/**
 * Implements hook_css_alter
 */
function checkbook3_css_alter(&$css) {
  // Installs the jquery.ui themeroller theme to the theme.
  if (isset($css['misc/ui/jquery.ui.theme.css'])) {
    $css['misc/ui/jquery.ui.theme.css']['data'] = drupal_get_path('theme', 'checkbook3') . '/css/jquery-ui/jquery-ui-1.8.17.custom.css';
  }
}

/**
 * 
 */
function using_ie() 
{ 
  $u_agent = $_SERVER['HTTP_USER_AGENT']; 
  $ub = False; 
  if(preg_match('/MSIE/i',$u_agent)) 
  { 
    $ub = True; 
  } 
  
  return $ub; 
} 





function checkbook3_pager_first($variables) {
  $text = $variables['text'];
  $element = $variables['element'];
  $parameters = $variables['parameters'];
  global $pager_page_array;
  $output = '';

  // If we are anywhere but the first page
  if ($pager_page_array[$element] == 0) {
    $attributes['class'] = "pagerItemDisabled";
  }
  $output = theme('pager_link', array('text' => $text, 'page_new' => pager_load_array(0, $element, $pager_page_array), 'element' => $element, 'parameters' => $parameters,'attributes'=>$attributes));
  

  return $output;
}


function checkbook3_pager_previous($variables) {
  $text = $variables['text'];
  $element = $variables['element'];
  $interval = $variables['interval'];
  $parameters = $variables['parameters'];
  global $pager_page_array;
  $output = '';

  if ($pager_page_array[$element] == 0) {
    $attributes['class'] = "pagerItemDisabled";
  }
  $page_new = pager_load_array($pager_page_array[$element] - $interval, $element, $pager_page_array);

  $output = theme('pager_link', array('text' => $text, 'page_new' => $page_new, 'element' => $element, 'parameters' => $parameters,'attributes'=>$attributes));

  return $output;
}



function checkbook3_pager_next($variables) {
  $text = $variables['text'];
  $element = $variables['element'];
  $interval = $variables['interval'];
  $parameters = $variables['parameters'];
  global $pager_page_array, $pager_total;
  $output = '';
  // If we are anywhere but the last page
  if ($pager_page_array[$element] == ($pager_total[$element] - 1)) {
    $attributes['class'] = "pagerItemDisabled";
  }
  
  $page_new = pager_load_array($pager_page_array[$element] + $interval, $element, $pager_page_array);
  $output = theme('pager_link', array('text' => $text, 'page_new' => $page_new, 'element' => $element, 'parameters' => $parameters,'attributes'=>$attributes));

  return $output;
}



function checkbook3_pager_last($variables) {
  $text = $variables['text'];
  $element = $variables['element'];
  $parameters = $variables['parameters'];
  global $pager_page_array, $pager_total;
  $output = '';

  // If we are anywhere but the last page
  if ($pager_page_array[$element] == ($pager_total[$element] - 1)) {
    $attributes['class'] = "pagerItemDisabled";
  }
  $output = theme('pager_link', array('text' => $text, 'page_new' => pager_load_array($pager_total[$element] - 1, $element, $pager_page_array)
        , 'element' => $element, 'parameters' => $parameters,'attributes'=>$attributes));

  return $output;
}





function checkbook3_pager($variables) {
  $tags = $variables['tags'];
  $element = $variables['element'];
  $parameters = $variables['parameters'];
  $quantity = $variables['quantity'];
  global $pager_page_array, $pager_total;

  // Calculate various markers within this pager piece:
  // Middle is used to "center" pages around the current page.
  $pager_middle = ceil($quantity / 2);
  // current is the page we are currently paged to
  $pager_current = $pager_page_array[$element] + 1;
  // first is the first page listed by this pager piece (re quantity)
  $pager_first = $pager_current - $pager_middle + 1;
  // last is the last page listed by this pager piece (re quantity)
  $pager_last = $pager_current + $quantity - $pager_middle;
  // max is the maximum page number
  $pager_max = $pager_total[$element];
  // End of marker calculations.

  // Prepare for generation loop.
  $i = $pager_first;
  if ($pager_last > $pager_max) {
    // Adjust "center" if at end of query.
    $i = $i + ($pager_max - $pager_last);
    $pager_last = $pager_max;
  }
  if ($i <= 0) {
    // Adjust "center" if at start of query.
    $pager_last = $pager_last + (1 - $i);
    $i = 1;
  }
  // End of generation loop preparation.

  $li_first = theme('pager_first', array('text' => (isset($tags[0]) ? $tags[0] : t('First')), 'element' => $element, 'parameters' => $parameters));
  $li_previous = theme('pager_previous', array('text' => (isset($tags[1]) ? $tags[1] : t('Previous')), 'element' => $element, 'interval' => 1, 'parameters' => $parameters));
  $li_next = theme('pager_next', array('text' => (isset($tags[3]) ? $tags[3] : t('Next')), 'element' => $element, 'interval' => 1, 'parameters' => $parameters));
  $li_last = theme('pager_last', array('text' => (isset($tags[4]) ? $tags[4] : t('Last')), 'element' => $element, 'parameters' => $parameters));

  if ($pager_total[$element] > 1) {
    if ($li_first) {
      $items[] = array(
          'class' => array('pager-first'),
          'data' => $li_first,
      );
    }
    if ($li_previous) {
      $items[] = array(
          'class' => array('pager-previous'),
          'data' => $li_previous,
      );
    }

    // When there is more than one page, create the pager list.
    if ($i != $pager_max) {
      // Now generate the actual pager piece.
      for (; $i <= $pager_last && $i <= $pager_max; $i++) {
        if ($i < $pager_current) {
          $items[] = array(
              'class' => array('pager-item'),
              'data' => theme('pager_previous', array('text' => $i, 'element' => $element, 'interval' => ($pager_current - $i), 'parameters' => $parameters)),
          );
        }
        if ($i == $pager_current) {
          $items[] = array(
              'class' => array('pager-current'),
              'data' => $i,
          );
        }
        if ($i > $pager_current) {
          $items[] = array(
              'class' => array('pager-item'),
              'data' => theme('pager_next', array('text' => $i, 'element' => $element, 'interval' => ($i - $pager_current), 'parameters' => $parameters)),
          );
        }
      }
      
    }
    // End generation.
    if ($li_next) {
      $items[] = array(
          'class' => array('pager-next'),
          'data' => $li_next,
      );
    }
    if ($li_last) {
      $items[] = array(
          'class' => array('pager-last'),
          'data' => $li_last,
      );
    }
    return '<h2 class="element-invisible">' . t('Pages') . '</h2>' . theme('item_list', array(
        'items' => $items,
        'attributes' => array('class' => array('pager')),
    ));
  }
}



function checkbook3_pager_link($variables) {
  $text = $variables['text'];
  $page_new = $variables['page_new'];
  $element = $variables['element'];
  $parameters = $variables['parameters'];
  $attributes = $variables['attributes'];
  $page = isset($_GET['page']) ? $_GET['page'] : '';
  if ($new_page = implode(',', pager_load_array($page_new[$element], $element, explode(',', $page)))) {
    $parameters['page'] = $new_page;
  }

  $query = array();
  if (count($parameters)) {
    $query = drupal_get_query_parameters($parameters, array());
  }
  if ($query_pager = pager_get_query_parameters()) {
    $query = array_merge($query, $query_pager);
  }

  // Set each pager link title
  if (!isset($attributes['title'])) {
    static $titles = NULL;
    if (!isset($titles)) {
      $titles = array(
          t('First') => t('Go to first page'),
          t('Previous') => t('Go to previous page'),
          t('Next') => t('Go to next page'),
          t('Last') => t('Go to last page'),
      );
    }
    if (isset($titles[$text])) {
      $attributes['title'] = $titles[$text];
    }
    elseif (is_numeric($text)) {
      $attributes['title'] = t('Go to page @number', array('@number' => $text));
    }
  }

  // @todo l() cannot be used here, since it adds an 'active' class based on the
  //   path only (which is always the current path for pager links). Apparently,
  //   none of the pager links is active at any time - but it should still be
  //   possible to use l() here.
  // @see http://drupal.org/node/1410574
  $attributes['href'] = url($_GET['q'], array('query' => $query));
  $attributes['href'] = urldecode($attributes['href']);
  return '<a' . drupal_attributes($attributes) . '>' . check_plain($text) . '</a>';
}

/*
 * Implements theme_select
 *
 * Overridden to provide custom helper function that allows addition of
 * other attributes to <option> elements through FAPI.
 *
 * Use the custom attribute #option_attributes to add other attributes.
 * #option_attributes takes the form of an associative array where the value of the <option>
 * must match the key of the array. The value will be an array in the form that
 * drupal_attributes() requires.
 */


function checkbook3_select($variables) {
  $element = $variables['element'];
  element_set_attributes($element, array('id', 'name', 'size'));
  _form_set_class($element, array('form-select'));
  return '<select' . drupal_attributes($element['#attributes']) . '>' . checkbook3_form_select_options($element) . '</select>';
}

 
/*
 * Helper function for checkbook3_select
 *
 * Retains functionality of _form_select_options(), but with additional functionality to add other attributes
 * to <option> tags.
 */
function checkbook3_form_select_options($element,$choices=NULL){
  if (!isset($choices)) {
    $choices = $element['#options'];
  }
  $option_att = $element['#option_attributes'];
  // array_key_exists() accommodates the rare event where $element['#value'] is NULL.
  // isset() fails in this situation.
  $value_valid = isset($element['#value']) || array_key_exists('#value', $element);
  $value_is_array = $value_valid && is_array($element['#value']);
  $options = '';
  if (is_array($choices)) {
    foreach ($choices as $key => $choice) {
      if (is_array($choice)) {
        $options .= '<optgroup label="' . $key . '">';
        $options .= checkbook3_form_select_options($element, $choice);
        $options .= '</optgroup>';
      }
      elseif (is_object($choice)) {
        $options .= checkbook3_form_select_options($element, $choice->option);
      }
      else {
        $key = (string) $key;
        if ($value_valid && (!$value_is_array && (string) $element['#value'] === $key || ($value_is_array && in_array($key, $element['#value'])))) {
          $selected = ' selected="selected"';
        }
        else {
          $selected = '';
        }
        //custom section that allows addition of other attributes to <option> tags
        if ($option_att && is_array($option_att) && array_key_exists($key,$option_att)){
          $attributes = drupal_attributes($option_att[$key]);
        } else {
          $attributes = '';
        }
        $options .= '<option value="' . check_plain($key) . '"' . $selected . $attributes . '>' . check_plain($choice) . '</option>';
      }
    }
  }

  return $options;
}



