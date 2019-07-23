
--table for last job processed

DROP TABLE IF EXISTS mwbe_last_job CASCADE;

CREATE table mwbe_last_job (
use char(10),
job_id integer 
) DISTRIBUTED BY (use);

-- This table should be given access to mwbe etl user to update etl job_id in order to get incremental files. The value is to be set to 0 if a complete load is needed


insert into mwbe_last_job values ('etl',0); -- this statement should be commented out after first time execution



--agency
/*CREATE OR REPLACE VIEW ref_agency_mwbe AS 
 
SELECT a.agency_id, a.agency_name, a.agency_code, a.created_load_id
   FROM ONLY ref_agency a, mwbe_last_job b, etl.etl_data_load c
WHERE c.data_source_code::text = 'A'::text AND c.load_id = a.created_load_id AND b.job_id >= c.job_id;*/

-- Only agencies which are in fms and payroll summary are to be displayed

   CREATE OR REPLACE VIEW ref_agency_mwbe
AS
   SELECT distinct c.agency_id as DepartmentID,
	  c.agency_name as DeptName ,
          c.agency_code as DeptCode,
          coalesce(c.updated_load_id,c.created_load_id) as load_id
     FROM (SELECT DISTINCT (agency_id)
             FROM disbursement_line_item_details) a
                   JOIN ref_agency c
             ON a.agency_id = c.agency_id;


--department
/*CREATE VIEW ref_department_mwbe AS
SELECT   department_name, department_code, a.agency_id,e.agency_code, d.fund_class_code, fiscal_year, original_department_name, a.created_load_id, a.updated_load_id FROM ONLY ref_department a, 

mwbe_last_job b,etl.etl_data_load c,ref_fund_class d,ref_agency e where c.data_source_code ='A'  and c.load_id =a.created_load_id and b.job_id >=c.job_id and a.fund_class_id = d.fund_class_id and a.agency_id = 

e.agency_id;
*/


CREATE OR REPLACE VIEW ref_department_mwbe
AS
   SELECT distinct a.department_id as AppropriationUnitID,b.department_code as AppropriationUnitCode,
                 b.department_name as AppropriationUnitName,c.agency_code as DeptCode, 
	d.fund_class_code as FundClassCode,b.fiscal_year as FiscalYear,
coalesce(b.updated_load_id,b.created_load_id) AS load_id        
    FROM (SELECT DISTINCT (department_id)
             FROM disbursement_line_item_details) a
          JOIN ref_department b
             ON a.department_id = b.department_id
          JOIN ref_agency c
             ON b.agency_id = c.agency_id
          JOIN ref_fund_class d
             ON d.fund_class_id = b.fund_class_id;






--expenditure
/*CREATE VIEW ref_expenditure_mwbe AS
select  expenditure_object_code,expenditure_object_name,fiscal_year,original_expenditure_object_name,(case when created_load_id>updated_load_id then created_load_id else updated_load_id end) as load_id from 

ref_expenditure_object;*/


CREATE OR REPLACE VIEW ref_expenditure_mwbe
AS
   SELECT c.expenditure_object_id as ExpenditureObjID,c.expenditure_object_code as ExpenditureObjCode,
	  c.expenditure_object_name as ExpenditureObjName,c.fiscal_year as FiscalYear,
        coalesce(c.updated_load_id,c.created_load_id) AS LoadID
     FROM (SELECT DISTINCT (expenditure_object_id)
             FROM disbursement_line_item_details) a
          JOIN ref_expenditure_object c
             ON a.expenditure_object_id = c.expenditure_object_id;


--payroll

/* CREATE VIEW  payroll_mwbe AS
 	SELECT a.payroll_id,pay_cycle_code,
		a.payroll_number,a.job_sequence_number,b.agency_id,a.fiscal_year,
		a.orig_pay_date_id,a.pay_frequency,a.department_history_id,a.annual_salary,
		a.orig_pay_cycle_code,a.agency_code,a.agency_name,a.department_id,
		a.department_code,a.department_name,a.employee_id,a.employee_name,a.fiscal_year_id,
		a.pay_date,a.gross_pay_ytd,a.calendar_fiscal_year_id,a.calendar_fiscal_year,a.gross_pay_cytd,
		a.created_date,a.created_load_id,a.updated_date,
		a.updated_load_id
 	FROM	payroll a join ref_agency b on a.agency_id = b.agency_id
	,mwbe_last_job c 
	where a.created_load_id >=c.job_id or a.updated_load_id >= c.job_id;	
*/

   CREATE OR REPLACE VIEW  payroll_mwbe AS
SELECT payroll_summary_id as DisbursementID,payroll_number , pay_cycle_code,total_amount as LineItemAmount,6 AS CategoryID,
'Individuals and Others'::varchar(50) as CategoryName,b.agency_id,b.agency_name,d.date,
       c.department_id as AppropriationUnitID, c.department_name as AppropriationUnitName, coalesce(a.updated_load_id,a.created_load_id) as load_id
  FROM payroll_summary a
       JOIN ref_agency_history b
          ON a.agency_history_id = b.agency_history_id
       JOIN ref_department_history c
          ON a.department_history_id = c.department_history_id
       JOIN ref_date d
          ON a.pay_date_id = d.date_id
       JOIN etl.etl_data_load e
          ON e.load_id = coalesce(a.updated_load_id,a.created_load_id)
        --where e.job_id > (select max(job_id) from mwbe_last_job);
        -- where e.publish_start_time::date >= '2013-07-01';
         -- WHERE d.date >= '2013-01-18' ;
         WHERE e.job_id > (select max(job_id) from mwbe_last_job);

--disbursement

