<?php
/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (C) 2012, 2013 New York City
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Stores form values and moves to next step of Data Feeds wizard in Contracts.
 *
 * @param array $form
 *   Data Feeds wizard form array
 * @param array $form_state
 *   Data Feeds wizard form_state array
 */
function checkbook_datafeeds_contracts_next_submit($form, &$form_state) {

    $current_step = & $form_state['step'];
    $form_state['step_information'][$current_step]['stored_values'] = $form_state['values'];
    $current_step = 'contracts_confirmation';
    if (!empty($form_state['step_information'][$current_step]['stored_values'])) {
        $form_state['values'] = $form_state['step_information'][$current_step]['stored_values'];
    }
    else {
        $form_state['values'] = array();
    }
    // Force rebuild with next step.
    $form_state['rebuild'] = TRUE;
}

/**
 * Outputs selected search criteria and number of records found for Contracts.
 *
 * @param array $form
 *   Data Feeds wizard form array
 * @param array $form_state
 *   Data Feeds wizard form_state array
 *
 * @return array
 *   Renderable array of Contracts confirmation step
 */
function checkbook_datafeeds_contracts_confirmation($form, &$form_state)
{
    $values = $form_state['step_information']['contracts']['stored_values'];
    $response_type = $form_state['step_information']['type']['stored_values']['format'];
    $user_criteria = array();
    //Get current data source
    $agency = $values['nycha_agency'];
    $agency_code = emptyToZero($agency);
    $year = $values['year'];

    $data_source = !isset($form_state['input']['data_source']) ? 'checkbook' : $form_state['input']['data_source'];
    //Set type/source of data by data_source
    if($data_source == Datasource::NYCHA){
        if($agency_code == 'z81'){
            $data_source == Datasource::OGE;
            $user_criteria['Type of Data'] = 'Contracts_OGE';
        }else{
            $user_criteria['Type of Data'] = 'Contracts_NYCHA';
            $year = $values['nycha_year'];
        }
    }else{
        $user_criteria['Type of Data'] = 'Contracts';
    }

    $form['download_feeds'] = array(
        '#markup' => '<h2 id="edit-description">Download Data</h2>',
    );
    $form['columns'] = array(
        '#type' => 'fieldset',
        '#title' => t('Selected Columns'),
    );
    $form['#attributes'] = array(
        'class' => array(
            'confirmation-page',
            'data-feeds-wizard',
        ),
    );

    $formatted_search_criteria = array();
    //Used to maintain the order of the columns
    $selected_columns = checkbook_datafeeds_format_columns(Domain::$CONTRACTS, $data_source);
    //Filter columns for current data source
    $filtered_columns = checkbook_datafeeds_contracts_filter_selected_columns($selected_columns, $data_source, $response_type, $values['df_contract_status'],$values['category'],$year);
    if($data_source == Datasource::OGE){
        $form_state['step_information']['contracts']['stored_values']['column_select_oge_expense'] = $filtered_columns;
    }else if($data_source == Datasource::NYCHA){
        $form_state['step_information']['contracts']['stored_values']['column_select_nycha'] = $filtered_columns;
    }else{
        if($values['df_contract_status'] == 'pending'){
            if ($values['category'] != 'all') {
                $form_state['step_information']['contracts']['stored_values']['column_select_pending'] = $filtered_columns;
            }
            if ($values['category'] == 'all') {
                $form_state['step_information']['contracts']['stored_values']['column_select_pending_all'] = $filtered_columns;
            }
        }else{
            if($values['category'] == 'all'){
                $form_state['step_information']['contracts']['stored_values']['column_select_all'] = $filtered_columns;
            }
            if($values['category'] == 'expense'){
                $form_state['step_information']['contracts']['stored_values']['column_select_expense'] = $filtered_columns;
            }
            if($values['category'] == 'revenue'){
                $form_state['step_information']['contracts']['stored_values']['column_select_revenue'] = $filtered_columns;
            }
        }
    }
    foreach ($filtered_columns as $column) {
        $form['columns'][$column] = array(
            '#markup' => '<div>' . $column . '</div>',
        );
        $user_criteria['Columns'][] = $column;
    }
    $form['filter'] = array(
        '#type' => 'fieldset',
        '#title' => t('Search Criteria'),
    );
    $form['filter']['data_type'] = array(
        '#markup' => '<div><strong>Type of Data:</strong> Contracts</div>',
    );
    $formatted_search_criteria['Type of Data'] = 'Contracts';
    $form['filter']['file_type'] = array(
        '#markup' => '<div><strong>Type of File:</strong> ' . $form_state['step_information']['type']['stored_values']['format'] . '</div>',
    );
    $formatted_search_criteria['Type of File'] = $form_state['step_information']['type']['stored_values']['format'];

    $agency = ($data_source == Datasource::OGE) ? ($values['agency'] == 'Select One' ? 'All' : $values['agency']) : $values['agency'];
    switch ($data_source) {
        case Datasource::OGE:
            if ($values['df_contract_status']) {
                $form['filter']['df_contract_status'] = array('#markup' => '<div><strong>Status:</strong> ' . $values['df_contract_status'] . '</div>');
                $user_criteria['Contract Status'] = $values['df_contract_status'];
                $formatted_search_criteria['Status'] = $values['df_contract_status'];
            }
            if ($values['vendor']) {
                $form['filter']['vendor'] = array(
                    '#markup' => '<div><strong>Prime Vendor:</strong> ' . $values['vendor'] . '</div>',
                );
                $user_criteria['Prime Vendor'] = $values['vendor'];
                $formatted_search_criteria['Prime Vendor'] = $values['vendor'];
            }
            if ($values['contract_type']) {
                if ($values['contract_type'] != 'No Contract Type Selected') {
                    $form['filter']['contract_type'] = array('#markup' => '<div><strong>Contract Type:</strong> ' . $values['contract_type'] . '</div>');
                    $user_criteria['Contract Type'] = $values['contract_type'];
                    $formatted_search_criteria['Contract Type'] = $values['contract_type'];
                }
            }
            if ($values['contractno']) {
                $form['filter']['contractno'] = array(
                    '#markup' => '<div><strong>Contract ID:</strong> ' . $values['contractno'] . '</div>',
                );
                $user_criteria['Contract ID'] = $values['contractno'];
                $formatted_search_criteria[''] = null;
            }
            if ($values['commodity_line']) {
                $form['filter']['commodity_line'] = array(
                    '#markup' => '<div><strong>Commodity Line:</strong> ' . $values['commodity_line'] . '</div>',
                );
                $user_criteria['Commodity Line'] = $values['commodity_line'];
                $formatted_search_criteria['Commodity Line'] = $values['commodity_line'];
            }
            if ($values['entity_contract_number']) {
                $form['filter']['entity_contract_number'] = array(
                    '#markup' => '<div><strong>Entity Contract #:</strong> ' . $values['entity_contract_number'] . '</div>',
                );
                $user_criteria['Entity Contract #'] = $values['entity_contract_number'];
                $formatted_search_criteria['Entity Contract #'] = $values['entity_contract_number'];
            }
            if ($values['budget_name']) {
                $form['filter']['budget_name'] = array(
                    '#markup' => '<div><strong>Budget Name:</strong> ' . $values['budget_name'] . '</div>',
                );
                $user_criteria['Budget Name'] = $values['budget_name'];
                $formatted_search_criteria['Budget Name'] = $values['budget_name'];
            }
            if ($values['currentamtfrom'] && $values['currentamtto']) {
                $form['filter']['current_amount'] = array('#markup' => '<div><strong>Current Amount:</strong> Greater Than Equal to: $' . $values['currentamtfrom'] . ' Less Than Equal to: $' . $values['currentamtto'] . '</div>');
                $user_criteria['Current Amount Greater Than'] = $values['currentamtfrom'];
                $user_criteria['Current Amount Less Than'] = $values['currentamtto'];
                $formatted_search_criteria['Current Amount'] = 'Greater Than Equal to: $' . $values['currentamtfrom'] . ' Less Than Equal to: $' . $values['currentamtto'];
            } elseif (!$values['currentamtfrom'] && $values['currentamtto']) {
                $form['filter']['current_amount'] = array('#markup' => '<div><strong>Current Amount:</strong> Less Than Equal to: $' . $values['currentamtto'] . '</div>');
                $user_criteria['Current Amount Less Than'] = $values['currentamtto'];
                $formatted_search_criteria['Current Amount'] = 'Less Than Equal to: $' . $values['currentamtto'];
            } elseif ($values['currentamtfrom'] && !$values['currentamtto']) {
                $form['filter']['current_amount'] = array('#markup' => '<div><strong>Current Amount:</strong> Greater Than Equal to: $' . $values['currentamtfrom'] . '</div>');
                $user_criteria['Current Amount Greater Than'] = $values['currentamtfrom'];
                $formatted_search_criteria['Current Amount'] = 'Greater Than Equal to: $' . $values['currentamtfrom'];
            }
            if ($values['recdatefrom'] && $values['recdateto']) {
                $form['filter']['received_date'] = array('#markup' => '<div><strong>Received Date:</strong> Greater Than Equal to: ' . $values['recdatefrom'] . ' Less Than Equal to: ' . $values['recdateto'] . '</div>');
                $user_criteria['Received Date Greater Than'] = $values['recdatefrom'];
                $user_criteria['Received Date Less Than'] = $values['recdateto'];
                $formatted_search_criteria['Received Date'] = 'Greater Than Equal to: ' . $values['recdatefrom'] . ' Less Than Equal to: ' . $values['recdateto'];
            } elseif (!$values['recdatefrom'] && $values['recdateto']) {
                $form['filter']['received_date'] = array('#markup' => '<div><strong>Received Date:</strong> Less Than Equal to: ' . $values['recdateto'] . '</div>');
                $user_criteria['Received Date Less Than'] = $values['recdateto'];
                $formatted_search_criteria['Received Date'] = 'Less Than Equal to: ' . $values['recdateto'];
            } elseif ($values['recdatefrom'] && !$values['recdateto']) {
                $form['filter']['received_date'] = array('#markup' => '<div><strong>Received Date:</strong> Greater Than Equal to: ' . $values['recdatefrom'] . '</div>');
                $user_criteria['Received Date Greater Than'] = $values['recdatefrom'];
                $formatted_search_criteria['Received Date'] = 'Greater Than Equal to: ' . $values['recdatefrom'];
            }
            if ($values['agency']) {
                $form['filter']['agency'] = array(
                    '#markup' => '<div><strong>Other Government Entities:</strong> ' . $agency . '</div>',
                );
                $user_criteria['Other Government Entities'] = $values['agency'];
                $formatted_search_criteria['Other Government Entities'] = $values['agency'];
            }
            if ($values['category']) {
                $form['filter']['category'] = array('#markup' => '<div><strong>Category:</strong> ' . $values['category'] . '</div>');
                $user_criteria['Category'] = $values['category'];
                $formatted_search_criteria['Category'] = $values['category'];
            }
            if ($values['purpose']) {
                $form['filter']['purpose'] = array(
                    '#markup' => '<div><strong>Purpose:</strong> ' . $values['purpose'] . '</div>',
                );
                $user_criteria['Purpose'] = $values['purpose'];
                $formatted_search_criteria['Purpose'] = $values['purpose'];
            }
            if ($values['pin']) {
                $form['filter']['pin'] = array(
                    '#markup' => '<div><strong>PIN:</strong> ' . $values['pin'] . '</div>',
                );
                $user_criteria['PIN'] = $values['pin'];
                $formatted_search_criteria['PIN'] = $values['pin'];
            }
            if ($values['apt_pin']) {
                $form['filter']['apt_pin'] = array(
                    '#markup' => '<div><strong>APT PIN:</strong> ' . $values['apt_pin'] . '</div>',
                );
                $user_criteria['APT PIN'] = $values['apt_pin'];
                $formatted_search_criteria['APT PIN'] = $values['apt_pin'];
            }
            if ($values['award_method']) {
                if ($values['award_method'] != 'No Award Method Selected') {
                    $form['filter']['award_method'] = array('#markup' => '<div><strong>Award Method:</strong> ' . $values['award_method'] . '</div>');
                    $user_criteria['Award Method'] = $values['award_method'];
                    $formatted_search_criteria['Award Method'] = $values['award_method'];
                }
            }
            if ($values['startdatefrom'] && $values['startdateto']) {
                $form['filter']['start_date'] = array('#markup' => '<div><strong>Start Date:</strong> Greater Than Equal to: ' . $values['startdatefrom'] . ' Less Than Equal to: ' . $values['startdateto'] . '</div>');
                $user_criteria['Start Date Greater Than'] = $values['startdatefrom'];
                $user_criteria['Start Date Less Than'] = $values['startdateto'];
                $formatted_search_criteria['Start Date'] = 'Greater Than Equal to: ' . $values['startdatefrom'] . ' Less Than Equal to: ' . $values['startdateto'];
            } elseif (!$values['startdatefrom'] && $values['startdateto']) {
                $form['filter']['start_date'] = array('#markup' => '<div><strong>Start Date:</strong> Less Than Equal to: ' . $values['startdateto'] . '</div>');
                $user_criteria['Start Date Less Than'] = $values['startdateto'];
                $formatted_search_criteria['Start Date'] = 'Less Than Equal to: ' . $values['startdateto'];
            } elseif ($values['startdatefrom'] && !$values['startdateto']) {
                $form['filter']['start_date'] = array('#markup' => '<div><strong>Start Date:</strong> Greater Than Equal to: ' . $values['startdatefrom'] . '</div>');
                $user_criteria['Start Date Greater Than'] = $values['startdatefrom'];
                $formatted_search_criteria['Start Date'] = 'Greater Than Equal to: ' . $values['startdatefrom'];
            }
            if ($values['enddatefrom'] && $values['enddateto']) {
                $form['filter']['end_date'] = array('#markup' => '<div><strong>End Date:</strong> Greater Than Equal to: ' . $values['enddatefrom'] . ' Less Than Equal to: ' . $values['enddateto'] . '</div>');
                $user_criteria['End Date Greater Than'] = $values['enddatefrom'];
                $user_criteria['End Date Less Than'] = $values['enddateto'];
                $formatted_search_criteria['End Date'] = 'Greater Than Equal to: ' . $values['enddatefrom'] . ' Less Than Equal to: ' . $values['enddateto'];
            } elseif (!$values['enddatefrom'] && $values['enddateto']) {
                $form['filter']['end_date'] = array('#markup' => '<div><strong>End Date:</strong> Less Than Equal to: ' . $values['enddateto'] . '</div>');
                $user_criteria['End Date Less Than'] = $values['enddateto'];
                $formatted_search_criteria['End Date'] = 'Less Than Equal to: ' . $values['enddateto'];
            } elseif ($values['enddatefrom'] && !$values['enddateto']) {
                $form['filter']['end_date'] = array('#markup' => '<div><strong>End Date:</strong> Greater Than Equal to: ' . $values['enddatefrom'] . '</div>');
                $user_criteria['End Date Greater Than'] = $values['enddatefrom'];
                $formatted_search_criteria['End Date'] = 'Greater Than Equal to: ' . $values['enddatefrom'];
            }
            if ($values['year']) {
                if($values['year'] == 'ALL') {
                    $form['filter']['year'] = array(
                        '#markup' => '<div><strong>Year:</strong> All Years</div>',
                    );
                    $formatted_search_criteria['Year'] = 'All Years';
                }
                else {
                    $form['filter']['year'] = array(
                        '#markup' => '<div><strong>Year:</strong> ' . $values['year'] . '</div>',
                    );
                    $formatted_search_criteria['Year'] = $values['year'];
                }
            }
            break;
        case Datasource::NYCHA:
            if ($values['purchase_order_type']) {
                $form['filter']['purchase_order_type'] = array('#markup' => '<div><strong>Purchase Order Type:</strong> ' . $values['purchase_order_type'] . '</div>');
                $user_criteria['Purchase Order Type'] = $values['purchase_order_type'];
                $formatted_search_criteria['Purchase Order Type'] = $values['purchase_order_type'];
            }
            if ($values['nycha_contract_id']) {
                $form['filter']['nycha_contract_id'] = array('#markup' => '<div><strong>Contract ID:</strong> ' . $values['nycha_contract_id'] . '</div>');
                $user_criteria['Contract ID'] = $values['nycha_contract_id'];
                $formatted_search_criteria['Contract ID'] = $values['nycha_contract_id'];
            }
            if ($values['nycha_vendor']) {
                $form['filter']['nycha_vendor'] = array('#markup' => '<div><strong>Vendor:</strong> ' . $values['nycha_vendor'] . '</div>');
                $user_criteria['Vendor'] = $values['nycha_vendor'];
                $formatted_search_criteria['Vendor'] = $values['nycha_vendor'];
            }
            if ($values['resp_center']) {
                $form['filter']['resp_center'] = array('#markup' => '<div><strong>Responsibility Center:</strong> ' . $values['resp_center'] . '</div>');
                $user_criteria['Responsibility Center'] = $values['resp_center'];
                $formatted_search_criteria['Responsibility Center'] = $values['resp_center'];
            }
            if ($values['nycha_contract_type']) {
                if ($values['nycha_contract_type'] != 'No Contract Type Selected') {
                    $form['filter']['nycha_contract_type'] = array('#markup' => '<div><strong>Contract Type:</strong> ' . $values['nycha_contract_type'] . '</div>');
                    $user_criteria['Contract Type'] = $values['nycha_contract_type'];
                    $formatted_search_criteria['Contract Type'] = $values['nycha_contract_type'];
                }
            }
            if ($values['nycha_awd_method']) {
                if ($values['nycha_awd_method'] != 'No Award Method Selected') {
                    $form['filter']['nycha_awd_method'] = array('#markup' => '<div><strong>Award Method:</strong> ' . $values['nycha_awd_method'] . '</div>');
                    $user_criteria['Award Method'] = $values['nycha_awd_method'];
                    $formatted_search_criteria['Award Method'] = $values['nycha_awd_method'];
                }
            }
            if ($values['nycha_industry']) {
                if ($values['nycha_industry'] != 'No Industry Selected') {
                    $form['filter']['nycha_industry'] = array('#markup' => '<div><strong>Industry:</strong> ' . $values['nycha_industry'] . '</div>');
                    $user_criteria['Industry'] = $values['nycha_industry'];
                    $formatted_search_criteria['Industry'] = $values['nycha_industry'];
                }
            }
            if ($values['nycha_currentamtfrom'] && $values['nycha_currentamtto']) {
                $form['filter']['nycha_current_amount'] = array('#markup' => '<div><strong>Current Amount:</strong> Greater Than Equal to: $' . $values['nycha_currentamtfrom'] . ' Less Than Equal to: $' . $values['nycha_currentamtto'] . '</div>');
                $user_criteria['Current Amount Greater Than'] = $values['nycha_currentamtfrom'];
                $user_criteria['Current Amount Less Than'] = $values['nycha_currentamtto'];
                $formatted_search_criteria['Current Amount'] = 'Greater Than Equal to: $' . $values['nycha_currentamtfrom'] . ' Less Than Equal to: $' . $values['nycha_currentamtto'];
            } elseif (!$values['nycha_currentamtfrom'] && $values['nycha_currentamtto']) {
                $form['filter']['nycha_current_amount'] = array('#markup' => '<div><strong>Current Amount:</strong> Less Than Equal to: $' . $values['nycha_currentamtto'] . '</div>');
                $user_criteria['Current Amount Less Than'] = $values['currentamtto'];
                $formatted_search_criteria['Current Amount'] = 'Less Than Equal to: $' . $values['nycha_currentamtto'];
            } elseif ($values['nycha_currentamtfrom'] && !$values['nycha_currentamtto']) {
                $form['filter']['nycha_current_amount'] = array('#markup' => '<div><strong>Current Amount:</strong> Greater Than Equal to: $' . $values['nycha_currentamtfrom'] . '</div>');
                $user_criteria['Current Amount Greater Than'] = $values['nycha_currentamtfrom'];
                $formatted_search_criteria['Current Amount'] = 'Greater Than Equal to: $' . $values['nycha_currentamtfrom'];
            }

            if ($values['nycha_agency']) {
                $form['filter']['nycha_agency'] = array(
                    '#markup' => '<div><strong>Other Government Entities:</strong> ' . $values['nycha_agency'] . '</div>',
                );
                $user_criteria['Other Government Entities'] = $values['nycha_agency'];
                $formatted_search_criteria['Other Government Entities'] = $values['nycha_agency'];
            }
            if ($values['nycha_purpose']) {
                $form['filter']['nycha_purpose'] = array(
                    '#markup' => '<div><strong>Purpose:</strong> ' . $values['nycha_purpose'] . '</div>',
                );
                $user_criteria['Purpose'] = $values['nycha_purpose'];
                $formatted_search_criteria['Purpose'] = $values['nycha_purpose'];
            }
            if ($values['nycha_apt_pin']) {
                $form['filter']['nycha_apt_pin'] = array(
                    '#markup' => '<div><strong>APT PIN:</strong> ' . $values['nycha_apt_pin'] . '</div>',
                );
                $user_criteria['APT PIN'] = $values['nycha_apt_pin'];
                $formatted_search_criteria['APT PIN'] = $values['nycha_apt_pin'];
            }

            if ($values['nycha_startdatefrom'] && $values['nycha_startdateto']) {
                $form['filter']['nycha_start_date'] = array('#markup' => '<div><strong>Start Date:</strong> Greater Than Equal to: ' . $values['nycha_startdatefrom'] . ' Less Than Equal to: ' . $values['nycha_startdateto'] . '</div>');
                $user_criteria['Start Date Greater Than'] = $values['nycha_startdatefrom'];
                $user_criteria['Start Date Less Than'] = $values['nycha_startdateto'];
                $formatted_search_criteria['Start Date'] = 'Greater Than Equal to: ' . $values['startdatefrom'] . ' Less Than Equal to: ' . $values['startdateto'];
            } elseif (!$values['nycha_startdatefrom'] && $values['nycha_startdateto']) {
                $form['filter']['nycha_start_date'] = array('#markup' => '<div><strong>Start Date:</strong> Less Than Equal to: ' . $values['nycha_startdateto'] . '</div>');
                $user_criteria['Start Date Less Than'] = $values['nycha_startdateto'];
                $formatted_search_criteria['Start Date'] = 'Less Than Equal to: ' . $values['startdateto'];
            } elseif ($values['nycha_startdatefrom'] && !$values['nycha_startdateto']) {
                $form['filter']['nycha_start_date'] = array('#markup' => '<div><strong>Start Date:</strong> Greater Than Equal to: ' . $values['nycha_startdatefrom'] . '</div>');
                $user_criteria['Start Date Greater Than'] = $values['nycha_startdatefrom'];
                $formatted_search_criteria['Start Date'] = 'Greater Than Equal to: ' . $values['nycha_startdatefrom'];
            }
            if ($values['nycha_enddatefrom'] && $values['nycha_enddateto']) {
                $form['filter']['nycha_end_date'] = array('#markup' => '<div><strong>End Date:</strong> Greater Than Equal to: ' . $values['nycha_enddatefrom'] . ' Less Than Equal to: ' . $values['nycha_enddateto'] . '</div>');
                $user_criteria['End Date Greater Than'] = $values['nycha_enddatefrom'];
                $user_criteria['End Date Less Than'] = $values['nycha_enddateto'];
                $formatted_search_criteria['End Date'] = 'Greater Than Equal to: ' . $values['nycha_enddatefrom'] . ' Less Than Equal to: ' . $values['nycha_enddateto'];
            } elseif (!$values['nycha_enddatefrom'] && $values['nycha_enddateto']) {
                $form['filter']['nycha_end_date'] = array('#markup' => '<div><strong>End Date:</strong> Less Than Equal to: ' . $values['nycha_enddateto'] . '</div>');
                $user_criteria['End Date Less Than'] = $values['nycha_enddateto'];
                $formatted_search_criteria['End Date'] = 'Less Than Equal to: ' . $values['nycha_enddateto'];
            } elseif ($values['nycha_enddatefrom'] && !$values['nycha_enddateto']) {
                $form['filter']['nycha_end_date'] = array('#markup' => '<div><strong>End Date:</strong> Greater Than Equal to: ' . $values['nycha_enddatefrom'] . '</div>');
                $user_criteria['End Date Greater Than'] = $values['nycha_enddatefrom'];
                $formatted_search_criteria['End Date'] = 'Greater Than Equal to: ' . $values['nycha_enddatefrom'];
            }

            if ($values['nycha_appr_datefrom'] && $values['nycha_appr_dateto']) {
                $form['filter']['nycha_approved_date'] = array('#markup' => '<div><strong>Approved Date:</strong> Greater Than Equal to: ' . $values['nycha_enddatefrom'] . ' Less Than Equal to: ' . $values['nycha_appr_dateto'] . '</div>');
                $user_criteria['Approved Date Greater Than'] = $values['nycha_appr_datefrom'];
                $user_criteria['Approved Date Less Than'] = $values['nycha_appr_dateto'];
                $formatted_search_criteria['Approved Date'] = 'Greater Than Equal to: ' . $values['nycha_appr_datefrom'] . ' Less Than Equal to: ' . $values['nycha_appr_dateto'];
            } elseif (!$values['nycha_appr_datefrom'] && $values['nycha_appr_dateto']) {
                $form['filter']['approved'] = array('#markup' => '<div><strong>Approved Date:</strong> Less Than Equal to: ' . $values['nycha_appr_dateto'] . '</div>');
                $user_criteria['Approved Date Less Than'] = $values['nycha_appr_dateto'];
                $formatted_search_criteria['Approved Date'] = 'Less Than Equal to: ' . $values['nycha_appr_dateto'];
            } elseif ($values['nycha_appr_datefrom'] && !$values['nycha_appr_dateto']) {
                $form['filter']['approved'] = array('#markup' => '<div><strong>Approved Date:</strong> Greater Than Equal to: ' . $values['nycha_appr_datefrom'] . '</div>');
                $user_criteria['Approved Date Greater Than'] = $values['nycha_appr_datefrom'];
                $formatted_search_criteria['Approved Date'] = 'Greater Than Equal to: ' . $values['nycha_appr_datefrom'];
            }

            if ($values['nycha_year']) {
                if($values['nycha_year'] == 'ALL') {
                    $form['filter']['nycha_year'] = array('#markup' => '<div><strong>Year:</strong> All Years</div>',);
                    $formatted_search_criteria['Year'] = 'All Years';
                }
                else {
                    $form['filter']['nycha_year'] = array('#markup' => '<div><strong>Year:</strong> ' . $values['nycha_year'] . '</div>',);
                    $formatted_search_criteria['Year'] = $values['nycha_year'];
                }
            }
            break;
        default:
            if ($values['df_contract_status']) {
                $form['filter']['df_contract_status'] = array('#markup' => '<div><strong>Status:</strong> ' . $values['df_contract_status'] . '</div>');
                $user_criteria['Contract Status'] = $values['df_contract_status'];
                $formatted_search_criteria['Status'] = $values['df_contract_status'];
            }
            if ($values['vendor']) {
                $form['filter']['vendor'] = array(
                    '#markup' => '<div><strong>Vendor:</strong> ' . $values['vendor'] . '</div>',
                );
                $user_criteria['Vendor'] = $values['vendor'];
                $formatted_search_criteria['Vendor'] = $values['vendor'];
            }
            if ($values['mwbe_category']) {
                $form['filter']['mwbe_category'] = array('#markup' => '<div><strong>M/WBE Category:</strong> ' . MappingUtil::getCurrenEthnicityName(explode('~',$values['mwbe_category'])) . '</div>');
                $user_criteria['M/WBE Category'] = $values['mwbe_category'];
                $formatted_search_criteria['M/WBE Category'] = MappingUtil::getCurrenEthnicityName(explode('~',$values['mwbe_category']));
            }
            if ($values['contract_type']) {
                if ($values['contract_type'] != 'No Contract Type Selected') {
                    $form['filter']['contract_type'] = array('#markup' => '<div><strong>Contract Type:</strong> ' . $values['contract_type'] . '</div>');
                    $user_criteria['Contract Type'] = $values['contract_type'];
                    $formatted_search_criteria['Contract Type'] = $values['contract_type'];
                }
            }
            if ($values['contractno']) {
                $form['filter']['contractno'] = array(
                    '#markup' => '<div><strong>Contract ID:</strong> ' . $values['contractno'] . '</div>',
                );
                $user_criteria['Contract ID'] = $values['contractno'];
                $formatted_search_criteria[''] = null;
            }
            if ($values['contract_includes_sub_vendors_id']) {
                if ($values['contract_includes_sub_vendors_id'] != 'Select Status') {
                    $scntrc_status_name = MappingUtil::getscntrc_status_name($values['contract_includes_sub_vendors_id']);
                    $form['filter']['contract_includes_sub_vendors_id'] = array('#markup' => '<div><strong>Contract Includes Sub Vendors:</strong> ' . $scntrc_status_name . '</div>');
                    $user_criteria['Contract Includes Sub Vendors'] = $values['contract_includes_sub_vendors_id'];
                    $formatted_search_criteria['Contract Includes Sub Vendors'] = $scntrc_status_name;
                }
            }
            if ($values['pin']) {
                $form['filter']['pin'] = array(
                    '#markup' => '<div><strong>PIN:</strong> ' . $values['pin'] . '</div>',
                );
                $user_criteria['PIN'] = $values['pin'];
                $formatted_search_criteria['PIN'] = $values['pin'];
            }
            if ($values['currentamtfrom'] && $values['currentamtto']) {
                $form['filter']['current_amount'] = array('#markup' => '<div><strong>Current Amount:</strong> Greater Than Equal to: $' . $values['currentamtfrom'] . ' Less Than Equal to: $' . $values['currentamtto'] . '</div>');
                $user_criteria['Current Amount Greater Than'] = $values['currentamtfrom'];
                $user_criteria['Current Amount Less Than'] = $values['currentamtto'];
                $formatted_search_criteria['Current Amount'] = 'Greater Than Equal to: $' . $values['currentamtfrom'] . ' Less Than Equal to: $' . $values['currentamtto'];
            } elseif (!$values['currentamtfrom'] && $values['currentamtto']) {
                $form['filter']['current_amount'] = array('#markup' => '<div><strong>Current Amount:</strong> Less Than Equal to: $' . $values['currentamtto'] . '</div>');
                $user_criteria['Current Amount Less Than'] = $values['currentamtto'];
                $formatted_search_criteria['Current Amount'] = 'Less Than Equal to: $' . $values['currentamtto'];
            } elseif ($values['currentamtfrom'] && !$values['currentamtto']) {
                $form['filter']['current_amount'] = array('#markup' => '<div><strong>Current Amount:</strong> Greater Than Equal to: $' . $values['currentamtfrom'] . '</div>');
                $user_criteria['Current Amount Greater Than'] = $values['currentamtfrom'];
                $formatted_search_criteria['Current Amount'] = 'Greater Than Equal to: $' . $values['currentamtfrom'];
            }
            if ($values['enddatefrom'] && $values['enddateto']) {
                $form['filter']['end_date'] = array('#markup' => '<div><strong>End Date:</strong> Greater Than Equal to: ' . $values['enddatefrom'] . ' Less Than Equal to: ' . $values['enddateto'] . '</div>');
                $user_criteria['End Date Greater Than'] = $values['enddatefrom'];
                $user_criteria['End Date Less Than'] = $values['enddateto'];
                $formatted_search_criteria['End Date'] = 'Greater Than Equal to: ' . $values['enddatefrom'] . ' Less Than Equal to: ' . $values['enddateto'];
            } elseif (!$values['enddatefrom'] && $values['enddateto']) {
                $form['filter']['end_date'] = array('#markup' => '<div><strong>End Date:</strong> Less Than Equal to: ' . $values['enddateto'] . '</div>');
                $user_criteria['End Date Less Than'] = $values['enddateto'];
                $formatted_search_criteria['End Date'] = 'Less Than Equal to: ' . $values['enddateto'];
            } elseif ($values['enddatefrom'] && !$values['enddateto']) {
                $form['filter']['end_date'] = array('#markup' => '<div><strong>End Date:</strong> Greater Than Equal to: ' . $values['enddatefrom'] . '</div>');
                $user_criteria['End Date Greater Than'] = $values['enddatefrom'];
                $formatted_search_criteria['End Date'] = 'Greater Than Equal to: ' . $values['enddatefrom'];
            }
            if ($values['regdatefrom'] && $values['regdateto']) {
                $form['filter']['regdate'] = array('#markup' => '<div><strong>Registration Date:</strong> Greater Than Equal to: ' . $values['regdatefrom'] . ' Less Than Equal to: ' . $values['regdateto'] . '</div>');
                $user_criteria['Registered Date Greater Than'] = $values['regdatefrom'];
                $user_criteria['Registered Date Less Than'] = $values['regdateto'];
                $formatted_search_criteria['Registration Date'] = 'Greater Than Equal to: ' . $values['regdatefrom'] . ' Less Than Equal to: ' . $values['regdateto'];
            } elseif (!$values['regdatefrom'] && $values['regdateto']) {
                $form['filter']['regdate'] = array('#markup' => '<div><strong>Registration Date:</strong> Less Than Equal to: ' . $values['regdateto'] . '</div>');
                $user_criteria['Registered Date Less Than'] = $values['regdateto'];
                $formatted_search_criteria['Registration Date'] = 'Less Than Equal to: ' . $values['regdateto'];
            } elseif ($values['regdatefrom'] && !$values['regdateto']) {
                $form['filter']['regdate'] = array('#markup' => '<div><strong>Registration Date:</strong> Greater Than Equal to: $' . $values['regdatefrom'] . '</div>');
                $user_criteria['Registered Date Greater Than'] = $values['regdatefrom'];
                $formatted_search_criteria['Registration Date'] = 'Greater Than Equal to: $' . $values['regdatefrom'];
            }
            if ($values['category']) {
                $form['filter']['category'] = array('#markup' => '<div><strong>Category:</strong> ' . $values['category'] . '</div>');
                $user_criteria['Category'] = $values['category'];
                $formatted_search_criteria['Category'] = $values['category'];
            }
            if ($values['sub_vendor_status_in_pip_id']) {
                if ($values['sub_vendor_status_in_pip_id'] != 'Select Status') {
                    $aprv_sta_name = MappingUtil::getaprv_sta_name($values['sub_vendor_status_in_pip_id']);
                    $form['filter']['sub_vendor_status_in_pip_id'] = array('#markup' => '<div><strong>Sub Vendor Status in PIP&nbsp;:</strong> ' . $aprv_sta_name . '</div>');
                    $user_criteria['Sub Vendor Status in PIP'] = $values['sub_vendor_status_in_pip_id'];
                    $formatted_search_criteria['Sub Vendor Status in PIP'] = $aprv_sta_name;
                }
            }
            if ($values['purpose']) {
                $form['filter']['purpose'] = array(
                    '#markup' => '<div><strong>Purpose:</strong> ' . $values['purpose'] . '</div>',
                );
                $user_criteria['Purpose'] = $values['purpose'];
                $formatted_search_criteria['Purpose'] = $values['purpose'];
            }
            if ($values['agency']) {
                $form['filter']['agency'] = array(
                    '#markup' => '<div><strong>Agency:</strong> ' . $agency . '</div>',
                );
                $user_criteria['Agency'] = $values['agency'];
                $formatted_search_criteria['Agency'] = $values['agency'];
            }
            if ($values['industry']) {
                preg_match("/.*?(\\[.*?\\])/is", $values['industry'], $matches);
                $industry_type_name = str_replace($matches[1],"",$matches[0]);
                $industry_type_id = trim($matches[1], '[ ]');
                $form['filter']['industry'] = array('#markup' => '<div><strong>Industry:</strong> ' . $industry_type_name . '</div>');
                $user_criteria['Industry'] = $industry_type_id;
                $formatted_search_criteria['Industry'] = $industry_type_name;
            }
            if ($values['apt_pin']) {
                $form['filter']['apt_pin'] = array(
                    '#markup' => '<div><strong>APT PIN:</strong> ' . $values['apt_pin'] . '</div>',
                );
                $user_criteria['APT PIN'] = $values['apt_pin'];
                $formatted_search_criteria['APT PIN'] = $values['apt_pin'];
            }
            if ($values['award_method']) {
                if ($values['award_method'] != 'No Award Method Selected') {
                    $form['filter']['award_method'] = array('#markup' => '<div><strong>Award Method:</strong> ' . $values['award_method'] . '</div>');
                    $user_criteria['Award Method'] = $values['award_method'];
                    $formatted_search_criteria['Award Method'] = $values['award_method'];
                }
            }
            if ($values['startdatefrom'] && $values['startdateto']) {
                $form['filter']['start_date'] = array('#markup' => '<div><strong>Start Date:</strong> Greater Than Equal to: ' . $values['startdatefrom'] . ' Less Than Equal to: ' . $values['startdateto'] . '</div>');
                $user_criteria['Start Date Greater Than'] = $values['startdatefrom'];
                $user_criteria['Start Date Less Than'] = $values['startdateto'];
                $formatted_search_criteria['Start Date'] = 'Greater Than Equal to: ' . $values['startdatefrom'] . ' Less Than Equal to: ' . $values['startdateto'];
            } elseif (!$values['startdatefrom'] && $values['startdateto']) {
                $form['filter']['start_date'] = array('#markup' => '<div><strong>Start Date:</strong> Less Than Equal to: ' . $values['startdateto'] . '</div>');
                $user_criteria['Start Date Less Than'] = $values['startdateto'];
                $formatted_search_criteria['Start Date'] = 'Less Than Equal to: ' . $values['startdateto'];
            } elseif ($values['startdatefrom'] && !$values['startdateto']) {
                $form['filter']['start_date'] = array('#markup' => '<div><strong>Start Date:</strong> Greater Than Equal to: ' . $values['startdatefrom'] . '</div>');
                $user_criteria['Start Date Greater Than'] = $values['startdatefrom'];
                $formatted_search_criteria['Start Date'] = 'Greater Than Equal to: ' . $values['startdatefrom'];
            }
            if ($values['recdatefrom'] && $values['recdateto']) {
                $form['filter']['received_date'] = array('#markup' => '<div><strong>Received Date:</strong> Greater Than Equal to: ' . $values['recdatefrom'] . ' Less Than Equal to: ' . $values['recdateto'] . '</div>');
                $user_criteria['Received Date Greater Than'] = $values['recdatefrom'];
                $user_criteria['Received Date Less Than'] = $values['recdateto'];
                $formatted_search_criteria['Received Date'] = 'Greater Than Equal to: ' . $values['recdatefrom'] . ' Less Than Equal to: ' . $values['recdateto'];
            } elseif (!$values['recdatefrom'] && $values['recdateto']) {
                $form['filter']['received_date'] = array('#markup' => '<div><strong>Received Date:</strong> Less Than Equal to: ' . $values['recdateto'] . '</div>');
                $user_criteria['Received Date Less Than'] = $values['recdateto'];
                $formatted_search_criteria['Received Date'] = 'Less Than Equal to: ' . $values['recdateto'];
            } elseif ($values['recdatefrom'] && !$values['recdateto']) {
                $form['filter']['received_date'] = array('#markup' => '<div><strong>Received Date:</strong> Greater Than Equal to: ' . $values['recdatefrom'] . '</div>');
                $user_criteria['Received Date Greater Than'] = $values['recdatefrom'];
                $formatted_search_criteria['Received Date'] = 'Greater Than Equal to: ' . $values['recdatefrom'];
            }
            if ($values['year']) {
                if($values['year'] == 'ALL') {
                    $form['filter']['year'] = array(
                        '#markup' => '<div><strong>Year:</strong> All Years</div>',
                    );
                    $formatted_search_criteria['Year'] = 'All Years';
                }
                else {
                    $form['filter']['year'] = array(
                        '#markup' => '<div><strong>Year:</strong> ' . $values['year'] . '</div>',
                    );
                    $formatted_search_criteria['Year'] = $values['year'];
                }
            }
            break;
    }

    $user_criteria['Formatted'] = $formatted_search_criteria;
    $criteria = checkbook_datafeeds_process_contracts_values($form, $form_state, $data_source);
    $criteria['required_columns'] = checkbook_datafeeds_contracts_add_required_columns($response_type, $data_source,$values['df_contract_status'],$values['category'],$year);
    $form_state['step_information']['confirmation']['stored_values']['criteria'] = $criteria;
    $form_state['step_information']['confirmation']['stored_values']['user_criteria'] = $user_criteria;

    //Update Criteria with overridden column order on export only for CityWide Active/Registered Expense Contracts
    if(strtolower($values['df_contract_status']) != 'pending' && strtolower($values['category']) != 'revenue' && !in_array($data_source, array(Datasource::OGE, Datasource::NYCHA))){
        $intended_order =  _checkbook_datafeeds_contracts_override_column_options($response_type, $data_source,$values['df_contract_status'],$values['category'],$year);
        $criteria['responseColumns'] = checkbook_datafeeds_override_column_order($criteria['responseColumns'], $intended_order);
    }

    $modified_form = checkbook_datafeeds_end_of_confirmation_form($form, $form_state, $criteria, $response_type, 'contracts');
    return $modified_form;
}

