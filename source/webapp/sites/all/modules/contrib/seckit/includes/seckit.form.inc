<?php
/**
 * @file
 * Administrative interface for SecKit settings.
 */

/**
 * Forms administration page.
 */
function seckit_admin_form() {
  // Get default/configured (and unaltered) options.
  if ($hooks = module_implements('seckit_options_alter')) {
    foreach ($hooks as $key => $module) {
      $hooks[$key] = $module . '_seckit_options_alter()';
    }
    drupal_set_message(t("Some settings may be overridden at runtime. See @hooks.", array('@hooks' => implode(', ', $hooks))), 'status', FALSE);
    $options = _seckit_get_options(TRUE, FALSE);
  }
  else {
    $options = _seckit_get_options();
  }
  $defaults = _seckit_get_options_defaults();

  // main description
  $args = array(
    '!browserscope' => l(t('Browserscope'), 'http://www.browserscope.org/?category=security'),
  );
  $form['seckit_description'] = array(
    '#type' => 'item',
    '#description' => t('This module provides your website with various options to mitigate risks of common web application vulnerabilities like Cross-site Scripting, Cross-site Request Forgery and Clickjacking. It also has some options to improve your SSL/TLS security and fixes Drupal 6 core Upload module issue leading to an easy exploitation of an old Internet Explorer MIME sniffer HTML injection vulnerability. Note that some security features are not supported by all browsers. You may find this out at !browserscope.', $args),
  );

  // main fieldset for XSS
  $form['seckit_xss'] = array(
    '#type' => 'fieldset',
    '#title' => t('Cross-site Scripting'),
    '#collapsible' => TRUE,
    '#tree' => TRUE,
    '#description' => t('Configure levels and various techniques of protection from cross-site scripting attacks'),
  );

  // fieldset for Content Security Policy (CSP)
  $args = array(
    '!wiki' => l(t('Mozilla Wiki'), 'https://wiki.mozilla.org/Security/CSP'),
    '!caniuse' => l(t('Can I use'), 'https://caniuse.com/#feat=contentsecuritypolicy'),
  );
  $form['seckit_xss']['csp'] = array(
    '#type' => 'fieldset',
    '#title' => t('Content Security Policy'),
    '#collapsible' => TRUE,
    '#collapsed' => !$options['seckit_xss']['csp']['checkbox'],
    '#tree' => TRUE,
    '#description' => t('Content Security Policy is a policy framework that allows to specify trustworthy sources of content and to restrict its capabilities. You may read more about it at !wiki.', $args),
  );
  // CSP enable/disable
  $form['seckit_xss']['csp']['checkbox'] = array(
    '#type' => 'checkbox',
    '#default_value' => $options['seckit_xss']['csp']['checkbox'],
    '#title' => t('Enable Content Security Policy'),
    '#return_value' => 1,
    '#description' => t('Send Content-Security-Policy HTTP response headers with the list of Content Security Policy directives. <br/>Violation reports are ignored when this option is disabled.'),
  );
  $form['seckit_xss']['csp']['vendor-prefix'] = array(
    '#type' => 'fieldset',
    '#title' => t('Vendor Prefixed CSP headers'),
    '#collapsible' => TRUE,
    '#tree' => TRUE,
    '#collapsed' => !($options['seckit_xss']['csp']['vendor-prefix']['x'] || $options['seckit_xss']['csp']['vendor-prefix']['webkit']),
    '#description' => t('Support for legacy vendor-prefixed CSP headers. Details at !caniuse.', $args),
  );
  $form['seckit_xss']['csp']['vendor-prefix']['x'] = array(
    '#type' => 'checkbox',
    '#default_value' => $options['seckit_xss']['csp']['vendor-prefix']['x'],
    '#title' => t('Send X-Content-Security-Policy HTTP response header'),
    '#return_value' => 1,
    '#description' => t('Send vendor-prefixed X-Content-Security-Policy HTTP response headers with the list of Content Security Policy directives.'),
  );
  $form['seckit_xss']['csp']['vendor-prefix']['webkit'] = array(
    '#type' => 'checkbox',
    '#default_value' => $options['seckit_xss']['csp']['vendor-prefix']['webkit'],
    '#title' => t('Send X-WebKit-CSP HTTP response header'),
    '#return_value' => 1,
    '#description' => t('Send vendor-prefixed X-WebKit-CSP HTTP response headers with the list of Content Security Policy directives.'),
  );
  // CSP Upgrade Insecure Requests
  $form['seckit_xss']['csp']['upgrade-req'] = array(
    '#type' => 'checkbox',
      '#default_value' => $options['seckit_xss']['csp']['upgrade-req'],
      '#title' => t('Enable Upgrade Insecure Requests'),
      '#return_value' => 1,
      '#description' => t('Upgrade Insecure Requests (upgrade-insecure-requests) instructs user agents to rewrite URL schemes, changing HTTP to HTTPS. This directive is used to protect your visitors from insecure content or for websites with large numbers of old URL\'s that need to be rewritten.'),
   );
  // CSP report-only mode
  $form['seckit_xss']['csp']['report-only'] = array(
    '#type' => 'checkbox',
    '#default_value' => $options['seckit_xss']['csp']['report-only'],
    '#title' => t('Report Only'),
    '#return_value' => 1,
    '#description' => t('Use Content Security Policy in report-only mode. In this case, violations of policies will only be reported, not blocked. Use this while configuring policies. Reports are logged to watchdog.'),
  );
  $form['seckit_xss']['csp']['csp_limits'] = array(
    '#type' => 'item',
    '#title' => t("CSP violation reporting limits are currently disabled. See !Advanced_options below.", array('!Advanced_options' => '<a href="#edit-seckit-advanced">' . t('Advanced options') . '</a>')),
    '#description' => t("Reporting limits should be enabled once your CSP is production-ready, to prevent excessive report logging should the violation report URL be flooded."),
    '#states' => array(
      'visible' => array(
        ':input[name="seckit_advanced[unlimited_csp_reports]"]' => array('checked' => TRUE),
      ),
    ),
  );
  // CSP description
  $args = array(
    '!keywords' => theme('item_list', array('items' => array(
                     "'none' - block content from any source",
                     "'self' - allow content only from your domain",
                     "'unsafe-inline' - allow specific inline content (note, that it is supported by a subset of directives)",
                     "'unsafe-eval' - allow a set of string-to-code API which is restricted by default (supported by script-src directive)",
                   ))),
    '!wildcards' => theme('item_list', array('items' => array(
                      '* - load content from any source',
                      '*.example.com - load content from example.com and all its subdomains',
                      'example.com:* - load content from example.com via any port.  Otherwise, it will use your website default port',
                    ))),
    '!spec' => l(t('specification page'), 'http://www.w3.org/TR/CSP/'),
  );
  $form['seckit_xss']['csp']['description'] = array(
    '#type' => 'item',
    '#title' => t('Directives'),
    '#description' => t("Set up security policy for different types of content. Don't use www prefix. Keywords are: !keywords Wildcards (*) are allowed: !wildcards More information is available at !spec.", $args),
  );
  // CSP default-src directive
  $form['seckit_xss']['csp']['default-src'] = array(
    '#type' => 'textfield',
    '#maxlength'=> 1024,
    '#default_value' => $options['seckit_xss']['csp']['default-src'],
    '#title' => 'default-src',
    '#description' => t("Specify security policy for all types of content, which are not specified further (frame-ancestors excepted). Default is 'self'."),
  );
  // CSP script-src directive
  $form['seckit_xss']['csp']['script-src'] = array(
    '#type' => 'textfield',
    '#maxlength'=> 1024,
    '#default_value' => $options['seckit_xss']['csp']['script-src'],
    '#title' => 'script-src',
    '#description' => t('Specify trustworthy sources for &lt;script&gt; elements.'),
  );
  // CSP object-src directive
  $form['seckit_xss']['csp']['object-src'] = array(
    '#type' => 'textfield',
    '#maxlength'=> 1024,
    '#default_value' => $options['seckit_xss']['csp']['object-src'],
    '#title' => 'object-src',
    '#description' => t('Specify trustworthy sources for &lt;object&gt;, &lt;embed&gt; and &lt;applet&gt; elements.'),
  );
  // CSP style-src directive
  $form['seckit_xss']['csp']['style-src'] = array(
    '#type' => 'textfield',
    '#maxlength'=> 1024,
    '#default_value' => $options['seckit_xss']['csp']['style-src'],
    '#title' => 'style-src',
    '#description' => t('Specify trustworthy sources for stylesheets. Note, that inline stylesheets and style attributes of HTML elements are allowed.'),
  );
  // CSP img-src directive
  $form['seckit_xss']['csp']['img-src'] = array(
    '#type' => 'textfield',
    '#maxlength'=> 1024,
    '#default_value' => $options['seckit_xss']['csp']['img-src'],
    '#title' => 'img-src',
    '#description' => t('Specify trustworthy sources for &lt;img&gt; elements.'),
  );
  // CSP media-src directive
  $form['seckit_xss']['csp']['media-src'] = array(
    '#type' => 'textfield',
    '#maxlength'=> 1024,
    '#default_value' => $options['seckit_xss']['csp']['media-src'],
    '#title' => 'media-src',
    '#description' => t('Specify trustworthy sources for &lt;audio&gt; and &lt;video&gt; elements.'),
  );
  // CSP frame-src directive
  $form['seckit_xss']['csp']['frame-src'] = array(
    '#type' => 'textfield',
    '#maxlength'=> 1024,
    '#default_value' => $options['seckit_xss']['csp']['frame-src'],
    '#title' => 'frame-src',
    '#description' => t('Specify trustworthy sources for &lt;iframe&gt; and &lt;frame&gt; elements. This directive is deprecated and will be replaced by child-src. It is recommended to use the both the frame-src and child-src directives until all browsers you support recognize the child-src directive.'),
  );
  // CSP frame-ancestors directive.
  $form['seckit_xss']['csp']['frame-ancestors'] = array(
    '#type' => 'textfield',
    '#maxlength' => 1024,
    '#default_value' => $options['seckit_xss']['csp']['frame-ancestors'],
    '#title' => 'frame-ancestors',
    '#description' => t('Specify trustworthy hosts which are allowed to embed this site\'s resources via &lt;iframe&gt;, &lt;frame&gt;, &lt;object&gt;, &lt;embed&gt; and &lt;applet&gt; elements.'),
  );
  // CSP child-src directive
  $form['seckit_xss']['csp']['child-src'] = array(
    '#type' => 'textfield',
    '#maxlength'=> 1024,
    '#default_value' => $options['seckit_xss']['csp']['child-src'],
    '#title' => 'child-src',
    '#description' => t('Specify trustworthy sources for &lt;iframe&gt; and &lt;frame&gt; elements as well as for loading Workers.'),
  );
  // CSP font-src directive
  $form['seckit_xss']['csp']['font-src'] = array(
    '#type' => 'textfield',
    '#maxlength'=> 1024,
    '#default_value' => $options['seckit_xss']['csp']['font-src'],
    '#title' => 'font-src',
    '#description' => t('Specify trustworthy sources for @font-src CSS loads.'),
  );
  // CSP connect-src directive
  $form['seckit_xss']['csp']['connect-src'] = array(
    '#type' => 'textfield',
    '#maxlength'=> 1024,
    '#default_value' => $options['seckit_xss']['csp']['connect-src'],
    '#title' => 'connect-src',
    '#description' => t('Specify trustworthy sources for XMLHttpRequest, WebSocket and EventSource connections.'),
  );
  // CSP report-uri directive
  $form['seckit_xss']['csp']['report-uri'] = array(
    '#type' => 'textfield',
    '#maxlength'=> 1024,
    '#default_value' => $options['seckit_xss']['csp']['report-uri'],
    '#title' => 'report-uri',
    '#description' => t('Specify a URL (relative to the Drupal root or absolute) to which user-agents will report CSP violations. Use the default value of <code>@csp_report_url</code> (which logs the report data in watchdog), unless you have set up an alternative handler for these reports.', array('@csp_report_url' => SECKIT_CSP_REPORT_URL)),
  );
  // CSP policy-uri directive
  $form['seckit_xss']['csp']['policy-uri'] = array(
    '#type' => 'textfield',
    '#maxlength'=> 1024,
    '#default_value' => $options['seckit_xss']['csp']['policy-uri'],
    '#title' => 'policy-uri',
    '#description' => t("Specify a URL (relative to the Drupal root) for a file containing the (entire) policy. <strong>All other directives will be omitted</strong> by Security Kit, as <code>policy-uri</code> may only be defined in the <em>absence</em> of other policy definitions in the <code>X-Content-Security-Policy</code> HTTP header. The MIME type for this URI <strong>must</strong> be <code>text/x-content-security-policy</code>, otherwise user-agents will enforce the policy <code>allow 'none'</code>  instead."),
  );

  // fieldset for X-XSS-Protection
  $form['seckit_xss']['x_xss'] = array(
    '#type' => 'fieldset',
    '#title' => t('X-XSS-Protection header'),
    '#collapsible' => TRUE,
    '#collapsed' => ($options['seckit_xss']['x_xss']['select'] == SECKIT_X_XSS_DISABLE),
    '#tree' => TRUE,
    '#description' => t('X-XSS-Protection HTTP response header controls Microsoft Internet Explorer, Google Chrome and Apple Safari internal XSS filters.'),
  );
  // options for X-XSS-Protection
  $x_xss_protection_options = array(
    SECKIT_X_XSS_DISABLE => t('Disabled'),
    SECKIT_X_XSS_0 => '0',
    SECKIT_X_XSS_1 => '1',
    SECKIT_X_XSS_1_BLOCK => '1; mode=block',
  );
  // configure X-XSS-Protection
  $link = l(t("IE's XSS filter security flaws in past"), 'http://hackademix.net/2009/11/21/ies-xss-filter-creates-xss-vulnerabilities');
  $args = array(
    '!values' => theme('item_list', array('items' => array(
                   'Disabled - XSS filter will work in default mode. Enabled by default',
                   '0 - XSS filter will be disabled for a website. It may be useful because of ' . $link,
                   '1 - XSS filter will be left enabled, and will modify dangerous content',
                   '1; mode=block - XSS filter will be left enabled, but it will block entire page instead of modifying dangerous content',
                 ))),
  );
  $form['seckit_xss']['x_xss']['select'] = array(
    '#type' => 'select',
    '#title' => t('Configure'),
    '#options' => $x_xss_protection_options,
    '#default_value' => $options['seckit_xss']['x_xss']['select'],
    '#description' => t('!values', $args),
  );

  // main fieldset for CSRF
  $form['seckit_csrf'] = array(
    '#type' => 'fieldset',
    '#title' => t('Cross-site Request Forgery'),
    '#tree' => TRUE,
    '#collapsible' => TRUE,
    '#collapsed' => !$options['seckit_csrf']['origin'],
    '#description' => t('Configure levels and various techniques of protection from cross-site request forgery attacks'),
  );
  // enable/disable Origin
  $form['seckit_csrf']['origin'] = array(
    '#type' => 'checkbox',
    '#title' => t('HTTP Origin'),
    '#default_value' => $options['seckit_csrf']['origin'],
    '#description' => t('Check Origin HTTP request header.'),
  );
  // Origin whitelist
  $form['seckit_csrf']['origin_whitelist'] = array(
    '#type' => 'textfield',
    '#title' => t('Allow requests from'),
    '#default_value' => implode(", ", $options['seckit_csrf']['origin_whitelist']),
    '#size' => 90,
    '#maxlength' => 255,
    '#description' => t('Comma separated list of trustworthy sources. Do not enter your website URL - it is automatically added. Syntax of the source is: [protocol] :// [host] : [port] . E.g, http://example.com, https://example.com, https://www.example.com, http://www.example.com:8080'),
  );

  // main fieldset for Clickjacking
  $form['seckit_clickjacking'] = array(
    '#type' => 'fieldset',
    '#title' => t('Clickjacking'),
    '#collapsible' => TRUE,
    '#tree' => FALSE,
    '#description' => t('Configure levels and various techniques of protection from Clickjacking/UI Redressing attacks'),
  );

  // fieldset for X-Frame-Options settings. non-#tree.
  $form['seckit_clickjacking']['x_frame_options'] = array(
    '#type' => 'fieldset',
    '#title' => t('X-Frame-Options header'),
    '#collapsible' => TRUE,
    '#collapsed' => ($options['seckit_clickjacking']['x_frame'] == SECKIT_X_FRAME_DISABLE),
    '#tree' => FALSE,
    '#description' => t('Configure the X-Frame-Options HTTP header'),
  );

  // options for X-Frame-Options
  $x_frame_options = array(
    SECKIT_X_FRAME_DISABLE => t('Disabled'),
    SECKIT_X_FRAME_SAMEORIGIN => 'SAMEORIGIN',
    SECKIT_X_FRAME_DENY => 'DENY',
    SECKIT_X_FRAME_ALLOW_FROM => 'ALLOW-FROM',
  );
  // configure X-Frame-Options
  $args = array(
    '!values' => theme('item_list', array('items' => array(
                   'Disabled - turn off X-Frame-Options',
                   'SAMEORIGIN - browser allows all the attempts of framing website within its domain. Enabled by default',
                   'DENY - browser rejects any attempt of framing website',
                   'ALLOW-FROM - browser allows framing website only from specified source',
                 ))),
    '!msdn' => l(t('MSDN article'), 'http://blogs.msdn.com/b/ie/archive/2009/01/27/ie8-security-part-vii-clickjacking-defenses.aspx'),
    '!spec' => l(t('specification'), 'http://tools.ietf.org/html/draft-ietf-websec-x-frame-options-01'),
  );
  $form['seckit_clickjacking']['x_frame_options']['x_frame'] = array(
    '#type' => 'select',
    '#title' => t('X-Frame-Options'),
    '#options' => $x_frame_options,
    '#default_value' => $options['seckit_clickjacking']['x_frame'],
    '#description' => t("X-Frame-Options HTTP response header controls browser's policy of frame rendering. Possible values: !values You may read more about it at !msdn or !spec.", $args),
    // Non-tree (we skip a parent).
    '#parents' => array(
      'seckit_clickjacking',
      'x_frame',
    ),
  );

  // Origin value for "ALLOW-FROM" option.
  $form['seckit_clickjacking']['x_frame_options']['x_frame_allow_from'] = array(
    '#type' => 'textarea',
    '#title' => t('ALLOW-FROM'),
    '#default_value' => implode("\n", $options['seckit_clickjacking']['x_frame_allow_from']),
    '#description' => t('Origin URIs (as specified by RFC 6454) for the "X-Frame-Options: ALLOW-FROM" value. One per line. Example, http://domain.com'),
    // Non-tree (we skip a parent).
    '#parents' => array(
      'seckit_clickjacking',
      'x_frame_allow_from',
    ),
  );

  // fieldset for JavaScript settings. non-#tree.
  $form['seckit_clickjacking']['javascript'] = array(
    '#type' => 'fieldset',
    '#title' => t('JavaScript-based protection'),
    '#collapsible' => TRUE,
    '#collapsed' => !$options['seckit_clickjacking']['js_css_noscript'],
    '#tree' => FALSE,
    '#description' => t("Warning: With this enabled, the site <em>will not work at all</em> for users who have JavaScript disabled (e.g. users running the popular !NoScript browser extension, if they haven't whitelisted your site).", array('!NoScript' => l('NoScript', 'https://noscript.net/'))),
  );

  // Enable/disable JS + CSS + Noscript protection
  $args = array(
    '!EduardoVela' => l('Eduardo Vela', 'http://sirdarckcat.blogspot.com/'),
    '%js' => 'seckit.document_write.js',
    '%stop' => '<!-- stop SecKit protection -->',
    '%css' => 'seckit.no_body.css',
  );
  $form['seckit_clickjacking']['javascript']['js_css_noscript'] = array(
    '#type' => 'checkbox',
    '#title' => t('Enable JavaScript + CSS + Noscript protection'),
    '#return_value' => 1,
    '#default_value' => $options['seckit_clickjacking']['js_css_noscript'],
    '#description' => t('Enable protection via JavaScript, CSS and &lt;noscript&gt; tag. This is the most efficient Clickjacking prevention technique. If website is not being framed, %js starts commenting with <em>document.write()</em> and stops when the comment %stop is reached. Thus %css, which hides the page body, is ignored. If particularly this JavaScript file is being blocked (with XSS filter of Internet Explorer 8 or Safari), %css applies <em>display: none</em> to <em>body</em>, hiding it. If JavaScript is disabled within browser, it shows a special message. Credits for this trick go to !EduardoVela.', $args),
    // Non-tree (we skip a parent).
    '#parents' => array(
      'seckit_clickjacking',
      'js_css_noscript',
    ),
  );

  // custom text for "disabled JavaScript" message
  $form['seckit_clickjacking']['javascript']['noscript_message'] = array(
    '#type' => 'textfield',
    '#title' => t('Custom text for disabled JavaScript message'),
    '#default_value' => $options['seckit_clickjacking']['noscript_message'],
    '#description' => t('This message will be shown to user when JavaScript is disabled or unsupported in his browser. Default is "Sorry, you need to enable JavaScript to visit this website."'),
    // Non-tree (we skip a parent).
    '#parents' => array(
      'seckit_clickjacking',
      'noscript_message',
    ),
  );

  // main fieldset for SSL/TLS
  $form['seckit_ssl'] = array(
    '#type' => 'fieldset',
    '#title' => t('SSL/TLS'),
    '#collapsible' => TRUE,
    '#collapsed' => !$options['seckit_ssl']['hsts'],
    '#tree' => TRUE,
    '#description' => t('Configure various techniques to improve security of SSL/TLS'),
  );

  // enable/disable HTTP Strict Transport Security (HSTS)
  $args = array(
    '!wiki' => l(t('Wikipedia'), 'http://en.wikipedia.org/wiki/HTTP_Strict_Transport_Security'),
  );
  $form['seckit_ssl']['hsts'] = array(
    '#type' => 'checkbox',
    '#title' => t('HTTP Strict Transport Security'),
    '#description' => t('Enable Strict-Transport-Security HTTP response header. HTTP Strict Transport Security (HSTS) header is proposed to prevent eavesdropping and man-in-the-middle attacks like SSLStrip, when a single non-HTTPS request is enough for credential theft or hijacking. It forces browser to connect to the server in HTTPS-mode only and automatically convert HTTP links into secure before sending request. !wiki has more information about HSTS', $args),
    '#default_value' => $options['seckit_ssl']['hsts'],
    '#return_value' => 1,
  );
  // HSTS max-age directive
  $form['seckit_ssl']['hsts_max_age'] = array(
    '#type' => 'textfield',
    '#title' => t('Max-Age'),
    '#description' => t('Specify Max-Age value in seconds. It sets period when user-agent should remember receipt of this header field from this server. Default is 1000.'),
    '#default_value' => $options['seckit_ssl']['hsts_max_age'],
  );
  // HSTS includeSubDomains directive
  $form['seckit_ssl']['hsts_subdomains'] = array(
    '#type' => 'checkbox',
    '#title' => t('Include Subdomains'),
    '#description' => t('Force HTTP Strict Transport Security for all subdomains. If enabled, HSTS policy will be applied for all subdomains, otherwise only for the main domain.'),
    '#return_value' => 1,
    '#default_value' => $options['seckit_ssl']['hsts_subdomains'],
  );
  $args = array(
    '!HSTS_preload_list' => l(t('HSTS Preload list'), 'https://hstspreload.appspot.com/'),
  );
  // HSTS preload directive
  $form['seckit_ssl']['hsts_preload'] = array(
    '#type' => 'checkbox',
    '#title' => t('Preload'),
    '#description' => t("If you intend to submit your domain to the !HSTS_preload_list, you will need to enable the preload flag as confirmation. Don't submit your domain unless you're sure that you can support HTTPS for the long term, as this action cannot be undone.", $args),
    '#return_value' => 1,
    '#default_value' => $options['seckit_ssl']['hsts_preload'],
  );

  // main fieldset for Expect-CT
  $args = array(
    '!url_docs' => "<a href='https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Expect-CT'>Mozilla's developer documentation</a>.",
  );
  $form['seckit_ct'] = array(
    '#type' => 'fieldset',
    '#title' => t('Expect-CT'),
    '#collapsible' => TRUE,
    '#collapsed' => !$options['seckit_ct']['expect_ct'],
    '#tree' => TRUE,
    '#description' => t('Configure the Expect-CT header which allows sites to opt in to reporting and/or enforcement of Certificate Transparency requirements. See !url_docs', $args),
  );

  // enable/disable the Expect-CT settings.
  $form['seckit_ct']['expect_ct'] = array(
    '#type' => 'checkbox',
    '#title' => t('Expect-CT'),
    '#description' => t('Enable the Expect-CT header.'),
    '#default_value' => $options['seckit_ct']['expect_ct'],
    '#return_value' => 1,
  );
  // Expect-CT max-age directive
  $form['seckit_ct']['max_age'] = array(
    '#type' => 'textfield',
    '#title' => t('Max-Age'),
    '#description' => t('Specify Max-Age value in seconds.'),
    '#default_value' => $options['seckit_ct']['max_age'],
    '#states' => array(
      'required' => array(
        'input[name="seckit_ct[expect_ct]"]' => array('checked' => TRUE),
      ),
    ),
  );
  // Expect-CT report-uri directive
  $form['seckit_ct']['report-uri'] = array(
    '#type' => 'textfield',
    '#maxlength'=> 1024,
    '#default_value' => $options['seckit_ct']['report-uri'],
    '#title' => 'report-uri',
    '#description' => t('Specify the (absolute) URI to which the user agent should report Expect-CT failures.'),
  );
  // Expect-CT enforce directive
  $form['seckit_ct']['enforce'] = array(
    '#type' => 'checkbox',
    '#title' => t('Enforce'),
    '#description' => t('Enforce the Certificate Transparency policy.'),
    '#return_value' => 1,
    '#default_value' => $options['seckit_ct']['enforce'],
  );

  // main fieldset for feature_policy
  $args = array(
    '!Google' => l(t("Google's developer documentation"), 'https://developers.google.com/web/updates/2018/06/feature-policy'),
  );
  $form['seckit_fp'] = array(
    '#type' => 'fieldset',
    '#title' => t('Feature policy'),
    '#collapsible' => TRUE,
    '#collapsed' => (!$options['seckit_fp']['feature_policy']),
    '#tree' => TRUE,
    '#description' => t("Allows configuration of the Feature-Policy header to selectively enable, disable, and modify the behavior of certain APIs and web features in the browser. See !Google.", $args)
  );

  // enable/disable Feature Policy
  $form['seckit_fp']['feature_policy'] = array(
    '#type' => 'checkbox',
    '#title' => t('Feature policy'),
    '#default_value' => $options['seckit_fp']['feature_policy'],
    '#description' => t(' Enable the Feature-Policy header.'),
  );
  // From-Origin destination
  $form['seckit_fp']['feature_policy_policy'] = array(
    '#type' => 'textarea',
    '#title' => t('Policy'),
    '#default_value' => $options['seckit_fp']['feature_policy_policy'],
    '#description' => t('Specify the policy to be sent out with Feature-Policy headers.'),
  );

  // main fieldset for various
  $form['seckit_various'] = array(
    '#type' => 'fieldset',
    '#title' => t('Miscellaneous'),
    '#collapsible' => TRUE,
    '#collapsed' => ($options['seckit_various'] == $defaults['seckit_various']),
    '#tree' => TRUE,
    '#description' => t('Configure miscellaneous other security enhancements'),
  );

  // Enable/disable Referrer-Policy.
  $args = array(
    '!Referrer_Policy_Spec' => l(t('specification'), 'http://www.w3.org/TR/referrer-policy/'),
  );

  $form['seckit_various']['referrer_policy'] = array(
    '#type' => 'checkbox',
    '#title' => t('Referrer-Policy'),
    '#default_value' => $options['seckit_various']['referrer_policy'],
    '#description' => t('Enable Referrer-Policy HTTP response header. This affects the Referer HTTP header for outgoing requests and navigations. More information is available at the !Referrer_Policy_Spec page.', $args),
  );

  // Referrer-Policy policy selection.
  $items = array(
    t('no-referrer - No referrer information will be sent.'),
    t('no-referrer-when-downgrade - No referrer information will be sent when navigating from HTTPS to HTTP.'),
    t('same-origin - Referrer information is sent only with requests to the same origin.'),
    t('origin - This will strip any path information from the referrer information.'),
    t('strict-origin - Like no-referrer-when-downgrade, but sends only the origin.'),
    t('origin-when-cross-origin - Sends full referrer information with same-origin request and origin only with cross-origin requests.'),
    t('strict-origin-when-cross-origin - Sends full referrer information with same-origin requests, origin only with cross-origin requests, and no referrer information if downgrading from https to http.'),
    t('unsafe-url - Sends full referrer information with all requests; as the name implies, this is not safe.'),
    t('"" (empty string) - Default to no-referrer-when-downgrade unless a higher-level policy has been defined elsewhere.'),
  );
  $args = array(
    '!items' => theme('item_list', array('items' => $items)),
  );

  $referrer_policy_options = array(
    'no-referrer' => 'no-referrer',
    'no-referrer-when-downgrade' => 'no-referrer-when-downgrade',
    'same-origin' => 'same-origin',
    'strict-origin' => 'strict-origin',
    'origin-when-cross-origin' => 'origin-when-cross-origin',
    'strict-origin-when-cross-origin' => 'strict-origin-when-cross-origin',
    'unsafe-url' => 'unsafe-url',
    'empty' => t('"" (empty string)'),
  );
  $form['seckit_various']['referrer_policy_policy'] = array(
    '#type' => 'select',
    '#title' => t('Select policy'),
    '#default_value' => $options['seckit_various']['referrer_policy_policy'],
    '#description' => t('Policy options are: !items', $args),
    '#options' => $referrer_policy_options,
    '#states' => array(
      'required' => array(
        'input[name="seckit_various[referrer_policy]"]' => array('checked' => TRUE),
      ),
    ),
  );

  // enable/disable From-Origin
  $args = array(
    '!spec' => l(t('specification'), 'http://www.w3.org/TR/from-origin/'),
  );
  $form['seckit_various']['from_origin'] = array(
    '#type' => 'checkbox',
    '#title' => t('From-Origin'),
    '#default_value' => $options['seckit_various']['from_origin'],
    '#description' => t('Enable From-Origin HTTP response header. This forces user-agent to retrieve embedded content from your site only to listed destination. More information is available at !spec page.', $args),
  );
  // From-Origin destination
  $args = array(
    '!items' => theme('item_list', array('items' => array(
                  'same - allow loading of content only from your site. Default value.',
                  'serialized origin - address of trustworthy destination. For example, http://example.com, https://example.com, https://www.example.com, http://www.example.com:8080',
                ))),
  );
  $form['seckit_various']['from_origin_destination'] = array(
    '#type' => 'textfield',
    '#title' => t('Allow loading content to'),
    '#default_value' => $options['seckit_various']['from_origin_destination'],
    '#size' => 90,
    '#description' => t('Trustworthy destination. Possible variants are: !items', $args),
  );
  // Disable autocomplete on login and registration forms.
  $form['seckit_various']['disable_autocomplete'] = array(
    '#type' => 'checkbox',
    '#title' => t('Disable autocomplete on login and registration forms'),
    '#default_value' => $options['seckit_various']['disable_autocomplete'],
    '#description' => t('Prevent the browser from populating login/registration form fields using its autocomplete functionality. This as populated fields may contain sensitive information, facilitating unauthorized access.'),
  );

  // Advanced / developer options.
  $form['seckit_advanced'] = array(
    '#type' => 'fieldset',
    '#title' => t('Advanced options'),
    '#collapsible' => TRUE,
    '#collapsed' => ($options['seckit_advanced'] == $defaults['seckit_advanced']),
    '#tree' => TRUE,
    // '#description' => t(''),
  );
  $form['seckit_advanced']['disable_seckit'] = array(
    '#type' => 'checkbox',
    '#default_value' => $options['seckit_advanced']['disable_seckit'],
    '#title' => t('Disable Security Kit'),
    '#description' => t('Prevent the module from doing anything.'),
  );
  if ($options['seckit_advanced']['disable_seckit'] && empty($_POST)) {
    drupal_set_message(t("Security Kit is currently disabled in the Advanced options (below)."), 'warning');
  }

  // CSP report limits
  $form['seckit_advanced']['unlimited_csp_reports'] = array(
    '#type' => 'checkbox',
    '#default_value' => $options['seckit_advanced']['unlimited_csp_reports'],
    '#title' => t('Unlimited CSP reports'),
    '#description' => t('Ignore restrictions on the size and quantity of CSP violation reports. This should be disabled once the CSP is production-ready.'),
  );
  $form['seckit_advanced']['csp_limits'] = array(
    '#type' => 'fieldset',
    '#title' => t('CSP violation reporting limits'),
    '#description' => t("Reports breaching these limits will not be logged."),
    '#collapsible' => TRUE,
    '#collapsed' => ($options['seckit_advanced']['csp_limits'] == $defaults['seckit_advanced']['csp_limits']),
    '#states' => array(
      'visible' => array(
        ':input[name="seckit_advanced[unlimited_csp_reports]"]' => array('checked' => FALSE),
      ),
    ),
  );
  $limits = $options['seckit_advanced']['csp_limits'];
  $form['seckit_advanced']['csp_limits']['max_size'] = array(
    '#title' => t('Maximum report size (bytes)'),
    '#type' => 'textfield',
    '#maxlength'=> 16,
    '#default_value' => $limits['max_size'],
  );
  $form['seckit_advanced']['csp_limits']['flood'] = array(
    '#type' => 'fieldset',
    '#title' => t('Flood settings'),
    '#collapsible' => TRUE,
    '#collapsed' => ($options['seckit_advanced']['csp_limits']['flood'] == $defaults['seckit_advanced']['csp_limits']['flood']),
  );
  $form['seckit_advanced']['csp_limits']['flood']['limit_user'] = array(
    '#title' => t('Maximum reports per IP address'),
    '#description' => t('Applicable within the given time window'),
    '#type' => 'textfield',
    '#maxlength'=> 16,
    '#default_value' => $limits['flood']['limit_user'],
  );
  $form['seckit_advanced']['csp_limits']['flood']['window_user'] = array(
    '#title' => t('Time window for per IP address flood detection'),
    '#description' => t('Duration in seconds'),
    '#type' => 'textfield',
    '#maxlength'=> 16,
    '#default_value' => $limits['flood']['window_user'],
  );
  $form['seckit_advanced']['csp_limits']['flood']['limit_global'] = array(
    '#title' => t('Maximum reports globally (i.e. irrespective of IP address)'),
    '#description' => t('Applicable within the given time window'),
    '#type' => 'textfield',
    '#maxlength'=> 16,
    '#default_value' => $limits['flood']['limit_global'],
  );
  $form['seckit_advanced']['csp_limits']['flood']['window_global'] = array(
    '#title' => t('Time window for global flood detection'),
    '#description' => t('Duration in seconds'),
    '#type' => 'textfield',
    '#maxlength'=> 16,
    '#default_value' => $limits['flood']['window_global'],
  );

  // Execute the default handlers first.
  $form = system_settings_form($form);

  // Run custom validation and submission handlers afterwards.
  $form['#validate'][] = 'seckit_admin_form_validate';
  $form['#submit'][] = 'seckit_admin_form_submit';

  return $form;
}

