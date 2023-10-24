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


namespace Drupal\checkbook_smart_search\Controller;

require_once(\Drupal::service('extension.list.module')->getPath('checkbook_smart_search') . '/includes/checkbook_smart_search.inc');

use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;
use Drupal\checkbook_project\CommonUtilities\CheckbookDateUtil;
use Drupal\checkbook_solr\CheckbookSolr;
use Drupal\checkbook_solr\CheckbookSolrQuery;
use Drupal\Core\Controller\ControllerBase;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Default controller for the checkbook_smart_search module.
 */
class DefaultController extends ControllerBase {

  /** Returns results for smart search
   * @param string $solr_datasource
   * @return mixed|string
   * @throws Exception
   */
  public function _checkbook_smart_search_get_results(string $solr_datasource = 'citywide') {

    $search_term = \Drupal::request()->query->get('search_term');
    $page = \Drupal::request()->query->get('page');
    $solr_query = new CheckbookSolrQuery($solr_datasource, $search_term, 10, $page ?: 0);

    $datasource_facets = (array) CheckbookSolr::getFacetConfigByDatasource($solr_datasource);

    $selected_facets = $solr_query->getSelectedFacets();

    foreach ($datasource_facets as $facet_name => $facet) {
      if (isset($facet->intervals)) {
        $solr_query->addInterval($facet_name, (array) $facet->intervals);
        continue;
      }
      $solr_query->addFacet($facet_name);
      if (isset($facet->sub) && in_array($facet_name, array_keys($selected_facets))) {
        foreach ($selected_facets[$facet_name] as $facet_value) {
          if ($domain_facets = ($facet->sub->$facet_value) ? $facet->sub->$facet_value : FALSE) {
            $solr_query->addFacets($domain_facets);
          }
        }
      }
    }

    $selected_facets = $solr_query->getSelectedFacets();

    $solr = CheckbookSolr::getInstance($solr_datasource);
    $query = $solr_query->buildQuery();

    //Registered Contracts Count
    $registeredContractsQuery = getRegisteredContractsQuery($query, $selected_facets);
    $registeredContractsCount = 0;
    if ($registeredContractsQuery) {
      $registeredContracts = $solr->request_phps('select/?' . $registeredContractsQuery);
      $registeredContractsCount = $registeredContracts['response']['numFound'];
    }

    //Active Contracts Count
    $activeContractsQuery = getActiveContractsQuery($query, $selected_facets);
    $activeContractsCount = 0;
    if ($activeContractsQuery) {
      $activeContracts = $solr->request_phps('select/?' . $activeContractsQuery);
      $activeContractsCount = $activeContracts['response']['numFound'];
    }

    //Search Results
    $search_results = $solr->request_phps('select/?' . $query);
    _inject_smart_search_drupal_settings($search_results);

    $search_results = _ckbk_ss_sort_results($search_results, $datasource_facets);

    if ($selected_facets) {
      /**
       * Get unchecked facet numbers for selected facets
       */
      $solr_query
        ->setRows(0)
        ->tagFacets($selected_facets);
      $unchecked_results = $solr->request_phps('select/?' . $solr_query->buildQuery());
     /* $unchecked_results = array_merge(
        ($unchecked_results['facet_counts']['facet_fields']) ? $unchecked_results['facet_counts']['facet_fields'] : [],
        ($unchecked_results['facet_counts']['facet_intervals']) ? $unchecked_results['facet_counts']['facet_intervals'] : []
      );*/
      $unchecked_results = array_merge(
        $unchecked_results['facet_counts']['facet_fields'] ?? [],
        $unchecked_results['facet_counts']['facet_intervals'] ?? []
      );

      $unchecked_values = [];

      foreach (array_keys($selected_facets) as $facet) {
        if (!sizeof($unchecked_results) || !isset($unchecked_results[$facet]) || !sizeof($unchecked_results[$facet])) {
          continue;
        }
        if (isset($search_results['facet_counts']['facet_fields'][$facet])) {
          /**
           * removing checked ones
           */
          $unchecked_values = array_diff_key($unchecked_results[$facet], array_flip($selected_facets[$facet]));
          /**
           * For multivalued facets overlap happens, we need to remove unchecked from selected manually
           */
          /*if (($search_results['facet_counts']['facet_fields'][$facet]) ? $search_results['facet_counts']['facet_fields'][$facet] : FALSE) {
            $search_results['facet_counts']['facet_fields'][$facet] = array_intersect_key($search_results['facet_counts']['facet_fields'][$facet], array_flip($selected_facets[$facet]));
          }*/
          if ($search_results['facet_counts']['facet_fields'][$facet] ?? false) {
            $search_results['facet_counts']['facet_fields'][$facet] =
              array_intersect_key($search_results['facet_counts']['facet_fields'][$facet], array_flip($selected_facets[$facet]));
          }
        }
        elseif (isset($search_results['facet_counts']['facet_intervals'][$facet])) {
          // facet.mincount does not work with intervals/ranges in solr
          foreach ($search_results['facet_counts']['facet_intervals'][$facet] as $k => $v) {
            if (!$v) {
              unset($search_results['facet_counts']['facet_intervals'][$facet][$k]);
            }
            $unchecked_values = array_diff_key($unchecked_results[$facet], $search_results['facet_counts']['facet_intervals'][$facet]);
          }
        }

        // for sorted facet values like years DESC.

        /*
         * // Threw errors like: Undefined property: stdClass::$sort_by_key  .
        if ($sort = $datasource_facets[$facet]->sort_by_key ? $datasource_facets[$facet]->sort_by_key : 0) {
          if ($sort > 0) {
            ksort($unchecked_values);
          }
          else {
            krsort($unchecked_values);
          }
        }
        */

        if( isset($datasource_facets)
          && is_object($datasource_facets[$facet])
          && isset($datasource_facets[$facet]->sort_by_key)
        ){
          $sort = '1';
          krsort($unchecked_values);
        } else {
          $sort = '0';
          //ksort($unchecked_values);
          arsort($unchecked_values);
        }

        if (isset($search_results['facet_counts']['facet_fields'][$facet])) {
          $search_results['facet_counts']['facet_fields'][$facet] += ($unchecked_values ?? []);
        }
        elseif (isset($search_results['facet_counts']['facet_intervals'][$facet])) {
          $search_results['facet_counts']['facet_intervals'][$facet] += ($unchecked_values ?? []);
        }
      }
    }
    $search_results = _ckbk_remove_empty_intervals($search_results);
    return [
      '#theme' => 'smart_search_results',
      '#search_results'=> $search_results,
      '#solr_datasource' => $solr_datasource,
      '#selected_facet_results' => $selected_facets,
      '#registered_contracts' => $registeredContractsCount,
      '#active_contracts' => $activeContractsCount,
    ];

  }

