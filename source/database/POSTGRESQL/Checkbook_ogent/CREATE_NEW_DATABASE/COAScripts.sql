/*
Functions defined
	processCOAAgency
	processCOADepartment
	processCOAExpenditureObject	
	processCOALocation
	processCOAObjectClass
	processfundingclass
	processrevenuecategory
	processrevenueclass
	processrevenuesource
	processbudget	
	
*/

CREATE OR REPLACE FUNCTION etl.processCOAAgency(p_load_file_id_in int,p_load_id_in bigint) RETURNS INT AS $$
DECLARE
	l_count smallint;
BEGIN
	CREATE TEMPORARY TABLE tmp_ref_agency(uniq_id bigint,agency_code varchar(20),agency_name varchar,agency_short_name varchar(15), exists_flag char(1), modified_flag char(1))
	;
	
	-- For all records check if data is modified/new
	
	INSERT INTO tmp_ref_agency
	SELECT  a.uniq_id,
		a.agency_code, 
	       a.agency_name,
	       a.agency_short_name,	
	       (CASE WHEN b.agency_code IS NULL THEN 'N' ELSE 'Y' END) as exists_flag,
	       (CASE WHEN b.agency_code IS NOT NULL AND (a.agency_name <> b.agency_name OR a.agency_short_name <>b.agency_short_name) THEN 'Y' ELSE 'N' END) as modified_flag
	FROM   etl.stg_agency a LEFT JOIN ref_agency b ON a.agency_code = b.agency_code;
	
	
	-- Generate the agency id for new records
		
	TRUNCATE etl.ref_agency_id_seq;
	
	INSERT INTO etl.ref_agency_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_ref_agency
	WHERE  exists_flag ='N';
	
	INSERT INTO ref_agency(agency_id,agency_code,agency_name,created_date,created_load_id,original_agency_name,agency_short_name)
	SELECT a.agency_id,b.agency_code,b.agency_name,now()::timestamp,p_load_id_in,b.agency_name,b.agency_short_name
	FROM   etl.ref_agency_id_seq a JOIN tmp_ref_agency b ON a.uniq_id = b.uniq_id;
	
	GET DIAGNOSTICS l_count = ROW_COUNT;	

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'A',l_count, '# of records inserted into ref_agency');
	END IF;
	
	-- Generate the agency history id for history records
	
	TRUNCATE etl.ref_agency_history_id_seq;
	
	INSERT INTO etl.ref_agency_history_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_ref_agency
	WHERE  exists_flag ='N'
		OR (exists_flag ='Y' and modified_flag='Y');
		

	CREATE TEMPORARY TABLE tmp_ref_agency_1(uniq_id bigint,agency_code varchar(20),agency_name varchar, agency_short_name varchar(15),exists_flag char(1), modified_flag char(1), agency_id smallint)
	;

	INSERT INTO tmp_ref_agency_1
	SELECT a.*,b.agency_id FROM tmp_ref_agency a JOIN ref_agency b ON a.agency_code = b.agency_code
	WHERE exists_flag ='Y' and modified_flag='Y';

	RAISE NOTICE '1';
	
	UPDATE ref_agency a
	SET	agency_name = b.agency_name,
		agency_short_name = b.agency_short_name,
		updated_date = now()::timestamp,
		updated_load_id = p_load_id_in,
		original_agency_name = (CASE WHEN COALESCE(a.original_agency_name,'')='' THEN b.agency_name 
						ELSE a.original_agency_name END)
	FROM	tmp_ref_agency_1 b		
	WHERE	a.agency_id = b.agency_id;

	GET DIAGNOSTICS l_count = ROW_COUNT;	

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'A',l_count, '# of records updated in ref_agency');
	END IF;
	
	RAISE NOTICE '2';
	
	INSERT INTO ref_agency_history(agency_history_id,agency_id,agency_name,created_date,load_id,agency_short_name)
	SELECT a.agency_history_id,c.agency_id,b.agency_name,now()::timestamp,p_load_id_in,b.agency_short_name
	FROM   etl.ref_agency_history_id_seq a JOIN tmp_ref_agency b ON a.uniq_id = b.uniq_id
		JOIN ref_agency c ON b.agency_code = c.agency_code
	WHERE   exists_flag ='N'
		OR (exists_flag ='Y' and modified_flag='Y') 	;
		
	GET DIAGNOSTICS l_count = ROW_COUNT;	

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'A',l_count, 'Number of records inserted into ref_agency_history');
	END IF;		

	UPDATE ref_agency
	SET is_display = 'Y'
	WHERE agency_code in ('068','069','073','003','011','012','010','013','014','381','390','391','392','382','383','384','385','386','387','388','389','471','480','481','482','483', 
	'484','485','486','487','488','472','473','474','475','476','477','478','479','038','829','004','103','102','043','042','134','054','226','312','099','125','810','030','856', 
	'866','072','126','850','040','017','826','836','816','071','858','032','130','846','781','860','827','801','841','260','902','901','904','903','905','138','133','127','057', 
	'819','214','806','132','136','025','341','350','351','352','342','343','344','345','346','347','348','349','002','098','037','035','156','021','820','313','906', 
	'008','015','131','095','056','942','943','941','944','945','101','039','431','440','441','442','443','444','432','433','434','435','436','437','438','439','044','491','492',
	'493','998','085');
	
	RETURN 1;
EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in processCOAAgency';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;	

	RETURN 0;
END;
$$ language plpgsql;

---------------------------------------------------------------------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION etl.processCOADepartment(p_load_file_id_in int,p_load_id_in bigint) RETURNS INT AS $$
DECLARE
	l_count int;
