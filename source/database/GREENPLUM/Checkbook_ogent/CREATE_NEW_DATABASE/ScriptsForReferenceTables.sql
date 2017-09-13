COPY etl.ref_data_source FROM '/home/gpadmin/GREENPLUM/Checkbook_ogent/CREATE_NEW_DATABASE/ref_data_source.csv' CSV HEADER QUOTE as '"';

COPY etl.ref_column_mapping FROM '/home/gpadmin/GREENPLUM/Checkbook_ogent/CREATE_NEW_DATABASE/ref_column_mapping.csv' CSV HEADER QUOTE as '"';

COPY etl.ref_validation_rule FROM '/home/gpadmin/GREENPLUM/Checkbook_ogent/CREATE_NEW_DATABASE/ref_validation_rule.csv' CSV HEADER QUOTE as '"';

COPY etl.ref_file_name_pattern FROM '/home/gpadmin/GREENPLUM/Checkbook_ogent/CREATE_NEW_DATABASE/ref_file_name_pattern.csv' CSV HEADER QUOTE as '"';

COPY etl.aggregate_tables FROM '/home/gpadmin/GREENPLUM/Checkbook_ogent/CREATE_NEW_DATABASE/widget_aggregate_tables_edc.csv' CSV HEADER QUOTE as '"';

---------------------------------------------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION etl.initializedate(p_start_date_in date, p_end_date_in date) RETURNS INT AS $$
DECLARE
	l_no_of_days int;
	l_no_of_years int;
BEGIN
	l_no_of_days := p_end_date_in - p_start_date_in;

	RAISE NOTICE 'l_no_of_days %',l_no_of_days;

	IF l_no_of_days > 0 THEN

		INSERT INTO ref_year(year_value)
		SELECT s.a as year_value
		FROM GENERATE_SERIES(EXTRACT(year from p_start_date_in)::int,(EXTRACT(year from p_end_date_in)::int)+1,1) as s(a);

		INSERT INTO ref_month(month_value, month_name,year_id)
		SELECT EXTRACT(month from p_start_date_in) + series_month.month as month_value,
			 to_char(to_timestamp(to_char(EXTRACT(month from p_start_date_in) + series_month.month, '99'), 'MM'), 'Month') as month_name,
			ref_year.year_id
		FROM GENERATE_SERIES(0,11,1) as series_month(month)
		     CROSS JOIN generate_series(EXTRACT(year from p_start_date_in)::int,EXTRACT(year from p_end_date_in)::int,1) as series_year(year)
		     JOIN ref_year ON series_year.year = ref_year.year_value;

		INSERT INTO ref_date(date,nyc_year_id,calendar_month_id)
		SELECT dates,b.year_id,c.month_id
		FROM
			(
			SELECT p_start_date_in + series_days.day_count as dates , (CASE WHEN EXTRACT(MONTH FROM p_start_date_in + series_days.day_count) >= 7 THEN extract(year from p_start_date_in + series_days.day_count)+1
						ELSE EXTRACT(YEAR FROM p_start_date_in + series_days.day_count) END) as year_value,
						EXTRACT(MONTH FROM p_start_date_in + series_days.day_count) as calendar_month,
						EXTRACT(YEAR FROM p_start_date_in + series_days.day_count) as calendar_year
			FROM   generate_series(1,l_no_of_days,1) as series_days(day_count)
			) inner_tbl JOIN ref_year b ON inner_tbl.year_value = b.year_value
			JOIN ref_month c ON  inner_tbl.calendar_month = c.month_value
			JOIN ref_year d ON inner_tbl.calendar_year = d.year_value AND d.year_id = c.year_id ;

		RETURN 1;
	ELSE
		RETURN 2;
	END IF;

	UPDATE ref_month
	SET display_order = (CASE WHEN month_value = 7 THEN 1
				  WHEN month_value = 8 THEN 2
				  WHEN month_value = 9 THEN 3
				  WHEN month_value = 10 THEN 4
				  WHEN month_value = 11 THEN 5
				  WHEN month_value = 12 THEN 6
				  WHEN month_value = 1 THEN 7
				  WHEN month_value = 2 THEN 8
				  WHEN month_value = 3 THEN 9
				  WHEN month_value = 4 THEN 10
				  WHEN month_value = 5 THEN 11
				  WHEN month_value = 6 THEN 12
				  END);


	UPDATE ref_month
	SET month_short_name = (CASE WHEN month_value = 1 THEN 'JAN'
				  WHEN month_value = 2 THEN 'FEB'
				  WHEN month_value = 3 THEN 'MAR'
				  WHEN month_value = 4 THEN 'APR'
				  WHEN month_value = 5 THEN 'MAY'
				  WHEN month_value = 6 THEN 'JUN'
				  WHEN month_value = 7 THEN 'JUL'
				  WHEN month_value = 8 THEN 'AUG'
				  WHEN month_value = 9 THEN 'SEP'
				  WHEN month_value = 10 THEN 'OCT'
				  WHEN month_value = 11 THEN 'NOV'
				  WHEN month_value = 12 THEN 'DEC'
				  END);

EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in etl.initializedate';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;

	RETURN 0;
END;
$$ language plpgsql;

--------------------------------------------------------------------------------------------------------------------------------

INSERT INTO ref_address_type(address_type_code,address_type_name,created_date) VALUES('BI','Billing',now()::timestamp),
											('PA','Payment',now()::timestamp),
											('PR','Ordering',now()::timestamp),
											('WR','Account Administrator',now()::timestamp),
											('OT','Other',now()::timestamp);

update ref_address_type set created_date = now()::timestamp;

INSERT INTO ref_business_type(business_type_code,business_type_name,created_date) values ('EENT','Emerging Enterprises Business',now()::timestamp),
				     ('EXMP','Exempt From MWBE Rpt Card',now()::timestamp),
				     ('LOCB','Local Business',now()::timestamp),
				     ('MNRT','Minority Owned',now()::timestamp),
				     ('WMNO','Woman Owned',now()::timestamp);

update ref_business_type set created_date = now()::timestamp;

INSERT INTO ref_minority_type values (1,'Unspecified MWBE',now()::timestamp),
				     (2,'African American',now()::timestamp),
				     (3,'Hispanic American',now()::timestamp),
				     (4,'Asian-Pacific',now()::timestamp),
				     (5,'Asian-Indian',now()::timestamp),
				     (6,'Native',now()::timestamp),
				     (7,'Non-Minority',now()::timestamp),
				     (8,'Other',now()::timestamp),
				     (9,'Caucasian Woman',now()::timestamp),
				     (10,'Asian American',now()::timestamp),
				     (11,'Individuals & Others',now()::timestamp);


update ref_minority_type set created_date = now()::timestamp;

INSERT INTO ref_business_type_status values (1,'Requested',now()::timestamp),
					(2,'Accepted',now()::timestamp),
					(3,'Rejected',now()::timestamp);

update ref_business_type_status set created_date = now()::timestamp;


