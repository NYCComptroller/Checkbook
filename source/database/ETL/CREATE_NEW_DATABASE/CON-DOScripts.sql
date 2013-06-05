-- *****************************************************************************
-- This file is part of the Checkbook NYC financial transparency software.
-- 
-- Copyright (C) 2012, 2013 New York City
-- 
-- This program is free software: you can redistribute it and/or modify
-- it under the terms of the GNU Affero General Public License as
-- published by the Free Software Foundation, either version 3 of the
-- License, or (at your option) any later version.
-- 
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU Affero General Public License for more details.
-- 
-- You should have received a copy of the GNU Affero General Public License
-- along with this program.  If not, see <http://www.gnu.org/licenses/>.
-- *****************************************************************************

/* Functions defined
	updateForeignKeysForDO1InHeader
	associateMAGToDO1	
	updateForeignKeysForDOInAccLine
	processCONDeliveryOrders
*/
CREATE OR REPLACE FUNCTION etl.updateForeignKeysForDO1InHeader(p_load_file_id_in bigint,p_load_id_in bigint) RETURNS INT AS $$
DECLARE
	l_count bigint;
BEGIN
	/* UPDATING FOREIGN KEY VALUES	FOR THE HEADER RECORD*/		
	
	CREATE TEMPORARY TABLE tmp_fk_values_do1 (uniq_id bigint, document_code_id smallint,agency_history_id smallint,
					      record_date_id int,source_created_date_id int,source_updated_date_id int,original_term_begin_date_id int,
					      original_term_end_date_id int,source_updated_fiscal_year smallint,source_updated_calendar_year smallint,
					      source_updated_calendar_year_id smallint,source_updated_fiscal_year_id smallint)
	DISTRIBUTED BY (uniq_id);
	
	-- FK:Document_Code_id
	
	INSERT INTO tmp_fk_values_do1(uniq_id,document_code_id)
	SELECT	a.uniq_id, b.document_code_id
	FROM etl.stg_con_do1_header a JOIN ref_document_code b ON a.doc_cd = b.document_code;
	
	-- FK:Agency_history_id
	
	INSERT INTO tmp_fk_values_do1(uniq_id,agency_history_id)
	SELECT	a.uniq_id, max(c.agency_history_id) as agency_history_id
	FROM etl.stg_con_do1_header a JOIN ref_agency b ON a.doc_dept_cd = b.agency_code
		JOIN ref_agency_history c ON b.agency_id = c.agency_id
	GROUP BY 1;
	
	CREATE TEMPORARY TABLE tmp_fk_values_do1_new_agencies(dept_cd varchar,uniq_id bigint)
	DISTRIBUTED BY (uniq_id);
	
	INSERT INTO tmp_fk_values_do1_new_agencies
	SELECT doc_dept_cd,MIN(b.uniq_id) as uniq_id
	FROM etl.stg_con_do1_header a join (SELECT uniq_id
						 FROM tmp_fk_values_do1
						 GROUP BY 1
						 HAVING max(agency_history_id) is null) b on a.uniq_id=b.uniq_id
	GROUP BY 1;

	RAISE NOTICE '1';
	
	TRUNCATE etl.ref_agency_id_seq;
	
	INSERT INTO etl.ref_agency_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_fk_values_do1_new_agencies;
	
	INSERT INTO ref_agency(agency_id,agency_code,agency_name,created_date,created_load_id,original_agency_name)
	SELECT a.agency_id,b.dept_cd,'<Unknown Agency>' as agency_name,now()::timestamp,p_load_id_in,'<Unknown Agency>' as original_agency_name
	FROM   etl.ref_agency_id_seq a JOIN tmp_fk_values_do1_new_agencies b ON a.uniq_id = b.uniq_id;

	GET DIAGNOSTICS l_count = ROW_COUNT;	

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'C',l_count, 'Number of records inserted into ref_agency from Delivery Order header');
	END IF;
	
	RAISE NOTICE '1.1';

	-- Generate the agency history id for history records
	
	TRUNCATE etl.ref_agency_history_id_seq;
	
	INSERT INTO etl.ref_agency_history_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_fk_values_do1_new_agencies;

	INSERT INTO ref_agency_history(agency_history_id,agency_id,agency_name,created_date,load_id)
	SELECT a.agency_history_id,b.agency_id,'<Unknown Agency>' as agency_name,now()::timestamp,p_load_id_in
	FROM   etl.ref_agency_history_id_seq a JOIN etl.ref_agency_id_seq b ON a.uniq_id = b.uniq_id;

	GET DIAGNOSTICS l_count = ROW_COUNT;	

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'C',l_count, 'Number of records inserted into ref_agency_history from Delivery Order header');
	END IF;
	
	RAISE NOTICE '1.3';
	INSERT INTO tmp_fk_values_do1(uniq_id,agency_history_id)
	SELECT	a.uniq_id, max(c.agency_history_id) 
	FROM etl.stg_con_do1_header a JOIN ref_agency b ON a.doc_dept_cd = b.agency_code
		JOIN ref_agency_history c ON b.agency_id = c.agency_id
		JOIN etl.ref_agency_history_id_seq d ON c.agency_history_id = d.agency_history_id
	GROUP BY 1	;	
	
	-- FK:record_date_id
	
	INSERT INTO tmp_fk_values_do1(uniq_id,record_date_id)
	SELECT	a.uniq_id, b.date_id
	FROM etl.stg_con_do1_header a JOIN ref_date b ON a.doc_rec_dt_dc = b.date;
	
	--FK:source_created_date_id
	
	INSERT INTO tmp_fk_values_do1(uniq_id,source_created_date_id)
	SELECT	a.uniq_id, b.date_id
	FROM etl.stg_con_do1_header a JOIN ref_date b ON a.doc_appl_crea_dt = b.date;
	
	--FK:source_updated_date_id
	
	INSERT INTO tmp_fk_values_do1(uniq_id,source_updated_date_id,source_updated_fiscal_year,source_updated_fiscal_year_id, source_updated_calendar_year,source_updated_calendar_year_id)
	SELECT	a.uniq_id, b.date_id,c.year_value,b.nyc_year_id,e.year_value,d.year_id
	FROM etl.stg_con_do1_header a JOIN ref_date b ON a.doc_appl_last_dt = b.date
		JOIN ref_year c ON b.nyc_year_id = c.year_id
		JOIN ref_month d ON b.calendar_month_id = d.month_id
		JOIN ref_year e ON d.year_id = e.year_id;
	
	--Updating con_do1_header with all the FK values
	
	UPDATE etl.stg_con_do1_header a
	SET	document_code_id = ct_table.document_code_id ,
		agency_history_id = ct_table.agency_history_id,		
		record_date_id = ct_table.record_date_id,
		source_created_date_id = ct_table.source_created_date_id,
		source_updated_date_id = ct_table.source_updated_date_id,
		source_updated_fiscal_year = ct_table.source_updated_fiscal_year,
		source_updated_fiscal_year_id = ct_table.source_updated_fiscal_year_id,		
		source_updated_calendar_year = ct_table.source_updated_calendar_year,
		source_updated_calendar_year_id = ct_table.source_updated_calendar_year_id					
	FROM	(SELECT uniq_id, max(document_code_id) as document_code_id ,
				 max(agency_history_id) as agency_history_id,				
				 max(record_date_id) as record_date_id,
				 max(source_created_date_id) as source_created_date_id,
				 max(source_updated_date_id) as source_updated_date_id,
				 max(source_updated_fiscal_year) as source_updated_fiscal_year,
				 max(source_updated_fiscal_year_id) as source_updated_fiscal_year_id,
				 max(source_updated_calendar_year) as source_updated_calendar_year, 
				 max(source_updated_calendar_year_id) as source_updated_calendar_year_id				 
		 FROM	tmp_fk_values_do1
		 GROUP BY 1) ct_table
	WHERE	a.uniq_id = ct_table.uniq_id;	
	
	RETURN 1;
EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in updateForeignKeysForDO1InHeader';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;	

	RETURN 0;
END;
$$ language plpgsql;

---------------------------------------------------------------------------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION etl.associateMAGToDO1(p_load_file_id_in bigint,p_load_id_in bigint) RETURNS INT AS $$
DECLARE
	l_worksite_col_array VARCHAR ARRAY[10];
	l_array_ctr smallint;
	l_fk_update int;
	l_count bigint;
BEGIN
						  
	-- Fetch all the contracts associated with MAG
	
	CREATE TEMPORARY TABLE tmp_do1_mag(uniq_id bigint, master_agreement_id bigint,mag_document_id varchar, 
				mag_agency_history_id smallint, mag_document_code_id smallint, mag_document_code varchar, mag_agency_code varchar )	
	DISTRIBUTED BY(uniq_id);
	
	INSERT INTO tmp_do1_mag
	SELECT uniq_id, 0 as master_agreement_id,
	       max(agree_doc_id),
	       max(d.agency_history_id) as mag_agency_history_id,
	       max(c.document_code_id),
	       max(c.document_code),
	       max(b.agency_code)
	FROM	etl.stg_con_do1_header a JOIN ref_agency b ON a.agree_doc_dept_cd = b.agency_code
		JOIN ref_document_code c ON a.agree_doc_cd = c.document_code
		JOIN ref_agency_history d ON b.agency_id = d.agency_id
	GROUP BY 1,2;		
		
	
	-- Identify the MAG Id
	
	CREATE TEMPORARY TABLE tmp_old_do1_mag_con(uniq_id bigint,master_agreement_id bigint, action_flag char(1), latest_flag char(1))
	DISTRIBUTED BY (uniq_id);
	
	INSERT INTO tmp_old_do1_mag_con
	SELECT uniq_id,
	       b.master_agreement_id
	FROM tmp_do1_mag a JOIN history_master_agreement b ON a.mag_document_id = b.document_id 
		JOIN ref_document_code f ON a.mag_document_code = f.document_code AND b.document_code_id = f.document_code_id
		JOIN ref_agency e ON a.mag_agency_code = e.agency_code 
		JOIN ref_agency_history c ON b.agency_history_id = c.agency_history_id AND e.agency_id = c.agency_id
	WHERE b.original_version_flag='Y';		
	
	UPDATE tmp_do1_mag a
	SET	master_agreement_id = b.master_agreement_id
	FROM	tmp_old_do1_mag_con b
	WHERE	a.uniq_id = b.uniq_id;
	
	-- Identify the MAG ones which have to be created
	
	CREATE TEMPORARY TABLE tmp_new_do1_mag_con(mag_document_code varchar,mag_agency_code varchar, mag_document_id varchar,
					   mag_agency_history_id smallint,mag_document_code_id smallint,uniq_id bigint);
	
	INSERT INTO tmp_new_do1_mag_con
	SELECT 	mag_document_code,mag_agency_code, mag_document_id,mag_agency_history_id,mag_document_code_id,min(uniq_id)
	FROM	tmp_do1_mag
	WHERE	master_agreement_id =0
	GROUP BY 1,2,3,4,5;
	
	TRUNCATE etl.agreement_id_seq;
	
	INSERT INTO etl.agreement_id_seq(uniq_id)
	SELECT uniq_id
	FROM  tmp_new_do1_mag_con;
	
	INSERT INTO history_master_agreement(master_agreement_id,document_code_id,agency_history_id,document_id,document_version,privacy_flag,created_load_id,created_date)
	SELECT  b.agreement_id, a.mag_document_code_id,a.mag_agency_history_id,a.mag_document_id,0 as document_version,'F' as privacy_flag,p_load_id_in,now()::timestamp
	FROM	tmp_new_do1_mag_con a JOIN etl.agreement_id_seq b ON a.uniq_id = b.uniq_id;
	
		
	GET DIAGNOSTICS l_count = ROW_COUNT;	

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'C',l_count, '# of records inserted into history_master_agreement');
	END IF;

	-- Updating the newly created MAG identifier.
	
	CREATE TEMPORARY TABLE tmp_new_do1_mag_con_2(uniq_id bigint,master_agreement_id bigint)
	DISTRIBUTED BY (uniq_id);
			
	INSERT INTO tmp_new_do1_mag_con_2
	SELECT a.uniq_id,d.agreement_id
	FROM   tmp_do1_mag a JOIN tmp_new_do1_mag_con b ON b.mag_document_code = a.mag_document_code
				     AND b.mag_agency_code = a.mag_agency_code
				     AND b.mag_document_id = a.mag_document_id
		JOIN etl.agreement_id_seq d ON b.uniq_id = d.uniq_id;
		
	UPDATE tmp_do1_mag a
	SET	master_agreement_id = b.master_agreement_id
	FROM	tmp_new_do1_mag_con_2 b
	WHERE	a.uniq_id = b.uniq_id
		AND a.master_agreement_id =0;
	 	
	 UPDATE etl.stg_con_do1_header a
	 SET	master_agreement_id = b.master_agreement_id
	 FROM	tmp_do1_mag b
	 WHERE	a.uniq_id = b.uniq_id;
	 
	RETURN 1;
EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in associateMAGToDO1';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;	

	RETURN 0;
END;
$$ language plpgsql;
----------------------------------------------------------------------------------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION etl.updateForeignKeysForDOInAccLine(p_load_file_id_in bigint,p_load_id_in bigint) RETURNS INT AS $$
DECLARE
	l_count bigint;
BEGIN
	-- UPDATING FK VALUES IN ETL.STG_CON_DO1_ACCOUNTING_LINE
	
	CREATE TEMPORARY TABLE tmp_fk_values_do1_acc_line(uniq_id bigint,fund_class_id smallint,agency_history_id smallint,
							department_history_id int, expenditure_object_history_id integer,budget_code_id integer)
	DISTRIBUTED BY (uniq_id);
	
	INSERT INTO tmp_fk_values_do1_acc_line(uniq_id)
	SELECT DISTINCT  uniq_id
	FROM etl.stg_con_do1_accounting_line;
	
	UPDATE etl.stg_con_do1_accounting_line 
	SET fund_cd = NULL
	WHERE fund_cd = '';
	
	UPDATE etl.stg_con_do1_accounting_line 
	SET dept_cd = NULL
	WHERE dept_cd = '';
	
	UPDATE etl.stg_con_do1_accounting_line 
	SET appr_cd = NULL
	WHERE appr_cd = '';
	
		
	UPDATE etl.stg_con_do1_accounting_line 
	SET obj_cd = NULL
	WHERE obj_cd = '';
	
	INSERT INTO tmp_fk_values_do1_acc_line(uniq_id)
	SELECT DISTINCT  uniq_id
	FROM etl.stg_con_do1_accounting_line;
	
	-- FK:fund_class_id

	INSERT INTO tmp_fk_values_do1_acc_line(uniq_id,fund_class_id)
	SELECT	a.uniq_id, b.fund_class_id
	FROM etl.stg_con_do1_accounting_line a JOIN ref_fund_class b ON COALESCE(a.fund_cd,'---') = b.fund_class_code;	

	CREATE TEMPORARY TABLE tmp_fk_values_do1_acc_line_new_fund_class(fund_class_code varchar,uniq_id integer)
	DISTRIBUTED BY (uniq_id);
	
	INSERT INTO tmp_fk_values_do1_acc_line_new_fund_class
	SELECT COALESCE(a.fund_cd,'---'),MIN(b.uniq_id) as uniq_id
	FROM etl.stg_con_do1_accounting_line a join (SELECT uniq_id
				    FROM tmp_fk_values_do1_acc_line
				    GROUP BY 1
				    HAVING max(fund_class_id) is null) b on a.uniq_id=b.uniq_id
	GROUP BY 1;
	
	TRUNCATE etl.ref_fund_class_id_seq;
	
	INSERT INTO etl.ref_fund_class_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_fk_values_do1_acc_line_new_fund_class;
	
	INSERT INTO ref_fund_class(fund_class_id,fund_class_code,fund_class_name,created_date,created_load_id)
	SELECT a.fund_class_id,COALESCE(b.fund_class_code,'---'),(CASE WHEN COALESCE(b.fund_class_code,'---') <> '---'  THEN '<Unknown Fund Class>' 
							ELSE '<Non-Applicable Fund Class>' END) as fund_class_name,
				now()::timestamp,p_load_id_in
	FROM   etl.ref_fund_class_id_seq a JOIN tmp_fk_values_do1_acc_line_new_fund_class b ON a.uniq_id = b.uniq_id;
	
	GET DIAGNOSTICS l_count = ROW_COUNT;	

	IF l_count > 0 THEN 
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'C',l_count, 'Number of records inserted into ref_fund_class from delivery order accounting lines');	
	END IF;	
	
	INSERT INTO tmp_fk_values_do1_acc_line(uniq_id,fund_class_id)
	SELECT	a.uniq_id, b.fund_class_id
	FROM etl.stg_con_do1_accounting_line a JOIN ref_fund_class b ON COALESCE(a.fund_cd,'---') = b.fund_class_code
		JOIN etl.ref_fund_class_id_seq c ON c.fund_class_id = b.fund_class_id;	
	
	-- FK:agency_history_id

	INSERT INTO tmp_fk_values_do1_acc_line(uniq_id,agency_history_id)
	SELECT	a.uniq_id, max(c.agency_history_id) 
	FROM etl.stg_con_do1_accounting_line a JOIN ref_agency b ON COALESCE(a.dept_cd,'---') = b.agency_code
		JOIN ref_agency_history c ON b.agency_id = c.agency_id
	GROUP BY 1	;	

	CREATE TEMPORARY TABLE tmp_fk_values_do1_acc_line_new_agencies(dept_cd varchar,uniq_id bigint)
	DISTRIBUTED BY (uniq_id);
	
	INSERT INTO tmp_fk_values_do1_acc_line_new_agencies
	SELECT COALESCE(dept_cd,'---') as dept_cd ,MIN(b.uniq_id) as uniq_id
	FROM etl.stg_con_do1_accounting_line a join (SELECT uniq_id
						 FROM tmp_fk_values_do1_acc_line
						 GROUP BY 1
						 HAVING max(agency_history_id) is null) b on a.uniq_id=b.uniq_id
	GROUP BY 1;
	
	TRUNCATE etl.ref_agency_id_seq;

	RAISE NOTICE '1.1';
	
	INSERT INTO etl.ref_agency_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_fk_values_do1_acc_line_new_agencies;
	
	INSERT INTO ref_agency(agency_id,agency_code,agency_name,created_date,created_load_id,original_agency_name)
	SELECT a.agency_id,COALESCE(b.dept_cd,'---'),(CASE WHEN COALESCE(b.dept_cd,'---')='---' THEN '<Non-Applicable Agency>' ELSE '<Unknown Agency>' END)as agency_name,
		now()::timestamp,p_load_id_in,'<Unknown Agency>' as original_agency_name
	FROM   etl.ref_agency_id_seq a JOIN tmp_fk_values_do1_acc_line_new_agencies b ON a.uniq_id = b.uniq_id;

	GET DIAGNOSTICS l_count = ROW_COUNT;	

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'C',l_count, 'Number of records inserted into ref_agency from delivery order accounting lines');
	END IF;
	
	RAISE NOTICE '1.2';
	
	-- Generate the agency history id for history records
	
	TRUNCATE etl.ref_agency_history_id_seq;
	
	INSERT INTO etl.ref_agency_history_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_fk_values_do1_acc_line_new_agencies;

	INSERT INTO ref_agency_history(agency_history_id,agency_id,agency_name,created_date,load_id)
	SELECT a.agency_history_id,b.agency_id,(CASE WHEN COALESCE(c.dept_cd,'---')='---' THEN '<Non-Applicable Agency>' ELSE '<Unknown Agency>' END),now()::timestamp,p_load_id_in
	FROM   etl.ref_agency_history_id_seq a JOIN etl.ref_agency_id_seq b ON a.uniq_id = b.uniq_id
		 JOIN tmp_fk_values_do1_acc_line_new_agencies c ON a.uniq_id = c.uniq_id;

	RAISE NOTICE '1.3';
	
	GET DIAGNOSTICS l_count = ROW_COUNT;	

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'C',l_count, 'Number of records inserted into ref_agency_history records inserted from delivery order accounting lines');
	END IF;
	
	INSERT INTO tmp_fk_values_do1_acc_line(uniq_id,agency_history_id)
	SELECT	a.uniq_id, max(c.agency_history_id) 
	FROM etl.stg_con_do1_accounting_line a JOIN ref_agency b ON COALESCE(a.dept_cd,'---') = b.agency_code
		JOIN ref_agency_history c ON b.agency_id = c.agency_id
		JOIN etl.ref_agency_history_id_seq d ON c.agency_history_id = d.agency_history_id
	GROUP BY 1	;	

	RAISE NOTICE '1.4';
	-- FK:department_history_id

	INSERT INTO tmp_fk_values_do1_acc_line(uniq_id,department_history_id)
	SELECT	a.uniq_id, max(c.department_history_id) 
	FROM etl.stg_con_do1_accounting_line a JOIN ref_department b ON COALESCE(a.appr_cd,'---------') = b.department_code AND a.fy_dc = b.fiscal_year
		JOIN ref_department_history c ON b.department_id = c.department_id
		JOIN ref_agency d ON a.dept_cd = d.agency_code AND b.agency_id = d.agency_id
		JOIN ref_fund_class e ON a.fund_cd = e.fund_class_code AND e.fund_class_id = b.fund_class_id
	GROUP BY 1	;		


	-- Generate the department id for new records

	CREATE TEMPORARY TABLE tmp_fk_values_do1_acc_line_new_dept(agency_id integer,appr_cd varchar,
						fund_class_id smallint,fiscal_year smallint, uniq_id bigint)
	DISTRIBUTED BY (uniq_id);
	
	INSERT INTO tmp_fk_values_do1_acc_line_new_dept
	SELECT c.agency_id,COALESCE(appr_cd,'---------'),e.fund_class_id,fy_dc,MIN(b.uniq_id) as uniq_id
	FROM etl.stg_con_do1_accounting_line a join (SELECT uniq_id
						 FROM tmp_fk_values_do1_acc_line
						 GROUP BY 1
						 HAVING max(department_history_id) IS NULL) b on a.uniq_id=b.uniq_id
		JOIN ref_agency c ON COALESCE(a.dept_cd,'---') = c.agency_code		
		JOIN ref_fund_class e ON COALESCE(a.fund_cd,'---') = e.fund_class_code
	GROUP BY 1,2,3,4;

	RAISE NOTICE '1.4';
							
		
	TRUNCATE etl.ref_department_id_seq;
	
	INSERT INTO etl.ref_department_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_fk_values_do1_acc_line_new_dept;

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
	FROM   etl.ref_department_id_seq a JOIN tmp_fk_values_do1_acc_line_new_dept b ON a.uniq_id = b.uniq_id;

	GET DIAGNOSTICS l_count = ROW_COUNT;	

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'C',l_count, 'Number of records inserted into ref_department from delivery order accounting lines');
	END IF;
	
	RAISE NOTICE '1.5';
	-- Generate the department history id for history records
	
	TRUNCATE etl.ref_department_history_id_seq;
	
	INSERT INTO etl.ref_department_history_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_fk_values_do1_acc_line_new_dept;

	INSERT INTO ref_department_history(department_history_id,department_id,
					   department_name,agency_id,fund_class_id,
					   fiscal_year,created_date,load_id)
	SELECT a.department_history_id,c.department_id,	
		(CASE WHEN COALESCE(b.appr_cd,'---------') <> '---------' THEN '<Unknown Department>'
		      ELSE 'Non-Applicable Department' END) as department_name,
		b.agency_id,b.fund_class_id,b.fiscal_year,now()::timestamp,p_load_id_in
	FROM   etl.ref_department_history_id_seq a JOIN tmp_fk_values_do1_acc_line_new_dept b ON a.uniq_id = b.uniq_id
		JOIN etl.ref_department_id_seq  c ON a.uniq_id = c.uniq_id ;

	GET DIAGNOSTICS l_count = ROW_COUNT;	

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'C',l_count, 'Number of records inserted into ref_department_history from delivery order accounting lines');
	END IF;

	RAISE NOTICE '1.6';
	
	INSERT INTO tmp_fk_values_do1_acc_line(uniq_id,department_history_id)
	SELECT	a.uniq_id, max(c.department_history_id) 
	FROM etl.stg_con_do1_accounting_line a JOIN ref_department b  ON COALESCE(a.appr_cd,'---------') = b.department_code AND a.fy_dc = b.fiscal_year
		JOIN ref_department_history c ON b.department_id = c.department_id
		JOIN ref_agency d ON COALESCE(a.dept_cd,'---') = d.agency_code AND b.agency_id = d.agency_id
		JOIN ref_fund_class e ON COALESCE(a.fund_cd,'---') = e.fund_class_code AND e.fund_class_id = b.fund_class_id
		JOIN etl.ref_department_history_id_seq f ON c.department_history_id = f.department_history_id
	GROUP BY 1	;	

	RAISE NOTICE '1.7';
	
	-- FK:expenditure_object_history_id

	INSERT INTO tmp_fk_values_do1_acc_line(uniq_id,expenditure_object_history_id)
	SELECT	a.uniq_id, max(c.expenditure_object_history_id) 
	FROM etl.stg_con_do1_accounting_line a JOIN ref_expenditure_object b ON COALESCE(a.obj_cd,'----') = b.expenditure_object_code AND a.fy_dc = b.fiscal_year
		JOIN ref_expenditure_object_history c ON b.expenditure_object_id = c.expenditure_object_id
	GROUP BY 1	;

	-- Generate the expenditure_object id for new records

	CREATE TEMPORARY TABLE tmp_fk_values_do1_acc_line_new_exp_object(obj_cd varchar,fiscal_year smallint,uniq_id bigint)
	DISTRIBUTED BY (uniq_id);
	
	INSERT INTO tmp_fk_values_do1_acc_line_new_exp_object
	SELECT COALESCE(a.obj_cd,'----') as obj_cd,fy_dc,MIN(a.uniq_id) as uniq_id
	FROM etl.stg_con_do1_accounting_line a join (SELECT uniq_id
						 FROM tmp_fk_values_do1_acc_line
						 GROUP BY 1
						 HAVING max(expenditure_object_history_id) is null) b on a.uniq_id=b.uniq_id
	GROUP BY 1,2;	
		
	TRUNCATE etl.ref_expenditure_object_id_seq;
	
	INSERT INTO etl.ref_expenditure_object_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_fk_values_do1_acc_line_new_exp_object;

	RAISE NOTICE '1.9';
	
	INSERT INTO ref_expenditure_object(expenditure_object_id,expenditure_object_code,
		expenditure_object_name,fiscal_year,created_date,created_load_id,original_expenditure_object_name)
	SELECT a.expenditure_object_id,b.obj_cd,
		(CASE WHEN b.obj_cd <> '----' THEN '<Unknown Expenditure Object>'
			ELSE '<Non-Applicable Expenditure Object>' END) as expenditure_object_name,
		b.fiscal_year,now()::timestamp,p_load_id_in,
		(CASE WHEN b.obj_cd <> '----' THEN '<Unknown Expenditure Object>'
			ELSE '<Non-Applicable Expenditure Object>' END) as original_expenditure_object_name
	FROM   etl.ref_expenditure_object_id_seq a JOIN tmp_fk_values_do1_acc_line_new_exp_object b ON a.uniq_id = b.uniq_id;

	GET DIAGNOSTICS l_count = ROW_COUNT;	

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'C',l_count, 'Number of records inserted into ref_expenditure_object from delivery order accounting lines');
	END IF;
	
	-- Generate the expenditure_object history id for history records
	
	TRUNCATE etl.ref_expenditure_object_history_id_seq;
	
	INSERT INTO etl.ref_expenditure_object_history_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_fk_values_do1_acc_line_new_exp_object;

	RAISE NOTICE '1.10';
	
	INSERT INTO ref_expenditure_object_history(expenditure_object_history_id,expenditure_object_id,fiscal_year,expenditure_object_name,created_date,load_id)
	SELECT a.expenditure_object_history_id,c.expenditure_object_id,b.fiscal_year,
		(CASE WHEN b.obj_cd <> '----' THEN '<Unknown Expenditure Object>'
			ELSE '<Non-Applicable Expenditure Object>' END) as expenditure_object_name,now()::timestamp,p_load_id_in
	FROM   etl.ref_expenditure_object_history_id_seq a JOIN tmp_fk_values_do1_acc_line_new_exp_object b ON a.uniq_id = b.uniq_id
		JOIN etl.ref_expenditure_object_id_seq c ON a.uniq_id = c.uniq_id;

	GET DIAGNOSTICS l_count = ROW_COUNT;	

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'C',l_count, 'Number of records inserted into ref_expenditure_object_history from delivery order accounting lines');
	END IF;

	RAISE NOTICE '1.11';
	
	INSERT INTO tmp_fk_values_do1_acc_line(uniq_id,expenditure_object_history_id)
	SELECT	a.uniq_id, max(c.expenditure_object_history_id) 
	FROM etl.stg_con_do1_accounting_line a JOIN ref_expenditure_object b ON COALESCE(a.obj_cd,'----') = b.expenditure_object_code AND a.fy_dc = b.fiscal_year
		JOIN ref_expenditure_object_history c ON b.expenditure_object_id = c.expenditure_object_id
		JOIN etl.ref_expenditure_object_history_id_seq d ON c.expenditure_object_history_id = d.expenditure_object_history_id
	GROUP BY 1	;
	
	
	-- FK:budget_code_id

	INSERT INTO tmp_fk_values_do1_acc_line(uniq_id,budget_code_id)
	SELECT	a.uniq_id, b.budget_code_id
	FROM etl.stg_con_do1_accounting_line a JOIN ref_budget_code b ON a.func_cd = b.budget_code AND a.fy_dc=b.fiscal_year
		JOIN ref_agency d ON a.dept_cd = d.agency_code AND b.agency_id = d.agency_id
		JOIN ref_fund_class e ON a.fund_cd = e.fund_class_code AND e.fund_class_id = b.fund_class_id;	
		
	
	UPDATE etl.stg_con_do1_accounting_line a
	SET	
		fund_class_id =ct_table.fund_class_id ,
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
		FROM	tmp_fk_values_do1_acc_line
		GROUP	BY 1) ct_table
	WHERE	a.uniq_id = ct_table.uniq_id;	

	RETURN 1;
	
EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in updateForeignKeysForDOInAccLine';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;	

	RETURN 0;
END;
$$ language plpgsql;

---------------------------------------------------------------------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION etl.processCONDeliveryOrders(p_load_file_id_in int,p_load_id_in bigint) RETURNS INT AS $$
DECLARE
	l_fk_update int;
	l_count bigint;
BEGIN
				      
	l_fk_update := etl.updateForeignKeysForDO1InHeader(p_load_file_id_in,p_load_id_in);

	RAISE NOTICE 'CON 1';
	
	IF l_fk_update = 1 THEN
		l_fk_update := etl.associateMAGToDO1(p_load_file_id_in,p_load_id_in);
	ELSE
		RETURN -1;
	END IF;

	RAISE NOTICE 'CON 3';
	
	IF l_fk_update = 1 THEN
		l_fk_update := etl.processvendor(p_load_file_id_in,p_load_id_in,'DO1');
	ELSE
		RETURN -1;
	END IF;

	RAISE NOTICE 'CON 4';
	
	IF l_fk_update = 1 THEN	 
		l_fk_update := etl.updateForeignKeysForDOInAccLine(p_load_file_id_in,p_load_id_in);	
	ELSE
		RETURN -1;
	END IF;


	IF l_fk_update <> 1 THEN
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
	CREATE TEMPORARY TABLE tmp_do1_con(uniq_id bigint, agency_history_id smallint,doc_id varchar,agreement_id bigint, action_flag char(1), 
					  latest_flag char(1),doc_vers_no smallint,privacy_flag char(1),old_agreement_ids varchar)
	DISTRIBUTED BY (uniq_id);
	
	INSERT INTO tmp_do1_con(uniq_id,agency_history_id,doc_id,doc_vers_no,privacy_flag,action_flag)
	SELECT uniq_id,agency_history_id,doc_id,doc_vers_no,'F' as privacy_flag,'I' as action_flag
	FROM etl.stg_con_do1_header;
	
	-- Identifying the versions of the agreements for update
	CREATE TEMPORARY TABLE tmp_old_do1_con(uniq_id bigint, agreement_id bigint);
	
	INSERT INTO tmp_old_do1_con
	SELECT  uniq_id,		
		b.agreement_id
	FROM etl.stg_con_do1_header a JOIN history_agreement b ON a.doc_id = b.document_id AND a.document_code_id = b.document_code_id AND a.doc_vers_no = b.document_version
		JOIN ref_agency_history c ON a.agency_history_id = c.agency_history_id
		JOIN ref_agency_history d ON b.agency_history_id = d.agency_history_id and c.agency_id = d.agency_id;				
	
	UPDATE tmp_do1_con a
	SET	agreement_id = b.agreement_id,
		action_flag = 'U'		
	FROM	tmp_old_do1_con b
	WHERE	a.uniq_id = b.uniq_id;

	RAISE NOTICE '1';
	
	-- Identifying the versions of the agreements for update
	TRUNCATE etl.agreement_id_seq;
	
	INSERT INTO etl.agreement_id_seq
	SELECT uniq_id
	FROM	tmp_do1_con
	WHERE	action_flag ='I' 
		AND COALESCE(agreement_id,0) =0 ;

	UPDATE tmp_do1_con a
	SET	agreement_id = b.agreement_id	
	FROM	etl.agreement_id_seq b
	WHERE	a.uniq_id = b.uniq_id;	

	RAISE NOTICE '2';
	
	INSERT INTO history_agreement(agreement_id,master_agreement_id,document_code_id,
				agency_history_id,document_id,document_version,
				tracking_number,record_date_id,budget_fiscal_year,
				document_fiscal_year,document_period,description,
				actual_amount_original,actual_amount,
				amendment_number,replacing_agreement_id,replaced_by_agreement_id,
				procurement_id,procurement_type_id,
				reason_modification,
				source_created_date_id,source_updated_date_id,document_function_code,
				vendor_history_id,vendor_preference_level,
				document_name,original_term_begin_date_id,
				original_term_end_date_id,privacy_flag,created_load_id,created_date,
				source_updated_fiscal_year,source_updated_fiscal_year_id, source_updated_calendar_year,
		   		source_updated_calendar_year_id,contract_number,brd_awd_no,rfed_amount_original,rfed_amount)
	SELECT	d.agreement_id,a.master_agreement_id,a.document_code_id,
		a.agency_history_id,a.doc_id,a.doc_vers_no,
		a.trkg_no,a.record_date_id,a.doc_bfy,
		a.doc_fy_dc,a.doc_per_dc,a.doc_dscr,
		a.doc_actu_am, (CASE WHEN a.doc_actu_am IS NULL THEN 0 ELSE a.doc_actu_am END) as actual_amount,
		a.amend_no,0 as replacing_agreement_id,0 as replaced_by_agreement_id,
		a.prcu_id,a.prcu_typ_id,
		a.reas_mod_dc,
		a.source_created_date_id,a.source_updated_date_id,a.doc_func_cd,
		b.vendor_history_id,b.vend_pref_lvl,
		a.doc_nm,a.original_term_begin_date_id,
		a.original_term_end_date_id,d.privacy_flag,p_load_id_in,now()::timestamp,
		source_updated_fiscal_year,source_updated_fiscal_year_id, source_updated_calendar_year,
		source_updated_calendar_year_id,a.doc_cd||a.doc_dept_cd||a.doc_id as contract_number,a.brd_awd_no,a.rfed_am, (CASE WHEN a.rfed_am IS NULL THEN 0 ELSE a.rfed_am END) as rfed_amount
	FROM	etl.stg_con_do1_header a JOIN etl.stg_con_do1_vendor b ON a.doc_cd = b.doc_cd AND a.doc_dept_cd = b.doc_dept_cd 
					     AND a.doc_id = b.doc_id AND a.doc_vers_no = b.doc_vers_no
					 JOIN tmp_do1_con d ON a.uniq_id = d.uniq_id
	WHERE   action_flag='I';				 

	GET DIAGNOSTICS l_count = ROW_COUNT;
	IF l_count > 0 THEN 
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,document_type,num_transactions,description)
		VALUES(p_load_file_id_in,'C','DO1',l_count,'# of records inserted into history_agreement');
	END IF;	
	
	/* Updates */
	CREATE TEMPORARY TABLE tmp_con_do1_update AS
	SELECT d.agreement_id,a.master_agreement_id,a.document_code_id,
			a.agency_history_id,a.doc_id,a.doc_vers_no,
			a.trkg_no,a.record_date_id,a.doc_bfy,
			a.doc_fy_dc,a.doc_per_dc,a.doc_dscr,
			a.doc_actu_am,
			a.amend_no,0 as replacing_agreement_id,0 as replaced_by_agreement_id,
			a.prcu_id,a.prcu_typ_id,
			a.reas_mod_dc,
			a.source_created_date_id,a.source_updated_date_id,a.doc_func_cd,
			b.vendor_history_id,b.vend_pref_lvl,
			a.doc_nm,a.original_term_begin_date_id,
			a.original_term_end_date_id,d.privacy_flag,p_load_id_in as load_id,now()::timestamp as updated_date,
			source_updated_fiscal_year,source_updated_fiscal_year_id, source_updated_calendar_year,
			source_updated_calendar_year_id,a.brd_awd_no, a.rfed_am	
		FROM	etl.stg_con_do1_header a JOIN etl.stg_con_do1_vendor b ON a.doc_cd = b.doc_cd AND a.doc_dept_cd = b.doc_dept_cd 
						     AND a.doc_id = b.doc_id AND a.doc_vers_no = b.doc_vers_no
						 JOIN tmp_do1_con d ON a.uniq_id = d.uniq_id
	WHERE   action_flag='U'
	DISTRIBUTED BY (agreement_id);				 

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
		actual_amount = (CASE WHEN b.doc_actu_am IS NULL THEN 0 ELSE b.doc_actu_am END) ,
		amendment_number = b.amend_no,
		replacing_agreement_id = b.replacing_agreement_id,
		replaced_by_agreement_id = b.replaced_by_agreement_id,
		procurement_id = b.prcu_id,
		procurement_type_id = b.prcu_typ_id,
		reason_modification = b.reas_mod_dc,
		source_created_date_id = b.source_created_date_id,
		source_updated_date_id = b.source_updated_date_id,
		document_function_code = b.doc_func_cd,		
		vendor_history_id = b.vendor_history_id,
		vendor_preference_level = b.vend_pref_lvl,				
		document_name = b.doc_nm,
		original_term_begin_date_id = b.original_term_begin_date_id,
		original_term_end_date_id = b.original_term_end_date_id,
		privacy_flag = b.privacy_flag,
		updated_load_id = b.load_id,		
		updated_date = b.updated_date,
		source_updated_fiscal_year = b.source_updated_fiscal_year,
		source_updated_fiscal_year_id = b.source_updated_fiscal_year_id,
		source_updated_calendar_year = b.source_updated_calendar_year,
		source_updated_calendar_year_id = b.source_updated_calendar_year_id,
		brd_awd_no = b.brd_awd_no,
		rfed_amount_original = b.rfed_am,
		rfed_amount = (CASE WHEN b.rfed_am IS NULL THEN 0 ELSE b.rfed_am END)
	FROM	tmp_con_do1_update b
	WHERE	a.agreement_id = b.agreement_id;
	
	GET DIAGNOSTICS l_count = ROW_COUNT;
	
	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,document_type,num_transactions,description)
		VALUES(p_load_file_id_in,'C','DO1',l_count,'# of records updated in history_agreement');
	END IF;	


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
	FROM	etl.stg_con_do1_header a JOIN etl.stg_con_do1_accounting_line b ON a.doc_cd = b.doc_cd AND a.doc_dept_cd = b.doc_dept_cd 
					     AND a.doc_id = b.doc_id AND a.doc_vers_no = b.doc_vers_no
					     JOIN tmp_do1_con d ON a.uniq_id = d.uniq_id
	WHERE   action_flag = 'I';

	GET DIAGNOSTICS l_count = ROW_COUNT;
	
	IF l_count > 0 THEN
			INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,document_type,num_transactions,description)
			VALUES(p_load_file_id_in,'C','DO1',l_count,'# of records inserted into history_agreement_accounting_line');
		END IF;	



	RAISE NOTICE '6';
	-- Identify the agreement accounting lines which need to be deleted/updated

	CREATE TEMPORARY TABLE tmp_do1_acc_lines_actions(agreement_id bigint, commodity_line_number integer,line_number integer,action_flag char(1),uniq_id bigint)
	DISTRIBUTED BY (agreement_id);
	
	INSERT INTO tmp_do1_acc_lines_actions
	SELECT  COALESCE(latest_tbl.agreement_id,old_tbl.agreement_id) as agreement_id,
		COALESCE(latest_tbl.doc_comm_ln_no, old_tbl.commodity_line_number) as commodity_line_number,
		COALESCE(latest_tbl.doc_actg_ln_no, old_tbl.line_number) as line_number,
		(CASE WHEN latest_tbl.agreement_id = old_tbl.agreement_id THEN 'U'
		      WHEN old_tbl.agreement_id IS NULL THEN 'I'
		      WHEN latest_tbl.agreement_id IS NULL THEN 'D' END) as action_flag	,
		      uniq_id
	FROM	      
		(SELECT a.agreement_id,c.doc_comm_ln_no,c.doc_actg_ln_no,c.uniq_id
		FROM   tmp_do1_con a JOIN etl.stg_con_do1_header b ON a.uniq_id = b.uniq_id
			JOIN etl.stg_con_do1_accounting_line c ON c.doc_cd = b.doc_cd AND c.doc_dept_cd = b.doc_dept_cd 
						     AND c.doc_id = b.doc_id AND c.doc_vers_no = b.doc_vers_no
		WHERE   a.action_flag ='U'
		order by 1,2,3 ) latest_tbl				     
		FULL OUTER JOIN (SELECT e.agreement_id,e.commodity_line_number,e.line_number 
			    FROM   history_agreement_accounting_line e JOIN tmp_do1_con f ON e.agreement_id = f.agreement_id ) old_tbl ON latest_tbl.agreement_id = old_tbl.agreement_id 
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
	FROM	etl.stg_con_do1_header a JOIN etl.stg_con_do1_accounting_line b ON a.doc_cd = b.doc_cd AND a.doc_dept_cd = b.doc_dept_cd 
					     AND a.doc_id = b.doc_id AND a.doc_vers_no = b.doc_vers_no
					     JOIN tmp_do1_con d ON a.uniq_id = d.uniq_id
					     JOIN tmp_do1_acc_lines_actions e ON d.agreement_id = e.agreement_id AND b.doc_actg_ln_no = e.line_number AND b.doc_comm_ln_no = e.commodity_line_number
	WHERE   d.action_flag = 'U' AND e.action_flag='I';

	GET DIAGNOSTICS l_count = ROW_COUNT;
	
	IF l_count > 0 THEN
				INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,document_type,num_transactions,description)
				VALUES(p_load_file_id_in,'C','DO1',l_count,'# of records inserted into history_agreement_accounting_line');
			END IF;	

		
	RAISE NOTICE '8';
	
	INSERT INTO deleted_agreement_accounting_line
	SELECT a.*,now()::timestamp, p_load_id_in as deleted_load_id
	FROM   history_agreement_accounting_line a JOIN tmp_do1_acc_lines_actions b  ON a.agreement_id = b.agreement_id AND a.line_number = b.line_number
		JOIN tmp_do1_con c ON a.agreement_id = c.agreement_id
	WHERE	b.action_flag = 'D' AND c.action_flag='U';
	
	RAISE NOTICE '9';
	
	DELETE FROM ONLY history_agreement_accounting_line a 
	USING tmp_do1_acc_lines_actions b , tmp_do1_con c
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
		line_amount = (CASE WHEN b.ln_am IS NULL THEN 0 ELSE b.ln_am END),
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
	FROM   etl.stg_con_do1_header a, etl.stg_con_do1_accounting_line b,
		tmp_do1_con d,tmp_do1_acc_lines_actions e			      	      
	WHERE  d.action_flag = 'U' AND e.action_flag='U'
	       AND a.doc_cd = b.doc_cd AND a.doc_dept_cd = b.doc_dept_cd 
	       AND a.doc_id = b.doc_id AND a.doc_vers_no = b.doc_vers_no
	       AND a.uniq_id = d.uniq_id
	       AND d.agreement_id = e.agreement_id AND b.uniq_id = e.uniq_id
	       AND f.agreement_id = d.agreement_id AND f.line_number = e.line_number AND f.agreement_id = e.agreement_id AND f.commodity_line_number = e.commodity_line_number;	       

	GET DIAGNOSTICS l_count = ROW_COUNT;
	       
	IF l_count > 0 THEN
					INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,document_type,num_transactions,description)
					VALUES(p_load_file_id_in,'C','DO1',l_count,'# of records updated in history_agreement_accounting_line');
			END IF;	


	RAISE NOTICE '11';
	
	-- For now not processing commodities
	/*
	DELETE FROM history_agreement_commodity a 
	USING tmp_do1_con b 
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
	FROM	etl.stg_con_do1_header a JOIN etl.stg_con_do1_commodity b ON a.doc_cd = b.doc_cd AND a.doc_dept_cd = b.doc_dept_cd 
						     AND a.doc_id = b.doc_id AND a.doc_vers_no = b.doc_vers_no
						     JOIN tmp_do1_con d ON a.uniq_id = d.uniq_id;
	
	*/
	RETURN 1;
	
EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in processCONDeliveryOrders';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;	

	RETURN 0;
END;
$$ language plpgsql;