BEGIN
	CREATE TEMPORARY TABLE tmp_ref_department(uniq_id bigint,agency_code varchar,agency_id int,fund_class_code varchar,fund_class_id int,
						  department_code varchar(20),fiscal_year smallint,department_name varchar, department_short_name varchar, exists_flag char(1), 
						  modified_flag char(1))
	;
	
	-- For all records check if data is modified/new
	
	INSERT INTO tmp_ref_department
	SELECT inner_tbl.uniq_id,
		inner_tbl.agency_code,
		inner_tbl.agency_id,
		inner_tbl.fund_class_code,
		inner_tbl.fund_class_id,
		inner_tbl.department_code, 
		inner_tbl.fiscal_year,
	        inner_tbl.department_name,
	        inner_tbl.department_short_name,
	       (CASE WHEN b.department_code IS NULL THEN 'N' ELSE 'Y' END) as exists_flag,
	       (CASE WHEN b.department_code IS NOT NULL AND (inner_tbl.department_name  <> b.department_name OR inner_tbl.department_short_name <>b.department_short_name)
THEN 'Y' ELSE 'N' END) as modified_flag
	FROM       
	(SELECT a.uniq_id,
		a.agency_code,
		d.agency_id,		
		a.fund_class_code,
		c.fund_class_id,
		a.department_code,
		a.fiscal_year, 
	        a.department_name,
	        a.department_short_name		       
	FROM   etl.stg_department a LEFT JOIN ref_fund_class c ON a.fund_class_code = c.fund_class_code 
	       LEFT JOIN ref_agency d ON a.agency_code = d.agency_code ) inner_tbl
	       LEFT JOIN ref_department b ON inner_tbl.department_code = b.department_code AND inner_tbl.fiscal_year=b.fiscal_year
						AND inner_tbl.agency_id =b.agency_id AND inner_tbl.fund_class_id = b.fund_class_id;

	RAISE NOTICE '1';
	
	-- Generate the agency id for new agency records & insert into ref_agency/ref_agency_history
	
	TRUNCATE etl.ref_agency_id_seq;
	
	INSERT INTO etl.ref_agency_id_seq(uniq_id)
	SELECT min(uniq_id)
	FROM   tmp_ref_department
	WHERE  exists_flag ='N'
	       AND COALESCE(agency_id,0) =0
	GROUP BY agency_code;
	
	CREATE TEMPORARY TABLE tmp_agency_id(uniq_id bigint, agency_id smallint)
	;
	
	INSERT INTO tmp_agency_id
	SELECT c.uniq_id,b.agency_id
	FROM	tmp_ref_department a JOIN etl.ref_agency_id_seq b ON a.uniq_id = b.uniq_id
		JOIN tmp_ref_department c ON a.agency_code = c.agency_code;
	
	UPDATE 	tmp_ref_department a
	SET	agency_id = b.agency_id
	FROM	tmp_agency_id b
	WHERE 	a.uniq_id = b.uniq_id;

	RAISE NOTICE '2';
	
	INSERT INTO ref_agency(agency_id,agency_code,created_date,created_load_id)
	SELECT a.agency_id,b.agency_code,now()::timestamp,p_load_id_in
	FROM   etl.ref_agency_id_seq a JOIN tmp_ref_department b ON a.uniq_id = b.uniq_id;

	GET DIAGNOSTICS l_count = ROW_COUNT;	

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'D',l_count, 'Number of records inserted into ref_agency from COA Department Feed');
	END IF;
	
	TRUNCATE etl.ref_agency_history_id_seq;
	
	INSERT INTO etl.ref_agency_history_id_seq(uniq_id)
	SELECT uniq_id
	FROM   etl.ref_agency_id_seq;

	RAISE NOTICE '3';

	
	INSERT INTO ref_agency_history(agency_history_id,agency_id,created_date,load_id)
	SELECT a.agency_history_id,c.agency_id,now()::timestamp,p_load_id_in
	FROM   etl.ref_agency_history_id_seq a JOIN tmp_ref_department b ON a.uniq_id = b.uniq_id
		JOIN ref_agency c ON b.agency_code = c.agency_code;
		
	
	GET DIAGNOSTICS l_count = ROW_COUNT;	

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'D',l_count, 'Number of records inserted into ref_agency_history from COA Department Feed');
	END IF;
	
	-- Generate the fund class identifier for new fund class
	
	TRUNCATE etl.ref_fund_class_id_seq;
	
	INSERT INTO etl.ref_fund_class_id_seq
	SELECT min(uniq_id)
	FROM   tmp_ref_department
	WHERE  exists_flag ='N'
	       AND COALESCE(fund_class_id,0) =0
	GROUP BY fund_class_code;

	RAISE NOTICE '3.1';
	
	CREATE TEMPORARY TABLE tmp_fund_class_id_id(uniq_id bigint, fund_class_id smallint)
	;
	
	INSERT INTO tmp_fund_class_id_id
	SELECT c.uniq_id,b.fund_class_id
	FROM	tmp_ref_department a JOIN etl.ref_fund_class_id_seq b ON a.uniq_id = b.uniq_id
		JOIN tmp_ref_department c ON a.fund_class_code = c.fund_class_code;
	
	UPDATE 	tmp_ref_department a
	SET	fund_class_id = b.fund_class_id
	FROM	tmp_fund_class_id_id b
	WHERE 	a.uniq_id = b.uniq_id;
	
	INSERT INTO ref_fund_class(fund_class_id,fund_class_code,created_load_id,created_date)
	SELECT a.fund_class_id,b.fund_class_code,p_load_id_in,now()::timestamp
	FROM 	etl.ref_fund_class_id_seq a JOIN tmp_ref_department b ON a.uniq_id = b.uniq_id;
	
	GET DIAGNOSTICS l_count = ROW_COUNT;	

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'D',l_count, 'Number of records inserted into ref_fund_class from COA Department Feed');
	END IF;
	
	RAISE NOTICE '3.2';
	
	-- Generate the department id for new records
		
	TRUNCATE etl.ref_department_id_seq;
	
	INSERT INTO etl.ref_department_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_ref_department
	WHERE  exists_flag ='N';
	
	INSERT INTO ref_department(department_id,department_code,department_name,department_short_name,agency_id,fund_class_id,fiscal_year,created_date,created_load_id,original_department_name)
	SELECT a.department_id,b.department_code,b.department_name,b.department_short_name,b.agency_id,b.fund_class_id,b.fiscal_year,now()::timestamp,p_load_id_in,b.department_name
	FROM   etl.ref_department_id_seq a JOIN tmp_ref_department b ON a.uniq_id = b.uniq_id;

	GET DIAGNOSTICS l_count = ROW_COUNT;	

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'D',l_count, '# of records inserted into ref_department');
	END IF;
	
	RAISE NOTICE '3.3';
	-- Generate the department history id for history records
	
	TRUNCATE etl.ref_department_history_id_seq;
	
	INSERT INTO etl.ref_department_history_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_ref_department
	WHERE  exists_flag ='N'
		OR (exists_flag ='Y' and modified_flag='Y');
		
	RAISE NOTICE '3.4';

	CREATE TEMPORARY TABLE tmp_ref_department_1(uniq_id bigint,agency_code varchar,agency_id int,fund_class_code varchar,fund_class_id int,
						  department_code varchar(20),fiscal_year smallint,department_name varchar, department_short_name varchar,exists_flag char(1), 
						  modified_flag char(1), department_id int)
	;

	INSERT INTO tmp_ref_department_1
	SELECT a.*,b.department_id FROM tmp_ref_department a JOIN ref_department b ON a.department_code = b.department_code AND a.agency_id = b.agency_id 
							AND a.fund_class_id = b.fund_class_id AND a.fiscal_year=b.fiscal_year
	WHERE exists_flag ='Y' and modified_flag='Y';

	RAISE NOTICE '4';
	
	UPDATE ref_department a
	SET	department_name = b.department_name,
		department_short_name = b.department_short_name,
		updated_date = now()::timestamp,
		updated_load_id = p_load_id_in
	FROM	tmp_ref_department_1 b		
	WHERE	a.department_id = b.department_id;

	GET DIAGNOSTICS l_count = ROW_COUNT;	

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'D',l_count, '# of records updated in ref_department');
	END IF;
	
	RAISE NOTICE '5';
	
	INSERT INTO ref_department_history(department_history_id,department_id,department_name,department_short_name,agency_id,fund_class_id,fiscal_year,created_date,load_id)
	SELECT a.department_history_id,c.department_id,b.department_name,b.department_short_name,b.agency_id,b.fund_class_id,b.fiscal_year,now()::timestamp,p_load_id_in
	FROM   etl.ref_department_history_id_seq a JOIN tmp_ref_department b ON a.uniq_id = b.uniq_id
		JOIN ref_department c ON b.department_code = c.department_code AND b.agency_id = c.agency_id 
			AND b.fund_class_id = c.fund_class_id AND b.fiscal_year=c.fiscal_year
	WHERE	exists_flag ='N'
		OR (exists_flag ='Y' and modified_flag='Y');			

	GET DIAGNOSTICS l_count = ROW_COUNT;	

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'D',l_count, 'Number of records inserted into ref_department_history');
	END IF;
	
	RETURN 1;
EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in processCOAdepartment';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;	

	RETURN 0;
END;
$$ language plpgsql;
--------------------------------------------------------------------------------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION etl.processCOAExpenditureObject(p_load_file_id_in int,p_load_id_in bigint) RETURNS INT AS $$
DECLARE
	l_count int;
