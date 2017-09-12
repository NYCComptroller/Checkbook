/*
Functions defined

	updateForeignKeysForMAGInHeader
	updateForeignKeysForMAGInAwardDetail
	processMAG
	updateMAGFlags
	postProcessMAG

*/
set search_path=etl;

CREATE OR REPLACE FUNCTION etl.updateForeignKeysForMAGInHeader(p_load_file_id_in bigint,p_load_id_in bigint) RETURNS INT AS $$
DECLARE
	l_count bigint;
BEGIN
	/* UPDATING FOREIGN KEY VALUES	FOR THE HEADER RECORD*/

	CREATE TEMPORARY TABLE tmp_fk_values (uniq_id bigint, document_code_id smallint,agency_history_id smallint,record_date_id int,
					      effective_begin_date_id int,effective_end_date_id int,source_created_date_id int,
					      source_updated_date_id int,registered_date_id int, original_term_begin_date_id int,
					      original_term_end_date_id int,registered_fiscal_year smallint,registered_fiscal_year_id smallint, registered_calendar_year smallint,
					      registered_calendar_year_id smallint,effective_begin_fiscal_year smallint,effective_begin_fiscal_year_id smallint, effective_begin_calendar_year smallint,
					      effective_begin_calendar_year_id smallint,effective_end_fiscal_year smallint,effective_end_fiscal_year_id smallint, effective_end_calendar_year smallint,
					      effective_end_calendar_year_id smallint,source_updated_fiscal_year smallint,source_updated_calendar_year smallint,source_updated_calendar_year_id smallint,
					      source_updated_fiscal_year_id smallint, board_approved_award_date_id int)
	DISTRIBUTED BY (uniq_id);

	-- FK:Document_Code_id

	INSERT INTO tmp_fk_values(uniq_id,document_code_id)
	SELECT	a.uniq_id, b.document_code_id
	FROM etl.stg_mag_header a JOIN ref_document_code b ON a.doc_cd = b.document_code;

	-- FK:Agency_history_id

	INSERT INTO tmp_fk_values(uniq_id,agency_history_id)
	SELECT	a.uniq_id, max(c.agency_history_id) as agency_history_id
	FROM etl.stg_mag_header a JOIN ref_agency b ON a.doc_dept_cd = b.agency_code
		JOIN ref_agency_history c ON b.agency_id = c.agency_id
	GROUP BY 1;

	CREATE TEMPORARY TABLE tmp_fk_values_new_agencies(dept_cd varchar,uniq_id bigint)
	DISTRIBUTED BY (uniq_id);

	INSERT INTO tmp_fk_values_new_agencies
	SELECT doc_dept_cd,MIN(b.uniq_id) as uniq_id
	FROM etl.stg_mag_header a join (SELECT uniq_id
						 FROM tmp_fk_values
						 GROUP BY 1
						 HAVING max(agency_history_id) is null) b on a.uniq_id=b.uniq_id
	GROUP BY 1;

	RAISE NOTICE '1';

	TRUNCATE etl.ref_agency_id_seq;

	INSERT INTO etl.ref_agency_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_fk_values_new_agencies;

	INSERT INTO ref_agency(agency_id,agency_code,agency_name,created_date,created_load_id,original_agency_name)
	SELECT a.agency_id,b.dept_cd,'<Unknown Agency>' as agency_name,now()::timestamp,p_load_id_in,'<Unknown Agency>' as original_agency_name
	FROM   etl.ref_agency_id_seq a JOIN tmp_fk_values_new_agencies b ON a.uniq_id = b.uniq_id;

	GET DIAGNOSTICS l_count = ROW_COUNT;

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'C',l_count, 'Number of records inserted into ref_agency from MAG header');
	END IF;

	RAISE NOTICE '1.1';

	-- Generate the agency history id for history records

	TRUNCATE etl.ref_agency_history_id_seq;

	INSERT INTO etl.ref_agency_history_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_fk_values_new_agencies;

	INSERT INTO ref_agency_history(agency_history_id,agency_id,agency_name,created_date,load_id)
	SELECT a.agency_history_id,b.agency_id,'<Unknown Agency>' as agency_name,now()::timestamp,p_load_id_in
	FROM   etl.ref_agency_history_id_seq a JOIN etl.ref_agency_id_seq b ON a.uniq_id = b.uniq_id;

	GET DIAGNOSTICS l_count = ROW_COUNT;

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'C',l_count, 'Number of records inserted into ref_agency_history from MAG header');
	END IF;

	RAISE NOTICE '1.3';

	INSERT INTO tmp_fk_values(uniq_id,agency_history_id)
	SELECT	a.uniq_id, max(c.agency_history_id)
	FROM etl.stg_mag_header a JOIN ref_agency b ON a.doc_dept_cd = b.agency_code
		JOIN ref_agency_history c ON b.agency_id = c.agency_id
		JOIN etl.ref_agency_history_id_seq d ON c.agency_history_id = d.agency_history_id
	GROUP BY 1	;

	-- FK:record_date_id

	INSERT INTO tmp_fk_values(uniq_id,record_date_id)
	SELECT	a.uniq_id, b.date_id
	FROM etl.stg_mag_header a JOIN ref_date b ON a.doc_rec_dt_dc = b.date;

	--FK:effective_begin_date_id

	INSERT INTO tmp_fk_values(uniq_id,effective_begin_date_id,effective_begin_fiscal_year,effective_begin_fiscal_year_id, effective_begin_calendar_year,effective_begin_calendar_year_id)
	SELECT	a.uniq_id, b.date_id,c.year_value,b.nyc_year_id,e.year_value,d.year_id
	FROM etl.stg_mag_header a JOIN ref_date b ON a.efbgn_dt = b.date
		JOIN ref_year c ON b.nyc_year_id = c.year_id
		JOIN ref_month d ON b.calendar_month_id = d.month_id
		JOIN ref_year e ON d.year_id = e.year_id;

	--FK:effective_end_date_id

	INSERT INTO tmp_fk_values(uniq_id,effective_end_date_id,effective_end_fiscal_year,effective_end_fiscal_year_id, effective_end_calendar_year,effective_end_calendar_year_id)
	SELECT	a.uniq_id, b.date_id,c.year_value,b.nyc_year_id,e.year_value,d.year_id
	FROM etl.stg_mag_header a JOIN ref_date b ON a.efend_dt = b.date
		JOIN ref_year c ON b.nyc_year_id = c.year_id
		JOIN ref_month d ON b.calendar_month_id = d.month_id
		JOIN ref_year e ON d.year_id = e.year_id;

	RAISE NOTICE '1.4';

	--FK:source_created_date_id

	INSERT INTO tmp_fk_values(uniq_id,source_created_date_id)
	SELECT	a.uniq_id, b.date_id
	FROM etl.stg_mag_header a JOIN ref_date b ON a.doc_appl_crea_dt = b.date;

	--FK:source_updated_date_id

	INSERT INTO tmp_fk_values(uniq_id,source_updated_date_id,source_updated_fiscal_year,source_updated_fiscal_year_id, source_updated_calendar_year,source_updated_calendar_year_id)
	SELECT	a.uniq_id, b.date_id,c.year_value,b.nyc_year_id,e.year_value,d.year_id
	FROM etl.stg_mag_header a JOIN ref_date b ON a.doc_appl_last_dt = b.date
		JOIN ref_year c ON b.nyc_year_id = c.year_id
		JOIN ref_month d ON b.calendar_month_id = d.month_id
		JOIN ref_year e ON d.year_id = e.year_id;

	--FK:registered_date_id

	INSERT INTO tmp_fk_values(uniq_id,registered_date_id, registered_fiscal_year,registered_fiscal_year_id, registered_calendar_year,registered_calendar_year_id)
	SELECT	a.uniq_id, b.date_id,c.year_value,b.nyc_year_id,e.year_value,d.year_id
	FROM etl.stg_mag_header a JOIN ref_date b ON a.reg_dt = b.date
		JOIN ref_year c ON b.nyc_year_id = c.year_id
		JOIN ref_month d ON b.calendar_month_id = d.month_id
		JOIN ref_year e ON d.year_id = e.year_id;

	RAISE NOTICE '1.5';

	--FK:original_term_begin_date_id

	INSERT INTO tmp_fk_values(uniq_id,original_term_begin_date_id)
	SELECT	a.uniq_id, b.date_id
	FROM etl.stg_mag_header a JOIN ref_date b ON a.orig_strt_dt = b.date;

	--FK:original_term_end_date_id

	INSERT INTO tmp_fk_values(uniq_id,original_term_end_date_id)
	SELECT	a.uniq_id, b.date_id
	FROM etl.stg_mag_header a JOIN ref_date b ON a.orig_end_dt = b.date;

	-- FK:board_approved_award_date_id
	INSERT INTO tmp_fk_values(uniq_id,board_approved_award_date_id)
	SELECT	a.uniq_id, b.date_id
	FROM etl.stg_mag_header a JOIN ref_date b ON a.brd_awd_dt = b.date;

	RAISE NOTICE '1.6';

	--Updating stg_mag_header with all the FK values

	UPDATE etl.stg_mag_header a
	SET	document_code_id = ct_table.document_code_id ,
		agency_history_id = ct_table.agency_history_id,
		record_date_id = ct_table.record_date_id,
		effective_begin_date_id = ct_table.effective_begin_date_id,
		effective_end_date_id = ct_table.effective_end_date_id,
		source_created_date_id = ct_table.source_created_date_id,
		source_updated_date_id = ct_table.source_updated_date_id,
		registered_date_id = ct_table.registered_date_id,
		original_term_begin_date_id = ct_table.original_term_begin_date_id,
		original_term_end_date_id = ct_table.original_term_end_date_id,
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
		source_updated_calendar_year_id = ct_table.source_updated_calendar_year_id,
		board_approved_award_date_id = ct_table.board_approved_award_date_id

	FROM	(SELECT uniq_id, max(document_code_id) as document_code_id ,
				 max(agency_history_id) as agency_history_id,
				 max(record_date_id) as record_date_id,
				 max(effective_begin_date_id) as effective_begin_date_id,
				 max(effective_end_date_id) as effective_end_date_id,
				 max(source_created_date_id) as source_created_date_id,
				 max(source_updated_date_id) as source_updated_date_id,
				 max(registered_date_id) as registered_date_id,
				 max(original_term_begin_date_id) as original_term_begin_date_id,
				 max(original_term_end_date_id) as original_term_end_date_id,
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
				 max(source_updated_calendar_year_id) as source_updated_calendar_year_id,
				 max(board_approved_award_date_id) as board_approved_award_date_id
		 FROM	tmp_fk_values
		 GROUP BY 1) ct_table
	WHERE	a.uniq_id = ct_table.uniq_id;

	RETURN 1;
EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in updateForeignKeysForMAGInHeader';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;

	RETURN 0;
END;
$$ language plpgsql;

-------------------------------------------------------------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION updateForeignKeysForMAGInAwardDetail() RETURNS INT AS $$
DECLARE
BEGIN
	-- UPDATING FK VALUES IN AWARD DETAIL

	CREATE TEMPORARY TABLE tmp_fk_values_award_detail(uniq_id bigint,award_method_id smallint,agreement_type_id smallint,
						      		award_category_id_1 smallint,award_category_id_2 smallint, award_category_id_3 smallint, award_category_id_4 smallint,
					      			award_category_id_5 smallint)
	DISTRIBUTED BY (uniq_id);

	-- FK:award_method_id

	INSERT INTO tmp_fk_values_award_detail(uniq_id,award_method_id)
	SELECT a.uniq_id , b.award_method_id
	FROM	etl.stg_mag_award_detail a JOIN ref_award_method b ON a.awd_meth_cd = b.award_method_code;

	--FK:agreement_type_id

	INSERT INTO tmp_fk_values_award_detail(uniq_id,agreement_type_id)
	SELECT a.uniq_id , b.agreement_type_id
	FROM	etl.stg_mag_award_detail a JOIN ref_agreement_type b ON a.cttyp_cd = b.agreement_type_code;

	--FK:award_category_id_1

	INSERT INTO tmp_fk_values_award_detail(uniq_id,award_category_id_1)
	SELECT a.uniq_id , b.award_category_id
	FROM	etl.stg_mag_award_detail a JOIN ref_award_category b ON a.ctcat_cd_1 = b.award_category_code;

	--FK:award_category_id_2

	INSERT INTO tmp_fk_values_award_detail(uniq_id,award_category_id_2)
	SELECT a.uniq_id , b.award_category_id
	FROM	etl.stg_mag_award_detail a JOIN ref_award_category b ON a.ctcat_cd_2 = b.award_category_code;

	--FK:award_category_id_3

	INSERT INTO tmp_fk_values_award_detail(uniq_id,award_category_id_3)
	SELECT a.uniq_id , b.award_category_id
	FROM	etl.stg_mag_award_detail a JOIN ref_award_category b ON a.ctcat_cd_3 = b.award_category_code;

	--FK:award_category_id_4

	INSERT INTO tmp_fk_values_award_detail(uniq_id,award_category_id_4)
	SELECT a.uniq_id , b.award_category_id
	FROM	etl.stg_mag_award_detail a JOIN ref_award_category b ON a.ctcat_cd_4 = b.award_category_code;

	--FK:award_category_id_5

	INSERT INTO tmp_fk_values_award_detail(uniq_id,award_category_id_5)
	SELECT a.uniq_id , b.award_category_id
	FROM	etl.stg_mag_award_detail a JOIN ref_award_category b ON a.ctcat_cd_5 = b.award_category_code;


	UPDATE etl.stg_mag_award_detail a
	SET	award_method_id = ct_table.award_method_id ,
		agreement_type_id = ct_table.agreement_type_id ,
		award_category_id_1 = ct_table.award_category_id_1 ,
		award_category_id_2 = ct_table.award_category_id_2 ,
		award_category_id_3 = ct_table.award_category_id_3 ,
		award_category_id_4 = ct_table.award_category_id_4 ,
		award_category_id_5 = ct_table.award_category_id_5
	FROM
		(SELECT uniq_id,
			max(award_method_id) as award_method_id ,
			max(agreement_type_id) as agreement_type_id   ,
			max(award_category_id_1) as award_category_id_1	 ,
			max(award_category_id_2) as award_category_id_2	 ,
			max(award_category_id_3) as award_category_id_3	 ,
			max(award_category_id_4) as award_category_id_4	 ,
			max(award_category_id_5) as award_category_id_5
		FROM 	tmp_fk_values_award_detail
		GROUP BY 1 )ct_table
	WHERE	a.uniq_id = ct_table.uniq_id;


	RETURN 1;
EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in updateForeignKeysForMAGInAwardDetail';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;

	RETURN 0;
END;
$$ language plpgsql;

------------------------------------------------------------------------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION etl.processMAG(p_load_file_id_in int,p_load_id_in bigint) RETURNS INT AS $$
DECLARE
	l_worksite_col_array VARCHAR ARRAY[10];
	l_array_ctr smallint;
	l_fk_update int;
	l_worksite_per_array VARCHAR ARRAY[10];
	l_insert_sql VARCHAR;
	l_count bigint;
