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

set search_path=public;
/* Functions defined
	updateForeignKeysForFMSInHeader
	updateForeignKeysForFMSVendors
	updateForeignKeysForFMSInAccLine		
	associateCONToFMS
	processFMS
	refreshFactsForFMS
*/
CREATE OR REPLACE FUNCTION etl.updateForeignKeysForFMSInHeader(p_load_file_id_in bigint,p_load_id_in bigint) RETURNS INT AS $$
DECLARE
	l_count bigint;
BEGIN
	/* UPDATING FOREIGN KEY VALUES	FOR THE HEADER RECORD*/		
	
	CREATE TEMPORARY TABLE tmp_fk_fms_values (uniq_id bigint, document_code_id smallint,agency_history_id smallint,record_date_id integer,
						check_eft_issued_date_id integer,check_eft_record_date_id integer, expenditure_status_id smallint,
						expenditure_cancel_type_id smallint, expenditure_cancel_reason_id smallint,check_eft_issued_nyc_year_id smallint)
	DISTRIBUTED BY (uniq_id);
	
	-- FK:Document_Code_id
	
	INSERT INTO tmp_fk_fms_values(uniq_id,document_code_id)
	SELECT	a.uniq_id, b.document_code_id
	FROM etl.stg_fms_header a JOIN ref_document_code b ON a.doc_cd = b.document_code;
	
	-- FK:Agency_history_id
	
	INSERT INTO tmp_fk_fms_values(uniq_id,agency_history_id)
	SELECT	a.uniq_id, max(c.agency_history_id) as agency_history_id
	FROM etl.stg_fms_header a JOIN ref_agency b ON a.doc_dept_cd = b.agency_code
		 JOIN ref_agency_history c ON b.agency_id = c.agency_id
	GROUP BY 1;
	
	CREATE TEMPORARY TABLE tmp_fk_fms_values_new_agencies(dept_cd varchar,uniq_id bigint)
	DISTRIBUTED BY (uniq_id);
	
	INSERT INTO tmp_fk_fms_values_new_agencies
	SELECT doc_dept_cd,MIN(b.uniq_id) as uniq_id
	FROM etl.stg_fms_header a join (SELECT uniq_id
						 FROM tmp_fk_fms_values
						 GROUP BY 1
						 HAVING max(agency_history_id) is null) b on a.uniq_id=b.uniq_id
	GROUP BY 1;

	RAISE NOTICE '1';
	
	TRUNCATE etl.ref_agency_id_seq;
	
	INSERT INTO etl.ref_agency_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_fk_fms_values_new_agencies;
	
	INSERT INTO ref_agency(agency_id,agency_code,agency_name,created_date,created_load_id,original_agency_name)
	SELECT a.agency_id,b.dept_cd,'<Unknown Agency>' as agency_name,now()::timestamp,p_load_id_in,'<Unknown Agency>' as original_agency_name
	FROM   etl.ref_agency_id_seq a JOIN tmp_fk_fms_values_new_agencies b ON a.uniq_id = b.uniq_id;

	GET DIAGNOSTICS l_count = ROW_COUNT;	

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'F',l_count, 'Number of records inserted into ref_agency from disbursements header');
	END IF;
	
	RAISE NOTICE '1.1';

	-- Generate the agency history id for history records
	
	TRUNCATE etl.ref_agency_history_id_seq;
	
	INSERT INTO etl.ref_agency_history_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_fk_fms_values_new_agencies;

	INSERT INTO ref_agency_history(agency_history_id,agency_id,agency_name,created_date,load_id)
	SELECT a.agency_history_id,b.agency_id,'<Unknown Agency>' as agency_name,now()::timestamp,p_load_id_in
	FROM   etl.ref_agency_history_id_seq a JOIN etl.ref_agency_id_seq b ON a.uniq_id = b.uniq_id;

	GET DIAGNOSTICS l_count = ROW_COUNT;	

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'C',l_count, 'Number of records into ref_agency_history from disbursements header');
	END IF;
	
	RAISE NOTICE '1.3';
	
	INSERT INTO tmp_fk_fms_values(uniq_id,agency_history_id)
	SELECT	a.uniq_id, max(c.agency_history_id) 
	FROM etl.stg_fms_header a JOIN ref_agency b ON a.doc_dept_cd = b.agency_code
		JOIN ref_agency_history c ON b.agency_id = c.agency_id
		JOIN etl.ref_agency_history_id_seq d ON c.agency_history_id = d.agency_history_id
	GROUP BY 1	;	
	
	-- FK:record_date_id
	
	INSERT INTO tmp_fk_fms_values(uniq_id,record_date_id)
	SELECT	a.uniq_id, b.date_id
	FROM etl.stg_fms_header a JOIN ref_date b ON a.doc_rec_dt_dc = b.date;
	
	-- FK:check_eft_issued_date_id
	
	INSERT INTO tmp_fk_fms_values(uniq_id,check_eft_issued_date_id)
	SELECT	a.uniq_id, b.date_id
	FROM etl.stg_fms_header a JOIN ref_date b ON a.chk_eft_iss_dt = b.date;
	
	-- FK:check_eft_issued_nyc_year_id
	
	INSERT INTO tmp_fk_fms_values(uniq_id,check_eft_issued_nyc_year_id)
	SELECT	a.uniq_id, b.nyc_year_id
	FROM etl.stg_fms_header a JOIN ref_date b ON a.chk_eft_iss_dt = b.date;
	
	-- FK:check_eft_record_date_id
	
	INSERT INTO tmp_fk_fms_values(uniq_id,check_eft_record_date_id)
	SELECT	a.uniq_id, b.date_id
	FROM etl.stg_fms_header a JOIN ref_date b ON a.chk_eft_rec_dt = b.date;

	raise notice '1';
		
	UPDATE etl.stg_fms_header a
	SET	document_code_id = ct_table.document_code_id ,
		agency_history_id = ct_table.agency_history_id,		
		record_date_id = ct_table.record_date_id,
		check_eft_issued_date_id = ct_table.check_eft_issued_date_id, 
		check_eft_record_date_id = ct_table.check_eft_record_date_id,
		check_eft_issued_nyc_year_id = ct_table.check_eft_issued_nyc_year_id
	FROM	(SELECT uniq_id, max(document_code_id) as document_code_id ,
				 max(agency_history_id) as agency_history_id,max(record_date_id) as record_date_id,
				 max(check_eft_issued_date_id) as check_eft_issued_date_id, max(check_eft_record_date_id) as check_eft_record_date_id,
				max(check_eft_issued_nyc_year_id) as check_eft_issued_nyc_year_id
		 FROM	tmp_fk_fms_values
		 GROUP BY 1) ct_table
	WHERE	a.uniq_id = ct_table.uniq_id;	
	
	
	RETURN 1;
EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in updateForeignKeysForFMSInHeader';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;	

	RETURN 0;
END;
$$ language plpgsql;

---------------------------------------------------------------------------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION etl.updateForeignKeysForFMSInAccLine(p_load_file_id_in bigint,p_load_id_in bigint) RETURNS INT AS $$
DECLARE
	l_count bigint;
	