  public function _checkbook_smart_search_autocomplete_main_input($solr_datasource = 'citywide') {
    $search_term = htmlspecialchars_decode(\Drupal::request()->query->get('term'), ENT_QUOTES);
    $search_term = strtolower($search_term);

    // NYCCHKBK - 10039 handling smart search text box autocomplete with special characters.
    $autoCompleteSearchTerm = '';
    $terms = explode(' ', $search_term);
    if (count($terms) > 0) {
      foreach ($terms as $value) {
        $autoCompleteSearchTerm .= CheckbookSolrQuery::escape($value) . ' ';
      }
    }
    $search_term = '(.* ' . urldecode(trim($autoCompleteSearchTerm)) . '.*)|(^' . urldecode(trim($autoCompleteSearchTerm)) . '.*)';

    $query = new CheckbookSolrQuery($solr_datasource);
    $autocomplete_categories = (array) CheckbookSolr::getAutocompleteTerms($solr_datasource);

    $query
      ->addTermFields(array_keys($autocomplete_categories))
      ->setTermsRegex($search_term);

    $solr = new CheckbookSolr($solr_datasource);
    $contents = $solr->requestTerms($query);

    $smart_search_url = '/smart_search/' . $solr_datasource;

    $matches_render = [];

    foreach ($contents['terms'] as $term => $matches) {
      foreach ($matches as $match => $count) {
        if ('-' == $match) {
          continue;
        }
        $url = $smart_search_url . "?search_term=*!*{$term}=";
        $matches_render[] = [
          "url" => $url,
          "category" => $autocomplete_categories[$term],
          "label" => $match,
          "value" => urlencode($match),
        ];
      }
    }

    if (!sizeof($matches_render)) {
      $matches_render = [
        "url" => "",
        "label" => '<span>' . "No matches found" . '</span>',
        'value' => 'No matches found',
      ];
    }
    return new JsonResponse($matches_render);
  }