BEGIN
	CREATE TEMPORARY TABLE tmp_ref_expenditure_object(uniq_id bigint,expenditure_object_code varchar(20),fiscal_year smallint,expenditure_object_name varchar, exists_flag char(1), modified_flag char(1))
	;
	
	-- For all records check if data is modified/new
	
	INSERT INTO tmp_ref_expenditure_object
	SELECT  a.uniq_id,
		a.expenditure_object_code,
		a.fiscal_year, 
	       a.expenditure_object_name,	       
	       (CASE WHEN b.expenditure_object_code IS NULL THEN 'N' ELSE 'Y' END) as exists_flag,
	       (CASE WHEN b.expenditure_object_code IS NOT NULL AND a.expenditure_object_name <> b.expenditure_object_name THEN 'Y' ELSE 'N' END) as modified_flag
	FROM   etl.stg_expenditure_object a LEFT JOIN ref_expenditure_object b ON a.expenditure_object_code = b.expenditure_object_code AND a.fiscal_year=b.fiscal_year;
	
	
	-- Generate the expenditure_object id for new records
		
	TRUNCATE etl.ref_expenditure_object_id_seq;
	
	INSERT INTO etl.ref_expenditure_object_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_ref_expenditure_object
	WHERE  exists_flag ='N';
	
	INSERT INTO ref_expenditure_object(expenditure_object_id,expenditure_object_code,expenditure_object_name,fiscal_year,created_date,created_load_id,original_expenditure_object_name)
	SELECT a.expenditure_object_id,b.expenditure_object_code,b.expenditure_object_name,fiscal_year,now()::timestamp,p_load_id_in,b.expenditure_object_name
	FROM   etl.ref_expenditure_object_id_seq a JOIN tmp_ref_expenditure_object b ON a.uniq_id = b.uniq_id;
	
	GET DIAGNOSTICS l_count = ROW_COUNT;	

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'E',l_count, '# of records inserted into ref_expenditure_object');
	END IF;
	
	-- Generate the expenditure_object history id for history records
	
	TRUNCATE etl.ref_expenditure_object_history_id_seq;
	
	INSERT INTO etl.ref_expenditure_object_history_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_ref_expenditure_object
	WHERE  exists_flag ='N'
		OR (exists_flag ='Y' and modified_flag='Y');
		

	CREATE TEMPORARY TABLE tmp_ref_expenditure_object_1(uniq_id bigint,expenditure_object_code varchar(20),fiscal_year smallint,expenditure_object_name varchar, exists_flag char(1), modified_flag char(1), expenditure_object_id smallint)
	;

	INSERT INTO tmp_ref_expenditure_object_1
	SELECT a.*,b.expenditure_object_id FROM tmp_ref_expenditure_object a JOIN ref_expenditure_object b ON a.expenditure_object_code = b.expenditure_object_code AND a.fiscal_year=b.fiscal_year
	WHERE exists_flag ='Y' and modified_flag='Y';

	RAISE NOTICE '1';
	
	UPDATE ref_expenditure_object a
	SET	expenditure_object_name = b.expenditure_object_name,
		updated_date = now()::timestamp,
		updated_load_id = p_load_id_in
	FROM	tmp_ref_expenditure_object_1 b		
	WHERE	a.expenditure_object_id = b.expenditure_object_id;

	GET DIAGNOSTICS l_count = ROW_COUNT;	

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'E',l_count, '# of records updated in ref_expenditure_object');
	END IF;
	
	RAISE NOTICE '2';
	
	INSERT INTO ref_expenditure_object_history(expenditure_object_history_id,expenditure_object_id,fiscal_year,expenditure_object_name,created_date,load_id)
	SELECT a.expenditure_object_history_id,c.expenditure_object_id,b.fiscal_year,b.expenditure_object_name,now()::timestamp,p_load_id_in
	FROM   etl.ref_expenditure_object_history_id_seq a JOIN tmp_ref_expenditure_object b ON a.uniq_id = b.uniq_id
		JOIN ref_expenditure_object c ON b.expenditure_object_code = c.expenditure_object_code AND b.fiscal_year = c.fiscal_year
	WHERE	exists_flag ='N'
		OR (exists_flag ='Y' and modified_flag='Y');

	GET DIAGNOSTICS l_count = ROW_COUNT;	

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'E',l_count, 'Number of records inserted into ref_expenditure_object_history');
	END IF;		
		
	RETURN 1;
EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in processCOAexpenditure_object';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;	

	RETURN 0;
END;
$$ language plpgsql;

--------------------------------------------------------------------------------------------------------------------------------------------------------------------

-- Schema: etl
CREATE OR REPLACE FUNCTION etl.processCOALocation(p_load_file_id_in int,p_load_id_in bigint) RETURNS INT AS $$
DECLARE
	l_count int;
BEGIN
	CREATE TEMPORARY TABLE tmp_ref_location(uniq_id bigint,agency_code varchar,agency_id int,
						  location_code varchar(20),location_name varchar,location_short_name varchar, exists_flag char(1), 
						  modified_flag char(1))
	;
	
	-- For all records check if data is modified/new
	
	INSERT INTO tmp_ref_location
	SELECT inner_tbl.uniq_id,
		inner_tbl.agency_code,
		inner_tbl.agency_id,
		inner_tbl.location_code, 
		inner_tbl.location_name,
	       inner_tbl.location_short_name,
	       (CASE WHEN b.location_code IS NULL THEN 'N' ELSE 'Y' END) as exists_flag,
	       (CASE WHEN b.location_code IS NOT NULL AND inner_tbl.location_name <> b.location_name THEN 'Y' ELSE 'N' END) as modified_flag
	FROM       
	(SELECT a.uniq_id,
		a.agency_code,
		d.agency_id,		
		a.location_code,
		a.location_name,
		a.location_short_name
	FROM   etl.stg_location a LEFT JOIN ref_agency d ON a.agency_code = d.agency_code ) inner_tbl
	       LEFT JOIN ref_location b ON inner_tbl.location_code = b.location_code AND inner_tbl.agency_id =b.agency_id;

	RAISE NOTICE '1';
	
	-- Generate the agency id for new agency records & insert into ref_agency/ref_agency_history
	
	TRUNCATE etl.ref_agency_id_seq;
	
	INSERT INTO etl.ref_agency_id_seq(uniq_id)
	SELECT min(uniq_id)
	FROM   tmp_ref_location
	WHERE  exists_flag ='N'
	       AND COALESCE(agency_id,0) =0
	GROUP BY agency_code;
	
	CREATE TEMPORARY TABLE tmp_agency_id(uniq_id bigint, agency_id smallint)
	;
	
	INSERT INTO tmp_agency_id
	SELECT c.uniq_id,b.agency_id
	FROM	tmp_ref_location a JOIN etl.ref_agency_id_seq b ON a.uniq_id = b.uniq_id
		JOIN tmp_ref_location c ON a.agency_code = c.agency_code;
	
	UPDATE 	tmp_ref_location a
	SET	agency_id = b.agency_id
	FROM	tmp_agency_id b
	WHERE 	a.uniq_id = b.uniq_id;

	RAISE NOTICE '2';

	
	
	INSERT INTO ref_agency(agency_id,agency_code,created_date,created_load_id)
	SELECT a.agency_id,b.agency_code,now()::timestamp,p_load_id_in
	FROM   etl.ref_agency_id_seq a JOIN tmp_ref_location b ON a.uniq_id = b.uniq_id;
	
	GET DIAGNOSTICS l_count = ROW_COUNT;	

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'L',l_count, 'Number of records inserted into ref_agency from COA Location Feed');
	END IF;
	
	TRUNCATE etl.ref_agency_history_id_seq;
	
	INSERT INTO etl.ref_agency_history_id_seq(uniq_id)
	SELECT uniq_id
	FROM   etl.ref_agency_id_seq;

	RAISE NOTICE '3';


	
	INSERT INTO ref_agency_history(agency_history_id,agency_id,created_date,load_id)
	SELECT a.agency_history_id,c.agency_id,now()::timestamp,p_load_id_in
	FROM   etl.ref_agency_history_id_seq a JOIN tmp_ref_location b ON a.uniq_id = b.uniq_id
		JOIN ref_agency c ON b.agency_code = c.agency_code;
		
	GET DIAGNOSTICS l_count = ROW_COUNT;	

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'L',l_count, 'Number of records inserted into ref_agency_history from COA Location Feed');
	END IF;
	
	-- Generate the location id for new records
		
	TRUNCATE etl.ref_location_id_seq;
	
	INSERT INTO etl.ref_location_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_ref_location
	WHERE  exists_flag ='N';
	
	INSERT INTO ref_location(location_id,location_code,location_name,agency_id,location_short_name,created_date,created_load_id,original_location_name)
	SELECT a.location_id,b.location_code,b.location_name,b.agency_id,b.location_short_name,now()::timestamp,p_load_id_in,b.location_name
	FROM   etl.ref_location_id_seq a JOIN tmp_ref_location b ON a.uniq_id = b.uniq_id;

	GET DIAGNOSTICS l_count = ROW_COUNT;	

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'L',l_count, '# of records inserted into ref_location');
	END IF;
	
	RAISE NOTICE '3.3';
	-- Generate the location history id for history records
	
	TRUNCATE etl.ref_location_history_id_seq;
	
	INSERT INTO etl.ref_location_history_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_ref_location
	WHERE  exists_flag ='N'
		OR (exists_flag ='Y' and modified_flag='Y');
		
	RAISE NOTICE '3.4';

	CREATE TEMPORARY TABLE tmp_ref_location_1(uniq_id bigint,agency_code varchar,agency_id int,
						  location_code varchar(20),location_name varchar,location_short_name varchar, exists_flag char(1), 
						  modified_flag char(1), location_id smallint)
	;

	INSERT INTO tmp_ref_location_1
	SELECT a.*,b.location_id FROM tmp_ref_location a JOIN ref_location b ON a.location_code = b.location_code AND a.agency_id = b.agency_id 							
	WHERE exists_flag ='Y' and modified_flag='Y';

	RAISE NOTICE '4';
	
	UPDATE ref_location a
	SET	location_name = b.location_name,
		updated_date = now()::timestamp,
		updated_load_id = p_load_id_in
	FROM	tmp_ref_location_1 b		
	WHERE	a.location_id = b.location_id;

	GET DIAGNOSTICS l_count = ROW_COUNT;	

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'L',l_count, '# of records updated in ref_location');
	END IF;
	
	RAISE NOTICE '5';
	
	INSERT INTO ref_location_history(location_history_id,location_id,location_name,agency_id,location_short_name,created_date,load_id)
	SELECT a.location_history_id,c.location_id,b.location_name,b.agency_id,b.location_short_name,now()::timestamp,p_load_id_in
	FROM   etl.ref_location_history_id_seq a JOIN tmp_ref_location b ON a.uniq_id = b.uniq_id
		JOIN ref_location c ON b.location_code = c.location_code AND b.agency_id = c.agency_id
	WHERE	exists_flag ='N'
		OR (exists_flag ='Y' and modified_flag='Y');
		
	GET DIAGNOSTICS l_count = ROW_COUNT;	

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'L',l_count, 'Number of records inserted into ref_location_history');
	END IF;
	
	RETURN 1;
EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in processCOAlocation';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;	

	RETURN 0;
END;
$$ language plpgsql;

---------------------------------------------------------------------------------------------------------------------------------------------------------------
CREATE OR REPLACE FUNCTION etl.processCOAObjectClass(p_load_file_id_in int,p_load_id_in bigint) RETURNS INT AS $$
DECLARE
	l_count int;
BEGIN
	CREATE TEMPORARY TABLE tmp_ref_object_class(uniq_id bigint,object_class_code varchar(4),object_class_name varchar(60),intr_cty_fl bit(1),exists_flag char(1), modified_flag char(1))
	;
	
	-- For all records check if data is modified/new
	
	INSERT INTO tmp_ref_object_class
	SELECT  a.uniq_id,
		a.object_class_code, 
	       a.object_class_name,
	       a.intr_cty_fl,
	       (CASE WHEN b.object_class_code IS NULL THEN 'N' ELSE 'Y' END) as exists_flag,
	       (CASE WHEN b.object_class_code IS NOT NULL AND (a.object_class_name <> b.object_class_name OR a.intr_cty_fl <> b.intra_city_flag )THEN 'Y' ELSE 'N' END) as modified_flag
	FROM   etl.stg_object_class a LEFT JOIN ref_object_class b ON a.object_class_code = b.object_class_code;

	RAISE NOTICE 'start';
	
	
	-- Generate the object_class id for new records
		
	TRUNCATE etl.ref_object_class_id_seq;
	
	INSERT INTO etl.ref_object_class_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_ref_object_class
	WHERE  exists_flag ='N';
	
	INSERT INTO ref_object_class(object_class_id,object_class_code,object_class_name,object_class_short_name,
				     active_flag, effective_begin_date_id, effective_end_date_id, budget_allowed_flag, description,
				     source_updated_date,intra_city_flag,contracts_positions_flag,payroll_type,extended_description,
				     related_object_class_code,created_date,created_load_id,original_object_class_name)
	SELECT a.object_class_id,b.object_class_code,b.object_class_name,c.short_name,
		act_fl, d.date_id, e.date_id, alw_bud_fl, c.description,
		c.tbl_last_dt,b.intr_cty_fl,cntrc_pos_fl,c.pyrl_typ,c.dscr_ext,
		c.rltd_ocls_cd,now()::timestamp,p_load_id_in,b.object_class_name
	FROM   etl.ref_object_class_id_seq a JOIN tmp_ref_object_class b ON a.uniq_id = b.uniq_id
		JOIN etl.stg_object_class c ON b.uniq_id = c.uniq_id
		LEFT JOIN ref_date d ON c.effective_begin_date::date = d.date
		LEFT JOIN ref_date e ON c.effective_end_date::date = e.date;

	GET DIAGNOSTICS l_count = ROW_COUNT;	

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'O',l_count, '# of records inserted into ref_object_class');
	END IF;
	
	RAISE NOTICE 'start.2';
	
	-- Generate the object_class history id for history records
	
	TRUNCATE etl.ref_object_class_history_id_seq;
	
	INSERT INTO etl.ref_object_class_history_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_ref_object_class
	WHERE  exists_flag ='N'
		OR (exists_flag ='Y' and modified_flag='Y');
		

	CREATE TEMPORARY TABLE tmp_ref_object_class_1(uniq_id bigint,object_class_code varchar(20),object_class_name varchar, intr_cty_fl bit(1), exists_flag char(1), modified_flag char(1), object_class_id smallint)
	;

	INSERT INTO tmp_ref_object_class_1
	SELECT a.*,b.object_class_id FROM tmp_ref_object_class a JOIN ref_object_class b ON a.object_class_code = b.object_class_code
	WHERE exists_flag ='Y' and modified_flag='Y';

	RAISE NOTICE '1';
	
	UPDATE ref_object_class a
	SET	object_class_name = b.object_class_name,
	    intra_city_flag   = b.intr_cty_fl,
		updated_date = now()::timestamp,
		updated_load_id = p_load_id_in
	FROM	tmp_ref_object_class_1 b		
	WHERE	a.object_class_id = b.object_class_id;

	GET DIAGNOSTICS l_count = ROW_COUNT;	

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'O',l_count, '# of records updated in ref_object_class');
	END IF;
	
	RAISE NOTICE '2';
	
	INSERT INTO ref_object_class_history(object_class_history_id,object_class_id,object_class_name,object_class_short_name,
				     active_flag, effective_begin_date_id, effective_end_date_id, budget_allowed_flag, description,
				     source_updated_date,intra_city_flag,contracts_positions_flag,payroll_type,extended_description,
				     related_object_class_code,created_date,load_id)
	SELECT a.object_class_history_id,c.object_class_id,b.object_class_name,d.short_name,
		d.act_fl, e.date_id, f.date_id, d.alw_bud_fl, d.description,
		d.tbl_last_dt,b.intr_cty_fl, cntrc_pos_fl,d.pyrl_typ,d.dscr_ext,
		d.rltd_ocls_cd,now()::timestamp,p_load_id_in
	FROM   etl.ref_object_class_history_id_seq a JOIN tmp_ref_object_class b ON a.uniq_id = b.uniq_id
		JOIN ref_object_class c ON b.object_class_code = c.object_class_code
		JOIN etl.stg_object_class d ON b.uniq_id = d.uniq_id
		LEFT JOIN ref_date e ON d.effective_begin_date::date = e.date
		LEFT JOIN ref_date f ON d.effective_end_date::date = f.date
	WHERE 	exists_flag ='N'
		OR (exists_flag ='Y' and modified_flag='Y');
		
	GET DIAGNOSTICS l_count = ROW_COUNT;	

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'O',l_count, 'Number of records inserted into ref_object_class_history');
	END IF;
	
	RETURN 1;
EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in processCOAobject_class';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;	

	RETURN 0;
END;
$$ language plpgsql;



------------------------------------------------------------------------------------------------------------------------------------------------------------

-- Function: etl.processfundingclass(integer, bigint)

-- DROP FUNCTION etl.processfundingclass(integer, bigint);

CREATE OR REPLACE FUNCTION etl.processfundingclass(p_load_file_id_in integer, p_load_id_in bigint)
  RETURNS integer AS
$BODY$
DECLARE
		fc_data_source_code etl.ref_data_source.data_source_code%TYPE;
		fc_count int:=0;
		fc_ins_count int:=0;
		fc_update_count int:=0;
		fc_insert_sql varchar;
		fc_update_sql varchar;



