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

$http_ref = $_SERVER['HTTP_REFERER'];
$current_url = $_GET['q'];

//Advanced Search page should not have static text
$advanced_search_page = preg_match("/nycha_contract\/search\/transactions/",$current_url);
$advanced_search_page = $advanced_search_page || preg_match("/nycha_contract\/all\/transactions/",$current_url);
$advanced_search_page = $advanced_search_page || preg_match("/nycha_contract\/search\/transactions/",$http_ref);
$advanced_search_page = $advanced_search_page || preg_match("/nycha_contract\/all\/transactions/",$http_ref);
if($advanced_search_page) return;

    print '<div class="transactions-total-amount">$'
        . custom_number_formatter_format($node->data[0]['total_current_amount'],2)
        .'<div class="amount-title">Total Current Contract Amount</div></div>';

