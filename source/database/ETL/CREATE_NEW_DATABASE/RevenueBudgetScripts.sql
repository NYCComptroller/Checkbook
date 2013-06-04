-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
-- Function: etl.updateforeignkeysforrevenuebudget(bigint)

-- DROP FUNCTION etl.updateforeignkeysforrevenuebudget(bigint);

CREATE OR REPLACE FUNCTION etl.updateforeignkeysforrevenuebudget(p_load_id_in bigint)
  RETURNS integer AS
$BODY$
  DECLARE

	l_count bigint;
  
  BEGIN
  	/* UPDATING FOREIGN KEY VALUES	FOR BUDGET DATA*/		
  	
  	CREATE TEMPORARY TABLE tmp_fk_revenue_budget_values (uniq_id bigint, fund_class_id smallint, agency_history_id smallint, 
  						     budget_code_id integer,revenue_source_id integer,updated_date_id smallint,
  						     budget_fiscal_year_id smallint,agency_id smallint,agency_name varchar,
						     budget_code varchar, budget_code_name varchar,revenue_source_name varchar,
  						     agency_code varchar,revenue_source_code varchar,agency_short_name varchar)
  	DISTRIBUTED BY (uniq_id);
  	
  	
  	
  		UPDATE etl.stg_revenue_budget 
		SET fund_class_code = NULL
		WHERE fund_class_code = '';
		
		UPDATE etl.stg_revenue_budget 
		SET agency_code = NULL
		WHERE agency_code = '';
		
			
  	
  	-- FK:fund_class_id
  
  	INSERT INTO tmp_fk_revenue_budget_values(uniq_id,fund_class_id)
  	SELECT	a.uniq_id, b.fund_class_id as fund_class_id
  	FROM etl.stg_revenue_budget a JOIN ref_fund_class b ON COALESCE(a.fund_class_code,'---') = b.fund_class_code;
  		

  	-- FK:Agency_history_id
  	
  	INSERT INTO tmp_fk_revenue_budget_values(uniq_id,agency_history_id,agency_id,agency_name,agency_code,agency_short_name)
  	SELECT	a.uniq_id, max(c.agency_history_id)as agency_history_id,max(b.agency_id) as agency_id,
  		max(c.agency_name) as agency_name,b.agency_code,b.agency_short_name
  	FROM etl.stg_revenue_budget a JOIN ref_agency b ON a.agency_code = b.agency_code
  		JOIN ref_agency_history c ON b.agency_id = c.agency_id
  	GROUP BY 1,5,6;

  	CREATE TEMPORARY TABLE tmp_fk_bdgt_values_new_agencies(dept_cd varchar,uniq_id bigint)
  	DISTRIBUTED BY (uniq_id);
  	
  	INSERT INTO tmp_fk_bdgt_values_new_agencies
  	SELECT COALESCE(agency_code,'---'),MIN(b.uniq_id) as uniq_id
  	FROM etl.stg_revenue_budget a join (SELECT uniq_id
  				    FROM tmp_fk_revenue_budget_values
  				    GROUP BY 1
  				    HAVING max(agency_history_id) is null) b on a.uniq_id=b.uniq_id
  	GROUP BY 1;
  
  	RAISE NOTICE '1';
  	
  	TRUNCATE etl.ref_agency_id_seq;
  	
  	INSERT INTO etl.ref_agency_id_seq(uniq_id)
  	SELECT uniq_id
  	FROM   tmp_fk_bdgt_values_new_agencies;
  	
  	INSERT INTO ref_agency(agency_id,agency_code,agency_name,created_date,created_load_id,original_agency_name,agency_short_name)
  	SELECT a.agency_id,COALESCE(b.dept_cd,'---') as agency_code,(CASE WHEN COALESCE(b.dept_cd,'---')='---' THEN '<Non-Applicable Agency>' ELSE '<Unknown Agency>' END) as agency_name,
  	now()::timestamp,p_load_id_in,'<Unknown Agency>' as original_agency_name,'N/A'
  	FROM   etl.ref_agency_id_seq a JOIN tmp_fk_bdgt_values_new_agencies b ON a.uniq_id = b.uniq_id;
  
  	
  	GET DIAGNOSTICS l_count = ROW_COUNT;
  	IF l_count >0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'RB',l_count,'Number of records inserted into ref_agency from revenue_budget ');
	END IF;
  
  	RAISE NOTICE '1.1';
  
  	-- Generate the agency history id for history records
  	
  	TRUNCATE etl.ref_agency_history_id_seq;
  	
  	INSERT INTO etl.ref_agency_history_id_seq(uniq_id)
  	SELECT uniq_id
  	FROM   tmp_fk_bdgt_values_new_agencies;
  
  	INSERT INTO ref_agency_history(agency_history_id,agency_id,agency_name,created_date,load_id,agency_short_name)
  	SELECT a.agency_history_id,b.agency_id,'<Unknown Agency>' as agency_name,
  	now()::timestamp,p_load_id_in,'N/A'
  	FROM   etl.ref_agency_history_id_seq a JOIN etl.ref_agency_id_seq b ON a.uniq_id = b.uniq_id;
  
  
  	GET DIAGNOSTICS l_count = ROW_COUNT;
	  	IF l_count >0 THEN
			INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
			VALUES(p_load_file_id_in,'RB',l_count,'Number of records inserted into ref_agency_history from revenue_budget ');
	END IF;
  	
  	RAISE NOTICE '1.3';
  	INSERT INTO tmp_fk_revenue_budget_values(uniq_id,agency_history_id,agency_id,agency_name,agency_code)
  	SELECT	a.uniq_id, max(c.agency_history_id) , max(b.agency_id), b.agency_name,
  		b.agency_code
  	FROM etl.stg_revenue_budget a JOIN ref_agency b ON  COALESCE(a.agency_code,'---') = b.agency_code
  		JOIN ref_agency_history c ON b.agency_id = c.agency_id
  		JOIN etl.ref_agency_history_id_seq d ON c.agency_history_id = d.agency_history_id
  	GROUP BY 1,4,5;	
  	
  	
  	
  	RAISE NOTICE '1.7';
  	
  	-- FK:budget_code_id

  	INSERT INTO tmp_fk_revenue_budget_values(uniq_id,budget_code_id,budget_code,budget_code_name)
  	SELECT	a.uniq_id, b.budget_code_id as budget_code_id,b.budget_code,b.attribute_name
  	FROM etl.stg_revenue_budget a JOIN ref_budget_code b ON a.budget_code = b.budget_code and a.budget_fiscal_year = b.fiscal_year
  		JOIN ref_agency d ON a.agency_code = d.agency_code AND b.agency_id = d.agency_id
  		JOIN ref_fund_class e ON a.fund_class_code = e.fund_class_code AND e.fund_class_id = b.fund_class_id;
  		
  	CREATE TEMPORARY TABLE tmp_fk_values_bdgt_new_budget_code(budget_code varchar,fund_class_code varchar, budget_fiscal_year smallint,uniq_id bigint,
  								  agency_id smallint, fund_class_id smallint)
  	DISTRIBUTED BY (uniq_id);
  	
  	INSERT INTO tmp_fk_values_bdgt_new_budget_code
  	SELECT a.budget_code,a.fund_class_code,a.budget_fiscal_year,MIN(b.uniq_id) as uniq_id,
  		c.agency_id,e.fund_class_id
  	FROM etl.stg_revenue_budget a join (SELECT uniq_id
  				    FROM tmp_fk_revenue_budget_values
  				    GROUP BY 1
  				    HAVING max(budget_code_id) IS NULL) b on a.uniq_id=b.uniq_id	
  	JOIN ref_agency c ON a.agency_code = c.agency_code
  	JOIN ref_fund_class e ON a.fund_class_code = e.fund_class_code			    
  	GROUP BY 1,2,3,5,6;
  	
  
  	TRUNCATE etl.ref_budget_code_id_seq;
  	
  	INSERT INTO etl.ref_budget_code_id_seq
  	SELECT uniq_id
  	FROM   tmp_fk_values_bdgt_new_budget_code;
  	
  
  	INSERT INTO ref_budget_code( budget_code_id,fiscal_year,budget_code,agency_id,
  				     fund_class_id,attribute_name, 
  				     created_date,load_id)
  	SELECT  a.budget_code_id,b.budget_fiscal_year,b.budget_code,agency_id,
  		fund_class_id,'<Unknown>',
  		now()::timestamp,p_load_id_in
  	FROM	etl.ref_budget_code_id_seq a JOIN tmp_fk_values_bdgt_new_budget_code b ON a.uniq_id = b.uniq_id;
  	
  	INSERT INTO tmp_fk_revenue_budget_values(uniq_id,budget_code_id,budget_code,budget_code_name)
  	SELECT	a.uniq_id, f.budget_code_id,b.budget_code,b.attribute_name 
  	FROM etl.stg_revenue_budget a JOIN ref_budget_code b ON a.budget_code = b.budget_code and a.budget_fiscal_year = b.fiscal_year
  		JOIN ref_agency d ON a.agency_code = d.agency_code AND b.agency_id = d.agency_id
  		JOIN ref_fund_class e ON a.fund_class_code = e.fund_class_code AND e.fund_class_id = b.fund_class_id
  		JOIN etl.ref_budget_code_id_seq f ON b.budget_code_id = f.budget_code_id;		


  	RAISE NOTICE '1';
  	

	--FK:revenue_source_id
	INSERT INTO tmp_fk_revenue_budget_values(uniq_id,revenue_source_id,revenue_source_code,revenue_source_name)
  	SELECT	a.uniq_id, b.revenue_source_id as revenue_source_id,b.revenue_source_code,b.revenue_source_name
  	FROM etl.stg_revenue_budget a JOIN ref_revenue_source b ON a.revenue_source_code = b.revenue_source_code and a.budget_fiscal_year = b.fiscal_year;
  		
  	CREATE TEMPORARY TABLE tmp_fk_values_bdgt_new_revenue_source_code(revenue_source_code varchar,budget_fiscal_year smallint,uniq_id bigint)
  	DISTRIBUTED BY (uniq_id);


  	INSERT INTO tmp_fk_values_bdgt_new_revenue_source_code
  	SELECT a.revenue_source_code,a.budget_fiscal_year,MIN(b.uniq_id) as uniq_id
    	FROM etl.stg_revenue_budget a join (SELECT uniq_id
  				    FROM tmp_fk_revenue_budget_values
  				    GROUP BY 1
  				    HAVING max(revenue_source_id) IS NULL) b on a.uniq_id=b.uniq_id	
  	GROUP BY 1,2;

  	TRUNCATE etl.ref_revenue_source_id_seq;
  	
  	INSERT INTO etl.ref_revenue_source_id_seq
  	SELECT uniq_id
  	FROM   tmp_fk_values_bdgt_new_revenue_source_code;
  	
  
  	INSERT INTO ref_revenue_source( revenue_source_id,fiscal_year,revenue_source_code,revenue_source_name,
  				     created_date,created_load_id)
  	SELECT  a.revenue_source_id,b.budget_fiscal_year,b.revenue_source_code,'<Unknown revenue source code>',
  		now()::timestamp,p_load_id_in
  	FROM	etl.ref_revenue_source_id_seq a JOIN tmp_fk_values_bdgt_new_revenue_source_code b ON a.uniq_id = b.uniq_id;
  	
  	INSERT INTO tmp_fk_revenue_budget_values(uniq_id,revenue_source_id)
  	SELECT	a.uniq_id, b.revenue_source_id 
  	FROM etl.stg_revenue_budget a JOIN ref_revenue_source b ON a.revenue_source_code = b.revenue_source_code and a.budget_fiscal_year = b.fiscal_year
  	JOIN etl.ref_revenue_source_id_seq f ON b.revenue_source_id = f.revenue_source_id;
  		
  	
  	
   				  	
  	
  	-- FK:budget_fiscal_year_id
  	
  	INSERT INTO tmp_fk_revenue_budget_values(uniq_id,budget_fiscal_year_id)
  	SELECT	a.uniq_id, b.year_id
  	FROM etl.stg_revenue_budget a JOIN ref_year b ON a.budget_fiscal_year = b.year_value;

  	UPDATE etl.stg_revenue_budget a
  	SET	fund_class_id = ct_table.fund_class_id,
  		agency_history_id = ct_table.agency_history_id,
  		agency_id = ct_table.agency_id,
  		budget_code_id = ct_table.budget_code_id,		
  		budget_fiscal_year_id = ct_table.budget_fiscal_year_id,
  		agency_name = ct_table.agency_name,
  		revenue_source_name = ct_table.revenue_source_name,
  		revenue_source_id = ct_table.revenue_source_id,
  		agency_short_name = ct_table.agency_short_name,
  		budget_code_name =ct_table.budget_code_name
  	FROM	(SELECT uniq_id, max(fund_class_id) as fund_class_id, 
  				 max(agency_history_id) as agency_history_id,
  				 max(agency_id) as agency_id,
        		 max(budget_code_id) as budget_code_id,
  				 max(budget_fiscal_year_id) as budget_fiscal_year_id,
  				 max(agency_name) as agency_name,
				 max(revenue_source_name) as revenue_source_name,
  				 max(budget_code_name) as budget_code_name,
  				 max(revenue_source_id) as revenue_source_id,
  				 max(agency_short_name) as agency_short_name
				 	
  				 FROM	tmp_fk_revenue_budget_values
  		 GROUP BY 1) ct_table
  	WHERE	a.uniq_id = ct_table.uniq_id;	

  	
  	RETURN 1;
  EXCEPTION
  	WHEN OTHERS THEN
  	RAISE NOTICE 'Exception Occurred in updateForeignKeysForBudget';
  	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;	
  
  	RETURN 0;
  END;
  $BODY$
  LANGUAGE plpgsql VOLATILE;
