-- *****************************************************************************
-- This file is part of the Checkbook NYC financial transparency software.
-- 
-- Copyright (C) 2012, 2013 New York City
-- 
-- This program is free software: you can redistribute it and/or modify
-- it under the terms of the GNU Affero General Public License as
-- published by the Free Software Foundation, either version 3 of the
-- License, or (at your option) any later version.
-- 
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU Affero General Public License for more details.
-- 
-- You should have received a copy of the GNU Affero General Public License
-- along with this program.  If not, see <http://www.gnu.org/licenses/>.
-- *****************************************************************************

create language plpgsql;

/* Sequences for reference tables*/
--  CREATE SEQUENCE seq_etl_data_load_load_id;
CREATE SEQUENCE seq_ref_agency_agency_id;
CREATE SEQUENCE seq_ref_fund_class_fund_class_id;
CREATE SEQUENCE seq_ref_department_department_id;
CREATE SEQUENCE seq_ref_award_category_award_category_id;
CREATE SEQUENCE seq_ref_award_method_award_method_id;
CREATE SEQUENCE seq_ref_budget_code_budget_code_id;
CREATE SEQUENCE seq_ref_business_type_business_type_id;
CREATE SEQUENCE seq_ref_document_code_document_code_id;
CREATE SEQUENCE seq_ref_expenditure_cancel_reason_expenditure_cancel_reason_id;
CREATE SEQUENCE seq_ref_expenditure_cancel_type_expenditure_cancel_type_id;
CREATE SEQUENCE seq_ref_agreement_type_agreement_type_id;
CREATE SEQUENCE seq_ref_expenditure_object_expendtiure_object_id;
CREATE SEQUENCE seq_ref_expenditure_privacy_type_expenditure_privacy_type_id;
CREATE SEQUENCE seq_ref_expenditure_status_expenditure_status_id;
CREATE SEQUENCE seq_ref_funding_class_funding_class_id;
CREATE SEQUENCE seq_ref_funding_source_funding_source_id;
CREATE SEQUENCE seq_ref_location_location_id;
CREATE SEQUENCE seq_ref_object_class_object_class_id;
CREATE SEQUENCE seq_ref_revenue_category_revenue_category_id;
CREATE SEQUENCE seq_ref_revenue_class_revenue_class_id;
CREATE SEQUENCE seq_ref_revenue_source_revenue_source_id;
CREATE SEQUENCE seq_ref_budget_budget_code_id;
CREATE SEQUENCE seq_ref_address_type_address_type_id;
CREATE SEQUENCE seq_ref_date_date_id;
CREATE SEQUENCE seq_budget_budget_id;
CREATE SEQUENCE seq_vendor_address_vendor_address_id;
CREATE SEQUENCE seq_vendor_bus_type_vendor_bus_type_id;
CREATE SEQUENCE seq_vendor_history_vendor_history_id;
CREATE SEQUENCE seq_ref_misc_vendor_misc_vendor_id;
CREATE SEQUENCE seq_agreement_commodity_agreement_commodity_id;
CREATE SEQUENCE seq_agreement_worksite_agreement_worksite_id;
CREATE SEQUENCE seq_agreement_accounting_line_id;
CREATE SEQUENCE seq_disbursement_line_item_id;
CREATE SEQUENCE seq_ref_department_history_id;
CREATE SEQUENCE seq_ref_agency_history_id;
CREATE SEQUENCE seq_ref_expenditure_object_history_id;
CREATE SEQUENCE seq_ref_location_history_id;
CREATE SEQUENCE seq_ref_object_class_history_id;
CREATE SEQUENCE seq_ref_year_year_id;
CREATE SEQUENCE seq_ref_month_month_id;
CREATE SEQUENCE seq_payroll_payroll_id;
CREATE SEQUENCE seq_employee_employee_id;
CREATE SEQUENCE seq_employee_history_employee_history_id;
CREATE SEQUENCE seq_revenue_budget_revenue_budget_id;
CREATE SEQUENCE seq_ref_award_category_industry_award_category_industry_id;
-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

/*Sequences for FMSV data feed*/

CREATE SEQUENCE seq_address_address_id;
CREATE SEQUENCE seq_vendor_vendor_id;
CREATE SEQUENCE seq_vendor_vendor_sub_code;
-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
/* Sequence for CON data feed*/

CREATE SEQUENCE seq_agreement_agreement_id;

-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

/* Sequence for FMS data feed*/

CREATE SEQUENCE seq_expenditure_expenditure_id;

-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

/* Sequence for PMS data feed*/

CREATE SEQUENCE seq_payroll_summary_payroll_summary_id;

-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

/* REVENUE */

CREATE SEQUENCE seq_revenue_revenue_id;

-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

/*Lookup Tables
*/

/*CREATE TABLE etl_data_load (
    load_id integer PRIMARY KEY DEFAULT nextval('seq_etl_data_load_load_id'::regclass) NOT NULL,
    data_source_code character(1),
    publish_start_time timestamp without time zone,
    publish_end_time timestamp without time zone,
    is_published bit(1)
) distributed by (load_id);
*/
 
/* 
CREATE TABLE ref_data_source (
  data_source_code varchar(2) ,
  description varchar(40) ,
  created_date timestamp 
) DISTRIBUTED BY (data_source_code);
*/

CREATE TABLE ref_agency (
    agency_id smallint PRIMARY KEY DEFAULT nextval('seq_ref_agency_agency_id'::regclass) NOT NULL,
    agency_code character varying(20),
    agency_name character varying(100),
    agency_short_name character varying(15),
    original_agency_name character varying(100),
    is_display char(1) default 'N',
    created_date timestamp without time zone,
    updated_date timestamp,
    created_load_id integer,
    updated_load_id integer
) distributed by (agency_id);

/* ALTER TABLE  ref_agency ADD constraint fk_ref_agency_etl_data_load FOREIGN KEY(created_load_id) references etl_data_load(load_id);
 ALTER TABLE  ref_agency ADD constraint fk_ref_agency_etl_data_load_2 FOREIGN KEY(updated_load_id) references etl_data_load(load_id);
*/

CREATE TABLE ref_agency_history (
    agency_history_id smallint PRIMARY KEY DEFAULT nextval('seq_ref_agency_history_id'::regclass) NOT NULL,
    agency_id smallint,    
    agency_name character varying(100),
    agency_short_name character varying(15),
    created_date timestamp without time zone,
    load_id integer    
) distributed by (agency_history_id);

-- ALTER TABLE  ref_agency_history ADD constraint fk_ref_agency_history_etl_data_load FOREIGN KEY(load_id) references etl_data_load(load_id);
 ALTER TABLE  ref_agency_history ADD constraint fk_ref_agency_history_ref_agency FOREIGN KEY(agency_id) references ref_agency(agency_id);


CREATE TABLE ref_year(
	year_id smallint PRIMARY KEY default nextval('seq_ref_year_year_id'),
	year_value smallint
	)
DISTRIBUTED BY (year_id);
	
CREATE TABLE ref_month(
	month_id int PRIMARY KEY default nextval('seq_ref_month_month_id'),
	month_value smallint,
	month_name varchar,
	year_id smallint,
	display_order smallint,
	month_short_name varchar(3)
	)
DISTRIBUTED BY (month_id);
ALTER TABLE  ref_month ADD constraint fk_ref_month_ref_year FOREIGN KEY(year_id) references ref_year(year_id);

CREATE TABLE ref_date(
  date_id    int PRIMARY KEY default nextval('seq_ref_date_date_id'),
  date	     DATE,
  nyc_year_id   smallint,
  calendar_month_id int
 )
DISTRIBUTED BY (date_id);
ALTER TABLE  ref_date ADD constraint fk_ref_date_ref_month FOREIGN KEY(calendar_month_id) references ref_month(month_id);
ALTER TABLE  ref_date ADD constraint fk_ref_date_ref_year FOREIGN KEY(nyc_year_id) references ref_year(year_id);  
 
CREATE TABLE ref_fund_class (
  fund_class_id smallint PRIMARY KEY default nextval('seq_ref_fund_class_fund_class_id'),
  fund_class_code varchar(5),
  fund_class_name varchar(50),
  created_load_id integer,
  created_date timestamp
) DISTRIBUTED BY (fund_class_id);

CREATE TABLE ref_department (
    department_id integer PRIMARY KEY DEFAULT nextval('seq_ref_department_department_id'::regclass) NOT NULL,
    department_code character varying(20),
    department_name character varying(100),
    department_short_name character varying(15),
    agency_id smallint,
    fund_class_id smallint,
    fiscal_year smallint,
    original_department_name character varying(50),
    created_date timestamp without time zone,
    updated_date timestamp,
    created_load_id integer,
    updated_load_id integer    
) distributed by (department_id);

 ALTER TABLE  ref_department ADD constraint fk_ref_department_ref_fund_class FOREIGN KEY(fund_class_id) references ref_fund_class (fund_class_id);
-- ALTER TABLE  ref_department ADD constraint fk_ref_department_etl_data_load FOREIGN KEY(created_load_id) references etl_data_load;
 ALTER TABLE  ref_department ADD constraint fk_ref_department_ref_agency foreign key (agency_id) references ref_agency (agency_id);
-- ALTER TABLE  ref_department ADD constraint fk_ref_department_etl_data_load_2 FOREIGN KEY(updated_load_id) references etl_data_load;
 
CREATE TABLE ref_department_history (
    department_history_id integer PRIMARY KEY DEFAULT nextval('seq_ref_department_history_id'::regclass) NOT NULL,
    department_id integer,
    department_name character varying(100),
    department_short_name character varying(15),
    agency_id smallint,
    fund_class_id smallint,
    fiscal_year smallint,
    created_date timestamp without time zone,
    load_id integer    
) distributed by (department_history_id);

 ALTER TABLE  ref_department_history ADD constraint fk_ref_department_history_ref_fund_class FOREIGN KEY(fund_class_id) references ref_fund_class (fund_class_id);
 ALTER TABLE  ref_department_history ADD constraint fk_ref_department_history_ref_agency foreign key (agency_id) references ref_agency (agency_id);
-- ALTER TABLE  ref_department_history ADD constraint fk_ref_department_history_etl_data_load FOREIGN KEY(load_id) references etl_data_load(load_id);
 ALTER TABLE  ref_department_history ADD constraint fk_ref_department_history_ref_department FOREIGN KEY(department_id) references ref_department(department_id);

 CREATE TABLE ref_award_category (
  award_category_id smallint PRIMARY KEY default nextval('seq_ref_award_category_award_category_id'),
  award_category_code varchar(10) default null,
  award_category_name varchar(50) default null,
  created_date timestamp
) DISTRIBUTED BY (award_category_id);


