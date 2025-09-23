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

namespace Drupal\checkbook_smart_search\Form;

use Drupal;
use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class CheckbookSmartSearchForm extends FormBase {

  function getFormId() {
    return 'checkbook_smart_search_form';
  }

  /**
   * Returns the smart search form
   * @param $form
   * @param $form_state
   * @return mixed
   */
  function buildForm(array $form, FormStateInterface $form_state) {

    Drupal::moduleHandler()->loadInclude('checkbook_smart_search', 'inc', 'includes/checkbook_smart_search');
    $solr_datasource = Datasource::getCurrentSolrDatasource();

    $form['search_box'] = [
      '#type' => 'textfield',
      '#size' => 30,
      '#maxlength' => 100,
    ];
    $form['domain'] = [
      '#type' => 'hidden',
      '#value' => $solr_datasource,
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => t('Search'),
      '#submit' => ['_checkbook_smart_search_submit']
    ];

    $form['#theme'] = 'smart_search_form';
    $form['#cache']['contexts'] = ['url.path', 'url.query_args'];

    return $form;
  }

  function submitForm(array &$form, FormStateInterface $form_state) {

  }

}
