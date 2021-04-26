CONTENTS OF THIS FILE
---------------------

* Introduction
* Requirements
* Recommended modules
* Installation
* Configuration
* Troubleshooting
* Maintainers

INTRODUCTION
------------

The XML sitemap engines module, part of the XML sitemap
(https://www.drupal.org/project/xmlsitemap) package, uploads the sitemap to
search engines automatically. The XML sitemap module creates a sitemap that
conforms to the sitemaps.org specification. This helps search engines to more
intelligently crawl a website and keep their results up to date.

* For a full description of the module visit
  https://www.drupal.org/documentation/modules/xmlsitemap

* To submit bug reports and feature suggestions, or to track changes visit
  https://www.drupal.org/project/issues/xmlsitemap


REQUIREMENTS
------------

This module requires the following module:

* XML sitemap - https://www.drupal.org/project/xmlsitemap


RECOMMENDED MODULES
-------------------

* Ctools - https://www.drupal.org/project/ctools
* RobotsTxt - https://www.drupal.org/project/robotstxt
* Site Verification - https://www.drupal.org/project/site_verify
* Browscap - https://www.drupal.org/project/browscap
* Vertical Tabs - https://www.drupal.org/project/vertical_tabs


INSTALLATION
------------

This is a submodule of the XML sitemap module. Install the XML sitemap module
as you would normally install a contributed Drupal module. Visit
https://www.drupal.org/node/895232 for further information.


CONFIGURATION
-------------

1. Install the XML sitemap module.
2. Enable the XML sitemap module.
3. To upload sitemaps to the search engines and customize how often the sitemaps
   should be uploaded, enable the XML sitemap engines module.
4. After building an XML sitemap, navigate to Administration > Configuration >
   XML sitemap > Search Engines.
5. Choose which engines you wish to send the sitemap to by selecting the
   appropriate checkboxes. Save configuration.


TROUBLESHOOTING
---------------

To verify the sitemap’s ownership with search engines, be sure Cron is run
regularly.


MAINTAINERS
-----------
* Andrei Mateescu (amateescu) - https://www.drupal.org/u/amateescu
* Dave Reid - https://www.drupal.org/u/dave-reid
* Juampy NR (juampynr) - https://www.drupal.org/u/juampynr
* Tasya Rukmana (tadityar) - https://www.drupal.org/u/tadityar