/**
 * Column options for contracts domain.
 *
 * @param $data_source
 * @param $status
 * @param $category
 * @param string $data_type
 *   Type of data for Data Feeds to return (xml or csv)
 * @return array
 *   FAPI structured array for select box #options
 */
function _checkbook_datafeeds_contracts_column_options($data_source, $status, $category, $data_type)
{
    $options = DatafeedsConfigUtil::getConfig(strtolower('contracts'));

    $options = !isset($options['data_source'][$data_source]) ? $options['data_source']['checkbook'] : $options['data_source'][$data_source];
    $options = !isset($options['status'][$status]) ? $options['status']['default'] : $options['status'][$status];
    $options = !isset($options['category'][$category]) ? $options['category']['default'] : $options['category'][$category];
    $options = !isset($options['data_type'][$data_type]) ? $options['data_type']['default'] : $options['data_type'][$data_type];

    return $options;
}

/**
 * Reads the checkbook api configuration file to get the column order to override on export
 * @param $response_type
 * @param $data_source
 * @param $status
 * @param $expense_category
 * @param $year
 * @return array
 */
function _checkbook_datafeeds_contracts_override_column_options($response_type, $data_source, $status, $expense_category, $year)
{
    //Get column configurations
    $domain = 'contracts';
    if($data_source == Datasource::OGE){
        $domain = $domain.'_oge';
    }else if($data_source == Datasource::NYCHA){
        $domain = $domain.'_nycha';
    }
    $domain_config = ConfigUtil::getDomainConfiguration($domain);
    $domain_filter = checkbook_datafeeds_get_configuration_key($domain, $data_source, $status, $expense_category, $year);

    //Get columns which should be required to get but not display
    $override_columns = $domain_config->$domain_filter->dataset->displayConfiguration->$response_type->overrideColumns;

    return (array)$override_columns;
}