BEGIN


	--Initialise variables
		fc_data_source_code :='';
		fc_insert_sql :='';
		fc_update_sql :='';

		--Determine source code

		SELECT b.data_source_code 
		FROM   etl.etl_data_load_file a JOIN etl.etl_data_load b ON a.load_id = b.load_id	       
		WHERE  a.load_file_id = p_load_file_id_in     
		INTO   fc_data_source_code;

	CREATE TEMPORARY TABLE tmp_ref_funding_class(uniq_id bigint,fy int,funding_class_code varchar(5),funding_class_name varchar(52),
				short_name varchar(50),category_name varchar(52),cty_fund_fl bit,
				intr_cty_fl bit,fund_aloc_req_fl bit,tbl_last_dt varchar(20),
				ams_row_vers_no char(1),rsfcls_nm_up varchar(52),fund_category varchar(50),
				 exists_flag char(1), modified_flag char(1))
	;
	
	-- For all records check if data is modified/new


	
	INSERT INTO tmp_ref_funding_class
	SELECT  a.uniq_id,
		a.fy, 
	       a.funding_class_code,
	       a.funding_class_name,
	       a.short_name,
	       a.category_name,
	(case when a.cty_fund_fl='1' then 1::bit else 0::bit end),
	(case when a.intr_cty_fl ='1' then 1::bit else 0::bit end),
	(case when a.fund_aloc_req_fl='1' then 1::bit else 0::bit end), 	       
	       a.tbl_last_dt,
	       a.ams_row_vers_no,			
	       a.rsfcls_nm_up,
	       a.fund_category,
	       (CASE WHEN b.funding_class_code IS NULL THEN 'N' ELSE 'Y' END) as exists_flag,
	       (CASE WHEN b.funding_class_code IS NOT NULL AND a.funding_class_name <> b.funding_class_name THEN 'Y' ELSE 'N' END) as modified_flag
	FROM   etl.stg_funding_class a LEFT JOIN ref_funding_class b ON a.funding_class_code = b.funding_class_code and a.fy = b.fiscal_year;
	
	Raise notice '1';
	
	-- Generate the funding class id for new records
		
	TRUNCATE etl.ref_funding_class_id_seq;
	
	INSERT INTO etl.ref_funding_class_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_ref_funding_class
	WHERE  exists_flag ='N';

	
	Raise notice '3';

INSERT INTO   ref_funding_class(funding_class_id,fiscal_year,funding_class_code,funding_class_name,funding_class_short_name,category_name,city_fund_flag,intra_city_flag,fund_allocation_required_flag,category_code,created_date,created_load_id)
SELECT a.funding_class_id,b.fy,b.funding_class_code,b.funding_class_name,b.short_name,b.category_name,
	 b.cty_fund_fl,b.intr_cty_fl , b.fund_aloc_req_fl ,b.fund_category,
	now()::timestamp, p_load_id_in     
from etl.ref_funding_class_id_seq a JOIN tmp_ref_funding_class b ON a.uniq_id = b.uniq_id;

		GET DIAGNOSTICS fc_count = ROW_COUNT;
				fc_ins_count := fc_count;
				
		IF fc_ins_count> 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,fc_data_source_code,fc_ins_count, '# of records inserted into ref_funding_class');
		END IF;


CREATE TEMPORARY TABLE tmp_ref_funding_class_1(funding_class_code varchar(5),funding_class_name varchar(52), exists_flag char(1), modified_flag char(1), funding_class_id smallint)
	;

	INSERT INTO tmp_ref_funding_class_1
	SELECT a.funding_class_code,a.funding_class_name,b.funding_class_id FROM tmp_ref_funding_class a JOIN ref_funding_class b ON a.funding_class_code = b.funding_class_code and a.fy = b.fiscal_year
	WHERE exists_flag ='Y' and modified_flag='Y';
Raise notice '5';
	
	UPDATE ref_funding_class a
	SET	funding_class_name= b.funding_class_name,
		updated_date = now()::timestamp,
		updated_load_id =p_load_id_in
	FROM	tmp_ref_funding_class_1 b		
	WHERE	a.funding_class_id = b.funding_class_id;
	

	EXECUTE fc_update_sql;
		GET DIAGNOSTICS fc_count = ROW_COUNT;
				fc_update_count := fc_count;
				
		IF fc_update_count>0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,fc_data_source_code,fc_update_count, '# of records updated in ref_funding_class');
		END IF;



	Return 1;
	
EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in processfundingclass';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;	

	RETURN 0;

	
END;
$BODY$
  LANGUAGE plpgsql VOLATILE;



--------------------------------------------------------------------------------------------------------------------------------------------------------------
CREATE OR REPLACE FUNCTION etl.processrevenuecategory(p_load_file_id_in integer, p_load_id_in bigint) RETURNS integer AS $$
DECLARE

	ry_data_source_code etl.ref_data_source.data_source_code%TYPE;
	ry_count int:=0;
	ry_ins_count int:=0;
	ry_update_count int:=0;
	
BEGIN

	--Initialise variables
	ry_data_source_code :='';


	--Determine source code

	SELECT b.data_source_code 
	FROM   etl.etl_data_load_file a JOIN etl.etl_data_load b ON a.load_id = b.load_id	       
	WHERE  a.load_file_id = p_load_file_id_in     
	INTO   ry_data_source_code;

	CREATE TEMPORARY TABLE tmp_ref_revenue_category(uniq_id bigint,rscat_cd varchar(20),rscat_nm varchar,rscat_sh_nm varchar, exists_flag char(1), modified_flag char(1))
	;

	-- For all records check if data is modified/new

	INSERT INTO tmp_ref_revenue_category
	SELECT  a.uniq_id,
		a.rscat_cd, 
	       a.rscat_nm,
	       a.rscat_sh_nm,
	       (CASE WHEN b.revenue_category_code IS NULL THEN 'N' ELSE 'Y' END) as exists_flag,
	       (CASE WHEN b.revenue_category_code IS NOT NULL AND a.rscat_nm <> b.revenue_category_name THEN 'Y' ELSE 'N' END) as modified_flag
	FROM   etl.stg_revenue_category a LEFT JOIN ref_revenue_category b ON a.rscat_cd = b.revenue_category_code;

	RAISE NOTICE '1';

	-- Generate the revenue category id for new records

	TRUNCATE etl.ref_revenue_category_id_seq;

	INSERT INTO etl.ref_revenue_category_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_ref_revenue_category
	WHERE  exists_flag ='N';


	INSERT INTO ref_revenue_category(revenue_category_id,revenue_category_code,revenue_category_name,revenue_category_short_name,created_date,created_load_id)
	SELECT a.revenue_category_id,b.rscat_cd,b.rscat_nm,rscat_sh_nm, now()::timestamp,p_load_id_in 
	FROM   etl.ref_revenue_category_id_seq a JOIN tmp_ref_revenue_category b ON a.uniq_id = b.uniq_id;


	GET DIAGNOSTICS ry_count = ROW_COUNT;
	ry_ins_count := ry_count;
	
	IF ry_ins_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,ry_data_source_code,ry_ins_count, '# of records inserted into ref_revenue_category');
	END IF;

	CREATE TEMPORARY TABLE tmp_ref_revenue_category_1(uniq_id bigint,rscat_cd varchar(20),rscat_nm varchar,rscat_sh_nm varchar, exists_flag char(1), modified_flag char(1), revenue_category_id smallint)
	;

	INSERT INTO tmp_ref_revenue_category_1
	SELECT a.*,b.revenue_category_id FROM tmp_ref_revenue_category a JOIN ref_revenue_category b ON a.rscat_cd = b.revenue_category_code
	WHERE exists_flag ='Y' and modified_flag='Y';

	RAISE NOTICE '2';

	UPDATE ref_revenue_category a
	SET	revenue_category_name = b.rscat_nm,
		revenue_category_short_name = b.rscat_sh_nm,
		updated_date = now()::timestamp,
		updated_load_id =  p_load_id_in
	FROM	tmp_ref_revenue_category_1 b		
	WHERE	a.revenue_category_id = b.revenue_category_id;


	GET DIAGNOSTICS ry_count = ROW_COUNT;
	ry_update_count := ry_count;
	
	IF ry_update_count > 0 THEN 
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,ry_data_source_code,ry_update_count, '# of records updated in ref_revenue_category');
	END IF;

	RAISE NOTICE 'INSIDE';

	Return 1;

EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in processrevenuecategory';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;	

	RETURN 0;


END;
$$ language plpgsql;

--------------------------------------------------------------------------------------------------------------------------------------------------------------------
CREATE OR REPLACE FUNCTION etl.processrevenueclass(p_load_file_id_in integer, p_load_id_in bigint) RETURNS integer AS $$
DECLARE

	rc_data_source_code etl.ref_data_source.data_source_code%TYPE;
	rc_count int:=0;
	rc_ins_count int:=0;
	rc_update_count int:=0;
