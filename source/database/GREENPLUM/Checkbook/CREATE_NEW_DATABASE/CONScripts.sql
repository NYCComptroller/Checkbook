/*
Functions defined

	updateForeignKeysForCTInHeader
	updateForeignKeysForCTInAwardDetail
	associateMAGToCT
	updateForeignKeysForCTInAccLine
	processCONGeneralContracts
	processCon
	updateCONFlags
	postProcessContracts
	refreshContractsPreAggregateTables

*/


CREATE OR REPLACE FUNCTION etl.updateForeignKeysForCTInHeader(p_load_file_id_in bigint,p_load_id_in bigint) RETURNS INT AS $$
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
					      source_updated_fiscal_year_id smallint)
	DISTRIBUTED BY (uniq_id);

	-- FK:Document_Code_id

	INSERT INTO tmp_fk_values(uniq_id,document_code_id)
	SELECT	a.uniq_id, b.document_code_id
	FROM etl.stg_con_ct_header a JOIN ref_document_code b ON a.doc_cd = b.document_code;

	-- FK:Agency_history_id

	INSERT INTO tmp_fk_values(uniq_id,agency_history_id)
	SELECT	a.uniq_id, max(c.agency_history_id) as agency_history_id
	FROM etl.stg_con_ct_header a JOIN ref_agency b ON a.doc_dept_cd = b.agency_code
		JOIN ref_agency_history c ON b.agency_id = c.agency_id
	GROUP BY 1;

	CREATE TEMPORARY TABLE tmp_fk_values_new_agencies(dept_cd varchar,uniq_id bigint)
	DISTRIBUTED BY (uniq_id);

	INSERT INTO tmp_fk_values_new_agencies
	SELECT doc_dept_cd,MIN(b.uniq_id) as uniq_id
	FROM etl.stg_con_ct_header a join (SELECT uniq_id
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
		VALUES(p_load_file_id_in,'C',l_count, 'Number of records inserted into ref_agency  from general contracts header');
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
		VALUES(p_load_file_id_in,'C',l_count, 'Number of records inserted into ref_agency_history from general contracts header');
	END IF;

	RAISE NOTICE '1.3';
	INSERT INTO tmp_fk_values(uniq_id,agency_history_id)
	SELECT	a.uniq_id, max(c.agency_history_id)
	FROM etl.stg_con_ct_header a JOIN ref_agency b ON a.doc_dept_cd = b.agency_code
		JOIN ref_agency_history c ON b.agency_id = c.agency_id
		JOIN etl.ref_agency_history_id_seq d ON c.agency_history_id = d.agency_history_id
	GROUP BY 1	;

	-- FK:record_date_id

	INSERT INTO tmp_fk_values(uniq_id,record_date_id)
	SELECT	a.uniq_id, b.date_id
	FROM etl.stg_con_ct_header a JOIN ref_date b ON a.doc_rec_dt_dc = b.date;

	--FK:effective_begin_date_id

	INSERT INTO tmp_fk_values(uniq_id,effective_begin_date_id,effective_begin_fiscal_year,effective_begin_fiscal_year_id, effective_begin_calendar_year,effective_begin_calendar_year_id)
	SELECT	a.uniq_id, b.date_id,c.year_value,b.nyc_year_id,e.year_value,d.year_id
	FROM etl.stg_con_ct_header a JOIN ref_date b ON a.cntrct_strt_dt = b.date
		JOIN ref_year c ON b.nyc_year_id = c.year_id
		JOIN ref_month d ON b.calendar_month_id = d.month_id
		JOIN ref_year e ON d.year_id = e.year_id;

	--FK:effective_end_date_id

	INSERT INTO tmp_fk_values(uniq_id,effective_end_date_id,effective_end_fiscal_year,effective_end_fiscal_year_id, effective_end_calendar_year,effective_end_calendar_year_id)
	SELECT	a.uniq_id, b.date_id,c.year_value,b.nyc_year_id,e.year_value,d.year_id
	FROM etl.stg_con_ct_header a JOIN ref_date b ON a.cntrct_end_dt = b.date
		JOIN ref_year c ON b.nyc_year_id = c.year_id
		JOIN ref_month d ON b.calendar_month_id = d.month_id
		JOIN ref_year e ON d.year_id = e.year_id;

	--FK:source_created_date_id

	INSERT INTO tmp_fk_values(uniq_id,source_created_date_id)
	SELECT	a.uniq_id, b.date_id
	FROM etl.stg_con_ct_header a JOIN ref_date b ON a.doc_appl_crea_dt = b.date;

	--FK:source_updated_date_id

	INSERT INTO tmp_fk_values(uniq_id,source_updated_date_id,source_updated_fiscal_year,source_updated_fiscal_year_id, source_updated_calendar_year,source_updated_calendar_year_id)
	SELECT	a.uniq_id, b.date_id,c.year_value,b.nyc_year_id,e.year_value,d.year_id
	FROM etl.stg_con_ct_header a JOIN ref_date b ON a.doc_appl_last_dt = b.date
		JOIN ref_year c ON b.nyc_year_id = c.year_id
		JOIN ref_month d ON b.calendar_month_id = d.month_id
		JOIN ref_year e ON d.year_id = e.year_id;

	--FK:registered_date_id

	INSERT INTO tmp_fk_values(uniq_id,registered_date_id, registered_fiscal_year,registered_fiscal_year_id, registered_calendar_year,registered_calendar_year_id)
	SELECT	a.uniq_id, b.date_id,c.year_value,b.nyc_year_id,e.year_value,d.year_id
	FROM etl.stg_con_ct_header a JOIN ref_date b ON a.reg_dt = b.date
		JOIN ref_year c ON b.nyc_year_id = c.year_id
		JOIN ref_month d ON b.calendar_month_id = d.month_id
		JOIN ref_year e ON d.year_id = e.year_id;

	--FK:original_term_begin_date_id

	INSERT INTO tmp_fk_values(uniq_id,original_term_begin_date_id)
	SELECT	a.uniq_id, b.date_id
	FROM etl.stg_con_ct_header a JOIN ref_date b ON a.orig_cntrc_strt_dt = b.date;

	--FK:original_term_end_date_id

	INSERT INTO tmp_fk_values(uniq_id,original_term_end_date_id)
	SELECT	a.uniq_id, b.date_id
	FROM etl.stg_con_ct_header a JOIN ref_date b ON a.orig_cntrc_end_dt = b.date;

	--Updating con_ct_header with all the FK values

	UPDATE etl.stg_con_ct_header a
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
		source_updated_calendar_year_id = ct_table.source_updated_calendar_year_id
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
				 max(source_updated_calendar_year_id) as source_updated_calendar_year_id
		 FROM	tmp_fk_values
		 GROUP BY 1) ct_table
	WHERE	a.uniq_id = ct_table.uniq_id;

	RETURN 1;
EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in updateForeignKeysForCTInHeader';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;

	RETURN 0;
END;
$$ language plpgsql;

-------------------------------------------------------------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION etl.updateForeignKeysForCTInAwardDetail() RETURNS INT AS $$
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
	FROM	etl.stg_con_ct_award_detail a JOIN ref_award_method b ON a.awd_meth_cd = b.award_method_code;

	--FK:agreement_type_id

	INSERT INTO tmp_fk_values_award_detail(uniq_id,agreement_type_id)
	SELECT a.uniq_id , b.agreement_type_id
	FROM	etl.stg_con_ct_award_detail a JOIN ref_agreement_type b ON a.cttyp_cd = b.agreement_type_code;

	--FK:award_category_id_1

	INSERT INTO tmp_fk_values_award_detail(uniq_id,award_category_id_1)
	SELECT a.uniq_id , b.award_category_id
	FROM	etl.stg_con_ct_award_detail a JOIN ref_award_category b ON a.ctcat_cd_1 = b.award_category_code;

	--FK:award_category_id_2

	INSERT INTO tmp_fk_values_award_detail(uniq_id,award_category_id_2)
	SELECT a.uniq_id , b.award_category_id
	FROM	etl.stg_con_ct_award_detail a JOIN ref_award_category b ON a.ctcat_cd_2 = b.award_category_code;

	--FK:award_category_id_3

	INSERT INTO tmp_fk_values_award_detail(uniq_id,award_category_id_3)
	SELECT a.uniq_id , b.award_category_id
	FROM	etl.stg_con_ct_award_detail a JOIN ref_award_category b ON a.ctcat_cd_3 = b.award_category_code;

	--FK:award_category_id_4

	INSERT INTO tmp_fk_values_award_detail(uniq_id,award_category_id_4)
	SELECT a.uniq_id , b.award_category_id
	FROM	etl.stg_con_ct_award_detail a JOIN ref_award_category b ON a.ctcat_cd_4 = b.award_category_code;

	--FK:award_category_id_5

	INSERT INTO tmp_fk_values_award_detail(uniq_id,award_category_id_5)
	SELECT a.uniq_id , b.award_category_id
	FROM	etl.stg_con_ct_award_detail a JOIN ref_award_category b ON a.ctcat_cd_5 = b.award_category_code;


	UPDATE etl.stg_con_ct_award_detail a
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
	RAISE NOTICE 'Exception Occurred in updateForeignKeysForCTInAwardDetail';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;

	RETURN 0;
END;
$$ language plpgsql;

------------------------------------------------------------------------------------------------------------------------------------------------------------
CREATE OR REPLACE FUNCTION etl.associateMAGToCT(p_load_file_id_in bigint,p_load_id_in bigint) RETURNS INT AS $$
DECLARE
	l_worksite_col_array VARCHAR ARRAY[10];
	l_array_ctr smallint;
	l_fk_update int;
	l_count bigint;
BEGIN

	-- Fetch all the contracts associated with MAG

	CREATE TEMPORARY TABLE tmp_ct_mag(uniq_id bigint, master_agreement_id bigint,mag_document_id varchar,
				mag_agency_history_id smallint, mag_document_code_id smallint, mag_document_code varchar, mag_agency_code varchar )
	DISTRIBUTED BY(uniq_id);

	INSERT INTO tmp_ct_mag
	SELECT uniq_id, 0 as master_agreement_id,
	       max(agree_doc_id),
	       max(d.agency_history_id) as mag_agency_history_id,
	       max(c.document_code_id),
	       max(c.document_code),
	       max(b.agency_code)
	FROM	etl.stg_con_ct_header a JOIN ref_agency b ON a.agree_doc_dept_cd = b.agency_code
		JOIN ref_document_code c ON a.agree_doc_cd = c.document_code
		JOIN ref_agency_history d ON b.agency_id = d.agency_id
	GROUP BY 1,2;


	-- Identify the MAG Id

	CREATE TEMPORARY TABLE tmp_old_ct_mag_con(uniq_id bigint,master_agreement_id bigint, action_flag char(1), latest_flag char(1))
	DISTRIBUTED BY (uniq_id);

	INSERT INTO tmp_old_ct_mag_con
	SELECT uniq_id,
	       b.master_agreement_id
	FROM tmp_ct_mag a JOIN history_master_agreement b ON a.mag_document_id = b.document_id
		JOIN ref_document_code f ON a.mag_document_code = f.document_code AND b.document_code_id = f.document_code_id
		JOIN ref_agency e ON a.mag_agency_code = e.agency_code
		JOIN ref_agency_history c ON b.agency_history_id = c.agency_history_id AND e.agency_id = c.agency_id
	WHERE b.original_version_flag='Y';

	UPDATE tmp_ct_mag a
	SET	master_agreement_id = b.master_agreement_id
	FROM	tmp_old_ct_mag_con b
	WHERE	a.uniq_id = b.uniq_id;

	-- Identify the MAG ones which have to be created

	CREATE TEMPORARY TABLE tmp_new_ct_mag_con(mag_document_code varchar,mag_agency_code varchar, mag_document_id varchar,
					   mag_agency_history_id smallint,mag_document_code_id smallint,uniq_id bigint);

	INSERT INTO tmp_new_ct_mag_con
	SELECT 	mag_document_code,mag_agency_code, mag_document_id,mag_agency_history_id,mag_document_code_id,min(uniq_id)
	FROM	tmp_ct_mag
	WHERE	master_agreement_id =0
	GROUP BY 1,2,3,4,5;

	TRUNCATE etl.agreement_id_seq;

	INSERT INTO etl.agreement_id_seq(uniq_id)
	SELECT uniq_id
	FROM  tmp_new_ct_mag_con;

	INSERT INTO history_master_agreement(master_agreement_id,document_code_id,agency_history_id,document_id,document_version,privacy_flag,created_load_id,created_date,contract_number)
	SELECT  b.agreement_id, a.mag_document_code_id,a.mag_agency_history_id,a.mag_document_id,1 as document_version,'F' as privacy_flag,p_load_id_in,now()::timestamp,mag_document_code||mag_agency_code||mag_document_id as contract_number
	FROM	tmp_new_ct_mag_con a JOIN etl.agreement_id_seq b ON a.uniq_id = b.uniq_id;

	GET DIAGNOSTICS l_count = ROW_COUNT;

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'C',l_count, '# of records inserted into history_master_agreement inserted from general contracts');
	END IF;

	-- Updating the newly created MAG identifier.

	CREATE TEMPORARY TABLE tmp_new_ct_mag_con_2(uniq_id bigint,master_agreement_id bigint)
	DISTRIBUTED BY (uniq_id);

	INSERT INTO tmp_new_ct_mag_con_2
	SELECT a.uniq_id,d.agreement_id
	FROM   tmp_ct_mag a JOIN tmp_new_ct_mag_con b ON b.mag_document_code = a.mag_document_code
				     AND b.mag_agency_code = a.mag_agency_code
				     AND b.mag_document_id = a.mag_document_id
		JOIN etl.agreement_id_seq d ON b.uniq_id = d.uniq_id;

	UPDATE tmp_ct_mag a
	SET	master_agreement_id = b.master_agreement_id
	FROM	tmp_new_ct_mag_con_2 b
	WHERE	a.uniq_id = b.uniq_id
		AND a.master_agreement_id =0;

	 UPDATE etl.stg_con_ct_header a
	 SET	master_agreement_id = b.master_agreement_id
	 FROM	tmp_ct_mag b
	 WHERE	a.uniq_id = b.uniq_id;

	RETURN 1;
EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in associateMAGToCT';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;

	RETURN 0;
END;
$$ language plpgsql;

------------------------------------------------------------------------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION etl.updateForeignKeysForCTInAccLine(p_load_file_id_in bigint,p_load_id_in bigint) RETURNS INT AS $$
DECLARE
	l_count bigint;
