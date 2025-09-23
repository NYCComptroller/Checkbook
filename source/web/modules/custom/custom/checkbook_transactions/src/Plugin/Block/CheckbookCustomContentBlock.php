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

namespace Drupal\checkbook_transactions\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use ParseError;

/**
 * Provides a 'Checkbook Custom Content' Block.
 *
 * @Block(
 *   id = "checkbook_custom_content_block",
 *   admin_label = @Translation("New Custom Content"),
 *   category = @Translation("Custom"),
 * )
 */

class CheckbookCustomContentBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [
      '#type' => 'container',
    ];
    if (!empty($this->configuration['title'])) {
      $title_heading_value = (empty($this->configuration['title_heading'])) ? 'h2' : $this->configuration['title_heading'];
      $title_value = $this->configuration['title'];
      $build['title'] = [
        '#type' => 'markup',
        '#markup' =>"<$title_heading_value>$title_value</$title_heading_value>",
        '#cache' => ['contexts' => ['url.path', 'url.query_args']]
      ];
    }
    if (!empty($this->configuration['body'])) {
        $build['body'] = [
          '#type' => 'processed_text',
          '#text' => $this->configuration['body']['value'],
          '#format' => $this->configuration['body']['format'],
        ];
    }
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $formTitle = (empty($this->configuration['title'])) ? '' : $this->configuration['title'];
    $form['title'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#maxlength' => 128,
      '#size' => 60,
      '#default_value' => $formTitle,
    );
    $title_heading_value = (empty($this->configuration['title_heading'])) ? 'h2' : $this->configuration['title_heading'];
    $form['title_heading'] = array(
      '#title' => t('Title Heading'),
      '#type' => 'select',
      '#description' => 'Select the title heading',
      '#default_value' => $title_heading_value,
      '#options' => ['h1' => $this->t('h1'),
                     'h2' => $this->t('h2'),
                     'h3' => $this->t('h3'),
                     'h4' => $this->t('h4'),
                     'h5' => $this->t('h5'),
                     'h6' => $this->t('h6'),
                     'div' => $this->t('div'),
                     'span' => $this->t('span')],
    );
    $bodyFormat = (empty($this->configuration['body']['format'])) ? 'php_code' : $this->configuration['body']['format'];
    $bodyValue = (empty($this->configuration['body']['value'])) ? '' : $this->configuration['body']['value'];
    $form['body'] = array(
      '#type' => 'text_format',
      '#title' => 'Body',
      '#format' => $bodyFormat,
      '#default_value' => $bodyValue,
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['title']  = $form_state->getValue('title');
    $this->configuration['body']  = $form_state->getValue('body');
    $this->configuration['title_heading']  = $form_state->getValue('title_heading');
  }

  public function blockValidate($form, FormStateInterface $form_state) {
    $php_value = $form_state->getValue('body');
    if (!empty($php_value['format']) && $php_value['format'] == 'php_code') {
      //remove the php tags as eval should not have them
      $php_value = str_replace('<?php', '', $php_value);
      $php_value = str_replace('?>', '', $php_value);
      if (!empty($php_value)) {
        try{
          $result = eval($php_value['value']);
        } catch (ParseError $e) {
          $form_state->setErrorByName('body', $this->t('Error in PHP code'));
        }
      }
    }
    return $form_state;
  }
}