BEGIN
	l_worksite_col_array := ARRAY['wk_site_cd_01',
				      'wk_site_cd_02',
				      'wk_site_cd_03',
				      'wk_site_cd_04',
				      'wk_site_cd_05',
				      'wk_site_cd_06',
				      'wk_site_cd_07',
				      'wk_site_cd_08',
				      'wk_site_cd_09',
				      'wk_site_cd_10'];

	l_worksite_per_array := ARRAY['percent_01',
				      'percent_02',
				      'percent_03',
				      'percent_04',
				      'percent_05',
				      'percent_06',
				      'percent_07',
				      'percent_08',
				      'percent_09',
				      'percent_10'];


	l_fk_update := etl.updateForeignKeysForMAGInHeader(p_load_file_id_in,p_load_id_in);

	RAISE NOTICE 'MAG 1';

	IF l_fk_update = 1 THEN
		l_fk_update := etl.updateForeignKeysForMAGInAwardDetail();
	ELSE
		RETURN -1;
	END IF;

	RAISE NOTICE 'MAG 2';

	IF l_fk_update = 1 THEN
		l_fk_update := etl.processvendor(p_load_file_id_in,p_load_id_in,'');
	ELSE
		RETURN -1;
	END IF;


	IF l_fk_update <> 1 THEN
		RETURN -1;
	END IF;


	RAISE NOTICE 'MAG 3';


	-- Inserting all records from staging header

	RAISE NOTICE 'MAG 4';
	CREATE TEMPORARY TABLE tmp_mag(uniq_id bigint, agency_history_id smallint,doc_id varchar,master_agreement_id bigint, action_flag char(1),
					  latest_flag char(1),doc_vers_no smallint,privacy_flag char(1),old_agreement_ids varchar)
	DISTRIBUTED BY (uniq_id);

	INSERT INTO tmp_mag(uniq_id,agency_history_id,doc_id,doc_vers_no,privacy_flag,action_flag)
	SELECT uniq_id,agency_history_id,doc_id,doc_vers_no,'F' as privacy_flag,'I' as action_flag
	FROM etl.stg_mag_header;

	-- Identifying the versions of the agreements for update (doubt)
	CREATE TEMPORARY TABLE tmp_old_mag(uniq_id bigint, master_agreement_id bigint);

	INSERT INTO tmp_old_mag
	SELECT  uniq_id,
		b.master_agreement_id
	FROM etl.stg_mag_header a JOIN history_master_agreement b ON a.doc_id = b.document_id AND a.document_code_id = b.document_code_id AND a.doc_vers_no = b.document_version
		JOIN ref_agency_history c ON a.agency_history_id = c.agency_history_id
		JOIN ref_agency_history d ON b.agency_history_id = d.agency_history_id and c.agency_id = d.agency_id;

	UPDATE tmp_mag a
	SET	master_agreement_id = b.master_agreement_id,
		action_flag = 'U'
	FROM	tmp_old_mag b
	WHERE	a.uniq_id = b.uniq_id;

	RAISE NOTICE 'MAG 5';
	-- Identifying the versions of the agreements for update

	TRUNCATE etl.agreement_id_seq;

	INSERT INTO etl.agreement_id_seq
	SELECT uniq_id
	FROM	tmp_mag
	WHERE	action_flag ='I'
		AND COALESCE(master_agreement_id,0) =0 ;

	UPDATE tmp_mag a
	SET	master_agreement_id = b.agreement_id
	FROM	etl.agreement_id_seq b
	WHERE	a.uniq_id = b.uniq_id;

	-- doubt
	INSERT INTO history_master_agreement(master_agreement_id,document_code_id,
				agency_history_id,document_id,document_version,
				tracking_number,record_date_id,budget_fiscal_year,
				document_fiscal_year,document_period,description,
				actual_amount_original,actual_amount,
				total_amount_original,total_amount,
				maximum_spending_limit_original,maximum_spending_limit,
				replacing_master_agreement_id,replaced_by_master_agreement_id,
				award_status_id,procurement_id,procurement_type_id,
				effective_begin_date_id,effective_end_date_id,reason_modification,
				source_created_date_id,source_updated_date_id,document_function_code,
				award_method_id,award_level_code,agreement_type_id,
				contract_class_code,award_category_id_1,award_category_id_2,
				award_category_id_3,award_category_id_4,award_category_id_5,
				number_responses,location_service,location_zip,
				borough_code,block_code,lot_code,
				council_district_code,vendor_history_id,vendor_preference_level,
				board_approved_award_no, board_approved_award_date_id,
				original_contract_amount_original,original_contract_amount,registered_date_id,oca_number,
				number_solicitation,document_name,original_term_begin_date_id,
				original_term_end_date_id,privacy_flag,created_load_id,created_date,
				registered_fiscal_year,registered_fiscal_year_id, registered_calendar_year,
				registered_calendar_year_id,effective_end_fiscal_year,effective_end_fiscal_year_id,
				effective_end_calendar_year,effective_end_calendar_year_id,effective_begin_fiscal_year,
				effective_begin_fiscal_year_id, effective_begin_calendar_year,effective_begin_calendar_year_id,
		   		source_updated_fiscal_year,source_updated_fiscal_year_id, source_updated_calendar_year,
		   		source_updated_calendar_year_id,contract_number)
	SELECT	d.master_agreement_id,a.document_code_id,
		a.agency_history_id,a.doc_id,a.doc_vers_no,
		a.trkg_no,a.record_date_id,a.doc_bfy,
		a.doc_fy_dc,a.doc_per_dc,a.doc_dscr,
		a.doc_actu_am,(CASE WHEN a.doc_actu_am IS NULL THEN 0 ELSE a.doc_actu_am END ) as actual_amount,
		a.ord_tot_am, (CASE WHEN a.ord_tot_am IS NULL THEN 0 ELSE a.ord_tot_am END ) as total_amount,
		a.ma_prch_lmt_am, (CASE WHEN a.ma_prch_lmt_am IS NULL THEN 0 ELSE a.ma_prch_lmt_am END ) as maximum_spending_limit,
		0 as replacing_master_agreement_id,0 as replaced_by_master_agreement_id,
		a.cntrc_sta,a.prcu_id,a.prcu_typ_id,
		a.effective_begin_date_id,a.effective_end_date_id,a.reas_mod_dc,
		a.source_created_date_id,a.source_updated_date_id,a.doc_func_cd,
		c.award_method_id,c.awd_lvl_cd,c.agreement_type_id,
		c.ctcls_cd,c.award_category_id_1,c.award_category_id_2,
		c.award_category_id_3,c.award_category_id_4,c.award_category_id_5,
		c.resp_no,c.loc_serv,c.loc_zip,
		c.brgh_cd,c.blck_cd,c.lot_cd,
		c.coun_dist_cd,b.vendor_history_id,b.vend_pref_lvl,
		a.brd_awd_no,a.board_approved_award_date_id,
		a.orig_max_ct_amt, (CASE WHEN a.orig_max_ct_amt IS NULL THEN 0 ELSE a.orig_max_ct_amt END ) as original_contract_amount, a.registered_date_id,a.oca_no,
		c.out_of_no_so,a.doc_nm,a.original_term_begin_date_id,
		a.original_term_end_date_id,d.privacy_flag,p_load_id_in,now()::timestamp,
		registered_fiscal_year,registered_fiscal_year_id, registered_calendar_year,
		registered_calendar_year_id,effective_end_fiscal_year,effective_end_fiscal_year_id,
		effective_end_calendar_year,effective_end_calendar_year_id,effective_begin_fiscal_year,
		effective_begin_fiscal_year_id, effective_begin_calendar_year,effective_begin_calendar_year_id,
		source_updated_fiscal_year,source_updated_fiscal_year_id, source_updated_calendar_year,
		source_updated_calendar_year_id,a.doc_cd||a.doc_dept_cd||a.doc_id as document_version
	FROM	etl.stg_mag_header a JOIN etl.stg_mag_vendor b ON a.doc_cd = b.doc_cd AND a.doc_dept_cd = b.doc_dept_cd
					     AND a.doc_id = b.doc_id AND a.doc_vers_no = b.doc_vers_no
					JOIN etl.stg_mag_award_detail c ON a.doc_cd = c.doc_cd AND a.doc_dept_cd = c.doc_dept_cd
					     AND a.doc_id = c.doc_id AND a.doc_vers_no = c.doc_vers_no
					 JOIN tmp_mag d ON a.uniq_id = d.uniq_id
	WHERE   action_flag='I';

	GET DIAGNOSTICS l_count = ROW_COUNT;

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'M',l_count, '# of records inserted into history_master_agreement from MAG Feed');
	END IF;

	RAISE NOTICE 'MAG 6';
	/* Updates */
	CREATE TEMPORARY TABLE tmp_mag_update AS
	SELECT d.master_agreement_id,a.document_code_id,
			a.agency_history_id,a.doc_id,a.doc_vers_no,
			a.trkg_no,a.record_date_id,a.doc_bfy,
			a.doc_fy_dc,a.doc_per_dc,a.doc_dscr,
			a.doc_actu_am,a.ord_tot_am,a.ma_prch_lmt_am,
			0 as replacing_master_agreement_id,0 as replaced_by_master_agreement_id,
			a.award_status_id,a.prcu_id,a.prcu_typ_id,
			a.effective_begin_date_id,a.effective_end_date_id,a.reas_mod_dc,
			a.source_created_date_id,a.source_updated_date_id,a.doc_func_cd,
			c.award_method_id,c.awd_lvl_cd,c.agreement_type_id,
			c.ctcls_cd,c.award_category_id_1,c.award_category_id_2,
			c.award_category_id_3,c.award_category_id_4,c.award_category_id_5,
			c.resp_no,c.loc_serv,c.loc_zip,
			c.brgh_cd,c.blck_cd,c.lot_cd,
			c.coun_dist_cd,b.vendor_history_id,b.vend_pref_lvl,
			a.brd_awd_no,a.board_approved_award_date_id,
			a.orig_max_ct_amt,a.registered_date_id,a.oca_no,
			c.out_of_no_so,a.doc_nm,a.original_term_begin_date_id,
			a.original_term_end_date_id,d.privacy_flag,p_load_id_in as load_id,now()::timestamp as updated_date,
			registered_fiscal_year,registered_fiscal_year_id, registered_calendar_year,
			registered_calendar_year_id,effective_end_fiscal_year,effective_end_fiscal_year_id,
			effective_end_calendar_year,effective_end_calendar_year_id,effective_begin_fiscal_year,
			effective_begin_fiscal_year_id, effective_begin_calendar_year,effective_begin_calendar_year_id,
			source_updated_fiscal_year,source_updated_fiscal_year_id, source_updated_calendar_year,
			source_updated_calendar_year_id
		FROM	etl.stg_mag_header a JOIN etl.stg_mag_vendor b ON a.doc_cd = b.doc_cd AND a.doc_dept_cd = b.doc_dept_cd
						     AND a.doc_id = b.doc_id AND a.doc_vers_no = b.doc_vers_no
						JOIN etl.stg_mag_award_detail c ON a.doc_cd = c.doc_cd AND a.doc_dept_cd = c.doc_dept_cd
						     AND a.doc_id = c.doc_id AND a.doc_vers_no = c.doc_vers_no
						 JOIN tmp_mag d ON a.uniq_id = d.uniq_id
	WHERE   action_flag='U'
	DISTRIBUTED BY (master_agreement_id);

	UPDATE history_master_agreement a
	SET	document_code_id = b.document_code_id,
		agency_history_id  = b.agency_history_id,
		document_id  = b.doc_id,
		document_version = b.doc_vers_no,
		tracking_number = b.trkg_no,
		record_date_id = b.record_date_id,
		budget_fiscal_year = b.doc_bfy,
		document_fiscal_year = b.doc_fy_dc,
		document_period = b.doc_per_dc,
		description = b.doc_dscr,
		actual_amount_original = b.doc_actu_am,
		actual_amount = (CASE WHEN b.doc_actu_am IS NULL THEN 0 ELSE b.doc_actu_am END ) ,
		total_amount_original = b.ord_tot_am,
		total_amount = (CASE WHEN b.ord_tot_am IS NULL THEN 0 ELSE b.ord_tot_am END ) ,
		maximum_spending_limit_original = b.ma_prch_lmt_am,
		maximum_spending_limit = (CASE WHEN b.ma_prch_lmt_am IS NULL THEN 0 ELSE b.ma_prch_lmt_am END ) ,
		replacing_master_agreement_id = b.replacing_master_agreement_id,
		replaced_by_master_agreement_id = b.replaced_by_master_agreement_id,
		award_status_id = b.award_status_id,
		procurement_id = b.prcu_id,
		procurement_type_id = b.prcu_typ_id,
		effective_begin_date_id = b.effective_begin_date_id,
		effective_end_date_id = b.effective_end_date_id,
		reason_modification = b.reas_mod_dc,
		source_created_date_id = b.source_created_date_id,
		source_updated_date_id = b.source_updated_date_id,
		document_function_code = b.doc_func_cd,
		award_method_id = b.award_method_id,
		award_level_code = b.awd_lvl_cd,
		agreement_type_id = b.agreement_type_id,
		contract_class_code = b.ctcls_cd,
		award_category_id_1 = b.award_category_id_1,
		award_category_id_2 = b.award_category_id_2,
		award_category_id_3 = b.award_category_id_3,
		award_category_id_4 = b.award_category_id_4,
		award_category_id_5 = b.award_category_id_5,
		number_responses = b.resp_no,
		location_service = b.loc_serv,
		location_zip = b.loc_zip,
		borough_code = b.brgh_cd,
		block_code = b.blck_cd,
		lot_code = b.lot_cd,
		council_district_code = b.coun_dist_cd,
		vendor_history_id = b.vendor_history_id,
		vendor_preference_level = b.vend_pref_lvl,
		board_approved_award_no = b.brd_awd_no,
		board_approved_award_date_id = b.board_approved_award_date_id,
		original_contract_amount_original = b.orig_max_ct_amt,
		original_contract_amount = (CASE WHEN b.orig_max_ct_amt IS NULL THEN 0 ELSE b.orig_max_ct_amt END ) ,
		registered_date_id = b.registered_date_id,
		oca_number = b.oca_no,
		number_solicitation = b.out_of_no_so,
		document_name = b.doc_nm,
		original_term_begin_date_id = b.original_term_begin_date_id,
		original_term_end_date_id = b.original_term_end_date_id,
		privacy_flag = b.privacy_flag,
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
		source_updated_calendar_year_id = b.source_updated_calendar_year_id
	FROM	tmp_mag_update b
	WHERE	a.master_agreement_id = b.master_agreement_id;

	GET DIAGNOSTICS l_count = ROW_COUNT;

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'M',l_count, '# of records updated in history_master_agreement from MAG Feed');
	END IF;

	RAISE NOTICE 'MAG 7';

  -- For now not processing worksite and commodities
  --   agreement worksite changes
	/*


	DELETE FROM history_agreement_worksite a
	USING tmp_mag b
	WHERE a.agreement_id = b.master_agreement_id
	      AND b.action_flag ='U';

	GET DIAGNOSTICS l_count = ROW_COUNT;

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'M',l_count, 'MAG worksite records deleted due to the updated from MAG Feed');
	END IF;

	FOR l_array_ctr IN 1..array_upper(l_worksite_col_array,1) LOOP

		l_insert_sql := ' INSERT INTO history_agreement_worksite(agreement_id,worksite_code,percentage,amount,master_agreement_yn,load_id,created_date) '||
				' SELECT d.master_agreement_id,b.'||l_worksite_col_array[l_array_ctr]||',b.'|| l_worksite_per_array[l_array_ctr] || ',(a.ma_prch_lmt_am *b.'||l_worksite_per_array[l_array_ctr] || ')/100 as amount ,''Y'',' ||p_load_id_in || ', now()::timestamp '||
				' FROM	etl.stg_mag_header a JOIN etl.stg_mag_award_detail b ON a.doc_cd = b.doc_cd AND a.doc_dept_cd = b.doc_dept_cd '||
				'			     AND a.doc_id = b.doc_id AND a.doc_vers_no = b.doc_vers_no '||
				'			     JOIN tmp_mag d ON a.uniq_id = d.uniq_id '||
				' WHERE b.'|| l_worksite_col_array[l_array_ctr] || ' IS NOT NULL' ;

		EXECUTE l_insert_sql;

		GET DIAGNOSTICS l_count = ROW_COUNT;
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,document_type,num_transactions,description)
		VALUES(p_load_file_id_in,'M','',l_count,'# of records inserted into history_agreement_worksite from MAG Feed');

	END LOOP;

	RAISE NOTICE 'MAG 8';

	DELETE FROM history_agreement_commodity a
	USING tmp_mag b
	WHERE a.agreement_id = b.master_agreement_id
	      AND b.action_flag ='U';

	INSERT INTO history_agreement_commodity(agreement_id,line_number,master_agreement_yn,
					    description,commodity_code,commodity_type_id,
					    quantity,unit_of_measurement,unit_price,
					    contract_amount,commodity_specification,load_id,
					    created_date)
	SELECT	d.master_agreement_id,b.doc_comm_ln_no,'Y' as master_agreement_yn,
		b.cl_dscr,b.comm_cd,b.ln_typ,
		b.qty,b.unit_meas_cd,b.unit_price,
		b.cntrc_am,b.comm_cd_spfn,p_load_id_in,
		now()::timestamp
	FROM	etl.stg_mag_header a JOIN etl.stg_mag_commodity b ON a.doc_cd = b.doc_cd AND a.doc_dept_cd = b.doc_dept_cd
						     AND a.doc_id = b.doc_id AND a.doc_vers_no = b.doc_vers_no
						     JOIN tmp_mag d ON a.uniq_id = d.uniq_id;

	*/
	l_fk_update := etl.updateMAGFlags(p_load_id_in);

	IF l_fk_update <> 1 THEN
		RETURN -1;
	END IF;

	RETURN 1;

EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in processMAG';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;

	RETURN 0;
END;
$$ language plpgsql;


--------------------------------------------------------------------------------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION etl.updateMAGFlags(p_load_id_in bigint) RETURNS INT AS $$
DECLARE
BEGIN


	-- Get the master agreements (key elements only without version) which have been created or updated

	CREATE TEMPORARY TABLE tmp_loaded_master_agreements_flags(document_id varchar,document_version integer,document_code_id smallint, agency_id smallint,
		latest_version_no smallint,first_version smallint ) DISTRIBUTED BY (document_id);

	INSERT INTO tmp_loaded_master_agreements_flags
	SELECT distinct document_id,document_version,document_code_id, agency_id
	FROM history_master_agreement a JOIN ref_agency_history b ON a.agency_history_id = b.agency_history_id
	WHERE coalesce(a.updated_load_id, a.created_load_id) = p_load_id_in ;

	-- Get the max version and min version

	CREATE TEMPORARY TABLE tmp_loaded_master_agreements_1_flags(document_id varchar,document_code_id smallint, agency_id smallint,
		latest_version_no smallint,first_version_no smallint ) DISTRIBUTED BY (document_id);

	INSERT INTO tmp_loaded_master_agreements_1_flags
	SELECT a.document_id,a.document_code_id, b.agency_id,
	       max(a.document_version) as latest_version_no, min(a.document_version) as first_version_no
	FROM history_master_agreement a JOIN tmp_loaded_master_agreements_flags b ON a.document_id = b.document_id AND a.document_code_id = b.document_code_id
		JOIN ref_agency_history c ON a.agency_history_id = c.agency_history_id AND c.agency_id = b.agency_id
		GROUP BY 1,2,3;

	RAISE NOTICE 'PMAG_FLAG1';

	-- Update the versions which are no more the first versions


	CREATE TEMPORARY TABLE tmp_master_agreement_flag_changes_flags (document_id varchar,document_code_id smallint, agency_id smallint,
					latest_master_agreement_id bigint, first_master_agreement_id bigint,non_latest_master_agreement_id varchar, non_first_master_agreement_id varchar
					) DISTRIBUTED BY (document_id);

	INSERT INTO tmp_master_agreement_flag_changes_flags
	SELECT a.document_id,a.document_code_id, b.agency_id,
		MAX(CASE WHEN a.document_version = b.latest_version_no THEN master_agreement_id END) as latest_master_agreement_id,
		MAX(CASE WHEN a.document_version = b.first_version_no THEN master_agreement_id END) as first_master_agreement_id,
		group_concat(CASE WHEN a.document_version <> b.latest_version_no THEN master_agreement_id ELSE 0 END) as non_latest_master_agreement_id,
		group_concat(CASE WHEN a.document_version <> b.first_version_no THEN master_agreement_id ELSE 0 END) as non_first_master_agreement_id
	FROM   history_master_agreement a JOIN tmp_loaded_master_agreements_1_flags b ON a.document_id = b.document_id AND a.document_code_id = b.document_code_id
		JOIN ref_agency_history c ON a.agency_history_id = c.agency_history_id AND c.agency_id = b.agency_id
	GROUP BY 1,2,3;

	RAISE NOTICE 'PMAG_FLAG2';

	-- Updating the original flag for non first agreements
	CREATE TEMPORARY TABLE tmp_master_agreement_unnest_flags(master_agreement_id bigint, first_master_agreement_id bigint)
	DISTRIBUTED BY (master_agreement_id);

	TRUNCATE TABLE tmp_master_agreement_unnest_flags;

	INSERT INTO tmp_master_agreement_unnest_flags
	SELECT unnest(string_to_array(non_first_master_agreement_id,','))::int as master_agreement_id ,
		first_master_agreement_id
	FROM	tmp_master_agreement_flag_changes_flags ;


	UPDATE history_master_agreement a
	SET    original_version_flag = 'N',
		original_master_agreement_id = b.first_master_agreement_id
	FROM   tmp_master_agreement_unnest_flags b
	WHERE  a.master_agreement_id = b.master_agreement_id;

	TRUNCATE TABLE tmp_master_agreement_unnest_flags;

	INSERT INTO tmp_master_agreement_unnest_flags
	SELECT unnest(string_to_array(non_latest_master_agreement_id,','))::int as master_agreement_id,
	NULL as first_master_agreement_id
	FROM	tmp_master_agreement_flag_changes_flags ;

	UPDATE history_master_agreement a
	SET    latest_flag = 'N'
	FROM   tmp_master_agreement_unnest_flags b
	WHERE  a.master_agreement_id = b.master_agreement_id
		AND a.latest_flag = 'Y';

	UPDATE history_master_agreement a
	SET     original_version_flag = 'Y',
		original_master_agreement_id = b.first_master_agreement_id
	FROM    tmp_master_agreement_flag_changes_flags  b
	WHERE  a.master_agreement_id = b.first_master_agreement_id;


	UPDATE history_master_agreement a
	SET    latest_flag = 'Y'
	FROM    tmp_master_agreement_flag_changes_flags  b
	WHERE  a.master_agreement_id = b.latest_master_agreement_id
		AND COALESCE(a.latest_flag,'N') = 'N';


	RAISE NOTICE 'PMAG_FLAG3';



		RETURN 1;



EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in updateMAGFlags';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;

	RETURN 0;

END;
$$ language plpgsql;


-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------


