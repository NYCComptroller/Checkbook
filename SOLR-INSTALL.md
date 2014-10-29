Solr Installation for Checkbook NYC
-----------------------------------

Checkbook NYC runs Solr inside Tomcat, and comes with both of them
pre-packaged for direct installation by copying, as described below.

**Requirements**

Apache Tomcat requires the java runtime environment.  

**Installation Steps**

1. Copy the Solr and Tomcat directories from the Checkbook project to your system.

          $ sudo cp -a software/solr-4.1.0 /opt/solr-4.1.0
          $ sudo cp -a software/apache-tomcat-6.0.35 /opt/apache-tomcat-6.0.35

 *Note: you may need to create the `/opt` directory first on some systems.*

2. Create the Tomcat log directory.

          $ sudo mkdir /opt/apache-tomcat-6.0.35/logs

 *Note: as the above implies, Tomcat will be running as `root` and
 writing to its log directory as `root`.  This is not ideal; probably
 Tomcat should run as `www-data` or `apache`.  However, we have not tested that
 configuration yet, so for now we are documenting running as `root`.*
 
3. Set the PostgreSQL connection authentication configuration.

 Open up the PostgreSQL configuration file (a file somewhere
 like `/etc/postgresql/9.3/main/pg_hba.conf` if Ubuntu 12.04 or
 `/var/lib/pgsql/9.3/data/pg_hba.conf` if CentOS 6.4) and apply the 
 changes listed below: 
 
 **'local' is for Unix domain socket connections only**

        local     all         all              md5

 **IPv4 local connections: **

        host      all       0.0.0.0/0          md5

 **IPv6 local connection: **

        host      all          all             md5
       
 *Note:  This configuration is simply for initial setup and can be customized to your 
 preference. The "postgres" user is the main database user, and
 changing its authentication mechanism could affect up other things.
 The long-term solution is for Checkbook to have its own user.*

4. Restart PostgreSQL.
          
          $ sudo service postgresql-9.3 restart

5. Verify that the user postgres can connect to the checkbook database with
 the following commands.  `<host>` can be replace with `localhost` 
 or ip address. 

          $ PGPASSWORD=postgres psql -U postgres checkbook
          $ PGPASSWORD=postgres psql -U postgres -h <host> checkbook
          
 The expected result is something like this:

          psql (9.3.5)
          Type "help" for help.
          checkbook=# \q  (to quit)

6. Configure Solr's PostgreSQL configuration.

 Edit `/opt/solr-4.1.0/indexes/solr/collection1/conf/db-config.xml`
to look something like this in the `<dataSource>` tags:
          
          url="jdbc:postgresql://localhost/checkbook" 
          user="postgres" password="postgres"
          
 *Note: In a production environment the default username and 
password of "postgres" should be changed to something more suitable.  

7. Start Solr inside Tomcat.

 To start up Tomcat:

          $ cd /opt/apache-tomcat-6.0.35/bin
          $ sudo ./startup.sh

  Then visit <http://localhost:8080/solr-checkbook/> in a browser to verify that
 Solr is now running.  For troubleshooting errors, see the detailed
 logs in `/opt/apache-tomcat-6.0.35/logs/`.

 *Note: On systems with less memory, like 32 bit systems, you may need to modify the java memory settings `Xms7130M -Xmx7130M` in `/opt/apache-tomcat-6.0.35/bin/catalina.sh`.

8. Start Solr indexing.

 Visit this url in a browser start indexing:
 <http://localhost:8080/solr-checkbook/dataimport?command=full-import&clean=true&jobID=0>

 You can monitor the progress of indexing by repeatedly visiting
 <http://localhost:8080/solr-checkbook/dataimport>.

 Detailed logs for troubleshooting errors can be found at
 `/opt/apache-tomcat-6.0.35/logs/`.

 *Note: You may need to open up the server's firewall to enable a web
 browser to reach port 8080.  Firewall configuration varies widely
 from system to system, so we cannot document all the possibilities
 here, but if it's iptables, then `iptables -F` should flush all
 existing firewall rules.  You probably wouldn't want to do that on a
 production server, but that approach might make sense for a test
 instance, especially one running in a virtual machine.*
