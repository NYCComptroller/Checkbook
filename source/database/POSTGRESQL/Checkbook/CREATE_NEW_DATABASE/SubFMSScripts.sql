set search_path=public;
/* Functions defined
	updateForeignKeysForSubPayments	
	associateSubCONToSubPayments
	processSubPayments
	refreshFactsForSubPayments
	
*/
CREATE OR REPLACE FUNCTION etl.updateForeignKeysForSubPayments(p_load_file_id_in bigint,p_load_id_in bigint) RETURNS INT AS $$
DECLARE
	l_count bigint;
BEGIN
	/* UPDATING FOREIGN KEY VALUES	FOR THE HEADER RECORD*/		
	
	CREATE TEMPORARY TABLE tmp_sub_fk_fms_values (uniq_id bigint, document_code_id smallint,agency_history_id smallint,
						check_eft_issued_date_id integer, check_eft_issued_nyc_year_id smallint)
	;
	
	-- FK:Document_Code_id
	
	INSERT INTO tmp_sub_fk_fms_values(uniq_id,document_code_id)
	SELECT	a.uniq_id, b.document_code_id
	FROM etl.stg_scntrc_pymt a JOIN ref_document_code b ON a.doc_cd = b.document_code;
	
	-- FK:Agency_history_id
	
	INSERT INTO tmp_sub_fk_fms_values(uniq_id,agency_history_id)
	SELECT	a.uniq_id, max(c.agency_history_id) as agency_history_id
	FROM etl.stg_scntrc_pymt a JOIN ref_agency b ON a.doc_dept_cd = b.agency_code
		 JOIN ref_agency_history c ON b.agency_id = c.agency_id
	GROUP BY 1;
	
		

	-- FK:check_eft_issued_date_id
	
	INSERT INTO tmp_sub_fk_fms_values(uniq_id,check_eft_issued_date_id)
	SELECT	a.uniq_id, b.date_id
	FROM etl.stg_scntrc_pymt a JOIN ref_date b ON a.scntrc_pymt_dt = b.date;
	
	-- FK:check_eft_issued_nyc_year_id
	
	INSERT INTO tmp_sub_fk_fms_values(uniq_id,check_eft_issued_nyc_year_id)
	SELECT	a.uniq_id, b.nyc_year_id
	FROM etl.stg_scntrc_pymt a JOIN ref_date b ON a.scntrc_pymt_dt = b.date;
	


	raise notice '1';
		
	UPDATE etl.stg_scntrc_pymt a
	SET	document_code_id = ct_table.document_code_id ,
		agency_history_id = ct_table.agency_history_id,	
		check_eft_issued_date_id = ct_table.check_eft_issued_date_id, 
		check_eft_issued_nyc_year_id = ct_table.check_eft_issued_nyc_year_id
	FROM	(SELECT uniq_id, max(document_code_id) as document_code_id ,
				 max(agency_history_id) as agency_history_id,
				 max(check_eft_issued_date_id) as check_eft_issued_date_id, 
				max(check_eft_issued_nyc_year_id) as check_eft_issued_nyc_year_id
		 FROM	tmp_sub_fk_fms_values
		 GROUP BY 1) ct_table
	WHERE	a.uniq_id = ct_table.uniq_id;	
	
	
	RETURN 1;
EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in updateForeignKeysForSubPayments';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;	

	RETURN 0;
END;
$$ language plpgsql;

---------------------------------------------------------------------------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION etl.associateSubCONToSubPayments(p_load_file_id_in bigint, p_load_id_in bigint) RETURNS INT AS $$
DECLARE
	l_worksite_col_array VARCHAR ARRAY[10];
	l_array_ctr smallint;
	l_fk_update int;
	l_count bigint;
