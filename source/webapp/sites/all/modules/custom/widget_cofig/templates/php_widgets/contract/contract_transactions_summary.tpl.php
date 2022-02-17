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

$output = '';
foreach($node->data as $key=>$value){
    $output .= '<div class="field-label">'. WidgetUtil::getLabel('contract_id') .': </div><div class="field-items">'. $value['contract_id'].'</div>'.
    '<div class="field-label">'. WidgetUtil::getLabel('contract_status') .': </div><div class="field-items">'. $value['status'].'</div>'.
    '<div class="field-label">'. WidgetUtil::getLabel('vendor_name') .': </div><div class="field-items">'. $value['vendor_name'].'</div>'.
    '<div class="field-label">'. WidgetUtil::getLabel('current_amount') .': </div><div class="field-items">'. custom_number_formatter_format($value['current_amount'],2,'$').'</div>'.
    '<div class="field-label">'. WidgetUtil::getLabel('original_amount') .': </div><div class="field-items">'. custom_number_formatter_format($value['original_amount'],2,'$').'</div>'.
    '<div class="field-label">'. WidgetUtil::getLabel('contract_purpose') .': </div><div class="field-items">'. $value['description'].'</div>'.
    '<div class="field-label">'. WidgetUtil::getLabel('start_date') .': </div><div class="field-items">'. $value['start_date'].'</div>'.
    '<div class="field-label">'. WidgetUtil::getLabel('end_date') .': </div><div class="field-items">'. $value['end_date'].'</div>'.
    '<div class="field-label">'. WidgetUtil::getLabel('contract_type') .': </div><div class="field-items">'. $value['agreement_type_name'].'</div>'.
    '<div class="field-label">'. WidgetUtil::getLabel('spent_to_date') .': </div><div class="field-items">'. $value['spent_to_date'].'</div>'.
    '<div class="field-label">'. WidgetUtil::getLabel('recv_date') .': </div><div class="field-items">'. $value['receive_date'].'</div>'.
    '<div class="field-label">'. WidgetUtil::getLabel('agency_name') .': </div><div class="field-items">'. $value['agency_name'].'</div>'.
    '<div class="field-label">'. WidgetUtil::getLabel('voucher_amount') .': </div><div class="field-items">'.'</div>'.
    '<div class="field-label">'. WidgetUtil::getLabel('reg_date') .': </div><div class="field-items">'. $value['registered_date'].'</div>'.
    '<div class="field-label">'. WidgetUtil::getLabel('award_method') .': </div><div class="field-items">'. $value['award_method_name'].'</div>'.
    '<div class="field-label">'. WidgetUtil::getLabel('encumbered_amount') .': </div><div class="field-items">'.'</div>'.
    '<div class="field-label">'. WidgetUtil::getLabel('version_number') .': </div><div class="field-items">'. $value['document_version'].'</div>'.
    '<div class="field-label">'. WidgetUtil::getLabel('apt_pin') .': </div><div class="field-items">'.'</div>'.
    '<div class="field-label">'. WidgetUtil::getLabel('pin') .': </div><div class="field-items">'. $value['pin'] .'</div>'.
    '<div class="field-label">'. WidgetUtil::getLabel('loc_site') .': </div><div class="field-items">'. $value['worksites_name'] .'</div>'.
    '<div class="field-label">'. WidgetUtil::getLabel('sol_per_cont') .': </div><div class="field-items">'. $value['number_solicitation'] .'</div>'.
    '<div class="field-label">'. WidgetUtil::getLabel('fms_doc') .': </div><div class="field-items">'. $value['document_code'] .'</div>'.
    '<div class="field-label">'. WidgetUtil::getLabel('resp_per_sol') .': </div><div class="field-items">'. $value['number_responses'] .'</div>'.
    '<div class="field-label">'. WidgetUtil::getLabel('fms_doc_id') .': </div><div class="field-items">'. $value['parent_contract_id'] .'</div>';

}
print $output;