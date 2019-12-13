<?php
include_once CUSTOM_MODULES_DIR . '/checkbook_datafeeds/checkbook_datafeeds.module';
include_once CUSTOM_MODULES_DIR . '/checkbook_datafeeds/includes/checkbook_datafeeds_budget.inc';
include_once CUSTOM_MODULES_DIR . '/checkbook_datafeeds/includes/checkbook_datafeeds_contracts.inc';
include_once CUSTOM_MODULES_DIR . '/checkbook_datafeeds/includes/checkbook_datafeeds_tracking.inc';
include_once CUSTOM_MODULES_DIR . '/checkbook_widget_redesign/checkbook_infrastructure_layer/constants/CommonConstants.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/includes/checkbook_project.inc';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/checkbook_project.module';

use PHPUnit\Framework\TestCase;

/**
 * Class CheckbookDatafeedsModuleTest
 */
class CheckbookDatafeedsModuleTest extends TestCase
{

    /**
     *
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     *
     */
    public function test_checkbook_datafeeds_menu()
    {
        $items = checkbook_datafeeds_menu();
        $this->assertEquals('array', gettype($items));
        $this->assertEquals(8, sizeof($items));
        $this->assertEquals(4, sizeof($items['data-feeds']));
        $this->assertEquals(3, sizeof($items['data-feeds/data-feeds']));
        $this->assertEquals(5, sizeof($items['data-feeds/api']));
        $this->assertEquals(2, sizeof($items['track-data-feed']));
        $this->assertEquals(4, sizeof($items['data-feeds/download']));
        $this->assertEquals(4, sizeof($items['datafeeds/budget/department/%/%']));
        $this->assertEquals(4, sizeof($items['datafeeds/budget/expcat/%/%/%']));
        $this->assertEquals(4, sizeof($items['datafeeds/spending/agency/%/%']));
    }

    /**
     *
     */
    public function test_checkbook_datafeeds_library()
    {
        $libs = checkbook_datafeeds_library();
        $this->assertEquals('array', gettype($libs));
        $this->assertEquals(5, sizeof($libs['jquery_multiselect']));
    }

    /**
     *
     */
    public function test_checkbook_datafeeds_theme()
    {
        $hooks = checkbook_datafeeds_theme(1, 2, 3, 4);
        $this->assertEquals(1, sizeof($hooks['user_criteria']));
        $this->assertEquals('array', gettype($hooks));
    }

    /**
     *
     */
    public function checkbook_datafeeds_api_page()
    {
        $this->assertTrue(checkbook_datafeeds_api_page());
    }

    /**
     *
     */
    public function test_checkbook_datafeeds_tracking_form()
    {
        $form = checkbook_datafeeds_tracking_form();
        $this->assertEquals('array', gettype($form));
        $this->assertEquals(1, sizeof($form['#attached']));
        $this->assertEquals(1, sizeof($form['text']));
        $this->assertEquals(4, sizeof($form['tracking_number']));
        $this->assertEquals(3, sizeof($form['go']));
    }

    /**
     *
     */
    public function test_checkbook_datafeeds_tracking_form_submit()
    {
        $test_form_state = [
            'values' => [
                'tracking_number' => 'secret007'
            ]
        ];
        checkbook_datafeeds_tracking_form_submit(true, $test_form_state);
        $this->assertEquals(2, sizeof($test_form_state['redirect']));
    }

    /**
     *
     */
    public function test_checkbook_datafeeds_tracking_results_page()
    {
        $page = checkbook_datafeeds_tracking_results_page();
        $this->assertEquals('array', gettype($page));
        $this->assertEquals(2, sizeof($page['instructions']));
        $this->assertEquals(1, sizeof($page['trackstatus']));
        $this->assertEquals(1, sizeof($page['tftitle']));
        $this->assertEquals(1, sizeof($page['tracking']));
        $this->assertEquals(1, sizeof($page['closediv']));
        $this->assertEquals(1, sizeof($page['markupstart']));
        $this->assertEquals(1, sizeof($page['tracking-status']));
        $this->assertEquals(1, sizeof($page['markupend']));
    }

    /**
     *
     */
    public function test_theme_user_criteria()
    {
        $test_vars = [
            'usercriteria' => [
                'Columns' => 'a',
                'Record Count' => 'b',
                'Something else' => 'c'
            ]
        ];
        $out = theme_user_criteria($test_vars);
        $this->assertEquals('<div><strong>Something else</strong>: c</div>', $out);
    }

    /**
     *
     */
    public function test_checkbook_datafeeds_data_feeds_page()
    {
        $page = checkbook_datafeeds_data_feeds_page();
        $this->assertEquals('array', gettype($page));
        $this->assertEquals(2, sizeof($page['instructions']));
        $this->assertEquals(1, sizeof($page['trackstatus']));
        $this->assertEquals(1, sizeof($page['tftitle']));
        $this->assertEquals(1, sizeof($page['tracking']));
        $this->assertEquals(1, sizeof($page['closediv']));
        $this->assertEquals(1, sizeof($page['data-feed-wizard']));
        $this->assertEquals(1, sizeof($page['rotator']));
    }

