<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 12/9/15
 * Time: 4:55 PM
 */

require_once(realpath(drupal_get_path('module', 'checkbook_project')) .'/customclasses/constants/Constants.php');


class PayrollUtil {

    static function getEmploymentTypeByAmountBasisId($amount_basis_id){
        $type_of_employment = $amount_basis_id == 1 ? PayrollType::$SALARIED : PayrollType::$NON_SALARIED;
        return $type_of_employment;
    }

    /**
     * Function updates the current payroll facet data, maps amount_basis_id from the database to the Payroll Type
     *
     * @param $node
     * @return array
     */
    static function updatePayrollTypeFacetData($node) {

        $data = array();
        $count = 0;
        foreach($node->data as $row) {
            if($row['payroll_type'] == PayrollType::$SALARIED) {
                $new_row = array(
                    'amount_basis_id_amount_basis_id' => '1',
                    'payroll_type' => $row['payroll_type'],
                    'txcount' => $row['txcount']
                );
                array_push($data, $new_row);
            }
            else {
                $count = $count+$row['txcount'];
            }
        }

        if($count > 0){
            array_push($data, array('amount_basis_id_amount_basis_id' => '2~3',
                'payroll_type' => PayrollType::$NON_SALARIED,
                'txcount' => $count
            ));
        }

        return $data;
    }
} 