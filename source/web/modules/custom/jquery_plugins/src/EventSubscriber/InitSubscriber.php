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

namespace Drupal\jquery_plugins\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class InitSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [KernelEvents::REQUEST => ['onEvent', 0]];
  }

  public function onEvent() {

    $build['jquery_plugins']['#attached']['library'][] = 'jquery_plugins/cycle';
    $build['jquery_plugins']['#attached']['library'][] = 'jquery_plugins/easyListSplitter';
    $build['jquery_plugins']['#attached']['library'][] = 'jquery_plugins/jScrollPane';
    $build['jquery_plugins']['#attached']['library'][] = 'jquery_plugins/chosen';
    $build['jquery_plugins']['#attached']['library'][] = 'jquery_plugins/fitvids';
    $build['jquery_plugins']['#attached']['library'][] = 'jquery_plugins/fancyBox';
    $build['jquery_plugins']['#attached']['library'][] = 'jquery_plugins/custom-scrollbar';
    $build['jquery_plugins']['#attached']['library'][] = 'jquery_plugins/sticky';
    $build['jquery_plugins']['#attached']['library'][] = 'jquery_plugins/simplePagination';
    $build['system']['#attached']['library'][] = 'jquery_plugins/ui.dialog';
    $build['system']['#attached']['library'][] = 'jquery_plugins/jquery.cookie';
  }
}
