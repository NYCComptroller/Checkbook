CONTENTS OF THIS FILE
---------------------

* Introduction
* Requirements
* Recommended modules
* Installation
* Configuration
* Maintainers


INTRODUCTION
------------

The XML sitemap menu module, part of the XML sitemap
(https://www.drupal.org/project/xmlsitemap) package, enables menu links to be on
the site map. The XML sitemap module creates a sitemap that conforms to the
sitemaps.org specification. This helps search engines to more intelligently
crawl a website and keep their results up to date.

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

This is a submodule of the XML sitemap module. Install the XML sitemap module as
you would normally install a contributed Drupal module. Visit
https://www.drupal.org/node/895232 for further information.


CONFIGURATION
-------------

1. Install the XML sitemap module.
2. Enable the XML sitemap module.
3. To include menu items in the sitemap, enable the XML sitemap menu submodule.
4. Navigate to Administration > Configuration > Search > XML Sitemap.
5. Select the Settings tab and there will be a Menu link field set. Open.
6. Choose the menu link to be edited. There will now be a XML sitemap horizontal
   tab. Under "Inclusion" change "Excluded" to become "Included". Select Save.
7. Once that is all complete, go to Configuration > Search and Metadata > XML
   Sitemap.
8. Select the Rebuild Links tab in the upper right.
9. Select on "Rebuild sitemap" even if the message says that you do not need to.
10. Now you're taken back to the configuration page which shows you the link to
    your XML sitemap which you can select and confirm that pages have been
    added.


MAINTAINERS
-----------

* Andrei Mateescu (amateescu) - https://www.drupal.org/u/amateescu
* Dave Reid - https://www.drupal.org/u/dave-reid
* Juampy NR (juampynr) - https://www.drupal.org/u/juampynr
* Tasya Rukmana (tadityar) - https://www.drupal.org/u/tadityar