/**
 * Retreives form values from previous step and moves back one step in Contracts.
 *
 * @param array $form
 *   Data Feeds wizard form array
 * @param array $form_state
 *   Data Feeds wizard form_state array
 */
function checkbook_datafeeds_contracts_confirmation_previous_submit($form, &$form_state) {
    $current_step = & $form_state['step'];
    $form_state['step_information'][$current_step]['stored_values'] = $form_state['values'];
    $current_step = 'contracts';
    $form_state['values'] = $form_state['step_information'][$current_step]['stored_values'];
    $form_state['rebuild'] = TRUE;
}


/**
 * Convert values from Contracts section of form to an array format expected by API SearchCriteria.
 *
 * @param array $form
 *   Data Feeds wizard form array
 * @param array $form_state
 *   Data Feeds wizard form_state array
 *
 * @param $data_source
 * @return array
 *   Structured array expected by API
 */
function checkbook_datafeeds_process_contracts_values($form, &$form_state, $data_source){
    $values = $form_state['step_information']['contracts']['stored_values'];
    $pattern = "/.*?(\\[.*?\\])/is";
    global $conf;
    $max_records= isset($conf['check_book']['data_feeds']['max_record_limit']) ? $conf['check_book']['data_feeds']['max_record_limit'] : 200000;

    if($data_source == Datasource::OGE){
        $type_of_data = 'Contracts_OGE';
    }else if($data_source == Datasource::NYCHA){
        $type_of_data = 'Contracts_NYCHA';
    }else{
        $type_of_data = 'Contracts';
    }

    $criteria = array(
        'global' => array(
            //Set data source for query
            'type_of_data' => $type_of_data,
            'records_from' => 1,
            'max_records' => $max_records,
        ),
    );
    if($data_source == Datasource::OGE){
        $criteria['responseColumns'] = $values['column_select_oge_expense'];
    }else if($data_source == Datasource::NYCHA){
        $criteria['responseColumns'] = $values['column_select_nycha'];
    }else{
        if($values['df_contract_status'] == 'pending'){
            if ($values['category'] != 'all') {
                $criteria['responseColumns'] = $values['column_select_pending'];
            }
            if ($values['category'] == 'all') {
                $criteria['responseColumns'] = $values['column_select_pending_all'];
            }
        }else{
            if($values['category'] == 'all'){
                $criteria['responseColumns'] = $values['column_select_all'];
            }
            if($values['category'] == 'expense'){
                $criteria['responseColumns'] = $values['column_select_expense'];
            }
            if($values['category'] == 'revenue'){
                $criteria['responseColumns'] = $values['column_select_revenue'];
            }
        }
    }

    if($data_source != Datasource::NYCHA) {
        if ($values['year'] && $values['year'] != 'ALL') {
            if (startsWith($values['year'], 'F')) {
                $criteria['value']['fiscal_year'] = ltrim($values['year'], 'FY');
            } elseif (startsWith($values['year'], 'C')) {
                $criteria['value']['calendar_year'] = ltrim($values['year'], 'CY');
            }
        }

        if ($values['currentamtfrom'] !== '' || $values['currentamtto'] !== '') {
            $criteria['range']['current_amount'] = array(
                checknull($values['currentamtfrom']),
                checknull($values['currentamtto']),
            );
        }
        if ($values['award_method'] && $values['award_method'] != 'No Award Method Selected') {
            preg_match($pattern, $values['award_method'], $awmatches);
            $criteria['value']['award_method'] = trim($awmatches[1], '[ ]');
        }
        if ($values['contractno']) {
            $criteria['value']['contract_id'] = $values['contractno'];
        }
        if ($values['contract_includes_sub_vendors_id'] != 'Select Status' && $values['contract_includes_sub_vendors_id'] != 0) {
            $criteria['value']['contract_includes_sub_vendors_id'] = $values['contract_includes_sub_vendors_id'];
        }
        if ($values['sub_vendor_status_in_pip_id'] != 'Select Status' && $values['sub_vendor_status_in_pip_id'] != 0) {
            $criteria['value']['sub_vendor_status_in_pip_id'] = $values['sub_vendor_status_in_pip_id'];
        }
        if ($values['contract_type'] != 'No Contract Type Selected') {
            preg_match($pattern, $values['contract_type'], $ctypematches);
            $criteria['value']['contract_type'] = trim($ctypematches[1], '[ ]');
        }
        if ($values['startdatefrom'] !== '' || $values['startdateto'] !== '') {
            $criteria['range']['start_date'] = array(
                checknull($values['startdatefrom']),
                checknull($values['startdateto']),
            );
        }
        if ($values['enddatefrom'] !== '' || $values['enddateto'] !== '') {
            $criteria['range']['end_date'] = array(
                checknull($values['enddatefrom']),
                checknull($values['enddateto']),
            );
        }
        if ($values['df_contract_status']) {
            $criteria['value']['status'] = $values['df_contract_status'];
        }
        if ($values['category']) {
            $criteria['value']['category'] = $values['category'];
        }
        if ($data_source != Datasource::OGE) {
            if ($values['regdatefrom'] !== '' || $values['regdateto'] !== '') {
                $criteria['range']['registration_date'] = array(
                    checknull($values['regdatefrom']),
                    checknull($values['regdateto']),
                );
            }
            if ($values['recdatefrom'] !== '' || $values['recdateto'] !== '') {
                $criteria['range']['received_date'] = array(
                    checknull($values['recdatefrom']),
                    checknull($values['recdateto']),
                );
            }
        }
        if ($values['pin']) {
            $criteria['value']['pin'] = strtoupper($values['pin']);
        }
        if ($values['apt_pin']) {
            $criteria['value']['apt_pin'] = strtoupper($values['apt_pin']);
        }
        if ($values['purpose']) {
            $criteria['value']['purpose'] = $values['purpose'];
        }
    }
    //By data source
    switch($data_source)
    {
        case Datasource::OGE:
            if ($values['agency'] != 'Select One') {
                preg_match($pattern, $values['agency'], $amatches);
                $criteria['value']['other_government_entities_code'] = trim($amatches[1], '[ ]');
            }
            if ($values['entity_contract_number']) {
                $criteria['value']['entity_contract_number'] = $values['entity_contract_number'];
            }
            if ($values['commodity_line']) {
                $criteria['value']['commodity_line'] = $values['commodity_line'];
            }
            if ($values['budget_name']) {
                $criteria['value']['budget_name'] = $values['budget_name'];
            }
            if ($values['vendor']) {
                $criteria['value']['prime_vendor'] = $values['vendor'];
            }
            break;
        case Datasource::NYCHA:
            if ($values['purchase_order_type'] && $values['purchase_order_type'] != 'All') {
                preg_match($pattern, $values['purchase_order_type'], $pmatches);
                if ($pmatches) {
                    $criteria['value']['purchase_order_type'] = trim($pmatches[1], '[ ]');
                }else{
                    $criteria['value']['purchase_order_type'] = $values['purchase_order_type'];
                }
            }
            if ($values['nycha_contract_id']) {
                $criteria['value']['contract_id'] = $values['nycha_contract_id'];
            }
            if ($values['nycha_vendor']) {
                preg_match($pattern, $values['nycha_vendor'], $vmatches);
                if ($vmatches) {
                    $criteria['value']['vendor'] = trim($vmatches[1], '[ ]');
                }
                else {
                    $criteria['value']['vendor'] = $values['nycha_vendor'];
                }
            }
            if ($values['resp_center']) {
                preg_match($pattern, $values['resp_center'], $amatches);
                $criteria['value']['resp_center'] = trim($amatches[1], '[ ]');
            }
            if ($values['nycha_contract_type']) {
                preg_match($pattern, $values['nycha_contract_type'], $amatches);
                $criteria['value']['contract_type'] = trim($amatches[1], '[ ]');
            }
            if ($values['nycha_awd_method']) {
                preg_match($pattern, $values['nycha_awd_method'], $amatches);
                $criteria['value']['award_method'] = trim($amatches[1], '[ ]');
            }
            if ($values['nycha_industry']) {
                preg_match($pattern, $values['nycha_industry'], $amatches);
                $criteria['value']['industry'] = trim($amatches[1], '[ ]');
            }
            if ($values['nycha_currentamtfrom'] !== '' || $values['nycha_currentamtto'] !== '') {
                $criteria['range']['current_amount'] = array(checknull($values['nycha_currentamtfrom']), checknull($values['nycha_currentamtto']),);
            }
            if ($values['nycha_agency'] != 'Select One') {
                preg_match($pattern, $values['nycha_agency'], $amatches);
                $criteria['value']['other_government_entities_code'] = trim($amatches[1], '[ ]');
            }
            if ($values['nycha_purpose']) {
                $criteria['value']['purpose'] = $values['nycha_purpose'];
            }
            if ($values['nycha_apt_pin']) {
                $criteria['value']['apt_pin'] = strtoupper($values['nycha_apt_pin']);
            }
            if ($values['nycha_startdatefrom'] !== '' || $values['nycha_startdateto'] !== '') {
                $criteria['range']['start_date'] = array(checknull($values['nycha_startdatefrom']), checknull($values['nycha_startdateto']),);
            }
            if ($values['nycha_enddatefrom'] !== '' || $values['nycha_enddateto'] !== '') {
                $criteria['range']['end_date'] = array(checknull($values['nycha_enddatefrom']), checknull($values['nycha_enddateto']),);
            }
            if ($values['nycha_appr_datefrom'] !== '' || $values['nycha_appr_dateto'] !== '') {
                $criteria['range']['approved_date'] = array(checknull($values['nycha_appr_datefrom']), checknull($values['nycha_appr_dateto']),);
            }
            if ($values['nycha_year'] && $values['nycha_year'] != 'ALL') {
                if (startsWith($values['nycha_year'], 'F')) {
                    $criteria['value']['fiscal_year'] = ltrim($values['nycha_year'], 'FY');
                }
            }
            break;
        default:
            if ($values['agency'] != 'Citywide (All Agencies)') {
                preg_match($pattern, $values['agency'], $amatches);
                $criteria['value']['agency_code'] = trim($amatches[1], '[ ]');
            }
            if ($values['vendor']) {
                preg_match($pattern, $values['vendor'], $vmatches);
                if ($vmatches) {
                    $criteria['value']['vendor_code'] = trim($vmatches[1], '[ ]');
                }
                else {
                    $criteria['value']['vendor_code'] = $values['vendor'];
                }
            }
            if ($values['mwbe_category'] && $values['mwbe_category'] != 'Select Category') {
                $criteria['value']['mwbe_category'] = $values['mwbe_category'];
            }
            if ($values['industry'] && $values['industry'] != 'Select Industry') {
                preg_match($pattern, $values['industry'], $imatches);
                $criteria['value']['industry_type_id'] = trim($imatches[1], '[ ]');
            }
            break;
    }

    return $criteria;
}

