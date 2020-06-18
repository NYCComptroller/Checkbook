<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML+RDFa 1.0//EN"
  "http://www.w3.org/MarkUp/DTD/xhtml-rdfa-1.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php print $language->language; ?>" version="XHTML+RDFa 1.0" dir="<?php print $language->dir; ?>"<?php print $rdf_namespaces; ?>>
<head profile="<?php print $grddl_profile; ?>">
  <?php print $head; ?>
  <title>Checkbook NYC</title>
  <meta name="title" content="Checkbook NYC" />
  <meta name="description" content="Checkbook NYC, an online transparency tool that for the first time placed the City’s day-to-day spending in the public domain" />
  <meta name="keywords" content="Checkbook NYC, NYC Checkbook, New York City, NYC Spending, NYC Contracts, NYC Budget, NYC Payroll, NYC Revenue, NYCHA, EDC, Citywide" />
  <?php print $styles; ?>
  <?php print $scripts; ?>
</head>
<body<?php print $attributes;?>>
<div id="body-inner">
<?php if (using_ie()) print '<div id="ie">'; ?>
  <div id="skip-link">
    <a href="#main-content" class="element-invisible element-focusable"><?php print t('Skip to main content'); ?></a>
  </div>
	<a name="top"></a>
  <?php print $page_top; ?>
  <?php print $page; ?>
  <?php print $page_bottom; ?>
<?php if (using_ie()) print '</div>'; ?>
</div>
</body>
</html>
