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

namespace Drupal\checkbook_datafeeds\Common;

use Drupal\checkbook_datafeeds\Budget\BudgetFeedCitywide;
use Drupal\checkbook_datafeeds\Budget\BudgetFeedNycha;
use Drupal\checkbook_datafeeds\Contracts\ContractsFeedCitywide;
use Drupal\checkbook_datafeeds\Contracts\ContractsFeedNycedc;
use Drupal\checkbook_datafeeds\Contracts\ContractsFeedNycha;
use Drupal\checkbook_datafeeds\Payroll\PayrollFeed;
use Drupal\checkbook_datafeeds\Revenue\RevenueFeedCitywide;
use Drupal\checkbook_datafeeds\Revenue\RevenueFeedNycha;
use Drupal\checkbook_datafeeds\Spending\SpendingFeedCitywide;
use Drupal\checkbook_datafeeds\Spending\SpendingFeedNycedc;
use Drupal\checkbook_datafeeds\Spending\SpendingFeedNycha;
use Drupal\checkbook_infrastructure_layer\Constants\Common\CheckbookDomain;
use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;

/**
 * Class FeedFactory
 * @package checkbook_datafeeds
 */
class FeedFactory
{
  private static $class = '';
  /**
   * @param $data_source
   * @param $domain
   * @return SpendingFeed|bool
   */
  public static function getFeed($data_source, $domain)
  {
    switch ($data_source) {
      case Datasource::OGE:
        if  ($domain == CheckbookDomain::$SPENDING){
          self::$class = new SpendingFeedNycedc();
        }
        if  ($domain == CheckbookDomain::$CONTRACTS){
          self::$class = new ContractsFeedNycedc();
        }
        break;
      case Datasource::NYCHA:
        if ($domain == 'budget'){
          self::$class = new BudgetFeedNycha();
        }elseif ($domain == 'revenue'){
          self::$class = new RevenueFeedNycha();
        }elseif ($domain == 'spending'){
          self::$class = new SpendingFeedNycha();
        }elseif ($domain == 'contracts'){
          self::$class = new ContractsFeedNycha();
        }elseif ($domain == 'payroll'){
          self::$class = new PayrollFeed(Datasource::NYCHA);
        }
        break;
      default:
        if ($domain == 'budget'){
          self::$class =  new BudgetFeedCitywide();
        }elseif ($domain == 'revenue'){
          self::$class = new RevenueFeedCitywide();
        }elseif ($domain == 'spending'){
          self::$class =  new SpendingFeedCitywide();
        }elseif ($domain == 'contracts'){
          self::$class =  new ContractsFeedCitywide();
        }elseif ($domain == 'payroll'){
          self::$class =  new PayrollFeed(Datasource::CITYWIDE);
        }
        break;
    }
    return self::$class;
  }
}