/**
 * Validate handler for Contracts section of form.
 *
 * @param array $form
 *   Data Feeds wizard form array
 * @param array $form_state
 *   Data Feeds wizard form_state array
 */
function checkbook_datafeeds_contracts_validate($form, &$form_state)
{
    $data_source = $form_state['input']['datafeeds-contracts-domain-filter'];
    $agency = $form_state['input']['agency'];
    $agency_code = emptyToZero($agency);
    if($data_source == Datasource::NYCHA){
        if($agency_code == 'z81'){
            $data_source = Datasource::OGE;
        }
    }
    if($data_source == Datasource::NYCHA){
        $startfrom = $form_state['values']['nycha_startdatefrom'];
        $startto = $form_state['values']['nycha_startdateto'];
        $enddatefrom = $form_state['values']['nycha_enddatefrom'];
        $enddateto = $form_state['values']['nycha_enddateto'];
        $appr_date_from = $form_state['values']['nycha_appr_datefrom'];
        $appr_date_to = $form_state['values']['nycha_appr_dateto'];
        $currentfrom = $form_state['values']['nycha_currentamtfrom'];
        $currentto = $form_state['values']['nycha_currentamtto'];
        $nycha_multi_select = $form_state['values']['column_select_nycha'];
        $vendor = $form_state['values']['nycha_vendor'];

        $multi_select_hidden = isset($form_state['input']['column_select_nycha']) ? '|' . implode('||', $form_state['input']['column_select_nycha']) . '|' : '';

        // Current Amount:
        if ($currentfrom && !is_numeric($currentfrom)) {
            form_set_error('nycha_currentamtfrom', t('Current Amount must be a number.'));
        }
        if ($currentto && !is_numeric($currentto)) {
            form_set_error('nycha_currentamtto', t('Current Amount must be a number.'));
        }
        if ($currentfrom && $currentto && $currentto < $currentfrom) {
            form_set_error('nycha_currentamtto', t('Invalid range for Current Amount.'));
        }
        // Start Date:
        if ($startfrom && !checkDateFormat($startfrom)) {
            form_set_error('nycha_startdatefrom', t('Start Date must be a valid date (YYYY-MM-DD).'));
        }
        if ($startto && !checkDateFormat($startto)) {
            form_set_error('nycha_startdateto', t('Start Date must be a valid date (YYYY-MM-DD).'));
        }
        if ($startfrom && $startto && strtotime($startto) < strtotime($startfrom)) {
            form_set_error('nycha_startdateto', t('Invalid date range for Start Date.'));
        }
        // End Date:
        if ($enddatefrom && !checkDateFormat($enddatefrom)) {
            form_set_error('nycha_enddatefrom', t('End Date must be a valid date (YYYY-MM-DD).'));
        }
        if ($enddateto && !checkDateFormat($enddateto)) {
            form_set_error('nycha_enddateto', t('End Date must be a valid date (YYYY-MM-DD).'));
        }
        if ($enddatefrom && $enddateto && strtotime($enddateto) < strtotime($enddatefrom)) {
            form_set_error('nycha_enddateto', t('Invalid date range for End Date.'));
        }
        // Registered Date:
        if ($appr_date_from && !checkDateFormat($appr_date_from)) {
            form_set_error('nycha_appr_datefrom', t('Approved Date must be a valid date (YYYY-MM-DD).'));
        }
        if ($appr_date_to && !checkDateFormat($appr_date_to)) {
            form_set_error('nycha_appr_dateto', t('Approved Date must be a valid date (YYYY-MM-DD).'));
        }
        if ($appr_date_from && $appr_date_to && strtotime($appr_date_to) < strtotime($appr_date_from)) {
            form_set_error('nycha_appr_dateto', t('Invalid Date range for Approved Date.'));
        }
        //multi-select
        if(!$nycha_multi_select){
            form_set_error('column_select_nycha', t('You must select at least one column.'));
        }

        $form_state['complete form']['agency_hidden']['#value'] = $form_state['values']['nycha_agency'];
    }else {
        $startfrom = $form_state['values']['startdatefrom'];
        $startto = $form_state['values']['startdateto'];
        $enddatefrom = $form_state['values']['enddatefrom'];
        $enddateto = $form_state['values']['enddateto'];
        $regdatefrom = $form_state['values']['regdatefrom'];
        $regdateto = $form_state['values']['regdateto'];
        $recdatefrom = $form_state['values']['recdatefrom'];
        $recdateto = $form_state['values']['recdateto'];
        $currentfrom = $form_state['values']['currentamtfrom'];
        $currentto = $form_state['values']['currentamtto'];
        $activeexpense = $form_state['values']['column_select_expense'];
        $activerevenue = $form_state['values']['column_select_revenue'];
        $all = $form_state['values']['column_select_all'];
        $pending = $form_state['values']['column_select_pending'];
        $pending_all = $form_state['values']['column_select_pending_all'];
        $vendor = $form_state['values']['vendor'];
        $contract_id = $form_state['values']['contractno'];
        $ogeexpense = $form_state['values']['column_select_oge_expense'];
        $entity_contractno = $form_state['values']['entity_contract_number'];

        //Contract Id
        if ($contract_id && strlen($contract_id) > 32) {
            form_set_error('contractno', t('Contract ID must be less than or equal to 32 characters'));
        }

        //Commodity Line
        if (Datasource::OGE == $data_source) {
            $commodity_line = $form_state['values']['commodity_line'];
            if ($commodity_line && !is_numeric($commodity_line)) {
                form_set_error('commodity_line', t('Commodity Line must be a number'));
            }
            if ($entity_contractno && !is_numeric($entity_contractno)) {
                form_set_error('entity_contract_number', t('Entity Contract # must be a number'));
            }
        }

        // Start Date:
        if ($startfrom && !checkDateFormat($startfrom)) {
            form_set_error('startdatefrom', t('Start Date must be a valid date (YYYY-MM-DD).'));
        }
        if ($startto && !checkDateFormat($startto)) {
            form_set_error('startdateto', t('Start Date must be a valid date (YYYY-MM-DD).'));
        }
        if ($startfrom && $startto && strtotime($startto) < strtotime($startfrom)) {
            form_set_error('startdateto', t('Invalid date range for Start Date.'));
        }
        // End Date:
        if ($enddatefrom && !checkDateFormat($enddatefrom)) {
            form_set_error('enddatefrom', t('End Date must be a valid date (YYYY-MM-DD).'));
        }
        if ($enddateto && !checkDateFormat($enddateto)) {
            form_set_error('enddateto', t('End Date must be a valid date (YYYY-MM-DD).'));
        }
        if ($enddatefrom && $enddateto && strtotime($enddateto) < strtotime($enddatefrom)) {
            form_set_error('enddateto', t('Invalid date range for End Date.'));
        }
        // Registered Date:
        if ($regdatefrom && !checkDateFormat($regdatefrom)) {
            form_set_error('regdatefrom', t('Registered Date must be a valid date (YYYY-MM-DD).'));
        }
        if ($regdateto && !checkDateFormat($regdateto)) {
            form_set_error('regdateto', t('Registered Date must be a valid date (YYYY-MM-DD).'));
        }
        if ($regdatefrom && $regdateto && strtotime($regdateto) < strtotime($regdatefrom)) {
            form_set_error('regdateto', t('Invalid Date range for Registered Date.'));
        }
        // Received Date:
        if ($recdatefrom && !checkDateFormat($recdatefrom)) {
            form_set_error('recdatefrom', t('Received Date must be a valid date (YYYY-MM-DD).'));
        }
        if ($recdateto && !checkDateFormat($recdateto)) {
            form_set_error('recdateto', t('Received Date must be a valid date (YYYY-MM-DD).'));
        }
        if ($recdatefrom && $recdateto && strtotime($recdateto) < strtotime($recdatefrom)) {
            form_set_error('recdateto', t('Invalid Date range for Received Date.'));
        }
        // Current Amount:
        if ($currentfrom && !is_numeric($currentfrom)) {
            form_set_error('currentamtfrom', t('Current Amount must be a number.'));
        }
        if ($currentto && !is_numeric($currentto)) {
            form_set_error('currentamtto', t('Current Amount must be a number.'));
        }
        if ($currentfrom && $currentto && $currentto < $currentfrom) {
            form_set_error('currentamtto', t('Invalid range for Current Amount.'));
        }

        if ($form_state['values']['df_contract_status'] != 'pending') {
            if ($form_state['values']['recdatefrom']) {
                form_set_error('recdatefrom', t('Received Date filter is not available on active or registered contracts.'));
            }
            if ($form_state['values']['recdateto']) {
                form_set_error('recdateto', t('Received Date filter is not available on active or registered contracts.'));
            }
        } elseif ($form_state['values']['df_contract_status'] == 'pending') {
            if ($form_state['values']['regdatefrom']) {
                form_set_error('spentto', t('Registered Date filter is not available on pending contracts'));
            }
            if ($form_state['values']['regdateto']) {
                form_set_error('spentto', t('Registered Date filter is not available on pending contracts'));
            }
        }
        //Multi-select Columns
        if($data_source == Datasource::OGE){
            $multi_select_hidden = isset($form_state['input']['column_select_oge_expense']) ? '|' . implode('||', $form_state['input']['column_select_oge_expense']) . '|' : '';
            if(!$ogeexpense){
                form_set_error('column_select_oge_expense', t('You must select at least one column.'));
            }
        }else{
            if($form_state['values']['df_contract_status'] == 'pending'){
                if ($form_state['values']['category'] != 'all') {
                    $multi_select_hidden = isset($form_state['input']['column_select_pending']) ? '|' . implode('||', $form_state['input']['column_select_pending']) . '|' : '';
                    if(!$pending) {
                        form_set_error('column_select_pending', t('You must select at least one column.'));
                    }
                }
                if ($form_state['values']['category'] == 'all') {
                    $multi_select_hidden = isset($form_state['input']['column_select_pending_all']) ? '|' . implode('||', $form_state['input']['column_select_pending_all']) . '|' : '';
                    if(!$pending_all) {
                        form_set_error('column_select_pending_all', t('You must select at least one column.'));
                    }
                }
            }else{
                if($form_state['values']['category'] == 'all'){
                    $multi_select_hidden = isset($form_state['input']['column_select_all']) ? '|' . implode('||', $form_state['input']['column_select_all']) . '|' : '';
                    if(!$all) {
                        form_set_error('column_select_all', t('You must select at least one column.'));
                    }
                }
                if($form_state['values']['category'] == 'expense'){
                    $multi_select_hidden = isset($form_state['input']['column_select_expense']) ? '|' . implode('||', $form_state['input']['column_select_expense']) . '|' : '';
                    if(!$activeexpense) {
                        form_set_error('column_select_expense', t('You must select at least one column.'));
                    }
                }
                if(!$activerevenue && $form_state['values']['category'] == 'revenue'){
                    $multi_select_hidden = isset($form_state['input']['column_select_revenue']) ? '|' . implode('||', $form_state['input']['column_select_revenue']) . '|' : '';
                    if(!$activerevenue) {
                        form_set_error('column_select_revenue', t('You must select at least one column.'));
                    }
                }
            }
        }

        if(in_array($form_state['values']['contract_includes_sub_vendors_id'], array(1, 3, 4))){
            $form_state['values']['sub_vendor_status_in_pip_id'] = 0;
            $form_state['complete form']['sub_vendor_status_in_pip_id']['#value'] = 0;
        }
        $form_state['complete form']['agency_hidden']['#value'] = $form_state['values']['agency'];
    }
    // Vendor:
    if ($vendor) {
        $pattern = "/.*?(\\[.*?\\])/is";
        preg_match($pattern, $vendor, $vmatches);
        if (!$vmatches) {
            try {
                $dataController = data_controller_get_instance();
                switch ($data_source) {
                    case Datasource::NYCHA:
                        $query = "SELECT vendor_name FROM all_agreement_transactions 
                                        WHERE latest_flag = 'Y' 
                                        AND vendor_name ILIKE '" . $vendor . "'";
                        $results = _checkbook_project_execute_sql($query, "main", $data_source);
                        if (!$results[0]) {
                            form_set_error('nycha_vendor', t('Please enter a valid vendor name.'));
                        }
                        break;
                    case Datasource::OGE:
                        $query = "SELECT DISTINCT display_vendor_name 
                                    FROM contracts_detailed_transactions
                                    WHERE status_flag = 'A' 
                                    AND display_vendor_name ILIKE '" . $vendor . "'";
                        $results = _checkbook_project_execute_sql($query, "main", $data_source);
                        if (!$results[0]) {
                            form_set_error('payee_name', t('Please enter a valid vendor name.'));
                        }
                        break;
                    default:
                        $results = $dataController->queryDataset('checkbook:vendor', array('vendor_customer_code'), array('vendor_customer_code' => $vendor));
                        if (!$results[0]) {
                            form_set_error('payee_name', t('Please enter a valid vendor code.'));
                        }
                        break;
                }
            } catch (Exception $e) {
                log_error($e->getMessage());
            }
        }
    }

    //Hidden Field for data-source
    $form_state['complete form']['data_source']['#value'] = $data_source;
    //Hidden Field for multi-select
    $form_state['complete form']['hidden_multiple_value']['#value'] = $multi_select_hidden;
}

