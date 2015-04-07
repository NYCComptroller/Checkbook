set search_path=public;

/*
DROP SEQUENCE IF EXISTS seq_subvendor_vendor_id CASCADE;
DROP SEQUENCE IF EXISTS seq_subvendor_bus_type_vendor_bus_type_id CASCADE;
DROP SEQUENCE IF EXISTS seq_subvendor_history_vendor_history_id CASCADE;
DROP SEQUENCE IF EXISTS seq_sub_agreement_agreement_id CASCADE;

CREATE SEQUENCE seq_subvendor_vendor_id;
CREATE SEQUENCE seq_subvendor_bus_type_vendor_bus_type_id;
CREATE SEQUENCE seq_subvendor_history_vendor_history_id;
CREATE SEQUENCE seq_sub_agreement_agreement_id;
*/

-- only one time


DROP TABLE IF EXISTS sub_industry_mappings ;
CREATE TABLE sub_industry_mappings(
industry_type_id smallint, 
sub_industry_type_id smallint
);


DROP  TABLE  IF EXISTS subcontract_vendor_business_type;	
CREATE TABLE subcontract_vendor_business_type (
	vendor_customer_code character varying(20),
	business_type_id smallint,
	status smallint,
    minority_type_id smallint,
    certification_start_date date,
    certification_end_date date, 
    initiation_date date,
    certification_no character varying(30),
    disp_certification_start_date date
);

DROP  TABLE  IF EXISTS subcontract_business_type;	
CREATE TABLE subcontract_business_type (
    contract_number varchar,
    subcontract_id character varying(20),
	prime_vendor_customer_code character varying(20),
	vendor_customer_code character varying(20),
	business_type_id smallint,
	status smallint,
    minority_type_id smallint,
    certification_start_date date,
    certification_end_date date, 
    initiation_date date,
    certification_no character varying(30),
    disp_certification_start_date date,
    load_id integer,
    created_date timestamp without time zone
);


DROP  TABLE  IF EXISTS subvendor;	
CREATE TABLE subvendor (
    vendor_id integer PRIMARY KEY DEFAULT nextval('seq_vendor_vendor_id'::regclass) NOT NULL,
    vendor_customer_code character varying(20),
    legal_name character varying(60),
    display_flag CHAR(1) DEFAULT 'Y',
    created_load_id integer,
    updated_load_id integer,
    created_date timestamp without time zone,
    updated_date timestamp without time zone
) ;


DROP  TABLE  IF EXISTS subvendor_history;	
CREATE TABLE subvendor_history (
    vendor_history_id integer PRIMARY KEY DEFAULT nextval('seq_vendor_history_vendor_history_id'::regclass) NOT NULL,
    vendor_id integer,   
    legal_name character varying(60),
    load_id integer,
    created_date timestamp without time zone,
    updated_date timestamp without time zone
) ;


DROP  TABLE  IF EXISTS subvendor_business_type;
CREATE TABLE subvendor_business_type (
    vendor_business_type_id bigint PRIMARY KEY DEFAULT nextval('seq_vendor_bus_type_vendor_bus_type_id') NOT NULL,
    vendor_history_id integer,
    business_type_id smallint,
    status smallint,
    minority_type_id smallint,
    load_id integer,
    created_date timestamp without time zone,
    updated_date timestamp without time zone
) ;


DROP  TABLE  IF EXISTS subvendor_min_bus_type ;
CREATE TABLE subvendor_min_bus_type
( vendor_id integer,
  vendor_history_id integer,
  business_type_id smallint,
  minority_type_id smallint,
  business_type_code character varying(4),
  business_type_name character varying(50),
  minority_type_name character varying(50)
);


DROP  TABLE  IF EXISTS subcontract_status;
CREATE TABLE subcontract_status (
    contract_number varchar,
	vendor_customer_code character varying(20), 
	scntrc_status smallint, 
	agreement_type_id smallint, 
	total_scntrc_max_am numeric(16,2),
	total_scntrc_pymt_am numeric(16,2),
    created_load_id integer,
    created_date timestamp without time zone,
    updated_load_id integer,
    updated_date timestamp without time zone
);


DROP  TABLE  IF EXISTS subcontract_details;
CREATE  TABLE subcontract_details
(
  agreement_id bigint NOT NULL DEFAULT nextval('seq_agreement_agreement_id'::regclass),
  contract_number varchar,
  sub_contract_id character varying(20),
  agency_history_id smallint,
  document_id character varying(20),
  document_code_id smallint,
  document_version integer,
  vendor_history_id integer,
  prime_vendor_id integer,
  agreement_type_id smallint,  
  aprv_sta smallint,
  aprv_reas_id character varying(3),
  aprv_reas_nm character varying(30),
  description character varying(256),
  is_mwbe_cert smallint,
  industry_type_id smallint,
  effective_begin_date_id integer,
  effective_end_date_id integer,
  registered_date_id integer,
  source_updated_date_id integer,
  maximum_contract_amount_original numeric(16,2),
  maximum_contract_amount  numeric(16,2),
  original_contract_amount_original numeric(16,2),
  original_contract_amount numeric(16,2),
  rfed_amount_original numeric(16,2), 
  rfed_amount numeric(16,2),
  total_scntrc_pymt numeric(16,2),
  is_scntrc_pymt_complete smallint,
  scntrc_mode smallint,
  tracking_number character varying(30),
  award_method_id smallint,
  award_category_id smallint,
  brd_awd_no character varying,
  number_responses integer,
  number_solicitation integer,
  doc_ref character varying(75),
  registered_fiscal_year smallint,
  registered_fiscal_year_id smallint,
  registered_calendar_year smallint,
  registered_calendar_year_id smallint,
  effective_end_fiscal_year smallint,
  effective_end_fiscal_year_id smallint,
  effective_end_calendar_year smallint,
  effective_end_calendar_year_id smallint,
  effective_begin_fiscal_year smallint,
  effective_begin_fiscal_year_id smallint,
  effective_begin_calendar_year smallint,
  effective_begin_calendar_year_id smallint,
  source_updated_fiscal_year smallint,
  source_updated_fiscal_year_id smallint,
  source_updated_calendar_year smallint,
  source_updated_calendar_year_id smallint,
  original_agreement_id bigint,
  original_version_flag character(1),
  master_agreement_id bigint,
  latest_flag character(1),
  privacy_flag character(1),
  created_load_id integer,
  updated_load_id integer,
  created_date timestamp without time zone,
  updated_date timestamp without time zone
);


