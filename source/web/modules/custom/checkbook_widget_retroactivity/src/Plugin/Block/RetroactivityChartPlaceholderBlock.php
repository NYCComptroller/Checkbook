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

namespace Drupal\checkbook_widget_retroactivity\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Retroactivity Chart Placeholder' Block.
 *
 * @Block(
 * id = "retroactivity_chart_placeholder",
 * admin_label = @Translation("Retroactivity Chart Placeholder"),
 * )
 */
class RetroactivityChartPlaceholderBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'retroactivity_chart_placeholder',
      /*
      '#attached' => [
        'library' => [
          'checkbook_widget_retroactivity/retroactivity-form',
          'checkbook_widget_retroactivity/highcharts',
          'checkbook_widget_retroactivity/retroactivity-ajax',
        ],
      ],
      */
    ];
  }
}