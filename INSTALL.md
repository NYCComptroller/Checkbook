Checkbook NYC Installation
==========================

Table of contents:

 * Introduction
 * Requirements
 * Optional configuration for scalability and performance
 * Installation
 
Introduction
------------

Our goal is for Checkbook NYC to be easily deployable by any city or
other entity that wants to offer a financial transparency dashboard.
The level of IT expertise required to deploy Checkbook NYC should be
about the same as that required to run, say, a Drupal site or other
LAMP-stack open source application suite.

However, the Checkbook deployment process is not at that stage yet.
We open sourced it knowing that more work is needed to smooth and
standardize deployment, and we intend to conduct that work in the
open, with participation from all interested parties.  The code's
authors, who are intimiately familiar with every step of the process,
naturally can and do regularly deploy production instances to
http://checkbooknyc.com/ -- but we are aware that many improvements
need to be made to these instructions, to the installation scripts,
etc, before that process is equally easy for newcomers.

We therefore ask for your patience *and your help* to make those
improvements.  As you try out Checkbook, please stay in contact with
the project, asking questions and making suggestions.  There is no
better source of information than feedback from people doing
deployment in the wild.  Contact the project by reporting bugs and
submitting pull requests at https://github.com/NYCComptroller/Checkbook,
and by joining us in the Checkbook NYC technical discussion forum:

      https://groups.google.com/d/forum/checkbooknyc

You do not need to be subscribed to post there, though we recommend
you subscribe if you're interested in following Checkbook development.

Requirements 
-------------

Please note that unlike most Drupal-based applications, Checkbook uses
two separate databases simultaneously:

 * MySQL for the application itself (essentially Drupal + some modules)
 * PostgreSQL for the financial data (usually much larger than the app)