DROP  TABLE  IF EXISTS subcontract_spending;
CREATE TABLE subcontract_spending (
    disbursement_line_item_id bigint  PRIMARY KEY DEFAULT nextval('seq_disbursement_line_item_id'::regclass) NOT NULL,
    document_code_id smallint,
    agency_history_id smallint,
    document_id character varying(20),
    document_version integer,
    payment_id character varying(10),
    disbursement_number character varying(40),
    check_eft_amount_original numeric(16,2),
    check_eft_amount numeric(16,2),
    check_eft_issued_date_id int,
    check_eft_issued_nyc_year_id smallint,
    payment_description character varying(256),
    payment_proof character varying(256),
    is_final_payment varchar(3),
    doc_ref character varying(75),
    contract_number varchar,
  	sub_contract_id character varying(20),
    agreement_id bigint,
    vendor_history_id integer,
    prime_vendor_id integer,     
    created_load_id integer,
    updated_load_id integer,
    created_date timestamp without time zone,
    updated_date timestamp without time zone
) ;


DROP  TABLE  IF EXISTS sub_agreement_snapshot;
CREATE TABLE sub_agreement_snapshot
 (
   original_agreement_id bigint,
   document_version smallint,
   document_code_id smallint,
   agency_history_id smallint,
   agency_id smallint,
   agency_code character varying(20),
   agency_name character varying(100),
   agreement_id bigint,
   starting_year smallint,
   starting_year_id smallint,
   ending_year smallint,
   ending_year_id smallint,
   registered_year smallint,
   registered_year_id smallint,
   contract_number character varying,
   sub_contract_id character varying(20),
   original_contract_amount numeric(16,2),
   maximum_contract_amount numeric(16,2),
   description character varying,
   vendor_history_id integer,
   vendor_id integer,
   vendor_code character varying(20),
   vendor_name character varying,
   prime_vendor_id integer,
   prime_minority_type_id smallint,
   prime_minority_type_name character varying(50),
   dollar_difference numeric(16,2),
   percent_difference numeric(17,4),
   agreement_type_id smallint,
   agreement_type_code character varying(2),
   agreement_type_name character varying,
   award_category_id smallint,
   award_category_code character varying(10),
   award_category_name character varying,
   award_method_id smallint,
   award_method_code character varying(10) ,
   award_method_name character varying,
   expenditure_object_codes character varying,
   expenditure_object_names character varying,
   industry_type_id smallint,
   industry_type_name character varying(50),
   award_size_id smallint,
   effective_begin_date date,
   effective_begin_date_id integer,
   effective_begin_year smallint,
   effective_begin_year_id smallint,
   effective_end_date date,
   effective_end_date_id integer,
   effective_end_year smallint,
   effective_end_year_id smallint,
   registered_date date,
   registered_date_id integer,
   brd_awd_no character varying,
   tracking_number character varying,
   rfed_amount numeric(16,2),
   minority_type_id smallint,
   minority_type_name character varying(50),
   original_version_flag character(1),
   master_agreement_id bigint,  
   master_contract_number character varying,
   latest_flag character(1),
   load_id integer,
   last_modified_date timestamp without time zone,
   job_id bigint
 ) ;
 
 
 DROP  TABLE  IF EXISTS sub_agreement_snapshot_cy;
 CREATE TABLE sub_agreement_snapshot_cy (LIKE sub_agreement_snapshot) ;
 
 
 DROP  TABLE  IF EXISTS sub_agreement_snapshot_expanded;
 CREATE TABLE sub_agreement_snapshot_expanded(
	original_agreement_id bigint,
	agreement_id bigint,
	fiscal_year smallint,
	description varchar,
	contract_number varchar,
	sub_contract_id character varying(20),
	vendor_id int,
	prime_vendor_id int,
	prime_minority_type_id smallint,
	agency_id smallint,
	industry_type_id smallint,
    award_size_id smallint,
	original_contract_amount numeric(16,2) ,
	maximum_contract_amount numeric(16,2),
	rfed_amount numeric(16,2),
	starting_year smallint,	
	ending_year smallint,
	dollar_difference numeric(16,2), 
	percent_difference numeric(17,4),
	award_method_id smallint,
	document_code_id smallint,	
	minority_type_id smallint,
 	minority_type_name character varying(50),
	status_flag char(1)
	);	


DROP  TABLE  IF EXISTS sub_agreement_snapshot_expanded_cy;
CREATE TABLE sub_agreement_snapshot_expanded_cy(
	original_agreement_id bigint,
	agreement_id bigint,
	fiscal_year smallint,
	description varchar,
	contract_number varchar,
	sub_contract_id character varying(20),
	vendor_id int,
	prime_vendor_id int,
	prime_minority_type_id smallint,
	agency_id smallint,
	industry_type_id smallint,
    award_size_id smallint,
	original_contract_amount numeric(16,2) ,
	maximum_contract_amount numeric(16,2),
	rfed_amount numeric(16,2),
	starting_year smallint,	
	ending_year smallint,
	dollar_difference numeric(16,2), 
	percent_difference numeric(17,4),
	award_method_id smallint,
	document_code_id smallint,	
	minority_type_id smallint,
 	minority_type_name character varying(50),
	status_flag char(1)
	);	


DROP  TABLE  IF EXISTS sub_agreement_snapshot_deleted;
CREATE TABLE sub_agreement_snapshot_deleted (
  agreement_id bigint NOT NULL,
  original_agreement_id bigint NOT NULL,
  starting_year smallint,
  load_id integer,
  deleted_date timestamp without time zone,
  job_id bigint
) ;


DROP  TABLE  IF EXISTS sub_agreement_snapshot_cy_deleted;
CREATE TABLE sub_agreement_snapshot_cy_deleted (
  agreement_id bigint NOT NULL,
  original_agreement_id bigint NOT NULL,
  starting_year smallint,
  load_id integer,
  deleted_date timestamp without time zone,
  job_id bigint
) ;


DROP  TABLE  IF EXISTS subcontract_spending_details;
CREATE TABLE subcontract_spending_details(
	disbursement_line_item_id bigint,
	disbursement_number character varying(40),
	payment_id character varying(10),
	check_eft_issued_date_id int,
	check_eft_issued_nyc_year_id smallint,
	fiscal_year smallint,
	check_eft_issued_cal_month_id int,
	agreement_id bigint,
	check_amount numeric(16,2),
	agency_id smallint,
	agency_history_id smallint,
	agency_code varchar(20),
	vendor_id integer,
	prime_vendor_id integer,
	prime_minority_type_id smallint,
    prime_minority_type_name character varying(50),
	maximum_contract_amount numeric(16,2),
	maximum_contract_amount_cy numeric(16,2),	
	document_id varchar(20),
	vendor_name varchar,
	vendor_customer_code varchar(20), 
	check_eft_issued_date date,
	agency_name varchar(100),	
	agency_short_name character varying(15),  	
	expenditure_object_name varchar(40),
	expenditure_object_code varchar(4),
	contract_number varchar,
	sub_contract_id character varying(20),
	contract_vendor_id integer,
  	contract_vendor_id_cy integer,
	contract_prime_vendor_id integer,
  	contract_prime_vendor_id_cy integer,
  	contract_agency_id smallint,
  	contract_agency_id_cy smallint,
  	purpose varchar,
	purpose_cy varchar,
	reporting_code varchar(15),
	spending_category_id smallint,
	spending_category_name varchar,
	calendar_fiscal_year_id smallint,
	calendar_fiscal_year smallint,
	reference_document_number character varying,
	reference_document_code varchar(8),
	contract_document_code varchar(8),
	minority_type_id smallint,
 	minority_type_name character varying(50),
 	industry_type_id smallint,
   	industry_type_name character varying(50),
   	agreement_type_code character varying(2),
   	award_method_code character varying(10),
   	contract_industry_type_id smallint,
	contract_industry_type_id_cy smallint,
	contract_minority_type_id smallint,
	contract_minority_type_id_cy smallint,
	contract_prime_minority_type_id smallint,
    contract_prime_minority_type_id_cy smallint,
	master_agreement_id bigint,  
    master_contract_number character varying,
	file_type char(1),
	load_id integer,
	last_modified_date timestamp without time zone,
	job_id bigint
);