/* CREATE TABLE etl.stg_funding_class(
	fy int,
	funding_class_code varchar(5),
	name varchar(52),
	short_name varchar(50),
	category_name varchar(52),
	cty_fund_fl int,
	intr_cty_fl int,
	fund_aloc_req_fl int,
	tbl_last_dt varchar(20),
	ams_row_vers_no char(1),
	rsfcls_nm_up  varchar(52),
	fund_category  varchar(50));

COPY etl.stg_funding_class FROM '/home/gpadmin/GREENPLUM/Checkbook_ogent/CREATE_NEW_DATABASE/FundingClass.txt' DELIMITER AS '|' ESCAPE '~' FILL MISSING FIELDS;


INSERT INTO   ref_funding_class(funding_class_code,funding_class_name,funding_class_short_name,category_name,city_fund_flag,intra_city_flag,fund_allocation_required_flag,category_code,created_date)
SELECT funding_class_code,name,short_name,category_name,
	(case when cty_fund_fl='1' then 1::bit else 0::bit end),
	(case when intr_cty_fl ='1' then 1::bit else 0::bit end),
	(case when fund_aloc_req_fl='1' then 1::bit else 0::bit end)  ,fund_category,
	now()::timestamp
from etl.stg_funding_class;

*/
select etl.initializedate('1900-01-01'::date,'2200-12-31'::date);

	UPDATE ref_month
	SET display_order = (CASE WHEN month_value = 7 THEN 1
				  WHEN month_value = 8 THEN 2
				  WHEN month_value = 9 THEN 3
				  WHEN month_value = 10 THEN 4
				  WHEN month_value = 11 THEN 5
				  WHEN month_value = 12 THEN 6
				  WHEN month_value = 1 THEN 7
				  WHEN month_value = 2 THEN 8
				  WHEN month_value = 3 THEN 9
				  WHEN month_value = 4 THEN 10
				  WHEN month_value = 5 THEN 11
				  WHEN month_value = 6 THEN 12
				  END);

	UPDATE ref_month
	SET month_short_name = (CASE WHEN month_value = 1 THEN 'JAN'
				  WHEN month_value = 2 THEN 'FEB'
				  WHEN month_value = 3 THEN 'MAR'
				  WHEN month_value = 4 THEN 'APR'
				  WHEN month_value = 5 THEN 'MAY'
				  WHEN month_value = 6 THEN 'JUN'
				  WHEN month_value = 7 THEN 'JUL'
				  WHEN month_value = 8 THEN 'AUG'
				  WHEN month_value = 9 THEN 'SEP'
				  WHEN month_value = 10 THEN 'OCT'
				  WHEN month_value = 11 THEN 'NOV'
				  WHEN month_value = 12 THEN 'DEC'
				  END);
--------------------------------------------------------------------------------------------------------------------------------------------

COPY etl.stg_award_method FROM '/home/gpadmin/GREENPLUM/Checkbook_ogent/CREATE_NEW_DATABASE/AwardMethod.csv' CSV QUOTE as '"' ;

INSERT INTO ref_award_method(award_method_code,award_method_name,created_date) SELECT  award_method_code,award_method_name,now()::timestamp  FROM etl.stg_award_method;

COPY etl.stg_agreement_type FROM '/home/gpadmin/GREENPLUM/Checkbook_ogent/CREATE_NEW_DATABASE/AgreementType.csv' DELIMITER AS ',' ;

insert into ref_agreement_type(agreement_type_code,agreement_type_name,created_date) SELECT agreement_type_code,name,now()::timestamp from etl.stg_agreement_type;


COPY etl.stg_award_category FROM '/home/gpadmin/GREENPLUM/Checkbook_ogent/CREATE_NEW_DATABASE/AgreementCategory.csv' CSV QUOTE as '"' ;

INSERT INTO ref_award_category(award_category_code,award_category_name,created_date) SELECT award_category_code, award_method_name,now()::timestamp  from etl.stg_award_category;


INSERT INTO ref_document_code(document_code,document_name,created_date) VALUES ('CT1','General Contract',now()::timestamp),
										('CTA1', 'Multiple Award Contract',now()::timestamp),
										('CTA2','Consortium Contract',now()::timestamp),
										('DO1', 'Delivery Order',now()::timestamp),
										('MA1', 'Master agreement',now()::timestamp),
										('MMA1','Multiple Award Master Agreement',now()::timestamp),
										('RCT1',NULL,now()::timestamp),
										('MAC1',NULL,now()::timestamp),
										('POC','Commodity Purchase Order -Small Purchase',now()::timestamp),
										('POD','Commodity Purchase Order -Micro Purchase',now()::timestamp),
										('PCC1','Commodity Purchase Order -Small Purchase Construction',now()::timestamp),
										('AD','Automated Disbursement',now()::timestamp),
										('MD1','Manual Disbursement',now()::timestamp),
										('MD2','Manual Disbursement specific for SCA',now()::timestamp),
										('OD1','Online Disbursement',now()::timestamp),
										('OD2','Online Disbursement specific for SCA',now()::timestamp),
										('EFT','EFT',now()::timestamp),
										('N/A','N/A (PRIVACY/SECURITY)',now()::timestamp),
										('MAR','MAR',now()::timestamp),
										('CTR','CTR',now()::timestamp);

