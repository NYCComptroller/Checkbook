Solr Installation for Checkbook NYC
-----------------------------------

_Note: This is just a first and very rough pass at documenting Solr
installation for Checkbook.  This document probably needs improvement;
the more feedback we get, the better it will be._

1. Move directories to the right place

 - copy `software/apache-tomcat-6.0.35` to `/opt/apache-tomcat-6.0.35`
 - copy `software/solr-4.1.0` to `/opt/solr-4.1.0`

2. Update solr database settings.

 - Update /opt/solr-4.1.0/indexes/solr/collection1/conf/db-config.xml 
   with correct username, password and schema for the PostgreSQL database.

 - update second line of
   `/opt/solr-4.1.0/indexes/solr/collection1/conf/db-config.xml` with
   correct database details (there is no line break or backslash here;
   the backslash just indicates line continuation):
          
              url="jdbc:postgresql://localhost/<database schema name>" \
              user="<database username>" password="<database password>"

3. Start Solr:

 - Change to directory `/opt/apache-tomcat-6.0.35/bin`, type
   `./startup.sh`, and hit Enter to start Solr. 
 - Go to `http://localhost:8080/solr-checkbook/` to see that Solr has
   started.
 - Detailed logs for troubleshooting errors can be found at
   `/opt/apache-tomcat-6.0.35/logs`.

4. Start indexing.

 - Go to this url to restart reindexing:

              http://localhost:8088/solr-checkbook/dataimport?command=full-import&clean=true&jobID=0

5. Monitor indexing

 - Monitor the progress at `http://localhost:8080/solr-checkbook/dataimport `
 - Detailed logs for troubleshooting errors can be found at `/opt/apache-tomcat-6.0.35/logs`.