DROP TABLE IF EXISTS subcontract_spending_deleted;
CREATE TABLE subcontract_spending_deleted (
  disbursement_line_item_id bigint NOT NULL,
  agency_id smallint,
  load_id integer,
  deleted_date timestamp without time zone,
  job_id bigint
) ;



DROP TABLE IF EXISTS all_agreement_transactions;
CREATE TABLE all_agreement_transactions
 (
   original_agreement_id bigint,
   document_version smallint,
   document_code_id smallint,
   agency_history_id smallint,
   agency_id smallint,
   agency_code character varying(20),
   agency_name character varying(100),
   agreement_id bigint,
   starting_year smallint,
   starting_year_id smallint,
   ending_year smallint,
   ending_year_id smallint,
   registered_year smallint,
   registered_year_id smallint,
   contract_number character varying,
   sub_contract_id character varying(20),
   original_contract_amount numeric(16,2),
   maximum_contract_amount numeric(16,2),
   description character varying,
   vendor_history_id integer,
   vendor_id integer,
   vendor_code character varying(20),
   vendor_name character varying,
   prime_vendor_id integer,
   prime_vendor_name character varying,
   prime_minority_type_id smallint,
   prime_minority_type_name character varying(50),
   dollar_difference numeric(16,2),
   percent_difference numeric(17,4),
   master_agreement_id bigint,
   master_contract_number character varying,
   agreement_type_id smallint,
   agreement_type_code character varying(2),
   agreement_type_name character varying,
   award_category_id smallint,
   award_category_code character varying(10),
   award_category_name character varying,
   award_method_id smallint,
   award_method_code character varying(10) ,
   award_method_name character varying,
   expenditure_object_codes character varying,
   expenditure_object_names character varying,
   industry_type_id smallint,
   industry_type_name character varying(50),
   award_size_id smallint,
   effective_begin_date date,
   effective_begin_date_id integer,
   effective_begin_year smallint,
   effective_begin_year_id smallint,
   effective_end_date date,
   effective_end_date_id integer,
   effective_end_year smallint,
   effective_end_year_id smallint,
   registered_date date,
   registered_date_id integer,
   brd_awd_no character varying,
   tracking_number character varying,
   rfed_amount numeric(16,2),
    minority_type_id smallint,
 	minority_type_name character varying(50),
   master_agreement_yn character(1),  
   has_children character(1),
   has_mwbe_children character(1),
   original_version_flag character(1),
   latest_flag character(1),
   load_id integer,
   last_modified_date timestamp without time zone,
   last_modified_year_id smallint,
   is_prime_or_sub character(1),
   is_minority_vendor character(1), 
   vendor_type character(2),
   contract_original_agreement_id bigint,
   job_id bigint
 ) ;
 
 
 
 DROP TABLE IF EXISTS all_agreement_transactions_cy;
 CREATE TABLE all_agreement_transactions_cy (LIKE all_agreement_transactions) ;
 
 
 
 DROP TABLE IF EXISTS all_disbursement_transactions;
 CREATE TABLE all_disbursement_transactions(
	disbursement_line_item_id bigint,
	disbursement_id integer,
	line_number integer,
	disbursement_number character varying(40),
	payment_id character varying(10),
	check_eft_issued_date_id int,
	check_eft_issued_nyc_year_id smallint,
	fiscal_year smallint,
	check_eft_issued_cal_month_id int,
	agreement_id bigint,
	master_agreement_id bigint,
	fund_class_id smallint,
	check_amount numeric(16,2),
	agency_id smallint,
	agency_history_id smallint,
	agency_code varchar(20),
	expenditure_object_id integer,
	vendor_id integer,
	prime_vendor_id integer,
	prime_vendor_name character varying,
	prime_minority_type_id smallint,
    prime_minority_type_name character varying(50),
	department_id integer,
	maximum_contract_amount numeric(16,2),
	maximum_contract_amount_cy numeric(16,2),
	maximum_spending_limit numeric(16,2),
	maximum_spending_limit_cy numeric(16,2),
	document_id varchar(20),
	vendor_name varchar,
	vendor_customer_code varchar(20), 
	check_eft_issued_date date,
	agency_name varchar(100),	
	agency_short_name character varying(15),  	
	location_name varchar,
	location_code varchar(4),
	department_name varchar(100),
	department_short_name character varying(15),
	department_code varchar(20),
	expenditure_object_name varchar(40),
	expenditure_object_code varchar(4),
	budget_code_id integer,
	budget_code varchar(10),
	budget_name varchar(60),
	contract_number varchar,
	sub_contract_id character varying(20),
	master_contract_number character varying,
	master_child_contract_number character varying,
  	contract_vendor_id integer,
  	contract_vendor_id_cy integer,
	contract_prime_vendor_id integer,
  	contract_prime_vendor_id_cy integer,
  	master_contract_vendor_id integer,
  	master_contract_vendor_id_cy integer,
  	contract_agency_id smallint,
  	contract_agency_id_cy smallint,
  	master_contract_agency_id smallint,
  	master_contract_agency_id_cy smallint,
  	master_purpose character varying,
  	master_purpose_cy character varying,
	purpose varchar,
	purpose_cy varchar,
	master_child_contract_agency_id smallint,
	master_child_contract_agency_id_cy smallint,
	master_child_contract_vendor_id integer,
	master_child_contract_vendor_id_cy integer,
	reporting_code varchar(15),
	location_id integer,
	fund_class_name varchar(50),
	fund_class_code varchar(5),
	spending_category_id smallint,
	spending_category_name varchar,
	calendar_fiscal_year_id smallint,
	calendar_fiscal_year smallint,
	agreement_accounting_line_number integer,
	agreement_commodity_line_number integer,
	agreement_vendor_line_number integer, 
	reference_document_number character varying,
	reference_document_code varchar(8),
	contract_document_code varchar(8),
	master_contract_document_code varchar(8),
	minority_type_id smallint,
 	minority_type_name character varying(50),
 	industry_type_id smallint,
   	industry_type_name character varying(50),
   	agreement_type_code character varying(2),
   	award_method_code character varying(10),
   	contract_industry_type_id smallint,
	contract_industry_type_id_cy smallint,
	master_contract_industry_type_id smallint,
	master_contract_industry_type_id_cy smallint,
	contract_minority_type_id smallint,
	contract_minority_type_id_cy smallint,
	master_contract_minority_type_id smallint,
	master_contract_minority_type_id_cy smallint,
	file_type char(1),
	load_id integer,
	last_modified_date timestamp without time zone,
	last_modified_fiscal_year_id smallint,
	last_modified_calendar_year_id smallint,
	is_prime_or_sub character(1),
	is_minority_vendor character(1), 
    vendor_type character(2),
    contract_original_agreement_id bigint,
	job_id bigint
);



    
-- aggregate tables