CREATE OR REPLACE FUNCTION etl.postProcessMAG(p_job_id_in bigint) RETURNS INT AS $$
DECLARE

	l_start_time  timestamp;
	l_end_time  timestamp;
	l_load_id bigint;
BEGIN

	l_start_time := timeofday()::timestamp;

	SELECT load_id
	FROM etl.etl_data_load
	WHERE job_id = p_job_id_in	AND data_source_code = 'M'
	INTO l_load_id;

	-- Get the master agreements (key elements only without version) which have been created or updated

	CREATE TEMPORARY TABLE tmp_loaded_master_agreements(document_id varchar,document_version integer,document_code_id smallint, agency_id smallint,
		latest_version_no smallint,first_version smallint ) DISTRIBUTED BY (document_id);

	INSERT INTO tmp_loaded_master_agreements
	SELECT distinct document_id,document_version,document_code_id, agency_id
	FROM history_master_agreement a JOIN ref_agency_history b ON a.agency_history_id = b.agency_history_id
	JOIN etl.etl_data_load c ON coalesce(a.updated_load_id, a.created_load_id) = c.load_id
	WHERE c.job_id = p_job_id_in AND c.data_source_code IN ('C','M','F');

	-- Get the max version and min version

	CREATE TEMPORARY TABLE tmp_loaded_master_agreements_1(document_id varchar,document_code_id smallint, agency_id smallint,
		latest_version_no smallint,first_version_no smallint ) DISTRIBUTED BY (document_id);

	INSERT INTO tmp_loaded_master_agreements_1
	SELECT a.document_id,a.document_code_id, b.agency_id,
	       max(a.document_version) as latest_version_no, min(a.document_version) as first_version_no
	FROM history_master_agreement a JOIN tmp_loaded_master_agreements b ON a.document_id = b.document_id AND a.document_code_id = b.document_code_id
		JOIN ref_agency_history c ON a.agency_history_id = c.agency_history_id AND c.agency_id = b.agency_id
		GROUP BY 1,2,3;

	RAISE NOTICE 'PMAG1';

	-- Update the versions which are no more the first versions


	CREATE TEMPORARY TABLE tmp_master_agreement_flag_changes (document_id varchar,document_code_id smallint, agency_id smallint,
					latest_master_agreement_id bigint, first_master_agreement_id bigint,non_latest_master_agreement_id varchar, non_first_master_agreement_id varchar
					) DISTRIBUTED BY (document_id);

	INSERT INTO tmp_master_agreement_flag_changes
	SELECT a.document_id,a.document_code_id, b.agency_id,
		MAX(CASE WHEN a.document_version = b.latest_version_no THEN master_agreement_id END) as latest_master_agreement_id,
		MAX(CASE WHEN a.document_version = b.first_version_no THEN master_agreement_id END) as first_master_agreement_id,
		group_concat(CASE WHEN a.document_version <> b.latest_version_no THEN master_agreement_id ELSE 0 END) as non_latest_master_agreement_id,
		group_concat(CASE WHEN a.document_version <> b.first_version_no THEN master_agreement_id ELSE 0 END) as non_first_master_agreement_id
	FROM   history_master_agreement a JOIN tmp_loaded_master_agreements_1 b ON a.document_id = b.document_id AND a.document_code_id = b.document_code_id
		JOIN ref_agency_history c ON a.agency_history_id = c.agency_history_id AND c.agency_id = b.agency_id
	GROUP BY 1,2,3;

	RAISE NOTICE 'PMAG2';

	-- Updating the original flag for non first agreements
	CREATE TEMPORARY TABLE tmp_master_agreement_unnest(master_agreement_id bigint, first_master_agreement_id bigint)
	DISTRIBUTED BY (master_agreement_id);

	TRUNCATE TABLE tmp_master_agreement_unnest;

	INSERT INTO tmp_master_agreement_unnest
	SELECT unnest(string_to_array(non_first_master_agreement_id,','))::int as master_agreement_id ,
		first_master_agreement_id
	FROM	tmp_master_agreement_flag_changes ;


	UPDATE history_master_agreement a
	SET    original_version_flag = 'N',
		original_master_agreement_id = b.first_master_agreement_id
	FROM   tmp_master_agreement_unnest b
	WHERE  a.master_agreement_id = b.master_agreement_id;

	TRUNCATE TABLE tmp_master_agreement_unnest;

	INSERT INTO tmp_master_agreement_unnest
	SELECT unnest(string_to_array(non_latest_master_agreement_id,','))::int as master_agreement_id,
	NULL as first_master_agreement_id
	FROM	tmp_master_agreement_flag_changes ;

	UPDATE history_master_agreement a
	SET    latest_flag = 'N'
	FROM   tmp_master_agreement_unnest b
	WHERE  a.master_agreement_id = b.master_agreement_id
		AND a.latest_flag = 'Y';

	UPDATE history_master_agreement a
	SET     original_version_flag = 'Y',
		original_master_agreement_id = b.first_master_agreement_id
	FROM    tmp_master_agreement_flag_changes  b
	WHERE  a.master_agreement_id = b.first_master_agreement_id;


	UPDATE history_master_agreement a
	SET    latest_flag = 'Y'
	FROM    tmp_master_agreement_flag_changes  b
	WHERE  a.master_agreement_id = b.latest_master_agreement_id
		AND COALESCE(a.latest_flag,'N') = 'N';


	RAISE NOTICE 'PMAG3';

	/*
	-- Populating the REFD_AMOUNT by calculating it from all the children.

	CREATE TEMPORARY TABLE tmp_rfed_loaded_master_agreements(master_agreement_id bigint,original_master_agreement_id bigint,doc_appl_last_dt date) DISTRIBUTED BY (original_master_agreement_id);

	INSERT INTO tmp_rfed_loaded_master_agreements
	SELECT  master_agreement_id, original_master_agreement_id, b.date
	FROM history_master_agreement a JOIN ref_date b ON a.source_updated_date_id = b.date_id
	JOIN etl.etl_data_load c ON coalesce(a.updated_load_id, a.created_load_id) = c.load_id
	WHERE c.job_id = p_job_id_in AND c.data_source_code IN ('C','M','F');

	CREATE TEMPORARY TABLE tmp_rfed_matching_agreements(master_agreement_id bigint,document_id varchar, document_code_id smallint, agency_id smallint, document_version integer)  DISTRIBUTED BY (master_agreement_id);

	INSERT INTO tmp_rfed_matching_agreements
	SELECT a.master_agreement_id, b.document_id, b.document_code_id, d.agency_id, max(b.document_version) as document_version
	FROM tmp_rfed_loaded_master_agreements a, history_agreement b, ref_date c, ref_agency_history d
	WHERE a.original_master_agreement_id = b.master_agreement_id AND b.source_updated_date_id = c.date_id AND b.agency_history_id = d.agency_history_id AND c.date <= a.doc_appl_last_dt
	GROUP BY 1,2,3,4;


	CREATE TEMPORARY TABLE tmp_rfed_amounts_for_master(master_agreement_id bigint,rfed_amount numeric) DISTRIBUTED BY(master_agreement_id);

	INSERT INTO tmp_rfed_amounts_for_master
	SELECT a.master_agreement_id, sum(b.rfed_amount) as rfed_amount
	FROM tmp_rfed_matching_agreements a, history_agreement b, ref_agency_history c
	WHERE a.document_id = b.document_id AND a.document_code_id = b.document_code_id AND a.document_version = b.document_version AND b.agency_history_id = c.agency_history_id AND a.agency_id = c.agency_id
	GROUP BY 1;

	UPDATE history_master_agreement a
	SET    rfed_amount = b.rfed_amount
	FROM tmp_rfed_amounts_for_master b
	WHERE a.master_agreement_id = b.master_agreement_id ;

	-- Populating the agreement_snapshot tables

	*/

-- Populating the agreement_snapshot tables for Fiscal Year (FY)

	CREATE TEMPORARY TABLE tmp_master_agreement_snapshot(original_master_agreement_id bigint,starting_year smallint,starting_year_id smallint,document_version smallint,
						     ending_year smallint, ending_year_id smallint ,rank_value smallint,master_agreement_id bigint, effective_begin_fiscal_year smallint,effective_begin_fiscal_year_id
						     smallint,effective_end_fiscal_year smallint,   effective_end_fiscal_year_id smallint, registered_fiscal_year smallint, original_version_flag char(1))
	DISTRIBUTED BY 	(original_master_agreement_id);

	-- Get the latest version for every year of modification

	INSERT INTO tmp_master_agreement_snapshot
	SELECT  b.original_master_agreement_id, b.source_updated_fiscal_year, b.source_updated_fiscal_year_id,
		max(b.document_version) as document_version,
		lead(source_updated_fiscal_year) over (partition by original_master_agreement_id ORDER BY source_updated_fiscal_year),
		lead(source_updated_fiscal_year_id) over (partition by original_master_agreement_id ORDER BY source_updated_fiscal_year),
		rank() over (partition by original_master_agreement_id order by source_updated_fiscal_year asc) as rank_value,
		NULL as master_agreement_id,
		max(effective_begin_fiscal_year) as effective_begin_fiscal_year,
		max(effective_begin_fiscal_year_id) as effective_begin_fiscal_year_id,
		max(effective_end_fiscal_year) as effective_end_fiscal_year,
		max(effective_end_fiscal_year_id) as effective_end_fiscal_year_id,
		max(registered_fiscal_year) as registered_fiscal_year,
		'N' as original_version_flag
	FROM	tmp_master_agreement_flag_changes a JOIN history_master_agreement b ON a.first_master_agreement_id = b.original_master_agreement_id
	GROUP  BY 1,2,3;


	UPDATE tmp_master_agreement_snapshot a
	SET     master_agreement_id = b.master_agreement_id,
			registered_fiscal_year = b.registered_fiscal_year
	FROM	history_master_agreement b
	WHERE   a.original_master_agreement_id = b.original_master_agreement_id
		AND a.document_version = b.document_version;

	-- Updating the POP years from the latest version of the agreement

	UPDATE tmp_master_agreement_snapshot a
	SET	effective_begin_fiscal_year = b.effective_begin_fiscal_year,
		effective_begin_fiscal_year_id = b.effective_begin_fiscal_year_id,
		effective_end_fiscal_year = b.effective_end_fiscal_year,
		effective_end_fiscal_year_id = b.effective_end_fiscal_year_id
	FROM	history_master_agreement b
	WHERE   a.original_master_agreement_id = b.original_master_agreement_id
		AND b.latest_flag = 'Y';

	UPDATE 	tmp_master_agreement_snapshot
	SET	starting_year = 2010,
		starting_year_id = year_id
	FROM	ref_year
	WHERE	year_value = 2010
		AND starting_year > 2010
		AND registered_fiscal_year <= 2010
		AND rank_value = 1;