CREATE TABLE ref_award_method (
  award_method_id smallint PRIMARY KEY default nextval('seq_ref_award_method_award_method_id'),
  award_method_code varchar(3) ,
  award_method_name varchar,
  created_date timestamp
) DISTRIBUTED BY (award_method_id);

CREATE TABLE ref_budget_code (
    budget_code_id integer PRIMARY KEY DEFAULT nextval('seq_ref_budget_code_budget_code_id'::regclass) NOT NULL,
    fiscal_year smallint,
    budget_code character varying(10),
    agency_id smallint,
    fund_class_id smallint,
    budget_code_name character varying(60),
    attribute_name character varying(60),
    attribute_short_name character varying(15),
    responsibility_center character varying(4),
    control_category character varying(4),
    ua_funding_flag bit(1),
    payroll_default_flag bit(1),
    budget_function character varying(5),
    description character varying,
    created_date timestamp without time zone,
    load_id integer,
    updated_date timestamp without time zone,
    updated_load_id integer
) distributed by (budget_code_id);

 ALTER TABLE  ref_budget_code ADD constraint fk_ref_budget_code_ref_fund_class foreign key (fund_class_id) references ref_fund_class (fund_class_id);
 ALTER TABLE  ref_budget_code ADD constraint fk_ref_budget_code_ref_agency FOREIGN KEY (agency_id) REFERENCES ref_agency(agency_id);
-- ALTER TABLE  ref_budget_code ADD constraint fk_ref_budget_code_etl_data_load foreign key (load_id) references etl_data_load (load_id);

CREATE TABLE ref_business_type (
  business_type_id smallint PRIMARY KEY default nextval('seq_ref_business_type_business_type_id'),
  business_type_code varchar(4) ,
  business_type_name varchar(50) ,
  created_date timestamp
) DISTRIBUTED BY (business_type_id);


CREATE TABLE ref_business_type_status (
  business_type_status_id smallint PRIMARY KEY ,
  business_type_status varchar(50) ,
  created_date timestamp
) DISTRIBUTED BY (business_type_status_id);

CREATE TABLE ref_document_code (
  document_code_id  smallint PRIMARY KEY default nextval('seq_ref_document_code_document_code_id'),
  document_code varchar(8) ,
  document_name varchar(100) ,
  created_date timestamp 
) DISTRIBUTED BY (document_code_id);

CREATE TABLE ref_expenditure_cancel_reason (
  expenditure_cancel_reason_id smallint PRIMARY KEY default nextval('seq_ref_expenditure_cancel_reason_expenditure_cancel_reason_id'),
  expenditure_cancel_reason_name varchar(50) ,
  created_date timestamp 
  ) DISTRIBUTED BY (expenditure_cancel_reason_id);


CREATE TABLE ref_expenditure_cancel_type (
  expenditure_cancel_type_id  smallint PRIMARY KEY default nextval('seq_ref_expenditure_cancel_type_expenditure_cancel_type_id'),
  expenditure_cancel_type_name varchar(50) ,
  created_date timestamp 
) DISTRIBUTED BY (expenditure_cancel_type_id);

 
 CREATE TABLE ref_agreement_type (
   agreement_type_id SMALLINT PRIMARY KEY DEFAULT nextval('seq_ref_agreement_type_agreement_type_id'),
   agreement_type_code varchar(2),
   agreement_type_name varchar(50),
   created_date timestamp
) DISTRIBUTED BY (agreement_type_id);

CREATE TABLE ref_expenditure_object (
  expenditure_object_id INT PRIMARY KEY DEFAULT  nextval('seq_ref_expenditure_object_expendtiure_object_id'),
  expenditure_object_code varchar(4) ,
  expenditure_object_name varchar(40) ,
  fiscal_year smallint ,
  original_expenditure_object_name varchar(40) ,
  created_date timestamp ,
    updated_date timestamp,
    created_load_id integer,
    updated_load_id integer      
) DISTRIBUTED BY (expenditure_object_id);

-- ALTER TABLE  ref_expenditure_object ADD constraint fk_ref_expenditure_object_etl_data_load foreign key (created_load_id) references etl_data_load (load_id);
-- ALTER TABLE  ref_expenditure_object ADD constraint fk_ref_expenditure_object_etl_data_load_2 foreign key (updated_load_id) references etl_data_load (load_id);

CREATE TABLE ref_expenditure_object_history (
  expenditure_object_history_id INT PRIMARY KEY DEFAULT  nextval('seq_ref_expenditure_object_history_id'),
  expenditure_object_id INT,  
  expenditure_object_name varchar(40) ,
  fiscal_year smallint ,
  created_date timestamp ,
  load_id int
) DISTRIBUTED BY (expenditure_object_history_id);

-- ALTER TABLE  ref_expenditure_object_history ADD constraint fk_ref_exp_object_history_etl_data_load foreign key (load_id) references etl_data_load (load_id);
 ALTER TABLE  ref_expenditure_object_history ADD constraint fk_ref_exp_object_history_ref_exp_object foreign key (expenditure_object_id) references ref_expenditure_object (expenditure_object_id);


CREATE TABLE ref_expenditure_privacy_type (
  expenditure_privacy_type_id SMALLINT PRIMARY KEY DEFAULT nextval('seq_ref_expenditure_privacy_type_expenditure_privacy_type_id'),
  expenditure_privacy_code varchar(1) ,
  expenditure_privacy_name varchar(20) ,
  created_date timestamp 
) DISTRIBUTED BY (expenditure_privacy_type_id);

CREATE TABLE ref_expenditure_status (
  expenditure_status_id SMALLINT PRIMARY KEY DEFAULT nextval('seq_ref_expenditure_status_expenditure_status_id'),
  expenditure_status_name varchar(50) ,
  created_date timestamp 
) DISTRIBUTED BY (expenditure_status_id);


CREATE TABLE ref_funding_class (
    funding_class_id smallint PRIMARY KEY DEFAULT nextval('seq_ref_funding_class_funding_class_id'::regclass) NOT NULL,
    funding_class_code character varying(4),
    funding_class_name character varying(60),
    funding_class_short_name character varying(15),
    category_name character varying(60),
    city_fund_flag bit(1),
    intra_city_flag bit(1),
    fund_allocation_required_flag bit(1),
    category_code character varying(2),
    created_date timestamp without time zone,
    fiscal_year smallint,
    updated_date timestamp,
    created_load_id integer,
    updated_load_id integer
) distributed by (funding_class_id);


CREATE TABLE ref_funding_source (
  funding_source_id SMALLINT PRIMARY KEY DEFAULT nextval('seq_ref_funding_source_funding_source_id'),
  funding_source_code varchar(5) ,
  funding_source_name varchar(20) ,
  created_date timestamp
) DISTRIBUTED BY (funding_source_id);



CREATE TABLE ref_location (
    location_id integer PRIMARY KEY DEFAULT nextval('seq_ref_location_location_id'::regclass) NOT NULL,
    location_code character varying(4),
    agency_id smallint,
    location_name character varying(60),
    location_short_name character varying(16),
    original_location_name character varying(60),
    created_date timestamp without time zone,
    updated_date timestamp,
    created_load_id integer,
    updated_load_id integer  
) distributed by (location_id);


-- ALTER TABLE  ref_location ADD constraint fk_ref_location_etl_data_load foreign key (created_load_id) references etl_data_load (load_id);
 ALTER TABLE  ref_location ADD CONSTRAINT fk_ref_location_ref_agency FOREIGN KEY (agency_id) REFERENCES ref_agency(agency_id);
-- ALTER TABLE  ref_location ADD constraint fk_ref_location_etl_data_load_2 foreign key (updated_load_id) references etl_data_load (load_id);

CREATE TABLE ref_location_history (
    location_history_id integer PRIMARY KEY DEFAULT nextval('seq_ref_location_history_id'::regclass) NOT NULL,
    location_id integer,
    agency_id smallint,
    location_name character varying(60),
    location_short_name character varying(16),
    created_date timestamp without time zone,
    load_id integer
) distributed by (location_history_id);


-- ALTER TABLE  ref_location_history ADD constraint fk_ref_location_history_etl_data_load foreign key (load_id) references etl_data_load (load_id);
 ALTER TABLE  ref_location_history ADD CONSTRAINT fk_ref_location_history_ref_agency FOREIGN KEY (agency_id) REFERENCES ref_agency(agency_id);
 ALTER TABLE  ref_location_history ADD CONSTRAINT fk_ref_location_history_ref_location FOREIGN KEY (location_id) REFERENCES ref_location(location_id);


CREATE TABLE ref_minority_type (
  minority_type_id smallint primary key,
  minority_type_name VARCHAR(50),
  created_date timestamp
);

CREATE TABLE ref_object_class (
    object_class_id integer PRIMARY KEY DEFAULT nextval('seq_ref_object_class_object_class_id'::regclass) NOT NULL,
    object_class_code character varying(4),
    object_class_name character varying(60),
    object_class_short_name character varying(15),
    active_flag bit(1),
    effective_begin_date_id int,
    effective_end_date_id int,
    budget_allowed_flag bit(1),
    description character varying(100),
    source_updated_date timestamp without time zone,
    intra_city_flag bit(1),
    contracts_positions_flag bit(1),
    payroll_type integer,
    extended_description character varying,
    related_object_class_code character varying(4),
    original_object_class_name character varying(60),
    created_date timestamp without time zone,
    updated_date timestamp,
    created_load_id integer,
    updated_load_id integer      
) distributed by (object_class_id);


 ALTER TABLE  ref_object_class 	ADD constraint fk_ref_object_class_ref_date foreign key (effective_begin_date_id) references ref_date (date_id);
 ALTER TABLE  ref_object_class 	ADD constraint fk_ref_object_class_ref_date_1 foreign key (effective_end_date_id) references ref_date (date_id);
-- ALTER TABLE  ref_object_class ADD constraint fk_ref_object_class_etl_data_load foreign key (created_load_id) references etl_data_load (load_id);
-- ALTER TABLE  ref_object_class ADD constraint fk_ref_object_class_etl_data_load_2 foreign key (updated_load_id) references etl_data_load (load_id);

