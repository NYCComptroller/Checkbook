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

namespace Drupal\checkbook_transactions\Layout;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Layout\LayoutDefault;
use Drupal\Core\Plugin\PluginFormInterface;

require_once(dirname(__FILE__) . "/../../../checkbook_project/includes/checkbook_database.inc");

/**
 * Alternate class for custom three columns layout.
 */
class TransactionsTowColumns66BricksLayoutClass extends LayoutDefault implements PluginFormInterface {
  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);
    $configuration = $this->getConfiguration();
    $form['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#maxlength' => 128,
      '#size' => 60,
      '#default_value' => $configuration['title'],
    ];
    $form['php'] = array(
      '#type' => 'text_format',
      '#title' => 'Record Exists PHP Code',
      '#format' => 'php_code',
      '#default_value' => $configuration['php']['value'],
      '#allowed_formats' => ['php_code'],
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->configuration['title'] = $form_state->getValue('title');
    $this->configuration['php'] = $form_state->getValue('php');
    parent::submitConfigurationForm($form, $form_state);
  }
}