BEGIN
	--Initialise variables
	rc_data_source_code :='';

	--Determine source code

	SELECT b.data_source_code 
	FROM   etl.etl_data_load_file a JOIN etl.etl_data_load b ON a.load_id = b.load_id	       
	WHERE  a.load_file_id = p_load_file_id_in     
	INTO   rc_data_source_code;

	CREATE TEMPORARY TABLE tmp_ref_revenue_class(uniq_id bigint,rscls_cd varchar(20),rscls_nm varchar,rscls_sh_nm varchar, exists_flag char(1), modified_flag char(1))
	;

	-- For all records check if data is modified/new

	INSERT INTO tmp_ref_revenue_class
	SELECT  a.uniq_id,
		a.rscls_cd, 
	       a.rscls_nm,
	       a.rscls_sh_nm,
	       (CASE WHEN b.revenue_class_code IS NULL THEN 'N' ELSE 'Y' END) as exists_flag,
	       (CASE WHEN b.revenue_class_code IS NOT NULL AND a.rscls_nm <> b.revenue_class_name THEN 'Y' ELSE 'N' END) as modified_flag
	FROM   etl.stg_revenue_class a LEFT JOIN ref_revenue_class b ON a.rscls_cd = b.revenue_class_code;

	Raise notice '1';

	-- Generate the revenue class id for new records

	TRUNCATE etl.ref_revenue_class_id_seq;

	INSERT INTO etl.ref_revenue_class_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_ref_revenue_class
	WHERE  exists_flag ='N';


	Raise notice '3';
	INSERT INTO ref_revenue_class(revenue_class_id,revenue_class_code,revenue_class_name,revenue_class_short_name,created_date,created_load_id)
	SELECT a.revenue_class_id,b.rscls_cd,b.rscls_nm,b.rscls_sh_nm, now()::timestamp, p_load_id_in
	FROM   etl.ref_revenue_class_id_seq a JOIN tmp_ref_revenue_class b ON a.uniq_id = b.uniq_id;

	GET DIAGNOSTICS rc_count = ROW_COUNT;
	rc_ins_count := rc_count;
	
	IF rc_ins_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,rc_data_source_code,rc_ins_count, '# of records inserted into ref_revenue_class');
	END IF;

	CREATE TEMPORARY TABLE tmp_ref_revenue_class_1(uniq_id bigint,rscls_cd varchar(20),
						rscls_nm varchar,rscls_sh_nm varchar, 
						exists_flag char(1), modified_flag char(1), 
						revenue_class_id smallint)
	;

	INSERT INTO tmp_ref_revenue_class_1
	SELECT a.*,b.revenue_class_id FROM tmp_ref_revenue_class a JOIN ref_revenue_class b ON a.rscls_cd = b.revenue_class_code
	WHERE exists_flag ='Y' and modified_flag='Y';

	Raise notice '5';


	UPDATE ref_revenue_class a
	SET	revenue_class_name = b.rscls_nm,
		revenue_class_short_name = b.rscls_sh_nm,
		updated_date = now()::timestamp,
		updated_load_id = p_load_id_in
	FROM	tmp_ref_revenue_class_1 b		
	WHERE	a.revenue_class_id = b.revenue_class_id;


	GET DIAGNOSTICS rc_count = ROW_COUNT;
	rc_update_count := rc_count;
	
	IF rc_update_count > 0 THEN 
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,rc_data_source_code,rc_update_count, '# of records updated in ref_revenue_class');
	END IF;
	
	RAISE NOTICE 'INSIDE';

	Return 1;

EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in processrevenueclass';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;	

	RETURN 0;	
END;
$$ language plpgsql;

--------------------------------------------------------------------------------------------------------------------------------------------------------------------
CREATE OR REPLACE FUNCTION etl.processrevenuesource(p_load_file_id_in integer, p_load_id_in bigint) RETURNS integer AS $$
DECLARE

	rs_data_source_code etl.ref_data_source.data_source_code%TYPE;
	rs_count int:=0;
	rs_ins_count int:=0;
	rs_update_count int:=0;