The full list of dependencies is:

 * GNU/Linux or similar operating system
 * Drupal 7.x  _(Note: Checkbook includes Drupal, so don't download Drupal separately.)_
 * PHP 5.3 or higher
 * MySQL 5.0.15 or higher (http://www.mysql.com/ -- we have not tested 
   with a drop-in replacement such as MariaDB <https://mariadb.org/>
   but would be interested to know if it works) 
 * PostgreSQL 8.3 or higher (http://www.postgresql.org/ -- any
   database that supports PostgresSQL is compatible for storing 
   Checkbook data (e.g., PostgreSQL, Greenplum)
 * psql client (PostgreSQL command line interface)
 * Apache HTTPD with following PHP extensions
   - PHP Intl extension (http://php.net/manual/en/intl.setup.php)
   - PHP PostgreSQL extension (http://www.php.net/manual/en/pgsql.setup.php)
 * Solr 4.* search platform (http://lucene.apache.org/solr/)
 * Drush version 5.0 (Drupal command-line scripting tool)

The Checkbook distribution includes the Drupal source tree, along with
additional (non-core) custom and contributed Drupal modules used by
Checkbook.  The distribution also includes some SQL data required for
setup: a Drupal database that is loaded into MySQL as part of
installation, which is described in more detail later in this file.

Optional configuration for scalability and performance
------------------------------------------------------

 * A load balancer for distributing requests across multiple instances.
 * Varnish or any other reverse proxy cache for caching last access pages.
 * PGPool for distributing load across multiple PostgreSQL or
   Greenplum databases. 

Installation
------------

It is assumed that a GNU/Linux server with LAMP stack is installed.

The following assumptions are made about the installation:

 * Webroot is `/var/www/html`.
   (Actually it could be anywhere; it's always just referred to as
   "&lt;Webroot>" in these instructions.  However, `/var/www/html` is
   a typical value for the webroot.)
 * The Drupal database is named "checkbook_drupal".
 * The PostgreSQL database is named "checkbook".

Steps to install:

1. Download the latest version of the Checkbook code:
   
 `git clone https://github.com/NYCComptroller/Checkbook.git`

 The next steps will look familiar if you've installed Drupal before:

 * Copy the contents of the folder `source/webapp/` to the webroot
   directory, such that the top level inside &lt;Webroot> looks like
   the top level inside `source/webapp/` (i.e., looks like the top of
   a Drupal tree).

 * Copy `<Webroot>/sites/default/default.settings.php` to
   `<Webroot>/sites/default/settings.php` -- we will customize it at
   the latter location.

2. Bring over some external third-party libraries.

 * Highcharts
   - Download version 3.0.1 from http://www.highcharts.com/products/highcharts
   - Unpack and copy into `<Webroot>/sites/all/modules/dashboard_platform/widget_highcharts/highcharts/3.0.1`
   - Verify that the path to `highcharts.src.js` is `<Webroot>/sites/all/modules/dashboard_platform/widget_highcharts/highcharts/3.0.1/js/highcharts.src.js`

 * Highstock
   - Download version 1.2.4 from http://www.highcharts.com/products/highstock
   - Unpack and copy into `<Webroot>/sites/all/modules/dashboard_platform/widget_highcharts/highstock/1.2.4`
   - Verify that the path to `highstock.src.js` is `<Webroot>/sites/all/modules/dashboard_platform/widget_highcharts/highstock/1.2.4/js/highstock.src.js`

 Note that these Highcharts and Highstock downloads are available at
 no charge, but they are not licensed under open source licenses.
 We are actively seeking open source replacements to recommend in
 these installation instructions, and welcome suggestions.  Ideally
 such replacements would be drop-in compatible, but if they are not we
 will consider the necessary code adjustments.

3. Install the Drupal database.

 Create and import the database into MySQL using the following commands:

          $ mysql -u<username> -p<password>
          mysql> create database checkbook_drupal
          mysql> use checkbook_drupal
          mysql> source data/checkbook_drupal.sql

 (The path `data/checkbook_drupal.sql` is relative to the top of this
 source tree; you may need to give an absolute path or a different
 relative path when you issue the MySQL `source` command above,
 depending on where you invoked mysql.)

4. Install PostgreSQL database.

 Create and import the database into PostgreSQL using the following commands:

          $ psql -Upostgres
            _(enter the password for "postgres" user)_
          postgres=# create database checkbook ;
          postgres=# \q
            _(to exit from the database interactive prompt)_
          $ psql -Upostgres checkbook -f data/checkbook_demo_database_for_postgres_db_20130524.zip
            _(enter the password for the "postgres" user)_

 Use this command to verify that one can connect to the PostgreSQL DB:

          $ PGPASSWORD=password psql -h ip -U username db

 *Note: The data set loaded above by psql contains sanitized sample
 data for testing Checkbook -- you would not load it into a production
 instance.  We plan to better document the process for loading real
 data into production instances.  In the meantime, see these files:*

          documentation/Creating new Database and running ETL Job.docx
          documentation/Data Mapping  4_29_2013.xlsx
          documentation/NYC Checkbook2 ETL Implementation Approach_2013_29_01.docx

5. Install Solr and configure its settings.

 Installing Apache Solr can be complex if you've never done it before.
 Please refer to SOLR-INSTALL.md for Solr installation instructions.

 Configuring Solr:

 * Reindex Solr by using the following command:

              http://<solr-host>:<solr-port>/<solr webapp name>/dataimport?command=full-import&clean=true&jobID=0

 * Update the following settings in `<Webroot>/sites/default/settings.php`:

     - URL of Solr instance:

                  //Solr URL
                  $conf['check_book']['solr']['url'] = 'http://<solr server ip>:<solr server port>/<solr instance name>/';

     - Make sure this setting points to a directory that is writable by
       the user Apache HTTPD runs as:

                  $conf['check_book']['data_feeds']['db_file_dir'] = '/data/datafeeds';

     - Adjust this setting if you want the files generated by datafeeds
       be in a different directory than the default:

                  //relative directory path to 'sites/default/files' to store generated files
                  $conf['check_book']['data_feeds']['output_file_dir'] = 'datafeeds';

     - URL of the site (this is included in email notifications):

                  $conf['check_book']['data_feeds']['site_url'] = 'http://<site url>';

     - Adjust location of reference data text files.  (TODO: this could
       probably use some more explanation!)

                  //Reference data outputDirectory
                  $conf['check_book']['ref_data_dir'] = 'refdata';

     - Optionally adjust where temporary files are written when doing an
       export through the application:

                  //Export data outputDirectory
                  $conf['check_book']['export_data_dir'] = 'exportdata';

     - This setting is used to limit the number of records for the
       export file:

                  //no of records to limit for datatables
                  $conf['check_book']['datatables']['iTotalDisplayRecords'] = 200000;

     - Update the psql command in the obvious ways.  There is no actual
       backslash or linebreak here -- the quoted linebreak is just in
       these instructions to avoid an overly long line:

                  // update the command for psql.
                  $conf['check_book']['data_feeds']['command'] = \
                  'PGPASSWORD=<password> psql -h <postgres-db-ip> -U <postgres-db-user> <postgresdb-name>'

6. Install or Modify Fonts.

 On its New York City production instance at checkbooknyc.com,
 Checkbook uses Novecento Wide Normal font.  This font can be
 downloaded from
 http://www.myfonts.com/fonts/synthview/novecento/wide-normal/buy.html.
 Once downloaded, the following font files

          Novecentowide-Normal-webfont.eot
          Novecentowide-Normal-webfont.svg
          Novecentowide-Normal-webfont.ttf
          Novecentowide-Normal-webfont.woff  

  should be copied into

          webapp/sites/all/themes/checkbook3/fonts/

  Fonts on the site can be changed by editing these files:

          webapp/sites/all/themes/checkbook3/fonts/fonts.css
          webapp/sites/all/themes/checkbook3/css/fontfamily.css

  *There is no requirement to use the Novecento fonts.  They are used
  on New York City's production instance of Checkbook NYC, but other
  instances of Checkbook can use other fonts.  As we learn what other
  fonts look good, we will update this section.*

7. Set up cron jobs.

  Add the following entries to crontab (there are no line breaks or
  backslashes below -- long lines are just wrapped for readability):

          */15 * * * * root /usr/bin/php <path to drush>/drush.php             \
          --root="<Webroot>" scr processQueueJob                               \
          --script-path="sites/all/modules/custom/checkbook_api/script/"       \
          >> /dev/null 2>&1

          */5 * * * * root /usr/bin/php <path to drush>/drush.php              \
          --root="<Webroot>" scr sendFeedCompletionEmails                      \
          --script-path="sites/all/modules/custom/checkbook_datafeeds/script/" \
          >> /dev/null 2>&1

8. Verify that the site is working.

 * Test the site by accessing the root URL. 
   The default page should be the spending transactions page for
   current fiscal year.

 * Click on search button and should see the search results page.

 * Start typing any name in the search box and autocomplete results
   should appear.

 * Click on advanced search and click on submit. results page should appear.

 * Click on export in advances search results page to verfiy that
    export is working.
