<?php

/**
 * @file
 * Administrative page callbacks for the prettify module.
 */

/**
 * Form builder for the prettify settings page.
 */
function prettify_admin_settings() {
  $library_path = prettify_library_get_path();
  if (!file_exists("$library_path/prettify.js")) {
    drupal_set_message(t('Could not find Google Code Prettify JavaScript library. Check the <a href="@status">status report</a> for more information.', array('@status' => url('admin/reports/status'))), 'error');
  }

  drupal_add_js(drupal_get_path('module', 'prettify') . '/prettify.admin.js');

  // -- Automatic syntax highlighting settings

  $form['auto'] = array(
    '#type' => 'fieldset',
    '#title' => t('Automatic syntax highlighting settings'),
  );

  /*
  $form['auto']['prettify_auto'] = array(
    '#type' => 'checkbox',
    '#title' => t('Enable automatic syntax highlighting'),
    '#description' => t('Code prettify automatically applies syntax highlighting of source code snippets in your Drupal site. If you only want use the code pretiffy filter or insert manually markup for code snippets, you can uncheck this checkbox to disable automatic syntax highlighting.'),
    '#default_value' => variable_get('prettify_auto', TRUE),
  );
  */

  $form['auto']['prettify_auto_markup'] = array(
    '#type' => 'checkboxes',
    '#title' => t('Markup for code blocks'),
    //'#description' => t('Choose the HTML tags which will be used to automatically apply syntax highlighting of source code snippets in your Drupal site.'),
    '#description' => t("Choose which DOM elements containing source code to highlight. The language of the code will be auto-detected for Prettify."),
    '#default_value' => variable_get('prettify_auto_markup', _prettify_default_auto_markup()),
    '#options' => array(
      PRETTIFY_MARKUP_CODE    => '<code>'. check_plain('<code>...</code>') .'</code>',
      PRETTIFY_MARKUP_PRE     => '<code>'. check_plain('<pre>...</pre>') .'</code>',
      PRETTIFY_MARKUP_PRECODE => '<code>'. check_plain('<pre><code>...</code></pre>') .'</code>',
    ),
  );

  $description = t("Constrains syntax highlighting to within the bounds of the specified element - can be a DOM element or a jQuery selector, like 'div.node-type-story, .block .content'. Empty, for  document element.");
  $form['auto']['prettify_auto_element_class'] = array(
    '#type' => 'textfield',
    '#title' => t('Containment element'),
    '#default_value' => variable_get('prettify_auto_element_class', '.content'),
    '#description' => $description,
  );

  $form['auto']['prettify_auto_disabled_class'] = array(
    '#type' => 'textfield',
    '#title' => t('Class to disable highlighting'),
    '#default_value' => variable_get('prettify_auto_disabled_class', 'no-code'),
    '#description' => t('Don\'t apply automatic syntax highlighting if HTML tags has this classes. Empty, for all tags.'),
    '#description' => t('You can use this HTML class to identify a block that is not code, like &lt;pre class="nocode"&gt;...&lt;/pre&gt;.'),
    '#description' => t('Use this class to disable highlighting of a fragment, like &lt;pre class="nocode"&gt;...&lt;/pre&gt;.')
  );

  $args = array('%selector1' => '.node .field-field-source-code pre', '%selector2' => 'pre');
  $description = t("Enter one jQuery selector per line. Example selectors are %selector1 or %selector2.", $args);
  $description = t('If you use different markup for code blocks you can use jQuery selectors to select elements containing the code to highlight. Enter one jQuery selector per line, example selectors are %selector1 or %selector2.', $args);
  $form['auto']['prettify_auto_custom_markup'] = array(
    '#type' => 'textarea',
    '#title' => t('Custom markup for code blocks'),
    //'#title' => t('jQuery selectors'),
    '#default_value' => variable_get('prettify_auto_custom_markup', ''),
    '#description' => $description,
    '#rows' => 3,
  );

  // -- Default behaviour settings

  $form['global'] = array(
    '#type' => 'fieldset',
    '#title' => t('Global settings'),
    //'#collapsible' => TRUE,
  );

  $styles = _prettify_get_options_styles();
  $styles['custom'] = t('Define CSS');
  $form['global']['prettify_css'] = array(
    '#type' => 'radios',
    '#title' => t('Styles'),
    '#default_value' => variable_get('prettify_css', 'default'),
    '#options' => $styles,
    '#description' => t('Defines the stylesheets to be used. View <a href="@url">gallery of themes</a>.', array('@url' => url('prettify/gallery'))),
  );

  $form['global']['prettify_css_path'] = array(
    '#type' => 'textfield',
    '#title' => t('Custom CSS path'),
    '#default_value' => variable_get('prettify_css_path', ''),
    '#description' => t('If "Define CSS" was selected above, enter external URL or path to a CSS file. Available tokens: <code>%b</code> (base path, eg: <code>/</code>), <code>%t</code> (path to theme, eg: <code>themes/garland</code>)') . '<br />' . t('Example:') . ' css/prettify.css,/themes/garland/prettify.css,%b%t/prettify.css,http://example.com/external.css',
  );

  $form['global']['prettify_behaviour_linenums'] = array(
    '#type' => 'select',
    '#title' => t('Show line numbers'),
    '#description' => t('Turn on line numbering by default.'),
    '#default_value' => variable_get('prettify_behaviour_linenums', 0),
    '#options' => array(t('No'), t('Yes')),
  );

  $form['global']['prettify_behaviour_extensions'] = array(
    '#type' => 'checkboxes',
    '#title' => t('Optionally language extensions'),
    '#description' => t("Because of commenting conventions, Prettify doesn't work on Smalltalk, Lisp-like, or CAML-like languages without an explicit lang class."),
    '#default_value' => variable_get('prettify_behaviour_extensions', array()),
    '#options' => _prettify_get_options_lang_extensions(),
  );

  // --

  $form['page_load_settings'] = array(
    '#type' => 'fieldset',
    '#title' => t('Page specific activation settings'),
    '#collapsible' => TRUE,
  );

  $options = array(
    t('Load on every page except the listed pages.'),
    t('Load on only the listed pages.'),
  );

  $description = t("Enter one page per line as Drupal paths. The '*' character is a wildcard. Example paths are %blog for the blog page and %blog-wildcard for every personal blog. %front is the front page.", array('%blog' => 'blog', '%blog-wildcard' => 'blog/*', '%front' => '<front>'));

  if (user_access('use PHP for settings')) {
    $options[] = t('Load if the following PHP code returns <code>TRUE</code> (PHP-mode, experts only).');
    $description .= ' '. t('If the PHP-mode is chosen, enter PHP code between %php. Note that executing incorrect PHP-code can break your Drupal site.', array('%php' => '<?php ?>'));
  }

  $form['page_load_settings']['prettify_activation_mode'] = array(
    '#type' => 'radios',
    '#title' => t('Load Prettify on specific pages'),
    '#options' => $options,
    '#default_value' => variable_get('prettify_activation_mode', 0),
  );
  $form['page_load_settings']['prettify_activation_pages'] = array(
    '#type' => 'textarea',
    '#title' => t('Pages'),
    '#default_value' => variable_get('prettify_activation_pages', implode("\n", array('admin', 'admin/*'))),
    '#description' => $description,
  );

  // --

  $form['advanced_settings'] = array(
    '#type' => 'fieldset',
    '#title' => t('Advanced settings'),
    '#collapsible' => TRUE,
  );

  /*
  $form['advanced_settings']['custom'] = array(
    '#type' => 'fieldset',
    '#title' => t('Custom automatic syntax highlighting'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );
  */

  $form['advanced_settings']['prettify_add_js_scope'] = array(
    '#type' => 'select',
    '#title' => t('Prettify JavaScript scope'),
    '#options' => array('header' => t('Header'), 'footer' => t('Footer')),
    '#default_value' => variable_get('prettify_add_js_scope', 'header'),
    '#description' => t('The location in which you want to place the Prettify script (default: Header).'),
  );

  $form['advanced_settings']['prettify_add_js_preprocess'] = array(
    '#type' => 'checkbox',
    '#title' => t('Prettify JavaScript preprocess'),
    '#default_value' => variable_get('prettify_add_js_preprocess', TRUE),
    '#description' => t('If is checked, the Prettify JS file be aggregated if this feature has been turned on under the performance section (default: checked).'),
  );

  $form['advanced_settings']['prettify_add_js_defer'] = array(
    '#type' => 'checkbox',
    '#title' => t('Prettify JavaScript defer'),
    '#default_value' => variable_get('prettify_add_js_defer', FALSE),
    '#description' => t('If is checked, the <a href="@url">defer attribute</a> is set on the &lt;script&gt; tag (default: unchecked).', array('@url' => 'http://www.w3.org/TR/html40/interact/scripts.html#h-18.2.1')),
  );

  $form['advanced_settings']['prettify_add_css_media'] = array(
    '#type' => 'select',
    '#title' => t('Prettify CSS media type'),
    '#options' => array('all' => t('All'), 'print' => t('Print'), 'screen' => t('Screen')),
    '#default_value' => variable_get('prettify_add_css_media', 'all'),
    '#description' => t('The media type for the Prettify stylesheet (default: All).'),
  );

  $form['advanced_settings']['prettify_add_css_preprocess'] = array(
    '#type' => 'checkbox',
    '#title' => t('Prettify CSS preprocess'),
    '#default_value' => variable_get('prettify_add_css_preprocess', TRUE),
    '#description' => t('If is checked, Prettify CSS file be aggregated and compressed if this feature has been turned on under the performance section (default: checked).'),
  );

  $form['#submit'][] = 'prettify_admin_settings_submit';
  return system_settings_form($form);
}