CREATE TABLE ref_object_class_history (
    object_class_history_id integer PRIMARY KEY DEFAULT nextval('seq_ref_object_class_history_id'::regclass) NOT NULL,
    object_class_id integer,    
    object_class_name character varying(60),
    object_class_short_name character varying(15),
    active_flag bit(1),
    effective_begin_date_id int,
    effective_end_date_id int,
    budget_allowed_flag bit(1),
    description character varying(100),
    source_updated_date timestamp without time zone,
    intra_city_flag bit(1),
    contracts_positions_flag bit(1),
    payroll_type integer,
    extended_description character varying,
    related_object_class_code character varying(4),
    created_date timestamp without time zone,
    load_id integer
) distributed by (object_class_history_id);


 ALTER TABLE  ref_object_class_history 	ADD constraint fk_ref_object_class_history_ref_date foreign key (effective_begin_date_id) references ref_date (date_id);
 ALTER TABLE  ref_object_class_history 	ADD constraint fk_ref_object_class_history_ref_date_1 foreign key (effective_end_date_id) references ref_date (date_id);
 ALTER TABLE  ref_object_class_history 	ADD constraint fk_ref_obj_class_history_ref_obj_class foreign key (object_class_id) references ref_object_class (object_class_id);
-- ALTER TABLE  ref_object_class_history ADD constraint fk_ref_object_class_history_etl_data_load foreign key (load_id) references etl_data_load (load_id);

CREATE TABLE ref_revenue_category (
    revenue_category_id smallint PRIMARY KEY DEFAULT nextval('seq_ref_revenue_category_revenue_category_id'::regclass) NOT NULL,
    revenue_category_code character varying(4),
    revenue_category_name character varying(60),
    revenue_category_short_name character varying(15),
    active_flag bit(1),
    budget_allowed_flag bit(1),
    description character varying(100),
    created_date timestamp without time zone,
    created_load_id integer,
    updated_load_id integer,
updated_date timestamp without time zone
) distributed by (revenue_category_id);

CREATE TABLE ref_revenue_class (
  revenue_class_id smallint PRIMARY KEY DEFAULT nextval('seq_ref_revenue_class_revenue_class_id'),
  revenue_class_code varchar(4) not null default '0',
  revenue_class_name varchar(60) ,
  revenue_class_short_name varchar(15) ,
  active_flag bit ,
  budget_allowed_flag bit ,
  description varchar(100),
  created_date timestamp,
  created_load_id integer,  
  updated_load_id integer,
  updated_date timestamp without time zone
) DISTRIBUTED BY (revenue_class_id);

CREATE TABLE ref_revenue_source (
    revenue_source_id int PRIMARY KEY DEFAULT nextval('seq_ref_revenue_source_revenue_source_id'::regclass) NOT NULL,
    fiscal_year smallint,
    revenue_source_code character varying(5),
    revenue_source_name character varying(60),
    revenue_source_short_name character varying(15),
    description character varying(100),
    funding_class_id smallint,
    revenue_class_id smallint,
    revenue_category_id smallint,
    active_flag bit(1),
    budget_allowed_flag bit(1),
    operating_indicator integer,
    fasb_class_indicator integer,
    fhwa_revenue_credit_flag integer,
    usetax_collection_flag integer,
    apply_interest_late_fee integer,
    apply_interest_admin_fee integer,
    apply_interest_nsf_fee integer,
    apply_interest_other_fee integer,
    eligible_intercept_process integer,
    earned_receivable_code character varying(4),
    finance_fee_override_flag integer,
    allow_override_interest integer,
    billing_lag_days integer,
    billing_frequency integer,
    billing_fiscal_year_start_month smallint,
    billing_fiscal_year_start_day smallint,
    federal_agency_code character varying(2),
    federal_agency_suffix character varying(3),
    federal_name character varying(60),
    srsrc_req character(1),
    created_date timestamp without time zone,
    updated_load_id integer,
    updated_date timestamp without time zone,
    created_load_id integer
) distributed by (revenue_source_id);


 ALTER TABLE  ref_revenue_source ADD constraint fk_ref_revenue_source_ref_funding_class foreign key (funding_class_id) references ref_funding_class (funding_class_id);
 ALTER TABLE  ref_revenue_source ADD constraint fk_ref_revenue_source_ref_revenue_class foreign key (revenue_class_id) references ref_revenue_class (revenue_class_id);
 ALTER TABLE  ref_revenue_source ADD constraint fk_ref_revenue_source_ref_revenue_category foreign key (revenue_category_id) references ref_revenue_category (revenue_category_id);

CREATE TABLE ref_address_type (
  address_type_id smallint PRIMARY KEY DEFAULT nextval('seq_ref_address_type_address_type_id'),
  address_type_code varchar(2),
  address_type_name varchar(50),
  created_date timestamp
);

CREATE TABLE ref_miscellaneous_vendor(
   misc_vendor_id smallint PRIMARY KEY DEFAULT nextval('seq_ref_misc_vendor_misc_vendor_id'),
   vendor_customer_code varchar(20),
   created_date timestamp
  );

  
----------------------------------------------------------------------------------------------------------------------------------------------------------------------------

/*
FMSV data feed
*/

CREATE TABLE address (
  address_id int PRIMARY KEY DEFAULT nextval('seq_address_address_id'),
  address_line_1 varchar(75) ,
  address_line_2 varchar(75) ,
  city varchar(60) ,
  state varchar(25) ,
  zip varchar(25) ,
  country varchar(25) 
) distributed by (address_id);

CREATE TABLE vendor (
    vendor_id integer PRIMARY KEY DEFAULT nextval('seq_vendor_vendor_id'::regclass) NOT NULL,
    vendor_customer_code character varying(25),
    legal_name character varying(60),
    alias_name character varying(60),
    miscellaneous_vendor_flag bit(1),
    vendor_sub_code integer DEFAULT nextval('seq_vendor_vendor_sub_code'::regclass),
    display_flag CHAR(1) DEFAULT 'Y',
    created_load_id integer,
    updated_load_id integer,
    created_date timestamp without time zone,
    updated_date timestamp without time zone
) distributed by (vendor_id);

-- ALTER TABLE vendor ADD constraint fk_vendor_etl_data_load foreign key (created_load_id) references etl_data_load (load_id);

CREATE TABLE vendor_history (
    vendor_history_id integer PRIMARY KEY DEFAULT nextval('seq_vendor_history_vendor_history_id'::regclass) NOT NULL,
    vendor_id integer,   
    legal_name character varying(60),
    alias_name character varying(60),
    miscellaneous_vendor_flag bit(1),
    vendor_sub_code integer DEFAULT nextval('seq_vendor_vendor_sub_code'::regclass),
    load_id integer,
    created_date timestamp without time zone,
    updated_date timestamp without time zone
) distributed by (vendor_history_id);

ALTER TABLE vendor_history ADD CONSTRAINT fk_vendor_history_vendor FOREIGN KEY (vendor_id) references vendor(vendor_id);



CREATE TABLE vendor_address (
    vendor_address_id bigint PRIMARY KEY DEFAULT nextval('seq_vendor_address_vendor_address_id') NOT NULL,
    vendor_history_id integer,
    address_id integer,
    address_type_id smallint,
    effective_begin_date_id int,
    effective_end_date_id int,
    load_id integer,
    created_date timestamp without time zone,
    updated_date timestamp without time zone
) distributed by (vendor_address_id);

ALTER TABLE vendor_address ADD constraint fk_vendor_address_vendor_history foreign key (vendor_history_id) references vendor_history (vendor_history_id);
ALTER TABLE vendor_address ADD constraint fk_vendor_address_address foreign key (address_id) references address (address_id);
ALTER TABLE vendor_address ADD constraint fk_vendor_address_ref_address_type foreign key (address_type_id) references ref_address_type (address_type_id);
-- ALTER TABLE vendor_address ADD constraint fk_vendor_address_etl_data_load foreign key (load_id) references etl_data_load (load_id);
ALTER TABLE vendor_address ADD constraint fk_vendor_addressr_ref_date foreign key (effective_begin_date_id) references ref_date (date_id);
ALTER TABLE vendor_address ADD constraint fk_vendor_address_ref_date_1 foreign key (effective_end_date_id) references ref_date (date_id);


CREATE TABLE vendor_business_type (
    vendor_business_type_id bigint PRIMARY KEY DEFAULT nextval('seq_vendor_bus_type_vendor_bus_type_id') NOT NULL,
    vendor_history_id integer,
    business_type_id smallint,
    status smallint,
    minority_type_id smallint,
    load_id integer,
    created_date timestamp without time zone,
    updated_date timestamp without time zone
) distributed by (vendor_business_type_id);



ALTER TABLE vendor_business_type ADD constraint fk_vendor_business_type_vendor_history foreign key (vendor_history_id) references vendor_history (vendor_history_id);
ALTER TABLE vendor_business_type ADD constraint fk_vendor_business_type_ref_business_type foreign key (business_type_id) references ref_business_type (business_type_id);
-- ALTER TABLE vendor_business_type ADD constraint fk_vendor_business_type_etl_data_load foreign key (load_id) references etl_data_load (load_id);
ALTER TABLE vendor_business_type ADD constraint fk_vendor_business_type_ref_minority_type foreign key (minority_type_id) references ref_minority_type (minority_type_id);

CREATE TABLE fmsv_business_type (
	vendor_customer_code character varying(20),
	business_type_id smallint,
	status smallint,
    	minority_type_id smallint,
    	certification_start_date date,
    	certification_end_date date, 
    	initiation_date date
)
DISTRIBUTED BY (vendor_customer_code);


CREATE TABLE vendor_details (
	vendor_history_id integer,
	vendor_id		integer,
	vendor_customer_code	character varying(20),
	legal_name		character varying(60),
	alias_name		character varying(60),
	miscellaneous_vendor_flag	bit(1),
	vendor_sub_code		integer,
	address_type_code	character varying(2),
	address_type_name	character varying(50),
	address_id		integer,
	address_line_1		character varying(75),
	address_line_2		character varying(75),
	city			character varying(60),
	state			character(2),
	zip			character varying(10),
	country			character(3),
	status			smallint,
	business_type_id	smallint,
	business_type_code	character varying(4),
	business_type_name	character varying(50),
	minority_type_id	smallint,
	minority_type_name	character varying(50)
)
DISTRIBUTED BY (vendor_history_id);


----------------------------------------------------------------------------------------------------------------------------------------------------------------------------

