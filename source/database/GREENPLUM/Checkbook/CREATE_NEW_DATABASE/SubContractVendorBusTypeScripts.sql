/*
Functions defined
	processFMSVVendorBusType
*/
CREATE OR REPLACE FUNCTION etl.processSubConVendorBusType(p_load_file_id_in int,p_load_id_in bigint) RETURNS INT AS $$
DECLARE
	rec_count int;
BEGIN

	--  processing data to insert/update in subcontract_vendor_business_type table

	UPDATE etl.stg_scntrc_bus_type SET vendor_cust_cd = 'N/A' WHERE vendor_cust_cd ='N/A (PRIVACY/SECURITY)';
	UPDATE etl.stg_scntrc_bus_type SET scntrc_vend_cd = 'N/A' WHERE scntrc_vend_cd ='N/A (PRIVACY/SECURITY)';

	CREATE TEMPORARY TABLE tmp_scntrc_ven_bus_type(uniq_id bigint, scntrc_vend_cd varchar, bus_typ varchar, bus_typ_sta integer, min_typ integer,  action_flag char(1))
	DISTRIBUTED BY (uniq_id);

	INSERT INTO tmp_scntrc_ven_bus_type(uniq_id, scntrc_vend_cd, bus_typ,	bus_typ_sta, min_typ,  action_flag)
	SELECT MAX(uniq_id) as uniq_id, scntrc_vend_cd, bus_typ, bus_typ_sta, min_typ,  'I' as action_flag
	FROM etl.stg_scntrc_bus_type
	GROUP BY 2,3,4,5,6;

	UPDATE tmp_scntrc_ven_bus_type a
	SET action_flag = 'U'
	FROM subcontract_vendor_business_type b LEFT JOIN ref_business_type c ON b.business_type_id = c.business_type_id
	WHERE a.scntrc_vend_cd = b.vendor_customer_code AND a.bus_typ = c.business_type_code
	AND a.bus_typ_sta = b.status AND coalesce(a.min_typ,0) = coalesce(b.minority_type_id,0) ;

	DELETE FROM  subcontract_vendor_business_type WHERE certification_no = 'FMSV Vendor bus type file';

	INSERT INTO subcontract_vendor_business_type(vendor_customer_code,business_type_id,status,
    				       minority_type_id,certification_start_date,certification_end_date, initiation_date, certification_no, disp_certification_start_date)
    	SELECT  a.scntrc_vend_cd,c.business_type_id,a.bus_typ_sta,
    		a.min_typ,a.cert_strt_dt,a.cert_end_dt,a.init_dt, a.cert_no, a.disp_cert_strt_dt
    	FROM	etl.stg_scntrc_bus_type a JOIN tmp_scntrc_ven_bus_type b ON a.uniq_id = b.uniq_id LEFT JOIN ref_business_type c ON b.bus_typ = c.business_type_code
    	WHERE b.action_flag = 'I';


    	GET DIAGNOSTICS rec_count = ROW_COUNT;

	INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
	VALUES(p_load_file_id_in,'SV',rec_count, '# of records inserted into subcontract_vendor_business_type');


    UPDATE 	subcontract_vendor_business_type a
    SET certification_start_date = b.cert_strt_dt,
    certification_end_date = b.cert_end_dt,
    initiation_date = b.init_dt,
    certification_no = b.cert_no,
    disp_certification_start_date = b.disp_cert_strt_dt
    FROM tmp_scntrc_ven_bus_type d JOIN etl.stg_scntrc_bus_type b ON d.uniq_id = b.uniq_id LEFT JOIN ref_business_type c ON b.bus_typ = c.business_type_code
    WHERE a.vendor_customer_code = b.scntrc_vend_cd AND a.business_type_id = c.business_type_id
    AND a.status = b.bus_typ_sta AND coalesce(a.minority_type_id,0) = coalesce(b.min_typ,0)
    AND d.action_flag = 'U';

	GET DIAGNOSTICS rec_count = ROW_COUNT;

	INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
	VALUES(p_load_file_id_in,'SV',rec_count, '# of records updated into subcontract_vendor_business_type');

	-- modified to get vendor minority type from FMSV feed if not got in subcontract business type file.

	INSERT INTO subcontract_vendor_business_type(vendor_customer_code,business_type_id,status,minority_type_id,certification_start_date,
	certification_end_date, initiation_date, certification_no, disp_certification_start_date)
	SELECT a.vendor_customer_code, a.business_type_id,a.status,	a.minority_type_id,a.certification_start_date,a.certification_end_date,
	a.initiation_date, 'FMSV Vendor bus type file' as certification_no, null as disp_certification_start_date
	FROM fmsv_business_type a JOIN subvendor b ON a.vendor_customer_code = b.vendor_customer_code
	LEFT JOIN subcontract_vendor_business_type c ON a.vendor_customer_code = c.vendor_customer_code
	WHERE c.vendor_customer_code IS NULL AND a.status = 2 ;

	--  processing data to insert/update in subcontract_business_type table

	CREATE TEMPORARY TABLE tmp_scntrc_bus_type(uniq_id bigint, doc_cd varchar, doc_dept_cd varchar, doc_id varchar, scntrc_id varchar,
	scntrc_vend_cd varchar, bus_typ varchar, bus_typ_sta integer, min_typ integer,  action_flag char(1))
	DISTRIBUTED BY (uniq_id);


	INSERT INTO tmp_scntrc_bus_type(uniq_id, doc_cd, doc_dept_cd, doc_id, scntrc_id, scntrc_vend_cd, bus_typ,	bus_typ_sta, min_typ,  action_flag)
	SELECT MAX(uniq_id) as uniq_id, doc_cd, doc_dept_cd, doc_id, scntrc_id, scntrc_vend_cd, bus_typ, bus_typ_sta, min_typ,  'I' as action_flag
	FROM etl.stg_scntrc_bus_type
	GROUP BY 2,3,4,5,6,7,8,9;

	UPDATE tmp_scntrc_bus_type a
	SET action_flag = 'U'
	FROM subcontract_business_type b LEFT JOIN ref_business_type c ON b.business_type_id = c.business_type_id
	WHERE  a.doc_cd || a.doc_dept_cd || a.doc_id  = b.contract_number
	AND a.scntrc_id = b.subcontract_id AND a.scntrc_vend_cd = b.vendor_customer_code AND a.bus_typ = c.business_type_code
	AND a.bus_typ_sta = b.status AND coalesce(a.min_typ,0) = coalesce(b.minority_type_id,0) ;


	INSERT INTO subcontract_business_type(prime_vendor_customer_code, vendor_customer_code,contract_number, subcontract_id, business_type_id,status,
    				       minority_type_id, certification_start_date, load_id, created_date)
    	SELECT  a.vendor_cust_cd, a.scntrc_vend_cd,a.doc_cd || a.doc_dept_cd || a.doc_id, a.scntrc_id, c.business_type_id,a.bus_typ_sta,
    		a.min_typ, a.cert_strt_dt, p_load_id_in as created_load_id, now()::timestamp
    	FROM	etl.stg_scntrc_bus_type a JOIN tmp_scntrc_bus_type b ON a.uniq_id = b.uniq_id LEFT JOIN ref_business_type c ON b.bus_typ = c.business_type_code
    	WHERE b.action_flag = 'I';


    	GET DIAGNOSTICS rec_count = ROW_COUNT;

	INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
	VALUES(p_load_file_id_in,'SV',rec_count, '# of records inserted into subcontract_business_type');



	RETURN 1;

EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in processSubConVendorBusType';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;

	RETURN 0;

END;
$$ language plpgsql;
