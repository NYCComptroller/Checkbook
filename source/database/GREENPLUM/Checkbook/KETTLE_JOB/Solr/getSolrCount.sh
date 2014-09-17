cd /home/gpadmin/GREENPLUM/Checkbook/KETTLE_JOB/Solr

wget -O solr_count.xml "http://hostname:port/solrCoreName/select/?q=*%3A*&version=2.2&start=0&rows=10&indent=on"

wget -O solr_count_di.xml "http://hostname:port/solrCoreName/dataimport/"
