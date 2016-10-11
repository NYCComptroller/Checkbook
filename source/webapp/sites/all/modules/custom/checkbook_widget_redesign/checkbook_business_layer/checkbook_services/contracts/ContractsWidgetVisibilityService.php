<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ContractsWidgetVisibilityService {
    static function getWidgetVisibility($widget) {
        switch($widget){
            case 'departments':
                $view = 'contracts_departments_view';
                break;
            default : 
                //handle the exception when there is no match
                $view = ''; 
        }
        return $view;
    }
}