BEGIN
	-- UPDATING FK VALUES IN ETL.STG_CON_CT_ACCOUNTING_LINE

	CREATE TEMPORARY TABLE tmp_fk_values_acc_line(uniq_id bigint,fund_class_id smallint,agency_history_id smallint,
							department_history_id int, expenditure_object_history_id integer,budget_code_id integer)
	DISTRIBUTED BY (uniq_id);

	INSERT INTO tmp_fk_values_acc_line(uniq_id)
	SELECT DISTINCT  uniq_id
	FROM etl.stg_con_ct_accounting_line;

	UPDATE etl.stg_con_ct_accounting_line
	SET fund_cd = NULL
	WHERE fund_cd = '';

	UPDATE etl.stg_con_ct_accounting_line
	SET dept_cd = NULL
	WHERE dept_cd = '';

	UPDATE etl.stg_con_ct_accounting_line
	SET appr_cd = NULL
	WHERE appr_cd = '';


	UPDATE etl.stg_con_ct_accounting_line
	SET obj_cd = NULL
	WHERE obj_cd = '';


	INSERT INTO tmp_fk_values_acc_line(uniq_id)
	SELECT DISTINCT  uniq_id
	FROM etl.stg_con_ct_accounting_line;


	-- FK:fund_class_id


	INSERT INTO tmp_fk_values_acc_line(uniq_id,fund_class_id)
	SELECT	a.uniq_id, b.fund_class_id
	FROM etl.stg_con_ct_accounting_line a JOIN ref_fund_class b ON COALESCE(a.fund_cd,'---') = b.fund_class_code;

	CREATE TEMPORARY TABLE tmp_fk_values_acc_line_new_fund_class(fund_class_code varchar,uniq_id integer)
	DISTRIBUTED BY (uniq_id);

	INSERT INTO tmp_fk_values_acc_line_new_fund_class
	SELECT COALESCE(a.fund_cd,'---'),MIN(b.uniq_id) as uniq_id
	FROM etl.stg_con_ct_accounting_line a join (SELECT uniq_id
				    FROM tmp_fk_values_acc_line
				    GROUP BY 1
				    HAVING max(fund_class_id) is null) b on a.uniq_id=b.uniq_id
	GROUP BY 1;

	TRUNCATE etl.ref_fund_class_id_seq;

	INSERT INTO etl.ref_fund_class_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_fk_values_acc_line_new_fund_class;

	INSERT INTO ref_fund_class(fund_class_id,fund_class_code,fund_class_name,created_date,created_load_id)
	SELECT a.fund_class_id,COALESCE(b.fund_class_code,'---'),(CASE WHEN COALESCE(b.fund_class_code,'---') <> '---'  THEN '<Unknown Fund Class>'
							ELSE '<Non-Applicable Fund Class>' END) as fund_class_name,
				now()::timestamp,p_load_id_in
	FROM   etl.ref_fund_class_id_seq a JOIN tmp_fk_values_acc_line_new_fund_class b ON a.uniq_id = b.uniq_id;

	GET DIAGNOSTICS l_count = ROW_COUNT;

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'C',l_count, 'Number of records inserted into ref_fund_class from general contracts accounting lines');
	END IF;

	INSERT INTO tmp_fk_values_acc_line(uniq_id,fund_class_id)
	SELECT	a.uniq_id, b.fund_class_id
	FROM etl.stg_con_ct_accounting_line a JOIN ref_fund_class b ON COALESCE(a.fund_cd,'---') = b.fund_class_code
		JOIN etl.ref_fund_class_id_seq c ON c.fund_class_id = b.fund_class_id ;

	-- FK:agency_history_id

	INSERT INTO tmp_fk_values_acc_line(uniq_id,agency_history_id)
	SELECT	a.uniq_id, max(c.agency_history_id)
	FROM etl.stg_con_ct_accounting_line a JOIN ref_agency b ON COALESCE(a.dept_cd,'---') = b.agency_code
		JOIN ref_agency_history c ON b.agency_id = c.agency_id
	GROUP BY 1	;

	CREATE TEMPORARY TABLE tmp_fk_values_acc_line_new_agencies(dept_cd varchar,uniq_id bigint)
	DISTRIBUTED BY (uniq_id);

	INSERT INTO tmp_fk_values_acc_line_new_agencies
	SELECT COALESCE(dept_cd,'---') as dept_cd ,MIN(b.uniq_id) as uniq_id
	FROM etl.stg_con_ct_accounting_line a join (SELECT uniq_id
						 FROM tmp_fk_values_acc_line
						 GROUP BY 1
						 HAVING max(agency_history_id) is null) b on a.uniq_id=b.uniq_id
	GROUP BY 1;

	TRUNCATE etl.ref_agency_id_seq;

	RAISE NOTICE '1.1';

	INSERT INTO etl.ref_agency_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_fk_values_acc_line_new_agencies;

	INSERT INTO ref_agency(agency_id,agency_code,agency_name,created_date,created_load_id,original_agency_name)
	SELECT a.agency_id,COALESCE(b.dept_cd,'---'),(CASE WHEN COALESCE(b.dept_cd,'---')='---' THEN '<Non-Applicable Agency>' ELSE '<Unknown Agency>' END)as agency_name,
		now()::timestamp,p_load_id_in,'<Unknown Agency>' as original_agency_name
	FROM   etl.ref_agency_id_seq a JOIN tmp_fk_values_acc_line_new_agencies b ON a.uniq_id = b.uniq_id;

	GET DIAGNOSTICS l_count = ROW_COUNT;

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'C',l_count, 'Number of records inserted into ref_agency from general contracts accounting lines');
	END IF;

	RAISE NOTICE '1.2';

	-- Generate the agency history id for history records

	TRUNCATE etl.ref_agency_history_id_seq;

	INSERT INTO etl.ref_agency_history_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_fk_values_acc_line_new_agencies;

	INSERT INTO ref_agency_history(agency_history_id,agency_id,agency_name,created_date,load_id)
	SELECT a.agency_history_id,b.agency_id,(CASE WHEN COALESCE(c.dept_cd,'---')='---' THEN '<Non-Applicable Agency>' ELSE '<Unknown Agency>' END),now()::timestamp,p_load_id_in
	FROM   etl.ref_agency_history_id_seq a JOIN etl.ref_agency_id_seq b ON a.uniq_id = b.uniq_id
		 JOIN tmp_fk_values_acc_line_new_agencies c ON a.uniq_id = c.uniq_id;

	RAISE NOTICE '1.3';

	GET DIAGNOSTICS l_count = ROW_COUNT;

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'C',l_count, 'Number of records inserted into ref_agency_history from general contracts accounting lines');
	END IF;

	INSERT INTO tmp_fk_values_acc_line(uniq_id,agency_history_id)
	SELECT	a.uniq_id, max(c.agency_history_id)
	FROM etl.stg_con_ct_accounting_line a JOIN ref_agency b ON COALESCE(a.dept_cd,'---') = b.agency_code
		JOIN ref_agency_history c ON b.agency_id = c.agency_id
		JOIN etl.ref_agency_history_id_seq d ON c.agency_history_id = d.agency_history_id
	GROUP BY 1	;

	RAISE NOTICE '1.4';
	-- FK:department_history_id

	INSERT INTO tmp_fk_values_acc_line(uniq_id,department_history_id)
	SELECT	a.uniq_id, max(c.department_history_id)
	FROM etl.stg_con_ct_accounting_line a JOIN ref_department b ON COALESCE(a.appr_cd,'---------') = b.department_code AND a.fy_dc = b.fiscal_year
		JOIN ref_department_history c ON b.department_id = c.department_id
		JOIN ref_agency d ON a.dept_cd = d.agency_code AND b.agency_id = d.agency_id
		JOIN ref_fund_class e ON a.fund_cd = e.fund_class_code AND e.fund_class_id = b.fund_class_id
	GROUP BY 1	;

	-- Generate the department id for new records

	CREATE TEMPORARY TABLE tmp_fk_values_acc_line_new_dept(agency_id integer,appr_cd varchar,
						fund_class_id smallint,fiscal_year smallint, uniq_id bigint)
	DISTRIBUTED BY (uniq_id);

	INSERT INTO tmp_fk_values_acc_line_new_dept
	SELECT c.agency_id,COALESCE(appr_cd,'---------'),e.fund_class_id,fy_dc,MIN(b.uniq_id) as uniq_id
	FROM etl.stg_con_ct_accounting_line a join (SELECT uniq_id
						 FROM tmp_fk_values_acc_line
						 GROUP BY 1
						 HAVING max(department_history_id) IS NULL) b on a.uniq_id=b.uniq_id
		JOIN ref_agency c ON COALESCE(a.dept_cd,'---') = c.agency_code
		JOIN ref_fund_class e ON COALESCE(a.fund_cd,'---') = e.fund_class_code
	GROUP BY 1,2,3,4;

	RAISE NOTICE '1.4';


	TRUNCATE etl.ref_department_id_seq;

	INSERT INTO etl.ref_department_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_fk_values_acc_line_new_dept;

	INSERT INTO ref_department(department_id,department_code,
				   department_name,
				   agency_id,fund_class_id,
				   fiscal_year,created_date,created_load_id,original_department_name)
	SELECT a.department_id,COALESCE(b.appr_cd,'---------') as department_code,
		(CASE WHEN COALESCE(b.appr_cd,'---------') <> '---------' THEN '<Unknown Department>'
			ELSE 'Non-Applicable Department' END) as department_name,
		b.agency_id,b.fund_class_id,b.fiscal_year,
		now()::timestamp,p_load_id_in,
		(CASE WHEN COALESCE(b.appr_cd,'---------') <> '---------' THEN '<Unknown Department>'
			ELSE 'Non-Applicable Department' END) as original_department_name
	FROM   etl.ref_department_id_seq a JOIN tmp_fk_values_acc_line_new_dept b ON a.uniq_id = b.uniq_id;

	GET DIAGNOSTICS l_count = ROW_COUNT;

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'C',l_count, 'Number of records inserted into ref_department from general contracts accounting lines');
	END IF;

	RAISE NOTICE '1.5';
	-- Generate the department history id for history records

	TRUNCATE etl.ref_department_history_id_seq;

	INSERT INTO etl.ref_department_history_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_fk_values_acc_line_new_dept;

	INSERT INTO ref_department_history(department_history_id,department_id,
					   department_name,agency_id,fund_class_id,
					   fiscal_year,created_date,load_id)
	SELECT a.department_history_id,c.department_id,
		(CASE WHEN COALESCE(b.appr_cd,'---------') <> '---------' THEN '<Unknown Department>'
		      ELSE 'Non-Applicable Department' END) as department_name,
		b.agency_id,b.fund_class_id,b.fiscal_year,now()::timestamp,p_load_id_in
	FROM   etl.ref_department_history_id_seq a JOIN tmp_fk_values_acc_line_new_dept b ON a.uniq_id = b.uniq_id
		JOIN etl.ref_department_id_seq  c ON a.uniq_id = c.uniq_id ;

	GET DIAGNOSTICS l_count = ROW_COUNT;

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'C',l_count, 'Number of records inserted into department_history_id from general contracts accounting lines');
	END IF;

	RAISE NOTICE '1.6';

	INSERT INTO tmp_fk_values_acc_line(uniq_id,department_history_id)
	SELECT	a.uniq_id, max(c.department_history_id)
	FROM etl.stg_con_ct_accounting_line a JOIN ref_department b  ON COALESCE(a.appr_cd,'---------') = b.department_code AND a.fy_dc = b.fiscal_year
		JOIN ref_department_history c ON b.department_id = c.department_id
		JOIN ref_agency d ON COALESCE(a.dept_cd,'---') = d.agency_code AND b.agency_id = d.agency_id
		JOIN ref_fund_class e ON COALESCE(a.fund_cd,'---') = e.fund_class_code AND e.fund_class_id = b.fund_class_id
		JOIN etl.ref_department_history_id_seq f ON c.department_history_id = f.department_history_id
	GROUP BY 1	;

	RAISE NOTICE '1.7';


	-- FK:expenditure_object_history_id

	INSERT INTO tmp_fk_values_acc_line(uniq_id,expenditure_object_history_id)
	SELECT	a.uniq_id, max(c.expenditure_object_history_id)
	FROM etl.stg_con_ct_accounting_line a JOIN ref_expenditure_object b ON COALESCE(a.obj_cd,'----') = b.expenditure_object_code AND a.fy_dc = b.fiscal_year
		JOIN ref_expenditure_object_history c ON b.expenditure_object_id = c.expenditure_object_id
	GROUP BY 1	;

	-- Generate the expenditure_object id for new records

	CREATE TEMPORARY TABLE tmp_fk_values_acc_line_new_exp_object(obj_cd varchar,fiscal_year smallint,uniq_id bigint)
	DISTRIBUTED BY (uniq_id);

	INSERT INTO tmp_fk_values_acc_line_new_exp_object
	SELECT COALESCE(a.obj_cd,'----') as obj_cd,fy_dc,MIN(a.uniq_id) as uniq_id
	FROM etl.stg_con_ct_accounting_line a join (SELECT uniq_id
						 FROM tmp_fk_values_acc_line
						 GROUP BY 1
						 HAVING max(expenditure_object_history_id) is null) b on a.uniq_id=b.uniq_id
	GROUP BY 1,2;

	TRUNCATE etl.ref_expenditure_object_id_seq;

	INSERT INTO etl.ref_expenditure_object_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_fk_values_acc_line_new_exp_object;

	RAISE NOTICE '1.9';

	INSERT INTO ref_expenditure_object(expenditure_object_id,expenditure_object_code,
		expenditure_object_name,fiscal_year,created_date,created_load_id,original_expenditure_object_name)
	SELECT a.expenditure_object_id,b.obj_cd,
		(CASE WHEN b.obj_cd <> '----' THEN '<Unknown Expenditure Object>'
			ELSE '<Non-Applicable Expenditure Object>' END) as expenditure_object_name,
		b.fiscal_year,now()::timestamp,p_load_id_in,
		(CASE WHEN b.obj_cd <> '----' THEN '<Unknown Expenditure Object>'
			ELSE '<Non-Applicable Expenditure Object>' END) as original_expenditure_object_name
	FROM   etl.ref_expenditure_object_id_seq a JOIN tmp_fk_values_acc_line_new_exp_object b ON a.uniq_id = b.uniq_id;

	GET DIAGNOSTICS l_count = ROW_COUNT;

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'C',l_count, 'Number of records inserted into ref_expenditure_object from general contracts accounting lines');
	END IF;

	-- Generate the expenditure_object history id for history records

	TRUNCATE etl.ref_expenditure_object_history_id_seq;

	INSERT INTO etl.ref_expenditure_object_history_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_fk_values_acc_line_new_exp_object;

	RAISE NOTICE '1.10';

	INSERT INTO ref_expenditure_object_history(expenditure_object_history_id,expenditure_object_id,fiscal_year,expenditure_object_name,created_date,load_id)
	SELECT a.expenditure_object_history_id,c.expenditure_object_id,b.fiscal_year,
		(CASE WHEN b.obj_cd <> '----' THEN '<Unknown Expenditure Object>'
			ELSE '<Non-Applicable Expenditure Object>' END) as expenditure_object_name,now()::timestamp,p_load_id_in
	FROM   etl.ref_expenditure_object_history_id_seq a JOIN tmp_fk_values_acc_line_new_exp_object b ON a.uniq_id = b.uniq_id
		JOIN etl.ref_expenditure_object_id_seq c ON a.uniq_id = c.uniq_id;

	GET DIAGNOSTICS l_count = ROW_COUNT;

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'C',l_count, 'Number of records inserted into ref_expenditure_object_history from general contracts accounting lines');
	END IF;

	RAISE NOTICE '1.11';

	INSERT INTO tmp_fk_values_acc_line(uniq_id,expenditure_object_history_id)
	SELECT	a.uniq_id, max(c.expenditure_object_history_id)
	FROM etl.stg_con_ct_accounting_line a JOIN ref_expenditure_object b ON COALESCE(a.obj_cd,'----') = b.expenditure_object_code AND a.fy_dc = b.fiscal_year
		JOIN ref_expenditure_object_history c ON b.expenditure_object_id = c.expenditure_object_id
		JOIN etl.ref_expenditure_object_history_id_seq d ON c.expenditure_object_history_id = d.expenditure_object_history_id
	GROUP BY 1	;

	-- FK:budget_code_id

	INSERT INTO tmp_fk_values_acc_line(uniq_id,budget_code_id)
	SELECT	a.uniq_id, b.budget_code_id
	FROM etl.stg_con_ct_accounting_line a JOIN ref_budget_code b ON a.func_cd = b.budget_code AND a.fy_dc=b.fiscal_year
		JOIN ref_agency d ON a.dept_cd = d.agency_code AND b.agency_id = d.agency_id
		JOIN ref_fund_class e ON a.fund_cd = e.fund_class_code AND e.fund_class_id = b.fund_class_id;

	RAISE NOTICE '1.12';

	UPDATE etl.stg_con_ct_accounting_line a
	SET	fund_class_id =ct_table.fund_class_id ,
		agency_history_id =ct_table.agency_history_id ,
		department_history_id =ct_table.department_history_id ,
		expenditure_object_history_id =ct_table.expenditure_object_history_id ,
		budget_code_id=ct_table.budget_code_id
	FROM
		(SELECT uniq_id,
			max(fund_class_id )as fund_class_id ,
			max(agency_history_id )as agency_history_id ,
			max(department_history_id )as department_history_id ,
			max(expenditure_object_history_id )as expenditure_object_history_id ,
			max(budget_code_id) as budget_code_id
		FROM	tmp_fk_values_acc_line
		GROUP	BY 1) ct_table
	WHERE	a.uniq_id = ct_table.uniq_id;

	RETURN 1;

EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in updateForeignKeysForCTInAccLine';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;

	RETURN 0;
END;
$$ language plpgsql;

------------------------------------------------------------------------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION etl.processCONGeneralContracts(p_load_file_id_in int,p_load_id_in bigint) RETURNS INT AS $$
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


	l_fk_update := etl.updateForeignKeysForCTInHeader(p_load_file_id_in,p_load_id_in);

	RAISE NOTICE 'CON 1';

	IF l_fk_update = 1 THEN
		l_fk_update := etl.updateForeignKeysForCTInAwardDetail();
	ELSE
		RETURN -1;
	END IF;

	RAISE NOTICE 'CON 2';

	IF l_fk_update = 1 THEN
		l_fk_update := etl.associateMAGToCT(p_load_file_id_in,p_load_id_in);
	ELSE
		RETURN -1;
	END IF;

	RAISE NOTICE 'CON 3';

	IF l_fk_update = 1 THEN
		l_fk_update := etl.processvendor(p_load_file_id_in,p_load_id_in,'CT1');
	ELSE
		RETURN -1;
	END IF;


	IF l_fk_update <> 1 THEN
		RETURN -1;
	END IF;

	RAISE NOTICE 'CON 4';

	IF l_fk_update = 1 THEN
		l_fk_update := etl.updateForeignKeysForCTInAccLine(p_load_file_id_in,p_load_id_in);
	ELSE
		RETURN -1;
	END IF;


	RAISE NOTICE 'CON 5';


	/*
	1.Pull the key information such as document code, document id, document version etc for all agreements
	2. For the existing contracts gather details on max version in the transaction, staging tables..Determine if the staged agreement is latest version...
	3. Identify the new agreements. Determine the latest version for each of it.
	*/

	-- Inserting all records from staging header

	RAISE NOTICE 'CON 6';
	CREATE TEMPORARY TABLE tmp_ct_con(uniq_id bigint, agency_history_id smallint,doc_id varchar,agreement_id bigint, action_flag char(1),
					  latest_flag char(1),doc_vers_no smallint,privacy_flag char(1),old_agreement_ids varchar)
	DISTRIBUTED BY (uniq_id);

	INSERT INTO tmp_ct_con(uniq_id,agency_history_id,doc_id,doc_vers_no,privacy_flag,action_flag)
	SELECT uniq_id,agency_history_id,doc_id,doc_vers_no,'F' as privacy_flag,'I' as action_flag
	FROM etl.stg_con_ct_header;

	-- Identifying the versions of the agreements for update
	CREATE TEMPORARY TABLE tmp_old_ct_con(uniq_id bigint, agreement_id bigint);

	INSERT INTO tmp_old_ct_con
	SELECT  uniq_id,
		b.agreement_id
	FROM etl.stg_con_ct_header a JOIN history_agreement b ON a.doc_id = b.document_id AND a.document_code_id = b.document_code_id AND a.doc_vers_no = b.document_version
		JOIN ref_agency_history c ON a.agency_history_id = c.agency_history_id
		JOIN ref_agency_history d ON b.agency_history_id = d.agency_history_id and c.agency_id = d.agency_id;

	UPDATE tmp_ct_con a
	SET	agreement_id = b.agreement_id,
		action_flag = 'U'
	FROM	tmp_old_ct_con b
	WHERE	a.uniq_id = b.uniq_id;

	RAISE NOTICE '1';

	-- Identifying the versions of the agreements for update

	TRUNCATE etl.agreement_id_seq ;

	INSERT INTO etl.agreement_id_seq
	SELECT uniq_id
	FROM	tmp_ct_con
	WHERE	action_flag ='I'
		AND COALESCE(agreement_id,0) =0 ;

	UPDATE tmp_ct_con a
	SET	agreement_id = b.agreement_id
	FROM	etl.agreement_id_seq b
	WHERE	a.uniq_id = b.uniq_id;

	RAISE NOTICE '2';

	INSERT INTO history_agreement(agreement_id,master_agreement_id,document_code_id,
				agency_history_id,document_id,document_version,
				tracking_number,record_date_id,budget_fiscal_year,
				document_fiscal_year,document_period,description,
				actual_amount_original,actual_amount,obligated_amount_original,obligated_amount,
				maximum_contract_amount_original,maximum_contract_amount,
				amendment_number,replacing_agreement_id,replaced_by_agreement_id,
				award_status_id,procurement_id,procurement_type_id,
				effective_begin_date_id,effective_end_date_id,reason_modification,
				source_created_date_id,source_updated_date_id,document_function_code,
				award_method_id,award_level_code,agreement_type_id,
				contract_class_code,award_category_id_1,award_category_id_2,
				award_category_id_3,award_category_id_4,award_category_id_5,
				number_responses,location_service,location_zip,
				borough_code,block_code,lot_code,
				council_district_code,vendor_history_id,vendor_preference_level,
				original_contract_amount_original,original_contract_amount,registered_date_id,oca_number,
				number_solicitation,document_name,original_term_begin_date_id,
				original_term_end_date_id,privacy_flag,created_load_id,created_date,
				registered_fiscal_year,registered_fiscal_year_id, registered_calendar_year,
				registered_calendar_year_id,effective_end_fiscal_year,effective_end_fiscal_year_id,
				effective_end_calendar_year,effective_end_calendar_year_id,effective_begin_fiscal_year,
				effective_begin_fiscal_year_id, effective_begin_calendar_year,effective_begin_calendar_year_id,
		   		source_updated_fiscal_year,source_updated_fiscal_year_id, source_updated_calendar_year,
		   		source_updated_calendar_year_id,contract_number,brd_awd_no,rfed_amount_original, rfed_amount)
	SELECT	d.agreement_id,a.master_agreement_id,a.document_code_id,
		a.agency_history_id,a.doc_id,a.doc_vers_no,
		a.trkg_no,a.record_date_id,a.doc_bfy,
		a.doc_fy_dc,a.doc_per_dc,a.doc_dscr,
		a.doc_actu_am,(CASE WHEN a.doc_actu_am IS NULL THEN 0 ELSE a.doc_actu_am END) as actual_amount,a.enc_am, (CASE WHEN a.enc_am IS NULL THEN 0 ELSE a.enc_am END) as obligated_amount,
		a.max_cntrc_am, (CASE WHEN a.max_cntrc_am IS NULL THEN 0 ELSE a.max_cntrc_am END) as maximum_contract_amount,
		a.amend_no,0 as replacing_agreement_id,0 as replaced_by_agreement_id,
		a.cntrc_sta,a.prcu_id,a.prcu_typ_id,
		a.effective_begin_date_id,a.effective_end_date_id,a.reas_mod_dc,
		a.source_created_date_id,a.source_updated_date_id,a.doc_func_cd,
		c.award_method_id,c.awd_lvl_cd,c.agreement_type_id,
		c.ctcls_cd,c.award_category_id_1,c.award_category_id_2,
		c.award_category_id_3,c.award_category_id_4,c.award_category_id_5,
		c.resp_ct,c.loc_serv,c.loc_zip,
		c.brgh_cd,c.blck_cd,c.lot_cd,
		c.coun_dist_cd,b.vendor_history_id,b.vend_pref_lvl,
		a.orig_max_am,(CASE WHEN a.orig_max_am IS NULL THEN 0 ELSE a.orig_max_am END) as original_contract_amount,a.registered_date_id,a.oca_no,
		c.out_of_no_so,a.doc_nm,a.original_term_begin_date_id,
		a.original_term_end_date_id,d.privacy_flag,p_load_id_in,now()::timestamp,
		registered_fiscal_year,registered_fiscal_year_id, registered_calendar_year,
		registered_calendar_year_id,effective_end_fiscal_year,effective_end_fiscal_year_id,
		effective_end_calendar_year,effective_end_calendar_year_id,effective_begin_fiscal_year,
		effective_begin_fiscal_year_id, effective_begin_calendar_year,effective_begin_calendar_year_id,
		source_updated_fiscal_year,source_updated_fiscal_year_id, source_updated_calendar_year,
		source_updated_calendar_year_id,a.doc_cd||a.doc_dept_cd||a.doc_id as contract_number,a.brd_awd_no,a.rfed_am, (CASE WHEN a.rfed_am IS NULL THEN 0 ELSE a.rfed_am END) as rfed_amount
	FROM	etl.stg_con_ct_header a JOIN etl.stg_con_ct_vendor b ON a.doc_cd = b.doc_cd AND a.doc_dept_cd = b.doc_dept_cd
					     AND a.doc_id = b.doc_id AND a.doc_vers_no = b.doc_vers_no
					JOIN etl.stg_con_ct_award_detail c ON a.doc_cd = c.doc_cd AND a.doc_dept_cd = c.doc_dept_cd
					     AND a.doc_id = c.doc_id AND a.doc_vers_no = c.doc_vers_no
					 JOIN tmp_ct_con d ON a.uniq_id = d.uniq_id
	WHERE   action_flag='I';

	GET DIAGNOSTICS l_count = ROW_COUNT;
			IF l_count > 0 THEN
				INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,document_type,num_transactions,description)
				VALUES(p_load_file_id_in,'C','CT1,CTA1',l_count,'# of records inserted into history_agreement');
		END IF;


	RAISE NOTICE '3';
	/* Updates */
	CREATE TEMPORARY TABLE tmp_con_ct_update AS
	SELECT d.agreement_id,a.master_agreement_id,a.document_code_id,
			a.agency_history_id,a.doc_id,a.doc_vers_no,
			a.trkg_no,a.record_date_id,a.doc_bfy,
			a.doc_fy_dc,a.doc_per_dc,a.doc_dscr,
			a.doc_actu_am,a.enc_am,a.max_cntrc_am,
			a.amend_no,0 as replacing_agreement_id,0 as replaced_by_agreement_id,
			a.cntrc_sta,a.prcu_id,a.prcu_typ_id,
			a.effective_begin_date_id,a.effective_end_date_id,a.reas_mod_dc,
			a.source_created_date_id,a.source_updated_date_id,a.doc_func_cd,
			c.award_method_id,c.awd_lvl_cd,c.agreement_type_id,
			c.ctcls_cd,c.award_category_id_1,c.award_category_id_2,
			c.award_category_id_3,c.award_category_id_4,c.award_category_id_5,
			c.resp_ct,c.loc_serv,c.loc_zip,
			c.brgh_cd,c.blck_cd,c.lot_cd,
			c.coun_dist_cd,b.vendor_history_id,b.vend_pref_lvl,
			a.orig_max_am,a.registered_date_id,a.oca_no,
			c.out_of_no_so,a.doc_nm,a.original_term_begin_date_id,
			a.original_term_end_date_id,d.privacy_flag,p_load_id_in as load_id,now()::timestamp as updated_date,
			registered_fiscal_year,registered_fiscal_year_id, registered_calendar_year,
			registered_calendar_year_id,effective_end_fiscal_year,effective_end_fiscal_year_id,
			effective_end_calendar_year,effective_end_calendar_year_id,effective_begin_fiscal_year,
			effective_begin_fiscal_year_id, effective_begin_calendar_year,effective_begin_calendar_year_id,
			source_updated_fiscal_year,source_updated_fiscal_year_id, source_updated_calendar_year,
			source_updated_calendar_year_id,a.brd_awd_no,a.rfed_am
		FROM	etl.stg_con_ct_header a JOIN etl.stg_con_ct_vendor b ON a.doc_cd = b.doc_cd AND a.doc_dept_cd = b.doc_dept_cd
						     AND a.doc_id = b.doc_id AND a.doc_vers_no = b.doc_vers_no
						JOIN etl.stg_con_ct_award_detail c ON a.doc_cd = c.doc_cd AND a.doc_dept_cd = c.doc_dept_cd
						     AND a.doc_id = c.doc_id AND a.doc_vers_no = c.doc_vers_no
						 JOIN tmp_ct_con d ON a.uniq_id = d.uniq_id
	WHERE   action_flag='U'
	DISTRIBUTED BY (agreement_id);



	RAISE NOTICE '4';

	UPDATE history_agreement a
	SET	master_agreement_id = b.master_agreement_id,
		document_code_id = b.document_code_id,
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
		actual_amount = (CASE WHEN b.doc_actu_am IS NULL THEN 0 ELSE b.doc_actu_am END),
		obligated_amount_original = b.enc_am,
		obligated_amount = (CASE WHEN b.enc_am IS NULL THEN 0 ELSE b.enc_am END) ,
		maximum_contract_amount_original = b.max_cntrc_am,
		maximum_contract_amount = (CASE WHEN b.max_cntrc_am IS NULL THEN 0 ELSE b.max_cntrc_am END) ,
		amendment_number = b.amend_no,
		replacing_agreement_id = b.replacing_agreement_id,
		replaced_by_agreement_id = b.replaced_by_agreement_id,
		award_status_id = b.cntrc_sta,
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
		number_responses = b.resp_ct,
		location_service = b.loc_serv,
		location_zip = b.loc_zip,
		borough_code = b.brgh_cd,
		block_code = b.blck_cd,
		lot_code = b.lot_cd,
		council_district_code = b.coun_dist_cd,
		vendor_history_id = b.vendor_history_id,
		vendor_preference_level = b.vend_pref_lvl,
		original_contract_amount_original = b.orig_max_am,
		original_contract_amount = (CASE WHEN b.orig_max_am IS NULL THEN 0 ELSE b.orig_max_am END) ,
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
		source_updated_calendar_year_id = b.source_updated_calendar_year_id,
		brd_awd_no = b.brd_awd_no,
		rfed_amount_original = b.rfed_am,
		rfed_amount = (CASE WHEN b.rfed_am IS NULL THEN 0 ELSE b.rfed_am END)
	FROM	tmp_con_ct_update b
	WHERE	a.agreement_id = b.agreement_id;

	GET DIAGNOSTICS l_count = ROW_COUNT;

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,document_type,num_transactions,description)
		VALUES(p_load_file_id_in,'C','CT1,CTA1',l_count,'# of records updated in history_agreement from General Contracts');
	END IF;


	RAISE NOTICE '5';

	-- Agreement line changes

	INSERT INTO history_agreement_accounting_line(agreement_id,commodity_line_number,line_number,
			event_type_code,description,line_amount_original,line_amount,
			budget_fiscal_year,fiscal_year,fiscal_period,
			fund_class_id,agency_history_id,department_history_id,
			expenditure_object_history_id,revenue_source_id,location_code,
			budget_code_id,reporting_code,rfed_line_amount_original,rfed_line_amount,created_load_id,
			created_date)
	SELECT  d.agreement_id,b.doc_comm_ln_no,b.doc_actg_ln_no,
		b.evnt_typ_id,b.actg_ln_dscr,b.ln_am, (CASE WHEN b.ln_am IS NULL THEN 0 ELSE b.ln_am END) as line_amount,
		b.bfy,b.fy_dc,b.per_dc,
		b.fund_class_id,b.agency_history_id,b.department_history_id,
		b.expenditure_object_history_id,null as revenue_source_id,b.loc_cd,
		b.budget_code_id,b.rpt_cd,b.rfed_ln_am,(CASE WHEN b.rfed_ln_am IS NULL THEN 0 ELSE b.rfed_ln_am END) as rfed_line_amount,p_load_id_in,
		now()::timestamp
	FROM	etl.stg_con_ct_header a JOIN etl.stg_con_ct_accounting_line b ON a.doc_cd = b.doc_cd AND a.doc_dept_cd = b.doc_dept_cd
					     AND a.doc_id = b.doc_id AND a.doc_vers_no = b.doc_vers_no
					     JOIN tmp_ct_con d ON a.uniq_id = d.uniq_id
	WHERE   action_flag = 'I';

	GET DIAGNOSTICS l_count = ROW_COUNT;

		IF l_count > 0 THEN
			INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,document_type,num_transactions,description)
			VALUES(p_load_file_id_in,'C','CT1,CTA1',l_count,'# of records inserted into history_agreement_accounting_line from General Contracts');
	END IF;


	RAISE NOTICE '6';
	-- Identify the agreement accounting lines which need to be deleted/updated

	CREATE TEMPORARY TABLE tmp_acc_lines_actions(agreement_id bigint, commodity_line_number integer,line_number integer,action_flag char(1),uniq_id bigint)
	DISTRIBUTED BY (agreement_id);

	INSERT INTO tmp_acc_lines_actions
	SELECT  COALESCE(latest_tbl.agreement_id,old_tbl.agreement_id) as agreement_id,
		COALESCE(latest_tbl.doc_comm_ln_no, old_tbl.commodity_line_number) as commodity_line_number,
		COALESCE(latest_tbl.doc_actg_ln_no, old_tbl.line_number) as line_number,
		(CASE WHEN latest_tbl.agreement_id = old_tbl.agreement_id THEN 'U'
		      WHEN old_tbl.agreement_id IS NULL THEN 'I'
		      WHEN latest_tbl.agreement_id IS NULL THEN 'D' END) as action_flag	,
		      uniq_id
	FROM
		(SELECT a.agreement_id,c.doc_comm_ln_no,c.doc_actg_ln_no,c.uniq_id
		FROM   tmp_ct_con a JOIN etl.stg_con_ct_header b ON a.uniq_id = b.uniq_id
			JOIN etl.stg_con_ct_accounting_line c ON c.doc_cd = b.doc_cd AND c.doc_dept_cd = b.doc_dept_cd
						     AND c.doc_id = b.doc_id AND c.doc_vers_no = b.doc_vers_no
		WHERE   a.action_flag ='U'
		order by 1,2,3 ) latest_tbl
		FULL OUTER JOIN (SELECT e.agreement_id,e.commodity_line_number,e.line_number
			    FROM   history_agreement_accounting_line e JOIN tmp_ct_con f ON e.agreement_id = f.agreement_id ) old_tbl ON latest_tbl.agreement_id = old_tbl.agreement_id
			    AND latest_tbl.doc_comm_ln_no = old_tbl.commodity_line_number AND latest_tbl.doc_actg_ln_no = old_tbl.line_number;

	RAISE NOTICE '7';

	INSERT INTO history_agreement_accounting_line(agreement_id,commodity_line_number,line_number,
			event_type_code,description,line_amount_original,line_amount,
			budget_fiscal_year,fiscal_year,fiscal_period,
			fund_class_id,agency_history_id,department_history_id,
			expenditure_object_history_id,revenue_source_id,location_code,
			budget_code_id,reporting_code,rfed_line_amount_original,rfed_line_amount,created_load_id,
			created_date)
	SELECT  d.agreement_id,b.doc_comm_ln_no,b.doc_actg_ln_no,
		b.evnt_typ_id,b.actg_ln_dscr,b.ln_am,(CASE WHEN b.ln_am IS NULL THEN 0 ELSE b.ln_am END) as line_amount,
		b.bfy,b.fy_dc,b.per_dc,
		b.fund_class_id,b.agency_history_id,b.department_history_id,
		b.expenditure_object_history_id,null as revenue_source_id,b.loc_cd,
		b.budget_code_id,b.rpt_cd,b.rfed_ln_am,(CASE WHEN b.rfed_ln_am IS NULL THEN 0 ELSE b.rfed_ln_am END) as rfed_line_amount,p_load_id_in,
		now()::timestamp
	FROM	etl.stg_con_ct_header a JOIN etl.stg_con_ct_accounting_line b ON a.doc_cd = b.doc_cd AND a.doc_dept_cd = b.doc_dept_cd
					     AND a.doc_id = b.doc_id AND a.doc_vers_no = b.doc_vers_no
					     JOIN tmp_ct_con d ON a.uniq_id = d.uniq_id
					     JOIN tmp_acc_lines_actions e ON d.agreement_id = e.agreement_id AND b.doc_actg_ln_no = e.line_number AND b.doc_comm_ln_no = e.commodity_line_number
	WHERE   d.action_flag = 'U' AND e.action_flag='I';

	GET DIAGNOSTICS l_count = ROW_COUNT;

			IF l_count > 0 THEN
				INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,document_type,num_transactions,description)
				VALUES(p_load_file_id_in,'C','CT1,CTA1',l_count,'# of records inserted into history_agreement_accounting_line from General Contracts');
	END IF;

	RAISE NOTICE '8';

	INSERT INTO deleted_agreement_accounting_line
	SELECT a.*,now()::timestamp, p_load_id_in as deleted_load_id
	FROM   history_agreement_accounting_line a JOIN tmp_acc_lines_actions b  ON a.agreement_id = b.agreement_id AND a.line_number = b.line_number
		JOIN tmp_ct_con c ON a.agreement_id = c.agreement_id
	WHERE	b.action_flag = 'D' AND c.action_flag='U';

	RAISE NOTICE '9';

	DELETE FROM ONLY history_agreement_accounting_line a
	USING tmp_acc_lines_actions b , tmp_ct_con c
	WHERE   a.agreement_id = b.agreement_id
		AND a.commodity_line_number = b.commodity_line_number
		AND a.line_number = b.line_number
		AND a.agreement_id = c.agreement_id
		AND b.action_flag = 'D' AND c.action_flag='U';

	RAISE NOTICE '10';

	UPDATE  history_agreement_accounting_line f
	SET     event_type_code = b.evnt_typ_id,
		description = b.actg_ln_dscr,
		line_amount_original = b.ln_am,
		line_amount = (CASE WHEN b.ln_am IS NULL THEN 0 ELSE b.ln_am END) ,
		budget_fiscal_year = b.bfy,
		fiscal_year = b.fy_dc,
		fiscal_period = b.per_dc,
		fund_class_id = b.fund_class_id,
		agency_history_id = b.agency_history_id,
		department_history_id =b.department_history_id,
		expenditure_object_history_id = b.expenditure_object_history_id,
		location_code = b.loc_cd,
		budget_code_id = b.budget_code_id,
		reporting_code = b.rpt_cd,
		rfed_line_amount_original = b.rfed_ln_am,
		rfed_line_amount = (CASE WHEN b.rfed_ln_am IS NULL THEN 0 ELSE b.rfed_ln_am END),
		updated_load_id = p_load_id_in,
		updated_date = now()::timestamp
	FROM   etl.stg_con_ct_header a, etl.stg_con_ct_accounting_line b,
		tmp_ct_con d,tmp_acc_lines_actions e
	WHERE  d.action_flag = 'U' AND e.action_flag='U'
	       AND a.doc_cd = b.doc_cd AND a.doc_dept_cd = b.doc_dept_cd
	       AND a.doc_id = b.doc_id AND a.doc_vers_no = b.doc_vers_no
	       AND a.uniq_id = d.uniq_id
	       AND d.agreement_id = e.agreement_id AND b.uniq_id = e.uniq_id
	       AND f.agreement_id = d.agreement_id AND f.line_number = e.line_number AND f.agreement_id = e.agreement_id AND f.commodity_line_number = e.commodity_line_number;


		GET DIAGNOSTICS l_count = ROW_COUNT;
				IF l_count > 0 THEN
					INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,document_type,num_transactions,description)
					VALUES(p_load_file_id_in,'C','CT1,CTA1',l_count,'# of records updated in history_agreement_accounting_line from General Contracts');
	                END IF;

	RAISE NOTICE '11';

	-- For now not processing worksites and commodities
	/*

	DELETE FROM history_agreement_worksite a
	USING tmp_ct_con b
	WHERE a.agreement_id = b.agreement_id
	      AND b.action_flag ='U';

	FOR l_array_ctr IN 1..array_upper(l_worksite_col_array,1) LOOP

		RAISE NOTICE 'asdasda %',l_worksite_col_array[l_array_ctr];

		l_insert_sql := ' INSERT INTO history_agreement_worksite(agreement_id,worksite_code,percentage,amount,master_agreement_yn,load_id,created_date) '||
				' SELECT d.agreement_id,b.'||l_worksite_col_array[l_array_ctr]||',b.'|| l_worksite_per_array[l_array_ctr] || ',(a.max_cntrc_am *b.'||l_worksite_per_array[l_array_ctr] || ')/100 as amount ,''N'',' ||p_load_id_in || ', now()::timestamp '||
				' FROM	etl.stg_con_ct_header a JOIN etl.stg_con_ct_award_detail b ON a.doc_cd = b.doc_cd AND a.doc_dept_cd = b.doc_dept_cd '||
				'			     AND a.doc_id = b.doc_id AND a.doc_vers_no = b.doc_vers_no '||
				'			     JOIN tmp_ct_con d ON a.uniq_id = d.uniq_id '||
				' WHERE b.'|| l_worksite_col_array[l_array_ctr] || ' IS NOT NULL' ;

		EXECUTE l_insert_sql;

		GET DIAGNOSTICS l_count = ROW_COUNT;
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,document_type,num_transactions,description)
		VALUES(p_load_file_id_in,'C','CT1,CTA1',l_count,'# of records inserted in history_agreement_worksite ');

	END LOOP;

	DELETE FROM history_agreement_commodity a
	USING tmp_ct_con b
	WHERE a.agreement_id = b.agreement_id
	      AND b.action_flag ='U';

	INSERT INTO history_agreement_commodity(agreement_id,line_number,master_agreement_yn,
					    description,commodity_code,commodity_type_id,
					    quantity,unit_of_measurement,unit_price,
					    contract_amount,commodity_specification,load_id,
					    created_date)
	SELECT	d.agreement_id,b.doc_comm_ln_no,'N' as master_agreement_yn,
		b.cl_dscr,b.comm_cd,b.ln_typ,
		b.qty,b.unit_meas_cd,b.unit_price,
		b.cntrc_am,b.comm_cd_spfn,p_load_id_in,
		now()::timestamp
	FROM	etl.stg_con_ct_header a JOIN etl.stg_con_ct_commodity b ON a.doc_cd = b.doc_cd AND a.doc_dept_cd = b.doc_dept_cd
						     AND a.doc_id = b.doc_id AND a.doc_vers_no = b.doc_vers_no
						     JOIN tmp_ct_con d ON a.uniq_id = d.uniq_id;
		*/


	RETURN 1;

EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in processCONGeneralContracts';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;

	RETURN 0;
END;
$$ language plpgsql;

-----------------------------------------------------------------------------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION etl.processCon(p_load_file_id_in int,p_load_id_in bigint) RETURNS INT AS $$
DECLARE
	l_status int;
BEGIN

	l_status := etl.processCONGeneralContracts(p_load_file_id_in,p_load_id_in);

	IF l_status = 1 THEN
		l_status := etl.processCONDeliveryOrders(p_load_file_id_in,p_load_id_in);
	ELSE
		RETURN 0;
	END IF;

	IF l_status = 1 THEN
			l_status := etl.processCONPurchaseOrder(p_load_file_id_in,p_load_id_in);
		ELSE
			RETURN 0;
	END IF;

	IF l_status = 1 THEN
			l_status := etl.updateCONFlags(p_load_id_in);
		ELSE
			RETURN 0;
	END IF;


	RETURN 1;

EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in processCon';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;

	RETURN 0;

END;
$$ language plpgsql;


--------------------------------------------------------------------------------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION etl.updateCONFlags(p_load_id_in bigint) RETURNS INT AS $$
DECLARE
BEGIN
	/* Common for all types
	Can be done once per etl
	*/

	-- Get the contracts (key elements only without version) which have been created or updated

	CREATE TEMPORARY TABLE tmp_loaded_agreements_flags(document_id varchar,document_version integer,document_code_id smallint, agency_id smallint,
		latest_version_no smallint,first_version smallint ) DISTRIBUTED BY (document_id);

	INSERT INTO tmp_loaded_agreements_flags
	SELECT distinct document_id,document_version,document_code_id, agency_id
	FROM history_agreement a JOIN ref_agency_history b ON a.agency_history_id = b.agency_history_id
	WHERE coalesce(a.updated_load_id, a.created_load_id) = p_load_id_in ;

	-- Get the max version and min version

	CREATE TEMPORARY TABLE tmp_loaded_agreements_1_flags(document_id varchar,document_code_id smallint, agency_id smallint,
		latest_version_no smallint,first_version_no smallint )  DISTRIBUTED BY (document_id);

	INSERT INTO tmp_loaded_agreements_1_flags
	SELECT a.document_id,a.document_code_id, c.agency_id,
	       max(a.document_version) as latest_version_no, min(a.document_version) as first_version_no
	FROM history_agreement a JOIN tmp_loaded_agreements_flags b ON a.document_id = b.document_id AND a.document_code_id = b.document_code_id
		JOIN ref_agency_history c ON a.agency_history_id = c.agency_history_id AND c.agency_id = b.agency_id
	GROUP BY 1,2,3;

	RAISE NOTICE 'PCON_FLAG1';

	-- Update the versions which are no more the first versions
	-- Might have to change the disbursements linkage here

	CREATE TEMPORARY TABLE tmp_agreement_flag_changes_flags (document_id varchar,document_code_id smallint, agency_id smallint,
					latest_agreement_id bigint, first_agreement_id bigint,non_latest_agreement_id varchar, non_first_agreement_id varchar,
					latest_maximum_contract_amount numeric(16,2)
					) DISTRIBUTED BY (document_id);

	INSERT INTO tmp_agreement_flag_changes_flags
	SELECT a.document_id,a.document_code_id, b.agency_id,
		MAX(CASE WHEN a.document_version = b.latest_version_no THEN agreement_id END) as latest_agreement_id,
		MAX(CASE WHEN a.document_version = b.first_version_no THEN agreement_id END) as first_agreement_id,
		group_concat(CASE WHEN a.document_version <> b.latest_version_no THEN agreement_id ELSE 0 END) as non_latest_agreement_id,
		group_concat(CASE WHEN a.document_version <> b.first_version_no THEN agreement_id ELSE 0 END) as non_first_agreement_id,
		MAX(CASE WHEN a.document_version = b.latest_version_no THEN maximum_contract_amount END) as latest_current_amount
	FROM   history_agreement a JOIN tmp_loaded_agreements_1_flags b ON a.document_id = b.document_id AND a.document_code_id = b.document_code_id
		JOIN ref_agency_history c ON a.agency_history_id = c.agency_history_id AND c.agency_id = b.agency_id
	GROUP BY 1,2,3;

	-- Updating the original flag for non first agreements

	RAISE NOTICE 'PCON_FLAG2';

	CREATE TEMPORARY TABLE tmp_agreements_update_flags(agreement_id bigint,first_agreement_id bigint)
	DISTRIBUTED BY (agreement_id);

	INSERT INTO tmp_agreements_update_flags
	SELECT unnest(string_to_array(non_first_agreement_id,','))::int as agreement_id ,
		first_agreement_id
		FROM	tmp_agreement_flag_changes_flags;

	UPDATE history_agreement a
	SET    original_version_flag = 'N',
		original_agreement_id = b.first_agreement_id
	FROM   tmp_agreements_update_flags b
	WHERE  a.agreement_id = b.agreement_id;

	TRUNCATE tmp_agreements_update_flags;

	INSERT INTO tmp_agreements_update_flags
	SELECT unnest(string_to_array(non_latest_agreement_id,','))::int as agreement_id , NULL as first_agreement_id
		FROM	tmp_agreement_flag_changes_flags;

	UPDATE history_agreement a
	SET    latest_flag = 'N'
	FROM   tmp_agreements_update_flags b
	WHERE  a.agreement_id = b.agreement_id ;

	UPDATE history_agreement a
	SET     original_version_flag = 'Y',
		original_agreement_id = b.first_agreement_id
	FROM    tmp_agreement_flag_changes_flags  b
	WHERE  a.agreement_id = b.first_agreement_id;


	UPDATE history_agreement a
	SET    latest_flag = 'Y'
	FROM    tmp_agreement_flag_changes_flags  b
	WHERE  a.agreement_id = b.latest_agreement_id
		AND COALESCE(a.latest_flag,'N') = 'N';


	RAISE NOTICE 'PCON_FLAG3';


			RETURN 1;




EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in updateCONFlags';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;

	RETURN 0;

END;
$$ language plpgsql;



----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION etl.postProcessContracts(p_job_id_in bigint) RETURNS INT AS $$
DECLARE
	l_start_time  timestamp;
	l_end_time  timestamp;
	l_load_id bigint;
BEGIN
	/* Common for all types
	Can be done once per etl
	*/

	-- Get the contracts (key elements only without version) which have been created or updated

	l_start_time := timeofday()::timestamp;

	SELECT load_id
	FROM etl.etl_data_load
	WHERE job_id = p_job_id_in	AND data_source_code = 'C'
	INTO l_load_id;

	CREATE TEMPORARY TABLE tmp_loaded_agreements(document_id varchar,document_version integer,document_code_id smallint, agency_id smallint,
		latest_version_no smallint,first_version smallint ) DISTRIBUTED BY (document_id);

	INSERT INTO tmp_loaded_agreements
	SELECT distinct document_id,document_version,document_code_id, agency_id
	FROM history_agreement a JOIN ref_agency_history b ON a.agency_history_id = b.agency_history_id
	JOIN etl.etl_data_load c ON coalesce(a.updated_load_id, a.created_load_id) = c.load_id
	WHERE c.job_id = p_job_id_in AND c.data_source_code IN ('C','M','F');

	-- Get the max version and min version

	CREATE TEMPORARY TABLE tmp_loaded_agreements_1(document_id varchar,document_code_id smallint, agency_id smallint,
		latest_version_no smallint,first_version_no smallint )  DISTRIBUTED BY (document_id);

	INSERT INTO tmp_loaded_agreements_1
	SELECT a.document_id,a.document_code_id, c.agency_id,
	       max(a.document_version) as latest_version_no, min(a.document_version) as first_version_no
	FROM history_agreement a JOIN tmp_loaded_agreements b ON a.document_id = b.document_id AND a.document_code_id = b.document_code_id
		JOIN ref_agency_history c ON a.agency_history_id = c.agency_history_id AND c.agency_id = b.agency_id
	GROUP BY 1,2,3;

	RAISE NOTICE 'PCON1';

	-- Update the versions which are no more the first versions
	-- Might have to change the disbursements linkage here

	CREATE TEMPORARY TABLE tmp_agreement_flag_changes (document_id varchar,document_code_id smallint, agency_id smallint,
					latest_agreement_id bigint, first_agreement_id bigint,non_latest_agreement_id varchar, non_first_agreement_id varchar,
					latest_maximum_contract_amount numeric(16,2)
					) DISTRIBUTED BY (document_id);

	INSERT INTO tmp_agreement_flag_changes
	SELECT a.document_id,a.document_code_id, b.agency_id,
		MAX(CASE WHEN a.document_version = b.latest_version_no THEN agreement_id END) as latest_agreement_id,
		MAX(CASE WHEN a.document_version = b.first_version_no THEN agreement_id END) as first_agreement_id,
		group_concat(CASE WHEN a.document_version <> b.latest_version_no THEN agreement_id ELSE 0 END) as non_latest_agreement_id,
		group_concat(CASE WHEN a.document_version <> b.first_version_no THEN agreement_id ELSE 0 END) as non_first_agreement_id,
		MAX(CASE WHEN a.document_version = b.latest_version_no THEN maximum_contract_amount END) as latest_current_amount
	FROM   history_agreement a JOIN tmp_loaded_agreements_1 b ON a.document_id = b.document_id AND a.document_code_id = b.document_code_id
		JOIN ref_agency_history c ON a.agency_history_id = c.agency_history_id AND c.agency_id = b.agency_id
	GROUP BY 1,2,3;

	-- Updating the original flag for non first agreements

	RAISE NOTICE 'PCON2';

	CREATE TEMPORARY TABLE tmp_agreements_update(agreement_id bigint,first_agreement_id bigint)
	DISTRIBUTED BY (agreement_id);

	INSERT INTO tmp_agreements_update
	SELECT unnest(string_to_array(non_first_agreement_id,','))::int as agreement_id ,
		first_agreement_id
		FROM	tmp_agreement_flag_changes;

	UPDATE history_agreement a
	SET    original_version_flag = 'N',
		original_agreement_id = b.first_agreement_id
	FROM   tmp_agreements_update b
	WHERE  a.agreement_id = b.agreement_id;

	TRUNCATE tmp_agreements_update;

	INSERT INTO tmp_agreements_update
	SELECT unnest(string_to_array(non_latest_agreement_id,','))::int as agreement_id , NULL as first_agreement_id
		FROM	tmp_agreement_flag_changes;

	UPDATE history_agreement a
	SET    latest_flag = 'N'
	FROM   tmp_agreements_update b
	WHERE  a.agreement_id = b.agreement_id;

	UPDATE history_agreement a
	SET     original_version_flag = 'Y',
		original_agreement_id = b.first_agreement_id
	FROM    tmp_agreement_flag_changes  b
	WHERE  a.agreement_id = b.first_agreement_id;


	UPDATE history_agreement a
	SET    latest_flag = 'Y'
	FROM    tmp_agreement_flag_changes  b
	WHERE  a.agreement_id = b.latest_agreement_id
		AND COALESCE(a.latest_flag,'N') = 'N';


	RAISE NOTICE 'PCON3';
	-- Populating the agreement_snapshot tables


	CREATE TEMPORARY TABLE tmp_agreement_snapshot(original_agreement_id bigint,starting_year smallint,starting_year_id smallint,document_version smallint,
						     master_agreement_id bigint,ending_year smallint, ending_year_id smallint ,rank_value smallint,agreement_id bigint,
						     effective_begin_fiscal_year smallint,effective_begin_fiscal_year_id smallint,effective_end_fiscal_year smallint,
						     effective_end_fiscal_year_id smallint,registered_fiscal_year smallint,original_version_flag char(1))
	DISTRIBUTED BY 	(original_agreement_id);

	-- Get the latest version for every year of modification

	INSERT INTO tmp_agreement_snapshot
	SELECT  b.original_agreement_id, b.source_updated_fiscal_year, b.source_updated_fiscal_year_id,
		max(b.document_version) as document_version,
		max(b.master_agreement_id) as master_agreement_id,
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
	FROM	tmp_agreement_flag_changes a JOIN history_agreement b ON a.first_agreement_id = b.original_agreement_id
	GROUP  BY 1,2,3;

	-- Update the agreement id based on the version number and original agreeement if

	UPDATE tmp_agreement_snapshot a
	SET     agreement_id = b.agreement_id,
		registered_fiscal_year = b.registered_fiscal_year
	FROM	history_agreement b
	WHERE   a.original_agreement_id = b.original_agreement_id
		AND a.document_version = b.document_version;

	RAISE NOTICE 'PCON4';
	-- Updating the POP years from the latest version of the agreement
	UPDATE tmp_agreement_snapshot a
	SET	effective_begin_fiscal_year = b.effective_begin_fiscal_year,
		effective_begin_fiscal_year_id = b.effective_begin_fiscal_year_id,
		effective_end_fiscal_year = b.effective_end_fiscal_year,
		effective_end_fiscal_year_id = b.effective_end_fiscal_year_id
	FROM	history_agreement b
	WHERE   a.original_agreement_id = b.original_agreement_id
		AND b.latest_flag = 'Y';

	-- Update the starting year to 2010 for the very first record of an agreement in the snapshot if starting year >2010 and pop start year prior to 2010

	UPDATE 	tmp_agreement_snapshot
	SET	starting_year = 2010,
		starting_year_id = year_id
	FROM	ref_year
	WHERE	year_value = 2010
		AND starting_year > 2010
		AND rank_value = 1
		AND registered_fiscal_year <= 2010;


	-- Updating the starting_year to effective_begin_fiscal_year if starting_year > effective_begin_fiscal_year

		/*
		 UPDATE 	tmp_agreement_snapshot
		SET	starting_year = effective_begin_fiscal_year,
		starting_year_id = effective_begin_fiscal_year_id
		WHERE rank_value = 1 AND starting_year > effective_begin_fiscal_year ;
		 */

		UPDATE 	tmp_agreement_snapshot
		SET	starting_year = effective_begin_fiscal_year,
		starting_year_id = effective_begin_fiscal_year_id
		WHERE rank_value = 1 AND starting_year > effective_begin_fiscal_year AND effective_begin_fiscal_year IS NOT NULL;

		UPDATE 	tmp_agreement_snapshot a
		SET	starting_year = a.registered_fiscal_year,
		starting_year_id = b.year_id
		FROM	ref_year b
		WHERE a.registered_fiscal_year = b.year_value
		AND rank_value = 1 AND starting_year > registered_fiscal_year AND registered_fiscal_year IS NOT NULL;

	-- Updating the ending year to be ending year - 1
	-- Until this step ending year of a record is equivalent to the staring year of the sucessor. So -1 should be done to ensure no overlapping

	UPDATE 	tmp_agreement_snapshot
	SET	ending_year = ending_year - 1,
		ending_year_id  = year_id
	FROM	ref_year
	WHERE	year_value = ending_year - 1
		AND ending_year is not null;

	UPDATE tmp_agreement_snapshot
	SET original_version_flag = 'Y'
	WHERE rank_value = 1;

	RAISE NOTICE 'PCON5';

	INSERT INTO agreement_snapshot_deleted(agreement_id, original_agreement_id, starting_year, master_agreement_yn, load_id, deleted_date, job_id)
	SELECT distinct a.agreement_id, a.original_agreement_id, a.starting_year, a.master_agreement_yn, l_load_id, now()::timestamp, p_job_id_in
	FROM agreement_snapshot a , tmp_agreement_snapshot b
	WHERE a.original_agreement_id = b.original_agreement_id;


	DELETE FROM ONLY agreement_snapshot a USING  tmp_agreement_snapshot b WHERE a.original_agreement_id = b.original_agreement_id;

	INSERT INTO agreement_snapshot(original_agreement_id, starting_year,starting_year_id,document_version,document_code_id,agency_history_id, agency_id,agency_code,agency_name,
				       agreement_id, ending_year,ending_year_id,contract_number,
				       original_contract_amount,maximum_contract_amount,description,
					vendor_history_id,vendor_id,vendor_code,vendor_name,
					dollar_difference,
					percent_difference,
					master_agreement_id, master_contract_number,agreement_type_id,
					agreement_type_code, agreement_type_name,award_category_id,award_category_code,award_category_name,award_method_id,award_method_code,award_method_name,expenditure_object_codes,
					expenditure_object_names,industry_type_id,
					industry_type_name, award_size_id,effective_begin_date,effective_begin_date_id,
					effective_end_date, effective_end_date_id,registered_date,
					registered_date_id,brd_awd_no,tracking_number,rfed_amount,
					registered_year, registered_year_id,latest_flag,original_version_flag,
					effective_begin_year,effective_begin_year_id,effective_end_year,effective_end_year_id,
					minority_type_id, minority_type_name,
					master_agreement_yn,load_id,last_modified_date,job_id, scntrc_status)
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
	        		      ELSE b.effective_end_fiscal_year_id END),b.contract_number,
	        b.original_contract_amount,b.maximum_contract_amount,b.description,
		b.vendor_history_id,c.vendor_id, v.vendor_customer_code, COALESCE(c.legal_name,c.alias_name),
		coalesce(b.maximum_contract_amount,0) - coalesce(b.original_contract_amount,0) as dollar_difference,
		(CASE WHEN coalesce(b.original_contract_amount,0) = 0 THEN 0 ELSE
		ROUND((( coalesce(b.maximum_contract_amount,0) - coalesce(b.original_contract_amount,0)) * 100 )::decimal / coalesce(b.original_contract_amount,0),2) END) as percent_difference,
		b.master_agreement_id,d.contract_number,e.agreement_type_id,
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
		(CASE WHEN b.maximum_contract_amount IS NULL THEN 5 WHEN b.maximum_contract_amount <= 5000 THEN 4 WHEN b.maximum_contract_amount > 5000
		AND b.maximum_contract_amount <= 100000 THEN 3 		WHEN  b.maximum_contract_amount > 100000 AND b.maximum_contract_amount <= 1000000 THEN 2 WHEN b.maximum_contract_amount > 1000000 THEN 1
		ELSE 5 END) as award_size_id,h.date as effective_begin_date, h.date_id as effective_begin_date_id,
		i.date as effective_end_date, i.date_id as effective_end_date_id,j.date as registered_date,
		j.date_id as registered_date_id,b.brd_awd_no,b.tracking_number,b.rfed_amount,
		b.registered_fiscal_year, b.registered_fiscal_year_id,b.latest_flag,a.original_version_flag,
		a.effective_begin_fiscal_year,a.effective_begin_fiscal_year_id,a.effective_end_fiscal_year,a.effective_end_fiscal_year_id,
		m.minority_type_id, m.minority_type_name,
		'N' as master_agreement_yn, coalesce(b.updated_load_id, b.created_load_id),coalesce(b.updated_date, b.created_date), p_job_id_in, b.scntrc_status
	FROM	tmp_agreement_snapshot a JOIN history_agreement b ON a.agreement_id = b.agreement_id
		LEFT JOIN vendor_history c ON b.vendor_history_id = c.vendor_history_id
		LEFT JOIN vendor v ON c.vendor_id = v.vendor_id
		LEFT JOIN ref_agency_history ah ON b.agency_history_id = ah.agency_history_id
		LEFT JOIN ref_agency ag ON ah.agency_id = ag.agency_id
		LEFT JOIN history_master_agreement d ON b.master_agreement_id = d.master_agreement_id
		LEFT JOIN ref_agreement_type e ON b.agreement_type_id = e.agreement_type_id
		LEFT JOIN ref_award_category f ON b.award_category_id_1 = f.award_category_id
		LEFT JOIN ref_award_method am ON b.award_method_id = am.award_method_id
		LEFT JOIN (SELECT z.agreement_id, GROUP_CONCAT(distinct y.expenditure_object_name) as expenditure_object_names, GROUP_CONCAT(distinct expenditure_object_code) as expenditure_object_codes
			   FROM history_agreement_accounting_line z JOIN ref_expenditure_object_history y ON z.expenditure_object_history_id = y.expenditure_object_history_id
			   JOIN ref_expenditure_object w ON y.expenditure_object_id = w.expenditure_object_id
			   JOIN tmp_agreement_snapshot x ON x.agreement_id = z.agreement_id
			   GROUP BY 1) g ON a.agreement_id = g.agreement_id
		LEFT JOIN ref_date h ON h.date_id = b.effective_begin_date_id
		LEFT JOIN ref_date i ON i.date_id = b.effective_end_date_id
		LEFT JOIN ref_date j ON j.date_id = b.registered_date_id
		LEFT JOIN ref_award_category_industry k ON k.award_category_code = f.award_category_code
		LEFT JOIN ref_industry_type l ON k.industry_type_id = l.industry_type_id
		LEFT JOIN vendor_min_bus_type m ON b.vendor_history_id = m.vendor_history_id
		WHERE b.source_updated_date_id IS NOT NULL;


	RAISE NOTICE 'PCON6';



	-- update from subcontract_status table

	UPDATE agreement_snapshot a
	SET scntrc_status = b.scntrc_status
	FROM subcontract_status b, ref_document_code c
	WHERE a.contract_number = b.contract_number
	AND a.document_code_id=c.document_code_id
	AND a.effective_end_date >= '2015-07-01'
	AND c.document_code in ('CT1','CTA1','CT2');

  -- new logic for scntrc_status

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


  -- updating upstream history_agreement.scntrc_status in case it matters.

  UPDATE history_agreement a
  SET scntrc_status = b.scntrc_status
  FROM agreement_snapshot b
  WHERE a.agreement_id = b.agreement_id
  AND a.scntrc_status IS DISTINCT FROM b.scntrc_status
  ;


	RAISE NOTICE 'PCON6.1';

	UPDATE agreement_snapshot a
	SET minority_type_id=11,
		minority_type_name = 'Individuals & Others'
	WHERE job_id = p_job_id_in AND agreement_type_code IN ('35','36','39','40','44','65','68','79','85')
	AND ( minority_type_id IS NULL OR minority_type_id IN (1,6,7,8));

	UPDATE agreement_snapshot a
	SET minority_type_id=11,
		minority_type_name = 'Individuals & Others'
	WHERE job_id = p_job_id_in AND award_method_code IN ('07','08','09','17','18','44','45','55')
	AND ( minority_type_id IS NULL OR minority_type_id IN (1,6,7,8));

	UPDATE agreement_snapshot a
	SET minority_type_id=7,
		minority_type_name = 'Non-Minority'
	WHERE job_id = p_job_id_in 	AND ( minority_type_id IS NULL OR minority_type_id IN (1,6,7,8));

	/* End of one time changes */

	-- Populating the agreement_snapshot tables related to the calendar year

	-- Get the latest version for every year of modification

	TRUNCATE tmp_agreement_snapshot;

	INSERT INTO tmp_agreement_snapshot
	SELECT  b.original_agreement_id, b.source_updated_calendar_year, b.source_updated_calendar_year_id,
		max(b.document_version) as document_version,
		max(b.master_agreement_id) as master_agreement_id,
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
	FROM	tmp_agreement_flag_changes a JOIN history_agreement b ON a.first_agreement_id = b.original_agreement_id
	GROUP  BY 1,2,3;

	-- Update the agreement id based on the version number and original agreeement if

	UPDATE tmp_agreement_snapshot a
	SET     agreement_id = b.agreement_id,
		registered_fiscal_year = b.registered_calendar_year
	FROM	history_agreement b
	WHERE   a.original_agreement_id = b.original_agreement_id
		AND a.document_version = b.document_version;

	RAISE NOTICE 'PCON7';

	-- Updating the POP years from the latest version of the agreement
	UPDATE tmp_agreement_snapshot a
	SET	effective_begin_fiscal_year = b.effective_begin_calendar_year,
		effective_begin_fiscal_year_id = b.effective_begin_calendar_year_id,
		effective_end_fiscal_year = b.effective_end_calendar_year,
		effective_end_fiscal_year_id = b.effective_end_calendar_year_id
	FROM	history_agreement b
	WHERE   a.original_agreement_id = b.original_agreement_id
		AND b.latest_flag = 'Y';

	-- Update the starting year to 2010 for the very first record of an agreement in the snapshot if starting year >2010 and pop start year prior to 2010

	UPDATE 	tmp_agreement_snapshot
	SET	starting_year = 2010,
		starting_year_id = year_id
	FROM	ref_year
	WHERE	year_value = 2010
		AND starting_year > 2010
		AND rank_value = 1
		AND registered_fiscal_year <= 2010;

		-- Updating the starting_year to effective_begin_fiscal_year if starting_year > effective_begin_fiscal_year

		/*
		 UPDATE 	tmp_agreement_snapshot
		SET	starting_year = effective_begin_fiscal_year,
		starting_year_id = effective_begin_fiscal_year_id
		WHERE rank_value = 1 AND starting_year > effective_begin_fiscal_year ;
		 */

		UPDATE 	tmp_agreement_snapshot
		SET	starting_year = effective_begin_fiscal_year,
		starting_year_id = effective_begin_fiscal_year_id
		WHERE rank_value = 1 AND starting_year > effective_begin_fiscal_year AND effective_begin_fiscal_year IS NOT NULL;

		UPDATE 	tmp_agreement_snapshot a
		SET	starting_year = a.registered_fiscal_year,
		starting_year_id = b.year_id
		FROM	ref_year b
		WHERE a.registered_fiscal_year = b.year_value
		AND rank_value = 1 AND starting_year > registered_fiscal_year AND registered_fiscal_year IS NOT NULL;


	-- Updating the ending year to be ending year - 1
	-- Until this step ending year of a record is equivalent to the staring year of the sucessor. So -1 should be done to ensure no overlapping

	UPDATE 	tmp_agreement_snapshot
	SET	ending_year = ending_year - 1,
		ending_year_id  = year_id
	FROM	ref_year
	WHERE	year_value = ending_year - 1
		AND ending_year is not null;

	UPDATE tmp_agreement_snapshot
	SET original_version_flag = 'Y'
	WHERE rank_value = 1;

	RAISE NOTICE 'PCON8';

	INSERT INTO agreement_snapshot_cy_deleted(agreement_id, original_agreement_id, starting_year, master_agreement_yn, load_id, deleted_date, job_id)
	SELECT distinct a.agreement_id, a.original_agreement_id, a.starting_year, a.master_agreement_yn, l_load_id, now()::timestamp, p_job_id_in
	FROM agreement_snapshot_cy a , tmp_agreement_snapshot b
	WHERE a.original_agreement_id = b.original_agreement_id;

	DELETE FROM ONLY agreement_snapshot_cy a USING  tmp_agreement_snapshot b WHERE a.original_agreement_id = b.original_agreement_id;

	INSERT INTO agreement_snapshot_cy(original_agreement_id, starting_year,starting_year_id,document_version,document_code_id,agency_history_id, agency_id,agency_code,agency_name,
				       agreement_id, ending_year,ending_year_id,contract_number,
				       original_contract_amount,maximum_contract_amount,description,
					vendor_history_id,vendor_id,vendor_code,vendor_name,
					dollar_difference,
					percent_difference,
					master_agreement_id, master_contract_number,agreement_type_id,
					agreement_type_code, agreement_type_name,award_category_id,award_category_code,award_category_name,award_method_id,award_method_code,award_method_name,expenditure_object_codes,
					expenditure_object_names,industry_type_id,
					industry_type_name,award_size_id,effective_begin_date,effective_begin_date_id,
					effective_end_date, effective_end_date_id,registered_date,
					registered_date_id,brd_awd_no,tracking_number,rfed_amount,
					registered_year, registered_year_id,latest_flag,original_version_flag,
					effective_begin_year,effective_begin_year_id,effective_end_year,effective_end_year_id,
					minority_type_id, minority_type_name,
					master_agreement_yn,load_id,last_modified_date, job_id, scntrc_status)
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
				      ELSE b.effective_end_calendar_year_id END),b.contract_number,
		b.original_contract_amount,b.maximum_contract_amount,b.description,
		b.vendor_history_id,c.vendor_id, v.vendor_customer_code, COALESCE(c.legal_name,c.alias_name),
		coalesce(b.maximum_contract_amount,0) - coalesce(b.original_contract_amount,0) as  dollar_difference,
		(CASE WHEN coalesce(b.original_contract_amount,0) = 0 THEN 0 ELSE
		ROUND((( coalesce(b.maximum_contract_amount,0) - coalesce(b.original_contract_amount,0)) * 100 )::decimal / coalesce(b.original_contract_amount,0),2) END) as percent_difference,
		b.master_agreement_id,d.contract_number,e.agreement_type_id,
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
		(CASE WHEN b.maximum_contract_amount IS NULL THEN 5 WHEN b.maximum_contract_amount <= 5000 THEN 4 WHEN b.maximum_contract_amount > 5000
		AND b.maximum_contract_amount <= 100000 THEN 3 	WHEN  b.maximum_contract_amount > 100000 AND b.maximum_contract_amount <= 1000000 THEN 2 WHEN b.maximum_contract_amount > 1000000 THEN 1
		ELSE 5 END) as award_size_id,h.date as effective_begin_date, h.date_id as effective_begin_date_id,
		i.date as effective_end_date, i.date_id as effective_end_date_id,j.date as registered_date,
		j.date_id as registered_date_id,b.brd_awd_no,b.tracking_number,b.rfed_amount,
		b.registered_calendar_year, b.registered_calendar_year_id,b.latest_flag,a.original_version_flag,
		a.effective_begin_fiscal_year,a.effective_begin_fiscal_year_id,a.effective_end_fiscal_year,a.effective_end_fiscal_year_id,
		m.minority_type_id, m.minority_type_name,
		'N' as master_agreement_yn,coalesce(b.updated_load_id, b.created_load_id),coalesce(b.updated_date, b.created_date), p_job_id_in, b.scntrc_status
	FROM	tmp_agreement_snapshot a JOIN history_agreement b ON a.agreement_id = b.agreement_id
		LEFT JOIN vendor_history c ON b.vendor_history_id = c.vendor_history_id
		LEFT JOIN vendor v ON c.vendor_id = v.vendor_id
		LEFT JOIN ref_agency_history ah ON b.agency_history_id = ah.agency_history_id
		LEFT JOIN ref_agency ag ON ah.agency_id = ag.agency_id
		LEFT JOIN history_master_agreement d ON b.master_agreement_id = d.master_agreement_id
		LEFT JOIN ref_agreement_type e ON b.agreement_type_id = e.agreement_type_id
		LEFT JOIN ref_award_category f ON b.award_category_id_1 = f.award_category_id
		LEFT JOIN ref_award_method am ON b.award_method_id = am.award_method_id
		LEFT JOIN (SELECT z.agreement_id, GROUP_CONCAT(distinct y.expenditure_object_name) as expenditure_object_names, GROUP_CONCAT(distinct expenditure_object_code) as expenditure_object_codes
			   FROM history_agreement_accounting_line z JOIN ref_expenditure_object_history y ON z.expenditure_object_history_id = y.expenditure_object_history_id
			   JOIN ref_expenditure_object w ON y.expenditure_object_id = w.expenditure_object_id
			   JOIN tmp_agreement_snapshot x ON x.agreement_id = z.agreement_id
			   GROUP BY 1) g ON a.agreement_id = g.agreement_id
		LEFT JOIN ref_date h ON h.date_id = b.effective_begin_date_id
		LEFT JOIN ref_date i ON i.date_id = b.effective_end_date_id
		LEFT JOIN ref_date j ON j.date_id = b.registered_date_id
		LEFT JOIN ref_award_category_industry k ON k.award_category_code = f.award_category_code
		LEFT JOIN ref_industry_type l ON k.industry_type_id = l.industry_type_id
		LEFT JOIN vendor_min_bus_type m ON b.vendor_history_id = m.vendor_history_id
		WHERE b.source_updated_date_id IS NOT NULL;

	RAISE NOTICE 'PCON9';

	UPDATE agreement_snapshot_cy a
	SET minority_type_id=11,
		minority_type_name = 'Individuals & Others'
	WHERE job_id = p_job_id_in AND agreement_type_code IN ('35','36','39','40','44','65','68','79','85')
	AND ( minority_type_id IS NULL OR minority_type_id IN (1,6,7,8));

	UPDATE agreement_snapshot_cy a
	SET minority_type_id=11,
		minority_type_name = 'Individuals & Others'
	WHERE job_id = p_job_id_in AND award_method_code IN ('07','08','09','17','18','44','45','55')
	AND ( minority_type_id IS NULL OR minority_type_id IN (1,6,7,8));

	UPDATE agreement_snapshot_cy a
	SET minority_type_id=7,
		minority_type_name = 'Non-Minority'
	WHERE job_id = p_job_id_in 	AND ( minority_type_id IS NULL OR minority_type_id IN (1,6,7,8));



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



	CREATE TEMPORARY TABLE tmp_master_has_mwbe_children (original_master_agreement_id bigint, total_children int)
	DISTRIBUTED BY (original_master_agreement_id);

	INSERT INTO tmp_master_has_mwbe_children
	SELECT distinct original_master_agreement_id, 0 as total_children
	FROM history_master_agreement ;

	CREATE TEMPORARY TABLE tmp_master_has_mwbe_children_1 (original_master_agreement_id bigint, total_children int)
	DISTRIBUTED BY (original_master_agreement_id);

	INSERT INTO tmp_master_has_mwbe_children_1
	SELECT b.original_master_agreement_id, count(distinct original_agreement_id) as total_children
	FROM agreement_snapshot a JOIN tmp_master_has_mwbe_children b
	ON a.master_agreement_id = b.original_master_agreement_id
	WHERE a.master_agreement_yn = 'N' and a.latest_flag = 'Y' AND a.minority_type_id in (2,3,4,5,9)
	GROUP BY 1;

	UPDATE tmp_master_has_mwbe_children a
	SET total_children =  b.total_children
	FROM tmp_master_has_mwbe_children_1 b
	WHERE a.original_master_agreement_id = b.original_master_agreement_id ;


	UPDATE 	agreement_snapshot a
	SET has_mwbe_children = (CASE WHEN b.total_children > 0 THEN 'Y' ELSE 'N' END)
	FROM tmp_master_has_mwbe_children b
	WHERE a.master_agreement_yn = 'Y' AND a.original_agreement_id = b.original_master_agreement_id;

	UPDATE 	agreement_snapshot_cy a
	SET has_mwbe_children = (CASE WHEN b.total_children > 0 THEN 'Y' ELSE 'N' END)
	FROM tmp_master_has_mwbe_children b
	WHERE a.master_agreement_yn = 'Y' AND a.original_agreement_id = b.original_master_agreement_id;

	-- Associate Disbursement line item to the original version of the agreement

	CREATE TEMPORARY TABLE tmp_ct_fms_line_item(disbursement_line_item_id bigint, agreement_id bigint,maximum_contract_amount numeric(16,2))
	DISTRIBUTED BY (disbursement_line_item_id);

	CREATE TEMPORARY TABLE tmp_agreement(agreement_id bigint,first_agreement_id bigint,maximum_contract_amount numeric(16,2))
	DISTRIBUTED BY (agreement_id);

	INSERT INTO tmp_agreement
	SELECT unnest(string_to_array(non_first_agreement_id,','))::int as agreement_id ,
		first_agreement_id,
		latest_maximum_contract_amount
	FROM   tmp_agreement_flag_changes;

	CREATE TEMPORARY TABLE tmp_agreement_non_zero(agreement_id bigint,first_agreement_id bigint,maximum_contract_amount numeric(16,2))
	DISTRIBUTED BY (agreement_id);

	INSERT INTO tmp_agreement_non_zero
	SELECT agreement_id, first_agreement_id, maximum_contract_amount FROM
	tmp_agreement WHERE agreement_id > 0;


	CREATE TEMPORARY TABLE tmp_ct_fms_non_partial_disbs(disbursement_line_item_id bigint, agreement_id bigint)
	DISTRIBUTED BY (agreement_id);

	INSERT INTO tmp_ct_fms_non_partial_disbs
	SELECT disbursement_line_item_id, agreement_id
	FROM disbursement_line_item
	WHERE coalesce(file_type,'F') = 'F';

	INSERT INTO tmp_ct_fms_line_item
	SELECT disbursement_line_item_id, b.first_agreement_id
	FROM tmp_ct_fms_non_partial_disbs a JOIN  tmp_agreement_non_zero b ON a.agreement_id = b.agreement_id;


	UPDATE disbursement_line_item a
	SET	agreement_id = b.agreement_id
	FROM	tmp_ct_fms_line_item b
	WHERE	a.disbursement_line_item_id = b.disbursement_line_item_id;

	UPDATE disbursement_line_item_details a
	SET	agreement_id = b.agreement_id
	FROM	tmp_ct_fms_line_item b
	WHERE	a.disbursement_line_item_id = b.disbursement_line_item_id;

	UPDATE disbursement_line_item_details a
	SET	master_agreement_id = c.master_agreement_id
	FROM	tmp_ct_fms_line_item b, history_agreement c
	WHERE	a.disbursement_line_item_id = b.disbursement_line_item_id
	 AND a.agreement_id = c.agreement_id;

	 RAISE NOTICE 'PCON10';

	 -- updating maximum_contract_amount in disbursement_line_item_details

	UPDATE disbursement_line_item_details a
	SET	maximum_contract_amount = c.maximum_contract_amount,
		industry_type_id = c.industry_type_id,
		industry_type_name = c.industry_type_name,
		agreement_type_code = c.agreement_type_code,
		award_method_code = c.award_method_code,
		contract_industry_type_id = c.industry_type_id,
		contract_minority_type_id = c.minority_type_id,
		purpose = c.description
	FROM	tmp_ct_fms_line_item b, agreement_snapshot c
	WHERE	a.disbursement_line_item_id = b.disbursement_line_item_id
		AND a.agreement_id = c.original_agreement_id AND master_agreement_yn = 'N' AND a.fiscal_year between c.starting_year AND c.ending_year;

	 -- updating maximum_contract_amount_cy in disbursement_line_item_details

	UPDATE disbursement_line_item_details a
	SET	maximum_contract_amount_cy = c.maximum_contract_amount,
	contract_industry_type_id_cy = c.industry_type_id,
	contract_minority_type_id_cy = c.minority_type_id,
	purpose_cy = c.description
	FROM	tmp_ct_fms_line_item b, agreement_snapshot_cy c
	WHERE	a.disbursement_line_item_id = b.disbursement_line_item_id
		AND a.agreement_id = c.original_agreement_id AND master_agreement_yn = 'N' AND a.calendar_fiscal_year between c.starting_year AND c.ending_year;

	 -- updating maximum_spending_limit in disbursement_line_item_details

	UPDATE disbursement_line_item_details a
	SET	maximum_spending_limit = c.maximum_contract_amount,
		master_contract_industry_type_id = c.industry_type_id,
		master_contract_minority_type_id = c.minority_type_id,
		master_purpose = c.description
	FROM	tmp_ct_fms_line_item b, agreement_snapshot c
	WHERE	a.disbursement_line_item_id = b.disbursement_line_item_id
		AND a.master_agreement_id = c.original_agreement_id AND master_agreement_yn = 'Y' AND a.fiscal_year between c.starting_year AND c.ending_year;

	 -- updating maximum_spending_limit_cy in disbursement_line_item_details

	UPDATE disbursement_line_item_details a
	SET	maximum_spending_limit_cy = c.maximum_contract_amount,
		master_contract_industry_type_id_cy = c.industry_type_id,
		master_contract_minority_type_id_cy = c.minority_type_id,
		master_purpose_cy = c.description
	FROM	tmp_ct_fms_line_item b, agreement_snapshot_cy c
	WHERE	a.disbursement_line_item_id = b.disbursement_line_item_id
		AND a.master_agreement_id = c.original_agreement_id AND master_agreement_yn = 'Y' AND a.calendar_fiscal_year between c.starting_year AND c.ending_year;

	-- End of associating Disbursement line item to the original version of an agreement

	l_end_time := timeofday()::timestamp;

	INSERT INTO etl.etl_script_execution_status(job_id,script_name,completed_flag,start_time,end_time)
	VALUES(p_job_id_in,'etl.postProcessContracts',1,l_start_time,l_end_time);

			RETURN 1;

	/* End of one time changes */


EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in postProcessContracts';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;

	l_end_time := timeofday()::timestamp;

	INSERT INTO etl.etl_script_execution_status(job_id,script_name,completed_flag,start_time,end_time,errno,errmsg)
	VALUES(p_job_id_in,'etl.postProcessContracts',0,l_start_time,l_end_time,SQLSTATE,SQLERRM);

	RETURN 0;

END;
$$ language plpgsql;


--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION etl.refreshContractsPreAggregateTables(p_job_id_in bigint) RETURNS INT AS $$
DECLARE
	l_start_time  timestamp;
	l_end_time  timestamp;

BEGIN



	l_start_time := timeofday()::timestamp;


	TRUNCATE agreement_snapshot_expanded;

	INSERT INTO agreement_snapshot_expanded
SELECT  original_agreement_id ,
	agreement_id,
	fiscal_year ,
	description ,
	contract_number ,
	vendor_id ,
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
	master_agreement_id,
	minority_type_id,
	minority_type_name,
	master_agreement_yn,
	status_flag,
	scntrc_status
FROM
(SELECT original_agreement_id,
	agreement_id,
	generate_series(effective_begin_year,effective_end_year,1) as fiscal_year,
	description,
	contract_number,
	vendor_id,
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
	master_agreement_id,
	minority_type_id,
	minority_type_name,
	master_agreement_yn,
	'A' as status_flag,
	scntrc_status
FROM	agreement_snapshot ) expanded_tbl  WHERE fiscal_year between starting_year AND ending_year
AND fiscal_year >= 2010 AND ( (fiscal_year <= extract(year from now()::date) AND extract(month from now()::date) <= 6) OR
		     (fiscal_year <= (extract(year from now()::date)::smallint)+1 AND extract(month from now()::date) > 6) );