update ref_document_code set created_date = now()::timestamp;

INSERT INTO ref_miscellaneous_vendor(vendor_customer_code,created_date) values ('JUDGCLAIMS',now()::timestamp),('MISCPAYVEN',now()::timestamp);

update ref_miscellaneous_vendor set created_date = now()::timestamp;


INSERT INTO ref_spending_category(spending_category_id, spending_category_code, spending_category_name, display_name, display_order)
values(1,'c','Contracts', 'Contract Spending', 4),(2,'p','Payroll', 'Payroll Spending', 2),(3,'cc','Capital Contracts', 'Capital Spending', 3),
(4,'o','Others','Other Spending', 6),(5,'ta','Trust & Agency','Trust & Agency Spending', 5), (6,'ts','Total', 'Total Spending', 1);

INSERT INTO ref_fiscal_period VALUES (1,'July'),
				      (2, 'August'),
				      (3, 'September'),
				      (4, 'October'),
				      (5, 'November'),
				      (6, 'December'),
				      (7, 'January'),
				      (8, 'February'),
				      (9, 'March'),
				      (10, 'April'),
				      (11, 'May'),
				      (12, 'June'),
				      (13,'Post Adjustment Closing')
				      ;

INSERT INTO ref_pay_frequency VALUES (1,'BI-WEEKLY'),
				      (2, 'SEMI-MONTHLY'),
				      (3, 'SUPPLEMENTAL'),
				      (4, 'WEEKLY'),
				      (5, 'DAILY'),
				      (6, 'HOURLY');


-- Contract by ondustry and contract by size

INSERT INTO ref_industry_type(industry_type_id, industry_type_name,created_date) VALUES(1,'Construction Services',now()::timestamp),
																						(2,'Goods',now()::timestamp),
																						(3,'Professional Services',now()::timestamp),
																						(4,'Standardized Services',now()::timestamp),
																						(5,'Not Classified',now()::timestamp);
update ref_industry_type set created_date = now()::timestamp;

INSERT INTO ref_award_size(award_size_id, award_size_name,created_date) VALUES(1,'Greater than $1 Million',now()::timestamp),
																						(2,'Between $100,000 and $1 Million',now()::timestamp),
																						(3,'Between $5,000 and $100,000',now()::timestamp),
																						(4,'Less than $5,000',now()::timestamp),
																						(5,'Unclassified',now()::timestamp);

update ref_award_size set created_date = now()::timestamp;


COPY etl.stg_award_category_industry FROM '/home/gpadmin/GREENPLUM/Checkbook_ogent/CREATE_NEW_DATABASE/AgreementIndustryCategory.csv' CSV QUOTE as '"' ;

INSERT INTO ref_award_category_industry(award_category_code,industry_type_id,created_date) SELECT award_category_code, industry_type_id,now()::timestamp  from etl.stg_award_category_industry;

-- Dummy values
insert into vendor(vendor_id,vendor_customer_code,legal_name,alias_name,miscellaneous_vendor_flag) values(nextval('seq_vendor_vendor_id'),'N/A','N/A (PRIVACY/SECURITY)','N/A (PRIVACY/SECURITY)',0::bit);
insert into vendor_history(vendor_history_id,vendor_id,legal_name,alias_name,miscellaneous_vendor_flag)
select nextval('seq_vendor_history_vendor_history_id'),vendor_id,legal_name,alias_name,miscellaneous_vendor_flag
from vendor where vendor_customer_code='N/A'
and legal_name='N/A (PRIVACY/SECURITY)';