DROP TABLE IF EXISTS aggregateon_subven_spending_coa_entities;
CREATE TABLE aggregateon_subven_spending_coa_entities (
	agency_id smallint,
	spending_category_id smallint,
	vendor_id integer,
	prime_vendor_id integer,
	prime_minority_type_id smallint,
	minority_type_id smallint,
	industry_type_id smallint,
	month_id int,
	year_id smallint,
	type_of_year char(1),
	total_spending_amount numeric(16,2),
	total_disbursements integer
	) ;
	

DROP TABLE IF EXISTS aggregateon_subven_spending_contract;
CREATE TABLE aggregateon_subven_spending_contract (
    agreement_id bigint,
    document_id character varying(20),
    sub_contract_id character varying(20),
    document_code character varying(8),
	vendor_id integer,
	prime_vendor_id integer,
	prime_minority_type_id smallint,
	minority_type_id smallint,
	industry_type_id smallint,
	agency_id smallint,
	description character varying(256),	
	spending_category_id smallint,
	year_id smallint,
	type_of_year char(1),
	total_spending_amount numeric(16,2), 
	total_contract_amount numeric(16,2)
	) ;
	
	
DROP TABLE IF EXISTS aggregateon_subven_spending_vendor;
CREATE TABLE aggregateon_subven_spending_vendor (
	vendor_id integer,
	prime_vendor_id integer,
	prime_minority_type_id smallint,
	minority_type_id smallint,
	industry_type_id smallint,
	agency_id smallint,
	spending_category_id smallint,
	year_id smallint,
	type_of_year char(1),
	total_spending_amount numeric(16,2), 
	total_contract_amount numeric(16,2),
	total_sub_contracts integer,
	is_all_categories char(1)
	) ;
	
	
DROP TABLE IF EXISTS mid_aggregateon_subven_disbursement_spending_year;
CREATE TABLE mid_aggregateon_subven_disbursement_spending_year(
	original_agreement_id bigint,
	fiscal_year smallint,
	fiscal_year_id smallint,
	check_amount numeric(16,2),
	type_of_year char(1));


DROP TABLE IF EXISTS aggregateon_subven_contracts_cumulative_spending;
CREATE TABLE aggregateon_subven_contracts_cumulative_spending(
	original_agreement_id bigint,
	fiscal_year smallint,
	fiscal_year_id smallint,
	document_code_id smallint,
	description varchar,
	contract_number varchar,
	sub_contract_id character varying(20),
	vendor_id int,
	prime_vendor_id int,
	prime_minority_type_id smallint,
	minority_type_id smallint,
	award_method_id smallint,
	agency_id smallint,
	industry_type_id smallint,
	award_size_id smallint,
	original_contract_amount numeric(16,2),
	maximum_contract_amount numeric(16,2),
	spending_amount_disb numeric(16,2),
	spending_amount numeric(16,2),
	current_year_spending_amount numeric(16,2),
	dollar_difference numeric(16,2),
	percent_difference numeric(16,2),
	status_flag char(1),
	type_of_year char(1)	
) ;


DROP TABLE IF EXISTS aggregateon_subven_contracts_spending_by_month;
CREATE TABLE aggregateon_subven_contracts_spending_by_month(
 original_agreement_id bigint,
 fiscal_year smallint,
 fiscal_year_id smallint,
 document_code_id smallint,
 month_id integer,
 vendor_id int,
 prime_vendor_id int,
 prime_minority_type_id smallint,
 minority_type_id smallint,
 award_method_id smallint,
 agency_id smallint,
 industry_type_id smallint,
 award_size_id smallint,
 spending_amount numeric(16,2),
 status_flag char(1),
 type_of_year char(1) 
) ;


DROP TABLE IF EXISTS aggregateon_subven_total_contracts;
CREATE TABLE aggregateon_subven_total_contracts
(
fiscal_year smallint,
fiscal_year_id smallint,
vendor_id int,
 prime_vendor_id int,
 prime_minority_type_id smallint,
 minority_type_id smallint,
award_method_id smallint,
agency_id smallint,
industry_type_id smallint,
award_size_id smallint,
total_contracts bigint,
total_commited_contracts bigint,
total_master_agreements bigint,
total_standalone_contracts bigint,
total_revenue_contracts bigint,
total_revenue_contracts_amount numeric(16,2),
total_commited_contracts_amount numeric(16,2),
total_contracts_amount numeric(16,2),
total_spending_amount_disb numeric(16,2), 
total_spending_amount numeric(16,2), 
status_flag char(1),
type_of_year char(1)
) ;



DROP TABLE IF EXISTS contracts_subven_spending_transactions;
CREATE TABLE contracts_subven_spending_transactions
(
disbursement_line_item_id bigint,
original_agreement_id bigint,
fiscal_year smallint,
fiscal_year_id smallint,
document_code_id smallint,
vendor_id int,
prime_vendor_id int,
prime_minority_type_id smallint,
minority_type_id smallint,
award_method_id smallint,
document_agency_id smallint,
industry_type_id smallint,
award_size_id smallint,
disb_document_id  character varying(20),
disb_vendor_name  character varying,
disb_check_eft_issued_date  date,
disb_agency_name  character varying(100),
disb_check_amount  numeric(16,2),
disb_contract_number  character varying,
disb_sub_contract_id  character varying,
disb_purpose  character varying,
disb_reporting_code  character varying(15),
disb_spending_category_name  character varying,
disb_agency_id  smallint,
disb_vendor_id  integer,
disb_spending_category_id  smallint,
disb_agreement_id  bigint,
disb_contract_document_code  character varying(8),
disb_fiscal_year_id  smallint,
disb_check_eft_issued_cal_month_id integer,
disb_disbursement_number character varying(40),
disb_minority_type_id smallint,
disb_minority_type_name character varying(50),
disb_vendor_type character(2),
disb_master_contract_number  character varying,
status_flag char(1),
type_of_year char(1)
) ;


