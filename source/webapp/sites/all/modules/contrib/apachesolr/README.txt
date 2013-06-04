
This module integrates Drupal with the Apache Solr search platform. Solr search
can be used as a replacement for core content search and boasts both extra
features and better performance. Among the extra features is the ability to have
faceted search on facets ranging from content author to taxonomy to arbitrary
Field API fields.

The module comes with a schema.xml, solrconfig.xml, and protwords.txt file which
must be used in your Solr installation.

This module depends on the search framework in core.  When used in combination
with core search module, Apache Solr is not the default search. Access it via a
new tab on the default search page, called "Search".  You may configure it
to be default at ?q=admin/config/search/settings

Updating from 6.x
-----------------
Make sure that you have first updated to the latest 6.x version on the relevant
branch and that you have run all schema updates.  You will have to install the
new schema.xml and solrconfig.xml files, and restart the Solr server (or core)
and delete your index and reindex all content.

Installation
------------

Prerequisite: Java 5 or higher (a.k.a. 1.5.x).  PHP 5.2.4 or higher.

Install the Apache Solr Drupal module as you would any Drupal module. Note
that the Drupal 7.x-1.x branch does not require the SolrPhpClient to
be installed. All necessary code is now included with this module.

Before enabling the module, you must have a working Solr server, or be
subscribed to a service like Acquia Search.

The Debian/Ubuntu packages for Solr should NOT be used to install Solr.
For example, do NOT install the solr or solr-jetty packages.

Download the latest Solr 1.4.x or 3.x release (e.g. 1.4.1 or 3.5.0) from:
http://www.apache.org/dyn/closer.cgi/lucene/solr/

Apache Lucene 3.1, 3.2 or 3.3, have a possible index corruption bug on
server crash or power loss (LUCENE-3418). Solr 3.4 has a problem
with SortMissingLast so Solr  3.5.0 is preferred.

Unpack the tarball somewhere not visible to the web (not in your webserver
docroot and not inside of your Drupal directory).

The Solr download comes with an example application that you can use for
testing, development, and even for smaller production sites. This
application is found at apache-solr-1.4.1/example.

Move apache-solr-1.4.1/example/solr/conf/schema.xml and rename it to
something like schema.bak. Then move the solr-conf/schema.xml that
comes with this Drupal module to take its place. If you are using
Solr 3.5 or later, you can use solr-conf/schema-solr3x.xml instead.

Similarly, move apache-solr-1.4.1/example/solr/conf/solrconfig.xml and rename
it like solrconfig.bak. Then move the solr-conf/solrconfig.xml that comes
with this module to take its place.

Finally, move apache-solr-1.4.1/example/solr/conf/protwords.txt and rename
it like protwords.bak. Then move the solr-conf/protwords.txt that comes
with this module to take its place.

Make sure that the conf directory includes the following files - the Solr core
may not load if you don't have at least an empty file present:
solrconfig.xml
schema.xml
elevate.xml
mapping-ISOLatin1Accent.txt
protwords.txt
stopwords.txt
synonyms.txt

Now start the solr application by opening a shell, changing directory to
apache-solr-1.4.1/example, and executing the command java -jar start.jar

Test that your solr server is now available by visiting
http://localhost:8983/solr/admin/

Now, you should enable the "Apache Solr framework" and "Apache Solr search"
modules. Check that you can connect to Solr at ?q=admin/setting/apachesolr
Now run cron on your Drupal site until your content is indexed. You
can monitor the index at ?q=admin/settings/apachesolr/index

The solrconfig.xml that comes with this modules defines auto-commit, so
it may take a few minutes between running cron and when the new content
is visible in search.

To use facets you should download facetapi http://drupal.org/project/facetapi
This module will allow you to define and set facets next to your search pages.
Once this module is enabled, enable blocks for facets first at
Administer > Site configuration > Apache Solr > Enabled filters
then position them as you like at Administer > Site building > Blocks.

Configuration variables
--------------

The module provides some (hidden) variables that can be used to tweak its
behavior:

 - apachesolr_luke_limit: the limit (in terms of number of documents in the
   index) above which the module will not retrieve the number of terms per field
   when performing LUKE queries (for performance reasons).

 - apachesolr_tags_to_index: the list of HTML tags that the module will index
   (see apachesolr_add_tags_to_document()).

 - apachesolr_exclude_nodeapi_types: an array of node types each of which is
   an array of one or more module names, such as 'comment'.  Any type listed
   will have any listed modules' hook_node_update_index() implementation skipped
   when indexing. This can be useful for excluding comments or taxonomy links.

 - apachesolr_ping_timeout: the timeout (in seconds) after which the module will
   consider the Apache Solr server unavailable.

 - apachesolr_optimize_interval: the interval (in seconds) between automatic
   optimizations of the Apache Solr index. Set to 0 to disable.

 - apachesolr_cache_delay: the interval (in seconds) after an update after which
   the module will requery the Apache Solr for the index structure. Set it to
   your autocommit delay plus a few seconds.

 - apachesolr_query_class: the default query class to use.

 - apachesolr_index_comments_with_node: TRUE | FALSE. Whether to index comments
   along with each node.

 - apachesolr_cron_mass_limit: update or delete at most this many documents in
   each Solr request, such as when making {apachesolr_search_node} consistent
   with {node}.

Troubleshooting
--------------
Problem:
Links to nodes appear in the search results with a different host name or
subdomain than is preferred.  e.g. sometimes at http://example.com
and sometimes at http://www.example.com

Solution:
Set $base_url in settings.php to insure that an identical absolute url is
generated at all times when nodes are indexed.  Alternately, set up a re-direct
in .htaccess to prevent site visitors from accessing the site via more than one
site address.

Problem:
The 'Solr Index Queries' test fails with file permission errors.

Solution:
When running this test you should have your tomcat/jetty running as the same user
as the user under which PHP runs (often the same as the webserver). This is
important because of the on-the-fly folder creation within PHP.


Themers
----------------

See inline docs in apachesolr_theme and apachesolr_search_theme functions
within apachesolr.module and apachesolr_search.module.

