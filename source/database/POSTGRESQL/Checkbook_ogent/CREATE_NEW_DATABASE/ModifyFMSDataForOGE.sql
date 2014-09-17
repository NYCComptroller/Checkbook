CREATE OR REPLACE FUNCTION etl.modifyFMSDataForOGE(p_job_id_in bigint) RETURNS INT AS $$
DECLARE
	l_count bigint;
	l_start_time  timestamp;
	l_end_time  timestamp;
BEGIN
	l_start_time := timeofday()::timestamp;
	

	
	UPDATE disbursement_line_item_details disb
	SET agency_code = edc_data.agency_code,
		agency_id = edc_data.agency_id,
		contract_agency_id = edc_data.agency_id,
		contract_agency_id_cy = edc_data.agency_id,
		--master_contract_agency_id = edc_data.agency_id,
		--master_contract_agency_id_cy = edc_data.agency_id,
		master_child_contract_agency_id = (CASE WHEN disb.master_contract_agency_id IS  NULL THEN edc_data.agency_id ELSE disb.master_contract_agency_id END),
		master_child_contract_agency_id_cy = (CASE WHEN disb.master_contract_agency_id_cy IS  NULL THEN edc_data.agency_id ELSE disb.master_contract_agency_id_cy END),
		agency_name = edc_data.agency_name,
		agency_short_name = edc_data.agency_short_name,
		agency_history_id = edc_data.agency_history_id,
		vendor_name = edc_data.vendor_name,
		vendor_id = edc_data.vendor_id,
		contract_vendor_id = edc_data.vendor_id,
		contract_vendor_id_cy = edc_data.vendor_id,
		--master_contract_vendor_id = edc_data.vendor_id,
		--master_contract_vendor_id_cy = edc_data.vendor_id,
		master_child_contract_vendor_id = (CASE WHEN disb.master_contract_vendor_id IS NULL THEN edc_data.vendor_id ELSE disb.master_contract_vendor_id END),
		master_child_contract_vendor_id_cy = (CASE WHEN disb.master_contract_vendor_id_cy IS NULL THEN edc_data.vendor_id ELSE disb.master_contract_vendor_id_cy END),
		vendor_customer_code = NULL,
		department_id = edc_data.department_id ,
		department_name = edc_data.department_name,
		department_short_name = edc_data.department_short_name,
		department_code = edc_data.department_code,
		oge_contract_number = edc_data.oge_contract_number,
		oge_budget_name = edc_data.oge_budget_name
	FROM (select disb.disbursement_line_item_id, ag.agency_code, ag.agency_id, ag.agency_name, ag.agency_short_name, agh.agency_history_id, edc.vendor_id, edc.vendor_name, 
	dep.department_id, dep.department_name, dep.department_short_name, dep.department_code, edc.oge_contract_number, edc.budget_name as oge_budget_name FROM disbursement_line_item_details disb JOIN oge_contract edc ON disb.contract_number = edc.fms_contract_number AND disb.agreement_commodity_line_number = edc.fms_commodity_line  JOIN ref_agency ag ON edc.agency_id = ag.agency_id 
	JOIN (select agency_id, max(agency_history_id) agency_history_id from ref_agency_history group by 1) agh ON ag.agency_id = agh.agency_id
	JOIN ref_department dep ON edc.department_id = dep.department_id) edc_data
	WHERE disb.disbursement_line_item_id = edc_data.disbursement_line_item_id;
	
	UPDATE disbursement_line_item disb
	SET agency_history_id = disb1.agency_history_id
	FROM disbursement_line_item_details disb1
	WHERE disb.disbursement_line_item_id = disb1.disbursement_line_item_id;
	
	UPDATE disbursement_line_item disb
	SET department_history_id = dep_his.department_history_id
	FROM disbursement_line_item_details disb1, ref_department dep, ref_department_history dep_his
	WHERE disb.disbursement_line_item_id = disb1.disbursement_line_item_id AND disb1.department_id = dep.department_id AND dep.department_id = dep_his.department_id ;
	
	UPDATE disbursement_line_item_details disb
	SET maximum_contract_amount = edc_data.current_amount,
		maximum_contract_amount_cy = edc_data.current_amount
	FROM
	oge_contract_vendor_level edc_data