-- Updating the starting_year to effective_begin_fiscal_year if starting_year > effective_begin_fiscal_year

		UPDATE 	tmp_master_agreement_snapshot
		SET	starting_year = effective_begin_fiscal_year,
		starting_year_id = effective_begin_fiscal_year_id
		WHERE rank_value = 1 AND starting_year > effective_begin_fiscal_year AND effective_begin_fiscal_year IS NOT NULL;

		UPDATE 	tmp_master_agreement_snapshot a
		SET	starting_year = a.registered_fiscal_year,
		starting_year_id = b.year_id
		FROM	ref_year b
		WHERE a.registered_fiscal_year = b.year_value
		AND rank_value = 1 AND starting_year > registered_fiscal_year AND registered_fiscal_year IS NOT NULL;


	UPDATE 	tmp_master_agreement_snapshot
	SET	ending_year = ending_year - 1,
		ending_year_id  = year_id
	FROM	ref_year
	WHERE	year_value = ending_year - 1
		AND ending_year is not null;

	UPDATE tmp_master_agreement_snapshot
	SET original_version_flag = 'Y'
	WHERE rank_value = 1;
	RAISE NOTICE 'PMAG5';

	INSERT INTO agreement_snapshot_deleted(agreement_id, original_agreement_id, starting_year, master_agreement_yn, load_id, deleted_date, job_id)
	SELECT a.agreement_id, a.original_agreement_id, a.starting_year, a.master_agreement_yn, l_load_id, now()::timestamp, p_job_id_in
	FROM agreement_snapshot a , tmp_master_agreement_snapshot b
	WHERE a.original_agreement_id = b.original_master_agreement_id;

	DELETE FROM ONLY agreement_snapshot a USING  tmp_master_agreement_snapshot b WHERE a.original_agreement_id = b.original_master_agreement_id;

	RAISE NOTICE 'PMAG6';

	INSERT INTO agreement_snapshot(original_agreement_id, starting_year,starting_year_id,document_version,document_code_id,agency_history_id, agency_id,agency_code,agency_name,
				       agreement_id, ending_year,ending_year_id,contract_number,
				       original_contract_amount,maximum_contract_amount,description,
					vendor_history_id,vendor_id,vendor_code,vendor_name,
					dollar_difference,
					percent_difference,
					agreement_type_id,
					agreement_type_code, agreement_type_name,award_category_id,award_category_code,award_category_name,award_method_id,award_method_code,award_method_name,expenditure_object_codes,
					expenditure_object_names,industry_type_id,
					industry_type_name,award_size_id,effective_begin_date,effective_begin_date_id,
					effective_end_date, effective_end_date_id,registered_date,
					registered_date_id,brd_awd_no,tracking_number,
					registered_year, registered_year_id,latest_flag,original_version_flag,
					effective_begin_year,effective_begin_year_id,effective_end_year,effective_end_year_id,
					 minority_type_id, minority_type_name, master_agreement_yn,
					load_id,last_modified_date, scntrc_status, job_id)
	SELECT 	a.original_master_agreement_id, a.starting_year,a.starting_year_id,a.document_version,b.document_code_id,b.agency_history_id, ah.agency_id,ag.agency_code,ah.agency_name,
	        a.master_agreement_id, (CASE WHEN a.ending_year IS NOT NULL THEN ending_year
	        			   WHEN (a.effective_end_fiscal_year IS NULL OR a.effective_end_fiscal_year < a.registered_fiscal_year)
		              AND a.registered_fiscal_year IS NOT NULL AND a.starting_year < a.registered_fiscal_year THEN a.registered_fiscal_year
	        		      WHEN a.effective_end_fiscal_year < a.starting_year THEN a.starting_year
	        		      ELSE a.effective_end_fiscal_year END),
	        		(CASE WHEN a.ending_year IS NOT NULL THEN ending_year_id
	        			  WHEN (a.effective_end_fiscal_year IS NULL OR a.effective_end_fiscal_year < a.registered_fiscal_year)
		              AND a.registered_fiscal_year IS NOT NULL AND a.starting_year < a.registered_fiscal_year THEN b.registered_fiscal_year_id
	        		      WHEN a.effective_end_fiscal_year < a.starting_year THEN a.starting_year_id
	        		      ELSE a.effective_end_fiscal_year_id END),b.contract_number,
	        b.original_contract_amount,b.maximum_spending_limit,b.description,
		b.vendor_history_id,c.vendor_id, v.vendor_customer_code, COALESCE(c.legal_name,c.alias_name),
		coalesce(b.maximum_spending_limit,0) - coalesce(b.original_contract_amount,0) as dollar_difference,
		(CASE WHEN coalesce(b.original_contract_amount,0) = 0 THEN 0 ELSE
		ROUND((( coalesce(b.maximum_spending_limit,0) - coalesce(b.original_contract_amount,0)) * 100 )::decimal / coalesce(b.original_contract_amount,0),2) END) as percent_difference,
		e.agreement_type_id,
		e.agreement_type_code, e.agreement_type_name,f.award_category_id, f.award_category_code, f.award_category_name,am.award_method_id,am.award_method_code,am.award_method_name,g.expenditure_object_codes,
		g.expenditure_object_names,
		(CASE WHEN e.agreement_type_code in ('51','70') AND f.award_category_code = '23' THEN 3
      WHEN e.agreement_type_code = '70' AND f.award_category_code in ('30','40') THEN 4
      WHEN e.agreement_type_code = '70' THEN 6
	  WHEN e.agreement_type_code in ('05','48','52') THEN 1
	  WHEN e.agreement_type_code in ('46','51','81','82') THEN 2
	  ELSE k.industry_type_id END) as industry_type_id,
	(CASE WHEN e.agreement_type_code in ('51','70') AND f.award_category_code = '23' THEN 'Professional Services'
      WHEN e.agreement_type_code = '70' AND f.award_category_code in ('30','40') THEN 'Standardized Services'
      WHEN e.agreement_type_code = '70' THEN 'Human Services'
	  WHEN e.agreement_type_code in ('05','48','52') THEN 'Construction Services'
	  WHEN e.agreement_type_code in ('46','51','81','82') THEN 'Goods'
		ELSE l.industry_type_name END) as industry_type_name,
		(CASE WHEN b.maximum_spending_limit IS NULL THEN 5 WHEN b.maximum_spending_limit <= 5000 THEN 4 WHEN b.maximum_spending_limit > 5000
		AND b.maximum_spending_limit <= 100000 THEN 3 	WHEN  b.maximum_spending_limit > 100000 AND b.maximum_spending_limit <= 1000000 THEN 2 WHEN b.maximum_spending_limit > 1000000 THEN 1
		ELSE 5 END) as award_size_id,h.date as effective_begin_date, h.date_id as effective_begin_date_id,
		i.date as effective_end_date, i.date_id as effective_end_date_id,j.date as registered_date,
		j.date_id as registered_date_id,b.board_approved_award_no,b.tracking_number,
		b.registered_fiscal_year, registered_fiscal_year_id,b.latest_flag,a.original_version_flag,
		a.effective_begin_fiscal_year,a.effective_begin_fiscal_year_id,a.effective_end_fiscal_year,a.effective_end_fiscal_year_id,
		m.minority_type_id, m.minority_type_name, 'Y' as master_agreement_yn,
		coalesce(b.updated_load_id, b.created_load_id),coalesce(b.updated_date, b.created_date), 5, p_job_id_in
	FROM	tmp_master_agreement_snapshot a JOIN history_master_agreement b ON a.master_agreement_id = b.master_agreement_id
		LEFT JOIN vendor_history c ON b.vendor_history_id = c.vendor_history_id
		LEFT JOIN vendor v ON c.vendor_id = v.vendor_id
		LEFT JOIN ref_agency_history ah ON b.agency_history_id = ah.agency_history_id
		LEFT JOIN ref_agency ag ON ah.agency_id = ag.agency_id
		LEFT JOIN ref_agreement_type e ON b.agreement_type_id = e.agreement_type_id
		LEFT JOIN ref_award_category f ON b.award_category_id_1 = f.award_category_id
		LEFT JOIN ref_award_method am ON b.award_method_id = am.award_method_id
		LEFT JOIN (SELECT z.agreement_id, GROUP_CONCAT(distinct y.expenditure_object_name) as expenditure_object_names, GROUP_CONCAT(distinct expenditure_object_code) as expenditure_object_codes
			   FROM history_agreement_accounting_line z JOIN ref_expenditure_object_history y ON z.expenditure_object_history_id = y.expenditure_object_history_id
			   JOIN ref_expenditure_object w ON y.expenditure_object_id = w.expenditure_object_id
			   JOIN tmp_master_agreement_snapshot x ON x.master_agreement_id = z.agreement_id
			   GROUP BY 1) g ON a.master_agreement_id = g.agreement_id
		LEFT JOIN ref_date h ON h.date_id = b.effective_begin_date_id
		LEFT JOIN ref_date i ON i.date_id = b.effective_end_date_id
		LEFT JOIN ref_date j ON j.date_id = b.registered_date_id
		LEFT JOIN ref_award_category_industry k ON k.award_category_code = f.award_category_code
		LEFT JOIN ref_industry_type l ON k.industry_type_id = l.industry_type_id
		LEFT JOIN vendor_min_bus_type m ON b.vendor_history_id = m.vendor_history_id;

		-- Populating the agreement_snapshot tables for Calendar Year (CY)

	CREATE TEMPORARY TABLE tmp_master_agreement_snapshot_cy(original_master_agreement_id bigint,starting_year smallint,starting_year_id smallint,document_version smallint,
						     ending_year smallint, ending_year_id smallint ,rank_value smallint,master_agreement_id bigint, effective_begin_calendar_year smallint,effective_begin_calendar_year_id
						     smallint,effective_end_calendar_year smallint,   effective_end_calendar_year_id smallint, registered_calendar_year smallint, original_version_flag char(1))
	DISTRIBUTED BY 	(original_master_agreement_id);

	INSERT INTO tmp_master_agreement_snapshot_cy
	SELECT  b.original_master_agreement_id, b.source_updated_calendar_year, b.source_updated_calendar_year_id,
		max(b.document_version) as document_version,
		lead(source_updated_calendar_year) over (partition by original_master_agreement_id ORDER BY source_updated_calendar_year),
		lead(source_updated_calendar_year_id) over (partition by original_master_agreement_id ORDER BY source_updated_calendar_year),
		rank() over (partition by original_master_agreement_id order by source_updated_calendar_year asc) as rank_value,
		NULL as master_agreement_id,
		max(effective_begin_calendar_year) as effective_begin_calendar_year,
		max(effective_begin_calendar_year_id) as effective_begin_calendar_year_id,
		max(effective_end_calendar_year) as effective_end_calendar_year,
		max(effective_end_calendar_year_id) as effective_end_calendar_year_id,
		max(registered_calendar_year) as registered_calendar_year,
		'N' as original_version_flag
	FROM	tmp_master_agreement_flag_changes a JOIN history_master_agreement b ON a.first_master_agreement_id = b.original_master_agreement_id
	GROUP  BY 1,2,3;


	UPDATE tmp_master_agreement_snapshot_cy a
	SET     master_agreement_id = b.master_agreement_id,
			registered_calendar_year = b.registered_calendar_year
	FROM	history_master_agreement b
	WHERE   a.original_master_agreement_id = b.original_master_agreement_id
		AND a.document_version = b.document_version;


		-- Updating the POP years from the latest version of the agreement

	UPDATE tmp_master_agreement_snapshot_cy a
	SET	effective_begin_calendar_year = b.effective_begin_calendar_year,
		effective_begin_calendar_year_id = b.effective_begin_calendar_year_id,
		effective_end_calendar_year = b.effective_end_calendar_year,
		effective_end_calendar_year_id = b.effective_end_calendar_year_id
	FROM	history_master_agreement b
	WHERE   a.original_master_agreement_id = b.original_master_agreement_id
		AND b.latest_flag = 'Y';


	UPDATE 	tmp_master_agreement_snapshot_cy
	SET	starting_year = 2010,
		starting_year_id = year_id
	FROM	ref_year
	WHERE	year_value = 2010
		AND starting_year > 2010
		AND registered_calendar_year <= 2010
		AND rank_value = 1;

