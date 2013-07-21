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

      https://groups.google.com/group/checkbooknyc

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
   (Actually it could be anywhere; we sometimes refer to it as
   "&lt;Webroot>" in these instructions.  However, `/var/www/html` is
   a typical value for the webroot.)
 * The Drupal database is named `checkbook_drupal`.
 * The PostgreSQL database is named `checkbook`.

Steps to install:

1. Ensure you have the necessary system dependencies installed.  Under
 the Ubuntu 12.04 Server operating system, that looks like this:

          $ sudo apt-get update
          $ sudo apt-get install php5
          $ sudo apt-get install php5-gd
          $ sudo apt-get install php5-intl
          $ sudo apt-get install php5-mysql
          $ sudo apt-get install php5-pgsql
          $ sudo apt-get install mysql-server
          $ sudo apt-get install postgresql
          $ sudo apt-get install postgresql-client
          $ sudo apt-get install postgresql-contrib
          $ sudo apt-get install drush
          $ sudo apt-get install apache2
          $ sudo apt-get install openjdk-6-jre-headless
          $ sudo apt-get install zip

 (You will also need to install Apache Solr; that process will be
 detailed at a later step in these instructions.)

 TODO: Also, make sure ports 22, 80, and 8080 are open.  In AWS, do
 this via the security group.  Shell in using the PEM file, e.g.:
 $ ssh -i foo.pem ubuntu@ec2-54-234-239-21.compute-1.amazonaws.com