ALTER FUNCTION etl.updateforeignkeysforrevenuebudget(bigint)
  OWNER TO gpadmin;

  
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------


-- Function: etl.processrevenuebudget(integer, bigint)

-- DROP FUNCTION etl.processrevenuebudget(integer, bigint);

CREATE OR REPLACE FUNCTION etl.processrevenuebudget(p_load_file_id_in integer, p_load_id_in bigint)
  RETURNS integer AS
$BODY$
DECLARE
	l_fk_update int;
	l_count bigint;
	l_start_time  timestamp;
	l_end_time  timestamp;
BEGIN	      

	l_start_time := timeofday()::timestamp;
	l_fk_update := etl.updateForeignKeysForRevenueBudget(p_load_id_in);

	IF l_fk_update <> 1 THEN
		RETURN -1;
	END IF;
	
	RAISE NOTICE 'REVENUE BUDGET 1';

	UPDATE etl.stg_revenue_budget 
	SET action_flag = 'I';


	CREATE TEMPORARY TABLE tmp_revenue_budget_unique_keys(uniq_id bigint, budget_fiscal_year smallint, fund_class_id smallint, agency_history_id smallint, budget_code_id integer,revenue_source_id integer, action_flag character(1), budget_id integer) 
	DISTRIBUTED BY (uniq_id);

	INSERT INTO tmp_revenue_budget_unique_keys(uniq_id, budget_fiscal_year, fund_class_id, agency_history_id,  
					   budget_code_id,revenue_source_id,action_flag, budget_id)
	SELECT 	a.uniq_id, a.budget_fiscal_year, a.fund_class_id, a.agency_history_id,   
		a.budget_code_id,a.revenue_source_id,'U' as action_flag, b.budget_id
	FROM 	etl.stg_revenue_budget a JOIN revenue_budget b ON a.budget_fiscal_year = b.budget_fiscal_year 
		AND a.fund_class_id = b.fund_class_id  
		AND a.revenue_source_id = b.revenue_source_id
		AND a.budget_code_id = b.budget_code_id 
		AND a.agency_id = b.agency_id;