/*
MAG Data feed
*/

CREATE TABLE history_master_agreement
(
  master_agreement_id bigint NOT NULL DEFAULT nextval('seq_agreement_agreement_id'::regclass),
  document_code_id smallint,
  agency_history_id smallint,
  document_id character varying(20),
  document_version integer,
  tracking_number character varying(30),
  record_date_id int,
  budget_fiscal_year smallint,
  document_fiscal_year smallint,
  document_period character(2),
  description character varying(60),
  actual_amount_original numeric(16,2),
  actual_amount numeric(16,2),
  total_amount_original numeric(16,2),
  total_amount numeric(16,2),
  replacing_master_agreement_id bigint,
  replaced_by_master_agreement_id bigint,
  award_status_id smallint,
  procurement_id character varying(20),
  procurement_type_id smallint,
  effective_begin_date_id int,
  effective_end_date_id int,
  reason_modification character varying,
  source_created_date_id int,
  source_updated_date_id int,
  document_function_code character varying,
  award_method_id smallint,
  agreement_type_id smallint,
  award_category_id_1 smallint,
  award_category_id_2 smallint,
  award_category_id_3 smallint,
  award_category_id_4 smallint,
  award_category_id_5 smallint,
  number_responses integer,
  location_service character varying(255),
  location_zip character varying(10),
  borough_code character varying(10),
  block_code character varying(10),
  lot_code character varying(10),
  council_district_code character varying(10),
  vendor_history_id integer,
  vendor_preference_level integer,
  board_approved_award_no character varying(15),
  board_approved_award_date_id int,
  original_contract_amount_original numeric(20,2),
  original_contract_amount numeric(20,2),
  oca_number character varying(20),
  original_term_begin_date_id int,
  original_term_end_date_id int,
  registered_date_id int,
  maximum_amount_original numeric(20,2),
  maximum_amount numeric(20,2),
  maximum_spending_limit_original numeric(20,2),
  maximum_spending_limit numeric(20,2),
  award_level_code character(2),
  contract_class_code character varying(2),
  number_solicitation integer,
  document_name character varying(60),
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
  contract_number character varying,
  original_master_agreement_id bigint,
  original_version_flag character(1),
  latest_flag character(1),
  privacy_flag character(1),
  created_load_id integer,
  updated_load_id integer,
  created_date timestamp without time zone,
  updated_date timestamp without time zone,
  CONSTRAINT history_master_agreement_pkey PRIMARY KEY (master_agreement_id)
)
DISTRIBUTED BY (master_agreement_id);

 -- ALTER TABLE  history_master_agreement ADD CONSTRAINT fk_history_master_agreement_etl_data_load FOREIGN KEY (created_load_id) REFERENCES etl_data_load(load_id);
 ALTER TABLE  history_master_agreement ADD CONSTRAINT fk_history_master_agreement_ref_agency_history FOREIGN KEY (agency_history_id) REFERENCES ref_agency_history(agency_history_id);
 ALTER TABLE  history_master_agreement ADD CONSTRAINT fk_history_master_agreement_ref_agreement_type FOREIGN KEY (agreement_type_id) REFERENCES ref_agreement_type(agreement_type_id);
 ALTER TABLE  history_master_agreement ADD CONSTRAINT fk_history_master_agreement_ref_award_category_1 FOREIGN KEY (award_category_id_1) REFERENCES ref_award_category(award_category_id);
 ALTER TABLE  history_master_agreement ADD CONSTRAINT fk_history_master_agreement_ref_award_category_2 FOREIGN KEY (award_category_id_2) REFERENCES ref_award_category(award_category_id);
 ALTER TABLE  history_master_agreement ADD CONSTRAINT fk_history_master_agreement_ref_award_category_3 FOREIGN KEY (award_category_id_3) REFERENCES ref_award_category(award_category_id);
 ALTER TABLE  history_master_agreement ADD CONSTRAINT fk_history_master_agreement_ref_award_category_4 FOREIGN KEY (award_category_id_4) REFERENCES ref_award_category(award_category_id);
 ALTER TABLE  history_master_agreement ADD CONSTRAINT fk_history_master_agreement_ref_award_category_5 FOREIGN KEY (award_category_id_5) REFERENCES ref_award_category(award_category_id);
  ALTER TABLE  history_master_agreement ADD CONSTRAINT fk_history_master_agreement_ref_award_method FOREIGN KEY (award_method_id) REFERENCES ref_award_method(award_method_id);
 ALTER TABLE  history_master_agreement ADD CONSTRAINT fk_history_master_agreement_ref_document_code FOREIGN KEY (document_code_id) REFERENCES ref_document_code(document_code_id);
 ALTER TABLE  history_master_agreement ADD CONSTRAINT fk_history_master_agreement_vendor_history FOREIGN KEY (vendor_history_id) REFERENCES vendor_history(vendor_history_id);
  ALTER TABLE  history_master_agreement ADD constraint fk_history_master_agreement_ref_date foreign key (record_date_id) references ref_date (date_id);
  ALTER TABLE  history_master_agreement ADD constraint fk_history_master_agreement_ref_date_1 foreign key (effective_begin_date_id) references ref_date (date_id);
  ALTER TABLE  history_master_agreement ADD constraint fk_history_master_agreement_ref_date_2 foreign key (effective_end_date_id) references ref_date (date_id);
  ALTER TABLE  history_master_agreement ADD constraint fk_history_master_agreement_ref_date_3 foreign key (source_created_date_id) references ref_date (date_id);
  ALTER TABLE  history_master_agreement ADD constraint fk_history_master_agreement_ref_date_4 foreign key (source_updated_date_id) references ref_date (date_id);
  ALTER TABLE  history_master_agreement ADD constraint fk_history_master_agreement_ref_date_5 foreign key (board_approved_award_date_id) references ref_date (date_id);
  ALTER TABLE  history_master_agreement ADD constraint fk_history_master_agreement_ref_date_6 foreign key (original_term_begin_date_id) references ref_date (date_id);
  ALTER TABLE  history_master_agreement ADD constraint fk_history_master_agreement_ref_date_7 foreign key (original_term_end_date_id) references ref_date (date_id);
  ALTER TABLE  history_master_agreement ADD constraint fk_history_master_agreement_ref_date_8 foreign key (registered_date_id) references ref_date (date_id); 
 
CREATE TABLE history_agreement_commodity (
    agreement_commodity_id bigint  DEFAULT nextval('seq_agreement_commodity_agreement_commodity_id'::regclass) NOT NULL,
    agreement_id bigint,
    line_number integer,
    master_agreement_yn character(1),
    description character varying(60),
    commodity_code character varying(14),
    commodity_type_id integer,
    quantity numeric(27,5),
    unit_of_measurement character varying(4),
    unit_price numeric(28,6),
    contract_amount numeric(16,2),
    commodity_specification character varying,
    load_id integer,
    created_date timestamp without time zone,
    updated_date timestamp without time zone
) distributed by (agreement_id);


 
CREATE TABLE history_agreement_worksite (
    agreement_worksite_id bigint DEFAULT nextval('seq_agreement_worksite_agreement_worksite_id'::regclass) NOT NULL,
    agreement_id bigint,
    worksite_code varchar(3),
    percentage numeric(17,4),
    amount numeric(17,4),
    master_agreement_yn character(1),
    load_id integer,
    created_date timestamp without time zone,
    updated_date timestamp without time zone
) distributed by (agreement_id);


 
-------------------------------------------------------------------------------------------------------------------------------------------------------------------------

/* CON data feed */

CREATE TABLE history_agreement (
    agreement_id bigint  PRIMARY KEY DEFAULT nextval('seq_agreement_agreement_id'::regclass) NOT NULL,
    master_agreement_id bigint,
    document_code_id smallint,
    agency_history_id smallint,
    document_id character varying(20),
    document_version integer,
    tracking_number character varying(30),
    record_date_id int,
    budget_fiscal_year smallint,
    document_fiscal_year smallint,
    document_period character(2),
    description character varying(60),
    actual_amount_original numeric(16,2),
    actual_amount numeric(16,2),
    obligated_amount_original numeric(16,2),
    obligated_amount numeric(16,2),
    maximum_contract_amount_original numeric(16,2),
    maximum_contract_amount numeric(16,2),
    amendment_number character varying(19),
    replacing_agreement_id bigint,
    replaced_by_agreement_id bigint,
    award_status_id smallint,
    procurement_id character varying(20),
    procurement_type_id smallint,
    effective_begin_date_id int,
    effective_end_date_id int,
    reason_modification character varying,
    source_created_date_id int,
    source_updated_date_id int,
    document_function_code varchar,
    award_method_id smallint,
    award_level_code varchar,
    agreement_type_id smallint,
    contract_class_code character varying(2),
    award_category_id_1 smallint,
    award_category_id_2 smallint,
    award_category_id_3 smallint,
    award_category_id_4 smallint,
    award_category_id_5 smallint,
    number_responses integer,
    location_service character varying(255),
    location_zip character varying(10),
    borough_code character varying(10),
    block_code character varying(10),
    lot_code character varying(10),
    council_district_code character varying(10),
    vendor_history_id integer,
    vendor_preference_level integer,
    original_contract_amount_original numeric(16,2),
    original_contract_amount numeric(16,2),
    registered_date_id int,
    oca_number character varying(20),
    number_solicitation integer,
    document_name character varying(60),
    original_term_begin_date_id int,
    original_term_end_date_id int,    
    brd_awd_no varchar,
    rfed_amount_original numeric(16,2),
    rfed_amount numeric(16,2),
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
    contract_number varchar,
    original_agreement_id bigint,
    original_version_flag char(1),
    latest_flag char(1),
    privacy_flag char(1),
    created_load_id integer,
    updated_load_id integer,
    created_date timestamp without time zone,
    updated_date timestamp without time zone
) distributed by (agreement_id);
 
 
 
 CREATE TABLE agreement_snapshot
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
   original_contract_amount numeric(16,2),
   maximum_contract_amount numeric(16,2),
   description character varying,
   vendor_history_id integer,
   vendor_id integer,
   vendor_code character varying(20),
   vendor_name character varying,
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
   master_agreement_yn character(1),  
   has_children character(1),
   original_version_flag character(1),
   latest_flag character(1),
   load_id integer,
   last_modified_date timestamp without time zone,
   job_id bigint
 ) DISTRIBUTED BY (original_agreement_id);
 

