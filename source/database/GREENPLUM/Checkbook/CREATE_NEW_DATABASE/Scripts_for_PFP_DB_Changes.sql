DROP TABLE IF EXISTS etl.stg_fms_vendor;

CREATE TABLE etl.stg_fms_vendor(
	doc_cd varchar(8),
	doc_dept_cd varchar(4),
	doc_id varchar(20),
	doc_vers_no integer,
	doc_vend_ln_no integer,
	vend_cust_cd varchar(25),
	lgl_nm varchar(60),
	alias_nm varchar(60),
	ad_id varchar(25),
	org_cls varchar(25),
	misc_acct_fl integer,
	ad_ln_1 varchar(75),
	ad_ln_2 varchar(75),
	ctry varchar(25),
	st varchar(25),
	zip varchar(25),
	city varchar(60),
	bustype_mnrt varchar(4),
	bustype_mnrt_status smallint,
	minority_type_id smallint,
	bustype_wmno varchar(4),
	bustype_wmno_status smallint,
	bustype_locb varchar(4),
	bustype_locb_status smallint,
	bustype_eent varchar(4),
	bustype_eent_status smallint,
	bustype_exmp varchar(4),
	bustype_exmp_status smallint,
	vendor_history_id integer,
	uniq_id bigint default nextval('etl.seq_stg_fms_vendor_uniq_id'),
	invalid_flag char(1),
	invalid_reason varchar	)
DISTRIBUTED BY (uniq_id)	;	

DROP TABLE IF EXISTS etl.archive_fms_vendor_pfp;

CREATE TABLE etl.archive_fms_vendor_pfp as select * from etl.archive_fms_vendor;

DROP TABLE IF EXISTS etl.archive_fms_vendor;

CREATE TABLE etl.archive_fms_vendor (LIKE etl.stg_fms_vendor) DISTRIBUTED BY (uniq_id);
ALTER TABLE etl.archive_fms_vendor ADD COLUMN load_file_id bigint;

DROP TABLE IF EXISTS etl.invalid_fms_vendor;
CREATE TABLE etl.invalid_fms_vendor (LIKE etl.archive_fms_vendor) DISTRIBUTED BY (uniq_id);


INSERT INTO etl.archive_fms_vendor(
            doc_cd, doc_dept_cd, doc_id, doc_vers_no, doc_vend_ln_no, vend_cust_cd, 
            lgl_nm, alias_nm, ad_id, org_cls, misc_acct_fl, ad_ln_1, ad_ln_2, 
            ctry, st, zip, city, bustype_mnrt, bustype_mnrt_status, minority_type_id, 
            bustype_wmno, bustype_wmno_status, bustype_locb, bustype_locb_status, 
            bustype_eent, bustype_eent_status, bustype_exmp, bustype_exmp_status, 
            vendor_history_id, uniq_id, invalid_flag, invalid_reason, load_file_id)
SELECT      doc_cd, doc_dept_cd, doc_id, doc_vers_no, doc_vend_ln_no, vend_cust_cd, 
            lgl_nm, alias_nm, ad_id, org_cls, misc_acct_fl, ad_ln_1, ad_ln_2, 
            ctry, st, zip, city, NULL as bustype_mnrt, NULL as bustype_mnrt_status,  NULL as minority_type_id, 
            NULL as bustype_wmno, NULL as bustype_wmno_status, NULL as bustype_locb, NULL as bustype_locb_status, 
            NULL as bustype_eent, NULL as bustype_eent_status, NULL as bustype_exmp, NULL as bustype_exmp_status, 
            vendor_history_id, uniq_id, invalid_flag, invalid_reason, load_file_id
FROM etl.archive_fms_vendor_pfp ;

DROP TABLE IF EXISTS disbursement_pfp;

CREATE TABLE disbursement_pfp as select * from disbursement;

DROP TABLE IF EXISTS disbursement CASCADE;