/*CREATE VIEW disbursement_mwbe
AS
 SELECT disbursement_id as DisbursementID,check_eft_issued_date_id as DisbursementDate,disbursement_line_item_id as LineItemNum,
          check_amount as LineItemAmount,
          agency_id  as DepartmentID,
          check_eft_issued_date as Check_Date,
          department_id as appropriationUnitID,vendor_id,location_code,
          agreement_id, expenditure_object_name,
          expenditure_object_code,contract_number,contract_vendor_id,contract_agency_id
          fiscal_year,a.load_id
     FROM disbursement_line_item_details a JOIN (SELECT data_source_code, max(load_id) AS load_id
                  FROM    etl.etl_data_load c
                       JOIN
                          mwbe_last_job d
                       ON c.job_id >= d.job_id
                 WHERE data_source_code = 'F'
                GROUP BY 1) b
             ON b.load_id = a.load_id;

CREATE   OR REPLACE VIEW  disbursement_mwbe
AS
select 
document_id as DocID,disbursement_id as DisbursementID,rd.date as DisbursementDate,disbursement_line_item_id as LineItemNum,
          check_amount as LineItemAmount,
          agency_id  as DepartmentID,
          agency_name as DeptName,
          agency_code as DeptCode,
          'D'::char(1) as RecordType,
          a.vendor_id as VendorId,b.vendor_customer_code as VendorCode,(case when b.miscellaneous_vendor_flag = '1' then b.vendor_id else 0 end) as vendor_sub_code ,b.legal_name::varchar(500) as VendorName,
	  (case when c.business_type_id =2 then 6 
	   when c.business_type_id = 5 then 1
           else c.minority_type_id end) as MinoritytypeId,
          (case when c.business_type_id = 2 then 'Native' 
	   			when c.business_type_id = 5 then 'Unspecified MWBE'
           		else d.minority_type_name end) as MinorityGroup,
           	c.business_type_id as BusinessTypeId,
           	i.business_type_code as BusinessTypeCode,
           	i.business_type_name as BusinessTypeName,
          department_id as AppropriationUnitID,
          department_code as AppropriationUnitCode,
          location_code,
          expenditure_object_id,
          expenditure_object_name,
          expenditure_object_code,
          fiscal_year,a.load_id,
          a.contract_number::varchar(25) AS AgreementID,
          split_part(a.disbursement_number, '-', 2)::integer as DocumentVersion,
          split_part(a.disbursement_number, '-', 3) as DocDeptCode,
          split_part(a.disbursement_number, '-', 4) as DocCode,
          a.line_number as LineNumber
 from disbursement_line_item_details a left join  vendor b on a.vendor_id =b.vendor_id 
				left join (select vendor_customer_code , business_type_id ,minority_type_id from fmsv_business_type 
				where status =2 and (business_type_id=2 or minority_type_id is not null or (vendor_customer_code in 
					(select distinct vendor_customer_code from fmsv_business_type 
					  where  business_type_id = 5 and status = 2 and vendor_customer_code not in (select distinct vendor_customer_code from fmsv_business_type where minority_type_id is not null)) AND business_type_id=5)))c
				 on b.vendor_customer_code =c.vendor_customer_code
				left join ref_minority_type d on c.minority_type_id = d.minority_type_id 
				left join ref_business_type i on c.business_type_id = i.business_type_id
				JOIN etl.etl_data_load e ON e.load_id = a.load_id
				JOIN ref_date rd on a.check_eft_issued_date_id = rd.date_id
				--where e.job_id > (select max(job_id) from mwbe_last_job);
				-- where a.spending_category_id != 2 AND e.publish_start_time::date >= '2013-07-01';
				WHERE a.spending_category_id != 2 AND e.job_id > (select max(job_id) from mwbe_last_job);

 
CREATE VIEW disbursement_mwbe
AS         
select 
a.document_id as DocID,a.disbursement_id as DisbursementID,rd.date as DisbursementDate,disbursement_line_item_id as LineItemNum,
          check_amount as LineItemAmount,
          agency_id  as DepartmentID,
          agency_name as DeptName,
          agency_code as DeptCode,
          'D'::char(1) as RecordType,
          a.vendor_id as VendorId,b.vendor_customer_code as VendorCode,(case when b.miscellaneous_vendor_flag = '1' then b.vendor_id else 0 end) as vendor_sub_code ,b.legal_name::varchar(500) as VendorName,
	  (case when c.business_type_id =2 then 6 
	   when c.business_type_id = 5 then 1
           else c.minority_type_id end) as MinoritytypeId,
          (case when c.business_type_id = 2 then 'Native' 
	   			when c.business_type_id = 5 then 'Unspecified MWBE'
           		else d.minority_type_name end) as MinorityGroup,
           	c.business_type_id as BusinessTypeId,
           	i.business_type_code as BusinessTypeCode,
           	i.business_type_name as BusinessTypeName,
          department_id as AppropriationUnitID,
          department_code as AppropriationUnitCode,
          location_code,
          expenditure_object_id,
          expenditure_object_name,
          expenditure_object_code,
          fiscal_year,a.load_id,
          a.contract_number::varchar(25) AS AgreementID,
          split_part(a.disbursement_number, '-', 2)::integer as DocumentVersion,
          split_part(a.disbursement_number, '-', 3) as DocDeptCode,
          split_part(a.disbursement_number, '-', 4) as DocCode,
          a.line_number as LineNumber
 from disbursement disb JOIN disbursement_line_item_details a ON disb.disbursement_id = a.disbursement_id join vendor_history vh ON disb.vendor_history_id = vh.vendor_history_id JOIN vendor b on vh.vendor_id =b.vendor_id 
				left join (select vendor_customer_code , business_type_id ,minority_type_id from fmsv_business_type 
				where status =2 and (business_type_id=2 or minority_type_id is not null or (vendor_customer_code in 
					(select distinct vendor_customer_code from fmsv_business_type 
					  where  business_type_id = 5 and status = 2 and vendor_customer_code not in (select distinct vendor_customer_code from fmsv_business_type where minority_type_id is not null and status = 2)) AND business_type_id=5)))c
				 on b.vendor_customer_code =c.vendor_customer_code
				left join ref_minority_type d on c.minority_type_id = d.minority_type_id 
				left join ref_business_type i on c.business_type_id = i.business_type_id
				JOIN etl.etl_data_load e ON e.load_id = a.load_id
				JOIN ref_date rd on a.check_eft_issued_date_id = rd.date_id
				--where e.job_id > (select max(job_id) from mwbe_last_job);
				-- where a.spending_category_id != 2 AND e.publish_start_time::date >= '2013-07-01';
				WHERE a.spending_category_id != 2 AND disb.privacy_flag = 'F' AND e.job_id > (select max(job_id) from mwbe_last_job)
  UNION ALL
 select 
a.document_id as DocID,a.disbursement_id as DisbursementID,rd.date as DisbursementDate,disbursement_line_item_id as LineItemNum,
          check_amount as LineItemAmount,
          agency_id  as DepartmentID,
          agency_name as DeptName,
          agency_code as DeptCode,
          'D'::char(1) as RecordType,
          a.vendor_id as VendorId,b.vendor_customer_code as VendorCode,(case when b.miscellaneous_vendor_flag = '1' then b.vendor_id else 0 end) as vendor_sub_code ,b.legal_name::varchar(500) as VendorName,
            (case when disb.bustype_exmp = 'EXMP' AND disb.bustype_exmp_status = 2 then 6
			when disb.bustype_mnrt = 'MNRT' AND disb.bustype_mnrt_status = 2 then d.minority_type_id 
			WHEN disb.bustype_wmno = 'WMNO' AND disb.bustype_wmno_status = 2 then 1
           		else NULL end) as MinoritytypeId,
          (case when disb.bustype_exmp = 'EXMP' AND disb.bustype_exmp_status = 2 then 'Native'
			when disb.bustype_mnrt = 'MNRT' AND disb.bustype_mnrt_status = 2 then d.minority_type_name 
			WHEN disb.bustype_wmno = 'WMNO' AND disb.bustype_wmno_status = 2 then 'Unspecified MWBE'
           		else NULL end) as MinorityGroup,
           	 (case when disb.bustype_exmp = 'EXMP' AND disb.bustype_exmp_status = 2 then 2
			when disb.bustype_mnrt = 'MNRT' AND disb.bustype_mnrt_status = 2 then 4
			WHEN disb.bustype_wmno = 'WMNO' AND disb.bustype_wmno_status = 2 then 5
			when disb.bustype_locb = 'LOCB' AND disb.bustype_locb_status = 2 then 3
			WHEN disb.bustype_eent = 'EENT' AND disb.bustype_eent_status = 2 then 1
           		else NULL end) as BusinessTypeId,
           	(case when disb.bustype_exmp = 'EXMP' AND disb.bustype_exmp_status = 2 then 'EXMP'
			when disb.bustype_mnrt = 'MNRT' AND disb.bustype_mnrt_status = 2 then 'MNRT'
			WHEN disb.bustype_wmno = 'WMNO' AND disb.bustype_wmno_status = 2 then 'WMNO'
			when disb.bustype_locb = 'LOCB' AND disb.bustype_locb_status = 2 then 'LOCB'
			WHEN disb.bustype_eent = 'EENT' AND disb.bustype_eent_status = 2 then 'EENT'
           		else NULL end) as BusinessTypeCode,
         	(case when disb.bustype_exmp = 'EXMP' AND disb.bustype_exmp_status = 2 then 'Exempt From MWBE Rpt Card'
			when disb.bustype_mnrt = 'MNRT' AND disb.bustype_mnrt_status = 2 then 'Minority Owned'
			WHEN disb.bustype_wmno = 'WMNO' AND disb.bustype_wmno_status = 2 then 'Woman Owned'
			when disb.bustype_locb = 'LOCB' AND disb.bustype_locb_status = 2 then 'Local Business'
			WHEN disb.bustype_eent = 'EENT' AND disb.bustype_eent_status = 2 then 'Emerging Enterprises Business'
           		else NULL end) as BusinessTypeName,
          department_id as AppropriationUnitID,
          department_code as AppropriationUnitCode,
          location_code,
          expenditure_object_id,
          expenditure_object_name,
          expenditure_object_code,
          fiscal_year,a.load_id,
          a.contract_number::varchar(25) AS AgreementID,
          split_part(a.disbursement_number, '-', 2)::integer as DocumentVersion,
          split_part(a.disbursement_number, '-', 3) as DocDeptCode,
          split_part(a.disbursement_number, '-', 4) as DocCode,
          a.line_number as LineNumber
 from disbursement disb JOIN disbursement_line_item_details a ON disb.disbursement_id = a.disbursement_id left join  vendor b on a.vendor_id =b.vendor_id 
				left join ref_minority_type d on disb.minority_type_id = d.minority_type_id 
				JOIN etl.etl_data_load e ON e.load_id = a.load_id
				JOIN ref_date rd on a.check_eft_issued_date_id = rd.date_id
				--where e.job_id > (select max(job_id) from mwbe_last_job);
				-- where a.spending_category_id != 2 AND e.publish_start_time::date >= '2013-07-01';
				WHERE a.spending_category_id != 2 AND disb.privacy_flag = 'P' AND e.job_id > (select max(job_id) from mwbe_last_job) ;
				


         CREATE OR REPLACE VIEW disbursement_mwbe
AS         
select 
a.document_id as DocID,a.disbursement_id as DisbursementID,rd.date as DisbursementDate,disbursement_line_item_id as LineItemNum,
          check_amount as LineItemAmount,
          agency_id  as DepartmentID,
          agency_name as DeptName,
          agency_code as DeptCode,
          'D'::char(1) as RecordType,
          a.vendor_id as VendorId,b.vendor_customer_code as VendorCode,(case when b.miscellaneous_vendor_flag = '1' then b.vendor_id else 0 end) as vendor_sub_code ,b.legal_name::varchar(500) as VendorName,
	  (case when coalesce(f.business_type_id,c.business_type_id) =2 then 11 
	   when coalesce(f.business_type_id,c.business_type_id) = 5 then 9
           else coalesce(f.minority_type_id,c.minority_type_id) end) as MinoritytypeId,
          (case when coalesce(f.business_type_id,c.business_type_id) = 2 then 'Individuals & Others' 
	   			when coalesce(f.business_type_id,c.business_type_id) = 5 then 'Caucasian Woman'
           		else coalesce(g.minority_type_name,d.minority_type_name) end) as MinorityGroup,
           	coalesce(f.business_type_id,c.business_type_id) as BusinessTypeId,
           	coalesce(h.business_type_code, i.business_type_code) as BusinessTypeCode,
           	coalesce(h.business_type_name, i.business_type_name) as BusinessTypeName,
          department_id as AppropriationUnitID,
          department_code as AppropriationUnitCode,
          location_code,
          expenditure_object_id,
          expenditure_object_name,
          expenditure_object_code,
          fiscal_year,a.load_id,
          a.contract_number::varchar(25) AS AgreementID,
          disb.document_version as DocumentVersion ,
          (CASE WHEN split_part(a.disbursement_number, '-', 2) IN ('1','2') THEN split_part(a.disbursement_number, '-', 3) ELSE split_part(a.disbursement_number, '-', 4) END) as DocDeptCode,
          rdc.document_code as DocCode,
          a.line_number as LineNumber,
          rat.agreement_type_code,
          ram.award_method_code,
          disb.vendor_org_classification
 from disbursement disb JOIN disbursement_line_item_details a ON disb.disbursement_id = a.disbursement_id join vendor_history vh ON disb.vendor_history_id = vh.vendor_history_id JOIN vendor b on vh.vendor_id =b.vendor_id 
				left join (select vendor_customer_code , business_type_id ,minority_type_id from fmsv_business_type 
				where status =2 and (business_type_id=2 or minority_type_id is not null or (vendor_customer_code in 
					(select distinct vendor_customer_code from fmsv_business_type 
					  where  business_type_id = 5 and status = 2 and vendor_customer_code not in (select distinct vendor_customer_code from fmsv_business_type where minority_type_id is not null and status = 2)) AND business_type_id=5)))c
				 on b.vendor_customer_code =c.vendor_customer_code
				left join ref_minority_type d on c.minority_type_id = d.minority_type_id 
				left join ref_business_type i on c.business_type_id = i.business_type_id
				left join (select vendor_history_id , business_type_id ,minority_type_id from vendor_business_type 
				where status =2 and (business_type_id=2 or minority_type_id is not null or (vendor_history_id in 
					(select distinct vendor_history_id from vendor_business_type 
					  where  business_type_id = 5 and status = 2 and vendor_history_id not in (select distinct vendor_history_id from vendor_business_type where minority_type_id is not null and status = 2)) AND business_type_id=5))) f
					  ON disb.vendor_history_id = f.vendor_history_id
				left join ref_minority_type g on f.minority_type_id = g.minority_type_id 
				left join ref_business_type h on f.business_type_id = h.business_type_id
				JOIN etl.etl_data_load e ON e.load_id = a.load_id
				JOIN ref_date rd on a.check_eft_issued_date_id = rd.date_id
				LEFT JOIN (select original_agreement_id, agreement_id,  agreement_type_id, award_method_id FROM history_agreement where original_version_flag = 'Y') hag ON hag.original_agreement_id = a.agreement_id
				LEFT JOIN ref_agreement_type rat ON rat.agreement_type_id = hag.agreement_type_id
				LEFT JOIN ref_award_method ram ON ram.award_method_id = hag.award_method_id
				LEFT JOIN ref_document_code rdc ON disb.document_code_id = rdc.document_code_id
				-- where a.spending_category_id != 2 AND disb.privacy_flag = 'F' AND e.publish_start_time::date >= '2013-07-01';
				WHERE a.spending_category_id != 2 AND disb.privacy_flag = 'F' AND e.job_id > (select max(job_id) from mwbe_last_job)
  UNION ALL
 select 
a.document_id as DocID,a.disbursement_id as DisbursementID,rd.date as DisbursementDate,disbursement_line_item_id as LineItemNum,
          check_amount as LineItemAmount,
          agency_id  as DepartmentID,
          agency_name as DeptName,
          agency_code as DeptCode,
          'D'::char(1) as RecordType,
          a.vendor_id as VendorId,b.vendor_customer_code as VendorCode,(case when b.miscellaneous_vendor_flag = '1' then b.vendor_id else 0 end) as vendor_sub_code ,b.legal_name::varchar(500) as VendorName,
            (case when disb.bustype_exmp = 'EXMP' AND disb.bustype_exmp_status = 2 then 11
			when disb.bustype_mnrt = 'MNRT' AND disb.bustype_mnrt_status = 2 then d.minority_type_id 
			WHEN disb.bustype_wmno = 'WMNO' AND disb.bustype_wmno_status = 2 then 9
           		else NULL end) as MinoritytypeId,
          (case when disb.bustype_exmp = 'EXMP' AND disb.bustype_exmp_status = 2 then 'Individuals & Others'
			when disb.bustype_mnrt = 'MNRT' AND disb.bustype_mnrt_status = 2 then d.minority_type_name 
			WHEN disb.bustype_wmno = 'WMNO' AND disb.bustype_wmno_status = 2 then 'Caucasian Woman'
           		else NULL end) as MinorityGroup,
           	 (case when disb.bustype_exmp = 'EXMP' AND disb.bustype_exmp_status = 2 then 2
			when disb.bustype_mnrt = 'MNRT' AND disb.bustype_mnrt_status = 2 then 4
			WHEN disb.bustype_wmno = 'WMNO' AND disb.bustype_wmno_status = 2 then 5
			when disb.bustype_locb = 'LOCB' AND disb.bustype_locb_status = 2 then 3
			WHEN disb.bustype_eent = 'EENT' AND disb.bustype_eent_status = 2 then 1
           		else NULL end) as BusinessTypeId,
           	(case when disb.bustype_exmp = 'EXMP' AND disb.bustype_exmp_status = 2 then 'EXMP'
			when disb.bustype_mnrt = 'MNRT' AND disb.bustype_mnrt_status = 2 then 'MNRT'
			WHEN disb.bustype_wmno = 'WMNO' AND disb.bustype_wmno_status = 2 then 'WMNO'
			when disb.bustype_locb = 'LOCB' AND disb.bustype_locb_status = 2 then 'LOCB'
			WHEN disb.bustype_eent = 'EENT' AND disb.bustype_eent_status = 2 then 'EENT'
           		else NULL end) as BusinessTypeCode,
         	(case when disb.bustype_exmp = 'EXMP' AND disb.bustype_exmp_status = 2 then 'Exempt From MWBE Rpt Card'
			when disb.bustype_mnrt = 'MNRT' AND disb.bustype_mnrt_status = 2 then 'Minority Owned'
			WHEN disb.bustype_wmno = 'WMNO' AND disb.bustype_wmno_status = 2 then 'Woman Owned'
			when disb.bustype_locb = 'LOCB' AND disb.bustype_locb_status = 2 then 'Local Business'
			WHEN disb.bustype_eent = 'EENT' AND disb.bustype_eent_status = 2 then 'Emerging Enterprises Business'
           		else NULL end) as BusinessTypeName,
          department_id as AppropriationUnitID,
          department_code as AppropriationUnitCode,
          location_code,
          expenditure_object_id,
          expenditure_object_name,
          expenditure_object_code,
          fiscal_year,a.load_id,
          a.contract_number::varchar(25) AS AgreementID,
          disb.document_version as DocumentVersion,
          (CASE WHEN split_part(a.disbursement_number, '-', 2) IN ('1','2') THEN split_part(a.disbursement_number, '-', 3) ELSE split_part(a.disbursement_number, '-', 4) END) as DocDeptCode,
          rdc.document_code as DocCode,
          a.line_number as LineNumber,
          rat.agreement_type_code,
          ram.award_method_code,
          disb.vendor_org_classification
 from disbursement disb JOIN disbursement_line_item_details a ON disb.disbursement_id = a.disbursement_id left join  vendor b on a.vendor_id =b.vendor_id 
				left join ref_minority_type d on disb.minority_type_id = d.minority_type_id 
				JOIN etl.etl_data_load e ON e.load_id = a.load_id
				JOIN ref_date rd on a.check_eft_issued_date_id = rd.date_id
				LEFT JOIN (select original_agreement_id, agreement_id,  agreement_type_id, award_method_id FROM history_agreement where original_version_flag = 'Y') hag ON hag.original_agreement_id = a.agreement_id
				LEFT JOIN ref_agreement_type rat ON rat.agreement_type_id = hag.agreement_type_id
				LEFT JOIN ref_award_method ram ON ram.award_method_id = hag.award_method_id
				LEFT JOIN ref_document_code rdc ON disb.document_code_id = rdc.document_code_id
				-- where a.spending_category_id != 2 AND disb.privacy_flag = 'P' AND e.publish_start_time::date >= '2013-07-01';
				WHERE a.spending_category_id != 2 AND disb.privacy_flag = 'P' AND e.job_id > (select max(job_id) from mwbe_last_job) ;
  */
         
  CREATE OR REPLACE VIEW disbursement_mwbe
