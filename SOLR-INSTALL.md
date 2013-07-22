Solr Installation for Checkbook NYC
-----------------------------------

Checkbook NYC runs Solr inside Tomcat, and comes with both of them
pre-packaged for direct installation by copying, as described below.

1. Copy the Solr and Tomcat directories to the right places.

          $ sudo cp -a software/solr-4.1.0 /opt/solr-4.1.0
          $ sudo cp -a software/apache-tomcat-6.0.35 /opt/apache-tomcat-6.0.35

2. Create the Tomcat log directory.

          $ sudo mkdir /opt/apache-tomcat-6.0.35/logs

 *Note: as the above implies, Tomcat will be running as `root` and
 writing to its log directory as `root`.  This is not ideal; probably
 Tomcat should run as `www-data`.  However, we have not tested that
 configuration yet, so for now we are documenting running as `root`.*

3. Ensure that Solr will be able to connect to the PostgreSQL DB.

          $ sudo -u postgres psql -c "ALTER USER postgres WITH PASSWORD 'postgres';"

 *Note: Hmm, this step does not really belong in the Solr installation
 instructions.  It's really about setting the postgres DB user's
 password, and therefore should be documented in INSTALL.md.*

4. Update the Solr database settings:

 Update `/opt/solr-4.1.0/indexes/solr/collection1/conf/db-config.xml`
 with the correct username, password and db name for the PostgreSQL
 database.  By default, they are "`postgres`", "`postgres`" and
 "`checkbook`" respectively.

 Next, open up the PostgreSQL configuration file (a file somewhere
 like `/etc/postgresql/9.1/main/pg_hba.conf`) and change this line

          `local  all        postgres          peer`

 to this:

          `local  all        postgres          md5`

 *Note: Again, that "postgres" user is the main PG database user, and
 changing its authentication mechanism could affect up other things.
 The long-term solution is for Checkbook to have its own PG user.*

 Then restart PostgreSQL:

          $ sudo service postgresql restart

 Now PostgreSQL can accept password authentication.  You can use this
 command to test that it worked:

          $ PGPASSWORD=postgres psql -U postgres checkbook

 The expected result is something like this:

          psql (9.1.9)
          Type "help" for help.
          checkbook=# \q  (to quit)

 Next edit `/opt/solr-4.1.0/indexes/solr/collection1/conf/db-config.xml`
 to insert the correct database details.  There is no line break or
 backslash here; the backslash just indicates line continuation:
          
          url="jdbc:postgresql://localhost/checkbook" \
          user="postgres" password="postgres"

 *Note: Actually, you may not need to edit it if you're just testing,
 as the shipped file has the username "postgres" with password
 "postgres", for testing.  However, in a production environment those
 would be different, and this is where you would need to set them.*

5. Start Solr inside Tomcat:

 To run Tomcat, you'll need a Java runtime environment.  If your
 system doesn't already have one, you can install it like this under
 Ubuntu 12.04:

          $ sudo apt-get update
          $ sudo apt-get install openjdk-6-jre-headless

 Now that Java is installed, start up Tomcat:

          $ cd /opt/apache-tomcat-6.0.35/bin
          $ sudo ./startup.sh

 That starts up Tomcat, and Solr within Tomcat.  Visit
 <http://localhost:8080/solr-checkbook/> in a browser to verify that
 Solr is now running.  For troubleshooting errors, see the detailed
 logs in `/opt/apache-tomcat-6.0.35/logs/`.

6. Start Solr indexing.

 Visit this url in a browser start indexing:
 <http://localhost:8080/solr-checkbook/dataimport?command=full-import&clean=true&jobID=0>

 You can monitor the progress of indexing by repeatedly visiting
 <http://localhost:8080/solr-checkbook/dataimport>.

 Detailed logs for troubleshooting errors can be found at
 </opt/apache-tomcat-6.0.35/logs/>.