    /**
     *
     */
    public function test_checkbook_datafeeds_data_feed_wizard()
    {
        $test_form_state = [
            'step' => 'none'
        ];
        $form = checkbook_datafeeds_data_feed_wizard([], $test_form_state);
        $this->assertEquals('array', gettype($form));
        $this->assertEquals('type', $test_form_state['step']);
        $this->assertEquals(5, sizeof($form['type']));
        $this->assertEquals(4, sizeof($form['format']));
        $this->assertEquals(5, sizeof($form['type_next']));
        $this->assertEquals(1, sizeof($form['#attributes']));
    }

    public function test_checkbook_datafeeds_budget_column_options_csv()
    {
        $out = _checkbook_datafeeds_budget_column_options('csv');
        $this->assertEquals('array', gettype($out));
        $this->assertEquals('Accrued Expense', $out[0]);
        $this->assertEquals(13, sizeof($out));
    }

    public function test_checkbook_datafeeds_budget_column_options_xml()
    {
        $out = _checkbook_datafeeds_budget_column_options('xml');
        $this->assertEquals('array', gettype($out));
        $this->assertEquals('accrued_expense', $out[0]);
        $this->assertEquals(13, sizeof($out));
    }

    public function test_checkbook_datafeeds_budget_next_submit()
    {
        $test_form_state = [
            'step' => 'forward',
            'step_information' => [
                'forward' => [
                    'stored_values' => ['none']
                ]
            ],
            'values' => ['some']
        ];
        checkbook_datafeeds_budget_next_submit([], $test_form_state);
        $this->assertEquals([], $test_form_state['values']);
        $this->assertEquals(true, $test_form_state['rebuild']);

        $test_form_state['step_information']['budget_confirmation']['stored_values'] = 777;
        $test_form_state['values'] = 888;
        checkbook_datafeeds_budget_next_submit([], $test_form_state);
        $this->assertEquals(888, $test_form_state['values']);

        $test_form_state['step'] = 'backward';
        $test_form_state['step_information']['budget_confirmation']['stored_values'] = 777;
        $test_form_state['values'] = 888;
        checkbook_datafeeds_budget_next_submit([], $test_form_state);
        $this->assertEquals(777, $test_form_state['values']);
    }

    //    public function test_checkbook_datafeeds_contracts_filter_data()
//    {
//        $test_form = [];
//        $test_form_state = [
//            'triggering_element' =>
//            [
//                '#value' => 'somevalue',
//                '#ajax' => ['parameters'=> ['data_source_changed'=>'yes']]
//            ]
//        ];
//        $test_data_source = [];
//        $form = checkbook_datafeeds_contracts_filter_data($test_form, $test_form_state, $test_data_source);
//        $this->assertEquals('array', gettype($form));
//    }

    public function test_checkbook_datafeeds_process_contracts_values()
    {
        $test_form = [];
        $test_form_state = [];
        $test_form_state['step_information']['contracts']['stored_values'] = [
            'data_source' => 'checkbook_oge',
            'datafeeds-contracts-domain-filter' => 'checkbook_oge',
            'df_contract_status' => 'active',
            'vendor' => '',
            'mwbe_category' => '',
            'contract_type' => 'No Contract Type Selected',
            'contractno' => '',
            'commodity_line' => '',
            'entity_contract_number' => '',
            'budget_name' => '',
            'contract_includes_sub_vendors_id' => 0,
            'pin' => '',
            'currentamtfrom' => '',
            'currentamtto' => '',
            'enddatefrom' => '',
            'enddateto' => '',
            'regdatefrom' => '',
            'regdateto' => '',
            'category' => 'expense',
            'sub_vendor_status_in_pip_id' => '0',
            'purpose' => '',
            'agency' => 'Select One',
            'industry' => '0',
            'apt_pin' => '',
            'award_method' => 'No Award Method Selected',
            'startdatefrom' => '',
            'startdateto' => '',
            'recdatefrom' => '',
            'recdateto' => '',
            'year' => 'ALL',
            'agency_hidden' => 'Select One',
            'column_select_expense' =>
                array (
                ),
            'column_select_revenue' =>
                array (
                ),
            'column_select_all' =>
                array (
                ),
            'column_select_pending' =>
                array (
                ),
            'column_select_pending_all' =>
                array (
                ),
            'column_select_oge_expense' =>
                array (
                    'Award Method' => 'Award Method',
                    'Budget Name' => 'Budget Name',
                    'Commodity Line' => 'Commodity Line',
                    'Contract ID' => 'Contract ID',
                    'Contract Industry' => 'Contract Industry',
                    'Contract Type' => 'Contract Type',
                    'Current Amount' => 'Current Amount',
                ),
        ];
        $criteria = checkbook_datafeeds_process_contracts_values($test_form, $test_form_state, 'checkbook_oge');
        $this->assertEquals('array', gettype($criteria));
    }
}