AS         
select 
a.document_id as DocID,a.disbursement_id as DisbursementID,rd.date as DisbursementDate,disbursement_line_item_id as LineItemNum,
          check_amount as LineItemAmount,
          agency_id  as DepartmentID,
          agency_name as DeptName,
          agency_code as DeptCode,
          'D'::char(1) as RecordType,
          a.vendor_id as VendorId,b.vendor_customer_code as VendorCode,(case when b.miscellaneous_vendor_flag = '1' then b.vendor_id else 0 end) as vendor_sub_code ,b.legal_name::varchar(500) as VendorName,
	  (case when f.business_type_id =2 then 11 
	   when f.business_type_id = 5 then 9
           else f.minority_type_id end) as MinoritytypeId,
          (case when f.business_type_id = 2 then 'Individuals & Others' 
	   			when f.business_type_id = 5 then 'Caucasian Woman'
           		else g.minority_type_name end) as MinorityGroup,
           	f.business_type_id as BusinessTypeId,
           	h.business_type_code as BusinessTypeCode,
           	h.business_type_name as BusinessTypeName,
          department_id as AppropriationUnitID,
          department_code as AppropriationUnitCode,
          location_code,
          expenditure_object_id,
          expenditure_object_name,
          expenditure_object_code,
          fiscal_year,a.load_id,
          a.contract_number::varchar(25) AS AgreementID,
          disb.document_version as DocumentVersion ,
          (CASE WHEN split_part(a.disbursement_number, '-', 2) IN ('1','2') THEN split_part(a.disbursement_number, '-', 3) ELSE split_part(a.disbursement_number, '-', 4) END) as DocDeptCode,
          rdc.document_code as DocCode,
          a.line_number as LineNumber,
          rat.agreement_type_code,
          ram.award_method_code,
          disb.vendor_org_classification
 from disbursement disb JOIN disbursement_line_item_details a ON disb.disbursement_id = a.disbursement_id join vendor_history vh ON disb.vendor_history_id = vh.vendor_history_id JOIN vendor b on vh.vendor_id =b.vendor_id 
				left join (select vendor_history_id , business_type_id ,minority_type_id from vendor_business_type 
				where status =2 and (business_type_id=2 or minority_type_id is not null or (vendor_history_id in 
					(select distinct vendor_history_id from vendor_business_type 
					  where  business_type_id = 5 and status = 2 and vendor_history_id not in (select distinct vendor_history_id from vendor_business_type where minority_type_id is not null and status = 2)) AND business_type_id=5))) f
					  ON disb.vendor_history_id = f.vendor_history_id
				left join ref_minority_type g on f.minority_type_id = g.minority_type_id 
				left join ref_business_type h on f.business_type_id = h.business_type_id
				JOIN etl.etl_data_load e ON e.load_id = a.load_id
				JOIN ref_date rd on a.check_eft_issued_date_id = rd.date_id
				LEFT JOIN (select original_agreement_id, agreement_id,  agreement_type_id, award_method_id FROM history_agreement where original_version_flag = 'Y') hag ON hag.original_agreement_id = a.agreement_id
				LEFT JOIN ref_agreement_type rat ON rat.agreement_type_id = hag.agreement_type_id
				LEFT JOIN ref_award_method ram ON ram.award_method_id = hag.award_method_id
				LEFT JOIN ref_document_code rdc ON disb.document_code_id = rdc.document_code_id
				-- where a.spending_category_id != 2 AND disb.privacy_flag = 'F' AND e.publish_start_time::date >= '2013-07-01';
				WHERE a.spending_category_id != 2 AND disb.privacy_flag = 'F' AND e.job_id > (select max(job_id) from mwbe_last_job)
  UNION ALL
 select 
