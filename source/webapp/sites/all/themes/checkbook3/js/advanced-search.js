jQuery(document).ready(function ($) {
    $('a.advanced-search').click(function () {
        // var prev = $('a.advanced-search').html();
        // $('a.advanced-search').after('<div class="ajax-progress"><div class="throbber"></div></div>');

        if ($('#checkbook-advanced-search-form').length) {
            advanced_search_bootstrap();
        } else {
            $('.block-checkbook-advanced-search .content').load('/advanced-search-ajax', advanced_search_bootstrap);
        }
        // $('.ajax-progress').html(prev);
    });

    function advanced_search_bootstrap() {

        $('#checkbook-advanced-search-form').attr('action', '/advanced-search');

        advanced_search_bootstrap_domains();

        var href = window.location.href.replace(/(http|https):\/\//, '');
        var n = href.indexOf('?');
        href = href.substring(0, n != -1 ? n : href.length);
        var data_source = (href.indexOf('datasource/checkbook_oge') !== -1) ? "checkbook_oge" : "checkbook";
        var page_clicked_from = this.id ? this.id : href.split('/')[1];
        var active_accordion_window = initializeActiveAccordionWindow(page_clicked_from, data_source);


        //Initialize Attributes and styling
        initializeAccordionAttributes('advanced_search');

        $('#block-checkbook-advanced-search-checkbook-advanced-search-form').dialog({
            title: "Advanced Search",
            position: ['center', 'center'],
            width: 800,
            modal: true,
            autoResize: true,
            resizable: false,
            dragStart: function () {
                $(".ui-autocomplete-input").autocomplete("close")
            },
            open: function () {
            },
            close: function () {
                $(".ui-autocomplete-input").autocomplete("close")
                $('input[name="budget_submit"]').css('display', 'none');
                $('input[name="revenue_submit"]').css('display', 'none');
                $('input[name="spending_submit"]').css('display', 'none');
                $('input[name="contracts_submit"]').css('display', 'none');
                $('input[name="payroll_submit"]').css('display', 'none');
            }
        });
        /* Correct min-height for IE9, causes hover event to add spaces */
        $('#block-checkbook-advanced-search-checkbook-advanced-search-form').css('min-height', '0%');

        $('.advanced-search-accordion').accordion({
            autoHeight: false,
            active: active_accordion_window
        });

        /* For oge, Budget, Revenue & Payroll are not applicable and are disabled */
        disableAccordionSections(data_source);

        clearInputFields("#payroll-advanced-search", 'payroll');
        clearInputFieldByDataSource("#contracts-advanced-search", 'contracts', data_source);
        clearInputFieldByDataSource("#spending-advanced-search", 'spending', data_source);
        clearInputFields("#budget-advanced-search", 'budget');
        clearInputFields("#revenue-advanced-search", 'revenue');

        clearInputFields("#payroll-advanced-search", 'payroll');
        clearInputFieldByDataSource("#contracts-advanced-search", 'contracts', data_source);
        clearInputFieldByDataSource("#spending-advanced-search", 'spending', data_source);
        clearInputFields("#budget-advanced-search", 'budget');
        clearInputFields("#revenue-advanced-search", 'revenue');

        return false;
    }

    function advanced_search_bootstrap_domains() {
        advanced_search_budget_init();
        advanced_search_contracts_init();
        advanced_search_payroll_init();
        advanced_search_revenue_init();
        advanced_search_spending_init();
        advanced_search_buttons_init();
    }

// advanced-search-budget
    function advanced_search_budget_init() {
        function reloadDepartment() {
            var fiscal_year = ($('#edit-budget-fiscal-year').val()) ? $('#edit-budget-fiscal-year').val() : 0;
            var agency = ($('#edit-budget-agencies').val()) ? $('#edit-budget-agencies').val() : 0;
            $.ajax({
                url: '/advanced-search/autocomplete/budget/department/' + fiscal_year + '/' + agency,
                success: function (data) {
                    var html = '<option select="selected" value="0" >Select Department</option>';
                    if (data[0]) {
                        if (data[0].label !== 'No Matches Found') {
                            for (var i = 0; i < data.length; i++) {
                                html = html + '<option value="' + data[i] + ' ">' + data[i] + '</option>';
                            }
                        }
                    }
                    $('#edit-budget-department').html(html);
                    $('#edit-budget-department').removeAttr("disabled");
                }
            });
        }

        function reloadExpenseCategory() {
            var fiscal_year = ($('#edit-budget-fiscal-year').val()) ? $('#edit-budget-fiscal-year').val() : 0;
            var agency = ($('#edit-budget-agencies').val()) ? $('#edit-budget-agencies').val() : 0;
            var dept = ($('#edit-budget-department').val()) ? ($('#edit-budget-department').val()) : 0;

            $.ajax({
                url: '/advanced-search/autocomplete/budget/expcategory/' + fiscal_year + '/' + agency + '/' + dept.toString().replace(/\//g, "__"),
                success: function (data) {
                    var html = '<option select="selected" value="0" >Select Expense Category</option>';
                    if (data[0]) {
                        if (data[0].label !== 'No Matches Found') {
                            for (var i = 0; i < data.length; i++) {
                                html = html + '<option value="' + data[i] + ' ">' + data[i] + '</option>';
                            }
                        }
                    }
                    $('#edit-budget-expense-category').html(html);
                    $('#edit-budget-expense-category').removeAttr("disabled");
                }
            });
        }

        function reloadBudgetCode() {
            var fiscal_year = ($('#edit-budget-fiscal-year').val()) ? $('#edit-budget-fiscal-year').val() : 0;
            var agency = ($('#edit-budget-agencies').val()) ? $('#edit-budget-agencies').val() : 0;
            var dept = ($('#edit-budget-department').val()) ? ($('#edit-budget-department').val()) : 0;
            var exp_cat = ($('#edit-budget-expense-category').val()) ? ($('#edit-budget-expense-category').val()) : 0;
            var budget_code = ($('#edit-budget-budget-code').val()) ? $('#edit-budget-budget-code').val() : 0;
            var budget_name = ($('#edit-budget-budget-name').val()) ? $('#edit-budget-budget-name').val() : 0;

            $.ajax({
                url: '/advanced-search/autocomplete/budget/budgetcode/' + fiscal_year + '/' + agency + '/' + dept.toString().replace(/\//g, "__") + '/' + exp_cat.toString().replace(/\//g, "__") + '/' + budget_name.toString().replace(/\//g, "__"),
                success: function (data) {
                    var html = '<option select="selected" value="0" title="">Select Budget Code</option>';
                    if (data[0]) {
                        if (data[0].label !== 'No Matches Found') {
                            for (var i = 0; i < data.length; i++) {
                                html = html + '<option title="' + data[i] + '" value="' + data[i] + ' ">' + data[i] + '</option>';
                            }
                        }
                    }
                    $('#edit-budget-budget-code').html(html);
                    $('#edit-budget-budget-code').val(budget_code);
                    $('#edit-budget-budget-code').trigger("chosen:updated");
                    //if(budget_name !== $('#edit-budget-budget-name').val()){
                    //    reloadBudgetCode();
                    //}
                }
            });
        }

        function reloadBudgetName() {
            var fiscal_year = ($('#edit-budget-fiscal-year').val()) ? $('#edit-budget-fiscal-year').val() : 0;
            var agency = ($('#edit-budget-agencies').val()) ? $('#edit-budget-agencies').val() : 0;
            var dept = ($('#edit-budget-department').val()) ? ($('#edit-budget-department').val()) : 0;
            var exp_cat = ($('#edit-budget-expense-category').val()) ? ($('#edit-budget-expense-category').val()) : 0;
            var budget_code = ($('#edit-budget-budget-code').val()) ? $('#edit-budget-budget-code').val() : 0;
            var budget_name = ($('#edit-budget-budget-name').val()) ? $('#edit-budget-budget-name').val() : 0;

            $.ajax({
                url: '/advanced-search/autocomplete/budget/budgetname/' + fiscal_year + '/' + agency + '/' + dept.toString().replace(/\//g, "__") + '/' + exp_cat.toString().replace(/\//g, "__") + '/' + budget_code,
                success: function (data) {
                    var html = '<option select="selected" value="0" title="">Select Budget Name</option>';
                    if (data[0]) {
                        if (data[0].label !== 'No Matches Found') {
                            for (var i = 0; i < data.length; i++) {
                                html = html + '<option title="' + data[i].value + '" value="' + data[i].value + ' ">' + data[i].label + '</option>';
                            }
                        }
                    }
                    $('#edit-budget-budget-name').html(html);
                    $('#edit-budget-budget-name').val(budget_name);
                    $('#edit-budget-budget-name').trigger("chosen:updated");
                    //if(budget_code !== $('#edit-budget-budget-code').val()){
                    //    reloadBudgetName();
                    //}
                }
            });
        }

        $('#edit-budget-budget-code').chosen({
            no_results_text: "No matches found"
        });
        $('#edit_budget_budget_code_chosen .chosen-search-input').attr("placeholder", "Search Budget Code");

        $('#edit-budget-budget-name').chosen({
            no_results_text: "No matches found"
        });
        $('#edit_budget_budget_name_chosen .chosen-search-input').attr("placeholder", "Search Budget Name");

        reloadBudgetCode();
        reloadBudgetName();

        $('#edit-budget-agencies').change(function () {
            if ($('#edit-budget-agencies').val() === "0") {
                $('#edit-budget-department').val('0');
                $('#edit-budget-expense-category').val('0');
                $('#edit-budget-department').attr("disabled", "disabled");
                $('#edit-budget-expense-category').attr("disabled", "disabled");
            }
            else {
                reloadDepartment();
                reloadExpenseCategory();
            }
            reloadBudgetCode();
            reloadBudgetName();
        });

        $('#edit-budget-department').change(function () {
            reloadExpenseCategory();
            reloadBudgetCode();
            reloadBudgetName();
        });

        $('#edit-budget-expense-category').change(function () {
            reloadBudgetCode();
            reloadBudgetName();
        });

        $('#edit-budget-budget-code').change(function () {
            reloadBudgetName();
        });

        $('#edit-budget-budget-name').change(function () {
            reloadBudgetCode();
        });

        $('#edit-budget-fiscal-year').change(function () {
            reloadBudgetCode();
            reloadBudgetName();
        });

        $('#edit-budget-clear').click(function () {
            $('#edit-budget-expense-category').attr("disabled", "disabled");
            $('#edit-budget-department').attr("disabled", "disabled");
        });

    }

// advanced-search-clear-button
    function clearInputFields(enclosingDiv, domain) {
        jQuery(enclosingDiv).find(':input').each(function () {
            switch (this.type) {
                case 'select-one':
                    var defaultoption = jQuery(this).attr('default_selected_value');
                    if (defaultoption == null)
                        jQuery(this).find('option:first').attr("selected", "selected");
                    else
                        jQuery(this).find('option[value=' + defaultoption + ']').attr("selected", "selected");
                    break;
                case 'text':
                    jQuery(this).val('');
                    break;
                case 'select-multiple':
                case 'password':
                case 'textarea':
                    jQuery(this).val('');
                    break;
                case 'checkbox':
                case 'radio':
                    switch (domain) {
                        case 'payroll':
                            jQuery('#edit-payroll-amount-type-0').attr('checked', 'checked');
                            break;
                    }
                    break;
            }
        })
        /* Disable the drop-downs by domain */
        switch (domain) {
            case 'budget':
                jQuery('#edit-budget-expense-category').attr("disabled", "disabled");
                jQuery('#edit-budget-department').attr("disabled", "disabled");
                jQuery('#edit-budget-budget-code').val("0");
                jQuery('#edit-budget-budget-code').trigger("chosen:updated");
                jQuery('#edit-budget-budget-name').val("0");
                jQuery('#edit-budget-budget-name').trigger("chosen:updated");
                reloadBudgetCode();
                reloadBudgetName();

                break;
        }
    }

    function reloadBudgetCode() {
        var fiscal_year = (jQuery('#edit-budget-fiscal-year').val()) ? jQuery('#edit-budget-fiscal-year').val() : 0;
        var agency = (jQuery('#edit-budget-agencies').val()) ? jQuery('#edit-budget-agencies').val() : 0;
        var dept = (jQuery('#edit-budget-department').val()) ? (jQuery('#edit-budget-department').val()) : 0;
        var exp_cat = (jQuery('#edit-budget-expense-category').val()) ? (jQuery('#edit-budget-expense-category').val()) : 0;
        var budget_code = (jQuery('#edit-budget-budget-code').val()) ? jQuery('#edit-budget-budget-code').val() : 0;
        var budget_name = (jQuery('#edit-budget-budget-name').val()) ? jQuery('#edit-budget-budget-name').val() : 0;

        jQuery.ajax({
            url: '/advanced-search/autocomplete/budget/budgetcode/' + fiscal_year + '/' + agency + '/' + dept.toString().replace(/\//g, "__") + '/' + exp_cat.toString().replace(/\//g, "__") + '/' + budget_name.toString().replace(/\//g, "__"),
            success: function (data) {
                var html = '<option select="selected" value="0" title="">Select Budget Code</option>';
                if (data[0]) {
                    if (data[0].label !== 'No Matches Found') {
                        for (var i = 0; i < data.length; i++) {
                            html = html + '<option title="' + data[i] + '" value="' + data[i] + ' ">' + data[i] + '</option>';
                        }
                    }
                }
                jQuery('#edit-budget-budget-code').html(html);
                jQuery('#edit-budget-budget-code').val(budget_code);
                jQuery('#edit-budget-budget-code').trigger("chosen:updated");
                if (budget_name !== jQuery('#edit-budget-budget-name').val()) {
                    reloadBudgetCode();
                }
            }
        });
    }

    function reloadBudgetName() {
        var fiscal_year = (jQuery('#edit-budget-fiscal-year').val()) ? jQuery('#edit-budget-fiscal-year').val() : 0;
        var agency = (jQuery('#edit-budget-agencies').val()) ? jQuery('#edit-budget-agencies').val() : 0;
        var dept = (jQuery('#edit-budget-department').val()) ? (jQuery('#edit-budget-department').val()) : 0;
        var exp_cat = (jQuery('#edit-budget-expense-category').val()) ? (jQuery('#edit-budget-expense-category').val()) : 0;
        var budget_code = (jQuery('#edit-budget-budget-code').val()) ? jQuery('#edit-budget-budget-code').val() : 0;
        var budget_name = (jQuery('#edit-budget-budget-name').val()) ? jQuery('#edit-budget-budget-name').val() : 0;

        jQuery.ajax({
            url: '/advanced-search/autocomplete/budget/budgetname/' + fiscal_year + '/' + agency + '/' + dept.toString().replace(/\//g, "__") + '/' + exp_cat.toString().replace(/\//g, "__") + '/' + budget_code,
            success: function (data) {
                var html = '<option select="selected" value="0" title="">Select Budget Name</option>';
                if (data[0]) {
                    if (data[0].label !== 'No Matches Found') {
                        for (var i = 0; i < data.length; i++) {
                            html = html + '<option title="' + data[i].value + '" value="' + data[i].value + ' ">' + data[i].label + '</option>';
                        }
                    }
                }
                jQuery('#edit-budget-budget-name').html(html);
                jQuery('#edit-budget-budget-name').val(budget_name);
                jQuery('#edit-budget-budget-name').trigger("chosen:updated");
                if (budget_code !== jQuery('#edit-budget-budget-code').val()) {
                    reloadBudgetName();
                }
            }
        });
    }

    function clearInputFieldByDataSource(enclosingDiv, domain, dataSource) {
        jQuery(enclosingDiv).find(':input').each(function () {
            switch (this.type) {
                case 'select-one':
                    jQuery("select#edit-checkbook-contracts-category").val("expense");
                    //jQuery('#edit-spending-fiscal-year').removeAttr("disabled");
                    var defaultoption = jQuery(this).attr('default_selected_value');
                    if (defaultoption == null)
                        jQuery(this).find('option:first').attr("selected", "selected");
                    else
                        jQuery(this).find('option[value=' + defaultoption + ']').attr("selected", "selected");
                    break;
                case 'text':
                    jQuery(this).val('');
                    break;
                case 'select-multiple':
                case 'password':
                case 'textarea':
                    jQuery(this).val('');
                    break;
                case 'checkbox':
                case 'radio':
                    switch (domain) {
                        case 'payroll':
                            jQuery('#edit-payroll-amount-type-0').attr('checked', 'checked');
                            break;
                        case 'spending':
                            jQuery(':radio[name="spending_advanced_search_domain_filter"][value="' + dataSource + '"]').click();
                            break;
                        case 'contracts':
                            jQuery(':radio[name="contracts_advanced_search_domain_filter"][value="' + dataSource + '"]').click();
                            break;
                    }
                    break;
            }
        })
    }

//Disable Advanced Search Form Fields based on the selection criteria
    function disableInputFields() {
        /****************disabling Budget fields*****************/
        if (jQuery('#edit-budget-agencies').val() === 0) {
            jQuery('#edit-budget-department').attr("disabled", "disabled");
            jQuery('#edit-budget-expense-category').attr("disabled", "disabled");
        }
        if (jQuery('#edit-budget-department').val() === 0) {
            jQuery('#edit-budget-expense-category').attr("disabled", "disabled");
        }

        /****************disabling Spending fields*****************/
            //Agency, Department and Expense Category
        var spending_data_source = jQuery('input:radio[name=spending_advanced_search_domain_filter]:checked').val();
        if (jQuery('select[name=' + spending_data_source + '_spending_agency]').val() === 0) {
            jQuery('select[name=' + spending_data_source + '_spending_department]').attr("disabled", "disabled");
            jQuery('select[name=' + spending_data_source + '_spending_expense_category]').attr("disabled", "disabled");
        }
        if (jQuery('select[name=' + spending_data_source + '_spending_department]').val() === 0) {
            jQuery('select[name=' + spending_data_source + '_spending_expense_category]').attr("disabled", "disabled");
        }

        //Spending Category, Contract ID and Payee Name
        if (jQuery('select[name=' + spending_data_source + '_spending_expense_type]').val() === 2) {
            jQuery('input:text[name=' + spending_data_source + '_spending_contract_num]').attr("disabled", "disabled");
            jQuery('input:text[name=' + spending_data_source + '_spending_contract_num]').val("");
            jQuery('input:text[name=' + spending_data_source + '_spending_payee_name]').attr("disabled", "disabled");
            jQuery('input:text[name=' + spending_data_source + '_spending_payee_name]').val("");
        }
        else if (jQuery('select[name=' + spending_data_source + '_spending_expense_type]').val() === 4) {
            jQuery('input:text[name=' + spending_data_source + '_spending_contract_num]').attr("disabled", "disabled");
            jQuery('input:text[name=' + spending_data_source + '_spending_contract_num]').val("");
        }

        //Date Filter
        var value = jQuery('input:radio[name=' + spending_data_source + '_spending_date_filter]:checked').val();
        if (value === 0) {
            jQuery('select[name="' + spending_data_source + '_spending_fiscal_year"]').attr('disabled', '');
            jQuery('input:text[name="' + spending_data_source + '_spending_issue_date_from[date]"]').attr('disabled', 'disabled');
            jQuery('input:text[name="' + spending_data_source + '_spending_issue_date_to[date]"]').attr('disabled', 'disabled');
        } else if (value === 1) {
            jQuery('select[name="' + spending_data_source + '_spending_fiscal_year"]').attr('disabled', 'disabled');
        }

        /****************disabling Contracts fields*****************/
        var contracts_data_source = jQuery('input:radio[name=contracts_advanced_search_domain_filter]:checked').val();

        //If the datasource is 'OGE'
        if (contracts_data_source === 'checkbook_oge') {
            jQuery('input:text[name=' + contracts_data_source + '_contracts_apt_pin]').attr('disabled', 'disabled');
            jQuery('input:text[name="' + contracts_data_source + '_contracts_received_date_from[date]"]').attr('disabled', 'disabled');
            jQuery('input:text[name="' + contracts_data_source + '_contracts_received_date_to[date]"]').attr('disabled', 'disabled');
            jQuery('input:text[name="' + contracts_data_source + '_contracts_registration_date_from[date]"]').attr('disabled', 'disabled');
            jQuery('input:text[name="' + contracts_data_source + '_contracts_registration_date_to[date]"]').attr('disabled', 'disabled');
        }

        //upon 'Status' change
        var contract_status = jQuery('select[name=' + contracts_data_source + '_contracts_status]').val();
        if (contract_status === 'P') {
            if (contracts_data_source === 'checkbook') {
                jQuery('input:text[name="' + contracts_data_source + '_contracts_registration_date_from[date]"]').attr('disabled', 'disabled');
                jQuery('input:text[name="' + contracts_data_source + '_contracts_registration_date_to[date]"]').attr('disabled', 'disabled');
            }
            jQuery('select[name="' + contracts_data_source + '_contracts_year"]').attr("disabled", "disabled");
        } else {
            jQuery('input:text[name="' + contracts_data_source + '_contracts_received_date_from[date]"]').attr('disabled', 'disabled');
            jQuery('input:text[name="' + contracts_data_source + '_contracts_received_date_to[date]"]').attr('disabled', 'disabled');
        }

        //upon 'Incudes Sub Vendor' change
        var includes_sub_vendors = jQuery('select[name="' + contracts_data_source + '_contracts_includes_sub_vendors"]').val();
        if (includes_sub_vendors === 3 || includes_sub_vendors === 1) {
            jQuery('select[name="' + contracts_data_source + '_contracts_sub_vendor_status"]').attr("disabled", "disabled");
        }

        //upon 'Category' change
        var contract_category = jQuery('select[name=' + contracts_data_source + '_contracts_category]').val();
        if (contract_status === 'P' || contract_category === 'revenue') {
            jQuery('select[name="' + contracts_data_source + '_contracts_includes_sub_vendors"]').attr("disabled", "disabled");
            jQuery('select[name="' + contracts_data_source + '_contracts_sub_vendor_status"]').attr("disabled", "disabled");
        }
    }

// advanced-search-contracts
    function advanced_search_contracts_init() {

        var contracts_div = function (data_source, div_contents) {
            this.div_elements = {
                'agency': 'select[name=' + data_source + '_contracts_agency]',
                'status': 'select[name=' + data_source + '_contracts_status]',
                'category': 'select[name=' + data_source + '_contracts_category]',
                'contract_type': 'select[name=' + data_source + '_contracts_type]',
                'award_method': 'select[name=' + data_source + '_contracts_award_method]',
                'year': 'select[name="' + data_source + '_contracts_year"]',
                'mwbe_category': 'select[name=' + data_source + '_contracts_mwbe_category]',
                'industry': 'select[name=' + data_source + '_contracts_industry]',
                'vendor_name': 'input:text[name=' + data_source + '_contracts_vendor_name]',
                'contract_id': 'input:text[name=' + data_source + '_contracts_contract_num]',
                'apt_pin': 'input:text[name=' + data_source + '_contracts_apt_pin]',
                'pin': 'input:text[name=' + data_source + '_contracts_pin]',
                'registration_date_from': 'input:text[name="' + data_source + '_contracts_registration_date_from[date]"]',
                'registration_date_to': 'input:text[name="' + data_source + '_contracts_registration_date_to[date]"]',
                'received_date_from': 'input:text[name="' + data_source + '_contracts_received_date_from[date]"]',
                'received_date_to': 'input:text[name="' + data_source + '_contracts_received_date_to[date]"]',
                'entity_contract_number': 'input:text[name=' + data_source + '_contracts_entity_contract_number]',
                'commodity_line': 'input:text[name=' + data_source + '_contracts_commodity_line]',
                'budget_name': 'input:text[name=' + data_source + '_contracts_budget_name]',
                'includes_sub_vendors': 'select[name="' + data_source + '_contracts_includes_sub_vendors"]',
                'sub_vendor_status': 'select[name="' + data_source + '_contracts_sub_vendor_status"]',
                'purpose': 'input:text[name=' + data_source + '_contracts_purpose]',
                'curremt_amount_from': 'input:text[name="' + data_source + '_contracts_current_contract_amount_from"]',
                'curremt_amount_to': 'input:text[name="' + data_source + '_contracts_current_contract_amount_to"]'
            };

            this.data_source = data_source;
            this.div_contents = div_contents;
        };
        contracts_div.prototype.contents = function () {
            return this.div_contents;
        };
        contracts_div.prototype.ele = function (element_name) {
            var selector = this.div_elements[element_name];
            return this.div_contents.find(selector);
        };

        var div_contracts_main = $("#contracts-advanced-search");
        var div_checkbook_contracts = new contracts_div('checkbook', div_contracts_main.children('div.checkbook'));
        var div_checkbook_contracts_oge = new contracts_div('checkbook_oge', div_contracts_main.children('div.checkbook-oge'));

        //On change of data source
        $('input:radio[name=contracts_advanced_search_domain_filter]').change(function () {
            onChangeDataSource($('input[name=contracts_advanced_search_domain_filter]:checked').val());
        });
        ///checkbook_advanced_search_clear_button.js sets this value by default
        $('input:radio[name=contracts_advanced_search_domain_filter]').click(function () {
            onChangeDataSource($('input[name=contracts_advanced_search_domain_filter]:checked').val());
        });

        function showHidePrimeAndSubFields(div) {

            var note = jQuery(".prime-and-sub-note");
            var contract_status = div.ele('status').parent();
            var vendor = div.ele('vendor_name').parent();
            var mwbe_category = div.ele('mwbe_category').parent();
            var current_amt_from = div.ele('curremt_amount_from').parent().parent().parent();
            var category = div.ele('category').parent();
            var sub_vendor_status_in_pip = div.ele('sub_vendor_status').parent();
            var purpose = div.ele('purpose').parent();
            var industry = div.ele('industry').parent();
            var year = div.ele('year').parent();

            // Remove all asterisk fields & note
            note.remove();
            removePrimeAndSubIcon(contract_status);
            removePrimeAndSubIcon(vendor);
            removePrimeAndSubIcon(mwbe_category);
            removePrimeAndSubIcon(current_amt_from);
            removePrimeAndSubIcon(category);
            removePrimeAndSubIcon(sub_vendor_status_in_pip);
            removePrimeAndSubIcon(purpose);
            removePrimeAndSubIcon(industry);
            removePrimeAndSubIcon(year);

            var contract_status_val = div.ele('status').val();
            var category_val = div.ele('category').val();

            if ((contract_status_val == 'A' || contract_status_val == 'R') && (category_val == 'expense' || category_val == 'all')) {
                jQuery("<div class='prime-and-sub-note'>All Fields are searchable by Prime data, unless designated as Prime & Sub (<img src='/sites/all/themes/checkbook3/images/prime-and-sub.png' />).</div>").insertBefore(jQuery("#edit-contracts-advanced-search-domain-filter"));
                addPrimeAndSubIcon(contract_status);
                addPrimeAndSubIcon(vendor);
                addPrimeAndSubIcon(mwbe_category);
                addPrimeAndSubIcon(current_amt_from);
                addPrimeAndSubIcon(category);
                addPrimeAndSubIcon(sub_vendor_status_in_pip);
                addPrimeAndSubIcon(purpose);
                addPrimeAndSubIcon(industry);
                addPrimeAndSubIcon(year);
            }
        }

        /**
         * Function will remove the asterisk icon css from a field
         * @param ele
         */
        function removePrimeAndSubIcon(ele) {
            ele.find('.prime-and-sub').remove();
            ele.removeClass('asterisk-style');

        }

        /**
         * Function will add the asterisk icon css to a field
         * @param ele
         */
        function addPrimeAndSubIcon(ele) {
            var primeAndSubIcon = "<img class='prime-and-sub' src='/sites/all/themes/checkbook3/images/prime-and-sub.png' />";
            jQuery(ele).find('label').first().prepend(primeAndSubIcon);
            ele.addClass('asterisk-style');
        }


        function onChangeDataSource(dataSource) {

            /* Reset all the fields for the data source */
            resetFields(div_checkbook_contracts.contents());
            resetFields(div_checkbook_contracts_oge.contents());

            /* Initialize the disabled fields */
            onStatusChange(div_checkbook_contracts);
            onStatusChange(div_checkbook_contracts_oge);
            onCategoryChange(div_checkbook_contracts);

            /* Initialize view by data source */
            switch (dataSource) {
                case "checkbook_oge":
                    initializeContractsView(div_checkbook_contracts_oge);
                    div_checkbook_contracts.contents().hide();
                    div_checkbook_contracts_oge.contents().show();

                    //handle oge attributes
                    div_checkbook_contracts_oge.ele('status').find('option[value=P]').remove();
                    div_checkbook_contracts_oge.ele('category').find('option[value=revenue]').remove();
                    div_checkbook_contracts_oge.ele('category').find('option[value=all]').remove();
                    div_checkbook_contracts_oge.ele('apt_pin').attr('disabled', 'disabled');
                    div_checkbook_contracts_oge.ele('received_date_from').attr('disabled', 'disabled');
                    div_checkbook_contracts_oge.ele('received_date_to').attr('disabled', 'disabled');
                    div_checkbook_contracts_oge.ele('registration_date_from').attr('disabled', 'disabled');
                    div_checkbook_contracts_oge.ele('registration_date_to').attr('disabled', 'disabled');

                    // Remove note
                    jQuery(".prime-and-sub-note").remove();
                    break;

                default:
                    initializeContractsView(div_checkbook_contracts);
                    div_checkbook_contracts.contents().show();
                    div_checkbook_contracts_oge.contents().hide();
                    showHidePrimeAndSubFields(div_checkbook_contracts);
                    //Fix the default for category
                    jQuery("select#edit-checkbook-contracts-category").val("expense");
                    break;
            }
        }

        function autoCompletes(div) {
            var status = div.ele('status').val() ? div.ele('status').val() : 0;
            var category = div.ele('category').val() ? div.ele('category').val() : 0;
            var mwbe_category = div.ele('mwbe_category').val() ? div.ele('mwbe_category').val() : 0;
            var industry = div.ele('industry').val() ? div.ele('industry').val() : 0;
            var contract_type = div.ele('contract_type').val() ? div.ele('contract_type').val() : 0;
            var agency = div.ele('agency').val() ? div.ele('agency').val() : 0;
            var award_method = div.ele('award_method').val() ? div.ele('award_method').val() : 0;
            var year = div.ele('year').val() ? div.ele('year').val() : 0;
            var includes_sub_vendors = div.ele('includes_sub_vendors').val() ? div.ele('includes_sub_vendors').val() : 0;
            var sub_vendor_status = div.ele('sub_vendor_status').val() ? div.ele('sub_vendor_status').val() : 0;
            var data_source = $('input:radio[name=contracts_advanced_search_domain_filter]:checked').val();

            div.ele('vendor_name').autocomplete({
                source: '/advanced-search/autocomplete/contracts/vendor-name/' + status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + mwbe_category + '/' + industry + '/' + includes_sub_vendors + '/' + sub_vendor_status + '/' + data_source,
                select: function (event, ui) {
                    $(this).parent().next().val(ui.item.label);
                }
            });
            div.ele('contract_id').autocomplete({
                source: '/advanced-search/autocomplete/contracts/contract-num/' + status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + mwbe_category + '/' + industry + '/' + includes_sub_vendors + '/' + sub_vendor_status + '/' + data_source,
                select: function (event, ui) {
                    $(this).parent().next().val(ui.item.label);
                }
            });
            div.ele('apt_pin').autocomplete({source: '/advanced-search/autocomplete/contracts/apt-pin/' + status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + mwbe_category + '/' + industry + '/' + includes_sub_vendors + '/' + sub_vendor_status + '/' + data_source});
            div.ele('pin').autocomplete({source: '/advanced-search/autocomplete/contracts/pin/' + status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + mwbe_category + '/' + industry + '/' + includes_sub_vendors + '/' + sub_vendor_status + '/' + data_source});
            div.ele('entity_contract_number').autocomplete({source: '/advanced-search/autocomplete/contracts/entity_contract_number/' + status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + mwbe_category + '/' + industry + '/' + includes_sub_vendors + '/' + sub_vendor_status + '/' + data_source});
            div.ele('commodity_line').autocomplete({source: '/advanced-search/autocomplete/contracts/commodity_line/' + status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + mwbe_category + '/' + industry + '/' + includes_sub_vendors + '/' + sub_vendor_status + '/' + data_source});
            div.ele('budget_name').autocomplete({source: '/advanced-search/autocomplete/contracts/budget_name/' + status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + mwbe_category + '/' + industry + '/' + includes_sub_vendors + '/' + sub_vendor_status + '/' + data_source});
        }

        function initializeContractsView(div) {

            autoCompletes(div);

            $('#contracts-advanced-search').each(function () {
                $(this).focusout(function () {
                    autoCompletes(div);
                });
            });
            //prevent the auto-complete from wrapping un-necessarily
            fixAutoCompleteWrapping(div.contents());
        }

        //Prevent the auto-complete from wrapping un-necessarily
        function fixAutoCompleteWrapping(divWrapper) {
            jQuery(divWrapper.children()).find('input.ui-autocomplete-input:text').each(function () {
                $(this).data("autocomplete")._resizeMenu = function () {
                    (this.menu.element).outerWidth('100%');
                }
            });
        }

        function resetFields(divWrapper) {
            jQuery(divWrapper.children()).find(':input').each(function () {
                if (this.type === 'text') {
                    jQuery(this).val('');
                }
                if (this.type === 'select-one') {
                    jQuery(this).val('');
                }
            });
        }

        //On change of "Status"
        div_checkbook_contracts.ele('status').change(function () {
            showHidePrimeAndSubFields(div_checkbook_contracts);
            onStatusChange(div_checkbook_contracts);
        });
        div_checkbook_contracts_oge.ele('status').change(function () {
            onStatusChange(div_checkbook_contracts_oge);
        });

        function onStatusChange(div) {
            var data_source = $('input[name=contracts_advanced_search_domain_filter]:checked').val();
            var contract_status = div.ele('status').val();
            if (contract_status === 'P') {
                if (data_source === 'checkbook') {
                    div.ele('registration_date_from').val('').attr("disabled", "disabled");
                    div.ele('registration_date_to').val('').attr("disabled", "disabled");
                }
                div.ele('year').attr("disabled", "disabled");
                div.ele('received_date_from').removeAttr("disabled");
                div.ele('received_date_to').removeAttr("disabled");
            } else {
                if (data_source === 'checkbook') {
                    div.ele('registration_date_from').removeAttr("disabled");
                    div.ele('registration_date_to').removeAttr("disabled");
                }
                div.ele('year').removeAttr("disabled");
                div.ele('received_date_from').attr("disabled", "disabled");
                div.ele('received_date_to').attr("disabled", "disabled");
                div.ele('received_date_from').val("");
                div.ele('received_date_to').val("");

            }
            updateSubVendorFields(div);
        }

        //On change of "Category"
        div_checkbook_contracts.ele('category').change(function () {
            showHidePrimeAndSubFields(div_checkbook_contracts);
            onCategoryChange(div_checkbook_contracts);
        });

        function onCategoryChange(div) {
            updateSubVendorFields(div);
        }

        function updateSubVendorFields(div) {
            var contract_status = div.ele('status').val();
            var contract_category = div.ele('category').val();

            if (contract_status === 'P' || contract_category === 'revenue') {
                div.ele('includes_sub_vendors').attr("disabled", "disabled");
                //div.ele('includes_sub_vendors').val('');
                div.ele('sub_vendor_status').attr("disabled", "disabled");
                //div.ele('sub_vendor_status').val('');
            } else {
                div.ele('includes_sub_vendors').removeAttr("disabled");
                div.ele('sub_vendor_status').removeAttr("disabled");
            }
        }

        //On change of "Contract Includes Sub Vendors" status - NYCCHKBK-6187
        div_checkbook_contracts.ele('includes_sub_vendors').change(function () {
            onIncludeSubvendorChange(div_checkbook_contracts);
        });

        function onIncludeSubvendorChange(div) {
            updateSubvendorStatusField(div);
        }

        function updateSubvendorStatusField(div) {
            var includes_sub_vendors = div.ele('includes_sub_vendors').val();

            if (includes_sub_vendors === 3 || includes_sub_vendors === 1 || includes_sub_vendors === 4) {
                div.ele('sub_vendor_status').attr("disabled", "disabled");
                // div.ele('sub_vendor_status').val('');
            } else {
                div.ele('sub_vendor_status').removeAttr("disabled");
            }
        }

        //On change of "Sub Vendor Status in PIP" status -  NYCCHKBK-6187
        div_checkbook_contracts.ele('sub_vendor_status').change(function () {
            onSubvendorStatusChange(div_checkbook_contracts);
        });

        function onSubvendorStatusChange(div) {
            updateIncludeSubvendorsField(div);
        }

        function updateIncludeSubvendorsField(div) {
            var sub_vendor_status = div.ele('sub_vendor_status').val();
            var includes_sub_vendors = div.ele('includes_sub_vendors').val();

            if (sub_vendor_status === 6 || sub_vendor_status === 1 || sub_vendor_status === 4 || sub_vendor_status === 3 || sub_vendor_status === 2 || sub_vendor_status === 5) {
                if (includes_sub_vendors === 2) {
                    div.ele('includes_sub_vendors').html('<option value="0">Select Status</option>' +
                        '<option value="2" selected>Yes</option>');
                } else {
                    div.ele('includes_sub_vendors').html('<option value="0" selected>Select Status</option>' +
                        '<option value="2">Yes</option>');
                }
            } else {
                if (includes_sub_vendors === 2) {
                    div.ele('includes_sub_vendors').html('<option value="0">Select Status</option>' +
                        '<option value="2" selected>Yes</option>' +
                        '<option value="3">No</option>' +
                        '<option value="1">No Data Entered</option>' +
                        '<option value="4">Not Required</option>');
                } else {
                    div.ele('includes_sub_vendors').html('<option value="0" selected>Select Status</option>' +
                        '<option value="2">Yes</option>' +
                        '<option value="3">No</option>' +
                        '<option value="1">No Data Entered</option>' +
                        '<option value="4">Not Required</option>');
                }
            }
            jQuery("#edit-contracts-clear").click(function () {
                showHidePrimeAndSubFields(div_checkbook_contracts);
                div.ele('includes_sub_vendors').html('<option value="0" selected>Select Status</option>' +
                    '<option value="2">Yes</option>' +
                    '<option value="3">No</option>' +
                    '<option value="1">No Data Entered</option>' +
                    '<option value="4">Not Required</option>');
            });
        }
    }

// advanced-search-payroll
    function advanced_search_payroll_init() {
        var agency, pay_frequency, year;

        // employee_name = ($('#edit-payroll-employee-name')).val() ? $('#edit-payroll-employee-name').val() : 0;
        pay_frequency = ($('#edit-payroll-pay-frequency').val()) ? $('#edit-payroll-pay-frequency').val() : 0;
        agency = ($('#edit-payroll-agencies').val()) ? $('#edit-payroll-agencies').val() : 0;
        year = ($('#edit-payroll-year').val()) ? $('#edit-payroll-year').val() : 0;

        $('#edit-payroll-employee-name').autocomplete({
            source: '/advanced-search/autocomplete/payroll/employee-name/' + pay_frequency + '/' + agency + '/' + year,
            select: function (event, ui) {
                $(this).parent().next().val(ui.item.label);
            }
        });
        $('#payroll-advanced-search').each(function () {
            $(this).focusout(function () {
                // employee_name = ($('#edit-payroll-employee-name')).val() ? $('#edit-payroll-employee-name').val() : 0;
                pay_frequency = ($('#edit-payroll-pay-frequency').val()) ? $('#edit-payroll-pay-frequency').val() : 0;
                agency = ($('#edit-payroll-agencies').val()) ? $('#edit-payroll-agencies').val() : 0;
                year = ($('#edit-payroll-year').val()) ? $('#edit-payroll-year').val() : 0;
                $('#edit-payroll-employee-name').autocomplete({source: '/advanced-search/autocomplete/payroll/employee-name/' + pay_frequency + '/' + agency + '/' + year});
            });
        });
    }

// advanced-search-revenue
    function advanced_search_revenue_init() {
        var p = /\[(.*?)\]/;
        var year, fundclass, agency, budgetyear, revcat, revclass, revsrc, fundingsrc, from_advanced_search;

        //year = ($('#edit-revenue-fiscal-year').val()) ? $('#edit-revenue-fiscal-year').val() : 0;
        year = 0; //do not change, this is a needed for the new change
        fundclass = ($('#edit-revenue-fund-class').val()) ? $('#edit-revenue-fund-class').val() : 0;
        agency = ($('#edit-revenue-agencies').val()) ? $('#edit-revenue-agencies').val() : 0;
        budgetyear = ($('#edit-revenue-budget-fiscal-year').val()) ? $('#edit-revenue-budget-fiscal-year').val() : 0;
        revcat = ($('#edit-revenue-revenue-category').val()) ? $('#edit-revenue-revenue-category').val() : 0;
        revclass = $('#edit-revenue-revenue-class').val() ? $('#edit-revenue-revenue-class').val().replace('/', '~') : 0;
        revsrc = $('#edit-revenue-revenue-source').val() ? $('#edit-revenue-revenue-source').val().replace('/', '~') : 0;
        fundingsrc = $('#edit-revenue-funding-source').val() ? $('#edit-revenue-funding-source').val() : 0;

        $('#edit-revenue-revenue-class').autocomplete({
            source: '/advanced-search/autocomplete/revenue/revenueclass/' + year + '/' + fundclass + '/' + agency + '/' + budgetyear + '/' + revcat + '/' + revsrc + '/' + fundingsrc,
            select: function (event, ui) {
                $(this).parent().next().val(ui.item.label);
            }
        });
        $('#edit-revenue-revenue-source').autocomplete({
            source: '/advanced-search/autocomplete/revenue/revenuesource/' + year + '/' + fundclass + '/' + agency + '/' + budgetyear + '/' + revcat + '/' + revclass + '/' + fundingsrc,
            select: function (event, ui) {
                $(this).parent().next().val(ui.item.label);
            }
        });
        $('#revenue-advanced-search').each(function () {
            $(this).focusout(function () {
                //year = ($('#edit-revenue-fiscal-year').val()) ? $('#edit-revenue-fiscal-year').val() : 0;
                year = 0; //do not change, this is a needed for the new change
                fundclass = ($('#edit-revenue-fund-class').val()) ? $('#edit-revenue-fund-class').val() : 0;
                agency = ($('#edit-revenue-agencies').val()) ? $('#edit-revenue-agencies').val() : 0;
                budgetyear = ($('#edit-revenue-budget-fiscal-year').val()) ? $('#edit-revenue-budget-fiscal-year').val() : 0;
                revcat = ($('#edit-revenue-revenue-category').val()) ? $('#edit-revenue-revenue-category').val() : 0;
                revclass = $('#edit-revenue-revenue-class').val() ? $('#edit-revenue-revenue-class').val().replace('/', '~') : 0;
                revsrc = $('#edit-revenue-revenue-source').val() ? $('#edit-revenue-revenue-source').val().replace('/', '~') : 0;
                fundingsrc = $('#edit-revenue-funding-source').val() ? $('#edit-revenue-funding-source').val() : 0;
                $('#edit-revenue-revenue-class').autocomplete({source: '/advanced-search/autocomplete/revenue/revenueclass/' + year + '/' + fundclass + '/' + agency + '/' + budgetyear + '/' + revcat + '/' + revsrc + '/' + fundingsrc});
                $('#edit-revenue-revenue-source').autocomplete({source: '/advanced-search/autocomplete/revenue/revenuesource/' + year + '/' + fundclass + '/' + agency + '/' + budgetyear + '/' + revcat + '/' + revclass + '/' + fundingsrc});
            });
        });

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
    }

// advanced-search-spending
    function advanced_search_spending_init() {
        var p = /\[(.*?)\]/;
        var year, dept, agency, exptype, expcategory, mwbecat, industry, datasource;

        var spending_div = function (data_source, div_contents) {
            this.div_elements = {
                'agency': 'select[name=' + data_source + '_spending_agency]',
                'dept': 'select[name=' + data_source + '_spending_department]',
                'exp_category': 'select[name=' + data_source + '_spending_expense_category]',
                'spending_category': 'select[name=' + data_source + '_spending_expense_type]',
                'mwbe_category': 'select[name=' + data_source + '_spending_mwbe_category]',
                'industry': 'select[name=' + data_source + '_spending_industry]',
                'payee_name': 'input:text[name=' + data_source + '_spending_payee_name]',
                'check_amt_from': 'input:text[name="' + data_source + '_spending_check_amount_from[date]"]',
                'check_amt_to': 'input:text[name="' + data_source + '_spending_check_amount_to[date]"]',
                'contract_id': 'input:text[name=' + data_source + '_spending_contract_num]',
                'entity_contract_number': 'input:text[name=' + data_source + '_spending_entity_contract_number]',
                'document_id': 'input:text[name=' + data_source + '_spending_document_id]',
                'capital_project': 'input:text[name=' + data_source + '_spending_capital_project]',
                'commodity_line': 'input:text[name=' + data_source + '_spending_commodity_line]',
                'budget_name': 'input:text[name=' + data_source + '_spending_budget_name]',
                'date_filter': 'input:radio[name=' + data_source + '_spending_date_filter]',
                'date_filter_year': 'input:radio[name=' + data_source + '_spending_date_filter][value=0]',
                'date_filter_issue_date': 'input:radio[name=' + data_source + '_spending_date_filter][value=1]',
                'date_filter_checked': 'input:radio[name=' + data_source + '_spending_date_filter]:checked',
                'fiscal_year': 'select[name="' + data_source + '_spending_fiscal_year"]',
                'issue_date_from': 'input:text[name="' + data_source + '_spending_issue_date_from[date]"]',
                'issue_date_to': 'input:text[name="' + data_source + '_spending_issue_date_to[date]"]'
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
            if (div.ele('agency').val() == 0) {
                div.ele('dept').val('0');
                div.ele('exp_category').val('0');
                div.ele('dept').attr("disabled", "disabled");
                div.ele('exp_category').attr("disabled", "disabled");
            }
            else {
                var year = 0;
                if (div.ele('date_filter_checked').val() == 0) {
                    year = (div.ele('fiscal_year').val()) ? div.ele('fiscal_year').val() : 0;
                }
                var agency = (div.ele('agency').val()) ? div.ele('agency').val() : 0;
                var dept = (div.ele('dept').val()) ? (div.ele('dept').val()) : 0;
                dept = dept.toString().replace(/\//g, "__");
                var exptype = (div.ele('spending_category').val()) ? (div.ele('spending_category').val()) : 0;
                var data_source = $('input:radio[name=spending_advanced_search_domain_filter]:checked').val();
                $.ajax({
                    url: '/advanced-search/autocomplete/spending/expcategory/' + year + '/' + agency + '/' + dept + '/' + exptype + '/' + data_source
                    , success: function (data) {
                        var html = '<option select="selected" value="0" >Select Expense Category</option>';
                        if (data[0]) {
                            if (data[0] !== 'No Matches Found') {
                                $.each(data, function (key, exp_cat) {
                                    html = html + '<option value="' + exp_cat.code + '">' + exp_cat.name + '</option>';
                                });
                            }
                            else {
                                html = html + '<option value="">' + data[0] + '</option>';
                            }
                        }
                        div.ele('exp_category').html(html);
                    }
                });
                $.ajax({
                    url: '/advanced-search/autocomplete/spending/department/' + year + '/' + agency + '/' + exptype + '/' + data_source
                    , success: function (data) {
                        var html = '<option select="selected" value="0" >Select Department</option>';
                        if (data[0]) {
                            if (data[0] !== 'No Matches Found') {
                                for (var i = 0; i < data.length; i++) {
                                    html = html + '<option value="' + data[i] + ' ">' + data[i] + '</option>';
                                }
                            }
                            else {
                                html = html + '<option value="">' + data[0] + '</option>';
                            }
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
            if (div.ele('date_filter_checked').val() == 0) {
                year = (div.ele('fiscal_year').val()) ? div.ele('fiscal_year').val() : 0;
            }
            var agency = (div.ele('agency').val()) ? div.ele('agency').val() : 0;
            var dept = (div.ele('dept').val()) ? (div.ele('dept').val()) : 0;
            dept = dept.toString().replace(/\//g, "__");
            var exptype = (div.ele('spending_category').val()) ? (div.ele('spending_category').val()) : 0;
            var data_source = $('input:radio[name=spending_advanced_search_domain_filter]:checked').val();

            $.ajax({
                url: '/advanced-search/autocomplete/spending/expcategory/' + year + '/' + agency + '/' + dept + '/' + exptype + '/' + data_source
                , success: function (data) {
                    var html = '<option select="selected" value="0" >Select Expense Category</option>';
                    if (data[0]) {
                        if (data[0] !== 'No Matches Found') {
                            $.each(data, function (key, exp_cat) {
                                html = html + '<option value="' + exp_cat.code + '">' + exp_cat.name + '</option>';
                            });
                        }
                        else {
                            html = html + '<option value="">' + data[0] + '</option>';
                        }
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
            if (div.ele('spending_category').val() == 2) {
                div.ele('contract_id').attr("disabled", "disabled");
                div.ele('contract_id').val("");
                div.ele('payee_name').attr("disabled", "disabled");
                div.ele('payee_name').val("");
            }
            else if (div.ele('spending_category').val() == 4) {
                div.ele('contract_id').attr("disabled", "disabled");
                div.ele('contract_id').val("");
            }
            else {
                div.ele('contract_id').removeAttr("disabled");
                div.ele('payee_name').removeAttr("disabled");
            }

            year = 0;
            if (div.ele('date_filter_checked').val() == 0) {
                year = (div.ele('fiscal_year').val()) ? div.ele('fiscal_year').val() : 0;
            }
            var agency = (div.ele('agency').val()) ? div.ele('agency').val() : 0;
            var dept = (div.ele('dept').val()) ? (div.ele('dept').val()) : 0;
            dept = dept.toString().replace(/\//g, "__");
            var exptype = (div.ele('spending_category').val()) ? (div.ele('spending_category').val()) : 0;
            var data_source = $('input:radio[name=spending_advanced_search_domain_filter]:checked').val();
            $.ajax({
                url: '/advanced-search/autocomplete/spending/expcategory/' + year + '/' + agency + '/' + dept + '/' + exptype + '/' + data_source
                , success: function (data) {
                    var html = '<option select="selected" value="0" >Select Expense Category</option>';
                    if (data[0]) {
                        if (data[0] !== 'No Matches Found') {
                            $.each(data, function (key, exp_cat) {
                                html = html + '<option value="' + exp_cat.code + '">' + exp_cat.name + '</option>';
                            });
                        }
                        else {
                            html = html + '<option value="">' + data[0] + '</option>';
                        }
                    }
                    div.ele('exp_category').html(html);
                }
            });
            $.ajax({
                url: '/advanced-search/autocomplete/spending/department/' + year + '/' + agency + '/' + exptype + '/' + data_source
                , success: function (data) {
                    var html = '<option select="selected" value="0" >Select Department</option>';
                    if (data[0]) {
                        if (data[0] != 'No Matches Found') {
                            for (var i = 0; i < data.length; i++) {
                                html = html + '<option value="' + data[i] + ' ">' + data[i] + '</option>';
                            }
                        }
                        else {
                            html = html + '<option value="">' + data[0] + '</option>';
                        }
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
            if (div.ele('date_filter_checked').val() === 0) {
                year = (div.ele('fiscal_year').val()) ? div.ele('fiscal_year').val() : 0;
            }
            var agency = (div.ele('agency').val()) ? div.ele('agency').val() : 0;

            //Don't update drop downs if no agency is selected
            if (agency == 0) return;

            var dept = (div.ele('dept').val()) ? (div.ele('dept').val()) : 0;
            dept = dept.toString().replace(/\//g, "__");
            var exptype = (div.ele('spending_category').val()) ? (div.ele('spending_category').val()) : 0;
            var data_source = $('input:radio[name=spending_advanced_search_domain_filter]:checked').val();
            $.ajax({
                url: '/advanced-search/autocomplete/spending/expcategory/' + year + '/' + agency + '/' + dept + '/' + exptype + '/' + data_source
                , success: function (data) {
                    var html = '<option select="selected" value="0" >Select Expense Category</option>';
                    if (data[0]) {
                        if (data[0] !== 'No Matches Found') {
                            $.each(data, function (key, exp_cat) {
                                html = html + '<option value="' + exp_cat.code + '">' + exp_cat.name + '</option>';
                            });
                        }
                        else {
                            html = html + '<option value="">' + data[0] + '</option>';
                        }
                    }
                    div.ele('exp_category').html(html);
                }
            });
            $.ajax({
                url: '/advanced-search/autocomplete/spending/department/' + year + '/' + agency + '/' + exptype + '/' + data_source
                , success: function (data) {
                    var html = '<option select="selected" value="0" >Select Department</option>';
                    if (data[0]) {
                        if (data[0] !== 'No Matches Found') {
                            for (var i = 0; i < data.length; i++) {
                                html = html + '<option value="' + data[i] + ' ">' + data[i] + '</option>';
                            }
                        }
                        else {
                            html = html + '<option value="">' + data[0] + '</option>';
                        }
                    }
                    div.ele('dept').html(html);
                }
            });
        }

        //On clicking "Clear"
        $('div.spending-submit.checkbook').find('input:submit[value="Clear All"]').click(function (e) {
            onClearClick(div_checkbook_spending);
            e.preventDefault();
        });
        $('div.spending-submit.checkbook-oge').find('input:submit[value="Clear All"]').click(function (e) {
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
            if (div.ele('spending_category').val() == 2) {
                div.ele('contract_id').attr("disabled", "disabled");
                div.ele('contract_id').val("");
                div.ele('payee_name').attr("disabled", "disabled");
                div.ele('payee_name').val("");
            }
            else if (div.ele('spending_category').val() == 4) {
                div.ele('contract_id').attr("disabled", "disabled");
                div.ele('contract_id').val("");
            }
            else {
                div.ele('contract_id').removeAttr("disabled");
                div.ele('payee_name').removeAttr("disabled");
            }

            //reset Date Filter
            var value = div.ele('date_filter_checked').val();
            if (value == 0) {
                div.ele('fiscal_year').attr('disabled', '');
                div.ele('issue_date_from').attr('disabled', 'disabled');
                div.ele('issue_date_to').attr('disabled', 'disabled');
            } else if (value == 1) {
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
            if (value == 0) {
                div.ele('fiscal_year').attr('disabled', '');
                div.ele('issue_date_from').attr('disabled', 'disabled');
                div.ele('issue_date_to').attr('disabled', 'disabled');
            } else if (value == 1) {
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

            if (dataSource == "checkbook_oge") {
                div.ele('date_filter_issue_date').attr('disabled', 'disabled');
            }

            //Both
            div.ele('dept').attr("disabled", "disabled");
            div.ele('exp_category').attr("disabled", "disabled");

            year = 0;
            if (div.ele('date_filter_checked').val() == 0) {
                year = (div.ele('fiscal_year').val()) ? div.ele('fiscal_year').val() : 0;
            }
            agency = (div.ele('agency').val()) ? div.ele('agency').val() : 0;
            dept = (div.ele('dept').val()) ? (div.ele('dept').val()) : 0;
            dept = dept.toString().replace(/\//g, "__");
            expcategory = (div.ele('exp_category').val()) ? (div.ele('exp_category').val()) : 0;
            exptype = (div.ele('spending_category').val()) ? (div.ele('spending_category').val()) : 0;
            mwbecat = (div.ele('mwbe_category').val()) ? (div.ele('mwbe_category').val()) : 0;
            industry = (div.ele('industry').val()) ? (div.ele('industry').val()) : 0;
            datasource = $('input:radio[name=spending_advanced_search_domain_filter]:checked').val();

            div.ele('payee_name').autocomplete({
                source: '/advanced-search/autocomplete/spending/payee/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + industry + '/' + datasource,
                select: function (event, ui) {
                    $(this).parent().next().val(ui.item.label);
                }
            });
            div.ele('contract_id').autocomplete({
                source: '/advanced-search/autocomplete/spending/contractno/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + industry + '/' + datasource,
                select: function (event, ui) {
                    $(this).parent().next().val(ui.item.label);
                }
            });
            div.ele('capital_project').autocomplete({
                source: '/advanced-search/autocomplete/spending/capitalproject/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + industry + '/' + datasource,
                select: function (event, ui) {
                    $(this).parent().next().val(ui.item.label);
                }
            });
            div.ele('document_id').autocomplete({
                source: '/advanced-search/autocomplete/spending/expenseid/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + industry + '/' + datasource,
                select: function (event, ui) {
                    $(this).parent().next().val(ui.item.label);
                }
            });
            div.ele('commodity_line').autocomplete({
                source: '/advanced-search/autocomplete/spending/commodityline/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + industry + '/' + datasource,
                select: function (event, ui) {
                    $(this).parent().next().val(ui.item.label);
                }
            });
            div.ele('budget_name').autocomplete({
                source: '/advanced-search/autocomplete/spending/budgetname/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + industry + '/' + datasource,
                select: function (event, ui) {
                    $(this).parent().next().val(ui.item.label);
                }
            });
            div.ele('entity_contract_number').autocomplete({
                source: '/advanced-search/autocomplete/spending/entitycontractnum/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + industry + '/' + datasource,
                select: function (event, ui) {
                    $(this).parent().next().val(ui.item.label);
                }
            });

            div_spending_main.each(function () {
                $(this).focusout(function () {
                    year = 0;
                    if (div.ele('date_filter_checked').val() == 0) {
                        year = (div.ele('fiscal_year').val()) ? div.ele('fiscal_year').val() : 0;
                    }
                    agency = (div.ele('agency').val()) ? div.ele('agency').val() : 0;
                    dept = (div.ele('dept').val()) ? (div.ele('dept').val()) : 0;
                    dept = dept.toString().replace(/\//g, "__");
                    expcategory = (div.ele('exp_category').val()) ? (div.ele('exp_category').val()) : 0;
                    exptype = (div.ele('spending_category').val()) ? (div.ele('spending_category').val()) : 0;
                    mwbecat = (div.ele('mwbe_category').val()) ? (div.ele('mwbe_category').val()) : 0;
                    industry = (div.ele('industry').val()) ? (div.ele('industry').val()) : 0;
                    datasource = $('input:radio[name=spending_advanced_search_domain_filter]:checked').val();

                    div.ele('payee_name').autocomplete({source: '/advanced-search/autocomplete/spending/payee/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + industry + '/' + datasource});
                    div.ele('contract_id').autocomplete({source: '/advanced-search/autocomplete/spending/contractno/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + industry + '/' + datasource});
                    div.ele('capital_project').autocomplete({source: '/advanced-search/autocomplete/spending/capitalproject/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + industry + '/' + datasource});
                    div.ele('document_id').autocomplete({source: '/advanced-search/autocomplete/spending/expenseid/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + industry + '/' + datasource});
                    div.ele('commodity_line').autocomplete({source: '/advanced-search/autocomplete/spending/commodityline/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + industry + '/' + datasource});
                    div.ele('budget_name').autocomplete({source: '/advanced-search/autocomplete/spending/budgetname/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + industry + '/' + datasource});
                    div.ele('entity_contract_number').autocomplete({source: '/advanced-search/autocomplete/spending/entitycontractnum/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + industry + '/' + datasource});
                });
            });
            if (div.ele('date_filter_checked').val() == 0) {
                div.ele('issue_date_from').attr('disabled', 'disabled');
                div.ele('issue_date_to').attr('disabled', 'disabled');
            }

            //prevent the auto-complete from wrapping un-necessarily
            fixAutoCompleteWrapping(div.contents());
        }

        //Reset fields to default values
        function resetFields(divWrapper) {
            jQuery(divWrapper.children()).find(':input').each(function () {
                if (this.type == 'text') {
                    jQuery(this).val('');
                }
                if (this.type == 'select-one') {
                    var default_option = jQuery(this).attr('default_selected_value');
                    if (default_option == null)
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
    }

    function advanced_search_buttons_init() {
        $('#edit-payroll-clear').click(function (e) {
            //$('#checkbook-advanced-search-form')[0].reset(); //this works
            clearInputFields('#payroll-advanced-search', 'payroll');
            $(this).blur();
            /* Remove focus */
            e.preventDefault();
        });
        $('#edit-budget-clear').click(function (e) {
            clearInputFields('#budget-advanced-search', 'budget');
            $(this).blur();
            /* Remove focus */
            e.preventDefault();
        });
        $('#edit-revenue-clear').click(function (e) {
            clearInputFields('#revenue-advanced-search', 'revenue');
            $(this).blur();
            /* Remove focus */
            e.preventDefault();
        });
        $('div.contracts-submit.checkbook-oge').find('input:submit[value="Clear All"]').click(function (e) {
            clearInputFieldByDataSource("#contracts-advanced-search", 'contracts', 'checkbook_oge');
            $(this).blur();
            /* Remove focus */
            e.preventDefault();
        });
        $('div.contracts-submit.checkbook').find('input:submit[value="Clear All"]').click(function (e) {
            clearInputFieldByDataSource("#contracts-advanced-search", 'contracts', 'checkbook');
            $(this).blur();
            /* Remove focus */
            e.preventDefault();
        });
        $('div.spending-submit.checkbook-oge').find('input:submit[value="Clear All"]').click(function (e) {
            clearInputFieldByDataSource("#spending-advanced-search", 'spending', 'checkbook_oge');
            $(this).blur();
            /* Remove focus */
            e.preventDefault();
        });
        $('div.spending-submit.checkbook').find('input:submit[value="Clear All"]').click(function (e) {
            clearInputFieldByDataSource("#spending-advanced-search", 'spending', 'checkbook');
            $(this).blur();
            /* Remove focus */
            e.preventDefault();
        });
    }

    /*
     * This code is used to determine which window in the accordion should be open when users click the "Advanced Search" link, based on the page
     * from where the link has been clicked
     * Eg: if the "Advanced Search" link from spending page is clicked, the URL would be http://checkbook/SPENDING/transactions.....
     * if the "Advanced Search" link from budget page is clicked, the URL would be http://checkbook/BUDGET/transactions.....
     * based on the url param in the caps above, we have to keep the specific window in the accordion open
     * check the code in checkbook_advanced_search.module where we generate the form
     */
    function initializeActiveAccordionWindow(page_clicked_from, data_source) {
        var active_accordion_window = 2;
        switch (page_clicked_from) {
            case "budget":
                active_accordion_window = 0;
                break;
            case "revenue":
                active_accordion_window = 1;
                break;
            case "contracts_revenue_landing":
            case "contracts_landing":
            case "contracts_pending_rev_landing":
            case "contracts_pending_exp_landing":
            case "contracts_pending_landing":
            case "contract":
                active_accordion_window = 3;
                break;
            case "payroll":
                active_accordion_window = 4;
                break;
            default: //spending
                active_accordion_window = 2;
                break;
        }

        clearInputFields("#payroll-advanced-search",'payroll');
        clearInputFieldByDataSource("#contracts-advanced-search",'contracts',data_source);
        clearInputFieldByDataSource("#spending-advanced-search",'spending',data_source);
        clearInputFields("#budget-advanced-search",'budget');
        clearInputFields("#revenue-advanced-search",'revenue');

        return active_accordion_window;
    }

    function initializeAccordionAttributes(accordion_type) {
        $('#advanced-search-rotator').css('display', 'none');
        $("#block-checkbook-advanced-search-checkbook-advanced-search-form :input").removeAttr("disabled");
        $('.create-alert-customize-results').css('display','none');
        $('.create-alert-schedule-alert').css('display','none');
        $('.create-alert-confirmation').css('display','none');
        $('#edit-next-submit').attr('disabled', true);
        $('#edit-back-submit').attr('disabled', true);
        $('.create-alert-submit').css('display','none');
        $('div.ui-dialog-titlebar').css('width', 'auto');
        switch(accordion_type) {
            case 'advanced_search':
                $('.create-alert-view').css('display','none');
                $('input[name="budget_submit"]').css('display','inline');
                $('input[name="revenue_submit"]').css('display','inline');
                $('input[name="spending_submit"]').css('display','inline');
                $('input[name="contracts_submit"]').css('display','inline');
                $('input[name="payroll_submit"]').css('display','inline');
                $('input[name="budget_next"]').css('display','none');
                $('input[name="revenue_next"]').css('display','none');
                $('input[name="spending_next"]').css('display','none');
                $('input[name="contracts_next"]').css('display','none');
                $('input[name="payroll_next"]').css('display','none');
                $('.advanced-search-accordion').css('display','inline');
                break;

            case 'advanced_search_create_alerts':
                $('.create-alert-view').css('display','inline');
                $('div.create-alert-submit #edit-next-submit').val('Next');
                $('input[name="budget_submit"]').css('display','none');
                $('input[name="revenue_submit"]').css('display','none');
                $('input[name="spending_submit"]').css('display','none');
                $('input[name="contracts_submit"]').css('display','none');
                $('input[name="payroll_submit"]').css('display','none');
                $('input[name="budget_next"]').css('display','inline');
                $('input[name="revenue_next"]').css('display','inline');
                $('input[name="spending_next"]').css('display','inline');
                $('input[name="contracts_next"]').css('display','inline');
                $('input[name="payroll_next"]').css('display','inline');
                $('.advanced-search-accordion').css('display','inline');
                break;
        }
    }

    /* For oge, Budget, Revenue & Payroll are not applicable and are disabled */
    function disableAccordionSections(data_source) {
        if(data_source == "checkbook_oge") {
            disableAccordionSection('Budget');
            disableAccordionSection('Revenue');
            disableAccordionSection('Payroll');
        }
    }

    /* Function will apply disable the click of the accordian section and apply an attribute for future processing */
    function disableAccordionSection(name) {
        var accordion_section = $("a:contains("+name+")").closest("h3");
        accordion_section.attr("data-enabled","false");
        accordion_section.addClass('ui-state-section-disabled');
        accordion_section.unbind("click");
    }
});