<?php

abstract class BudgetConstants{
    const BASE_YEAR_ID = 112;
     public static function getCurrent() {
        return self::BASE_YEAR_ID;
     }
}

abstract class NychaBudgetConstants{
  const BASE_YEAR_ID = 119;
  public static function getCurrent() {
    return self::BASE_YEAR_ID;
  }
}

abstract class BudgetAgencyPercentWidgetViews {

    const AGENCIES_PERCENT_DIFF = "percent_difference_by_agencies";
    const AGENCIES_PERCENT_DIFF_PREVIOUS_1 = "percent_difference_by_agencies_previous_1";
    const AGENCIES_PERCENT_DIFF_PREVIOUS_2 = "percent_difference_by_agencies_previous_2";

     public static function getCurrent() {
        $base_year = BudgetConstants::getCurrent();
        $year = RequestUtilities::get(UrlParameter::YEAR);

        if($year == $base_year + 1) {
            return self::AGENCIES_PERCENT_DIFF_PREVIOUS_1;
        }
        else if($year == $base_year + 2) {
            return self::AGENCIES_PERCENT_DIFF_PREVIOUS_2;
        }
        else if($year > $base_year) {
            return self::AGENCIES_PERCENT_DIFF;
        }
     }
}

abstract class BudgetDepartmentPercentWidgetViews {
    const DEPT_PERCENT_DIFF = "percent_difference_by_departments";
    const DEPT_PERCENT_DIFF_PREVIOUS_1 = "percent_difference_by_departments_previous_1";
    const DEPT_PERCENT_DIFF_PREVIOUS_2 = "percent_difference_by_departments_previous_2";

     public static function getCurrent() {
        $base_year = BudgetConstants::getCurrent();
        $year = RequestUtilities::get(UrlParameter::YEAR);

        if($year == $base_year + 1) {
            return self::DEPT_PERCENT_DIFF_PREVIOUS_1;
        }
        else if($year == $base_year + 2) {
            return self::DEPT_PERCENT_DIFF_PREVIOUS_2;
        }
        else if($year > $base_year) {
            return self::DEPT_PERCENT_DIFF;
        }
     }
}

abstract class BudgetCatExpensePercentWidgetViews {

    const EXPCAT_PERCENT_DIFF = "percent_difference_by_expense_categories";
    const EXPCAT_PERCENT_DIFF_PREVIOUS_1 = "percent_difference_by_expense_categories_previous_1";
    const EXPCAT_PERCENT_DIFF_PREVIOUS_2 = "percent_difference_by_expense_categories_previous_2";

     public static function getCurrent() {
        $base_year = BudgetConstants::getCurrent();
        $year = RequestUtilities::get(UrlParameter::YEAR);

        if($year == $base_year + 1) {
            return self::EXPCAT_PERCENT_DIFF_PREVIOUS_1;
        }
        else if($year == $base_year + 2) {
            return self::EXPCAT_PERCENT_DIFF_PREVIOUS_2;
        }
        else if($year > $base_year) {
            return self::EXPCAT_PERCENT_DIFF;
        }
     }
}

abstract class NychaBudgetCatExpensePercentWidgetViews {
  const PERCENT_DIFF = "percent_difference_by_expense_categories";
  const PERCENT_DIFF_PREVIOUS_1 = "percent_difference_by_expense_categories_previous_1";
  const PERCENT_DIFF_PREVIOUS_2 = "percent_difference_by_expense_categories_previous_2";

  public static function getCurrent() {
    $base_year = NychaBudgetConstants::getCurrent();;
    $year = RequestUtilities::get(UrlParameter::YEAR);

    if($year == $base_year + 1) {
      return self::PERCENT_DIFF_PREVIOUS_1;
    }
    else if($year == $base_year + 2) {
      return self::PERCENT_DIFF_PREVIOUS_2;
    }
    else if($year > $base_year) {
      return self::PERCENT_DIFF;
    }
  }
}

abstract class NychaBudgetRespCentersWidgetViews {
  const PERCENT_DIFF = "percent_difference_by_resp_center";
  const PERCENT_DIFF_PREVIOUS_1 = "percent_difference_by_resp_center_previous_1";
  const PERCENT_DIFF_PREVIOUS_2 = "percent_difference_by_resp_center_previous_2";

  public static function getCurrent() {
    $base_year = NychaBudgetConstants::getCurrent();;
    $year = RequestUtilities::get(UrlParameter::YEAR);

    if($year == $base_year + 1) {
      return self::PERCENT_DIFF_PREVIOUS_1;
    }
    else if($year == $base_year + 2) {
      return self::PERCENT_DIFF_PREVIOUS_2;
    }
    else if($year > $base_year) {
      return self::PERCENT_DIFF;
    }
  }
}

abstract class NychaBudgetFundingSourcesWidgetViews {
  const PERCENT_DIFF = "percent_difference_by_fundsrc";
  const PERCENT_DIFF_PREVIOUS_1 = "percent_difference_by_fundsrc_previous_1";
  const PERCENT_DIFF_PREVIOUS_2 = "percent_difference_by_fundsrc_previous_2";

  public static function getCurrent() {
    $base_year = NychaBudgetConstants::getCurrent();;
    $year = RequestUtilities::get(UrlParameter::YEAR);

    if($year == $base_year + 1) {
      return self::PERCENT_DIFF_PREVIOUS_1;
    }
    else if($year == $base_year + 2) {
      return self::PERCENT_DIFF_PREVIOUS_2;
    }
    else if($year > $base_year) {
      return self::PERCENT_DIFF;
    }
  }
}

abstract class NychaBudgetProgramsWidgetViews {
  const PERCENT_DIFF = "percent_difference_by_programs";
  const PERCENT_DIFF_PREVIOUS_1 = "percent_difference_by_programs_previous_1";
  const PERCENT_DIFF_PREVIOUS_2 = "percent_difference_by_programs_previous_2";

  public static function getCurrent() {
    $base_year = NychaBudgetConstants::getCurrent();;
    $year = RequestUtilities::get(UrlParameter::YEAR);

    if($year == $base_year + 1) {
      return self::PERCENT_DIFF_PREVIOUS_1;
    }
    else if($year == $base_year + 2) {
      return self::PERCENT_DIFF_PREVIOUS_2;
    }
    else if($year > $base_year) {
      return self::PERCENT_DIFF;
    }
  }
}

abstract class NychaBudgetProjectsViews {
  const PERCENT_DIFF = "percent_difference_by_projects";
  const PERCENT_DIFF_PREVIOUS_1 = "percent_difference_by_projects_previous_1";
  const PERCENT_DIFF_PREVIOUS_2 = "percent_difference_by_projects_previous_2";

  public static function getCurrent() {
    $base_year = NychaBudgetConstants::getCurrent();;
    $year = RequestUtilities::get(UrlParameter::YEAR);

    if($year == $base_year + 1) {
      return self::PERCENT_DIFF_PREVIOUS_1;
    }
    else if($year == $base_year + 2) {
      return self::PERCENT_DIFF_PREVIOUS_2;
    }
    else if($year > $base_year) {
      return self::PERCENT_DIFF;
    }
  }
}