CREATE TABLE history_agreement_accounting_line (
    agreement_accounting_line_id bigint DEFAULT nextval('seq_agreement_accounting_line_id'::regclass) NOT NULL,
    agreement_id bigint,
    commodity_line_number integer,
    line_number integer,
    event_type_code char(4),
    description character varying(100),
    line_amount_original numeric(16,2),
    line_amount numeric(16,2),
    budget_fiscal_year smallint,
    fiscal_year smallint,
    fiscal_period character(2),
    fund_class_id smallint,
    agency_history_id smallint,
    department_history_id integer,
    expenditure_object_history_id integer,
    revenue_source_id int,
    location_code character varying(4),
    budget_code_id integer,
    reporting_code character varying(15),
    rfed_line_amount_original numeric(16,2),
    rfed_line_amount numeric(16,2),
    created_load_id integer,
    updated_load_id integer,
    created_date timestamp without time zone,
    updated_date timestamp without time zone
) distributed by (agreement_id);

------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
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
    created_load_id integer,
    updated_load_id integer,
    created_date timestamp without time zone,
    updated_date timestamp without time zone
) distributed by (disbursement_id);



CREATE TABLE disbursement_line_item (
    disbursement_line_item_id bigint  PRIMARY KEY DEFAULT nextval('seq_disbursement_line_item_id'::regclass) NOT NULL,
    disbursement_id integer,
    line_number integer,
    disbursement_number character varying(40),
    budget_fiscal_year smallint,
    fiscal_year smallint,
    fiscal_period character(2),
    fund_class_id smallint,
    agency_history_id smallint,
    department_history_id integer,
    expenditure_object_history_id integer,
    budget_code_id integer,
    fund_code varchar(4),
    reporting_code character varying(15),
    check_amount_original numeric(16,2),
    check_amount numeric(16,2),
    agreement_id bigint,
    agreement_accounting_line_number integer,
    agreement_commodity_line_number integer,
    agreement_vendor_line_number integer, 
    reference_document_number character varying,
    reference_document_code varchar(8),
    location_history_id integer,
    retainage_amount_original numeric(16,2),
    retainage_amount numeric(16,2),
    check_eft_issued_nyc_year_id smallint,
    file_type char(1),
    created_load_id integer,
    updated_load_id integer,
    created_date timestamp without time zone,
    updated_date timestamp without time zone
   ) distributed by (disbursement_line_item_id);


CREATE TABLE disbursement_line_item_deleted (
  disbursement_line_item_id bigint NOT NULL,
  load_id integer,
  deleted_date timestamp without time zone,
  job_id bigint
) DISTRIBUTED BY (disbursement_line_item_id);

----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
CREATE TABLE payroll_summary (
    payroll_summary_id bigint  PRIMARY KEY DEFAULT nextval('seq_payroll_summary_payroll_summary_id'::regclass) NOT NULL,
    agency_history_id smallint,
    pay_cycle_code char(1),
    expenditure_object_history_id integer,
    payroll_number varchar,
    payroll_description varchar,
    department_history_id integer,
    pms_fiscal_year smallint,
    budget_code_id integer,
    total_amount_original numeric(15,2),
    total_amount numeric(15,2),
    pay_date_id int,
    fiscal_year smallint,
    fiscal_year_id smallint,
    calendar_fiscal_year_id smallint, 
    calendar_fiscal_year smallint,
    created_load_id integer,
    created_date timestamp without time zone,
    updated_date timestamp without time zone,
    updated_load_id integer
) distributed by (payroll_summary_id);


ALTER TABLE  payroll_summary ADD constraint fk_payroll_summary_ref_exp_object_history foreign key (expenditure_object_history_id) references ref_expenditure_object_history (expenditure_object_history_id);
ALTER TABLE  payroll_summary ADD constraint fk_payroll_summary_ref_department_history FOREIGN KEY (department_history_id) REFERENCES ref_department_history(department_history_id);
ALTER TABLE  payroll_summary ADD constraint fk_payroll_summary_ref_budget_code foreign key (budget_code_id) references ref_budget_code (budget_code_id);
-- ALTER TABLE  payroll_summary ADD constraint fk_payroll_summary_etl_data_load foreign key (created_load_id) references etl_data_load (load_id);
ALTER TABLE  payroll_summary ADD constraint fk_payroll_summary_ref_agency_summary foreign key (agency_history_id) references ref_agency_history (agency_history_id);

------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

CREATE TABLE revenue (
    revenue_id bigint  PRIMARY KEY  DEFAULT nextval('seq_revenue_revenue_id'::regclass) NOT NULL,
    record_date_id int,
    fiscal_period character(2),
    fiscal_year smallint,
    budget_fiscal_year smallint,
    fiscal_quarter smallint,
    event_category character varying(4),
    event_type character varying(4),
    bank_account_code character varying(4),
    posting_pair_type character varying(1),
    posting_code character varying(4),
    debit_credit_indicator character varying(1),
    line_function smallint,
    posting_amount_original numeric(16,2),
    posting_amount numeric(16,2),
    increment_decrement_indicator character varying(1),
    time_of_occurence timestamp without time zone,
    balance_sheet_account_code character varying(4),
    balance_sheet_account_type smallint,
    expenditure_object_history_id integer,
    government_branch_code character varying(4),
    cabinet_code character varying(4),
    agency_history_id smallint,
    department_history_id integer,
    reporting_activity_code character varying(10),
    budget_code_id integer,
    fund_category character varying(4),
    fund_type character varying(4),
    fund_group character varying(4),
    balance_sheet_account_class_code character varying(4),
    balance_sheet_account_category_code character varying(4),
    balance_sheet_account_group_code character varying(4),
    balance_sheet_account_override_flag character(1),
    object_class_history_id integer,
    object_category_code character varying(4),
    object_type_code character varying(4),
    object_group_code character varying(4),
    document_category character varying(8),
    document_type character varying(8),
    document_code_id smallint,
    document_agency_history_id smallint,
    document_id character varying(20),
    document_version_number integer,
    document_function_code varchar,
    document_unit character varying(8),
    commodity_line integer,
    accounting_line integer,
    document_posting_line integer,
    ref_document_code_id smallint,
    ref_document_agency_history_id smallint,
    ref_document_id character varying(20),
    ref_commodity_line integer,
    ref_accounting_line integer,
    ref_posting_line integer,
    reference_type smallint,
    line_description character varying(100),
    service_start_date_id int,
    service_end_date_id int,
    reason_code character varying(8),
    reclassification_flag integer,
    closing_classification_code character varying(2),
    closing_classification_name character varying(45),
    revenue_category_id smallint,
    revenue_class_id smallint,
    revenue_source_id int,
    funding_source_id smallint,
    fund_class_id smallint,
    reporting_code character varying(15),
    major_cafr_revenue_type character varying(4),
    minor_cafr_revenue_type character varying(4),
    vendor_history_id integer,
    fiscal_year_id smallint,
    budget_fiscal_year_id smallint,    
    load_id integer,
    created_date timestamp without time zone    
) distributed by (revenue_id);

-- ALTER TABLE  revenue ADD CONSTRAINT fk_revenue_etl_data_load FOREIGN KEY (load_id) REFERENCES etl_data_load(load_id);
ALTER TABLE  revenue ADD CONSTRAINT fk_revenue_ref_agency_history FOREIGN KEY (agency_history_id) REFERENCES ref_agency_history(agency_history_id);
ALTER TABLE  revenue ADD CONSTRAINT fk_revenue_ref_agency_history_2 FOREIGN KEY (document_agency_history_id) REFERENCES ref_agency_history(agency_history_id);
ALTER TABLE  revenue ADD CONSTRAINT fk_revenue_ref_agency_history_3 FOREIGN KEY (ref_document_agency_history_id) REFERENCES ref_agency_history(agency_history_id);
ALTER TABLE  revenue ADD CONSTRAINT fk_revenue_ref_budget_code FOREIGN KEY (budget_code_id) REFERENCES ref_budget_code(budget_code_id);
ALTER TABLE  revenue ADD CONSTRAINT fk_revenue_ref_department_history FOREIGN KEY (department_history_id) REFERENCES ref_department_history(department_history_id);
ALTER TABLE  revenue ADD CONSTRAINT fk_revenue_ref_document_code FOREIGN KEY (document_code_id) REFERENCES ref_document_code(document_code_id);
ALTER TABLE  revenue ADD CONSTRAINT fk_revenue_ref_document_code_2 FOREIGN KEY (ref_document_code_id) REFERENCES ref_document_code(document_code_id);
ALTER TABLE  revenue ADD CONSTRAINT fk_revenue_ref_exp_object_history FOREIGN KEY (expenditure_object_history_id) REFERENCES ref_expenditure_object_history(expenditure_object_history_id);
ALTER TABLE  revenue ADD CONSTRAINT fk_revenue_ref_fund_class FOREIGN KEY (fund_class_id) REFERENCES ref_fund_class(fund_class_id);
ALTER TABLE  revenue ADD CONSTRAINT fk_revenue_ref_funding_source FOREIGN KEY (funding_source_id) REFERENCES ref_funding_source(funding_source_id);
ALTER TABLE  revenue ADD CONSTRAINT fk_revenue_ref_object_class_history FOREIGN KEY (object_class_history_id) REFERENCES ref_object_class_history(object_class_history_id);
ALTER TABLE  revenue ADD CONSTRAINT fk_revenue_ref_revenue_category FOREIGN KEY (revenue_category_id) REFERENCES ref_revenue_category(revenue_category_id);
ALTER TABLE  revenue ADD CONSTRAINT fk_revenue_ref_revenue_class FOREIGN KEY (revenue_class_id) REFERENCES ref_revenue_class(revenue_class_id);
ALTER TABLE  revenue ADD CONSTRAINT fk_revenue_ref_revenue_source FOREIGN KEY (revenue_source_id) REFERENCES ref_revenue_source(revenue_source_id);
ALTER TABLE  revenue ADD CONSTRAINT fk_revenue_vendor_history FOREIGN KEY (vendor_history_id) REFERENCES vendor_history(vendor_history_id);
ALTER TABLE  revenue ADD CONSTRAINT fk_revenue_ref_date FOREIGN KEY (record_date_id) REFERENCES ref_date(date_id);
ALTER TABLE  revenue ADD CONSTRAINT fk_revenue_ref_date_1 FOREIGN KEY (service_start_date_id) REFERENCES ref_date(date_id);
ALTER TABLE  revenue ADD CONSTRAINT fk_revenue_ref_date_2 FOREIGN KEY (service_end_date_id) REFERENCES ref_date(date_id);

