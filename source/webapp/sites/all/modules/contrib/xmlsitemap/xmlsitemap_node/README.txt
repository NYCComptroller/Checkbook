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

The XML sitemap node module, part of the XML sitemap
(https://www.drupal.org/project/xmlsitemap) package, enables content nodes to
be in the sitemap. The XML sitemap module creates a sitemap that conforms to
the sitemaps.org specification. This helps search engines to more intelligently
crawl a website and keep their results up to date.

* For a full description of the module visit:
  https://www.drupal.org/project/xmlsitemap

* To submit bug reports and feature suggestions, or to track changes visit:
  https://www.drupal.org/project/issues/xmlsitemap


REQUIREMENTS
------------

This module requires the following modules:

* XML sitemap - (https://www.drupal.org/project/xmlsitemap)


RECOMMENDED MODULES
-------------------

* Ctools - (https://www.drupal.org/project/ctools)
* RobotsTxt - (https://www.drupal.org/project/robotstxt)
* Site Verification - (https://www.drupal.org/project/site_verify)
* Browscap - (https://www.drupal.org/project/browscap)
* Vertical Tabs - (https://www.drupal.org/project/vertical_tabs)


INSTALLATION
------------

* This is a submodule of the XML sitemap module. Install the XML sitemap module
as you would normally install a contributed Drupal module. Visit
https://www.drupal.org/node/895232 for further information.


CONFIGURATION
-------------

1. Install the XML sitemap module.
2. Enable the XML sitemap module.
3. To include nodes in the sitemap, enable the XML sitemap node submodule.
4. To add nodes to the sitemap, visit the Edit page of the Content Type which
   you want to appear on the sitemap.
5. Select the XML sitemap horizontal tab.
6. Under "Inclusion" change "Excluded" to become "Included". Save.
7. If enabled, all content of the specific node type will be included.
   Individual nodes can be excluded on their specific node edit page.
8. Once that is all complete, go to Configurations --> Search and Metadata -->
   XML sitemap.
9. Select the Rebuild Links tab in the upper right.
10. Select on "Rebuild sitemap" even if the message says that you do not need
   to.
11. Now you're taken back to the config page which shows you the link to your
    XML sitemap which you can select and confirm that pages have been added.


MAINTAINERS
-----------

* Andrei Mateescu (amateescu) - https://www.drupal.org/u/amateescu
* Dave Reid - https://www.drupal.org/u/dave-reid
* Juampy NR (juampynr) - https://www.drupal.org/u/juampynr
* Tasya Rukmana (tadityar) - https://www.drupal.org/u/tadityar