BEGIN
						  

	
	-- Fetch all the contracts associated with Disbursements
	
	CREATE TEMPORARY TABLE tmp_sub_ct_fms(uniq_id bigint, agreement_id bigint,con_document_id varchar, 
				con_agency_history_id smallint, con_document_code_id smallint, con_document_code varchar, con_agency_code varchar, con_sub_contract_id varchar )	
	;
	
	INSERT INTO tmp_sub_ct_fms
	SELECT uniq_id, 0 as agreement_id,
	       max(a.doc_id)as con_document_id ,
	       max(d.agency_history_id) as con_agency_history_id,
	       max(c.document_code_id) as con_document_code_id,
	       max(c.document_code) as con_document_code,
	       max(b.agency_code) as con_agency_code,
	       max(a.scntrc_id) as con_sub_contract_id
	FROM	etl.stg_scntrc_pymt a JOIN ref_agency b ON a.doc_dept_cd = b.agency_code
		JOIN ref_document_code c ON a.doc_cd = c.document_code
		JOIN ref_agency_history d ON b.agency_id = d.agency_id
	GROUP BY 1,2;		
		
	RAISE NOTICE 'FMS AC 1';
	-- Identify the agreement/CON Id
	
	CREATE TEMPORARY TABLE tmp_sub_old_ct_fms_con(uniq_id bigint,agreement_id bigint, action_flag char(1), latest_flag char(1))
	;
	
	INSERT INTO tmp_sub_old_ct_fms_con
	SELECT uniq_id,
	       original_agreement_id as agreement_id	
	FROM	
		(SELECT  uniq_id,		
			 b.document_version as mag_document_version,
			 b.original_agreement_id,
			 rank()over(partition by uniq_id order by b.document_version desc) as rank_value
		FROM tmp_sub_ct_fms a JOIN subcontract_details b ON a.con_document_id = b.document_id AND a.con_sub_contract_id = b.sub_contract_id
			JOIN ref_document_code f ON a.con_document_code = f.document_code AND b.document_code_id = f.document_code_id
			JOIN ref_agency e ON a.con_agency_code = e.agency_code 
			JOIN ref_agency_history c ON b.agency_history_id = c.agency_history_id AND e.agency_id = c.agency_id
		WHERE b.original_version_flag ='Y'	
		) inner_tbl
	WHERE	rank_value = 1;	
	
	UPDATE tmp_sub_ct_fms a
	SET	agreement_id = b.agreement_id
	FROM	tmp_sub_old_ct_fms_con b
	WHERE	a.uniq_id = b.uniq_id;
	
	RAISE NOTICE 'FMS AC 2';	

	 UPDATE etl.stg_scntrc_pymt a
	 SET	agreement_id = b.agreement_id
	 FROM	tmp_sub_ct_fms b
	 WHERE	a.uniq_id = b.uniq_id;
	 
	 UPDATE etl.stg_scntrc_pymt a
	 SET	agreement_id = NULL
	 WHERE agreement_id = 0;
	 
	RETURN 1;
EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in associateSubCONToSubPayments';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;	

	RETURN 0;
END;
$$ language plpgsql;


------------------------------------------------------------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION etl.processSubPayments(p_load_file_id_in int,p_load_id_in bigint) RETURNS INT AS $$
DECLARE


	l_fk_update int;
	l_insert_sql VARCHAR;
	l_display_type char(1);
	l_masked_agreement_id bigint;
	l_masked_vendor_history_id integer;
	l_count bigint;
