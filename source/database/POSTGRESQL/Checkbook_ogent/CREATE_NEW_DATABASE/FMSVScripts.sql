/*
Functions defined
	processFMSVVendorBusType
*/
CREATE OR REPLACE FUNCTION etl.processFMSVVendorBusType(p_load_file_id_in int,p_load_id_in bigint) RETURNS INT AS $$
DECLARE
	rec_count int;
BEGIN	
	TRUNCATE etl.ref_business_type_id_seq;
	
	INSERT INTO etl.ref_business_type_id_seq(uniq_id)
	SELECT uniq_id
	FROM (SELECT bus_typ,COUNT(b.business_type_id), MIN(a.uniq_id) as uniq_id
		FROM etl.stg_fmsv_business_type a LEFT JOIN ref_business_type b ON a.bus_typ = b.business_type_code		
		GROUP BY 1
		HAVING COUNT(business_type_id) =0 ) bus_type_table;
		
	INSERT INTO ref_business_type(business_type_id,business_type_code,business_type_name,created_date)
	SELECT b.business_type_id,a.bus_typ,'<Unknown Business Type>',now()::timestamp
	FROM	etl.stg_fmsv_business_type a JOIN  etl.ref_business_type_id_seq b ON a.uniq_id = b.uniq_id;
	
	TRUNCATE fmsv_business_type;	
	
	INSERT INTO fmsv_business_type(vendor_customer_code,business_type_id,status,
    				       minority_type_id,certification_start_date,certification_end_date, initiation_date)
    	SELECT  a.vend_cust_cd,b.business_type_id,a.bus_typ_sta,
    		a.min_typ,a.disp_cert_strt_dt,a.cert_end_dt,a.init_dt
    	FROM	etl.stg_fmsv_business_type a JOIN ref_business_type b ON a.bus_typ = b.business_type_code;
		
	GET DIAGNOSTICS rec_count = ROW_COUNT;	
	
	INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
	VALUES(p_load_file_id_in,'V',rec_count, '# of records inserted into fmsv_business_type');
	
	RETURN 1;

	
EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in processFMSVVendorBusType';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;	

	RETURN 0;
		
END;
$$ language plpgsql;