INSERT INTO address(address_id,address_line_1 ,address_line_2,city,	state ,zip ,country)
VALUES(nextval('seq_address_address_id'), 'N/A (PRIVACY/SECURITY)', 'N/A (PRIVACY/SECURITY)', 'N/A (PRIVACY/SECURITY)', 'N/A (PRIVACY/SECURITY)', 'N/A (PRIVACY/SECURITY)', 'N/A (PRIVACY/SECURITY)');

INSERT INTO vendor_address(vendor_address_id, address_id, address_type_id, created_date)
SELECT nextval('seq_vendor_address_vendor_address_id'), address_id, 2, now()::timestamp
FROM address where address_line_1 = 'N/A (PRIVACY/SECURITY)';

UPDATE vendor_address a
SET vendor_history_id = b.vendor_history_id
FROM vendor_history b WHERE b.legal_name = 'N/A (PRIVACY/SECURITY)';


ALTER SEQUENCE seq_address_address_id RESTART WITH 5;
ALTER SEQUENCE seq_vendor_vendor_id RESTART WITH 5;
ALTER SEQUENCE seq_vendor_history_vendor_history_id RESTART WITH 5;
ALTER SEQUENCE seq_vendor_address_vendor_address_id RESTART WITH 5;

INSERT INTO ref_expenditure_object(expenditure_object_id, expenditure_object_code, expenditure_object_name, fiscal_year, original_expenditure_object_name, created_date, created_load_id)
VALUES(nextval('seq_ref_expenditure_object_expendtiure_object_id'),'!PS!','Payroll Summary',2009, 'Payroll Summary', now()::timestamp, 0);

INSERT INTO ref_expenditure_object(expenditure_object_id, expenditure_object_code, expenditure_object_name, fiscal_year, original_expenditure_object_name, created_date, created_load_id)
VALUES(nextval('seq_ref_expenditure_object_expendtiure_object_id'),'!PS!','Payroll Summary',2010, 'Payroll Summary', now()::timestamp, 0);

INSERT INTO ref_expenditure_object(expenditure_object_id, expenditure_object_code, expenditure_object_name, fiscal_year, original_expenditure_object_name, created_date, created_load_id)
VALUES(nextval('seq_ref_expenditure_object_expendtiure_object_id'),'!PS!','Payroll Summary',2011, 'Payroll Summary', now()::timestamp, 0);

INSERT INTO ref_expenditure_object(expenditure_object_id, expenditure_object_code, expenditure_object_name, fiscal_year, original_expenditure_object_name, created_date, created_load_id)
VALUES(nextval('seq_ref_expenditure_object_expendtiure_object_id'),'!PS!','Payroll Summary',2012, 'Payroll Summary', now()::timestamp, 0);

INSERT INTO ref_expenditure_object(expenditure_object_id, expenditure_object_code, expenditure_object_name, fiscal_year, original_expenditure_object_name, created_date, created_load_id)
VALUES(nextval('seq_ref_expenditure_object_expendtiure_object_id'),'!PS!','Payroll Summary',2013, 'Payroll Summary', now()::timestamp, 0);

INSERT INTO ref_expenditure_object(expenditure_object_id, expenditure_object_code, expenditure_object_name, fiscal_year, original_expenditure_object_name, created_date, created_load_id)
VALUES(nextval('seq_ref_expenditure_object_expendtiure_object_id'),'!PS!','Payroll Summary',2014, 'Payroll Summary', now()::timestamp, 0);


INSERT INTO ref_expenditure_object_history(expenditure_object_history_id, expenditure_object_id, expenditure_object_name, fiscal_year, created_date, load_id)
SELECT nextval('seq_ref_expenditure_object_history_id'), expenditure_object_id, expenditure_object_name, fiscal_year, created_date, created_load_id
FROM ref_expenditure_object WHERE expenditure_object_code = '!PS!' ;