BEGIN


	SELECT display_type
	FROM   etl.etl_data_load_file
	WHERE  load_file_id = p_load_file_id_in
	INTO   l_display_type;
	

	
	l_fk_update := etl.updateForeignKeysForSubPayments(p_load_file_id_in,p_load_id_in);

	RAISE NOTICE 'FMS 1';
	
	IF l_fk_update = 1 THEN
		l_fk_update := etl.processsubvendor(p_load_file_id_in,p_load_id_in);
	ELSE
		RETURN -1;
	END IF;


	RAISE NOTICE 'FMS 3';
	
	IF l_fk_update = 1 THEN
		l_fk_update := etl.associateSubCONToSubPayments(p_load_file_id_in,p_load_id_in);
	ELSE
		RETURN -1;
	END IF;

	RAISE NOTICE 'FMS 5';
	
	/*
	1.Pull the key information such as document code, document id, document version etc for all agreements
	2. For the existing contracts gather details on max version in the transaction, staging tables..Determine if the staged agreement is latest version...
	3. Identify the new agreements. Determine the latest version for each of it.
	*/
	
	RAISE NOTICE 'FMS 6';
	
	-- Handling interload duplicates
	
	CREATE TEMPORARY TABLE tmp_sub_all_disbs(uniq_id bigint, contract_number varchar, sub_contract_id varchar, payment_id varchar, disbursement_line_item_id bigint, action_flag char(1)) 
	;
	
	INSERT INTO tmp_sub_all_disbs(uniq_id,contract_number,sub_contract_id,payment_id, action_flag)
	SELECT uniq_id, doc_cd || doc_dept_cd || doc_id as contract_number, scntrc_id as sub_contract_id, scntrc_pymt_id as payment_id, 'I' as action_flag
	FROM etl.stg_scntrc_pymt;
	
	CREATE TEMPORARY TABLE tmp_sub_old_disbs(disbursement_line_item_id bigint, uniq_id bigint) 
	;
	
	INSERT INTO tmp_sub_old_disbs 
	SELECT a.disbursement_line_item_id, b.uniq_id
	FROM subcontract_spending a JOIN etl.stg_scntrc_pymt b ON a.contract_number = b.doc_cd || b.doc_dept_cd || b.doc_id 
	AND a.sub_contract_id = b.scntrc_id  AND a.payment_id = b.scntrc_pymt_id 	;
	
	
	UPDATE tmp_sub_all_disbs a
	SET	disbursement_line_item_id = b.disbursement_line_item_id,
		action_flag = 'U'		
	FROM	tmp_sub_old_disbs b
	WHERE	a.uniq_id = b.uniq_id;

	RAISE NOTICE 'FMS 13';
	
	TRUNCATE etl.seq_disbursement_line_item_id;
		
	INSERT INTO etl.seq_disbursement_line_item_id
	SELECT uniq_id
	FROM	tmp_sub_all_disbs
	WHERE	action_flag ='I' 
		AND COALESCE(disbursement_line_item_id,0) =0 ;

	UPDATE tmp_sub_all_disbs a
	SET	disbursement_line_item_id = b.disbursement_line_item_id	
	FROM	etl.seq_disbursement_line_item_id b
	WHERE	a.uniq_id = b.uniq_id;	

	RAISE NOTICE 'FMS 14';
	

	INSERT INTO subcontract_spending(disbursement_line_item_id,document_code_id,agency_history_id,
				 document_id, contract_number, sub_contract_id, payment_id, 
				 check_eft_amount_original,check_eft_amount,check_eft_issued_date_id,check_eft_issued_nyc_year_id, 
				 payment_description, payment_proof, is_final_payment,
				 vendor_history_id,prime_vendor_id,agreement_id,
				 created_load_id,created_date)
	SELECT d.disbursement_line_item_id, a.document_code_id,a.agency_history_id,
	       a.doc_id,a.doc_cd || a.doc_dept_cd || a.doc_id as contract_number, a.scntrc_id as sub_contract_id, a.scntrc_pymt_id as payment_id,
	       a.scntrc_pymt_am,coalesce(a.scntrc_pymt_am,0) as check_eft_amount,a.check_eft_issued_date_id,a.check_eft_issued_nyc_year_id,
	       a.scntrc_pymt_dscr as payment_description, a.scntrc_prf_pymt as payment_proof, a.scntrc_fnl_pymt_fl as is_final_payment,
	        a.vendor_history_id, (CASE WHEN a.vendor_cust_cd = 'N/A' THEN 0 ELSE f.vendor_id END)  as prime_vendor_id,a.agreement_id,
	       p_load_id_in,now()::timestamp
	FROM	etl.stg_scntrc_pymt a 
		JOIN tmp_sub_all_disbs d ON a.uniq_id = d.uniq_id
		LEFT JOIN (select vendor_customer_code, vendor_id from vendor where miscellaneous_vendor_flag = 0::bit) f ON a.vendor_cust_cd = f.vendor_customer_code
	WHERE   action_flag='I';
		
	GET DIAGNOSTICS l_count = ROW_COUNT;	
							
			IF l_count > 0 THEN 
			INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
				VALUES(p_load_file_id_in,'SF',l_count, '# of records inserted into subcontract_spending');	
	END IF;	
		
		
	RAISE NOTICE 'FMS 15';
	
	CREATE TEMPORARY TABLE tmp_sub_disbs_update AS
	SELECT d.disbursement_line_item_id, a.document_code_id,a.agency_history_id,
	       a.doc_id,a.doc_cd || a.doc_dept_cd || a.doc_id as contract_number, a.scntrc_id as sub_contract_id, a.scntrc_pymt_id as payment_id,
	       a.scntrc_pymt_am,a.check_eft_issued_date_id,a.check_eft_issued_nyc_year_id,
	       a.scntrc_pymt_dscr as payment_description, a.scntrc_prf_pymt as payment_proof, a.scntrc_fnl_pymt_fl as is_final_payment,
	        a.vendor_history_id, (CASE WHEN a.vendor_cust_cd = 'N/A' THEN 0 ELSE f.vendor_id END) as prime_vendor_id, a.agreement_id	    
	FROM	etl.stg_scntrc_pymt a 
		JOIN tmp_sub_all_disbs d ON a.uniq_id = d.uniq_id
		LEFT JOIN (select vendor_customer_code, vendor_id from vendor where miscellaneous_vendor_flag = 0::bit) f ON a.vendor_cust_cd = f.vendor_customer_code
	WHERE   action_flag='U'
	;	
	
	UPDATE subcontract_spending a
	SET document_code_id = b.document_code_id,
		agency_history_id = b.agency_history_id,
		document_id = b.doc_id,
		contract_number = b.contract_number, 
		sub_contract_id = b.sub_contract_id, 
		payment_id = b.payment_id,
		check_eft_amount_original = b.scntrc_pymt_am,
		check_eft_amount = coalesce(b.scntrc_pymt_am,0),
		check_eft_issued_date_id = b.check_eft_issued_date_id,
		check_eft_issued_nyc_year_id = b.check_eft_issued_nyc_year_id,
		payment_description = b.payment_description,
		payment_proof = b.payment_proof,
		is_final_payment = b.is_final_payment,
		vendor_history_id = b.vendor_history_id,
		prime_vendor_id = b.prime_vendor_id,
		agreement_id = b.agreement_id,
		updated_load_id = p_load_id_in,
		updated_date = now()::timestamp
	FROM	tmp_sub_disbs_update b
	WHERE	a.disbursement_line_item_id = b.disbursement_line_item_id;
	
		GET DIAGNOSTICS l_count = ROW_COUNT;	
				
					IF l_count > 0 THEN 
						INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
						VALUES(p_load_file_id_in,'SF',l_count, '# of records updated in subcontract_spending');	
	END IF;	
	
		RAISE NOTICE 'FMS 16';
		
		
	RETURN 1;
	
	
EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in processSubPayments';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;	

	RETURN 0;
END;
$$ language plpgsql;

---------------------------------------------------------------------------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION etl.refreshFactsForSubPayments(p_job_id_in bigint) RETURNS INT AS
$$
DECLARE
	l_start_time  timestamp;
	l_end_time  timestamp;
BEGIN
	
	
	l_start_time := timeofday()::timestamp;
	
	RAISE NOTICE 'FMS RF 1';
	
	INSERT INTO subcontract_spending_deleted(disbursement_line_item_id, agency_id, load_id, deleted_date, job_id)
	SELECT a.disbursement_line_item_id, a.agency_id, c.load_id, now()::timestamp, p_job_id_in
	FROM subcontract_spending_details a, subcontract_spending b, etl.etl_data_load c
	WHERE   a.disbursement_line_item_id = b.disbursement_line_item_id 
	AND b.updated_load_id = c.load_id 
	AND c.job_id = p_job_id_in AND c.data_source_code IN ('SC','SF');
	
	DELETE FROM ONLY subcontract_spending_details a 
	USING subcontract_spending b,etl.etl_data_load c
	WHERE    a.disbursement_line_item_id = b.disbursement_line_item_id 
	AND b.updated_load_id = c.load_id
	AND c.job_id = p_job_id_in AND c.data_source_code IN ('SC','SF'); 
	

		
		
	INSERT INTO subcontract_spending_details(disbursement_line_item_id,disbursement_number,payment_id,check_eft_issued_date_id,	
						check_eft_issued_nyc_year_id,fiscal_year, check_eft_issued_cal_month_id,
						agreement_id,sub_contract_id,
						check_amount,agency_id,agency_history_id,agency_code,
						vendor_id,prime_vendor_id,prime_minority_type_id, prime_minority_type_name,maximum_contract_amount,						
						document_id,vendor_name,vendor_customer_code,check_eft_issued_date,agency_name,agency_short_name,
						spending_category_id,spending_category_name,calendar_fiscal_year_id,calendar_fiscal_year,
						reference_document_number,reference_document_code,
						minority_type_id, minority_type_name,
						load_id,last_modified_date,job_id)
	SELECT  b.disbursement_line_item_id,b.disbursement_number,b.payment_id, b.check_eft_issued_date_id,
		f.nyc_year_id,l.year_value,f.calendar_month_id,
		b.agreement_id,b.sub_contract_id,
		b.check_eft_amount as check_amount,c.agency_id,b.agency_history_id,m.agency_code,
		e.vendor_id,b.prime_vendor_id, (CASE WHEN pm.minority_type_id IS NULL THEN 7 else pm.minority_type_id END) as prime_minority_type_id, (CASE WHEN pm.minority_type_id IS NULL THEN 'Non-Minority' else pm.minority_type_name END) as prime_minority_type_name, NULL as maximum_contract_amount, 
		b.document_id,e.legal_name as vendor_name,q.vendor_customer_code,f.date,c.agency_name,c.agency_short_name,
		1 as spending_category_id,
		'Contracts' as spending_category_name,x.year_id,x.year_value,
		b.contract_number as reference_document_number,dc.document_code as reference_document_code,
		 vmb.minority_type_id, vmb.minority_type_name,
		 coalesce(b.updated_load_id, b.created_load_id), coalesce(b.updated_date, b.created_date),p_job_id_in
		FROM subcontract_spending b 
			JOIN ref_agency_history c ON b.agency_history_id = c.agency_history_id
			JOIN ref_agency m on c.agency_id = m.agency_id			
			JOIN subvendor_history e ON b.vendor_history_id = e.vendor_history_id
			JOIN subvendor q ON q.vendor_id = e.vendor_id
			JOIN ref_date f ON b.check_eft_issued_date_id = f.date_id
			JOIN ref_year l on f.nyc_year_id = l.year_id
			JOIN ref_month y on f.calendar_month_id = y.month_id
			JOIN ref_year x on y.year_id = x.year_id
			JOIN ref_document_code dc ON b.document_code_id = dc.document_code_id
			JOIN etl.etl_data_load z ON coalesce(b.updated_load_id, b.created_load_id) = z.load_id
			LEFT JOIN subvendor_min_bus_type vmb ON e.vendor_history_id = vmb.vendor_history_id
			LEFT JOIN (SELECT a.vendor_id,	 
						(CASE WHEN a.minority_type_id IS NULL OR a.minority_type_id = 11 THEN 7 ELSE a.minority_type_id END) as minority_type_id, 	
						(CASE WHEN a.minority_type_id IS NULL OR a.minority_type_id = 11 THEN 'Non-Minority' ELSE b.minority_type_name END) as minority_type_name,year_id 	
						FROM
						(SELECT a.vendor_id, a.year_id, a.max_check_eft_issued_date, max(b.minority_type_id) as minority_type_id
							FROM (SELECT vendor_id, max(check_eft_issued_date) as max_check_eft_issued_date, check_eft_issued_nyc_year_id as year_id
									FROM disbursement_line_item_details  WHERE spending_category_id !=2 AND fiscal_year > 2011
									GROUP BY 1,3 ) a
							JOIN disbursement_line_item_details b ON a.vendor_id = b.vendor_id AND a.year_id = b.check_eft_issued_nyc_year_id AND a.max_check_eft_issued_date = b.check_eft_issued_date
							GROUP BY 1,2,3) a JOIN ref_minority_type b ON a.minority_type_id = b.minority_type_id) pm ON b.prime_vendor_id = pm.vendor_id AND f.nyc_year_id = pm.year_id
		WHERE z.job_id = p_job_id_in AND z.data_source_code IN ('SC','SF');
		
		
	
	
	RAISE NOTICE 'FMS RF 2';
	
	CREATE TEMPORARY TABLE tmp_sub_agreement_con(disbursement_line_item_id bigint,agreement_id bigint,fiscal_year smallint,calendar_fiscal_year smallint,maximum_contract_amount numeric(16,2),
												maximum_contract_amount_cy numeric(16,2), 
												purpose varchar, purpose_cy varchar, contract_number varchar, contract_vendor_id integer, contract_vendor_id_cy integer,
												contract_prime_vendor_id integer, contract_prime_vendor_id_cy integer, contract_prime_minority_type_id smallint, contract_prime_minority_type_id_cy smallint,
												contract_agency_id smallint, contract_agency_id_cy smallint,contract_document_code varchar, 
												industry_type_id smallint, industry_type_name varchar, agreement_type_code varchar, award_method_code varchar,
												contract_industry_type_id smallint, contract_industry_type_id_cy smallint,
												contract_minority_type_id smallint, contract_minority_type_id_cy smallint,
												master_agreement_id bigint,master_contract_number varchar)
	;
	
	INSERT INTO tmp_sub_agreement_con(disbursement_line_item_id,agreement_id,fiscal_year,calendar_fiscal_year)
	SELECT DISTINCT a.disbursement_line_item_id, a.agreement_id, a.fiscal_year, a.calendar_fiscal_year 
	FROM subcontract_spending_details a JOIN subcontract_spending b ON a.disbursement_line_item_id = b.disbursement_line_item_id		 
		 JOIN etl.etl_data_load d ON coalesce(b.updated_load_id, b.created_load_id) = d.load_id
		WHERE d.job_id = p_job_id_in AND d.data_source_code IN ('SC','SF') AND b.agreement_id > 0;
	
		
	-- Getting maximum_contract_amount, master_agreement_id, purpose, contract_number,  contract_vendor_id, contract_agency_id for FY from non master contracts.
	
	CREATE TEMPORARY TABLE tmp_sub_agreement_con_fy(disbursement_line_item_id bigint,agreement_id bigint, contract_number varchar,
						maximum_contract_amount_fy numeric(16,2), purpose_fy varchar, contract_vendor_id_fy integer, contract_prime_vendor_id_fy integer,contract_prime_minority_type_id_fy smallint, 
						contract_agency_id_fy smallint, contract_document_code_fy varchar, 
						industry_type_id smallint, industry_type_name varchar, agreement_type_code varchar, award_method_code varchar,
						contract_industry_type_id_fy smallint, contract_minority_type_id_fy smallint,
						master_agreement_id bigint,master_contract_number varchar)
	;
	
	INSERT INTO tmp_sub_agreement_con_fy
	SELECT a.disbursement_line_item_id, b.original_agreement_id,b.contract_number,
	b.maximum_contract_amount as maximum_contract_amount_fy ,
	b.description as purpose_fy ,
	b.vendor_id as contract_vendor_id_fy,
	b.prime_vendor_id as contract_prime_vendor_id_fy,
	b.prime_minority_type_id as contract_prime_minority_type_id_fy,
	b.agency_id as contract_agency_id_fy,
	e.document_code as contract_document_code_fy,
	b.industry_type_id as industry_type_id,
	b.industry_type_name as industry_type_name,
	b.agreement_type_code as agreement_type_code,
	b.award_method_code as award_method_code,
	b.industry_type_id as contract_industry_type_id_fy,
	b.minority_type_id as contract_minority_type_id_fy,
	b.master_agreement_id,
	b.master_contract_number
		FROM tmp_sub_agreement_con a JOIN sub_agreement_snapshot b ON a.agreement_id = b.original_agreement_id AND a.fiscal_year between b.starting_year and b.ending_year
		JOIN subcontract_spending c ON a.disbursement_line_item_id = c.disbursement_line_item_id
		JOIN ref_document_code e ON b.document_code_id = e.document_code_id ;
		
	
	INSERT INTO tmp_sub_agreement_con_fy
    SELECT a.disbursement_line_item_id, b.original_agreement_id,b.contract_number,
	b.maximum_contract_amount as maximum_contract_amount_fy ,
	b.description as purpose_fy ,
	b.vendor_id as contract_vendor_id_fy,
	b.prime_vendor_id as contract_prime_vendor_id_fy,
	b.prime_minority_type_id as contract_prime_minority_type_id_fy,
	b.agency_id as contract_agency_id_fy,
	e.document_code as contract_document_code_fy,
	b.industry_type_id as industry_type_id,
	b.industry_type_name as industry_type_name,
	b.agreement_type_code as agreement_type_code,
	b.award_method_code as award_method_code,
	b.industry_type_id as contract_industry_type_id_fy,
	b.minority_type_id as contract_minority_type_id_fy,
	b.master_agreement_id,
	b.master_contract_number
		FROM tmp_sub_agreement_con a JOIN sub_agreement_snapshot b ON a.agreement_id = b.original_agreement_id AND b.latest_flag='Y'
		JOIN subcontract_spending c ON a.disbursement_line_item_id = c.disbursement_line_item_id
		JOIN ref_document_code e ON b.document_code_id = e.document_code_id 
		LEFT JOIN tmp_sub_agreement_con_fy f ON a.disbursement_line_item_id = f.disbursement_line_item_id
		WHERE f.disbursement_line_item_id IS NULL ;
   	
	UPDATE tmp_sub_agreement_con a
	SET maximum_contract_amount = b.maximum_contract_amount_fy,
		purpose = b.purpose_fy,
		contract_number = b.contract_number,
		contract_vendor_id = b.contract_vendor_id_fy,
		contract_prime_vendor_id = b.contract_prime_vendor_id_fy,
		contract_prime_minority_type_id = b.contract_prime_minority_type_id_fy,
		contract_agency_id = b.contract_agency_id_fy,
		contract_document_code = b.contract_document_code_fy,
		industry_type_id = b.industry_type_id,
		industry_type_name = b.industry_type_name,
		agreement_type_code = b.agreement_type_code,
		award_method_code = b.award_method_code,
		contract_industry_type_id = b.contract_industry_type_id_fy,
		contract_minority_type_id = b.contract_minority_type_id_fy,
		master_agreement_id = b.master_agreement_id,
		master_contract_number = b.master_contract_number
	FROM tmp_sub_agreement_con_fy b
	WHERE a.disbursement_line_item_id = b.disbursement_line_item_id;
	
		
	
	RAISE NOTICE 'FMS RF 3';
	
	
	
	-- Getting maximum_contract_amount, master_agreement_id, purpose, contract_number,  contract_vendor_id, contract_agency_id for CY from non master contracts.
	
	CREATE TEMPORARY TABLE tmp_sub_agreement_con_cy(disbursement_line_item_id bigint,agreement_id bigint,
						maximum_contract_amount_cy numeric(16,2), purpose_cy varchar, contract_vendor_id_cy integer, contract_prime_vendor_id_cy integer, contract_prime_minority_type_id_cy smallint,
						contract_agency_id_cy smallint,	contract_industry_type_id_cy smallint, contract_minority_type_id_cy smallint)
	;
	
	INSERT INTO tmp_sub_agreement_con_cy
    SELECT a.disbursement_line_item_id, b.original_agreement_id,
	b.maximum_contract_amount as maximum_contract_amount_cy ,
	b.description as purpose_cy ,
	b.vendor_id as contract_vendor_id_cy,
	b.prime_vendor_id as contract_prime_vendor_id_cy,
	b.prime_minority_type_id as contract_prime_minority_type_id_cy,
	b.agency_id as contract_agency_id_cy,
	b.industry_type_id as contract_industry_type_id_cy,
	b.minority_type_id as contract_minority_type_id_cy
		FROM tmp_sub_agreement_con a JOIN sub_agreement_snapshot_cy b ON a.agreement_id = b.original_agreement_id AND a.calendar_fiscal_year between b.starting_year and b.ending_year
		JOIN subcontract_spending c ON a.disbursement_line_item_id = c.disbursement_line_item_id
		JOIN ref_document_code e ON b.document_code_id = e.document_code_id;
		
	
	INSERT INTO tmp_sub_agreement_con_cy
    SELECT a.disbursement_line_item_id, b.original_agreement_id,
	b.maximum_contract_amount as maximum_contract_amount_cy ,
	b.description as purpose_cy ,
	b.vendor_id as contract_vendor_id_cy,
	b.prime_vendor_id as contract_prime_vendor_id_cy,
	b.prime_minority_type_id as contract_prime_minority_type_id_cy,
	b.agency_id as contract_agency_id_cy,
	b.industry_type_id as contract_industry_type_id_cy,
	b.minority_type_id as contract_minority_type_id_cy
		FROM tmp_sub_agreement_con a JOIN sub_agreement_snapshot_cy b ON a.agreement_id = b.original_agreement_id AND b.latest_flag='Y'
		JOIN subcontract_spending c ON a.disbursement_line_item_id = c.disbursement_line_item_id
		LEFT JOIN tmp_sub_agreement_con_cy f ON a.disbursement_line_item_id = f.disbursement_line_item_id
		WHERE f.disbursement_line_item_id IS NULL ;
   
		
	UPDATE tmp_sub_agreement_con a
	SET maximum_contract_amount_cy = b.maximum_contract_amount_cy,
		purpose_cy = b.purpose_cy,
		contract_vendor_id_cy = b.contract_vendor_id_cy,
		contract_prime_minority_type_id_cy = b.contract_prime_minority_type_id_cy,
		contract_prime_vendor_id_cy = b.contract_prime_vendor_id_cy,
		contract_agency_id_cy = b.contract_agency_id_cy,
		contract_industry_type_id_cy = b.contract_industry_type_id_cy,
		contract_minority_type_id_cy = b.contract_minority_type_id_cy
	FROM tmp_sub_agreement_con_cy b
	WHERE a.disbursement_line_item_id = b.disbursement_line_item_id;
	
	
	
	RAISE NOTICE 'FMS RF 4';
	
	UPDATE subcontract_spending_details a
	SET	agreement_id = a.agreement_id,	
		contract_number = b.contract_number,
		maximum_contract_amount =b.maximum_contract_amount,
		purpose = b.purpose,	
		contract_agency_id  = b.contract_agency_id ,
		contract_vendor_id  = b.contract_vendor_id ,
		contract_prime_vendor_id  = b.contract_prime_vendor_id,
		contract_prime_minority_type_id = b.contract_prime_minority_type_id,
		contract_prime_minority_type_id_cy = b.contract_prime_minority_type_id_cy,
		maximum_contract_amount_cy =b.maximum_contract_amount_cy,
		purpose_cy = b.purpose_cy,
		contract_agency_id_cy  = b.contract_agency_id_cy ,
		contract_vendor_id_cy  = b.contract_vendor_id_cy ,
		contract_prime_vendor_id_cy  = b.contract_prime_vendor_id_cy ,
		contract_document_code = b.contract_document_code,
		industry_type_id = b.industry_type_id,
		industry_type_name = b.industry_type_name,
		agreement_type_code = b.agreement_type_code,
		award_method_code = b.award_method_code,
		contract_industry_type_id = b.contract_industry_type_id,
		contract_industry_type_id_cy = b.contract_industry_type_id_cy,
		contract_minority_type_id = b.contract_minority_type_id,
		contract_minority_type_id_cy = b.contract_minority_type_id_cy,
		master_agreement_id = b.master_agreement_id,
		master_contract_number = b.master_contract_number
	FROM	tmp_sub_agreement_con  b
	WHERE   a.disbursement_line_item_id = b.disbursement_line_item_id;
	

	
	
