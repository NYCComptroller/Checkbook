Solr Installation for Checkbook NYC
-----------------------------------

1. Move directories to the right places and create the log directory:

          $ sudo cp -a software/solr-4.1.0 /opt/solr-4.1.0
          $ sudo cp -a software/apache-tomcat-6.0.35 /opt/apache-tomcat-6.0.35
          $ sudo mkdir /opt/apache-tomcat-6.0.35/logs

   (TODO: Does the last line above mean that the logs directory is
   only writeable by Tomcat if Tomcat is running as root?  Er, we
   think so.  That is probably not a good long-term plan :-) ).

2. Check that Solr will be able to connect to the PostgreSQL DB:

          $ sudo -u postgres psql -c "ALTER USER postgres WITH PASSWORD '<THE_PASSWORD>';"

3. Update the Solr database settings:

   This involves updating `/opt/solr-4.1.0/indexes/solr/collection1/conf/db-config.xml` 
   with the correct username, password and db name for the PostgreSQL database.  

   (TODO: These are "postgres", "postgres" and "checkbook", a
   situation which needs to get fixed.)

   Next, edit the PostgreSQL configuration file (the file path will be
   something like `/etc/postgresql/9.1/main/pg_hba.conf`) to change
   this line

          `local  all        postgres          peer`

   to this:

          `local  all        postgres          md5`

   (TODO: again, that "postgres" is the main PG database user, and
   changing its authentication mechanism could screw up other things.)

   Then restart PostgreSQL:

          $ sudo service postgresql restart

   Now PostgreSQL can accept password authentication.  You can use
   this command to test that it worked:

          $ PGPASSWORD=<THE_PASSWORD> psql -U <POSTGRES_DB_USER> <POSTGRES_DB_NAME>

   TODO: expected result:

          psql (9.1.9)
          Type "help" for help.
          checkbook=# \q  (to quit)

   Next edit `/opt/solr-4.1.0/indexes/solr/collection1/conf/db-config.xml` 
   to insert the correct database details.  There is no line break or
   backslash here; the backslash just indicates line continuation:
          
          url="jdbc:postgresql://localhost/checkbook" \
          user="_POSTGRES_DB_USER_" password="_THE_PASSWORD_"

   (TODO: user and password are both "postgres" in the shipped file,
   but this is a bad state of affairs -- it needs to be templatized.)

4. Start Solr inside Tomcat:

   To run Tomcat, you'll need a Java runtime environment.  If your
   system doesn't already have one, you can install it like this under
   Ubuntu 12.04:

          $ sudo apt-get update
          $ sudo apt-get install openjdk-6-jre-headless

   Now that Java is installed, start up Tomcat:

          $ cd /opt/apache-tomcat-6.0.35/bin
          $ sudo ./startup.sh

   That starts Tomcat; visit `http://localhost:8080/solr-checkbook/`
   in a browser to verify that Solr is enabled now too.

   (For troubleshooting errors, `/opt/apache-tomcat-6.0.35/logs` has
   detailed logs.)

   TODO: the flags "-Xms7130M -Xmx7130M" in the CATALINA_OPTS env var in 
   ~/src/checkbook/Checkbook/software/apache-tomcat-6.0.35/bin/catalina.sh
   ask for too much memory for most people just playing in a sandbox.
   We reduced to 512M and it was fine; when it was 7130, we got errors
   in `/opt/apache-tomcat-6.0.35/logs/catalina.out`:

           Error occurred during initialization of VM
           Could not reserve enough space for object heap

5. Start indexing.

 - Go to this url to restart reindexing:

          http://localhost:8080/solr-checkbook/dataimport?command=full-import&clean=true&jobID=0

6. Monitor indexing

 - Monitor the progress at `http://localhost:8080/solr-checkbook/dataimport `
 - Detailed logs for troubleshooting errors can be found at `/opt/apache-tomcat-6.0.35/logs`.
