/*
Functions defined

	updateForeignKeysForSubContracts
	processSubContracts
	updateSnapshotScntrcStatus
	updateSubCONFlags
	postProcessSubContracts
	refreshSubContractsPreAggregateTables
	refreshCommonTransactionTables

*/


CREATE OR REPLACE FUNCTION etl.updateForeignKeysForSubContracts(p_load_file_id_in bigint,p_load_id_in bigint) RETURNS INT AS $$
DECLARE
	l_count bigint;
BEGIN
	/* UPDATING FOREIGN KEY VALUES	FOR THE HEADER RECORD*/

	CREATE TEMPORARY TABLE tmp_sub_fk_values (uniq_id bigint, document_code_id smallint,agency_history_id smallint,
					      effective_begin_date_id int,effective_end_date_id int, source_updated_date_id int,registered_date_id int,
					      registered_fiscal_year smallint,registered_fiscal_year_id smallint, registered_calendar_year smallint,
					      registered_calendar_year_id smallint,effective_begin_fiscal_year smallint,effective_begin_fiscal_year_id smallint, effective_begin_calendar_year smallint,
					      effective_begin_calendar_year_id smallint,effective_end_fiscal_year smallint,effective_end_fiscal_year_id smallint, effective_end_calendar_year smallint,
					      effective_end_calendar_year_id smallint,source_updated_fiscal_year smallint,source_updated_calendar_year smallint,source_updated_calendar_year_id smallint,
					      source_updated_fiscal_year_id smallint)
	DISTRIBUTED BY (uniq_id);

	-- updating  doc_appl_last_dt and reg_dt based on prime contract original version
	/*
	UPDATE etl.stg_scntrc_details a
	SET doc_appl_last_dt = c.date
	FROM history_agreement b LEFT JOIN ref_date c ON b.source_updated_date_id = c.date_id
	WHERE a.doc_cd || a.doc_dept_cd || a.doc_id = b.contract_number
	AND b.original_version_flag = 'Y' ;
	*/

	UPDATE etl.stg_scntrc_details a
	SET doc_appl_last_dt =  now()::date ;


	UPDATE etl.stg_scntrc_details a
	SET reg_dt = c.date
	FROM history_agreement b LEFT JOIN ref_date c ON b.registered_date_id = c.date_id
	WHERE a.doc_cd || a.doc_dept_cd || a.doc_id = b.contract_number
	AND b.original_version_flag = 'Y' ;


	-- FK:Document_Code_id

	INSERT INTO tmp_sub_fk_values(uniq_id,document_code_id)
	SELECT	a.uniq_id, b.document_code_id
	FROM etl.stg_scntrc_details a JOIN ref_document_code b ON a.doc_cd = b.document_code;

	-- FK:Agency_history_id

	INSERT INTO tmp_sub_fk_values(uniq_id,agency_history_id)
	SELECT	a.uniq_id, max(c.agency_history_id) as agency_history_id
	FROM etl.stg_scntrc_details a JOIN ref_agency b ON a.doc_dept_cd = b.agency_code
		JOIN ref_agency_history c ON b.agency_id = c.agency_id
	GROUP BY 1;



	--FK:effective_begin_date_id

	INSERT INTO tmp_sub_fk_values(uniq_id,effective_begin_date_id,effective_begin_fiscal_year,effective_begin_fiscal_year_id, effective_begin_calendar_year,effective_begin_calendar_year_id)
	SELECT	a.uniq_id, b.date_id,c.year_value,b.nyc_year_id,e.year_value,d.year_id
	FROM etl.stg_scntrc_details a JOIN ref_date b ON a.scntrc_strt_dt = b.date
		JOIN ref_year c ON b.nyc_year_id = c.year_id
		JOIN ref_month d ON b.calendar_month_id = d.month_id
		JOIN ref_year e ON d.year_id = e.year_id;

	--FK:effective_end_date_id

	INSERT INTO tmp_sub_fk_values(uniq_id,effective_end_date_id,effective_end_fiscal_year,effective_end_fiscal_year_id, effective_end_calendar_year,effective_end_calendar_year_id)
	SELECT	a.uniq_id, b.date_id,c.year_value,b.nyc_year_id,e.year_value,d.year_id
	FROM etl.stg_scntrc_details a JOIN ref_date b ON a.scntrc_end_dt = b.date
		JOIN ref_year c ON b.nyc_year_id = c.year_id
		JOIN ref_month d ON b.calendar_month_id = d.month_id
		JOIN ref_year e ON d.year_id = e.year_id;


	--FK:source_updated_date_id

	INSERT INTO tmp_sub_fk_values(uniq_id,source_updated_date_id,source_updated_fiscal_year,source_updated_fiscal_year_id, source_updated_calendar_year,source_updated_calendar_year_id)
	SELECT	a.uniq_id, b.date_id,c.year_value,b.nyc_year_id,e.year_value,d.year_id
	FROM etl.stg_scntrc_details a JOIN ref_date b ON a.doc_appl_last_dt = b.date
		JOIN ref_year c ON b.nyc_year_id = c.year_id
		JOIN ref_month d ON b.calendar_month_id = d.month_id
		JOIN ref_year e ON d.year_id = e.year_id;

	--FK:registered_date_id

	INSERT INTO tmp_sub_fk_values(uniq_id,registered_date_id, registered_fiscal_year,registered_fiscal_year_id, registered_calendar_year,registered_calendar_year_id)
	SELECT	a.uniq_id, b.date_id,c.year_value,b.nyc_year_id,e.year_value,d.year_id
	FROM etl.stg_scntrc_details a JOIN ref_date b ON a.reg_dt = b.date
		JOIN ref_year c ON b.nyc_year_id = c.year_id
		JOIN ref_month d ON b.calendar_month_id = d.month_id
		JOIN ref_year e ON d.year_id = e.year_id;


	--Updating stg_scntrc_details with all the FK values

	UPDATE etl.stg_scntrc_details a
	SET	document_code_id = ct_table.document_code_id ,
		agency_history_id = ct_table.agency_history_id,
		effective_begin_date_id = ct_table.effective_begin_date_id,
		effective_end_date_id = ct_table.effective_end_date_id,
		source_updated_date_id = ct_table.source_updated_date_id,
		registered_date_id = ct_table.registered_date_id,
		registered_fiscal_year = ct_table.registered_fiscal_year,
		registered_fiscal_year_id = ct_table.registered_fiscal_year_id,
		registered_calendar_year = ct_table.registered_calendar_year,
		registered_calendar_year_id = ct_table.registered_calendar_year_id,
		effective_begin_fiscal_year = ct_table.effective_begin_fiscal_year,
		effective_begin_fiscal_year_id = ct_table.effective_begin_fiscal_year_id,
		effective_begin_calendar_year = ct_table.effective_begin_calendar_year,
		effective_begin_calendar_year_id = ct_table.effective_begin_calendar_year_id,
		effective_end_fiscal_year = ct_table.effective_end_fiscal_year,
		effective_end_fiscal_year_id = ct_table.effective_end_fiscal_year_id,
		effective_end_calendar_year = ct_table.effective_end_calendar_year,
		effective_end_calendar_year_id = ct_table.effective_end_calendar_year_id,
		source_updated_fiscal_year = ct_table.source_updated_fiscal_year,
		source_updated_fiscal_year_id = ct_table.source_updated_fiscal_year_id,
		source_updated_calendar_year = ct_table.source_updated_calendar_year,
		source_updated_calendar_year_id = ct_table.source_updated_calendar_year_id
	FROM	(SELECT uniq_id, max(document_code_id) as document_code_id ,
				 max(agency_history_id) as agency_history_id,
				 max(effective_begin_date_id) as effective_begin_date_id,
				 max(effective_end_date_id) as effective_end_date_id,
				 max(source_updated_date_id) as source_updated_date_id,
				 max(registered_date_id) as registered_date_id,
				 max(registered_fiscal_year) as registered_fiscal_year,
				 max(registered_fiscal_year_id) as registered_fiscal_year_id,
				 max(registered_calendar_year) as registered_calendar_year,
				 max(registered_calendar_year_id) as registered_calendar_year_id,
				 max(effective_begin_fiscal_year) as effective_begin_fiscal_year,
				 max(effective_begin_fiscal_year_id) as effective_begin_fiscal_year_id,
				 max(effective_begin_calendar_year) as effective_begin_calendar_year,
				 max(effective_begin_calendar_year_id) as effective_begin_calendar_year_id,
				 max(effective_end_fiscal_year) as effective_end_fiscal_year,
				 max(effective_end_fiscal_year_id) as effective_end_fiscal_year_id,
				 max(effective_end_calendar_year) as effective_end_calendar_year,
				 max(effective_end_calendar_year_id) as effective_end_calendar_year_id,
				 max(source_updated_fiscal_year) as source_updated_fiscal_year,
				 max(source_updated_fiscal_year_id) as source_updated_fiscal_year_id,
				 max(source_updated_calendar_year) as source_updated_calendar_year,
				 max(source_updated_calendar_year_id) as source_updated_calendar_year_id
		 FROM	tmp_sub_fk_values
		 GROUP BY 1) ct_table
	WHERE	a.uniq_id = ct_table.uniq_id;

	RETURN 1;
EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in updateForeignKeysForSubContracts';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;

	RETURN 0;
END;
$$ language plpgsql;



------------------------------------------------------------------------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION etl.processSubContracts(p_load_file_id_in int,p_load_id_in bigint) RETURNS INT AS $$
DECLARE
	l_worksite_col_array VARCHAR ARRAY[10];
	l_array_ctr smallint;
	l_fk_update int;
	l_worksite_per_array VARCHAR ARRAY[10];
	l_insert_sql VARCHAR;
	l_count bigint;
BEGIN


	l_fk_update := etl.updateForeignKeysForSubContracts(p_load_file_id_in,p_load_id_in);

	RAISE NOTICE 'SUB CON 1';


	IF l_fk_update = 1 THEN
		l_fk_update := etl.processsubvendor(p_load_file_id_in,p_load_id_in);
	ELSE
		RETURN -1;
	END IF;


	IF l_fk_update <> 1 THEN
		RETURN -1;
	END IF;

	RAISE NOTICE 'SUB CON 4';


	/*
	1.Pull the key information such as document code, document id, document version etc for all agreements
	2. For the existing contracts gather details on max version in the transaction, staging tables..Determine if the staged agreement is latest version...
	3. Identify the new agreements. Determine the latest version for each of it.
	*/

	-- Inserting all records from staging header

	RAISE NOTICE 'CON 6';
	CREATE TEMPORARY TABLE tmp_sub_ct_con(uniq_id bigint, agency_history_id smallint,doc_id varchar,agreement_id bigint, action_flag char(1),
					  latest_flag char(1),doc_vers_no smallint,privacy_flag char(1),old_agreement_ids varchar)
	DISTRIBUTED BY (uniq_id);

	INSERT INTO tmp_sub_ct_con(uniq_id,agency_history_id,doc_id,doc_vers_no,action_flag)
	SELECT uniq_id,agency_history_id,doc_id,scntrc_vers_no,'I' as action_flag
	FROM etl.stg_scntrc_details;

	-- Identifying the versions of the agreements for update
	CREATE TEMPORARY TABLE tmp_sub_old_ct_con(uniq_id bigint, agreement_id bigint) DISTRIBUTED BY (uniq_id);

	INSERT INTO tmp_sub_old_ct_con
	SELECT  uniq_id,
		b.agreement_id
	FROM etl.stg_scntrc_details a JOIN subcontract_details b ON a.doc_id = b.document_id
	AND a.document_code_id = b.document_code_id AND a.scntrc_vers_no = b.document_version AND a.scntrc_id = b.sub_contract_id
		JOIN ref_agency_history c ON a.agency_history_id = c.agency_history_id
		JOIN ref_agency_history d ON b.agency_history_id = d.agency_history_id and c.agency_id = d.agency_id;

	UPDATE tmp_sub_ct_con a
	SET	agreement_id = b.agreement_id,
		action_flag = 'U'
	FROM	tmp_sub_old_ct_con b
	WHERE	a.uniq_id = b.uniq_id;

	RAISE NOTICE '1';

	-- Identifying the versions of the agreements for update

	TRUNCATE etl.agreement_id_seq ;

	INSERT INTO etl.agreement_id_seq
	SELECT uniq_id
	FROM	tmp_sub_ct_con
	WHERE	action_flag ='I'
		AND COALESCE(agreement_id,0) =0 ;

	UPDATE tmp_sub_ct_con a
	SET	agreement_id = b.agreement_id
	FROM	etl.agreement_id_seq b
	WHERE	a.uniq_id = b.uniq_id;

	RAISE NOTICE '2';

	INSERT INTO subcontract_details(agreement_id,document_code_id,
				agency_history_id,document_id,document_version,sub_contract_id,
				tracking_number,description,industry_type_id,is_mwbe_cert,
				maximum_contract_amount_original,maximum_contract_amount,
				effective_begin_date_id,effective_end_date_id,
				source_updated_date_id,vendor_history_id,prime_vendor_id,registered_date_id,created_load_id,created_date,
				registered_fiscal_year,registered_fiscal_year_id, registered_calendar_year,
				registered_calendar_year_id,effective_end_fiscal_year,effective_end_fiscal_year_id,
				effective_end_calendar_year,effective_end_calendar_year_id,effective_begin_fiscal_year,
				effective_begin_fiscal_year_id, effective_begin_calendar_year,effective_begin_calendar_year_id,
		   		source_updated_fiscal_year,source_updated_fiscal_year_id, source_updated_calendar_year,
		   		source_updated_calendar_year_id,contract_number,rfed_amount_original, rfed_amount,
		   		aprv_sta, scntrc_mode, aprv_reas_id, aprv_reas_nm)
	SELECT	d.agreement_id,a.document_code_id,
		a.agency_history_id,a.doc_id,a.scntrc_vers_no,a.scntrc_id,
		a.scntrc_trkg_no,a.scntrc_dscr,e.industry_type_id,a.scntrc_mwbe_cert,
		a.scntrc_max_am, (CASE WHEN a.scntrc_max_am IS NULL THEN 0 ELSE a.scntrc_max_am END) as maximum_contract_amount,
		a.effective_begin_date_id,a.effective_end_date_id,
		a.source_updated_date_id,a.vendor_history_id,(CASE WHEN a.vendor_cust_cd = 'N/A' THEN 0 ELSE f.vendor_id END) as prime_vendor_id,a.registered_date_id,p_load_id_in,now()::timestamp,
		registered_fiscal_year,registered_fiscal_year_id, registered_calendar_year,
		registered_calendar_year_id,effective_end_fiscal_year,effective_end_fiscal_year_id,
		effective_end_calendar_year,effective_end_calendar_year_id,effective_begin_fiscal_year,
		effective_begin_fiscal_year_id, effective_begin_calendar_year,effective_begin_calendar_year_id,
		source_updated_fiscal_year,source_updated_fiscal_year_id, source_updated_calendar_year,
		source_updated_calendar_year_id,a.doc_cd||a.doc_dept_cd||a.doc_id as contract_number,a.tot_scntrc_pymt, (CASE WHEN a.tot_scntrc_pymt IS NULL THEN 0 ELSE a.tot_scntrc_pymt END) as rfed_amount,
		a.aprv_sta, a.scntrc_mode, a.aprv_reas_id, a.aprv_reas_nm
	FROM	etl.stg_scntrc_details a
					 JOIN tmp_sub_ct_con d ON a.uniq_id = d.uniq_id
					 LEFT JOIN sub_industry_mappings e ON a.indus_cls = e.sub_industry_type_id
					 LEFT JOIN (select vendor_customer_code, vendor_id from vendor where miscellaneous_vendor_flag = 0::bit) f ON a.vendor_cust_cd = f.vendor_customer_code
	WHERE   action_flag='I' ;

	GET DIAGNOSTICS l_count = ROW_COUNT;
			IF l_count > 0 THEN
				INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
				VALUES(p_load_file_id_in,'SC',l_count,'# of records inserted into subcontract_details');
		END IF;

		RAISE NOTICE '2.5';
		IF l_count > 0 THEN
			l_fk_update := etl.updateSnapshotScntrcStatus(p_load_file_id_in);
		END IF;

	RAISE NOTICE '3';
	/* Updates */
	CREATE TEMPORARY TABLE tmp_sub_con_ct_update AS
	SELECT d.agreement_id,a.document_code_id,
			a.agency_history_id,a.doc_id,a.scntrc_vers_no,
			a.scntrc_trkg_no as tracking_number,a.scntrc_dscr,e.industry_type_id,a.scntrc_mwbe_cert,a.scntrc_max_am,
			a.effective_begin_date_id,a.effective_end_date_id,a.cntrc_typ,
			a.source_updated_date_id,a.vendor_history_id,(CASE WHEN a.vendor_cust_cd = 'N/A' THEN 0 ELSE f.vendor_id END) as prime_vendor_id,
			a.registered_date_id,
			p_load_id_in as load_id,now()::timestamp as updated_date,
			registered_fiscal_year,registered_fiscal_year_id, registered_calendar_year,
			registered_calendar_year_id,effective_end_fiscal_year,effective_end_fiscal_year_id,
			effective_end_calendar_year,effective_end_calendar_year_id,effective_begin_fiscal_year,
			effective_begin_fiscal_year_id, effective_begin_calendar_year,effective_begin_calendar_year_id,
			source_updated_fiscal_year,source_updated_fiscal_year_id, source_updated_calendar_year,
			source_updated_calendar_year_id,a.tot_scntrc_pymt, a.aprv_sta,a.scntrc_mode, a.aprv_reas_id, a.aprv_reas_nm
		FROM	etl.stg_scntrc_details a
		JOIN tmp_sub_ct_con d ON a.uniq_id = d.uniq_id
		LEFT JOIN sub_industry_mappings e ON a.indus_cls = e.sub_industry_type_id
		LEFT JOIN (select vendor_customer_code, vendor_id from vendor where miscellaneous_vendor_flag = 0::bit) f ON a.vendor_cust_cd = f.vendor_customer_code
	WHERE   action_flag='U'
	DISTRIBUTED BY (agreement_id);



	RAISE NOTICE '4';

	UPDATE subcontract_details a
	SET	document_code_id = b.document_code_id,
		agency_history_id  = b.agency_history_id,
		document_id  = b.doc_id,
		document_version = b.scntrc_vers_no,
		tracking_number = b.tracking_number,
		description = b.scntrc_dscr,
		industry_type_id = b.industry_type_id,
		is_mwbe_cert = b.scntrc_mwbe_cert,
		maximum_contract_amount_original = b.scntrc_max_am,
		maximum_contract_amount = (CASE WHEN b.scntrc_max_am IS NULL THEN 0 ELSE b.scntrc_max_am END) ,
		effective_begin_date_id = b.effective_begin_date_id,
		effective_end_date_id = b.effective_end_date_id,
		source_updated_date_id = b.source_updated_date_id,
		vendor_history_id = b.vendor_history_id,
		prime_vendor_id = b.prime_vendor_id,
		registered_date_id = b.registered_date_id,
		updated_load_id = b.load_id,
		updated_date = b.updated_date,
		registered_fiscal_year = b.registered_fiscal_year,
		registered_fiscal_year_id = b.registered_fiscal_year_id,
		registered_calendar_year = b.registered_calendar_year,
		registered_calendar_year_id = b.registered_calendar_year_id,
		effective_end_fiscal_year = b.effective_end_fiscal_year,
		effective_end_fiscal_year_id = b.effective_end_fiscal_year_id,
		effective_end_calendar_year = b.effective_end_calendar_year,
		effective_end_calendar_year_id = b.effective_end_calendar_year_id,
		effective_begin_fiscal_year = b.effective_begin_fiscal_year,
		effective_begin_fiscal_year_id = b.effective_begin_fiscal_year_id,
		effective_begin_calendar_year = b.effective_begin_calendar_year,
		effective_begin_calendar_year_id = b.effective_begin_calendar_year_id,
		source_updated_fiscal_year = b.source_updated_fiscal_year,
		source_updated_fiscal_year_id = b.source_updated_fiscal_year_id,
		source_updated_calendar_year = b.source_updated_calendar_year,
		source_updated_calendar_year_id = b.source_updated_calendar_year_id,
		rfed_amount_original = b.tot_scntrc_pymt,
		rfed_amount = (CASE WHEN b.tot_scntrc_pymt IS NULL THEN 0 ELSE b.tot_scntrc_pymt END),
		aprv_sta = b.aprv_sta,
		scntrc_mode = b.scntrc_mode,
		aprv_reas_id = b.aprv_reas_id,
		aprv_reas_nm = b.aprv_reas_nm
	FROM	tmp_sub_con_ct_update b
	WHERE	a.agreement_id = b.agreement_id;

	GET DIAGNOSTICS l_count = ROW_COUNT;

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'SC',l_count,'# of records updated in subcontract_details from General Contracts');
	END IF;



	RAISE NOTICE '5';

		l_fk_update := etl.updateSubCONFlags(p_load_id_in);

	RAISE NOTICE '6';

	UPDATE subcontract_details a
	SET  original_contract_amount = b.maximum_contract_amount,
		original_contract_amount_original = b.maximum_contract_amount_original
	FROM (select maximum_contract_amount, maximum_contract_amount_original, original_agreement_id FROM subcontract_details WHERE original_version_flag = 'Y') b
	WHERE a.original_agreement_id = b.original_agreement_id;

	UPDATE subcontract_details a
	SET  award_method_id = b.award_method_id,
		award_category_id = b.award_category_id,
		brd_awd_no = b.brd_awd_no,
		number_solicitation = b.number_solicitation,
		number_responses = b.number_responses,
		master_agreement_id = b.master_agreement_id,
		agreement_type_id = b.agreement_type_id
	FROM (select contract_number, award_method_id, award_category_id_1 as award_category_id, brd_awd_no, number_solicitation, number_responses, master_agreement_id, agreement_type_id from history_agreement where latest_flag = 'Y') b
	WHERE a.contract_number = b.contract_number;

	RAISE NOTICE '7';

	RETURN 1;

EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in processSubContracts';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;

	RETURN 0;
END;
$$ language plpgsql;


--------------------------------------------------------------------------------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION etl.updateSnapshotScntrcStatus(p_load_file_id_in bigint) RETURNS INT AS $$
DECLARE
	l_start_time  timestamp;
	l_end_time  timestamp;
	l_count bigint;
BEGIN
  /* Copy the same scntrc_status logic for table agreement_snapshot from CONScripts.sql
  Have to do it again to update existing records in snapshot due to possible subcontract_details changes
  */


	l_start_time := timeofday()::timestamp;


	-- update from subcontract_status table

	UPDATE agreement_snapshot a
	SET scntrc_status = b.scntrc_status
	FROM subcontract_status b, ref_document_code c
	WHERE a.contract_number = b.contract_number
	AND a.document_code_id=c.document_code_id
	AND a.effective_end_date >= '2015-07-01'
	AND c.document_code in ('CT1','CTA1','CT2');

	DROP TABLE IF EXISTS tmp_agreement_id_scntrc_status;
	CREATE TABLE tmp_agreement_id_scntrc_status (
     agreement_id    bigint,
     scntrc_status smallint
	)	DISTRIBUTED BY (agreement_id)
	;

  INSERT INTO tmp_agreement_id_scntrc_status
  SELECT
    a.agreement_id,
    (
      CASE
        WHEN b.document_code NOT IN ('CT1','CTA1','CT2') THEN 5
        WHEN (COALESCE(a.scntrc_status,0) IN (0,1,4)) AND (a.effective_end_year >= 2016) THEN (
          CASE
            WHEN a.contract_number IN (SELECT contract_number FROM subcontract_details) THEN 2
            WHEN a.maximum_contract_amount < 250000
            OR a.agreement_type_code IN (
                '15','17', '18', '20','25', '29','30','35','36','40','41','42',
                '43','44','65','68','79','85','86','88','99')
            OR a.award_method_code IN (
                '04','040', '07', '08','09', '0W1','0W2','100','101','102',
                '105','107','88','99','13','14','15','16','18','24','40','41',
                '42','43','44','45','51','511','55','68','78','79')
            THEN 4
            ELSE 1
          END)
        WHEN (a.scntrc_status = 1) AND (a.effective_end_year < 2016) THEN 0
        ELSE COALESCE(a.scntrc_status,0)
      END) as scntrc_status
    FROM agreement_snapshot a
    JOIN ref_document_code b ON a.document_code_id = b.document_code_id
  ;

  UPDATE agreement_snapshot a
  SET scntrc_status = b.scntrc_status
  FROM tmp_agreement_id_scntrc_status b
  WHERE a.agreement_id = b.agreement_id
  AND a.scntrc_status IS DISTINCT FROM b.scntrc_status
  ;

	TRUNCATE tmp_agreement_id_scntrc_status;

  INSERT INTO tmp_agreement_id_scntrc_status
    SELECT
    a.agreement_id,
    b.scntrc_status
    FROM agreement_snapshot a
    JOIN (
      SELECT aa.contract_number, aa.scntrc_status
      FROM agreement_snapshot aa
      WHERE aa.effective_end_year >= 2016
      AND latest_flag = 'Y'
      AND aa.scntrc_status IN (1,2,3,4)
    ) b ON a.contract_number = b.contract_number
    WHERE a.effective_end_year < 2016
  ;

  UPDATE agreement_snapshot a
  SET scntrc_status = b.scntrc_status
  FROM tmp_agreement_id_scntrc_status b
  WHERE a.agreement_id = b.agreement_id
  AND a.scntrc_status IS DISTINCT FROM b.scntrc_status
  ;






	-- update from subcontract_status table

	UPDATE agreement_snapshot_cy a
	SET scntrc_status = b.scntrc_status
	FROM subcontract_status b, ref_document_code c
	WHERE a.contract_number = b.contract_number
	AND a.document_code_id=c.document_code_id
	AND a.effective_end_date >= '2015-07-01'
	AND c.document_code in ('CT1','CTA1','CT2');

	TRUNCATE tmp_agreement_id_scntrc_status;

  INSERT INTO tmp_agreement_id_scntrc_status
  SELECT
    a.agreement_id,
    (
      CASE
        WHEN b.document_code NOT IN ('CT1','CTA1','CT2') THEN 5
        WHEN (COALESCE(a.scntrc_status,0) IN (0,1,4)) AND (a.effective_end_year >= 2016) THEN (
          CASE
            WHEN a.contract_number IN (SELECT contract_number FROM subcontract_details) THEN 2
            WHEN a.maximum_contract_amount < 250000
            OR a.agreement_type_code IN (
                '15','17', '18', '20','25', '29','30','35','36','40','41','42',
                '43','44','65','68','79','85','86','88','99')
            OR a.award_method_code IN (
                '04','040', '07', '08','09', '0W1','0W2','100','101','102',
                '105','107','88','99','13','14','15','16','18','24','40','41',
                '42','43','44','45','51','511','55','68','78','79')
            THEN 4
            ELSE 1
          END)
        WHEN (a.scntrc_status = 1) AND (a.effective_end_year < 2016) THEN 0
        ELSE COALESCE(a.scntrc_status,0)
      END) as scntrc_status
    FROM agreement_snapshot_cy a
    JOIN ref_document_code b ON a.document_code_id = b.document_code_id
  ;

  UPDATE agreement_snapshot_cy a
  SET scntrc_status = b.scntrc_status
  FROM tmp_agreement_id_scntrc_status b
  WHERE a.agreement_id = b.agreement_id
  AND a.scntrc_status IS DISTINCT FROM b.scntrc_status
  ;

	TRUNCATE tmp_agreement_id_scntrc_status;

  INSERT INTO tmp_agreement_id_scntrc_status
    SELECT
    a.agreement_id,
    b.scntrc_status
    FROM agreement_snapshot_cy a
    JOIN (
      SELECT aa.contract_number, aa.scntrc_status
      FROM agreement_snapshot_cy aa
      WHERE aa.effective_end_year >= 2016
      AND latest_flag = 'Y'
      AND aa.scntrc_status IN (1,2,3,4)
    ) b ON a.contract_number = b.contract_number
    WHERE a.effective_end_year < 2016
  ;

  UPDATE agreement_snapshot_cy a
  SET scntrc_status = b.scntrc_status
  FROM tmp_agreement_id_scntrc_status b
  WHERE a.agreement_id = b.agreement_id
  AND a.scntrc_status IS DISTINCT FROM b.scntrc_status
  ;



  UPDATE agreement_snapshot_expanded a
  SET scntrc_status = b.scntrc_status
  FROM agreement_snapshot b
  WHERE a.agreement_id = b.agreement_id
  AND a.scntrc_status IS DISTINCT FROM b.scntrc_status
  ;

  UPDATE agreement_snapshot_expanded_cy a
  SET scntrc_status = b.scntrc_status
  FROM agreement_snapshot_cy b
  WHERE a.agreement_id = b.agreement_id
  AND a.scntrc_status IS DISTINCT FROM b.scntrc_status
  ;

	l_end_time := timeofday()::timestamp;

	INSERT INTO etl.etl_script_execution_status(load_file_id,script_name,completed_flag,start_time,end_time)
	VALUES(p_load_file_id_in,'etl.updateSnapshotScntrcStatus',1,l_start_time,l_end_time);

	RETURN 1;


EXCEPTION
  WHEN OTHERS THEN
  RAISE NOTICE 'Exception Occurred in updateSnapshotScntrcStatus';
  RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;

	l_end_time := timeofday()::timestamp;

	INSERT INTO etl.etl_script_execution_status(load_file_id,script_name,completed_flag,start_time,end_time,errno,errmsg)
	VALUES(p_load_file_id_in,'etl.updateSnapshotScntrcStatus',0,l_start_time,l_end_time,SQLSTATE,errmsg);

  RETURN 0;

END;
$$ language plpgsql;

--------------------------------------------------------------------------------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION etl.updateSubCONFlags(p_load_id_in bigint) RETURNS INT AS $$
DECLARE
BEGIN
	/* Common for all types
	Can be done once per etl
	*/

	-- Get the contracts (key elements only without version) which have been created or updated

	CREATE TEMPORARY TABLE tmp_sub_loaded_agreements_flags(document_id varchar,document_version integer,document_code_id smallint, sub_contract_id varchar, agency_id smallint,
		latest_version_no smallint,first_version smallint ) DISTRIBUTED BY (document_id);

	INSERT INTO tmp_sub_loaded_agreements_flags
	SELECT distinct document_id,document_version,document_code_id, sub_contract_id, agency_id
	FROM subcontract_details a JOIN ref_agency_history b ON a.agency_history_id = b.agency_history_id
	WHERE coalesce(a.updated_load_id, a.created_load_id) = p_load_id_in ;

	-- Get the max version and min version

	CREATE TEMPORARY TABLE tmp_sub_loaded_agreements_1_flags(document_id varchar,document_code_id smallint, sub_contract_id varchar, agency_id smallint,
		latest_version_no smallint,first_version_no smallint )  DISTRIBUTED BY (document_id);

	INSERT INTO tmp_sub_loaded_agreements_1_flags
	SELECT a.document_id,a.document_code_id, a.sub_contract_id, c.agency_id,
	       max(a.document_version) as latest_version_no, min(a.document_version) as first_version_no
	FROM subcontract_details a JOIN tmp_sub_loaded_agreements_flags b ON a.document_id = b.document_id AND a.document_code_id = b.document_code_id AND a.sub_contract_id = b.sub_contract_id
		JOIN ref_agency_history c ON a.agency_history_id = c.agency_history_id AND c.agency_id = b.agency_id
	GROUP BY 1,2,3,4;

	RAISE NOTICE 'PCON_FLAG1';

	-- Update the versions which are no more the first versions
	-- Might have to change the disbursements linkage here

	CREATE TEMPORARY TABLE tmp_sub_agreement_flag_changes_flags (document_id varchar,document_code_id smallint, sub_contract_id varchar, agency_id smallint,
					latest_agreement_id bigint, first_agreement_id bigint,non_latest_agreement_id varchar, non_first_agreement_id varchar,
					latest_maximum_contract_amount numeric(16,2)
					) DISTRIBUTED BY (document_id);

	INSERT INTO tmp_sub_agreement_flag_changes_flags
	SELECT a.document_id,a.document_code_id, a.sub_contract_id, b.agency_id,
		MAX(CASE WHEN a.document_version = b.latest_version_no THEN agreement_id END) as latest_agreement_id,
		MAX(CASE WHEN a.document_version = b.first_version_no THEN agreement_id END) as first_agreement_id,
		group_concat(CASE WHEN a.document_version <> b.latest_version_no THEN agreement_id ELSE 0 END) as non_latest_agreement_id,
		group_concat(CASE WHEN a.document_version <> b.first_version_no THEN agreement_id ELSE 0 END) as non_first_agreement_id,
		MAX(CASE WHEN a.document_version = b.latest_version_no THEN maximum_contract_amount END) as latest_current_amount
	FROM   subcontract_details a JOIN tmp_sub_loaded_agreements_1_flags b ON a.document_id = b.document_id AND a.document_code_id = b.document_code_id AND a.sub_contract_id = b.sub_contract_id
		JOIN ref_agency_history c ON a.agency_history_id = c.agency_history_id AND c.agency_id = b.agency_id
	GROUP BY 1,2,3,4;

	-- Updating the original flag for non first agreements

	RAISE NOTICE 'PCON_FLAG2';

	CREATE TEMPORARY TABLE tmp_sub_agreements_update_flags(agreement_id bigint,first_agreement_id bigint)
	DISTRIBUTED BY (agreement_id);

	INSERT INTO tmp_sub_agreements_update_flags
	SELECT unnest(string_to_array(non_first_agreement_id,','))::int as agreement_id ,
		first_agreement_id
		FROM	tmp_sub_agreement_flag_changes_flags;

	UPDATE subcontract_details a
	SET    original_version_flag = 'N',
		original_agreement_id = b.first_agreement_id
	FROM   tmp_sub_agreements_update_flags b
	WHERE  a.agreement_id = b.agreement_id;

	TRUNCATE tmp_sub_agreements_update_flags;

	INSERT INTO tmp_sub_agreements_update_flags
	SELECT unnest(string_to_array(non_latest_agreement_id,','))::int as agreement_id , NULL as first_agreement_id
		FROM	tmp_sub_agreement_flag_changes_flags;

	UPDATE subcontract_details a
	SET    latest_flag = 'N'
	FROM   tmp_sub_agreements_update_flags b
	WHERE  a.agreement_id = b.agreement_id ;

	UPDATE subcontract_details a
	SET     original_version_flag = 'Y',
		original_agreement_id = b.first_agreement_id
	FROM    tmp_sub_agreement_flag_changes_flags  b
	WHERE  a.agreement_id = b.first_agreement_id;


	UPDATE subcontract_details a
	SET    latest_flag = 'Y'
	FROM    tmp_sub_agreement_flag_changes_flags  b
	WHERE  a.agreement_id = b.latest_agreement_id
		AND COALESCE(a.latest_flag,'N') = 'N';


	RAISE NOTICE 'PCON_FLAG3';

			RETURN 1;




EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in updateSubCONFlags';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;

	RETURN 0;

END;
$$ language plpgsql;



----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION etl.postProcessSubContracts(p_job_id_in bigint) RETURNS INT AS $$
DECLARE
	l_start_time  timestamp;
	l_end_time  timestamp;
	l_load_id bigint;