a.document_id as DocID,a.disbursement_id as DisbursementID,rd.date as DisbursementDate,disbursement_line_item_id as LineItemNum,
          check_amount as LineItemAmount,
          agency_id  as DepartmentID,
          agency_name as DeptName,
          agency_code as DeptCode,
          'D'::char(1) as RecordType,
          a.vendor_id as VendorId,b.vendor_customer_code as VendorCode,(case when b.miscellaneous_vendor_flag = '1' then b.vendor_id else 0 end) as vendor_sub_code ,b.legal_name::varchar(500) as VendorName,
            (case when disb.bustype_exmp = 'EXMP' AND disb.bustype_exmp_status = 2 then 11
			when disb.bustype_mnrt = 'MNRT' AND disb.bustype_mnrt_status = 2 then d.minority_type_id 
			WHEN disb.bustype_wmno = 'WMNO' AND disb.bustype_wmno_status = 2 then 9
           		else NULL end) as MinoritytypeId,
          (case when disb.bustype_exmp = 'EXMP' AND disb.bustype_exmp_status = 2 then 'Individuals & Others'
			when disb.bustype_mnrt = 'MNRT' AND disb.bustype_mnrt_status = 2 then d.minority_type_name 
			WHEN disb.bustype_wmno = 'WMNO' AND disb.bustype_wmno_status = 2 then 'Caucasian Woman'
           		else NULL end) as MinorityGroup,
           	 (case when disb.bustype_exmp = 'EXMP' AND disb.bustype_exmp_status = 2 then 2
			when disb.bustype_mnrt = 'MNRT' AND disb.bustype_mnrt_status = 2 then 4
			WHEN disb.bustype_wmno = 'WMNO' AND disb.bustype_wmno_status = 2 then 5
			when disb.bustype_locb = 'LOCB' AND disb.bustype_locb_status = 2 then 3
			WHEN disb.bustype_eent = 'EENT' AND disb.bustype_eent_status = 2 then 1
           		else NULL end) as BusinessTypeId,
           	(case when disb.bustype_exmp = 'EXMP' AND disb.bustype_exmp_status = 2 then 'EXMP'
			when disb.bustype_mnrt = 'MNRT' AND disb.bustype_mnrt_status = 2 then 'MNRT'
			WHEN disb.bustype_wmno = 'WMNO' AND disb.bustype_wmno_status = 2 then 'WMNO'
			when disb.bustype_locb = 'LOCB' AND disb.bustype_locb_status = 2 then 'LOCB'
			WHEN disb.bustype_eent = 'EENT' AND disb.bustype_eent_status = 2 then 'EENT'
           		else NULL end) as BusinessTypeCode,
         	(case when disb.bustype_exmp = 'EXMP' AND disb.bustype_exmp_status = 2 then 'Exempt From MWBE Rpt Card'
			when disb.bustype_mnrt = 'MNRT' AND disb.bustype_mnrt_status = 2 then 'Minority Owned'
			WHEN disb.bustype_wmno = 'WMNO' AND disb.bustype_wmno_status = 2 then 'Woman Owned'
			when disb.bustype_locb = 'LOCB' AND disb.bustype_locb_status = 2 then 'Local Business'
			WHEN disb.bustype_eent = 'EENT' AND disb.bustype_eent_status = 2 then 'Emerging Enterprises Business'
           		else NULL end) as BusinessTypeName,
          department_id as AppropriationUnitID,
          department_code as AppropriationUnitCode,
          location_code,
          expenditure_object_id,
          expenditure_object_name,
          expenditure_object_code,
          fiscal_year,a.load_id,
          a.contract_number::varchar(25) AS AgreementID,
          disb.document_version as DocumentVersion,
          (CASE WHEN split_part(a.disbursement_number, '-', 2) IN ('1','2') THEN split_part(a.disbursement_number, '-', 3) ELSE split_part(a.disbursement_number, '-', 4) END) as DocDeptCode,
          rdc.document_code as DocCode,
          a.line_number as LineNumber,
          rat.agreement_type_code,
          ram.award_method_code,
          disb.vendor_org_classification
 from disbursement disb JOIN disbursement_line_item_details a ON disb.disbursement_id = a.disbursement_id left join  vendor b on a.vendor_id =b.vendor_id 
				left join ref_minority_type d on disb.minority_type_id = d.minority_type_id 
				JOIN etl.etl_data_load e ON e.load_id = a.load_id
				JOIN ref_date rd on a.check_eft_issued_date_id = rd.date_id
				LEFT JOIN (select original_agreement_id, agreement_id,  agreement_type_id, award_method_id FROM history_agreement where original_version_flag = 'Y') hag ON hag.original_agreement_id = a.agreement_id
				LEFT JOIN ref_agreement_type rat ON rat.agreement_type_id = hag.agreement_type_id
				LEFT JOIN ref_award_method ram ON ram.award_method_id = hag.award_method_id
				LEFT JOIN ref_document_code rdc ON disb.document_code_id = rdc.document_code_id
				-- where a.spending_category_id != 2 AND disb.privacy_flag = 'P' AND e.publish_start_time::date >= '2013-07-01';
				WHERE a.spending_category_id != 2 AND disb.privacy_flag = 'P' AND e.job_id > (select max(job_id) from mwbe_last_job) ;
				
				
