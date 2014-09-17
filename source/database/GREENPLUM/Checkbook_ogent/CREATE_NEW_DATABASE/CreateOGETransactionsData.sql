CREATE OR REPLACE FUNCTION etl.createOGETransactionsData(p_job_id_in bigint) RETURNS INT AS $$
DECLARE
	l_count bigint;
	l_start_time  timestamp;
	l_end_time  timestamp;
BEGIN
	l_start_time := timeofday()::timestamp;
	
    TRUNCATE  history_agreement_edc	;	
	INSERT INTO history_agreement_edc(
            agreement_id, master_agreement_id, document_code_id, agency_history_id, 
            document_id, document_version, tracking_number, record_date_id, 
            budget_fiscal_year, document_fiscal_year, document_period, description, 
            actual_amount_original, actual_amount, obligated_amount_original, 
            obligated_amount, maximum_contract_amount_original, maximum_contract_amount, 
            amendment_number, replacing_agreement_id, replaced_by_agreement_id, 
            award_status_id, procurement_id, procurement_type_id, effective_begin_date_id, 
            effective_end_date_id, reason_modification, source_created_date_id, 
            source_updated_date_id, document_function_code, award_method_id, 
            award_level_code, agreement_type_id, contract_class_code, award_category_id_1, 
            award_category_id_2, award_category_id_3, award_category_id_4, 
            award_category_id_5, number_responses, location_service, location_zip, 
            borough_code, block_code, lot_code, council_district_code, vendor_history_id, 
            vendor_preference_level, original_contract_amount_original, original_contract_amount, 
            registered_date_id, oca_number, number_solicitation, document_name, 
            original_term_begin_date_id, original_term_end_date_id, brd_awd_no, 
            rfed_amount_original, rfed_amount, registered_fiscal_year, registered_fiscal_year_id, 
            registered_calendar_year, registered_calendar_year_id, effective_end_fiscal_year, 
            effective_end_fiscal_year_id, effective_end_calendar_year, effective_end_calendar_year_id, 
            effective_begin_fiscal_year, effective_begin_fiscal_year_id, 
            effective_begin_calendar_year, effective_begin_calendar_year_id, 
            source_updated_fiscal_year, source_updated_fiscal_year_id, source_updated_calendar_year, 
            source_updated_calendar_year_id, contract_number, original_agreement_id, 
            original_version_flag, latest_flag, privacy_flag, created_load_id, 
            updated_load_id, created_date, updated_date)
	SELECT agreement_id, master_agreement_id, document_code_id, agency_history_id, 
            document_id, document_version, tracking_number, record_date_id, 
            budget_fiscal_year, document_fiscal_year, document_period, description, 
            actual_amount_original, actual_amount, obligated_amount_original, 
            obligated_amount, maximum_contract_amount_original, maximum_contract_amount, 
            amendment_number, replacing_agreement_id, replaced_by_agreement_id, 
            award_status_id, procurement_id, procurement_type_id, effective_begin_date_id, 
            effective_end_date_id, reason_modification, source_created_date_id, 
            source_updated_date_id, document_function_code, award_method_id, 
            award_level_code, agreement_type_id, contract_class_code, award_category_id_1, 
            award_category_id_2, award_category_id_3, award_category_id_4, 
            award_category_id_5, number_responses, location_service, location_zip, 
            borough_code, block_code, lot_code, council_district_code, vendor_history_id, 
            vendor_preference_level, original_contract_amount_original, original_contract_amount, 
            registered_date_id, oca_number, number_solicitation, document_name, 
            original_term_begin_date_id, original_term_end_date_id, brd_awd_no, 
            rfed_amount_original, rfed_amount, registered_fiscal_year, registered_fiscal_year_id, 
            registered_calendar_year, registered_calendar_year_id, effective_end_fiscal_year, 
            effective_end_fiscal_year_id, effective_end_calendar_year, effective_end_calendar_year_id, 
            effective_begin_fiscal_year, effective_begin_fiscal_year_id, 
            effective_begin_calendar_year, effective_begin_calendar_year_id, 
            source_updated_fiscal_year, source_updated_fiscal_year_id, source_updated_calendar_year, 
            source_updated_calendar_year_id, contract_number, original_agreement_id, 
            original_version_flag, latest_flag, privacy_flag, a.created_load_id, 
            a.updated_load_id, a.created_date, a.updated_date 
	FROM history_agreement a JOIN 
			(select distinct fms_contract_number from oge_contract) b ON a.contract_number = b.fms_contract_number ;
	
	
	TRUNCATE  history_agreement_accounting_line_edc	;	
	INSERT INTO history_agreement_accounting_line_edc(
            agreement_accounting_line_id, agreement_id, commodity_line_number, 
            line_number, event_type_code, description, line_amount_original, 
            line_amount, budget_fiscal_year, fiscal_year, fiscal_period, 
            fund_class_id, agency_history_id, department_history_id, expenditure_object_history_id, 
            revenue_source_id, location_code, budget_code_id, reporting_code, 
            rfed_line_amount_original, rfed_line_amount, created_load_id, 
            updated_load_id, created_date, updated_date)
	SELECT  agreement_accounting_line_id, a.agreement_id, commodity_line_number, 
            line_number, event_type_code, a.description, line_amount_original, 
            line_amount, a.budget_fiscal_year, a.fiscal_year, a.fiscal_period, 
            a.fund_class_id, a.agency_history_id, a.department_history_id, a.expenditure_object_history_id, 
            a.revenue_source_id, a.location_code, a.budget_code_id, reporting_code, 
            rfed_line_amount_original, rfed_line_amount, a.created_load_id, 
            a.updated_load_id, a.created_date, a.updated_date
	FROM  history_agreement_accounting_line a, history_agreement_edc b, oge_contract c
	WHERE  a.agreement_id = b.agreement_id AND b.contract_number = c.fms_contract_number AND a.commodity_line_number = c.fms_commodity_line;
	
	TRUNCATE history_master_agreement_edc;
	INSERT INTO history_master_agreement_edc(
            master_agreement_id, document_code_id, agency_history_id, document_id, 
            document_version, tracking_number, record_date_id, budget_fiscal_year, 
            document_fiscal_year, document_period, description, actual_amount_original, 
            actual_amount, total_amount_original, total_amount, replacing_master_agreement_id, 
            replaced_by_master_agreement_id, award_status_id, procurement_id, 
            procurement_type_id, effective_begin_date_id, effective_end_date_id, 
            reason_modification, source_created_date_id, source_updated_date_id, 
            document_function_code, award_method_id, agreement_type_id, award_category_id_1, 
            award_category_id_2, award_category_id_3, award_category_id_4, 
            award_category_id_5, number_responses, location_service, location_zip, 
            borough_code, block_code, lot_code, council_district_code, vendor_history_id, 
            vendor_preference_level, board_approved_award_no, board_approved_award_date_id, 
            original_contract_amount_original, original_contract_amount, 
            oca_number, original_term_begin_date_id, original_term_end_date_id, 
            registered_date_id, maximum_amount_original, maximum_amount, 
            maximum_spending_limit_original, maximum_spending_limit, award_level_code, 
            contract_class_code, number_solicitation, document_name, registered_fiscal_year, 
            registered_fiscal_year_id, registered_calendar_year, registered_calendar_year_id, 
            effective_end_fiscal_year, effective_end_fiscal_year_id, effective_end_calendar_year, 
            effective_end_calendar_year_id, effective_begin_fiscal_year, 
            effective_begin_fiscal_year_id, effective_begin_calendar_year, 
            effective_begin_calendar_year_id, source_updated_fiscal_year, 
            source_updated_fiscal_year_id, source_updated_calendar_year, 
            source_updated_calendar_year_id, contract_number, original_master_agreement_id, 
            original_version_flag, latest_flag, privacy_flag, created_load_id, 
            updated_load_id, created_date, updated_date)
	SELECT  a.master_agreement_id, document_code_id, agency_history_id, document_id, 
            document_version, tracking_number, record_date_id, budget_fiscal_year, 
            document_fiscal_year, document_period, description, actual_amount_original, 
            actual_amount, total_amount_original, total_amount, replacing_master_agreement_id, 
            replaced_by_master_agreement_id, award_status_id, procurement_id, 
            procurement_type_id, effective_begin_date_id, effective_end_date_id, 
            reason_modification, source_created_date_id, source_updated_date_id, 
            document_function_code, award_method_id, agreement_type_id, award_category_id_1, 
            award_category_id_2, award_category_id_3, award_category_id_4, 
            award_category_id_5, number_responses, location_service, location_zip, 
            borough_code, block_code, lot_code, council_district_code, vendor_history_id, 
            vendor_preference_level, board_approved_award_no, board_approved_award_date_id, 
            original_contract_amount_original, original_contract_amount, 
            oca_number, original_term_begin_date_id, original_term_end_date_id, 
            registered_date_id, maximum_amount_original, maximum_amount, 
            maximum_spending_limit_original, maximum_spending_limit, award_level_code, 
            contract_class_code, number_solicitation, document_name, registered_fiscal_year, 
            registered_fiscal_year_id, registered_calendar_year, registered_calendar_year_id, 
            effective_end_fiscal_year, effective_end_fiscal_year_id, effective_end_calendar_year, 
            effective_end_calendar_year_id, effective_begin_fiscal_year, 
            effective_begin_fiscal_year_id, effective_begin_calendar_year, 
            effective_begin_calendar_year_id, source_updated_fiscal_year, 
            source_updated_fiscal_year_id, source_updated_calendar_year, 
            source_updated_calendar_year_id, contract_number, original_master_agreement_id, 
            original_version_flag, latest_flag, privacy_flag, created_load_id, 
            updated_load_id, created_date, updated_date
	FROM history_master_agreement a JOIN (select distinct master_agreement_id from history_agreement_edc) b ON a.original_master_agreement_id = b.master_agreement_id ;
	
