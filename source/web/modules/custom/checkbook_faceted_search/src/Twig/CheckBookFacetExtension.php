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

namespace Drupal\checkbook_faceted_search\Twig;

use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;
use Drupal\checkbook_infrastructure_layer\Utilities\FormattingUtilities;
use Drupal\checkbook_project\CommonUtilities\CheckbookDateUtil;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CheckBookFacetExtension extends AbstractExtension
{

  /**
   * Generates a list of all Twig functions that this extension defines.
   *
   * @return array
   *   A key/value array that defines custom Twig functions. The key denotes the
   *   function name used in the tag, e.g.:
   * @code
   *   {{ testfunc() }}
   * @endcode
   *
   *   The value is a standard PHP callback that defines what the function does.
   */
  public function getFunctions()
  {
    return [
      'checkbook_faceted_search_auto' => new TwigFunction('checkbook_faceted_search_auto', [
        $this,
        'checkbook_faceted_search_auto',
      ]),
      'renderFacets' => new TwigFunction('renderFacets', [
        $this,
        'renderFacets',
      ]),
      'checkbook_faceted_search_check' => new TwigFunction('checkbook_faceted_search_check', [
        $this,
        'checkbook_faceted_search_check',
      ]),
      'checkbook_faceted_search_uncheck' => new TwigFunction('checkbook_faceted_search_uncheck', [
        $this,
        'checkbook_faceted_search_uncheck',
      ]),
      'checkbook_faceted_search_js' => new TwigFunction('checkbook_faceted_search_js', [
        $this,
        'checkbook_faceted_search_js',
      ]),
    ];
  }

  function checkbook_faceted_search_auto($disabled, $pages, $node)
  {
    if (!isset($node->widgetConfig->autocomplete) || $node->widgetConfig->autocomplete == TRUE) {
      $output = "
      <div class=\"autocomplete\">
        <input class=\"autocomplete\" " . $disabled . " pages=\"" . $pages . "\" type=\"text\" name=\"" . $node->widgetConfig->urlParameterName .
            "\" autocomplete_param_name=\"" . $node->widgetConfig->autocompleteParamName . "\" nodeid=\"" . $node->nid . "\" id=\"" . $node->widgetConfig->autocompleteID . "\">
        <input type=\"hidden\" id=\"" . $node->widgetConfig->autocompleteID . "_orig\" value=\"\">
      </div>";
    }
    return $output;
  }

  public function renderFacets($facets_render, $selected_facet_results)
  {

    //var_dump($facets_render);
    // Do not display future year in the fiscal year facet
    //ticket NYCCHKBK-13156 - moving the below line out of for loop below, trying to resolve slowness with row expand
    $current_year = CheckbookDateUtil::getMaxDatasourceFiscalYear(Datasource::getDatasourceMapBySolr());

    foreach ($facets_render ?? [] as $facet_name => $facet) {
      // skipping children (sub facets)
      if ($facet->child ?? FALSE) {
        continue;
      }

      $selected_facet_results['contract_status'] = is_array($selected_facet_results['contract_status']) ? $selected_facet_results['contract_status'] : [];
      if (in_array('registered', $selected_facet_results['contract_status']) && strtolower($facet_name) == 'facet_year_array') {
        continue;
      }
      if (!in_array('registered', $selected_facet_results['contract_status']) && strtolower($facet_name) == 'registered_fiscal_year') {
        continue;
      }

      if (strtolower($facet_name) == 'facet_year_array') {
        foreach ($facet->results as $fvalue => $fcount) {
          if ($fvalue > $current_year) {
            unset($facet->results[$fvalue]);
          }
        }
      }

      $span = '';
      $display_facet = 'none';

      // keep domain facet always open
      if ($facet->selected || $facet_name == 'domain') {
        $span = 'open';
        $display_facet = 'block';
      }

      $output = '<div class="filter-content-' . $facet_name . ' filter-content">';
      $output .= '  <div class="filter-title"><span class="' . $span . '">By ' . htmlentities($facet->title) . '</span></div>';
      $output .= '    <div class="facet-content" style="display:' . $display_facet . '" ><div class="progress"></div>';

      if ($facet->autocomplete && sizeof($facet->results) > 9) {
        $autocomplete_id = "autocomplete_" . $facet_name;
        $disabled = '';

        // Autocomplete's result(s) displays and allows to select options that are already selected
        // thereby counting an option twice. Hence, removing duplicates
        $facet->selected = array_unique($facet->selected ? $facet->selected : []);

        //NYCCHKBK-9957 : Disable autocomplete search box if 5 or more options are selected
        $no_of_selected_options = count($facet->selected ? $facet->selected : []);
        if ($no_of_selected_options >= 5) {
          $disabled = " DISABLED=true";
        }

        $output .= '<div class="autocomplete">
              <input id="' . $autocomplete_id . '" ' . $disabled . ' type="text" class="solr_autocomplete" facet="' . $facet_name . '" />
            </div>';
      }

      $output .= '<div class="options">';
      $output .= '<ul class="rows">';

      $index = 0;
      $lowercase_selected = [];
      if ($facet->selected) {
        foreach ($facet->selected as $facet_val) {
          $lowercase_selected[strtolower($facet_val)] = strtolower($facet_val);
        }
      }

      foreach ($facet->results as $facet_value => $count) {
        $facet_result_title = $facet_value;
        if (is_array($count)) {
          [$facet_result_title, $count] = $count;
        }

        $id = 'facet_' . $facet_name . $index;
        $active = '';
        //echo <<<END

        $output .= '<li class="row">
      <label for="{$id}">
        <div class="checkbox">';

        //END;

        if ($lowercase_selected && isset($lowercase_selected[strtolower($facet_value)])) {
          $checked = ' checked="checked" ';
          $active = ' class="active" ';
          $disabled = '';
        } else {
          $checked = '';
          $active = '';
          $disabled = '';
        }

        //Disable unchecked options if 5 or more options from the same category are already selected
        if ((!$checked || $checked == '') && (count($lowercase_selected) >= 5)) {
          $disabled = " DISABLED=true";
        }

        $output .= '<input type="checkbox" onclick="return applySearchFilters();" id="' . $id . '" ' . $checked . $disabled . ' facet="' . $facet_name . '" value="' .
          htmlentities(urlencode($facet_value)) . '" />';
        //echo <<<END

        $output .= '<label for="' . $id . '" />';
        $output .= '</div>';

        //END;

        $output .= '<div class="number"><span' . $active . '>' . number_format($count) . '</span></div>';
        $output .= '<div class="name">' . htmlentities($facet_result_title) . '</div>';
        $output .= '</label>';
        $output .= '</li>';

        if (($checked) && ($children = $facet->sub->$facet_value ?? FALSE)) {
          $sub_index = 0;
          foreach ($children as $child) {
            $sub_facet = $facets_render[$child];
            if (!$sub_facet) {
              continue;
            }

            $sub_facet_name = $child;
            $output .= '<ul class="sub-category">';
            $output .= '<div class="subcat-filter-title">By ' . htmlentities($sub_facet->title) . '</div>';
            //Set Active and Registered Contracts Counts
            if ($sub_facet_name == 'contract_status') {
              unset($sub_facet->results['registered']);
              unset($sub_facet->results['active']);
              if (isset($registered_contracts) &&  $registered_contracts > 0) {
                $sub_facet->results['registered'] = $registered_contracts;
              }
              if (isset($active_contracts) &&  $active_contracts > 0) {
                $sub_facet->results['active'] = $active_contracts;
              }
            }

            foreach ($sub_facet->results as $sub_facet_value => $sub_count) {
              $facet_result_title = $sub_facet_value;
              if (is_array($sub_count)) {
                [$facet_result_title, $sub_count] = $sub_count;
              }

              $id = 'facet_' . $sub_facet_name . $sub_index;
              $active = '';
              $output .= '<li class="row">';
              $output .= "<label for=\"{$id}\">";
              $output .= '<div class="checkbox">';
              $checked = '';
              if ($sub_facet->selected) {
                $checked = in_array($sub_facet_value, $sub_facet->selected);
              }

              $checked = $checked ? ' checked="checked" ' : '';
              $active = $checked ? ' class="active" ' : '';

              if (isset($sub_facet->input) && $sub_facet->input == 'radio') {
                $output .= '<input type="radio" name="' . htmlentities($sub_facet->title) . '" ' . 'id="' . $id . '" ' . $checked . ' facet="' . $sub_facet_name . '" value="' .
                  htmlentities(urlencode($sub_facet_value)) . '" />';
              } else {
                $output .= '<input type="checkbox" onclick="return applySearchFilters();" id="' . $id . '" ' . $checked . ' facet="' . $sub_facet_name . '" value="' .
                  htmlentities(urlencode($sub_facet_value)) . '" />';
              }
              $output .= "<label for=\"{$id}\" />";
              $output .= '</div>';

              $output .= '<div class="number"><span' . $active . '>' . number_format($sub_count) . '</span></div>';
              $output .= '<div class="name">' . htmlentities($facet_result_title) . '</div>';
              $output .= '</label>';

              $output .= '</li>';
              $sub_index++;
            }
            $output .= '</ul>';
          }
        }

        $index++;
      }

      $output .= '</ul></div>';
      $output .= '</div></div>';

      return $output;

    }
  }

  public function checkbook_faceted_search_check($checked, $disableFacet, $autocomplete_id, $filter_name)
  {
    $id_filter_name = str_replace(" ", "_", strtolower($filter_name));
    $final_output = '';
    if (isset($checked) && $checked) {
      foreach ($checked as $row) {
        $output = '';
        if ($row[2] > 0) {
          $row[0] = str_replace('__', '/', $row[0]);
          $row[1] = str_replace('__', '/', $row[1]);
          $row[0] = str_replace('@Q', ':', $row[0]);
          $row[1] = str_replace('@Q', ':', $row[1]);
          $id = $id_filter_name . "_checked_" . $ct;
          if ($filter_name == 'Contract ID') {
            $row_text = '<div class="name">' . $row[1] . '</div>';
          } else {
            $row_text = '<div class="name">' . FormattingUtilities::_break_text_custom2($row[1], 15) . '</div>';
          }
          $output = "
            <div class=\"row\">
              <label for=" . $id . ">
                <div class=\"checkbox\">
             <input class='styled' id='" . $id . "' name= '" . $autocomplete_id . "' type='checkbox' " . $disableFacet . " checked='checked' value='" . urlencode(html_entity_decode($row[0], ENT_QUOTES)) . "' onClick=\"return applyTableListFilters();\" />" .
            " <label for='" . $id . "' />" .
            "</div>" . $row_text .
            "<div class=\"number\"><span class=\"active\">" . number_format($row[2]) . "</span></div>
             </label>
             </div>";
          $ct++;
        }
        $final_output .= $output . "\n";
      }
    }
    return $final_output;
  }

  public function checkbook_faceted_search_uncheck($unchecked, $disabled, $autocomplete_id, $filter_name)
  {
    $id_filter_name = str_replace(" ", "_", strtolower($filter_name));
    $ct = 0;
    if (isset($unchecked) && $unchecked) {
      foreach ($unchecked as $row) {
        if ($row[2] > 0) {
          $row[0] = str_replace('__', '/', $row[0]);
          $row[1] = str_replace('__', '/', $row[1]);
          $row[0] = str_replace('@Q', ':', $row[0]);
          $row[1] = str_replace('@Q', ':', $row[1]);
          $id = $id_filter_name . "_unchecked_" . $ct;
          if ($filter_name == 'Contract ID') {
            $row_text = '<div class="name">' . $row[1] . '</div>';
          } else {
            $row_text = '<div class="name">' . FormattingUtilities::_break_text_custom2($row[1], 15) . '</div>';
          }
          $output = "
              <div class=\"row\">
                <label for=\"" . $id . "\">
                  <div class=\"checkbox\">
            <input class='styled' id='" . $id . "' name= '" . $autocomplete_id . "' type='checkbox' " . $disabled . "value='" . urlencode(html_entity_decode($row[0], ENT_QUOTES)) . "' onClick=\"return applyTableListFilters();\">" .
            " <label for='" . $id . "' />" .
            "</div>" . $row_text .
            "  <div class=\"number\"><span>" . number_format($row[2]) . "</span></div></label></div>";
          $ct++;
        }
        $final_output .= $output . "\n";
      }
    }
    return $final_output;
  }

  /**
   * @param $node
   * @return string
   */
  function checkbook_faceted_search_js($node)
  {
    if ($node->widgetConfig->facetPager == TRUE) {
      $scroll_facet = "var page" . $node->nid . " = 0;
      $(this).next().find('.options').mCustomScrollbar('destroy');
      $(this).next().find('.options').mCustomScrollbar({
          horizontalScroll:false,
          scrollButtons:{
              enable:false
          },
          callbacks:{
              // this function causing disappearing checkbox issue
              onTotalScroll: function (){
          var pages = $(this).next().find('input.autocomplete').attr('pages');
          if(pages == 1) return false;
          if(page" . $node->nid . "  >= pages ) {
            return false;
          }
          page" . $node->nid . "++;

              },
              onTotalScrollBack: function(){
                  var pages = $(this).next().find('input.autocomplete').attr('pages');
                  if(pages == 1) return false;
                  if (page" . $node->nid . " > 0){
                      page" . $node->nid . "--;
                      paginateScroll(" . $node->nid . ", page" . $node->nid . ");
                  }

              }
          },
          theme:'dark'
      });";
    } else {
      $scroll_facet = "
        $(this).next().find('.options').mCustomScrollbar('destroy');
        $(this).next().find('.options').mCustomScrollbar({
            horizontalScroll:false,
            scrollButtons:{
                enable:false
            },
            theme:'dark'
        });
      ";
    }

    $js = "(function($){
    $( document ).ready( function(){
          $('.filter-title').each(function(){
            if($(this).children('span').hasClass('open')){";

    $js .= $scroll_facet . "}});
            $('.filter-title').unbind('click');
          $('.filter-title').click(function(){
                if($(this).next().css('display') == 'block'){
                    $(this).next().css('display','none');
                    $(this).children('span').removeClass('open');
                    $(this).next().find('.options').mCustomScrollbar('destroy');
                } else {
                    $(this).next().css('display','block');
                    $(this).children('span').addClass('open');
                    $(this).next().find('.options').mCustomScrollbar('destroy');
                    $(this).next().find('.options').mCustomScrollbar({
                        horizontalScroll:false,
                        scrollButtons:{
                            enable:false
                        },
                        theme:'dark'
                    });
                }
              });
            });
          })(jQuery);";

    return $js;
  }

} // close class