-- Need to check with Vinay if Payroll summary data should be excluded while giving disbursement data using the above view.

--vendor
/*
CREATE VIEW vendor_mwbe
AS
   SELECT a.vendor_id as VendorID,a.vendor_customer_code as VendorCode,a.legal_name as VendorName,a.miscellaneous_vendor_flag,
          a.created_load_id as LoadID,c.vendor_business_type_id,d.minority_type_name,
          f.address_line_1 asStreetAddrLine1,f.address_line_2 as StreetAddrLine2,f.city as City,f.state as StateProv,f.zip as PostalCode,f.country as Country
     FROM vendor a
          JOIN (SELECT a.vendor_id,
                       max(vendor_history_id) AS vendor_history_id
                  FROM vendor_history a
                GROUP BY 1) a1
             ON a1.vendor_id = a.vendor_id
          JOIN (SELECT a.vendor_history_id,
                       max(vendor_business_type_id)
                          AS vendor_business_type_id
                  FROM    vendor_history a
                       LEFT JOIN
                          vendor_business_type b
                       ON a.vendor_history_id = b.vendor_history_id
                GROUP BY 1) a2
             ON a1.vendor_history_id = a2.vendor_history_id
          LEFT JOIN vendor_business_type c
             ON a2.vendor_business_type_id = c.vendor_business_type_id
          LEFT JOIN ref_minority_type d
             ON d.minority_type_id = c.minority_type_id
          LEFT JOIN (SELECT a3.vendor_history_id,
                            a3.vendor_address_id,
                            e.address_id
                       FROM    (SELECT a.vendor_history_id,
                                       max(vendor_address_id)
                                          AS vendor_address_id
                                  FROM vendor_address a
                                 WHERE a.address_type_id = 3
                                GROUP BY 1) a3
                            LEFT JOIN
                               vendor_address e
                            ON a3.vendor_address_id = e.vendor_address_id) a4
             ON a2.vendor_history_id = a4.vendor_history_id
          LEFT JOIN address f
             ON f.address_id = a4.address_id;


CREATE OR REPLACE VIEW vendor_mwbe as
SELECT 
a.vendor_id as VendorID,a.vendor_customer_code as VendorCode,a.legal_name as VendorName,a.miscellaneous_vendor_flag,
          a.created_load_id as LoadID,
          f.address_line_1 as StreetAddrLine1,f.address_line_2 as StreetAddrLine2,f.city as City,f.state as StateProv,f.zip as PostalCode,f.country as Country,
          (case when g.business_type_id =2 then 6 
	   			when g.business_type_id = 5 then 1
           		else g.minority_type_id end) as MinoritytypeId,
          h.minority_type_name as MinorityGroup
          FROM vendor a 
		   left join (SELECT a.vendor_id,
                       max(vendor_history_id) AS vendor_history_id
                  FROM vendor_history a
                GROUP BY 1) a1
             ON a1.vendor_id = a.vendor_id
		left join		   
		  (SELECT a2.vendor_history_id,
                            a2.vendor_address_id,
                            e.address_id
                       FROM    (SELECT a.vendor_history_id,
                                       max(vendor_address_id)
                                          AS vendor_address_id
                                  FROM vendor_address a
                                 WHERE a.address_type_id = 2
                                GROUP BY 1) a2
                            LEFT JOIN
                               vendor_address e
                            ON a2.vendor_address_id = e.vendor_address_id) a3
                            on a1.vendor_history_id = a3.vendor_history_id
			left join address f on f.address_id = a3.address_id
			left join (select vendor_customer_code , business_type_id ,minority_type_id from fmsv_business_type 
				where status =2 and (business_type_id=2 or minority_type_id is not null or (vendor_customer_code in 
					(select distinct vendor_customer_code from fmsv_business_type 
					  where  business_type_id = 5 and status = 2 and vendor_customer_code not in (select distinct vendor_customer_code from fmsv_business_type where minority_type_id is not null)) AND business_type_id=5)))g
					 on a.vendor_customer_code =g.vendor_customer_code 
			left join ref_minority_type h on g.minority_type_id = h.minority_type_id  ; 


*/
				
