<?php

namespace Drupal\checkbook_infrastructure_layer\Constants\Contract;

abstract class ContractType {

    const ACTIVE_EXPENSE = "active_expense";
    const REGISTERED_EXPENSE = "registered_expense";
    const ACTIVE_REVENUE = "active_revenue";
    const REGISTERED_REVENUE = "registered_revenue";
    const PENDING_EXPENSE = "pending_expense";
    const PENDING_REVENUE = "pending_revenue";

    public static array $CONTRACTS_LANDING_PAGE_BY_TYPE = array(
      self::ACTIVE_EXPENSE => "contracts_landing", self::REGISTERED_EXPENSE => "contracts_landing",
      self::ACTIVE_REVENUE => "contracts_revenue_landing", self::REGISTERED_REVENUE => "contracts_revenue_landing",
      self::PENDING_EXPENSE=> "contracts_pending_exp_landing", self::PENDING_REVENUE => "contracts_pending_rev_landing"
    );

  /**
   * @return string
   * retruns current contract category and statuss
   */
    public static function getCurrent(): string
    {
        $status = ContractStatus::getCurrent();
        $category = ContractCategory::getCurrent();
        return "{$status}_{$category}";
    }

  /**
   * @return mixed|string
   * returns Contracts Landing Page Path for current Contract Status and Category
   */
    public static function getCurrentContractsLandingPage(): mixed
    {
        return self::$CONTRACTS_LANDING_PAGE_BY_TYPE[self::getCurrent()];
    }
}
