<?php

namespace Drupal\checkbook_custom_breadcrumbs\Breadcrumb;

/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (c) 2012 – 2023 New York City.
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

use Drupal\Core\Breadcrumb\Breadcrumb;

/**
 * Breadcrumb class.
 */
class CheckbookBreadcrumb extends Breadcrumb {

  /**
   * Show history on/off.
   *
   * @var bool
   */
  protected $history = FALSE;

  /**
   * Get history status.
   *
   * @return bool
   *   The history.
   */
  public function getHistory() {
    return $this->history;
  }

  /**
   * Set history on/off.
   */
  public function setHistory($status) {
    $this->history = $status;
  }

  /**
   * {@inheritdoc}
   */
  public function setLinks(array $links) {
    $this->links = $links;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function toRenderable() {
    if (empty($this->history)) {
      $build = parent::toRenderable();
    }
    else {
      $build = [
        '#theme' => 'breadcrumb_history',
        '#attached' => [
          'library' => ['checkbook_custom_breadcrumbs/cookie'],
        ],
        '#cache' => [
          'contexts' => $this->cacheContexts,
          'tags' => $this->cacheTags,
          'max-age' => $this->cacheMaxAge,
        ],
      ];
    }
    return $build ?? [];
  }

}