BEGIN


	-- Get the contracts (key elements only without version) which have been created or updated

	l_start_time := timeofday()::timestamp;

	SELECT load_id
	FROM etl.etl_data_load
	WHERE job_id = p_job_id_in	AND data_source_code = 'SC'
	INTO l_load_id;

	CREATE TEMPORARY TABLE tmp_sub_loaded_agreements(document_id varchar,document_version integer,document_code_id smallint, sub_contract_id varchar, agency_id smallint,
		latest_version_no smallint,first_version smallint ) DISTRIBUTED BY (document_id);

	INSERT INTO tmp_sub_loaded_agreements
	SELECT distinct document_id,document_version,document_code_id, sub_contract_id, agency_id
	FROM subcontract_details a JOIN ref_agency_history b ON a.agency_history_id = b.agency_history_id
	JOIN etl.etl_data_load c ON coalesce(a.updated_load_id, a.created_load_id) = c.load_id
	WHERE c.job_id = p_job_id_in AND c.data_source_code IN ('SC') ;

	-- Get the max version and min version

	CREATE TEMPORARY TABLE tmp_sub_loaded_agreements_1(document_id varchar,document_code_id smallint, sub_contract_id varchar, agency_id smallint,
		latest_version_no smallint,first_version_no smallint )  DISTRIBUTED BY (document_id);

	INSERT INTO tmp_sub_loaded_agreements_1
	SELECT a.document_id,a.document_code_id, a.sub_contract_id, c.agency_id,
	       max(a.document_version) as latest_version_no, min(a.document_version) as first_version_no
	FROM subcontract_details a JOIN tmp_sub_loaded_agreements b ON a.document_id = b.document_id AND a.document_code_id = b.document_code_id AND a.sub_contract_id = b.sub_contract_id
		JOIN ref_agency_history c ON a.agency_history_id = c.agency_history_id AND c.agency_id = b.agency_id
	GROUP BY 1,2,3,4;

	RAISE NOTICE 'PCON1';

	-- Update the versions which are no more the first versions
	-- Might have to change the disbursements linkage here

	CREATE TEMPORARY TABLE tmp_sub_agreement_flag_changes (document_id varchar,document_code_id smallint, sub_contract_id varchar, agency_id smallint,
					latest_agreement_id bigint, first_agreement_id bigint,non_latest_agreement_id varchar, non_first_agreement_id varchar,
					latest_maximum_contract_amount numeric(16,2)
					) DISTRIBUTED BY (document_id);

	INSERT INTO tmp_sub_agreement_flag_changes
	SELECT a.document_id,a.document_code_id, a.sub_contract_id, b.agency_id,
		MAX(CASE WHEN a.document_version = b.latest_version_no THEN agreement_id END) as latest_agreement_id,
		MAX(CASE WHEN a.document_version = b.first_version_no THEN agreement_id END) as first_agreement_id,
		group_concat(CASE WHEN a.document_version <> b.latest_version_no THEN agreement_id ELSE 0 END) as non_latest_agreement_id,
		group_concat(CASE WHEN a.document_version <> b.first_version_no THEN agreement_id ELSE 0 END) as non_first_agreement_id,
		MAX(CASE WHEN a.document_version = b.latest_version_no THEN maximum_contract_amount END) as latest_current_amount
	FROM   subcontract_details a JOIN tmp_sub_loaded_agreements_1 b ON a.document_id = b.document_id AND a.document_code_id = b.document_code_id AND a.sub_contract_id = b.sub_contract_id
		JOIN ref_agency_history c ON a.agency_history_id = c.agency_history_id AND c.agency_id = b.agency_id
	GROUP BY 1,2,3,4;

	-- Updating the original flag for non first agreements

	RAISE NOTICE 'PCON2';

	CREATE TEMPORARY TABLE tmp_sub_agreements_update(agreement_id bigint,first_agreement_id bigint)
	DISTRIBUTED BY (agreement_id);

	INSERT INTO tmp_sub_agreements_update
	SELECT unnest(string_to_array(non_first_agreement_id,','))::int as agreement_id ,
		first_agreement_id
		FROM	tmp_sub_agreement_flag_changes;

	UPDATE subcontract_details a
	SET    original_version_flag = 'N',
		original_agreement_id = b.first_agreement_id
	FROM   tmp_sub_agreements_update b
	WHERE  a.agreement_id = b.agreement_id;

	TRUNCATE tmp_sub_agreements_update;

	INSERT INTO tmp_sub_agreements_update
	SELECT unnest(string_to_array(non_latest_agreement_id,','))::int as agreement_id , NULL as first_agreement_id
		FROM	tmp_sub_agreement_flag_changes;

	UPDATE subcontract_details a
	SET    latest_flag = 'N'
	FROM   tmp_sub_agreements_update b
	WHERE  a.agreement_id = b.agreement_id;

	UPDATE subcontract_details a
	SET     original_version_flag = 'Y',
		original_agreement_id = b.first_agreement_id
	FROM    tmp_sub_agreement_flag_changes  b
	WHERE  a.agreement_id = b.first_agreement_id;


	UPDATE subcontract_details a
	SET    latest_flag = 'Y'
	FROM    tmp_sub_agreement_flag_changes  b
	WHERE  a.agreement_id = b.latest_agreement_id
		AND COALESCE(a.latest_flag,'N') = 'N';


	RAISE NOTICE 'PCON3';
	-- Populating the agreement_snapshot tables


	CREATE TEMPORARY TABLE tmp_sub_agreement_snapshot(original_agreement_id bigint,starting_year smallint,starting_year_id smallint,document_version smallint,
						     ending_year smallint, ending_year_id smallint ,rank_value smallint,agreement_id bigint,
						     effective_begin_fiscal_year smallint,effective_begin_fiscal_year_id smallint,effective_end_fiscal_year smallint,
						     effective_end_fiscal_year_id smallint,registered_fiscal_year smallint,original_version_flag char(1))
	DISTRIBUTED BY 	(original_agreement_id);

	-- Get the latest version for every year of modification

	INSERT INTO tmp_sub_agreement_snapshot
	SELECT  b.original_agreement_id, b.source_updated_fiscal_year, b.source_updated_fiscal_year_id,
		max(b.document_version) as document_version,
		lead(source_updated_fiscal_year) over (partition by original_agreement_id ORDER BY source_updated_fiscal_year),
		lead(source_updated_fiscal_year_id) over (partition by original_agreement_id ORDER BY source_updated_fiscal_year),
		rank() over (partition by original_agreement_id order by source_updated_fiscal_year ASC) as rank_value,
		NULL as agreement_id,
		max(effective_begin_fiscal_year) as effective_begin_fiscal_year,
		max(effective_begin_fiscal_year_id) as effective_begin_fiscal_year_id,
		max(effective_end_fiscal_year) as effective_end_fiscal_year,
		max(effective_end_fiscal_year_id) as effective_end_fiscal_year_id,
		NULL as registered_fiscal_year,
		'N' as original_version_flag
	FROM	tmp_sub_agreement_flag_changes a JOIN subcontract_details b ON a.first_agreement_id = b.original_agreement_id
	GROUP  BY 1,2,3;

	-- Update the agreement id based on the version number and original agreeement if

	UPDATE tmp_sub_agreement_snapshot a
	SET     agreement_id = b.agreement_id,
		registered_fiscal_year = b.registered_fiscal_year
	FROM	subcontract_details b
	WHERE   a.original_agreement_id = b.original_agreement_id
		AND a.document_version = b.document_version;

	RAISE NOTICE 'PCON4';
	-- Updating the POP years from the latest version of the agreement
	UPDATE tmp_sub_agreement_snapshot a
	SET	effective_begin_fiscal_year = b.effective_begin_fiscal_year,
		effective_begin_fiscal_year_id = b.effective_begin_fiscal_year_id,
		effective_end_fiscal_year = b.effective_end_fiscal_year,
		effective_end_fiscal_year_id = b.effective_end_fiscal_year_id
	FROM	subcontract_details b
	WHERE   a.original_agreement_id = b.original_agreement_id
		AND b.latest_flag = 'Y';

	-- Update the starting year to 2010 for the very first record of an agreement in the snapshot if starting year >2010 and pop start year prior to 2010

	UPDATE 	tmp_sub_agreement_snapshot
	SET	starting_year = 2010,
		starting_year_id = year_id
	FROM	ref_year
	WHERE	year_value = 2010
		AND starting_year > 2010
		AND rank_value = 1
		AND registered_fiscal_year <= 2010;

     -- Updating the starting year to POP start year if starting year > POP start year

		 UPDATE 	tmp_sub_agreement_snapshot
		SET	starting_year = effective_begin_fiscal_year,
		starting_year_id = effective_begin_fiscal_year_id
		WHERE rank_value = 1 AND starting_year > effective_begin_fiscal_year AND effective_begin_fiscal_year IS NOT NULL ;

		UPDATE 	tmp_sub_agreement_snapshot a
		SET	starting_year = a.registered_fiscal_year,
		starting_year_id = b.year_id
		FROM	ref_year b
		WHERE a.registered_fiscal_year = b.year_value
		AND rank_value = 1 AND starting_year > registered_fiscal_year AND registered_fiscal_year IS NOT NULL;


	-- Updating the ending year to be ending year - 1
	-- Until this step ending year of a record is equivalent to the staring year of the sucessor. So -1 should be done to ensure no overlapping

	UPDATE 	tmp_sub_agreement_snapshot
	SET	ending_year = ending_year - 1,
		ending_year_id  = year_id
	FROM	ref_year
	WHERE	year_value = ending_year - 1
		AND ending_year is not null;

	UPDATE tmp_sub_agreement_snapshot
	SET original_version_flag = 'Y'
	WHERE rank_value = 1;

	RAISE NOTICE 'PCON5';

	INSERT INTO sub_agreement_snapshot_deleted(agreement_id, original_agreement_id, starting_year,  load_id, deleted_date, job_id)
	SELECT distinct a.agreement_id, a.original_agreement_id, a.starting_year,  l_load_id, now()::timestamp, p_job_id_in
	FROM sub_agreement_snapshot a , tmp_sub_agreement_snapshot b
	WHERE a.original_agreement_id = b.original_agreement_id;


	DELETE FROM ONLY sub_agreement_snapshot a USING  tmp_sub_agreement_snapshot b WHERE a.original_agreement_id = b.original_agreement_id;

	INSERT INTO sub_agreement_snapshot(original_agreement_id, starting_year,starting_year_id,document_version,document_code_id,agency_history_id, agency_id,agency_code,agency_name,
				       agreement_id, ending_year,ending_year_id,contract_number,sub_contract_id,
				       original_contract_amount,maximum_contract_amount,description,
					vendor_history_id,vendor_id,vendor_code,vendor_name,prime_vendor_id,prime_minority_type_id, prime_minority_type_name,
					dollar_difference,
					percent_difference,
					agreement_type_id,	agreement_type_code, agreement_type_name,
					award_category_id,award_category_code,award_category_name,award_method_id,award_method_code,award_method_name,
					expenditure_object_codes,expenditure_object_names,industry_type_id,
					industry_type_name, award_size_id,effective_begin_date,effective_begin_date_id,
					effective_end_date, effective_end_date_id,registered_date,
					registered_date_id,brd_awd_no,tracking_number,rfed_amount,
					registered_year, registered_year_id,latest_flag,original_version_flag,
					effective_begin_year,effective_begin_year_id,effective_end_year,effective_end_year_id,
					minority_type_id, minority_type_name,master_agreement_id,master_contract_number,
					load_id,last_modified_date,job_id, aprv_sta)
	SELECT 	a.original_agreement_id, a.starting_year,a.starting_year_id,a.document_version,b.document_code_id,b.agency_history_id, ah.agency_id,ag.agency_code,ah.agency_name,
	        a.agreement_id, (CASE WHEN a.ending_year IS NOT NULL THEN ending_year
	        			  WHEN (b.effective_end_fiscal_year IS NULL OR b.effective_end_fiscal_year < b.registered_fiscal_year)
		              AND b.registered_fiscal_year IS NOT NULL AND a.starting_year < b.registered_fiscal_year THEN b.registered_fiscal_year
	        		      WHEN b.effective_end_fiscal_year < a.starting_year OR b.effective_end_fiscal_year IS NULL THEN a.starting_year
	        		      ELSE b.effective_end_fiscal_year END),
	        		(CASE WHEN a.ending_year IS NOT NULL THEN ending_year_id
	        			  WHEN (b.effective_end_fiscal_year IS NULL OR b.effective_end_fiscal_year < b.registered_fiscal_year)
		              AND b.registered_fiscal_year IS NOT NULL AND a.starting_year < b.registered_fiscal_year THEN b.registered_fiscal_year_id
	        		      WHEN b.effective_end_fiscal_year < a.starting_year OR b.effective_end_fiscal_year IS NULL THEN a.starting_year_id
	        		      ELSE b.effective_end_fiscal_year_id END),b.contract_number,b.sub_contract_id,
	        b.original_contract_amount,b.maximum_contract_amount,b.description,
		b.vendor_history_id,c.vendor_id, v.vendor_customer_code, c.legal_name as vendor_name, b.prime_vendor_id, n.minority_type_id as prime_minority_type_id, n.minority_type_name as prime_minority_type_name,
		coalesce(b.maximum_contract_amount,0) - coalesce(b.original_contract_amount,0) as dollar_difference,
		(CASE WHEN coalesce(b.original_contract_amount,0) = 0 THEN 0 ELSE
		ROUND((( coalesce(b.maximum_contract_amount,0) - coalesce(b.original_contract_amount,0)) * 100 )::decimal / coalesce(b.original_contract_amount,0),2) END) as percent_difference,
		e.agreement_type_id, e.agreement_type_code, e.agreement_type_name,
		f.award_category_id, f.award_category_code, f.award_category_name,am.award_method_id,am.award_method_code,am.award_method_name,g.expenditure_object_codes,
		g.expenditure_object_names, b.industry_type_id as industry_type_id,
		l.industry_type_name as industry_type_name, (CASE WHEN b.maximum_contract_amount IS NULL THEN 5 WHEN b.maximum_contract_amount <= 5000 THEN 4 WHEN b.maximum_contract_amount > 5000
		AND b.maximum_contract_amount <= 100000 THEN 3 		WHEN  b.maximum_contract_amount > 100000 AND b.maximum_contract_amount <= 1000000 THEN 2 WHEN b.maximum_contract_amount > 1000000 THEN 1
		ELSE 5 END) as award_size_id,h.date as effective_begin_date, h.date_id as effective_begin_date_id,
		i.date as effective_end_date, i.date_id as effective_end_date_id,j.date as registered_date,
		j.date_id as registered_date_id,b.brd_awd_no,b.tracking_number,b.rfed_amount,
		b.registered_fiscal_year, b.registered_fiscal_year_id,b.latest_flag,a.original_version_flag,
		a.effective_begin_fiscal_year,a.effective_begin_fiscal_year_id,a.effective_end_fiscal_year,a.effective_end_fiscal_year_id,
		m.minority_type_id, m.minority_type_name,b.master_agreement_id,d.contract_number,
		coalesce(b.updated_load_id, b.created_load_id),coalesce(b.updated_date, b.created_date), p_job_id_in, b.aprv_sta
	FROM	tmp_sub_agreement_snapshot a JOIN subcontract_details b ON a.agreement_id = b.agreement_id
		LEFT JOIN subvendor_history c ON b.vendor_history_id = c.vendor_history_id
		LEFT JOIN subvendor v ON c.vendor_id = v.vendor_id
		LEFT JOIN ref_agency_history ah ON b.agency_history_id = ah.agency_history_id
		LEFT JOIN ref_agency ag ON ah.agency_id = ag.agency_id
		LEFT JOIN history_master_agreement d ON b.master_agreement_id = d.master_agreement_id
		LEFT JOIN ref_agreement_type e ON b.agreement_type_id = e.agreement_type_id
		LEFT JOIN ref_award_category f ON b.award_category_id = f.award_category_id
		LEFT JOIN ref_award_method am ON b.award_method_id = am.award_method_id
		LEFT JOIN (SELECT x.agreement_id, GROUP_CONCAT(distinct y.expenditure_object_name) as expenditure_object_names, GROUP_CONCAT(distinct expenditure_object_code) as expenditure_object_codes
			   FROM history_agreement_accounting_line z JOIN ref_expenditure_object_history y ON z.expenditure_object_history_id = y.expenditure_object_history_id
			   JOIN ref_expenditure_object w ON y.expenditure_object_id = w.expenditure_object_id
			   JOIN history_agreement ha ON z.agreement_id = ha.agreement_id
			   JOIN subcontract_details sd ON ha.contract_number = sd.contract_number
			   JOIN tmp_sub_agreement_snapshot x ON x.agreement_id = sd.agreement_id
			   WHERE sd.latest_flag = 'Y'
			   GROUP BY 1) g ON a.agreement_id = g.agreement_id
		LEFT JOIN ref_date h ON h.date_id = b.effective_begin_date_id
		LEFT JOIN ref_date i ON i.date_id = b.effective_end_date_id
		LEFT JOIN ref_date j ON j.date_id = b.registered_date_id
		LEFT JOIN ref_industry_type l ON b.industry_type_id = l.industry_type_id
		LEFT JOIN subvendor_min_bus_type m ON b.vendor_history_id = m.vendor_history_id
		LEFT JOIN (SELECT contract_number, vendor_id, minority_type_id, minority_type_name
		FROM agreement_snapshot WHERE latest_flag = 'Y' and contract_number ilike 'CT%') n  ON b.contract_number = n.contract_number AND b.prime_vendor_id = n.vendor_id
		WHERE b.source_updated_date_id IS NOT NULL ;


	RAISE NOTICE 'PCON6';

	UPDATE sub_agreement_snapshot a
	SET minority_type_id=11,
		minority_type_name = 'Individuals & Others'
	WHERE job_id = p_job_id_in AND agreement_type_code IN ('35','36','39','40','44','65','68','79','85')
	AND ( minority_type_id IS NULL OR minority_type_id IN (1,6,7,8));

	UPDATE sub_agreement_snapshot a
	SET minority_type_id=11,
		minority_type_name = 'Individuals & Others'
	WHERE job_id = p_job_id_in AND award_method_code IN ('07','08','09','17','18','44','45','55')
	AND ( minority_type_id IS NULL OR minority_type_id IN (1,6,7,8));

	UPDATE sub_agreement_snapshot a
	SET minority_type_id=7,
		minority_type_name = 'Non-Minority'
	WHERE job_id = p_job_id_in 	AND ( minority_type_id IS NULL OR minority_type_id IN (1,6,7,8));



	-- Populating the agreement_snapshot tables related to the calendar year

	-- Get the latest version for every year of modification

	TRUNCATE tmp_sub_agreement_snapshot;

	INSERT INTO tmp_sub_agreement_snapshot
	SELECT  b.original_agreement_id, b.source_updated_calendar_year, b.source_updated_calendar_year_id,
		max(b.document_version) as document_version,
		lead(source_updated_calendar_year) over (partition by original_agreement_id ORDER BY source_updated_calendar_year),
		lead(source_updated_calendar_year_id) over (partition by original_agreement_id ORDER BY source_updated_calendar_year),
		rank() over (partition by original_agreement_id order by source_updated_calendar_year ASC) as rank_value,
		NULL as agreement_id,
		max(effective_begin_calendar_year) as effective_begin_fiscal_year,
		max(effective_begin_calendar_year_id) as effective_begin_fiscal_year_id,
		max(effective_end_calendar_year) as effective_end_fiscal_year,
		max(effective_end_calendar_year_id) as effective_end_fiscal_year_id,
		NULL as registered_fiscal_year,
		'N' as original_version_flag
	FROM	tmp_sub_agreement_flag_changes a JOIN subcontract_details b ON a.first_agreement_id = b.original_agreement_id
	GROUP  BY 1,2,3;

	-- Update the agreement id based on the version number and original agreeement if

	UPDATE tmp_sub_agreement_snapshot a
	SET     agreement_id = b.agreement_id,
		registered_fiscal_year = b.registered_calendar_year
	FROM	subcontract_details b
	WHERE   a.original_agreement_id = b.original_agreement_id
		AND a.document_version = b.document_version;

	RAISE NOTICE 'PCON7';

	-- Updating the POP years from the latest version of the agreement
	UPDATE tmp_sub_agreement_snapshot a
	SET	effective_begin_fiscal_year = b.effective_begin_calendar_year,
		effective_begin_fiscal_year_id = b.effective_begin_calendar_year_id,
		effective_end_fiscal_year = b.effective_end_calendar_year,
		effective_end_fiscal_year_id = b.effective_end_calendar_year_id
	FROM	subcontract_details b
	WHERE   a.original_agreement_id = b.original_agreement_id
		AND b.latest_flag = 'Y';

	-- Update the starting year to 2010 for the very first record of an agreement in the snapshot if starting year >2010 and pop start year prior to 2010

	UPDATE 	tmp_sub_agreement_snapshot
	SET	starting_year = 2010,
		starting_year_id = year_id
	FROM	ref_year
	WHERE	year_value = 2010
		AND starting_year > 2010
		AND rank_value = 1
		AND registered_fiscal_year <= 2010;

		-- Updating the starting_year to effective_begin_fiscal_year if starting_year > effective_begin_fiscal_year


		 UPDATE 	tmp_sub_agreement_snapshot
		SET	starting_year = effective_begin_fiscal_year,
		starting_year_id = effective_begin_fiscal_year_id
		WHERE rank_value = 1 AND starting_year > effective_begin_fiscal_year AND effective_begin_fiscal_year IS NOT NULL;

		UPDATE 	tmp_sub_agreement_snapshot a
		SET	starting_year = a.registered_fiscal_year,
		starting_year_id = b.year_id
		FROM	ref_year b
		WHERE a.registered_fiscal_year = b.year_value
		AND rank_value = 1 AND starting_year > registered_fiscal_year AND registered_fiscal_year IS NOT NULL;

	-- Updating the ending year to be ending year - 1
	-- Until this step ending year of a record is equivalent to the staring year of the sucessor. So -1 should be done to ensure no overlapping

	UPDATE 	tmp_sub_agreement_snapshot
	SET	ending_year = ending_year - 1,
		ending_year_id  = year_id
	FROM	ref_year
	WHERE	year_value = ending_year - 1
		AND ending_year is not null;

	UPDATE tmp_sub_agreement_snapshot
	SET original_version_flag = 'Y'
	WHERE rank_value = 1;

	RAISE NOTICE 'PCON8';

	INSERT INTO sub_agreement_snapshot_cy_deleted(agreement_id, original_agreement_id, starting_year,  load_id, deleted_date, job_id)
	SELECT distinct a.agreement_id, a.original_agreement_id, a.starting_year, l_load_id, now()::timestamp, p_job_id_in
	FROM sub_agreement_snapshot_cy a , tmp_sub_agreement_snapshot b
	WHERE a.original_agreement_id = b.original_agreement_id;

	DELETE FROM ONLY sub_agreement_snapshot_cy a USING  tmp_sub_agreement_snapshot b WHERE a.original_agreement_id = b.original_agreement_id;

	INSERT INTO sub_agreement_snapshot_cy(original_agreement_id, starting_year,starting_year_id,document_version,document_code_id,agency_history_id, agency_id,agency_code,agency_name,
				       agreement_id, ending_year,ending_year_id,contract_number,sub_contract_id,
				       original_contract_amount,maximum_contract_amount,description,
					vendor_history_id,vendor_id,vendor_code,vendor_name,prime_vendor_id,prime_minority_type_id, prime_minority_type_name,
					dollar_difference,
					percent_difference,
					agreement_type_id,agreement_type_code, agreement_type_name,
					award_category_id,award_category_code,award_category_name,award_method_id,award_method_code,award_method_name,expenditure_object_codes,
					expenditure_object_names,industry_type_id,
					industry_type_name,award_size_id,effective_begin_date,effective_begin_date_id,
					effective_end_date, effective_end_date_id,registered_date,
					registered_date_id,brd_awd_no,tracking_number,rfed_amount,
					registered_year, registered_year_id,latest_flag,original_version_flag,
					effective_begin_year,effective_begin_year_id,effective_end_year,effective_end_year_id,
					minority_type_id, minority_type_name,master_agreement_id,master_contract_number,
					load_id,last_modified_date, job_id)
	SELECT 	a.original_agreement_id, a.starting_year,a.starting_year_id,a.document_version,b.document_code_id,b.agency_history_id, ah.agency_id,ag.agency_code,ah.agency_name,
		a.agreement_id, (CASE WHEN a.ending_year IS NOT NULL THEN ending_year
		              WHEN (b.effective_end_calendar_year IS NULL OR b.effective_end_calendar_year < b.registered_calendar_year)
		              AND b.registered_calendar_year IS NOT NULL AND a.starting_year < b.registered_calendar_year THEN b.registered_calendar_year
				      WHEN b.effective_end_calendar_year < a.starting_year  OR b.effective_end_calendar_year IS NULL THEN a.starting_year
				      ELSE b.effective_end_calendar_year END),
				(CASE WHEN a.ending_year IS NOT NULL THEN ending_year_id
					    WHEN (b.effective_end_calendar_year IS NULL OR b.effective_end_calendar_year < b.registered_calendar_year)
		              AND b.registered_calendar_year IS NOT NULL AND a.starting_year < b.registered_calendar_year THEN b.registered_calendar_year_id
				      WHEN b.effective_end_calendar_year < a.starting_year OR b.effective_end_calendar_year IS NULL THEN a.starting_year_id
				      ELSE b.effective_end_calendar_year_id END),b.contract_number,b.sub_contract_id,
		b.original_contract_amount,b.maximum_contract_amount,b.description,
		b.vendor_history_id,c.vendor_id, v.vendor_customer_code, c.legal_name as vendor_name,b.prime_vendor_id, n.minority_type_id as prime_minority_type_id, n.minority_type_name as prime_minority_type_name,
		coalesce(b.maximum_contract_amount,0) - coalesce(b.original_contract_amount,0) as  dollar_difference,
		(CASE WHEN coalesce(b.original_contract_amount,0) = 0 THEN 0 ELSE
		ROUND((( coalesce(b.maximum_contract_amount,0) - coalesce(b.original_contract_amount,0)) * 100 )::decimal / coalesce(b.original_contract_amount,0),2) END) as percent_difference,
		e.agreement_type_id,e.agreement_type_code, e.agreement_type_name,
		f.award_category_id, f.award_category_code, f.award_category_name,am.award_method_id,am.award_method_code,am.award_method_name,g.expenditure_object_codes,
		g.expenditure_object_names,	b.industry_type_id as industry_type_id,
		industry_type_name as industry_type_name,(CASE WHEN b.maximum_contract_amount IS NULL THEN 5 WHEN b.maximum_contract_amount <= 5000 THEN 4 WHEN b.maximum_contract_amount > 5000
		AND b.maximum_contract_amount <= 100000 THEN 3 	WHEN  b.maximum_contract_amount > 100000 AND b.maximum_contract_amount <= 1000000 THEN 2 WHEN b.maximum_contract_amount > 1000000 THEN 1
		ELSE 5 END) as award_size_id,h.date as effective_begin_date, h.date_id as effective_begin_date_id,
		i.date as effective_end_date, i.date_id as effective_end_date_id,j.date as registered_date,
		j.date_id as registered_date_id,b.brd_awd_no,b.tracking_number,b.rfed_amount,
		b.registered_calendar_year, b.registered_calendar_year_id,b.latest_flag,a.original_version_flag,
		a.effective_begin_fiscal_year,a.effective_begin_fiscal_year_id,a.effective_end_fiscal_year,a.effective_end_fiscal_year_id,
		m.minority_type_id, m.minority_type_name,b.master_agreement_id,d.contract_number,
		coalesce(b.updated_load_id, b.created_load_id),coalesce(b.updated_date, b.created_date), p_job_id_in
	FROM	tmp_sub_agreement_snapshot a JOIN subcontract_details b ON a.agreement_id = b.agreement_id
		LEFT JOIN subvendor_history c ON b.vendor_history_id = c.vendor_history_id
		LEFT JOIN subvendor v ON c.vendor_id = v.vendor_id
		LEFT JOIN ref_agency_history ah ON b.agency_history_id = ah.agency_history_id
		LEFT JOIN ref_agency ag ON ah.agency_id = ag.agency_id
		LEFT JOIN history_master_agreement d ON b.master_agreement_id = d.master_agreement_id
		LEFT JOIN ref_agreement_type e ON b.agreement_type_id = e.agreement_type_id
		LEFT JOIN ref_award_category f ON b.award_category_id = f.award_category_id
		LEFT JOIN ref_award_method am ON b.award_method_id = am.award_method_id
		LEFT JOIN (SELECT x.agreement_id, GROUP_CONCAT(distinct y.expenditure_object_name) as expenditure_object_names, GROUP_CONCAT(distinct expenditure_object_code) as expenditure_object_codes
			   FROM history_agreement_accounting_line z JOIN ref_expenditure_object_history y ON z.expenditure_object_history_id = y.expenditure_object_history_id
			   JOIN ref_expenditure_object w ON y.expenditure_object_id = w.expenditure_object_id
			   JOIN history_agreement ha ON z.agreement_id = ha.agreement_id
			   JOIN subcontract_details sd ON ha.contract_number = sd.contract_number
			   JOIN tmp_sub_agreement_snapshot x ON x.agreement_id = sd.agreement_id
			   WHERE sd.latest_flag = 'Y'
			   GROUP BY 1) g ON a.agreement_id = g.agreement_id
		LEFT JOIN ref_date h ON h.date_id = b.effective_begin_date_id
		LEFT JOIN ref_date i ON i.date_id = b.effective_end_date_id
		LEFT JOIN ref_date j ON j.date_id = b.registered_date_id
		LEFT JOIN ref_industry_type l ON b.industry_type_id = l.industry_type_id
		LEFT JOIN subvendor_min_bus_type m ON b.vendor_history_id = m.vendor_history_id
		LEFT JOIN (SELECT contract_number, vendor_id, minority_type_id, minority_type_name
		FROM agreement_snapshot_cy WHERE latest_flag = 'Y' and contract_number ilike 'CT%') n  ON b.contract_number = n.contract_number AND b.prime_vendor_id = n.vendor_id
		WHERE b.source_updated_date_id IS NOT NULL ;

	RAISE NOTICE 'PCON9';

	UPDATE sub_agreement_snapshot_cy a
	SET minority_type_id=11,
		minority_type_name = 'Individuals & Others'
	WHERE job_id = p_job_id_in AND agreement_type_code IN ('35','36','39','40','44','65','68','79','85')
	AND ( minority_type_id IS NULL OR minority_type_id IN (1,6,7,8));

	UPDATE sub_agreement_snapshot_cy a
	SET minority_type_id=11,
		minority_type_name = 'Individuals & Others'
	WHERE job_id = p_job_id_in AND award_method_code IN ('07','08','09','17','18','44','45','55')
	AND ( minority_type_id IS NULL OR minority_type_id IN (1,6,7,8));

	UPDATE sub_agreement_snapshot_cy a
	SET minority_type_id=7,
		minority_type_name = 'Non-Minority'
	WHERE job_id = p_job_id_in 	AND ( minority_type_id IS NULL OR minority_type_id IN (1,6,7,8));

	-- Associate Disbursement line item to the original version of the agreement

	CREATE TEMPORARY TABLE tmp_sub_ct_fms_line_item(disbursement_line_item_id bigint, agreement_id bigint,maximum_contract_amount numeric(16,2))
	DISTRIBUTED BY (disbursement_line_item_id);

	CREATE TEMPORARY TABLE tmp_sub_agreement(agreement_id bigint,first_agreement_id bigint,maximum_contract_amount numeric(16,2))
	DISTRIBUTED BY (agreement_id);

	INSERT INTO tmp_sub_agreement
	SELECT unnest(string_to_array(non_first_agreement_id,','))::int as agreement_id ,
		first_agreement_id,
		latest_maximum_contract_amount
	FROM   tmp_sub_agreement_flag_changes;

	CREATE TEMPORARY TABLE tmp_sub_agreement_non_zero(agreement_id bigint,first_agreement_id bigint,maximum_contract_amount numeric(16,2))
	DISTRIBUTED BY (agreement_id);

	INSERT INTO tmp_sub_agreement_non_zero
	SELECT agreement_id, first_agreement_id, maximum_contract_amount FROM
	tmp_sub_agreement WHERE agreement_id > 0;


	CREATE TEMPORARY TABLE tmp_sub_ct_fms_non_partial_disbs(disbursement_line_item_id bigint, agreement_id bigint)
	DISTRIBUTED BY (agreement_id);

	INSERT INTO tmp_sub_ct_fms_non_partial_disbs
	SELECT disbursement_line_item_id, agreement_id
	FROM subcontract_spending;

	INSERT INTO tmp_sub_ct_fms_line_item
	SELECT disbursement_line_item_id, b.first_agreement_id
	FROM tmp_sub_ct_fms_non_partial_disbs a JOIN  tmp_sub_agreement_non_zero b ON a.agreement_id = b.agreement_id;


	UPDATE subcontract_spending a
	SET	agreement_id = b.agreement_id
	FROM	tmp_sub_ct_fms_line_item b
	WHERE	a.disbursement_line_item_id = b.disbursement_line_item_id;

	UPDATE subcontract_spending_details a
	SET	agreement_id = b.agreement_id
	FROM	tmp_sub_ct_fms_line_item b
	WHERE	a.disbursement_line_item_id = b.disbursement_line_item_id;


	 RAISE NOTICE 'PCON10';

	 -- updating maximum_contract_amount in disbursement_line_item_details

	UPDATE subcontract_spending_details a
	SET	maximum_contract_amount = c.maximum_contract_amount,
		industry_type_id = c.industry_type_id,
		industry_type_name = c.industry_type_name,
		agreement_type_code = c.agreement_type_code,
		award_method_code = c.award_method_code,
		contract_industry_type_id = c.industry_type_id,
		contract_minority_type_id = c.minority_type_id,
		purpose = c.description
	FROM	tmp_sub_ct_fms_line_item b, sub_agreement_snapshot c
	WHERE	a.disbursement_line_item_id = b.disbursement_line_item_id
		AND a.agreement_id = c.original_agreement_id AND a.fiscal_year between c.starting_year AND c.ending_year;

	 -- updating maximum_contract_amount_cy in disbursement_line_item_details

	UPDATE subcontract_spending_details a
	SET	maximum_contract_amount_cy = c.maximum_contract_amount,
		contract_industry_type_id_cy = c.industry_type_id,
		contract_minority_type_id_cy = c.minority_type_id,
		purpose_cy = c.description
	FROM	tmp_sub_ct_fms_line_item b, sub_agreement_snapshot_cy c
	WHERE	a.disbursement_line_item_id = b.disbursement_line_item_id
		AND a.agreement_id = c.original_agreement_id AND a.calendar_fiscal_year between c.starting_year AND c.ending_year;


    RAISE NOTICE 'PCON11';

	-- End of associating Disbursement line item to the original version of an agreement

	l_end_time := timeofday()::timestamp;

	INSERT INTO etl.etl_script_execution_status(job_id,script_name,completed_flag,start_time,end_time)
	VALUES(p_job_id_in,'etl.postProcessSubContracts',1,l_start_time,l_end_time);

			RETURN 1;

	/* End of one time changes */


EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in postProcessSubContracts';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;

	l_end_time := timeofday()::timestamp;

	INSERT INTO etl.etl_script_execution_status(job_id,script_name,completed_flag,start_time,end_time,errno,errmsg)
	VALUES(p_job_id_in,'etl.postProcessSubContracts',0,l_start_time,l_end_time,SQLSTATE,SQLERRM);

	RETURN 0;

END;
$$ language plpgsql;


--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION etl.refreshSubContractsPreAggregateTables(p_job_id_in bigint) RETURNS INT AS $$
DECLARE
	l_start_time  timestamp;
	l_end_time  timestamp;

BEGIN



	l_start_time := timeofday()::timestamp;


	TRUNCATE sub_agreement_snapshot_expanded;

	INSERT INTO sub_agreement_snapshot_expanded
SELECT  original_agreement_id ,
	agreement_id,
	fiscal_year ,
	description ,
	contract_number ,
	sub_contract_id,
	vendor_id ,
	prime_vendor_id,
	prime_minority_type_id,
	agency_id,
	industry_type_id,
	award_size_id,
	original_contract_amount ,
	maximum_contract_amount ,
	rfed_amount,
	starting_year,
	ending_year ,
	dollar_difference ,
	percent_difference ,
	award_method_id,
	document_code_id,
	minority_type_id,
	minority_type_name,
	status_flag