DROP TABLE IF EXISTS aggregateon_all_contracts_cumulative_spending;
CREATE TABLE aggregateon_all_contracts_cumulative_spending(
	original_agreement_id bigint,
	fiscal_year smallint,
	fiscal_year_id smallint,
	document_code_id smallint,
	master_agreement_yn character(1),
	description varchar,
	contract_number varchar,
	sub_contract_id character varying(20),
	vendor_id int,
	prime_vendor_id int,
	prime_minority_type_id smallint,
	minority_type_id smallint,
	award_method_id smallint,
	agency_id smallint,
	industry_type_id smallint,
	award_size_id smallint,
	original_contract_amount numeric(16,2),
	maximum_contract_amount numeric(16,2),
	spending_amount_disb numeric(16,2),
	spending_amount numeric(16,2),
	current_year_spending_amount numeric(16,2),
	dollar_difference numeric(16,2),
	percent_difference numeric(16,2),
	status_flag char(1),
	type_of_year char(1)	
)  ;


DROP TABLE IF EXISTS contracts_all_spending_transactions;
CREATE TABLE contracts_all_spending_transactions(
	disbursement_line_item_id bigint,
	original_agreement_id bigint,
	fiscal_year smallint,
	fiscal_year_id smallint,
	document_code_id smallint,
	vendor_id int,
	prime_vendor_id integer,
	prime_minority_type_id smallint,
	minority_type_id smallint,
	award_method_id smallint,
	document_agency_id smallint,
	industry_type_id smallint,
    award_size_id smallint,
	disb_document_id  character varying(20),
	disb_vendor_name  character varying,
	disb_check_eft_issued_date  date,
	disb_agency_name  character varying(100),
	disb_department_short_name  character varying(15),
	disb_check_amount  numeric(16,2),
	disb_expenditure_object_name  character varying(40),
	disb_budget_name  character varying(60),
	disb_contract_number  character varying,
	disb_sub_contract_id character varying,
	disb_purpose  character varying,
	disb_reporting_code  character varying(15),
	disb_spending_category_name  character varying,
	disb_agency_id  smallint,
	disb_vendor_id  integer,
	disb_expenditure_object_id  integer,
	disb_department_id  integer,
	disb_spending_category_id  smallint,
	disb_agreement_id  bigint,
	disb_contract_document_code  character varying(8),
	disb_master_agreement_id  bigint,
	disb_fiscal_year_id  smallint,
	disb_check_eft_issued_cal_month_id integer,
	disb_disbursement_number character varying(40),
	disb_minority_type_id smallint,
	disb_minority_type_name character varying(50),
	disb_vendor_type character(2),
	status_flag char(1),
	type_of_year char(1),
	is_prime_or_sub character(1)
) ;


CREATE INDEX idx_agreement_id_all_disb_trans ON all_disbursement_transactions(agreement_id); 
CREATE INDEX idx_agency_id_all_disb_trans ON all_disbursement_transactions USING btree (agency_id);
CREATE INDEX idx_nyc_year_id_all_disb_trans ON all_disbursement_transactions USING btree (check_eft_issued_nyc_year_id);
CREATE INDEX idx_ma_agreement_id_all_disb_trans ON all_disbursement_transactions(master_agreement_id);


 CREATE INDEX idx_disb_agr_id_cont_all_spen_trans ON contracts_all_spending_transactions(disb_agreement_id);
 CREATE INDEX idx_fiscal_year_id_cont_all_spen_trans ON contracts_all_spending_transactions(fiscal_year_id);
 CREATE INDEX idx_disb_fis_year_id_cont_all_spen_trans ON contracts_all_spending_transactions(disb_fiscal_year_id);
 CREATE INDEX idx_disb_cont_doc_code_cont_all_spen_trans ON contracts_all_spending_transactions(disb_contract_document_code);
 CREATE INDEX idx_document_agency_id_cont_all_spen_trans ON contracts_all_spending_transactions(document_agency_id);
 CREATE INDEX idx_disb_cal_month_id_cont_all_spen_trans ON contracts_all_spending_transactions(disb_check_eft_issued_cal_month_id);
 CREATE INDEX idx_document_code_id_cont_all_spen_trans ON contracts_all_spending_transactions(document_code_id);
 CREATE INDEX idx_vendor_id_cont_all_spen_trans ON contracts_all_spending_transactions(vendor_id);
 CREATE INDEX idx_award_method_id_cont_all_spen_trans ON contracts_all_spending_transactions(award_method_id);
 

CREATE INDEX idx_vendor_id_all_disb_trans ON all_disbursement_transactions(vendor_id);
CREATE INDEX idx_vendor_name_all_disb_trans ON all_disbursement_transactions(vendor_name);
CREATE INDEX idx_pri_vendor_id_all_disb_trans ON all_disbursement_transactions(prime_vendor_id);
CREATE INDEX idx_pr_vendor_name_all_disb_trans ON all_disbursement_transactions(prime_vendor_name);
CREATE INDEX idx_department_id_all_disb_trans ON all_disbursement_transactions(department_id);
CREATE INDEX idx_department_name_all_disb_trans ON all_disbursement_transactions(department_name);
CREATE INDEX idx_exp_object_name_all_disb_trans ON all_disbursement_transactions(expenditure_object_name);
CREATE INDEX idx_chk_eft_iss_date_all_disb_trans ON all_disbursement_transactions(check_eft_issued_date);

CREATE INDEX idx_vendor_id_all_agreement_trans ON all_agreement_transactions(vendor_id);
CREATE INDEX idx_vendor_name_all_agreement_trans ON all_agreement_transactions(vendor_name);
CREATE INDEX idx_pri_vendor_id_all_agreement_trans ON all_agreement_transactions(prime_vendor_id);
CREATE INDEX idx_pri_vendor_name_all_agreement_trans ON all_agreement_transactions(prime_vendor_name);
CREATE INDEX idx_agency_id_all_agreement_trans ON all_agreement_transactions(agency_id);
CREATE INDEX idx_award_method_id_all_agreement_trans ON all_agreement_transactions(award_method_id);
CREATE INDEX idx_award_category_id_all_agreement_trans ON all_agreement_transactions(award_category_id);

CREATE INDEX idx_vendor_id_all_agreement_trans_cy ON all_agreement_transactions_cy(vendor_id);
CREATE INDEX idx_vendor_name_all_agreement_trans_cy ON all_agreement_transactions_cy(vendor_name);
CREATE INDEX idx_pri_vendor_id_all_agreement_trans_cy ON all_agreement_transactions_cy(prime_vendor_id);
CREATE INDEX idx_pri_vendor_name_all_agreement_trans_cy ON all_agreement_transactions_cy(prime_vendor_name);
CREATE INDEX idx_agency_id_all_agreement_trans_cy ON all_agreement_transactions_cy(agency_id);
CREATE INDEX idx_award_method_id_all_agreement_trans_cy ON all_agreement_transactions_cy(award_method_id);
CREATE INDEX idx_award_category_id_all_agreement_trans_cy ON all_agreement_transactions_cy(award_category_id);


-- for distributed by columns that are not primary keys

