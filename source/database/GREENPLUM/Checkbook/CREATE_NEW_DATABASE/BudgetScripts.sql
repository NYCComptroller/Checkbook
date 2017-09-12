/* Function defined
updateforeignkeysforbudget
processbudget
refreshbudgetaggregatetable
*/


-- Function: etl.updateforeignkeysforbudget(bigint)

-- DROP FUNCTION etl.updateforeignkeysforbudget(bigint);

CREATE OR REPLACE FUNCTION etl.updateforeignkeysforbudget(p_load_file_id_in bigint,p_load_id_in bigint)
  RETURNS integer AS $$

DECLARE
l_count bigint;
l_start_time timestamp;
l_end_time timestamp;
BEGIN
    l_start_time := timeofday()::timestamp;

    /* UPDATING FOREIGN KEY VALUES    FOR BUDGET DATA*/

    CREATE TEMPORARY TABLE tmp_fk_budget_values (uniq_id bigint, fund_class_id smallint, agency_history_id smallint, department_history_id integer,
                             budget_code_id integer, object_class_history_id integer,
                             budget_fiscal_year_id smallint,agency_id smallint,object_class_id integer,department_id integer,
                             agency_name varchar,department_name varchar,object_class_name varchar,budget_code varchar, budget_name varchar,
                             agency_code varchar,department_code varchar,object_class_code varchar,agency_short_name varchar,department_short_name varchar,budget_code_name varchar)
    DISTRIBUTED BY (uniq_id);




    UPDATE etl.stg_budget
    SET agency_code = NULL
    WHERE agency_code = '';


    UPDATE etl.stg_budget
    SET department_code = NULL
    WHERE department_code = '';




    -- FK:fund_class_id

    INSERT INTO tmp_fk_budget_values(uniq_id,fund_class_id)
    SELECT    a.uniq_id, b.fund_class_id as fund_class_id
    FROM etl.stg_budget a JOIN ref_fund_class b ON a.fund_class_code = b.fund_class_code;

    -- FK:Agency_history_id

    INSERT INTO tmp_fk_budget_values(uniq_id,agency_history_id,agency_id,agency_name,agency_code,agency_short_name)
    SELECT    a.uniq_id, max(c.agency_history_id)as agency_history_id,max(b.agency_id) as agency_id,
        max(c.agency_name) as agency_name,b.agency_code,b.agency_short_name
    FROM etl.stg_budget a JOIN ref_agency b ON COALESCE(a.agency_code,'---') = b.agency_code
        JOIN ref_agency_history c ON b.agency_id = c.agency_id
    GROUP BY 1,5,6;

    CREATE TEMPORARY TABLE tmp_fk_bdgt_values_new_agencies(dept_cd varchar,uniq_id bigint)
    DISTRIBUTED BY (uniq_id);

    INSERT INTO tmp_fk_bdgt_values_new_agencies
    SELECT COALESCE(agency_code,'---'),MIN(b.uniq_id) as uniq_id
    FROM etl.stg_budget a join (SELECT uniq_id
                    FROM tmp_fk_budget_values
                    GROUP BY 1
                    HAVING max(agency_history_id) is null) b on a.uniq_id=b.uniq_id
    GROUP BY 1;

    RAISE NOTICE '1';

    TRUNCATE etl.ref_agency_id_seq;

    INSERT INTO etl.ref_agency_id_seq(uniq_id)
    SELECT uniq_id
    FROM   tmp_fk_bdgt_values_new_agencies;

    INSERT INTO ref_agency(agency_id,agency_code,agency_name,created_date,created_load_id,original_agency_name,agency_short_name)
    SELECT a.agency_id,COALESCE(b.dept_cd,'---'),(CASE WHEN COALESCE(b.dept_cd,'---')='---' THEN '<Non-Applicable Agency>' ELSE '<Unknown Agency>' END)as agency_name,
    now()::timestamp,p_load_id_in,'<Unknown Agency>' as original_agency_name,'N/A'
    FROM   etl.ref_agency_id_seq a JOIN tmp_fk_bdgt_values_new_agencies b ON a.uniq_id = b.uniq_id;

    GET DIAGNOSTICS l_count = ROW_COUNT;

            IF l_count > 0 THEN
                INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
                VALUES(p_load_file_id_in,'B',l_count, 'Number of recods inserted into ref_agency FROM expense budget');
        END IF;


    RAISE NOTICE '1.1';

    -- Generate the agency history id for history records

    TRUNCATE etl.ref_agency_history_id_seq;

    INSERT INTO etl.ref_agency_history_id_seq(uniq_id)
    SELECT uniq_id
    FROM   tmp_fk_bdgt_values_new_agencies;

    INSERT INTO ref_agency_history(agency_history_id,agency_id,agency_name,created_date,load_id,agency_short_name)
    SELECT a.agency_history_id,b.agency_id, '<Unknown Agency>' as agency_name,
    now()::timestamp,p_load_id_in,'N/A'
    FROM   etl.ref_agency_history_id_seq a JOIN etl.ref_agency_id_seq b ON a.uniq_id = b.uniq_id;

    GET DIAGNOSTICS l_count = ROW_COUNT;

        IF l_count > 0 THEN
            INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
            VALUES(p_load_file_id_in,'B',l_count, 'Number of recods inserted into ref_agency_history FROM expense budget');
        END IF;




    RAISE NOTICE '1.3';
    INSERT INTO tmp_fk_budget_values(uniq_id,agency_history_id,agency_id,agency_name,agency_code,agency_short_name)
    SELECT    a.uniq_id, max(c.agency_history_id) , max(b.agency_id), max(c.agency_name) as agency_name,b.agency_code,'N/A'
    FROM etl.stg_budget a JOIN ref_agency b ON COALESCE(a.agency_code,'---')= b.agency_code
        JOIN ref_agency_history c ON b.agency_id = c.agency_id
        JOIN etl.ref_agency_history_id_seq d ON c.agency_history_id = d.agency_history_id
    GROUP BY 1,5    ;


    -- FK:department_history_id

    INSERT INTO tmp_fk_budget_values(uniq_id,department_history_id,department_id,department_name,department_code,department_short_name)
    SELECT    a.uniq_id, max(e.department_history_id) as department_history_id,max(d.department_id) as department_id,
        max(e.department_name) as department_name,d.department_code,d.department_short_name
    FROM etl.stg_budget a JOIN ref_agency b ON a.agency_code = b.agency_code
    JOIN ref_fund_class c ON a.fund_class_code = c.fund_class_code
    JOIN ref_department d ON a.department_code = d.department_code AND b.agency_id = d.agency_id AND c.fund_class_id = d.fund_class_id AND  a.budget_fiscal_year = d.fiscal_year
    JOIN ref_department_history e ON d.department_id = e.department_id
    GROUP BY 1,5,6;


    CREATE TEMPORARY TABLE tmp_fk_values_bdgt_new_dept(agency_history_id integer,agency_id integer,appr_cd varchar,
                        fund_class_id smallint,fiscal_year smallint, uniq_id bigint)
    DISTRIBUTED BY (uniq_id);

    INSERT INTO tmp_fk_values_bdgt_new_dept
    SELECT d.agency_history_id,c.agency_id, COALESCE(department_code,'---------'),e.fund_class_id,budget_fiscal_year,MIN(b.uniq_id) as uniq_id
    FROM etl.stg_budget a join (SELECT uniq_id
                         FROM tmp_fk_budget_values
                         GROUP BY 1
                         HAVING max(department_history_id) IS NULL) b on a.uniq_id=b.uniq_id
        JOIN ref_agency c ON COALESCE(a.agency_code,'---') = c.agency_code
        JOIN ref_agency_history d ON c.agency_id = d.agency_id
        JOIN ref_fund_class e ON COALESCE(a.fund_class_code,'---') = e.fund_class_code
    GROUP BY 1,2,3,4,5;

    RAISE NOTICE '1.4';

    -- Generate the department id for new records

    TRUNCATE etl.ref_department_id_seq;

    INSERT INTO etl.ref_department_id_seq(uniq_id)
    SELECT uniq_id
    FROM   tmp_fk_values_bdgt_new_dept;

    INSERT INTO ref_department(department_id,department_code,
                   department_name,
                   agency_id,fund_class_id,
                   fiscal_year,created_date,created_load_id,original_department_name,department_short_name)
    SELECT a.department_id,COALESCE(b.appr_cd,'---------') as department_code,
        (CASE WHEN COALESCE(b.appr_cd,'---------') <> '---------' THEN '<Unknown Department>'
            ELSE 'Non-Applicable Department' END) as department_name,
        b.agency_id,b.fund_class_id,b.fiscal_year,
        now()::timestamp,p_load_id_in,
        (CASE WHEN COALESCE(b.appr_cd,'---------') <> '---------' THEN '<Unknown Department>'
            ELSE 'Non-Applicable Department' END) as original_department_name,
        'N/A'
    FROM   etl.ref_department_id_seq a JOIN tmp_fk_values_bdgt_new_dept b ON a.uniq_id = b.uniq_id;

    GET DIAGNOSTICS l_count = ROW_COUNT;

                IF l_count > 0 THEN
                    INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
                    VALUES(p_load_file_id_in,'B',l_count, 'Number of records inserted into ref_department FROM expense budget');
        END IF;

    RAISE NOTICE '1.5';
    -- Generate the department history id for history records

    TRUNCATE etl.ref_department_history_id_seq;

    INSERT INTO etl.ref_department_history_id_seq(uniq_id)
    SELECT uniq_id
    FROM   tmp_fk_values_bdgt_new_dept;

    INSERT INTO ref_department_history(department_history_id,department_id,
                       department_name,agency_id,fund_class_id,
                       fiscal_year,created_date,load_id,department_short_name)
    SELECT a.department_history_id,c.department_id,
        '<Unknown Department>' as department_name,
        b.agency_id,b.fund_class_id,b.fiscal_year,now()::timestamp,p_load_id_in,'N/A'
    FROM   etl.ref_department_history_id_seq a JOIN tmp_fk_values_bdgt_new_dept b ON a.uniq_id = b.uniq_id
        JOIN etl.ref_department_id_seq  c ON a.uniq_id = c.uniq_id ;


    GET DIAGNOSTICS l_count = ROW_COUNT;

            IF l_count > 0 THEN
                INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
                VALUES(p_load_file_id_in,'B',l_count, 'Number of records inserted into ref_department_history FROM expense budget');
        END IF;



    RAISE NOTICE '1.6';

    INSERT INTO tmp_fk_budget_values(uniq_id,department_history_id,department_id,department_name,department_code)
    SELECT    a.uniq_id, max(c.department_history_id) ,max(b.department_id) as department_id,
        max(c.department_name) as department_name,b.department_code
    FROM etl.stg_budget a JOIN ref_department b  ON COALESCE(a.department_code,'---------') = b.department_code AND a.budget_fiscal_year = b.fiscal_year
        JOIN ref_department_history c ON b.department_id = c.department_id
        JOIN ref_agency d ON COALESCE(a.agency_code,'---')= d.agency_code AND b.agency_id = d.agency_id
        JOIN ref_fund_class e ON COALESCE(a.fund_class_code,'---')  = e.fund_class_code AND e.fund_class_id = b.fund_class_id
        JOIN etl.ref_department_history_id_seq f ON c.department_history_id = f.department_history_id
    GROUP BY 1,5    ;

    RAISE NOTICE '1.7';

    -- FK:budget_code_id

    INSERT INTO tmp_fk_budget_values(uniq_id,budget_code_id,budget_code,budget_code_name)
    SELECT    a.uniq_id, b.budget_code_id as budget_code_id,b.budget_code,b.attribute_name
    FROM etl.stg_budget a JOIN ref_budget_code b ON a.budget_code = b.budget_code and a.budget_fiscal_year = b.fiscal_year
        JOIN ref_agency d ON a.agency_code = d.agency_code AND b.agency_id = d.agency_id
        JOIN ref_fund_class e ON a.fund_class_code = e.fund_class_code AND e.fund_class_id = b.fund_class_id;

    CREATE TEMPORARY TABLE tmp_fk_values_bdgt_new_budget_code(budget_code varchar,fund_class_code varchar, budget_fiscal_year smallint,uniq_id bigint,
                                  agency_id smallint, fund_class_id smallint)
    DISTRIBUTED BY (uniq_id);

    INSERT INTO tmp_fk_values_bdgt_new_budget_code
    SELECT a.budget_code,a.fund_class_code,a.budget_fiscal_year,MIN(b.uniq_id) as uniq_id,
        c.agency_id,e.fund_class_id
    FROM etl.stg_budget a join (SELECT uniq_id
                    FROM tmp_fk_budget_values
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
    FROM    etl.ref_budget_code_id_seq a JOIN tmp_fk_values_bdgt_new_budget_code b ON a.uniq_id = b.uniq_id;


    GET DIAGNOSTICS l_count = ROW_COUNT;

            IF l_count > 0 THEN
                INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
                VALUES(p_load_file_id_in,'B',l_count, 'Number of records inserted into ref_budget_code FROM expense budget');
        END IF;



    INSERT INTO tmp_fk_budget_values(uniq_id,budget_code_id,budget_code,budget_code_name)
    SELECT    a.uniq_id, f.budget_code_id, b.budget_code,b.attribute_name
    FROM etl.stg_budget a JOIN ref_budget_code b ON a.budget_code = b.budget_code and a.budget_fiscal_year = b.fiscal_year
        JOIN ref_agency d ON a.agency_code = d.agency_code AND b.agency_id = d.agency_id
        JOIN ref_fund_class e ON a.fund_class_code = e.fund_class_code AND e.fund_class_id = b.fund_class_id
        JOIN etl.ref_budget_code_id_seq f ON b.budget_code_id = f.budget_code_id;

    -- FK:object_class_history_id

    INSERT INTO tmp_fk_budget_values(uniq_id,object_class_history_id,object_class_id,object_class_name,object_class_code)
    SELECT    a.uniq_id, max(c.object_class_history_id) as object_class_history_id,max(b.object_class_id) as object_class_id,
        max(c.object_class_name) as object_class_name,b.object_class_code
    FROM etl.stg_budget a JOIN ref_object_class b ON a.object_class_code = b.object_class_code
        JOIN ref_object_class_history c ON b.object_class_id = c.object_class_id
    GROUP BY 1,5;

    CREATE TEMPORARY TABLE tmp_fk_bdgt_values_new_object_class(object_class_code varchar,uniq_id bigint)
    DISTRIBUTED BY (uniq_id);

    INSERT INTO tmp_fk_bdgt_values_new_object_class
    SELECT object_class_code,MIN(b.uniq_id) as uniq_id
    FROM etl.stg_budget a join (SELECT uniq_id
                    FROM tmp_fk_budget_values
                    GROUP BY 1
                    HAVING max(object_class_history_id) is null) b on a.uniq_id=b.uniq_id
    GROUP BY 1;

    RAISE NOTICE '2';

    TRUNCATE etl.ref_object_class_id_seq;

    INSERT INTO etl.ref_object_class_id_seq(uniq_id)
    SELECT uniq_id
    FROM   tmp_fk_bdgt_values_new_object_class;

    INSERT INTO ref_object_class(object_class_id,object_class_code,object_class_name,created_date,created_load_id,original_object_class_name)
    SELECT a.object_class_id,b.object_class_code,'<Unknown Object Class>' as object_class_name,now()::timestamp,p_load_id_in,'<Unknown Object Class>' as original_object_class_name
    FROM   etl.ref_object_class_id_seq a JOIN tmp_fk_bdgt_values_new_object_class b ON a.uniq_id = b.uniq_id;

    GET DIAGNOSTICS l_count = ROW_COUNT;

                IF l_count > 0 THEN
                    INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
                    VALUES(p_load_file_id_in,'B',l_count, 'Number of records inserted into ref_object_class FROM expense budget');
        END IF;

    RAISE NOTICE '2.1';

    -- Generate the object class id for history records

    TRUNCATE etl.ref_object_class_history_id_seq;

    INSERT INTO etl.ref_object_class_history_id_seq(uniq_id)
    SELECT uniq_id
    FROM   tmp_fk_bdgt_values_new_object_class;

    INSERT INTO ref_object_class_history(object_class_history_id,object_class_id,object_class_name,created_date,load_id)
    SELECT a.object_class_history_id,b.object_class_id,'<Unknown Object Class>' as object_class_name,now()::timestamp,p_load_id_in
    FROM   etl.ref_object_class_history_id_seq a JOIN etl.ref_object_class_id_seq b ON a.uniq_id = b.uniq_id;

    RAISE NOTICE '2.3';

    INSERT INTO tmp_fk_budget_values(uniq_id,object_class_history_id,object_class_id,object_class_name,object_class_code)
    SELECT    a.uniq_id, max(c.object_class_history_id) , max(b.object_class_id) as object_class_id,
        max(c.object_class_name) as object_class_name,b.object_class_code
    FROM etl.stg_budget a JOIN ref_object_class b ON a.object_class_code = b.object_class_code
        JOIN ref_object_class_history c ON b.object_class_id = c.object_class_id
        JOIN etl.ref_object_class_history_id_seq d ON c.object_class_history_id = d.object_class_history_id
    GROUP BY 1,5    ;

    RAISE NOTICE '2.4';

    --FK:effective_begin_date_id


    RAISE NOTICE '2.5';
    -- FK:budget_fiscal_year_id

    INSERT INTO tmp_fk_budget_values(uniq_id,budget_fiscal_year_id)
    SELECT    a.uniq_id, b.year_id
    FROM etl.stg_budget a JOIN ref_year b ON a.budget_fiscal_year = b.year_value;

    RAISE NOTICE '2.6';


    UPDATE etl.stg_budget a
    SET    fund_class_id = ct_table.fund_class_id,
        agency_history_id = ct_table.agency_history_id,
        department_history_id = ct_table.department_history_id,
        budget_code_id = ct_table.budget_code_id,
        object_class_history_id = ct_table.object_class_history_id,
        budget_fiscal_year_id = ct_table.budget_fiscal_year_id,
        agency_name = ct_table.agency_name,
        department_name = ct_table.department_name,
        object_class_name = ct_table.object_class_name,
        budget_code = ct_table.budget_code,
        budget_code_name = ct_table.budget_code_name,
        agency_code = ct_table.agency_code,
        department_code = ct_table.department_code,
        object_class_code = ct_table.object_class_code,
        agency_short_name = ct_table.agency_short_name,
        department_short_name = ct_table.department_short_name,
        agency_id =ct_table.agency_id,
        department_id=ct_table.department_id,
        object_class_id = ct_table.object_class_id
    FROM    (SELECT uniq_id, max(fund_class_id) as fund_class_id,
                 max(agency_history_id) as agency_history_id,
                 max(department_history_id) as department_history_id,
                 max(budget_code_id) as budget_code_id,
                 max(object_class_history_id) as object_class_history_id,
                 max(budget_fiscal_year_id) as budget_fiscal_year_id,
                 max(agency_name) as agency_name,
                 max(department_name) as department_name,
                 max(object_class_name) as object_class_name,
                 max(budget_code) as budget_code,
                 max(budget_code_name) as budget_code_name,
                 max(agency_code) as agency_code,
                 max(department_code) as department_code,
                 max(object_class_code) as object_class_code,
                 max(agency_short_name) as agency_short_name,
                 max(department_short_name) as department_short_name,
                 max(agency_id) as agency_id,
                 max(department_id) as department_id,
                 max(object_class_id) as object_class_id
         FROM    tmp_fk_budget_values
         GROUP BY 1) ct_table
    WHERE    a.uniq_id = ct_table.uniq_id;

    RETURN 1;
EXCEPTION
    WHEN OTHERS THEN
    RAISE NOTICE 'Exception Occurred in updateForeignKeysForBudget';
    RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;

    l_end_time := timeofday()::timestamp;

    INSERT INTO etl.etl_script_execution_status(load_file_id,script_name,completed_flag,start_time,end_time,errno,errmsg)
    VALUES(p_load_file_id_in,'etl.updateforeignkeysforbudget',0,l_start_time,l_end_time,SQLSTATE,SQLERRM);

    RETURN 0;
END;
 $$ LANGUAGE plpgsql VOLATILE;

-------------------------------------------------------------------------------------------------------------------------------------------------------


-- Function: etl.processbudget(integer, bigint)

-- DROP FUNCTION etl.processbudget(integer, bigint);

CREATE OR REPLACE FUNCTION etl.processbudget(p_load_file_id_in integer, p_load_id_in bigint)
  RETURNS integer AS
$BODY$
DECLARE
    l_fk_update int;
    l_count bigint;
    l_start_time  timestamp;
    l_end_time  timestamp;

BEGIN

    l_start_time := timeofday()::timestamp;
    l_fk_update := etl.updateForeignKeysForBudget(p_load_file_id_in,p_load_id_in);

    IF l_fk_update <> 1 THEN
        RETURN -1;
    END IF;

    RAISE NOTICE 'BUDGET 1';


    UPDATE etl.stg_budget
    SET action_flag = 'I',
        total_expenditure_amount = coalesce(pre_encumbered_amount,0) + coalesce(encumbered_amount,0) +
                       coalesce(accrued_expense_amount,0) + coalesce(cash_expense_amount,0) +
                       coalesce(post_closing_adjustment_amount,0);

    UPDATE etl.stg_budget
    SET remaining_budget = current_budget_amount-total_expenditure_amount;

    CREATE TEMPORARY TABLE tmp_budget_unique_keys(uniq_id bigint, budget_fiscal_year smallint, fund_class_id smallint, agency_history_id smallint,  department_history_id integer, budget_code_id integer, object_class_history_id integer, action_flag character(1), budget_id integer)
    DISTRIBUTED BY (uniq_id);

    INSERT INTO tmp_budget_unique_keys(uniq_id, budget_fiscal_year, fund_class_id, agency_history_id,  department_history_id,
                       budget_code_id, object_class_history_id, action_flag, budget_id)
    SELECT     a.uniq_id, a.budget_fiscal_year, a.fund_class_id, a.agency_history_id,  a.department_history_id,
        a.budget_code_id, a.object_class_history_id, 'U' as action_flag, b.budget_id
    FROM     etl.stg_budget a JOIN budget b ON a.budget_fiscal_year = b.budget_fiscal_year
        AND a.fund_class_id = b.fund_class_id
        AND a.budget_code_id = b.budget_code_id
        AND a.agency_id = b.agency_id
        AND a.department_id = b.department_id
        AND a.object_class_id = b.object_class_id;

    UPDATE etl.stg_budget a
    SET    action_flag = b.action_flag,
        budget_id = b.budget_id
    FROM    tmp_budget_unique_keys b
    WHERE     a.uniq_id = b.uniq_id;

    CREATE TEMPORARY TABLE tmp_budget_data_to_update(budget_id integer, adopted_amount numeric(20,2), current_budget_amount numeric(20,2),
                             pre_encumbered_amount numeric(20,2),  encumbered_amount numeric(20,2), accrued_expense_amount numeric(20,2),
                             cash_expense_amount numeric(20,2), post_closing_adjustment_amount numeric(20,2),
                             total_expenditure_amount numeric(20,2),remaining_budget numeric(20,2), load_id integer,budget_fiscal_year_id smallint,
                             agency_code varchar,department_code varchar,object_class_code varchar,agency_short_name varchar,department_short_name varchar)
    DISTRIBUTED BY (budget_id);

    INSERT INTO tmp_budget_data_to_update(budget_id, adopted_amount, current_budget_amount,
                          pre_encumbered_amount,  encumbered_amount, accrued_expense_amount,
                          cash_expense_amount, post_closing_adjustment_amount,
                          total_expenditure_amount,remaining_budget,load_id,budget_fiscal_year_id,
                          agency_code,department_code,object_class_code,
                          agency_short_name,department_short_name)
    SELECT budget_id, adopted_amount, current_budget_amount,
           pre_encumbered_amount,  encumbered_amount, accrued_expense_amount,
           cash_expense_amount, post_closing_adjustment_amount,
           total_expenditure_amount,remaining_budget, p_load_id_in,budget_fiscal_year_id,
           agency_code,department_code,object_class_code,
           agency_short_name,department_short_name
    FROM etl.stg_budget
    WHERE action_flag = 'U' AND budget_id IS NOT NULL;

    UPDATE budget a
    SET     adopted_amount_original = b.adopted_amount,
        adopted_amount = coalesce(b.adopted_amount,0),
        current_budget_amount_original = b.current_budget_amount,
        current_budget_amount = coalesce(b.current_budget_amount,0),
        pre_encumbered_amount_original = b.pre_encumbered_amount,
        pre_encumbered_amount = coalesce(b.pre_encumbered_amount,0),
        encumbered_amount_original = b.encumbered_amount,
        encumbered_amount = coalesce(b.encumbered_amount,0),
        accrued_expense_amount_original = b.accrued_expense_amount,
        accrued_expense_amount = coalesce(b.accrued_expense_amount,0),
        cash_expense_amount_original = b.cash_expense_amount,
        cash_expense_amount = coalesce(b.cash_expense_amount,0),
        post_closing_adjustment_amount_original = b.post_closing_adjustment_amount,
        post_closing_adjustment_amount = coalesce(b.post_closing_adjustment_amount,0),
        total_expenditure_amount = b.total_expenditure_amount,
        remaining_budget = b.remaining_budget,
        updated_load_id = b.load_id,
        updated_date = now()::timestamp,
        budget_fiscal_year_id = b.budget_fiscal_year_id,
        agency_code = b.agency_code,
        department_code = b.department_code,
        object_class_code = b.object_class_code,
        agency_short_name = b.agency_short_name,
        department_short_name = b.department_short_name
    FROM     tmp_budget_data_to_update b
    WHERE     a.budget_id = b.budget_id;

    GET DIAGNOSTICS l_count = ROW_COUNT;
    INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
    VALUES(p_load_file_id_in,'B',l_count,'# of records updated in budget ');

    INSERT INTO budget(budget_fiscal_year, fund_class_id, agency_history_id, department_history_id, budget_code_id,
               object_class_history_id, adopted_amount_original, adopted_amount, current_budget_amount_original, current_budget_amount,
               pre_encumbered_amount_original, pre_encumbered_amount, encumbered_amount_original,encumbered_amount,
               accrued_expense_amount_original, accrued_expense_amount, cash_expense_amount_original,cash_expense_amount,
               post_closing_adjustment_amount_original,post_closing_adjustment_amount, total_expenditure_amount,remaining_budget,
               created_load_id, created_date,budget_fiscal_year_id,agency_id,object_class_id ,department_id ,
               agency_name,object_class_name,department_name,budget_code,budget_code_name,budget_code_name_code_display,
               agency_code,department_code,object_class_code,agency_short_name,department_short_name)
    SELECT budget_fiscal_year, fund_class_id, agency_history_id, department_history_id, budget_code_id,
        object_class_history_id, adopted_amount as adopted_amount_original, coalesce(adopted_amount,0) as adopted_amount, current_budget_amount as current_budget_amount_original, coalesce(current_budget_amount,0) as current_budget_amount,
        pre_encumbered_amount as pre_encumbered_amount_original, coalesce(pre_encumbered_amount,0) as pre_encumbered_amount, encumbered_amount as encumbered_amount_original, coalesce(encumbered_amount,0) as encumbered_amount,
        accrued_expense_amount as accrued_expense_amount_original, coalesce(accrued_expense_amount,0) as accrued_expense_amount,  cash_expense_amount as cash_expense_amount_original, coalesce(cash_expense_amount,0) as cash_expense_amount,
        post_closing_adjustment_amount as post_closing_adjustment_amount_original, coalesce(post_closing_adjustment_amount,0) as post_closing_adjustment_amount,  total_expenditure_amount, remaining_budget,
        p_load_id_in, now()::timestamp,budget_fiscal_year_id,agency_id,object_class_id ,department_id ,
        agency_name,object_class_name,department_name,budget_code,budget_code_name,budget_code_name || ' ( ' || budget_code || ' )',
        agency_code,department_code,object_class_code,agency_short_name,department_short_name
    FROM  etl.stg_budget
    WHERE action_flag = 'I' AND budget_id IS NULL;

    GET DIAGNOSTICS l_count = ROW_COUNT;

    IF l_count>0 THEN
    INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
    VALUES(p_load_file_id_in,'B',l_count,'# of records inserted into budget ');
    END IF;

    RAISE NOTICE 'BUDGET 2';

    -- budget_code_name fix.
    UPDATE budget a
    SET
      budget_code_name = b.attribute_name,
      budget_code_name_code_display = b.attribute_name || ' ( ' || a.budget_code || ' )',
      updated_load_id = p_load_id_in,
      updated_date = now()::timestamp
    FROM ref_budget_code b
    WHERE a.budget_code_id = b.budget_code_id
    AND a.budget_code_name IS DISTINCT FROM b.attribute_name
    ;

    -- department_name fix.
    UPDATE budget a
    SET
      department_name = b.department_name,
      updated_load_id = p_load_id_in,
      updated_date = now()::timestamp
    FROM (
      SELECT department_id, department_name
        FROM ref_department_history
        WHERE (department_id, department_history_id) IN
        (
          SELECT department_id, MAX(department_history_id)
          FROM ref_department_history
          GROUP BY department_id
        )
    ) b
    WHERE a.department_id = b.department_id
    AND a.department_name IS DISTINCT FROM b.department_name
    ;

    UPDATE budget a set job_id = b.job_id
    FROM  etl.etl_data_load b
    WHERE coalesce(a.updated_load_id,a.created_load_id) = b.load_id ;



    RETURN 1;

EXCEPTION
    WHEN OTHERS THEN
    RAISE NOTICE 'Exception Occurred in processbudget';
    RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;

    l_end_time := timeofday()::timestamp;

    INSERT INTO etl.etl_script_execution_status(load_file_id,script_name,completed_flag,start_time,end_time,errno,errmsg)
    VALUES(p_load_file_id_in,'etl.processbudget',0,l_start_time,l_end_time,SQLSTATE,SQLERRM);

    RETURN 0;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE;
ALTER FUNCTION etl.processbudget(integer, bigint)
  OWNER TO gpadmin;
----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

-- Function: etl.refreshbudgetaggregatetable(bigint)

-- DROP FUNCTION etl.refreshbudgetaggregatetable(bigint);

CREATE OR REPLACE FUNCTION etl.refreshbudgetaggregatetable(p_job_id_in bigint)
  RETURNS integer AS
$BODY$
DECLARE

    l_start_time timestamp;
    l_end_time timestamp;
BEGIN
    l_start_time := timeofday()::timestamp;

    TRUNCATE aggregateon_budget_by_year;

       -- start agency filter_type A

    CREATE TEMPORARY TABLE tmp_budget_agency(agency_id integer,budget_fiscal_year smallint,budget_fiscal_year_id smallint, modified_budget_amount numeric (20,2)) DISTRIBUTED BY (agency_id);

    INSERT INTO tmp_budget_agency
    SELECT agency_id,budget_fiscal_year,budget_fiscal_year_id, sum(current_budget_amount) as modified_budget_amount FROM   budget group by 1,2,3;

    INSERT INTO aggregateon_budget_by_year(agency_id,budget_fiscal_year,budget_fiscal_year_id,modified_budget_amount,modified_budget_amount_py,modified_budget_amount_py_1,filter_type)
    select agency_id,budget_fiscal_year,budget_fiscal_year_id,modified_budget_amount,0 as modified_budget_amount_py,0 as modified_budget_amount_py_1,'A' as filter_type from tmp_budget_agency;



    Update aggregateon_budget_by_year a
    set modified_budget_amount_py = c.modified_budget_amount
    from tmp_budget_agency b ,tmp_budget_agency c
    where a.agency_id=b.agency_id and a.budget_fiscal_year =b.budget_fiscal_year
    and  b.agency_id=c.agency_id and  b.budget_fiscal_year-1=c.budget_fiscal_year;


    Update aggregateon_budget_by_year a
    set modified_budget_amount_py_1 = c.modified_budget_amount
    from tmp_budget_agency b ,tmp_budget_agency c
    where a.agency_id=b.agency_id and a.budget_fiscal_year =b.budget_fiscal_year
    and  b.agency_id=c.agency_id and  b.budget_fiscal_year-2=c.budget_fiscal_year;


    Update aggregateon_budget_by_year a
    set modified_budget_amount_py_2 = c.modified_budget_amount
    from tmp_budget_agency b ,tmp_budget_agency c
    where a.agency_id=b.agency_id and a.budget_fiscal_year =b.budget_fiscal_year
    and  b.agency_id=c.agency_id and  b.budget_fiscal_year-3=c.budget_fiscal_year;



    -- END agency
    --------------------------------
-- object class

--
--     select object_class_code , count(distinct budget_fiscal_year) from budget group by 1 having count(distinct budget_fiscal_year)>1
-- select object_class_code , agency_id,department_id,count(distinct budget_fiscal_year) from budget group by 1,2,3 having count(distinct budget_fiscal_year)>1

    CREATE TEMPORARY TABLE tmp_budget_object(object_class_id integer,budget_fiscal_year smallint,budget_fiscal_year_id smallint, modified_budget_amount numeric (20,2)) DISTRIBUTED BY (object_class_id);

    INSERT INTO tmp_budget_object
    SELECT object_class_id,budget_fiscal_year,budget_fiscal_year_id, sum(current_budget_amount) as modified_budget_amount FROM   budget  group by 1,2,3;

    INSERT INTO aggregateon_budget_by_year(object_class_id,budget_fiscal_year,budget_fiscal_year_id,modified_budget_amount,modified_budget_amount_py,modified_budget_amount_py_1,filter_type)
    SELECT object_class_id,budget_fiscal_year,budget_fiscal_year_id,modified_budget_amount,0 as modified_budget_amount_py,0 as modified_budget_amount_py_1,'O' as filter_type FROM tmp_budget_object;


    UPDATE aggregateon_budget_by_year a
    set modified_budget_amount_py = c.modified_budget_amount
    from tmp_budget_object b ,tmp_budget_object c
    where a.object_class_id=b.object_class_id and a.budget_fiscal_year =b.budget_fiscal_year
    and  b.object_class_id=c.object_class_id and  b.budget_fiscal_year-1=c.budget_fiscal_year;


    Update aggregateon_budget_by_year a
    set modified_budget_amount_py_1 = c.modified_budget_amount
    from tmp_budget_object b ,tmp_budget_object c
    where a.object_class_id=b.object_class_id and a.budget_fiscal_year =b.budget_fiscal_year
    and  b.object_class_id=c.object_class_id and  b.budget_fiscal_year-2=c.budget_fiscal_year;

    Update aggregateon_budget_by_year a
    set modified_budget_amount_py_2 = c.modified_budget_amount
    from tmp_budget_object b ,tmp_budget_object c
    where a.object_class_id=b.object_class_id and a.budget_fiscal_year =b.budget_fiscal_year
    and  b.object_class_id=c.object_class_id and  b.budget_fiscal_year-3=c.budget_fiscal_year;


--END object class

-----------------------


    CREATE TEMPORARY TABLE tmp_budget_agency_object(agency_id integer,object_class_id integer,budget_fiscal_year smallint,budget_fiscal_year_id smallint, modified_budget_amount numeric (20,2)) DISTRIBUTED BY (object_class_id);

    INSERT INTO tmp_budget_agency_object
    SELECT agency_id,object_class_id,budget_fiscal_year,budget_fiscal_year_id, sum(current_budget_amount) as modified_budget_amount FROM   budget  group by 1,2,3,4;

    INSERT INTO aggregateon_budget_by_year(agency_id,object_class_id,budget_fiscal_year,budget_fiscal_year_id,modified_budget_amount,modified_budget_amount_py,modified_budget_amount_py_1,filter_type)
    SELECT agency_id,object_class_id,budget_fiscal_year,budget_fiscal_year_id,modified_budget_amount,0 as modified_budget_amount_py,0 as modified_budget_amount_py_1,'AO' as filter_type FROM tmp_budget_agency_object;


    UPDATE aggregateon_budget_by_year a
    set modified_budget_amount_py = c.modified_budget_amount
    from tmp_budget_agency_object b ,tmp_budget_agency_object c
    where a.agency_id=b.agency_id and  a.object_class_id=b.object_class_id and a.budget_fiscal_year =b.budget_fiscal_year
    and b.agency_id=c.agency_id and b.object_class_id=c.object_class_id and  b.budget_fiscal_year-1=c.budget_fiscal_year;


    UPDATE aggregateon_budget_by_year a
    set modified_budget_amount_py_1 = c.modified_budget_amount
    from tmp_budget_agency_object b ,tmp_budget_agency_object c
    where a.agency_id=b.agency_id and  a.object_class_id=b.object_class_id and a.budget_fiscal_year =b.budget_fiscal_year
    and b.agency_id=c.agency_id and b.object_class_id=c.object_class_id and  b.budget_fiscal_year-2=c.budget_fiscal_year;


    UPDATE aggregateon_budget_by_year a
    set modified_budget_amount_py_2 = c.modified_budget_amount
    from tmp_budget_agency_object b ,tmp_budget_agency_object c
    where a.agency_id=b.agency_id and  a.object_class_id=b.object_class_id and a.budget_fiscal_year =b.budget_fiscal_year
    and b.agency_id=c.agency_id and b.object_class_id=c.object_class_id and  b.budget_fiscal_year-3=c.budget_fiscal_year;


--end agency object

------------------------------


-----------------------


    CREATE TEMPORARY TABLE tmp_budget_agency_dept(agency_id integer,department_code varchar,budget_fiscal_year smallint,budget_fiscal_year_id smallint, modified_budget_amount numeric (20,2)) DISTRIBUTED BY (department_code);

    INSERT INTO tmp_budget_agency_dept
    SELECT agency_id,department_code,budget_fiscal_year,budget_fiscal_year_id, sum(current_budget_amount) as modified_budget_amount FROM   budget  group by 1,2,3,4;

    INSERT INTO aggregateon_budget_by_year(agency_id,department_code,budget_fiscal_year,budget_fiscal_year_id,modified_budget_amount,modified_budget_amount_py,modified_budget_amount_py_1,filter_type)
    SELECT agency_id,department_code,budget_fiscal_year,budget_fiscal_year_id,modified_budget_amount,0 as modified_budget_amount_py,0 as modified_budget_amount_py_1,'AD' as filter_type FROM tmp_budget_agency_dept a ;


    UPDATE aggregateon_budget_by_year a
    set modified_budget_amount_py = c.modified_budget_amount
    from tmp_budget_agency_dept b ,tmp_budget_agency_dept c
    where a.agency_id=b.agency_id and  a.department_code=b.department_code and a.budget_fiscal_year =b.budget_fiscal_year
    and b.agency_id=c.agency_id and b.department_code=c.department_code and  b.budget_fiscal_year-1=c.budget_fiscal_year;


    UPDATE aggregateon_budget_by_year a
    set modified_budget_amount_py_1 = c.modified_budget_amount
    from tmp_budget_agency_dept b ,tmp_budget_agency_dept c
    where a.agency_id=b.agency_id and  a.department_code=b.department_code and a.budget_fiscal_year =b.budget_fiscal_year
    and b.agency_id=c.agency_id and b.department_code=c.department_code and  b.budget_fiscal_year-2=c.budget_fiscal_year;

UPDATE aggregateon_budget_by_year a
    set modified_budget_amount_py_2 = c.modified_budget_amount
    from tmp_budget_agency_dept b ,tmp_budget_agency_dept c
    where a.agency_id=b.agency_id and  a.department_code=b.department_code and a.budget_fiscal_year =b.budget_fiscal_year
    and b.agency_id=c.agency_id and b.department_code=c.department_code and  b.budget_fiscal_year-3=c.budget_fiscal_year;


    --end agency dept

    ---------------------



    CREATE TEMPORARY TABLE tmp_budget_agency_dept_obj(agency_id integer,department_code varchar,object_class_id integer,budget_fiscal_year smallint,budget_fiscal_year_id smallint, modified_budget_amount numeric (20,2)) DISTRIBUTED BY (department_code);

    INSERT INTO tmp_budget_agency_dept_obj
    SELECT agency_id,department_code,object_class_id,budget_fiscal_year,budget_fiscal_year_id, sum(current_budget_amount) as modified_budget_amount FROM   budget  group by 1,2,3,4,5;

    INSERT INTO aggregateon_budget_by_year(agency_id,department_code,object_class_id,budget_fiscal_year,budget_fiscal_year_id,modified_budget_amount,modified_budget_amount_py,modified_budget_amount_py_1,filter_type)
    SELECT agency_id,department_code,object_class_id,budget_fiscal_year,budget_fiscal_year_id,modified_budget_amount,0 as modified_budget_amount_py,0 as modified_budget_amount_py_1,'ADO' as filter_type FROM tmp_budget_agency_dept_obj;


    UPDATE aggregateon_budget_by_year a
    set modified_budget_amount_py = c.modified_budget_amount
    from tmp_budget_agency_dept_obj b ,tmp_budget_agency_dept_obj c
    where a.agency_id=b.agency_id and  a.department_code=b.department_code and a.object_class_id=b.object_class_id and a.budget_fiscal_year =b.budget_fiscal_year
    and b.agency_id=c.agency_id and b.department_code=c.department_code and b.object_class_id=c.object_class_id and b.budget_fiscal_year-1=c.budget_fiscal_year;

    UPDATE aggregateon_budget_by_year a
    set modified_budget_amount_py_1 = c.modified_budget_amount
    from tmp_budget_agency_dept_obj b ,tmp_budget_agency_dept_obj c
    where a.agency_id=b.agency_id and  a.department_code=b.department_code and a.object_class_id=b.object_class_id and a.budget_fiscal_year =b.budget_fiscal_year
    and b.agency_id=c.agency_id and b.department_code=c.department_code and b.object_class_id=c.object_class_id and b.budget_fiscal_year-2=c.budget_fiscal_year;



    UPDATE aggregateon_budget_by_year a
    set modified_budget_amount_py_2 = c.modified_budget_amount
    from tmp_budget_agency_dept_obj b ,tmp_budget_agency_dept_obj c
    where a.agency_id=b.agency_id and  a.department_code=b.department_code and a.object_class_id=b.object_class_id and a.budget_fiscal_year =b.budget_fiscal_year
    and b.agency_id=c.agency_id and b.department_code=c.department_code and b.object_class_id=c.object_class_id and b.budget_fiscal_year-3=c.budget_fiscal_year;


    --end agency dept object

    ---------------------


    UPDATE aggregateon_budget_by_year a
    SET department_id = b.department_id
    FROM ref_department b, ref_fund_class c
    where  a.department_code =b.department_code and a.agency_id = b.agency_id and a.budget_fiscal_year = b.fiscal_year  and b.fund_class_id = c.fund_class_id and c.fund_class_code ='001' and a.filter_type IN ('AD','ADO');


        l_end_time := timeofday()::timestamp;

    INSERT INTO etl.etl_script_execution_status(job_id,script_name,completed_flag,start_time,end_time)
    VALUES(p_job_id_in,'etl.refreshBudgetAggregateTable',1,l_start_time,l_end_time);


    RETURN 1;

EXCEPTION
    WHEN OTHERS THEN
    RAISE NOTICE 'Exception Occurred in refreshBudgetAggregateTable';
    RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;

    l_end_time := timeofday()::timestamp;

    INSERT INTO etl.etl_script_execution_status(job_id,script_name,completed_flag,start_time,end_time)
    VALUES(p_job_id_in,'etl.refreshBudgetAggregateTable',0,l_start_time,l_end_time);


    RETURN 0;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE;
ALTER FUNCTION etl.refreshbudgetaggregatetable(bigint)
  OWNER TO gpadmin;
