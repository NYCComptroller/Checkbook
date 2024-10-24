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

namespace Drupal\checkbook_services\Contracts;

use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;

class ContractsWidgetVisibilityService {

    const EXPENSE = 'expense';
    const PENDING_EXPENSE = 'pending expense';
    const REVENUE = 'revenue';
    const PENDING_REVENUE = 'pending revenue';

    /**
     * returns the view to be displayed
     * @param string $widget
     * @return string
     */
    public static function getWidgetVisibility(string $widget): ?string {
        $dashboard = RequestUtilities::get('dashboard');
        $category = ContractsParameters::getContractCategory();
        $view = NULL;

        switch($widget){
            case 'departments':
            case 'departments_vendor':
                if (RequestUtilities::get('agency')) {
                    if ($category === self::EXPENSE) {
                        if (RequestUtilities::isEDCPage()) {
                            if(RequestUtilities::get('vendor')) {
                                $view = 'contracts_departments_view';
                            }
                            else {
                                $view = 'oge_contracts_departments_view';
                            }
                        }
                        else {
                            if (($dashboard == NULL || $dashboard == 'mp') && RequestUtilities::get('agency')) {
                                $view = 'contracts_departments_view';
                            }
                        }
                    }
                }
                break;

            case 'contracts_modifications':
                switch($category) {
                    case self::EXPENSE:
                        switch($dashboard) {
                            case "ss":
                            case "sp":
                                $view = 'subcontracts_modifications_view';
                                break;
                            case "ms":
                                $view = 'mwbe_sub_contracts_modifications_view';
                                break;
                            default:
                                $view = RequestUtilities::isEDCPage() ? 'oge_contracts_modifications_view' : 'contracts_modifications_view';
                                break;
                        }
                        break;
                    case self::REVENUE:
                        $view = 'revenue_contracts_modifications_view';
                        break;
                    case self::PENDING_EXPENSE:
                        $view = 'expense_pending_contracts_modifications_view';
                        break;
                    case self::PENDING_REVENUE:
                        $view = 'revenue_pending_contracts_modifications_view';
                        break;
                    default:
                        break;
                }
                break;

            case 'contracts':
                switch($category) {
                    case self::EXPENSE:
                        switch($dashboard) {
                            case "ss":
                            case "sp":
                                $view = 'sub_contracts_view';
                                break;
                            case "ms":
                                $view = 'mwbe_sub_contracts_view';
                                break;
                            default:
                                $view = RequestUtilities::isEDCPage() ? 'oge_contracts_view' : 'contracts_view';
                                break;
                        }
                        break;
                    case self::REVENUE:
                        $view = 'revenue_contracts_view';
                        break;
                    case self::PENDING_EXPENSE:
                        $view = 'expense_pending_contracts_view';
                        break;
                    case self::PENDING_REVENUE:
                        $view = 'revenue_pending_contracts_view';
                        break;
                    default:
                        break;
                }
                break;

            case 'industries':
                if (!RequestUtilities::get('cindustry')) {
                    switch($category) {
                        case self::EXPENSE:
                            switch($dashboard) {
                                case "ss":
                                case "sp":
                                    $view = 'sub_contracts_by_industries_view';
                                    break;
                                case "ms":
                                    $view = 'mwbe_sub_contracts_by_industries_view';
                                    break;
                                default:
                                    $view = RequestUtilities::isEDCPage() ? 'oge_contracts_by_industries_view' : 'contracts_by_industries_view';
                                    break;
                            }
                            break;

                        case self::REVENUE:
                            $view = 'revenue_contracts_by_industries_view';
                            break;

                        case self::PENDING_EXPENSE:
                        case self::PENDING_REVENUE:
                            $view = 'pending_contracts_by_industries_view';
                            break;

                        default:
                            break;
                    }
                }
                break;
            case 'size':
                if (!RequestUtilities::get('csize')) {
                    switch($category) {
                        case self::EXPENSE:
                            switch($dashboard) {
                                case "ss":
                                case "sp":
                                    $view = 'sub_contracts_by_size_view';
                                    break;
                                case "ms":
                                    $view = 'mwbe_sub_contracts_by_size_view';
                                    break;
                                default:
                                    $view = RequestUtilities::isEDCPage() ? 'oge_contracts_by_size_view' : 'contracts_by_size_view';
                                    break;
                            }
                            break;

                        case self::REVENUE:
                            $view = 'revenue_contracts_by_size_view';
                            break;

                        case self::PENDING_EXPENSE:
                        case self::PENDING_REVENUE:
                            $view = 'pending_contracts_by_size_view';
                            break;

                        default:
                            break;
                    }
                }
                break;

            case 'award_methods':
                if (!RequestUtilities::get('awdmethod')) {
                    switch($category) {
                        case self::EXPENSE:
                            switch($dashboard) {
                                case "ss":
                                case "sp":
                                case "ms":
                                    $view = 'subvendor_award_methods_view';
                                    break;
                                default:
                                    $view = RequestUtilities::isEDCPage() ? 'oge_award_methods_view' : 'expense_award_methods_view';
                                    break;
                            }
                            break;

                        case self::REVENUE:
                            $view = 'revenue_award_methods_view';
                            break;

                        case self::PENDING_EXPENSE:
                        case self::PENDING_REVENUE:
                            $view = 'pending_award_methods_view';
                            break;

                        default:
                            break;
                    }
                }
                break;

            case 'master_agreements':
                if (RequestUtilities::isEDCPage()) {
                    $view = 'oge_master_agreements_view';
                }
                else {
                    switch($category) {
                        case self::EXPENSE:
                            switch($dashboard) {
                                case "ss":
                                case "sp":
                                case "ms":
                                    break;
                                default :
                                    $view = 'master_agreements_view';
                                    break;
                            }
                            break;
                        case self::PENDING_EXPENSE:
                            $view = 'pending_master_agreements_view';
                            break;
                        case self::REVENUE:
                        case self::PENDING_REVENUE:
                        default:
                            break;
                    }
                }
                break;

            case 'master_agreement_modifications':
                if (RequestUtilities::isEDCPage()) {
                    $view = 'oge_master_agreement_modifications_view';
                }
                else {
                    switch($category) {
                        case self::EXPENSE:
                            switch($dashboard) {
                                case "ss":
                                case "sp":
                                case "ms":
                                    break;
                                default :
                                    $view = 'master_agreement_modifications_view';
                                    break;
                            }
                            break;

                        case self::PENDING_EXPENSE:
                            $view = 'pending_master_agreement_modifications_view';
                            break;

                        case self::REVENUE:
                        case self::PENDING_REVENUE:
                        default:
                            break;
                    }
                }
                break;

            case 'vendors':
                if(!RequestUtilities::get('vendor')){
                    switch($category) {
                        case self::EXPENSE:
                            switch($dashboard) {
                                case "ss":
                                case "sp":
                                    $view = 'subcontracts_by_prime_vendors_view';
                                    break;
                                case "ms":
                                    $view = 'mwbe_subcontracts_by_prime_vendors_view';
                                    break;
                                case "mp":
                                    $view = 'mwbe_expense_contracts_by_prime_vendors_view';
                                    break;
                                default:
                                    $view = RequestUtilities::isEDCPage() ? 'oge_contracts_by_prime_vendors_view' : 'expense_contracts_by_prime_vendors_view';
                                    break;
                            }
                            break;

                        case self::REVENUE:
                            switch($dashboard) {
                                case "mp":
                                    $view = 'mwbe_revenue_contracts_by_prime_vendors_view';
                                    break;
                                default:
                                    $view = 'revenue_contracts_by_prime_vendors_view';
                                    break;
                            }
                            break;

                        case self::PENDING_EXPENSE:
                        case self::PENDING_REVENUE:
                            switch($dashboard) {
                                case "mp":
                                    $view = 'mwbe_pending_contracts_by_prime_vendors_view';
                                    break;
                                default:
                                    $view = 'pending_contracts_by_prime_vendors_view';
                                    break;
                            }
                            break;

                        default:
                            break;
                    }
                }
                break;

            case 'sub_vendors':
                if (!RequestUtilities::get('subvendor')) {
                    switch($dashboard) {
                        case "ss":
                        case "sp":
                        case "ms":
                            $view = 'contracts_subvendor_view';
                        break;
                        default:
                            $view = NULL;
                            break;
                    }
                }
                break;

            case 'agencies':
                if (!RequestUtilities::get('agency')) {
                    switch($category) {
                        case self::EXPENSE:
                            switch($dashboard) {
                                case "ss":
                                case "sp":
                                    $view = 'subcontracts_by_agencies_view';
                                    break;
                                case "ms":
                                    $view = 'mwbe_subcontracts_by_agencies_view';
                                    break;
                                default:
                                    $view = 'expense_contracts_by_agencies_view';
                                    break;
                            }
                            break;
                        case self::REVENUE:
                            $view = 'revenue_contracts_by_agencies_view';
                            break;
                        case self::PENDING_EXPENSE:
                        case self::PENDING_REVENUE:
                            $view = 'pending_contracts_by_agencies_view';
                            break;
                        default:
                            break;
                    }
                }
                break;

            default:
                $view = NULL;
                break;
        }

        return $view ?: NULL;
    }
}