CREATE INDEX idx_vendor_customer_code_sc_vendor_bus_type ON subcontract_vendor_business_type(vendor_customer_code);
CREATE INDEX idx_contract_number_sc_bus_type ON subcontract_business_type(contract_number);

CREATE INDEX idx_vendor_history_id_subvendor_min_bus_type ON subvendor_min_bus_type(vendor_history_id);
CREATE INDEX idx_contract_number_subcontract_status ON subcontract_status(contract_number);
CREATE INDEX idx_original_agreement_id_sub_agreement_snapshot ON sub_agreement_snapshot(original_agreement_id);
CREATE INDEX idx_original_agreement_id_sub_agreement_snapshot_cy ON sub_agreement_snapshot_cy(original_agreement_id);
CREATE INDEX idx_original_agreement_id_sub_agreement_snapshot_exp ON sub_agreement_snapshot_expanded(original_agreement_id);
CREATE INDEX idx_original_agreement_id_sub_agreement_snapshot_exp_cy ON sub_agreement_snapshot_expanded_cy(original_agreement_id);
CREATE INDEX idx_disbursement_line_item_id_sc_spending_details ON subcontract_spending_details(disbursement_line_item_id);
CREATE INDEX idx_original_agreement_id_all_agreement_trans ON all_agreement_transactions(original_agreement_id);
CREATE INDEX idx_original_agreement_id_all_agreement_trans_cy ON all_agreement_transactions_cy(original_agreement_id);
CREATE INDEX idx_disbursement_line_item_id_all_disb_trans ON all_disbursement_transactions(disbursement_line_item_id);

CREATE INDEX idx_vendor_id_agg_on_subven_spe_coa_entities   ON  aggregateon_subven_spending_coa_entities(vendor_id) ;
CREATE INDEX idx_agreement_id_agg_subven_spe_contract   ON  aggregateon_subven_spending_contract(agreement_id) ;
CREATE INDEX idx_vendor_id_agg_subven_spe_vendor   ON  aggregateon_subven_spending_vendor(vendor_id) ;
CREATE INDEX idx_original_agreement_id_mid_agg_subven_disb_spe_year  ON  mid_aggregateon_subven_disbursement_spending_year(original_agreement_id) ;
CREATE INDEX idx_vendor_id_agg_subven_contracts_cumulative_spe   ON  aggregateon_subven_contracts_cumulative_spending(vendor_id)  ;
CREATE INDEX idx_vendor_id_agg_subven_contracts_spe_by_month   ON  aggregateon_subven_contracts_spending_by_month(vendor_id)  ;
CREATE INDEX idx_fiscal_year_agg_subven_total_contracts  ON   aggregateon_subven_total_contracts(fiscal_year) ;  
CREATE INDEX idx_disbursement_line_item_id_contracts_subven_spe_trans  ON  contracts_subven_spending_transactions(disbursement_line_item_id)  ;
CREATE INDEX idx_vendor_id_agg_all_contracts_cumulative_spe   ON  aggregateon_all_contracts_cumulative_spending(vendor_id)  ;
CREATE INDEX idx_disbursement_line_item_id_contracts_all_spe_trans  ON  contracts_all_spending_transactions(disbursement_line_item_id)  ;


-- external and staging tables


set search_path=etl;

DROP SEQUENCE IF EXISTS seq_stg_scntrc_details_uniq_id CASCADE;
DROP SEQUENCE IF EXISTS seq_stg_scntrc_status_uniq_id CASCADE;
DROP SEQUENCE IF EXISTS seq_stg_scntrc_bus_type_uniq_id CASCADE;
DROP SEQUENCE IF EXISTS seq_stg_scntrc_pymt_uniq_id CASCADE;

create sequence   seq_stg_scntrc_details_uniq_id;
create sequence   seq_stg_scntrc_status_uniq_id;
create sequence   seq_stg_scntrc_bus_type_uniq_id;
create sequence   seq_stg_scntrc_pymt_uniq_id;


CREATE FOREIGN TABLE foreign_tbl_scntrc_details_data_feed
(
  doc_cd character varying(8),
  doc_dept_cd character varying(4),
  doc_id character varying(20),
  vendor_cust_cd character varying(20),
  cntrc_typ character varying,
  scntrc_id character varying,
  aprv_sta character varying,
  aprv_reas_id character varying,
  aprv_reas_nm character varying,
  aprv_reas_nm_up character varying,
  scntrc_dscr character varying,
  scntrc_dscr_up character varying,
  scntrc_mwbe_cert character varying,
  indus_cls character varying,
  scntrc_strt_dt character varying(100),
  scntrc_end_dt character varying(100),
  scntrc_max_am character varying(100),
  tot_scntrc_pymt character varying(100),
  scntrc_pymt_act character varying,
  scntrc_mode character varying,
  scntrc_vers_no character varying,
  scntrc_vend_cd character varying(20),
  scntrc_lgl_nm character varying(100),
  scntrc_lgl_nm_up character varying(100),
  scntrc_trkg_no character varying,
  scntrc_trkg_no_up character varying,
  lgl_nm character varying(100),
  lgl_nm_up character varying(100),
  doc_ref character varying,
  col30 character varying
		) SERVER postgresql_file_server
OPTIONS ( filename '/home/gpadmin/POSTGRESQL/Checkbook/GPFDIST_DIR/datafiles/scntrc_details_data_feed.txt', format 'text', delimiter '|'  );


DROP  TABLE IF EXISTS ext_stg_scntrc_details_data_feed;

CREATE  TABLE ext_stg_scntrc_details_data_feed
(
  doc_cd character varying(8),
  doc_dept_cd character varying(4),
  doc_id character varying(20),
  vendor_cust_cd character varying(20),
  cntrc_typ character varying,
  scntrc_id character varying,
  aprv_sta character varying,
  aprv_reas_id character varying,
  aprv_reas_nm character varying,
  aprv_reas_nm_up character varying,
  scntrc_dscr character varying,
  scntrc_dscr_up character varying,
  scntrc_mwbe_cert character varying,
  indus_cls character varying,
  scntrc_strt_dt character varying(100),
  scntrc_end_dt character varying(100),
  scntrc_max_am character varying(100),
  tot_scntrc_pymt character varying(100),
  scntrc_pymt_act character varying,
  scntrc_mode character varying,
  scntrc_vers_no character varying,
  scntrc_vend_cd character varying(20),
  scntrc_lgl_nm character varying(100),
  scntrc_lgl_nm_up character varying(100),
  scntrc_trkg_no character varying,
  scntrc_trkg_no_up character varying,
  lgl_nm character varying(100),
  lgl_nm_up character varying(100),
  doc_ref character varying,
  col30 character varying
);

DROP  TABLE  IF EXISTS stg_scntrc_details;
DROP  TABLE  IF EXISTS archive_scntrc_details;
DROP  TABLE  IF EXISTS invalid_scntrc_details;

