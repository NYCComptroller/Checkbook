<?php
/**
 * @file
 * checkbook_contexts.context.inc
 */

/**
 * Implements hook_context_default_contexts().
 */
function checkbook_contexts_context_default_contexts() {
  $export = array();

  $context = new stdClass();
  $context->disabled = FALSE; /* Edit this to true to make a default context disabled initially */
  $context->api_version = 3;
  $context->name = 'fiscal_year_calendar_year';
  $context->description = 'includes both fiscal year and cal year in year drop down';
  $context->tag = '';
  $context->conditions = array(
    'path' => array(
      'values' => array(
        'payroll/*' => 'payroll/*',
        'spending/*' => 'spending/*',
        'contracts_landing/*' => 'contracts_landing/*',
        'contracts_pending_rev_landing/*' => 'contracts_pending_rev_landing/*',
        'contracts_pending_exp_landing/*' => 'contracts_pending_exp_landing/*',
        'contract/*' => 'contract/*',
      ),
    ),
  );
  $context->reactions = array(
    'block' => array(
      'blocks' => array(
        'block-8' => array(
          'module' => 'block',
          'delta' => 8,
          'region' => 'header_first',
          'weight' => '-10',
        ),
        'checkbook_smart_search-checkbook_smart_search_form' => array(
          'module' => 'checkbook_smart_search',
          'delta' => 'checkbook_smart_search_form',
          'region' => 'header_first',
          'weight' => '-9',
        ),
        'block-7' => array(
          'module' => 'block',
          'delta' => 7,
          'region' => 'header_first',
          'weight' => '-8',
        ),
        'block-10' => array(
          'module' => 'block',
          'delta' => 10,
          'region' => 'header_first',
          'weight' => '-7',
        ),
      ),
    ),
  );
  $context->condition_mode = 0;

  // Translatables
  // Included for use with string extractors like potx.
  t('includes both fiscal year and cal year in year drop down');
  $export['fiscal_year_calendar_year'] = $context;

  $context = new stdClass();
  $context->disabled = FALSE; /* Edit this to true to make a default context disabled initially */
  $context->api_version = 3;
  $context->name = 'homepage';
  $context->description = 'Homepage';
  $context->tag = '';
  $context->conditions = array(
    'path' => array(
      'values' => array(
        '<front>' => '<front>',
      ),
    ),
  );
  $context->reactions = array(
    'block' => array(
      'blocks' => array(
        'views-analysis-analysis' => array(
          'module' => 'views',
          'delta' => 'analysis-analysis',
          'region' => 'postscript_first',
          'weight' => '-10',
        ),
        'views-spotlight-spotlight' => array(
          'module' => 'views',
          'delta' => 'spotlight-spotlight',
          'region' => 'postscript_second',
          'weight' => '-10',
        ),
        'views-9922eb0db7d97865c9bff51a9e17e9c1' => array(
          'module' => 'views',
          'delta' => '9922eb0db7d97865c9bff51a9e17e9c1',
          'region' => 'postscript_third',
          'weight' => '-10',
        ),
      ),
    ),
  );
  $context->condition_mode = 1;

  // Translatables
  // Included for use with string extractors like potx.
  t('Homepage');
  $export['homepage'] = $context;

  $context = new stdClass();
  $context->disabled = FALSE; /* Edit this to true to make a default context disabled initially */
  $context->api_version = 3;
  $context->name = 'new_window';
  $context->description = 'Disable regions when opening grid view and spending links in a new window';
  $context->tag = '';
  $context->conditions = array(
    'path' => array(
      'values' => array(
        'gridview/popup/*' => 'gridview/popup/*',
        '*/newwindow' => '*/newwindow',
      ),
    ),
  );
  $context->reactions = array(
    'region' => array(
      'bartik' => array(
        'disable' => array(
          'header' => 0,
          'help' => 0,
          'page_top' => 0,
          'page_bottom' => 0,
          'highlighted' => 0,
          'featured' => 0,
          'content' => 0,
          'sidebar_first' => 0,
          'sidebar_second' => 0,
          'triptych_first' => 0,
          'triptych_middle' => 0,
          'triptych_last' => 0,
          'footer_firstcolumn' => 0,
          'footer_secondcolumn' => 0,
          'footer_thirdcolumn' => 0,
          'footer_fourthcolumn' => 0,
          'footer' => 0,
          'dashboard_main' => 0,
          'dashboard_sidebar' => 0,
          'dashboard_inactive' => 0,
        ),
      ),
      'checkbook' => array(
        'disable' => array(
          'menu' => 'menu',
          'filters' => 'filters',
          'header_first' => 'header_first',
          'footer_first' => 'footer_first',
          'page_top' => 0,
          'page_bottom' => 0,
          'content' => 0,
          'user_first' => 0,
          'user_second' => 0,
          'branding' => 0,
          'sidebar_first' => 0,
          'sidebar_second' => 0,
          'header_second' => 0,
          'preface_first' => 0,
          'preface_second' => 0,
          'preface_third' => 0,
          'postscript_first' => 0,
          'postscript_second' => 0,
          'postscript_third' => 0,
          'postscript_fourth' => 0,
          'footer_second' => 0,
          'dashboard_main' => 0,
          'dashboard_sidebar' => 0,
          'dashboard_inactive' => 0,
        ),
      ),
      'seven' => array(
        'disable' => array(
          'content' => 0,
          'help' => 0,
          'page_top' => 0,
          'page_bottom' => 0,
          'sidebar_first' => 0,
          'dashboard_main' => 0,
          'dashboard_sidebar' => 0,
          'dashboard_inactive' => 0,
        ),
      ),
    ),
  );
  $context->condition_mode = 0;

  // Translatables
  // Included for use with string extractors like potx.
  t('Disable regions when opening grid view and spending links in a new window');
  $export['new_window'] = $context;

  $context = new stdClass();
  $context->disabled = FALSE; /* Edit this to true to make a default context disabled initially */
  $context->api_version = 3;
  $context->name = 'only_fiscal_year';
  $context->description = 'shows fiscal years in the year drop down list, not the calendar years';
  $context->tag = '';
  $context->conditions = array(
    'path' => array(
      'values' => array(
        'revenue/*' => 'revenue/*',
        'budget/*' => 'budget/*',
      ),
    ),
  );
  $context->reactions = array(
    'block' => array(
      'blocks' => array(
        'block-3' => array(
          'module' => 'block',
          'delta' => 3,
          'region' => 'header_first',
          'weight' => '-31',
        ),
        'checkbook_smart_search-checkbook_smart_search_form' => array(
          'module' => 'checkbook_smart_search',
          'delta' => 'checkbook_smart_search_form',
          'region' => 'header_first',
          'weight' => '-30',
        ),
        'block-7' => array(
          'module' => 'block',
          'delta' => 7,
          'region' => 'header_first',
          'weight' => '-25',
        ),
        'block-10' => array(
          'module' => 'block',
          'delta' => 10,
          'region' => 'header_first',
          'weight' => '-14',
        ),
      ),
    ),
  );
  $context->condition_mode = 0;

  // Translatables
  // Included for use with string extractors like potx.
  t('shows fiscal years in the year drop down list, not the calendar years');
  $export['only_fiscal_year'] = $context;

  $context = new stdClass();
  $context->disabled = FALSE; /* Edit this to true to make a default context disabled initially */
  $context->api_version = 3;
  $context->name = 'sitewide';
  $context->description = 'Sitewide block placement';
  $context->tag = '';
  $context->conditions = array(
    'sitewide' => array(
      'values' => array(
        1 => 1,
      ),
    ),
  );
  $context->reactions = array(
    'block' => array(
      'blocks' => array(
        'nice_menus-1' => array(
          'module' => 'nice_menus',
          'delta' => 1,
          'region' => 'menu',
          'weight' => '-10',
        ),
        'block-4' => array(
          'module' => 'block',
          'delta' => 4,
          'region' => 'filters',
          'weight' => '-10',
        ),
        'checkbook_smart_search-checkbook_smart_search_form' => array(
          'module' => 'checkbook_smart_search',
          'delta' => 'checkbook_smart_search_form',
          'region' => 'header_first',
          'weight' => '-29',
        ),
        'menu-menu-the-comptroller' => array(
          'module' => 'menu',
          'delta' => 'menu-the-comptroller',
          'region' => 'footer_first',
          'weight' => '-10',
        ),
        'menu-menu-the-administration' => array(
          'module' => 'menu',
          'delta' => 'menu-the-administration',
          'region' => 'footer_first',
          'weight' => '-9',
        ),
        'menu-menu-reports' => array(
          'module' => 'menu',
          'delta' => 'menu-reports',
          'region' => 'footer_first',
          'weight' => '-8',
        ),
        'menu-menu-bureaus' => array(
          'module' => 'menu',
          'delta' => 'menu-bureaus',
          'region' => 'footer_first',
          'weight' => '-7',
        ),
        'menu-menu-news-room' => array(
          'module' => 'menu',
          'delta' => 'menu-news-room',
          'region' => 'footer_first',
          'weight' => '-6',
        ),
        'menu-menu-employment' => array(
          'module' => 'menu',
          'delta' => 'menu-employment',
          'region' => 'footer_first',
          'weight' => '-5',
        ),
        'quickmenu-menu-the-comptroller' => array(
          'module' => 'quickmenu',
          'delta' => 'menu-the-comptroller',
          'region' => 'footer_first',
          'weight' => '-4',
        ),
        'quickmenu-menu-the-administration' => array(
          'module' => 'quickmenu',
          'delta' => 'menu-the-administration',
          'region' => 'footer_first',
          'weight' => '-3',
        ),
        'quickmenu-menu-reports' => array(
          'module' => 'quickmenu',
          'delta' => 'menu-reports',
          'region' => 'footer_first',
          'weight' => '-2',
        ),
        'quickmenu-menu-bureaus' => array(
          'module' => 'quickmenu',
          'delta' => 'menu-bureaus',
          'region' => 'footer_first',
          'weight' => '-1',
        ),
        'quickmenu-menu-news-room' => array(
          'module' => 'quickmenu',
          'delta' => 'menu-news-room',
          'region' => 'footer_first',
          'weight' => '0',
        ),
        'quickmenu-menu-employment' => array(
          'module' => 'quickmenu',
          'delta' => 'menu-employment',
          'region' => 'footer_first',
          'weight' => '1',
        ),
      ),
    ),
  );
  $context->condition_mode = 1;

  // Translatables
  // Included for use with string extractors like potx.
  t('Sitewide block placement');
  $export['sitewide'] = $context;

  return $export;
}
