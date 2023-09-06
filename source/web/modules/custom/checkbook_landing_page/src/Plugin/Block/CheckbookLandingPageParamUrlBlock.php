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

namespace Drupal\checkbook_landing_page\Plugin\Block;

use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_landing_page\Utilities\LandingPageUtil;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'Checkbook Page by Param URL' Block.
 * will get page if query param is the
 * eg. /budget/yeartype/B/year/124?expandBottomContURL=/budget/transactions/dtsmnid/264/yeartype/B/year/124
 *    if expandBottomContURL is asked for then that url page will be displayed in block
 *
 * @Block(
 *   id = "checkbook_landing_page_by_param_url_block",
 *   admin_label = @Translation("Checkbook Page by Param URL"),
 *   category = @Translation("Custom"),
 * )
 */

class CheckbookLandingPageParamUrlBlock extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
    $return = '';
    $current_route = \Drupal::routeMatch()->getRouteName();
    $queryParamName = (empty($this->configuration['param_name'])) ? 'expandBottomContURL' : $this->configuration['param_name'];

    if (str_contains($current_route, 'layout_builder.')) {
      $param_name_value = (empty($this->configuration['param_name'])) ? 'empty' : $this->configuration['param_name'];
      $return = "Checkbook Page by Param URL block. Param Name is $param_name_value";
    }
    else if (LandingPageUtil::hasQueryParam($queryParamName)) {
      $return = $this->getQueryParamPathReturn($queryParamName);
    }

    return [
      '#markup' => $return,
    ];
  }

  /**
   * @param $queryParamName
   * @return mixed|string
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getQueryParamPathReturn($queryParamName): mixed
  {
    $output = '';
    \Drupal::request()->getSchemeAndHttpHost();
    $queryParamPath = LandingPageUtil::getQueryParam($queryParamName);

    //Budget Transactions pages
    if (str_contains($queryParamPath, '/budget/transactions/budget_transactions')) {
      $output = LandingPageUtil::getTransactionsNodeOutputByNodeId(1088);
    }
    else if (str_contains($queryParamPath, '/expense_category_budget_details/budget/expcategory_details')) {
      $output = LandingPageUtil::getTransactionsNodeOutputByNodeId(1171);
    }
    else if (str_contains($queryParamPath, '/department_budget_details/budget/dept_details')) {
      $output = LandingPageUtil::getTransactionsNodeOutputByNodeId(1172);
    }
    else if (str_contains($queryParamPath, '/budget_agency_perecent_difference_transactions/budget/agency_details')) {
      $output = LandingPageUtil::getTransactionsNodeOutputByNodeId(1170);
    }

    //NYCHA Budget related transactions pages
    if (str_contains($queryParamPath, '/nycha_budget/transactions')) {
      $output = LandingPageUtil::getTransactionsNodeOutputByNodeId(1090);
    } else if (str_contains($queryParamPath, '/nycha_budget/fundsrc_details')) {
      $output = LandingPageUtil::getTransactionsNodeOutputByNodeId(1094);
    } else if (str_contains($queryParamPath, '/nycha_budget/respcenter_details')) {
      $output = LandingPageUtil::getTransactionsNodeOutputByNodeId(1095);
    } else if (str_contains($queryParamPath, '/nycha_budget/program_details')) {
      $output = LandingPageUtil::getTransactionsNodeOutputByNodeId(1096);
    } else if (str_contains($queryParamPath, '/nycha_budget/project_details')) {
      $output = LandingPageUtil::getTransactionsNodeOutputByNodeId(1097);
    } else if (str_contains($queryParamPath, '/nycha_budget/search/transactions')) {
      $output = LandingPageUtil::getTransactionsNodeOutputByNodeId(1123);
    }

    //NYC Revenue transactions pages
    if (str_contains($queryParamPath, '/revenue/transactions/revenue_transactions')) {
      $output = LandingPageUtil::getTransactionsNodeOutputByNodeId(1091);
    } else if (str_contains($queryParamPath, '/revenue/transactions')) {
      $output = LandingPageUtil::getTransactionsNodeOutputByNodeId(1125);
    } else if (str_contains($queryParamPath, '/revenue/agency_details')) {
      $output = LandingPageUtil::getTransactionsNodeOutputByNodeId(1127);
    } else if (str_contains($queryParamPath, '/revenue/revcat_details')) {
      $output = LandingPageUtil::getTransactionsNodeOutputByNodeId(1128);
    } else if (str_contains($queryParamPath, '/revenue/fundsrc_details')) {
      $output = LandingPageUtil::getTransactionsNodeOutputByNodeId(1129);
    }

    //NYCHA Revenue transactions pages
    if (str_contains($queryParamPath, '/nycha_revenue/transactions')) {
      $output = LandingPageUtil::getTransactionsNodeOutputByNodeId(1092);
    } else if (str_contains($queryParamPath, '/nycha_revenue/search/transactions')) {
      $output = LandingPageUtil::getTransactionsNodeOutputByNodeId(1130);
    }

    //NYCHA Spending transactions pages
    if (str_contains($queryParamPath, '/nycha_spending/transactions')) {
      $output = LandingPageUtil::getTransactionsNodeOutputByNodeId(1107);
    }

    //contracts transaction pages
    if (str_contains($queryParamPath, '/contract/transactions/')) {
      $contSatus = RequestUtilities::_getRequestParamValueBottomURL('contstatus');
      $contCategory = RequestUtilities::_getRequestParamValueBottomURL('contcat');
      $isEDCPage = Datasource::isOGE();
      $vendor = RequestUtilities::_getRequestParamValueBottomURL('vendor');
      $mwbe = RequestUtilities::_getRequestParamValueBottomURL('mwbe');
      $subvendor = RequestUtilities::_getRequestParamValueBottomURL('subvendor');
      $dashboard = RequestUtilities::_getRequestParamValueBottomURL('dashboard');
      $doc_type = RequestUtilities::_getRequestParamValueBottomURL('doctype');

      //Citywide Active/Registered Expense Contracts
      if( $contCategory != 'revenue' &&  $contSatus != 'P' && (!$isEDCPage) && !$mwbe && !$subvendor && !$dashboard){
        if(_checkbook_project_recordsExists(939)){
          //return "/contract/transactions/citywide_expense_contracts/$params";
          //if (strpos($queryParamPath, '/contract/transactions/citywide_expense_contracts') !== false) {
            $output = LandingPageUtil::getTransactionsNodeOutputByNodeId(1110);
          //}
        }
      }
      //Citywide Active/Registered Revenue Contracts
      else if( $contCategory == 'revenue' &&  $contSatus != 'P' && !$mwbe){
        if(_checkbook_project_recordsExists(667)){
          //return "/contract/transactions/citywide_revenue_contracts/$params";
         // if (strpos($queryParamPath, '/contract/transactions/citywide_revenue_contracts') !== false) {
            $output = LandingPageUtil::getTransactionsNodeOutputByNodeId(1111);
          //}
        }
      }
      //Citywide Pending Contracts
      else if($contSatus == 'P'){
        if(_checkbook_project_recordsExists(714)){
            $output = LandingPageUtil::getTransactionsNodeOutputByNodeId(1117);
        }
      }
      //EDC Active/Registered Expense Contracts
      else if( $contCategory != 'revenue' &&  $contSatus != 'P' && $isEDCPage){
        if(_checkbook_project_recordsExists(634)){
            $output = LandingPageUtil::getTransactionsNodeOutputByNodeId(1121);
        }
      }
      //MWBE Active/Registered Expense Contracts
      else if( $contCategory != 'revenue' &&  $contSatus != 'P' && (!$isEDCPage) && ($mwbe || $subvendor || $dashboard)){
        if(_checkbook_project_recordsExists(939)){
            $output = LandingPageUtil::getTransactionsNodeOutputByNodeId(1112);
        }
      }
      //MWBE Active/Registered Revenue Contracts
      else if( $contCategory == 'revenue' &&  $contSatus != 'P' && $mwbe){
        if(_checkbook_project_recordsExists(667)){
            $output = LandingPageUtil::getTransactionsNodeOutputByNodeId(1113);
        }
      }
    }



    //contracts transaction pages
    if (str_contains($queryParamPath, '/subcontract/transactions')) {
      $output = LandingPageUtil::getTransactionsNodeOutputByNodeId(1109);
    }
    if (str_contains($queryParamPath, '/contract/spending/transactions')) {
      if ((!preg_match('*dashboard*', $queryParamPath)) && Datasource::isOGE()) {
        $output = LandingPageUtil::getTransactionsNodeOutputByNodeId(1122);
      } else if (preg_match('*dashboard/s*', $queryParamPath) || preg_match('*dashboard/ms*', $queryParamPath)) {
        $output = LandingPageUtil::getTransactionsNodeOutputByNodeId(1119);
      } else {
        $output = LandingPageUtil::getTransactionsNodeOutputByNodeId(1116);
      }
    }
    if (str_contains($queryParamPath, '/contract_details')) {
      $output = LandingPageUtil::getTransactionsNodeOutputByNodeId(1124);
    }
    if (str_contains($queryParamPath, '/pending_contract_transactions')) {
      $output = LandingPageUtil::getTransactionsNodeOutputByNodeId(1173);
    }

    //Contracts search transactions pages
    if (str_contains($queryParamPath, '/contract/search/transactions/concat/expense')) {
      $output = LandingPageUtil::getTransactionsNodeOutputByNodeId(1131);
    }
    if (str_contains($queryParamPath, '/contract/search/transactions/concat/revenue')) {
      $output = LandingPageUtil::getTransactionsNodeOutputByNodeId(1132);
    }
    if (str_contains($queryParamPath, '/contract/search/all/transactions/concat/expense')) {
      $output = LandingPageUtil::getTransactionsNodeOutputByNodeId(1133);
    }
    if (str_contains($queryParamPath, '/contract/search/all/transactions/concat/revenue')) {
      $output = LandingPageUtil::getTransactionsNodeOutputByNodeId(1134);
    }
    if (str_contains($queryParamPath, '/edc_contract/search/transactions')) {
      $output = LandingPageUtil::getTransactionsNodeOutputByNodeId(1135);
    }
    if (str_contains($queryParamPath, '/edc_contract/search/all/transactions')) {
      $output = LandingPageUtil::getTransactionsNodeOutputByNodeId(1136);
    }

    //NYCHA Contracts transactions pages
    if (str_contains($queryParamPath, '/nycha_contracts/transactions')) {
      $output = LandingPageUtil::getTransactionsNodeOutputByNodeId(1114);
    }
    if (str_contains($queryParamPath, '/nycha_contract_details')) {
      $output = LandingPageUtil::getTransactionsNodeOutputByNodeId(1152);
    }

    //Spending transactions pages
    if (str_contains($queryParamPath, '/spending/transactions/')) {
      $isEDCPage = Datasource::isOGE();
      $dashboard = RequestUtilities::get('dashboard');
      //OGE Spending
      if ($isEDCPage) {
        $output = LandingPageUtil::getTransactionsNodeOutputByNodeId(1106);
      } //Subvendor Spending (ss/sp)
      else if($dashboard == 'ss' || $dashboard == 'sp'){
        $output = LandingPageUtil::getTransactionsNodeOutputByNodeId(1105);
      } // Dashboard MS
      else if($dashboard == 'ms') {
        $output = LandingPageUtil::getTransactionsNodeOutputByNodeId(1104);
      }
      //Citywide MWBE Spending Transaction
      else {
        $output = LandingPageUtil::getTransactionsNodeOutputByNodeId(1103);
      }
    }

    //Citywide Nycha Payroll transaction pages
    if (str_contains($queryParamPath, '/payroll/transactions')) {
      $output = LandingPageUtil::getTransactionsNodeOutputByNodeId(1093);
    } else if (str_contains($queryParamPath, '/payroll/payroll_title/transactions')) {
      $output = LandingPageUtil::getTransactionsNodeOutputByNodeId(1098);
    } else if (str_contains($queryParamPath, '/payroll/monthly/transactions')) {
      $output = LandingPageUtil::getTransactionsNodeOutputByNodeId(1099);
    } else if (str_contains($queryParamPath, '/payroll/agencywide/monthly/transactions')) {
      $output = LandingPageUtil::getTransactionsNodeOutputByNodeId(1100);
    } else if (str_contains($queryParamPath, '/payroll/agencywide/transactions')) {
      $output = LandingPageUtil::getTransactionsNodeOutputByNodeId(1101);
    } else if (str_contains($queryParamPath, '/payroll/employee/transactions')) {
      $pathParams = explode('/', $queryParamPath);
      $index = array_search('yeartype',$pathParams);
      if ($index && $pathParams[($index+1)] == 'C') {
        $output = LandingPageUtil::getTransactionsNodeOutputByNodeId(1108);
      } else {
        $output = LandingPageUtil::getTransactionsNodeOutputByNodeId(1102);
      }
    }

    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state): array
  {
    $param_name_value = empty($this->configuration['param_name']) ? '' : $this->configuration['param_name'];
    $form['param_name'] = array(
      '#type' => 'textfield',
      '#attributes' => array(
        'type' => 'number',
      ),
      '#title' => $this->t('Param Name'),
      '#description' => $this->t("Query param name to get page URL"),
      '#maxlength' => 150,
      '#size' => 50,
      '#default_value' => $param_name_value,
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['param_name']  = $form_state->getValue('param_name');
  }
}
