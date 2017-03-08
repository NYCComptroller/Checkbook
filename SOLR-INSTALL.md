Solr Installation for Checkbook NYC
-----------------------------------

Checkbook NYC runs Solr inside Tomcat, and comes with both of them
pre-packaged for direct installation by copying, as described below.

1. Copy the Solr and Tomcat directories to the right places.

          $ sudo cp -a software/solr-4.1.0 /opt/solr-4.1.0
          $ sudo cp -a software/apache-tomcat-6.0.35 /opt/apache-tomcat-6.0.35

 *Note: you may need to create the `/opt` directory first on some systems.*

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

4. Set the PostgreSQL connection authentication configuration:

 Open up the PostgreSQL configuration file (a file somewhere
 like `/etc/postgresql/9.1/main/pg_hba.conf` if Ubuntu 12.04 or
 `/var/lib/pgsql/9.3/data/pg_hba.conf` if CentOS 6.4) and apply the 
 changes listed below: 
 
# 'local' is for Unix domain socket connections only

       local     all         all              md5
       
# IPv4 local connections: 

       host      all       0.0.0.0/0          md5
       
# IPv6 local connection: 

       host      all          all             md5
       

 *Note: This configuration is set so any user can log into the database 
 providing the username: localhost, postgres, etc. with the password: postgres. 
 This configuration is simply for initial setup and can be customized to your 
 preference. 
 
 *Note: Again, that "postgres" user is the main PG database user, and
 changing its authentication mechanism could affect up other things.
 The long-term solution is for Checkbook to have its own PG user.*

5. Then restart PostgreSQL:
          
          $ sudo service postgresql restart

 Verify that the user postgres can connect to the checkbook database with
 the following command. 

          $ PGPASSWORD=postgres psql -U postgres checkbook
          
6. Next, verify that the localhost user can connect to the checkbook database 
   with password authentication. Later SOLR will connect to the database
   using U-localhost and -PGPASSWORD=postgres. The following command will 
   verify if that connection is succesfull. 

          $ PGPASSWORD=postgres psql -U localhost checkbook

 The expected result is something like this:

          psql (9.3.5)
          Type "help" for help.
          checkbook=# \q  (to quit)

7. Tell Solr how to connect to PostgreSQL:

 Edit `/opt/solr-4.1.0/indexes/solr/collection1/conf/db-config.xml`
 to insert the correct database details.  There is no line break or
 backslash here; the backslash just indicates line continuation:
          
          url="jdbc:postgresql://localhost/checkbook" \
          user="postgres" password="postgres"

 *Note: Actually, you may not need to edit it if you're just testing,
 as the shipped file has the username "postgres" with password
 "postgres", for testing.  However, in a production environment those
 would be different, and this is where you would need to set them.*

8. Start Solr inside Tomcat:

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

 * Note that if another Tomcat application is running on your machine,
   you may need to kill the existing Tomcat process and then run
   startup.sh to avoid "Address already in use" errors.

9. Start Solr indexing.

 Visit this url in a browser start indexing:
 <http://localhost:8080/solr-checkbook/dataimport?command=full-import&clean=true&jobID=0>

 You can monitor the progress of indexing by repeatedly visiting
 <http://localhost:8080/solr-checkbook/dataimport>.

 Detailed logs for troubleshooting errors can be found at
 </opt/apache-tomcat-6.0.35/logs/>.

 *Note: You may need to open up the server's firewall to enable a web
 browser to reach port 8080.  Firewall configuration varies widely
 from system to system, so we cannot document all the possibilities
 here, but if it's iptables, then `iptables -F` should flush all
 existing firewall rules.  You probably wouldn't want to do that on a
 production server, but that approach might make sense for a test
 instance, especially one running in a virtual machine.*
