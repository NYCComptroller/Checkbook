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

namespace Drupal\checkbook_smart_search\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides the CheckbookSmartSearchForm block.
 *
 * @Block(
 *   id = "checkbook_smart_search_checkbook_smart_search_form",
 *   admin_label = @Translation("Checkbook Smart Search Form Block")
 * )
 */
class CheckbookSmartSearchForm extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $block['content'] = \Drupal::formBuilder()->getForm('Drupal\checkbook_smart_search\Form\CheckbookSmartSearchForm');
    return $block;
  }


}