Raise NOTICE 'Revenue Budget 1.1';
			
	UPDATE etl.stg_revenue_budget a
	SET	action_flag = b.action_flag,
		budget_id = b.budget_id
	FROM	tmp_revenue_budget_unique_keys b
	WHERE 	a.uniq_id = b.uniq_id;

	CREATE TEMPORARY TABLE tmp_revenue_budget_data_to_update(budget_id integer, adopted_amount numeric(20,2), current_budget_amount numeric(20,2), 
							   load_id integer,budget_fiscal_year_id smallint,
							 agency_code varchar,revenue_source_code varchar) 
	DISTRIBUTED BY (budget_id);

	INSERT INTO tmp_revenue_budget_data_to_update(budget_id, adopted_amount, current_budget_amount, 
					        load_id,budget_fiscal_year_id,
					       agency_code,revenue_source_code)
	SELECT budget_id, adopted_amount, current_budget_amount, 
	         p_load_id_in,budget_fiscal_year_id,
	       agency_code,revenue_source_code
	FROM etl.stg_revenue_budget 
	WHERE action_flag = 'U' AND budget_id IS NOT NULL;

	UPDATE revenue_budget a
	SET 	adopted_amount_original = b.adopted_amount,
			adopted_amount = coalesce(b.adopted_amount,0),
		current_modified_budget_amount_original = b.current_budget_amount,
		current_modified_budget_amount = coalesce(b.current_budget_amount,0),
		updated_load_id = b.load_id,
		updated_date = now()::timestamp
	FROM 	tmp_revenue_budget_data_to_update b
	WHERE 	a.budget_id = b.budget_id;

	GET DIAGNOSTICS l_count = ROW_COUNT;
	INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
	VALUES(p_load_file_id_in,'RB',l_count,'# of records updated in revenue_budget ');
	
	
	INSERT INTO revenue_budget(budget_fiscal_year, fund_class_id, agency_history_id,  budget_code_id, 
				    adopted_amount_original, adopted_amount, current_modified_budget_amount_original,   current_modified_budget_amount, 
				 created_load_id,created_date,
				 budget_fiscal_year_id,agency_id,
				   agency_name,budget_code,agency_code,revenue_source_code,revenue_source_name,revenue_source_id,
				   agency_short_name,budget_code_name)				      
		   SELECT a.budget_fiscal_year, a.fund_class_id, a.agency_history_id, a.budget_code_id, 
						 a.adopted_amount as adopted_amount_original, coalesce(a.adopted_amount,0) as adopted_amount, a.current_budget_amount as current_modified_budget_amount_original, coalesce(a.current_budget_amount,0) as current_modified_budget_amount,
						 p_load_id_in,now()::timestamp,
						 a.budget_fiscal_year_id,a.agency_id,
						a.agency_name,a.budget_code,a.agency_code,a.revenue_source_code,a.revenue_source_name,a.revenue_source_id,
						a.agency_short_name,a.budget_code_name
		   FROM  etl.stg_revenue_budget a 
		   WHERE a.action_flag = 'I' AND a.budget_id IS NULL;		
	
			GET DIAGNOSTICS l_count = ROW_COUNT;
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'RB',l_count,'# of records inserted into revenue_budget ');
		
	RAISE NOTICE 'REVENUE BUDGET 2';
	
	-- Updating revenue catgeory details
		UPDATE revenue_budget a SET revenue_category_code = c.revenue_category_code,
					    revenue_category_id = c.revenue_category_id,	
					    revenue_category_name = c.revenue_category_name
		FROM ref_revenue_source b,ref_revenue_category c WHERE a.revenue_source_id =b.revenue_source_id
		AND b.revenue_category_id = c.revenue_category_id;
	
	--Updating funding class details	
		UPDATE revenue_budget a SET funding_class_code = c.funding_class_code,
					funding_class_name =c.funding_class_name,
					funding_class_id =c.funding_class_id				
		FROM ref_revenue_source b,ref_funding_class c WHERE a.revenue_source_id =b.revenue_source_id
		AND b.funding_class_id = c.funding_class_id;
	
		RAISE NOTICE 'REVENUE BUDGET 3';

	
	RETURN 1;
	
EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in processrevenuebudget';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;	
	
	l_end_time := timeofday()::timestamp;
	
	INSERT INTO etl.etl_script_execution_status(load_file_id,script_name,completed_flag,start_time,end_time,errno,errmsg)
	VALUES(p_load_file_id_in,'etl.processrevenuebudget',0,l_start_time,l_end_time,SQLSTATE,SQLERRM);

	RETURN 0;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE;
ALTER FUNCTION etl.processrevenuebudget(integer, bigint)
  OWNER TO gpadmin;
