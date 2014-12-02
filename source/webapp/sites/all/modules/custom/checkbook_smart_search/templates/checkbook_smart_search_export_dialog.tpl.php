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
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU Affero General Public License for more details.
* 
* You should have received a copy of the GNU Affero General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
?>
<?php
$max_records = 200000;
$search_terms = explode('*|*', $_REQUEST['searchTerm']);
$domains = explode("~", $_REQUEST['resultsdomains'] );
$domain_record_counts =  explode("~", check_plain($_REQUEST["totalRecords"]));

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

$total_records = $domain_records = 0;
foreach($domain_record_counts as $domain_record_count) {
    $domain_count =  explode("|", $domain_record_count);
    if($checked == $domain_count[0]) {
        $domain_records = $domain_count[1];
    }
    $total_records +=  $domain_count[1];
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
        echo "Maximum of ".number_format($max_records)." records available for download from ".number_format($domain_records)." available ".$checked." records. The report will be in Comma
            Delimited format. Only one domain can be selected at a time to download the data.";
        /*if($total_records > $max_records){
            echo "Maximum of ".number_format($max_records)." records available for download from ".number_format($domain_records)." available ".$checked." records. The report will be in Comma
            Delimited format. Only one domain can be selected at a time to download the data.";
        }else{
          echo number_format($domain_records)." ".$checked." records are available for download. The report will be in Comma
          Delimited format. Only one domain can be selected at a time to download the data.";
        }*/
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