CREATE OR REPLACE VIEW vendor_mwbe as
SELECT 
a.vendor_id as VendorID,a.vendor_customer_code as VendorCode,a.legal_name as VendorName,a.miscellaneous_vendor_flag,
          a.created_load_id as LoadID,
          (case when g.business_type_id =2 then 11
	   			when g.business_type_id = 5 then 9
           		else g.minority_type_id end) as MinoritytypeId,
           	(case when g.business_type_id =2 then 'Individuals & Others' 
	   			when g.business_type_id = 5 then 'Caucasian Woman'
           		else h.minority_type_name end) as MinorityGroup,
           		g.business_type_id as BusinessTypeId,
           		i.business_type_code as BusinessTypeCode,
           		i.business_type_name as BusinessTypeName
          FROM vendor a 
		   	left join (select vendor_customer_code , business_type_id ,minority_type_id from fmsv_business_type 
				where status =2 and (business_type_id=2 or minority_type_id is not null or (vendor_customer_code in 
					(select distinct vendor_customer_code from fmsv_business_type 
					  where  business_type_id = 5 and status = 2 and vendor_customer_code not in (select distinct vendor_customer_code from fmsv_business_type where minority_type_id is not null and status = 2)) AND business_type_id=5)))g
					 on a.vendor_customer_code =g.vendor_customer_code 
			left join ref_minority_type h on g.minority_type_id = h.minority_type_id  
			left join ref_business_type i on g.business_type_id = i.business_type_id; 



--contract 


/*CREATE OR REPLACE VIEW agreement_mwbe AS 
 SELECT COALESCE(a.contract_number, b.contract_number) AS contractid, 
  COALESCE(a.document_id, b.document_id) AS docid,
  COALESCE(a.effective_begin_date_id, b.effective_begin_date_id) AS contract_start_date, 
  COALESCE(a.effective_end_date_id, b.effective_end_date_id) AS contract_end_date, 
  COALESCE(a.description, b.description) AS purpose, a.maximum_contract_amount,
  COALESCE(a.agency_history_id, b.agency_history_id) AS agency_history_id, 
  COALESCE(a.vendor_history_id, b.vendor_history_id) AS vendor_id
FROM history_agreement a
   JOIN history_master_agreement b ON a.master_agreement_id = b.master_agreement_id
   JOIN etl.etl_data_load c ON c.load_id = COALESCE(a.created_load_id, b.created_load_id)
   JOIN mwbe_last_job d ON c.job_id >= d.job_id;
*/