--	(SELECT fms_contract_number, vendor_id, max(current_amount) as current_amount FROM oge_contract group by 1,2) edc_data
	WHERE disb.contract_number = edc_data.fms_contract_number AND disb.vendor_id = edc_data.vendor_id ;
	
	UPDATE disbursement_line_item_details disb
	SET maximum_spending_limit = edc_data.current_amount,
		maximum_spending_limit_cy = edc_data.current_amount
	FROM
	(SELECT  b.master_agreement_id, sum(current_amount) as current_amount 
	FROM oge_contract_contract_level a
	JOIN (select distinct contract_number, master_agreement_id FROM history_agreement) b ON a.fms_contract_number = b.contract_number GROUP BY 1) edc_data
	WHERE disb.master_agreement_id = edc_data.master_agreement_id ;
	
	
	RAISE NOTICE 'UPDATE FMS DATA FOR OGE 1';
	
	UPDATE history_agreement_accounting_line a
	SET department_history_id = edc_data.department_history_id,
	agency_history_id = edc_data.agency_history_id
	FROM (select ha.agreement_id, agh.agency_history_id,	deph.department_history_id  FROM history_agreement ha JOIN oge_contract edc ON ha.contract_number = edc.fms_contract_number  JOIN ref_agency ag ON edc.agency_id = ag.agency_id 
	JOIN (select agency_id, max(agency_history_id) agency_history_id from ref_agency_history group by 1) agh ON ag.agency_id = agh.agency_id
	JOIN ref_department dep ON edc.department_id = dep.department_id 
	JOIN ref_department_history deph ON dep.department_id = deph.department_id) edc_data 
	WHERE a.agreement_id = edc_data.agreement_id;
	
	RAISE NOTICE 'UPDATE FMS DATA FOR OGE 2';
	
	UPDATE history_agreement a
	SET agency_history_id = edc_data.agency_history_id
	FROM (select ha.agreement_id, agh.agency_history_id FROM history_agreement ha JOIN oge_contract edc ON ha.contract_number = edc.fms_contract_number  JOIN ref_agency ag ON edc.agency_id = ag.agency_id 
	JOIN (select agency_id, max(agency_history_id) agency_history_id from ref_agency_history group by 1) agh ON ag.agency_id = agh.agency_id) edc_data 
	WHERE a.agreement_id = edc_data.agreement_id;
	
	RAISE NOTICE 'UPDATE FMS DATA FOR OGE 3';
	
	
	DELETE FROM  agreement_snapshot where master_agreement_yn = 'N';
	
	INSERT INTO agreement_snapshot(
            original_agreement_id, document_version, document_code_id, agency_history_id, 
            agency_id, agency_code, agency_name, agreement_id, starting_year, 
            starting_year_id, ending_year, ending_year_id, registered_year, 
            registered_year_id, contract_number, original_contract_amount, 
            maximum_contract_amount, description, vendor_history_id, vendor_id, 
            vendor_code, vendor_name, dollar_difference, percent_difference, 
            master_agreement_id, master_contract_number, agreement_type_id, 
            agreement_type_code, agreement_type_name, award_category_id, 
            award_category_code, award_category_name, award_method_id, award_method_code, 
            award_method_name, expenditure_object_codes, expenditure_object_names, 
            industry_type_id, industry_type_name, award_size_id, effective_begin_date, 
            effective_begin_date_id, effective_begin_year, effective_begin_year_id, 
            effective_end_date, effective_end_date_id, effective_end_year, 
            effective_end_year_id, registered_date, registered_date_id, brd_awd_no, 
            tracking_number, rfed_amount, master_agreement_yn, has_children, 
            original_version_flag, latest_flag, oge_agency_id, oge_agency_name, load_id, last_modified_date, 
            job_id)
	SELECT original_agreement_id, document_version, document_code_id, b.agency_history_id, 
            b.agency_id, b.agency_code, b.agency_name, a.agreement_id, starting_year, 
            starting_year_id, ending_year, ending_year_id, registered_year, 
            registered_year_id, contract_number, b.original_amount as original_contract_amount, 
            b.current_amount as maximum_contract_amount, description, b.vendor_history_id , b.vendor_id , 
            NULL as vendor_code, b.vendor_name, coalesce(b.current_amount,0) - coalesce(b.original_amount,0) as dollar_difference, 
			(CASE WHEN coalesce(b.original_amount,0) = 0 THEN 0 ELSE 
		ROUND((( coalesce(b.current_amount,0) - coalesce(b.original_amount,0)) * 100 )::decimal / coalesce(b.original_amount,0),2) END) as percent_difference, 
            master_agreement_id, master_contract_number, agreement_type_id, 
            agreement_type_code, agreement_type_name, award_category_id, 
            award_category_code, award_category_name, award_method_id, award_method_code, 
            award_method_name, expenditure_object_codes, expenditure_object_names, 
            industry_type_id, industry_type_name, award_size_id, effective_begin_date, 
            effective_begin_date_id, effective_begin_year, effective_begin_year_id, 
            effective_end_date, effective_end_date_id, effective_end_year, 
            effective_end_year_id, registered_date, registered_date_id, brd_awd_no, 
            tracking_number, rfed_amount, master_agreement_yn, has_children, 
            original_version_flag, latest_flag, b.agency_id, b.agency_name,load_id, last_modified_date, 
            job_id
	FROM agreement_snapshot_edc a JOIN oge_contract_vendor_level b ON a.contract_number = b.fms_contract_number
	WHERE master_agreement_yn = 'N';
	
		
	DELETE FROM  agreement_snapshot_cy where master_agreement_yn = 'N';
	
	INSERT INTO agreement_snapshot_cy(
            original_agreement_id, document_version, document_code_id, agency_history_id, 
            agency_id, agency_code, agency_name, agreement_id, starting_year, 
            starting_year_id, ending_year, ending_year_id, registered_year, 
            registered_year_id, contract_number, original_contract_amount, 
            maximum_contract_amount, description, vendor_history_id, vendor_id, 
            vendor_code, vendor_name, dollar_difference, percent_difference, 
            master_agreement_id, master_contract_number, agreement_type_id, 
            agreement_type_code, agreement_type_name, award_category_id, 
            award_category_code, award_category_name, award_method_id, award_method_code, 
            award_method_name, expenditure_object_codes, expenditure_object_names, 
            industry_type_id, industry_type_name, award_size_id, effective_begin_date, 
            effective_begin_date_id, effective_begin_year, effective_begin_year_id, 
            effective_end_date, effective_end_date_id, effective_end_year, 
            effective_end_year_id, registered_date, registered_date_id, brd_awd_no, 
            tracking_number, rfed_amount, master_agreement_yn, has_children, 
            original_version_flag, latest_flag, oge_agency_id, oge_agency_name, load_id, last_modified_date, 
            job_id)
	SELECT original_agreement_id, document_version, document_code_id, b.agency_history_id, 
            b.agency_id, b.agency_code, b.agency_name, a.agreement_id, starting_year, 
            starting_year_id, ending_year, ending_year_id, registered_year, 
            registered_year_id, contract_number, b.original_amount as original_contract_amount, 
            b.current_amount as maximum_contract_amount, description, b.vendor_history_id , b.vendor_id , 
            NULL as vendor_code, b.vendor_name, coalesce(b.current_amount,0) - coalesce(b.original_amount,0) as dollar_difference, 
			(CASE WHEN coalesce(b.original_amount,0) = 0 THEN 0 ELSE 
		ROUND((( coalesce(b.current_amount,0) - coalesce(b.original_amount,0)) * 100 )::decimal / coalesce(b.original_amount,0),2) END) as percent_difference, 
            master_agreement_id, master_contract_number, agreement_type_id, 
            agreement_type_code, agreement_type_name, award_category_id, 
            award_category_code, award_category_name, award_method_id, award_method_code, 
            award_method_name, expenditure_object_codes, expenditure_object_names, 
            industry_type_id, industry_type_name, award_size_id, effective_begin_date, 
            effective_begin_date_id, effective_begin_year, effective_begin_year_id, 
            effective_end_date, effective_end_date_id, effective_end_year, 
            effective_end_year_id, registered_date, registered_date_id, brd_awd_no, 
            tracking_number, rfed_amount, master_agreement_yn, has_children, 
            original_version_flag, latest_flag, b.agency_id, b.agency_name, load_id, last_modified_date, 
            job_id
	FROM agreement_snapshot_cy_edc a JOIN oge_contract_vendor_level b ON a.contract_number = b.fms_contract_number
	WHERE master_agreement_yn = 'N';
	

	
	UPDATE agreement_snapshot a
	SET oge_agency_id = edc_data.agency_id,
	    oge_agency_name = edc_data.agency_name,
		original_contract_amount = edc_data.original_amount,
		maximum_contract_amount = edc_data.current_amount,
		dollar_difference = coalesce(edc_data.current_amount,0) - coalesce(edc_data.original_amount,0) ,
		percent_difference = (CASE WHEN coalesce(edc_data.original_amount,0) = 0 THEN 0 ELSE 
		ROUND((( coalesce(edc_data.current_amount,0) - coalesce(edc_data.original_amount,0)) * 100 )::decimal / coalesce(edc_data.original_amount,0),2) END)
	FROM (SELECT master_agreement_id, Y.agency_id, agency_name, current_amount, original_amount
FROM (SELECT  b.master_agreement_id, a.agency_id, sum(current_amount) as current_amount, sum(original_amount) as original_amount
	FROM oge_contract_contract_level a
	JOIN (select distinct contract_number, master_agreement_id FROM history_agreement) b ON a.fms_contract_number = b.contract_number GROUP BY 1,2) X 
	LEFT JOIN ref_agency Y ON X.agency_id = Y.agency_id) edc_data