INSERT INTO agreement_snapshot_expanded
SELECT original_agreement_id,
	agreement_id,
	registered_year,
	description,
	contract_number,
	vendor_id,
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
	master_agreement_id,
	minority_type_id,
	minority_type_name,
	master_agreement_yn,
	'R' as status_flag,
	scntrc_status
FROM	agreement_snapshot
WHERE registered_year between starting_year AND ending_year
AND registered_year >= 2010 ;

RAISE NOTICE 'PRE_CON_AGGR1';

CREATE TEMPORARY TABLE tmp_ct_child_agreement_snapshot_expanded AS
SELECT * from agreement_snapshot_expanded
WHERE master_agreement_yn = 'N';

CREATE TEMPORARY TABLE tmp_ct_master_agreement_snapshot_expanded AS
SELECT * from agreement_snapshot_expanded
WHERE master_agreement_yn = 'Y';


CREATE TEMPORARY TABLE tmp_ct_child_agreement_snapshot_expanded_maxyear(original_agreement_id bigint,max_fiscal_year smallint)
DISTRIBUTED BY (original_agreement_id);

INSERT INTO tmp_ct_child_agreement_snapshot_expanded_maxyear
SELECT original_agreement_id, max(fiscal_year)
FROM tmp_ct_child_agreement_snapshot_expanded
WHERE status_flag = 'A'
GROUP BY 1;


CREATE TEMPORARY TABLE tmp_ct_child_agreement_snapshot_expanded_active(agreement_id bigint, original_agreement_id bigint,rfed_amount numeric(16,2),status_flag character(1),fiscal_year smallint,max_fiscal_year smallint)
DISTRIBUTED BY (original_agreement_id);

INSERT INTO tmp_ct_child_agreement_snapshot_expanded_active
SELECT agreement_id , a.original_agreement_id, rfed_amount, status_flag, fiscal_year, b.max_fiscal_year
FROM tmp_ct_child_agreement_snapshot_expanded a LEFT JOIN tmp_ct_child_agreement_snapshot_expanded_maxyear b ON a.original_agreement_id = b.original_agreement_id ;


CREATE TEMPORARY TABLE tmp_ct_child_agreement_snapshot AS
SELECT * FROM agreement_snapshot
WHERE master_agreement_yn = 'N';

CREATE TEMPORARY TABLE tmp_ct_child_agreement_snapshot_max_endingyear(original_agreement_id bigint,max_ending_year smallint)
DISTRIBUTED BY (original_agreement_id);

