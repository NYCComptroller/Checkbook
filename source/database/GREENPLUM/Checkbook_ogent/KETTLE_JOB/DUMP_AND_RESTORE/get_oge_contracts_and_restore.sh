cd /home/gpadmin/GREENPLUM/Checkbook_ogent/KETTLE_JOB/DUMP_AND_RESTORE
rm -rf checkbook_oge_oge_*.sql
pg_dump -a -t oge_contract database_name2 > checkbook_oge_oge_contract.sql 2>checkbook_oge_oge_contract_dump.err
psql -d database_name1 -f Truncate_CB_oge_contract.sql 1>Truncate_CB_oge_contract.out 2>Truncate_CB_oge_contract.err
psql -d database_name1 -f checkbook_oge_oge_contract.sql 1>checkbook_oge_oge_contract_restore.out 2>checkbook_oge_oge_contract_restore.err