  public function _checkbook_smart_search_autocomplete(string $solr_datasource, string $facet) {
    $data = _checkbook_autocomplete($solr_datasource, $facet);
    $current_year = CheckbookDateUtil::getCurrentDatasourceFiscalYear(Datasource::getCurrent());
    foreach ($data as $key => &$line) {
      $line['value'] = urlencode($line['value']);
      if ($facet == 'facet_year_array' && $line['value'] > $current_year) {
        unset($data[$key]);
      }
    }
    if ($facet == 'facet_year_array') {
      // Sort by year.
      $key_values = array_column($data, 'value');
      array_multisort($key_values, SORT_DESC, $data);
    }
    return new JsonResponse($data);
  }

  public function _checkbook_advanced_search_autocomplete(string $solr_datasource, string $facet) {
    $data = _checkbook_autocomplete($solr_datasource, $facet);
    return new JsonResponse($data);
  }

  public function _checkbook_smart_search_export_form() {
    return [
      '#theme' => 'checkbook_smart_search_export_form',
      '#variables' => [
        '#search_results'=> $search_results,
        '#solr_datasource' => $solr_datasource
      ]
    ];
  }

  public function _checkbook_smart_search_export_download(string $solr_datasource) {
    $domain = ucfirst(\Drupal::request()->query->get('domain'));
    //$response = new Response();
    //$response->setContent(_checkbook_smart_search_export_data($solr_datasource));
    //$response->headers->set("Content-Type", "text/csv");
    //$response->headers->set("Content-Disposition", "attachment; filename={$solr_datasource}{$domain}.csv");
    //$response->headers->set("Pragma", "cache");
    //$response->headers->set("Expires", "-1");
    //$response->send();
    $response =  _checkbook_smart_search_export_data($solr_datasource);
    return $response;
    /*$fileheaders = array(
      'Content-Type' => 'text/csv',
      'Content-Disposition' => "attachment; filename={$solr_datasource}{$domain}.csv",
      'Pragma' => 'cache',
      'Expires' => '-1',
      //'Content-Length' => strlen($data)
    );*/
    //return new Response($data, 200, $fileheaders, true);
    //return $response;
  }

  public function _checkbook_smart_search_ajax_results(string $solr_datasource = 'citywide') {
    $solr_query = new CheckbookSolrQuery($solr_datasource, \Drupal::request()->query->get('search_term'), 10, (\Drupal::request()->query->get('page')) ? \Drupal::request()->query->get('page') : 0);
    $solr = CheckbookSolr::getInstance($solr_datasource);
    $search_results = $solr->request_phps('select/?' . $solr_query->buildQuery());
    $renderable = [
      '#theme' => 'ajax_results',
      '#solr_datasource' => $solr_datasource,
      '#search_results' => $search_results
    ];
    print \Drupal::service('renderer')->render($renderable);
    return new Response();
  }

}
