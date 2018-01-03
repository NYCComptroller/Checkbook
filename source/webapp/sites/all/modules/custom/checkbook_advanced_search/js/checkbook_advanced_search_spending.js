(function ($) {
    $(document).ready(function () {
        var p = /\[(.*?)\]/;
        var year, dept, agency, exptype, expcategory, mwbecat, industry, entitycontractnum, commodityline, budgetname, datasource;

        var spending_div = function (data_source, div_contents) {
            this.div_elements = {
                'agency':'select[name='+data_source+'_spending_agency]',
                'dept':'select[name='+data_source+'_spending_department]',
                'exp_category':'select[name='+data_source+'_spending_expense_category]',
                'spending_category':'select[name='+data_source+'_spending_expense_type]',
                'mwbe_category':'select[name='+data_source+'_spending_mwbe_category]',
                'industry':'select[name='+data_source+'_spending_industry]',
                'payee_name':'input:text[name='+data_source+'_spending_payee_name]',
                'check_amt_from':'input:text[name="'+data_source+'_spending_check_amount_from[date]"]',
                'check_amt_to':'input:text[name="'+data_source+'_spending_check_amount_to[date]"]',
                'contract_id':'input:text[name='+data_source+'_spending_contract_num]',
                'entity_contract_number':'input:text[name='+data_source+'_spending_entity_contract_number]',
                'document_id':'input:text[name='+data_source+'_spending_document_id]',
                'capital_project':'input:text[name='+data_source+'_spending_capital_project]',
                'commodity_line':'input:text[name='+data_source+'_spending_commodity_line]',
                'budget_name':'input:text[name='+data_source+'_spending_budget_name]',
                'date_filter':'input:radio[name='+data_source+'_spending_date_filter]',
                'date_filter_year':'input:radio[name='+data_source+'_spending_date_filter][value=0]',
                'date_filter_issue_date':'input:radio[name='+data_source+'_spending_date_filter][value=1]',
                'date_filter_checked':'input:radio[name='+data_source+'_spending_date_filter]:checked',
                'fiscal_year':'select[name="'+data_source+'_spending_fiscal_year"]',
                'issue_date_from':'input:text[name="'+data_source+'_spending_issue_date_from[date]"]',
                'issue_date_to':'input:text[name="'+data_source+'_spending_issue_date_to[date]"]'
            };

            this.data_source = data_source;
            this.div_contents = div_contents;
        };
        spending_div.prototype.contents = function () {
            return this.div_contents;
        };
        spending_div.prototype.ele = function (element_name) {
            var selector = this.div_elements[element_name];
            return this.div_contents.find(selector);
        };

        var div_spending_main = $("#spending-advanced-search");
        var div_checkbook_spending = new spending_div('checkbook', div_spending_main.children('div.checkbook'));
        var div_checkbook_spending_oge = new spending_div('checkbook_oge', div_spending_main.children('div.checkbook-oge'));

        //On change of "Agency"
        div_checkbook_spending.ele('agency').change(function () {
            onAgencyChange(div_checkbook_spending);
        });
        div_checkbook_spending_oge.ele('agency').change(function () {
            onAgencyChange(div_checkbook_spending_oge);
        });
        function onAgencyChange(div) {
            if (div.ele('agency').val() === 0) {
                div.ele('dept').val('0');
                div.ele('exp_category').val('0');
                div.ele('dept').attr("disabled", "disabled");
                div.ele('exp_category').attr("disabled", "disabled");
            }
            else
            {
                var year = 0;
                if(div.ele('date_filter_checked').val() === 0){
                    year = (div.ele('fiscal_year').val()) ? div.ele('fiscal_year').val() : 0;
                }
                var agency = (div.ele('agency').val()) ? div.ele('agency').val() : 0;
                var dept = (div.ele('dept').val()) ? (div.ele('dept').val()) : 0;
                dept = dept.replace(/\//g,"__");
                var exptype = (div.ele('spending_category').val()) ? (div.ele('spending_category').val()) : 0;
                var data_source = $('input:radio[name=spending_advanced_search_domain_filter]:checked').val();
                $.ajax({
                    url: '/advanced-search/autocomplete/spending/expcategory/' + year + '/' + agency + '/' + dept + '/' + exptype + '/' + data_source
                    ,success: function(data) {
                        var html = '<option select="selected" value="0" >Select Expense Category</option>';
                        if(data[0]){
                            if(data[0]!== 'No Matches Found'){
                                $.each(data, function(key, exp_cat){
                                    html = html + '<option value="' + exp_cat.code + '">' + exp_cat.name  + '</option>';
                                });
                            }
                            else { html=html + '<option value="">' + data[0]  + '</option>'; }
                        }
                        div.ele('exp_category').html(html);
                    }
                });
                $.ajax({
                    url: '/advanced-search/autocomplete/spending/department/' + year + '/' + agency + '/' + exptype + '/' + data_source
                    ,success: function(data) {
                        var html = '<option select="selected" value="0" >Select Department</option>';
                        if(data[0]){
                            if(data[0]!== 'No Matches Found'){
                                for (i = 0; i < data.length; i++) {
                                    html=html + '<option value="' + data[i] + ' ">' + data[i]  + '</option>';
                                }
                            }
                            else { html=html + '<option value="">' + data[0]  + '</option>'; }
                        }
                        div.ele('dept').html(html);
                    }
                });

                div.ele('dept').removeAttr("disabled");
                div.ele('exp_category').removeAttr("disabled");

            }
        }

        //On change of "Department"
        div_checkbook_spending.ele('dept').change(function () {
            onDeptChange(div_checkbook_spending);
        });
        div_checkbook_spending_oge.ele('dept').change(function () {
            onDeptChange(div_checkbook_spending_oge);
        });
        function onDeptChange(div) {
            year = 0;
            if(div.ele('date_filter_checked').val() === 0){
                year = (div.ele('fiscal_year').val()) ? div.ele('fiscal_year').val() : 0;
            }
            var agency = (div.ele('agency').val()) ? div.ele('agency').val() : 0;
            var dept = (div.ele('dept').val()) ? (div.ele('dept').val()) : 0;
            dept = dept.replace(/\//g,"__");
            var exptype = (div.ele('spending_category').val()) ? (div.ele('spending_category').val()) : 0;
            var data_source = $('input:radio[name=spending_advanced_search_domain_filter]:checked').val();

            $.ajax({
                url: '/advanced-search/autocomplete/spending/expcategory/' + year + '/' + agency + '/' + dept +'/' + exptype + '/' + data_source
                ,success: function(data) {
                    var html = '<option select="selected" value="0" >Select Expense Category</option>';
                    if(data[0]){
                        if(data[0]!== 'No Matches Found'){
                            $.each(data, function(key, exp_cat){
                                html = html + '<option value="' + exp_cat.code + '">' + exp_cat.name  + '</option>';
                            });
                        }
                        else { html=html + '<option value="">' + data[0]  + '</option>'; }
                    }
                    div.ele('exp_category').html(html);
                }
            });
        }

        //On change of "Expense Type"
        div_checkbook_spending.ele('spending_category').change(function () {
            onExpenseTypeChange(div_checkbook_spending);
        });
        div_checkbook_spending_oge.ele('spending_category').change(function () {
            onExpenseTypeChange(div_checkbook_spending_oge);
        });
        function onExpenseTypeChange(div) {
            if (div.ele('spending_category').val() === 2) {
                div.ele('contract_id').attr("disabled", "disabled");
                div.ele('contract_id').val("");
                div.ele('payee_name').attr("disabled", "disabled");
                div.ele('payee_name').val("");
            }
            else if (div.ele('spending_category').val() === 4) {
                div.ele('contract_id').attr("disabled", "disabled");
                div.ele('contract_id').val("");
            }
            else {
                div.ele('contract_id').removeAttr("disabled");
                div.ele('payee_name').removeAttr("disabled");
            }

            year = 0;
            if(div.ele('date_filter_checked').val() === 0){
                year = (div.ele('fiscal_year').val()) ? div.ele('fiscal_year').val() : 0;
            }
            var agency = (div.ele('agency').val()) ? div.ele('agency').val() : 0;
            var dept = (div.ele('dept').val()) ? (div.ele('dept').val()) : 0;
            dept = dept.replace(/\//g,"__");
            var exptype = (div.ele('spending_category').val()) ? (div.ele('spending_category').val()) : 0;
            var data_source = $('input:radio[name=spending_advanced_search_domain_filter]:checked').val();
            $.ajax({
                url: '/advanced-search/autocomplete/spending/expcategory/' + year + '/' + agency + '/' + dept +'/' + exptype + '/' + data_source
                ,success: function(data) {
                    var html = '<option select="selected" value="0" >Select Expense Category</option>';
                    if(data[0]){
                        if(data[0] !== 'No Matches Found'){
                            $.each(data, function(key, exp_cat){
                                html = html + '<option value="' + exp_cat.code + '">' + exp_cat.name  + '</option>';
                            });
                        }
                        else { html=html + '<option value="">' + data[0]  + '</option>'; }
                    }
                    div.ele('exp_category').html(html);
                }
            });
            $.ajax({
                url: '/advanced-search/autocomplete/spending/department/' + year + '/' + agency + '/' + exptype + '/' + data_source
                ,success: function(data) {
                    var html = '<option select="selected" value="0" >Select Department</option>';
                    if(data[0]){
                        if(data[0] !== 'No Matches Found'){
                            for (i = 0; i < data.length; i++) {
                                html=html + '<option value="' + data[i] + ' ">' + data[i]  + '</option>';
                            }
                        }
                        else { html=html + '<option value="">' + data[0]  + '</option>'; }
                    }
                    div.ele('dept').html(html);
                }
            });
        }

        //On change of "Fiscal Year"
        div_checkbook_spending.ele('fiscal_year').change(function () {
            onFiscalYearChange(div_checkbook_spending);
        });
        div_checkbook_spending_oge.ele('fiscal_year').change(function () {
            onFiscalYearChange(div_checkbook_spending_oge);
        });
        function onFiscalYearChange(div) {
            year = 0;
            if(div.ele('date_filter_checked').val() === 0){
                year = (div.ele('fiscal_year').val()) ? div.ele('fiscal_year').val() : 0;
            }
            var agency = (div.ele('agency').val()) ? div.ele('agency').val() : 0;

            //Don't update drop downs if no agency is selected
            if(agency === 0) return;

            var dept = (div.ele('dept').val()) ? (div.ele('dept').val()) : 0;
            dept = dept.replace(/\//g,"__");
            var exptype = (div.ele('spending_category').val()) ? (div.ele('spending_category').val()) : 0;
            var data_source = $('input:radio[name=spending_advanced_search_domain_filter]:checked').val();
            $.ajax({
                url: '/advanced-search/autocomplete/spending/expcategory/' + year + '/' + agency + '/' + dept +'/' + exptype + '/' + data_source
                ,success: function(data) {
                    var html = '<option select="selected" value="0" >Select Expense Category</option>';
                    if(data[0]){
                        if(data[0] !== 'No Matches Found'){
                            $.each(data, function(key, exp_cat){
                                html = html + '<option value="' + exp_cat.code + '">' + exp_cat.name  + '</option>';
                            });
                        }
                        else { html=html + '<option value="">' + data[0]  + '</option>'; }
                    }
                    div.ele('exp_category').html(html);
                }
            });
            $.ajax({
                url: '/advanced-search/autocomplete/spending/department/' + year + '/' + agency + '/' + exptype + '/' + data_source
                ,success: function(data) {
                    var html = '<option select="selected" value="0" >Select Department</option>';
                    if(data[0]){
                        if(data[0] !== 'No Matches Found'){
                            for (i = 0; i < data.length; i++) {
                                html=html + '<option value="' + data[i] + ' ">' + data[i]  + '</option>';
                            }
                        }
                        else { html=html + '<option value="">' + data[0]  + '</option>'; }
                    }
                    div.ele('dept').html(html);
                }
            });
        }

        //On clicking "Clear"
        $('div.spending-submit.checkbook').find('input:submit[value="Clear All"]').click(function(e){
            onClearClick(div_checkbook_spending);
            e.preventDefault();
        });
        $('div.spending-submit.checkbook-oge').find('input:submit[value="Clear All"]').click(function(e){
            onClearClick(div_checkbook_spending_oge);
            e.preventDefault();
        });
        function onClearClick(div) {
            div.ele('exp_category').attr("disabled", "disabled");
            div.ele('dept').attr("disabled", "disabled");
            div.ele('spending_category').val('Total Spending');
            div.ele('date_filter_year').attr('checked', 'checked');
            div.ele('date_filter_issue_date').removeAttr('checked');

            //reset Expense Type
            if (div.ele('spending_category').val() === 2) {
                div.ele('contract_id').attr("disabled", "disabled");
                div.ele('contract_id').val("");
                div.ele('payee_name').attr("disabled", "disabled");
                div.ele('payee_name').val("");
            }
            else if (div.ele('spending_category').val() === 4) {
                div.ele('contract_id').attr("disabled", "disabled");
                div.ele('contract_id').val("");
            }
            else {
                div.ele('contract_id').removeAttr("disabled");
                div.ele('payee_name').removeAttr("disabled");
            }

            //reset Date Filter
            var value = div.ele('date_filter_checked').val();
            if (value === 0) {
                div.ele('fiscal_year').attr('disabled', '');
                div.ele('issue_date_from').attr('disabled', 'disabled');
                div.ele('issue_date_to').attr('disabled', 'disabled');
            } else if (value === 1) {
                div.ele('fiscal_year').attr('disabled', 'disabled');
                div.ele('issue_date_from').removeAttr("disabled");
                div.ele('issue_date_to').removeAttr("disabled");
            }
        }

        //On click of "Date Filter"
        div_checkbook_spending.ele('date_filter').click(function () {
            onDateFilterClick(div_checkbook_spending);
        });
        div_checkbook_spending_oge.ele('date_filter').click(function () {
            onDateFilterClick(div_checkbook_spending_oge);
        });
        function onDateFilterClick(div) {
            var value = div.ele('date_filter_checked').val();
            if (value === 0) {
                div.ele('fiscal_year').attr('disabled', '');
                div.ele('issue_date_from').attr('disabled', 'disabled');
                div.ele('issue_date_to').attr('disabled', 'disabled');
            } else if (value === 1) {
                div.ele('fiscal_year').attr('disabled', 'disabled');
                div.ele('issue_date_from').removeAttr("disabled");
                div.ele('issue_date_to').removeAttr("disabled");
            }
        }

        //On change of data source
        $('input:radio[name=spending_advanced_search_domain_filter]').change(function () {
            onChangeDataSource($('input[name=spending_advanced_search_domain_filter]:checked').val());
        });
        //checkbook_advanced_search_clear_button.js sets this value by default
        $('input:radio[name=spending_advanced_search_domain_filter]').click(function () {
            onChangeDataSource($('input[name=spending_advanced_search_domain_filter]:checked').val());
        });

        function onChangeDataSource(dataSource) {

            /* Reset all the fields for the data source */
            resetFields(div_checkbook_spending.contents());
            resetFields(div_checkbook_spending_oge.contents());

            /* Initialize view by data source */
            switch (dataSource) {
                case "checkbook_oge":
                    initializeSpendingView(div_checkbook_spending_oge, dataSource);
                    div_checkbook_spending.contents().hide();
                    div_checkbook_spending_oge.contents().show();
                    break;

                default:
                    initializeSpendingView(div_checkbook_spending, dataSource);
                    div_checkbook_spending.contents().show();
                    div_checkbook_spending_oge.contents().hide();
                    div_checkbook_spending_oge.ele('agency')[0].selectedIndex = 1;
                    break;
            }
        }

        //Initialize the field elements in the view based on data source selected
        function initializeSpendingView(div, dataSource) {

            if(dataSource === "checkbook_oge") {
                div.ele('date_filter_issue_date').attr('disabled', 'disabled');
            }

            //Both
            div.ele('dept').attr("disabled", "disabled");
            div.ele('exp_category').attr("disabled", "disabled");

            year = 0;
            if (div.ele('date_filter_checked').val() === 0) {
                year = (div.ele('fiscal_year').val()) ? div.ele('fiscal_year').val() : 0;
            }
            agency = (div.ele('agency').val()) ? div.ele('agency').val() : 0;
            dept = (div.ele('dept').val()) ? (div.ele('dept').val()) : 0;
            dept = dept.replace(/\//g,"__");
            expcategory = (div.ele('exp_category').val()) ? (div.ele('exp_category').val()) : 0;
            exptype = (div.ele('spending_category').val()) ? (div.ele('spending_category').val()) : 0;
            mwbecat = (div.ele('mwbe_category').val()) ? (div.ele('mwbe_category').val()) : 0;
            industry = (div.ele('industry').val()) ? (div.ele('industry').val()) : 0;
            datasource = $('input:radio[name=spending_advanced_search_domain_filter]:checked').val();

            div.ele('payee_name').autocomplete({
                source:'/advanced-search/autocomplete/spending/payee/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + industry + '/' + datasource,
                select: function( event, ui ) {
                    $(this).parent().next().val(ui.item.label) ;
                }
            });
            div.ele('contract_id').autocomplete({
                source:'/advanced-search/autocomplete/spending/contractno/'  + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + industry + '/' + datasource,
                select: function( event, ui ) {
                    $(this).parent().next().val(ui.item.label) ;
                }
            });
            div.ele('capital_project').autocomplete({
                source:'/advanced-search/autocomplete/spending/capitalproject/'  + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + industry + '/' + datasource,
                select: function( event, ui ) {
                    $(this).parent().next().val(ui.item.label) ;
                }
            });
            div.ele('document_id').autocomplete({
                source:'/advanced-search/autocomplete/spending/expenseid/'  + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + industry + '/' + datasource,
                select: function( event, ui ) {
                    $(this).parent().next().val(ui.item.label) ;
                }
            });
            div.ele('commodity_line').autocomplete({
                source:'/advanced-search/autocomplete/spending/commodityline/'  + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + industry + '/' + datasource,
                select: function( event, ui ) {
                    $(this).parent().next().val(ui.item.label) ;
                }
            });
            div.ele('budget_name').autocomplete({
                source:'/advanced-search/autocomplete/spending/budgetname/'  + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + industry + '/' + datasource,
                select: function( event, ui ) {
                    $(this).parent().next().val(ui.item.label) ;
                }
            });
            div.ele('entity_contract_number').autocomplete({
                source:'/advanced-search/autocomplete/spending/entitycontractnum/'  + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + industry + '/' + datasource,
                select: function( event, ui ) {
                    $(this).parent().next().val(ui.item.label) ;
                }
            });

            div_spending_main.each(function () {
                $(this).focusout(function () {
                    year = 0;
                    if (div.ele('date_filter_checked').val() === 0) {
                        year = (div.ele('fiscal_year').val()) ? div.ele('fiscal_year').val() : 0;
                    }
                    agency = (div.ele('agency').val()) ? div.ele('agency').val() : 0;
                    dept = (div.ele('dept').val()) ? (div.ele('dept').val()) : 0;
                    dept = dept.replace(/\//g,"__");
                    expcategory = (div.ele('exp_category').val()) ? (div.ele('exp_category').val()) : 0;
                    exptype = (div.ele('spending_category').val()) ? (div.ele('spending_category').val()) : 0;
                    mwbecat = (div.ele('mwbe_category').val()) ? (div.ele('mwbe_category').val()) : 0;
                    industry = (div.ele('industry').val()) ? (div.ele('industry').val()) : 0;
                    datasource = $('input:radio[name=spending_advanced_search_domain_filter]:checked').val();

                    div.ele('payee_name').autocomplete({source:'/advanced-search/autocomplete/spending/payee/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + industry + '/' + datasource});
                    div.ele('contract_id').autocomplete({source:'/advanced-search/autocomplete/spending/contractno/'  + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + industry + '/' + datasource});
                    div.ele('capital_project').autocomplete({source:'/advanced-search/autocomplete/spending/capitalproject/'  + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + industry + '/' + datasource});
                    div.ele('document_id').autocomplete({source:'/advanced-search/autocomplete/spending/expenseid/'  + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + industry + '/' + datasource});
                    div.ele('commodity_line').autocomplete({source:'/advanced-search/autocomplete/spending/commodityline/'  + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + industry + '/' + datasource});
                    div.ele('budget_name').autocomplete({source:'/advanced-search/autocomplete/spending/budgetname/'  + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + industry + '/' + datasource});
                    div.ele('entity_contract_number').autocomplete({source:'/advanced-search/autocomplete/spending/entitycontractnum/'  + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + industry + '/' + datasource});
                });
            });
            if (div.ele('date_filter_checked').val() === 0) {
                div.ele('issue_date_from').attr('disabled', 'disabled');
                div.ele('issue_date_to').attr('disabled', 'disabled');
            }

            //prevent the auto-complete from wrapping un-necessarily
            fixAutoCompleteWrapping(div.contents());
        }

        //Reset fields to default values
        function resetFields(divWrapper) {
            jQuery(divWrapper.children()).find(':input').each(function () {
                if (this.type === 'text') {
                    jQuery(this).val('');
                }
                if (this.type === 'select-one') {
                    var default_option = jQuery(this).attr('default_selected_value');
                    if(default_option == null)
                        jQuery(this).find('option:first').attr("selected", "selected");
                    else
                        jQuery(this).find('option[value=' + default_option + ']').attr("selected", "selected");
                }
            });
        }

        //Prevent the auto-complete from wrapping un-necessarily
        function fixAutoCompleteWrapping(divWrapper) {
            jQuery(divWrapper.children()).find('input.ui-autocomplete-input:text').each(function () {
                $(this).data("autocomplete")._resizeMenu = function () {
                    (this.menu.element).outerWidth('100%');
                }
            });
        }

        function emptyToZero(input) {
            var inputval, output;
            inputval = p.exec(input);
            if (inputval) {
                output = inputval[1];
            } else {
                output = 0;
            }
            return output;
        }
    })
}(jQuery));