BEGIN
	--Initialise variables
	rs_data_source_code :='';

	--Determine source code

	SELECT b.data_source_code 
	FROM   etl.etl_data_load_file a JOIN etl.etl_data_load b ON a.load_id = b.load_id	       
	WHERE  a.load_file_id = p_load_file_id_in     
	INTO   rs_data_source_code;


	CREATE TEMPORARY TABLE tmp_ref_revenue_source
		(uniq_id bigint,
		fy integer,
		rsrc_cd varchar(20),
		rsrc_nm varchar, 
		exists_flag char(1),
		modified_flag char(1),
		rsrc_sh_nm varchar,
		act_fl bit(1),   
		alw_bud_fl bit(1), 
		oper_ind integer, 
		fasb_cls_ind integer,
		fhwa_rev_cr_fl integer, 
		usetax_coll_fl integer,
		rsrc_dscr varchar,	 
		apy_intr_lat_fee integer,
		apy_intr_admn_fee integer,
		apy_intr_nsf_fee integer,
		apy_intr_othr_fee integer,
		elg_inct_fl integer,
		earn_rcvb_cd VarChar, 
		fin_fee_ov_fl integer,
		apy_intr_ov integer,     
		bill_lag_dy integer,
		bill_freq integer,
		bill_fy_strt_mnth integer,
		bill_fy_strt_dy integer,
		fed_agcy_cd VarChar,
		fed_agcy_sfx VarChar,
		fed_nm VarChar,
		srsrc_req VarChar,
		rcls_id smallint,
		funding_class_id smallint,
		rscat_id smallint
		)
	;	
	-- For all records check if data is modified/new

	INSERT INTO tmp_ref_revenue_source
	SELECT  a.uniq_id,
		a.fy,
		a.rsrc_cd, 
		 a.rsrc_nm,
		(CASE WHEN b.revenue_source_code IS NULL THEN 'N' ELSE 'Y' END) as exists_flag,
		(CASE WHEN b.revenue_source_code IS NOT NULL AND a.rsrc_nm <> b.revenue_source_name THEN 'Y' ELSE 'N' END) as modified_flag,
		a.rsrc_sh_nm,
		a.act_fl,   
		a.alw_bud_fl, 
		a.oper_ind, 
		a.fasb_cls_ind,
		a.fhwa_rev_cr_fl, 
		a.usetax_coll_fl,
		a.rsrc_dscr,  
		a.apy_intr_lat_fee,
		a.apy_intr_admn_fee,
		a.apy_intr_nsf_fee,
		a.apy_intr_othr_fee,
		a.elg_inct_fl,
		a.earn_rcvb_cd, 
		a.fin_fee_ov_fl,
		a.apy_intr_ov,     
		a.bill_lag_dy,
		a.bill_freq,
		a.bill_fy_strt_mnth,
		a.bill_fy_strt_dy,
		a.fed_agcy_cd,
		a.fed_agcy_sfx,
		a.fed_nm,
		a.srsrc_req
	FROM   etl.stg_revenue_source a LEFT JOIN ref_revenue_source b 
		ON a.rsrc_cd = b.revenue_source_code and a.fy= fiscal_year;

	--For Populating temp with revenue class id

	CREATE TEMPORARY TABLE temp_revenuesource_class_id(uniq_id bigint,rcls_cd varchar,rcls_id smallint)
	; 

	INSERT INTO temp_revenuesource_class_id  
	select b.uniq_id as uniq_id, a.revenue_class_code as revenue_class_code,a.revenue_class_id as revenue_class_id 
	FROM etl.stg_revenue_source b  left join   ref_revenue_class a on a.revenue_class_code = b.rscls_cd;

	UPDATE tmp_ref_revenue_source a 
	set rcls_id = b.rcls_id 
	FROM temp_revenuesource_class_id b 
	WHERE a.uniq_id = b.uniq_id  ;


	--For populating temp with funding_class_id

	CREATE TEMPORARY TABLE temp_revenuesource_funding_class_id(uniq_id bigint,funding_class_cd varchar,funding_class_id smallint)
	; 

	INSERT INTO temp_revenuesource_funding_class_id  
	select b.uniq_id as uniq_id,a.funding_class_code,a.funding_class_id
	FROM etl.stg_revenue_source b  left join   ref_funding_class a on a.funding_class_code = b.fund_cls and a.fiscal_year = b.fy;

	UPDATE tmp_ref_revenue_source a 
	set funding_class_id = b.funding_class_id 
	FROM temp_revenuesource_funding_class_id b 
	WHERE a.uniq_id = b.uniq_id  ;

	--For populating temp with revenue category

	CREATE TEMPORARY TABLE temp_revenuesource_category_id(uniq_id bigint,rscat_cd varchar,rscat_id smallint)
	; 

	INSERT INTO temp_revenuesource_category_id 
	select b.uniq_id, a.revenue_category_code as revenue_category_code,a.revenue_category_id as revenue_category_id 
	FROM etl.stg_revenue_source b left join ref_revenue_category a 
	ON a.revenue_category_code = b.rscat_cd;

	update tmp_ref_revenue_source a 
	set rscat_id = b.rscat_id 
	from temp_revenuesource_category_id b 
	where a.uniq_id = b.uniq_id ;

	RAISE NOTICE 'RS -2';


	-- Generate the revenue source id for new records

	TRUNCATE etl.ref_revenue_source_id_seq;

	INSERT INTO etl.ref_revenue_source_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_ref_revenue_source
	WHERE  exists_flag ='N';

	RAISE NOTICE 'RS - 3';

	INSERT INTO ref_revenue_source(revenue_source_id,fiscal_year,
					revenue_source_code,revenue_source_name,
					revenue_source_short_name,description,
					funding_class_id,revenue_class_id,
					revenue_category_id,active_flag,budget_allowed_flag,
					operating_indicator,fasb_class_indicator,
					fhwa_revenue_credit_flag ,usetax_collection_flag ,
					apply_interest_late_fee ,apply_interest_admin_fee ,
					apply_interest_nsf_fee ,apply_interest_other_fee,
					eligible_intercept_process,earned_receivable_code,
					finance_fee_override_flag,allow_override_interest,
					billing_lag_days, billing_frequency ,
					billing_fiscal_year_start_month , billing_fiscal_year_start_day , 
					federal_agency_code , federal_agency_suffix , 
					federal_name ,srsrc_req ,
					created_date,created_load_id)
	SELECT 	a.revenue_source_id,
		b.fy,
		b.rsrc_cd,
		b.rsrc_nm,
		b.rsrc_sh_nm, 
		b.rsrc_dscr,
		b.funding_class_id,
		b.rcls_id,
		b.rscat_id,
		b.act_fl,   
		b.alw_bud_fl, 
		b.oper_ind, 
		b.fasb_cls_ind,
		b.fhwa_rev_cr_fl, 
		b.usetax_coll_fl,
		b.apy_intr_lat_fee,
		b.apy_intr_admn_fee,
		b.apy_intr_nsf_fee,
		b.apy_intr_othr_fee,
		b.elg_inct_fl,
		b.earn_rcvb_cd, 
		b.fin_fee_ov_fl,
		b.apy_intr_ov,     
		b.bill_lag_dy,
		b.bill_freq,
		b.bill_fy_strt_mnth,
		b.bill_fy_strt_dy,
		b.fed_agcy_cd,
		b.fed_agcy_sfx,
		b.fed_nm,
		b.srsrc_req,
		now()::timestamp, p_load_id_in
	FROM etl.ref_revenue_source_id_seq a JOIN tmp_ref_revenue_source b ON a.uniq_id = b.uniq_id;


	GET DIAGNOSTICS rs_count = ROW_COUNT;
	rs_ins_count := rs_count;
	
	IF rs_ins_count > 0 THEN 
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,rs_data_source_code,rs_ins_count,'# of records inserted in ref_revenue_source');
	END IF;

	RAISE NOTICE 'RS - 4';

	CREATE TEMPORARY TABLE tmp_ref_revenue_source_1(rsrc_nm varchar,rsrc_sh_nm varchar, revenue_source_id int, revenue_category_id smallint, revenue_class_id smallint, funding_class_id smallint)
	;

	INSERT INTO tmp_ref_revenue_source_1
	SELECT a.rsrc_nm,a.rsrc_sh_nm,b.revenue_source_id ,a.rscat_id,a.rcls_id,a.funding_class_id
	FROM tmp_ref_revenue_source a JOIN ref_revenue_source b ON a.rsrc_cd = b.revenue_source_code AND a.fy = b.fiscal_year
	WHERE exists_flag ='Y' and modified_flag='Y';


	Raise notice '5';

	UPDATE ref_revenue_source a
	SET revenue_source_name = b.rsrc_nm,
	    revenue_source_short_name = b.rsrc_sh_nm,
	    revenue_category_id = b.revenue_category_id,
	    revenue_class_id = b.revenue_class_id,
	    funding_class_id = b.funding_class_id,
	    updated_date = now()::timestamp,
	    updated_load_id =  p_load_id_in
	FROM tmp_ref_revenue_source_1 b		
	WHERE	a.revenue_source_id = b.revenue_source_id;

	GET DIAGNOSTICS rs_count = ROW_COUNT;
	rs_update_count := rs_count;
	
	IF rs_update_count > 0 THEN 
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,rs_data_source_code,rs_update_count, '# of records updated in ref_revenue_source');
	END IF;

	RAISE NOTICE 'INSIDE';

	Return 1;

EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in processrevenuesource';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;	

	RETURN 0;

END;
$$ language plpgsql;



----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------


-- Function: etl.processbudgetcode(integer, bigint)

-- DROP FUNCTION etl.processbudgetcode(integer, bigint);

CREATE OR REPLACE FUNCTION etl.processbudgetcode(p_load_file_id_in integer, p_load_id_in bigint)
  RETURNS integer AS
$BODY$
DECLARE

	bc_data_source_code etl.ref_data_source.data_source_code%TYPE;
	bc_count int:=0;
	bc_ins_count int:=0;
	bc_update_count int:=0;
	bc_agency_ins_count int:=0; 
	bc_agency_count int:=0;
	bc_fund_ins_count int:=0; 
	bc_fund_count int:=0;