CREATE TABLE disbursement (
    disbursement_id integer  PRIMARY KEY DEFAULT nextval('seq_expenditure_expenditure_id'::regclass) NOT NULL,
    document_code_id smallint,
    agency_history_id smallint,
    document_id character varying(20),
    document_version integer,
    disbursement_number character varying(40),
    record_date_id int,
    budget_fiscal_year smallint,
    document_fiscal_year smallint,
    document_period character(2),
    check_eft_amount_original numeric(16,2),
    check_eft_amount numeric(16,2),
    check_eft_issued_date_id int,
    check_eft_record_date_id int,
    expenditure_status_id smallint,
    expenditure_cancel_type_id smallint,
    expenditure_cancel_reason_id integer,
    total_accounting_line_amount_original numeric(16,2),
    total_accounting_line_amount numeric(16,2),
    vendor_history_id integer,
    retainage_amount_original numeric(16,2),
    retainage_amount numeric(16,2),
    privacy_flag char(1),
    vendor_org_classification smallint,
    bustype_mnrt varchar(4),
	bustype_mnrt_status smallint,
	minority_type_id smallint,
	bustype_wmno varchar(4),
	bustype_wmno_status smallint,
	bustype_locb varchar(4),
	bustype_locb_status smallint,
	bustype_eent varchar(4),
	bustype_eent_status smallint,
	bustype_exmp varchar(4),
	bustype_exmp_status smallint,
    created_load_id integer,
    updated_load_id integer,
    created_date timestamp without time zone,
    updated_date timestamp without time zone
) distributed by (disbursement_id);


ALTER TABLE  disbursement ADD CONSTRAINT fk_disbursement_ref_agency_history FOREIGN KEY (agency_history_id) REFERENCES ref_agency_history(agency_history_id);
ALTER TABLE  disbursement ADD CONSTRAINT fk_disbursement_ref_document_code FOREIGN KEY (document_code_id) REFERENCES ref_document_code(document_code_id);
ALTER TABLE  disbursement ADD CONSTRAINT fk_disbursement_vendor_history FOREIGN KEY (vendor_history_id) REFERENCES vendor_history(vendor_history_id);
-- ALTER TABLE  disbursement ADD constraint fk_disbursement_etl_data_load foreign key (created_load_id) references etl_data_load (load_id);
ALTER TABLE  disbursement ADD constraint fk_disbursement_ref_date foreign key (record_date_id) references ref_date (date_id);
ALTER TABLE  disbursement ADD constraint fk_disbursement_ref_date_1 foreign key (check_eft_issued_date_id) references ref_date (date_id);
ALTER TABLE  disbursement ADD constraint fk_disbursement_ref_date_2 foreign key (check_eft_record_date_id) references ref_date (date_id);

 ALTER TABLE  disbursement_line_item ADD constraint fk_disbursement_line_item_expenditure foreign key (disbursement_id) references disbursement (disbursement_id);
 
 
 
 INSERT INTO disbursement(
            disbursement_id, document_code_id, agency_history_id, document_id, 
            document_version, disbursement_number, record_date_id, budget_fiscal_year, 
            document_fiscal_year, document_period, check_eft_amount_original, 
            check_eft_amount, check_eft_issued_date_id, check_eft_record_date_id, 
            expenditure_status_id, expenditure_cancel_type_id, expenditure_cancel_reason_id, 
            total_accounting_line_amount_original, total_accounting_line_amount, 
            vendor_history_id, retainage_amount_original, retainage_amount, 
            privacy_flag, vendor_org_classification, bustype_mnrt, bustype_mnrt_status, minority_type_id, 
            bustype_wmno, bustype_wmno_status, bustype_locb, bustype_locb_status, 
            bustype_eent, bustype_eent_status, bustype_exmp, bustype_exmp_status, 
            created_load_id, updated_load_id, created_date, updated_date)
SELECT 	    disbursement_id, document_code_id, agency_history_id, document_id, 
            document_version, disbursement_number, record_date_id, budget_fiscal_year, 
            document_fiscal_year, document_period, check_eft_amount_original, 
            check_eft_amount, check_eft_issued_date_id, check_eft_record_date_id, 
            expenditure_status_id, expenditure_cancel_type_id, expenditure_cancel_reason_id, 
            total_accounting_line_amount_original, total_accounting_line_amount, 
            vendor_history_id, retainage_amount_original, retainage_amount, 
            privacy_flag, NULL as vendor_org_classification, NULL as bustype_mnrt, NULL as bustype_mnrt_status, NULL as minority_type_id, 
            NULL as bustype_wmno, NULL as bustype_wmno_status, NULL as bustype_locb, NULL as bustype_locb_status, 
            NULL as bustype_eent, NULL as bustype_eent_status, NULL as bustype_exmp, NULL as bustype_exmp_status, 
            created_load_id, updated_load_id, created_date, updated_date
      FROM disbursement_pfp;
	  