/**
 * Validates form data.
 */
function seckit_admin_form_validate($form, &$form_state) {
  // Check validity of CSP report-uri when CSP headers are on.
  $is_csp_header_enabled = $form_state['values']['seckit_xss']['csp']['checkbox'];
  $report_uri = $form_state['values']['seckit_xss']['csp']['report-uri'];
  // Ignore empty report-uri as it will be filled in by the default later.
  if ($is_csp_header_enabled and !empty($report_uri) and !drupal_valid_path($report_uri)) {
    form_error($form['seckit_xss']['csp']['report-uri'], t('Non-existent path for report-uri given: %uri', array('%uri' => $report_uri)));
  }
  // if From-Origin is enabled, it should be explicitly set
  $from_origin_enable = $form_state['values']['seckit_various']['from_origin'];
  $from_origin_destination = $form_state['values']['seckit_various']['from_origin_destination'];
  if (($from_origin_enable == 1) && (!$from_origin_destination)) {
    form_error($form['seckit_various']['from_origin_destination'], t('You have to set up trustworthy destination for From-Origin HTTP response header. Default is same.'));
  }
  // if X-Frame-Options is set to ALLOW-FROM, it should be explicitly set
  $x_frame_value = $form_state['values']['seckit_clickjacking']['x_frame'];
  if ($x_frame_value == SECKIT_X_FRAME_ALLOW_FROM) {
    $x_frame_allow_from = $form_state['values']['seckit_clickjacking']['x_frame_allow_from'];
    if (!_seckit_explode_value($x_frame_allow_from)) {
      form_error($form['seckit_clickjacking']['x_frame_allow_from'], t('You must specify a trusted Origin for the ALLOW-FROM value of the X-Frame-Options HTTP response header.'));
    }
  }
  // if HTTP Strict Transport Security is enabled, max-age must be specified.
  // HSTS max-age should only contain digits.
  $hsts_enable = $form_state['values']['seckit_ssl']['hsts'];
  $hsts_max_age = $form_state['values']['seckit_ssl']['hsts_max_age'];
  if (($hsts_enable == 1) && (!$hsts_max_age)) {
    form_error($form['seckit_ssl']['hsts_max_age'], t('You have to set up Max-Age value for HTTP Strict Transport Security. Default is 1000.'));
  }
  if (preg_match('/[^0-9]/', $hsts_max_age)) {
    form_error($form['seckit_ssl']['hsts_max_age'], t('Only digits are allowed in HTTP Strict Transport Security Max-Age field.'));
  }
  // if JS + CSS + Noscript Clickjacking protection is enabled,
  // custom text for disabled JS must be specified
  $js_css_noscript_enable = $form_state['values']['seckit_clickjacking']['js_css_noscript'];
  $noscript_message = $form_state['values']['seckit_clickjacking']['noscript_message'];
  if (($js_css_noscript_enable == 1) && (!$noscript_message)) {
    form_error($form['seckit_clickjacking']['noscript_message'], t('You have to set up Custom text for disabled JavaScript message when JS + CSS + Noscript protection is enabled.'));
  }
}

/**
 * Submits form.
 */
function seckit_admin_form_submit($form, &$form_state) {
  // Our depencency on seckit_boot() may have changed, so reset the
  // bootstrap module settings and cache.
  _system_update_bootstrap_status();
}
