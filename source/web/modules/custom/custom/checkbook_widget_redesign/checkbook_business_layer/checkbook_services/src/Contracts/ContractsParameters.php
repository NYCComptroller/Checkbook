<?php
/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (c) 2012 – 2023 New York City
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Drupal\checkbook_services\Contracts;

use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;

class ContractsParameters{
    /**
     * returns the contract category based on the page URL
     * @return string
     */
      public static function getContractCategory(){
        $urlPath = RequestUtilities::getCurrentPageUrl();
        $pathParams = explode('/', $urlPath);
        return match ($pathParams[1]) {
          'contracts_landing' => 'expense',
          'contracts_revenue_landing' => 'revenue',
          'contracts_pending_exp_landing' => 'pending expense',
          'contracts_pending_rev_landing' => 'pending revenue',
          default => NULL,
        };
    }
}