/**
 *
 */
function prettify_admin_settings_validate($form, &$form_state) {
  // Force to check <pre><code> tag if <pre> tag is checked.
  if (!empty($form_values['prettify_auto_tags'][PRETTIFY_MARKUP_PRE])) {
    $form_state['values']['prettify_auto_tags'][PRETTIFY_MARKUP_PRECODE] = PRETTIFY_MARKUP_PRECODE;
  }
}

/**
 *
 */
function prettify_admin_settings_submit($form, &$form_state) {
  // Only clear cache if code prettify filter is enabled.
  if (module_exists('filter')) {
    if (db_query("SELECT COUNT(*) FROM {filter} WHERE module = 'prettify' AND name = 'prettify' AND status = 1")) {
      if (module_exists('field')) {
        field_cache_clear();
      }
      cache_clear_all('*', 'cache_filter', TRUE);
    }
  }
}

function _prettify_get_options_lang_extensions() {
  return array('apollo' => t('AGC/AEA Assembly'), 'css' => t('CSS'), 'hs' => t('Haskell'), 'lisp' => t('Common Lisp and similar languages'), 'lua' => t('Lua'), 'ml' => t('OCaml, SML, F# and similar languages'), 'proto' => t('Protocol Buffers (<a href="@url">code.google.com/apis/protocolbuffers</a>)', array('@url' => url('http://code.google.com/apis/protocolbuffers'))), 'scala' => t('Scala'), 'sql' => t('SQL'), 'vb' => t('Visual Basic'), 'vhdl' => t("VHDL '93"), 'wiki' => t('WikiText'), 'yaml' => t('YAML'));
}

