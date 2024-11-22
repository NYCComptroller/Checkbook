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

namespace Drupal\widget_config\Utilities\Trends;

class TrendCsvUtil
{
  public static function getTrendsData($node)
  {

      switch ($node->nid) {

        // DebtCapacityTrendsCSV
        case '355':
          $result = DebtCapacityTrendsCSV::ratiosOutstandingDebtCsv($node);
          break;
        case '406':
          $result = DebtCapacityTrendsCSV::ratiosGeneralBondeDebt($node);
          break;
        case '420':
          $result = DebtCapacityTrendsCSV::pledgedRevCovNyc($node);
          break;
        case '407':
          $result = DebtCapacityTrendsCSV::legalDebtMargin($node);
          break;

        // FinancialTrendsCSV
        case '392':
          $result = FinancialTrendsCSV::changesInNetAssetsCsv($node);
          break;
        case '393':
          $result = FinancialTrendsCSV::fundBalGovtFundsCsv($node);
          break;
        case '394':
          $result = FinancialTrendsCSV::changesInFundBalCsv($node);
          break;
        case '316':
          $result = FinancialTrendsCSV::generalFundRevenueOtherFinSourcesCsv($node);
          break;
        case '347':
          $result = FinancialTrendsCSV::generalFundExpendOtherFinSourcesCsv($node);
          break;
        case '354':
          $result = FinancialTrendsCSV::capitalProjRevByAgencyCsv($node);
          break;
        case '395':
          $result = FinancialTrendsCSV::nycEduConstFundCsv($node);
          break;

        // RevenueCapacityTrendsCSV
        case '398':
          $result = RevenueCapacityTrendsCSV::assesedValAndEstdActValCsv($node);
          break;
        case '399':
          $result = RevenueCapacityTrendsCSV::propertyTaxRatesCsv($node);
          break;
        case '351':
          $result = RevenueCapacityTrendsCSV::propertyTaxLeviesCsv($node);
          break;
        case '400':
          $result = RevenueCapacityTrendsCSV::assessedValAndTaxRateByClassCsv($node);
          break;
        case '360':
          $result = RevenueCapacityTrendsCSV::collectionsCancellationsAbatementsCsv($node);
          break;
        case '401':
          $result = RevenueCapacityTrendsCSV::uncollectedParkingViolationFeeCsv($node);
          break;
        case '404':
          $result = RevenueCapacityTrendsCSV::hudsonYardsInfraCorpCsv($node);
          break;

        // DemographicTrendsCSV
        case '396':
          $result = DemographicTrendsCSV::nycPopulationCsv($node);
          break;
        case '353':
          $result = DemographicTrendsCSV::personalIncomeTaxRevenuesCsv($node);
          break;
        case '358':
          $result = DemographicTrendsCSV::nonAgrEmploymentCsv($node);
          break;
        case '397':
          $result = DemographicTrendsCSV::personsRecPubAsstCsv($node);
          break;
        case '359':
          $result = DemographicTrendsCSV::empStatusOfResidentPopulationCsv($node);
          break;

        // OperationalTrendsCSV
        case '405':
          $result = OperationalTrendsCSV::capAssetsStatsByProgramCsv($node);
          break;
        case '357':
          $result = OperationalTrendsCSV::noOfCityEmployeesCsv($node);
          break;

        default:
          $result = NULL;
          break;
      }

      return $result;
    }

}