-- Updating the starting_year to effective_begin_fiscal_year if starting_year > effective_begin_fiscal_year

		UPDATE 	tmp_master_agreement_snapshot
		SET	starting_year = effective_begin_fiscal_year,
		starting_year_id = effective_begin_fiscal_year_id
		WHERE rank_value = 1 AND starting_year > effective_begin_fiscal_year AND effective_begin_fiscal_year IS NOT NULL;

		UPDATE 	tmp_master_agreement_snapshot a
		SET	starting_year = a.registered_fiscal_year,
		starting_year_id = b.year_id
		FROM	ref_year b
		WHERE a.registered_fiscal_year = b.year_value
		AND rank_value = 1 AND starting_year > registered_fiscal_year AND registered_fiscal_year IS NOT NULL;


	UPDATE 	tmp_master_agreement_snapshot_cy
	SET	ending_year = ending_year - 1,
		ending_year_id  = year_id
	FROM	ref_year
	WHERE	year_value = ending_year - 1
		AND ending_year is not null;


	UPDATE tmp_master_agreement_snapshot_cy
	SET original_version_flag = 'Y'
	WHERE rank_value = 1;

	RAISE NOTICE 'PMAG7';

	INSERT INTO agreement_snapshot_cy_deleted(agreement_id, original_agreement_id, starting_year, master_agreement_yn, load_id, deleted_date, job_id)
	SELECT a.agreement_id, a.original_agreement_id, a.starting_year, a.master_agreement_yn, l_load_id, now()::timestamp, p_job_id_in
	FROM agreement_snapshot_cy a , tmp_master_agreement_snapshot_cy b
	WHERE a.original_agreement_id = b.original_master_agreement_id;

	DELETE FROM ONLY agreement_snapshot_cy a USING  tmp_master_agreement_snapshot_cy b WHERE a.original_agreement_id = b.original_master_agreement_id;

	RAISE NOTICE 'PMAG8';

	INSERT INTO agreement_snapshot_cy(original_agreement_id, starting_year,starting_year_id,document_version,document_code_id,agency_history_id, agency_id,agency_code,agency_name,
				       agreement_id, ending_year,ending_year_id,contract_number,
				       original_contract_amount,maximum_contract_amount,description,
					vendor_history_id,vendor_id,vendor_code,vendor_name,
					dollar_difference,
					percent_difference,
					agreement_type_id,
					agreement_type_code, agreement_type_name,award_category_id,award_category_code,award_category_name,award_method_id,award_method_code,award_method_name,expenditure_object_codes,
					expenditure_object_names,industry_type_id,
					industry_type_name,award_size_id,effective_begin_date,effective_begin_date_id,
					effective_end_date, effective_end_date_id,registered_date,
					registered_date_id,brd_awd_no,tracking_number,
					registered_year, registered_year_id,latest_flag,original_version_flag,
					effective_begin_year,effective_begin_year_id,effective_end_year,effective_end_year_id,
					minority_type_id, minority_type_name, master_agreement_yn,
					load_id,last_modified_date, job_id)
	SELECT 	a.original_master_agreement_id, a.starting_year,a.starting_year_id,a.document_version,b.document_code_id, b.agency_history_id, ah.agency_id,ag.agency_code,ah.agency_name,
	        a.master_agreement_id, (CASE WHEN a.ending_year IS NOT NULL THEN ending_year
	        			  WHEN (a.effective_end_calendar_year IS NULL OR a.effective_end_calendar_year < a.registered_calendar_year)
		              AND a.registered_calendar_year IS NOT NULL AND a.starting_year < a.registered_calendar_year THEN a.registered_calendar_year
	        		      WHEN b.effective_end_calendar_year < a.starting_year THEN a.starting_year
	        		      ELSE b.effective_end_calendar_year END),
	        		(CASE WHEN a.ending_year IS NOT NULL THEN ending_year_id
	        			  WHEN (a.effective_end_calendar_year IS NULL OR a.effective_end_calendar_year < a.registered_calendar_year)
		              AND a.registered_calendar_year IS NOT NULL AND a.starting_year < a.registered_calendar_year THEN b.registered_calendar_year_id
	        		      WHEN b.effective_end_calendar_year < a.starting_year THEN a.starting_year_id
	        		      ELSE b.effective_end_calendar_year_id END),b.contract_number,
	        b.original_contract_amount,b.maximum_spending_limit,b.description,
		b.vendor_history_id,c.vendor_id, v.vendor_customer_code, COALESCE(c.legal_name,c.alias_name),
		coalesce(b.maximum_spending_limit,0) - coalesce(b.original_contract_amount,0) as dollar_difference,
		(CASE WHEN coalesce(b.original_contract_amount,0) = 0 THEN 0 ELSE
		ROUND((( coalesce(b.maximum_spending_limit,0) - coalesce(b.original_contract_amount,0)) * 100 )::decimal / coalesce(b.original_contract_amount,0),2) END) as percent_difference,
		e.agreement_type_id,
		e.agreement_type_code, e.agreement_type_name,f.award_category_id, f.award_category_code, f.award_category_name,am.award_method_id,am.award_method_code,am.award_method_name,g.expenditure_object_codes,
		g.expenditure_object_names,
		(CASE WHEN e.agreement_type_code in ('51','70') AND f.award_category_code = '23' THEN 3
      WHEN e.agreement_type_code = '70' AND f.award_category_code in ('30','40') THEN 4
      WHEN e.agreement_type_code = '70' THEN 6
	  WHEN e.agreement_type_code in ('05','48','52') THEN 1
	  WHEN e.agreement_type_code in ('46','51','81','82') THEN 2
	  ELSE k.industry_type_id END) as industry_type_id,
	(CASE WHEN e.agreement_type_code in ('51','70') AND f.award_category_code = '23' THEN 'Professional Services'
      WHEN e.agreement_type_code = '70' AND f.award_category_code in ('30','40') THEN 'Standardized Services'
      WHEN e.agreement_type_code = '70' THEN 'Human Services'
	  WHEN e.agreement_type_code in ('05','48','52') THEN 'Construction Services'
	  WHEN e.agreement_type_code in ('46','51','81','82') THEN 'Goods'
		ELSE l.industry_type_name END) as industry_type_name,
		(CASE WHEN b.maximum_spending_limit IS NULL THEN 5 WHEN b.maximum_spending_limit <= 5000 THEN 4 WHEN b.maximum_spending_limit > 5000
		AND b.maximum_spending_limit <= 100000 THEN 3 	WHEN  b.maximum_spending_limit > 100000 AND b.maximum_spending_limit <= 1000000 THEN 2 WHEN b.maximum_spending_limit > 1000000 THEN 1
		ELSE 5 END) as award_size_id,h.date as effective_begin_date, h.date_id as effective_begin_date_id,
		i.date as effective_end_date, i.date_id as effective_end_date_id,j.date as registered_date,
		j.date_id as registered_date_id,b.board_approved_award_no,b.tracking_number,
		b.registered_calendar_year, registered_calendar_year_id,b.latest_flag,a.original_version_flag,
		a.effective_begin_calendar_year,a.effective_begin_calendar_year_id,a.effective_end_calendar_year,a.effective_end_calendar_year_id,
		m.minority_type_id, m.minority_type_name, 'Y' as master_agreement_yn,
		coalesce(b.updated_load_id, b.created_load_id),coalesce(b.updated_date, b.created_date), p_job_id_in
	FROM	tmp_master_agreement_snapshot_cy a JOIN history_master_agreement b ON a.master_agreement_id = b.master_agreement_id
		LEFT JOIN vendor_history c ON b.vendor_history_id = c.vendor_history_id
		LEFT JOIN vendor v ON c.vendor_id = v.vendor_id
		LEFT JOIN ref_agency_history ah ON b.agency_history_id = ah.agency_history_id
		LEFT JOIN ref_agency ag ON ah.agency_id = ag.agency_id
		LEFT JOIN ref_agreement_type e ON b.agreement_type_id = e.agreement_type_id
		LEFT JOIN ref_award_category f ON b.award_category_id_1 = f.award_category_id
		LEFT JOIN ref_award_method am ON b.award_method_id = am.award_method_id
		LEFT JOIN (SELECT z.agreement_id, GROUP_CONCAT(distinct y.expenditure_object_name) as expenditure_object_names, GROUP_CONCAT(distinct expenditure_object_code) as expenditure_object_codes
			   FROM history_agreement_accounting_line z JOIN ref_expenditure_object_history y ON z.expenditure_object_history_id = y.expenditure_object_history_id
			   JOIN ref_expenditure_object w ON y.expenditure_object_id = w.expenditure_object_id
			   JOIN tmp_master_agreement_snapshot x ON x.master_agreement_id = z.agreement_id
			   GROUP BY 1) g ON a.master_agreement_id = g.agreement_id
		LEFT JOIN ref_date h ON h.date_id = b.effective_begin_date_id
		LEFT JOIN ref_date i ON i.date_id = b.effective_end_date_id
		LEFT JOIN ref_date j ON j.date_id = b.registered_date_id
		LEFT JOIN ref_award_category_industry k ON k.award_category_code = f.award_category_code
		LEFT JOIN ref_industry_type l ON k.industry_type_id = l.industry_type_id
		LEFT JOIN vendor_min_bus_type m ON b.vendor_history_id = m.vendor_history_id;

	-- Associate contracts/agreements to the original version of the master agreement

	RAISE NOTICE 'PMAG9';

	CREATE TEMPORARY TABLE tmp_contracts_for_mag(agreement_id bigint, master_agreement_id bigint)
	DISTRIBUTED BY (agreement_id);

	TRUNCATE TABLE tmp_master_agreement_unnest;

	INSERT INTO tmp_master_agreement_unnest
	SELECT unnest(string_to_array(non_first_master_agreement_id,','))::int as master_agreement_id ,
		first_master_agreement_id
	FROM	tmp_master_agreement_flag_changes ;


	INSERT INTO tmp_contracts_for_mag
	SELECT a.agreement_id, b.first_master_agreement_id
	FROM history_agreement a JOIN  tmp_master_agreement_unnest b ON a.master_agreement_id = b.master_agreement_id;



	UPDATE history_agreement a
	SET	master_agreement_id = b.master_agreement_id
	FROM	tmp_contracts_for_mag b
	WHERE	a.agreement_id = b.agreement_id;



	RAISE NOTICE 'PMAG10';

	-- Updating to the original version of the master agreement in disbursement line item details

	CREATE TEMPORARY TABLE tmp_contracts_for_disbs(disbursement_line_item_id bigint, master_agreement_id bigint)
	DISTRIBUTED BY (disbursement_line_item_id);

	INSERT INTO tmp_contracts_for_disbs
	SELECT a.disbursement_line_item_id, b.first_master_agreement_id
	FROM disbursement_line_item_details a JOIN  tmp_master_agreement_unnest b ON a.master_agreement_id = b.master_agreement_id;



	UPDATE disbursement_line_item_details a
	SET	master_agreement_id = b.master_agreement_id
	FROM	tmp_contracts_for_disbs b
	WHERE	a.disbursement_line_item_id = b.disbursement_line_item_id;

	-- updating maximum_spending_limit in disbursement_line_item_details

	UPDATE disbursement_line_item_details a
	SET	maximum_spending_limit = c.maximum_contract_amount,
		master_contract_industry_type_id = c.industry_type_id,
		master_contract_minority_type_id = c.minority_type_id,
		master_purpose = c.description
	FROM	tmp_contracts_for_disbs b, agreement_snapshot c
	WHERE	a.disbursement_line_item_id = b.disbursement_line_item_id
		AND a.master_agreement_id = c.original_agreement_id AND master_agreement_yn = 'Y' AND a.fiscal_year between c.starting_year AND c.ending_year;

	-- updating maximum_spending_limit_cy in disbursement_line_item_details

	UPDATE disbursement_line_item_details a
	SET	maximum_spending_limit_cy = c.maximum_contract_amount,
		master_contract_industry_type_id_cy = c.industry_type_id,
		master_contract_minority_type_id_cy = c.minority_type_id,
		master_purpose_cy = c.description
	FROM	tmp_contracts_for_disbs b, agreement_snapshot_cy c
	WHERE	a.disbursement_line_item_id = b.disbursement_line_item_id
		AND a.master_agreement_id = c.original_agreement_id AND master_agreement_yn = 'Y' AND a.calendar_fiscal_year between c.starting_year AND c.ending_year;



	-- update has_children column

			/*
	CREATE TEMPORARY TABLE tmp_master_has_children_cy (original_master_agreement_id bigint, total_children smallint)
	DISTRIBUTED BY (original_master_agreement_id);

	INSERT INTO tmp_master_has_children_cy
	SELECT b.original_master_agreement_id, count(distinct agreement_id) as total_children
	FROM history_agreement a JOIN tmp_master_agreement_snapshot_cy b
	ON a.master_agreement_id = b.original_master_agreement_id
	GROUP BY 1;

	UPDATE 	tmp_master_agreement_snapshot_cy a
	SET has_children = (CASE WHEN b.total_children > 0 THEN 'Y' ELSE 'N' END)
	FROM tmp_master_has_children_cy b
	WHERE a.original_master_agreement_id = b.original_master_agreement_id;

	*/

	CREATE TEMPORARY TABLE tmp_master_has_children (original_master_agreement_id bigint, total_children int)
	DISTRIBUTED BY (original_master_agreement_id);

	INSERT INTO tmp_master_has_children
	SELECT distinct original_master_agreement_id, 0 as total_children
	FROM history_master_agreement ;

	CREATE TEMPORARY TABLE tmp_master_has_children_1 (original_master_agreement_id bigint, total_children int)
	DISTRIBUTED BY (original_master_agreement_id);

	INSERT INTO tmp_master_has_children_1
	SELECT b.original_master_agreement_id, count(distinct agreement_id) as total_children
	FROM history_agreement a JOIN tmp_master_has_children b
	ON a.master_agreement_id = b.original_master_agreement_id
	GROUP BY 1;

	UPDATE tmp_master_has_children a
	SET total_children =  b.total_children
	FROM tmp_master_has_children_1 b
	WHERE a.original_master_agreement_id = b.original_master_agreement_id ;


	UPDATE 	agreement_snapshot a
	SET has_children = (CASE WHEN b.total_children > 0 THEN 'Y' ELSE 'N' END)
	FROM tmp_master_has_children b
	WHERE a.master_agreement_yn = 'Y' AND a.original_agreement_id = b.original_master_agreement_id;

	UPDATE 	agreement_snapshot_cy a
	SET has_children = (CASE WHEN b.total_children > 0 THEN 'Y' ELSE 'N' END)
	FROM tmp_master_has_children b
	WHERE a.master_agreement_yn = 'Y' AND a.original_agreement_id = b.original_master_agreement_id;


	l_end_time := timeofday()::timestamp;

	INSERT INTO etl.etl_script_execution_status(job_id,script_name,completed_flag,start_time,end_time)
	VALUES(p_job_id_in,'etl.postProcessMAG',1,l_start_time,l_end_time);

	RETURN 1;

	/* End of one time changes */

EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in postProcessMAG';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;

	l_end_time := timeofday()::timestamp;

	INSERT INTO etl.etl_script_execution_status(job_id,script_name,completed_flag,start_time,end_time,errno,errmsg)
	VALUES(p_job_id_in,'etl.postProcessMAG',0,l_start_time,l_end_time,SQLSTATE,SQLERRM);

	RETURN 0;

END;
$$ language plpgsql;
