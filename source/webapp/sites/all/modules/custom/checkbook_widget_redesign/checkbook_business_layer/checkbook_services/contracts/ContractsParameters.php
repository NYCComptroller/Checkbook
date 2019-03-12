<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ContractsParameters{
    /**
     * returns the contract category based on the page URL
     * @return string
     */
      public static function getContractCategory(){
        $urlPath = drupal_get_path_alias($_GET['q']);
        $pathParams = explode('/', $urlPath);
        $category = NULL;
        
        switch($pathParams[2]){
            case 'contracts_landing':
                $category = 'expense';
                break;
            case 'contracts_revenue_landing':
               $category = 'revenue';
               break; 
            case 'contracts_pending_exp_landing':
                $category = 'pending expense';
               break;  
            case 'contracts_pending_rev_landing':
                $category = 'pending revenue';
               break; 
        }
        
        return $category;
    }
}