CREATE  TABLE stg_scntrc_details
(
  doc_cd character varying(8),
  doc_dept_cd character varying(4),
  doc_id character varying(20),
  vendor_cust_cd character varying(20),
  cntrc_typ smallint,
  scntrc_id character varying(20),
  aprv_sta smallint,
  aprv_reas_id character varying(3),
  aprv_reas_nm character varying(30),
  aprv_reas_nm_up character varying(30),
  scntrc_dscr character varying(256),
  scntrc_dscr_up character varying(256),
  scntrc_mwbe_cert smallint,
  indus_cls smallint,
  scntrc_strt_dt date,
  scntrc_end_dt date,
  scntrc_max_am numeric(16,2),
  tot_scntrc_pymt numeric(16,2),
  scntrc_pymt_act smallint,
  scntrc_mode smallint,
  scntrc_vers_no integer,
  scntrc_vend_cd character varying(20),
  scntrc_lgl_nm character varying(60),
  scntrc_lgl_nm_up character varying(60),
  scntrc_trkg_no character varying(30),
  scntrc_trkg_no_up character varying(30),
  lgl_nm character varying(60),
  lgl_nm_up character varying(60),
  doc_ref character varying(75),
  doc_appl_last_dt date,
  reg_dt date,
  document_code_id smallint,
  agency_history_id smallint,
  vendor_history_id integer,
	effective_begin_date_id int,
	effective_end_date_id int,
	source_updated_date_id int,
	registered_date_id int,
  	registered_fiscal_year smallint,
	registered_fiscal_year_id smallint, 
	registered_calendar_year smallint,
	registered_calendar_year_id smallint,
	effective_begin_fiscal_year smallint,
	effective_begin_fiscal_year_id smallint, 
	effective_begin_calendar_year smallint,
	effective_begin_calendar_year_id smallint,
	effective_end_fiscal_year smallint,
	effective_end_fiscal_year_id smallint, 
	effective_end_calendar_year smallint,
	effective_end_calendar_year_id smallint,
	source_updated_calendar_year smallint,
	source_updated_calendar_year_id smallint,
	source_updated_fiscal_year_id smallint,
	source_updated_fiscal_year smallint,
  uniq_id bigint DEFAULT nextval('etl.seq_stg_scntrc_details_uniq_id'::regclass),
  invalid_flag character(1),
  invalid_reason character varying
);


CREATE TABLE archive_scntrc_details (LIKE stg_scntrc_details) ;
ALTER TABLE archive_scntrc_details ADD COLUMN load_file_id bigint;

CREATE TABLE invalid_scntrc_details (LIKE archive_scntrc_details);

CREATE FOREIGN TABLE foreign_tbl_scntrc_status_data_feed
(
  doc_cd character varying(8),
  doc_dept_cd character varying(4),
  doc_id character varying(20),
  vendor_cust_cd character varying(20),
  scntrc_sta character varying,
  cntrc_typ character varying,
  tot_scntrc_max_am character varying(100),
  tot_scntrc_pymt character varying(100),
  col9 character varying
) SERVER postgresql_file_server
OPTIONS ( filename '/home/gpadmin/POSTGRESQL/Checkbook/GPFDIST_DIR/datafiles/scntrc_status_data_feed.txt', format 'text', delimiter '|'  );

DROP   TABLE  IF EXISTS ext_stg_scntrc_status_data_feed;
CREATE  TABLE ext_stg_scntrc_status_data_feed
(
  doc_cd character varying(8),
  doc_dept_cd character varying(4),
  doc_id character varying(20),
  vendor_cust_cd character varying(20),
  scntrc_sta character varying,
  cntrc_typ character varying,
  tot_scntrc_max_am character varying(100),
  tot_scntrc_pymt character varying(100),
  col9 character varying
);

DROP  TABLE  IF EXISTS stg_scntrc_status;
DROP  TABLE  IF EXISTS archive_scntrc_status;
DROP  TABLE  IF EXISTS invalid_scntrc_status;

CREATE  TABLE stg_scntrc_status
(
  doc_cd character varying(8),
  doc_dept_cd character varying(4),
  doc_id character varying(20),
  vendor_cust_cd character varying(20),
  scntrc_sta smallint,
  cntrc_typ smallint,
  tot_scntrc_max_am numeric(16,2),
  tot_scntrc_pymt numeric(16,2),
  uniq_id bigint DEFAULT nextval('etl.seq_stg_scntrc_status_uniq_id'::regclass),
  invalid_flag character(1),
  invalid_reason character varying
) ;


CREATE TABLE archive_scntrc_status (LIKE stg_scntrc_status) ;
ALTER TABLE archive_scntrc_status ADD COLUMN load_file_id bigint;

CREATE TABLE invalid_scntrc_status (LIKE archive_scntrc_status) ;


CREATE FOREIGN TABLE foreign_tbl_scntrc_bus_type_data_feed
(
  doc_cd character varying(8),
  doc_dept_cd character varying(4),
  doc_id character varying(20),
  vendor_cust_cd character varying(20),
  scntrc_id character varying,
  scntrc_vend_cd character varying(20),
  bus_typ character varying,
  bus_typ_sta character varying,
  cert_strt_dt character varying(100),
  init_dt character varying(100),
  disp_cert_strt_dt character varying(100),
  cert_end_dt character varying(100),
  cert_no character varying(30),
  min_typ character varying(10),
  col15 character varying
) 		SERVER postgresql_file_server
OPTIONS ( filename '/home/gpadmin/POSTGRESQL/Checkbook/GPFDIST_DIR/datafiles/scntrc_bus_type_data_feed.txt', format 'text', delimiter '|'  );


DROP   TABLE  IF EXISTS ext_stg_scntrc_bus_type_data_feed;
CREATE  TABLE ext_stg_scntrc_bus_type_data_feed
(
  doc_cd character varying(8),
  doc_dept_cd character varying(4),
  doc_id character varying(20),
  vendor_cust_cd character varying(20),
  scntrc_id character varying,
  scntrc_vend_cd character varying(20),
  bus_typ character varying,
  bus_typ_sta character varying,
  cert_strt_dt character varying(100),
  init_dt character varying(100),
  disp_cert_strt_dt character varying(100),
  cert_end_dt character varying(100),
  cert_no character varying(30),
  min_typ character varying(10),
  col15 character varying
);


DROP  TABLE  IF EXISTS stg_scntrc_bus_type;
DROP  TABLE  IF EXISTS archive_scntrc_bus_type;
DROP  TABLE  IF EXISTS invalid_scntrc_bus_type;

CREATE  TABLE stg_scntrc_bus_type
(
  doc_cd character varying(8),
  doc_dept_cd character varying(4),
  doc_id character varying(20),
  vendor_cust_cd character varying(20),
  scntrc_id character varying(20),
  scntrc_vend_cd character varying(20),
  bus_typ character varying(10),
  bus_typ_sta integer,
  cert_strt_dt date,
  init_dt date,
  disp_cert_strt_dt date,
  cert_end_dt date,
  cert_no character varying(30),
  min_typ integer,
  uniq_id bigint DEFAULT nextval('etl.seq_stg_scntrc_bus_type_uniq_id'::regclass),
  invalid_flag character(1),
  invalid_reason character varying
) ;