BEGIN
	-- UPDATING FK VALUES IN ETL.STG_FMS_ACCOUNTING_LINE
	
	UPDATE etl.stg_fms_accounting_line 
	SET loc_cd = NULL
	WHERE loc_cd = '';
	
	UPDATE etl.stg_fms_accounting_line 
	SET fcls_cd = NULL
	WHERE fcls_cd = '';
	
	UPDATE etl.stg_fms_accounting_line 
	SET dept_cd = NULL
	WHERE dept_cd = '';
	
	UPDATE etl.stg_fms_accounting_line 
	SET appr_cd = NULL
	WHERE appr_cd = '';
	
	UPDATE etl.stg_fms_accounting_line 
	SET obj_cd = NULL
	WHERE obj_cd = '';
	
	
	CREATE TEMPORARY TABLE tmp_fk_values_fms_acc_line(uniq_id bigint,fund_class_id smallint,agency_history_id smallint,
							department_history_id int, expenditure_object_history_id integer,budget_code_id integer,
							fund_id smallint, location_history_id int, masked_agency_history_id smallint, masked_department_history_id int)
	DISTRIBUTED BY (uniq_id);
	
	INSERT INTO tmp_fk_values_fms_acc_line(uniq_id)
	SELECT DISTINCT  uniq_id
	FROM etl.stg_fms_accounting_line;
	
	-- FK:fund_class_id

	INSERT INTO tmp_fk_values_fms_acc_line(uniq_id,fund_class_id)
	SELECT	a.uniq_id, b.fund_class_id
	FROM etl.stg_fms_accounting_line a JOIN ref_fund_class b ON a.fcls_cd = b.fund_class_code;	

	CREATE TEMPORARY TABLE tmp_fk_values_fms_acc_line_new_fund_class(fund_class_code varchar,uniq_id integer)
	DISTRIBUTED BY (uniq_id);
	
	INSERT INTO tmp_fk_values_fms_acc_line_new_fund_class
	SELECT COALESCE(a.fcls_cd,'---'),MIN(b.uniq_id) as uniq_id
	FROM etl.stg_fms_accounting_line a join (SELECT uniq_id
				    FROM tmp_fk_values_fms_acc_line
				    GROUP BY 1
				    HAVING max(fund_class_id) is null) b on a.uniq_id=b.uniq_id
	GROUP BY 1;
	
	TRUNCATE etl.ref_fund_class_id_seq;
	
	INSERT INTO etl.ref_fund_class_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_fk_values_fms_acc_line_new_fund_class;
	
	INSERT INTO ref_fund_class(fund_class_id,fund_class_code,fund_class_name,created_date,created_load_id)
	SELECT a.fund_class_id,COALESCE(b.fund_class_code,'---'),(CASE WHEN COALESCE(b.fund_class_code,'---') <> '---'  THEN '<Unknown Fund Class>' 
							ELSE '<Non-Applicable Fund Class>' END) as fund_class_name,
				now()::timestamp,p_load_id_in
	FROM   etl.ref_fund_class_id_seq a JOIN tmp_fk_values_fms_acc_line_new_fund_class b ON a.uniq_id = b.uniq_id;
	
	GET DIAGNOSTICS l_count = ROW_COUNT;	

	IF l_count > 0 THEN 
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'F',l_count, 'Number of records inserted into ref_fund_class from disbursements accounting lines');	
	END IF;	
	
	
		INSERT INTO tmp_fk_values_fms_acc_line(uniq_id,fund_class_id)
	SELECT	a.uniq_id, b.fund_class_id 
	FROM etl.stg_fms_accounting_line a JOIN ref_fund_class b ON COALESCE(a.fcls_cd,'---') = b.fund_class_code
		JOIN etl.ref_fund_class_id_seq c ON c.fund_class_id = b.fund_class_id ;	
		
		
	-- FK:agency_history_id

	INSERT INTO tmp_fk_values_fms_acc_line(uniq_id,agency_history_id)
	SELECT	a.uniq_id, max(c.agency_history_id) 
	FROM etl.stg_fms_accounting_line a JOIN ref_agency b ON coalesce(a.dept_cd,'---') = b.agency_code
		JOIN ref_agency_history c ON b.agency_id = c.agency_id
	GROUP BY 1	;	

	RAISE NOTICE '1';
	
	CREATE TEMPORARY TABLE tmp_fk_values_fms_acc_line_new_agencies(dept_cd varchar,uniq_id bigint)
	DISTRIBUTED BY (uniq_id);
	
	INSERT INTO tmp_fk_values_fms_acc_line_new_agencies
	SELECT coalesce(dept_cd,'---'),MIN(b.uniq_id) as uniq_id
	FROM etl.stg_fms_accounting_line a join (SELECT uniq_id
						 FROM tmp_fk_values_fms_acc_line
						 GROUP BY 1
						 HAVING max(agency_history_id) is null) b on a.uniq_id=b.uniq_id
	GROUP BY 1;
	
	TRUNCATE etl.ref_agency_id_seq;

	RAISE NOTICE '1.1';
	
	INSERT INTO etl.ref_agency_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_fk_values_fms_acc_line_new_agencies;
	
	INSERT INTO ref_agency(agency_id,agency_code,agency_name,created_date,created_load_id,original_agency_name)
	SELECT a.agency_id,b.dept_cd,'<Unknown Agency>' as agency_name,now()::timestamp,p_load_id_in,'<Unknown Agency>' as original_agency_name
	FROM   etl.ref_agency_id_seq a JOIN tmp_fk_values_fms_acc_line_new_agencies b ON a.uniq_id = b.uniq_id;

	GET DIAGNOSTICS l_count = ROW_COUNT;	

	IF l_count > 0 THEN 
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'F',l_count, 'Number of records inserted into ref_agency from disbursements accounting lines');	
	END IF;	
	
	RAISE NOTICE '1.2';
	
	-- Generate the agency history id for history records
	
	TRUNCATE etl.ref_agency_history_id_seq;
	
	INSERT INTO etl.ref_agency_history_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_fk_values_fms_acc_line_new_agencies;

	INSERT INTO ref_agency_history(agency_history_id,agency_id,agency_name,created_date,load_id)
	SELECT a.agency_history_id,b.agency_id,'<Unknown Agency>' as agency_name,now()::timestamp,p_load_id_in
	FROM   etl.ref_agency_history_id_seq a JOIN etl.ref_agency_id_seq b ON a.uniq_id = b.uniq_id;

	GET DIAGNOSTICS l_count = ROW_COUNT;	

	IF l_count > 0 THEN 
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'F',l_count, 'Number of records inserted into ref_agency_history from disbursements accounting lines');	
	END IF;	
	
	INSERT INTO tmp_fk_values_fms_acc_line(uniq_id,agency_history_id)
	SELECT	a.uniq_id, max(c.agency_history_id) 
	FROM etl.stg_fms_accounting_line a JOIN ref_agency b ON coalesce(a.dept_cd,'---') = b.agency_code
		JOIN ref_agency_history c ON b.agency_id = c.agency_id
		JOIN etl.ref_agency_history_id_seq d ON c.agency_history_id = d.agency_history_id
	GROUP BY 1	;	

	RAISE NOTICE '1.3';
	
	-- FK:department_history_id

	INSERT INTO tmp_fk_values_fms_acc_line(uniq_id,department_history_id)
	SELECT	a.uniq_id, max(c.department_history_id) 
	FROM etl.stg_fms_accounting_line a JOIN ref_department b ON coalesce(a.appr_cd,'---------') = b.department_code AND a.fy_dc = b.fiscal_year
		JOIN ref_department_history c ON b.department_id = c.department_id
		JOIN ref_agency d ON a.dept_cd = d.agency_code AND b.agency_id = d.agency_id
		JOIN ref_fund_class e ON a.fcls_cd = e.fund_class_code AND e.fund_class_id = b.fund_class_id
	GROUP BY 1;
	
	CREATE TEMPORARY TABLE tmp_fk_values_fms_acc_line_new_dept(agency_history_id integer,agency_id integer,appr_cd varchar,
						fund_class_id smallint,fiscal_year smallint, uniq_id bigint)
	DISTRIBUTED BY (uniq_id);
	
	INSERT INTO tmp_fk_values_fms_acc_line_new_dept
	SELECT d.agency_history_id,c.agency_id,coalesce(appr_cd,'---------'),e.fund_class_id,fy_dc,MIN(b.uniq_id) as uniq_id
	FROM etl.stg_fms_accounting_line a join (SELECT uniq_id
						 FROM tmp_fk_values_fms_acc_line
						 GROUP BY 1
						 HAVING max(department_history_id) IS NULL) b on a.uniq_id=b.uniq_id
		JOIN ref_agency c ON a.dept_cd = c.agency_code
		JOIN ref_agency_history d ON c.agency_id = d.agency_id
		JOIN ref_fund_class e ON a.fcls_cd = e.fund_class_code
	GROUP BY 1,2,3,4,5;

	RAISE NOTICE '1.4';
						
	-- Generate the department id for new records
		
	TRUNCATE etl.ref_department_id_seq;
	
	INSERT INTO etl.ref_department_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_fk_values_fms_acc_line_new_dept;

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
	FROM   etl.ref_department_id_seq a JOIN tmp_fk_values_fms_acc_line_new_dept b ON a.uniq_id = b.uniq_id;

	GET DIAGNOSTICS l_count = ROW_COUNT;	

	IF l_count > 0 THEN 
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'F',l_count, 'Number of records inserted into ref_department from disbursements accounting lines');	
	END IF;	
	
	RAISE NOTICE '1.5';
	-- Generate the department history id for history records
	
	TRUNCATE etl.ref_department_history_id_seq;
	
	INSERT INTO etl.ref_department_history_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_fk_values_fms_acc_line_new_dept;

	INSERT INTO ref_department_history(department_history_id,department_id,
					   department_name,agency_id,fund_class_id,
					   fiscal_year,created_date,load_id)
	SELECT a.department_history_id,c.department_id,	
		(CASE WHEN COALESCE(b.appr_cd,'---------') <> '---------' THEN '<Unknown Department>'
		      ELSE 'Non-Applicable Department' END) as department_name,
		b.agency_id,b.fund_class_id,b.fiscal_year,now()::timestamp,p_load_id_in
	FROM   etl.ref_department_history_id_seq a JOIN tmp_fk_values_fms_acc_line_new_dept b ON a.uniq_id = b.uniq_id
		JOIN etl.ref_department_id_seq  c ON a.uniq_id = c.uniq_id ;


	GET DIAGNOSTICS l_count = ROW_COUNT;	

	IF l_count > 0 THEN 
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'F',l_count, 'Number of records inserted into ref_department_history from disbursements accounting lines');	
	END IF;	
	
	RAISE NOTICE '1.6';
	
	INSERT INTO tmp_fk_values_fms_acc_line(uniq_id,department_history_id)
	SELECT	a.uniq_id, max(c.department_history_id) 
	FROM etl.stg_fms_accounting_line a JOIN ref_department b  ON coalesce(a.appr_cd,'---------') = b.department_code AND a.fy_dc = b.fiscal_year
		JOIN ref_department_history c ON b.department_id = c.department_id
		JOIN ref_agency d ON a.dept_cd = d.agency_code AND b.agency_id = d.agency_id
		JOIN ref_fund_class e ON a.fcls_cd = e.fund_class_code AND e.fund_class_id = b.fund_class_id
		JOIN etl.ref_department_history_id_seq f ON c.department_history_id = f.department_history_id
	GROUP BY 1	;	

	RAISE NOTICE '1.7';
	
	-- FK:expenditure_object_history_id

	INSERT INTO tmp_fk_values_fms_acc_line(uniq_id,expenditure_object_history_id)
	SELECT	a.uniq_id, max(c.expenditure_object_history_id) 
	FROM etl.stg_fms_accounting_line a JOIN ref_expenditure_object b ON coalesce(a.obj_cd,'----') = b.expenditure_object_code AND a.fy_dc = b.fiscal_year
		JOIN ref_expenditure_object_history c ON b.expenditure_object_id = c.expenditure_object_id
	GROUP BY 1	;


	RAISE NOTICE '1.8';
	
	CREATE TEMPORARY TABLE tmp_fk_values_fms_acc_line_new_exp_object(obj_cd varchar,fiscal_year smallint,uniq_id bigint)
	DISTRIBUTED BY (uniq_id);
	
	INSERT INTO tmp_fk_values_fms_acc_line_new_exp_object
	SELECT COALESCE(a.obj_cd,'----') as obj_cd,fy_dc,MIN(a.uniq_id) as uniq_id
	FROM etl.stg_fms_accounting_line a join (SELECT uniq_id
						 FROM tmp_fk_values_fms_acc_line
						 GROUP BY 1
						 HAVING max(expenditure_object_history_id) is null) b on a.uniq_id=b.uniq_id
	GROUP BY 1,2;

	-- Generate the expenditure_object id for new records
		
	TRUNCATE etl.ref_expenditure_object_id_seq;
	
	INSERT INTO etl.ref_expenditure_object_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_fk_values_fms_acc_line_new_exp_object;

	RAISE NOTICE '1.9';
	
	INSERT INTO ref_expenditure_object(expenditure_object_id,expenditure_object_code,
		expenditure_object_name,fiscal_year,created_date,created_load_id,original_expenditure_object_name)
	SELECT a.expenditure_object_id,b.obj_cd,
		(CASE WHEN b.obj_cd <> '----' THEN '<Unknown Expenditure Object>'
			ELSE '<Non-Applicable Expenditure Object>' END) as expenditure_object_name,
		b.fiscal_year,now()::timestamp,p_load_id_in,
		(CASE WHEN b.obj_cd <> '----' THEN '<Unknown Expenditure Object>'
			ELSE '<Non-Applicable Expenditure Object>' END) as original_expenditure_object_name
	FROM   etl.ref_expenditure_object_id_seq a JOIN tmp_fk_values_fms_acc_line_new_exp_object b ON a.uniq_id = b.uniq_id;

	GET DIAGNOSTICS l_count = ROW_COUNT;	

	IF l_count > 0 THEN 
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'F',l_count, 'Number of records inserted into ref_expenditure_object from disbursements accounting lines');	
	END IF;	
	
	-- Generate the expenditure_object history id for history records
	
	TRUNCATE etl.ref_expenditure_object_history_id_seq;
	
	INSERT INTO etl.ref_expenditure_object_history_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_fk_values_fms_acc_line_new_exp_object;

	RAISE NOTICE '1.10';
	
	INSERT INTO ref_expenditure_object_history(expenditure_object_history_id,expenditure_object_id,fiscal_year,expenditure_object_name,created_date,load_id)
	SELECT a.expenditure_object_history_id,c.expenditure_object_id,b.fiscal_year,
		(CASE WHEN b.obj_cd <> '----' THEN '<Unknown Expenditure Object>'
			ELSE '<Non-Applicable Expenditure Object>' END) as expenditure_object_name,now()::timestamp,p_load_id_in
	FROM   etl.ref_expenditure_object_history_id_seq a JOIN tmp_fk_values_fms_acc_line_new_exp_object b ON a.uniq_id = b.uniq_id
		JOIN etl.ref_expenditure_object_id_seq c ON a.uniq_id = c.uniq_id;

	GET DIAGNOSTICS l_count = ROW_COUNT;	

	IF l_count > 0 THEN 
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'F',l_count, 'Number of records inserted into ref_expenditure_object_history from disbursements accounting lines');	
	END IF;	
	
	INSERT INTO tmp_fk_values_fms_acc_line(uniq_id,expenditure_object_history_id)
	SELECT	a.uniq_id, max(c.expenditure_object_history_id) 
	FROM etl.stg_fms_accounting_line a JOIN ref_expenditure_object b ON COALESCE(a.obj_cd,'----') = b.expenditure_object_code   AND a.fy_dc = b.fiscal_year
		JOIN ref_expenditure_object_history c ON b.expenditure_object_id = c.expenditure_object_id
		JOIN etl.ref_expenditure_object_history_id_seq d ON c.expenditure_object_history_id = d.expenditure_object_history_id
	GROUP BY 1	;
		
	-- FK:budget_code_id

	INSERT INTO tmp_fk_values_fms_acc_line(uniq_id,budget_code_id)
	SELECT	a.uniq_id, b.budget_code_id
	FROM etl.stg_fms_accounting_line a JOIN ref_budget_code b ON a.func_cd = b.budget_code AND a.fy_dc=b.fiscal_year
		JOIN ref_agency d ON a.dept_cd = d.agency_code AND b.agency_id = d.agency_id
		JOIN ref_fund_class e ON a.fcls_cd = e.fund_class_code AND e.fund_class_id = b.fund_class_id;	
		
	-- FK:location_history_id

	INSERT INTO tmp_fk_values_fms_acc_line(uniq_id,location_history_id)
	SELECT	a.uniq_id, max(c.location_history_id) 
	FROM etl.stg_fms_accounting_line a JOIN ref_location b ON COALESCE(a.loc_cd,'----') = COALESCE(b.location_code,'----') 
		JOIN ref_location_history c ON b.location_id = c.location_id
		JOIN ref_agency d ON a.dept_cd = d.agency_code AND b.agency_id = d.agency_id
	GROUP BY 1	;	
	
	CREATE TEMPORARY TABLE tmp_fk_values_fms_acc_line_new_loc(loc_cd varchar,agency_history_id integer,agency_id integer, fiscal_year smallint,uniq_id bigint)
	DISTRIBUTED BY (uniq_id);

	INSERT INTO tmp_fk_values_fms_acc_line_new_loc
	SELECT COALESCE(loc_cd,'----') as loc_cd,
		d.agency_history_id,c.agency_id,a.fy_dc,min(b.uniq_id)
	FROM etl.stg_fms_accounting_line a join (SELECT uniq_id
						 FROM tmp_fk_values_fms_acc_line
						 GROUP BY 1
						 HAVING max(location_history_id) is null) b on a.uniq_id=b.uniq_id
	     JOIN ref_agency c ON a.dept_cd = c.agency_code
	     JOIN ref_agency_history d ON c.agency_id = d.agency_id					 
	GROUP BY 1,2,3,4;
	
	-- Generate the location id for new records
		
	TRUNCATE etl.ref_location_id_seq;
	
	INSERT INTO etl.ref_location_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_fk_values_fms_acc_line_new_loc;

	INSERT INTO ref_location(location_id,location_code,location_name,agency_id,created_date,created_load_id,original_location_name)
	SELECT a.location_id,b.loc_cd,(CASE WHEN COALESCE(b.loc_cd,'----') <> '----' THEN '<Unknown Location>'
			ELSE 'Non-Applicable Location' END) as location_name,b.agency_id,now()::timestamp,p_load_id_in,
			(CASE WHEN COALESCE(b.loc_cd,'----') <> '----' THEN '<Unknown Location>'
			ELSE 'Non-Applicable Location' END) as original_location_name
	FROM   etl.ref_location_id_seq a JOIN tmp_fk_values_fms_acc_line_new_loc b ON a.uniq_id = b.uniq_id;
	
	GET DIAGNOSTICS l_count = ROW_COUNT;	

	IF l_count > 0 THEN 
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'F',l_count, 'Number of records inserted into ref_location from disbursements accounting lines');	
	END IF;	
	

	RAISE NOTICE '1.5';
	-- Generate the location history id for history records
	
	TRUNCATE etl.ref_location_history_id_seq;
	
	INSERT INTO etl.ref_location_history_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_fk_values_fms_acc_line_new_loc;

	INSERT INTO ref_location_history(location_history_id,location_id,location_name,
					 created_date,load_id)
	SELECT a.location_history_id,c.location_id,	
		(CASE WHEN COALESCE(b.loc_cd,'----') <> '----' THEN '<Unknown Location>'
			ELSE 'Non-Applicable Location' END) as location_name,
		now()::timestamp,p_load_id_in
	FROM   etl.ref_location_history_id_seq a JOIN tmp_fk_values_fms_acc_line_new_loc b ON a.uniq_id = b.uniq_id
		JOIN etl.ref_location_id_seq  c ON a.uniq_id = c.uniq_id ;

	GET DIAGNOSTICS l_count = ROW_COUNT;	

	IF l_count > 0 THEN 
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'F',l_count, 'Number of records inserted into ref_location_history from disbursements accounting lines');	
	END IF;	
	

	RAISE NOTICE '1.6';
	
	INSERT INTO tmp_fk_values_fms_acc_line(uniq_id,location_history_id)
	SELECT	a.uniq_id, max(c.location_history_id) 
	FROM etl.stg_fms_accounting_line a JOIN ref_location b ON COALESCE(a.loc_cd,'----') = COALESCE(b.location_code,'----') 
		JOIN ref_location_history c ON b.location_id = c.location_id
		JOIN ref_agency d ON a.dept_cd = d.agency_code AND b.agency_id = d.agency_id
		JOIN etl.ref_location_history_id_seq f ON c.location_history_id = f.location_history_id
	GROUP BY 1	;		

	-- FK:masked_agency_history_id
	-- If dept_cd is 096 in stg_fms_accounting_line, public_agency_id is updated to the one corresponding to 069.	
	INSERT INTO tmp_fk_values_fms_acc_line(uniq_id,masked_agency_history_id)
	SELECT	a.uniq_id, d.agency_history_id 
	FROM etl.stg_fms_accounting_line a CROSS JOIN (SELECT max(agency_history_id)  as agency_history_id
						 FROM ref_agency b JOIN ref_agency_history c ON b.agency_id = c.agency_id
						 WHERE b.agency_code='069') d
	WHERE	a.dept_cd='096';
	
	-- FK:masked_agency_history_id	
	-- If dept_cd is 098 in stg_fms_accounting_line and associated to an agreement with the department code as 015 
	-- i.e. rqporf_doc_dept_cd as 015, public_agency_id is updated to the one corresponding to 015.
	
	INSERT INTO tmp_fk_values_fms_acc_line(uniq_id,masked_agency_history_id)
	SELECT	a.uniq_id, max(c.agency_history_id)
	FROM etl.stg_fms_accounting_line a JOIN ref_agency b ON a.rqporf_doc_dept_cd = b.agency_code 
		JOIN ref_agency_history c ON b.agency_id = c.agency_id
	WHERE	a.rqporf_doc_dept_cd='015' AND a.dept_cd ='098'
	GROUP BY 1;
	
	-- FK:masked_agency_history_id
	-- If dept_cd is 098 and obj_cd is 4000/ 4140/ 6000/ 6130/ 6150/ 6220/ 6650/ 6710/ 6780/ 6810/ 6820/ 6830/ 6860, 
	-- public_agency_id will be set to the one corresponding to rqporf_doc_dept_cd.
	
	INSERT INTO tmp_fk_values_fms_acc_line(uniq_id,masked_agency_history_id)
	SELECT	a.uniq_id, max(c.agency_history_id) 
	FROM etl.stg_fms_accounting_line a JOIN ref_agency b ON  a.rqporf_doc_dept_cd = b.agency_code
		JOIN ref_agency_history c ON b.agency_id = c.agency_id
	WHERE	a.dept_cd ='098'
		AND a.obj_cd IN ('4000','4140','6000','6130','6150','6220','6650','6710','6780','6810','6820','6830','6860') 
	GROUP BY 1;


	-- FK:masked_department_history_id
	-- Getting the appropriate appropriation unit for the masked agency
	
	INSERT INTO tmp_fk_values_fms_acc_line(uniq_id,masked_department_history_id)
	SELECT	a.uniq_id, max(c.department_history_id) 
	FROM etl.stg_fms_accounting_line a JOIN ref_department b ON a.appr_cd = b.department_code AND a.fy_dc = b.fiscal_year
		JOIN ref_department_history c ON b.department_id = c.department_id
		JOIN ref_agency d ON b.agency_id = d.agency_id
		JOIN ref_fund_class e ON a.fcls_cd = e.fund_class_code AND e.fund_class_id = b.fund_class_id
	WHERE	a.dept_cd='096'
		AND d.agency_code='069'		
	GROUP BY 1;		

	--FK:masked_department_history_id
	INSERT INTO tmp_fk_values_fms_acc_line(uniq_id,masked_department_history_id)
	SELECT	a.uniq_id, max(c.department_history_id) 
	FROM etl.stg_fms_accounting_line a JOIN ref_department b ON a.appr_cd = b.department_code AND a.fy_dc = b.fiscal_year
		JOIN ref_department_history c ON b.department_id = c.department_id
		JOIN ref_agency d ON b.agency_id = d.agency_id
		JOIN ref_fund_class e ON a.fcls_cd = e.fund_class_code AND e.fund_class_id = b.fund_class_id
		JOIN etl.stg_fms_header f ON  a.doc_cd = f.doc_cd AND a.doc_dept_cd = f.doc_dept_cd
					AND a.doc_id = f.doc_id AND a.doc_vers_no = f.doc_vers_no
	WHERE	a.rqporf_doc_dept_cd='015' AND a.dept_cd ='098'
		AND d.agency_code='015'
	GROUP BY 1;

	--FK:masked_department_history_id
	INSERT INTO tmp_fk_values_fms_acc_line(uniq_id,masked_department_history_id)
	SELECT	a.uniq_id, max(c.department_history_id) 
	FROM etl.stg_fms_accounting_line a JOIN ref_department b ON a.appr_cd = b.department_code AND a.fy_dc = b.fiscal_year
		JOIN ref_department_history c ON b.department_id = c.department_id
		JOIN ref_agency d ON b.agency_id = d.agency_id
		JOIN ref_fund_class e ON a.fcls_cd = e.fund_class_code AND e.fund_class_id = b.fund_class_id
		JOIN etl.stg_fms_header f ON  a.doc_cd = f.doc_cd AND a.doc_dept_cd = f.doc_dept_cd
					AND a.doc_id = f.doc_id AND a.doc_vers_no = f.doc_vers_no
	WHERE	a.dept_cd ='098'
		AND a.obj_cd IN ('4000','4140','6000','6130','6150','6220','6650','6710','6780','6810','6820','6830','6860') 
		AND d.agency_code=a.rqporf_doc_dept_cd
	GROUP BY 1;
	
	RAISE NOTICE '1.7';
	
	UPDATE etl.stg_fms_accounting_line a
	SET	fund_class_id =ct_table.fund_class_id ,
		agency_history_id =ct_table.agency_history_id ,
		department_history_id =ct_table.department_history_id ,
		expenditure_object_history_id =ct_table.expenditure_object_history_id ,
		budget_code_id=ct_table.budget_code_id,		
		location_history_id = ct_table.location_history_id,
		masked_agency_history_id = ct_table.masked_agency_history_id,
		masked_department_history_id = ct_table.masked_department_history_id
	FROM	
		(SELECT uniq_id,
			max(fund_class_id )as fund_class_id ,
			max(agency_history_id )as agency_history_id ,
			max(department_history_id )as department_history_id ,
			max(expenditure_object_history_id )as expenditure_object_history_id ,
			max(budget_code_id) as budget_code_id ,			
			max(location_history_id) as location_history_id,
			max(masked_agency_history_id) as masked_agency_history_id,
			max(masked_department_history_id) as masked_department_history_id
		FROM	tmp_fk_values_fms_acc_line
		GROUP	BY 1) ct_table
	WHERE	a.uniq_id = ct_table.uniq_id;	

	RETURN 1;
	
EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in updateForeignKeysForFMSInAccLine';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;	

	RETURN 0;
END;
$$ language plpgsql;
---------------------------------------------------------------------------------------------------------------------------------------------------------------
CREATE OR REPLACE FUNCTION etl.associateCONToFMS(p_privacy_flag_in char(1), p_load_file_id_in bigint, p_load_id_in bigint) RETURNS INT AS $$
DECLARE
	l_worksite_col_array VARCHAR ARRAY[10];
	l_array_ctr smallint;
	l_fk_update int;
	l_count bigint;
BEGIN
						  
	-- updating staging table columns (rqporf_doc_id, rqporf_doc_dept_cd,  rqporf_doc_cd)  with 'N/A' if it has 'N/A (PRIVACY/SECURITY)'
	
	UPDATE etl.stg_fms_accounting_line
	SET rqporf_doc_id = 'N/A',
		rqporf_doc_dept_cd = 'N/A',
		rqporf_doc_cd = 'N/A',
		file_type ='P'
	WHERE rqporf_doc_id = 'N/A (PRIVACY/SECURITY)';
	
	UPDATE etl.invalid_fms_accounting_line 
	SET file_type ='P' 
	WHERE  rqporf_actg_ln_no ='N/A (PRIVACY/SECURITY)' 
	AND load_file_id =p_load_file_id_in;
	
	UPDATE etl.archive_fms_accounting_line 
	SET file_type ='P' 
	WHERE  rqporf_actg_ln_no ='N/A (PRIVACY/SECURITY)' 
	AND load_file_id =p_load_file_id_in;
	
	-- Fetch all the contracts associated with Disbursements
	
	CREATE TEMPORARY TABLE tmp_ct_fms(uniq_id bigint, agreement_id bigint,con_document_id varchar, 
				con_agency_history_id smallint, con_document_code_id smallint, con_document_code varchar, con_agency_code varchar )	
	DISTRIBUTED BY(uniq_id);
	
	INSERT INTO tmp_ct_fms
	SELECT uniq_id, 0 as agreement_id,
	       max(rqporf_doc_id),
	       max(d.agency_history_id) as con_agency_history_id,
	       max(c.document_code_id),
	       max(c.document_code),
	       max(b.agency_code)
	FROM	etl.stg_fms_accounting_line a JOIN ref_agency b ON a.rqporf_doc_dept_cd = b.agency_code
		JOIN ref_document_code c ON a.rqporf_doc_cd = c.document_code
		JOIN ref_agency_history d ON b.agency_id = d.agency_id
	GROUP BY 1,2;		
		
	RAISE NOTICE 'FMS AC 1';
	-- Identify the agreement/CON Id
	
	CREATE TEMPORARY TABLE tmp_old_ct_fms_con(uniq_id bigint,agreement_id bigint, action_flag char(1), latest_flag char(1))
	DISTRIBUTED BY (uniq_id);
	
	INSERT INTO tmp_old_ct_fms_con
	SELECT uniq_id,
	       original_agreement_id as agreement_id	
	FROM	
		(SELECT  uniq_id,		
			 b.document_version as mag_document_version,
			 b.original_agreement_id,
			 rank()over(partition by uniq_id order by b.document_version desc) as rank_value
		FROM tmp_ct_fms a JOIN history_agreement b ON a.con_document_id = b.document_id 
			JOIN ref_document_code f ON a.con_document_code = f.document_code AND b.document_code_id = f.document_code_id
			JOIN ref_agency e ON a.con_agency_code = e.agency_code 
			JOIN ref_agency_history c ON b.agency_history_id = c.agency_history_id AND e.agency_id = c.agency_id
		WHERE b.original_version_flag ='Y'	
		) inner_tbl
	WHERE	rank_value = 1;	
	
	UPDATE tmp_ct_fms a
	SET	agreement_id = b.agreement_id
	FROM	tmp_old_ct_fms_con b
	WHERE	a.uniq_id = b.uniq_id;
	
	RAISE NOTICE 'FMS AC 2';	
	-- Identify the CON ones which have to be created
	
	CREATE TEMPORARY TABLE tmp_new_ct_fms_con(con_document_code varchar,con_agency_code varchar, con_document_id varchar,
					   con_agency_history_id smallint,con_document_code_id smallint,uniq_id bigint)
					   DISTRIBUTED BY (uniq_id);
	
	INSERT INTO tmp_new_ct_fms_con
	SELECT 	con_document_code,con_agency_code, con_document_id,con_agency_history_id,con_document_code_id,min(uniq_id)
	FROM	tmp_ct_fms
	WHERE	agreement_id =0 AND con_document_code in ('CT1','CTA1','DO1','POD','POC','PCC1')
	GROUP BY 1,2,3,4,5;
	
	TRUNCATE etl.agreement_id_seq;
	
	INSERT INTO etl.agreement_id_seq(uniq_id)
	SELECT uniq_id
	FROM  tmp_new_ct_fms_con;
	
	INSERT INTO history_agreement(agreement_id,document_code_id,agency_history_id,document_id,document_version,privacy_flag,original_version_flag,latest_flag,original_agreement_id,created_load_id,created_date,contract_number)
	SELECT  b.agreement_id, a.con_document_code_id,a.con_agency_history_id,a.con_document_id,1 as document_version,p_privacy_flag_in,'Y','Y', b.agreement_id, p_load_id_in, now()::timestamp,a.con_document_code||a.con_agency_code||a.con_document_id as contract_number
	FROM	tmp_new_ct_fms_con a JOIN etl.agreement_id_seq b ON a.uniq_id = b.uniq_id;
	
	GET DIAGNOSTICS l_count = ROW_COUNT;	

	IF l_count > 0 THEN 
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'F',l_count, 'Number of records inserted into history_agreement from disbursements accounting lines');	
	END IF;		
	
	RAISE NOTICE 'FMS AC 3';
	-- Updating the newly created CON identifier. The below statements are slow. SO need to be modified
	
	
	
	CREATE TEMPORARY TABLE tmp_new_ct_fms_con_2(uniq_id bigint,agreement_id bigint)
	DISTRIBUTED BY (uniq_id);
	
	
	INSERT INTO tmp_new_ct_fms_con_2
	SELECT c.uniq_id,d.agreement_id
	FROM   tmp_ct_fms a JOIN tmp_new_ct_fms_con b ON a.uniq_id = b.uniq_id
		JOIN tmp_ct_fms c ON c.con_document_code = a.con_document_code
				     AND c.con_agency_code = a.con_agency_code
				     AND c.con_document_id = a.con_document_id
		JOIN etl.agreement_id_seq d ON b.uniq_id = d.uniq_id;
		
	UPDATE tmp_ct_fms a
	SET	agreement_id = b.agreement_id
	FROM	tmp_new_ct_fms_con_2 b
	WHERE	a.uniq_id = b.uniq_id
		AND a.agreement_id =0;
	 
		RAISE NOTICE 'FMS AC 4';
	 UPDATE etl.stg_fms_accounting_line a
	 SET	agreement_id = b.agreement_id
	 FROM	tmp_ct_fms b
	 WHERE	a.uniq_id = b.uniq_id;
	 
	 UPDATE etl.stg_fms_accounting_line a
	 SET	agreement_id = NULL
	 WHERE agreement_id = 0;
	 
	RETURN 1;
EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in associateCONToFMS';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;	

	RETURN 0;
END;
$$ language plpgsql;


------------------------------------------------------------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION etl.processFMS(p_load_file_id_in int,p_load_id_in bigint) RETURNS INT AS $$
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
	
	/*
	
	SELECT	agreement_id
	FROM	history_agreement a JOIN ref_document_code b ON a.document_code_id = b.document_code_id
	WHERE	a.document_id='N/A (PRIVACY/SECURITY)'
		AND b.document_code='N/A (PRIVACY/SECURITY)'
	INTO	l_masked_agreement_id;
	
	SELECT	a.vendor_history_id
	FROM	vendor_history a JOIN vendor b ON a.vendor_id = b.vendor_id
	WHERE	b.vendor_customer_code='N/A (PRIVACY/SECURITY)'
		AND b.legal_name='N/A (PRIVACY/SECURITY)'
	INTO	l_masked_vendor_history_id;	
	
	*/
	
	l_fk_update := etl.updateForeignKeysForFMSInHeader(p_load_file_id_in,p_load_id_in);

	RAISE NOTICE 'FMS 1';
	
	IF l_fk_update = 1 THEN
		l_fk_update := etl.processvendor(p_load_file_id_in,p_load_id_in,'F');
	ELSE
		RETURN -1;
	END IF;

	RAISE NOTICE 'FMS 2';
	
	IF l_fk_update = 1 THEN
		l_fk_update := etl.updateForeignKeysForFMSInAccLine(p_load_file_id_in,p_load_id_in);
	ELSE
		RETURN -1;
	END IF;

	RAISE NOTICE 'FMS 3';
	
	IF l_fk_update = 1 THEN
		l_fk_update := etl.associateCONToFMS(l_display_type,p_load_file_id_in,p_load_id_in);
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
	
	CREATE TEMPORARY TABLE tmp_all_disbs(uniq_id bigint, agency_history_id smallint,doc_id varchar,disbursement_id integer, action_flag char(1),doc_vers_no smallint) 
	DISTRIBUTED BY (uniq_id);
	
	INSERT INTO tmp_all_disbs(uniq_id,agency_history_id,doc_id,doc_vers_no,action_flag)
	SELECT uniq_id,agency_history_id,doc_id,doc_vers_no,'I' as action_flag
	FROM etl.stg_fms_header;
	
	CREATE TEMPORARY TABLE tmp_old_disbs(disbursement_id integer, uniq_id bigint) 
	DISTRIBUTED BY (uniq_id);
	
	INSERT INTO tmp_old_disbs 
	SELECT a.disbursement_id, b.uniq_id
	FROM disbursement a JOIN etl.stg_fms_header b ON a.document_id = b.doc_id AND a.document_version = b.doc_vers_no AND a.document_code_id = b.document_code_id	
	JOIN ref_agency_history c ON a.agency_history_id = c.agency_history_id
	JOIN ref_agency_history d ON b.agency_history_id = d.agency_history_id and c.agency_id = d.agency_id;
	
	
	UPDATE tmp_all_disbs a
	SET	disbursement_id = b.disbursement_id,
		action_flag = 'U'		
	FROM	tmp_old_disbs b
	WHERE	a.uniq_id = b.uniq_id;

	RAISE NOTICE 'FMS 13';
	
	TRUNCATE etl.seq_expenditure_expenditure_id ;
		
	INSERT INTO etl.seq_expenditure_expenditure_id
	SELECT uniq_id
	FROM	tmp_all_disbs
	WHERE	action_flag ='I' 
		AND COALESCE(disbursement_id,0) =0 ;

	UPDATE tmp_all_disbs a
	SET	disbursement_id = b.disbursement_id	
	FROM	etl.seq_expenditure_expenditure_id b
	WHERE	a.uniq_id = b.uniq_id;	

	RAISE NOTICE 'FMS 14';
	

	INSERT INTO disbursement(disbursement_id,document_code_id,agency_history_id,
				 document_id,document_version,disbursement_number,record_date_id,
				 budget_fiscal_year,document_fiscal_year,document_period,
				 check_eft_amount_original,check_eft_amount,check_eft_issued_date_id,check_eft_record_date_id,
				 expenditure_status_id,expenditure_cancel_type_id,expenditure_cancel_reason_id,
				 total_accounting_line_amount_original,total_accounting_line_amount,vendor_history_id,
				 retainage_amount_original,retainage_amount,privacy_flag,created_load_id,created_date)
	SELECT d.disbursement_id, a.document_code_id,a.agency_history_id,
	       a.doc_id,a.doc_vers_no,a.doc_id||'-'||a.doc_vers_no||'-'|| a.doc_dept_cd || '-' || a.doc_cd,
	       a.record_date_id,
	       a.doc_bfy,a.doc_fy_dc,a.doc_per_dc,
	       a.chk_eft_am,coalesce(a.chk_eft_am,0) as check_eft_amount,a.check_eft_issued_date_id,a.check_eft_record_date_id,
	       a.chk_eft_sta,a.can_typ_cd,a.can_reas_cd_dc,
	       a.ln_am,coalesce(a.ln_am,0) as total_accounting_line_amount, b.vendor_history_id, 
	       a.rtg_am,coalesce(a.rtg_am,0) as retainage_amount, l_display_type,p_load_id_in,now()::timestamp
	FROM	etl.stg_fms_header a JOIN etl.stg_fms_vendor b ON a.doc_cd = b.doc_cd AND a.doc_dept_cd = b.doc_dept_cd
					AND a.doc_id = b.doc_id AND a.doc_vers_no = b.doc_vers_no
		JOIN tmp_all_disbs d ON a.uniq_id = d.uniq_id
	WHERE   action_flag='I';
		
	GET DIAGNOSTICS l_count = ROW_COUNT;	
							
			IF l_count > 0 THEN 
			INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
				VALUES(p_load_file_id_in,'F',l_count, '# of records inserted into disbursement');	
	END IF;	
		
		
	RAISE NOTICE 'FMS 15';
	
	CREATE TEMPORARY TABLE tmp_disbs_update AS
	SELECT d.disbursement_id, a.document_code_id,a.agency_history_id,
	       a.doc_id,a.doc_vers_no,
	       a.doc_id||'-'||a.doc_vers_no||'-'|| a.doc_dept_cd || '-' || a.doc_cd as disbursement_number,
	       a.record_date_id,
	       a.doc_bfy,a.doc_fy_dc,a.doc_per_dc,
	       a.chk_eft_am,a.check_eft_issued_date_id,a.check_eft_record_date_id,
	       a.chk_eft_sta,a.can_typ_cd,a.can_reas_cd_dc,
	       a.ln_am,b.vendor_history_id, 
	       a.rtg_am
	FROM	etl.stg_fms_header a JOIN etl.stg_fms_vendor b ON a.doc_cd = b.doc_cd AND a.doc_dept_cd = b.doc_dept_cd
					AND a.doc_id = b.doc_id AND a.doc_vers_no = b.doc_vers_no
		JOIN tmp_all_disbs d ON a.uniq_id = d.uniq_id
	WHERE   action_flag='U'
	DISTRIBUTED BY (disbursement_id);	
	
	UPDATE disbursement a
	SET document_code_id = b.document_code_id,
		agency_history_id = b.agency_history_id,
		document_id = b.doc_id,
		document_version = b.doc_vers_no,
		disbursement_number = b.disbursement_number,
		record_date_id = b.record_date_id,
		budget_fiscal_year = b.doc_bfy,
		document_fiscal_year = b.doc_fy_dc,
		document_period = b.doc_per_dc,
		check_eft_amount_original = b.chk_eft_am,
		check_eft_amount = coalesce(b.chk_eft_am,0),
		check_eft_issued_date_id = b.check_eft_issued_date_id,
		check_eft_record_date_id = b.check_eft_record_date_id,
		expenditure_status_id = b.chk_eft_sta,
		expenditure_cancel_type_id = b.can_typ_cd,
		expenditure_cancel_reason_id = b.can_reas_cd_dc,
		total_accounting_line_amount_original = b.ln_am,
		total_accounting_line_amount = coalesce(b.ln_am,0) ,
		vendor_history_id = b.vendor_history_id,
		retainage_amount_original = b.rtg_am,
		retainage_amount = coalesce(b.rtg_am,0),
		privacy_flag = l_display_type,
		updated_load_id = p_load_id_in,
		updated_date = now()::timestamp
	FROM	tmp_disbs_update b
	WHERE	a.disbursement_id = b.disbursement_id;
	
		GET DIAGNOSTICS l_count = ROW_COUNT;	
				
					IF l_count > 0 THEN 
						INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
						VALUES(p_load_file_id_in,'F',l_count, '# of records updated in disbursement');	
	END IF;	
	
	
	RAISE NOTICE 'FMS 16';
	
	-- Disbursement line item changes
	
	
	TRUNCATE etl.seq_disbursement_line_item_id;
	
	INSERT INTO etl.seq_disbursement_line_item_id(uniq_id)
	SELECT b.uniq_id
	FROM	etl.stg_fms_header a JOIN etl.stg_fms_accounting_line b ON a.doc_cd = b.doc_cd AND a.doc_dept_cd = b.doc_dept_cd
			AND a.doc_id = b.doc_id AND a.doc_vers_no = b.doc_vers_no	
			JOIN tmp_all_disbs c ON a.uniq_id = c.uniq_id
	WHERE	action_flag ='I' ;
	
	
	INSERT INTO disbursement_line_item(disbursement_line_item_id,disbursement_id,line_number,disbursement_number,
						budget_fiscal_year,fiscal_year,fiscal_period,
						fund_class_id,agency_history_id,department_history_id,
						expenditure_object_history_id,budget_code_id,fund_code,
						reporting_code,check_amount_original,check_amount,agreement_id,
						agreement_accounting_line_number, agreement_commodity_line_number, agreement_vendor_line_number, 
						reference_document_number, 
						reference_document_code,
						location_history_id,retainage_amount_original,retainage_amount,check_eft_issued_nyc_year_id,
						created_load_id,created_date,file_type)
	SELECT  c.disbursement_line_item_id,d.disbursement_id,a.doc_actg_ln_no,a.doc_id||'-'||a.doc_vers_no||'-'|| a.doc_dept_cd || '-' || a.doc_cd,
		a.bfy,a.fy_dc,a.per_dc,
		a.fund_class_id,coalesce(a.masked_agency_history_id,a.agency_history_id) as agency_history_id, coalesce(a.masked_department_history_id,a.department_history_id) as department_history_id,
		a.expenditure_object_history_id,a.budget_code_id,a.fund_cd,
		a.rpt_cd,(CASE WHEN a.doc_vers_no > 1 THEN -1 * a.chk_amt ELSE a.chk_amt END) as check_amount_original,(CASE WHEN a.doc_vers_no > 1 THEN -1 * coalesce(a.chk_amt,0) ELSE coalesce(a.chk_amt,0) END) as check_amount,a.agreement_id,
		(case when a.rqporf_actg_ln_no ='N/A (PRIVACY/SECURITY)' then NULL else rqporf_actg_ln_no end)::integer as rqporf_actg_ln_no,
		(case when a.rqporf_comm_ln_no ='N/A (PRIVACY/SECURITY)' then NULL else rqporf_comm_ln_no end)::integer as rqporf_comm_ln_no,
		(case when a.rqporf_vend_ln_no='N/A (PRIVACY/SECURITY)' then NULL else a.rqporf_vend_ln_no end)::integer as rqporf_vend_ln_no,
		(case when a.rqporf_doc_cd ='N/A' then NULL	when coalesce(a.rqporf_doc_cd, '') ='' then NULL else a.rqporf_doc_cd || a.rqporf_doc_dept_cd || a.rqporf_doc_id end),
		(case when coalesce(a.rqporf_doc_cd, '') = '' then NULL else a.rqporf_doc_cd end) as reference_document_code,
		a.location_history_id,a.rtg_ln_am,coalesce(a.rtg_ln_am,0) as retainage_amount,b.check_eft_issued_nyc_year_id,
		p_load_id_in, now()::timestamp,a.file_type
	FROM	etl.stg_fms_accounting_line a JOIN etl.stg_fms_header b ON a.doc_cd = b.doc_cd AND a.doc_dept_cd = b.doc_dept_cd
					AND a.doc_id = b.doc_id AND a.doc_vers_no = b.doc_vers_no
		JOIN etl.seq_disbursement_line_item_id c ON a.uniq_id = c.uniq_id
		JOIN etl.seq_expenditure_expenditure_id d ON b.uniq_id = d.uniq_id;
		
	GET DIAGNOSTICS l_count = ROW_COUNT;	
			IF l_count > 0 THEN 
			INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
				VALUES(p_load_file_id_in,'F',l_count, '# of records inserted into disbursements_line_item');	
	END IF;	

	RAISE NOTICE 'FMS 17';
	
	-- Identify the disbursement accounting lines which need to be deleted/updated
	
	CREATE TEMPORARY TABLE tmp_disbs_lines_actions(disbursement_id bigint, line_number integer,action_flag char(1),disbursement_line_item_id bigint, uniq_id bigint)
	DISTRIBUTED BY (disbursement_id);
	
	INSERT INTO tmp_disbs_lines_actions
	SELECT  COALESCE(latest_tbl.disbursement_id,old_tbl.disbursement_id) as disbursement_id,
		COALESCE(latest_tbl.doc_actg_ln_no, old_tbl.line_number) as line_number,
		(CASE WHEN latest_tbl.disbursement_id = old_tbl.disbursement_id AND latest_tbl.doc_actg_ln_no = old_tbl.line_number THEN 'U'
		      WHEN latest_tbl.disbursement_id IS NOT NULL AND old_tbl.disbursement_id IS NULL THEN 'I'
		      WHEN latest_tbl.disbursement_id IS NULL AND old_tbl.line_number IS NOT NULL THEN 'D' END) as action_flag,
		      old_tbl.disbursement_line_item_id, latest_tbl.uniq_id  
	FROM	      
		(SELECT a.disbursement_id,c.doc_actg_ln_no, c.uniq_id
		FROM   tmp_all_disbs a JOIN etl.stg_fms_header b ON a.uniq_id = b.uniq_id
			JOIN etl.stg_fms_accounting_line c ON c.doc_cd = b.doc_cd AND c.doc_dept_cd = b.doc_dept_cd 
						     AND c.doc_id = b.doc_id AND c.doc_vers_no = b.doc_vers_no
		WHERE   a.action_flag ='U'
		order by 1,2 ) latest_tbl				     
		FULL OUTER JOIN (SELECT e.disbursement_id,e.line_number , disbursement_line_item_id
			    FROM   disbursement_line_item e JOIN tmp_all_disbs f ON e.disbursement_id = f.disbursement_id WHERE f.action_flag ='U') old_tbl ON latest_tbl.disbursement_id = old_tbl.disbursement_id 
			    AND latest_tbl.doc_actg_ln_no = old_tbl.line_number;
	
	
	

		
	RAISE NOTICE 'FMS 18';
	
	INSERT INTO disbursement_line_item(disbursement_id,line_number,disbursement_number,
						budget_fiscal_year,fiscal_year,fiscal_period,
						fund_class_id,agency_history_id,department_history_id,
						expenditure_object_history_id,budget_code_id,fund_code,
						reporting_code,check_amount_original,check_amount,agreement_id,
						agreement_accounting_line_number, agreement_commodity_line_number, agreement_vendor_line_number, 
						reference_document_number, 
						reference_document_code, 
						location_history_id,retainage_amount_original,retainage_amount,check_eft_issued_nyc_year_id,
						created_load_id,created_date,file_type)
	SELECT  d.disbursement_id,a.doc_actg_ln_no,a.doc_id||'-'||a.doc_vers_no||'-'|| a.doc_dept_cd || '-' || a.doc_cd,
		a.bfy,a.fy_dc,a.per_dc,
		a.fund_class_id,coalesce(a.masked_agency_history_id,a.agency_history_id) as agency_history_id,coalesce(a.masked_department_history_id,a.department_history_id) as department_history_id,
		a.expenditure_object_history_id,a.budget_code_id,a.fund_cd,
		a.rpt_cd,(CASE WHEN a.doc_vers_no > 1 THEN -1 * a.chk_amt ELSE a.chk_amt END) as check_amount_original,(CASE WHEN a.doc_vers_no > 1 THEN -1 * coalesce(a.chk_amt,0) ELSE coalesce(a.chk_amt,0) END) as check_amount,a.agreement_id,
		(case when a.rqporf_actg_ln_no ='N/A (PRIVACY/SECURITY)' then NULL else rqporf_actg_ln_no end)::integer as rqporf_actg_ln_no,
		(case when a.rqporf_comm_ln_no ='N/A (PRIVACY/SECURITY)' then NULL else rqporf_comm_ln_no end)::integer as rqporf_comm_ln_no,
		(case when a.rqporf_vend_ln_no='N/A (PRIVACY/SECURITY)' then NULL else a.rqporf_vend_ln_no end)::integer as rqporf_vend_ln_no,
		(case when a.rqporf_doc_cd ='N/A' then NULL	when coalesce(a.rqporf_doc_cd, '') ='' then NULL else a.rqporf_doc_cd || a.rqporf_doc_dept_cd || a.rqporf_doc_id end), 
		(case when coalesce(a.rqporf_doc_cd, '') = '' then NULL else a.rqporf_doc_cd end) as reference_document_code,
		a.location_history_id,a.rtg_ln_am,coalesce(a.rtg_ln_am,0) as retainage_amount,b.check_eft_issued_nyc_year_id,
		p_load_id_in, now()::timestamp, a.file_type
	FROM	etl.stg_fms_accounting_line a JOIN etl.stg_fms_header b ON a.doc_cd = b.doc_cd AND a.doc_dept_cd = b.doc_dept_cd
					AND a.doc_id = b.doc_id AND a.doc_vers_no = b.doc_vers_no 
					JOIN tmp_all_disbs d ON b.uniq_id = d.uniq_id
					JOIN tmp_disbs_lines_actions e ON d.disbursement_id = e.disbursement_id AND a.doc_actg_ln_no = e.line_number 
	WHERE   d.action_flag = 'U' AND e.action_flag='I';
	
	
	GET DIAGNOSTICS l_count = ROW_COUNT;	
	
		IF l_count > 0 THEN 
			INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
			VALUES(p_load_file_id_in,'F',l_count, '# of records inserted into disbursements_line_item');	
	END IF;	
	
	RAISE NOTICE 'FMS 18.1';
	
	
	RAISE NOTICE 'FMS 18.2';
	
	DELETE FROM ONLY disbursement_line_item a 
	USING tmp_disbs_lines_actions b , tmp_all_disbs c
	WHERE   a.disbursement_id = b.disbursement_id 		
		AND a.line_number = b.line_number		
		AND a.disbursement_id = c.disbursement_id
		AND b.action_flag = 'D' AND c.action_flag='U';
		
		
	 RAISE NOTICE 'FMS 19';
	
	 
	
        CREATE TEMPORARY TABLE tmp_disbs_line_items_update AS
                SELECT e.disbursement_line_item_id, b.bfy, b.fy_dc, b.per_dc, b.fund_class_id, coalesce(b.masked_agency_history_id,b.agency_history_id) as agency_history_id, coalesce(b.masked_department_history_id,b.department_history_id) as department_history_id, b.expenditure_object_history_id, b.budget_code_id,             
                                  b.fund_cd, b.rpt_cd, (CASE WHEN b.doc_vers_no > 1 THEN -1 * b.chk_amt ELSE b.chk_amt END) as chk_amt, b.agreement_id, b.rqporf_actg_ln_no,b.rqporf_comm_ln_no, b.rqporf_vend_ln_no, 
                                  (CASE WHEN b.rqporf_doc_cd = 'N/A' THEN NULL WHEN coalesce(b.rqporf_doc_cd, '') ='' THEN NULL ELSE b.rqporf_doc_cd || b.rqporf_doc_dept_cd || b.rqporf_doc_id END) as reference_document_number, 
                                  (case when coalesce(b.rqporf_doc_cd, '') = '' then NULL else b.rqporf_doc_cd end) as reference_document_code, b.location_history_id, b.rtg_ln_am, a.check_eft_issued_nyc_year_id,b.file_type
                                  ,b.doc_id||'-'||b.doc_vers_no||'-'|| b.doc_dept_cd || '-' || b.doc_cd as disbursement_number
                FROM etl.stg_fms_header a, etl.stg_fms_accounting_line b,
                                tmp_all_disbs d,tmp_disbs_lines_actions e
                WHERE  d.action_flag = 'U' AND e.action_flag='U'
                       AND a.doc_cd = b.doc_cd AND a.doc_dept_cd = b.doc_dept_cd 
                       AND a.doc_id = b.doc_id AND a.doc_vers_no = b.doc_vers_no
                       AND a.uniq_id = d.uniq_id
                       AND d.disbursement_id = e.disbursement_id AND b.uniq_id = e.uniq_id                       
     DISTRIBUTED BY (disbursement_line_item_id); 

	RAISE NOTICE 'FMS 19.1';
	
	UPDATE  disbursement_line_item f
	SET budget_fiscal_year = b.bfy,
	    disbursement_number = b.disbursement_number,
		fiscal_year = b.fy_dc,
		fiscal_period = b.per_dc,
		fund_class_id = b.fund_class_id,
		agency_history_id = b.agency_history_id,
		department_history_id =b.department_history_id,
		expenditure_object_history_id = b.expenditure_object_history_id,
		budget_code_id = b.budget_code_id,		
		fund_code = b.fund_cd,
		reporting_code = b.rpt_cd,
		check_amount_original = coalesce(b.chk_amt,0),
		check_amount = b.chk_amt,
		agreement_id = b.agreement_id,
		agreement_accounting_line_number = (case when b.rqporf_actg_ln_no ='N/A (PRIVACY/SECURITY)' then NULL else b.rqporf_actg_ln_no end)::integer,
		agreement_commodity_line_number = (case when b.rqporf_comm_ln_no ='N/A (PRIVACY/SECURITY)' then NULL else b.rqporf_comm_ln_no end)::integer, 
		agreement_vendor_line_number = (case when b.rqporf_vend_ln_no ='N/A (PRIVACY/SECURITY)' then NULL else b.rqporf_vend_ln_no end)::integer, 
		reference_document_number = b.reference_document_number, 
		reference_document_code = b.reference_document_code,
		location_history_id = b.location_history_id,
		retainage_amount_original = b.rtg_ln_am,
		retainage_amount = coalesce(b.rtg_ln_am,0),
		check_eft_issued_nyc_year_id = b.check_eft_issued_nyc_year_id,
		updated_load_id = p_load_id_in,
		updated_date = now()::timestamp,
		file_type = b.file_type
	FROM   tmp_disbs_line_items_update b			      	      
	WHERE   f.disbursement_line_item_id = b.disbursement_line_item_id ;	
	
	GET DIAGNOSTICS l_count = ROW_COUNT;	
		
			IF l_count > 0 THEN 
				INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
				VALUES(p_load_file_id_in,'F',l_count, '# of records updated in disbursements_line_item');	
	END IF;	
	
	RAISE NOTICE 'FMS 20';	

	/*
	 IF l_fk_update = 1 THEN
		l_fk_update := etl.refreshFactsForFMS(p_load_id_in);
	ELSE
		RETURN -2;
	END IF;	
	*/
	
	RETURN 1;
	
	
EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in processFMS';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;	

	RETURN 0;
END;
$$ language plpgsql;

---------------------------------------------------------------------------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION etl.refreshFactsForFMS(p_job_id_in bigint) RETURNS INT AS
$$
DECLARE
	l_start_time  timestamp;
	l_end_time  timestamp;
BEGIN
	-- Inserting into the disbursement_line_item_details
	
	l_start_time := timeofday()::timestamp;
	
	RAISE NOTICE 'FMS RF 1';
	
	INSERT INTO disbursement_line_item_deleted(disbursement_line_item_id, load_id, deleted_date, job_id)
	SELECT a.disbursement_line_item_id, c.load_id, now()::timestamp, p_job_id_in
	FROM disbursement_line_item_details a, disbursement b, etl.etl_data_load c
	WHERE   a.disbursement_id = b.disbursement_id 
	AND b.updated_load_id = c.load_id
	AND c.job_id = p_job_id_in AND c.data_source_code IN ('C','M','F');
	
	DELETE FROM ONLY disbursement_line_item_details a 
	USING disbursement b, etl.etl_data_load c
	WHERE   a.disbursement_id = b.disbursement_id 
	AND updated_load_id = c.load_id
	AND c.job_id = p_job_id_in AND c.data_source_code IN ('C','M','F'); 
	

		
		
	INSERT INTO disbursement_line_item_details(disbursement_line_item_id,disbursement_id,line_number,disbursement_number,check_eft_issued_date_id,	
						check_eft_issued_nyc_year_id,fiscal_year, check_eft_issued_cal_month_id,
						agreement_id,master_agreement_id,fund_class_id,
						check_amount,agency_id,agency_history_id,agency_code,expenditure_object_id,
						vendor_id,maximum_contract_amount,maximum_spending_limit,department_id,						
						document_id,vendor_name,vendor_customer_code,check_eft_issued_date,agency_name,agency_short_name,location_name,
						department_name,department_short_name,department_code,expenditure_object_name,expenditure_object_code,
						budget_code_id,budget_code,budget_name,reporting_code,location_id,location_code,fund_class_name,fund_class_code,
						spending_category_id,spending_category_name,calendar_fiscal_year_id,calendar_fiscal_year,
						agreement_accounting_line_number, agreement_commodity_line_number, agreement_vendor_line_number, reference_document_number,reference_document_code,
						load_id,last_modified_date,file_type,job_id)
	SELECT  b.disbursement_line_item_id,a.disbursement_id,b.line_number,b.disbursement_number,a.check_eft_issued_date_id,
		f.nyc_year_id,l.year_value,f.calendar_month_id,
		b.agreement_id,NULL as master_agreement_id,b.fund_class_id,
		b.check_amount,c.agency_id,b.agency_history_id,m.agency_code,d.expenditure_object_id,
		e.vendor_id,NULL as maximum_contract_amount, NULL as maximum_spending_limit, g.department_id,
		a.document_id,COALESCE(e.legal_name,e.alias_name) as vendor_name,q.vendor_customer_code,f.date,c.agency_name,c.agency_short_name, COALESCE(i.location_short_name,i.location_name),
		g.department_name,g.department_short_name,o.department_code,d.expenditure_object_name,p.expenditure_object_code,
		j.budget_code_id,j.budget_code,j.attribute_name,b.reporting_code,i.location_id,n.location_code,k.fund_class_name,k.fund_class_code,
		(CASE WHEN k.fund_class_code in ('400', '402') THEN 3
		      WHEN reference_document_number IS NOT NULL AND k.fund_class_code in ('001') THEN 1
		      WHEN k.fund_class_code not in ('400', '402', '001') THEN 5
		      ELSE 4
		 END) as spending_category_id,
		 (CASE WHEN k.fund_class_code in ('400', '402') THEN 'Capital Contracts'
		 	   WHEN reference_document_number IS NOT NULL AND k.fund_class_code in ('001') THEN  'Contracts'
		 	   WHEN k.fund_class_code not in ('400', '402', '001') THEN 'Trust & Agency'
		 	   ELSE 'Others'
		 END) as spending_category_name,x.year_id,x.year_value,
		 b.agreement_accounting_line_number, b.agreement_commodity_line_number, b.agreement_vendor_line_number, b.reference_document_number,b.reference_document_code,
		 coalesce(a.updated_load_id, a.created_load_id),
		 coalesce(a.updated_date, a.created_date),b.file_type,p_job_id_in
		FROM disbursement a JOIN disbursement_line_item b ON a.disbursement_id = b.disbursement_id
			JOIN ref_agency_history c ON b.agency_history_id = c.agency_history_id
			JOIN ref_agency m on c.agency_id = m.agency_id
			JOIN ref_expenditure_object_history d ON b.expenditure_object_history_id = d.expenditure_object_history_id
			JOIN ref_expenditure_object p on d.expenditure_object_id = p.expenditure_object_id
			JOIN vendor_history e ON a.vendor_history_id = e.vendor_history_id
			JOIN vendor q ON q.vendor_id = e.vendor_id
			JOIN ref_date f ON a.check_eft_issued_date_id = f.date_id
			JOIN ref_year l on f.nyc_year_id = l.year_id
			JOIN ref_department_history g ON b.department_history_id = g.department_history_id
			JOIN ref_department o on g.department_id = o.department_id
			JOIN ref_location_history i ON b.location_history_id = i.location_history_id
			JOIN ref_location  n ON i.location_id = n.location_id
			LEFT JOIN ref_budget_code j ON j.budget_code_id = b.budget_code_id
			JOIN ref_fund_class k ON k.fund_class_id = b.fund_class_id			
			JOIN ref_month y on f.calendar_month_id = y.month_id
			JOIN ref_year x on y.year_id = x.year_id
			JOIN etl.etl_data_load z ON coalesce(a.updated_load_id, a.created_load_id) = z.load_id
		WHERE z.job_id = p_job_id_in AND z.data_source_code IN ('C','M','F');
		
		
	
	
	RAISE NOTICE 'FMS RF 2';
	
	CREATE TEMPORARY TABLE tmp_agreement_con(disbursement_line_item_id bigint,agreement_id bigint,fiscal_year smallint,calendar_fiscal_year smallint,master_agreement_id bigint,  maximum_contract_amount numeric(16,2),
												maximum_contract_amount_cy numeric(16,2), maximum_spending_limit numeric(16,2), maximum_spending_limit_cy numeric(16,2),
												purpose varchar, purpose_cy varchar, contract_number varchar, master_contract_number varchar, contract_vendor_id integer, contract_vendor_id_cy integer,
												master_contract_vendor_id integer, master_contract_vendor_id_cy integer, contract_agency_id smallint, contract_agency_id_cy smallint, master_contract_agency_id smallint,
												master_contract_agency_id_cy smallint, master_purpose varchar, master_purpose_cy varchar, contract_document_code varchar, master_contract_document_code varchar)
	DISTRIBUTED  BY (disbursement_line_item_id);
	
	INSERT INTO tmp_agreement_con(disbursement_line_item_id,agreement_id,fiscal_year,calendar_fiscal_year)
	SELECT DISTINCT a.disbursement_line_item_id, a.agreement_id, a.fiscal_year, a.calendar_fiscal_year 
	FROM disbursement_line_item_details a JOIN disbursement_line_item b ON a.disbursement_line_item_id = b.disbursement_line_item_id
		 JOIN disbursement c ON b.disbursement_id = c.disbursement_id
		 JOIN etl.etl_data_load d ON coalesce(c.updated_load_id, c.created_load_id) = d.load_id
		WHERE d.job_id = p_job_id_in AND d.data_source_code IN ('C','M','F') AND b.agreement_id > 0;
	
		
	-- Getting maximum_contract_amount, master_agreement_id, purpose, contract_number,  contract_vendor_id, contract_agency_id for FY from non master contracts.
	
	CREATE TEMPORARY TABLE tmp_agreement_con_fy(disbursement_line_item_id bigint,agreement_id bigint,master_agreement_id bigint, contract_number varchar,
						maximum_contract_amount_fy numeric(16,2), purpose_fy varchar, contract_vendor_id_fy integer, contract_agency_id_fy smallint, contract_document_code_fy varchar )
	DISTRIBUTED  BY (disbursement_line_item_id);
	
	INSERT INTO tmp_agreement_con_fy
	SELECT a.disbursement_line_item_id, b.original_agreement_id,b.master_agreement_id,b.contract_number,
	b.maximum_contract_amount as maximum_contract_amount_fy ,
	b.description as purpose_fy ,
	b.vendor_id as contract_vendor_id_fy,
	b.agency_id as contract_agency_id_fy,
	e.document_code as contract_document_code_fy
		FROM tmp_agreement_con a JOIN agreement_snapshot b ON a.agreement_id = b.original_agreement_id AND a.fiscal_year between b.starting_year and b.ending_year
		JOIN disbursement_line_item c ON a.disbursement_line_item_id = c.disbursement_line_item_id
		JOIN disbursement d ON c.disbursement_id = d.disbursement_id 
		JOIN ref_document_code e ON b.document_code_id = e.document_code_id ;
		
	
	INSERT INTO tmp_agreement_con_fy
    SELECT a.disbursement_line_item_id, b.original_agreement_id,b.master_agreement_id,b.contract_number,
	b.maximum_contract_amount as maximum_contract_amount_fy ,
	b.description as purpose_fy ,
	b.vendor_id as contract_vendor_id_fy,
	b.agency_id as contract_agency_id_fy,
	e.document_code as contract_document_code_fy
		FROM tmp_agreement_con a JOIN agreement_snapshot b ON a.agreement_id = b.original_agreement_id AND b.latest_flag='Y'
		JOIN disbursement_line_item c ON a.disbursement_line_item_id = c.disbursement_line_item_id
		JOIN disbursement d ON c.disbursement_id = d.disbursement_id
		JOIN ref_document_code e ON b.document_code_id = e.document_code_id 
		LEFT JOIN tmp_agreement_con_fy f ON a.disbursement_line_item_id = f.disbursement_line_item_id
		WHERE f.disbursement_line_item_id IS NULL ;
   	
	UPDATE tmp_agreement_con a
	SET master_agreement_id = b.master_agreement_id,
		maximum_contract_amount = b.maximum_contract_amount_fy,
		purpose = b.purpose_fy,
		contract_number = b.contract_number,
		contract_vendor_id = b.contract_vendor_id_fy,
		contract_agency_id = b.contract_agency_id_fy,
		contract_document_code = b.contract_document_code_fy
	FROM tmp_agreement_con_fy b
	WHERE a.disbursement_line_item_id = b.disbursement_line_item_id;
	
	-- Getting maximum_spending_limit, master_contract_number, master_contract_vendor_id, master_contract_agency_id for FY for master agreements
	
	CREATE TEMPORARY TABLE tmp_agreement_con_master_fy(disbursement_line_item_id bigint, master_agreement_id bigint, master_contract_number varchar, maximum_spending_limit_fy numeric(16,2), 
	master_contract_vendor_id_fy integer, master_contract_agency_id_fy smallint, master_purpose_fy varchar, master_contract_document_code_fy varchar)
	DISTRIBUTED  BY (disbursement_line_item_id);
	
	INSERT INTO tmp_agreement_con_master_fy
	SELECT a.disbursement_line_item_id, a.master_agreement_id,
	b.contract_number as master_contract_number,
	b.maximum_contract_amount as maximum_spending_limit_fy ,
	b.vendor_id as master_contract_vendor_id_fy,
	b.agency_id as master_contract_agency_id_fy,
	b.description as master_purpose_fy,
	e.document_code as master_contract_document_code_fy
	FROM tmp_agreement_con a JOIN agreement_snapshot b ON a.master_agreement_id = b.original_agreement_id AND b.master_agreement_yn = 'Y' AND a.fiscal_year between b.starting_year and b.ending_year
		JOIN disbursement_line_item c ON a.disbursement_line_item_id = c.disbursement_line_item_id
		JOIN disbursement d ON c.disbursement_id = d.disbursement_id 
		JOIN ref_document_code e ON b.document_code_id = e.document_code_id;
		
	INSERT INTO tmp_agreement_con_master_fy
	SELECT a.disbursement_line_item_id, a.master_agreement_id,
	b.contract_number as master_contract_number,
	b.maximum_contract_amount as maximum_spending_limit_fy ,
	b.vendor_id as master_contract_vendor_id_fy,
	b.agency_id as master_contract_agency_id_fy,
	b.description as master_purpose_fy ,
	e.document_code as master_contract_document_code_fy
	FROM tmp_agreement_con a JOIN agreement_snapshot b ON a.master_agreement_id = b.original_agreement_id AND b.master_agreement_yn = 'Y' AND b.latest_flag='Y'
		JOIN disbursement_line_item c ON a.disbursement_line_item_id = c.disbursement_line_item_id
		JOIN disbursement d ON c.disbursement_id = d.disbursement_id 
		JOIN ref_document_code e ON b.document_code_id = e.document_code_id
		LEFT JOIN tmp_agreement_con_master_fy f ON a.disbursement_line_item_id = f.disbursement_line_item_id
		WHERE f.disbursement_line_item_id IS NULL ;
		
		
	
	UPDATE tmp_agreement_con a
	SET maximum_spending_limit = b.maximum_spending_limit_fy,
		master_contract_number = b.master_contract_number,
		master_contract_vendor_id = b.master_contract_vendor_id_fy,
		master_contract_agency_id = b.master_contract_agency_id_fy,
		master_purpose = b.master_purpose_fy,
		master_contract_document_code = b.master_contract_document_code_fy
	FROM tmp_agreement_con_master_fy b
	WHERE a.disbursement_line_item_id = b.disbursement_line_item_id;
	
	
	
	RAISE NOTICE 'FMS RF 3';
	
	-- Getting maximum_contract_amount and purpose for CY 
	
	/* CREATE TEMPORARY TABLE tmp_agreement_con_cy(disbursement_line_item_id bigint,agreement_id bigint, 
						maximum_contract_amount_cy numeric(16,2),maximum_contract_amount_latest numeric(16,2), description_cy varchar,
						description_latest varchar)
	DISTRIBUTED  BY (disbursement_line_item_id);
	
	INSERT INTO tmp_agreement_con_cy
    SELECT a.disbursement_line_item_id, b.original_agreement_id,
	SUM(CASE WHEN a.calendar_fiscal_year between b.starting_year and b.ending_year THEN b.maximum_contract_amount ELSE 0 END) as maximum_contract_amount_cy ,
	SUM(CASE WHEN b.latest_flag='Y' THEN b.maximum_contract_amount ELSE 0 END) as maximum_contract_amount_latest ,
	MIN(CASE WHEN a.calendar_fiscal_year between b.starting_year and b.ending_year THEN b.description ELSE NULL END) as description_cy ,
	MIN(CASE WHEN b.latest_flag='Y' THEN b.description ELSE NULL END) as description_latest 
	FROM tmp_agreement_con a JOIN agreement_snapshot_cy b ON a.agreement_id = b.original_agreement_id 
		JOIN disbursement_line_item c ON a.disbursement_line_item_id = c.disbursement_line_item_id
		JOIN disbursement d ON c.disbursement_id = d.disbursement_id
		GROUP BY 1,2;
		
	UPDATE tmp_agreement_con a
	SET maximum_contract_amount_cy = COALESCE(b.maximum_contract_amount_cy, b.maximum_contract_amount_latest),
		purpose_cy = COALESCE(b.description_cy,b.description_latest)	
	FROM tmp_agreement_con_cy b
	WHERE a.disbursement_line_item_id = b.disbursement_line_item_id;
	
	-- Getting maximum_spending_limit for CY
	
	CREATE TEMPORARY TABLE tmp_agreement_con_master_cy(disbursement_line_item_id bigint, master_agreement_id_cy bigint, maximum_spending_limit_cy numeric(16,2),  maximum_spending_limit_latest numeric(16,2))
	DISTRIBUTED  BY (disbursement_line_item_id);
	
	INSERT INTO tmp_agreement_con_master_cy
	SELECT a.disbursement_line_item_id, a.master_agreement_id,
	SUM(CASE WHEN a.calendar_fiscal_year between b.starting_year and b.ending_year THEN b.maximum_contract_amount ELSE 0 END) as maximum_spending_limit_cy ,
	SUM(CASE WHEN b.latest_flag='Y' THEN b.maximum_contract_amount ELSE 0 END) as maximum_spending_limit_latest
	FROM tmp_agreement_con a JOIN agreement_snapshot_cy b ON a.master_agreement_id = b.original_agreement_id AND b.master_agreement_yn = 'Y'
		JOIN disbursement_line_item c ON a.disbursement_line_item_id = c.disbursement_line_item_id
		JOIN disbursement d ON c.disbursement_id = d.disbursement_id
		GROUP BY 1,2;
		
	UPDATE tmp_agreement_con a
	SET maximum_spending_limit_cy = COALESCE(b.maximum_spending_limit_cy, b.maximum_spending_limit_latest)
	FROM tmp_agreement_con_master_cy b
	WHERE a.disbursement_line_item_id = b.disbursement_line_item_id; */
	
	
	-- Getting maximum_contract_amount, master_agreement_id, purpose, contract_number,  contract_vendor_id, contract_agency_id for FY from non master contracts.
	
	CREATE TEMPORARY TABLE tmp_agreement_con_cy(disbursement_line_item_id bigint,agreement_id bigint,
						maximum_contract_amount_cy numeric(16,2), purpose_cy varchar, contract_vendor_id_cy integer, contract_agency_id_cy smallint)
	DISTRIBUTED  BY (disbursement_line_item_id);
	
	INSERT INTO tmp_agreement_con_cy
    SELECT a.disbursement_line_item_id, b.original_agreement_id,
	b.maximum_contract_amount as maximum_contract_amount_cy ,
	b.description as purpose_cy ,
	b.vendor_id as contract_vendor_id_cy,
	b.agency_id as contract_agency_id_cy
		FROM tmp_agreement_con a JOIN agreement_snapshot_cy b ON a.agreement_id = b.original_agreement_id AND a.fiscal_year between b.starting_year and b.ending_year
		JOIN disbursement_line_item c ON a.disbursement_line_item_id = c.disbursement_line_item_id
		JOIN ref_document_code e ON b.document_code_id = e.document_code_id;
		
	
	INSERT INTO tmp_agreement_con_cy
    SELECT a.disbursement_line_item_id, b.original_agreement_id,
	b.maximum_contract_amount as maximum_contract_amount_cy ,
	b.description as purpose_cy ,
	b.vendor_id as contract_vendor_id_cy,
	b.agency_id as contract_agency_id_cy
		FROM tmp_agreement_con a JOIN agreement_snapshot_cy b ON a.agreement_id = b.original_agreement_id AND b.latest_flag='Y'
		JOIN disbursement_line_item c ON a.disbursement_line_item_id = c.disbursement_line_item_id
		JOIN disbursement d ON c.disbursement_id = d.disbursement_id
		LEFT JOIN tmp_agreement_con_cy f ON a.disbursement_line_item_id = f.disbursement_line_item_id
		WHERE f.disbursement_line_item_id IS NULL ;
   
		
	UPDATE tmp_agreement_con a
	SET maximum_contract_amount_cy = b.maximum_contract_amount_cy,
		purpose_cy = b.purpose_cy,
		contract_vendor_id_cy = b.contract_vendor_id_cy,
		contract_agency_id_cy = b.contract_agency_id_cy
	FROM tmp_agreement_con_cy b
	WHERE a.disbursement_line_item_id = b.disbursement_line_item_id;
	
	-- Getting maximum_spending_limit, master_contract_number, master_contract_vendor_id, master_contract_agency_id for FY for master agreements
	
	CREATE TEMPORARY TABLE tmp_agreement_con_master_cy(disbursement_line_item_id bigint, master_agreement_id bigint,  maximum_spending_limit_cy numeric(16,2), 
	master_contract_vendor_id_cy integer, master_contract_agency_id_cy smallint, master_purpose_cy varchar)
	DISTRIBUTED  BY (disbursement_line_item_id);
	
	
	INSERT INTO tmp_agreement_con_master_cy
	SELECT a.disbursement_line_item_id, a.master_agreement_id,
	b.maximum_contract_amount as maximum_spending_limit_cy ,
	b.vendor_id as master_contract_vendor_id_cy,
	b.agency_id as master_contract_agency_id_cy,
	b.description as master_purpose_cy
	FROM tmp_agreement_con a JOIN agreement_snapshot_cy b ON a.master_agreement_id = b.original_agreement_id AND b.master_agreement_yn = 'Y' AND a.fiscal_year between b.starting_year and b.ending_year
		JOIN disbursement_line_item c ON a.disbursement_line_item_id = c.disbursement_line_item_id
		JOIN ref_document_code e ON b.document_code_id = e.document_code_id;
		
		
	INSERT INTO tmp_agreement_con_master_cy
	SELECT a.disbursement_line_item_id, a.master_agreement_id,
	b.maximum_contract_amount as maximum_spending_limit_cy ,
	b.vendor_id as master_contract_vendor_id_cy,
	b.agency_id as master_contract_agency_id_cy,
	b.description as master_purpose_cy 
	FROM tmp_agreement_con a JOIN agreement_snapshot_cy b ON a.master_agreement_id = b.original_agreement_id AND b.master_agreement_yn = 'Y' AND b.latest_flag='Y'
		JOIN disbursement_line_item c ON a.disbursement_line_item_id = c.disbursement_line_item_id
		JOIN disbursement d ON c.disbursement_id = d.disbursement_id 
		LEFT JOIN tmp_agreement_con_master_cy f ON a.disbursement_line_item_id = f.disbursement_line_item_id
		WHERE f.disbursement_line_item_id IS NULL ;
		
		

		
	UPDATE tmp_agreement_con a
	SET maximum_spending_limit_cy = b.maximum_spending_limit_cy,
		master_contract_vendor_id_cy = b.master_contract_vendor_id_cy,
		master_contract_agency_id_cy = b.master_contract_agency_id_cy,
		master_purpose_cy = b.master_purpose_cy
	FROM tmp_agreement_con_master_cy b
	WHERE a.disbursement_line_item_id = b.disbursement_line_item_id;
	
	RAISE NOTICE 'FMS RF 4';
	
	UPDATE disbursement_line_item_details a
	SET	agreement_id = a.agreement_id,
		master_agreement_id = b.master_agreement_id,		
		contract_number = b.contract_number,
		master_contract_number = b.master_contract_number,
		maximum_contract_amount =b.maximum_contract_amount,
		maximum_spending_limit = b.maximum_spending_limit,
		purpose = b.purpose,
		master_purpose = b.master_purpose,		
		contract_agency_id  = b.contract_agency_id ,
		master_contract_agency_id  = b.master_contract_agency_id,
		contract_vendor_id  = b.contract_vendor_id ,
		master_contract_vendor_id  = b.master_contract_vendor_id ,		
		maximum_contract_amount_cy =b.maximum_contract_amount_cy,
		maximum_spending_limit_cy = b.maximum_spending_limit_cy,
		purpose_cy = b.purpose_cy,
		master_purpose_cy = b.master_purpose_cy,		
		contract_agency_id_cy  = b.contract_agency_id_cy ,
		master_contract_agency_id_cy  = b.master_contract_agency_id_cy,
		contract_vendor_id_cy  = b.contract_vendor_id_cy ,
		master_contract_vendor_id_cy  = b.master_contract_vendor_id_cy,
		contract_document_code = b.contract_document_code,
		master_contract_document_code = b.master_contract_document_code,
		master_child_contract_agency_id = coalesce(b.master_contract_agency_id,b.contract_agency_id),
		master_child_contract_agency_id_cy = coalesce(b.master_contract_agency_id_cy,b.contract_agency_id_cy),
		master_child_contract_vendor_id = coalesce(b.master_contract_vendor_id,b.contract_vendor_id),
		master_child_contract_vendor_id_cy = coalesce(b.master_contract_vendor_id_cy,b.contract_vendor_id_cy),
		master_child_contract_number = coalesce(b.master_contract_number,b.contract_number)
	FROM	tmp_agreement_con  b
	WHERE   a.disbursement_line_item_id = b.disbursement_line_item_id;
	
	
	-- needs to delete after first load
	/*
	INSERT INTO disbursement_line_item_deleted(disbursement_line_item_id, load_id, deleted_date, job_id)
	SELECT a.disbursement_line_item_id, coalesce(a.updated_load_id, a.created_load_id), now()::timestamp, p_job_id_in
	FROM disbursement_line_item a, disbursement b
	WHERE   a.disbursement_id = b.disbursement_id 
	AND b.document_version > 1;
	 
	DELETE FROM ONLY disbursement_line_item_details a
	USING disbursement b
	WHERE   a.disbursement_id = b.disbursement_id 
	AND b.document_version > 1;
	
	DELETE FROM ONLY disbursement_line_item a
	USING disbursement b
	WHERE   a.disbursement_id = b.disbursement_id 
	AND b.document_version > 1;
	
	DELETE FROM ONLY disbursement
	WHERE   document_version > 1;
	
	*/
	-- needs to add the script which will delete the version > 1 if we do not have with version = 1
	
	
	 
	CREATE TEMPORARY TABLE tmp_disb_delete_ver_gt1_without_ver0(disbursement_id bigint)
	DISTRIBUTED  BY (disbursement_id);
	
	INSERT INTO tmp_disb_delete_ver_gt1_without_ver0
	select a.disbursement_id from 
	(select * from disbursement where document_version > 1) a 
	LEFT JOIN (select * from disbursement where document_version = 1) b 
	ON a.document_code_id = b.document_code_id  AND a.agency_history_id = b.agency_history_id AND a.document_id = b.document_id
	WHERE b.document_id IS NULL ;
	
	DELETE FROM disbursement a
	USING tmp_disb_delete_ver_gt1_without_ver0 b
	WHERE   a.disbursement_id = b.disbursement_id 
	AND a.document_version > 1 ;
	
	DELETE FROM ONLY disbursement_line_item_details a
	USING tmp_disb_delete_ver_gt1_without_ver0 b
	WHERE   a.disbursement_id = b.disbursement_id ;
	
	INSERT INTO disbursement_line_item_deleted(disbursement_line_item_id, load_id, deleted_date,job_id)
	SELECT a.disbursement_line_item_id, c.load_id, now()::timestamp,p_job_id_in
	FROM disbursement_line_item a, tmp_disb_delete_ver_gt1_without_ver0 b , etl.etl_data_load c
	WHERE   a.disbursement_id = b.disbursement_id 	AND coalesce(a.updated_load_id, a.created_load_id) = c.load_id ;
		
		
	DELETE FROM ONLY disbursement_line_item a
	USING tmp_disb_delete_ver_gt1_without_ver0 b
	WHERE   a.disbursement_id = b.disbursement_id ;
	
	DELETE FROM disbursement_line_item_details 
	WHERE reference_document_code IN ('MA1','MMA1') ;
	 
	 
	
	l_end_time := timeofday()::timestamp;
	
	INSERT INTO etl.etl_script_execution_status(job_id,script_name,completed_flag,start_time,end_time)
	VALUES(p_job_id_in,'etl.refreshFactsForFMS',1,l_start_time,l_end_time);
	
	RETURN 1;
	
EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in refreshFactsForFMS';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;	
	
	l_end_time := timeofday()::timestamp;
	
	INSERT INTO etl.etl_script_execution_status(job_id,script_name,completed_flag,start_time,end_time,errno,errmsg)
	VALUES(p_job_id_in,'etl.refreshFactsForFMS',0,l_start_time,l_end_time,SQLSTATE,SQLERRM);
	
	RETURN 0;
	
END;
$$ language plpgsql;	
