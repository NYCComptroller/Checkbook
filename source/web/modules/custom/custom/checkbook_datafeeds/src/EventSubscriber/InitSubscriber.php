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

namespace Drupal\checkbook_datafeeds\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class checkbook_datafeeds implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [KernelEvents::REQUEST => ['onEvent', 0]];
  }

  public function onEvent() {
    require_once(\Drupal::service('extension.list.module')->getPath('checkbook_datafeeds') . "/includes/checkbook_database.inc");
    require_once(\Drupal::service('extension.list.module')->getPath('checkbook_datafeeds') . "/includes/contracts/checkbook_datafeeds_contracts.inc");
    require_once(\Drupal::service('extension.list.module')->getPath('checkbook_datafeeds') . "/includes/payroll/checkbook_datafeeds_payroll.inc");
    require_once(\Drupal::service('extension.list.module')->getPath('checkbook_datafeeds') . "/includes/revenue/spending/checkbook_datafeeds_spending.inc");
  }

}