INSERT INTO tmp_ct_child_agreement_snapshot_max_endingyear
SELECT original_agreement_id, max(ending_year)
FROM tmp_ct_child_agreement_snapshot
GROUP BY 1;

CREATE TEMPORARY TABLE tmp_ct_child_agreement_snapshot_active(master_agreement_id bigint, rfed_amount numeric(16,2),starting_year smallint, ending_year smallint, max_ending_year smallint)
DISTRIBUTED BY (master_agreement_id);

INSERT INTO tmp_ct_child_agreement_snapshot_active
SELECT master_agreement_id, rfed_amount,  starting_year, coalesce(ending_year,starting_year),  b.max_ending_year
FROM tmp_ct_child_agreement_snapshot a LEFT JOIN tmp_ct_child_agreement_snapshot_max_endingyear b ON a.original_agreement_id = b.original_agreement_id ;

RAISE NOTICE 'PRE_CON_AGGR2';

CREATE TEMPORARY TABLE tmp_ct_master_agreements_rfed(original_master_agreement_id bigint, status_flag char(1), fiscal_year smallint, rfed_amount numeric(16,2)) DISTRIBUTED BY(original_master_agreement_id);

INSERT INTO tmp_ct_master_agreements_rfed
SELECT magse.original_agreement_id as original_master_agreement_id,magse.status_flag as status_flag, magse.fiscal_year as fiscal_year, sum(cagse.rfed_amount) as rfed_amount
FROM tmp_ct_child_agreement_snapshot_expanded_active cagse, tmp_ct_master_agreement_snapshot_expanded magse, history_agreement ha, ref_document_code dc
WHERE magse.status_flag = 'A' AND dc.document_code = 'MMA1' AND cagse.agreement_id = ha.agreement_id
AND ha.master_agreement_id = magse.original_agreement_id AND (cagse.fiscal_year = magse.fiscal_year OR ( magse.fiscal_year > cagse.fiscal_year AND cagse.fiscal_year = cagse.max_fiscal_year))
AND cagse.status_flag = magse.status_flag AND magse.document_code_id = dc.document_code_id
GROUP BY 1,2,3;

RAISE NOTICE 'PRE_CON_AGGR2.1';

INSERT INTO tmp_ct_master_agreements_rfed
SELECT magse.original_agreement_id as original_master_agreement_id,magse.status_flag as status_flag, magse.fiscal_year as fiscal_year, sum(ha.rfed_amount) as rfed_amount
FROM tmp_ct_master_agreement_snapshot_expanded magse, history_agreement ha, ref_document_code dc
WHERE magse.status_flag = 'R' AND dc.document_code = 'MMA1'
AND ha.master_agreement_id = magse.original_agreement_id  AND ha.original_version_flag = 'Y'
AND magse.document_code_id = dc.document_code_id
GROUP BY 1,2,3;

RAISE NOTICE 'PRE_CON_AGGR2.2';

INSERT INTO tmp_ct_master_agreements_rfed
SELECT magse.original_agreement_id as original_master_agreement_id,magse.status_flag as status_flag, magse.fiscal_year as fiscal_year, sum(ha.rfed_amount) as rfed_amount
FROM tmp_ct_master_agreement_snapshot_expanded magse, tmp_ct_child_agreement_snapshot_active ha, ref_document_code dc
WHERE magse.status_flag = 'A' AND dc.document_code = 'MA1'
AND ha.master_agreement_id = magse.original_agreement_id AND (magse.fiscal_year between ha.starting_year and ha.ending_year OR (magse.fiscal_year > ha.ending_year AND ha.ending_year = ha.max_ending_year))
AND magse.document_code_id = dc.document_code_id
GROUP BY 1,2,3;

RAISE NOTICE 'PRE_CON_AGGR2.3';

INSERT INTO tmp_ct_master_agreements_rfed
SELECT magse.original_agreement_id as original_master_agreement_id,magse.status_flag as status_flag, magse.fiscal_year as fiscal_year, sum(ha.rfed_amount) as rfed_amount
FROM tmp_ct_master_agreement_snapshot_expanded magse, history_agreement ha, ref_document_code dc
WHERE magse.status_flag = 'R' AND dc.document_code = 'MA1'
AND ha.master_agreement_id = magse.original_agreement_id AND ha.original_version_flag = 'Y'
AND magse.document_code_id = dc.document_code_id
GROUP BY 1,2,3;

UPDATE agreement_snapshot_expanded a
SET rfed_amount = b.rfed_amount
FROM tmp_ct_master_agreements_rfed b
WHERE a.original_agreement_id = b.original_master_agreement_id
AND a.fiscal_year = b.fiscal_year
AND a.status_flag = b.status_flag
AND a.master_agreement_yn = 'Y';

UPDATE agreement_snapshot_expanded
SET rfed_amount = 0
WHERE rfed_amount IS NULL
AND master_agreement_yn = 'Y';

/*
UPDATE agreement_snapshot X
SET rfed_amount = Y.rfed_amount
FROM
(select a.agreement_id, a.rfed_amount from  agreement_snapshot_expanded a,
(select agreement_id, max(fiscal_year) as fiscal_year from agreement_snapshot_expanded where master_agreement_yn = 'Y' group by 1) b
WHERE a.agreement_id = b.agreement_id AND a.fiscal_year = b.fiscal_year AND a.status_flag = 'A') Y
WHERE X.agreement_id = Y.agreement_id AND X.master_agreement_yn = 'Y' AND X.latest_flag = 'Y' ;
*/

UPDATE agreement_snapshot X
SET rfed_amount = Y.rfed_amount
FROM
(select master_agreement_id, sum(rfed_amount) as rfed_amount from agreement_snapshot where master_agreement_yn = 'N' and latest_flag = 'Y' group by 1) Y
WHERE X.original_agreement_id = Y.master_agreement_id AND X.master_agreement_yn = 'Y' AND X.latest_flag = 'Y' ;



UPDATE agreement_snapshot
SET rfed_amount = 0
WHERE rfed_amount IS NULL
AND master_agreement_yn = 'Y' AND latest_flag = 'Y' ;

RAISE NOTICE 'PRE_CON_AGGR3';

-- changes for agreement_snapshot_expanded_cy

	TRUNCATE agreement_snapshot_expanded_cy;


INSERT INTO agreement_snapshot_expanded_cy
SELECT  original_agreement_id ,
	agreement_id,
	fiscal_year ,
	description ,
	contract_number ,
	vendor_id ,
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
	master_agreement_id,
	minority_type_id,
	minority_type_name,
	master_agreement_yn,
	status_flag,
	scntrc_status
FROM
(SELECT original_agreement_id,
	agreement_id,
	generate_series(effective_begin_year,effective_end_year,1) as fiscal_year,
	description,
	contract_number,
	vendor_id,
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
	master_agreement_id,
	minority_type_id,
	minority_type_name,
	master_agreement_yn,
	'A' as status_flag,
	scntrc_status
FROM	agreement_snapshot_cy ) expanded_tbl WHERE fiscal_year between starting_year AND ending_year
AND fiscal_year >= 2010 AND (fiscal_year <= extract(year from now()::date) ) ;

INSERT INTO agreement_snapshot_expanded_cy
SELECT original_agreement_id,
	agreement_id,
	registered_year,
	description,
	contract_number,
	vendor_id,
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
	master_agreement_id,
	minority_type_id,
	minority_type_name,
	master_agreement_yn,
	'R' as status_flag,
	scntrc_status
FROM	agreement_snapshot_cy
WHERE registered_year between starting_year AND ending_year
AND registered_year >= 2010 ;

RAISE NOTICE 'PRE_CON_AGGR4';

CREATE TEMPORARY TABLE tmp_ct_child_agreement_snapshot_expanded_cy AS
SELECT * from agreement_snapshot_expanded_cy
WHERE master_agreement_yn = 'N';

CREATE TEMPORARY TABLE tmp_ct_master_agreement_snapshot_expanded_cy AS
SELECT * from agreement_snapshot_expanded_cy
WHERE master_agreement_yn = 'Y';


CREATE TEMPORARY TABLE tmp_ct_child_agreement_snapshot_expanded_maxyear_cy(original_agreement_id bigint,max_fiscal_year smallint)
DISTRIBUTED BY (original_agreement_id);

INSERT INTO tmp_ct_child_agreement_snapshot_expanded_maxyear_cy
SELECT original_agreement_id, max(fiscal_year)
FROM tmp_ct_child_agreement_snapshot_expanded_cy
WHERE status_flag = 'A'
GROUP BY 1;


CREATE TEMPORARY TABLE tmp_ct_child_agreement_snapshot_expanded_active_cy(agreement_id bigint, original_agreement_id bigint,rfed_amount numeric(16,2),status_flag character(1),fiscal_year smallint,max_fiscal_year smallint)
DISTRIBUTED BY (original_agreement_id);

INSERT INTO tmp_ct_child_agreement_snapshot_expanded_active_cy
SELECT agreement_id , a.original_agreement_id, rfed_amount, status_flag, fiscal_year, b.max_fiscal_year
FROM tmp_ct_child_agreement_snapshot_expanded_cy a LEFT JOIN tmp_ct_child_agreement_snapshot_expanded_maxyear_cy b ON a.original_agreement_id = b.original_agreement_id ;


CREATE TEMPORARY TABLE tmp_ct_child_agreement_snapshot_cy AS
SELECT * FROM agreement_snapshot_cy
WHERE master_agreement_yn = 'N';

CREATE TEMPORARY TABLE tmp_ct_child_agreement_snapshot_max_endingyear_cy(original_agreement_id bigint,max_ending_year smallint)
DISTRIBUTED BY (original_agreement_id);

INSERT INTO tmp_ct_child_agreement_snapshot_max_endingyear_cy
SELECT original_agreement_id, max(ending_year)
FROM tmp_ct_child_agreement_snapshot_cy
GROUP BY 1;

CREATE TEMPORARY TABLE tmp_ct_child_agreement_snapshot_active_cy(master_agreement_id bigint, rfed_amount numeric(16,2),starting_year smallint, ending_year smallint, max_ending_year smallint)
DISTRIBUTED BY (master_agreement_id);

INSERT INTO tmp_ct_child_agreement_snapshot_active_cy
SELECT master_agreement_id, rfed_amount,  starting_year, coalesce(ending_year,starting_year),  b.max_ending_year
FROM tmp_ct_child_agreement_snapshot_cy a LEFT JOIN tmp_ct_child_agreement_snapshot_max_endingyear_cy b ON a.original_agreement_id = b.original_agreement_id ;







TRUNCATE tmp_ct_master_agreements_rfed;

INSERT INTO tmp_ct_master_agreements_rfed
SELECT magse.original_agreement_id as original_master_agreement_id,magse.status_flag as status_flag, magse.fiscal_year as fiscal_year, sum(cagse.rfed_amount) as rfed_amount
FROM tmp_ct_child_agreement_snapshot_expanded_active_cy cagse, tmp_ct_master_agreement_snapshot_expanded_cy magse, history_agreement ha, ref_document_code dc
WHERE magse.status_flag = 'A' AND dc.document_code = 'MMA1' AND cagse.agreement_id = ha.agreement_id
AND ha.master_agreement_id = magse.original_agreement_id AND (cagse.fiscal_year = magse.fiscal_year OR ( magse.fiscal_year > cagse.fiscal_year AND cagse.fiscal_year = cagse.max_fiscal_year))
AND cagse.status_flag = magse.status_flag AND magse.document_code_id = dc.document_code_id
GROUP BY 1,2,3;

INSERT INTO tmp_ct_master_agreements_rfed
SELECT magse.original_agreement_id as original_master_agreement_id,magse.status_flag as status_flag, magse.fiscal_year as fiscal_year, sum(ha.rfed_amount) as rfed_amount
FROM tmp_ct_master_agreement_snapshot_expanded_cy magse, history_agreement ha, ref_document_code dc
WHERE magse.status_flag = 'R' AND dc.document_code = 'MMA1'
AND ha.master_agreement_id = magse.original_agreement_id AND ha.original_version_flag = 'Y'
AND magse.document_code_id = dc.document_code_id
GROUP BY 1,2,3;

INSERT INTO tmp_ct_master_agreements_rfed
SELECT magse.original_agreement_id as original_master_agreement_id,magse.status_flag as status_flag, magse.fiscal_year as fiscal_year, sum(ha.rfed_amount) as rfed_amount
FROM tmp_ct_master_agreement_snapshot_expanded_cy magse, tmp_ct_child_agreement_snapshot_active_cy ha, ref_document_code dc
WHERE magse.status_flag = 'A' AND dc.document_code = 'MA1'
AND ha.master_agreement_id = magse.original_agreement_id AND (magse.fiscal_year between ha.starting_year and ha.ending_year OR (magse.fiscal_year > ha.ending_year AND ha.ending_year = ha.max_ending_year))
AND magse.document_code_id = dc.document_code_id
GROUP BY 1,2,3;

INSERT INTO tmp_ct_master_agreements_rfed
SELECT magse.original_agreement_id as original_master_agreement_id,magse.status_flag as status_flag, magse.fiscal_year as fiscal_year, sum(ha.rfed_amount) as rfed_amount
FROM tmp_ct_master_agreement_snapshot_expanded_cy magse, history_agreement ha, ref_document_code dc
WHERE magse.status_flag = 'R' AND dc.document_code = 'MA1'
AND ha.master_agreement_id = magse.original_agreement_id AND ha.original_version_flag = 'Y'
AND magse.document_code_id = dc.document_code_id
GROUP BY 1,2,3;

UPDATE agreement_snapshot_expanded_cy a
SET rfed_amount = b.rfed_amount
FROM tmp_ct_master_agreements_rfed b
WHERE a.original_agreement_id = b.original_master_agreement_id
AND a.fiscal_year = b.fiscal_year
AND a.status_flag = b.status_flag
AND a.master_agreement_yn = 'Y';

UPDATE agreement_snapshot_expanded_cy
SET rfed_amount = 0
WHERE rfed_amount IS NULL
AND master_agreement_yn = 'Y';
	RAISE NOTICE 'PRE_CON_AGGR5';

	l_end_time := timeofday()::timestamp;

	INSERT INTO etl.etl_script_execution_status(job_id,script_name,completed_flag,start_time,end_time)
	VALUES(p_job_id_in,'etl.refreshContractsPreAggregateTables',1,l_start_time,l_end_time);

			RETURN 1;



EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in refreshContractsPreAggregateTables';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;

	l_end_time := timeofday()::timestamp;

	INSERT INTO etl.etl_script_execution_status(job_id,script_name,completed_flag,start_time,end_time,errno,errmsg)
	VALUES(p_job_id_in,'etl.refreshContractsPreAggregateTables',0,l_start_time,l_end_time,SQLSTATE,SQLERRM);

	RETURN 0;

END;
$$ language plpgsql;
