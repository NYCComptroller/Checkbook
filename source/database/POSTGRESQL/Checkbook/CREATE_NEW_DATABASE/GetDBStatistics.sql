/*
•	Size of the database: 
•	Total number of Transactions data:  
•	Average Number of Transactions inserted/updated in a month: 
•	Average Number of Transactions inserted/updated every day:  
•	Total number of rows in all the tables, including reference tables:
•	Total number of tables in the database: 


Size of the database : 140 GB
Total number of Transactions data :  59,135,812
Average Number of Transactions inserted/updated in a month : 1,548,000
Average Number of Transactions inserted/updated every day :  92,000

*/

DROP TABLE IF EXISTS etl.database_statistics ;

CREATE TABLE etl.database_statistics (
statistics_date date,
db_size varchar,
total_transactions_data bigint,
avg_transactions_per_month bigint,
avg_transactions_per_day bigint,
total_rows_in_db bigint,
total_tables_in_db int
) ;


 
 CREATE OR REPLACE FUNCTION etl.getdatabasestatistics()
  RETURNS integer AS
$BODY$
DECLARE
	l_tables RECORD;
	l_count_rec RECORD;
	l_count bigint;
	l_total_count bigint;
	l_grant_str varchar;
	l_db_size varchar ;
	l_total_transactions bigint;
	l_total_archive_data bigint;
	l_total_months numeric(10,4);
	l_avg_transactions_per_month bigint;
	l_last_one_month_archive_data bigint;
	l_avg_transactions_per_day bigint;
	l_total_db_tables int;
	
	
BEGIN

	SELECT  pg_size_pretty(sodddatsize) as db_size FROM gp_toolkit.gp_size_of_database where sodddatname = 'checkbook' INTO l_db_size ;

	RAISE notice 'l_db_size %',l_db_size;
	
	SELECT sum(total) as total_transactions_data FROM (
 SELECT count(*) as total  FROM vendor_history UNION ALL  
 SELECT count(*) as total  FROM vendor_business_type UNION ALL  
 SELECT count(*) as total  FROM revenue_budget UNION ALL  
 SELECT count(*) as total  FROM revenue UNION ALL  
 SELECT count(*) as total  FROM budget UNION ALL 
 SELECT count(*) as total  FROM pending_contracts UNION ALL  
 SELECT count(*) as total  FROM payroll_summary UNION ALL  
 SELECT count(*) as total  FROM payroll_future_data UNION ALL  
 SELECT count(*) as total  FROM payroll UNION ALL     
 SELECT count(*) as total  FROM invalid_records UNION ALL  
 SELECT count(*) as total  FROM history_master_agreement UNION ALL  
 SELECT count(*) as total  FROM history_agreement_worksite UNION ALL  
 SELECT count(*) as total  FROM history_agreement_commodity UNION ALL  
 SELECT count(*) as total  FROM history_agreement_accounting_line UNION ALL  
 SELECT count(*) as total  FROM history_agreement UNION ALL  
 SELECT count(*) as total  FROM fmsv_business_type UNION ALL 
 SELECT count(*) as total  FROM employee_history UNION ALL  
 SELECT count(*) as total  FROM address UNION ALL
 SELECT count(*) as total  FROM disbursement_line_item_deleted UNION ALL  
 SELECT count(*) as total  FROM disbursement_line_item UNION ALL  
 SELECT count(*) as total  FROM disbursement UNION ALL  
 SELECT count(*) as total  FROM deleted_agreement_accounting_line) X INTO l_total_transactions;