UPDATE disbursement a 
SET vendor_org_classification = (CASE WHEN coalesce(b.org_cls,'') = ''  THEN NULL ELSE b.org_cls::smallint END)
FROM (SELECT distinct a.doc_cd, a.doc_dept_cd, a.doc_id, a.doc_vers_no,a.load_file_id, a.org_cls
FROM etl.archive_fms_vendor a JOIN (select doc_cd, doc_dept_cd, doc_id, doc_vers_no, max(load_file_id) as max_load_file_id from etl.archive_fms_vendor group by 1,2,3,4) b 
ON a.doc_id = b.doc_id AND a.doc_dept_cd = b.doc_dept_cd AND a.doc_id = b.doc_id AND a.doc_vers_no = b.doc_vers_no AND a.load_file_id = b.max_load_file_id) b
WHERE a.disbursement_number = b.doc_id||'-'||b.doc_vers_no||'-'||b.doc_dept_cd || '-' || b.doc_cd ;



select etl.grantaccess('webuser1','SELECT');
	  
-- Execute the below procedure in etl schema of master database

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
				 retainage_amount_original,retainage_amount,privacy_flag,vendor_org_classification,
				 bustype_mnrt, bustype_mnrt_status, minority_type_id, bustype_wmno, bustype_wmno_status,
				 bustype_locb, bustype_locb_status, bustype_eent, bustype_eent_status, bustype_exmp, bustype_exmp_status,
				 created_load_id,created_date)
	SELECT d.disbursement_id, a.document_code_id,a.agency_history_id,
	       a.doc_id,a.doc_vers_no,a.doc_id||'-'||a.doc_vers_no||'-'|| a.doc_dept_cd || '-' || a.doc_cd,
	       a.record_date_id,
	       a.doc_bfy,a.doc_fy_dc,a.doc_per_dc,
	       a.chk_eft_am,coalesce(a.chk_eft_am,0) as check_eft_amount,a.check_eft_issued_date_id,a.check_eft_record_date_id,
	       a.chk_eft_sta,a.can_typ_cd,a.can_reas_cd_dc,
	       a.ln_am,coalesce(a.ln_am,0) as total_accounting_line_amount, b.vendor_history_id, 
	       a.rtg_am,coalesce(a.rtg_am,0) as retainage_amount, l_display_type,(CASE WHEN coalesce(b.org_cls,'') = ''  THEN NULL ELSE b.org_cls::smallint END) as vendor_org_classification,
	       b.bustype_mnrt, b.bustype_mnrt_status, b.minority_type_id, b.bustype_wmno, b.bustype_wmno_status,
		   b.bustype_locb, b.bustype_locb_status, b.bustype_eent, b.bustype_eent_status, b.bustype_exmp, b.bustype_exmp_status,
	       p_load_id_in,now()::timestamp
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
	       a.ln_am,b.vendor_history_id, a.rtg_am, (CASE WHEN coalesce(b.org_cls,'') = ''  THEN NULL ELSE b.org_cls::smallint END) as org_cls,
	       b.bustype_mnrt, b.bustype_mnrt_status, b.minority_type_id, b.bustype_wmno, b.bustype_wmno_status,
		   b.bustype_locb, b.bustype_locb_status, b.bustype_eent, b.bustype_eent_status, b.bustype_exmp, b.bustype_exmp_status
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
		vendor_org_classification = b.org_cls,
		bustype_mnrt = b.bustype_mnrt,
		bustype_mnrt_status = b.bustype_mnrt_status,
		minority_type_id = b.minority_type_id,
		bustype_wmno = b.bustype_wmno,
		bustype_wmno_status = b.bustype_wmno_status,
		bustype_locb = b.bustype_locb,
		bustype_locb_status = b.bustype_locb_status,
		bustype_eent = b.bustype_eent,
		bustype_eent_status = b.bustype_eent_status,
		bustype_exmp = b.bustype_exmp,
		bustype_exmp_status = b.bustype_exmp_status,
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

	  


-- Execute the below script after copying the ref_column_mapping.csv in /home/gpadmin/TREDDY/CREATE_NEW_DATABASE/  folder

TRUNCATE TABLE etl.ref_column_mapping;

COPY etl.ref_column_mapping FROM '/home/gpadmin/TREDDY/CREATE_NEW_DATABASE/ref_column_mapping.csv' CSV HEADER QUOTE as '"';	  

-- Shard Changes


DROP TABLE IF EXISTS disbursement ;

