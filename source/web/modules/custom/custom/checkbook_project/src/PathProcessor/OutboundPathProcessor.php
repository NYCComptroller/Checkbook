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

namespace Drupal\checkbook_project;

use Drupal\checkbook_project\CommonUtilities\CheckbookDateUtil;
use Drupal\Core\PathProcessor\OutboundPathProcessorInterface;
use Symfony\Component\HttpFoundation\Request;

class OutboundPathProcessor implements OutboundPathProcessorInterface {

  public function processOutbound($path, &$options = [], Request $request = NULL, BubbleableMetadata $bubbleable_metadata = NULL){

    if (strpos($path, 'admin') === 0) {
      return TRUE;
    }
    if (strpos($path, 'ctools') === 0) {
      return TRUE;
    }
    if (($path == 'spending_landing') || ($path == 'spending/agencies') || ($path == 'spending/vendors') || ($path == 'mwbe_agency_grading') || ($path == 'mwbe_agency_grading/sub_vendor_data') || ($path == 'payroll') || ($path == 'payroll/agency/%')) {

      $path .= "/yeartype/B/year/" . CheckbookDateUtil::getCurrentFiscalYearId();
      return TRUE;
    }
    elseif (($path == 'budget') || ($path == 'revenue')) {
      $path .= "/year/" . CheckbookDateUtil::getCurrentFiscalYearId();
    }
    elseif ($path == 'contracts_landing') {
      $path .= "/status/A/yeartype/B/year/" . CheckbookDateUtil::getCurrentFiscalYearId();
      return TRUE;
    }

  }

}