/*

CREATE  OR REPLACE VIEW disbursement_agreement_mwbe AS 
 SELECT COALESCE(a.contract_number,b.contract_number)::varchar(25) AS AgreementID, 
	x.document_code as AgreementDocCode,
	COALESCE(a.document_id, b.document_id) AS AgreementDocID,
    COALESCE(a.effective_begin_date_id, b.effective_begin_date_id) AS AgreementStartDate, 
  COALESCE(a.effective_end_date_id, b.effective_end_date_id) AS AgreementEndDate, 
  COALESCE(a.description, b.description) AS AgreementPurpose, a.maximum_contract_amount as MaximumAgreementamount,
  z.agency_id,
  z.agency_code  as AgreementDeptCode, 
  d1.disbursement_id as DisbursementID,
  d1.created_load_id
FROM  history_agreement a 
   JOIN history_master_agreement b ON a.master_agreement_id = b.master_agreement_id
   JOIN etl.etl_data_load c ON c.load_id = COALESCE(a.created_load_id, b.created_load_id)
   JOIN ref_document_code x on x.document_code_id = COALESCE(a.document_code_id, b.document_code_id)
   JOIN ref_agency_history y on y.agency_history_id = COALESCE(a.agency_history_id, b.agency_history_id)
   JOIN ref_agency z on y.agency_id = z.agency_id
   JOIN vendor_history y1 on y1.vendor_history_id = COALESCE(a.vendor_history_id, b.vendor_history_id)
   JOIN vendor z1 on z1.vendor_id =y1.vendor_id
   JOIN mwbe_last_job d ON c.job_id >= d.job_id
  JOIN disbursement_line_item d1 on d1.reference_document_number = Coalesce(a.contract_number,b.contract_number)



CREATE  OR REPLACE VIEW disbursement_agreement_mwbe AS 
SELECT b.contract_number ::varchar(25) AS AgreementID,dc.document_code as AgreementDocCode,b.document_id  AS AgreementDocID, b.effective_begin_date_id AS AgreementStartDate,
       b.effective_end_date_id AS AgreementEndDate,b.description AS AgreementPurpose,
       ag.agency_id AS AgreementDeptId,ag.agency_code AS AgreementDeptCode, agt.agreement_type_code as AgreementTypeCode 
FROM (SELECT distinct agreement_id  FROM disbursement_line_item_details  WHERE spending_category_id != 2 AND master_agreement_id is null) a JOIN history_agreement b on a.agreement_id = b.original_agreement_id 
                                      JOIN ref_date rb on  b.effective_begin_date_id =rb.date_id
                                      JOIN ref_date re on b.effective_end_date_id =re.date_id
                                      JOIN ref_document_code dc on b.document_code_id = dc.document_code_id
				      				  JOIN ref_agency_history h on b.agency_history_id = h.agency_history_id
                                      JOIN ref_agency ag on h.agency_id = ag.agency_id
                                      LEFT JOIN ref_agreement_type agt ON b.agreement_type_id = agt.agreement_type_id
                                    UNION
SELECT ma.contract_number ::varchar(25) AS AgreementID,dc.document_code as AgreementDocCode, ma.document_id AS AgreementDocID,ma.effective_begin_date_id AS AgreementStartDate,
	ma.effective_end_date_id AS AgreementEndDate,ma.description AS AgreementPurpose,
	ag.agency_id AS AgreementDeptId,ag.agency_code AS AgreementDeptCode, agt.agreement_type_code as AgreementTypeCode 
FROM (select distinct master_agreement_id  from disbursement_line_item_details WHERE spending_category_id != 2 AND master_agreement_id is not null) a JOIN history_master_agreement ma on a.master_agreement_id = ma.original_master_agreement_id	
                                      JOIN ref_date rb on  ma.effective_begin_date_id =rb.date_id
                                      JOIN ref_date re on ma.effective_end_date_id =re.date_id
                                      JOIN ref_document_code dc on ma.document_code_id = dc.document_code_id
                                      JOIN ref_agency_history h on ma.agency_history_id = h.agency_history_id
                                      JOIN ref_agency ag on h.agency_id = ag.agency_id   
                                      LEFT JOIN ref_agreement_type agt ON ma.agreement_type_id = agt.agreement_type_id;


*/


CREATE  OR REPLACE VIEW disbursement_agreement_mwbe AS 
SELECT b.contract_number::varchar(25) AS AgreementID,dc.document_code as AgreementDocCode,b.document_id  AS AgreementDocID, rb.date AS AgreementStartDate,
       re.date AS AgreementEndDate,b.description AS AgreementPurpose,
       ag.agency_id AS AgreementDeptId,ag.agency_code AS AgreementDeptCode, agt.agreement_type_code as AgreementTypeCode , b.maximum_contract_amount as MaxContractAmount
FROM (SELECT distinct agreement_id  FROM disbursement_line_item_details  WHERE spending_category_id != 2 ) a JOIN history_agreement b on a.agreement_id = b.original_agreement_id 
                                      LEFT JOIN ref_date rb on  b.effective_begin_date_id =rb.date_id
                                      LEFT JOIN ref_date re on b.effective_end_date_id =re.date_id
                                      JOIN ref_document_code dc on b.document_code_id = dc.document_code_id
				      				  JOIN ref_agency_history h on b.agency_history_id = h.agency_history_id
                                      JOIN ref_agency ag on h.agency_id = ag.agency_id
                                      LEFT JOIN ref_agreement_type agt ON b.agreement_type_id = agt.agreement_type_id 
                                      WHERE b.latest_flag = 'Y';
/*
 SELECT b.contract_number::varchar(25) AS AgreementID,dc.document_code as AgreementDocCode,b.document_id  AS AgreementDocID, b.effective_begin_date_id AS AgreementStartDate,
       b.effective_end_date_id AS AgreementEndDate,b.description AS AgreementPurpose,
       ag.agency_id AS AgreementDeptId,ag.agency_code AS AgreementDeptCode, agt.agreement_type_code as AgreementTypeCode , b.maximum_contract_amount as MaxContractAmount, disbursement_id, disbursement_line_item_id
FROM (SELECT distinct disbursement_id, disbursement_line_item_id, agreement_id  FROM disbursement_line_item_details  a JOIN etl.etl_data_load e ON e.load_id = a.load_id WHERE spending_category_id != 2 AND e.publish_start_time::date >= '2013-07-01') a JOIN history_agreement b on a.agreement_id = b.original_agreement_id 
                                      LEFT JOIN ref_date rb on  b.effective_begin_date_id =rb.date_id
                                      LEFT JOIN ref_date re on b.effective_end_date_id =re.date_id
                                      JOIN ref_document_code dc on b.document_code_id = dc.document_code_id
				      				  JOIN ref_agency_history h on b.agency_history_id = h.agency_history_id
                                      JOIN ref_agency ag on h.agency_id = ag.agency_id
                                      LEFT JOIN ref_agreement_type agt ON b.agreement_type_id = agt.agreement_type_id 
                                      WHERE b.latest_flag = 'Y';
 */                                      
-- minority type

CREATE OR REPLACE VIEW minoirty_mwbe
as select * from ref_minority_type order by 1;





-- industry type 



create or replace view industry_type_mwbe as 
select * from ref_industry_type order by 1;


--disbursement address


CREATE OR REPLACE VIEW disbursement_address_mwbe as
SELECT x.disbursement_id,a.vendor_id as VendorID,a.vendor_customer_code as VendorCode,a.legal_name as VendorName,
        f.address_line_1 as StreetAddrLine1,f.address_line_2 as StreetAddrLine2,f.city as City,f.state as StateProv,f.zip as PostalCode,f.country as Country,a.created_load_id as LoadID
          FROM disbursement_line_item_details x left join vendor a  on x.vendor_id = a.vendor_id join fmsv_business_type c on a.vendor_customer_code = c.vendor_customer_code  
		     left join (SELECT a.vendor_id,
                       max(vendor_history_id) AS vendor_history_id
                  FROM vendor_history a
                GROUP BY 1) a1
             ON a1.vendor_id = a.vendor_id
		left join		   
		  (SELECT a2.vendor_history_id,
                            a2.vendor_address_id,
                            e.address_id
                       FROM    (SELECT a.vendor_history_id,
                                       max(vendor_address_id)
                                          AS vendor_address_id
                                  FROM vendor_address a
                                 WHERE a.address_type_id = 2
                                GROUP BY 1) a2
                            LEFT JOIN
                               vendor_address e
                            ON a2.vendor_address_id = e.vendor_address_id) a3
                            on a1.vendor_history_id = a3.vendor_history_id
			left join address f on f.address_id = a3.address_id
			WHERE x.spending_category_id != 2;