CREATE TABLE budget (
    budget_id integer PRIMARY KEY DEFAULT nextval('seq_budget_budget_id'::regclass) NOT NULL,
    budget_fiscal_year smallint,
    fund_class_id smallint,
    agency_history_id smallint,
    department_history_id integer,
    budget_code_id integer,
    object_class_history_id integer,
    adopted_amount_original numeric(20,2),
    adopted_amount numeric(20,2),
    current_budget_amount_original numeric(20,2),
    current_budget_amount numeric(20,2),
    pre_encumbered_amount_original numeric(20,2),
    pre_encumbered_amount numeric(20,2),
    encumbered_amount_original numeric(20,2),
    encumbered_amount numeric(20,2),
    accrued_expense_amount_original numeric(20,2),
    accrued_expense_amount numeric(20,2),
    cash_expense_amount_original numeric(20,2),
    cash_expense_amount numeric(20,2),
    post_closing_adjustment_amount_original numeric(20,2),
    post_closing_adjustment_amount numeric(20,2),
    total_expenditure_amount numeric(20,2),
    remaining_budget numeric(20,2),
    source_updated_date_id int,
    budget_fiscal_year_id smallint,
    agency_id smallint,
    object_class_id integer,
    department_id integer,
    agency_name varchar,
    object_class_name varchar,
    department_name varchar,
    budget_code varchar,
    budget_code_name varchar,
    agency_code varchar,
    department_code varchar,
    object_class_code varchar,
    created_load_id integer,
    updated_load_id integer,
    created_date timestamp without time zone,
    updated_date timestamp without time zone,
    agency_short_name varchar(15),
    department_short_name varchar(15),
    job_id bigint
) distributed by (budget_id);
	
ALTER TABLE  budget ADD constraint fk_budget_ref_fund_class foreign key (fund_class_id) references ref_fund_class (fund_class_id);
ALTER TABLE  budget ADD constraint fk_budget_ref_agency_history FOREIGN KEY (agency_history_id) REFERENCES ref_agency_history(agency_history_id);
ALTER TABLE  budget ADD constraint fk_budget_ref_department_history FOREIGN KEY (department_history_id) REFERENCES ref_department_history(department_history_id);
ALTER TABLE  budget ADD constraint fk_budget_ref_budget_code foreign key (budget_code_id) references ref_budget_code (budget_code_id);
ALTER TABLE  budget ADD constraint fk_budget_ref_object_class_history foreign key (object_class_history_id) references ref_object_class_history (object_class_history_id);
-- ALTER TABLE  budget ADD constraint fk_budget_etl_data_load foreign key (created_load_id) references etl_data_load (load_id);
ALTER TABLE  budget ADD constraint fk_budget_ref_date foreign key (source_updated_date_id) references ref_date (date_id);

----------------------------------------------------------------------------------------------------------------------------------------------------------

CREATE TABLE revenue_budget
(
  budget_id integer NOT NULL DEFAULT nextval('seq_revenue_budget_revenue_budget_id'::regclass),
  budget_fiscal_year smallint,
  budget_code character varying,
  agency_code character varying,
  revenue_source_code character varying,
  adopted_amount_original numeric(20,2),
  adopted_amount numeric(20,2),
  current_modified_budget_amount_original numeric(20,2),
  current_modified_budget_amount numeric(20,2),
  fund_class_id smallint,
  agency_history_id smallint,
  budget_code_id integer,
  agency_id smallint,
  revenue_source_id int,
  agency_name character varying,
  agency_short_name character varying,
  revenue_source_name character varying,
  created_load_id integer,
  updated_load_id integer,
  created_date timestamp without time zone,
  updated_date timestamp without time zone,
  budget_fiscal_year_id smallint,
  revenue_category_id smallint,
  revenue_category_code varchar(4),
  revenue_category_name varchar(60),
  funding_class_id smallint,
  funding_class_code varchar(4),
  funding_class_name varchar(60),
  budget_code_name varchar(60)
) DISTRIBUTED BY (budget_id);


-- ALTER TABLE  revenue_budget ADD  CONSTRAINT fk_revenue_budget_etl_data_load FOREIGN KEY (created_load_id) REFERENCES etl_data_load (load_id);
ALTER TABLE  revenue_budget ADD  CONSTRAINT fk_revenue_budget_ref_agency_history FOREIGN KEY (agency_history_id)  REFERENCES ref_agency_history (agency_history_id);
ALTER TABLE  revenue_budget ADD  CONSTRAINT fk_revenue_budget_ref_budget_code FOREIGN KEY (budget_code_id) REFERENCES ref_budget_code (budget_code_id);
ALTER TABLE  revenue_budget ADD  CONSTRAINT fk_revenue_budget_ref_fund_class FOREIGN KEY (fund_class_id) REFERENCES ref_fund_class (fund_class_id);


----------------------------------------------------------------------------------------------------------------------------------------------------------

CREATE TABLE revenue_details
(	revenue_id bigint,
	fiscal_year smallint,
	fiscal_period char(2),
	posting_amount decimal(16,2),
	revenue_category_id smallint,
	revenue_source_id int,
	fiscal_year_id smallint,
	agency_id smallint,
	department_id integer,	
	revenue_class_id smallint,
	fund_class_id smallint,
	funding_class_id smallint,
	budget_code_id integer,
	budget_fiscal_year_id smallint,
	agency_name varchar,
	revenue_category_name varchar,
	revenue_source_name varchar,
	budget_fiscal_year smallint,
	department_name varchar,
	revenue_class_name varchar,
	fund_class_name varchar,
	funding_class_name varchar,
	agency_code varchar,
	revenue_class_code varchar,
	fund_class_code varchar,
	funding_class_code varchar,
	revenue_category_code varchar,
	revenue_source_code varchar,
	closing_classification_code  varchar(2),
    closing_classification_name varchar(45),
    budget_code varchar(10),
    adopted_amount decimal(20,2),
    current_modified_budget_amount decimal(20,2),
	agency_short_name varchar(15),
	department_short_name varchar(15),
	agency_history_id smallint,
	load_id integer,
    last_modified_date timestamp without time zone,
    job_id bigint   
) DISTRIBUTED BY (revenue_id);

ALTER TABLE  revenue_details ADD CONSTRAINT fk_revenue_details_ref_revenue_category FOREIGN KEY (revenue_category_id) REFERENCES ref_revenue_category(revenue_category_id);
ALTER TABLE  revenue_details ADD CONSTRAINT fk_revenue_details_ref_revenue_source FOREIGN KEY (revenue_source_id) REFERENCES ref_revenue_source(revenue_source_id);
ALTER TABLE  revenue_details ADD CONSTRAINT fk_revenue_detailse_revenue FOREIGN KEY (revenue_id) REFERENCES revenue(revenue_id);
-----------------------------------------------------------------------------------------------------------------------------------------------------------

ALTER TABLE  disbursement ADD CONSTRAINT fk_disbursement_ref_agency_history FOREIGN KEY (agency_history_id) REFERENCES ref_agency_history(agency_history_id);
ALTER TABLE  disbursement ADD CONSTRAINT fk_disbursement_ref_document_code FOREIGN KEY (document_code_id) REFERENCES ref_document_code(document_code_id);
ALTER TABLE  disbursement ADD CONSTRAINT fk_disbursement_vendor_history FOREIGN KEY (vendor_history_id) REFERENCES vendor_history(vendor_history_id);
-- ALTER TABLE  disbursement ADD constraint fk_disbursement_etl_data_load foreign key (created_load_id) references etl_data_load (load_id);
ALTER TABLE  disbursement ADD constraint fk_disbursement_ref_date foreign key (record_date_id) references ref_date (date_id);
ALTER TABLE  disbursement ADD constraint fk_disbursement_ref_date_1 foreign key (check_eft_issued_date_id) references ref_date (date_id);
ALTER TABLE  disbursement ADD constraint fk_disbursement_ref_date_2 foreign key (check_eft_record_date_id) references ref_date (date_id);


 ALTER TABLE  disbursement_line_item ADD constraint fk_disbursement_line_item_expenditure foreign key (disbursement_id) references disbursement (disbursement_id);
 ALTER TABLE  disbursement_line_item ADD constraint fk_disbursement_line_item_ref_fund_class foreign key (fund_class_id) references ref_fund_class (fund_class_id);
 ALTER TABLE  disbursement_line_item ADD constraint fk_disbursement_line_item_ref_agency_history FOREIGN KEY (agency_history_id) REFERENCES ref_agency_history(agency_history_id);
 ALTER TABLE  disbursement_line_item ADD constraint fk_disbursement_line_item_ref_department_history FOREIGN KEY (department_history_id) REFERENCES ref_department_history(department_history_id);
 ALTER TABLE  disbursement_line_item ADD constraint fk_disb_line_item_ref_exp_object_history foreign key (expenditure_object_history_id) references ref_expenditure_object_history (expenditure_object_history_id);
 ALTER TABLE  disbursement_line_item ADD constraint fk_disbursement_line_item_ref_budget_code foreign key (budget_code_id) references ref_budget_code (budget_code_id);
 ALTER TABLE  disbursement_line_item ADD constraint fk_disbursement_line_item_agreement foreign key (agreement_id) references history_agreement (agreement_id);
 -- ALTER TABLE  disbursement_line_item ADD constraint fk_disbursement_line_item_etl_data_load foreign key (created_load_id) references etl_data_load (load_id);
 ALTER TABLE  disbursement_line_item ADD constraint fk_disbursement_line_item_ref_location_history foreign key (location_history_id) references ref_location_history (location_history_id);


CREATE TABLE disbursement_line_item_details(
	disbursement_line_item_id bigint,
	disbursement_id integer,
	line_number integer,
	disbursement_number character varying(40),
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
	master_contract_number character varying,
	master_child_contract_number character varying,
  	contract_vendor_id integer,
  	contract_vendor_id_cy integer,
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
	file_type char(1),
	load_id integer,
	last_modified_date timestamp without time zone,
	job_id bigint
)
DISTRIBUTED BY (disbursement_line_item_id);

 ----------------------------------------------------------------------------------------------------------------------------------------
 
 /* aggregate tables  */