CREATE TABLE archive_scntrc_bus_type (LIKE stg_scntrc_bus_type) 
ALTER TABLE archive_scntrc_bus_type ADD COLUMN load_file_id bigint;

CREATE TABLE invalid_scntrc_bus_type (LIKE archive_scntrc_bus_type);



CREATE  FOREIGN TABLE foreign_tbl_scntrc_pymt_data_feed
(
  doc_cd character varying(8),
  doc_dept_cd character varying(4),
  doc_id character varying(20),
  vendor_cust_cd character varying(20),
  scntrc_id character varying,
  scntrc_pymt_id character varying,
  lgl_nm character varying,
  lgl_nm_up character varying,
  scntrc_lgl_nm character varying,
  scntrc_vend_cd character varying(20),
  scntrc_lgl_nm_up character varying,
  scntrc_pymt_dt character varying(100),
  scntrc_pymt_am character varying(100),
  scntrc_pymt_dscr character varying,
  scntrc_pymt_dscr_up character varying,
  scntrc_prf_pymt character varying,
  scntrc_prf_pymt_up character varying,
  scntrc_fnl_pymt_fl character varying,
  doc_ref character varying,
  col20 character varying
) SERVER postgresql_file_server
OPTIONS ( filename '/home/gpadmin/POSTGRESQL/Checkbook/GPFDIST_DIR/datafiles/scntrc_pymt_data_feed.txt', format 'text', delimiter '|'  );



DROP   TABLE  IF EXISTS ext_stg_scntrc_pymt_data_feed;
CREATE  TABLE ext_stg_scntrc_pymt_data_feed
(
  doc_cd character varying(8),
  doc_dept_cd character varying(4),
  doc_id character varying(20),
  vendor_cust_cd character varying(20),
  scntrc_id character varying,
  scntrc_pymt_id character varying,
  lgl_nm character varying,
  lgl_nm_up character varying,
  scntrc_lgl_nm character varying,
  scntrc_vend_cd character varying(20),
  scntrc_lgl_nm_up character varying,
  scntrc_pymt_dt character varying(100),
  scntrc_pymt_am character varying(100),
  scntrc_pymt_dscr character varying,
  scntrc_pymt_dscr_up character varying,
  scntrc_prf_pymt character varying,
  scntrc_prf_pymt_up character varying,
  scntrc_fnl_pymt_fl character varying,
  doc_ref character varying,
  col20 character varying
);



DROP  TABLE  IF EXISTS stg_scntrc_pymt;
DROP  TABLE  IF EXISTS archive_scntrc_pymt;
DROP  TABLE  IF EXISTS invalid_scntrc_pymt;

CREATE  TABLE stg_scntrc_pymt
(
  doc_cd character varying(8),
  doc_dept_cd character varying(4),
  doc_id character varying(20),
  vendor_cust_cd character varying(20),
  scntrc_id character varying(20),
  scntrc_pymt_id character varying(20),
  lgl_nm character varying(60),
  lgl_nm_up character varying(60),
  scntrc_lgl_nm character varying(60),
  scntrc_vend_cd character varying(20),
  scntrc_lgl_nm_up character varying(60),
  scntrc_pymt_dt date,
  scntrc_pymt_am numeric(16,2),
  scntrc_pymt_dscr character varying(256),
  scntrc_pymt_dscr_up character varying(256),
  scntrc_prf_pymt character varying(256),
  scntrc_prf_pymt_up character varying(256),
  scntrc_fnl_pymt_fl character varying(3),
  doc_ref character varying(75),
  agreement_id bigint,
  document_code_id smallint,
  agency_history_id smallint,
  vendor_history_id integer,
  check_eft_issued_date_id int,
  check_eft_issued_nyc_year_id smallint,  
  uniq_id bigint DEFAULT nextval('etl.seq_stg_scntrc_pymt_uniq_id'::regclass),
  invalid_flag character(1),
  invalid_reason character varying
) ;


CREATE TABLE archive_scntrc_pymt (LIKE stg_scntrc_pymt) 
ALTER TABLE archive_scntrc_pymt ADD COLUMN load_file_id bigint;

CREATE TABLE invalid_scntrc_pymt (LIKE archive_scntrc_pymt) ;



DROP  TABLE  IF EXISTS tmp_stg_scntrc_vendor;
CREATE TABLE tmp_stg_scntrc_vendor(
 	vend_cust_cd varchar(20),	
 	lgl_nm varchar(60),
 	vendor_history_id integer, 
 	uniq_id bigint
 	)	;
 	
 	
DROP  TABLE  IF EXISTS tmp_scntrc_all_vendors;		
CREATE TABLE tmp_scntrc_all_vendors(
	uniq_id bigint,
	vendor_customer_code varchar, 
	vendor_history_id integer, 
	vendor_id integer, 					
	is_new_vendor char(1), 
	is_name_changed char(1), 
	is_bus_type_changed char(1), 					
	lgl_nm varchar(60)
	)	;
	
	
DROP  TABLE  IF EXISTS tmp_scntrc_all_vendors_uniq_id;		 
CREATE TABLE tmp_scntrc_all_vendors_uniq_id(
	uniq_id bigint
	);

	
DROP  TABLE  IF EXISTS tmp_scntrc_vendor_update;		 
CREATE TABLE tmp_scntrc_vendor_update (
     	vendor_id integer,
     	legal_name varchar(60)
		)	;	
		

DROP TABLE IF EXISTS malformed_scntrc_details_data_feed;
CREATE TABLE malformed_scntrc_details_data_feed(
	record varchar,
	load_file_id integer);


DROP TABLE IF EXISTS malformed_scntrc_status_data_feed;
CREATE TABLE malformed_scntrc_status_data_feed(
	record varchar,
	load_file_id integer);


DROP TABLE IF EXISTS malformed_scntrc_bus_type_data_feed;
CREATE TABLE malformed_scntrc_bus_type_data_feed(
	record varchar,
	load_file_id integer);


DROP TABLE IF EXISTS malformed_scntrc_pymt_data_feed;
CREATE TABLE malformed_scntrc_pymt_data_feed(
	record varchar,
	load_file_id integer);

/*
Below are scripts changed for CB4.1 version

Scripts.sql 
SubCONScripts.sql
SubContractStatusScripts.sql
SubContractVendorBusTypeScripts.sql
SubFMSScripts.sql 
SubVendorScripts.sql
CONScripts.sql
FMSScripts.sql
MAGScripts.sql
PMSScripts.sql
VendorScripts.sql
PendingContracts.sql
Trends.sql
ScriptsForReferenceTables.sql

*/