FROM
(SELECT original_agreement_id,
	agreement_id,
	generate_series(effective_begin_year,effective_end_year,1) as fiscal_year,
	description,
	contract_number,
	sub_contract_id,
	vendor_id,
	prime_vendor_id,
	prime_minority_type_id,
	agency_id,
	industry_type_id,
	award_size_id,
	original_contract_amount,
	maximum_contract_amount,
	rfed_amount,
	starting_year,
	ending_year,
	dollar_difference,
	percent_difference,
	award_method_id,
	document_code_id,
	minority_type_id,
	minority_type_name,
	'A' as status_flag
FROM	sub_agreement_snapshot ) expanded_tbl  WHERE fiscal_year between starting_year AND ending_year
AND fiscal_year >= 2010 AND ( (fiscal_year <= extract(year from now()::date) AND extract(month from now()::date) <= 6) OR
		     (fiscal_year <= (extract(year from now()::date)::smallint)+1 AND extract(month from now()::date) > 6) );

INSERT INTO sub_agreement_snapshot_expanded
SELECT original_agreement_id,
	agreement_id,
	registered_year,
	description,
	contract_number,
	sub_contract_id,
	vendor_id,
	prime_vendor_id,
	prime_minority_type_id,
	agency_id,
	industry_type_id,
	award_size_id,
	original_contract_amount,
	maximum_contract_amount,
	rfed_amount,
	starting_year,
	ending_year,
	dollar_difference,
	percent_difference,
	award_method_id,
	document_code_id,
	minority_type_id,
	minority_type_name,
	'R' as status_flag
FROM	sub_agreement_snapshot
WHERE registered_year between starting_year AND ending_year
AND registered_year >= 2010 ;

RAISE NOTICE 'PRE_CON_AGGR1';


-- changes for agreement_snapshot_expanded_cy

	TRUNCATE sub_agreement_snapshot_expanded_cy;


INSERT INTO sub_agreement_snapshot_expanded_cy
SELECT  original_agreement_id ,
	agreement_id,
	fiscal_year ,
	description ,
	contract_number ,
	sub_contract_id,
	vendor_id ,
	prime_vendor_id ,
	prime_minority_type_id,
	agency_id,
	industry_type_id,
	award_size_id,
	original_contract_amount ,
	maximum_contract_amount ,
	rfed_amount,
	starting_year,
	ending_year ,
	dollar_difference ,
	percent_difference ,
	award_method_id,
	document_code_id,
	minority_type_id,
	minority_type_name,
	status_flag
FROM
(SELECT original_agreement_id,
	agreement_id,
	generate_series(effective_begin_year,effective_end_year,1) as fiscal_year,
	description,
	contract_number,
	sub_contract_id,
	vendor_id,
	prime_vendor_id ,
	prime_minority_type_id,
	agency_id,
	industry_type_id,
	award_size_id,
	original_contract_amount,
	maximum_contract_amount,
	rfed_amount,
	starting_year,
	ending_year,
	dollar_difference,
	percent_difference,
	award_method_id,
	document_code_id,
	minority_type_id,
	minority_type_name,
	'A' as status_flag
FROM	sub_agreement_snapshot_cy ) expanded_tbl WHERE fiscal_year between starting_year AND ending_year
AND fiscal_year >= 2010 AND (fiscal_year <= extract(year from now()::date) ) ;

INSERT INTO sub_agreement_snapshot_expanded_cy
SELECT original_agreement_id,
	agreement_id,
	registered_year as fiscal_year,
	description,
	contract_number,
	sub_contract_id,
	vendor_id,
	prime_vendor_id ,
	prime_minority_type_id,
	agency_id,
	industry_type_id,
	award_size_id,
	original_contract_amount,
	maximum_contract_amount,
	rfed_amount,
	starting_year,
	ending_year,
	dollar_difference,
	percent_difference,
	award_method_id,
	document_code_id,
	minority_type_id,
	minority_type_name,
	'R' as status_flag
FROM	sub_agreement_snapshot_cy
WHERE registered_year between starting_year AND ending_year
AND registered_year >= 2010 ;

RAISE NOTICE 'PRE_CON_AGGR4';


	l_end_time := timeofday()::timestamp;

	INSERT INTO etl.etl_script_execution_status(job_id,script_name,completed_flag,start_time,end_time)
	VALUES(p_job_id_in,'etl.refreshSubContractsPreAggregateTables',1,l_start_time,l_end_time);

			RETURN 1;



EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in refreshSubContractsPreAggregateTables';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;

	l_end_time := timeofday()::timestamp;

	INSERT INTO etl.etl_script_execution_status(job_id,script_name,completed_flag,start_time,end_time,errno,errmsg)
	VALUES(p_job_id_in,'etl.refreshSubContractsPreAggregateTables',0,l_start_time,l_end_time,SQLSTATE,SQLERRM);

	RETURN 0;

END;
$$ language plpgsql;



----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION etl.refreshCommonTransactionTables(p_job_id_in bigint) RETURNS INT AS $$
DECLARE
	l_start_time  timestamp;
	l_end_time  timestamp;
  agreement_id_max integer;
  agreement_id_pagination integer DEFAULT 2000;
  agreement_id_loop_end integer;

BEGIN



	l_start_time := timeofday()::timestamp;

	/*
	DELETE FROM ONLY all_agreement_transactions a
	USING agreement_snapshot b
	WHERE a.agreement_id = b.agreement_id
	AND b.job_id = p_job_id_in AND a.is_prime_or_sub = 'P';


	DELETE FROM all_agreement_transactions WHERE is_prime_or_sub = 'S';
	*/

	TRUNCATE TABLE all_agreement_transactions;

	INSERT INTO all_agreement_transactions(original_agreement_id, document_version, document_code_id, agency_history_id,
            agency_id, agency_code, agency_name, agreement_id, starting_year,
            starting_year_id, ending_year, ending_year_id, registered_year,
            registered_year_id, contract_number, original_contract_amount,
            maximum_contract_amount, description, vendor_history_id, vendor_id,
            vendor_code, vendor_name, prime_vendor_id, prime_vendor_name,
            prime_minority_type_id, prime_minority_type_name,dollar_difference, percent_difference,
            master_agreement_id, master_contract_number, agreement_type_id,
            agreement_type_code, agreement_type_name, award_category_id,
            award_category_code, award_category_name, award_method_id, award_method_code,
            award_method_name, expenditure_object_codes, expenditure_object_names,
            industry_type_id, industry_type_name, award_size_id, effective_begin_date,
            effective_begin_date_id, effective_begin_year, effective_begin_year_id,
            effective_end_date, effective_end_date_id, effective_end_year,
            effective_end_year_id, registered_date, registered_date_id, brd_awd_no,
            tracking_number, rfed_amount, minority_type_id, minority_type_name,
            master_agreement_yn, has_children, has_mwbe_children, original_version_flag, latest_flag,
            load_id, last_modified_date, last_modified_year_id, is_prime_or_sub,
            is_minority_vendor, vendor_type, contract_original_agreement_id,
            is_subvendor, associated_prime_vendor_name, mwbe_category_ui, job_id, scntrc_status, scntrc_status_name, aprv_sta, aprv_sta_name)
    SELECT  original_agreement_id, document_version, document_code_id, agency_history_id,
            agency_id, agency_code, agency_name, agreement_id, starting_year,
            starting_year_id, ending_year, ending_year_id, registered_year,
            registered_year_id, contract_number, original_contract_amount,
            maximum_contract_amount, description, vendor_history_id, vendor_id,
            vendor_code, vendor_name, vendor_id as prime_vendor_id, vendor_name as prime_vendor_name,
            minority_type_id as prime_minority_type_id, minority_type_name as prime_minority_type_name,dollar_difference, percent_difference,
            master_agreement_id, master_contract_number, agreement_type_id,
            agreement_type_code, agreement_type_name, award_category_id,
            award_category_code, award_category_name, award_method_id, award_method_code,
            award_method_name, expenditure_object_codes, expenditure_object_names,
            industry_type_id, industry_type_name, award_size_id, effective_begin_date,
            effective_begin_date_id, effective_begin_year, effective_begin_year_id,
            effective_end_date, effective_end_date_id, effective_end_year,
            effective_end_year_id, registered_date, registered_date_id, brd_awd_no,
            tracking_number, rfed_amount, minority_type_id, minority_type_name,
            master_agreement_yn, has_children, has_mwbe_children, original_version_flag, latest_flag,
            load_id, last_modified_date, b.nyc_year_id as last_modified_year_id, 'P' as is_prime_or_sub,
            (CASE WHEN minority_type_id in (2,3,4,5,9) THEN 'Y' ELSE 'N' END) as is_minority_vendor,
            (CASE WHEN minority_type_id in (2,3,4,5,9) THEN 'PM' ELSE 'P' END) as vendor_type,
            original_agreement_id as contract_original_agreement_id,
            'No' as is_subvendor, 'N/A' as associated_prime_vendor_name,
            (CASE WHEN minority_type_id = 2 THEN 'Black American' WHEN minority_type_id = 3 THEN 'Hispanic American' WHEN minority_type_id = 7 THEN 'Non-M/WBE'
				  WHEN minority_type_id = 9 THEN 'Women' WHEN minority_type_id = 11 THEN 'Individuals and Others' ELSE 'Asian American' END) AS mwbe_category_ui, job_id,
            COALESCE(scntrc_status,0) as scntrc_status,
            (CASE WHEN scntrc_status = 1 THEN 'No Data Entered' WHEN scntrc_status = 2 THEN 'Yes' WHEN scntrc_status = 3 THEN 'No' ELSE 'Prior Year Exclusions' END) AS scntrc_status_name,
            6 AS aprv_sta, 'N/A' AS aprv_sta_name
       FROM agreement_snapshot a LEFT JOIN ref_date b ON a.last_modified_date::date = b.date;


      INSERT INTO all_agreement_transactions(original_agreement_id, document_version, document_code_id, agency_history_id,
            agency_id, agency_code, agency_name, agreement_id, starting_year,
            starting_year_id, ending_year, ending_year_id, registered_year,
            registered_year_id, contract_number, sub_contract_id, original_contract_amount,
            maximum_contract_amount, description, vendor_history_id, vendor_id,
            vendor_code, vendor_name, prime_vendor_id, prime_vendor_name,
            prime_minority_type_id, prime_minority_type_name,dollar_difference,
            percent_difference, agreement_type_id, agreement_type_code, agreement_type_name,
            award_category_id, award_category_code, award_category_name,
            award_method_id, award_method_code, award_method_name, expenditure_object_codes,
            expenditure_object_names, industry_type_id, industry_type_name,
            award_size_id, effective_begin_date, effective_begin_date_id,
            effective_begin_year, effective_begin_year_id, effective_end_date,
            effective_end_date_id, effective_end_year, effective_end_year_id,
            registered_date, registered_date_id, brd_awd_no, tracking_number,
            rfed_amount, minority_type_id, minority_type_name, original_version_flag,
            master_agreement_id,master_contract_number,master_agreement_yn,
            latest_flag, load_id, last_modified_date, last_modified_year_id, is_prime_or_sub,
            is_minority_vendor, vendor_type, contract_original_agreement_id,
            is_subvendor, associated_prime_vendor_name, mwbe_category_ui, job_id, scntrc_status, scntrc_status_name, aprv_sta, aprv_sta_name)
     SELECT a.original_agreement_id, document_version, document_code_id, agency_history_id,
            agency_id, agency_code, agency_name, agreement_id, starting_year,
            starting_year_id, ending_year, ending_year_id, registered_year,
            registered_year_id, a.contract_number, a.sub_contract_id, a.original_contract_amount,
            maximum_contract_amount, description, vendor_history_id, a.vendor_id,
            vendor_code, vendor_name, prime_vendor_id, (CASE WHEN a.prime_vendor_id = 0 THEN 'N/A (PRIVACY/SECURITY)' ELSE c.legal_name END) as prime_vendor_name,
            prime_minority_type_id, prime_minority_type_name,dollar_difference,
            percent_difference, agreement_type_id, agreement_type_code, agreement_type_name,
            award_category_id, award_category_code, award_category_name,
            award_method_id, award_method_code, award_method_name, expenditure_object_codes,
            expenditure_object_names, industry_type_id, industry_type_name,
            award_size_id, effective_begin_date, effective_begin_date_id,
            effective_begin_year, effective_begin_year_id, effective_end_date,
            effective_end_date_id, effective_end_year, effective_end_year_id,
            registered_date, registered_date_id, brd_awd_no, tracking_number,
            rfed_amount, minority_type_id, minority_type_name, original_version_flag,
            master_agreement_id,master_contract_number,'N' as master_agreement_yn,
            latest_flag, load_id, last_modified_date,  b.nyc_year_id as last_modified_year_id, 'S' as is_prime_or_sub,
            (CASE WHEN minority_type_id in (2,3,4,5,9) THEN 'Y' ELSE 'N' END) as is_minority_vendor,
            (CASE WHEN minority_type_id in (2,3,4,5,9) THEN 'SM' ELSE 'S' END) as vendor_type,
            d.original_agreement_id as contract_original_agreement_id,
            'Yes' as is_subvendor, (CASE WHEN a.prime_vendor_id = 0 THEN 'N/A (PRIVACY/SECURITY)' ELSE c.legal_name END) as associated_prime_vendor_name,
            (CASE WHEN minority_type_id = 2 THEN 'Black American' WHEN minority_type_id = 3 THEN 'Hispanic American' WHEN minority_type_id = 7 THEN 'Non-M/WBE'
				  WHEN minority_type_id = 9 THEN 'Women' WHEN minority_type_id = 11 THEN 'Individuals and Others' ELSE 'Asian American' END) AS mwbe_category_ui, job_id,
			COALESCE(e.scntrc_status,0) as scntrc_status,
			CASE
			WHEN e.scntrc_status = 1 THEN 'No Data Entered'
			WHEN e.scntrc_status = 2 THEN 'Yes'
			WHEN e.scntrc_status = 3 THEN 'No'
			ELSE 'Prior Year Exclusions'
			END AS scntrc_status_name,
            (CASE WHEN aprv_sta=4 THEN (CASE WHEN payments.check_amount > 0 THEN  4 ELSE 1 END) WHEN aprv_sta is NULL THEN 0 ELSE aprv_sta END) AS aprv_sta,
            (CASE WHEN aprv_sta=4 THEN (CASE WHEN payments.check_amount >0 THEN 'ACCO Approved Sub Vendor' ELSE 'No Subcontract Payments Submitted' END )
	        WHEN aprv_sta=2 THEN 'ACCO Rejected Sub Vendor'
	        WHEN aprv_sta=5 THEN 'ACCO Canceled Sub Vendor'
	        WHEN aprv_sta=3 THEN 'ACCO Reviewing Sub Vendor'
	        WHEN aprv_sta is NULL THEN 'No Subcontract Information Submitted'
            END) as aprv_sta_name
     FROM sub_agreement_snapshot a
     LEFT JOIN ref_date b ON a.last_modified_date::date = b.date
     LEFT JOIN vendor c ON a.prime_vendor_id = c.vendor_id
     LEFT JOIN (select original_agreement_id, contract_number from history_agreement where latest_flag = 'Y') d ON a.contract_number = d.contract_number
     LEFT JOIN (select contract_number, sub_contract_id, SUM(check_eft_amount) as check_amount from subcontract_spending group by 1,2) payments on a.contract_number = payments.contract_number and a.sub_contract_id= payments.sub_contract_id
     LEFT JOIN (SELECT a.contract_number,a.scntrc_status FROM agreement_snapshot a WHERE latest_flag = 'Y') e ON a.contract_number = e.contract_number;

    UPDATE all_agreement_transactions a
    SET scntrc_status_name = b.scntrc_status_name
    FROM ref_subcontract_status b
    WHERE a.scntrc_status = b.scntrc_status
    AND a.scntrc_status_name IS DISTINCT FROM b.scntrc_status_name;

RAISE NOTICE 'REF COMMON TT1';