CREATE TABLE ref_spending_category (
  spending_category_id smallint NOT NULL ,
  spending_category_code character varying(2),
  spending_category_name character varying(100),
  display_name character varying(100),
  display_order smallint
  ) DISTRIBUTED BY (spending_category_id) ;
  
  
CREATE TABLE aggregateon_spending_coa_entities (
	department_id integer,
	agency_id smallint,
	spending_category_id smallint,
	expenditure_object_id integer,
	vendor_id integer,
	month_id int,
	year_id smallint,
	type_of_year char(1),
	total_spending_amount numeric(16,2),
	total_disbursements integer
	) DISTRIBUTED BY (department_id) ;

CREATE TABLE aggregateon_spending_contract (
    agreement_id bigint,
    document_id character varying(20),
    document_code character varying(8),
	vendor_id integer,
	agency_id smallint,
	description character varying(60),
	spending_category_id smallint,
	year_id smallint,
	type_of_year char(1),
	total_spending_amount numeric(16,2), 
	total_contract_amount numeric(16,2)
	) DISTRIBUTED BY (agreement_id) ;

	
CREATE TABLE aggregateon_spending_vendor (
	vendor_id integer,
	agency_id smallint,
	spending_category_id smallint,
	year_id smallint,
	type_of_year char(1),
	total_spending_amount numeric(16,2), 
	total_contract_amount numeric(16,2),
	is_all_categories char(1)
	) DISTRIBUTED BY (vendor_id) ;

CREATE TABLE aggregateon_spending_vendor_exp_object(
	vendor_id integer,
	expenditure_object_id integer,
	spending_category_id smallint,
	year_id smallint,
	type_of_year char(1),
	total_spending_amount numeric(16,2) )
DISTRIBUTED BY (expenditure_object_id);	

	
CREATE TABLE ref_fiscal_period(
	fiscal_period smallint,
	fiscal_period_name varchar
)
DISTRIBUTED BY (fiscal_period);

CREATE TABLE ref_pay_frequency(
	pay_frequency_id smallint,
	pay_frequency varchar
)
DISTRIBUTED BY (pay_frequency);



CREATE TABLE aggregateon_revenue_category_funding_class(
                revenue_category_code character varying,
                revenue_category_id smallint,
                funding_class_id smallint,
                funding_class_code character varying,
                agency_id  smallint,
                budget_fiscal_year_id smallint,
                posting_amount numeric(16,2),
                adopted_amount numeric(16,2),
                current_modified_amount numeric(16,2))
DISTRIBUTED BY (agency_id);	

CREATE TABLE aggregateon_revenue_category_funding_by_year(
                revenue_category_code character varying,
                revenue_category_id smallint,
                funding_class_id smallint,
                funding_class_code character varying,
                agency_id smallint,
                budget_fiscal_year_id smallint,
                posting_amount_cy numeric(16,2),
                posting_amount_ny numeric(16,2),
                posting_amount_ny_1 numeric(16,2),
                posting_amount numeric(16,2),
                other_amount numeric(16,2),
                remaining_amount numeric(16,2),
                current_modified_amount numeric(16,2))
DISTRIBUTED BY (agency_id);


CREATE TABLE ref_amount_basis (
  amount_basis_id smallint PRIMARY KEY,
  amount_basis_name varchar(50) ,
  created_date timestamp 
) DISTRIBUTED BY (amount_basis_id);

CREATE TABLE employee (
  employee_id bigint PRIMARY KEY DEFAULT nextval('seq_employee_employee_id'::regclass) NOT NULL,
  employee_number varchar,
  agency_id smallint,
  first_name varchar,
  last_name varchar,
  initial varchar,
  original_first_name varchar,
  original_last_name varchar,
  original_initial varchar,
  masked_name varchar,
  civil_service_title varchar,
  civil_service_code varchar(5),
  civil_service_level varchar(2),
  civil_service_suffix varchar(2),
  created_date timestamp,
  updated_date timestamp,
  created_load_id int,
  updated_load_id int
  )
  DISTRIBUTED BY (employee_id);
  
CREATE TABLE employee_history (
  employee_history_id bigint PRIMARY KEY DEFAULT nextval('seq_employee_history_employee_history_id'::regclass) NOT NULL,
  employee_id int,
  first_name varchar,
  last_name varchar,
  initial varchar,
  masked_name varchar,
  civil_service_title varchar,
  civil_service_code varchar(5),
  civil_service_level varchar(2),
  civil_service_suffix varchar(2),
  created_date timestamp,
  created_load_id int
  )
  DISTRIBUTED BY (employee_history_id);
  ALTER TABLE  employee_history ADD constraint fk_employee_history_employee foreign key (employee_id) references employee (employee_id);

CREATE TABLE payroll(
	payroll_id bigint PRIMARY KEY DEFAULT nextval('seq_payroll_payroll_id'::regclass) NOT NULL,
	pay_cycle_code CHAR(1),
	pay_date_id int,
	employee_history_id bigint,
	employee_number varchar(10),
	payroll_number varchar,
	job_sequence_number varchar,
	agency_history_id smallint,
	fiscal_year smallint,
	agency_start_date date,
	orig_pay_date_id int,
	pay_frequency varchar,
	department_history_id int,
	annual_salary_original numeric(16,2),
	annual_salary numeric(16,2),
	amount_basis_id smallint,
	base_pay_original numeric(16,2),
	base_pay numeric(16,2),
	overtime_pay_original numeric(16,2),
	overtime_pay numeric(16,2),
	other_payments_original numeric(16,2),
	other_payments numeric(16,2),
	gross_pay_original  numeric(16,2),
	gross_pay  numeric(16,2),
	civil_service_title varchar,
	salaried_amount numeric(16,2),
	non_salaried_amount numeric(16,2),
	orig_pay_cycle_code CHAR(1),
	agency_id smallint,
	agency_code varchar,
	agency_name varchar,
	department_id integer,
	department_code  varchar,
	department_name varchar,
	employee_id bigint,
	employee_name varchar,
	fiscal_year_id smallint,
	pay_date date,
	gross_pay_ytd numeric(16,2),
	calendar_fiscal_year_id smallint,
	calendar_fiscal_year smallint,
	gross_pay_cytd numeric(16,2),
	agency_short_name varchar,
	department_short_name varchar,
	created_date timestamp,
	created_load_id int,
	updated_date timestamp,
	updated_load_id int,
	job_id bigint)
DISTRIBUTED BY (payroll_id);	

ALTER TABLE  payroll ADD constraint fk_payroll_ref_date foreign key (pay_date_id) references ref_date (date_id);
ALTER TABLE  payroll ADD constraint fk_payroll_employee_history foreign key (employee_history_id) references employee_history (employee_history_id);
ALTER TABLE  payroll ADD constraint fk_payroll_ref_agency_history foreign key (agency_history_id) references ref_agency_history (agency_history_id);
ALTER TABLE  payroll ADD constraint fk_payroll_ref_date_1 foreign key (orig_pay_date_id) references ref_date (date_id);
ALTER TABLE  payroll ADD constraint fk_payroll_ref_department_history foreign key (department_history_id) references ref_department_history (department_history_id);
ALTER TABLE  payroll ADD constraint fk_payroll_ref_amount_basis foreign key (amount_basis_id) references ref_amount_basis (amount_basis_id);

CREATE TABLE payroll_future_data(
	payroll_id bigint NOT NULL,
	pay_cycle_code CHAR(1),
	pay_date_id int,
	employee_history_id bigint,
	employee_number varchar(10),
	payroll_number varchar,
	job_sequence_number varchar,
	agency_history_id smallint,
	fiscal_year smallint,
	agency_start_date date,
	orig_pay_date_id int,
	pay_frequency varchar,
	department_history_id int,
	annual_salary_original numeric(16,2),
	annual_salary numeric(16,2),
	amount_basis_id smallint,
	base_pay_original numeric(16,2),
	base_pay numeric(16,2),
	overtime_pay_original numeric(16,2),
	overtime_pay numeric(16,2),
	other_payments_original numeric(16,2),
	other_payments numeric(16,2),
	gross_pay_original  numeric(16,2),
	gross_pay  numeric(16,2),
	civil_service_title varchar,
	salaried_amount numeric(16,2),
	non_salaried_amount numeric(16,2),
	orig_pay_cycle_code CHAR(1),
	agency_id smallint,
	agency_code varchar,
	agency_name varchar,
	department_id integer,
	department_code  varchar,
	department_name varchar,
	employee_id bigint,
	employee_name varchar,
	fiscal_year_id smallint,
	pay_date date,
	gross_pay_ytd numeric(16,2),
	calendar_fiscal_year_id smallint,
	calendar_fiscal_year smallint,
	gross_pay_cytd numeric(16,2),
	agency_short_name varchar,
	department_short_name varchar,
	created_date timestamp,
	created_load_id int,
	updated_date timestamp,
	updated_load_id int,
	job_id bigint)
DISTRIBUTED BY (payroll_id);


CREATE TABLE aggregateon_payroll_employee_agency(
	employee_id bigint,
	agency_id smallint,
	fiscal_year_id smallint,
	type_of_year char(1),
	pay_frequency varchar,
	type_of_employment varchar,
	employee_number varchar(10),
	start_date date,	
	annual_salary numeric(16,2),
	base_pay numeric(16,2),
	overtime_pay numeric(16,2),
	other_payments numeric(16,2),
	gross_pay numeric(16,2) )
DISTRIBUTED BY (employee_id);

CREATE TABLE aggregateon_payroll_agency(	
	agency_id smallint,	
	fiscal_year_id smallint,
	type_of_year char(1),
	base_pay numeric(16,2),
	other_payments numeric(16,2),
	gross_pay numeric(16,2),
	overtime_pay numeric(16,2),
	total_employees int,
	total_salaried_employees int,
	total_hourly_employees int,
	total_overtime_employees int,
	annual_salary numeric(16,2))
DISTRIBUTED BY (agency_id);


CREATE TABLE aggregateon_payroll_coa_month(	
	agency_id smallint,
	department_id integer,
	fiscal_year_id smallint,
	month_id int,
	type_of_year char(1),	
	base_pay numeric(16,2),
	overtime_pay numeric(16,2),
	other_payments numeric(16,2),
	gross_pay numeric(16,2) )
DISTRIBUTED BY (agency_id);


CREATE TABLE aggregateon_payroll_year(	
	fiscal_year_id smallint,
	type_of_year char(1),	
	total_employees int,
	total_salaried_employees int,
	total_hourly_employees int,
	total_overtime_employees int)
