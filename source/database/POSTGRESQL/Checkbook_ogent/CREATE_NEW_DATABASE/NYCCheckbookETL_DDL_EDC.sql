set search_path=etl;

create sequence seq_stg_edc_contract_uniq_id;
create sequence seq_stg_tdc_contract_uniq_id;

-- EDC ETL Tables

CREATE FOREIGN TABLE foreign_tbl_edc_contract_data_feed(
  agency_code character varying(4),
  agency_name character varying,
  department_code character varying(4),
  department_name character varying,
  contract_number character varying,
  commodity_line character varying,  
  edc_contract_number character varying(15),
  is_sandy_related character varying(3),
  purpose character varying,
  budget_name character varying,
  edc_registered_amount character varying,
  contractor_name character varying,
  contractor_address character varying,
  contractor_city character varying,
  contractor_state character varying,
  contractor_zip character varying
	) SERVER postgresql_file_server
OPTIONS ( filename '/vol2share/NYC/FEEDS/POSTGRESQL_OGE/GPFDIST_DIR/datafiles/EDC_feed.csv', format 'csv'  );

 CREATE  TABLE ext_stg_edc_contract_data_feed
(
  agency_code character varying(4),
  agency_name character varying,
  department_code character varying(4),
  department_name character varying,
  contract_number character varying,
  commodity_line character varying,  
  edc_contract_number character varying(15),
  is_sandy_related character varying(3),
  purpose character varying,
  budget_name character varying,
  edc_registered_amount character varying,
  contractor_name character varying,
  contractor_address character varying,
  contractor_city character varying,
  contractor_state character varying,
  contractor_zip character varying
);


CREATE TABLE stg_edc_contract(
	agency_code character varying(4),
	fms_contract_number character varying,
	fms_commodity_line integer,
	is_sandy_related character varying(3),
	edc_contract_number varchar(15),
	purpose varchar(75),
	budget_name varchar(75),
	edc_registered_amount numeric(16,2),
	contractor_name varchar(150),
	contractor_address varchar(75),
	contractor_city varchar(60),
	contractor_state varchar(25),
	contractor_zip varchar(25),
	agency_id integer,
	vendor_id integer,
	uniq_id bigint default nextval('seq_stg_edc_contract_uniq_id'),
	invalid_flag char(1),
	invalid_reason varchar		)
	;	
	
	
CREATE TABLE archive_edc_contract (LIKE stg_edc_contract) ;
ALTER TABLE archive_edc_contract ADD COLUMN load_file_id bigint;

CREATE TABLE invalid_edc_contract (LIKE archive_edc_contract) ;


CREATE TABLE seq_edc_contract_id(uniq_id bigint,edc_contract_id integer default nextval('public.seq_edc_contract_id'))
;

CREATE TABLE malformed_edc_contract_data_feed(
	record varchar,
	load_file_id integer)
;


-- TDC ETL Tables

CREATE FOREIGN TABLE foreign_tbl_tdc_contract_data_feed(
  agency_code character varying(4),
  agency_name character varying,
  department_code character varying(4),
  department_name character varying,
  contract_number character varying,
  commodity_line character varying,
  tdc_contract_number character varying(15),
  is_sandy_related character varying(3),  
  purpose character varying,
  budget_name character varying,
  tdc_registered_amount character varying,
  contractor_name character varying,
  contractor_address character varying,
  contractor_city character varying,
  contractor_state character varying,
  contractor_zip character varying
	) SERVER postgresql_file_server
OPTIONS ( filename '/vol2share/NYC/FEEDS/POSTGRESQL_OGE/GPFDIST_DIR/datafiles/TDC_feed.txt', format 'text', delimiter '|'  );

 CREATE TABLE ext_stg_tdc_contract_data_feed
(
  agency_code character varying(4),
  agency_name character varying,
  department_code character varying(4),
  department_name character varying,
  contract_number character varying,
  commodity_line character varying,
  tdc_contract_number character varying(15),
  is_sandy_related character varying(3),  
  purpose character varying,
  budget_name character varying,
  tdc_registered_amount character varying,
  contractor_name character varying,
  contractor_address character varying,
  contractor_city character varying,
  contractor_state character varying,
  contractor_zip character varying
);


	
CREATE TABLE stg_tdc_contract(
	agency_code character varying(4),
	fms_contract_number character varying,
	fms_commodity_line integer,
	is_sandy_related character varying(3),
	tdc_contract_number varchar(15),
	purpose varchar(75),
	budget_name varchar(75),
	tdc_registered_amount numeric(16,2),
	contractor_name varchar(150),
	contractor_address varchar(75),
	contractor_city varchar(60),
	contractor_state varchar(25),
	contractor_zip varchar(25),
	agency_id integer,
	vendor_id integer,
	uniq_id bigint default nextval('seq_stg_tdc_contract_uniq_id'),
	invalid_flag char(1),
	invalid_reason varchar		)
	;	
	
	
CREATE TABLE archive_tdc_contract (LIKE stg_tdc_contract) ;
ALTER TABLE archive_tdc_contract ADD COLUMN load_file_id bigint;

CREATE TABLE invalid_tdc_contract (LIKE archive_tdc_contract) ;


CREATE TABLE seq_tdc_contract_id(uniq_id bigint,edc_contract_id integer default nextval('public.seq_tdc_contract_id'))
;

CREATE TABLE malformed_tdc_contract_data_feed(
	record varchar,
	load_file_id integer)
;