/*insert into ref_award_status(Award_status_name) select distinct cntrc_sta from etl.stg_con_ct_header where coalesce(cntrc_sta,0) <> 0;

insert into ref_document_function_code(document_function_code_id) select distinct doc_func_cd::int from etl.stg_con_ct_header where coalesce(doc_func_cd::int,0) <> 0;

insert into ref_procurement_type(procurement_type_id) select distinct PRCU_TYP_ID from etl.stg_con_ct_header where coalesce(PRCU_TYP_ID,0) <> 0;

insert into ref_award_level(award_level_code) select distinct AWD_LVL_CD from etl.stg_con_ct_award_detail where coalesce(AWD_LVL_CD,'') <> '';

insert into ref_event_type(event_type_code) select distinct EVNT_TYP_ID from etl.stg_con_ct_accounting_line where coalesce(EVNT_TYP_ID,'')<>'';

insert into ref_commodity_type (commodity_type_id ) select distinct LN_TYP from etl.stg_con_ct_commodity where coalesce(ln_typ,0)<>0;

insert into ref_worksite(worksite_code)  select distinct wk_site_cd_01 from etl.stg_con_ct_award_detail where coalesce(wk_site_cd_01,'') <> '';

insert into ref_expenditure_status(expenditure_status_id) values (1),(4);

insert into ref_expenditure_cancel_type(expenditure_cancel_type_id) values (1),(8);

insert into ref_expenditure_cancel_reason(expenditure_cancel_reason_id) values (9),(11);
*/

-----------------------------------------------------------------------------------------------
/*PMS*/
INSERT INTO ref_amount_basis(amount_basis_id,amount_basis_name) VALUES (1,'ANNUAL'),(2,'DAILY'),(3,'HOURLY');



/*ref_fund_class*/

 INSERT INTO ref_fund_class(fund_class_code,created_load_id) VALUES('360',2);
 INSERT INTO ref_fund_class(fund_class_code,fund_class_name,created_load_id) VALUES('001','General Fund',2);
 INSERT INTO ref_fund_class(fund_class_code,created_load_id) VALUES('510',2);
 INSERT INTO ref_fund_class(fund_class_code,created_load_id) VALUES('850',2);
 INSERT INTO ref_fund_class(fund_class_code,created_load_id) VALUES('320',2);
 INSERT INTO ref_fund_class(fund_class_code,fund_class_name,created_load_id) VALUES('400','Captial Fund',2);
 INSERT INTO ref_fund_class(fund_class_code,created_load_id) VALUES('853',2);
 INSERT INTO ref_fund_class(fund_class_code,fund_class_name,created_load_id) VALUES('402','Capital Fund',2);
 INSERT INTO ref_fund_class(fund_class_code,created_load_id) VALUES('852',2);
 INSERT INTO ref_fund_class(fund_class_code,fund_class_name,created_load_id) VALUES('700','Contracts',2);
 INSERT INTO ref_fund_class(fund_class_code,fund_class_name,created_load_id) VALUES('702','Contracts',2);
 INSERT INTO ref_fund_class(fund_class_code,created_load_id) VALUES('520',2);
 INSERT INTO ref_fund_class(fund_class_code,created_load_id) VALUES('701',2);
 update ref_fund_class set created_date = now()::timestamp;



 INSERT INTO ref_agency(agency_id, agency_code, agency_name, original_agency_name, created_date, agency_short_name) VALUES(nextval('seq_ref_agency_agency_id'),'N/A','N/A (PRIVACY/SECURITY)','N/A (PRIVACY/SECURITY)', now()::timestamp, 'N/A');

INSERT INTO ref_agency_history(agency_history_id, agency_id, agency_name, created_date) SELECT nextval('seq_ref_agency_history_id'),agency_id, agency_name,now()::timestamp FROM ref_agency WHERE agency_code = 'N/A';

ALTER SEQUENCE seq_ref_agency_agency_id RESTART WITH 9000;
ALTER SEQUENCE seq_ref_agency_history_id RESTART WITH 9000;
ALTER SEQUENCE seq_ref_department_department_id RESTART WITH 900000;
ALTER SEQUENCE seq_ref_department_history_id RESTART WITH 900000;

