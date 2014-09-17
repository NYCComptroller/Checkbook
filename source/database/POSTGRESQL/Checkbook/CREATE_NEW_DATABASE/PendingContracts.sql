CREATE OR REPLACE FUNCTION etl.processPendingContracts(p_load_file_id_in bigint,p_load_id_in bigint) RETURNS INT AS $$
DECLARE
	l_count bigint;
BEGIN
	CREATE TEMPORARY TABLE tmp_fk_values_pc (uniq_id bigint, document_code_id smallint, document_agency_id smallint,
						parent_document_code_id smallint, parent_document_agency_id smallint,submitting_agency_id smallint,
						awarding_agency_id smallint,submitting_agency_name varchar,submitting_agency_short_name varchar,
						awarding_agency_name varchar,awarding_agency_short_name varchar,start_date_id int,
						end_date_id int,revised_start_date_id int,revised_end_date_id int,	
						cif_received_date_id int, cif_fiscal_year smallint, cif_fiscal_year_id smallint, 
						document_agency_name varchar, document_agency_short_name varchar, award_category_id smallint )
	;
	
	INSERT INTO tmp_fk_values_pc (uniq_id,document_code_id)
	SELECT	a.uniq_id, b.document_code_id
	FROM etl.stg_pending_contracts a JOIN ref_document_code b ON a.con_trans_code = b.document_code;
	
	
	-- FK document_agency_id
	
	RAISE NOTICE '1';
	
	INSERT INTO tmp_fk_values_pc (uniq_id,document_agency_id,document_agency_name,document_agency_short_name)
	SELECT	a.uniq_id, max(b.agency_id),b.agency_name,b.agency_short_name
	FROM etl.stg_pending_contracts a JOIN ref_agency b ON a.con_trans_ad_code = b.agency_code
	GROUP BY 1,3,4;	
	
	
	CREATE TEMPORARY TABLE tmp_fk_values_pc_new_agencies(dept_cd varchar,uniq_id bigint)
	;
	
	INSERT INTO tmp_fk_values_pc_new_agencies
	SELECT con_trans_ad_code,MIN(b.uniq_id) as uniq_id
	FROM etl.stg_pending_contracts a join (SELECT uniq_id
						 FROM tmp_fk_values_pc
						 GROUP BY 1
						 HAVING max(document_agency_id) is null) b on a.uniq_id=b.uniq_id
	GROUP BY 1;
	
	

	RAISE NOTICE '2';
	
	TRUNCATE etl.ref_agency_id_seq;
	
	INSERT INTO etl.ref_agency_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_fk_values_pc_new_agencies;
	
	INSERT INTO ref_agency(agency_id,agency_code,agency_name,created_date,created_load_id,original_agency_name)
	SELECT a.agency_id,b.dept_cd,'<Unknown Agency>' as agency_name,now()::timestamp,p_load_id_in,'<Unknown Agency>' as original_agency_name
	FROM   etl.ref_agency_id_seq a JOIN tmp_fk_values_pc_new_agencies b ON a.uniq_id = b.uniq_id;

	GET DIAGNOSTICS l_count = ROW_COUNT;	

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'PC',l_count, 'Number of records inserted into ref_agency from Pending Contracts');
	END IF;
	
	RAISE NOTICE '3';

	-- Generate the agency history id for history records
	
	TRUNCATE etl.ref_agency_history_id_seq;
	
	INSERT INTO etl.ref_agency_history_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_fk_values_pc_new_agencies;

	INSERT INTO ref_agency_history(agency_history_id,agency_id,agency_name,created_date,load_id)
	SELECT a.agency_history_id,b.agency_id,'<Unknown Agency>' as agency_name,now()::timestamp,p_load_id_in
	FROM   etl.ref_agency_history_id_seq a JOIN etl.ref_agency_id_seq b ON a.uniq_id = b.uniq_id;

	GET DIAGNOSTICS l_count = ROW_COUNT;	

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'PC',l_count, 'Number of records inserted into ref_agency_history from Pending Contracts');
	END IF;
	
	INSERT INTO tmp_fk_values_pc (uniq_id,document_agency_id,document_agency_name,document_agency_short_name)
	SELECT	a.uniq_id, max(b.agency_id),b.agency_name,b.agency_short_name
	FROM etl.stg_pending_contracts a JOIN ref_agency b ON a.con_trans_ad_code = b.agency_code
	JOIN etl.ref_agency_id_seq c ON b.agency_id = c.agency_id
	GROUP BY 1,3,4;	
	
	RAISE NOTICE '4';
	-- FK parent_document_code_id
			
	INSERT INTO tmp_fk_values_pc (uniq_id,parent_document_code_id)
	SELECT	a.uniq_id, b.document_code_id
	FROM etl.stg_pending_contracts a JOIN ref_document_code b ON a.con_par_trans_code = b.document_code;	
	
	--FK  parent_document_agency_id
	
	INSERT INTO tmp_fk_values_pc (uniq_id,parent_document_agency_id)
	SELECT	a.uniq_id, max(b.agency_id)
	FROM etl.stg_pending_contracts a JOIN ref_agency b ON a.con_par_ad_code = b.agency_code
	GROUP BY 1;
	
	-- FK submitting_agency_id
	
	
	INSERT INTO tmp_fk_values_pc (uniq_id,submitting_agency_id,submitting_agency_name,submitting_agency_short_name)
	SELECT	a.uniq_id, max(b.agency_id),b.agency_name,b.agency_short_name
	FROM etl.stg_pending_contracts a JOIN ref_agency b ON a.submitting_agency_code = b.agency_code
	GROUP BY 1,3,4;
	
	RAISE NOTICE '5';
	-- FK awarding_agency_id
	
	
	INSERT INTO tmp_fk_values_pc (uniq_id,awarding_agency_id,awarding_agency_name,awarding_agency_short_name)
	SELECT	a.uniq_id, max(b.agency_id),b.agency_name,b.agency_short_name
	FROM etl.stg_pending_contracts a JOIN ref_agency b ON a.awarding_agency_code = b.agency_code
	GROUP BY 1,3,4;
	
	
	TRUNCATE TABLE tmp_fk_values_pc_new_agencies;
	
	
	INSERT INTO tmp_fk_values_pc_new_agencies
	SELECT awarding_agency_code,MIN(b.uniq_id) as uniq_id
	FROM etl.stg_pending_contracts a join (SELECT uniq_id
						 FROM tmp_fk_values_pc
						 GROUP BY 1
						 HAVING max(document_agency_id) is null) b on a.uniq_id=b.uniq_id
	GROUP BY 1;
	
	


	TRUNCATE etl.ref_agency_id_seq;
	
	INSERT INTO etl.ref_agency_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_fk_values_pc_new_agencies;
	
	INSERT INTO ref_agency(agency_id,agency_code,agency_name,created_date,created_load_id,original_agency_name)
	SELECT a.agency_id,b.dept_cd,'<Unknown Agency>' as agency_name,now()::timestamp,p_load_id_in,'<Unknown Agency>' as original_agency_name
	FROM   etl.ref_agency_id_seq a JOIN tmp_fk_values_pc_new_agencies b ON a.uniq_id = b.uniq_id;

	GET DIAGNOSTICS l_count = ROW_COUNT;	

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'PC',l_count, 'Number of records inserted into ref_agency from Pending Contracts');
	END IF;
	
	RAISE NOTICE '6';

	-- Generate the agency history id for history records
	
	TRUNCATE etl.ref_agency_history_id_seq;
	
	INSERT INTO etl.ref_agency_history_id_seq(uniq_id)
	SELECT uniq_id
	FROM   tmp_fk_values_pc_new_agencies;

	INSERT INTO ref_agency_history(agency_history_id,agency_id,agency_name,created_date,load_id)
	SELECT a.agency_history_id,b.agency_id,'<Unknown Agency>' as agency_name,now()::timestamp,p_load_id_in
	FROM   etl.ref_agency_history_id_seq a JOIN etl.ref_agency_id_seq b ON a.uniq_id = b.uniq_id;

	GET DIAGNOSTICS l_count = ROW_COUNT;	

	IF l_count > 0 THEN
		INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
		VALUES(p_load_file_id_in,'PC',l_count, 'Number of records inserted into ref_agency_history from Pending Contracts');
	END IF;
	
	
	INSERT INTO tmp_fk_values_pc (uniq_id,awarding_agency_id,awarding_agency_name,awarding_agency_short_name)
	SELECT	a.uniq_id, max(b.agency_id),b.agency_name,b.agency_short_name
	FROM etl.stg_pending_contracts a JOIN ref_agency b ON a.awarding_agency_code = b.agency_code
	JOIN etl.ref_agency_id_seq c ON b.agency_id = c.agency_id
	GROUP BY 1,3,4;
	
	RAISE NOTICE '7';
	-- FK start_date_id
	
	
	INSERT INTO tmp_fk_values_pc(uniq_id,start_date_id)
	SELECT a.uniq_id,b.date_id
	FROM etl.stg_pending_contracts a JOIN ref_date b ON a.con_term_from = b.date;
	
	INSERT INTO tmp_fk_values_pc(uniq_id,end_date_id)
	SELECT a.uniq_id,b.date_id
	FROM etl.stg_pending_contracts a JOIN ref_date b ON a.con_term_to = b.date;
	
	INSERT INTO tmp_fk_values_pc(uniq_id,revised_start_date_id)
	SELECT a.uniq_id,b.date_id
	FROM etl.stg_pending_contracts a JOIN ref_date b ON a.con_rev_start_dt = b.date;
	
	INSERT INTO tmp_fk_values_pc(uniq_id,revised_end_date_id)
	SELECT a.uniq_id,b.date_id
	FROM etl.stg_pending_contracts a JOIN ref_date b ON a.con_rev_end_dt = b.date;
	
	-- FK cif_fiscal_year
	
	INSERT INTO tmp_fk_values_pc(uniq_id,cif_received_date_id, cif_fiscal_year, cif_fiscal_year_id)
	SELECT a.uniq_id,b.date_id, c.year_value, c.year_id
	FROM etl.stg_pending_contracts a 
	JOIN ref_date b ON a.con_cif_received_date = b.date
	JOIN ref_year c ON b.nyc_year_id = c.year_id;	
	
	-- FK award_category_id
	
	INSERT INTO tmp_fk_values_pc(uniq_id,award_category_id)
	SELECT a.uniq_id , b.award_category_id
	FROM	etl.stg_pending_contracts a JOIN ref_award_category b ON a.award_category_code = b.award_category_code;
	
	RAISE NOTICE '8';
	--Updating con_ct_header with all the FK values

	UPDATE etl.stg_pending_contracts a
	SET	document_code_id = ct_table.document_code_id ,
		document_agency_id = ct_table.document_agency_id,
		document_agency_name = ct_table.document_agency_name,
		document_agency_short_name = ct_table.document_agency_short_name,
		parent_document_code_id = ct_table.parent_document_code_id,
		parent_document_agency_id = ct_table.parent_document_agency_id,
		submitting_agency_id = ct_table.submitting_agency_id,
		submitting_agency_name = ct_table.submitting_agency_name, 
		submitting_agency_short_name = ct_table.submitting_agency_short_name,		
		awarding_agency_id = ct_table.awarding_agency_id,
		awarding_agency_name = ct_table.awarding_agency_name, 		
		awarding_agency_short_name = ct_table.awarding_agency_short_name,
		start_date_id = ct_table.start_date_id,
		end_date_id = ct_table.end_date_id,
		revised_start_date_id = ct_table.revised_start_date_id,
		revised_end_date_id = ct_table.revised_end_date_id,
		cif_received_date_id = ct_table.cif_received_date_id,
		cif_fiscal_year = ct_table.cif_fiscal_year,
		cif_fiscal_year_id = ct_table.cif_fiscal_year_id,
		award_category_id = ct_table.award_category_id
	FROM	(SELECT uniq_id, max(document_code_id) as document_code_id ,
				 max(document_agency_id) as document_agency_id,
				 max(document_agency_name) as document_agency_name,
				 max(document_agency_short_name) as document_agency_short_name,
				 max(parent_document_code_id) as parent_document_code_id,
				 max(parent_document_agency_id) as parent_document_agency_id,
				 max(submitting_agency_id) as submitting_agency_id,
				 max(submitting_agency_name) as submitting_agency_name, 
				 max(submitting_agency_short_name) as submitting_agency_short_name, 
				 max(awarding_agency_id) as awarding_agency_id,
				 max(awarding_agency_name) as awarding_agency_name, 
				 max(awarding_agency_short_name) as awarding_agency_short_name,
				 max(start_date_id) as start_date_id, 
				 max(end_date_id) as end_date_id,
				 max(revised_start_date_id) as revised_start_date_id, 
				 max(revised_end_date_id) as revised_end_date_id,
				 max(cif_received_date_id) as cif_received_date_id,
				 max(cif_fiscal_year) as cif_fiscal_year,
				 max(cif_fiscal_year_id) as cif_fiscal_year_id,
				 max(award_category_id) as award_category_id
		 FROM	tmp_fk_values_pc
		 GROUP BY 1) ct_table
	WHERE	a.uniq_id = ct_table.uniq_id;	

	RAISE NOTICE '9';
	
	
	
	UPDATE etl.stg_pending_contracts
	SET fms_contract_number = con_trans_code||con_trans_ad_code||con_no ;
	
	CREATE TEMPORARY TABLE tmp_pc_ctr_mar_document_code(con_no varchar, con_trans_ad_code varchar, document_code varchar, document_code_id smallint) ;
	
	INSERT INTO tmp_pc_ctr_mar_document_code
	SELECT distinct con_no, con_trans_ad_code, e.document_code, e.document_code_id
	FROM etl.stg_pending_contracts a, history_agreement b, ref_agency_history c, ref_agency d, ref_document_code e, ref_document_code f
	WHERE a.con_no = b.document_id AND b.agency_history_id = c.agency_history_id AND c.agency_id = d.agency_id 
	AND a.con_trans_ad_code = d.agency_code AND b.document_code_id = e.document_code_id AND a.document_code_id = f.document_code_id
	AND e.document_code in ('CT1','CTA1') AND f.document_code = 'CTR' AND b.original_version_flag = 'Y' AND b.source_updated_date_id IS NOT NULL;
	
	
	UPDATE etl.stg_pending_contracts a
	SET fms_contract_number = b.document_code||b.con_trans_ad_code||b.con_no,
	document_code_id = b.document_code_id
	FROM tmp_pc_ctr_mar_document_code b 
	WHERE a.con_no = b.con_no AND a.con_trans_ad_code = b.con_trans_ad_code
	AND a.original_or_modified = 'M' AND a.con_trans_code = 'CTR' ;
	
	TRUNCATE tmp_pc_ctr_mar_document_code;
	
	INSERT INTO tmp_pc_ctr_mar_document_code
	SELECT distinct con_no, con_trans_ad_code, e.document_code, e.document_code_id
	FROM etl.stg_pending_contracts a, history_master_agreement b, ref_agency_history c, ref_agency d, ref_document_code e, ref_document_code f
	WHERE a.con_no = b.document_id AND b.agency_history_id = c.agency_history_id AND c.agency_id = d.agency_id 
	AND a.con_trans_ad_code = d.agency_code AND b.document_code_id = e.document_code_id AND a.document_code_id = f.document_code_id
	AND e.document_code in ('MA1','MMA1','RCT1') AND f.document_code = 'MAR' AND b.original_version_flag = 'Y' ;
	
	
	UPDATE etl.stg_pending_contracts a
	SET fms_contract_number = b.document_code||b.con_trans_ad_code||b.con_no,
	document_code_id = b.document_code_id
	FROM tmp_pc_ctr_mar_document_code b 
	WHERE a.con_no = b.con_no AND a.con_trans_ad_code = b.con_trans_ad_code
	AND a.original_or_modified = 'M' AND a.con_trans_code = 'MAR' ;
	
		
	UPDATE etl.stg_pending_contracts a
	SET original_agreement_id = b.original_agreement_id
	FROM history_agreement b
	WHERE a.fms_contract_number = b.contract_number
	AND b.original_version_flag = 'Y';
	
	UPDATE etl.stg_pending_contracts a
	SET original_agreement_id = b.original_master_agreement_id
	FROM history_master_agreement b
	WHERE a.fms_contract_number = b.contract_number
	AND b.original_version_flag = 'Y';
	
	RAISE NOTICE '10';
	
	CREATE TEMPORARY TABLE tmp_pc_funding_agency(original_agreement_id bigint, funding_agency_id smallint) ;
	
	INSERT INTO tmp_pc_funding_agency 
	SELECT distinct original_agreement_id, funding_agency_id
	FROM
	(SELECT x.original_agreement_id, first_value(y.agency_id) over (partition by x.original_agreement_id ORDER BY y.check_eft_issued_date DESC) as funding_agency_id
	FROM etl.stg_pending_contracts x, disbursement_line_item_details y
	WHERE x.original_agreement_id = y.agreement_id) z;
	
	
	UPDATE etl.stg_pending_contracts a
	SET funding_agency_id = b.funding_agency_id
	FROM tmp_pc_funding_agency b
	WHERE a.original_agreement_id = b.original_agreement_id ;
	
	UPDATE etl.stg_pending_contracts a
	SET funding_agency_code = b.agency_code,
		funding_agency_name = b.agency_name,
		funding_agency_short_name = b.agency_short_name
	FROM ref_agency b
	WHERE a.funding_agency_id = b.agency_id;
	
	RAISE NOTICE '11';
	TRUNCATE pending_contracts;
	
	INSERT INTO pending_contracts(document_code_id,document_agency_id,document_id,parent_document_code_id,
				      parent_document_agency_id,parent_document_id,encumbrance_amount_original,encumbrance_amount,
				      original_maximum_amount_original,original_maximum_amount,
				      revised_maximum_amount_original,revised_maximum_amount, vendor_legal_name,vendor_customer_code,description,
				      submitting_agency_id,oaisis_submitting_agency_desc,submitting_agency_code	,awarding_agency_id,
				      oaisis_awarding_agency_desc,awarding_agency_code,contract_type_name,cont_type_code,
				      award_method_name,award_method_code,award_method_id,start_date,end_date,revised_start_date,
				      revised_end_date,cif_received_date,cif_fiscal_year, cif_fiscal_year_id, tracking_number,board_award_number,
				      oca_number,version_number,fms_contract_number,contract_number,fms_parent_contract_number,
				      submitting_agency_name,submitting_agency_short_name,awarding_agency_name,awarding_agency_short_name,
				      start_date_id,end_date_id,revised_start_date_id,revised_end_date_id,
				      cif_received_date_id,document_agency_code,document_agency_name,document_agency_short_name,  
				      original_agreement_id, funding_agency_id, funding_agency_code, funding_agency_name, funding_agency_short_name,
				      dollar_difference, percent_difference,original_or_modified,original_or_modified_desc,award_size_id, award_category_id, industry_type_id, document_version, latest_flag )
	SELECT document_code_id,document_agency_id,con_no,parent_document_code_id,
	      parent_document_agency_id,con_par_reg_num,con_cur_encumbrance as encumbrance_amount_original,(CASE WHEN con_cur_encumbrance IS NULL THEN 0 ELSE con_cur_encumbrance END) as encumbrance_amount, 
	      con_original_max as original_maximum_amount_original, (CASE WHEN con_original_max IS NULL THEN 0 ELSE con_original_max END) as  original_maximum_amount, 
	      con_rev_max as revised_maximum_amount_original, (CASE WHEN con_rev_max IS NULL THEN con_original_max ELSE con_rev_max END) as revised_maximum_amount,vc_legal_name,con_vc_code,con_purpose,
	      submitting_agency_id,submitting_agency_desc,submitting_agency_code,awarding_agency_id,
	      awarding_agency_desc,awarding_agency_code,cont_desc,cont_code,
	      am_desc,am_code,b.award_method_id,con_term_from,con_term_to,con_rev_start_dt,
	      con_rev_end_dt,con_cif_received_date,cif_fiscal_year, cif_fiscal_year_id, con_pin,con_internal_pin,
	      con_batch_suffix,con_version,fms_contract_number,con_trans_code||con_trans_ad_code||con_no as contract_number, con_par_trans_code || con_par_ad_code || con_par_reg_num as fms_parent_contract_number,
	      submitting_agency_name,submitting_agency_short_name,awarding_agency_name,awarding_agency_short_name,
	      start_date_id,end_date_id,revised_start_date_id,revised_end_date_id,
	      cif_received_date_id,con_trans_ad_code,document_agency_name,document_agency_short_name,
	      original_agreement_id, funding_agency_id, funding_agency_code, funding_agency_name, funding_agency_short_name,
	      coalesce(con_rev_max,con_original_max) - coalesce(con_original_max,0) as dollar_difference,
		(CASE WHEN coalesce(con_original_max,0) = 0 THEN 0 ELSE 
		ROUND((( coalesce(con_rev_max,con_original_max) - coalesce(con_original_max,0)) * 100 )::decimal / coalesce(con_original_max,0),2) END) as percent_difference,
		original_or_modified, (CASE WHEN original_or_modified = 'M' THEN 'Modified' ELSE 'Original' END) as original_or_modified_desc,
		(CASE WHEN coalesce(con_rev_max,con_original_max) <= 5000 THEN 4 WHEN coalesce(con_rev_max,con_original_max)  > 5000 
		            AND coalesce(con_rev_max,con_original_max)  <= 100000 THEN 3 WHEN  coalesce(con_rev_max,con_original_max) > 100000 AND coalesce(con_rev_max,con_original_max) <= 1000000 THEN 2 
		            WHEN coalesce(con_rev_max,con_original_max) > 1000000 THEN 1 ELSE 5 END) as award_size_id, award_category_id, (CASE WHEN lpad(a.cont_code,2,'0') = '05' THEN 1 ELSE c.industry_type_id END) as industry_type_id,
            (CASE WHEN con_version = '' THEN 0 ELSE con_version::int END) as document_version, 'N' as latest_flag
	FROM  etl.stg_pending_contracts a 
	LEFT JOIN ref_award_method b ON (case when length(a.am_code)= 1 THEN lpad(a.am_code,2,'0') ELSE a.am_code END) = b.award_method_code
	LEFT JOIN ref_award_category_industry c ON c.award_category_code = a.award_category_code ;
	
	
	GET DIAGNOSTICS l_count = ROW_COUNT;	
	
		IF l_count > 0 THEN
			INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
			VALUES(p_load_file_id_in,'PC',l_count, '# of records inserted into pending_contracts');
		END IF;
	
	
	CREATE TEMPORARY TABLE tmp_pc_update_latest_flag AS
	SELECT contract_number, max(document_version) as document_version
	FROM pending_contracts GROUP BY 1;
	
	UPDATE pending_contracts a
	SET latest_flag = 'Y'
	FROM tmp_pc_update_latest_flag b
	WHERE a.contract_number = b.contract_number AND a.document_version = b.document_version;
	
	
	UPDATE pending_contracts SET registered_contract_max_amount = 0 ;
	
	CREATE TEMPORARY TABLE tmp_pc_child_reg_contract_amount AS
	SELECT b.original_agreement_id, maximum_contract_amount 
	FROM history_agreement a , pending_contracts b, ref_document_code c  
	WHERE a.original_agreement_id = b.original_agreement_id  and b.document_code_id = c.document_code_id and a.latest_flag = 'Y' and b.latest_flag = 'Y' and c.document_code in ('CT1','CTA1') ;
	
	UPDATE pending_contracts a
	SET registered_contract_max_amount = b.maximum_contract_amount
	FROM tmp_pc_child_reg_contract_amount b , ref_document_code c
	WHERE a.original_agreement_id = b.original_agreement_id  and a.document_code_id = c.document_code_id and c.document_code in ('CT1','CTA1');
	
	
	CREATE TEMPORARY TABLE tmp_pc_master_reg_contract_amount AS
	SELECT b.original_agreement_id, maximum_spending_limit 
	FROM history_master_agreement a , pending_contracts b, ref_document_code c  
	WHERE a.original_master_agreement_id = b.original_agreement_id  and b.document_code_id = c.document_code_id and a.latest_flag = 'Y' and b.latest_flag = 'Y' and c.document_code in ('MA1','MMA1','RCT1') ;
	
	UPDATE pending_contracts a
	SET registered_contract_max_amount = b.maximum_spending_limit
	FROM tmp_pc_master_reg_contract_amount b , ref_document_code c
	WHERE a.original_agreement_id = b.original_agreement_id  and a.document_code_id = c.document_code_id  and c.document_code in ('MA1','MMA1','RCT1');
	
	UPDATE pending_contracts a
	SET original_master_agreement_id = b.original_master_agreement_id
	FROM history_master_agreement b
	WHERE a.fms_parent_contract_number = b.contract_number AND b.original_version_flag = 'Y' ;
	
	UPDATE pending_contracts a 
	SET vendor_id = b.vendor_id
	FROM vendor b
	WHERE a.vendor_customer_code = b.vendor_customer_code
	AND b.miscellaneous_vendor_flag::BIT = 0::BIT ;
	
	
	TRUNCATE etl.vendor_id_seq_pending;
	
	INSERT INTO etl.vendor_id_seq_pending(vendor_customer_code)
	SELECT distinct vendor_customer_code 
	FROM pending_contracts 
	WHERE vendor_id IS NULL ;
	
	UPDATE pending_contracts a
	SET vendor_id = b.vendor_id
	FROM etl.vendor_id_seq_pending b
	WHERE a.vendor_customer_code = b.vendor_customer_code ;
	
	
	
	RETURN 1;
	
EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in processPendingContracts';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;	

	RETURN 0;	
END;
$$ language plpgsql;