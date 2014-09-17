/*
Functions defined
	processexternaldata

*/


CREATE OR REPLACE FUNCTION etl.loadDataFromForeignTables(p_load_file_id_in bigint)
  RETURNS integer AS $$
DECLARE
l_count bigint;
l_data_source_code etl.ref_data_source.data_source_code%TYPE;
l_load_id bigint;
l_start_time  timestamp;
l_end_time  timestamp;
BEGIN
	
	l_start_time := timeofday()::timestamp;
	
	SELECT b.data_source_code , a.load_id
	FROM   etl.etl_data_load_file a JOIN etl.etl_data_load b ON a.load_id = b.load_id	       
	WHERE  a.load_file_id = p_load_file_id_in     
	INTO   l_data_source_code, l_load_id;
	
	IF(l_data_source_code = 'A') THEN
	
	TRUNCATE etl.ext_stg_coa_agency_feed;
	
	INSERT INTO etl.ext_stg_coa_agency_feed(
            agency_code, agency_name, col3, agency_short_name, col5, col6, 
            col7, col8, col9, col10, col11, col12, col13, col14, col15, col16, 
            col17, col18, col19, col20, col21, col22, col23, col24, col25, 
            col26)
    SELECT agency_code, agency_name, col3, agency_short_name, col5, col6, 
            col7, col8, col9, col10, col11, col12, col13, col14, col15, col16, 
            col17, col18, col19, col20, col21, col22, col23, col24, col25, 
            col26 
	FROM etl.foreign_tbl_coa_agency_feed;
	
	
	ELSIF(l_data_source_code = 'E') THEN
	
	TRUNCATE  etl.ext_stg_coa_expenditure_object_feed;
	
	INSERT INTO etl.ext_stg_coa_expenditure_object_feed(
            col1, fiscal_year, expenditure_object_code, expenditure_object_name, 
            col5, col6, col7, col8, col9, col10, col11, col12, col13, col14, 
            col15, col16, col17, col18, col19, col20, col21, col22, col23, 
            col24, col25, col26, col27, col28, col29, col30, col31, col32, 
            col33, col34, col35, col36, col37, col38, col39, col40, col41, 
            col42, col43)
    SELECT col1, fiscal_year, expenditure_object_code, expenditure_object_name, 
            col5, col6, col7, col8, col9, col10, col11, col12, col13, col14, 
            col15, col16, col17, col18, col19, col20, col21, col22, col23, 
            col24, col25, col26, col27, col28, col29, col30, col31, col32, 
            col33, col34, col35, col36, col37, col38, col39, col40, col41, 
            col42, col43  
	FROM etl.foreign_tbl_coa_expenditure_object_feed;
	
	ELSIF(l_data_source_code = 'D') THEN
	
	TRUNCATE  etl.ext_stg_coa_department_feed;
	
	INSERT INTO etl.ext_stg_coa_department_feed(
            agency_code, fund_class_code, fiscal_year, department_code, department_name, 
            col6, col7, department_short_name, col9, col10, col11, col12, 
            col13, col14, col15, col16, col17, col18, col19, col20, col21)
     SELECT agency_code, fund_class_code, fiscal_year, department_code, department_name, 
            col6, col7, department_short_name, col9, col10, col11, col12, 
            col13, col14, col15, col16, col17, col18, col19, col20, col21 
	FROM etl.foreign_tbl_coa_department_feed;
	
	ELSIF(l_data_source_code = 'O') THEN
	
	TRUNCATE  etl.ext_stg_coa_object_class_feed;
	
	INSERT INTO etl.ext_stg_coa_object_class_feed(
            doc_dept_cd, object_class_code, object_class_name, short_name, 
            act_fl, effective_begin_date, effective_end_date, alw_bud_fl, 
            description, cntac_cd, object_class_name_up, tbl_last_dt, intr_cty_fl, 
            cntrc_pos_fl, pyrl_typ, dscr_ext, rltd_ocls_cd, col18)
    SELECT doc_dept_cd, object_class_code, object_class_name, short_name, 
            act_fl, effective_begin_date, effective_end_date, alw_bud_fl, 
            description, cntac_cd, object_class_name_up, tbl_last_dt, intr_cty_fl, 
            cntrc_pos_fl, pyrl_typ, dscr_ext, rltd_ocls_cd, col18
	FROM etl.foreign_tbl_coa_object_class_feed ;
	
	ELSIF(l_data_source_code = 'L') THEN
	
	TRUNCATE  etl.ext_stg_coa_location_feed;
	
	INSERT INTO etl.ext_stg_coa_location_feed(
            agency_code, location_code, location_name, location_short_name, 
            upper_case_name, col6, col7, col8, col9, col10, col11, col12, 
            col13, col14, col15, col16, col17)
    SELECT agency_code, location_code, location_name, location_short_name, 
            upper_case_name, col6, col7, col8, col9, col10, col11, col12, 
            col13, col14, col15, col16, col17
	FROM  etl.foreign_tbl_coa_location_feed;

	
	ELSIF(l_data_source_code = 'FC') THEN
	
	TRUNCATE  etl.ext_stg_funding_class;
	
	INSERT INTO etl.ext_stg_funding_class(
            doc_dept_cd, fy, funding_class_code, funding_class_name, short_name, 
            category_name, cty_fund_fl, intr_cty_fl, fund_aloc_req_fl, tbl_last_dt, 
            ams_row_vers_no, rsfcls_nm_up, fund_category)
    SELECT  doc_dept_cd, fy, funding_class_code, funding_class_name, short_name, 
            category_name, cty_fund_fl, intr_cty_fl, fund_aloc_req_fl, tbl_last_dt, 
            ams_row_vers_no, rsfcls_nm_up, fund_category
	FROM  etl.foreign_tbl_funding_class;

	
	ELSIF(l_data_source_code = 'BC') THEN
	
	TRUNCATE   etl.ext_stg_coa_budget_code_feed;
	
	
	INSERT INTO etl.ext_stg_coa_budget_code_feed(
            fy, fcls_cd, fcls_nm, dept_cd, dept_nm, func_cd, func_nm, func_attr_nm, 
            func_attr_sh_nm, resp_ctr, func_anlys_unit, cntrl_cat, local_svc_dist, 
            ua_fund_fl, pyrl_dflt_fl, bud_cat_a, bud_cat_b, bud_func, dscr_ext, 
            tbl_last_dt, func_attr_nm_up, fin_plan_sav_fl, col23)
    SELECT  fy, fcls_cd, fcls_nm, dept_cd, dept_nm, func_cd, func_nm, func_attr_nm, 
            func_attr_sh_nm, resp_ctr, func_anlys_unit, cntrl_cat, local_svc_dist, 
            ua_fund_fl, pyrl_dflt_fl, bud_cat_a, bud_cat_b, bud_func, dscr_ext, 
            tbl_last_dt, func_attr_nm_up, fin_plan_sav_fl, col23
	FROM  etl.foreign_tbl_coa_budget_code_feed ;

	
	
	ELSIF(l_data_source_code = 'V') THEN
	
	TRUNCATE  etl.ext_stg_fmsv_data_feed;
	
	INSERT INTO etl.ext_stg_fmsv_data_feed(
            record_type, doc_dept_cd, vend_cust_cd, bus_typ, bus_typ_sta, 
            min_typ, disp_cert_strt_dt, cert_end_dt, init_dt, col10)
    SELECT  record_type, doc_dept_cd, vend_cust_cd, bus_typ, bus_typ_sta, 
            min_typ, disp_cert_strt_dt, cert_end_dt, init_dt, col10
	FROM etl.foreign_tbl_fmsv_data_feed ;
	
	ELSIF(l_data_source_code = 'RY') THEN
	
	TRUNCATE  etl.ext_stg_coa_revenue_category_feed;
	
	INSERT INTO etl.ext_stg_coa_revenue_category_feed(
            doc_dept_cd, rscat_cd, rscat_nm, rscat_sh_nm, act_fl, efbgn_dt, 
            efend_dt, alw_bud_fl, rscat_dscr, cntac_cd, rscat_nm_up, tbl_last_dt, 
            col13)
    SELECT  doc_dept_cd, rscat_cd, rscat_nm, rscat_sh_nm, act_fl, efbgn_dt, 
            efend_dt, alw_bud_fl, rscat_dscr, cntac_cd, rscat_nm_up, tbl_last_dt, 
            col13
	FROM  etl.foreign_tbl_coa_revenue_category_feed;

	
	ELSIF(l_data_source_code = 'RC') THEN
	
	TRUNCATE  etl.ext_stg_coa_revenue_class_feed;
	
	INSERT INTO etl.ext_stg_coa_revenue_class_feed(
            doc_dept_cd, rscls_cd, rscls_nm, rscls_sh_nm, act_fl, efbgn_dt, 
            efend_dt, alw_bud_fl, rscls_dscr, cntac_cd, rscls_nm_up, tbl_last_dt, 
            col13)
    SELECT  doc_dept_cd, rscls_cd, rscls_nm, rscls_sh_nm, act_fl, efbgn_dt, 
            efend_dt, alw_bud_fl, rscls_dscr, cntac_cd, rscls_nm_up, tbl_last_dt, 
            col13
	FROM  etl.foreign_tbl_coa_revenue_class_feed;

	
	ELSIF(l_data_source_code = 'RS') THEN
	
	TRUNCATE  etl.ext_stg_coa_revenue_source_feed;
	
	INSERT INTO etl.ext_stg_coa_revenue_source_feed(
            doc_dept_cd, fy, rsrc_cd, rsrc_nm, rsrc_sh_nm, act_fl, efbgn_dt, 
            efend_dt, alw_bud_fl, oper_ind, fasb_cls_ind, fhwa_rev_cr_fl, 
            usetax_coll_fl, rscls_cd, rscat_cd, rstyp_cd, rsgrp_cd, mjr_crtyp_cd, 
            mnr_crtyp_cd, rsrc_dscr, cntac_cd, billu_rcvb_cd, billu_rcvb_s, 
            bille_rcvb_cd, bille_rcvb_s, billu_rev_cd, billu_rev_s, collu_rev_cd, 
            collu_rev_s, alw_bdebt_cd, alw_bdebt_s, bdebt_exp_obj, bdebt_exp_obj_s, 
            bill_dps_cd, bill_dps_s, coll_dps_cd, coll_dps_s, nsf_ckcg_rsrc, 
            nsf_ckcg_rsrc_s, intch_rsrc, intch_rsrc_s, lat_chrg_rsrc, lat_chrg_rsrc_s, 
            cc_fee_rsrc, cc_fee_rsrc_s, cc_fee_obj, cc_fee_obj_s, fin_chrg_fee1_cd, 
            fin_chrg_fee2_cd, fin_chrg_fee3_cd, fin_chrg_fee4_cd, fin_chrg_fee5_cd, 
            apy_intr_lat_fee, apy_intr_admn_fee, apy_intr_nsf_fee, apy_intr_othr_fee, 
            elg_inct_fl, rsrc_xfer_fl, bill_vend_rfnd_cd, bill_vend_rfnd_s, 
            uern_rcvb_wo_cd, uern_rcvb_wo_s, dps_rcvb_wo_cd, dps_rcvb_wo_s, 
            uern_rev_wo_cd, uern_rev_wo_s, dps_wo_cd, dps_wo_s, vrfnd_rcvb_wo_cd, 
            vrfnd_rcvb_wo_s, vrfnd_wo_cd, vrfnd_wo_s, ernrev_to_coll_cd, 
            ernrev_to_coll_s, vrfnd_to_coll_cd, vrfnd_to_coll_s, vend_rha_cd, 
            vend_rha_s, rs_opay_cd, rs_opay_s, urs_opay_cd, urs_opay_s, bill_dps_rec_cd, 
            bill_dps_rec_s, earn_rcvb_cd, earn_rcvb_s, rsrc_nm_up, rsrc_sh_nm_up, 
            fin_fee_ov_fl, apy_intr_ov, tbl_last_dt, ext_rep_nm, fund_cls, 
            fund_cls_nm, grnt_id, bill_lag_dy, bill_freq, bill_fy_strt_mnth, 
            bill_fy_strt_dy, fed_agcy_cd, fed_agcy_sfx, fed_nm, ext_rep_num, 
            dscr_ext, srsrc_req, col106)
    SELECT doc_dept_cd, fy, rsrc_cd, rsrc_nm, rsrc_sh_nm, act_fl, efbgn_dt, 
            efend_dt, alw_bud_fl, oper_ind, fasb_cls_ind, fhwa_rev_cr_fl, 
            usetax_coll_fl, rscls_cd, rscat_cd, rstyp_cd, rsgrp_cd, mjr_crtyp_cd, 
            mnr_crtyp_cd, rsrc_dscr, cntac_cd, billu_rcvb_cd, billu_rcvb_s, 
            bille_rcvb_cd, bille_rcvb_s, billu_rev_cd, billu_rev_s, collu_rev_cd, 
            collu_rev_s, alw_bdebt_cd, alw_bdebt_s, bdebt_exp_obj, bdebt_exp_obj_s, 
            bill_dps_cd, bill_dps_s, coll_dps_cd, coll_dps_s, nsf_ckcg_rsrc, 
            nsf_ckcg_rsrc_s, intch_rsrc, intch_rsrc_s, lat_chrg_rsrc, lat_chrg_rsrc_s, 
            cc_fee_rsrc, cc_fee_rsrc_s, cc_fee_obj, cc_fee_obj_s, fin_chrg_fee1_cd, 
            fin_chrg_fee2_cd, fin_chrg_fee3_cd, fin_chrg_fee4_cd, fin_chrg_fee5_cd, 
            apy_intr_lat_fee, apy_intr_admn_fee, apy_intr_nsf_fee, apy_intr_othr_fee, 
            elg_inct_fl, rsrc_xfer_fl, bill_vend_rfnd_cd, bill_vend_rfnd_s, 
            uern_rcvb_wo_cd, uern_rcvb_wo_s, dps_rcvb_wo_cd, dps_rcvb_wo_s, 
            uern_rev_wo_cd, uern_rev_wo_s, dps_wo_cd, dps_wo_s, vrfnd_rcvb_wo_cd, 
            vrfnd_rcvb_wo_s, vrfnd_wo_cd, vrfnd_wo_s, ernrev_to_coll_cd, 
            ernrev_to_coll_s, vrfnd_to_coll_cd, vrfnd_to_coll_s, vend_rha_cd, 
            vend_rha_s, rs_opay_cd, rs_opay_s, urs_opay_cd, urs_opay_s, bill_dps_rec_cd, 
            bill_dps_rec_s, earn_rcvb_cd, earn_rcvb_s, rsrc_nm_up, rsrc_sh_nm_up, 
            fin_fee_ov_fl, apy_intr_ov, tbl_last_dt, ext_rep_nm, fund_cls, 
            fund_cls_nm, grnt_id, bill_lag_dy, bill_freq, bill_fy_strt_mnth, 
            bill_fy_strt_dy, fed_agcy_cd, fed_agcy_sfx, fed_nm, ext_rep_num, 
            dscr_ext, srsrc_req, col106
	FROM  etl.foreign_tbl_coa_revenue_source_feed;
	
	
	ELSIF(l_data_source_code = 'RB') THEN
		
	TRUNCATE  etl.ext_stg_revenue_budget;

	INSERT INTO etl.ext_stg_revenue_budget(
            bfy, fcls_cd, dept_cd, func_cd, revenue_source, adpt_am, curr_bud_am, 
            col8, col9)
    SELECT bfy, fcls_cd, dept_cd, func_cd, revenue_source, adpt_am, curr_bud_am, 
            col8, col9
	FROM  etl.foreign_tbl_revenue_budget;
	
	
	ELSIF(l_data_source_code = 'B') THEN
	
	TRUNCATE  etl.ext_stg_budget_feed;
	
	INSERT INTO etl.ext_stg_budget_feed(
            budget_fiscal_year, fund_class_code, agency_code, department_code, 
            budget_code, object_class_code, adopted_amount, current_budget_amount, 
            pre_encumbered_amount, encumbered_amount, accrued_expense_amount, 
            cash_expense_amount, post_closing_adjustment_amount, updated_date, 
            col15)
    SELECT  budget_fiscal_year, fund_class_code, agency_code, department_code, 
            budget_code, object_class_code, adopted_amount, current_budget_amount, 
            pre_encumbered_amount, encumbered_amount, accrued_expense_amount, 
            cash_expense_amount, post_closing_adjustment_amount, updated_date, 
            col15
	FROM  etl.foreign_tbl_budget_feed;
	
	ELSIF(l_data_source_code = 'R') THEN
	
	TRUNCATE  etl.ext_stg_revenue;
	
	INSERT INTO etl.ext_stg_revenue(
            doc_rec_dt, per_dc, fy_dc, bfy, fqtr, evnt_cat_id, evnt_typ_id, 
            bank_acct_cd, pstng_pr_typ, pstng_cd_id, drcr_ind, ln_func_cd, 
            pstng_am, incr_dcrs_ind, run_tmdt, fund_cd, sfund_cd, bsa_cd, 
            sbsa_cd, bsa_typ_ind, obj_cd, sobj_cd, rsrc_cd, srsrc_cd, govt_brn_cd, 
            cab_cd, dept_cd, div_cd, gp_cd, sect_cd, dstc_cd, bur_cd, unit_cd, 
            sunit_cd, mjr_prog_cd, prog_cd, phase_cd, task_ord_cd, task_cd, 
            stask_cd, ppc_cd, fprfl_cd, fline_cd, fprty_cd, appr_cd, actv_cd, 
            sactv_cd, func_cd, sfunc_cd, rpt_cd, srpt_cd, dobj_cd, drsrc_cd, 
            loc_cd, sloc_cd, ig_fund_cd, ig_sfund_cd, ig_dept_cd, fcls_cd, 
            fcat_cd, ftyp_cd, fgrp_cd, cafrfgrp_cd, cafrftyp_cd, bscl_cd, 
            bsct_cd, bst_cd, bsg_cd, cmjrbgrp_cd, cmnrbgrp_cd, bsa_ov_fl, 
            ocls_cd, ocat_cd, otyp_cd, ogrp_cd, mjr_cetyp_cd, mnr_cetyp_cd, 
            rscls_cd, rscat_cd, rstyp_cd, rsgrp_cd, mjr_crtyp_cd, mnr_crtyp_cd, 
            apcls_cd, apcat_cd, aptyp_cd, apgrp_cd, lcls_cd, lcat_cd, ltyp_cd, 
            cnty_cd, acls_cd, acat_cd, atyp_cd, agrp_cd, caunit_cd, mjr_catyp_cd, 
            mnr_catyp_cd, fncls_cd, fncat_cd, fntyp_cd, fngrp_cd, rcls_cd, 
            rcat_cd, rtyp_cd, rgrp_cd, docls_cd, docat_cd, dotyp_cd, dogrp_cd, 
            drscls_cd, drscat_cd, drstyp_cd, drsgrp_cd, mjr_pcls_cd, mjr_pcat_cd, 
            mjr_ptyp_cd, mjr_pgrp_cd, pcls_cd, pcat_cd, ptyp_cd, pgrp_cd, 
            doc_cat, doc_typ, doc_cd, doc_dept_cd, doc_id, doc_vers_no, doc_func_cd, 
            doc_vend_ln_no, doc_unit_cd, doc_comm_ln_no, doc_actg_ln_no, 
            doc_pstng_ln_no, doc_last_usid, rfed_doc_cd, rfed_doc_dept_cd, 
            rfed_doc_id, rfed_vend_ln_no, rfed_comm_ln_no, rfed_actg_ln_no, 
            rfed_pstng_ln_no, rf_typ, stpf_cd, assoc_inv_no, assoc_inv_ln_no, 
            assoc_inv_dt, vend_cust_cd, vend_cust_ind, lgl_nm, bpro_cd, actg_ln_dscr, 
            misc3, svc_frm_dt, svc_to_dt, whse_cd, comm_cd, stk_itm_sfx, 
            reas_cd, tin, tin_typ, chk_eft_no, reclass_ind_fl, pscd_clos_cl_cd, 
            pscd_clos_cl_nm, col)
    SELECT doc_rec_dt, per_dc, fy_dc, bfy, fqtr, evnt_cat_id, evnt_typ_id, 
            bank_acct_cd, pstng_pr_typ, pstng_cd_id, drcr_ind, ln_func_cd, 
            pstng_am, incr_dcrs_ind, run_tmdt, fund_cd, sfund_cd, bsa_cd, 
            sbsa_cd, bsa_typ_ind, obj_cd, sobj_cd, rsrc_cd, srsrc_cd, govt_brn_cd, 
            cab_cd, dept_cd, div_cd, gp_cd, sect_cd, dstc_cd, bur_cd, unit_cd, 
            sunit_cd, mjr_prog_cd, prog_cd, phase_cd, task_ord_cd, task_cd, 
            stask_cd, ppc_cd, fprfl_cd, fline_cd, fprty_cd, appr_cd, actv_cd, 
            sactv_cd, func_cd, sfunc_cd, rpt_cd, srpt_cd, dobj_cd, drsrc_cd, 
            loc_cd, sloc_cd, ig_fund_cd, ig_sfund_cd, ig_dept_cd, fcls_cd, 
            fcat_cd, ftyp_cd, fgrp_cd, cafrfgrp_cd, cafrftyp_cd, bscl_cd, 
            bsct_cd, bst_cd, bsg_cd, cmjrbgrp_cd, cmnrbgrp_cd, bsa_ov_fl, 
            ocls_cd, ocat_cd, otyp_cd, ogrp_cd, mjr_cetyp_cd, mnr_cetyp_cd, 
            rscls_cd, rscat_cd, rstyp_cd, rsgrp_cd, mjr_crtyp_cd, mnr_crtyp_cd, 
            apcls_cd, apcat_cd, aptyp_cd, apgrp_cd, lcls_cd, lcat_cd, ltyp_cd, 
            cnty_cd, acls_cd, acat_cd, atyp_cd, agrp_cd, caunit_cd, mjr_catyp_cd, 
            mnr_catyp_cd, fncls_cd, fncat_cd, fntyp_cd, fngrp_cd, rcls_cd, 
            rcat_cd, rtyp_cd, rgrp_cd, docls_cd, docat_cd, dotyp_cd, dogrp_cd, 
            drscls_cd, drscat_cd, drstyp_cd, drsgrp_cd, mjr_pcls_cd, mjr_pcat_cd, 
            mjr_ptyp_cd, mjr_pgrp_cd, pcls_cd, pcat_cd, ptyp_cd, pgrp_cd, 
            doc_cat, doc_typ, doc_cd, doc_dept_cd, doc_id, doc_vers_no, doc_func_cd, 
            doc_vend_ln_no, doc_unit_cd, doc_comm_ln_no, doc_actg_ln_no, 
            doc_pstng_ln_no, doc_last_usid, rfed_doc_cd, rfed_doc_dept_cd, 
            rfed_doc_id, rfed_vend_ln_no, rfed_comm_ln_no, rfed_actg_ln_no, 
            rfed_pstng_ln_no, rf_typ, stpf_cd, assoc_inv_no, assoc_inv_ln_no, 
            assoc_inv_dt, vend_cust_cd, vend_cust_ind, lgl_nm, bpro_cd, actg_ln_dscr, 
            misc3, svc_frm_dt, svc_to_dt, whse_cd, comm_cd, stk_itm_sfx, 
            reas_cd, tin, tin_typ, chk_eft_no, reclass_ind_fl, pscd_clos_cl_cd, 
            pscd_clos_cl_nm, col
	FROM etl.foreign_tbl_revenue;

	
	ELSIF(l_data_source_code = 'M') THEN
	
	TRUNCATE etl.ext_stg_mag_data_feed;
	
	INSERT INTO etl.ext_stg_mag_data_feed(
            record_type, doc_cd, doc_dept_cd, doc_id, doc_vers_no, col6, 
            col7, col8, col9, col10, col11, col12, col13, col14, col15, col16, 
            col17, col18, col19, col20, col21, col22, col23, col24, col25, 
            col26, col27, col28, col29, col30, col31, col32, col33, col34, 
            col35, col36, col37, col38, col39, col40, col41, col42, col43, 
            col44, col45, col46, col47, col48, col49, col50, col51, col52, 
            col53, col54, col55, col56, col57, col58, col59, col60, col61, 
            col62, col63, col64, col65, col66, col67, col68, col69, col70, 
            col71, col72, col73, col74, col75, col76, col77, col78, col79, 
            col80, col81, col82, col83, col84, col85, col86, col87, col88, 
            col89, col90, col91, col92, col93, col94, col95)
    SELECT record_type, doc_cd, doc_dept_cd, doc_id, doc_vers_no, col6, 
            col7, col8, col9, col10, col11, col12, col13, col14, col15, col16, 
            col17, col18, col19, col20, col21, col22, col23, col24, col25, 
            col26, col27, col28, col29, col30, col31, col32, col33, col34, 
            col35, col36, col37, col38, col39, col40, col41, col42, col43, 
            col44, col45, col46, col47, col48, col49, col50, col51, col52, 
            col53, col54, col55, col56, col57, col58, col59, col60, col61, 
            col62, col63, col64, col65, col66, col67, col68, col69, col70, 
            col71, col72, col73, col74, col75, col76, col77, col78, col79, 
            col80, col81, col82, col83, col84, col85, col86, col87, col88, 
            col89, col90, col91, col92, col93, col94, col95
	FROM etl.foreign_tbl_mag_data_feed_header;
	
	INSERT INTO etl.ext_stg_mag_data_feed(
            record_type, doc_cd, doc_dept_cd, doc_id, doc_vers_no, col6, 
            col7, col8, col9, col10, col11, col12, col13, col14, col15, col16, 
            col17, col18, col19, col20, col21, col22, col23, col24, col25, 
            col26, col27, col28, col29, col30, col31, col32, col33, col34, 
            col35, col36, col37, col38, col39, col40, col41, col42)
    SELECT record_type, doc_cd, doc_dept_cd, doc_id, doc_vers_no, col6, 
            col7, col8, col9, col10, col11, col12, col13, col14, col15, col16, 
            col17, col18, col19, col20, col21, col22, col23, col24, col25, 
            col26, col27, col28, col29, col30, col31, col32, col33, col34, 
            col35, col36, col37, col38, col39, col40, col41, col42
	FROM etl.foreign_tbl_mag_data_feed_vendor;
	
	INSERT INTO etl.ext_stg_mag_data_feed(
            record_type, doc_cd, doc_dept_cd, doc_id, doc_vers_no, col6, 
            col7, col8, col9, col10, col11, col12, col13, col14, col15, col16, 
            col17, col18, col19, col20, col21, col22, col23, col24, col25, 
            col26, col27, col28, col29, col30, col31, col32, col33, col34, 
            col35, col36, col37, col38, col39, col40, col41, col42, col43, 
            col44, col45, col46, col47, col48, col49, col50, col51, col52, 
            col53, col54, col55, col56, col57, col58, col59, col60, col61, 
            col62, col63, col64, col65, col66, col67, col68, col69)
    SELECT record_type, doc_cd, doc_dept_cd, doc_id, doc_vers_no, col6, 
            col7, col8, col9, col10, col11, col12, col13, col14, col15, col16, 
            col17, col18, col19, col20, col21, col22, col23, col24, col25, 
            col26, col27, col28, col29, col30, col31, col32, col33, col34, 
            col35, col36, col37, col38, col39, col40, col41, col42, col43, 
            col44, col45, col46, col47, col48, col49, col50, col51, col52, 
            col53, col54, col55, col56, col57, col58, col59, col60, col61, 
            col62, col63, col64, col65, col66, col67, col68, col69
	FROM etl.foreign_tbl_mag_data_feed_detail;
	
	ELSIF(l_data_source_code = 'C') THEN
	
	TRUNCATE  etl.ext_stg_con_data_feed;
	
	
	INSERT INTO etl.ext_stg_con_data_feed(
            record_type, doc_cd, doc_dept_cd, doc_id, doc_vers_no, col6, 
            col7, col8, col9, col10, col11, col12, col13, col14, col15, col16, 
            col17, col18, col19, col20, col21, col22, col23, col24, col25, 
            col26, col27, col28, col29, col30, col31, col32, col33, col34, 
            col35, col36, col37, col38, col39, col40, col41, col42, col43, 
            col44, col45, col46, col47, col48, col49, col50, col51, col52, 
            col53, col54, col55, col56, col57, col58, col59, col60, col61, 
            col62, col63, col64, col65, col66, col67, col68, col69, col70, 
            col71, col72, col73, col74, col75, col76, col77, col78, col79, 
            col80, col81, col82, col83, col84, col85, col86, col87, col88, 
            col89, col90, col91, col92, col93, col94, col95, col96, col97, 
            col98, col99, col100, col101, col102, col103, col104, col105, 
            col106, col107, col108, col109, col110, col111, col112, col113)
    SELECT  record_type, doc_cd, doc_dept_cd, doc_id, doc_vers_no, col6, 
            col7, col8, col9, col10, col11, col12, col13, col14, col15, col16, 
            col17, col18, col19, col20, col21, col22, col23, col24, col25, 
            col26, col27, col28, col29, col30, col31, col32, col33, col34, 
            col35, col36, col37, col38, col39, col40, col41, col42, col43, 
            col44, col45, col46, col47, col48, col49, col50, col51, col52, 
            col53, col54, col55, col56, col57, col58, col59, col60, col61, 
            col62, col63, col64, col65, col66, col67, col68, col69, col70, 
            col71, col72, col73, col74, col75, col76, col77, col78, col79, 
            col80, col81, col82, col83, col84, col85, col86, col87, col88, 
            col89, col90, col91, col92, col93, col94, col95, col96, col97, 
            col98, col99, col100, col101, col102, col103, col104, col105, 
            col106, col107, col108, col109, col110, col111, col112, col113
	FROM etl.foreign_tbl_con_ct_data_feed_header;
	
	
	
	
	INSERT INTO etl.ext_stg_con_data_feed(
            record_type, doc_cd, doc_dept_cd, doc_id, doc_vers_no, col6, 
            col7, col8, col9, col10, col11, col12, col13, col14, col15, col16, 
            col17, col18, col19, col20, col21, col22, col23, col24, col25, 
            col26, col27, col28, col29, col30, col31, col32, col33, col34, 
            col35, col36, col37, col38, col39, col40)
    SELECT  record_type, doc_cd, doc_dept_cd, doc_id, doc_vers_no, col6, 
            col7, col8, col9, col10, col11, col12, col13, col14, col15, col16, 
            col17, col18, col19, col20, col21, col22, col23, col24, col25, 
            col26, col27, col28, col29, col30, col31, col32, col33, col34, 
            col35, col36, col37, col38, col39, col40
	FROM etl.foreign_tbl_con_ct_data_feed_vendor;
	
	
	
	INSERT INTO etl.ext_stg_con_data_feed(
            record_type, doc_cd, doc_dept_cd, doc_id, doc_vers_no, col6, 
            col7, col8, col9, col10, col11, col12, col13, col14, col15, col16, 
            col17, col18, col19, col20, col21, col22, col23, col24, col25, 
            col26, col27, col28, col29, col30, col31, col32, col33, col34, 
            col35, col36, col37, col38, col39, col40, col41, col42, col43, 
            col44, col45, col46, col47, col48, col49, col50, col51, col52, 
            col53, col54, col55, col56, col57, col58, col59, col60, col61, 
            col62, col63, col64, col65, col66, col67, col68, col69)
    SELECT  record_type, doc_cd, doc_dept_cd, doc_id, doc_vers_no, col6, 
            col7, col8, col9, col10, col11, col12, col13, col14, col15, col16, 
            col17, col18, col19, col20, col21, col22, col23, col24, col25, 
            col26, col27, col28, col29, col30, col31, col32, col33, col34, 
            col35, col36, col37, col38, col39, col40, col41, col42, col43, 
            col44, col45, col46, col47, col48, col49, col50, col51, col52, 
            col53, col54, col55, col56, col57, col58, col59, col60, col61, 
            col62, col63, col64, col65, col66, col67, col68, col69
	FROM etl.foreign_tbl_con_ct_data_feed_detail;
	
	
	
	INSERT INTO etl.ext_stg_con_data_feed(
            record_type, doc_cd, doc_dept_cd, doc_id, doc_vers_no, col6, 
            col7, col8, col9, col10, col11, col12, col13, col14, col15, col16, 
            col17, col18, col19, col20, col21, col22, col23, col24, col25, 
            col26, col27, col28, col29, col30, col31, col32, col33, col34, 
            col35, col36, col37, col38, col39, col40, col41, col42, col43, 
            col44, col45, col46, col47, col48, col49, col50, col51, col52, 
            col53, col54, col55, col56, col57, col58, col59, col60, col61, 
            col62, col63, col64, col65, col66, col67, col68, col69, col70, 
            col71)
    SELECT  record_type, doc_cd, doc_dept_cd, doc_id, doc_vers_no, col6, 
            col7, col8, col9, col10, col11, col12, col13, col14, col15, col16, 
            col17, col18, col19, col20, col21, col22, col23, col24, col25, 
            col26, col27, col28, col29, col30, col31, col32, col33, col34, 
            col35, col36, col37, col38, col39, col40, col41, col42, col43, 
            col44, col45, col46, col47, col48, col49, col50, col51, col52, 
            col53, col54, col55, col56, col57, col58, col59, col60, col61, 
            col62, col63, col64, col65, col66, col67, col68, col69, col70, 
            col71
	FROM etl.foreign_tbl_con_ct_data_feed_acc_line;
	
	
	INSERT INTO etl.ext_stg_con_data_feed(
            record_type, doc_cd, doc_dept_cd, doc_id, doc_vers_no, col6, 
            col7, col8, col9, col10, col11, col12, col13, col14, col15, col16, 
            col17, col18, col19, col20, col21, col22, col23, col24, col25, 
            col26, col27, col28, col29, col30, col31, col32, col33, col34, 
            col35, col36, col37, col38, col39, col40, col41, col42, col43, 
            col44, col45, col46, col47, col48, col49, col50, col51, col52, 
            col53, col54, col55, col56, col57, col58, col59, col60, col61, 
            col62, col63, col64, col65, col66, col67, col68, col69, col70, 
            col71, col72, col73, col74, col75, col76, col77, col78, col79, 
            col80, col81, col82, col83, col84, col85, col86, col87, col88, 
            col89, col90)
    SELECT  record_type, doc_cd, doc_dept_cd, doc_id, doc_vers_no, col6, 
            col7, col8, col9, col10, col11, col12, col13, col14, col15, col16, 
            col17, col18, col19, col20, col21, col22, col23, col24, col25, 
            col26, col27, col28, col29, col30, col31, col32, col33, col34, 
            col35, col36, col37, col38, col39, col40, col41, col42, col43, 
            col44, col45, col46, col47, col48, col49, col50, col51, col52, 
            col53, col54, col55, col56, col57, col58, col59, col60, col61, 
            col62, col63, col64, col65, col66, col67, col68, col69, col70, 
            col71, col72, col73, col74, col75, col76, col77, col78, col79, 
            col80, col81, col82, col83, col84, col85, col86, col87, col88, 
            col89, col90
	FROM etl.foreign_tbl_con_do1_data_feed_header;
	
	
	INSERT INTO etl.ext_stg_con_data_feed(
            record_type, doc_cd, doc_dept_cd, doc_id, doc_vers_no, col6, 
            col7, col8, col9, col10, col11, col12, col13, col14, col15, col16, 
            col17, col18, col19, col20, col21, col22, col23, col24, col25, 
            col26, col27, col28, col29, col30, col31, col32, col33, col34, 
            col35, col36, col37)
    SELECT  record_type, doc_cd, doc_dept_cd, doc_id, doc_vers_no, col6, 
            col7, col8, col9, col10, col11, col12, col13, col14, col15, col16, 
            col17, col18, col19, col20, col21, col22, col23, col24, col25, 
            col26, col27, col28, col29, col30, col31, col32, col33, col34, 
            col35, col36, col37
	FROM etl.foreign_tbl_con_do1_data_feed_vendor ;
	
	
	INSERT INTO etl.ext_stg_con_data_feed(
            record_type, doc_cd, doc_dept_cd, doc_id, doc_vers_no, col6, 
            col7, col8, col9, col10, col11, col12, col13, col14, col15, col16, 
            col17, col18, col19, col20, col21, col22, col23, col24, col25, 
            col26, col27, col28, col29, col30, col31, col32, col33, col34, 
            col35, col36, col37, col38, col39, col40, col41, col42, col43, 
            col44, col45, col46, col47, col48, col49, col50, col51, col52, 
            col53, col54, col55, col56, col57, col58, col59, col60, col61, 
            col62, col63, col64, col65, col66, col67, col68, col69)
    SELECT  record_type, doc_cd, doc_dept_cd, doc_id, doc_vers_no, col6, 
            col7, col8, col9, col10, col11, col12, col13, col14, col15, col16, 
            col17, col18, col19, col20, col21, col22, col23, col24, col25, 
            col26, col27, col28, col29, col30, col31, col32, col33, col34, 
            col35, col36, col37, col38, col39, col40, col41, col42, col43, 
            col44, col45, col46, col47, col48, col49, col50, col51, col52, 
            col53, col54, col55, col56, col57, col58, col59, col60, col61, 
            col62, col63, col64, col65, col66, col67, col68, col69
	FROM etl.foreign_tbl_con_do1_data_feed_acc_line;
	
	
	INSERT INTO etl.ext_stg_con_data_feed(
            record_type, doc_cd, doc_dept_cd, doc_id, doc_vers_no, col6, 
            col7, col8, col9, col10, col11, col12, col13, col14, col15, col16, 
            col17, col18, col19, col20, col21, col22, col23, col24, col25, 
            col26, col27, col28, col29, col30, col31, col32, col33, col34, 
            col35, col36, col37, col38, col39, col40, col41, col42, col43, 
            col44, col45, col46, col47, col48, col49, col50, col51, col52, 
            col53, col54, col55, col56, col57, col58, col59, col60, col61, 
            col62, col63, col64, col65, col66, col67, col68, col69, col70, 
            col71, col72, col73, col74, col75, col76, col77, col78, col79, 
            col80, col81, col82, col83, col84, col85, col86, col87, col88, 
            col89, col90, col91, col92, col93, col94, col95)
    SELECT  record_type, doc_cd, doc_dept_cd, doc_id, doc_vers_no, col6, 
            col7, col8, col9, col10, col11, col12, col13, col14, col15, col16, 
            col17, col18, col19, col20, col21, col22, col23, col24, col25, 
            col26, col27, col28, col29, col30, col31, col32, col33, col34, 
            col35, col36, col37, col38, col39, col40, col41, col42, col43, 
            col44, col45, col46, col47, col48, col49, col50, col51, col52, 
            col53, col54, col55, col56, col57, col58, col59, col60, col61, 
            col62, col63, col64, col65, col66, col67, col68, col69, col70, 
            col71, col72, col73, col74, col75, col76, col77, col78, col79, 
            col80, col81, col82, col83, col84, col85, col86, col87, col88, 
            col89, col90, col91, col92, col93, col94, col95
	FROM etl.foreign_tbl_con_po_data_feed_header;
	
	
	INSERT INTO etl.ext_stg_con_data_feed(
            record_type, doc_cd, doc_dept_cd, doc_id, doc_vers_no, col6, 
            col7, col8, col9, col10, col11, col12, col13, col14, col15, col16, 
            col17, col18, col19, col20, col21, col22, col23, col24, col25, 
            col26, col27, col28, col29, col30, col31, col32, col33, col34, 
            col35, col36, col37, col38)
    SELECT  record_type, doc_cd, doc_dept_cd, doc_id, doc_vers_no, col6, 
            col7, col8, col9, col10, col11, col12, col13, col14, col15, col16, 
            col17, col18, col19, col20, col21, col22, col23, col24, col25, 
            col26, col27, col28, col29, col30, col31, col32, col33, col34, 
            col35, col36, col37, col38
	FROM etl.foreign_tbl_con_po_data_feed_vendor;
	
	
	INSERT INTO etl.ext_stg_con_data_feed(
            record_type, doc_cd, doc_dept_cd, doc_id, doc_vers_no, col6, 
            col7, col8, col9, col10, col11, col12, col13, col14, col15, col16, 
            col17, col18, col19, col20, col21, col22, col23, col24, col25, 
            col26, col27, col28, col29, col30, col31, col32, col33, col34, 
            col35)
    SELECT  record_type, doc_cd, doc_dept_cd, doc_id, doc_vers_no, col6, 
            col7, col8, col9, col10, col11, col12, col13, col14, col15, col16, 
            col17, col18, col19, col20, col21, col22, col23, col24, col25, 
            col26, col27, col28, col29, col30, col31, col32, col33, col34, 
            col35
	FROM etl.foreign_tbl_con_po_data_feed_detail;
	
	
	INSERT INTO etl.ext_stg_con_data_feed(
            record_type, doc_cd, doc_dept_cd, doc_id, doc_vers_no, col6, 
            col7, col8, col9, col10, col11, col12, col13, col14, col15, col16, 
            col17, col18, col19, col20, col21, col22, col23, col24, col25, 
            col26, col27, col28, col29, col30, col31, col32, col33, col34, 
            col35, col36, col37, col38, col39, col40, col41, col42, col43, 
            col44, col45, col46, col47, col48, col49, col50, col51, col52, 
            col53, col54, col55, col56, col57, col58, col59, col60, col61, 
            col62, col63, col64, col65, col66, col67, col68, col69, col70, 
            col71)
    SELECT  record_type, doc_cd, doc_dept_cd, doc_id, doc_vers_no, col6, 
            col7, col8, col9, col10, col11, col12, col13, col14, col15, col16, 
            col17, col18, col19, col20, col21, col22, col23, col24, col25, 
            col26, col27, col28, col29, col30, col31, col32, col33, col34, 
            col35, col36, col37, col38, col39, col40, col41, col42, col43, 
            col44, col45, col46, col47, col48, col49, col50, col51, col52, 
            col53, col54, col55, col56, col57, col58, col59, col60, col61, 
            col62, col63, col64, col65, col66, col67, col68, col69, col70, 
            col71
	FROM etl.foreign_tbl_con_po_data_feed_acc_line;
	
	ELSIF(l_data_source_code = 'PC') THEN
	
	TRUNCATE  etl.ext_stg_oaisis_feed;
	
	INSERT INTO etl.ext_stg_oaisis_feed(
            con_trans_code, con_trans_ad_code, con_no, con_par_trans_code, 
            con_par_ad_code, con_par_reg_num, con_cur_encumbrance, con_original_max, 
            con_rev_max, vc_legal_name, con_vc_code, con_purpose, submitting_agency_desc, 
            submitting_agency_code, awarding_agency_desc, awarding_agency_code, 
            cont_desc, cont_code, am_desc, am_code, con_term_from, con_term_to, 
            con_rev_start_dt, con_rev_end_dt, con_cif_received_date, con_pin, 
            con_internal_pin, con_batch_suffix, con_version, original_or_modified, 
            award_category_code)
    SELECT  con_trans_code, con_trans_ad_code, con_no, con_par_trans_code, 
            con_par_ad_code, con_par_reg_num, con_cur_encumbrance, con_original_max, 
            con_rev_max, vc_legal_name, con_vc_code, con_purpose, submitting_agency_desc, 
            submitting_agency_code, awarding_agency_desc, awarding_agency_code, 
            cont_desc, cont_code, am_desc, am_code, con_term_from, con_term_to, 
            con_rev_start_dt, con_rev_end_dt, con_cif_received_date, con_pin, 
            con_internal_pin, con_batch_suffix, con_version, original_or_modified, 
            award_category_code
	FROM etl.foreign_tbl_oaisis_feed;
	
	ELSIF(l_data_source_code = 'F') THEN
	
	TRUNCATE  etl.ext_stg_fms_data_feed;
	
	
	INSERT INTO etl.ext_stg_fms_data_feed(
            record_type, doc_cd, doc_dept_cd, doc_id, doc_vers_no, col6, 
            col7, col8, col9, col10, col11, col12, col13, col14, col15, col16, 
            col17, col18, col19, col20, col21, col22)
    SELECT  record_type, doc_cd, doc_dept_cd, doc_id, doc_vers_no, col6, 
            col7, col8, col9, col10, col11, col12, col13, col14, col15, col16, 
            col17, col18, col19, col20, col21, col22
	FROM   etl.foreign_tbl_fms_data_feed_header;
	
	INSERT INTO etl.ext_stg_fms_data_feed(
            record_type, doc_cd, doc_dept_cd, doc_id, doc_vers_no, col6, 
            col7, col8, col9, col10, col11, col12, col13, col14, col15, col16, 
            col17, col18, col19)
    SELECT  record_type, doc_cd, doc_dept_cd, doc_id, doc_vers_no, col6, 
            col7, col8, col9, col10, col11, col12, col13, col14, col15, col16, 
            col17, col18, col19
	FROM   etl.foreign_tbl_fms_data_feed_vendor ;
	
	
	INSERT INTO etl.ext_stg_fms_data_feed(
            record_type, doc_cd, doc_dept_cd, doc_id, doc_vers_no, col6, 
            col7, col8, col9, col10, col11, col12, col13, col14, col15, col16, 
            col17, col18, col19, col20, col21, col22, col23, col24, col25, 
            col26, col27, col28, col29, col30, col31, col32, col33, col34, 
            col35, col36, col37, col38, col39, col40, col41, col42, col43, 
            col44, col45, col46, col47, col48, col49)
    SELECT  record_type, doc_cd, doc_dept_cd, doc_id, doc_vers_no, col6, 
            col7, col8, col9, col10, col11, col12, col13, col14, col15, col16, 
            col17, col18, col19, col20, col21, col22, col23, col24, col25, 
            col26, col27, col28, col29, col30, col31, col32, col33, col34, 
            col35, col36, col37, col38, col39, col40, col41, col42, col43, 
            col44, col45, col46, col47, col48, col49
	FROM   etl.foreign_tbl_fms_data_feed_acc_line;
	
	
	ELSIF(l_data_source_code = 'P') THEN
	
	TRUNCATE  etl.ext_stg_pms_data_feed;
	
	INSERT INTO etl.ext_stg_pms_data_feed(
            pay_cycle_code, pay_date, employee_number, payroll_number, job_sequence_number, 
            agency_code, agency_start_date, fiscal_year, orig_pay_cycle_code, 
            orig_pay_date, pay_frequency, last_name, department_code, annual_salary, 
            amount_basis, base_pay, overtime_pay, other_payments, gross_pay, 
            civil_service_code, civil_service_level, civil_service_suffix, 
            civil_service_title)
    SELECT pay_cycle_code, pay_date, employee_number, payroll_number, job_sequence_number, 
            agency_code, agency_start_date, fiscal_year, orig_pay_cycle_code, 
            orig_pay_date, pay_frequency, last_name, department_code, annual_salary, 
            amount_basis, base_pay, overtime_pay, other_payments, gross_pay, 
            civil_service_code, civil_service_level, civil_service_suffix, 
            civil_service_title
	FROM etl.foreign_tbl_pms_data_feed;
	
	ELSIF(l_data_source_code = 'PS') THEN
	
	TRUNCATE  etl.ext_stg_pms_summary_data_feed;
	
	INSERT INTO etl.ext_stg_pms_summary_data_feed(
            pay_cycle, pay_date, pyrl_no, pyrl_desc, uoa, uoa_name, fy, object, 
            object_desc, agency, agency_name, bud_code, bud_code_desc, total_amt, 
            col15)
    SELECT  pay_cycle, pay_date, pyrl_no, pyrl_desc, uoa, uoa_name, fy, object, 
            object_desc, agency, agency_name, bud_code, bud_code_desc, total_amt, 
            col15
	FROM etl.foreign_tbl_pms_summary_data_feed;

	END IF;
	
	l_end_time := timeofday()::timestamp;
	INSERT INTO etl.etl_script_execution_status(load_file_id,script_name,completed_flag,start_time,end_time)
	VALUES(p_load_file_id_in,'etl.loadDataFromForeignTables',1,l_start_time,l_end_time);
	
	
	RETURN 1;
EXCEPTION
	WHEN OTHERS THEN
	RAISE NOTICE 'Exception Occurred in loadDataFromForeignTables';
	RAISE NOTICE 'SQL ERRROR % and Desc is %' ,SQLSTATE,SQLERRM;	
	
	l_end_time := timeofday()::timestamp;
	INSERT INTO etl.etl_script_execution_status(load_file_id,script_name,completed_flag,start_time,end_time,errno,errmsg)
	VALUES(p_load_file_id_in,'etl.loadDataFromForeignTables',0,l_start_time,l_end_time,SQLSTATE,SQLERRM);

	RETURN 0;
END;
$$ LANGUAGE 'plpgsql' ;



