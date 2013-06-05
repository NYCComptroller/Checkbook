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

set search_path=public;
/* Functions defined
	updateForeignKeysForFMSInHeader
	
*/
-- Function: etl.processvendor(integer, bigint, character varying)

-- DROP FUNCTION etl.processvendor(integer, bigint, character varying);

CREATE OR REPLACE FUNCTION etl.processvendor(p_load_file_id_in integer, p_load_id_in bigint, p_doc_type character varying)  RETURNS integer AS $$

DECLARE

	l_data_source_code char(2);
	l_vendor_stg_table VARCHAR;
	l_update_query varchar;
	l_count bigint;

BEGIN


	SELECT data_source_code
	FROM   etl.etl_data_load a 
	WHERE  load_id = p_load_id_in
	INTO   l_data_source_code;

	
	
	TRUNCATE etl.tmp_stg_vendor;
	
	
	IF l_data_source_code = 'F' THEN
	
	UPDATE etl.stg_fms_vendor SET vend_cust_cd = 'N/A' WHERE vend_cust_cd ='N/A (PRIVACY/SECURITY)';
	
	l_vendor_stg_table :='etl.stg_fms_vendor';
	
	INSERT INTO etl.tmp_stg_vendor(vend_cust_cd, lgl_nm, alias_nm, ad_id, org_cls, misc_acct_fl, ad_ln_1, ad_ln_2, ctry, st, zip, 
							   city, vendor_history_id, uniq_id, address_type_code)
	SELECT vend_cust_cd, lgl_nm, alias_nm, ad_id, org_cls, misc_acct_fl, ad_ln_1, ad_ln_2, ctry, st, zip, city, 
							   vendor_history_id, uniq_id, 'PA' as address_type_code
	FROM etl.stg_fms_vendor;
	
	ELSIF l_data_source_code = 'M'  THEN
	
	l_vendor_stg_table :='etl.stg_mag_vendor';
	
	INSERT INTO etl.tmp_stg_vendor(vend_cust_cd, lgl_nm, alias_nm, ad_id, org_cls, misc_acct_fl, ad_ln_1, ad_ln_2, ctry, st, zip, 
							   city, vendor_history_id, uniq_id, address_type_code)
	SELECT vend_cust_cd, lgl_nm, alias_nm, ad_id, NULL as org_cls, 0 as misc_acct_fl, ad_ln_1, ad_ln_2, ctry, st, zip, city, 
							   vendor_history_id, uniq_id, 'PR' as address_type_code
	FROM etl.stg_mag_vendor;
	
	ELSIF l_data_source_code = 'C' AND (p_doc_type = 'CT1' OR p_doc_type = 'CTA1' OR p_doc_type = 'CTA2') THEN
	
	l_vendor_stg_table :='etl.stg_con_ct_vendor';
	
	INSERT INTO etl.tmp_stg_vendor(vend_cust_cd, lgl_nm, alias_nm, ad_id, org_cls, misc_acct_fl, ad_ln_1, ad_ln_2, ctry, st, zip, 
							   city, vendor_history_id, uniq_id, address_type_code)
	SELECT vend_cust_cd, lgl_nm, alias_nm, ad_id, NULL as org_cls, 0 as misc_acct_fl, ad_ln_1, ad_ln_2, ctry, st, zip, city, 
							   vendor_history_id, uniq_id, 'PR' as address_type_code
	FROM etl.stg_con_ct_vendor;
	
	ELSIF l_data_source_code = 'C' AND (p_doc_type = 'POC' OR p_doc_type = 'PCC1' OR p_doc_type = 'POD') THEN
	
	l_vendor_stg_table :='etl.stg_con_po_vendor';
	
	INSERT INTO etl.tmp_stg_vendor(vend_cust_cd, lgl_nm, alias_nm, ad_id, org_cls, misc_acct_fl, ad_ln_1, ad_ln_2, ctry, st, zip, 
							   city, vendor_history_id, uniq_id, address_type_code)
	SELECT vend_cust_cd, lgl_nm, alias_nm, ad_id, NULL as org_cls, 0 as misc_acct_fl, ad_ln_1, ad_ln_2, ctry, st, zip, city, 
							   vendor_history_id, uniq_id, 'PR' as address_type_code
	FROM etl.stg_con_po_vendor;
	
	ELSIF l_data_source_code = 'C' AND (p_doc_type = 'DO1') THEN
	
	l_vendor_stg_table :='etl.stg_con_do1_vendor';
	
	INSERT INTO etl.tmp_stg_vendor(vend_cust_cd, lgl_nm, alias_nm, ad_id, org_cls, misc_acct_fl, ad_ln_1, ad_ln_2, ctry, st, zip, 
							   city, vendor_history_id, uniq_id, address_type_code)
	SELECT vend_cust_cd, lgl_nm, alias_nm, ad_id, NULL as org_cls, 0 as misc_acct_fl, ad_ln_1, ad_ln_2, ctry, st, zip, city, 
							   vendor_history_id, uniq_id, 'PR' as address_type_code
	FROM etl.stg_con_do1_vendor;
	
	ELSE
	
	l_vendor_stg_table :='';
	
	END IF;

	RAISE NOTICE 'VENDOR 0';

	
	-- Getting all vendors and categorizing if they are new and/or name/address/business type changed.
	-- TO DO Filter, Address type, Updating vendor history id
	


	TRUNCATE etl.tmp_all_vendors;
	
	INSERT INTO etl.tmp_all_vendors
	SELECT MAX(uniq_id), a.vend_cust_cd as vendor_customer_code, COALESCE(MAX(b.vendor_history_id),0) as vendor_history_id, COALESCE(MAX(b.vendor_id),0) as vendor_id, COALESCE(misc_acct_fl,0) as misc_acct_fl,
				'N' as is_new_vendor, 'N' as is_name_changed, 'N' as is_vendor_address_changed, 'N' as  is_address_new,'N' as is_bus_type_changed, lgl_nm, alias_nm,
				ad_ln_1, ad_ln_2, ctry, st, zip, city, address_type_code		
	FROM etl.tmp_stg_vendor a LEFT JOIN 
	(SELECT max(b.vendor_id) as vendor_id, max(c.vendor_history_id) as vendor_history_id, b.vendor_customer_code 
	FROM vendor b, vendor_history c
	WHERE coalesce(b.miscellaneous_vendor_flag,0::bit) = 0::bit AND b.vendor_id = c.vendor_id
	GROUP BY 3) b
	ON a.vend_cust_cd = b.vendor_customer_code	
	WHERE COALESCE(misc_acct_fl,0) = 0 
	GROUP BY 2,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19;

	RAISE NOTICE 'VENDOR 001';
	
	INSERT INTO etl.tmp_all_vendors
	SELECT uniq_id, vend_cust_cd as vendor_customer_code, 0 as vendor_history_id, 0 as vendor_id, COALESCE(misc_acct_fl,0) as misc_acct_fl,
				'Y' as is_new_vendor, 'N' as is_name_changed, 'N' as is_vendor_address_changed, 'N' as  is_address_new,'N' as is_bus_type_changed, lgl_nm, alias_nm,
				ad_ln_1, ad_ln_2, ctry, st, zip, city, address_type_code		
	FROM	etl.tmp_stg_vendor 
	WHERE COALESCE(misc_acct_fl,0) = 1 ;

	RAISE NOTICE 'VENDOR 01';
	
	-- Identifying new vendors
	
	UPDATE etl.tmp_all_vendors
	SET is_new_vendor = 'Y'
	WHERE coalesce(vendor_history_id,0) =0 OR misc_acct_fl = 1;

	RAISE NOTICE 'VENDOR 02';
	-- Identifying existing vendors for which legal/alias name changed 
	
	TRUNCATE etl.tmp_all_vendors_uniq_id ;
	
	INSERT INTO etl.tmp_all_vendors_uniq_id
	SELECT uniq_id
	FROM etl.tmp_all_vendors a , vendor b
	WHERE a.vendor_customer_code = b.vendor_customer_code
	AND a.is_new_vendor = 'N' AND (COALESCE(a.lgl_nm, '') <>  COALESCE(b.legal_name, '') OR COALESCE(a.alias_nm, '') <> COALESCE(b.alias_name, ''));
	
	
	UPDATE etl.tmp_all_vendors a
	SET is_name_changed = 'Y'
	FROM
	etl.tmp_all_vendors_uniq_id b
	WHERE a.uniq_id = b.uniq_id AND a.is_new_vendor = 'N';

	RAISE NOTICE 'VENDOR 1';


	-- Identifying new addresses
	
	TRUNCATE etl.tmp_all_vendors_uniq_id ;
	
	INSERT INTO etl.tmp_all_vendors_uniq_id
	SELECT uniq_id
	FROM etl.tmp_all_vendors a LEFT JOIN address b
	ON COALESCE(a.ad_ln_1,'') = COALESCE(b.address_line_1,'')
	AND COALESCE(a.ad_ln_2,'') = COALESCE(b.address_line_2,'')
	AND COALESCE(a.city,'') = COALESCE(b.city,'')
	AND COALESCE(a.st,'') = COALESCE(b.state,'')
	AND COALESCE(a.zip,'') = COALESCE(b.zip,'')
	AND COALESCE(a.ctry,'') = COALESCE(b.country,'')
	WHERE b.address_id IS NULL ;
	
	UPDATE etl.tmp_all_vendors a
	SET is_address_new = 'Y'
	FROM
	etl.tmp_all_vendors_uniq_id b
	WHERE a.uniq_id = b.uniq_id ;
	
	-- Identifying existing vendors for which address information changed 
	
	TRUNCATE etl.tmp_all_vendors_uniq_id ;
	
	INSERT INTO etl.tmp_all_vendors_uniq_id
	SELECT b.uniq_id
	FROM
	(SELECT m.uniq_id,vendor_address_id
	FROM etl.tmp_all_vendors m 
			LEFT JOIN
			          (SELECT  a.vendor_address_id,h.vendor_customer_code, h.uniq_id,b.address_type_code,c.address_line_1,c.address_line_2,c.city,c.state,c.zip,c.country					
					FROM	vendor_address a , ref_address_type b,  address c, vendor_history f, etl.tmp_all_vendors h
					WHERE  a.address_type_id = b.address_type_id AND a.address_id = c.address_id
					AND a.vendor_history_id = f.vendor_history_id AND f.vendor_history_id = h.vendor_history_id 
					AND b.address_type_code = h.address_type_code AND h.is_new_vendor = 'N'	
					) z on m.vendor_customer_code = z.vendor_customer_code 
						AND COALESCE(m.ad_ln_1,'') = COALESCE(z.address_line_1,'')
						AND COALESCE(m.ad_ln_2,'') = COALESCE(z.address_line_2,'')
						AND COALESCE(m.city,'') = COALESCE(z.city,'')
						AND COALESCE(m.st,'') = COALESCE(z.state,'')
						AND COALESCE(m.zip,'') = COALESCE(z.zip,'')
						AND COALESCE(m.ctry,'') = COALESCE(z.country,'')
						WHERE m.is_new_vendor = 'N'
						) b
	WHERE b.vendor_address_id IS NULL;
	
	UPDATE etl.tmp_all_vendors a
	SET is_vendor_address_changed = 'Y'
	FROM
	etl.tmp_all_vendors_uniq_id b
	WHERE a.uniq_id = b.uniq_id
	AND a.is_new_vendor = 'N' ;

	
	
	RAISE NOTICE 'VENDOR 2';

	-- Identifying existing vendors for which vendor business type information changed 

	TRUNCATE etl.tmp_all_vendors_uniq_id ;
	
	INSERT INTO etl.tmp_all_vendors_uniq_id
	SELECT distinct a.uniq_id
	FROM etl.tmp_all_vendors a JOIN
	(SELECT distinct vendor_customer_code FROM
	(SELECT coalesce(z.vendor_customer_code, a.vendor_customer_code) as vendor_customer_code,
		(CASE WHEN vendor_business_type_id IS NOT NULL AND b.vendor_customer_code IS NOT NULL THEN 'N' 
			WHEN vendor_business_type_id IS NOT NULL AND b.vendor_customer_code IS NULL  THEN 'Y'
			WHEN vendor_business_type_id IS NULL AND b.vendor_customer_code IS NOT NULL  THEN 'Y'
			ELSE NULL END) as modified_flag
	FROM fmsv_business_type a JOIN (SELECT distinct vendor_customer_code FROM etl.tmp_all_vendors WHERE is_new_vendor = 'N') b ON a.vendor_customer_code = b.vendor_customer_code
		FULL OUTER JOIN (SELECT g.vendor_customer_code, g.uniq_id, d.vendor_business_type_id,b.business_type_id, status, minority_type_id
		                 FROM vendor_business_type d , ref_business_type b , vendor_history e, etl.tmp_all_vendors g
						WHERE  d.business_type_id = b.business_type_id AND e.vendor_history_id = d.vendor_history_id
						AND g.vendor_history_id = e.vendor_history_id AND g.is_new_vendor = 'N' ) as z ON a.vendor_customer_code = z.vendor_customer_code AND a.business_type_id=z.business_type_id
						AND a.status = z.status AND COALESCE(a.minority_type_id,0) = COALESCE(z.minority_type_id,0)) b WHERE b.modified_flag = 'Y') c
	ON a.vendor_customer_code = c.vendor_customer_code;
	
	
	UPDATE etl.tmp_all_vendors a
	SET is_bus_type_changed = 'Y'
	FROM etl.tmp_all_vendors_uniq_id b	
	WHERE a.uniq_id = b.uniq_id
	AND a.is_new_vendor = 'N'; 
	
	RAISE NOTICE 'VENDOR 3';

	-- Inserting new addresses in address table
	
	TRUNCATE etl.address_id_seq;

	INSERT INTO etl.address_id_seq(uniq_id)
	SELECT uniq_id
	FROM (SELECT min(uniq_id) as uniq_id, ad_ln_1, ad_ln_2, ctry, st, zip, city
	FROM   etl.tmp_all_vendors
	WHERE  is_address_new ='Y'
	GROUP BY 2,3,4,5,6,7) a;
	


	INSERT INTO address(address_id,address_line_1 ,address_line_2,city,
  				state ,zip ,country) 
	SELECT	min(b.address_id) as address_id,a.ad_ln_1 ,a.ad_ln_2,a.city,
  				a.st ,a.zip ,a.ctry  			
  	FROM	etl.tmp_all_vendors a JOIN etl.address_id_seq b ON a.uniq_id = b.uniq_id
  	GROUP BY 2,3,4,5,6,7;


	RAISE NOTICE 'VENDOR 4';
	
	-- Inserting new Vendor records

	TRUNCATE etl.vendor_id_seq;
	
	INSERT INTO etl.vendor_id_seq(uniq_id)
	SELECT uniq_id
	FROM (SELECT vendor_customer_code, min(uniq_id) as uniq_id
	      FROM   etl.tmp_all_vendors
		WHERE  is_new_vendor ='Y' AND misc_acct_fl =0
		GROUP BY 1) a;

	INSERT INTO etl.vendor_id_seq(uniq_id)
	SELECT uniq_id
	FROM   etl.tmp_all_vendors
	WHERE  is_new_vendor ='Y' AND misc_acct_fl =1;
	
	INSERT INTO vendor(vendor_id,vendor_customer_code,legal_name,alias_name,miscellaneous_vendor_flag,
			   vendor_sub_code,created_load_id,created_date)
	SELECT 	b.vendor_id,a.vendor_customer_code,a.lgl_nm,a.alias_nm,coalesce(a.misc_acct_fl,0)::bit as miscellaneous_vendor_flag,
		NULL as vendor_sub_code,p_load_id_in as created_load_id, now()::timestamp
	FROM	etl.tmp_all_vendors a JOIN etl.vendor_id_seq b ON a.uniq_id = b.uniq_id;
	
	GET DIAGNOSTICS l_count = ROW_COUNT;
			  	IF l_count >0 THEN
					INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
					VALUES(p_load_file_id_in,'V',l_count,'Number of records inserted into vendor');
	END IF;


	RAISE NOTICE 'VENDOR 5';

	


	-- Inserting the records into vendor_history
	
	TRUNCATE etl.vendor_history_id_seq;
	
	INSERT INTO etl.vendor_history_id_seq(uniq_id)
	SELECT uniq_id
	FROM   etl.tmp_all_vendors
	WHERE  is_new_vendor ='Y' OR is_name_changed='Y' OR is_vendor_address_changed = 'Y' OR is_bus_type_changed = 'Y';


	INSERT INTO vendor_history(vendor_history_id, vendor_id, legal_name,alias_name,miscellaneous_vendor_flag ,vendor_sub_code,
	    		load_id ,created_date)
		SELECT 	b.vendor_history_id,c.vendor_id,a.lgl_nm,a.alias_nm,coalesce(a.misc_acct_fl,0)::bit,
			NULL as vendor_sub_code,p_load_id_in as load_id, now()::timestamp
		FROM	etl.tmp_all_vendors a JOIN etl.vendor_history_id_seq b ON a.uniq_id = b.uniq_id
			JOIN vendor c ON a.vendor_customer_code = c.vendor_customer_code AND coalesce(a.misc_acct_fl,0)::bit = c.miscellaneous_vendor_flag
	WHERE coalesce(a.misc_acct_fl,0) = 0;
	
	GET DIAGNOSTICS l_count = ROW_COUNT;
			  	IF l_count >0 THEN
					INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
					VALUES(p_load_file_id_in,'V',l_count,'Number of records inserted into vendor_history');
	END IF;
	
	INSERT INTO vendor_history(vendor_history_id, vendor_id, legal_name,alias_name,miscellaneous_vendor_flag ,vendor_sub_code,
    		load_id ,created_date)
	SELECT 	b.vendor_history_id,c.vendor_id,a.lgl_nm,a.alias_nm,coalesce(a.misc_acct_fl,1)::bit,
		NULL as vendor_sub_code,p_load_id_in as load_id, now()::timestamp
	FROM	etl.tmp_all_vendors a JOIN etl.vendor_history_id_seq b ON a.uniq_id = b.uniq_id
		JOIN etl.vendor_id_seq c ON a.uniq_id = c.uniq_id
	WHERE coalesce(a.misc_acct_fl,0) = 1;

	GET DIAGNOSTICS l_count = ROW_COUNT;
			  	IF l_count >0 THEN
					INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
					VALUES(p_load_file_id_in,'V',l_count,'Number of records inserted into vendor_history');
	END IF;

		
	RAISE NOTICE 'VENDOR 6';	
	
	-- Updating vendor records which have been modified
	
	TRUNCATE etl.tmp_vendor_update ;
	
	INSERT INTO etl.tmp_vendor_update (vendor_id, legal_name, alias_name)
	SELECT vendor_id, x.lgl_nm as legal_name , x.alias_nm as alias_name
	FROM etl.tmp_all_vendors x, etl.vendor_history_id_seq y
	WHERE	x.uniq_id = y.uniq_id AND x.is_new_vendor ='N' 
	AND (x.is_name_changed='Y' OR x.is_vendor_address_changed = 'Y' OR x.is_bus_type_changed = 'Y') ;
	
	UPDATE vendor a
	SET    	legal_name = b.legal_name,
		alias_name = b.alias_name,
		updated_load_id = p_load_id_in,
		updated_date = now()::timestamp
	FROM	
	etl.tmp_vendor_update b
	WHERE a.vendor_id = b.vendor_id;

	RAISE NOTICE 'VENDOR 7';

		
	-- Update history id in the main staging table such as stg_fmsv_vendor based on fields such as customer code through address fields for the changed non miscellaneous vendor
	
	UPDATE etl.tmp_stg_vendor a
	SET vendor_history_id = b.vendor_history_id
	FROM
	(select y.vendor_history_id, x.vendor_customer_code, x.lgl_nm, alias_nm,
				ad_ln_1, ad_ln_2, ctry, st, zip, city, address_type_code, misc_acct_fl,
				is_new_vendor, is_name_changed, is_vendor_address_changed,  is_bus_type_changed
	FROM etl.tmp_all_vendors x, etl.vendor_history_id_seq  y
	WHERE x.uniq_id = y.uniq_id AND coalesce(x.misc_acct_fl,0) = 0
	AND (x.is_new_vendor = 'Y' OR x.is_name_changed = 'Y' OR x.is_vendor_address_changed = 'Y' OR x.is_bus_type_changed = 'Y')
	) b
	WHERE a.vend_cust_cd = b.vendor_customer_code AND coalesce(a.lgl_nm,'') = coalesce(b.lgl_nm,'')
	AND coalesce(a.alias_nm,'') = coalesce(b.alias_nm,'') AND coalesce(a.ad_ln_1,'') = coalesce(b.ad_ln_1,'')
	AND coalesce(a.ad_ln_2,'') = coalesce(b.ad_ln_2,'') AND coalesce(a.ctry,'') = coalesce(b.ctry,'')
	AND coalesce(a.st,'') = coalesce(b.st,'') AND coalesce(a.zip,'') = coalesce(b.zip,'')
	AND coalesce(a.city,'') = coalesce(b.city,'') AND coalesce(a.address_type_code,'') = coalesce(b.address_type_code,'')
	AND coalesce(a.misc_acct_fl,0) = 0 AND (b.is_new_vendor = 'Y' OR b.is_name_changed = 'Y' OR b.is_vendor_address_changed = 'Y' OR b.is_bus_type_changed = 'Y') ;


	RAISE NOTICE 'VENDOR 71';
	-- Update history id in the main staging table such as stg_fmsv_vendor based on customer code for the unchanged vendors
	
	UPDATE etl.tmp_stg_vendor a
	SET vendor_history_id = b.vendor_history_id
	FROM
	(SELECT vendor_history_id,  vendor_customer_code
	FROM etl.tmp_all_vendors 
	WHERE  is_new_vendor = 'N' AND is_name_changed = 'N' AND is_vendor_address_changed = 'N' AND is_bus_type_changed = 'N') b
	WHERE a.vend_cust_cd = b.vendor_customer_code ;


	RAISE NOTICE 'VENDOR 72';
	-- Update history id in the main staging table such as stg_fmsv_vendor based on uniq id in etl.vendor_history_id_seq for the miscellaneous vendors
	
	UPDATE etl.tmp_stg_vendor a
	SET vendor_history_id = b.vendor_history_id
	FROM
	etl.vendor_history_id_seq  b
	WHERE a.uniq_id = b.uniq_id 
	AND coalesce(a.misc_acct_fl,0) = 1;
	
	RAISE NOTICE 'VENDOR 73';
	
	l_update_query := 'UPDATE ' || l_vendor_stg_table || ' a SET vendor_history_id = b.vendor_history_id ' || 
	' FROM etl.tmp_stg_vendor b ' || 
	' WHERE a.uniq_id = b.uniq_id ' ;
	
	raise notice 'l_update_query  is  %',l_update_query;
	
	EXECUTE l_update_query;
	
	RAISE NOTICE 'VENDOR 74';
	
	-- Inserting into vendor_address table

	TRUNCATE etl.vendor_address_id_seq;
	
	INSERT INTO etl.vendor_address_id_seq(uniq_id)
	SELECT uniq_id
	FROM   etl.tmp_all_vendors
	WHERE  is_new_vendor ='Y' OR is_name_changed='Y' OR is_vendor_address_changed = 'Y' OR is_bus_type_changed = 'Y';

	INSERT INTO vendor_address(vendor_address_id,vendor_history_id,address_id,address_type_id,load_id,created_date)
	SELECT c.vendor_address_id,d.vendor_history_id, e.address_id,g.address_type_id, p_load_id_in,now()::timestamp
	FROM	etl.tmp_all_vendors a JOIN etl.vendor_history_id_seq d ON a.uniq_id = d.uniq_id
		JOIN address e ON COALESCE(a.ad_ln_1,'') = COALESCE(e.address_line_1,'')  
			   AND COALESCE(a.ad_ln_2,'') = COALESCE(e.address_line_2,'')  
			   AND COALESCE(a.city,'') = COALESCE(e.city,'')  
			   AND COALESCE(a.st,'') = COALESCE(e.state,'') 
			   AND COALESCE(a.zip,'') = COALESCE(e.zip,'') 
			   AND COALESCE(a.ctry,'') = COALESCE(e.country,'')	
		JOIN etl.vendor_address_id_seq c ON a.uniq_id = c.uniq_id	   
		LEFT JOIN ref_address_type g ON a.address_type_code = g.address_type_code;

	GET DIAGNOSTICS l_count = ROW_COUNT;
			  	IF l_count >0 THEN
					INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
					VALUES(p_load_file_id_in,'V',l_count,'Number of records inserted into vendor_address');
	END IF;

	RAISE NOTICE 'VENDOR 8';
	
	-- Inserting into  vendor_business_types table

		
	INSERT INTO vendor_business_type(vendor_history_id,business_type_id,status,  minority_type_id,load_id,created_date)
    	SELECT  d.vendor_history_id, b.business_type_id, b.status, b.minority_type_id, p_load_id_in, now()::timestamp
    	FROM	etl.tmp_all_vendors a JOIN fmsv_business_type b ON a.vendor_customer_code = b.vendor_customer_code    		
		JOIN etl.vendor_history_id_seq d ON a.uniq_id = d.uniq_id
		WHERE a.is_new_vendor ='Y' OR a.is_name_changed='Y' OR a.is_vendor_address_changed = 'Y' OR a.is_bus_type_changed = 'Y';

	GET DIAGNOSTICS l_count = ROW_COUNT;
			  	IF l_count >0 THEN
					INSERT INTO etl.etl_data_load_verification(load_file_id,data_source_code,num_transactions,description)
					VALUES(p_load_file_id_in,'V',l_count,'Number of records inserted into vendor_business_type');
	END IF;
	
	RETURN 1;
	
EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in processvendor';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;	

	RETURN 0;
END;
$$   LANGUAGE 'plpgsql' VOLATILE;




