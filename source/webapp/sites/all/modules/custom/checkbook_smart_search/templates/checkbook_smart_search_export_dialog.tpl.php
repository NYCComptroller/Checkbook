<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
?>
<?php
$total_records =  check_plain($_REQUEST["totalRecords"]);
$max_records = 200000;
$search_terms = explode('*|*', $_REQUEST['searchTerm']);
$domains = explode("~", $_REQUEST['resultsdomains'] );
/*
$domains = array();
foreach($search_terms as $search_term){
  $terms = explode("=", $search_term);
  if($terms[0] == "domains"){
    $domains = explode("~", $terms[1] );
  }
}
if( count($domains) == 0 ){
  $domains = explode("~", $_REQUEST['resultsdomains'] );
}
*/
$all_domains = false;
if(count($domains) ==  0 ) {
  $checked = "spending"; 
  $all_domains = true;
}else{
  if(in_array("spending",$domains)){
    $checked =  "spending";
  }
  elseif(in_array("payroll",$domains)){
    $checked =  "payroll"; 
  }
  elseif(in_array("contracts",$domains)){
    $checked =  "contracts";
  } 
  elseif(in_array("budget",$domains)){
    $checked =  "budget";
  }
  elseif(in_array("revenue",$domains)){
    $checked =  "revenue";
  } 
}
if($total_records > 0){  
?>
    <div id='dialog'>
        <div id='errorMessages'></div>
        <p>Type of Data<span>*</span>:</p>
        <table>
            <tr>
                <?php 
                  if($checked == 'spending') $checked_flag = "checked";
                  if(!$all_domains && !in_array("spending",$domains)) $disabled = "disabled"
                ?>
                <td><input type='radio' name='domain' <?php print $checked_flag; print $disabled; ?> value='spending'/>&nbsp;Spending</td>
                <?php 
                  $checked_flag = '';
                  $disabled = '';
                  if($checked == 'payroll') $checked_flag = "checked"; 
                  if(!$all_domains && !in_array("payroll",$domains)) $disabled = "disabled"
                ?>
                <td><input type='radio' name='domain' <?php print $checked_flag; print $disabled; ?> value='payroll'/>&nbsp;Payroll</td>
                <?php 
                  $checked_flag = '';
                  $disabled = '';
                  if($checked == 'contracts') $checked_flag = "checked"; 
                  if(!$all_domains && !in_array("contracts",$domains)) $disabled = "disabled"
                ?>
                <td><input type='radio' name='domain' <?php print $checked_flag; print $disabled; ?> value='contracts'/>&nbsp;Contracts</td>
            </tr>
            <tr>
                <?php 
                  $checked_flag = '';
                  $disabled = '';
                  if($checked == 'budget') $checked_flag = "checked"; 
                  if(!$all_domains && !in_array("budget",$domains)) $disabled = "disabled"
                ?>
                <td><input type='radio' name='domain' <?php print $checked_flag; print $disabled; ?> value='budget'/>&nbsp;Budget</td>
                <?php 
                  $checked_flag = '';
                  $disabled = '';
                  if($checked == 'revenue') $checked_flag = "checked"; 
                  if(!$all_domains && !in_array("revenue",$domains)) $disabled = "disabled"
                ?>
                
                <td><input type='radio' name='domain' <?php print $checked_flag; print $disabled; ?> value='revenue'/>&nbsp;Revenue</td>
            </tr>
            
        </table>
    </div>
    <span id="export-message">
        <?php
        if($total_records > $max_records){
            echo "Maximum of ".number_format($max_records)." records available for download from ".number_format($total_records)." available records. The report will be in Comma
            Delimited format. Only one domain can be selected at a time to download the data.";
        }else{
          echo number_format($total_records). " of records are available for download. The report will be in Comma
          Delimited format. Only one domain can be selected at a time to download the data.";
        }
         ?>
    </span>
<?php
}else{
?>
    <div id='dialog'>
        <table class="no-records clearfix">
            <tr>
                <td>No records are available for download.</td>
            </tr>
        </table>
    </div>
<?php
}
?>
