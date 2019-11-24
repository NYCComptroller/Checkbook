<?php
/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (C) 2019 New York City
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

class AgencyList
{

  public function getData(&$node)
  {

    $citywide_agencies = CheckbookGuru::get_instance(Datasource::CITYWIDE)->get_all_parsed('agency_name_id');

//    $edc_agencies = CheckbookGuru::get_instance(Datasource::OGE)->get_all_parsed('agency_name_id');
//    cached
    $edc_agencies = [[
      'title' => 'NEW YORK CITY ECONOMIC DEVELOPMENT CORPORATION',
      'id' => 9000]];
//    $nycha_agencies = CheckbookGuru::get_instance(Datasource::NYCHA)->get_all_parsed('agency_name_id');
//    cached
    $nycha_agencies = [['title' => 'NEW YORK CITY HOUSING AUTHORITY',
      'id' => 162]];

    $node->data = [
      Datasource::CITYWIDE => $citywide_agencies,
      Datasource::OGE => $edc_agencies,
      Datasource::NYCHA => $nycha_agencies,
    ];
  }

}