/**
 * Filters selected columns to only contain columns configured for this domain snd data_source
 *
 * @param array $selected_columns
 *   selected column array
 * @param string $data_source
 *   current selected data source (i.e. checkbook, checkbook_oge)
 * @param string $response_type
 *   response type (i.e. csv, xml)
 * @param string $status
 *   status
 * @param string $expense_category
 *   expense category
 * @param string $year
 *   year
 * @return array
 */
function checkbook_datafeeds_contracts_filter_selected_columns($selected_columns, $data_source, $response_type, $status, $expense_category, $year)
{
    //Get column configurations
    $domain = 'contracts';
    if($data_source == Datasource::OGE){
        $domain = $domain.'_oge';
    }else if($data_source == Datasource::NYCHA){
        $domain = $domain.'_nycha';
    }
    $domain_config = ConfigUtil::getDomainConfiguration($domain);
    $domain_filter = checkbook_datafeeds_get_configuration_key($domain, $data_source, $status, $expense_category, $year);

    //Get columns which should be included for the current data source
    $columns_included = $domain_config->$domain_filter->dataset->displayConfiguration->$response_type->elementsColumn;
    //Filter out selected columns which are not configured to show for this data source
    $filtered_columns = array_intersect_key($selected_columns, (array)$columns_included);
    return $filtered_columns;
}