BEGIN

	--Initialise variables
	bc_data_source_code :='';

	--Determine source code

	SELECT b.data_source_code 
	FROM   etl.etl_data_load_file a JOIN etl.etl_data_load b ON a.load_id = b.load_id	       
	WHERE  a.load_file_id = p_load_file_id_in     
	INTO   bc_data_source_code;

	CREATE TEMPORARY TABLE tmp_ref_budget_code
		(uniq_id bigint,
		fy integer,
		fcls_cd	varchar,
		fcls_nm	varchar,
		exists_flag char(1),
		budget_code_modified_flag char(1),
		agency_exists_flag char(1),
		fund_class_exists_flag char(1), 
		dept_cd	varchar,
		dept_nm	varchar,
		func_cd	varchar,
		func_nm	varchar,
		func_attr_nm varchar,
		func_attr_sh_nm varchar,
		resp_ctr varchar,
		func_anlys_unit varchar,
		cntrl_cat varchar,
		local_svc_dist varchar,
		ua_fund_fl bit(1),
		pyrl_dflt_fl bit(1),
		bud_cat_a varchar,
		bud_cat_b varchar,
		bud_func varchar,
		dscr_ext varchar,
		tbl_last_dt date,	
		func_attr_nm_up varchar,
		fin_plan_sav_fl bit(1),
		agency_id smallint,
		fund_class_id smallint

	)
	;	


	-- For all records check if data is modified/new

	INSERT INTO tmp_ref_budget_code
	SELECT  a.uniq_id,
		a.fy,
		a.fcls_cd, 
		a.fcls_nm,
		(CASE WHEN b.budget_code IS NULL THEN 'N' ELSE 'Y' END) as exists_flag,
		(CASE WHEN b.budget_code IS NOT NULL AND a.func_attr_nm <> coalesce(b.attribute_name,'') THEN 'Y' ELSE 'N' END) as budget_code_modified_flag,
		(CASE WHEN c.agency_code IS NULL THEN 'N' ELSE 'Y' END) as agency_exists_flag,
		(CASE WHEN d.fund_class_code IS NULL THEN 'N' ELSE 'Y' END) as fund_class_exists_flag,
		a.dept_cd,
		a.dept_nm,
		a.func_cd,
		a.func_nm,
		a.func_attr_nm,
		a.func_attr_sh_nm,
		a.resp_ctr,
		a.func_anlys_unit,
		a.cntrl_cat,
		a.local_svc_dist,
		a.ua_fund_fl ,
		a.pyrl_dflt_fl,
		a.bud_cat_a,
		a.bud_cat_b,
		a.bud_func,
		a.dscr_ext,
		a.tbl_last_dt,	
		a.func_attr_nm_up,
		a.fin_plan_sav_fl,
		b.agency_id,
		b.fund_class_id
	FROM   etl.stg_budget_code a LEFT JOIN ref_agency c on  a.dept_cd = c.agency_code 
				     LEFT JOIN ref_fund_class d on a.fcls_cd =  d.fund_class_code 
				     LEFT JOIN ref_budget_code b ON a.func_cd = b.budget_code 
				     and a.fy= b.fiscal_year 
				     and c.agency_id = b.agency_id 
				     and d.fund_class_id = b.fund_class_id;



	--New agency in budget_code

	CREATE TEMPORARY TABLE tmp_fk_budget_code_new_agencies(dept_cd varchar,uniq_id bigint)
	;
	
	INSERT INTO tmp_fk_budget_code_new_agencies
	SELECT dept_cd,MIN(a.uniq_id) as uniq_id
	FROM tmp_ref_budget_code a WHERE a.agency_exists_flag = 'N'
	GROUP BY 1;
						 
	TRUNCATE etl.ref_agency_id_seq;
	  	
	  	INSERT INTO etl.ref_agency_id_seq(uniq_id)
	  	SELECT uniq_id
	  	FROM   tmp_fk_budget_code_new_agencies;
	  	
	  	INSERT INTO ref_agency(agency_id,agency_code,agency_name,created_date,created_load_id,original_agency_name,agency_short_name)
	  	SELECT a.agency_id,b.dept_cd,'<Unknown Agency>' as agency_name,now()::timestamp,p_load_id_in,'<Unknown Agency>' as original_agency_name,'N/A'
	  	FROM   etl.ref_agency_id_seq a JOIN tmp_ref_budget_code b ON a.uniq_id = b.uniq_id;
  

	--  Agency history
 	
  	TRUNCATE etl.ref_agency_history_id_seq;
		
		INSERT INTO etl.ref_agency_history_id_seq(uniq_id)
		SELECT uniq_id
		FROM   tmp_fk_budget_code_new_agencies;
	
		INSERT INTO ref_agency_history(agency_history_id,agency_id,agency_name,created_date,load_id)
		SELECT a.agency_history_id,b.agency_id,'<Unknown Agency>' as agency_name,now()::timestamp,p_load_id_in
	FROM   etl.ref_agency_history_id_seq a JOIN etl.ref_agency_id_seq b ON a.uniq_id = b.uniq_id;



	--For Populating temp with agency _id
	
	CREATE TEMPORARY TABLE temp_budgetcode_agency_id(uniq_id bigint,agency_code varchar,agency_id smallint); 

	INSERT INTO temp_budgetcode_agency_id  
	select b.uniq_id as uniq_id,a.agency_code as agency_code,a.agency_id as agency_id  
	FROM etl.stg_budget_code b  join   ref_agency a 
	on a.agency_code = b.dept_cd;	

	UPDATE tmp_ref_budget_code a 
	SET agency_id = b.agency_id
	FROM temp_budgetcode_agency_id b 
	WHERE a.uniq_id = b.uniq_id  ;


	--New fund_class

	CREATE TEMPORARY TABLE tmp_fk_budget_code_new_fund_class(fcls_cd varchar,uniq_id bigint)
	;
	
	INSERT INTO tmp_fk_budget_code_new_fund_class
	SELECT fcls_cd,MIN(a.uniq_id) as uniq_id
	FROM tmp_ref_budget_code a WHERE a.fund_class_exists_flag = 'N'
	GROUP BY 1;
	
	TRUNCATE etl.ref_fund_class_id_seq;
	
  	INSERT INTO etl.ref_fund_class_id_seq(uniq_id)
  	SELECT uniq_id
  	FROM   tmp_fk_budget_code_new_fund_class;
	
  	
	INSERT INTO ref_fund_class(fund_class_id, fund_class_code,fund_class_name,created_date,created_load_id) 
	SELECT a.fund_class_id, b.fcls_cd, b.fcls_nm,now()::timestamp,p_load_id_in
	FROM etl.ref_fund_class_id_seq a JOIN tmp_ref_budget_code b ON a.uniq_id = b.uniq_id;


	--For populating temp with fund_class_id

	CREATE TEMPORARY TABLE temp_budgetcode_fund_class_id(uniq_id bigint,fund_class_cd varchar,fund_class_id smallint); 

	INSERT INTO temp_budgetcode_fund_class_id  
	select b.uniq_id as uniq_id,a.fund_class_code as fund_class_code,a.fund_class_id as fund_class_id  
	FROM etl.stg_budget_code b   join   ref_fund_class a on a.fund_class_code = b.fcls_cd;

	UPDATE tmp_ref_budget_code a 
	SET fund_class_id = b.fund_class_id 
	FROM temp_budgetcode_fund_class_id b 
	WHERE a.uniq_id = b.uniq_id  ;

	RAISE NOTICE 'RS -2';

	-- Generate the budget code id for new records

	TRUNCATE etl.ref_budget_code_id_seq;
	
	INSERT INTO etl.ref_budget_code_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_ref_budget_code
	WHERE  exists_flag ='N';

	RAISE NOTICE 'RS - 3';

	INSERT INTO ref_budget_code( budget_code_id ,
				     fiscal_year ,
				     budget_code ,
				     agency_id,
				     fund_class_id,
				     budget_code_name ,
				     attribute_name ,
				     attribute_short_name ,
				     responsibility_center ,	
				     control_category , 
				     ua_funding_flag ,
				     payroll_default_flag , 
				     budget_function ,
				     description , 
				     created_date  ,
				     load_id 
				 )
	SELECT 		a.budget_code_id,
			b.fy,
			b.func_cd,
			b.agency_id,
			b.fund_class_id,
			b.func_nm,
			b.func_attr_nm,
			b.func_attr_sh_nm,
			b.resp_ctr,
			b.cntrl_cat,
			b.ua_fund_fl ,b.pyrl_dflt_fl,
			b.bud_func,b.dscr_ext,
			now()::timestamp
			,p_load_id_in
	FROM   etl.ref_budget_code_id_seq a JOIN tmp_ref_budget_code b ON a.uniq_id = b.uniq_id;


	GET DIAGNOSTICS bc_count = ROW_COUNT;
	bc_ins_count := bc_count;
	
	IF bc_ins_count>0 THEN
	INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
	VALUES(p_load_file_id_in,bc_data_source_code,bc_ins_count, '# of records inserted into ref_budget_code');
	END IF;


		RAISE NOTICE 'RS - 4';

		-- change of budget_code
		CREATE TEMPORARY TABLE tmp_ref_budget_code_1(func_nm varchar, func_attr_nm varchar, func_attr_sh_nm varchar, budget_code_id integer)
		;


		INSERT INTO tmp_ref_budget_code_1
		SELECT a.func_nm,a.func_attr_nm,a.func_attr_sh_nm, b.budget_code_id 
		FROM tmp_ref_budget_code a JOIN ref_budget_code b ON a.func_cd = b.budget_code AND a.fy = b.fiscal_year AND a.agency_id = b.agency_id AND a.fund_class_id = b.fund_class_id
		WHERE exists_flag ='Y' and budget_code_modified_flag ='Y';


	-- TO Do check attributed name

		UPDATE ref_budget_code a
		SET budget_code_name = b.func_nm,
			attribute_name = b.func_attr_nm,
			attribute_short_name = b.func_attr_sh_nm,
			updated_date = now()::timestamp,
			updated_load_id = p_load_id_in 
		FROM	tmp_ref_budget_code_1 b		
		WHERE	a.budget_code_id = b.budget_code_id;


		GET DIAGNOSTICS bc_count = ROW_COUNT;
			bc_update_count := bc_count;
			
		IF bc_update_count > 0 THEN	
			INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
			VALUES(p_load_file_id_in,bc_data_source_code,bc_update_count, '# of records updated in ref_budget_code');
		END IF;

		RAISE NOTICE 'INSIDE';


		Return 1;


		EXCEPTION
		WHEN OTHERS THEN
		RAISE NOTICE 'Exception Occurred in processbudgetcode';
		RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;	

		RETURN 0;


END;

$BODY$
  LANGUAGE plpgsql VOLATILE;

