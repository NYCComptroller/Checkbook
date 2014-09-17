/*
Functions defined
	processexternaldata

*/


CREATE OR REPLACE FUNCTION etl.loadDataFromForeignTables(p_load_file_id_in bigint)
  RETURNS integer AS $$
DECLARE
l_count bigint;
l_data_source_code etl.ref_data_source.data_source_code%TYPE;
l_load_id bigint;
l_start_time  timestamp;
l_end_time  timestamp;
BEGIN
	
	l_start_time := timeofday()::timestamp;
	
	SELECT b.data_source_code , a.load_id
	FROM   etl.etl_data_load_file a JOIN etl.etl_data_load b ON a.load_id = b.load_id	       
	WHERE  a.load_file_id = p_load_file_id_in     
	INTO   l_data_source_code, l_load_id;
	
	IF(l_data_source_code = 'ED') THEN
	
	TRUNCATE etl.ext_stg_edc_contract_data_feed;
	
	INSERT INTO etl.ext_stg_edc_contract_data_feed(
            agency_code, agency_name, department_code, department_name, contract_number, commodity_line, 
            edc_contract_number, is_sandy_related, purpose, budget_name, edc_registered_amount, contractor_name, contractor_address, contractor_city, contractor_state, contractor_zip)
    SELECT agency_code, agency_name, department_code, department_name, contract_number, commodity_line, 
            edc_contract_number, is_sandy_related, purpose, budget_name, edc_registered_amount, contractor_name, contractor_address, contractor_city, contractor_state, contractor_zip 
	FROM etl.foreign_tbl_edc_contract_data_feed;
	
	
	END IF;
	
	l_end_time := timeofday()::timestamp;
	INSERT INTO etl.etl_script_execution_status(load_file_id,script_name,completed_flag,start_time,end_time)
	VALUES(p_load_file_id_in,'etl.loadDataFromForeignTables',1,l_start_time,l_end_time);
	
	
	RETURN 1;
EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in loadDataFromForeignTables';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;	
	
	l_end_time := timeofday()::timestamp;
	INSERT INTO etl.etl_script_execution_status(load_file_id,script_name,completed_flag,start_time,end_time,errno,errmsg)
	VALUES(p_load_file_id_in,'etl.loadDataFromForeignTables',0,l_start_time,l_end_time,SQLSTATE,SQLERRM);

	RETURN 0;
END;
$$ LANGUAGE 'plpgsql' ;