function _prettify_get_options_styles() {
  return array(
    'default' => t('Default'),
    'desert' => t('Desert vim theme (by <a href="@url">techto&hellip;@</a>)', array('@url' => 'http://code.google.com/u/@VhJeSlJYBhVMWgF7')),
    'sunburst' => t('Sunburst vim theme (by David Leibovic)', array('@url' => 'http://stackoverflow.com')),
    'googlecode' => t('Google Code (<a href="@url">code.google.com</a>)', array('@url' => 'http://code.google.com')),
    'stackoverflow' => t('Stack Overflow (<a href="@url">stackoverflow.com</a>)', array('@url' => 'http://stackoverflow.com')),
    'naspinski' => t('Naspinski (<a href="@url">naspinski.net</a>)', array('@url' => 'http://naspinski.net')),
    'drupalorg' => t('Drupal.org (<a href="@url">drupal.org</a>)', array('@url' => 'http://drupal.org')),
    'cobalt' => t('Cobalt Textmate adaptation (by <a href="@url">cartuchogl</a>)', array('@url' => 'http://groups.google.com/group/js-code-prettifier/browse_thread/thread/2a504720992aec6d/73b5bc2300c15d4f')),
  );
}

/**
 * Menu callback; @todo
 */
function prettify_admin_gallery_overview_page() {
  $script = <<<EOD
// Called by the demo.html frames loaded per theme to
// size the iframes properly and to allow them to tile
// the page nicely.
function adjustChildIframeSize(themeName, width, height) {
  var container = document.getElementById(themeName).parentNode;
  container.style.width = (+width + 16) + 'px';
  container.style.display = 'inline-block';
  var iframe = container.getElementsByTagName('iframe')[0];
  iframe.style.height = (+height + 16) + 'px';
}
EOD;

  drupal_add_js($script, 'inline');

  $output = '<p>'. t('Configure the theme in <a href="@url">Code Prettify settings page</a>. Print preview this page to see how the themes work on the printed page.', array('@url' => url('admin/settings/prettify'))) .'</p>';

  $styles = _prettify_get_options_styles();
  foreach ($styles as $css => $name) {
    $url = url("prettify/gallery/$css");
    $output .= '<div>';
    $output .= '<h3 style="margin-bottom: 0.5em">'. $name .'</h3>';
    $output .= '<iframe id="'. $css .'" src="'. $url .'" style="width: 100%; border-style: none; margin: 0; padding: 0"></iframe>';
    $output .= '</div>';
  }

  return $output;
}

