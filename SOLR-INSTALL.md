Solr Installation for Checkbook NYC
-----------------------------------

1. Move directories to the right places:

          $ sudo cp -a software/apache-tomcat-6.0.35 /opt/apache-tomcat-6.0.35
          $ sudo cp -a software/solr-4.1.0 /opt/solr-4.1.0

2. Update Solr database settings.

   This involves updating `/opt/solr-4.1.0/indexes/solr/collection1/conf/db-config.xml` 
   with the correct username, password and db name for the PostgreSQL database.  

   But in order to do that, you should first make sure that Solr will be able to
   connect to the PostgreSQL DB:

          $ sudo -u postgres psql -c "ALTER USER postgres WITH PASSWORD 'THE_PASSWORD';"

   Next, edit the PostgreSQL configuration file (the file path will be
   something like `/etc/postgresql/9.1/main/pg_hba.conf`) to change
   this line

          `local  all        postgres          peer`

   to this:

          `local  all        postgres          md5`

   Then restart PostgreSQL:

          $ sudo service postgresql restart

   Now PostgreSQL can accept password authentication.  You can use
   this command to test that it worked:

          $ PGPASSWORD=_THE_PASSWORD_ psql -U _POSTGRES_DB_USER_ _POSTGRES_DB_NAME_

   Next edit `/opt/solr-4.1.0/indexes/solr/collection1/conf/db-config.xml` 
   to insert the correct database details.  There is no line break or
   backslash here; the backslash just indicates line continuation:
          
          url="jdbc:postgresql://localhost/checkbook" \
          user="_POSTGRES_DB_USER_" password="_THE_PASSWORD_"

3. Start Solr inside Tomcat:

   To run Tomcat, you'll need a Java runtime environment.  If your
   system doesn't already have one, you can install it like this under
   Ubuntu 12.04:

          $ sudo apt-get update
          $ sudo apt-get install openjdk-6-jre

   Now that Java is installed, start up Tomcat:

          $ cd /opt/apache-tomcat-6.0.35/bin
          $ sudo ./startup.sh

   That starts Tomcat; visit `http://localhost:8080/solr-checkbook/`
   in a browser to verify that Solr is enabled now too.

   (For troubleshooting errors, `/opt/apache-tomcat-6.0.35/logs` has
   detailed logs.)

4. Start indexing.

 - Go to this url to restart reindexing:

          http://localhost:8088/solr-checkbook/dataimport?command=full-import&clean=true&jobID=0

5. Monitor indexing

 - Monitor the progress at `http://localhost:8080/solr-checkbook/dataimport `
 - Detailed logs for troubleshooting errors can be found at `/opt/apache-tomcat-6.0.35/logs`.
