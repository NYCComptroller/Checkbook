cd /home/gpadmin/POSTGRESQL/Checkbook_ogent/KETTLE_JOB/DUMP_AND_RESTORE
rm -rf checkbook_db_*.sql
pg_dump -a -t 'ref_*' -t 'seq_ref_*' -t fmsv_business_type database_name1 > checkbook_db_reference_data.sql 2>dump_ref_data.err 
pg_dump -a -t disbursement_edc -t disbursement_line_item_edc -t disbursement_line_item_details_edc -t history_agreement_edc -t history_agreement_accounting_line_edc -t history_master_agreement_edc -t agreement_snapshot_edc -t agreement_snapshot_cy_edc -t agreement_snapshot_expanded_edc -t agreement_snapshot_expanded_cy_edc -t vendor_edc -t vendor_history_edc -t vendor_address_edc -t vendor_business_type_edc -t address_edc -t pending_contracts_edc database_name1 > checkbook_db_transaction_data.sql 2>dump_transaction_data.err 
psql -d database_name2 -f TruncateTables.sql 1>truncate_data.out 2>truncate_data.err
psql -d database_name2 -f checkbook_db_reference_data.sql 1>restore_ref_data.out 2>restore_ref_data.err
psql -d database_name2 -f checkbook_db_transaction_data.sql 1>restore_transaction_data.out 2>restore_transaction_data.err