/**
 * This function is to filter out columns which are needed in the result set to derive others but will not be displayed
 *
 * @param $response_type
 * @param $data_source
 * @param $status
 * @param $expense_category
 * @param $year
 * @return mixed
 */
function checkbook_datafeeds_contracts_add_required_columns($response_type, $data_source, $status, $expense_category, $year)
{
    //Get column configurations
    $domain = 'contracts';
    if($data_source == Datasource::OGE){
        $domain = $domain.'_oge';
    }else if($data_source == Datasource::NYCHA){
        $domain = $domain.'_nycha';
    }
    $domain_config = ConfigUtil::getDomainConfiguration($domain);
    $domain_filter = checkbook_datafeeds_get_configuration_key($domain, $data_source, $status, $expense_category, $year);

    //Get columns which should be required to get but not display
    $required_columns = $domain_config->$domain->dataset->displayConfiguration->$response_type->required_non_display_columns;

    return $required_columns;
}


/**
 * Returns the configuration key
 * @param $domain
 * @param $data_source
 * @param $status
 * @param $expense_category
 * @param $year
 * @return mixed
 */
function checkbook_datafeeds_get_configuration_key($domain, $data_source, $status, $expense_category, $year){
    $domain_filter = $domain;
    if($data_source == Datasource::NYCHA){
        $domain_filter .=  ($year == 'ALL') ? "_all_years" :"";
    }else{
        $domain_filter .= $status == 'pending' ? '_pending' : '_active';
        $domain_filter .= $status == 'pending' ? '' : '_' . $expense_category;
        $domain_filter .=  ($year == 'All Years' || $year = 'ALL') ? "_all_years" :"";
    }
    return $domain_filter;
}
