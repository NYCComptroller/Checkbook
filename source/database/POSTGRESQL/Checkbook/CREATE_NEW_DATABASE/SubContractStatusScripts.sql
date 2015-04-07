/*
Functions defined
	processFMSVVendorBusType
*/
CREATE OR REPLACE FUNCTION etl.processSubConStatus(p_load_file_id_in int,p_load_id_in bigint) RETURNS INT AS $$
DECLARE
	rec_count int;
BEGIN	
	
	--  processing data to insert/update in subcontract_vendor_business_type table
	
	UPDATE etl.stg_scntrc_status SET vendor_cust_cd = 'N/A' WHERE vendor_cust_cd ='N/A (PRIVACY/SECURITY)';
	
	CREATE TEMPORARY TABLE tmp_scntrc_status(uniq_id bigint, doc_cd varchar, doc_dept_cd varchar, doc_id varchar,  action_flag char(1))
	;
	
	INSERT INTO tmp_scntrc_status(uniq_id, doc_cd, doc_dept_cd,	doc_id, action_flag)
	SELECT MAX(uniq_id) as uniq_id, doc_cd, doc_dept_cd,	doc_id,  'I' as action_flag
	FROM etl.stg_scntrc_status 
	GROUP BY 2,3,4,5;
	
	UPDATE tmp_scntrc_status a
	SET action_flag = 'U'
	FROM subcontract_status b 
	WHERE a.doc_cd || a.doc_dept_cd || a.doc_id = b.contract_number ;
	
	
	
	INSERT INTO subcontract_status(contract_number,vendor_customer_code, scntrc_status, agreement_type_id, total_scntrc_max_am, total_scntrc_pymt_am, created_load_id, created_date)
    	SELECT  a.doc_cd || a.doc_dept_cd || a.doc_id as contract_number, vendor_cust_cd as vendor_customer_code, scntrc_sta as scntrc_status,
    	cntrc_typ as agreement_type_id, tot_scntrc_max_am as total_scntrc_max_am, tot_scntrc_pymt as total_scntrc_pymt_am,  p_load_id_in as created_load_id, now()::timestamp
    	FROM	etl.stg_scntrc_status a JOIN tmp_scntrc_status b ON a.uniq_id = b.uniq_id 
    	WHERE b.action_flag = 'I';
    
    
    	GET DIAGNOSTICS rec_count = ROW_COUNT;	
	
	INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
	VALUES(p_load_file_id_in,'SS',rec_count, '# of records inserted into subcontract_status');
	
	
UPDATE 	subcontract_status a
    SET vendor_customer_code = b.vendor_cust_cd,
    scntrc_status = b.scntrc_sta,
    agreement_type_id = b.cntrc_typ,
    total_scntrc_max_am = b.tot_scntrc_max_am,
    total_scntrc_pymt_am = b.tot_scntrc_pymt,
    updated_load_id = p_load_id_in,
    updated_date = now()::timestamp
    FROM tmp_scntrc_status d JOIN etl.stg_scntrc_status b ON d.uniq_id = b.uniq_id 
    WHERE a.contract_number =  b.doc_cd || b.doc_dept_cd || b.doc_id
    AND d.action_flag = 'U';
		
	GET DIAGNOSTICS rec_count = ROW_COUNT;	
	
	INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
	VALUES(p_load_file_id_in,'SS',rec_count, '# of records updated into subcontract_status');
	
	RETURN 1;
	
EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in processSubConStatus';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;	

	RETURN 0;
		
END;
$$ language plpgsql;