UPDATE subcontract_spending_details
SET minority_type_id=11,
minority_type_name = 'Individuals & Others'
WHERE job_id = p_job_id_in AND agreement_type_code IN ('35','36','39','40','44','65','68','79','85') 
AND ( minority_type_id IS NULL OR minority_type_id IN (1,6,7,8));

UPDATE subcontract_spending_details
SET minority_type_id=11,
minority_type_name = 'Individuals & Others'
WHERE job_id = p_job_id_in AND award_method_code IN ('07','08','09','17','18','44','45','55')
AND ( minority_type_id IS NULL OR minority_type_id IN (1,6,7,8));


UPDATE subcontract_spending_details
SET minority_type_id=7,
	minority_type_name = 'Non-Minority'
WHERE job_id = p_job_id_in 	AND ( minority_type_id IS NULL OR minority_type_id IN (1,6,7,8));

	
	
	l_end_time := timeofday()::timestamp;
	
	INSERT INTO etl.etl_script_execution_status(job_id,script_name,completed_flag,start_time,end_time)
	VALUES(p_job_id_in,'etl.refreshFactsForSubPayments',1,l_start_time,l_end_time);
	
	RETURN 1;
	
EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in refreshFactsForSubPayments';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;	
	
	l_end_time := timeofday()::timestamp;
	
	INSERT INTO etl.etl_script_execution_status(job_id,script_name,completed_flag,start_time,end_time,errno,errmsg)
	VALUES(p_job_id_in,'etl.refreshFactsForSubPayments',0,l_start_time,l_end_time,SQLSTATE,SQLERRM);
	
	RETURN 0;
	
END;
$$ language plpgsql;	