2. Download the latest version of the base Checkbook code:
   
         $ git clone https://github.com/NYCComptroller/Checkbook.git

 (TODO: might need to install git too)

 (It doesn't matter where you put it; later installation steps will
 copy the relevant parts to the appropriate destinations.)

 The next steps will look familiar if you've installed Drupal before.
 We'll copy the contents of the folder `source/webapp/` to the webroot
 directory, such that the top level inside &lt;Webroot> looks like
 the top level inside `source/webapp/` (i.e., looks like the top of
 a Drupal tree).

 First, make sure there's nothing in the way at the destination:

          $ ls /var/www/html
          No such file or directory

 (TODO: on AWS, "chown -R www-data.www-data /var/www" then
 "su www-data" then the below.)

 Good -- the next step will create the `/var/www/html` directory.
 Note that you might need to become user `www-data` (or whatever user
 has permission to create new directories under `/var/www/`) for this:
 (TODO: and www-data for other steps below too -- how to say this?)

          $ cp -a source/webapp /var/www/html
          $ ls /var/www/html
          authorize.php index.php          INSTALL.txt     profiles/  themes/
          CHANGELOG.txt INSTALL.mysql.txt  LICENSE.txt     README.txt update.php
          COPYRIGHT.txt INSTALL.pgsql.txt  MAINTAINERS.txt robots.txt UPGRADE.txt
          cron.php      install.php        misc/           scripts/   web.config
          includes/     INSTALL.sqlite.txt modules/        sites/     xmlrpc.php
          $ 

 Copy `<Webroot>/sites/default/default.settings.php` to
 `<Webroot>/sites/default/settings.php` (we'll edit settings.php later).  
 There is no line break below, nor backslash -- the backslash just
 indicates that the line continues:

          $ cp /var/www/html/sites/default/default.settings.php \
               /var/www/html/sites/default/settings.php

3. Bring over some third-party libraries.

 **Highcharts:**
  - Download version 3.0.1 from http://www.highcharts.com/products/highcharts

              $ wget http://www.highcharts.com/downloads/zips/Highcharts-3.0.1.zip

  - Unpack it into the appropriate place in the web application:

              $ mkdir -p /var/www/html/sites/all/modules/custom/widget_framework/widget_highcharts/highcharts/
              $ unzip Highcharts-3.0.1.zip -d \
                /var/www/html/sites/all/modules/custom/widget_framework/widget_highcharts/highcharts/3.0.1

     (That creates the "3.0.1" destination dir.)
  - Verify that it is unpacked into the right place, by checking that the path to `highcharts.src.js` is `<Webroot>/sites/all/modules/custom/widget_framework/widget_highcharts/highcharts/3.0.1/js/highcharts.src.js`:

              $ ls /var/www/html/sites/all/modules/custom/widget_framework/widget_highcharts/highcharts/3.0.1/js/highcharts.src.js

 **Highstock:**
  - Download version 1.2.4 from http://www.highcharts.com/products/highstock

              $ wget http://www.highcharts.com/downloads/zips/Highstock-1.2.4.zip

  - Unpack it into `<Webroot>/sites/all/modules/custom/widget_framework/widget_highcharts/highstock/1.2.4`

              $ mkdir -p /var/www/html/sites/all/modules/custom/widget_framework/widget_highcharts/highstock/
              $ unzip Highstock-1.2.4.zip -d \
                /var/www/html/sites/all/modules/custom/widget_framework/widget_highcharts/highstock/1.2.4

  - Verify that it is unpacked correctly into the right place, by checking that the path to `highstock.src.js` is `<Webroot>/sites/all/modules/custom/widget_framework/widget_highcharts/highstock/1.2.4/js/highstock.src.js`:

              $ ls /var/www/html/sites/all/modules/custom/widget_framework/widget_highcharts/highstock/1.2.4/js/highstock.src.js

 Note that these Highcharts and Highstock downloads are available at
 no charge, but they are not licensed under open source licenses.
 We are actively seeking open source replacements to recommend in
 these installation instructions, and welcome suggestions.  Ideally
 such replacements would be drop-in compatible, but if they are not we
 will consider making the necessary code adjustments.

4. Install the Drupal database.

 Create and import the database into MySQL using the following commands:

 TODO document creating the user (use 'checkbook')

          $ mysql -u<username> -p<password>
            _(enter the MySQL password for <username>)_
          mysql> create database checkbook_drupal;
          mysql> use checkbook_drupal
          mysql> source data/checkbook_drupal.sql

 (The path `data/checkbook_drupal.sql` is relative to the top of this
 source tree; you may need to give an absolute path or a different
 relative path when you issue the MySQL `source` command above,
 depending on where you invoked mysql.)

5. Install PostgreSQL database.

 Create and import the database into PostgreSQL using the following commands:

          $ cd data
          $ unzip checkbook_demo_database_for_postgres_db_20130524.zip
          Archive:  checkbook_demo_database_for_postgres_db_20130524.zip
            inflating: checkbook_demo_database_for_postgres_db_20130524.sql
          $ cd ..
          $ sudo su postgres
          $ psql
          postgres=# create database checkbook ;
          postgres=# \q
            _(to exit from the database interactive prompt)_
          $ psql checkbook -f data/checkbook_demo_database_for_postgres_db_20130524.sql

 (TODO: "$ createdb checkbook && createuser checkbook" would have
 worked too.)

 *Note: The data set loaded above by psql contains sanitized sample
 data for testing Checkbook -- you would not load it into a production
 instance.  We plan to better document the process for loading real
 data into production instances.  These documentation files describe
 more about the process of importing data and running a production
 instance:*

          documentation/Creating new Database and running ETL Job.docx
          documentation/Data Mapping  4_29_2013.xlsx
          documentation/NYC Checkbook2 ETL Implementation Approach_2013_29_01.docx

6. Edit settings.php

   TODO: Having the mysql checkbook user be "checkbook" is okay, but
   in Postgres the user is the PG root user "postgres" -- and this is
   hardcoded into the demo data .sql file, so we can't just change it
   in these instructions.  Talk to REI about this.  See below.

     $databases = array(
         'default' => array(
             'default' => array(
                 'database' => 'checkbook_drupal',
                 'username' => 'checkbook',
                 'password' => 'checkbook',
                 'host' => 'localhost',
                 'port' => '',
                 'driver' => 'mysql',
                 'prefix' => '',
             ),
         ),
         'checkbook' => array(
             'main' => array(
                 'database' => 'checkbook',
                 'username' => 'postgres',
                 'password' => 'postgres',
                 'host' => '127.0.0.1',
                 'port' => '5432',
                 'driver' => 'pgsql',
                 'prefix' => '',
                 'schema' => 'public'
             ),
         ),
     );

  TODO: in email "mdw4 error" from Eric, the problem is that there are
  hardcoded REI-specific things in settings.php, for example the last
  line in the checkbook custom settings section at the end:

    $conf['check_book']['data_feeds']['command'] = \
      'PGPASSWORD=webuser1 psql -h mdw4 -U webuser1 checkbook';

  That looks like it's for ETL import or for APIs ("data_feeds"), but
  it's expecting "webuser1" in PG, and host "mdw4".

7. Install Solr and configure its settings.

 Installing Apache Solr can be complex if you've never done it before.
 Please refer to SOLR-INSTALL.md for Solr installation instructions.

 Configuring Solr:

 * Reindex Solr by using the following command:

              http://<solr-host>:<solr-port>/solr-checkbook/dataimport?command=full-import&clean=true&jobID=0

              (TODO: that is redundant with SOLR-INSTALL.md)

 * Update the following settings in `<Webroot>/sites/default/settings.php`:

     - URL of Solr instance:

                  //Solr URL
                  $conf['check_book']['solr']['url'] = 'http://<solr server ip>:<solr server port>/<solr instance name>/';

       TODO: You can just use "http://localhost:8080/solr-checkbook"
       for installs that use one box.

       TODO: Note that SOLR-INSTALL.md and default.settings.php
       contradict each other on this URL -- one has port 8080, the
       other 8088.  The real solution is to finish the templatization.

    - Make sure this setting points to a directory that is writable by
      the user Apache HTTPD runs as:
              
                  create directory /data/datafeeds 
                  $conf['check_book']['data_feeds']['db_file_dir'] = '/data/datafeeds';

                  (TODO: we did 'sites/default/files/db_file_dir'
                  instead.  And we created it first.)

     - Adjust this setting if you want the files generated by datafeeds to
       be in a different directory than the default:

                  (TODO: does the first line below mean that in this
                  step we should actually *create* the directory too?
                  We did, FWIW.)

                  create directory datafeeds sites/default/files/datafeeds
                  //relative directory path to 'sites/default/files' to store generated files
                  $conf['check_book']['data_feeds']['output_file_dir'] = 'datafeeds';

     - URL of the site (this is included in email notifications):

                  $conf['check_book']['data_feeds']['site_url'] = 'http://<site url>';

     - Adjust location of reference data text files. This directory is used to write reference data files.

                  (TODO: does the first line below mean that in this
                  step we should actually *create* the directory too?
                  We did, FWIW.)

                  create directory refdata at sites/default/files/refdata
                  //Reference data outputDirectory
                  $conf['check_book']['ref_data_dir'] = 'refdata';

     - Optionally adjust where temporary files are written when doing an
       export through the application:

                  (TODO: and what about here?  Do we have to create
                  this directory?  We did, FWIW.)

                  //Export data outputDirectory
                  $conf['check_book']['export_data_dir'] = 'exportdata';

     - This setting is used to limit the number of records for the
       export file:

                  //no of records to limit for datatables
                  $conf['check_book']['datatables']['iTotalDisplayRecords'] = 200000;

     - Update the psql command in the obvious ways.  Again, no actual
       backslash or linebreak here:

                  // update the command for psql.
                  $conf['check_book']['data_feeds']['command'] = \
                  'PGPASSWORD=<password> psql -h <postgres-db-ip> -U <postgres-db-user> <postgresdb-name>'

8. Install or Modify Fonts.

 TODO: This step can be skipped -- say so clearly.  Skipping it will
 result in, for example, in-browser errors visible to Firebug.

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

8. Set up cron jobs.

  TODO: which crontab?  Also, change "root" to "www-data" if appropriate.

  Add the following entries to crontab (again, backslashes escape line
  breaks):

          */15 * * * * www-data /usr/bin/drush                                 \
          --root="<Webroot>" scr processQueueJob                               \
          --script-path="sites/all/modules/custom/checkbook_api/script/"       \
          >> /dev/null 2>&1

          */5 * * * * www-data /usr/bin/drush                                  \
          --root="<Webroot>" scr sendFeedCompletionEmails                      \
          --script-path="sites/all/modules/custom/checkbook_datafeeds/script/" \
          >> /dev/null 2>&1

10. Make sure the web server can serve the site!

    TODO: Set up Apache config in /etc/apache2/sites-available/checkbook.conf,
    then symlink it into sites-enabled as per usual.  Then restart apache.

          <VirtualHost *:80>
            ServerAdmin webmaster@localhost
            ServerName your-checkbook-hostname.com
            DocumentRoot /var/www/html
            ErrorLog ${APACHE_LOG_DIR}/checkbook_error.log
            LogLevel debug
          </VirtualHost>

          <Directory /var/www/html>
            AllowOverride all
          </Directory>

          root@ip-10-30-128-33:/etc/apache2/sites-available# cd ..
          root@ip-10-30-128-33:/etc/apache2# cd sites-enabled/
          root@ip-10-30-128-33:/etc/apache2/sites-enabled# ln -s ../sites-available/checkbook.conf checkbook.conf
          root@ip-10-30-128-33:/etc/apache2/sites-enabled# ls -l
          total 0
          lrwxrwxrwx 1 root root 26 Jul 21 02:43 000-default -> ../sites-available/default
          lrwxrwxrwx 1 root root 33 Jul 21 03:32 checkbook.conf -> ../sites-available/checkbook.conf
          root@ip-10-30-128-33:/etc/apache2/sites-enabled# rm checkbook.conf 
          root@ip-10-30-128-33:/etc/apache2/sites-enabled# rm 000-default 
          root@ip-10-30-128-33:/etc/apache2/sites-enabled# ln -s ../sites-available/checkbook.conf 000-checkbook.conf
          root@ip-10-30-128-33:/etc/apache2/sites-enabled# 

          $ sudo service apache2 restart

11. Verify that the site is working.

 * Test the site by accessing the root URL. 
   The default page should be the spending transactions page for
   current fiscal year.

 * Click on search button and should see the search results page.

 * Start typing any name in the search box and autocomplete results
   should appear.

 * Click on advanced search and click on submit. results page should appear.

 * Click on export in advances search results page to verfiy that
    export is working.