INSERT INTO ref_agency(agency_id, agency_code, agency_name, original_agency_name, created_date, agency_short_name, is_display, is_oge_agency) VALUES(nextval('seq_ref_agency_agency_id'),'z81','NEW YORK CITY ECONOMIC DEVELOPMENT CORPORATION','NEW YORK CITY ECONOMIC DEVELOPMENT CORPORATION', now()::timestamp, 'NYC EDC','Y', 'Y');

INSERT INTO ref_agency_history(agency_history_id, agency_id, agency_name, created_date) SELECT nextval('seq_ref_agency_history_id'),agency_id, agency_name,now()::timestamp FROM ref_agency WHERE agency_code = 'z81';

INSERT INTO ref_agency(agency_id, agency_code, agency_name, original_agency_name, created_date, agency_short_name, is_display, is_oge_agency) VALUES(nextval('seq_ref_agency_agency_id'),'z82','NYC TECHNOLOGY DEVELOPMENT CORPORATION','NYC TECHNOLOGY DEVELOPMENT CORPORATION', now()::timestamp, 'NYC TDC','N', 'Y');

INSERT INTO ref_agency_history(agency_history_id, agency_id, agency_name, created_date) SELECT nextval('seq_ref_agency_history_id'),agency_id, agency_name,now()::timestamp FROM ref_agency WHERE agency_code = 'z82';


INSERT INTO ref_department(department_id,department_code,department_name,department_short_name,agency_id,fund_class_id,fiscal_year,created_date,original_department_name)
SELECT nextval('seq_ref_department_department_id'),'110' as department_code, 'NYCEDC' as department_name, 'NYCEDC' as department_short_name,b.agency_id,c.fund_class_id,2014 as fiscal_year,now()::timestamp, 'NYCEDC' as department_name
FROM   ( select agency_id from ref_agency where agency_code = 'z81') b, (select fund_class_id from ref_fund_class  where fund_class_code = '400') c;

INSERT INTO ref_department_history(department_history_id,department_id,department_name,department_short_name,agency_id,fund_class_id,fiscal_year,created_date)
SELECT nextval('seq_ref_department_history_id'),a.department_id,a.department_name,a.department_short_name,b.agency_id,a.fund_class_id,a.fiscal_year,now()::timestamp
FROM ref_department a , ref_agency b WHERE a.agency_id = b.agency_id AND b.agency_code = 'z81' and a.department_code = '110';

INSERT INTO ref_department(department_id,department_code,department_name,department_short_name,agency_id,fund_class_id,fiscal_year,created_date,original_department_name)
SELECT nextval('seq_ref_department_department_id'),'111' as department_code, 'NYCTDC' as department_name, 'NYCTDC' as department_short_name,b.agency_id,c.fund_class_id,2014 as fiscal_year,now()::timestamp, 'NYCTDC' as department_name
FROM   ( select agency_id from ref_agency where agency_code = 'z82') b, (select fund_class_id from ref_fund_class  where fund_class_code = '400') c;

INSERT INTO ref_department_history(department_history_id,department_id,department_name,department_short_name,agency_id,fund_class_id,fiscal_year,created_date)
SELECT nextval('seq_ref_department_history_id'),a.department_id,a.department_name,a.department_short_name,b.agency_id,a.fund_class_id,a.fiscal_year,now()::timestamp
FROM ref_department a , ref_agency b WHERE a.agency_id = b.agency_id AND b.agency_code = 'z82' and a.department_code = '111';

INSERT INTO ref_subcontract_status (scntrc_status, scntrc_status_name, display_flag, sort_order)
VALUES
  (2, 'Yes',                   1, 1),
  (3, 'No',                    1, 2),
  (1, 'No Data Entered',       1, 3),
  (4, 'Not Required',          1, 4),
  (0, 'Prior Year Exclusions', 0, 5),
  (5, 'N/A',                   0, 6)
;