WHERE a.original_agreement_id = edc_data.master_agreement_id AND a.master_agreement_yn = 'Y' ;
	
	UPDATE agreement_snapshot_cy a
	SET oge_agency_id = edc_data.agency_id,
	    oge_agency_name = edc_data.agency_name,
		original_contract_amount = edc_data.original_amount,
		maximum_contract_amount = edc_data.current_amount,
		dollar_difference = coalesce(edc_data.current_amount,0) - coalesce(edc_data.original_amount,0) ,
		percent_difference = (CASE WHEN coalesce(edc_data.original_amount,0) = 0 THEN 0 ELSE 
		ROUND((( coalesce(edc_data.current_amount,0) - coalesce(edc_data.original_amount,0)) * 100 )::decimal / coalesce(edc_data.original_amount,0),2) END)
	FROM (SELECT master_agreement_id, Y.agency_id, agency_name, current_amount, original_amount
FROM (SELECT  b.master_agreement_id, a.agency_id, sum(current_amount) as current_amount, sum(original_amount) as original_amount
	FROM oge_contract_contract_level a
	JOIN (select distinct contract_number, master_agreement_id FROM history_agreement) b ON a.fms_contract_number = b.contract_number GROUP BY 1,2) X 
	LEFT JOIN ref_agency Y ON X.agency_id = Y.agency_id) edc_data