-- disbursement industry type


CREATE OR REPLACE VIEW  disbursement_industry_mwbe as 
select disbursement_id as DisbursementID,disbursement_line_item_id as LineItemNum,b.industry_type_id,b.industry_type_name,a.created_load_id 
from disbursement_line_item a join agreement_snapshot b on a.reference_document_number = b.contract_number ;



-- vendor address mwbe
/*
CREATE OR REPLACE VIEW vendor_address_mwbe
as
select  
v.vendor_id,v.vendor_customer_code as VendorCode,v.legal_name as VendorName,v.miscellaneous_vendor_flag,
v.created_load_id as LoadID,c.address_line_1 as StreetAddrLine1,c.address_line_2 as StreetAddrLine2,c.city as City,c.state as StateProv,c.zip as PostalCode,c.country as Country
from vendor v ,
(select vendor_id,max(b.vendor_history_id) as vendor_history_id from vendor_history a join vendor_address b on a.vendor_history_id =b.vendor_history_id where address_type_id =2 group by 1)x ,
vendor_address  va, address c
WHERE v.vendor_id = x.vendor_id AND x.vendor_history_id = va.vendor_history_id AND va.address_id = c.address_id;
*/

/*
CREATE OR REPLACE VIEW vendor_address_mwbe as
SELECT 
a.vendor_id as VendorID,a.vendor_customer_code as VendorCode,a.legal_name as VendorName,a.miscellaneous_vendor_flag,
          a.created_load_id as LoadID,
          f.address_line_1 as StreetAddrLine1,f.address_line_2 as StreetAddrLine2,f.city as City,f.state as StateProv,f.zip as PostalCode,f.country as Country
          FROM vendor a 
		   left join (SELECT a.vendor_id,
                       max(vendor_history_id) AS vendor_history_id
                  FROM vendor_history a
                GROUP BY 1) a1
             ON a1.vendor_id = a.vendor_id
		left join		   
		  (SELECT a2.vendor_history_id,
                            a2.vendor_address_id,
                            e.address_id
                       FROM    (SELECT a.vendor_history_id,
                                       max(vendor_address_id)
                                          AS vendor_address_id
                                  FROM vendor_address a
                                 WHERE a.address_type_id = 2
                                GROUP BY 1) a2
                            LEFT JOIN
                               vendor_address e
                            ON a2.vendor_address_id = e.vendor_address_id) a3
                            on a1.vendor_history_id = a3.vendor_history_id
			left join address f on f.address_id = a3.address_id ;
			
	*/

CREATE OR REPLACE VIEW vendor_address_mwbe AS 
 SELECT a.vendor_id AS vendorid, a.vendor_customer_code AS vendorcode, a.legal_name AS vendorname, a.miscellaneous_vendor_flag, a.created_load_id AS loadid, f.address_line_1 AS streetaddrline1, f.address_line_2 AS streetaddrline2, f.city, f.state AS stateprov, f.zip AS postalcode, f.country
   FROM vendor a
   LEFT JOIN ( SELECT a.vendor_id, max(b.vendor_address_id) AS vendor_address_id
           FROM vendor_history a
      JOIN vendor_address b ON a.vendor_history_id = b.vendor_history_id
     GROUP BY a.vendor_id) a1 ON a1.vendor_id = a.vendor_id
   LEFT JOIN vendor_address e ON a1.vendor_address_id = e.vendor_address_id
   LEFT JOIN address f ON f.address_id = e.address_id;
   
   
-- payroll  disbursement mwbe

CREATE OR REPLACE VIEW processed_payroll_disbursement_mwbe AS
select data_source_code,b.load_id,job_id,b.processed_flag,b.publish_end_time 
from etl.etl_data_load a join etl.etl_data_load_file b 
on a.load_id=b.load_id
 where a.data_source_code in('F','PS')
 and b.processed_flag ='Y' group by 1,2,3,4,5;
 
 
 /*  
 select count(*) from vendor_mwbe limit 10000 --> 94215

select count(*) from fmsv_business_type -- 15729

select count(distinct vendor_customer_code) from fmsv_business_type  -- 9478

select count(*) from vendor_mwbe  where BusinessTypeId IS NOT NULL OR MinoritytypeId IS NOT NULL  --> 1453

select count(distinct VendorCode) from vendor_mwbe  where BusinessTypeId IS NOT NULL OR MinoritytypeId IS NOT NULL  -- 1453

--select (case when c.business_type_id = 2 then 'Native' 
--	   			when c.business_type_id = 5 then 'Unspecified MWBE'
--           		else d.minority_type_name end) as MinorityGroup,count(*)
-- from disbursement_line_item_details a left join  vendor b on a.vendor_id =b.vendor_id 
--				left join (select vendor_customer_code , business_type_id ,minority_type_id from fmsv_business_type 
--				where status =2 and (business_type_id=2 or minority_type_id is not null or (vendor_customer_code in 
--					(select distinct vendor_customer_code from fmsv_business_type 
--					  where  business_type_id = 5 and status = 2 and vendor_customer_code not in (select distinct vendor_customer_code from fmsv_business_type where minority_type_id is not null)) AND business_type_id=5)))c
--				 on b.vendor_customer_code =c.vendor_customer_code
--				left join ref_minority_type d on c.minority_type_id = d.minority_type_id 
--				left join ref_business_type i on c.business_type_id = i.business_type_id
--				JOIN etl.etl_data_load e ON e.load_id = a.load_id
--				JOIN ref_date rd on a.check_eft_issued_date_id = rd.date_id
--				WHERE a.spending_category_id != 2 AND e.publish_start_time::date >= '2013-07-01' GROUP BY 1 ORDER BY 1;
				
select sum(check_amount),         (case when d.business_type_id =2 then 6 
                   when d.business_type_id = 5 then 1
           else d.minority_type_id end) as MinoritytypeId 
from disbursement_line_item_details a JOIN etl.etl_data_load b ON a.load_id = b.load_id  
LEFT JOIN vendor c on a.vendor_id = c.vendor_id
LEFT JOIN (select vendor_customer_code , business_type_id ,minority_type_id from fmsv_business_type 
                                                                where status =2 and (business_type_id=2 or minority_type_id is not null or (vendor_customer_code in 
                                                                                (select distinct vendor_customer_code from fmsv_business_type 
                                                                                  where  business_type_id = 5
                                                                                   and status = 2 and vendor_customer_code not in
                                                                                    (select distinct vendor_customer_code from fmsv_business_type where minority_type_id is not null)) AND business_type_id=5))) d ON c.vendor_customer_code = d.vendor_customer_code
WHERE a.spending_category_id <> 2 AND b.publish_start_time::date >= '2013-07-01'  group by 2



 */