/**
 * Menu callback; render the demo.html page.
 */
function prettify_admin_gallery_demo_page($css) {
  $base_path = base_path();
  $library_path = $base_path . prettify_library_get_path();
  $module_path = $base_path . drupal_get_path('module', 'prettify');
  if ($css == 'default') {
    $css_path = $library_path . '/prettify.css';
  }
  else {
    $css_path = $module_path . '/styles/'. $css .'.css';
  }
  $linenums = variable_get('prettify_behaviour_linenums', 0);
  $class = 'prettyprint lang-html';
  if ($linenums) {
    $class .= ' linenums';
  }

  $output = <<<EOD
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Theme $css</title>
    <script type="text/javascript" src="$library_path/prettify.js"></script>
    <script type="text/javascript" src="$library_path/lang-css.js"></script>
    <link type="text/css" rel="stylesheet" media="all" href="$css_path" />
    <style type="text/css">
      body { margin: 0; padding: 0 }
      pre { margin: 0 }
    </style>
    <script type="text/javascript">
      // Call out to the parent so that it can resize the iframe once this
      // document's body is loaded.
      function adjustHeightInParent() {
        if (parent !== window) {
          try {
            var div = document.body.getElementsByTagName('div')[0];
            parent.adjustChildIframeSize('$css', div.offsetWidth, div.offsetHeight);
          } catch (ex) {
            // Can happen when this page is opened in its own tab.
          }
        }
      }
    </script>
  </head>
  <body onload="prettyPrint(); adjustHeightInParent()">
    <div style="width: 40em; display: inline-block">
<pre class="$class">
&lt;script type="text/javascript"&gt;
// Say hello world until the user starts questioning
// the meaningfulness of their existence.
function helloWorld(world) {
  for (var i = 42; --i &gt;= 0;) {
    alert('Hello ' + String(world));
  }
}
&lt;/script&gt;
&lt;style&gt;
p { color: pink }
b { color: blue }
u { color: "umber" }
&lt;/style&gt;
</pre>
    </div>
  </body>
</html>
EOD;

  print $output;
}