WHERE a.original_agreement_id = edc_data.master_agreement_id AND a.master_agreement_yn = 'Y' ;

UPDATE agreement_snapshot a
SET award_size_id = (CASE WHEN b.maximum_contract_amount IS NULL THEN 5 WHEN b.maximum_contract_amount <= 5000 THEN 4 WHEN b.maximum_contract_amount > 5000 
		AND b.maximum_contract_amount <= 100000 THEN 3 	WHEN  b.maximum_contract_amount > 100000 AND b.maximum_contract_amount <= 1000000 THEN 2 WHEN b.maximum_contract_amount > 1000000 THEN 1 
		ELSE 5 END)
FROM (select agreement_id, sum(maximum_contract_amount) as maximum_contract_amount FROM agreement_snapshot group by 1) b 
WHERE a.agreement_id = b.agreement_id ;

UPDATE agreement_snapshot_cy a
SET award_size_id = (CASE WHEN b.maximum_contract_amount IS NULL THEN 5 WHEN b.maximum_contract_amount <= 5000 THEN 4 WHEN b.maximum_contract_amount > 5000 
		AND b.maximum_contract_amount <= 100000 THEN 3 	WHEN  b.maximum_contract_amount > 100000 AND b.maximum_contract_amount <= 1000000 THEN 2 WHEN b.maximum_contract_amount > 1000000 THEN 1 
		ELSE 5 END)