RAISE notice 'l_total_transactions %',l_total_transactions;

 SELECT sum(total) as total_archive_data_avg_month FROM (
  select count(*)  as total from etl.archive_budget a JOIN etl.etl_data_load_file b ON a.load_file_id = b.load_file_id JOIN etl.etl_data_load c ON b.load_id =c.load_id WHERE b.processed_flag = 'Y' UNION ALL
  select count(*)  as total from etl.archive_revenue_budget a JOIN etl.etl_data_load_file b ON a.load_file_id = b.load_file_id JOIN etl.etl_data_load c ON b.load_id =c.load_id WHERE b.processed_flag = 'Y' UNION ALL
  select count(*)  as total from etl.archive_revenue a JOIN etl.etl_data_load_file b ON a.load_file_id = b.load_file_id JOIN etl.etl_data_load c ON b.load_id =c.load_id WHERE b.processed_flag = 'Y' UNION ALL
  select count(*)  as total from etl.archive_con_ct_accounting_line a JOIN etl.etl_data_load_file b ON a.load_file_id = b.load_file_id JOIN etl.etl_data_load c ON b.load_id =c.load_id WHERE b.processed_flag = 'Y' UNION ALL
  select count(*)  as total from etl.archive_con_ct_award_detail a JOIN etl.etl_data_load_file b ON a.load_file_id = b.load_file_id JOIN etl.etl_data_load c ON b.load_id =c.load_id WHERE b.processed_flag = 'Y' UNION ALL
  select count(*)  as total from etl.archive_con_ct_header a JOIN etl.etl_data_load_file b ON a.load_file_id = b.load_file_id JOIN etl.etl_data_load c ON b.load_id =c.load_id WHERE b.processed_flag = 'Y' UNION ALL
  select count(*)  as total from etl.archive_con_ct_vendor a JOIN etl.etl_data_load_file b ON a.load_file_id = b.load_file_id JOIN etl.etl_data_load c ON b.load_id =c.load_id WHERE b.processed_flag = 'Y' UNION ALL
  select count(*)  as total from etl.archive_con_do1_accounting_line a JOIN etl.etl_data_load_file b ON a.load_file_id = b.load_file_id JOIN etl.etl_data_load c ON b.load_id =c.load_id WHERE b.processed_flag = 'Y' UNION ALL
  select count(*)  as total from etl.archive_con_do1_header a JOIN etl.etl_data_load_file b ON a.load_file_id = b.load_file_id JOIN etl.etl_data_load c ON b.load_id =c.load_id WHERE b.processed_flag = 'Y' UNION ALL
  select count(*)  as total from etl.archive_con_do1_vendor a JOIN etl.etl_data_load_file b ON a.load_file_id = b.load_file_id JOIN etl.etl_data_load c ON b.load_id =c.load_id WHERE b.processed_flag = 'Y' UNION ALL
  select count(*)  as total from etl.archive_con_po_accounting_line a JOIN etl.etl_data_load_file b ON a.load_file_id = b.load_file_id JOIN etl.etl_data_load c ON b.load_id =c.load_id WHERE b.processed_flag = 'Y' UNION ALL
  select count(*)  as total from etl.archive_con_po_award_detail a JOIN etl.etl_data_load_file b ON a.load_file_id = b.load_file_id JOIN etl.etl_data_load c ON b.load_id =c.load_id WHERE b.processed_flag = 'Y' UNION ALL
  select count(*)  as total from etl.archive_con_po_header a JOIN etl.etl_data_load_file b ON a.load_file_id = b.load_file_id JOIN etl.etl_data_load c ON b.load_id =c.load_id WHERE b.processed_flag = 'Y' UNION ALL
 select count(*)  as total from etl.archive_con_po_vendor a JOIN etl.etl_data_load_file b ON a.load_file_id = b.load_file_id JOIN etl.etl_data_load c ON b.load_id =c.load_id WHERE b.processed_flag = 'Y' UNION ALL
 select count(*)  as total from etl.archive_mag_award_detail a JOIN etl.etl_data_load_file b ON a.load_file_id = b.load_file_id JOIN etl.etl_data_load c ON b.load_id =c.load_id WHERE b.processed_flag = 'Y' UNION ALL
 select count(*)  as total from etl.archive_mag_header a JOIN etl.etl_data_load_file b ON a.load_file_id = b.load_file_id JOIN etl.etl_data_load c ON b.load_id =c.load_id WHERE b.processed_flag = 'Y' UNION ALL
 select count(*)  as total from etl.archive_mag_vendor a JOIN etl.etl_data_load_file b ON a.load_file_id = b.load_file_id JOIN etl.etl_data_load c ON b.load_id =c.load_id WHERE b.processed_flag = 'Y' UNION ALL
 select count(*)  as total from etl.archive_pending_contracts a JOIN etl.etl_data_load_file b ON a.load_file_id = b.load_file_id JOIN etl.etl_data_load c ON b.load_id =c.load_id WHERE b.processed_flag = 'Y' UNION ALL
 select count(*)  as total from etl.archive_payroll a JOIN etl.etl_data_load_file b ON a.load_file_id = b.load_file_id JOIN etl.etl_data_load c ON b.load_id =c.load_id WHERE b.processed_flag = 'Y' UNION ALL
 select count(*)  as total from etl.archive_payroll_summary a JOIN etl.etl_data_load_file b ON a.load_file_id = b.load_file_id JOIN etl.etl_data_load c ON b.load_id =c.load_id WHERE b.processed_flag = 'Y' UNION ALL
 select count(*)  as total from etl.archive_fms_header a JOIN etl.etl_data_load_file b ON a.load_file_id = b.load_file_id JOIN etl.etl_data_load c ON b.load_id =c.load_id WHERE b.processed_flag = 'Y' UNION ALL
 select count(*)  as total from etl.archive_fms_vendor a JOIN etl.etl_data_load_file b ON a.load_file_id = b.load_file_id JOIN etl.etl_data_load c ON b.load_id =c.load_id WHERE b.processed_flag = 'Y' UNION ALL
 select count(*)  as total from etl.archive_fms_accounting_line a JOIN etl.etl_data_load_file b ON a.load_file_id = b.load_file_id JOIN etl.etl_data_load c ON b.load_id =c.load_id WHERE b.processed_flag = 'Y') X INTO l_total_archive_data;

 SELECT round((max(c.publish_start_time::date) - min(c.publish_start_time::date))/30.5,4) from etl.archive_con_ct_header a 
 JOIN etl.etl_data_load_file b ON a.load_file_id = b.load_file_id JOIN etl.etl_data_load c ON b.load_id =c.load_id WHERE b.processed_flag = 'Y' INTO l_total_months;

 SELECT round(l_total_archive_data/l_total_months) INTO l_avg_transactions_per_month;

 RAISE notice 'l_avg_transactions_per_month %',l_avg_transactions_per_month;

 SELECT sum(total) as total_archive_data_avg_day FROM (
  select count(*)  as total from etl.archive_budget a JOIN etl.etl_data_load_file b ON a.load_file_id = b.load_file_id JOIN etl.etl_data_load c ON b.load_id =c.load_id WHERE b.processed_flag = 'Y' AND c.publish_start_time::date >= (current_date - interval '1 month')::date UNION ALL
  select count(*)  as total from etl.archive_revenue_budget a JOIN etl.etl_data_load_file b ON a.load_file_id = b.load_file_id JOIN etl.etl_data_load c ON b.load_id =c.load_id WHERE b.processed_flag = 'Y'  AND c.publish_start_time::date >= (current_date - interval '1 month')::date UNION ALL
  select count(*)  as total from etl.archive_revenue a JOIN etl.etl_data_load_file b ON a.load_file_id = b.load_file_id JOIN etl.etl_data_load c ON b.load_id =c.load_id WHERE b.processed_flag = 'Y'  AND c.publish_start_time::date >= (current_date - interval '1 month')::date UNION ALL
  select count(*)  as total from etl.archive_con_ct_accounting_line a JOIN etl.etl_data_load_file b ON a.load_file_id = b.load_file_id JOIN etl.etl_data_load c ON b.load_id =c.load_id WHERE b.processed_flag = 'Y'  AND c.publish_start_time::date >= (current_date - interval '1 month')::date UNION ALL
  select count(*)  as total from etl.archive_con_ct_award_detail a JOIN etl.etl_data_load_file b ON a.load_file_id = b.load_file_id JOIN etl.etl_data_load c ON b.load_id =c.load_id WHERE b.processed_flag = 'Y'  AND c.publish_start_time::date >= (current_date - interval '1 month')::date UNION ALL
  select count(*)  as total from etl.archive_con_ct_header a JOIN etl.etl_data_load_file b ON a.load_file_id = b.load_file_id JOIN etl.etl_data_load c ON b.load_id =c.load_id WHERE b.processed_flag = 'Y'  AND c.publish_start_time::date >= (current_date - interval '1 month')::date UNION ALL
  select count(*)  as total from etl.archive_con_ct_vendor a JOIN etl.etl_data_load_file b ON a.load_file_id = b.load_file_id JOIN etl.etl_data_load c ON b.load_id =c.load_id WHERE b.processed_flag = 'Y'  AND c.publish_start_time::date >= (current_date - interval '1 month')::date UNION ALL
  select count(*)  as total from etl.archive_con_do1_accounting_line a JOIN etl.etl_data_load_file b ON a.load_file_id = b.load_file_id JOIN etl.etl_data_load c ON b.load_id =c.load_id WHERE b.processed_flag = 'Y'  AND c.publish_start_time::date >= (current_date - interval '1 month')::date UNION ALL
  select count(*)  as total from etl.archive_con_do1_header a JOIN etl.etl_data_load_file b ON a.load_file_id = b.load_file_id JOIN etl.etl_data_load c ON b.load_id =c.load_id WHERE b.processed_flag = 'Y'  AND c.publish_start_time::date >= (current_date - interval '1 month')::date UNION ALL
  select count(*)  as total from etl.archive_con_do1_vendor a JOIN etl.etl_data_load_file b ON a.load_file_id = b.load_file_id JOIN etl.etl_data_load c ON b.load_id =c.load_id WHERE b.processed_flag = 'Y'  AND c.publish_start_time::date >= (current_date - interval '1 month')::date UNION ALL
  select count(*)  as total from etl.archive_con_po_accounting_line a JOIN etl.etl_data_load_file b ON a.load_file_id = b.load_file_id JOIN etl.etl_data_load c ON b.load_id =c.load_id WHERE b.processed_flag = 'Y'  AND c.publish_start_time::date >= (current_date - interval '1 month')::date UNION ALL
  select count(*)  as total from etl.archive_con_po_award_detail a JOIN etl.etl_data_load_file b ON a.load_file_id = b.load_file_id JOIN etl.etl_data_load c ON b.load_id =c.load_id WHERE b.processed_flag = 'Y'  AND c.publish_start_time::date >= (current_date - interval '1 month')::date UNION ALL
  select count(*)  as total from etl.archive_con_po_header a JOIN etl.etl_data_load_file b ON a.load_file_id = b.load_file_id JOIN etl.etl_data_load c ON b.load_id =c.load_id WHERE b.processed_flag = 'Y'  AND c.publish_start_time::date >= (current_date - interval '1 month')::date UNION ALL
 select count(*)  as total from etl.archive_con_po_vendor a JOIN etl.etl_data_load_file b ON a.load_file_id = b.load_file_id JOIN etl.etl_data_load c ON b.load_id =c.load_id WHERE b.processed_flag = 'Y'  AND c.publish_start_time::date >= (current_date - interval '1 month')::date UNION ALL
 select count(*)  as total from etl.archive_mag_award_detail a JOIN etl.etl_data_load_file b ON a.load_file_id = b.load_file_id JOIN etl.etl_data_load c ON b.load_id =c.load_id WHERE b.processed_flag = 'Y'  AND c.publish_start_time::date >= (current_date - interval '1 month')::date UNION ALL
 select count(*)  as total from etl.archive_mag_header a JOIN etl.etl_data_load_file b ON a.load_file_id = b.load_file_id JOIN etl.etl_data_load c ON b.load_id =c.load_id WHERE b.processed_flag = 'Y'  AND c.publish_start_time::date >= (current_date - interval '1 month')::date UNION ALL
 select count(*)  as total from etl.archive_mag_vendor a JOIN etl.etl_data_load_file b ON a.load_file_id = b.load_file_id JOIN etl.etl_data_load c ON b.load_id =c.load_id WHERE b.processed_flag = 'Y'  AND c.publish_start_time::date >= (current_date - interval '1 month')::date UNION ALL
 select count(*)  as total from etl.archive_pending_contracts a JOIN etl.etl_data_load_file b ON a.load_file_id = b.load_file_id JOIN etl.etl_data_load c ON b.load_id =c.load_id WHERE b.processed_flag = 'Y'  AND c.publish_start_time::date >= (current_date - interval '1 month')::date UNION ALL
 select count(*)  as total from etl.archive_payroll a JOIN etl.etl_data_load_file b ON a.load_file_id = b.load_file_id JOIN etl.etl_data_load c ON b.load_id =c.load_id WHERE b.processed_flag = 'Y'  AND c.publish_start_time::date >= (current_date - interval '1 month')::date UNION ALL
 select count(*)  as total from etl.archive_payroll_summary a JOIN etl.etl_data_load_file b ON a.load_file_id = b.load_file_id JOIN etl.etl_data_load c ON b.load_id =c.load_id WHERE b.processed_flag = 'Y'  AND c.publish_start_time::date >= (current_date - interval '1 month')::date UNION ALL
 select count(*)  as total from etl.archive_fms_header a JOIN etl.etl_data_load_file b ON a.load_file_id = b.load_file_id JOIN etl.etl_data_load c ON b.load_id =c.load_id WHERE b.processed_flag = 'Y'  AND c.publish_start_time::date >= (current_date - interval '1 month')::date UNION ALL
 select count(*)  as total from etl.archive_fms_vendor a JOIN etl.etl_data_load_file b ON a.load_file_id = b.load_file_id JOIN etl.etl_data_load c ON b.load_id =c.load_id WHERE b.processed_flag = 'Y'  AND c.publish_start_time::date >= (current_date - interval '1 month')::date UNION ALL
 select count(*)  as total from etl.archive_fms_accounting_line a JOIN etl.etl_data_load_file b ON a.load_file_id = b.load_file_id JOIN etl.etl_data_load c ON b.load_id =c.load_id WHERE b.processed_flag = 'Y'  AND c.publish_start_time::date >= (current_date - interval '1 month')::date ) X  INTO l_last_one_month_archive_data;
  
 SELECT round(l_last_one_month_archive_data/23) INTO l_avg_transactions_per_day;

