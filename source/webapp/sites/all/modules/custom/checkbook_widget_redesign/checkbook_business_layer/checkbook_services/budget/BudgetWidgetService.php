<?php
/**
 * Created by PhpStorm.
 * User: sgade
 * Date: 02/22/17
 * Time: 2:05 PM
 */

class BudgetWidgetService extends WidgetDataService implements IWidgetService {

    /**
     * Function to allow the client to initialize the data service
     * @return mixed
     */
    public function initializeDataService() {
        return new BudgetDataService();
    }

    public function implementDerivedColumn($column_name,$row) {
        $value = null;
        $legacy_node_id = $this->getLegacyNodeId();
        switch($column_name) {
            case "agency_name_link":
                $column = $row['agency_name'];
                $url = "";
                $value = "<a href='{$url}'>{$column}</a>";
                break;
            case "department_name_link":
                $column = $row['department_name'];
                $url = BudgetUrlService::departmentUrl($row['department_id']);
                $value = "<a href='{$url}'>{$column}</a>";
                break;
            case "committed_budget_link":
               $column = $row['committed'];
               $url = "";
               $value = "<a href='{$url}'>{$column}</a>";
               break;
        }

        if(isset($value)) {
            return $value;
        }
        return $value;
    }

    public function adjustParameters($parameters, $urlPath) {
        //'filter_type' is used for 'Percent Difference' widgets
        if(isset($parameters['filter_type'])){
            $type_filter = array();
            $agency = RequestUtilities::getRequestParamValue('agency');
            $dept = RequestUtilities::getRequestParamValue('dept');
            $expcategory = RequestUtilities::getRequestParamValue('expcategory');

            if($agency || $parameters['filter_type'] == 'A'){ 
                $type_filter[] = 'A';
            }
            if($dept || $parameters['filter_type'] == 'D'){ 
                $type_filter[] = 'D';
            }
            if($expcategory || $parameters['filter_type'] == 'O'){
                $type_filter[] = 'O';
            }        
            $parameters['filter_type'] = implode("", $type_filter);
        }
        
        return $parameters;
    }

    public function getWidgetFooterUrl($parameters) {
        return BudgetUrlService::getFooterUrl($parameters,$this->getLegacyNodeId());
    }
}