FROM (select agreement_id, sum(maximum_contract_amount) as maximum_contract_amount FROM agreement_snapshot group by 1) b 
WHERE a.agreement_id = b.agreement_id ;

UPDATE agreement_snapshot a 
SET latest_flag = 'N'
WHERE latest_flag IS NULL AND master_agreement_yn = 'Y';

UPDATE agreement_snapshot_cy a 
SET latest_flag = 'N'
WHERE latest_flag IS NULL AND master_agreement_yn = 'Y';


		
RAISE NOTICE 'UPDATE FMS DATA FOR OGE 4';

	TRUNCATE agreement_snapshot_expanded CASCADE;
	INSERT INTO agreement_snapshot_expanded(
            original_agreement_id, agreement_id, fiscal_year, description, 
            contract_number, vendor_id, agency_id, industry_type_id, award_size_id, 
            original_contract_amount, maximum_contract_amount, rfed_amount, 
            starting_year, ending_year, dollar_difference, percent_difference, 
            award_method_id, document_code_id, master_agreement_id, master_agreement_yn, 
            status_flag)
	SELECT original_agreement_id, a.agreement_id, fiscal_year,  description, 
            contract_number, b.vendor_id, b.agency_id, a.industry_type_id, a.award_size_id as award_size_id, 
            b.original_amount as original_contract_amount, b.current_amount as maximum_contract_amount, rfed_amount, 
            starting_year, ending_year, coalesce(b.current_amount,0) - coalesce(b.original_amount,0) as dollar_difference, 
			(CASE WHEN coalesce(b.original_amount,0) = 0 THEN 0 ELSE 
		ROUND((( coalesce(b.current_amount,0) - coalesce(b.original_amount,0)) * 100 )::decimal / coalesce(b.original_amount,0),2) END) as percent_difference, 
            award_method_id, document_code_id, master_agreement_id, 'N' as master_agreement_yn, 
            status_flag
	FROM  agreement_snapshot_expanded_edc a JOIN 
	oge_contract_vendor_level b
	ON a.contract_number = b.fms_contract_number
	      WHERE a.master_agreement_yn = 'N';
	
			GET DIAGNOSTICS l_count = ROW_COUNT;	
	
	INSERT INTO etl.etl_data_load_verification(job_id,data_source_code,num_transactions,description)
	VALUES(p_job_id_in,'ED',l_count, '# of records inserted into agreement_snapshot_expanded');	
		  
	INSERT INTO agreement_snapshot_expanded(
            original_agreement_id, agreement_id, fiscal_year, description, 
            contract_number, vendor_id, agency_id, industry_type_id, award_size_id, 
            original_contract_amount, maximum_contract_amount, rfed_amount, 
            starting_year, ending_year, dollar_difference, percent_difference, 
            award_method_id, document_code_id, master_agreement_id, master_agreement_yn, 
            status_flag)
	SELECT original_agreement_id, a.agreement_id, fiscal_year, description, 
            a.contract_number, c.vendor_id, c.agency_id as agency_id, a.industry_type_id, a.award_size_id, 
            c.original_amount as original_contract_amount, c.current_amount as maximum_contract_amount, rfed_amount, 
            starting_year, ending_year, NULL as dollar_difference, NULL as percent_difference, 
            award_method_id, document_code_id, a.master_agreement_id, 'Y' as master_agreement_yn, 
            status_flag
	FROM  agreement_snapshot_expanded_edc a JOIN 
	(SELECT master_agreement_id, agency_id, vendor_id, sum(current_amount) as current_amount, sum(original_amount) as original_amount FROM 
	(select distinct contract_number, master_agreement_id FROM history_agreement) a  JOIN oge_contract_vendor_level b ON a.contract_number = b.fms_contract_number GROUP BY 1,2,3) c
	 ON a.original_agreement_id = c.master_agreement_id	
	WHERE a.master_agreement_yn = 'Y';
	
			GET DIAGNOSTICS l_count = ROW_COUNT;	
	
	INSERT INTO etl.etl_data_load_verification(job_id,data_source_code,num_transactions,description)
	VALUES(p_job_id_in,'ED',l_count, '# of records inserted into agreement_snapshot_expanded');
	

	TRUNCATE agreement_snapshot_expanded_cy CASCADE;
	INSERT INTO agreement_snapshot_expanded_cy(
            original_agreement_id, agreement_id, fiscal_year, description, 
            contract_number, vendor_id, agency_id, industry_type_id, award_size_id, 
            original_contract_amount, maximum_contract_amount, rfed_amount, 
            starting_year, ending_year, dollar_difference, percent_difference, 
            award_method_id, document_code_id, master_agreement_id, master_agreement_yn, 
            status_flag)
	SELECT original_agreement_id, a.agreement_id, fiscal_year,  description, 
            contract_number, b.vendor_id, b.agency_id, a.industry_type_id, a.award_size_id as award_size_id, 
            b.original_amount as original_contract_amount, b.current_amount as maximum_contract_amount, rfed_amount, 
            starting_year, ending_year, coalesce(b.current_amount,0) - coalesce(b.original_amount,0) as dollar_difference, 
			(CASE WHEN coalesce(b.original_amount,0) = 0 THEN 0 ELSE 
		ROUND((( coalesce(b.current_amount,0) - coalesce(b.original_amount,0)) * 100 )::decimal / coalesce(b.original_amount,0),2) END) as percent_difference, 
            award_method_id, document_code_id, master_agreement_id, 'N' as master_agreement_yn, 
            status_flag
	FROM  agreement_snapshot_expanded_cy_edc a JOIN 
	oge_contract_vendor_level b
	ON a.contract_number = b.fms_contract_number
	      WHERE a.master_agreement_yn = 'N';
	
			GET DIAGNOSTICS l_count = ROW_COUNT;	
	
	INSERT INTO etl.etl_data_load_verification(job_id,data_source_code,num_transactions,description)
	VALUES(p_job_id_in,'ED',l_count, '# of records inserted into agreement_snapshot_expanded_cy');	
		  
	INSERT INTO agreement_snapshot_expanded_cy(
            original_agreement_id, agreement_id, fiscal_year, description, 
            contract_number, vendor_id, agency_id, industry_type_id, award_size_id, 
            original_contract_amount, maximum_contract_amount, rfed_amount, 
            starting_year, ending_year, dollar_difference, percent_difference, 
            award_method_id, document_code_id, master_agreement_id, master_agreement_yn, 
            status_flag)
	SELECT original_agreement_id, a.agreement_id, fiscal_year, description, 
            a.contract_number, c.vendor_id, c.agency_id as agency_id, a.industry_type_id, a.award_size_id, 
            c.original_amount as original_contract_amount, c.current_amount as maximum_contract_amount, rfed_amount, 
            starting_year, ending_year, NULL as dollar_difference, NULL as percent_difference, 
            award_method_id, document_code_id, a.master_agreement_id, 'Y' as master_agreement_yn, 
            status_flag
	FROM  agreement_snapshot_expanded_cy_edc a JOIN 
	(SELECT master_agreement_id, agency_id, vendor_id, sum(current_amount) as current_amount, sum(original_amount) as original_amount FROM 
	(select distinct contract_number, master_agreement_id FROM history_agreement) a  JOIN oge_contract_vendor_level b ON a.contract_number = b.fms_contract_number GROUP BY 1,2,3) c
	 ON a.original_agreement_id = c.master_agreement_id
	      WHERE a.master_agreement_yn = 'Y';
	
			GET DIAGNOSTICS l_count = ROW_COUNT;	
	
	INSERT INTO etl.etl_data_load_verification(job_id,data_source_code,num_transactions,description)
	VALUES(p_job_id_in,'ED',l_count, '# of records inserted into agreement_snapshot_expanded_cy');
	
	
	RAISE NOTICE 'UPDATE FMS DATA FOR OGE 5';
	
	-- need to update the original_contract_amount  and maximum_contract_amount fields in agreement_snapshot_expanded and agreement_snapshot_expanded_cy for both child and master agreements
	
	
	
	UPDATE agreement_snapshot_expanded a
	SET original_contract_amount = edc_data.original_amount,
		maximum_contract_amount = edc_data.current_amount,
		dollar_difference = coalesce(edc_data.current_amount,0) - coalesce(edc_data.original_amount,0) ,
		percent_difference = (CASE WHEN coalesce(edc_data.original_amount,0) = 0 THEN 0 ELSE 
		ROUND((( coalesce(edc_data.current_amount,0) - coalesce(edc_data.original_amount,0)) * 100 )::decimal / coalesce(edc_data.original_amount,0),2) END)
	FROM (select master_agreement_id, sum(original_amount) as original_amount, sum(current_amount) as  current_amount 
	FROM (select distinct contract_number, master_agreement_id from history_agreement) a, oge_contract_contract_level b 	WHERE a.contract_number = b.fms_contract_number GROUP BY 1) edc_data 
	WHERE a.original_agreement_id = edc_data.master_agreement_id;
	
	UPDATE agreement_snapshot_expanded_cy a
	SET original_contract_amount = edc_data.original_amount,
		maximum_contract_amount = edc_data.current_amount,
		dollar_difference = coalesce(edc_data.current_amount,0) - coalesce(edc_data.original_amount,0) ,
		percent_difference = (CASE WHEN coalesce(edc_data.original_amount,0) = 0 THEN 0 ELSE 
		ROUND((( coalesce(edc_data.current_amount,0) - coalesce(edc_data.original_amount,0)) * 100 )::decimal / coalesce(edc_data.original_amount,0),2) END)
	FROM (select master_agreement_id, sum(original_amount) as original_amount, sum(current_amount) as  current_amount 
	FROM (select distinct contract_number, master_agreement_id from history_agreement) a, oge_contract_contract_level b 	WHERE a.contract_number = b.fms_contract_number GROUP BY 1) edc_data 
	WHERE a.original_agreement_id = edc_data.master_agreement_id;
	
	UPDATE agreement_snapshot_expanded a
	SET award_size_id = b.award_size_id
	FROM agreement_snapshot b
	WHERE a.agreement_id = b.agreement_id;
	
	UPDATE agreement_snapshot_expanded_cy a
	SET award_size_id = b.award_size_id
	FROM agreement_snapshot_cy b
	WHERE a.agreement_id = b.agreement_id;
	
	
	RAISE NOTICE 'UPDATE FMS DATA FOR OGE 6';
	
		l_end_time := timeofday()::timestamp;
INSERT INTO etl.etl_script_execution_status(job_id,script_name,completed_flag,start_time,end_time)
	VALUES(p_job_id_in,'etl.modifyFMSDataForOGE',1,l_start_time,l_end_time);
	
	RETURN 1;
	
EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in modifyFMSDataForOGE';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;	
	l_end_time := timeofday()::timestamp;
	INSERT INTO etl.etl_script_execution_status(job_id,script_name,completed_flag,start_time,end_time)
	VALUES(p_job_id_in,'etl.modifyFMSDataForOGE',0,l_start_time,l_end_time);
	RETURN 0;	
END;
$$ language plpgsql;