CREATE TABLE disbursement (
    disbursement_id integer,
    document_code_id smallint,
    agency_history_id smallint,
    document_id character varying(20),
    document_version integer,
    disbursement_number character varying(40),
    record_date_id int,
    budget_fiscal_year smallint,
    document_fiscal_year smallint,
    document_period character(2),
    check_eft_amount_original numeric(16,2),
    check_eft_amount numeric(16,2),
    check_eft_issued_date_id int,
    check_eft_record_date_id int,
    expenditure_status_id smallint,
    expenditure_cancel_type_id smallint,
    expenditure_cancel_reason_id integer,
    total_accounting_line_amount_original numeric(16,2),
    total_accounting_line_amount numeric(16,2),
    vendor_history_id integer,
    retainage_amount_original numeric(16,2),
    retainage_amount numeric(16,2),
    privacy_flag char(1),
    vendor_org_classification smallint,
    bustype_mnrt varchar(4),
	bustype_mnrt_status smallint,
	minority_type_id smallint,
	bustype_wmno varchar(4),
	bustype_wmno_status smallint,
	bustype_locb varchar(4),
	bustype_locb_status smallint,
	bustype_eent varchar(4),
	bustype_eent_status smallint,
	bustype_exmp varchar(4),
	bustype_exmp_status smallint,
    created_load_id integer,
    updated_load_id integer,
    created_date timestamp without time zone,
    updated_date timestamp without time zone
)WITH(appendonly=true)
distributed by (disbursement_id);


SET search_path = staging ;

DROP VIEW IF EXISTS disbursement;

DROP EXTERNAL WEB TABLE IF EXISTS disbursement__0 ;

CREATE EXTERNAL WEB TABLE disbursement__0 (
    disbursement_id integer,
    document_code_id smallint,
    agency_history_id smallint,
    document_id character varying,
    document_version integer,
    disbursement_number character varying(40),
    record_date_id int,
    budget_fiscal_year smallint,
    document_fiscal_year smallint,
    document_period bpchar,
    check_eft_amount_original numeric,
    check_eft_amount numeric,
    check_eft_issued_date_id int,
    check_eft_record_date_id int,
    expenditure_status_id smallint,
    expenditure_cancel_type_id smallint,
    expenditure_cancel_reason_id integer,
    total_accounting_line_amount_original numeric,
    total_accounting_line_amount numeric,
    vendor_history_id integer,
    retainage_amount_original numeric,
    retainage_amount numeric,
    privacy_flag bpchar,
    vendor_org_classification smallint,
    bustype_mnrt character varying(4),
	bustype_mnrt_status smallint,
	minority_type_id smallint,
	bustype_wmno character varying(4),
	bustype_wmno_status smallint,
	bustype_locb character varying(4),
	bustype_locb_status smallint,
	bustype_eent character varying(4),
	bustype_eent_status smallint,
	bustype_exmp character varying(4),
	bustype_exmp_status smallint,
    created_load_id integer,
    updated_load_id integer,
    created_date timestamp without time zone,
    updated_date timestamp without time zone
) EXECUTE E' psql -h mdw1 -p 5432  checkbook -c "copy public.disbursement to stdout csv"' ON SEGMENT 0 
 FORMAT 'csv' (delimiter E',' null E'' escape E'"' quote E'"')
ENCODING 'UTF8';

-- need to change the hostname in the above create statement based on the environment we are running

CREATE VIEW disbursement AS
    SELECT disbursement__0.disbursement_id, disbursement__0.document_code_id, disbursement__0.agency_history_id, disbursement__0.document_id, 
    disbursement__0.document_version,disbursement__0.disbursement_number, disbursement__0.record_date_id, disbursement__0.budget_fiscal_year, disbursement__0.document_fiscal_year, 
    disbursement__0.document_period, disbursement__0.check_eft_amount_original, disbursement__0.check_eft_amount, disbursement__0.check_eft_issued_date_id, disbursement__0.check_eft_record_date_id, 
    disbursement__0.expenditure_status_id, disbursement__0.expenditure_cancel_type_id, disbursement__0.expenditure_cancel_reason_id, disbursement__0.total_accounting_line_amount_original, 
    disbursement__0.total_accounting_line_amount, disbursement__0.vendor_history_id, disbursement__0.retainage_amount_original, disbursement__0.retainage_amount, disbursement__0.privacy_flag, disbursement__0.vendor_org_classification,     
    disbursement__0.bustype_mnrt, disbursement__0.bustype_mnrt_status, disbursement__0.minority_type_id, disbursement__0.bustype_wmno, disbursement__0.bustype_wmno_status, 
    disbursement__0.bustype_locb, disbursement__0.bustype_locb_status, disbursement__0.bustype_eent, disbursement__0.bustype_eent_status, disbursement__0.bustype_exmp, disbursement__0.bustype_exmp_status,     
    disbursement__0.created_load_id, disbursement__0.updated_load_id, disbursement__0.created_date , disbursement__0.updated_date FROM ONLY disbursement__0;

	SET search_path = public ;
	SELECT grantaccess('webuser1','SELECT');