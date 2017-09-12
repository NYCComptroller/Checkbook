/* Functions defined
updateEmployees
updateForeignKeysForPMS
processPayroll
updateForeignKeysForPMSSummary
processPayrollSummary
*/

CREATE OR REPLACE FUNCTION etl.updateEmployees(p_load_file_id_in bigint,p_load_id_in bigint) RETURNS INT AS $$
DECLARE
l_count bigint;
BEGIN



	-- temp script which needs to be removed after we get the data for civil service title

	-- update etl.stg_payroll set civil_service_title = 'CIVIL SERVICE TITLE1' where agency_code = '127';
	-- update etl.stg_payroll set civil_service_title = 'CIVIL SERVICE TITLE2' where agency_code = '131';
	-- update etl.stg_payroll set civil_service_title = 'CIVIL SERVICE TITLE3' where agency_code = '15';



	UPDATE etl.stg_payroll SET agency_code = lpad(agency_code,3,'0');



	CREATE TEMPORARY TABLE tmp_fk_emp_pms_values(uniq_id bigint,agency_history_id smallint,	agency_id smallint, agency_name varchar, agency_short_name varchar)
	DISTRIBUTED BY (uniq_id);

	INSERT INTO tmp_fk_emp_pms_values(uniq_id,agency_history_id,agency_id,agency_name,agency_short_name)
	SELECT uniq_id,d.agency_history_id,d.agency_id,d.agency_name,d.agency_short_name
	FROM
		(SELECT	a.uniq_id, max(c.agency_history_id) as agency_history_id
		FROM etl.stg_payroll a JOIN ref_agency b ON a.agency_code = b.agency_code
		JOIN ref_agency_history c ON b.agency_id = c.agency_id
		GROUP BY 1 ) inner_tbl JOIN ref_agency_history d ON inner_tbl.agency_history_id = d.agency_history_id;

	RAISE NOTICE ' INSIDE PMS UE 1.a';

	CREATE TEMPORARY TABLE tmp_fk_emp_pms_values_new_agencies(agency_code varchar,uniq_id bigint)
	DISTRIBUTED BY (uniq_id);

	INSERT INTO tmp_fk_emp_pms_values_new_agencies
	SELECT agency_code,MIN(b.uniq_id) as uniq_id
	FROM etl.stg_payroll a join (SELECT uniq_id
						 FROM tmp_fk_emp_pms_values
						 GROUP BY 1
						 HAVING max(agency_history_id) is null) b on a.uniq_id=b.uniq_id
	GROUP BY 1;

	RAISE NOTICE ' INSIDE PMS UE 1.b';

	TRUNCATE etl.ref_agency_id_seq;

	INSERT INTO etl.ref_agency_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_fk_emp_pms_values_new_agencies;

	INSERT INTO ref_agency(agency_id,agency_code,agency_name,created_date,created_load_id,original_agency_name)
	SELECT a.agency_id,b.agency_code,'<Unknown Agency>' as agency_name,now()::timestamp,p_load_id_in,'<Unknown Agency>' as original_agency_name
	FROM   etl.ref_agency_id_seq a JOIN tmp_fk_emp_pms_values_new_agencies b ON a.uniq_id = b.uniq_id;

	GET DIAGNOSTICS l_count = ROW_COUNT;

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'P',l_count, 'Number of records inserted into ref_agency from payroll');
	END IF;

	RAISE NOTICE ' INSIDE PMS UE 1.c';

	-- Generate the agency history id for history records

	TRUNCATE etl.ref_agency_history_id_seq;

	INSERT INTO etl.ref_agency_history_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_fk_emp_pms_values_new_agencies;

	INSERT INTO ref_agency_history(agency_history_id,agency_id,agency_name,created_date,load_id)
	SELECT a.agency_history_id,b.agency_id,'<Unknown Agency>' as agency_name,now()::timestamp,p_load_id_in
	FROM   etl.ref_agency_history_id_seq a JOIN etl.ref_agency_id_seq b ON a.uniq_id = b.uniq_id;

	GET DIAGNOSTICS l_count = ROW_COUNT;

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'P',l_count, 'Number of records inserted into ref_agency_history from payroll');
	END IF;

	RAISE NOTICE ' INSIDE PMS UE 1.d';

	INSERT INTO tmp_fk_emp_pms_values(uniq_id,agency_history_id,agency_id,agency_name,agency_short_name)
	SELECT	a.uniq_id, c.agency_history_id,b.agency_id,c.agency_name,c.agency_short_name
	FROM etl.stg_payroll a JOIN ref_agency b ON a.agency_code = b.agency_code
		JOIN ref_agency_history c ON b.agency_id = c.agency_id
		JOIN etl.ref_agency_history_id_seq d ON c.agency_history_id = d.agency_history_id;


	UPDATE etl.stg_payroll a
	SET
		agency_history_id =ct_table.agency_history_id ,
		agency_id = ct_table.agency_id,
		agency_name = ct_table.agency_name,
		agency_short_name = ct_table.agency_short_name
	FROM
		(SELECT uniq_id,
			max(agency_history_id )as agency_history_id ,
			max(agency_id ) as agency_id ,
			max(agency_name ) as agency_name ,
			max(agency_short_name) as agency_short_name
		FROM	tmp_fk_emp_pms_values
		GROUP	BY 1) ct_table
	WHERE	a.uniq_id = ct_table.uniq_id;


	RAISE NOTICE ' INSIDE PMS UE 1.e';

	CREATE TEMPORARY TABLE tmp_ref_employee_prev(employee_number varchar,agency_id smallint,pay_date date,civil_service_code varchar,last_name varchar, civil_service_title varchar,
	civil_service_level varchar,civil_service_suffix varchar,rank_value int)
	DISTRIBUTED BY (employee_number);

	INSERT INTO tmp_ref_employee_prev
	SELECT 	a.employee_number,a.agency_id, pay_date,
		    max( a.civil_service_code) as  civil_service_code,
	        max(a.last_name) as last_name,
	        max(a.civil_service_title) as civil_service_title,
	        max(a.civil_service_level),
	        max(a.civil_service_suffix) as civil_service_suffix,
	        rank() over (partition by employee_number,agency_id order by pay_date DESC) as rank_value
	FROM   etl.stg_payroll a GROUP BY 1,2,3;

	CREATE TEMPORARY TABLE tmp_ref_employee_prev1(employee_number varchar,agency_id smallint,civil_service_code varchar,last_name varchar, civil_service_title varchar,
	civil_service_level varchar,civil_service_suffix varchar)
	DISTRIBUTED BY (employee_number);

	INSERT INTO tmp_ref_employee_prev1
	SELECT employee_number,agency_id,civil_service_code,last_name, civil_service_title,civil_service_level ,civil_service_suffix
	FROM tmp_ref_employee_prev
	WHERE rank_value= 1;

	CREATE TEMPORARY TABLE tmp_ref_employee(employee_number varchar,agency_id smallint,last_name varchar, civil_service_title varchar,
	civil_service_code varchar,civil_service_level varchar,civil_service_suffix varchar,exists_flag char(1), modified_flag char(1))
	DISTRIBUTED BY (employee_number);


	-- For all records check if data is modified/new

	INSERT INTO tmp_ref_employee
	SELECT DISTINCT
			a.employee_number,
			a.agency_id,
	        a.last_name,
	        a.civil_service_title,
	        a.civil_service_code,
	        a.civil_service_level,
	        a.civil_service_suffix,
	       (CASE WHEN b.employee_number IS NULL THEN 'N'  ELSE 'Y' END) as exists_flag,
	       (CASE WHEN b.employee_number IS NOT NULL  AND (a.civil_service_code <> b.civil_service_code OR a.civil_service_title <> b.civil_service_title OR a.civil_service_suffix <> b.civil_service_suffix  OR a.civil_service_level <> b.civil_service_level)  THEN 'Y' ELSE 'N' END) as modified_flag
	FROM   tmp_ref_employee_prev1 a LEFT JOIN employee b ON a.employee_number = b.employee_number AND a.agency_id = b.agency_id ;

	RAISE NOTICE 'PMS UE 1';

	TRUNCATE etl.employee_id_seq;

	RAISE NOTICE 'PMS UE 2';

	INSERT INTO etl.employee_id_seq(employee_number,agency_id)
	SELECT employee_number,agency_id
	FROM   tmp_ref_employee
	WHERE  exists_flag ='N';

	RAISE NOTICE 'PMS UE 3';

	INSERT INTO employee(employee_id,employee_number,agency_id,last_name,created_date,created_load_id,original_last_name,masked_name,civil_service_title,
	civil_service_code,civil_service_level,civil_service_suffix)
	SELECT a.employee_id,b.employee_number,b.agency_id,last_name,now()::timestamp,p_load_id_in,last_name,
		coalesce(last_name,'') as masked_name,civil_service_title,b.civil_service_code,civil_service_level,civil_service_suffix
	FROM   etl.employee_id_seq a JOIN tmp_ref_employee b ON a.employee_number = b.employee_number AND a.agency_id = b.agency_id;

	RAISE NOTICE 'PMS UE 4';

	TRUNCATE etl.employee_history_id_seq;

	RAISE NOTICE 'PMS UE 5';

	INSERT INTO etl.employee_history_id_seq(employee_number,agency_id)
	SELECT employee_number,agency_id
	FROM   tmp_ref_employee
	WHERE  exists_flag ='N'
		OR (exists_flag ='Y' and modified_flag='Y');

	RAISE NOTICE 'PMS UE 6';

	CREATE TEMPORARY TABLE tmp_ref_employee_1(employee_number varchar,agency_id smallint,last_name varchar, civil_service_title varchar,
	civil_service_code varchar,civil_service_level varchar,civil_service_suffix varchar,exists_flag char(1), modified_flag char(1), employee_id int)
	DISTRIBUTED BY (employee_id);

	INSERT INTO tmp_ref_employee_1
	SELECT a.*,b.employee_id FROM tmp_ref_employee a JOIN employee b ON a.employee_number = b.employee_number AND a.agency_id = b.agency_id
	WHERE exists_flag ='Y' and modified_flag='Y';

	RAISE NOTICE 'PMS UE 7';

	UPDATE employee a
	SET	last_name = b.last_name,
		masked_name = coalesce(b.last_name,'')  ,
		civil_service_title = b.civil_service_title,
		civil_service_code=b.civil_service_code,
		civil_service_level=b.civil_service_level,
		civil_service_suffix=b.civil_service_suffix,
		updated_date = now()::timestamp,
		updated_load_id = p_load_id_in
	FROM	tmp_ref_employee_1 b
	WHERE	a.employee_id = b.employee_id;

	RAISE NOTICE 'PMS UE 8';

	INSERT INTO employee_history(employee_history_id,employee_id,last_name,masked_name,civil_service_title,
	civil_service_code,civil_service_level,civil_service_suffix,created_date,created_load_id)
	SELECT a.employee_history_id,c.employee_id,b.last_name,
	coalesce(b.last_name,'') as masked_name,b.civil_service_title,b.civil_service_code,b.civil_service_level,b.civil_service_suffix,
						     now()::timestamp,p_load_id_in
	FROM   etl.employee_history_id_seq a JOIN tmp_ref_employee b ON a.employee_number = b.employee_number AND a.agency_id = b.agency_id
		JOIN employee c ON b.employee_number = c.employee_number AND b.agency_id = c.agency_id
	WHERE   exists_flag ='N'
		OR (exists_flag ='Y' and modified_flag='Y') ;

	RAISE NOTICE 'PMS UE 9';
	RETURN 1;

EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in updateEmployees';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;

	RETURN 0;

END;
$$ language plpgsql;

-------------------------------------------------------------------------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION etl.updateForeignKeysForPMS(p_load_file_id_in bigint,p_load_id_in bigint) RETURNS INT AS $$
DECLARE
	l_count bigint;
BEGIN

	RAISE NOTICE ' INSIDE PMS 1';
	CREATE TEMPORARY TABLE tmp_fk_pms_values(uniq_id bigint,employee_history_id int, pay_date_id int,agency_history_id smallint,orig_pay_date_id int,
					department_history_id integer,amount_basis_id smallint,payroll_id bigint, action_flag char(1),
					agency_id smallint, agency_name varchar,department_id integer,
					department_name varchar,expenditure_object_id integer,
					fiscal_year smallint,fiscal_year_id smallint, employee_id bigint, employee_name varchar,civil_service_title varchar,
					calendar_fiscal_year_id smallint, calendar_fiscal_year smallint,
					agency_short_name varchar,department_short_name varchar)
	DISTRIBUTED BY (uniq_id);

	-- FK:pay_date_id

	INSERT INTO tmp_fk_pms_values(uniq_id,pay_date_id,calendar_fiscal_year_id,calendar_fiscal_year, fiscal_year_id, fiscal_year)
	SELECT	a.uniq_id, b.date_id,c.year_id as calendar_fiscal_year_id, d.year_value, b.nyc_year_id, e.year_value
	FROM etl.stg_payroll a JOIN ref_date b ON a.pay_date = b.date
		JOIN ref_month c ON b.calendar_month_id = c.month_id
		JOIN ref_year d ON c.year_id = d.year_id
		JOIN ref_year e ON b.nyc_year_id = e.year_id;

	-- FK:Agency_history_id

	INSERT INTO tmp_fk_pms_values(uniq_id,agency_history_id,agency_id,agency_name,agency_short_name)
	SELECT uniq_id,d.agency_history_id,d.agency_id,d.agency_name,d.agency_short_name
	FROM
		(SELECT	a.uniq_id, max(c.agency_history_id) as agency_history_id
		FROM etl.stg_payroll a JOIN ref_agency b ON a.agency_code = b.agency_code
		JOIN ref_agency_history c ON b.agency_id = c.agency_id
		GROUP BY 1 ) inner_tbl JOIN ref_agency_history d ON inner_tbl.agency_history_id = d.agency_history_id;

	RAISE NOTICE ' INSIDE PMS 1.a';

	CREATE TEMPORARY TABLE tmp_fk_pms_values_new_agencies(agency_code varchar,uniq_id bigint)
	DISTRIBUTED BY (uniq_id);

	INSERT INTO tmp_fk_pms_values_new_agencies
	SELECT agency_code,MIN(b.uniq_id) as uniq_id
	FROM etl.stg_payroll a join (SELECT uniq_id
						 FROM tmp_fk_pms_values
						 GROUP BY 1
						 HAVING max(agency_history_id) is null) b on a.uniq_id=b.uniq_id
	GROUP BY 1;

	RAISE NOTICE ' PMS 1';

	TRUNCATE etl.ref_agency_id_seq;

	INSERT INTO etl.ref_agency_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_fk_pms_values_new_agencies;

	INSERT INTO ref_agency(agency_id,agency_code,agency_name,created_date,created_load_id,original_agency_name)
	SELECT a.agency_id,b.agency_code,'<Unknown Agency>' as agency_name,now()::timestamp,p_load_id_in,'<Unknown Agency>' as original_agency_name
	FROM   etl.ref_agency_id_seq a JOIN tmp_fk_pms_values_new_agencies b ON a.uniq_id = b.uniq_id;

	GET DIAGNOSTICS l_count = ROW_COUNT;

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'P',l_count, 'Number of records inserted into ref_agency from payroll');
	END IF;

	RAISE NOTICE 'PMS 1.1';

	-- Generate the agency history id for history records

	TRUNCATE etl.ref_agency_history_id_seq;

	INSERT INTO etl.ref_agency_history_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_fk_pms_values_new_agencies;

	INSERT INTO ref_agency_history(agency_history_id,agency_id,agency_name,created_date,load_id)
	SELECT a.agency_history_id,b.agency_id,'<Unknown Agency>' as agency_name,now()::timestamp,p_load_id_in
	FROM   etl.ref_agency_history_id_seq a JOIN etl.ref_agency_id_seq b ON a.uniq_id = b.uniq_id;

	GET DIAGNOSTICS l_count = ROW_COUNT;

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'P',l_count, 'Number of records inserted into ref_agency_history from payroll');
	END IF;

	RAISE NOTICE '1.3';
	INSERT INTO tmp_fk_pms_values(uniq_id,agency_history_id,agency_id,agency_name,agency_short_name)
	SELECT	a.uniq_id, c.agency_history_id,b.agency_id,c.agency_name,c.agency_short_name
	FROM etl.stg_payroll a JOIN ref_agency b ON a.agency_code = b.agency_code
		JOIN ref_agency_history c ON b.agency_id = c.agency_id
		JOIN etl.ref_agency_history_id_seq d ON c.agency_history_id = d.agency_history_id;

	-- FK:orig_pay_date_id

	INSERT INTO tmp_fk_pms_values(uniq_id,orig_pay_date_id)
	SELECT	a.uniq_id, b.date_id
	FROM etl.stg_payroll a JOIN ref_date b ON a.orig_pay_date = b.date;

	-- FK:department_history_id
	-- Basis - PMS transactions are for general fund only

	INSERT INTO tmp_fk_pms_values(uniq_id,department_history_id,department_id,department_name,department_short_name)
	SELECT uniq_id, f.department_history_id,f.department_id,f.department_name ,f.department_short_name
	FROM
		(SELECT	a.uniq_id, max(c.department_history_id) as department_history_id
		FROM etl.stg_payroll a JOIN ref_department b ON a.department_code = b.department_code AND a.fiscal_year = b.fiscal_year
			JOIN ref_department_history c ON b.department_id = c.department_id
			JOIN ref_agency d ON a.agency_code = d.agency_code AND b.agency_id = d.agency_id
			JOIN ref_fund_class e ON '001' = e.fund_class_code AND e.fund_class_id = b.fund_class_id
		GROUP BY 1) inner_tbl JOIN ref_department_history f ON inner_tbl.department_history_id = f.department_history_id;


	CREATE TEMPORARY TABLE tmp_fk_values_pms_new_dept(agency_id integer,department_code varchar,
						fund_class_id smallint,fiscal_year smallint, uniq_id bigint)
	DISTRIBUTED BY (uniq_id);

	INSERT INTO tmp_fk_values_pms_new_dept
	SELECT c.agency_id,a.department_code,e.fund_class_id,a.fiscal_year,MIN(b.uniq_id) as uniq_id
	FROM etl.stg_payroll a join (SELECT uniq_id
						 FROM tmp_fk_pms_values
						 GROUP BY 1
						 HAVING max(department_history_id) IS NULL) b on a.uniq_id=b.uniq_id
		JOIN ref_agency c ON a.agency_code = c.agency_code
		JOIN ref_fund_class e ON '001' = e.fund_class_code
	GROUP BY 1,2,3,4;

	RAISE NOTICE '1.4';

	-- Generate the department id for new records

	TRUNCATE etl.ref_department_id_seq;

	INSERT INTO etl.ref_department_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_fk_values_pms_new_dept;

	INSERT INTO ref_department(department_id,department_code,
				   department_name,
				   agency_id,fund_class_id,
				   fiscal_year,created_date,created_load_id,original_department_name)
	SELECT a.department_id,COALESCE(b.department_code,'---------') as department_code,
		(CASE WHEN COALESCE(b.department_code,'') <> '' THEN '<Unknown Department>'
			ELSE 'Non-Applicable Department' END) as department_name,
		b.agency_id,b.fund_class_id,b.fiscal_year,
		now()::timestamp,p_load_id_in,
		(CASE WHEN COALESCE(b.department_code,'') <> '' THEN '<Unknown Department>'
			ELSE 'Non-Applicable Department' END) as original_department_name
	FROM   etl.ref_department_id_seq a JOIN tmp_fk_values_pms_new_dept b ON a.uniq_id = b.uniq_id;

	GET DIAGNOSTICS l_count = ROW_COUNT;

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'P',l_count, 'Number of records inserted into ref_department from payroll');
	END IF;

	RAISE NOTICE '1.5';
	-- Generate the department history id for history records

	TRUNCATE etl.ref_department_history_id_seq;

	INSERT INTO etl.ref_department_history_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_fk_values_pms_new_dept;

	INSERT INTO ref_department_history(department_history_id,department_id,
					   department_name,agency_id,fund_class_id,
					   fiscal_year,created_date,load_id)
	SELECT a.department_history_id,c.department_id,
		(CASE WHEN COALESCE(b.department_code,'') <> '' THEN '<Unknown Department>'
		      ELSE 'Non-Applicable Department' END) as department_name,
		b.agency_id,b.fund_class_id,b.fiscal_year,now()::timestamp,p_load_id_in
	FROM   etl.ref_department_history_id_seq a JOIN tmp_fk_values_pms_new_dept b ON a.uniq_id = b.uniq_id
		JOIN etl.ref_department_id_seq  c ON a.uniq_id = c.uniq_id ;

	GET DIAGNOSTICS l_count = ROW_COUNT;

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'P',l_count, 'Number of records inserted into ref_department_history from payroll');
	END IF;


	RAISE NOTICE '1.6';

	INSERT INTO tmp_fk_pms_values(uniq_id,department_history_id,department_id,department_name,department_short_name)
	SELECT	a.uniq_id, c.department_history_id,b.department_id,c.department_name ,c.department_short_name
	FROM etl.stg_payroll a JOIN ref_department b  ON a.department_code = b.department_code AND a.fiscal_year = b.fiscal_year
		JOIN ref_department_history c ON b.department_id = c.department_id
		JOIN ref_agency d ON a.agency_code = d.agency_code AND b.agency_id = d.agency_id
		JOIN ref_fund_class e ON '001' = e.fund_class_code AND e.fund_class_id = b.fund_class_id
		JOIN etl.ref_department_history_id_seq f ON c.department_history_id = f.department_history_id;

	RAISE NOTICE '1.7';

	-- FK: Fiscal Year
	/*
	INSERT INTO tmp_fk_pms_values(uniq_id,fiscal_year_id)
	SELECT a.uniq_id,b.year_id
	FROM 	etl.stg_payroll a JOIN  ref_year b ON a.fiscal_year = b.year_value;
	*/
	-- FK:amount_basis_id

	INSERT INTO tmp_fk_pms_values(uniq_id,amount_basis_id)
	SELECT	a.uniq_id, b.amount_basis_id
	FROM etl.stg_payroll a JOIN ref_amount_basis b ON a.amount_basis = b.amount_basis_name;

	-- FK: employee_history_id

	INSERT INTO tmp_fk_pms_values(uniq_id,employee_history_id,employee_id, employee_name, civil_service_title)
	SELECT uniq_id, d.employee_history_id,d.employee_id, d.masked_name, d.civil_service_title
	FROM
		(SELECT a.uniq_id,max(c.employee_history_id) as employee_history_id
		FROM	etl.stg_payroll a JOIN employee b ON a.employee_number = b.employee_number AND a.agency_id = b.agency_id
			JOIN employee_history c ON b.employee_id = c.employee_id
		GROUP BY 1 )inner_tbl join employee_history d ON d.employee_history_id = inner_tbl.employee_history_id	;

	-- FK: payroll_id


	TRUNCATE etl.payroll_id_seq;

	INSERT INTO etl.payroll_id_seq(uniq_id)
	SELECT DISTINCT a.uniq_id
	FROM 	etl.stg_payroll a;

	RAISE NOTICE '1.8';

	INSERT INTO tmp_fk_pms_values(uniq_id,payroll_id,action_flag)
	SELECT uniq_id,payroll_id,'I' as action_flag
	FROM   etl.payroll_id_seq;

	RAISE NOTICE '1.8.1';

	UPDATE etl.stg_payroll a
	SET
		pay_date_id =ct_table.pay_date_id ,
		agency_history_id =ct_table.agency_history_id ,
		orig_pay_date_id =ct_table.orig_pay_date_id ,
		department_history_id=ct_table.department_history_id,
		amount_basis_id = ct_table.amount_basis_id,
		employee_history_id = ct_table.employee_history_id,
		payroll_id = ct_table.payroll_id,
		action_flag = ct_table.action_flag,
		agency_id = ct_table.agency_id,
		agency_name = ct_table.agency_name,
		department_id = ct_table.department_id,
		department_name = ct_table.department_name,
		employee_id = ct_table.employee_id,
		employee_name = ct_table.employee_name,
	--	civil_service_title = ct_table.civil_service_title,
		fiscal_year_id = ct_table.fiscal_year_id,
		fiscal_year = ct_table.fiscal_year,
		calendar_fiscal_year_id = ct_table.calendar_fiscal_year_id,
		calendar_fiscal_year = ct_table.calendar_fiscal_year,
		agency_short_name = ct_table.agency_short_name,
		department_short_name = ct_table.department_short_name
	FROM
		(SELECT uniq_id,
			max(pay_date_id )as pay_date_id ,
			max(agency_history_id )as agency_history_id ,
			max(orig_pay_date_id )as orig_pay_date_id ,
			max(department_history_id )as department_history_id ,
			max(amount_basis_id) as amount_basis_id,
			max(employee_history_id) as employee_history_id,
			max(payroll_id) as payroll_id,
			max(action_flag) as action_flag,
			max(agency_id ) as agency_id ,
			max(agency_name ) as agency_name ,
			max(department_id) as department_id,
			max(department_name ) as department_name ,
			max(fiscal_year_id) as fiscal_year_id,
			max(fiscal_year) as fiscal_year,
			max(employee_id) as employee_id,
			max(employee_name) as employee_name,
			max(civil_service_title) as civil_service_title,
			max(calendar_fiscal_year_id) as calendar_fiscal_year_id,
			max(calendar_fiscal_year) as calendar_fiscal_year,
			max(agency_short_name) as agency_short_name,
			max(department_short_name) as department_short_name
		FROM	tmp_fk_pms_values
		GROUP	BY 1) ct_table
	WHERE	a.uniq_id = ct_table.uniq_id;

	RAISE NOTICE '1.9';
	RETURN 1;

EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in updateForeignKeysForPMS';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;

	RETURN 0;

END;
$$ language plpgsql;
----------------------------------------------------------------------------------------------------------------------------------------------------------------

-- Function: etl.processpayroll(integer, bigint)

-- DROP FUNCTION etl.processpayroll(integer, bigint);

CREATE OR REPLACE FUNCTION etl.processpayroll(p_load_file_id_in integer, p_load_id_in bigint)
  RETURNS integer AS
$BODY$
DECLARE
	l_count bigint;
	l_fk_update smallint;
	l_job_id bigint;

BEGIN
	l_fk_update := etl.updateEmployees(p_load_file_id_in,p_load_id_in);

	IF l_fk_update = 1 THEN
		l_fk_update := etl.updateForeignKeysForPMS(p_load_file_id_in,p_load_id_in);
	ELSE
		RETURN -1;
	END IF;

	IF l_fk_update <> 1 THEN
		RETURN -1;
	END IF;

	SELECT b.job_id
		FROM   etl.etl_data_load_file a JOIN etl.etl_data_load b ON a.load_id = b.load_id
		WHERE  a.load_file_id = p_load_file_id_in  INTO  l_job_id;

	RAISE NOTICE 'PAYROLL 1.1';
	INSERT INTO payroll(payroll_id, pay_cycle_code, pay_date_id, employee_history_id,employee_number,
						  payroll_number, job_sequence_number ,agency_history_id,fiscal_year,agency_start_date,
						  orig_pay_cycle_code,orig_pay_date_id,pay_frequency,department_history_id,annual_salary_original,annual_salary,
						  amount_basis_id,base_pay_original,base_pay,
						  overtime_pay_original,overtime_pay,other_payments_original,other_payments,
						  gross_pay_original,gross_pay,  civil_service_title,
						  salaried_amount,
						  non_salaried_amount,
						  agency_id,agency_code,agency_name,
						  department_id,department_code,department_name,
						  employee_id,employee_name,fiscal_year_id,pay_date,
						  calendar_fiscal_year_id,calendar_fiscal_year,
						  agency_short_name,department_short_name,
						  created_date,created_load_id,job_id)
	SELECT payroll_id, pay_cycle_code, pay_date_id, employee_history_id,employee_number,
	       payroll_number, job_sequence_number ,agency_history_id,fiscal_year,agency_start_date,
	       orig_pay_cycle_code,orig_pay_date_id,pay_frequency,department_history_id,annual_salary as annual_salary_original,coalesce(annual_salary,0) as annual_salary,
	       amount_basis_id,base_pay as base_pay_original,coalesce(base_pay,0) as base_pay,
	       overtime_pay as overtime_pay_original,coalesce(overtime_pay,0) as overtime_pay, other_payments as other_payments_original,coalesce(other_payments,0) as other_payments,
	       gross_pay as gross_pay_original,coalesce(gross_pay,0) as gross_pay, civil_service_title,
	       (CASE WHEN amount_basis_id = 1 THEN coalesce(annual_salary,0) ELSE NULL END) as salaried_amount,
	       (CASE WHEN amount_basis_id = 1 THEN NULL ELSE coalesce(annual_salary,0)  END) as non_salaried_amount,
	       agency_id,agency_code,agency_name,
	       department_id,department_code,department_name,
	       employee_id,employee_name,fiscal_year_id,pay_date,
	       calendar_fiscal_year_id,calendar_fiscal_year,
	       agency_short_name,department_short_name,
	       now()::timestamp,p_load_id_in,l_job_id
	FROM   etl.stg_payroll
	WHERE  action_flag = 'I';

	GET DIAGNOSTICS l_count = ROW_COUNT;
	INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
	VALUES(p_load_file_id_in,'P',l_count,'# of records inserted into payroll');

	/*

	INSERT INTO payroll_future_data
	SELECT * FROM payroll
	WHERE job_id =  l_job_id AND pay_date > CURRENT_DATE ;

	DELETE FROM payroll
	WHERE job_id =  l_job_id AND pay_date > CURRENT_DATE ;

	UPDATE payroll_future_data
	SET job_id = l_job_id
	WHERE pay_date <= CURRENT_DATE ;

	INSERT INTO payroll
	SELECT * FROM payroll_future_data
	WHERE job_id = l_job_id AND pay_date <= CURRENT_DATE ;


	DELETE FROM payroll_future_data
	WHERE job_id = l_job_id AND pay_date <= CURRENT_DATE ;

	*/

	UPDATE payroll_future_data
	SET job_id = l_job_id ;

	INSERT INTO payroll
	SELECT * FROM payroll_future_data
	WHERE job_id = l_job_id ;


	DELETE FROM payroll_future_data
	WHERE job_id = l_job_id  ;



	-- Updating the gross pay YTD based on budget fiscal year

	RAISE NOTICE 'PAYROLL 1.2';


	CREATE TEMPORARY TABLE tmp_employee_rec_gross_pay(payroll_id bigint, employee_id bigint, agency_id smallint, payroll_number varchar,job_sequence_number varchar,fiscal_year smallint, pay_date date)
	DISTRIBUTED BY (payroll_id);

	INSERT INTO tmp_employee_rec_gross_pay
	SELECT  DISTINCT a.payroll_id,a.employee_id, a.agency_id, a.payroll_number, a.job_sequence_number,
		a.fiscal_year,a.pay_date
	FROM   payroll a JOIN etl.stg_payroll b ON a.employee_id = b.employee_id
			AND a.agency_id = b.agency_id
			AND a.payroll_number = b.payroll_number
			AND a.job_sequence_number = b.job_sequence_number
			AND a.fiscal_year = b.fiscal_year
			AND a.pay_date >= b.pay_date;


	RAISE NOTICE 'PAYROLL 1.3';

	CREATE TEMPORARY TABLE tmp_employee_rec_gross_pay_1(payroll_id bigint, gross_pay_ytd numeric(16,2),created_load_id integer)
	DISTRIBUTED BY (payroll_id);

	INSERT INTO tmp_employee_rec_gross_pay_1
	SELECT b.payroll_id, sum(a.gross_pay) as gross_pay_ytd, MIN(created_load_id) as created_load_id
	FROM	payroll a JOIN tmp_employee_rec_gross_pay b ON a.employee_id = b.employee_id
			AND a.agency_id = b.agency_id
			AND a.payroll_number = b.payroll_number
			AND a.job_sequence_number = b.job_sequence_number
			AND b.pay_date >= a.pay_date
			AND a.fiscal_year = b.fiscal_year
	GROUP BY 1;

	RAISE NOTICE 'PAYROLL 1.4';

	UPDATE payroll a
	SET    gross_pay_ytd = b.gross_pay_ytd,
	       updated_load_id = (CASE WHEN b.created_load_id <> a.created_load_id THEN p_load_id_in END),
	       updated_date =  (CASE WHEN b.created_load_id <> a.created_load_id THEN now()::timestamp END),
	       job_id = l_job_id
	FROM   tmp_employee_rec_gross_pay_1 b
	WHERE	a.payroll_id = b.payroll_id;




	RAISE NOTICE 'PAYROLL 1.5';
	-- Updating the gross pay YTD based on calendar fiscal year

	TRUNCATE tmp_employee_rec_gross_pay;

	INSERT INTO tmp_employee_rec_gross_pay
	SELECT  DISTINCT a.payroll_id,a.employee_id, a.agency_id, a.payroll_number, a.job_sequence_number,
		a.calendar_fiscal_year,a.pay_date
	FROM   payroll a JOIN etl.stg_payroll b ON a.employee_id = b.employee_id
			AND a.agency_id = b.agency_id
		    AND a.payroll_number = b.payroll_number
			AND a.job_sequence_number = b.job_sequence_number
			AND a.calendar_fiscal_year = b.calendar_fiscal_year
			AND a.pay_date >= b.pay_date;

	RAISE NOTICE 'PAYROLL 1.6';


	TRUNCATE tmp_employee_rec_gross_pay_1;

	INSERT INTO tmp_employee_rec_gross_pay_1
	SELECT b.payroll_id, sum(a.gross_pay) as gross_pay_ytd, MIN(created_load_id) as created_load_id
	FROM	payroll a JOIN tmp_employee_rec_gross_pay b ON a.employee_id = b.employee_id
			AND a.agency_id = b.agency_id
			AND a.payroll_number = b.payroll_number
			AND a.job_sequence_number = b.job_sequence_number
			AND b.pay_date >= a.pay_date
			AND a.calendar_fiscal_year = b.fiscal_year
	GROUP BY 1;


	RAISE NOTICE 'PAYROLL 1.7';


	UPDATE payroll a
	SET    gross_pay_cytd = b.gross_pay_ytd,
	       updated_load_id = (CASE WHEN b.created_load_id <> a.created_load_id THEN p_load_id_in END),
	       updated_date =  (CASE WHEN b.created_load_id <> a.created_load_id THEN now()::timestamp END),
	       job_id = l_job_id
	FROM   tmp_employee_rec_gross_pay_1 b
	WHERE	a.payroll_id = b.payroll_id;



	RAISE NOTICE 'PAYROLL 1.8';

	RETURN 1;

EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in processPayroll';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;

	RETURN 0;

END;
$BODY$
  LANGUAGE plpgsql VOLATILE;
ALTER FUNCTION etl.processpayroll(integer, bigint)
  OWNER TO gpadmin;

-------------------------------------------------------------------------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION etl.updateforeignkeysforpmssummary(p_load_file_id_in bigint,p_load_id_in bigint)
  RETURNS integer AS
$BODY$
DECLARE
	l_count bigint;
BEGIN



	CREATE TEMPORARY TABLE tmp_fk_pms_summay_values(uniq_id bigint,pay_date_id int,agency_history_id smallint,agency_id smallint,
				        agency_name varchar,department_history_id integer,department_id integer,
				        department_name varchar,expenditure_object_history_id integer,expenditure_object_id integer,
				        expenditure_object_name varchar,budget_code_id integer,
				        budget_code_name varchar,fiscal_year_id smallint,payroll_summary_id bigint, action_flag char(1),
				        fiscal_year smallint, calendar_fiscal_year smallint, calendar_fiscal_year_id smallint,
				        calendar_month_id int, fund_class_id smallint,agency_short_name varchar, department_short_name varchar)
	DISTRIBUTED BY (uniq_id);

	UPDATE etl.stg_payroll_summary
	SET object = NULL
	WHERE object = '';

	UPDATE etl.stg_payroll_summary
	SET agency = NULL
	WHERE agency = '';

	UPDATE etl.stg_payroll_summary
	SET uoa = NULL
	WHERE uoa = '';


	INSERT INTO tmp_fk_pms_summay_values(uniq_id)
	SELECT DISTINCT  uniq_id
	FROM etl.stg_payroll_summary;


	-- FK:pay_date_id

	INSERT INTO tmp_fk_pms_summay_values(uniq_id,pay_date_id,fiscal_year, fiscal_year_id, calendar_fiscal_year, calendar_fiscal_year_id,calendar_month_id)
	SELECT	a.uniq_id, b.date_id,c.year_value,b.nyc_year_id,e.year_value,e.year_id,b.calendar_month_id
	FROM etl.stg_payroll_summary a JOIN ref_date b ON a.pay_date = b.date
		JOIN ref_year c ON b.nyc_year_id = c.year_id
		JOIN ref_month d ON b.calendar_month_id = d.month_id
		JOIN ref_year e ON e.year_id = d.year_id;


	INSERT INTO tmp_fk_pms_summay_values(uniq_id,fund_class_id)
	SELECT uniq_id,b.fund_class_id
	FROM etl.stg_payroll_summary a ,ref_fund_class b WHERE b.fund_class_code='001';

	-- FK:Agency_history_id

	INSERT INTO tmp_fk_pms_summay_values(uniq_id,agency_history_id,agency_id,agency_name,agency_short_name)
	SELECT	a.uniq_id, max(c.agency_history_id) as agency_history_id,b.agency_id,b.agency_name,b.agency_short_name
	FROM etl.stg_payroll_summary a JOIN ref_agency b ON coalesce(a.agency,'---') = b.agency_code
		JOIN ref_agency_history c ON b.agency_id = c.agency_id
	GROUP BY 1,3,4,5;

	CREATE TEMPORARY TABLE tmp_fk_pms_summary_values_new_agencies(agency_code varchar,uniq_id bigint)
	DISTRIBUTED BY (uniq_id);

	INSERT INTO tmp_fk_pms_summary_values_new_agencies
	SELECT coalesce(agency,'---'),MIN(b.uniq_id) as uniq_id
	FROM etl.stg_payroll_summary a join (SELECT uniq_id
						 FROM tmp_fk_pms_summay_values
						 GROUP BY 1
						 HAVING max(agency_history_id) is null) b on a.uniq_id=b.uniq_id
	GROUP BY 1;

	RAISE NOTICE '1';

	TRUNCATE etl.ref_agency_id_seq;

	INSERT INTO etl.ref_agency_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_fk_pms_summary_values_new_agencies;

	INSERT INTO ref_agency(agency_id,agency_code,agency_name,created_date,created_load_id,original_agency_name)
	SELECT a.agency_id,coalesce(b.agency_code,'---'),(CASE WHEN COALESCE(b.agency_code,'---')='---' THEN '<Non-Applicable Agency>' ELSE '<Unknown Agency>' END) as agency_name,
	now()::timestamp,p_load_id_in,'<Unknown Agency>' as original_agency_name
	FROM   etl.ref_agency_id_seq a JOIN tmp_fk_pms_summary_values_new_agencies b ON a.uniq_id = b.uniq_id;



	GET DIAGNOSTICS l_count = ROW_COUNT;

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'PS',l_count, 'Number of records inserted into ref_agency from payroll summary');
	END IF;

	RAISE NOTICE '1.1';

	-- Generate the agency history id for history records

	TRUNCATE etl.ref_agency_history_id_seq;

	INSERT INTO etl.ref_agency_history_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_fk_pms_summary_values_new_agencies;

	INSERT INTO ref_agency_history(agency_history_id,agency_id,agency_name,created_date,load_id)
	SELECT a.agency_history_id,b.agency_id,'<Unknown Agency>' as agency_name,now()::timestamp,p_load_id_in
	FROM   etl.ref_agency_history_id_seq a JOIN etl.ref_agency_id_seq b ON a.uniq_id = b.uniq_id;

	GET DIAGNOSTICS l_count = ROW_COUNT;

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'PS',l_count, 'Number of records inserted into ref_agency_history from payroll summary');
	END IF;

	RAISE NOTICE '1.3';
	INSERT INTO tmp_fk_pms_summay_values(uniq_id,agency_history_id,agency_id,agency_name,agency_short_name)
	SELECT	a.uniq_id, max(c.agency_history_id) ,b.agency_id,b.agency_name,b.agency_short_name
	FROM etl.stg_payroll_summary a JOIN ref_agency b ON a.agency = b.agency_code
		JOIN ref_agency_history c ON b.agency_id = c.agency_id
		JOIN etl.ref_agency_history_id_seq d ON c.agency_history_id = d.agency_history_id
	GROUP BY 1,3,4,5	;

	-- FK:department_history_id
	-- Basis - PMS transactions are for general fund only

	INSERT INTO tmp_fk_pms_summay_values(uniq_id,department_history_id,department_id,department_name,department_short_name)
	SELECT	a.uniq_id, max(c.department_history_id),b.department_id,b.department_name,b.department_short_name
	FROM etl.stg_payroll_summary a JOIN ref_department b ON coalesce(a.uoa,'---------') = b.department_code AND a.pms_fy = b.fiscal_year
		JOIN ref_department_history c ON b.department_id = c.department_id
		JOIN ref_agency d ON a.agency = d.agency_code AND b.agency_id = d.agency_id
		JOIN ref_fund_class e ON '001' = e.fund_class_code AND e.fund_class_id = b.fund_class_id
	GROUP BY 1,3,4,5;

	CREATE TEMPORARY TABLE tmp_fk_values_pms_summary_new_dept(agency_id integer,department_code varchar,
						fund_class_id smallint,fiscal_year smallint, uniq_id bigint)
	DISTRIBUTED BY (uniq_id);

	INSERT INTO tmp_fk_values_pms_summary_new_dept
	SELECT c.agency_id,coalesce(a.uoa,'---------'),e.fund_class_id,a.pms_fy,MIN(b.uniq_id) as uniq_id
	FROM etl.stg_payroll_summary a join (SELECT uniq_id
						 FROM tmp_fk_pms_summay_values
						 GROUP BY 1
						 HAVING max(department_history_id) IS NULL) b on a.uniq_id=b.uniq_id
		JOIN ref_agency c ON a.agency = c.agency_code
		JOIN ref_fund_class e ON '001' = e.fund_class_code
	GROUP BY 1,2,3,4;

	RAISE NOTICE '1.4';

	-- Generate the department id for new records

	TRUNCATE etl.ref_department_id_seq;

	INSERT INTO etl.ref_department_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_fk_values_pms_summary_new_dept;

	INSERT INTO ref_department(department_id,department_code,
				   department_name,
				   agency_id,fund_class_id,
				   fiscal_year,created_date,created_load_id,original_department_name)
	SELECT a.department_id,COALESCE(b.department_code,'---------') as department_code,
		(CASE WHEN COALESCE(b.department_code,'---------') <> '---------' THEN '<Unknown Department>'
			ELSE 'Non-Applicable Department' END) as department_name,
		b.agency_id,b.fund_class_id,b.fiscal_year,
		now()::timestamp,p_load_id_in,
		(CASE WHEN COALESCE(b.department_code,'---------') <> '---------' THEN '<Unknown Department>'
			ELSE 'Non-Applicable Department' END) as original_department_name
	FROM   etl.ref_department_id_seq a JOIN tmp_fk_values_pms_summary_new_dept b ON a.uniq_id = b.uniq_id;

	GET DIAGNOSTICS l_count = ROW_COUNT;

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'PS',l_count, 'Number of records inserted into ref_department from payroll summary');
	END IF;

	RAISE NOTICE '1.5';
	-- Generate the department history id for history records

	TRUNCATE etl.ref_department_history_id_seq;

	INSERT INTO etl.ref_department_history_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_fk_values_pms_summary_new_dept;

	INSERT INTO ref_department_history(department_history_id,department_id,
					   department_name,agency_id,fund_class_id,
					   fiscal_year,created_date,load_id)
	SELECT a.department_history_id,c.department_id,
		(CASE WHEN COALESCE(b.department_code,'---------') <> '---------' THEN '<Unknown Department>'
		      ELSE 'Non-Applicable Department' END) as department_name,
		b.agency_id,b.fund_class_id,b.fiscal_year,now()::timestamp,p_load_id_in
	FROM   etl.ref_department_history_id_seq a JOIN tmp_fk_values_pms_summary_new_dept b ON a.uniq_id = b.uniq_id
		JOIN etl.ref_department_id_seq  c ON a.uniq_id = c.uniq_id ;

	GET DIAGNOSTICS l_count = ROW_COUNT;

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'PS',l_count, 'Number of records inserted into ref_department_history from payroll summary');
	END IF;

	RAISE NOTICE '1.6';

	INSERT INTO tmp_fk_pms_summay_values(uniq_id,department_history_id,department_id,department_name,department_short_name)
	SELECT	a.uniq_id, max(c.department_history_id),b.department_id,b.department_name,b.department_short_name
	FROM etl.stg_payroll_summary a JOIN ref_department b  ON coalesce(a.uoa,'---------') = b.department_code AND a.pms_fy = b.fiscal_year
		JOIN ref_department_history c ON b.department_id = c.department_id
		JOIN ref_agency d ON a.agency = d.agency_code AND b.agency_id = d.agency_id
		JOIN ref_fund_class e ON '001' = e.fund_class_code AND e.fund_class_id = b.fund_class_id
		JOIN etl.ref_department_history_id_seq f ON c.department_history_id = f.department_history_id
	GROUP BY 1,3,4,5	;

	-- FK:expenditure_object_history_id

	INSERT INTO tmp_fk_pms_summay_values(uniq_id,expenditure_object_history_id,expenditure_object_id,expenditure_object_name)
	SELECT	a.uniq_id, max(c.expenditure_object_history_id),b.expenditure_object_id,b.expenditure_object_name
	FROM etl.stg_payroll_summary a JOIN ref_expenditure_object b ON COALESCE(a.object,'!PS!') = b.expenditure_object_code AND a.pms_fy = b.fiscal_year
		JOIN ref_expenditure_object_history c ON b.expenditure_object_id = c.expenditure_object_id
	GROUP BY 1,3,4	;


	RAISE NOTICE '1.8';

	CREATE TEMPORARY TABLE tmp_fk_values_pm_summary_new_exp_object(obj_cd varchar,fiscal_year smallint,uniq_id bigint)
	DISTRIBUTED BY (uniq_id);

	INSERT INTO tmp_fk_values_pm_summary_new_exp_object
	SELECT coalesce(object,'!PS!') as obj_cd,pms_fy,MIN(a.uniq_id) as uniq_id
	FROM etl.stg_payroll_summary a join (SELECT uniq_id
						 FROM tmp_fk_pms_summay_values
						 GROUP BY 1
						 HAVING max(expenditure_object_history_id) is null) b on a.uniq_id=b.uniq_id
	GROUP BY 1,2;




	-- Generate the expenditure_object id for new records

	TRUNCATE etl.ref_expenditure_object_id_seq;

	INSERT INTO etl.ref_expenditure_object_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_fk_values_pm_summary_new_exp_object;

	RAISE NOTICE '1.9';

	INSERT INTO ref_expenditure_object(expenditure_object_id,expenditure_object_code,
		expenditure_object_name,fiscal_year,created_date,created_load_id,original_expenditure_object_name)
	SELECT a.expenditure_object_id,b.obj_cd,
	(CASE WHEN COALESCE(obj_cd,'!PS!')='!PS!' THEN 'Payroll Summary' ELSE '<unknown expenditure object>' END) as expenditure_object_name,
	b.fiscal_year,now()::timestamp,p_load_id_in,
	(CASE WHEN COALESCE(obj_cd,'!PS!')='!PS!' THEN 'Payroll Summary' ELSE '<unknown expenditure object>' END) as original_expenditure_object_name
	FROM   etl.ref_expenditure_object_id_seq a JOIN tmp_fk_values_pm_summary_new_exp_object b ON a.uniq_id = b.uniq_id;

	GET DIAGNOSTICS l_count = ROW_COUNT;

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'PS',l_count, 'Number of records inserted into ref_expenditure_object from payroll summary');
	END IF;

	-- Generate the expenditure_object history id for history records

	TRUNCATE etl.ref_expenditure_object_history_id_seq;

	INSERT INTO etl.ref_expenditure_object_history_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_fk_values_pm_summary_new_exp_object;

	RAISE NOTICE '1.10';

	INSERT INTO ref_expenditure_object_history(expenditure_object_history_id,expenditure_object_id,fiscal_year,expenditure_object_name,created_date,load_id)
	SELECT a.expenditure_object_history_id,c.expenditure_object_id,b.fiscal_year,
		(CASE WHEN COALESCE(obj_cd,'!PS!')='!PS!' THEN 'Payroll Summary' ELSE '<unknown expenditure object>' END) as expenditure_object_name,now()::timestamp,p_load_id_in
	FROM   etl.ref_expenditure_object_history_id_seq a JOIN tmp_fk_values_pm_summary_new_exp_object b ON a.uniq_id = b.uniq_id
		JOIN etl.ref_expenditure_object_id_seq c ON a.uniq_id = c.uniq_id;

	GET DIAGNOSTICS l_count = ROW_COUNT;

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'PS',l_count, 'Number of records inserted into ref_expenditure_object_history from payroll summary');
	END IF;

	INSERT INTO tmp_fk_pms_summay_values(uniq_id,expenditure_object_history_id,expenditure_object_id,expenditure_object_name)
	SELECT	a.uniq_id, max(c.expenditure_object_history_id),b.expenditure_object_id,b.expenditure_object_name
	FROM etl.stg_payroll_summary a JOIN ref_expenditure_object b ON coalesce(a.object,'!PS!') = b.expenditure_object_code AND a.pms_fy = b.fiscal_year
		JOIN ref_expenditure_object_history c ON b.expenditure_object_id = c.expenditure_object_id
		JOIN etl.ref_expenditure_object_history_id_seq d ON c.expenditure_object_history_id = d.expenditure_object_history_id
	GROUP BY 1,3,4	;




	-- FK:budget_code_id

	INSERT INTO tmp_fk_pms_summay_values(uniq_id,budget_code_id,budget_code_name)
	SELECT	a.uniq_id, b.budget_code_id,b.budget_code_name
	FROM etl.stg_payroll_summary a JOIN ref_budget_code b ON a.bud_code = b.budget_code AND a.pms_fy=b.fiscal_year
		JOIN ref_agency d ON a.agency = d.agency_code AND b.agency_id = d.agency_id
		JOIN ref_fund_class e ON '001' = e.fund_class_code AND e.fund_class_id = b.fund_class_id;

	CREATE TEMPORARY TABLE tmp_fk_values_pms_summary_new_budget(agency_id integer,budget_code varchar,
						fund_class_id smallint,fiscal_year smallint, uniq_id bigint,
						bud_code_desc varchar)
	DISTRIBUTED BY (uniq_id);

	INSERT INTO tmp_fk_values_pms_summary_new_budget
	SELECT c.agency_id,a.bud_code,e.fund_class_id,a.pms_fy,MIN(b.uniq_id) as uniq_id, min(a.bud_code_desc) as bud_code_desc
	FROM etl.stg_payroll_summary a join (SELECT uniq_id
						 FROM tmp_fk_pms_summay_values
						 GROUP BY 1
						 HAVING max(budget_code_id) IS NULL) b on a.uniq_id=b.uniq_id
		JOIN ref_agency c ON a.agency = c.agency_code
		JOIN ref_fund_class e ON '001' = e.fund_class_code
	GROUP BY 1,2,3,4;

	TRUNCATE etl.ref_budget_code_id_seq;

	INSERT INTO etl.ref_budget_code_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_fk_values_pms_summary_new_budget;

	INSERT INTO  ref_budget_code( budget_code_id,fiscal_year ,budget_code ,
				      agency_id,fund_class_id,attribute_name,
				      created_date,load_id)
	SELECT a.budget_code_id,b.fiscal_year,b.budget_code,
		b.agency_id,b.fund_class_id,b.bud_code_desc,
		now()::timestamp,p_load_id_in
	FROM   etl.ref_budget_code_id_seq a JOIN tmp_fk_values_pms_summary_new_budget b ON a.uniq_id = b.uniq_id;


	GET DIAGNOSTICS l_count = ROW_COUNT;

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'PS',l_count, 'Number of records inserted into ref_budget_code from payroll summary');
	END IF;

	INSERT INTO tmp_fk_pms_summay_values(uniq_id,budget_code_id,budget_code_name)
	SELECT	a.uniq_id, max(b.budget_code_id),b.budget_code_name
	FROM etl.stg_payroll_summary a JOIN ref_budget_code b  ON a.bud_code = b.budget_code AND a.pms_fy = b.fiscal_year
		JOIN ref_agency d ON a.agency = d.agency_code AND b.agency_id = d.agency_id
		JOIN ref_fund_class e ON '001' = e.fund_class_code AND e.fund_class_id = b.fund_class_id
		JOIN etl.ref_budget_code_id_seq f ON b.budget_code_id = f.budget_code_id
	GROUP BY 1,3	;

	RAISE NOTICE '1.5';


	-- FK: Payroll Summary Id

	INSERT INTO tmp_fk_pms_summay_values(uniq_id,payroll_summary_id,action_flag)
	SELECT a.uniq_id,b.payroll_summary_id,'U' as action_flag
	FROM   etl.stg_payroll_summary a JOIN payroll_summary b ON a.pay_cycle = b.pay_cycle_code AND a.pyrl_no=b.payroll_number AND a.pms_fy = b.pms_fiscal_year
		JOIN ref_agency c ON a.agency = c.agency_code
		JOIN ref_agency_history d ON c.agency_id = d.agency_id AND d.agency_history_id = b.agency_history_id
		JOIN ref_department e ON a.uoa = e.department_code AND e.fiscal_year=a.pms_fy AND c.agency_id = e.agency_id
		JOIN ref_fund_class z on '001' = z.fund_class_code and z.fund_class_id = e.fund_class_id
		JOIN ref_department_history f on e.department_id = f.department_id AND b.department_history_id = f.department_history_id
		JOIN ref_expenditure_object g on g.expenditure_object_code = a.object AND a.pms_fy = g.fiscal_year
		JOIN ref_expenditure_object_history h on h.expenditure_object_id = g.expenditure_object_id AND b.expenditure_object_history_id = h.expenditure_object_history_id
		JOIN ref_budget_code i on a.bud_code = i.budget_code AND a.pms_fy = i.fiscal_year AND i.agency_id = c.agency_id AND z.fund_class_id = i.fund_class_id AND i.budget_code_id = b.budget_code_id
		JOIN ref_date j on a.pay_date = j.date AND j.date_id = b.pay_date_id;


	TRUNCATE etl.payroll_summary_id_seq;

	INSERT INTO etl.payroll_summary_id_seq(uniq_id)
	SELECT a.uniq_id
	FROM	etl.stg_payroll_summary a JOIN (SELECT uniq_id
						 FROM tmp_fk_pms_summay_values
						 GROUP BY 1
						 HAVING max(payroll_summary_id) is null) b on a.uniq_id=b.uniq_id;

	INSERT INTO tmp_fk_pms_summay_values(uniq_id,payroll_summary_id,action_flag)
	SELECT uniq_id,payroll_summary_id,'I' as action_flag
	FROM etl.payroll_summary_id_seq;

	UPDATE etl.stg_payroll_summary a
	SET
		pay_date_id =ct_table.pay_date_id ,
		agency_history_id =ct_table.agency_history_id ,
		department_history_id=ct_table.department_history_id,
		expenditure_object_history_id = ct_table.expenditure_object_history_id,
		budget_code_id = ct_table.budget_code_id,
		payroll_summary_id = ct_table.payroll_summary_id,
		action_flag = ct_table.action_flag,
		fiscal_year = ct_table.fiscal_year,
		fiscal_year_id = ct_table.fiscal_year_id,
		calendar_fiscal_year = ct_table.calendar_fiscal_year,
		calendar_fiscal_year_id = ct_table.calendar_fiscal_year_id,
		agency_id = ct_table.agency_id,
		agency_name = ct_table.agency_name,
		department_id = ct_table.department_id,
		department_name = ct_table.department_name,
		expenditure_object_id = ct_table.expenditure_object_id,
		expenditure_object_name = ct_table.expenditure_object_name,
		budget_code_name = ct_table.budget_code_name,
		calendar_month_id = ct_table.calendar_month_id,
		fund_class_id = ct_table.fund_class_id,
		agency_short_name = ct_table.agency_short_name,
		department_short_name = ct_table.department_short_name
	FROM
		(SELECT uniq_id,
			max(pay_date_id )as pay_date_id ,
			max(agency_history_id )as agency_history_id ,
			max(department_history_id )as department_history_id ,
			max(expenditure_object_history_id) as expenditure_object_history_id,
			max(budget_code_id) as budget_code_id,
			max(payroll_summary_id) as payroll_summary_id,
			max(action_flag) as action_flag,
			max(fiscal_year) as fiscal_year,
			max(fiscal_year_id) as fiscal_year_id,
			max(calendar_fiscal_year) as calendar_fiscal_year,
			max(calendar_fiscal_year_id) as calendar_fiscal_year_id,
			max(agency_id) as agency_id,
			max(agency_name) as agency_name,
			max(department_id) as department_id,
			max(department_name) as department_name,
			max(expenditure_object_name) as expenditure_object_name,
			max(expenditure_object_id) as expenditure_object_id,
			max(budget_code_name) as budget_code_name,
			max(calendar_month_id) as calendar_month_id,
			max(fund_class_id) as fund_class_id,
			max(agency_short_name) as agency_short_name,
			max(department_short_name) as department_short_name
		FROM	tmp_fk_pms_summay_values
		GROUP	BY 1) ct_table
	WHERE	a.uniq_id = ct_table.uniq_id;

	RAISE NOTICE '1.7';

	RETURN 1;

EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in updateForeignKeysForPMSSummary';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;

	RETURN 0;

END;
$BODY$
  LANGUAGE plpgsql VOLATILE;

----------------------------------------------------------------------------------------------------------------------------------------------------------------

-- Function: etl.processpayrollsummary(integer, bigint)

-- DROP FUNCTION etl.processpayrollsummary(integer, bigint);

CREATE OR REPLACE FUNCTION etl.processpayrollsummary(p_load_file_id_in integer, p_load_id_in bigint)
  RETURNS integer AS
$BODY$
DECLARE
	l_count bigint;
	l_fk_update smallint;
	l_job_id bigint;

BEGIN
	l_fk_update := etl.updateForeignKeysForPMSSummary(p_load_file_id_in,p_load_id_in);

	IF l_fk_update <> 1 THEN
		RETURN -1;
	END IF;

		SELECT job_id
		FROM   etl.etl_data_load
		WHERE  load_id = p_load_id_in  INTO  l_job_id;


	INSERT INTO payroll_summary(payroll_summary_id,agency_history_id,pay_cycle_code,
    				    expenditure_object_history_id, payroll_number,payroll_description,department_history_id,
    				    pms_fiscal_year ,budget_code_id ,total_amount_original,total_amount,
				    pay_date_id,fiscal_year,fiscal_year_id,calendar_fiscal_year_id, calendar_fiscal_year,
				    created_load_id, created_date)
	SELECT payroll_summary_id, agency_history_id,pay_cycle,
    	       expenditure_object_history_id, pyrl_no,pyrl_desc,department_history_id,
    	       pms_fy ,budget_code_id ,total_amt,coalesce(total_amt,0) as total_amount,
	       pay_date_id,fiscal_year,fiscal_year_id,calendar_fiscal_year_id, calendar_fiscal_year,
	       p_load_id_in,now()::timestamp
	FROM   etl.stg_payroll_summary
	WHERE  action_flag = 'I';


	GET DIAGNOSTICS l_count = ROW_COUNT;

		IF l_count > 0 THEN
			INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
			VALUES(p_load_file_id_in,'PS',l_count, '# of records inserted into payroll_summary');
	END IF;


	INSERT INTO disbursement_line_item_details(disbursement_line_item_id,check_eft_issued_date_id,check_eft_issued_nyc_year_id,check_eft_issued_cal_month_id,
				fund_class_id,check_amount,agency_id,agency_code,expenditure_object_id,department_id,check_eft_issued_date,
				agency_name,department_name,vendor_name,department_code,expenditure_object_name,expenditure_object_code,budget_code_id,
				budget_code,budget_name,fund_class_code,spending_category_id,
				spending_category_name,calendar_fiscal_year_id,calendar_fiscal_year,fiscal_year,
				minority_type_id, minority_type_name,
				agency_short_name,department_short_name,load_id, job_id)
	SELECT 	payroll_summary_id,pay_date_id,fiscal_year_id,calendar_month_id,
		fund_class_id,coalesce(total_amt,0) as check_amount,agency_id,agency,b.expenditure_object_id,department_id,pay_date::date,
		agency_name,department_name,department_name,uoa,b.expenditure_object_name,b.expenditure_object_code,budget_code_id,
		bud_code,budget_code_name,'001',2 as spending_category_id,
		'Payroll',calendar_fiscal_year_id,calendar_fiscal_year,a.fiscal_year,
		11 as minority_type_id, 'Individuals & Others' as minority_type_name,
		agency_short_name,department_short_name,p_load_id_in, l_job_id
	FROM 	etl.stg_payroll_summary a JOIN (select * from ref_expenditure_object where expenditure_object_code = '!PS!') b ON  a.pms_fy = b.fiscal_year
	WHERE  action_flag = 'I';



	CREATE TEMPORARY TABLE tmp_payroll_summary_update AS
	SELECT *
	FROM etl.stg_payroll_summary
	WHERE  action_flag = 'U'
	DISTRIBUTED BY (payroll_summary_id)  ;

	UPDATE payroll_summary a
	SET    agency_history_id = b.agency_history_id,
    	       expenditure_object_history_id = b.expenditure_object_history_id,
    	       payroll_description = b.pyrl_desc,
    	       department_history_id = b.department_history_id,
    	       total_amount_original = b.total_amt,
    	       total_amount = coalesce(b.total_amt,0),
	       updated_load_id = p_load_id_in,
	       updated_date = now()::timestamp
	FROM   tmp_payroll_summary_update b
	WHERE  a.payroll_summary_id = b.payroll_summary_id;

	GET DIAGNOSTICS l_count = ROW_COUNT;

				IF l_count > 0 THEN
					INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
					VALUES(p_load_file_id_in,'PS',l_count, '# of records updated in payroll_summary');
		END IF;

	UPDATE  disbursement_line_item_details a
	SET     check_amount = coalesce(b.total_amt,0),
		agency_name = b.agency_name,
		department_name = b.department_name,
		vendor_name =  b.department_name,
		expenditure_object_name = 'Payroll Summary',
		budget_name = b.budget_code_name,
		agency_short_name = b.agency_short_name,
		department_short_name = b.department_short_name,
		load_id = p_load_id_in
	FROM	tmp_payroll_summary_update b
	WHERE	a.disbursement_line_item_id = b.payroll_summary_id
		AND a.spending_category_id = 2;

	GET DIAGNOSTICS l_count = ROW_COUNT;
	INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
	VALUES(p_load_file_id_in,'PS',l_count,'# of records updated in payroll_summary');



	RETURN 1;

EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in processPayrollSummary';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;

	RETURN 0;

END;
$BODY$
  LANGUAGE plpgsql VOLATILE;
