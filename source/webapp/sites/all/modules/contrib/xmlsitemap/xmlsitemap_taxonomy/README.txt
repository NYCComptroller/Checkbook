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

The XML sitemap taxonomy module, part of the XML sitemap
(https://www.drupal.org/project/xmlsitemap) package, adds taxonomy term links
to the sitemap. The XML sitemap module creates a sitemap that conforms to the
sitemaps.org specification. This helps search engines to more intelligently
crawl a website and keep their results up to date.

* For a full description of the module visit
https://www.drupal.org/documentation/modules/xmlsitemap

* To submit bug reports and feature suggestions, or to track changes visit
https://www.drupal.org/project/issues/xmlsitemap


REQUIREMENTS
------------

This module requires the following modules:

XML sitemap - (https://www.drupal.org/project/xmlsitemap)


RECOMMENDED MODULES
-------------------

* Ctools - (https://www.drupal.org/project/ctools)
* RobotsTxt - (https://www.drupal.org/project/robotstxt)
* Site Verification - (https://www.drupal.org/project/site_verify)
* Browscap - (https://www.drupal.org/project/browscap)
* Vertical Tabs - (https://www.drupal.org/project/vertical_tabs)


INSTALLATION
------------

This is a submodule of the XML sitemap module. Install the XML sitemap module as
you would normally install a contributed Drupal module. Visit
https://www.drupal.org/node/895232 for more information.


CONFIGURATION
-------------

1. Install the XML sitemap module.
2. Enable the XML sitemap module.
3. To include taxonomy terms in the sitemap, enable the XML sitemap taxonomy
   submodule.
4. Navigate to Administration > Structure > Taxonomy.
5. To include a whole vocabulary in the sitemap, click "edit vocabulary".
   Select the XML sitemap field set. Under "Inclusion" change "Excluded" to
   become "Included". Save.
6. To include a single vocabulary term in the sitemap, select edit vocabulary.
   Select the vocabulary term to be included. Select the XML sitemap field set.
   Under "Inclusion" change "Excluded" to become "Included". Save.
7. Once that is all complete, go to Configurations > Search and Metadata > XML
   Sitemap.
8. Select the Rebuild Links tab in the upper right.
9. Select "Rebuild sitemap" even if the message says that you do not need to.
10. Now you're taken back to the configuration page which shows you the link to
   your XML sitemap which you can click and confirm that pages have been added.


MAINTAINERS
-----------

* Andrei Mateescu (amateescu) - https://www.drupal.org/u/amateescu
* Renato Gonçalves (RenatoG) - https://www.drupal.org/u/RenatoG
* Dave Reid - https://www.drupal.org/u/dave-reid
* Juampy NR (juampynr) - https://www.drupal.org/u/juampynr
* Tasya Rukmana (tadityar) - https://www.drupal.org/u/tadityar
