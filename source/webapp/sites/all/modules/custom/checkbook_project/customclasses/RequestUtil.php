<?php
/**
* This file is part of the Checkbook NYC financial transparency software.
*
* Copyright (C) 2012, 2013 New York City
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

class RequestUtil{

    //Links for landing pages. This can be avoided if ajax requests can be identified uniquely.
    static $landing_links = array("contracts_landing","contracts_revenue_landing","contracts_pending_rev_landing","contracts_pending_exp_landing");

    /** Checks if the page bottom container is expanded */
    static function isExpandBottomContainer(){
        $referer = $_SERVER['HTTP_REFERER'];

        foreach(self::$landing_links as $landing_link){
          if(preg_match("/$landing_link/i",$referer)){
                return true;
          }
        }

        return false;
    }

    /** Checks if the current URL is opened in a new window */
    static function isNewWindow(){
        $referer = $_SERVER['HTTP_REFERER'];

        return preg_match('/newwindow/i',$referer);
    }

    /** Checks if the current page is Pending Expense Contratcts page */
    static function isPendingExpenseContractPath($path){

        if( preg_match('/^contracts_pending_exp_landing/',$path)){
            return true;
        }

        return false;
    }

    /** Checks if the current page is Pending Revenue Contratcts page */
    static function isPendingRevenueContractPath($path){

        if( preg_match('/^contracts_pending_rev_landing/',$path)){
            return true;
        }

        return false;
    }

    /** Checks if the current page is Active/Registered Expense Contratcs page */
    static function isExpenseContractPath($path){

        if( preg_match('/^contracts_landing/',$path)){
            return true;
        }

        return false;
    }

    /** Checks if the current page is Active/Registered Pending Revenue Contratcs page */
    static function isRevenueContractPath($path){

        if( preg_match('/^contracts_revenue_landing/',$path)){
            return true;
        }

        return false;
    }

    /** Returns the request parameter value from URL */
    static function getRequestKeyValueFromURL($key, $urlPath){      
        $value = NULL;      
        $pathParams = explode('/', $urlPath);
        $index = array_search($key,$pathParams);      
        if($index != FALSE){
          $value =  filter_xss($pathParams[($index+1)]);
        }      
        return $value;
    }

    /** Returns Contracts page title and Breadcrumb */
    static function getContractBreadcrumbTitle(){
      $bottomURL = $_REQUEST['expandBottomContURL'];
      if(preg_match('/magid/',$bottomURL)){
        $magid = RequestUtil::getRequestKeyValueFromURL("magid",$bottomURL);
        $contract_number = _get_master_agreement_details($magid);        
        return $contract_number['contract_number'];
        
      } 
      elseif(preg_match('/agid/',$bottomURL)){
        $agid = RequestUtil::getRequestKeyValueFromURL("agid",$bottomURL);
        $contract_number = _get_child_agreement_details($agid);
        return $contract_number['contract_number'];
      }
      elseif(preg_match('/contract/',$bottomURL) && preg_match('/pending_contract_transactions/',$bottomURL)){
          $contract_number = RequestUtil::getRequestKeyValueFromURL("contract",$bottomURL);
          return $contract_number;

      }
      else if(isset($bottomURL) && preg_match('/transactions/',$bottomURL)){
        $smnid = RequestUtil::getRequestKeyValueFromURL("smnid",$bottomURL);
        $title = NodeSummaryUtil::getInitNodeSummaryTitle($smnid);
        if(preg_match('/^contracts_landing/',current_path()) && preg_match('/status\/A/',current_path())){
          $title = $title . ' Active Expense Contracts Transactions' ;
        }
        elseif(preg_match('/^contracts_landing/',current_path()) && preg_match('/status\/R/',current_path())){
          $title = $title . ' Registered Expense Contracts Transactions' ;
        }
        elseif(preg_match('/^contracts_revenue_landing/',current_path()) && preg_match('/status\/A/',current_path())){
          $title = $title . ' Active Revenue Contracts Transactions' ;
        }
        elseif(preg_match('/^contracts_revenue_landing/',current_path()) && preg_match('/status\/R/',current_path())){
          $title = $title . ' Registered Revenue Contracts Transactions' ;
        }
        elseif(preg_match('/^contracts_pending_exp_landing/',current_path())){
          $title = $title . ' Pending Expense Contracts Transactions' ;
        }
        elseif(preg_match('/^contracts_pending_rev_landing/',current_path())){
          $title = $title . ' Pending Revenue Contracts Transactions' ;
        }    
      }
      elseif(preg_match('/^contracts_landing/',current_path()) && preg_match('/status\/A/',current_path())){
        $title = _get_contracts_breadcrumb_title_drilldown() . ' Active Expense Contracts' ;
      }
      elseif(preg_match('/^contracts_landing/',current_path()) && preg_match('/status\/R/',current_path())){
        $title = _get_contracts_breadcrumb_title_drilldown() . ' Registered Expense Contracts' ;
      }
      elseif(preg_match('/^contracts_revenue_landing/',current_path()) && preg_match('/status\/A/',current_path())){
        $title = _get_contracts_breadcrumb_title_drilldown() . ' Active Revenue Contracts' ;
      }
      elseif(preg_match('/^contracts_revenue_landing/',current_path()) && preg_match('/status\/R/',current_path())){
        $title = _get_contracts_breadcrumb_title_drilldown() . ' Registered Revenue Contracts' ;
      }
      elseif(preg_match('/^contracts_pending_exp_landing/',current_path())){
        $title = _get_pending_contracts_breadcrumb_title_drilldown() . ' Pending Expense Contracts' ;
      }
      elseif(preg_match('/^contracts_pending_rev_landing/',current_path())){
        $title = _get_pending_contracts_breadcrumb_title_drilldown() . ' Pending Revenue Contracts' ;
      }
      else{
        GLOBAL $checkbook_breadcrumb_title;
        $title = $checkbook_breadcrumb_title;
      }
      return html_entity_decode($title);
    }

    /** Returns Payroll page title and Breadcrumb */
    static function getPayrollBreadcrumbTitle(){
      $bottomURL = $_REQUEST['expandBottomContURL'];
      if(isset($bottomURL) && preg_match('/payroll_agencytransactions/',$bottomURL)){        
        $smnid = RequestUtil::getRequestKeyValueFromURL("smnid",$bottomURL);
        if($smnid > 0){
          $title = NodeSummaryUtil::getInitNodeSummaryTitle($smnid) . " " . _checkbook_project_get_name_for_argument("agency_id",RequestUtil::getRequestKeyValueFromURL("agency",$bottomURL))  ;
        }
        else{
          $title =  _checkbook_project_get_name_for_argument("agency_id",RequestUtil::getRequestKeyValueFromURL("agency",$bottomURL)) . ' Payroll Transactions' ;
        }       
      }
      else if(isset($bottomURL) && preg_match('/payroll_employee_transactions/',$bottomURL)){
        $employeeTitle  = (_checkbook_project_get_name_for_argument("employee_id",RequestUtil::getRequestKeyValueFromURL("xyz",$bottomURL)))?_checkbook_project_get_name_for_argument("employee_id",RequestUtil::getRequestKeyValueFromURL("xyz",$bottomURL)):_checkbook_project_get_name_for_argument("employee_id",RequestUtil::getRequestKeyValueFromURL("abc",$bottomURL));
        $title = "Employee Payroll Transactions ( Title: " . $employeeTitle . ' )' ;
      }
      else if(isset($bottomURL) && preg_match('/payroll_nyc_transactions/',$bottomURL)){
        $smnid = RequestUtil::getRequestKeyValueFromURL("smnid",$bottomURL);
        $title = NodeSummaryUtil::getInitNodeSummaryTitle($smnid)  ;        
      }
      else if(isset($bottomURL) && preg_match('/payroll_by_month_nyc_transactions/',$bottomURL)){        
        $customTitle = "NYC Payroll Transactions";
        $monthDetails = CheckbookDateUtil::getMonthDetails(RequestUtil::getRequestKeyValueFromURL("month",$bottomURL));
        if(isset($monthDetails)){
          $customTitle .=  (" in the Month of ". $monthDetails[0]['month_name']) ;
        }
        $title = $customTitle;
      }            
      else if(isset($bottomURL) && preg_match('/payroll_agency_by_month_transactions/',$bottomURL)){
        $agency = _checkbook_project_get_name_for_argument("agency_id",RequestUtil::getRequestKeyValueFromURL("agency",$bottomURL));
        $customTitle = $agency . " Payroll Transactions";
        $monthDetails = CheckbookDateUtil::getMonthDetails(RequestUtil::getRequestKeyValueFromURL("month",$bottomURL));
        if(isset($monthDetails)){
          $customTitle .=  (" in the Month of ". $monthDetails[0]['month_name']) ;
        }
        $title = $customTitle;
      }
      elseif(preg_match('/^payroll\/search\/transactions/',current_path())){
        $title = "Payroll Transactions";
      }
      elseif(preg_match('/^payroll/',current_path()) && preg_match('/agency/',current_path())){        
        $title = _checkbook_project_get_name_for_argument("agency_id",RequestUtil::getRequestKeyValueFromURL("agency",current_path())) . ' Payroll' ;
      }
      elseif(preg_match('/^payroll/',current_path()) && !preg_match('/transactions/',current_path())){
        $title = 'New York City Payroll' ;
      }     
      else{
        GLOBAL $checkbook_breadcrumb_title;
        $title = $checkbook_breadcrumb_title;
      }
      return html_entity_decode($title);
    }

    /** Returns Spending Category based on 'category' value from current path */
    static function getSpendingCategoryName($defaultName = 'Total Spending'){
        $categoryId = _getRequestParamValue('category');
        if(isset($categoryId)){
            $categoryDetails = SpendingUtil::getSpendingCategoryDetails($categoryId,'display_name');
            if(is_array($categoryDetails)){
                return "Total " . $categoryDetails[0]['display_name'];
            }
        }

        return $defaultName;
    }

    /** Returns Spending Transaction page title based on 'category'/'featured dashboard' value from current path */
    static function getSpendingTransactionTitle($defaultName = 'Total Spending'){
        $categoryId = _getRequestParamValue('category');
        $mwbe_title = _checkbook_check_is_mwbe_page() ? 'M/WBE ' : '';
        if(isset($categoryId)){
            $categoryDetails = SpendingUtil::getSpendingCategoryDetails($categoryId,'display_name');
            if(is_array($categoryDetails)){
                return "Total " . $mwbe_title . $categoryDetails[0]['display_name'];
            }
        }

        return $defaultName;
    }

    /**
     * Returns Spending visualization title based on 'category'/'featured dashboard' values from current path.
     *
     * @param array $minority_type_id
     * @param string $defaultTitle
     * @return string
     */
    static function getSpendingVisualizationTitle($minority_type_id, $defaultTitle = 'Total Spending'){
        $title = '';

        if($minority_type_id != null) {
            $minority_category = MappingUtil::getMinorityCategoryById($minority_type_id);
            if(_checkbook_check_is_sub_vendor_ethnicity_page()) {
                $title = $minority_category . ' ';
            }
            else if(_checkbook_check_is_sub_vendor_level_page()) {
                $title = '<p class="sub-chart-title">M/WBE Category: '.$minority_category.'</p>';
            }
        }

        $title .= RequestUtil::getSpendingCategoryName($defaultTitle);
        return html_entity_decode($title);
    }

    /** Returns Spending page title and Breadcrumb */
    static function getSpendingBreadcrumbTitle(){
      $bottomURL = $_REQUEST['expandBottomContURL'];
      if(preg_match('/transactions/',current_path())){
        $title = SpendingUtil::getSpendingTransactionsTitle();
      }
      elseif(isset($bottomURL) && preg_match('/transactions/',$bottomURL)){
        $dtsmnid = RequestUtil::getRequestKeyValueFromURL("dtsmnid",$bottomURL);
        if($dtsmnid > 0){
          $title = NodeSummaryUtil::getInitNodeSummaryTitle($dtsmnid);
        }
        else{          
          $last_id = _getLastRequestParamValue($bottomURL);
          if($last_id['vendor'] > 0){
            $title = _checkbook_project_get_name_for_argument("vendor_id",RequestUtil::getRequestKeyValueFromURL("vendor",$bottomURL)) ;
          }
          elseif($last_id["agency"] > 0){
            $title = _checkbook_project_get_name_for_argument("agency_id",RequestUtil::getRequestKeyValueFromURL("agency",$bottomURL)) ;
          }
          elseif($last_id["expcategory"] > 0){
            $title = _checkbook_project_get_name_for_argument("expenditure_object_id",RequestUtil::getRequestKeyValueFromURL("expcategory",$bottomURL)) ;
          }  
          elseif($last_id["dept"] > 0){
            $title = _checkbook_project_get_name_for_argument("department_id",RequestUtil::getRequestKeyValueFromURL("dept",$bottomURL)) ;
          }
          elseif(preg_match("/\/agid/",$bottomURL)){
            $title = _checkbook_project_get_name_for_argument("agreement_id",RequestUtil::getRequestKeyValueFromURL("agid",$bottomURL)) ;
          }
          elseif(preg_match("/\/magid/",$bottomURL)){
            $title = _checkbook_project_get_name_for_argument("master_agreement_id",RequestUtil::getRequestKeyValueFromURL("magid",$bottomURL)) ;
          }                            
          if(preg_match('/\/category\/1/',$bottomURL)){
            $title = $title. ' Contract Spending Transactions' ;
          }
          elseif(preg_match('/\/category\/2/',$bottomURL)){
            $title = $title. ' Payroll Spending Transactions' ;
          }
          elseif(preg_match('/\/category\/3/',$bottomURL)){
            $title = $title . ' Capital Contracts Spending Transactions' ;
          }
          elseif(preg_match('/\/category\/4/',$bottomURL)){
            $title = $title . ' Others Spending Transactions' ;
          }
          elseif(preg_match('/\/category\/5/',$bottomURL)){
            $title = $title . ' Trust & Agency Spending Transactions' ;
          }
          else{
            $title = $title . ' Total Spending Transactions' ;
          }  
        }       
      }
      elseif(preg_match('/\/category\/1/',current_path())){
        $title = _get_spending_breadcrumb_title_drilldown() . ' Contract Spending' ;
      }
      elseif(preg_match('/\/category\/2/',current_path())){
        $title = _get_spending_breadcrumb_title_drilldown() . ' Payroll Spending' ;
      }
      elseif(preg_match('/\/category\/3/',current_path())){
        $title = _get_spending_breadcrumb_title_drilldown() . ' Capital Contracts Spending' ;
      }
      elseif(preg_match('/\/category\/4/',current_path())){
        $title = _get_spending_breadcrumb_title_drilldown() . ' Others Spending' ;
      }
      elseif(preg_match('/\/category\/5/',current_path())){
        $title = _get_spending_breadcrumb_title_drilldown() . ' Trust & Agency Spending' ;
      }
      else{
        $title = _get_spending_breadcrumb_title_drilldown() . ' Total Spending' ;
      }
      return html_entity_decode($title);
    }

    /** Prepares Payroll bottom navigation filter */
    static  public function preparePayrollBottomNavFilter($page, $category){

        $pathParams = explode('/',drupal_get_path_alias($_GET['q']));
        $url = $page;
        if(strlen($category) > 0){
            $url .= "/category/".$category;
        }
        $url .= _checkbook_append_url_params();
        $allowedFilters =  array("year","calyear","agency","yeartype","vendor");
        for($i=1;$i < count($pathParams);$i++){
            if(in_array($pathParams[$i] ,$allowedFilters) ){
                 $url .= '/'.$pathParams[$i].'/'.$pathParams[($i+1)];
            }
            $i++;
        }
        return $url;

    }

    /** Returns Budget page title and Breadcrumb */
    static function getBudgetBreadcrumbTitle(){
         $bottomURL = $_REQUEST['expandBottomContURL'];
         if((isset($bottomURL) && preg_match('/transactions/',$bottomURL))
             || preg_match('/budget_transactions/',current_path())
             || preg_match('/deppartment_budget_details/',$bottomURL)
             || preg_match('/deppartment_budget_details/',current_path())
             || preg_match('/expense_category_budget_details/',$bottomURL)
             || preg_match('/expense_category_budget_details/',current_path())
             || preg_match('/budget_agency_perecent_difference_transactions/',$bottomURL)
             || preg_match('/budget_agency_perecent_difference_transactions/',current_path())
         ){
           $dtsmnid = (isset($bottomURL)) ? RequestUtil::getRequestKeyValueFromURL("dtsmnid",$bottomURL) : RequestUtil::getRequestKeyValueFromURL("dtsmnid",current_path());
           if($dtsmnid > 0){
             $title = NodeSummaryUtil::getInitNodeSummaryTitle($dtsmnid);
           }
           else{
             $bottomURL = ($bottomURL)? $bottomURL : current_path();
             $last_id = _getLastRequestParamValue($bottomURL);
             if($last_id["agency"] > 0){
               $title = _checkbook_project_get_name_for_argument("agency_id",RequestUtil::getRequestKeyValueFromURL("agency",$bottomURL)) ;
             }
             elseif($last_id["expcategory"] > 0){
               $title = _checkbook_project_get_name_for_argument("object_class_id",RequestUtil::getRequestKeyValueFromURL("expcategory",$bottomURL)) ;
             }
             elseif($last_id["dept"] > 0){
               $title = _checkbook_project_get_name_for_argument("department_id",RequestUtil::getRequestKeyValueFromURL("dept",$bottomURL)) ;
             }
             elseif($last_id["bdgcode"] > 0){
                $title = _checkbook_project_get_name_for_argument("budget_code_id",RequestUtil::getRequestKeyValueFromURL("bdgcode",$bottomURL)) ;
             }
             $title = $title . ' Expense Budget Details';
           }
         }else if(!$bottomURL && preg_match('/^budget\/transactions/',current_path())){
            $title = "Expense Budget Details";
         }
         else{
           $title = _get_budget_breadcrumb_title_drilldown() . ' Exepnse Budget' ;
         }

         return html_entity_decode($title);
    }

    /** Returns Revenue page title and Breadcrumb */
    static function getRevenueBreadcrumbTitle(){
            $bottomURL = $_REQUEST['expandBottomContURL'];
            if((isset($bottomURL) && preg_match('/transactions/',$bottomURL))
                || preg_match('/agency_revenue_by_cross_year_collections_details/',current_path())
                || preg_match('/agency_revenue_by_cross_year_collections_details/',$bottomURL)
                || preg_match('/revenue_category_revenue_by_cross_year_collections_details/',current_path())
                || preg_match('/revenue_category_revenue_by_cross_year_collections_details/',$bottomURL)
                || preg_match('/funding_class_revenue_by_cross_year_collections_details/',current_path())
                || preg_match('/funding_class_revenue_by_cross_year_collections_details/',$bottomURL)
                || preg_match('/revenue_transactions/',current_path())
            ){
              $dtsmnid = (isset($bottomURL)) ? RequestUtil::getRequestKeyValueFromURL("dtsmnid",$bottomURL) : RequestUtil::getRequestKeyValueFromURL("dtsmnid",current_path());
              if($dtsmnid > 0){
                $title = NodeSummaryUtil::getInitNodeSummaryTitle($dtsmnid);
              }
              else{
                $bottomURL = ($bottomURL)? $bottomURL : current_path();
                $last_id = _getLastRequestParamValue($bottomURL);
                if($last_id["agency"] > 0){
                  $title = _checkbook_project_get_name_for_argument("agency_id",RequestUtil::getRequestKeyValueFromURL("agency",$bottomURL)) ;
                }elseif($last_id["revcat"] > 0){
                   $title = _checkbook_project_get_name_for_argument("revenue_category_id",RequestUtil::getRequestKeyValueFromURL("revcat",$bottomURL)) ;
                }
                elseif(isset($last_id["fundsrccode"])){
                   $title = _checkbook_project_get_name_for_argument("funding_class_code",RequestUtil::getRequestKeyValueFromURL("fundsrccode",$bottomURL)) ;
                }
                $title = $title . ' Revenue Details';
              }
            }else if(!$bottomURL && preg_match('/^revenue\/transactions/',current_path())){
               $title = "Revenue Details";
            }
            else{
              $title = _get_budget_breadcrumb_title_drilldown() . ' Revenue' ;
            }

            return html_entity_decode($title);
       }

    static function getRevenueNoRecordsMsg(){
        $bottomURL = $_REQUEST['expandBottomContURL'];
            if((isset($bottomURL) && preg_match('/transactions/',$bottomURL))
                || preg_match('/agency_revenue_by_cross_year_collections_details/',current_path())
                || preg_match('/agency_revenue_by_cross_year_collections_details/',$bottomURL)
                || preg_match('/revenue_category_revenue_by_cross_year_collections_details/',current_path())
                || preg_match('/revenue_category_revenue_by_cross_year_collections_details/',$bottomURL)
                || preg_match('/funding_class_revenue_by_cross_year_collections_details/',current_path())
                || preg_match('/funding_class_revenue_by_cross_year_collections_details/',$bottomURL)
                || preg_match('/revenue_transactions/',current_path())
            ){
              $smnid = (isset($bottomURL)) ? RequestUtil::getRequestKeyValueFromURL("smnid",$bottomURL) : RequestUtil::getRequestKeyValueFromURL("smnid",current_path());
              $dtsmnid = (isset($bottomURL)) ? RequestUtil::getRequestKeyValueFromURL("dtsmnid",$bottomURL) : RequestUtil::getRequestKeyValueFromURL("dtsmnid",current_path());
              if($smnid > 0 || $dtsmnid > 0){
                if($dtsmnid > 0){
                    $title = "There are no records to be displayed.";
                }else{
                    $bottomURL = ($bottomURL)? $bottomURL : current_path();
                    $last_id = _getLastRequestParamValue($bottomURL);
                    if($last_id["agency"] > 0){
                      $title = _checkbook_project_get_name_for_argument("agency_id",RequestUtil::getRequestKeyValueFromURL("agency",$bottomURL)) ;
                    }elseif($last_id["revcat"] > 0){
                       $title = _checkbook_project_get_name_for_argument("revenue_category_id",RequestUtil::getRequestKeyValueFromURL("revcat",$bottomURL)) ;
                    }
                    elseif(isset($last_id["fundsrccode"])){
                       $title = _checkbook_project_get_name_for_argument("funding_class_code",RequestUtil::getRequestKeyValueFromURL("fundsrccode",$bottomURL)) ;
                    }
                    $title = 'There are no records to be displayed for '.$title.'.';
                }
              }
            }
            else{
                  $title = "There are no revenue details.";
            }
            return html_entity_decode($title);
    }
    /** Returns top navigation URL */
    static function getTopNavURL($domain){
        $year = _getRequestParamValue("year");
        if($year == null ){
          $year = _getCurrentYearID();
        }
        switch($domain){
          case "contracts":            
              $path ="contracts_landing/status/A/yeartype/B/year/".$year._checkbook_append_url_params();
              if(_getRequestParamValue("agency") > 0){
                $path =  $path . "/agency/" . _getRequestParamValue("agency")  ;
              }else if(_checkbook_check_isEDCPage()){
                $path =  $path . "/agency/9000";
              }

              if(_getRequestParamValue("vendor") > 0){
                $path =  $path . "/vendor/" . _getRequestParamValue("vendor")  ;
              }
              break;
          case "spending":
              $path ="spending_landing/yeartype/B/year/".$year._checkbook_append_url_params();
              if(_getRequestParamValue("agency") > 0){
                $path =  $path . "/agency/" . _getRequestParamValue("agency")  ;
              }else if(_checkbook_check_isEDCPage()){
                  $path =  $path . "/agency/9000";
              }

              if(_getRequestParamValue("vendor") > 0){
                $path =  $path . "/vendor/" . _getRequestParamValue("vendor")  ;
              }
              break;
          case "payroll":
                if(_getRequestParamValue("agency") > 0){
                  $path ="payroll/". "agency/" . _getRequestParamValue("agency")  . "/yeartype/B/year/".$year ;
                }else{
                  $path ="payroll/yeartype/B/year/".$year;
                }
              break;
          case "budget":
            if(_getRequestParamValue("agency") > 0){
              $path ="budget/yeartype/B/year/".$year . "/agency/" . _getRequestParamValue("agency") ;
            }else{
              $path ="budget/yeartype/B/year/".$year;
            }
            break;
          case "revenue":
            if(_getRequestParamValue("agency") > 0){
              $path ="revenue/yeartype/B/year/".$year . "/agency/" . _getRequestParamValue("agency") ;
            }else{
              $path ="revenue/yeartype/B/year/".$year;
            }
            break;
            
        }

        return $path;
     
    }

    /** Checks if the current page is NYC level*/
    static function isNYCLevelPage(){
      self::isEDCPage();
        $landingPages = array("contracts_landing","contracts_revenue_landing",
                              "contracts_pending_rev_landing","contracts_pending_exp_landing",
                              "spending_landing","payroll","budget","revenue");

        $url = $_GET['q'];
        $urlPath = drupal_get_path_alias($url);
        $pathParams = explode('/', $urlPath);
        if(in_array($pathParams[0],$landingPages)){
            if($pathParams[0] == "payroll" && $pathParams[1] == "search"){
                return false;
            }
            return true;

        }else{
            return false;
        }
    }
    
    static function isEDCPage(){
      $vendor_id = _getRequestParamValue('vendor');
      if($vendor_id != null){
        $vendor = _checkbook_project_querydataset("checkbook:vendor","vendor_customer_code",array("vendor_id"=>$vendor_id)); 
     
        if($vendor[0]['vendor_customer_code'] == "0000776804"){
          return true; 
        }else{
          return false;
        } 
        
      }else{
        return false;
      }
    }
    
    static function getEDCURL(){
      $vendor = _checkbook_project_querydataset("checkbook:vendor","vendor_id",array("vendor_customer_code"=>"0000776804"));
      $url = "contracts_landing/status/A/yeartype/B/year/" . _getCurrentYearID() . "/vendor/" . $vendor[0]['vendor_id'];
      return $url;
    }
    
    static function getSpendingEDCURL(){
      $vendor = _checkbook_project_querydataset("checkbook:vendor","vendor_id",array("vendor_customer_code"=>"0000776804"));
      $url = "spending_landing/yeartype/B/year/" . _getCurrentYearID() . "/vendor/" . $vendor[0]['vendor_id'];
      return $url;
    }
    

    static function getLandingPageUrl($domain){
    	$year = _getCurrentYearID();
    	switch($domain){
    		case "contracts":
    			$path ="contracts_landing/status/A/yeartype/B/year/".$year;
    			break;
    		case "spending":
    			$path ="spending_landing/yeartype/B/year/".$year;
    			break;
    		case "payroll":
    			$path ="payroll/yeartype/B/year/".$year;
    			break;
    		case "budget":
    			$path ="budget/yeartype/B/year/".$year;
    			break;
    		case "revenue":
    			$path ="revenue/yeartype/B/year/".$year;
    			break;
    	}
    
    	return $path;
    }
    
    
    
}
