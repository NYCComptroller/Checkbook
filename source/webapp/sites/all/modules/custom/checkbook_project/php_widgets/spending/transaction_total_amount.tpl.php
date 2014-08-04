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
?>
<?php

    $categoryId = _getRequestParamValue('category');
    $catname = _checkbook_check_is_mwbe_page() ? RequestUtil::getSpendingCategoryName('Total M/WBE Spending') : RequestUtil::getSpendingCategoryName();
    print '<div class="dollar-amounts"><div class="total-spending-amount">$' . custom_number_formatter_format($node->data[0]['check_amount_sum'],2).'<div class="amount-title">'. ($catname.' Amount') .'</div></div></div>';
