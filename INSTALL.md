Checkbook NYC Installation
==========================

Table of contents:

 * Introduction
 * Requirements
 * Optional configuration for scalability and performance
 * Installation
 * Troubleshooting
 
Introduction
------------

Our goal is for Checkbook NYC to be easily deployable by any city or
other entity that wants to offer a financial transparency dashboard.
The level of IT expertise required to deploy Checkbook NYC should be
about the same as that required to run, say, a Drupal site or other
LAMP-stack open source application suite.

The Checkbook deployment process is getting closer to that ideal, but
it is not there yet.  We know that more work is needed to smooth and
standardize deployment, and we are conducting that work in the open,
with participation from all interested parties.  The code's authors,
who are intimately familiar with every step of the process, naturally
can and do regularly deploy production instances to
<http://checkbooknyc.com/> -- but various improvements need to be made
to these instructions before that process is equally easy for
newcomers.

We therefore ask for your patience *and your help* to make those
improvements.  As you try out Checkbook, please stay in contact with
the project, asking questions and making suggestions.  There is no
better source of information than feedback from people doing
deployment in the wild.  Contact the project by reporting bugs and
submitting pull requests at <https://github.com/NYCComptroller/Checkbook>,
and by joining us in the Checkbook NYC technical discussion forum at
<https://groups.google.com/group/checkbooknyc> (you do not need to
be subscribed to post there, though we recommend you subscribe if
you're interested in following Checkbook development).

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
 * MySQL 5.0.15 or higher (<http://www.mysql.com/> -- we have not tested 
   with a drop-in replacement such as MariaDB <https://mariadb.org/>
   but would be interested to know if it works) 
 * PostgreSQL 8.3 or higher (<http://www.postgresql.org/> -- any
   database that supports PostgresSQL is compatible for storing 
   Checkbook data (e.g., PostgreSQL, Greenplum)
 * psql client (PostgreSQL command line interface)
 * Apache HTTPD with following PHP extensions
   - PHP Intl extension (<http://php.net/manual/en/intl.setup.php>)
   - PHP PostgreSQL extension (<http://www.php.net/manual/en/pgsql.setup.php>)
 * Solr 4.* search platform (<http://lucene.apache.org/solr/>)
 * Drush version 5.0 (Drupal command-line scripting tool)

The Checkbook distribution includes the Drupal source tree, along with
additional (non-core) custom and contributed Drupal modules used by
Checkbook.  The distribution also includes some SQL data required for
setup: a Drupal database that is loaded into MySQL as part of
installation, which is described in more detail later in this file.

Optional configuration for scalability and performance
------------------------------------------------------

These instructions assume you're deploying Checkbook on a single
server, that is, where httpd, the databases, and Tomcat+Solr are all
running on one machine.

While this is okay for test deployments, in production you might want
to divide services among multiple machines, and (for example) use:

 * A load balancer for distributing requests across multiple instances.
 * Varnish or any other reverse proxy cache for caching last access pages.
 * PGPool for distributing load across multiple PostgreSQL or
   Greenplum databases. 

If you are configuring for scalability and performance in this way,
then parts of the instructions below will need adjustment, of course.

Installation
------------

We assume that a GNU/Linux server with LAMP stack is installed.
The following assumptions are also made about the installation:

 * The webroot is `/var/www/html`.
   (It could be somewhere else; `/var/www/html` is just the location
   we use in these instructions.)

The initial installation of the GNU/Linux server with LAMP stack takes on average 20 minutes. 

Steps to install:

1.  Make sure the right ports are open on your server.

    Ports 80 and 8080 will need to be opened.  If you plan to SSH in to
    the server to do the rest of this installation, you'll also need port
    22 open for SSH.

    (Note that if you're deploying on Amazon Web Services, port 22 may
    not be open by default; you would typically open it up via the AWS
    security group.)

2.  Ensure you have the necessary dependencies installed.

    On Ubuntu 12.04 Server, that looks like this (Debian GNU/Linux
    should be pretty similar):

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
        $ sudo apt-get install git
        $ sudo apt-get install drush
        $ sudo apt-get install apache2
        $ sudo apt-get install openjdk-6-jre-headless
        $ sudo apt-get install zip

    On CentOS 6.4 (Red Hat RHEL should similar), it's this:

        $ sudo yum install php.x86_64
        $ sudo yum install php-gd.x86_64
        $ sudo yum install php-intl.x86_64
        $ sudo yum install php-mysql.x86_64
        $ sudo yum install php-pgsql.x86_64
        $ sudo yum install php-xml
        $ sudo yum install mysql-server.x86_64
        $ sudo yum install git.x86_64
        $ sudo yum install httpd.x86_64
        $ sudo yum install java-1.6.0-openjdk.x86_64
        $ sudo yum install zip.x86_64

    The Drupal Shell (drush) is generally not provided as
    a package in official distro repositories, so we can
    install it using Composer:
    * https://getcomposer.org/doc/00-intro.md#globally
    * http://docs.drush.org/en/master/install-alternative/

    For PostgreSQL, we want version 9.x, but base CentOS 6.4
    only packages PostgreSQL 8.x.  So first download the RPM
    file from the PostgreSQL Yum/RPM Building Project at
    http://yum.postgresql.org/repopackages.php (look for the
    "CentOS 6 - x86_64" link, which as of this writing points to
    http://yum.postgresql.org/9.3/redhat/rhel-6-x86_64/pgdg-centos93-9.3-1.noarch.rpm).

        $ sudo rpm -ivh http://yum.postgresql.org/9.3/redhat/rhel-6-x86_64/pgdg-centos93-9.3-1.noarch.rpm

    That installed the repos RPM -- in other words, it made the
    yum/rpm system aware of the PostgreSQL package repository
    from which you can now install the actual packages.  Do so:

        $ sudo yum install postgresql93-server
        $ sudo yum install postgresql93-contrib

    For other operating systems, you'll have to translate the above to
    the appropriate package management system.

    *Note: the process of installing Apache Solr will be described
    later, as Checkbook currently installs Solr in an unusual way.*

3.  Download the latest version of the base Checkbook code:
   
        $ git clone https://github.com/NYCComptroller/Checkbook.git

    (It doesn't matter where you put it; later installation steps will
    copy the relevant parts to the appropriate destinations.)

4.  Install the Drupal app.

    The next steps will look familiar if you've installed Drupal before.
    We'll copy the contents of the folder `source/webapp/` to the webroot
    directory, such that the top level inside the webroot looks like the
    top level inside `source/webapp/` (i.e., looks like the top of a
    Drupal tree).

    First, make sure there's nothing in the way at the destination:

        $ ls /var/www/html
        No such file or directory

    Good.  Next, create the `/var/www/html` directory by copying the
    webapp from Checkbook:

        $ sudo su www-data
        $ cp -a source/webapp/* /var/www/html
        $ cp source/webapp/.htaccess /var/www/html
        $ ls /var/www/html/
        authorize.php index.php          INSTALL.txt     profiles/  themes/
        CHANGELOG.txt INSTALL.mysql.txt  LICENSE.txt     README.txt update.php
        COPYRIGHT.txt INSTALL.pgsql.txt  MAINTAINERS.txt robots.txt UPGRADE.txt
        cron.php      install.php        misc/           scripts/   web.config
        includes/     INSTALL.sqlite.txt modules/        sites/     xmlrpc.php
        $ 

    (If the `/var/www/html` directory is already there, then you'll
    need to adjust these instructions in the appropriate way.  Also,
    all of this assumes that directory `/var/www/` already exists and
    is owned by user `www-data` and group `www-data`.  If that's not
    the case, you may need to properly create and set root permissions
    for the www-data user with the following command. 
     `sudo chown -R www-data.www-data /var/www` would be one
    way to do that on Ubuntu 12.04.)

    Make sure the `sites/default/files/` directory has read, write,
    *and* execute permissions for the web server user:

        $ chmod ug+rwx /var/www/html/sites/default/files

    Finally, copy the `default.settings.php` file to
    `settings.php`. There is no actual line break below nor backslash --
    the backslash just indicates that the line continues:

        $ cp /var/www/html/sites/default/default.settings.php \
             /var/www/html/sites/default/settings.php

    (We'll edit `settings.php` later.)

5.  Bring over some third-party libraries.

    **Highcharts:**
    - Download version 7.1.1 from <http://www.highcharts.com/products/highcharts>:

            $ wget https://code.highcharts.com/zips/Highcharts-7.1.1.zip
    - Unpack it into the appropriate place in the web application:

            $ mkdir -p /var/www/html/sites/all/modules/custom/widget_framework/widget_highcharts/highcharts/
            $ unzip Highcharts-7.1.1.zip -d \
            /var/www/html/sites/all/modules/custom/widget_framework/widget_highcharts/highcharts/7.1.1

    - Verify that it unpacked into the right place, by checking the path to `highcharts.src.js`:

            $ ls /var/www/html/sites/all/modules/custom/widget_framework/widget_highcharts/highcharts/7.1.1/js/highcharts.src.js

    **Highstock:**
    - Download version 7.1.1 from <http://www.highcharts.com/products/highstock>:

            $ wget https://code.highcharts.com/zips/Highstock-7.1.1.zip
    - Unpack it:

            $ mkdir -p /var/www/html/sites/all/modules/custom/widget_framework/widget_highcharts/highstock/
            $ unzip Highstock-7.1.1.zip -d \
            /var/www/html/sites/all/modules/custom/widget_framework/widget_highcharts/highstock/7.1.1
    - Verify that it is unpacked into the right place, by checking that the path to `highstock.src.js`:

            $ ls /var/www/html/sites/all/modules/custom/widget_framework/widget_highcharts/highstock/7.1.1/js/highstock.src.js

    Note that these Highcharts and Highstock downloads are available at
    no charge, but they are not licensed under open source licenses.
    We are actively seeking open source replacements to recommend in
    these installation instructions, and welcome suggestions.  Ideally
    such replacements would be drop-in compatible, but if they are not we
    will consider making the necessary code adjustments.

6.  Install the Drupal (MySQL) database.

    Create and import the database into MySQL using the following commands:

        $ mysql -u root -p
          _(enter the MySQL password for the MySQL root user)_
        mysql> grant all on checkbook_drupal.* to checkbook@localhost \
               identified by 'checkbook';
        mysql> create database checkbook_drupal;
        mysql> use checkbook_drupal
        mysql> source data/checkbook_drupal.sql
        mysql> quit;

    *Notes:*

    On some operating systems (e.g., CentOS 6.4), the MySQL daemon may
    not have been invoked at system startup, and furthermore the MySQL
    root password may not have been set yet.  To deal with these
    situations respectively, do `sudo service mysqld start` and
    `mysqladmin -u root password "some_password"`.

    The path `data/checkbook_drupal.sql` is relative to the top of this
    source tree; you may need to give an absolute path or a different
    relative path when you issue the MySQL `source` command above,
    depending on where you invoked mysql.

    In this demo, we are giving the MySQL user "checkbook@localhost"
    the password 'checkbook', to match the default setting in
    `/var/www/html/sites/default/settings.php`.  For a production
    installation, you would want to use a better password of course.

7.  Install the Checkbook (PostgreSQL) database.
    Begin by inilitizing the PostgreSQL database with the following command: 
       $ service postgresql-9.3 initdb
       $ service postgresql-9.3 start;

    Create and import the database into PostgreSQL using the following commands:

        $ cd data
        $ unzip checkbook_demo_database_for_postgres_db_20140708.zip
        Archive:  checkbook_demo_database_for_postgres_db_20140708.zip
          inflating: checkbook_demo_database_for_postgres_db_20140708.sql
        $ unzip checkbook_demo_database_for_postgres_ogent_db_20140708.zip
        Archive:  checkbook_demo_database_for_postgres_ogent_db_20140708.zip
          inflating: checkbook_demo_database_for_postgres_ogent_db_20140708.sql
        $ cd ..
        $ sudo su postgres
        bash-4.1$ psql
        postgres=# create database checkbook ;
        postgres=# create database checkbook_ogent ;
        postgres=# \q
          _(to exit from the database interactive prompt)_
        bash-4.1$ psql checkbook -f         data/checkbook_demo_database_for_postgres_db_20140708/checkbook_demo_database_for_postgres_db_20140708.sql
        bash-4.1$ psql checkbook_ogent -f         data/checkbook_demo_database_for_postgres_ogent_db_20140708/checkbook_demo_database_for_postgres_ogent_db_20140708.sql
         bash-4.1$ exit
       
    
    Set the PostgreSQL database user's username and password with the following command:
    $ sudo -u postgres psql -c "ALTER USER postgres WITH PASSWORD 'postgres';"
    
    Verify that PostgreSQL can accept password authentication from the postgres user with the following command: 
    $ PGPASSWORD=postgres psql -U postgres checkbook
    *Notes:*

    The demo dataset loaded above assumes the PostgreSQL database user
    `postgres`, and the default settings.php file assumes that user's
    password is 'postgres' too.  Both of these should be changed in
    production, and ideally even our demo dataset should not assume a
    particular database username (and certainly not assume the admin user
    'postgres').  However, until that's fixed, these instructions are
    accurate.

    The sample data set contains sanitized data for testing Checkbook --
    you would not load it into a production instance.  We plan to better
    document the process for loading real data into production instances.
    These documentation files describe more about the process of
    importing data and running a production instance:

        documentation/Creating new Database and running ETL Job.docx
        documentation/Data Mapping  4_29_2013.xlsx
        documentation/NYC Checkbook2 ETL Implementation Approach_2013_29_01.docx

8.  Check the basic database settings in `settings.php`.

    Look for: $databases = array (); text in the following file `/var/www/html/sites/default/settings.php`:
    Insert the following settings listed below. 
        
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
             'checkbook_ogent' => array(
                'main' => array(
                    'database' => 'checkbook_ogent',
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
     
    If any of the settings don't look right for you, fix them.  (However,
    the default settings provided there should work assuming you used the
    defaults in the rest of these instructions.)

9.  Install Solr.

    Please refer to SOLR-INSTALL.md for Solr installation instructions.

10. Configure the rest of the webapp's `settings.php`.

   **Solr:**
   - URL of Solr instance:

            //Solr URL
            $conf['check_book']['solr']['url'] = 'http://<solr server ip>:<solr server port>/<solr instance name>/';

     You can change the value to "`http://localhost:8080/solr-checkbook`",
     assuming a deployment where everything runs on one server.

   **DB settings:**
   - Update the psql command in the obvious ways.  Again, there is no actual
     backslash nor linebreak after it:

            // update the command for psql.
            $conf['check_book']['data_feeds']['command'] = \
            'PGPASSWORD=<password> psql -h <postgres-db-ip> -U <postgres-db-user>'

     If you're just running PostgreSQL on the same server, you can
     either specify `-h localhost` or remove the `-h` option and
     argument entirely (since localhost is the default).  For the
     postgres username and password, put in the correct values, which
     are "postgres" for both if you've been using the defaults from
     these instructions, and similarly "checkbook" for the db name.
     
   **Site URL:**
   - URL of the site (this is included in email notifications):

            $conf['check_book']['data_feeds']['site_url'] = 'http://<site url>';

     Replace with the URL of your Checkbook site.  (E.g., on AWS, it might
     look something like '`<http://ec2-67-202-23-137.compute-1.amazonaws.com`').

   **File paths:**
   - Make sure this setting points to a directory that is writable by
     the user Apache HTTPD runs as:
              
            $conf['check_book']['data_feeds']['db_file_dir'] = '/data/datafeeds';

     This is an absolute path.  You don't have to use
     `/data/datafeeds`; another plausible location is
     '/var/www/html/sites/default/files/db_file_dir'.  Whatever value
     you use, make sure to create that directory and ensure it's
     writeable by user `www-data`.
   - Adjust this setting if you want the files generated by datafeeds to
     be in a different directory than the default:

            //relative directory path to 'sites/default/files' to store generated files
            $conf['check_book']['data_feeds']['output_file_dir'] = 'datafeeds';

     Make sure to create the directory `sites/default/files/datafeeds`
     too, and ensure it's writeable by `www-data`.
   - Adjust location of reference data text files. This directory is
     used to write reference data files.
  
            //Reference data outputDirectory
            $conf['check_book']['ref_data_dir'] = 'refdata';

     Make sure to create the directory `sites/default/files/refdata`
     too, and ensure it's writeable by `www-data`.
   - Optionally adjust where temporary files are written when doing an
     export through the application:

            //Export data outputDirectory
            $conf['check_book']['export_data_dir'] = 'exportdata';

     Make sure to create the directory `sites/default/files/exportdata`
     too, and ensure it's writeable by `www-data`.
   - This setting is used to limit the number of records for the export file:

            //no of records to limit for datatables
            $conf['check_book']['datatables']['iTotalDisplayRecords'] = 200000;

11. Optionally install Fonts.

    *This step is optional.  Without it, Checkbook just won't look
    quite the same as it looks at <http://checkbooknyc.com/>, and if
    you look at your site with an in-browser debugger such as Firebug,
    you'll see some warnings as fonts are requested and not found.*

    On its New York City production instance at checkbooknyc.com,
    Checkbook uses Novecento Wide Normal font.  This font can be
    downloaded from
    <http://www.myfonts.com/fonts/synthview/novecento/wide-normal/buy.html>.
    Once downloaded, the following font files

        Novecentowide-Normal-webfont.eot
        Novecentowide-Normal-webfont.svg
        Novecentowide-Normal-webfont.ttf
        Novecentowide-Normal-webfont.woff  

    should be copied into

        /var/www/html/sites/all/themes/checkbook3/fonts/

    Fonts on the site can be changed by editing these files:

        /var/www/html/sites/all/themes/checkbook3/fonts/fonts.css
        /var/www/html/sites/all/themes/checkbook3/css/fontfamily.css

12. Set up cron jobs.

    Use `crontab -e` to add the following entries to crontab (again,
    backslashes escape line breaks):

        */15 * * * * www-data /usr/bin/drush                                 \
        --root="/var/www/html" scr processQueueJob                           \
        --script-path="sites/all/modules/custom/checkbook_api/script/"       \
        >> /dev/null 2>&1

        */5 * * * * www-data /usr/bin/drush                                  \
        --root="/var/www/html" scr sendFeedCompletionEmails                  \
        --script-path="sites/all/modules/custom/checkbook_datafeeds/script/" \
        >> /dev/null 2>&1

13. Configure Apache to serve the site.

    Define an httpd configuration block for the site like this:

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

    On Ubuntu 12.04 or Debian GNU/Linux, the standard is to put that in a
    file named (e.g.) `/etc/apache2/sites-available/checkbook.conf`, and
    then install it like this:

        $ cd /etc/apache2/sites-enabled/
        $ sudo rm 000-default  # Old default site not interesting to us now.
        $ sudo ln -s ../sites-available/checkbook.conf 000-checkbook.conf

    Don't forget to restart Apache:

        $ sudo service apache2 restart

14.  Verify that the new Checkbook site is working:

 * Test the site by accessing the root URL.

   The default page should be the spending transactions page for
   current fiscal year.

 * Click on search button and should see the search results page.

 * Start typing any name in the search box and autocomplete results
   should appear.

 * Click on advanced search and click on submit. results page should appear.

 * Click on export in advances search results page to verfiy that
    export is working.

Troubleshooting
---------------

We're slowly collecting troubleshooting tips based on people's
installation experiences in the real world.  If you encounter a new
problem, please raise it in one of the feedback forums (e.g., as a
GitHub issue or as a post in the discussion group -- see README.md),
and once we've figured out the solution we'll list it here too.

  * Installation on a Virtual Machine.

    https://github.com/NYCComptroller/Checkbook/issues/26#issuecomment-34296699
    describes how user @sapariduo installed CheckbookNYC on a virtual
    machine, with the Host machine running Windows 7 and the Virtual
    Machine running Centos 6.4.

    To do this, he set up 2 virtual networks.  1 (NAT) used DHCP from
    the host machine, therefore having the same subnet address as the
    Host.  The other virtual network used a static IP address ("Host
    Only Configuration"), for the Checkbook server on CentOS 6.4.  The
    objective of this configuration was to allow the CentOS machine to
    be able to connect with the external network, so that a very
    minimal CentOS installation could be used without any need for any
    desktop features there, and allowing the Windows browser to
    connect to the Checkbook application on the CentOS VM.

    Here are some extra things he needed to do to make this work:

    - Set up or verify hostname of the server on
      `/etc/sysconfig/network` and put the static IP address and
      hostname on `/etc/hosts`.

    - In `/etc/httpd/conf/httpd.conf`:

      1. Find entry "ServerName", untag and set parameter with
         yourdomain:80 or yourIP:80

      2. Find entry "NameVirtualHost", untag and set parameter with *:80

      3. Set VirtualHost Configuration and use your domain or your IP
         on ServerName element

    - Configuration needed for PostgreSQL to make Solr able to connect:

      1. Change parameter on `/var/lib/pgsql/9.3./data/pg_hba.conf`:
         "local all all ident" --> "local all all trust"

      Then monitor whether Solr is able to connect with Postgres, from
      `/opt/apache-tomcat-6.0.35/logs/catalina.out`.  If there is still
      an error in the JDBC Postgres connection, try changeing this
      parameter too:
      "host all all 127.0.0.1/32 ident" --> "host all all 127.0.0.1/32 trust"

    Then restart HTTPD and Tomcat:

    `service httpd restart` 

    `./bin/shutdown.sh` (from within the apache-tomcat directory)