RAISE notice 'l_avg_transactions_per_day %',l_avg_transactions_per_day;

  select count(*) as total_tables from information_schema.tables where table_schema in ('etl','public') 
  and table_catalog = 'checkbook' and table_type = 'BASE TABLE' and table_name not ilike 'ext_stg%' INTO l_total_db_tables;
  
RAISE notice 'l_total_db_tables %',l_total_db_tables;

	l_total_count := 0;
	For l_tables IN  select table_schema || '.' || table_name as table_name from information_schema.tables where table_schema in ('etl','public') and table_catalog = 'checkbook' and table_type = 'BASE TABLE' and table_name not ilike 'ext_stg%' order by 1		      
	
	LOOP

		l_grant_str := 'SELECT count(*) as total FROM ' || l_tables.table_name  ;

		FOR l_count_rec IN EXECUTE l_grant_str
	LOOP
	l_count := l_count_rec.total ;
	l_total_count := l_total_count + l_count ;
	END LOOP ;
		

		RAISE notice 'l_grant_str %',l_grant_str;
		RAISE notice '% : %',l_tables.table_name,l_count;
		
	END LOOP;

	RAISE notice 'Total number of records : %',l_total_count;

		
	INSERT INTO etl.database_statistics(statistics_date, db_size, total_transactions_data, avg_transactions_per_month, avg_transactions_per_day, total_rows_in_db, total_tables_in_db)
	SELECT current_date, l_db_size, l_total_transactions, l_avg_transactions_per_month, l_avg_transactions_per_day, l_total_count, l_total_db_tables ;
	
	
	RETURN 1;
	
EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in getdatabasestatistics';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;	

	RETURN 0;
END;

$BODY$
  LANGUAGE 'plpgsql' VOLATILE;
ALTER FUNCTION etl.getdatabasestatistics() OWNER TO gpadmin;