UPDATE history_master_agreement_edc a 
	SET num_associated_contracts = b.total_contracts
	FROM (SELECT count(*) as total_contracts, a.master_agreement_id   FROM history_agreement a  JOIN history_master_agreement_edc b ON a.master_agreement_id = b.original_master_agreement_id
 WHERE  a.latest_flag = 'Y' GROUP BY 2) b  
 WHERE  a.original_master_agreement_id = b.master_agreement_id;
 
 
	TRUNCATE  agreement_snapshot_edc;
	INSERT INTO agreement_snapshot_edc(
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
            original_version_flag, latest_flag, load_id, last_modified_date, 
            job_id)
	SELECT original_agreement_id, document_version, document_code_id, agency_history_id, 
            agency_id, agency_code, agency_name, a.agreement_id, starting_year, 
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
            original_version_flag, latest_flag, load_id, last_modified_date, 
            job_id
	FROM agreement_snapshot a JOIN (select  agreement_id  from history_agreement_edc) b ON a.agreement_id = b.agreement_id
	WHERE a.master_agreement_yn = 'N' ;
	
	INSERT INTO agreement_snapshot_edc(
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
            original_version_flag, latest_flag, load_id, last_modified_date, 
            job_id)
	SELECT original_agreement_id, document_version, document_code_id, agency_history_id, 
            agency_id, agency_code, agency_name, agreement_id, starting_year, 
            starting_year_id, ending_year, ending_year_id, registered_year, 
            registered_year_id, contract_number, original_contract_amount, 
            maximum_contract_amount, description, vendor_history_id, vendor_id, 
            vendor_code, vendor_name, dollar_difference, percent_difference, 
            a.master_agreement_id, master_contract_number, agreement_type_id, 
            agreement_type_code, agreement_type_name, award_category_id, 
            award_category_code, award_category_name, award_method_id, award_method_code, 
            award_method_name, expenditure_object_codes, expenditure_object_names, 
            industry_type_id, industry_type_name, award_size_id, effective_begin_date, 
            effective_begin_date_id, effective_begin_year, effective_begin_year_id, 
            effective_end_date, effective_end_date_id, effective_end_year, 
            effective_end_year_id, registered_date, registered_date_id, brd_awd_no, 
            tracking_number, rfed_amount, master_agreement_yn, has_children, 
            original_version_flag, latest_flag, load_id, last_modified_date, 
            job_id
	FROM agreement_snapshot a JOIN (select  master_agreement_id  from history_master_agreement_edc) b ON a.agreement_id = b.master_agreement_id
	WHERE a.master_agreement_yn = 'Y' ;
	
	
	TRUNCATE  agreement_snapshot_cy_edc;
	INSERT INTO agreement_snapshot_cy_edc(
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
            original_version_flag, latest_flag, load_id, last_modified_date, 
            job_id)
	SELECT original_agreement_id, document_version, document_code_id, agency_history_id, 
            agency_id, agency_code, agency_name, a.agreement_id, starting_year, 
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
            original_version_flag, latest_flag, load_id, last_modified_date, 
            job_id
	FROM agreement_snapshot_cy a JOIN (select  agreement_id  from history_agreement_edc) b ON a.agreement_id = b.agreement_id
	WHERE a.master_agreement_yn = 'N' ;
	
	INSERT INTO agreement_snapshot_cy_edc(
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
            original_version_flag, latest_flag, load_id, last_modified_date, 
            job_id)
	SELECT original_agreement_id, document_version, document_code_id, agency_history_id, 
            agency_id, agency_code, agency_name, agreement_id, starting_year, 
            starting_year_id, ending_year, ending_year_id, registered_year, 
            registered_year_id, contract_number, original_contract_amount, 
            maximum_contract_amount, description, vendor_history_id, vendor_id, 
            vendor_code, vendor_name, dollar_difference, percent_difference, 
            a.master_agreement_id, master_contract_number, agreement_type_id, 
            agreement_type_code, agreement_type_name, award_category_id, 
            award_category_code, award_category_name, award_method_id, award_method_code, 
            award_method_name, expenditure_object_codes, expenditure_object_names, 
            industry_type_id, industry_type_name, award_size_id, effective_begin_date, 
            effective_begin_date_id, effective_begin_year, effective_begin_year_id, 
            effective_end_date, effective_end_date_id, effective_end_year, 
            effective_end_year_id, registered_date, registered_date_id, brd_awd_no, 
            tracking_number, rfed_amount, master_agreement_yn, has_children, 
            original_version_flag, latest_flag, load_id, last_modified_date, 
            job_id
	FROM agreement_snapshot_cy a JOIN (select  master_agreement_id  from history_master_agreement_edc) b ON a.agreement_id = b.master_agreement_id
	WHERE a.master_agreement_yn = 'Y' ;
	
	
	TRUNCATE agreement_snapshot_expanded_edc ;
	INSERT INTO agreement_snapshot_expanded_edc(
            original_agreement_id, agreement_id, fiscal_year, description, 
            contract_number, vendor_id, agency_id, industry_type_id, award_size_id, 
            original_contract_amount, maximum_contract_amount, rfed_amount, 
            starting_year, ending_year, dollar_difference, percent_difference, 
            award_method_id, document_code_id, master_agreement_id, master_agreement_yn, 
            status_flag)
	SELECT original_agreement_id, a.agreement_id, fiscal_year, description, 
            contract_number, vendor_id, agency_id, industry_type_id, award_size_id, 
            original_contract_amount, maximum_contract_amount, rfed_amount, 
            starting_year, ending_year, dollar_difference, percent_difference, 
            award_method_id, document_code_id, master_agreement_id, a.master_agreement_yn, 
            status_flag
	FROM  agreement_snapshot_expanded a JOIN (select distinct agreement_id, master_agreement_yn from agreement_snapshot_edc) b 
	ON a.agreement_id = b.agreement_id AND a.master_agreement_yn = b.master_agreement_yn ;
	
	TRUNCATE agreement_snapshot_expanded_cy_edc;
	INSERT INTO agreement_snapshot_expanded_cy_edc(
            original_agreement_id, agreement_id, fiscal_year, description, 
            contract_number, vendor_id, agency_id, industry_type_id, award_size_id, 
            original_contract_amount, maximum_contract_amount, rfed_amount, 
            starting_year, ending_year, dollar_difference, percent_difference, 
            award_method_id, document_code_id, master_agreement_id, master_agreement_yn, 
            status_flag)
	SELECT original_agreement_id, a.agreement_id, fiscal_year, description, 
            contract_number, vendor_id, agency_id, industry_type_id, award_size_id, 
            original_contract_amount, maximum_contract_amount, rfed_amount, 
            starting_year, ending_year, dollar_difference, percent_difference, 
            award_method_id, document_code_id, master_agreement_id, a.master_agreement_yn, 
            status_flag
	FROM  agreement_snapshot_expanded_cy a JOIN (select distinct agreement_id, master_agreement_yn from agreement_snapshot_cy_edc) b 
	ON a.agreement_id = b.agreement_id AND a.master_agreement_yn = b.master_agreement_yn ;
	
	TRUNCATE disbursement_line_item_edc ;
	INSERT INTO disbursement_line_item_edc(
            disbursement_line_item_id, disbursement_id, line_number, disbursement_number, 
            budget_fiscal_year, fiscal_year, fiscal_period, fund_class_id, 
            agency_history_id, department_history_id, expenditure_object_history_id, 
            budget_code_id, fund_code, reporting_code, check_amount_original, 
            check_amount, agreement_id, agreement_accounting_line_number, 
            agreement_commodity_line_number, agreement_vendor_line_number, 
            reference_document_number, reference_document_code, location_history_id, 
            retainage_amount_original, retainage_amount, check_eft_issued_nyc_year_id, 
            file_type, created_load_id, updated_load_id, created_date, updated_date)
	SELECT  disbursement_line_item_id, disbursement_id, a.line_number, disbursement_number, 
            a.budget_fiscal_year, a.fiscal_year, a.fiscal_period, a.fund_class_id, 
            a.agency_history_id, a.department_history_id, a.expenditure_object_history_id, 
            a.budget_code_id, a.fund_code, a.reporting_code, check_amount_original, 
            check_amount, a.agreement_id, agreement_accounting_line_number, 
            agreement_commodity_line_number, agreement_vendor_line_number, 
            reference_document_number, reference_document_code, location_history_id, 
            retainage_amount_original, retainage_amount, check_eft_issued_nyc_year_id, 
            file_type, a.created_load_id, a.updated_load_id, a.created_date, a.updated_date
	FROM disbursement_line_item a JOIN history_agreement_edc b ON a.agreement_id = b.agreement_id JOIN (select distinct fms_contract_number, fms_commodity_line from oge_contract) c ON b.contract_number = c.fms_contract_number AND a.agreement_commodity_line_number = c.fms_commodity_line ;
	
	
	TRUNCATE disbursement_edc;	
    INSERT INTO disbursement_edc(
            disbursement_id, document_code_id, agency_history_id, document_id, 
            document_version, disbursement_number, record_date_id, budget_fiscal_year, 
            document_fiscal_year, document_period, check_eft_amount_original, 
            check_eft_amount, check_eft_issued_date_id, check_eft_record_date_id, 
            expenditure_status_id, expenditure_cancel_type_id, expenditure_cancel_reason_id, 
            total_accounting_line_amount_original, total_accounting_line_amount, 
            vendor_history_id, retainage_amount_original, retainage_amount, 
            privacy_flag, vendor_org_classification, bustype_mnrt, bustype_mnrt_status, 
            minority_type_id, bustype_wmno, bustype_wmno_status, bustype_locb, 
            bustype_locb_status, bustype_eent, bustype_eent_status, bustype_exmp, 
            bustype_exmp_status, created_load_id, updated_load_id, created_date, updated_date)
	SELECT  a.disbursement_id, document_code_id, agency_history_id, document_id, 
            document_version, disbursement_number, record_date_id, budget_fiscal_year, 
            document_fiscal_year, document_period, check_eft_amount_original, 
            check_eft_amount, check_eft_issued_date_id, check_eft_record_date_id, 
            expenditure_status_id, expenditure_cancel_type_id, expenditure_cancel_reason_id, 
            total_accounting_line_amount_original, total_accounting_line_amount, 
            vendor_history_id, retainage_amount_original, retainage_amount, 
            privacy_flag, vendor_org_classification, bustype_mnrt, bustype_mnrt_status, 
            minority_type_id, bustype_wmno, bustype_wmno_status, bustype_locb, 
            bustype_locb_status, bustype_eent, bustype_eent_status, bustype_exmp, 
            bustype_exmp_status, created_load_id, updated_load_id, created_date, updated_date
	FROM disbursement a JOIN (select distinct disbursement_id FROM disbursement_line_item_edc) b ON a.disbursement_id = b.disbursement_id;
	
	
	TRUNCATE disbursement_line_item_details_edc ;
	INSERT INTO disbursement_line_item_details_edc(
            disbursement_line_item_id, disbursement_id, line_number, disbursement_number, 
            check_eft_issued_date_id, check_eft_issued_nyc_year_id, fiscal_year, 
            check_eft_issued_cal_month_id, agreement_id, master_agreement_id, 
            fund_class_id, check_amount, agency_id, agency_history_id, agency_code, 
            expenditure_object_id, vendor_id, department_id, maximum_contract_amount, 
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
            master_contract_document_code, file_type, load_id, last_modified_date, 
            job_id)
	SELECT 	a.disbursement_line_item_id, disbursement_id, line_number, disbursement_number, 
            check_eft_issued_date_id, check_eft_issued_nyc_year_id, fiscal_year, 
            check_eft_issued_cal_month_id, agreement_id, master_agreement_id, 
            fund_class_id, check_amount, agency_id, agency_history_id, agency_code, 
            expenditure_object_id, vendor_id, department_id, maximum_contract_amount, 
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
            master_contract_document_code, file_type, load_id, last_modified_date, 
            job_id
	FROM disbursement_line_item_details a JOIN (select distinct disbursement_line_item_id from disbursement_line_item_edc) b ON a.disbursement_line_item_id = b.disbursement_line_item_id;
	
	TRUNCATE pending_contracts_edc;
	
	INSERT INTO pending_contracts_edc(
            document_code_id, document_agency_id, document_id, parent_document_code_id, 
            parent_document_agency_id, parent_document_id, encumbrance_amount_original, 
            encumbrance_amount, original_maximum_amount_original, original_maximum_amount, 
            revised_maximum_amount_original, revised_maximum_amount, registered_contract_max_amount, 
            vendor_legal_name, vendor_customer_code, vendor_id, description, 
            submitting_agency_id, oaisis_submitting_agency_desc, submitting_agency_code, 
            awarding_agency_id, oaisis_awarding_agency_desc, awarding_agency_code, 
            contract_type_name, cont_type_code, award_method_name, award_method_code, 
            award_method_id, start_date, end_date, revised_start_date, revised_end_date, 
            cif_received_date, cif_fiscal_year, cif_fiscal_year_id, tracking_number, 
            board_award_number, oca_number, version_number, fms_contract_number, 
            contract_number, fms_parent_contract_number, submitting_agency_name, 
            submitting_agency_short_name, awarding_agency_name, awarding_agency_short_name, 
            start_date_id, end_date_id, revised_start_date_id, revised_end_date_id, 
            cif_received_date_id, document_agency_code, document_agency_name, 
            document_agency_short_name, funding_agency_id, funding_agency_code, 
            funding_agency_name, funding_agency_short_name, original_agreement_id, 
            original_master_agreement_id, dollar_difference, percent_difference, 
            original_or_modified, original_or_modified_desc, award_size_id, 
            award_category_id, industry_type_id, document_version, latest_flag)
	SELECT  document_code_id, document_agency_id, document_id, parent_document_code_id, 
            parent_document_agency_id, parent_document_id, encumbrance_amount_original, 
            encumbrance_amount, original_maximum_amount_original, original_maximum_amount, 
            revised_maximum_amount_original, revised_maximum_amount, registered_contract_max_amount, 
            vendor_legal_name, vendor_customer_code, vendor_id, description, 
            submitting_agency_id, oaisis_submitting_agency_desc, submitting_agency_code, 
            awarding_agency_id, oaisis_awarding_agency_desc, awarding_agency_code, 
            contract_type_name, cont_type_code, award_method_name, award_method_code, 
            award_method_id, start_date, end_date, revised_start_date, revised_end_date, 
            cif_received_date, cif_fiscal_year, cif_fiscal_year_id, tracking_number, 
            board_award_number, oca_number, version_number, a.fms_contract_number, 
            contract_number, fms_parent_contract_number, submitting_agency_name, 
            submitting_agency_short_name, awarding_agency_name, awarding_agency_short_name, 
            start_date_id, end_date_id, revised_start_date_id, revised_end_date_id, 
            cif_received_date_id, document_agency_code, document_agency_name, 
            document_agency_short_name, funding_agency_id, funding_agency_code, 
            funding_agency_name, funding_agency_short_name, original_agreement_id, 
            original_master_agreement_id, dollar_difference, percent_difference, 
            original_or_modified, original_or_modified_desc, award_size_id, 
            award_category_id, industry_type_id, document_version, latest_flag
	FROM pending_contracts a JOIN (select distinct fms_contract_number from oge_contract) b ON a.fms_contract_number = b.fms_contract_number;
	
		INSERT INTO pending_contracts_edc(
            document_code_id, document_agency_id, document_id, parent_document_code_id, 
            parent_document_agency_id, parent_document_id, encumbrance_amount_original, 
            encumbrance_amount, original_maximum_amount_original, original_maximum_amount, 
            revised_maximum_amount_original, revised_maximum_amount, registered_contract_max_amount, 
            vendor_legal_name, vendor_customer_code, vendor_id, description, 
            submitting_agency_id, oaisis_submitting_agency_desc, submitting_agency_code, 
            awarding_agency_id, oaisis_awarding_agency_desc, awarding_agency_code, 
            contract_type_name, cont_type_code, award_method_name, award_method_code, 
            award_method_id, start_date, end_date, revised_start_date, revised_end_date, 
            cif_received_date, cif_fiscal_year, cif_fiscal_year_id, tracking_number, 
            board_award_number, oca_number, version_number, fms_contract_number, 
            contract_number, fms_parent_contract_number, submitting_agency_name, 
            submitting_agency_short_name, awarding_agency_name, awarding_agency_short_name, 
            start_date_id, end_date_id, revised_start_date_id, revised_end_date_id, 
            cif_received_date_id, document_agency_code, document_agency_name, 
            document_agency_short_name, funding_agency_id, funding_agency_code, 
            funding_agency_name, funding_agency_short_name, original_agreement_id, 
            original_master_agreement_id, dollar_difference, percent_difference, 
            original_or_modified, original_or_modified_desc, award_size_id, 
            award_category_id, industry_type_id, document_version, latest_flag)
	SELECT  document_code_id, document_agency_id, document_id, parent_document_code_id, 
            parent_document_agency_id, parent_document_id, encumbrance_amount_original, 
            encumbrance_amount, original_maximum_amount_original, original_maximum_amount, 
            revised_maximum_amount_original, revised_maximum_amount, registered_contract_max_amount, 
            vendor_legal_name, vendor_customer_code, vendor_id, description, 
            submitting_agency_id, oaisis_submitting_agency_desc, submitting_agency_code, 
            awarding_agency_id, oaisis_awarding_agency_desc, awarding_agency_code, 
            contract_type_name, cont_type_code, award_method_name, award_method_code, 
            award_method_id, start_date, end_date, revised_start_date, revised_end_date, 
            cif_received_date, cif_fiscal_year, cif_fiscal_year_id, tracking_number, 
            board_award_number, oca_number, version_number, fms_contract_number, 
            a.contract_number, fms_parent_contract_number, submitting_agency_name, 
            submitting_agency_short_name, awarding_agency_name, awarding_agency_short_name, 
            start_date_id, end_date_id, revised_start_date_id, revised_end_date_id, 
            cif_received_date_id, document_agency_code, document_agency_name, 
            document_agency_short_name, funding_agency_id, funding_agency_code, 
            funding_agency_name, funding_agency_short_name, original_agreement_id, 
            original_master_agreement_id, dollar_difference, percent_difference, 
            original_or_modified, original_or_modified_desc, award_size_id, 
            award_category_id, industry_type_id, document_version, latest_flag
	FROM pending_contracts a JOIN (select distinct contract_number from history_master_agreement) b ON a.fms_contract_number = b.contract_number;
	
	
	TRUNCATE vendor_history_edc;
	INSERT INTO vendor_history_edc(
            vendor_history_id, vendor_id, legal_name, alias_name, miscellaneous_vendor_flag, 
            vendor_sub_code, load_id, created_date, updated_date)
	SELECT a.vendor_history_id, vendor_id, legal_name, alias_name, miscellaneous_vendor_flag, 
           vendor_sub_code, load_id, created_date, updated_date
	FROM   vendor_history a JOIN 
	(SELECT distinct vendor_history_id FROM history_agreement_edc UNION SELECT distinct vendor_history_id FROM history_master_agreement_edc 
	UNION SELECT distinct vendor_history_id FROM disbursement_edc) b ON a.vendor_history_id = b.vendor_history_id ;
	
	
	TRUNCATE vendor_edc;
	INSERT INTO vendor_edc(
            vendor_id, vendor_customer_code, legal_name, alias_name, miscellaneous_vendor_flag, 
            vendor_sub_code, display_flag, created_load_id, updated_load_id, 
            created_date, updated_date)
	SELECT a.vendor_id, vendor_customer_code, legal_name, alias_name, miscellaneous_vendor_flag, 
            vendor_sub_code, display_flag, created_load_id, updated_load_id, 
            created_date, updated_date
	FROM vendor a JOIN (select distinct vendor_id from vendor_history_edc) b ON a.vendor_id = b.vendor_id ;
	
	
	TRUNCATE vendor_address_edc;
	INSERT INTO vendor_address_edc(
            vendor_address_id, vendor_history_id, address_id, address_type_id, 
            effective_begin_date_id, effective_end_date_id, load_id, created_date, 
            updated_date)
	SELECT vendor_address_id, a.vendor_history_id, address_id, address_type_id, 
            effective_begin_date_id, effective_end_date_id, load_id, created_date, 
            updated_date
	FROM vendor_address a JOIN (select distinct vendor_history_id from vendor_history_edc) b ON a.vendor_history_id = b.vendor_history_id ;
	
	
	TRUNCATE vendor_business_type_edc;
	INSERT INTO vendor_business_type_edc(
            vendor_business_type_id, vendor_history_id, business_type_id, 
            status, minority_type_id, load_id, created_date, updated_date)
	SELECT vendor_business_type_id, a.vendor_history_id, business_type_id, 
            status, minority_type_id, load_id, created_date, updated_date
	FROM vendor_business_type a JOIN (select distinct vendor_history_id from vendor_history_edc) b ON a.vendor_history_id = b.vendor_history_id ;
	
	TRUNCATE address_edc;
	INSERT INTO address_edc(
            address_id, address_line_1, address_line_2, city, state, zip,  country)
	SELECT a.address_id, address_line_1, address_line_2, city, state, zip,  country
	FROM address a JOIN (select distinct address_id FROM vendor_address_edc) b ON a.address_id = b.address_id ;
			
	/*SELECT l9.contract_number,
       l9.original_agreement_id,
       l10.document_code AS document_code_checkbook_ref_document_code,
       l9.rfed_amount
  FROM history_agreement l9
       LEFT OUTER JOIN ref_document_code l10 ON l10.document_code_id = l9.document_code_id
       LEFT OUTER JOIN history_master_agreement l11 ON l11.master_agreement_id = l9.master_agreement_id
 WHERE l11.original_master_agreement_id = 1805956
   AND l9.latest_flag = 'Y'
 ORDER BY rfed_amount DESC, contract_number DESC
 
 
 SELECT count(*),a.master_agreement_id
  FROM history_agreement a
        JOIN history_master_agreement_edc b ON a.master_agreement_id = b.original_master_agreement_id
 WHERE  a.latest_flag = 'Y' GROUP BY 2
 */
	
	l_end_time := timeofday()::timestamp;
INSERT INTO etl.etl_script_execution_status(job_id,script_name,completed_flag,start_time,end_time)
	VALUES(p_job_id_in,'etl.createOGETransactionsData',1,l_start_time,l_end_time);
		
	
	
	RETURN 1;
	
EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in createOGETransactionsData';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;	
	l_end_time := timeofday()::timestamp;
	INSERT INTO etl.etl_script_execution_status(job_id,script_name,completed_flag,start_time,end_time)
	VALUES(p_job_id_in,'etl.createOGETransactionsData',0,l_start_time,l_end_time);
	RETURN 0;	
END;
$$ language plpgsql;