DISTRIBUTED BY (fiscal_year_id);

/* payroll aggregate tables for month_id */


CREATE TABLE aggregateon_payroll_employee_agency_month(
	employee_id bigint,
	agency_id smallint,
	fiscal_year_id smallint,
	type_of_year char(1),
	month_id int,
	pay_frequency varchar,
	type_of_employment varchar,
	employee_number varchar(10),
	start_date date,	
	annual_salary numeric(16,2),
	base_pay numeric(16,2),
	overtime_pay numeric(16,2),
	other_payments numeric(16,2),
	gross_pay numeric(16,2) )
DISTRIBUTED BY (employee_id);

CREATE TABLE aggregateon_payroll_agency_month(	
	agency_id smallint,	
	fiscal_year_id smallint,
	type_of_year char(1),
	month_id int,
	base_pay numeric(16,2),
	other_payments numeric(16,2),
	gross_pay numeric(16,2),
	overtime_pay numeric(16,2),
	total_employees int,
	total_salaried_employees int,
	total_hourly_employees int,
	total_overtime_employees int,
	annual_salary numeric(16,2))
DISTRIBUTED BY (agency_id);


CREATE TABLE aggregateon_payroll_year_and_month(	
	fiscal_year_id smallint,
	type_of_year char(1),	
	month_id int,
	total_employees int,
	total_salaried_employees int,
	total_hourly_employees int,
	total_overtime_employees int)
DISTRIBUTED BY (fiscal_year_id);


	
CREATE TABLE agreement_snapshot_cy (LIKE agreement_snapshot) DISTRIBUTED BY (original_agreement_id);

CREATE TABLE agreement_snapshot_deleted (
  agreement_id bigint NOT NULL,
  original_agreement_id bigint NOT NULL,
  starting_year smallint,
  master_agreement_yn character(1),
  load_id integer,
  deleted_date timestamp without time zone,
  job_id bigint
) DISTRIBUTED BY (agreement_id);

CREATE TABLE agreement_snapshot_cy_deleted (
  agreement_id bigint NOT NULL,
  original_agreement_id bigint NOT NULL,
  starting_year smallint,
  master_agreement_yn character(1),
  load_id integer,
  deleted_date timestamp without time zone,
  job_id bigint
) DISTRIBUTED BY (agreement_id);


CREATE TABLE deleted_agreement_accounting_line (LIKE history_agreement_accounting_line) DISTRIBUTED BY (agreement_id);
ALTER TABLE deleted_agreement_accounting_line ADD COLUMN deleted_date timestamp, ADD COLUMN deleted_load_id int;

 CREATE TABLE pending_contracts(
 	document_code_id smallint,
 	document_agency_id  smallint,
 	document_id  varchar,
  	parent_document_code_id smallint,
  	parent_document_agency_id  smallint,
 	parent_document_id  varchar,
 	encumbrance_amount_original numeric(15,2),
 	encumbrance_amount numeric(15,2),
 	original_maximum_amount_original numeric(15,2),
 	original_maximum_amount numeric(15,2),
 	revised_maximum_amount_original numeric(15,2),
 	revised_maximum_amount numeric(15,2),
 	registered_contract_max_amount numeric(15,2),
 	vendor_legal_name varchar(80),
 	vendor_customer_code varchar(20),
 	vendor_id int,
 	description varchar(78),
 	submitting_agency_id  smallint,
 	oaisis_submitting_agency_desc	 varchar(50),
 	submitting_agency_code	 varchar(4),
 	awarding_agency_id  smallint,
 	oaisis_awarding_agency_desc	 varchar(50),
 	awarding_agency_code	 varchar(4),
 	contract_type_name varchar(40),
 	cont_type_code  varchar(2),
 	award_method_name varchar(50),
 	award_method_code varchar(3),
 	award_method_id smallint,
 	start_date date,
 	end_date date,
 	revised_start_date date,
 	revised_end_date date,
 	cif_received_date date,
 	cif_fiscal_year smallint,
 	cif_fiscal_year_id smallint,
 	tracking_number varchar(30),
 	board_award_number varchar(15),
 	oca_number varchar(10),
	version_number varchar(5),
	fms_contract_number varchar,
	contract_number varchar,
	fms_parent_contract_number varchar,
	submitting_agency_name varchar,
	submitting_agency_short_name varchar,
	awarding_agency_name varchar,
	awarding_agency_short_name varchar,
	start_date_id int,
	end_date_id int,	
	revised_start_date_id int,
	revised_end_date_id int,	
	cif_received_date_id int,
	document_agency_code varchar, 
	document_agency_name varchar, 
	document_agency_short_name varchar ,
	funding_agency_id  smallint,
	funding_agency_code varchar, 
	funding_agency_name varchar, 
	funding_agency_short_name varchar ,
	original_agreement_id bigint,
	original_master_agreement_id bigint,
	dollar_difference numeric(16,2),
  	percent_difference numeric(17,4),
	original_or_modified varchar,
	original_or_modified_desc varchar,
	award_size_id smallint,
	award_category_id smallint,
	industry_type_id smallint,
	document_version smallint,
	latest_flag char(1)
 ) DISTRIBUTED BY (document_code_id);
 
-- Contract Aggregate Tables

CREATE TABLE agreement_snapshot_expanded(
	original_agreement_id bigint,
	agreement_id bigint,
	fiscal_year smallint,
	description varchar,
	contract_number varchar,
	vendor_id int,
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
	master_agreement_id bigint,
	master_agreement_yn character(1),
	status_flag char(1)
	)
DISTRIBUTED BY (original_agreement_id);	


CREATE TABLE agreement_snapshot_expanded_cy(
	original_agreement_id bigint,
	agreement_id bigint,
	fiscal_year smallint,
	description varchar,
	contract_number varchar,
	vendor_id int,
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
	master_agreement_id bigint,
	master_agreement_yn character(1),
	status_flag char(1)
	)
DISTRIBUTED BY (original_agreement_id);	


CREATE TABLE mid_aggregateon_disbursement_spending_year(
	original_agreement_id bigint,
	fiscal_year smallint,
	fiscal_year_id smallint,
	check_amount numeric(16,2),
	type_of_year char(1),
	master_agreement_yn character(1))
DISTRIBUTED BY (original_agreement_id) 	;

CREATE TABLE aggregateon_contracts_cumulative_spending(
	original_agreement_id bigint,
	fiscal_year smallint,
	fiscal_year_id smallint,
	document_code_id smallint,
	master_agreement_yn character(1),
	description varchar,
	contract_number varchar,
	vendor_id int,
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
) DISTRIBUTED BY (vendor_id);	


CREATE TABLE aggregateon_contracts_spending_by_month(
	original_agreement_id bigint,
	fiscal_year smallint,
	fiscal_year_id smallint,
	document_code_id smallint,
	month_id integer,
	vendor_id int,
	award_method_id smallint,
	agency_id smallint,
	industry_type_id smallint,
    award_size_id smallint,
	spending_amount numeric(16,2),
	status_flag char(1),
	type_of_year char(1)	
) DISTRIBUTED BY (vendor_id);	



CREATE TABLE aggregateon_total_contracts(
	fiscal_year smallint,
	fiscal_year_id smallint,
	vendor_id int,
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
) DISTRIBUTED BY (fiscal_year);	


CREATE TABLE aggregateon_contracts_department(
	document_code_id smallint,
	document_agency_id smallint,
	agency_id smallint,
	department_id integer,
	fiscal_year smallint,
	fiscal_year_id smallint,
	award_method_id smallint,
	vendor_id int,
	industry_type_id smallint,
    award_size_id smallint,
	spending_amount_disb numeric(16,2),
	spending_amount numeric(16,2),
	total_contracts integer,
	status_flag char(1),
	type_of_year char(1)
) DISTRIBUTED BY (department_id);	

CREATE TABLE contracts_spending_transactions(
	disbursement_line_item_id bigint,
	original_agreement_id bigint,
	fiscal_year smallint,
	fiscal_year_id smallint,
	document_code_id smallint,
	vendor_id int,
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
	status_flag char(1),
	type_of_year char(1)
) DISTRIBUTED BY (disbursement_line_item_id);	


CREATE TABLE aggregateon_contracts_expense(
	original_agreement_id bigint,
	expenditure_object_code character varying(4),
	expenditure_object_name character varying(40),
	encumbered_amount numeric(16,2),
	spending_amount_disb numeric(16,2),
	spending_amount numeric(16,2),
	is_disbursements_exist char(1)
) DISTRIBUTED BY (original_agreement_id);	

-- tables for contracts by industry and contracts by size

CREATE TABLE ref_industry_type (
  industry_type_id smallint primary key,
  industry_type_name VARCHAR(50),
  created_date timestamp
);


CREATE TABLE ref_award_size (
  award_size_id smallint primary key,
  award_size_name VARCHAR(50),
  created_date timestamp
);


 CREATE TABLE ref_award_category_industry (
  award_category_industry_id smallint PRIMARY KEY default nextval('seq_ref_award_category_industry_award_category_industry_id'),
  award_category_code varchar(10) default null,
  industry_type_id smallint default null,
  created_date timestamp
) DISTRIBUTED BY (award_category_industry_id);


CREATE TABLE invalid_records (
load_file_id bigint,
total_invalid_records bigint,
invalid_reason varchar(255)
) ;

CREATE TABLE aggregateon_budget_by_year
(
agency_id integer,
department_id integer,
department_code varchar,
budget_fiscal_year smallint,
budget_fiscal_year_id smallint,
object_class_id integer,
modified_budget_amount numeric(20,2),
modified_budget_amount_py numeric(20,2),
modified_budget_amount_py_1 numeric(20,2),
modified_budget_amount_py_2 numeric(20,2),
filter_type varchar(10)
) DISTRIBUTED BY (agency_id);


 ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 -- creating indexes
 
 CREATE INDEX idx_date_ref_date ON ref_date(date);
 
 -- payroll indexes on 12/08/2012
 
  CREATE INDEX idx_agency_id_employee ON employee(agency_id);
  CREATE INDEX idx_employee_number_employee ON employee(employee_number);
  CREATE INDEX idx_employee_id_employee_history ON employee_history(employee_id);
  CREATE INDEX idx_employee_id_payroll ON payroll(employee_id);
  CREATE INDEX idx_agency_id_payroll ON payroll(agency_id);
  CREATE INDEX idx_employee_number_payroll ON payroll(employee_number);
 