CREATE OR REPLACE FUNCTION etl.restoreOGETransactionsData(p_job_id_in bigint) RETURNS INT AS $$
DECLARE
	l_count bigint;
	l_start_time  timestamp;
	l_end_time  timestamp;
BEGIN
	l_start_time := timeofday()::timestamp;
	

    TRUNCATE  history_agreement CASCADE ;	
	INSERT INTO history_agreement(
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
            borough_code, block_code, lot_code, council_district_code, vendor_history_id + 100000, 
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
	FROM history_agreement_edc a  ;
	
		GET DIAGNOSTICS l_count = ROW_COUNT;	
	
	INSERT INTO etl.etl_data_load_verification(job_id,data_source_code,num_transactions,description)
	VALUES(p_job_id_in,'ED',l_count, '# of records inserted into history_agreement');
	
	TRUNCATE  history_agreement_accounting_line CASCADE;	
	INSERT INTO history_agreement_accounting_line(
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
	FROM  history_agreement_accounting_line_edc a;
	
				GET DIAGNOSTICS l_count = ROW_COUNT;	
	
	INSERT INTO etl.etl_data_load_verification(job_id,data_source_code,num_transactions,description)
	VALUES(p_job_id_in,'ED',l_count, '# of records inserted into history_agreement_accounting_line');
	
	TRUNCATE history_master_agreement CASCADE;
	INSERT INTO history_master_agreement(
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
            borough_code, block_code, lot_code, council_district_code, vendor_history_id + 100000, 
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
	FROM history_master_agreement_edc a;
	
			GET DIAGNOSTICS l_count = ROW_COUNT;	
	
	INSERT INTO etl.etl_data_load_verification(job_id,data_source_code,num_transactions,description)
	VALUES(p_job_id_in,'ED',l_count, '# of records inserted into history_master_agreement');
	
	
	TRUNCATE  agreement_snapshot CASCADE;
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
            original_version_flag, latest_flag, load_id, last_modified_date, 
            job_id)
	SELECT original_agreement_id, document_version, document_code_id, agency_history_id, 
            agency_id, agency_code, agency_name, a.agreement_id, starting_year, 
            starting_year_id, ending_year, ending_year_id, registered_year, 
            registered_year_id, contract_number, original_contract_amount, 
            maximum_contract_amount, description, vendor_history_id + 100000, vendor_id + 100000, 
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
	FROM agreement_snapshot_edc a;
	
			GET DIAGNOSTICS l_count = ROW_COUNT;	
	
	INSERT INTO etl.etl_data_load_verification(job_id,data_source_code,num_transactions,description)
	VALUES(p_job_id_in,'ED',l_count, '# of records inserted into agreement_snapshot');
	
	TRUNCATE  agreement_snapshot_cy CASCADE;
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
            original_version_flag, latest_flag, load_id, last_modified_date, 
            job_id)
	SELECT original_agreement_id, document_version, document_code_id, agency_history_id, 
            agency_id, agency_code, agency_name, a.agreement_id, starting_year, 
            starting_year_id, ending_year, ending_year_id, registered_year, 
            registered_year_id, contract_number, original_contract_amount, 
            maximum_contract_amount, description, vendor_history_id + 100000, vendor_id + 100000, 
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
	FROM agreement_snapshot_cy_edc a;
	
			GET DIAGNOSTICS l_count = ROW_COUNT;	
	
	INSERT INTO etl.etl_data_load_verification(job_id,data_source_code,num_transactions,description)
	VALUES(p_job_id_in,'ED',l_count, '# of records inserted into agreement_snapshot_cy');
	
	
	
		
	TRUNCATE disbursement CASCADE;	
    INSERT INTO disbursement(
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
            vendor_history_id + 100000, retainage_amount_original, retainage_amount, 
            privacy_flag, vendor_org_classification, bustype_mnrt, bustype_mnrt_status, 
            minority_type_id, bustype_wmno, bustype_wmno_status, bustype_locb, 
            bustype_locb_status, bustype_eent, bustype_eent_status, bustype_exmp, 
            bustype_exmp_status, created_load_id, updated_load_id, created_date, updated_date
	FROM disbursement_edc a ;
	
			GET DIAGNOSTICS l_count = ROW_COUNT;	
	
	INSERT INTO etl.etl_data_load_verification(job_id,data_source_code,num_transactions,description)
	VALUES(p_job_id_in,'ED',l_count, '# of records inserted into disbursement');
	
	TRUNCATE disbursement_line_item CASCADE;
	INSERT INTO disbursement_line_item (
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
	FROM disbursement_line_item_edc a ;
	
			GET DIAGNOSTICS l_count = ROW_COUNT;	
	
	INSERT INTO etl.etl_data_load_verification(job_id,data_source_code,num_transactions,description)
	VALUES(p_job_id_in,'ED',l_count, '# of records inserted into disbursement_line_item');
	
	
	TRUNCATE disbursement_line_item_details CASCADE;
	INSERT INTO disbursement_line_item_details(
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
            expenditure_object_id, vendor_id + 100000, department_id, maximum_contract_amount, 
            maximum_contract_amount_cy, maximum_spending_limit, maximum_spending_limit_cy, 
            document_id, vendor_name, vendor_customer_code, check_eft_issued_date, 
            agency_name, agency_short_name, location_name, location_code, 
            department_name, department_short_name, department_code, expenditure_object_name, 
            expenditure_object_code, budget_code_id, budget_code, budget_name, 
            contract_number, master_contract_number, master_child_contract_number, 
            contract_vendor_id + 100000, contract_vendor_id_cy + 100000, master_contract_vendor_id + 100000, 
            master_contract_vendor_id_cy + 100000, contract_agency_id, contract_agency_id_cy, 
            master_contract_agency_id, master_contract_agency_id_cy, master_purpose, 
            master_purpose_cy, purpose, purpose_cy, master_child_contract_agency_id, 
            master_child_contract_agency_id_cy, master_child_contract_vendor_id + 100000, 
            master_child_contract_vendor_id_cy  +100000, reporting_code, location_id, 
            fund_class_name, fund_class_code, spending_category_id, spending_category_name, 
            calendar_fiscal_year_id, calendar_fiscal_year, agreement_accounting_line_number, 
            agreement_commodity_line_number, agreement_vendor_line_number, 
            reference_document_number, reference_document_code, contract_document_code, 
            master_contract_document_code, file_type, load_id, last_modified_date, 
            job_id
	FROM disbursement_line_item_details_edc a ;
	
			GET DIAGNOSTICS l_count = ROW_COUNT;	
	
	INSERT INTO etl.etl_data_load_verification(job_id,data_source_code,num_transactions,description)
	VALUES(p_job_id_in,'ED',l_count, '# of records inserted into disbursement_line_item_details');
	
	DELETE FROM vendor_history where vendor_history_id > 100000;
	INSERT INTO vendor_history(
            vendor_history_id, vendor_id, legal_name, alias_name, miscellaneous_vendor_flag, 
            vendor_sub_code, load_id, created_date, updated_date)
	SELECT a.vendor_history_id + 100000, vendor_id + 100000, legal_name, alias_name, miscellaneous_vendor_flag, 
           vendor_sub_code, load_id, created_date, updated_date
	FROM   vendor_history_edc a  ;
	
			GET DIAGNOSTICS l_count = ROW_COUNT;	
	
	INSERT INTO etl.etl_data_load_verification(job_id,data_source_code,num_transactions,description)
	VALUES(p_job_id_in,'ED',l_count, '# of records inserted into vendor_history');
	
	DELETE FROM vendor WHERE vendor_id > 100000;
	INSERT INTO vendor(
            vendor_id, vendor_customer_code, legal_name, alias_name, miscellaneous_vendor_flag, 
            vendor_sub_code, display_flag, created_load_id, updated_load_id, 
            created_date, updated_date)
	SELECT a.vendor_id + 100000, vendor_customer_code, legal_name, alias_name, miscellaneous_vendor_flag, 
            vendor_sub_code, display_flag, created_load_id, updated_load_id, 
            created_date, updated_date
	FROM vendor_edc a  ;
	
			GET DIAGNOSTICS l_count = ROW_COUNT;	
	
	INSERT INTO etl.etl_data_load_verification(job_id,data_source_code,num_transactions,description)
	VALUES(p_job_id_in,'ED',l_count, '# of records inserted into vendor');
	
	
	DELETE FROM vendor_address WHERE vendor_address_id > 100000;
	INSERT INTO vendor_address(
            vendor_address_id, vendor_history_id, address_id, address_type_id, 
            effective_begin_date_id, effective_end_date_id, load_id, created_date, 
            updated_date)
	SELECT vendor_address_id + 100000, a.vendor_history_id + 100000, address_id + 100000, address_type_id, 
            effective_begin_date_id, effective_end_date_id, load_id, created_date, 
            updated_date
	FROM vendor_address_edc a ;
	
			GET DIAGNOSTICS l_count = ROW_COUNT;	
	
	INSERT INTO etl.etl_data_load_verification(job_id,data_source_code,num_transactions,description)
	VALUES(p_job_id_in,'ED',l_count, '# of records inserted into vendor_address');
	
	DELETE FROM vendor_business_type WHERE vendor_business_type_id > 100000;
	INSERT INTO vendor_business_type(
            vendor_business_type_id, vendor_history_id, business_type_id, 
            status, minority_type_id, load_id, created_date, updated_date)
	SELECT vendor_business_type_id + 100000, a.vendor_history_id + 100000, business_type_id, 
            status, minority_type_id, load_id, created_date, updated_date
	FROM vendor_business_type_edc a;
	
			GET DIAGNOSTICS l_count = ROW_COUNT;	
	
	INSERT INTO etl.etl_data_load_verification(job_id,data_source_code,num_transactions,description)
	VALUES(p_job_id_in,'ED',l_count, '# of records inserted into vendor_business_type');
	
	DELETE FROM address WHERE address_id > 100000;
	INSERT INTO address(
            address_id, address_line_1, address_line_2, city, state, zip,  country)
	SELECT a.address_id + 100000, address_line_1, address_line_2, city, state, zip,  country
	FROM address_edc a ;
	
			GET DIAGNOSTICS l_count = ROW_COUNT;	
	
	INSERT INTO etl.etl_data_load_verification(job_id,data_source_code,num_transactions,description)
	VALUES(p_job_id_in,'ED',l_count, '# of records inserted into address');
	
	
	/*
	DELETE FROM ref_agency_history a 
	USING ref_agency b
	WHERE   a.agency_id = b.agency_id  AND b.agency_code in ('z81');
		
	DELETE FROM ref_agency where agency_code in ('z81');
	
	INSERT INTO ref_agency(agency_id, agency_code, agency_name, original_agency_name, created_date, agency_short_name, is_display) VALUES(nextval('seq_ref_agency_agency_id'),'z81','NEW YORK CITY ECONOMIC DEVELOPMENT CORPORATION','NEW YORK CITY ECONOMIC DEVELOPMENT CORPORATION', now()::timestamp, 'NYC EDC','Y');

	INSERT INTO ref_agency_history(agency_history_id, agency_id, agency_name, created_date) SELECT nextval('seq_ref_agency_history_id'),agency_id, agency_name,now()::timestamp FROM ref_agency WHERE agency_code = 'z81';

	UPDATE edc_contract a
	SET agency_id = b.agency_id
	FROM ref_agency b
	WHERE a.agency_code = b.agency_code AND b.agency_code in ('z81');
	
	UPDATE tdc_contract a
	SET agency_id = b.agency_id
	FROM ref_agency b
	WHERE a.agency_code = b.agency_code AND b.agency_code in ('z81');
	
	UPDATE oge_contract a
	SET  agency_id = b.agency_id
	FROM ref_agency b
	WHERE a.agency_code = b.agency_code AND b.agency_code in ('z81');
	
	UPDATE disbursement_line_item_details disb
	SET agency_code = edc_data.agency_code,
		agency_id = edc_data.agency_id,
		contract_agency_id = edc_data.agency_id,
		contract_agency_id_cy = edc_data.agency_id,
		master_contract_agency_id = edc_data.agency_id,
		master_contract_agency_id_cy = edc_data.agency_id,
		master_child_contract_agency_id = edc_data.agency_id,
		master_child_contract_agency_id_cy = edc_data.agency_id,
		agency_name = edc_data.agency_name,
		agency_short_name = edc_data.agency_short_name,
		agency_history_id = edc_data.agency_history_id,
		vendor_name = edc_data.vendor_name,
		vendor_id = edc_data.vendor_id,
		contract_vendor_id = edc_data.vendor_id,
		contract_vendor_id_cy = edc_data.vendor_id,
		master_contract_vendor_id = edc_data.vendor_id,
		master_contract_vendor_id_cy = edc_data.vendor_id,
		master_child_contract_vendor_id = edc_data.vendor_id,
		master_child_contract_vendor_id_cy = edc_data.vendor_id,
		vendor_customer_code = NULL,
		department_id = edc_data.department_id ,
		department_name = edc_data.department_name,
		department_short_name = edc_data.department_short_name,
		department_code = edc_data.department_code		
	FROM (select disb.disbursement_line_item_id, ag.agency_code, ag.agency_id, ag.agency_name, ag.agency_short_name, agh.agency_history_id, edc.vendor_id, edc.vendor_name, 
	dep.department_id, dep.department_name, dep.department_short_name, dep.department_code FROM disbursement_line_item_details disb JOIN oge_contract edc ON disb.contract_number = edc.fms_contract_number AND disb.agreement_commodity_line_number = edc.fms_commodity_line  JOIN ref_agency ag ON edc.agency_id = ag.agency_id 
	JOIN (select agency_id, max(agency_history_id) agency_history_id from ref_agency_history group by 1) agh ON ag.agency_id = agh.agency_id
	JOIN ref_department dep ON edc.department_id = dep.department_id) edc_data
	WHERE disb.disbursement_line_item_id = edc_data.disbursement_line_item_id;
	
	UPDATE disbursement_line_item disb
	SET agency_history_id = disb1.agency_history_id
	FROM disbursement_line_item_details disb1
	WHERE disb.disbursement_line_item_id = disb1.disbursement_line_item_id;
	
	UPDATE disbursement_line_item disb
	SET department_history_id = disb1.department_history_id
	FROM disbursement_line_item_details disb1, ref_department dep, ref_department_history dep_his
	WHERE disb.disbursement_line_item_id = disb1.disbursement_line_item_id AND disb1.department_id = dep.department_id AND dep.department_id = dep_his.department_id ;
	
	
	TRUNCATE agreement_snapshot_expanded CASCADE;
	INSERT INTO agreement_snapshot_expanded(
            original_agreement_id, agreement_id, fiscal_year, description, 
            contract_number, vendor_id, agency_id, industry_type_id, award_size_id, 
            original_contract_amount, maximum_contract_amount, rfed_amount, 
            starting_year, ending_year, dollar_difference, percent_difference, 
            award_method_id, document_code_id, master_agreement_id, master_agreement_yn, 
            status_flag)
	SELECT original_agreement_id, a.agreement_id, fiscal_year, min(description) as description, 
            min(contract_number) as contract_number, b.vendor_id, min(b.agency_id) as agency_id, min(a.industry_type_id) as industry_type_id, min(a.award_size_id) as award_size_id, 
            sum(oge_registered_amount) as original_contract_amount, min(maximum_contract_amount) as maximum_contract_amount, min(rfed_amount) as rfed_amount, 
            min(starting_year) as starting_year, min(ending_year) ending_year, NULL as dollar_difference, NULL as percent_difference, 
            min(award_method_id) as award_method_id, min(document_code_id) as document_code_id, min(master_agreement_id) as master_agreement_id, 'N' as master_agreement_yn, 
            status_flag
	FROM  agreement_snapshot_expanded_edc a JOIN oge_contract b ON a.contract_number = b.fms_contract_number
	      WHERE a.master_agreement_yn = 'N'
		  GROUP BY 1,2,3,6,21;
	
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
	SELECT original_agreement_id, a.agreement_id, fiscal_year, min(description) as description, 
            min(a.contract_number) as contract_number, c.vendor_id, min(c.agency_id) as agency_id, min(a.industry_type_id) as industry_type_id, min(a.award_size_id) as award_size_id, 
            sum(oge_registered_amount) as original_contract_amount, min(maximum_contract_amount) as maximum_contract_amount, min(rfed_amount) as rfed_amount, 
            min(starting_year) as starting_year, min(ending_year) as ending_year, NULL as dollar_difference, NULL as percent_difference, 
            min(award_method_id) as award_method_id, min(document_code_id) as document_code_id, min(a.master_agreement_id) as master_agreement_id, 'Y' as master_agreement_yn, 
            status_flag
	FROM  agreement_snapshot_expanded_edc a JOIN (select distinct contract_number, master_agreement_id FROM history_agreement) b ON a.original_agreement_id = b.master_agreement_id JOIN oge_contract c ON b.contract_number = c.fms_contract_number
	      WHERE a.master_agreement_yn = 'Y'
		  GROUP BY 1,2,3,6,21;
	
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
	SELECT original_agreement_id, a.agreement_id, fiscal_year, min(description) as description, 
            min(contract_number) as contract_number, b.vendor_id, min(b.agency_id) as agency_id, min(a.industry_type_id) as industry_type_id, min(a.award_size_id) as award_size_id, 
            sum(oge_registered_amount) as original_contract_amount, min(maximum_contract_amount) as maximum_contract_amount, min(rfed_amount) as rfed_amount, 
            min(starting_year) as starting_year, min(ending_year) ending_year, NULL as dollar_difference, NULL as percent_difference, 
            min(award_method_id) as award_method_id, min(document_code_id) as document_code_id, min(master_agreement_id) as master_agreement_id, 'N' as master_agreement_yn, 
            status_flag
	FROM  agreement_snapshot_expanded_cy_edc a JOIN oge_contract b ON a.contract_number = b.fms_contract_number
	      WHERE a.master_agreement_yn = 'N'
		  GROUP BY 1,2,3,6,21;
	
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
	SELECT original_agreement_id, a.agreement_id, fiscal_year, min(description) as description, 
            min(a.contract_number) as contract_number, c.vendor_id, min(c.agency_id) as agency_id, min(a.industry_type_id) as industry_type_id, min(a.award_size_id) as award_size_id, 
            sum(oge_registered_amount) as original_contract_amount, min(maximum_contract_amount) as maximum_contract_amount, min(rfed_amount) as rfed_amount, 
            min(starting_year) as starting_year, min(ending_year) as ending_year, NULL as dollar_difference, NULL as percent_difference, 
            min(award_method_id) as award_method_id, min(document_code_id) as document_code_id, min(a.master_agreement_id) as master_agreement_id, 'Y' as master_agreement_yn, 
            status_flag
	FROM  agreement_snapshot_expanded_cy_edc a JOIN (select distinct contract_number, master_agreement_id FROM history_agreement) b ON a.original_agreement_id = b.master_agreement_id JOIN oge_contract c ON b.contract_number = c.fms_contract_number
	      WHERE a.master_agreement_yn = 'Y'
		  GROUP BY 1,2,3,6,21;
	
			GET DIAGNOSTICS l_count = ROW_COUNT;	
	
	INSERT INTO etl.etl_data_load_verification(job_id,data_source_code,num_transactions,description)
	VALUES(p_job_id_in,'ED',l_count, '# of records inserted into agreement_snapshot_expanded_cy');
	
	-- need to revisit the below logic for dollar difference and percent difference
	
	UPDATE agreement_snapshot_expanded
	SET  dollar_difference =  coalesce(maximum_contract_amount,0) - coalesce(original_contract_amount,0),
		 percent_difference = (CASE WHEN coalesce(original_contract_amount,0) = 0 THEN 0 ELSE 
		ROUND((( coalesce(maximum_contract_amount,0) - coalesce(original_contract_amount,0)) * 100 )::decimal / coalesce(original_contract_amount,0),2) END) ;
		
	UPDATE agreement_snapshot_expanded_cy
	SET  dollar_difference =  coalesce(maximum_contract_amount,0) - coalesce(original_contract_amount,0),
	     percent_difference = (CASE WHEN coalesce(original_contract_amount,0) = 0 THEN 0 ELSE 
		ROUND((( coalesce(maximum_contract_amount,0) - coalesce(original_contract_amount,0)) * 100 )::decimal / coalesce(original_contract_amount,0),2) END) ;
	
	TRUNCATE pending_contracts CASCADE;
	
	INSERT INTO pending_contracts (
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
	SELECT document_code_id, b.agency_id as document_agency_id, document_id, parent_document_code_id, 
            parent_document_agency_id, parent_document_id, encumbrance_amount_original, 
            encumbrance_amount, oge_registered_amount as original_maximum_amount_original, oge_registered_amount as original_maximum_amount, 
            revised_maximum_amount_original, revised_maximum_amount, registered_contract_max_amount, 
            b.vendor_name as vendor_legal_name, NULL as vendor_customer_code, b.vendor_id as vendor_id, description, 
            submitting_agency_id, oaisis_submitting_agency_desc, submitting_agency_code, 
            awarding_agency_id, oaisis_awarding_agency_desc, awarding_agency_code, 
            contract_type_name, cont_type_code, award_method_name, award_method_code, 
            award_method_id, start_date, end_date, revised_start_date, revised_end_date, 
            cif_received_date, cif_fiscal_year, cif_fiscal_year_id, tracking_number, 
            board_award_number, oca_number, version_number, a.fms_contract_number, 
            contract_number, fms_parent_contract_number, submitting_agency_name, 
            submitting_agency_short_name, awarding_agency_name, awarding_agency_short_name, 
            start_date_id, end_date_id, revised_start_date_id, revised_end_date_id, 
            cif_received_date_id, c.agency_code as document_agency_code, c.agency_name as document_agency_name, 
            c.agency_short_name as document_agency_short_name, funding_agency_id, funding_agency_code, 
            funding_agency_name, funding_agency_short_name, original_agreement_id, 
            original_master_agreement_id, dollar_difference, percent_difference, 
            original_or_modified, original_or_modified_desc, award_size_id, 
            award_category_id, industry_type_id, document_version, latest_flag
	FROM pending_contracts a JOIN (select fms_contract_number, vendor_id, agency_id, vendor_name, sum(oge_registered_amount) as oge_registered_amount FROM oge_contract group by 1,2,3,4) b ON a.fms_contract_number = b.fms_contract_number  JOIN ref_agency c ON b.agency_id = b.agency_id;
	
	INSERT INTO pending_contracts (
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
	SELECT document_code_id, b.agency_id as document_agency_id, document_id, parent_document_code_id, 
            parent_document_agency_id, parent_document_id, encumbrance_amount_original, 
            encumbrance_amount, oge_registered_amount as original_maximum_amount_original, oge_registered_amount as original_maximum_amount, 
            revised_maximum_amount_original, revised_maximum_amount, registered_contract_max_amount, 
            b.vendor_name as vendor_legal_name, NULL as vendor_customer_code, b.vendor_id as vendor_id, description, 
            submitting_agency_id, oaisis_submitting_agency_desc, submitting_agency_code, 
            awarding_agency_id, oaisis_awarding_agency_desc, awarding_agency_code, 
            contract_type_name, cont_type_code, award_method_name, award_method_code, 
            award_method_id, start_date, end_date, revised_start_date, revised_end_date, 
            cif_received_date, cif_fiscal_year, cif_fiscal_year_id, tracking_number, 
            board_award_number, oca_number, version_number, fms_contract_number, 
            a.contract_number, fms_parent_contract_number, submitting_agency_name, 
            submitting_agency_short_name, awarding_agency_name, awarding_agency_short_name, 
            start_date_id, end_date_id, revised_start_date_id, revised_end_date_id, 
            cif_received_date_id, c.agency_code as document_agency_code, c.agency_name as document_agency_name, 
            c.agency_short_name as document_agency_short_name, funding_agency_id, funding_agency_code, 
            funding_agency_name, funding_agency_short_name, original_agreement_id, 
            original_master_agreement_id, dollar_difference, percent_difference, 
            original_or_modified, original_or_modified_desc, award_size_id, 
            award_category_id, industry_type_id, document_version, latest_flag
	FROM pending_contracts a JOIN (select c.contract_number, vendor_id, agency_id, vendor_name, sum(oge_registered_amount) as  oge_registered_amount FROM oge_contract a JOIN (select distinct contract_number, master_agreement_id FROM history_agreement) b ON a.fms_contract_number = b.contract_number JOIN (SELECT distinct contract_number, original_master_agreement_id FROM history_master_agreement) c ON b.master_agreement_id = c.original_master_agreement_id group by 1,2,3,4) b ON a.fms_contract_number = b.contract_number  JOIN ref_agency c ON b.agency_id = b.agency_id;
	*/
		l_end_time := timeofday()::timestamp;
INSERT INTO etl.etl_script_execution_status(job_id,script_name,completed_flag,start_time,end_time)
	VALUES(p_job_id_in,'etl.restoreOGETransactionsData',1,l_start_time,l_end_time);
	
	RETURN 1;
	
EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in restoreOGETransactionsData';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;	
	l_end_time := timeofday()::timestamp;
	INSERT INTO etl.etl_script_execution_status(job_id,script_name,completed_flag,start_time,end_time)
	VALUES(p_job_id_in,'etl.restoreOGETransactionsData',0,l_start_time,l_end_time);
	RETURN 0;	
END;
$$ language plpgsql;