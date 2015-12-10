<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 12/9/15
 * Time: 4:55 PM
 */

require_once(realpath(drupal_get_path('module', 'checkbook_project')) .'/customclasses/constants/Constants.php');


class PayrollUtil {

    static function getEmploymentTypeByAmountBasisId($amount_basis_id = null){
        $amount_basis_id = !isset($amount_basis_id) ? _getRequestParamValue("salamttype") : $amount_basis_id;
        $type_of_employment = $amount_basis_id == 1 ? EmploymentType::$SALARIED : EmploymentType::$NON_SALARIED;
        return $type_of_employment;
    }

} 