/*
DELETE FROM ONLY all_agreement_transactions_cy a
	USING agreement_snapshot_cy b
	WHERE a.agreement_id = b.agreement_id
	AND b.job_id = p_job_id_in AND a.is_prime_or_sub = 'P';
	*/

	TRUNCATE TABLE all_agreement_transactions_cy ;

	INSERT INTO all_agreement_transactions_cy(original_agreement_id, document_version, document_code_id, agency_history_id,
            agency_id, agency_code, agency_name, agreement_id, starting_year,
            starting_year_id, ending_year, ending_year_id, registered_year,
            registered_year_id, contract_number, original_contract_amount,
            maximum_contract_amount, description, vendor_history_id, vendor_id,
            vendor_code, vendor_name, prime_vendor_id, prime_vendor_name,
            prime_minority_type_id, prime_minority_type_name,dollar_difference, percent_difference,
            master_agreement_id, master_contract_number, agreement_type_id,
            agreement_type_code, agreement_type_name, award_category_id,
            award_category_code, award_category_name, award_method_id, award_method_code,
            award_method_name, expenditure_object_codes, expenditure_object_names,
            industry_type_id, industry_type_name, award_size_id, effective_begin_date,
            effective_begin_date_id, effective_begin_year, effective_begin_year_id,
            effective_end_date, effective_end_date_id, effective_end_year,
            effective_end_year_id, registered_date, registered_date_id, brd_awd_no,
            tracking_number, rfed_amount, minority_type_id, minority_type_name,
            master_agreement_yn, has_children, has_mwbe_children, original_version_flag, latest_flag,
            load_id, last_modified_date, last_modified_year_id, is_prime_or_sub,
            is_minority_vendor, vendor_type, contract_original_agreement_id,
            is_subvendor, associated_prime_vendor_name, mwbe_category_ui, job_id, scntrc_status, scntrc_status_name)
    SELECT  original_agreement_id, document_version, document_code_id, agency_history_id,
            agency_id, agency_code, agency_name, agreement_id, starting_year,
            starting_year_id, ending_year, ending_year_id, registered_year,
            registered_year_id, contract_number, original_contract_amount,
            maximum_contract_amount, description, vendor_history_id, vendor_id,
            vendor_code, vendor_name, vendor_id as prime_vendor_id, vendor_name as prime_vendor_name,
            minority_type_id as prime_minority_type_id, minority_type_name as prime_minority_type_name,dollar_difference, percent_difference,
            master_agreement_id, master_contract_number, agreement_type_id,
            agreement_type_code, agreement_type_name, award_category_id,
            award_category_code, award_category_name, award_method_id, award_method_code,
            award_method_name, expenditure_object_codes, expenditure_object_names,
            industry_type_id, industry_type_name, award_size_id, effective_begin_date,
            effective_begin_date_id, effective_begin_year, effective_begin_year_id,
            effective_end_date, effective_end_date_id, effective_end_year,
            effective_end_year_id, registered_date, registered_date_id, brd_awd_no,
            tracking_number, rfed_amount, minority_type_id, minority_type_name,
            master_agreement_yn, has_children, has_mwbe_children, original_version_flag, latest_flag,
            load_id, last_modified_date, c.year_id as last_modified_year_id, 'P' as is_prime_or_sub,
            (CASE WHEN minority_type_id in (2,3,4,5,9) THEN 'Y' ELSE 'N' END) as is_minority_vendor,
            (CASE WHEN minority_type_id in (2,3,4,5,9) THEN 'PM' ELSE 'P' END) as vendor_type,
            original_agreement_id as contract_original_agreement_id,
            'No' as is_subvendor, 'N/A' as associated_prime_vendor_name,
            (CASE WHEN minority_type_id = 2 THEN 'Black American' WHEN minority_type_id = 3 THEN 'Hispanic American' WHEN minority_type_id = 7 THEN 'Non-M/WBE'
				  WHEN minority_type_id = 9 THEN 'Women' WHEN minority_type_id = 11 THEN 'Individuals and Others' ELSE 'Asian American' END) AS mwbe_category_ui, job_id,
            COALESCE(scntrc_status,0),
            (CASE WHEN scntrc_status = 1 THEN 'No Data Entered' WHEN scntrc_status = 2 THEN 'Yes' WHEN scntrc_status = 3 THEN 'No' ELSE 'N/A' END) AS scntrc_status_name
       FROM agreement_snapshot_cy a LEFT JOIN ref_date b ON a.last_modified_date::date = b.date
        LEFT JOIN ref_month c ON b.calendar_month_id = c.month_id;


      INSERT INTO all_agreement_transactions_cy(original_agreement_id, document_version, document_code_id, agency_history_id,
            agency_id, agency_code, agency_name, agreement_id, starting_year,
            starting_year_id, ending_year, ending_year_id, registered_year,
            registered_year_id, contract_number, sub_contract_id, original_contract_amount,
            maximum_contract_amount, description, vendor_history_id, vendor_id,
            vendor_code, vendor_name, prime_vendor_id, prime_vendor_name,
            prime_minority_type_id, prime_minority_type_name,dollar_difference,
            percent_difference, agreement_type_id, agreement_type_code, agreement_type_name,
            award_category_id, award_category_code, award_category_name,
            award_method_id, award_method_code, award_method_name, expenditure_object_codes,
            expenditure_object_names, industry_type_id, industry_type_name,
            award_size_id, effective_begin_date, effective_begin_date_id,
            effective_begin_year, effective_begin_year_id, effective_end_date,
            effective_end_date_id, effective_end_year, effective_end_year_id,
            registered_date, registered_date_id, brd_awd_no, tracking_number,
            rfed_amount, minority_type_id, minority_type_name, original_version_flag,
            master_agreement_id,master_contract_number, master_agreement_yn,
            latest_flag, load_id, last_modified_date, last_modified_year_id, is_prime_or_sub,
            is_minority_vendor, vendor_type, contract_original_agreement_id,
            is_subvendor, associated_prime_vendor_name, mwbe_category_ui, job_id)
     SELECT a.original_agreement_id, document_version, document_code_id, agency_history_id,
            agency_id, agency_code, agency_name, agreement_id, starting_year,
            starting_year_id, ending_year, ending_year_id, registered_year,
            registered_year_id, a.contract_number, sub_contract_id, original_contract_amount,
            maximum_contract_amount, description, vendor_history_id, a.vendor_id,
            vendor_code, vendor_name, prime_vendor_id, (CASE WHEN a.prime_vendor_id = 0 THEN 'N/A (PRIVACY/SECURITY)' ELSE d.legal_name END) as prime_vendor_name,
            prime_minority_type_id, prime_minority_type_name,dollar_difference,
            percent_difference, agreement_type_id, agreement_type_code, agreement_type_name,
            award_category_id, award_category_code, award_category_name,
            award_method_id, award_method_code, award_method_name, expenditure_object_codes,
            expenditure_object_names, industry_type_id, industry_type_name,
            award_size_id, effective_begin_date, effective_begin_date_id,
            effective_begin_year, effective_begin_year_id, effective_end_date,
            effective_end_date_id, effective_end_year, effective_end_year_id,
            registered_date, registered_date_id, brd_awd_no, tracking_number,
            rfed_amount, minority_type_id, minority_type_name, original_version_flag,
            master_agreement_id,master_contract_number,'N' as master_agreement_yn,
            latest_flag, load_id, last_modified_date, c.year_id as last_modified_year_id,'S' as is_prime_or_sub,
            (CASE WHEN minority_type_id in (2,3,4,5,9) THEN 'Y' ELSE 'N' END) as is_minority_vendor,
            (CASE WHEN minority_type_id in (2,3,4,5,9) THEN 'SM' ELSE 'S' END) as vendor_type,
            e.original_agreement_id as contract_original_agreement_id,
            'Yes' as is_subvendor, (CASE WHEN a.prime_vendor_id = 0 THEN 'N/A (PRIVACY/SECURITY)' ELSE d.legal_name END) as associated_prime_vendor_name,
            (CASE WHEN minority_type_id = 2 THEN 'Black American' WHEN minority_type_id = 3 THEN 'Hispanic American' WHEN minority_type_id = 7 THEN 'Non-M/WBE'
				  WHEN minority_type_id = 9 THEN 'Women' WHEN minority_type_id = 11 THEN 'Individuals and Others' ELSE 'Asian American' END) AS mwbe_category_ui, job_id
     FROM sub_agreement_snapshot_cy a LEFT JOIN ref_date b ON a.last_modified_date::date = b.date
        LEFT JOIN ref_month c ON b.calendar_month_id = c.month_id
        LEFT JOIN vendor d ON a.prime_vendor_id = d.vendor_id
        LEFT JOIN (select original_agreement_id, contract_number from history_agreement where latest_flag = 'Y') e ON a.contract_number = e.contract_number;

        	-- subcontract_status_by_prime_contract_id
        	TRUNCATE TABLE subcontract_status_by_prime_contract_id;

			INSERT INTO subcontract_status_by_prime_contract_id
			(
			original_agreement_id,
			contract_number,
			description,
			agency_id,
			agency_name,
			prime_vendor_type,
			prime_vendor_id,
			prime_vendor_name,
			prime_minority_type_id,
			prime_minority_type_name,
			sub_vendor_type,
			sub_vendor_id,
			sub_vendor_name,
			sub_minority_type_id,
			sub_minority_type_name,
			sub_contract_id,
			aprv_sta_id,
			aprv_sta_value,
			starting_year_id,
			ending_year_id,
			effective_begin_year_id,
			effective_end_year_id,
			sort_order,
			latest_flag,
      prime_sub_vendor_minority_type_by_name_code,
      award_method_id,
      award_method_name,
      industry_type_id,
      industry_type_name,
      award_size_id
			)
			SELECT
			a.original_agreement_id,
			a.contract_number,
			a.description,
			a.agency_id,
			a.agency_name,
			a.vendor_type as prime_vendor_type,
			a.vendor_id as prime_vendor_id,
			a.vendor_name as prime_vendor_name,
			a.minority_type_id as prime_minority_type_id,
			CASE
				WHEN a.minority_type_id = 2 THEN 'Black American'
				WHEN a.minority_type_id = 3 THEN 'Hispanic American'
				WHEN a.minority_type_id IN (4,5) THEN 'Asian American'
				WHEN a.minority_type_id = 7 THEN 'Non-M/WBE'
				WHEN a.minority_type_id = 9 THEN 'Women'
				WHEN a.minority_type_id = 11 THEN 'Individuals and Others'
			END as prime_minority_type_name,
			b.vendor_type as sub_vendor_type,
			b.vendor_id as sub_vendor_id,
			b.vendor_name as sub_vendor_name,
			b.minority_type_id as sub_minority_type_id,
			CASE
				WHEN b.minority_type_id = 2 THEN 'Black American'
				WHEN b.minority_type_id = 3 THEN 'Hispanic American'
				WHEN b.minority_type_id IN (4,5) THEN 'Asian American'
				WHEN b.minority_type_id = 7 THEN 'Non-M/WBE'
				WHEN b.minority_type_id = 9 THEN 'Women'
				WHEN b.minority_type_id = 11 THEN 'Individuals and Others'
			END as sub_minority_type_name,
			CASE WHEN b.sub_contract_id is NULL OR b.sub_contract_id = '' THEN 'NOT PROVIDED' ELSE b.sub_contract_id END as sub_contract_id,
			c.aprv_sta_id,
			c.aprv_sta_value,
			a.starting_year_id,
			a.ending_year_id,
			a.effective_begin_year_id,
			a.effective_end_year_id,
			c.sort_order,
			a.latest_flag,
			a.vendor_type||':'||a.minority_type_id||':'||CAST(vendor.vendor_customer_code as text)||':'||a.vendor_name ||
			(CASE
			    WHEN b.vendor_name IS NULL THEN ''
			    ELSE ','||b.vendor_type||':'||b.minority_type_id||':'||CAST(subvendor.vendor_customer_code as text)||':'||b.vendor_name
      END) AS prime_sub_vendor_minority_type_by_name_code,
      award_method_id,
      award_method_name,
      industry_type_id,
      industry_type_name,
      award_size_id
			FROM all_agreement_transactions a
			LEFT JOIN
			(
				SELECT a.original_agreement_id, a.contract_number, a.vendor_name, a.vendor_type, a.vendor_id,a.aprv_sta as aprv_sta_id, a.minority_type_id, a.sub_contract_id
				FROM all_agreement_transactions a
				WHERE (a.is_prime_or_sub = 'S' AND a.latest_flag = 'Y')
			) b on b.contract_number = a.contract_number
			LEFT JOIN subcontract_approval_status c ON c.aprv_sta_id = COALESCE(b.aprv_sta_id,6)
			JOIN ref_document_code rd ON rd.document_code_id = a.document_code_id
			JOIN
			(
				SELECT v.vendor_id, v.legal_name, v.vendor_customer_code FROM vendor v
				JOIN (SELECT vendor_id, MAX(vendor_history_id) AS vendor_history_id FROM vendor_history WHERE miscellaneous_vendor_flag::BIT = 0 ::BIT  GROUP BY 1) vh ON v.vendor_id = vh.vendor_id
			) vendor on vendor.vendor_id = a.vendor_id
			LEFT JOIN
			(
				SELECT v.vendor_id, v.legal_name, v.vendor_customer_code FROM subvendor v
				JOIN (SELECT vendor_id, MAX(vendor_history_id) AS vendor_history_id FROM subvendor_history GROUP BY 1) vh ON v.vendor_id = vh.vendor_id
			) subvendor on subvendor.vendor_id = b.vendor_id
			WHERE (a.is_prime_or_sub = 'P'
				--AND a.latest_flag = 'Y'
				AND a.scntrc_status = 2
				AND rd.document_code IN ('CTA1','CT1','CT2'));


			UPDATE subcontract_status_by_prime_contract_id tbl1
			SET prime_sub_vendor_code = tbl2.prime_sub_vendor_code,
			prime_sub_vendor_code_by_type = tbl2.prime_sub_vendor_code_by_type,
			prime_vendor_code = tbl2.prime_vendor_code,
			sub_vendor_code = tbl2.sub_vendor_code,
			prime_sub_minority_type_id = tbl2.prime_sub_minority_type_id
			-- SELECT *
			FROM
			(
				SELECT
				contract_number,
				prime_vendor_id,
				COALESCE(sub_vendor_id, 0) as sub_vendor_id,
				b.vendor_customer_code as prime_vendor_code,
				c.vendor_customer_code as sub_vendor_code,
				CASE
					WHEN c.vendor_customer_code IS NULL THEN CAST(b.vendor_customer_code as text)
					ELSE CAST(b.vendor_customer_code as text) || ',' || CAST(c.vendor_customer_code as text)
				END AS prime_sub_vendor_code,
				CASE
					WHEN a.prime_minority_type_id IN (7,11) THEN 'P:'||CAST(b.vendor_customer_code as text)
					ELSE 'PM:'||CAST(b.vendor_customer_code as text)
				END ||
				CASE
					WHEN a.sub_minority_type_id IS NULL THEN ''
					WHEN a.sub_minority_type_id IN (7,11) THEN ',S:'||CAST(c.vendor_customer_code as text)
					ELSE ',SM:'||CAST(c.vendor_customer_code as text)
				END AS prime_sub_vendor_code_by_type,
				CASE
					WHEN a.prime_minority_type_id IN (7,11) THEN 'P:'||a.prime_minority_type_id
					ELSE 'PM:'||a.prime_minority_type_id
				END ||
				CASE
					WHEN a.sub_minority_type_id IS NULL THEN ''
					WHEN a.sub_minority_type_id IN (7,11) THEN ',S:'||a.sub_minority_type_id
					ELSE ',SM:'||a.sub_minority_type_id
				END AS prime_sub_minority_type_id
				FROM subcontract_status_by_prime_contract_id a
				JOIN
				(
					SELECT v.vendor_id, v.legal_name, v.vendor_customer_code FROM vendor v
					JOIN (SELECT vendor_id, MAX(vendor_history_id) AS vendor_history_id FROM vendor_history WHERE miscellaneous_vendor_flag::BIT = 0 ::BIT  GROUP BY 1) vh ON v.vendor_id = vh.vendor_id
				) b on b.vendor_id = a.prime_vendor_id
				LEFT JOIN
				(
					SELECT v.vendor_id, v.legal_name, v.vendor_customer_code FROM subvendor v
					JOIN (SELECT vendor_id, MAX(vendor_history_id) AS vendor_history_id FROM subvendor_history GROUP BY 1) vh ON v.vendor_id = vh.vendor_id
				) c on c.vendor_id = a.sub_vendor_id
			) tbl2
			WHERE tbl2.contract_number = tbl1.contract_number
			AND tbl2.prime_vendor_id = tbl1.prime_vendor_id
			AND tbl2.sub_vendor_id = COALESCE(tbl1.sub_vendor_id, 0);


	TRUNCATE TABLE all_agreement_transactions_by_prime_sub_vendor;

	/*

	-- Prime Data
	Script to insert Prime Vendors data into all_agreement_transactions_by_prime_sub_vendor

	SELECT vendor_record_type,
	COUNT(vendor_record_type)
	FROM all_agreement_transactions_by_prime_sub_vendor
	GROUP BY vendor_record_type

	*/

	-- break the insert statement into small batches, ~2000 insert per insert
	-- faster than one big insert statement
	SELECT MAX(agreement_id) FROM all_agreement_transactions INTO agreement_id_max;
  agreement_id_max := COALESCE(agreement_id_max, 0);
  RAISE NOTICE 'MAX(agreement_id) is %', agreement_id_max;
  agreement_id_loop_end := agreement_id_max / agreement_id_pagination;

  FOR Loopid  IN 0..agreement_id_loop_end LOOP

		INSERT INTO all_agreement_transactions_by_prime_sub_vendor (
		agreement_id,
		original_agreement_id,
		contract_original_agreement_id,
		contract_number,
		scntrc_status,
		scntrc_status_name,
		master_agreement_yn,
		master_agreement_id,
		master_contract_number,
		master_contract_number_export,
		has_children,
		has_mwbe_children,
		document_code_id ,
		document_code,
		agency_id,
		agency_name,
		agency_code,
		vendor_record_type,
		agreement_type_id,
		agreement_type_code,
		agreement_type_name,
		award_method_id,
		award_method_code,
		award_method_name,
		award_size_id,
		expenditure_object_codes,
		expenditure_object_names,
		industry_type_id,
		industry_type_name,
		brd_awd_no,
		tracking_number,
		registered_year,
		registered_year_id,
		registered_date,
		registered_date_id,
		starting_year_id,
		ending_year_id,
		effective_begin_year_id,
		effective_end_year_id,
		latest_flag,
		version,
		prime_apt_pin,
		prime_pin,
		prime_vendor_type,
		prime_purpose,
		prime_original_contract_amount,
		prime_maximum_contract_amount,
		prime_amount_id,
		prime_amount_name,
		prime_dollar_difference,
		prime_percent_difference,
		prime_document_version,
		prime_vendor_id,
		prime_vendor_code,
		prime_vendor_name,
		associated_prime_vendor_code,
		associated_prime_vendor_name,
		prime_minority_type_id,
		prime_minority_type_name,
		prime_mwbe_adv_search_id,
		prime_mwbe_adv_search,
		prime_rfed_amount,
		prime_starting_year,
		prime_starting_year_id,
		prime_ending_year,
		prime_ending_year_id,
		prime_effective_begin_date,
		prime_effective_begin_date_id,
		prime_effective_begin_year,
		prime_effective_begin_year_id,
		prime_effective_end_date,
		prime_effective_end_date_id,
		prime_effective_end_year,
		prime_effective_end_year_id,
		prime_industry_type_id,
		prime_industry_type_name,
		sub_vendor_type,
		sub_vendor_id,
		sub_vendor_code,
		sub_vendor_name,
		sub_vendor_name_export,
		sub_minority_type_id,
		sub_minority_type_name,
		sub_minority_type_name_export,
		sub_purpose,
		sub_purpose_export,
		sub_apt_pin,
		sub_pin,
		aprv_sta,
		aprv_sta_name,
		aprv_sta_name_export,
		sub_original_contract_amount,
		sub_maximum_contract_amount,
		sub_amount_id,
		sub_amount_name,
		sub_dollar_difference,
		sub_percent_difference,
		sub_rfed_amount,
		sub_starting_year,
		sub_starting_year_id,
		sub_ending_year,
		sub_ending_year_id,
		sub_effective_begin_date,
		sub_effective_begin_date_export,
		sub_effective_begin_date_id,
		sub_effective_begin_year,
		sub_effective_begin_year_id,
		sub_effective_end_date,
		sub_effective_end_date_export,
		sub_effective_end_date_id,
		sub_effective_end_year,
		sub_effective_end_year_id,
		sub_document_version,
		sub_contract_id,
		sub_contract_id_export,
		sub_industry_type_id,
		sub_industry_type_name,
		sub_industry_type_name_export,
		sub_latest_flag,
		maximum_contract_amount
		)
		SELECT
		a.agreement_id,
		a.original_agreement_id,
		a.contract_original_agreement_id,
		a.contract_number,
		CASE WHEN d.document_code in ('CT1', 'CT2', 'CTA1') THEN a.scntrc_status ELSE 5 END AS scntrc_status,
		CASE WHEN d.document_code in ('CT1', 'CT2', 'CTA1') THEN a.scntrc_status_name ELSE 'N/A' END AS scntrc_status_name,
		a.master_agreement_yn,
		a.master_agreement_id,
		a.master_contract_number,
		CASE WHEN a.master_contract_number IS NULL THEN '-' ELSE a.master_contract_number END AS master_contract_number_export,
		a.has_children,
		a.has_mwbe_children,
		a.document_code_id,
		d.document_code,
		a.agency_id,
		a.agency_name,
		a.agency_code,
		'Prime Vendor' as vendor_record_type,
		a.agreement_type_id,
		a.agreement_type_code,
		a.agreement_type_name,
		a.award_method_id,
		a.award_method_code,
		a.award_method_name,
		a.award_size_id,
		a.expenditure_object_codes,
		a.expenditure_object_names,
		a.industry_type_id,
		a.industry_type_name,
		a.brd_awd_no,
		a.tracking_number,
		a.registered_year,
		a.registered_year_id,
		a.registered_date,
		a.registered_date_id,
		a.starting_year_id,
		a.ending_year_id,
		a.effective_begin_year_id,
		a.effective_end_year_id,
		a.latest_flag,
		a.document_version as version,
		e.brd_awd_no as prime_apt_pin, -- Latest Prime
		e.tracking_number as prime_pin, -- Latest Prime
		a.vendor_type as prime_vendor_type,
		e.description as prime_purpose, -- Latest Prime
		COALESCE(a.original_contract_amount,0) as prime_original_contract_amount,
		COALESCE(a.maximum_contract_amount,0) as prime_maximum_contract_amount,
		CASE
		WHEN COALESCE(a.maximum_contract_amount,0) > 100000000 THEN 1
		WHEN COALESCE(a.maximum_contract_amount,0) > 50000000 AND COALESCE(a.maximum_contract_amount,0) <= 100000000 THEN 2
		WHEN COALESCE(a.maximum_contract_amount,0) > 25000000 AND COALESCE(a.maximum_contract_amount,0) <= 50000000 THEN 3
		WHEN COALESCE(a.maximum_contract_amount,0) > 10000000 AND COALESCE(a.maximum_contract_amount,0) <= 25000000 THEN 4
		WHEN COALESCE(a.maximum_contract_amount,0) >= 1000000 AND COALESCE(a.maximum_contract_amount,0) <= 10000000 THEN 5
		WHEN COALESCE(a.maximum_contract_amount,0) < 1000000 THEN 6
		END as prime_amount_id,
		CASE
		WHEN COALESCE(a.maximum_contract_amount,0) > 100000000 THEN 'Greater than $100M'
		WHEN COALESCE(a.maximum_contract_amount,0) > 50000000 AND COALESCE(a.maximum_contract_amount,0) <= 100000000 THEN '$51M - $100M'
		WHEN COALESCE(a.maximum_contract_amount,0) > 25000000 AND COALESCE(a.maximum_contract_amount,0) <= 50000000 THEN '$26M - $50M'
		WHEN COALESCE(a.maximum_contract_amount,0) > 10000000 AND COALESCE(a.maximum_contract_amount,0) <= 25000000 THEN '$11M - $25M'
		WHEN COALESCE(a.maximum_contract_amount,0) >= 1000000 AND COALESCE(a.maximum_contract_amount,0) <= 10000000 THEN '$1M - $10M'
		WHEN COALESCE(a.maximum_contract_amount,0) < 1000000 THEN 'Less than $1M'
		END as prime_amount_name,
		a.dollar_difference as prime_dollar_difference,
		a.percent_difference as prime_percent_difference,
		a.document_version as prime_document_version,
		a.vendor_id as prime_vendor_id,
		a.vendor_code as prime_vendor_code,
		a.vendor_name as prime_vendor_name,
		a.vendor_code as associated_prime_vendor_code,
		a.vendor_name as associated_prime_vendor_name,
		a.minority_type_id as prime_minority_type_id,
		a.mwbe_category_ui as prime_minority_type_name,
		a.minority_type_id as prime_mwbe_adv_search_id,
		a.mwbe_category_ui as prime_mwbe_adv_search,
		e.rfed_amount as prime_rfed_amount,
		a.starting_year as prime_starting_year,
		a.starting_year_id as prime_starting_year_id,
		a.ending_year as prime_ending_year,
		a.ending_year_id as prime_ending_year_id,
		a.effective_begin_date as prime_effective_begin_date,
		a.effective_begin_date_id as prime_effective_begin_date_id,
		a.effective_begin_year as prime_effective_begin_year,
		a.effective_begin_year_id as prime_effective_begin_year_id,
		a.effective_end_date as prime_effective_end_date,
		a.effective_end_date_id as prime_effective_end_date_id,
		a.effective_end_year as prime_effective_end_year,
		a.effective_end_year_id as prime_effective_end_year_id,
		e.industry_type_id as prime_industry_type_id, -- Latest Prime
		e.industry_type_name as prime_industry_type_name, -- Latest Prime
		'N/A' as sub_vendor_type,
		0 as sub_vendor_id,
		'N/A' as sub_vendor_code,
		'N/A' as sub_vendor_name,
		'-' as sub_vendor_name_export,
		0 as sub_minority_type_id,
		'N/A' as sub_minority_type_name,
		'-' as sub_minority_type_name_export,
		a.description as sub_purpose,
		'-' as sub_purpose_export,
		'N/A' as sub_apt_pin,
		'N/A' as sub_pin,
		CASE WHEN a.scntrc_status = 2 AND (select count(*) from subcontract_details s where s.contract_number = a.contract_number) < 1 THEN c.aprv_sta_id  ELSE 0 END AS aprv_sta,
		CASE WHEN a.scntrc_status = 2 AND (select count(*) from subcontract_details s where s.contract_number = a.contract_number) < 1 THEN c.aprv_sta_value  ELSE 'N/A' END AS aprv_sta_name,
		CASE WHEN a.scntrc_status = 2 AND (select count(*) from subcontract_details s where s.contract_number = a.contract_number) < 1 THEN c.aprv_sta_value  ELSE  '-' END AS aprv_sta_name_export,
		0 as sub_original_contract_amount,
		0 as sub_maximum_contract_amount,
		NULL as sub_amount_id,
		Null as sub_amount_name,
		0 as sub_dollar_difference,
		0 as sub_percent_difference,
		0 as sub_rfed_amount,
		a.starting_year as sub_starting_year,
		a.starting_year_id as sub_starting_year_id,
		0 as sub_ending_year,
		0 as sub_ending_year_id,
		null as sub_effective_begin_date,
		CAST('-' AS TEXT) as sub_effective_begin_date_export,
		0 as sub_effective_begin_date_id,
		0 as sub_effective_begin_year,
		0 as sub_effective_begin_year_id,
		null as sub_effective_end_date,
		CAST('-' AS TEXT) as sub_effective_end_date_export,
		0 as sub_effective_end_date_id,
		0 as sub_effective_end_year,
		0 as sub_effective_end_year_id,
		0 as sub_document_version,
		'N/A' as sub_contract_id,
		'-' as sub_contract_id_export,
		0 as sub_industry_type_id,
		'N/A' as sub_industry_type_name,
		'-' as sub_industry_type_name_export,
		e.latest_flag as sub_latest_flag,
		a.maximum_contract_amount
		-- SELECT count(*)
		FROM all_agreement_transactions a
		LEFT JOIN
		(
		SELECT
		a.latest_flag,
		a.contract_number,
		a.brd_awd_no,
		a.tracking_number,
		a.description,
		a.industry_type_id,
		a.industry_type_name,
		a.rfed_amount
		FROM all_agreement_transactions a
		WHERE a.is_prime_or_sub IN ('P') AND a.latest_flag = 'Y'
		)e ON a.contract_number = e.contract_number
		LEFT JOIN subcontract_approval_status c ON c.aprv_sta_id = COALESCE(a.aprv_sta,6)
		LEFT JOIN ref_document_code d ON d.document_code_id = a.document_code_id
		WHERE a.is_prime_or_sub IN ('P')
		AND agreement_id >  Loopid * agreement_id_pagination
		AND agreement_id <= (Loopid+1) * agreement_id_pagination
		;
		IF NOT FOUND THEN
    	RAISE NOTICE 'I THINK WE ARE DONE. ONE MORE TRY MAYBE.';
		END IF;

    RAISE NOTICE 'REF COMMON TT now at agreement_id %', (Loopid+1) * agreement_id_pagination;
  END LOOP;

/*

-- Sub Data
Script to insert Sub Vendors data into all_agreement_transactions_by_prime_sub_vendor

SELECT vendor_record_type,
COUNT(vendor_record_type)
FROM all_agreement_transactions_by_prime_sub_vendor
GROUP BY vendor_record_type

*/
INSERT INTO all_agreement_transactions_by_prime_sub_vendor (
agreement_id,
original_agreement_id,
contract_original_agreement_id,
contract_number,
scntrc_status,
scntrc_status_name,
master_agreement_yn,
master_agreement_id,
master_contract_number,
master_contract_number_export,
has_children,
has_mwbe_children,
document_code_id ,
document_code,
agency_id,
agency_name,
agency_code,
vendor_record_type,
agreement_type_id,
agreement_type_code,
agreement_type_name,
award_method_id,
award_method_code,
award_method_name,
award_size_id,
expenditure_object_codes,
expenditure_object_names,
industry_type_id,
industry_type_name,
brd_awd_no,
tracking_number,
registered_year,
registered_year_id,
registered_date,
registered_date_id,
starting_year_id,
ending_year_id,
effective_begin_year_id,
effective_end_year_id,
latest_flag,
version,
prime_apt_pin,
prime_pin,
prime_vendor_type,
prime_purpose,
prime_original_contract_amount,
prime_maximum_contract_amount,
prime_amount_id,
prime_amount_name,
prime_dollar_difference,
prime_percent_difference,
prime_document_version,
prime_vendor_id,
prime_vendor_code,
prime_vendor_name,
associated_prime_vendor_code,
associated_prime_vendor_name,
prime_minority_type_id,
prime_minority_type_name,
prime_mwbe_adv_search_id,
prime_mwbe_adv_search,
prime_rfed_amount,
prime_starting_year,
prime_starting_year_id,
prime_ending_year,
prime_ending_year_id,
prime_effective_begin_date,
prime_effective_begin_date_id,
prime_effective_begin_year,
prime_effective_begin_year_id,
prime_effective_end_date,
prime_effective_end_date_id,
prime_effective_end_year,
prime_effective_end_year_id,
prime_industry_type_id,
prime_industry_type_name,
sub_vendor_type,
sub_vendor_id,
sub_vendor_code,
sub_vendor_name,
sub_vendor_name_export,
sub_minority_type_id,
sub_minority_type_name,
sub_minority_type_name_export,
sub_purpose,
sub_purpose_export,
sub_apt_pin,
sub_pin,
aprv_sta,
aprv_sta_name,
aprv_sta_name_export,
sub_original_contract_amount,
sub_maximum_contract_amount,
sub_amount_id,
sub_amount_name,
sub_dollar_difference,
sub_percent_difference,
sub_rfed_amount,
sub_starting_year,
sub_starting_year_id,
sub_ending_year,
sub_ending_year_id,
sub_effective_begin_date,
sub_effective_begin_date_export,
sub_effective_begin_date_id,
sub_effective_begin_year,
sub_effective_begin_year_id,
sub_effective_end_date,
sub_effective_end_date_export,
sub_effective_end_date_id,
sub_effective_end_year,
sub_effective_end_year_id,
sub_document_version,
sub_contract_id,
sub_contract_id_export,
sub_industry_type_id,
sub_industry_type_name,
sub_industry_type_name_export,
sub_latest_flag,
maximum_contract_amount
)
SELECT
a.agreement_id,
a.original_agreement_id,
a.contract_original_agreement_id,
a.contract_number,
CASE WHEN d.document_code in ('CT1', 'CT2', 'CTA1') THEN a.scntrc_status ELSE 5 END AS scntrc_status,
CASE WHEN d.document_code in ('CT1', 'CT2', 'CTA1') THEN a.scntrc_status_name ELSE 'N/A' END AS scntrc_status_name,
a.master_agreement_yn,
a.master_agreement_id,
a.master_contract_number,
CASE WHEN a.master_contract_number IS NULL THEN '-' ELSE a.master_contract_number END AS master_contract_number_export,
a.has_children,
a.has_mwbe_children,
a.document_code_id,
d.document_code,
a.agency_id,
a.agency_name,
a.agency_code,
'Sub Vendor' as vendor_record_type,
a.agreement_type_id,
a.agreement_type_code,
a.agreement_type_name,
a.award_method_id,
a.award_method_code,
a.award_method_name,
a.award_size_id,
a.expenditure_object_codes,
a.expenditure_object_names,
a.industry_type_id,
a.industry_type_name,
a.brd_awd_no,
a.tracking_number,
a.registered_year,
a.registered_year_id,
a.registered_date,
a.registered_date_id,
a.starting_year_id,
a.ending_year_id,
a.effective_begin_year_id,
a.effective_end_year_id,
a.latest_flag,
a.document_version as version,
e.brd_awd_no as prime_apt_pin, -- Latest Prime
e.tracking_number as prime_pin, -- Latest Prime
'N/A' as prime_vendor_type,
e.description as prime_purpose, -- Latest Prime
0 as prime_original_contract_amount,
0 as prime_maximum_contract_amount,
NULL as prime_amount_id,
NULL as prime_amount_name,
0 as prime_dollar_difference,
0 as prime_percent_difference,
e.document_version as prime_document_version, -- Latest Prime
e.vendor_id as prime_vendor_id, -- Latest Prime
e.vendor_code as prime_vendor_code, -- Latest Prime
e.vendor_name as prime_vendor_name, -- Latest Prime
'-' as associated_prime_vendor_code,
'-' as associated_prime_vendor_name,
e.minority_type_id as prime_minority_type_id, -- Latest Prime
e.mwbe_category_ui as prime_minority_type_name, -- Latest Prime
NULL as prime_mwbe_adv_search_id,
'-' as prime_mwbe_adv_search,
0 as prime_rfed_amount,
e.starting_year as prime_starting_year, -- Latest Prime
e.starting_year_id as prime_starting_year_id, -- Latest Prime
e.ending_year as prime_ending_year, -- Latest Prime
e.ending_year_id as prime_ending_year_id, -- Latest Prime
e.effective_begin_date as prime_effective_begin_date, -- Latest Prime
e.effective_begin_date_id as prime_effective_begin_date_id, -- Latest Prime
e.effective_begin_year as prime_effective_begin_year, -- Latest Prime
e.effective_begin_year_id as prime_effective_begin_year_id, -- Latest Prime
e.effective_end_date as prime_effective_end_date, -- Latest Prime
e.effective_end_date_id as prime_effective_end_date_id, -- Latest Prime
e.effective_end_year as prime_effective_end_year, -- Latest Prime
e.effective_end_year_id as prime_effective_end_year_id, -- Latest Prime
e.industry_type_id as prime_industry_type_id, -- Latest Prime
e.industry_type_name as prime_industry_type_name, -- Latest Prime
a.vendor_type as sub_vendor_type,
a.vendor_id as sub_vendor_id,
a.vendor_code as sub_vendor_code,
a.vendor_name as sub_vendor_name,
a.vendor_name AS sub_vendor_name_export,
a.minority_type_id as sub_minority_type_id,
CASE
WHEN a.minority_type_id = 2 THEN 'Black American'
WHEN a.minority_type_id = 3 THEN 'Hispanic American'
WHEN a.minority_type_id IN (4,5) THEN 'Asian American'
WHEN a.minority_type_id = 7 THEN 'Non-M/WBE'
WHEN a.minority_type_id = 9 THEN 'Women'
WHEN a.minority_type_id = 11 THEN 'Individuals and Others'
END as sub_minority_type_name,
CASE
WHEN a.minority_type_id = 2 THEN 'Black American'
WHEN a.minority_type_id = 3 THEN 'Hispanic American'
WHEN a.minority_type_id IN (4,5) THEN 'Asian American'
WHEN a.minority_type_id = 7 THEN 'Non-M/WBE'
WHEN a.minority_type_id = 9 THEN 'Women'
WHEN a.minority_type_id = 11 THEN 'Individuals and Others'
END AS sub_minority_type_name_export,
a.description as sub_purpose,
a.description as sub_purpose_export,
a.brd_awd_no as sub_apt_pin,
a.tracking_number as sub_pin,
c.aprv_sta_id as aprv_sta,
c.aprv_sta_value as aprv_sta_name,
c.aprv_sta_value as aprv_sta_name_export,
a.original_contract_amount as sub_original_contract_amount,
COALESCE(a.maximum_contract_amount,0) as sub_maximum_contract_amount,
CASE
WHEN COALESCE(a.maximum_contract_amount,0) > 100000000 THEN 1
WHEN COALESCE(a.maximum_contract_amount,0) > 50000000 AND COALESCE(a.maximum_contract_amount,0) <= 100000000 THEN 2
WHEN COALESCE(a.maximum_contract_amount,0) > 25000000 AND COALESCE(a.maximum_contract_amount,0) <= 50000000 THEN 3
WHEN COALESCE(a.maximum_contract_amount,0) > 10000000 AND COALESCE(a.maximum_contract_amount,0) <= 25000000 THEN 4
WHEN COALESCE(a.maximum_contract_amount,0) >= 1000000 AND COALESCE(a.maximum_contract_amount,0) <= 10000000 THEN 5
WHEN COALESCE(a.maximum_contract_amount,0) < 1000000 THEN 6
END as sub_amount_id,
CASE
WHEN COALESCE(a.maximum_contract_amount,0) > 100000000 THEN 'Greater than $100M'
WHEN COALESCE(a.maximum_contract_amount,0) > 50000000 AND COALESCE(a.maximum_contract_amount,0) <= 100000000 THEN '$51M - $100M'
WHEN COALESCE(a.maximum_contract_amount,0) > 25000000 AND COALESCE(a.maximum_contract_amount,0) <= 50000000 THEN '$26M - $50M'
WHEN COALESCE(a.maximum_contract_amount,0) > 10000000 AND COALESCE(a.maximum_contract_amount,0) <= 25000000 THEN '$11M - $25M'
WHEN COALESCE(a.maximum_contract_amount,0) >= 1000000 AND COALESCE(a.maximum_contract_amount,0) <= 10000000 THEN '$1M - $10M'
WHEN COALESCE(a.maximum_contract_amount,0) < 1000000 THEN 'Less than $1M'
END as sub_amount_name,
a.dollar_difference as sub_dollar_difference,
a.percent_difference as sub_percent_difference,
a.rfed_amount as sub_rfed_amount,
a.starting_year as sub_starting_year,
a.starting_year_id as sub_starting_year_id,
a.ending_year as sub_ending_year,
a.ending_year_id as sub_ending_year_id,
a.effective_begin_date as sub_effective_begin_date,
CAST(a.effective_begin_date AS TEXT) as sub_effective_begin_date_export,
a.effective_begin_date_id as sub_effective_begin_date_id,
a.effective_begin_year as sub_effective_begin_year,
a.effective_begin_year_id as sub_effective_begin_year_id,
a.effective_end_date as sub_effective_end_date,
CAST(a.effective_end_date AS TEXT) as sub_effective_end_date_export,
a.effective_end_date_id as sub_effective_end_date_id,
a.effective_end_year as sub_effective_end_year,
a.effective_end_year_id as sub_effective_end_year_id,
a.document_version as sub_document_version,
a.sub_contract_id as sub_contract_id,
a.sub_contract_id as sub_contract_id_export,
a.industry_type_id as sub_industry_type_id,
a.industry_type_name as sub_industry_type_name,
a.industry_type_name as sub_industry_type_name_export,
a.latest_flag as sub_latest_flag,
a.maximum_contract_amount
-- SELECT count(*)
FROM all_agreement_transactions a
LEFT JOIN
(
SELECT
a.contract_number,
a.brd_awd_no,
a.tracking_number,
a.description,
a.industry_type_id,
a.industry_type_name,
a.vendor_id,
a.vendor_name,
a.vendor_code,
a.minority_type_id,
a.mwbe_category_ui,
c.starting_year,
c.starting_year_id,
c.ending_year,
c.ending_year_id,
a.effective_begin_date,
a.effective_begin_date_id,
a.effective_begin_year,
a.effective_begin_year_id,
a.effective_end_date,
a.effective_end_date_id ,
a.effective_end_year ,
a.effective_end_year_id,
a.document_version,
a.maximum_contract_amount
FROM all_agreement_transactions a
LEFT JOIN
(
SELECT MIN(starting_year) as starting_year,
MAX(ending_year) as ending_year,
MIN(starting_year_id) as starting_year_id,
MAX(ending_year_id) as ending_year_id,
contract_number, sub_contract_id
FROM all_agreement_transactions
WHERE is_prime_or_sub = 'P'
GROUP BY contract_number, sub_contract_id
) c
on a.contract_number = c.contract_number
WHERE a.is_prime_or_sub IN ('P') AND a.latest_flag = 'Y'

)e ON a.contract_number = e.contract_number
LEFT JOIN subcontract_approval_status c ON c.aprv_sta_id = COALESCE(a.aprv_sta,6)
LEFT JOIN ref_document_code d ON d.document_code_id = a.document_code_id
WHERE a.is_prime_or_sub IN ('S');


	RAISE NOTICE 'REF COMMON TT2';

  DELETE FROM ONLY all_disbursement_transactions a
  USING disbursement_line_item_details b
  WHERE a.disbursement_line_item_id = b.disbursement_line_item_id
  AND b.job_id = p_job_id_in AND a.is_prime_or_sub = 'P';

  DELETE FROM  all_disbursement_transactions WHERE is_prime_or_sub = 'S';

  INSERT INTO all_disbursement_transactions(disbursement_line_item_id, disbursement_id, line_number, disbursement_number,
            check_eft_issued_date_id, check_eft_issued_nyc_year_id, fiscal_year,
            check_eft_issued_cal_month_id, agreement_id, master_agreement_id,
            fund_class_id, check_amount, agency_id, agency_history_id, agency_code,
            expenditure_object_id, vendor_id,  prime_vendor_id, prime_vendor_name,
            prime_minority_type_id, prime_minority_type_name,department_id, maximum_contract_amount,
            maximum_contract_amount_cy, maximum_spending_limit, maximum_spending_limit_cy,
            document_id, vendor_name, vendor_customer_code, check_eft_issued_date,
            agency_name, agency_short_name, location_name, location_code,
            department_name, department_short_name, department_code, expenditure_object_name,
            expenditure_object_code, budget_code_id, budget_code, budget_name,
            contract_number, master_contract_number, master_child_contract_number,
            contract_vendor_id, contract_vendor_id_cy, master_contract_vendor_id,
            master_contract_vendor_id_cy, contract_agency_id, contract_agency_id_cy,
            master_contract_agency_id, master_contract_agency_id_cy, master_purpose,
            master_purpose_cy, purpose, purpose_cy, master_child_contract_agency_id,
            master_child_contract_agency_id_cy, master_child_contract_vendor_id,
            master_child_contract_vendor_id_cy, reporting_code, location_id,
            fund_class_name, fund_class_code, spending_category_id, spending_category_name,
            calendar_fiscal_year_id, calendar_fiscal_year, agreement_accounting_line_number,
            agreement_commodity_line_number, agreement_vendor_line_number,
            reference_document_number, reference_document_code, contract_document_code,
            master_contract_document_code, minority_type_id, minority_type_name,
            industry_type_id, industry_type_name, agreement_type_code, award_method_code,
            contract_industry_type_id, contract_industry_type_id_cy, master_contract_industry_type_id,
            master_contract_industry_type_id_cy, contract_minority_type_id,
            contract_minority_type_id_cy, master_contract_minority_type_id,
            master_contract_minority_type_id_cy, file_type, load_id, last_modified_date,
            last_modified_fiscal_year_id, last_modified_calendar_year_id,
            is_prime_or_sub, is_minority_vendor, vendor_type, contract_original_agreement_id,
            is_subvendor, associated_prime_vendor_name, mwbe_category_ui, job_id)
     SELECT disbursement_line_item_id, disbursement_id, line_number, disbursement_number,
            check_eft_issued_date_id, check_eft_issued_nyc_year_id, fiscal_year,
            check_eft_issued_cal_month_id, agreement_id, master_agreement_id,
            fund_class_id, check_amount, agency_id, agency_history_id, agency_code,
            expenditure_object_id, vendor_id, vendor_id as prime_vendor_id, vendor_name as prime_vendor_name,
            minority_type_id as prime_minority_type_id, minority_type_name as prime_minority_type_name,department_id, maximum_contract_amount,
            maximum_contract_amount_cy, maximum_spending_limit, maximum_spending_limit_cy,
            document_id, vendor_name, vendor_customer_code, check_eft_issued_date,
            agency_name, agency_short_name, location_name, location_code,
            department_name, department_short_name, department_code, expenditure_object_name,
            expenditure_object_code, budget_code_id, budget_code, budget_name,
            contract_number, master_contract_number, master_child_contract_number,
            contract_vendor_id, contract_vendor_id_cy, master_contract_vendor_id,
            master_contract_vendor_id_cy, contract_agency_id, contract_agency_id_cy,
            master_contract_agency_id, master_contract_agency_id_cy, master_purpose,
            master_purpose_cy, purpose, purpose_cy, master_child_contract_agency_id,
            master_child_contract_agency_id_cy, master_child_contract_vendor_id,
            master_child_contract_vendor_id_cy, reporting_code, location_id,
            fund_class_name, fund_class_code, spending_category_id, spending_category_name,
            calendar_fiscal_year_id, calendar_fiscal_year, agreement_accounting_line_number,
            agreement_commodity_line_number, agreement_vendor_line_number,
            reference_document_number, reference_document_code, contract_document_code,
            master_contract_document_code, minority_type_id, minority_type_name,
            industry_type_id, industry_type_name, agreement_type_code, award_method_code,
            contract_industry_type_id, contract_industry_type_id_cy, master_contract_industry_type_id,
            master_contract_industry_type_id_cy, contract_minority_type_id,
            contract_minority_type_id_cy, master_contract_minority_type_id,
            master_contract_minority_type_id_cy, file_type, load_id, last_modified_date,
            b.nyc_year_id as last_modified_fiscal_year_id, c.year_id as last_modified_calendar_year_id,
            'P' as is_prime_or_sub,
            (CASE WHEN minority_type_id in (2,3,4,5,9) THEN 'Y' ELSE 'N' END) as is_minority_vendor,
            (CASE WHEN minority_type_id in (2,3,4,5,9) THEN 'PM' ELSE 'P' END) as vendor_type,
            agreement_id as contract_original_agreement_id,
            'No' as is_subvendor, 'N/A' as associated_prime_vendor_name,
            (CASE WHEN minority_type_id = 2 THEN 'Black American' WHEN minority_type_id = 3 THEN 'Hispanic American' WHEN minority_type_id = 7 THEN 'Non-M/WBE'
				  WHEN minority_type_id = 9 THEN 'Women' WHEN minority_type_id = 11 THEN 'Individuals and Others' ELSE 'Asian American' END) AS mwbe_category_ui,job_id
    FROM disbursement_line_item_details a LEFT JOIN ref_date b ON a.last_modified_date::date = b.date
        LEFT JOIN ref_month c ON b.calendar_month_id = c.month_id
    WHERE job_id = p_job_id_in;


    INSERT INTO all_disbursement_transactions(disbursement_line_item_id, disbursement_number, payment_id, check_eft_issued_date_id,
            check_eft_issued_nyc_year_id, fiscal_year, check_eft_issued_cal_month_id,
            agreement_id, check_amount, agency_id, agency_history_id, agency_code,
            vendor_id, prime_vendor_id, prime_vendor_name,
            prime_minority_type_id, prime_minority_type_name,maximum_contract_amount, maximum_contract_amount_cy,
            document_id, vendor_name, vendor_customer_code, check_eft_issued_date,
            agency_name, agency_short_name, expenditure_object_name, expenditure_object_code,
            contract_number, sub_contract_id, contract_vendor_id, contract_vendor_id_cy,
            contract_prime_vendor_id, contract_prime_vendor_id_cy, contract_agency_id,
            contract_agency_id_cy, purpose, purpose_cy, reporting_code, spending_category_id,
            spending_category_name, calendar_fiscal_year_id, calendar_fiscal_year,
            reference_document_number, reference_document_code, contract_document_code,
            minority_type_id, minority_type_name, industry_type_id, industry_type_name,
            agreement_type_code, award_method_code, contract_industry_type_id,
            contract_industry_type_id_cy, contract_minority_type_id, contract_minority_type_id_cy,
            master_agreement_id,master_contract_number,
            file_type, load_id, last_modified_date,
            last_modified_fiscal_year_id, last_modified_calendar_year_id,
            is_prime_or_sub, is_minority_vendor, vendor_type, contract_original_agreement_id,
            is_subvendor, associated_prime_vendor_name, mwbe_category_ui, job_id)
   SELECT  disbursement_line_item_id, disbursement_number, payment_id, check_eft_issued_date_id,
            check_eft_issued_nyc_year_id, fiscal_year, check_eft_issued_cal_month_id,
            agreement_id, check_amount, agency_id, agency_history_id, agency_code,
            a.vendor_id, prime_vendor_id, (CASE WHEN a.prime_vendor_id = 0 THEN 'N/A (PRIVACY/SECURITY)' ELSE d.legal_name END) as prime_vendor_name,
            prime_minority_type_id, prime_minority_type_name,maximum_contract_amount, maximum_contract_amount_cy,
            document_id, vendor_name, a.vendor_customer_code, check_eft_issued_date,
            agency_name, agency_short_name, expenditure_object_name, expenditure_object_code,
            a.contract_number, sub_contract_id, contract_vendor_id, contract_vendor_id_cy,
            contract_prime_vendor_id, contract_prime_vendor_id_cy, contract_agency_id,
            contract_agency_id_cy, purpose, purpose_cy, reporting_code, spending_category_id,
            spending_category_name, calendar_fiscal_year_id, calendar_fiscal_year,
            reference_document_number, reference_document_code, contract_document_code,
            minority_type_id, minority_type_name, industry_type_id, industry_type_name,
            agreement_type_code, award_method_code, contract_industry_type_id,
            contract_industry_type_id_cy, contract_minority_type_id, contract_minority_type_id_cy,
            master_agreement_id,master_contract_number,
            file_type, load_id, last_modified_date,
            b.nyc_year_id as last_modified_fiscal_year_id, c.year_id as last_modified_calendar_year_id,
            'S' as is_prime_or_sub,
            (CASE WHEN minority_type_id in (2,3,4,5,9) THEN 'Y' ELSE 'N' END) as is_minority_vendor,
            (CASE WHEN minority_type_id in (2,3,4,5,9) THEN 'SM' ELSE 'S' END) as vendor_type,
            e.original_agreement_id as contract_original_agreement_id,
             'Yes' as is_subvendor, (CASE WHEN a.prime_vendor_id = 0 THEN 'N/A (PRIVACY/SECURITY)' ELSE d.legal_name END) as associated_prime_vendor_name,
            (CASE WHEN minority_type_id = 2 THEN 'Black American' WHEN minority_type_id = 3 THEN 'Hispanic American' WHEN minority_type_id = 7 THEN 'Non-M/WBE'
				  WHEN minority_type_id = 9 THEN 'Women' WHEN minority_type_id = 11 THEN 'Individuals and Others' ELSE 'Asian American' END) AS mwbe_category_ui,job_id
    FROM subcontract_spending_details a LEFT JOIN ref_date b ON a.last_modified_date::date = b.date
        LEFT JOIN ref_month c ON b.calendar_month_id = c.month_id
        LEFT JOIN vendor d ON a.prime_vendor_id = d.vendor_id
        LEFT JOIN (select original_agreement_id, contract_number from history_agreement where latest_flag = 'Y') e ON a.contract_number = e.contract_number;



	l_end_time := timeofday()::timestamp;

	INSERT INTO etl.etl_script_execution_status(job_id,script_name,completed_flag,start_time,end_time)
	VALUES(p_job_id_in,'etl.refreshCommonTransactionTables',1,l_start_time,l_end_time);

			RETURN 1;



EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in refreshCommonTransactionTables';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;

	l_end_time := timeofday()::timestamp;

	INSERT INTO etl.etl_script_execution_status(job_id,script_name,completed_flag,start_time,end_time,errno,errmsg)
	VALUES(p_job_id_in,'etl.refreshCommonTransactionTables',0,l_start_time,l_end_time,SQLSTATE,SQLERRM);

	RETURN 0;

END;
$$ language plpgsql;
