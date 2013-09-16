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
echo eval($node->widgetConfig->header);  
$table_rows = array();
$years = array();
foreach( $node->data as $row){	
	$length =  $row['indentation_level'];
	$spaceString = '&nbsp';
	while($length > 0){
		$spaceString .= '&nbsp';
		$length -=1;
	}
	$table_rows[$row['display_order']]['category'] =  $row['category'];
	$table_rows[$row['display_order']]['highlight_yn'] = $row['highlight_yn'];
	$table_rows[$row['display_order']]['indentation_level'] = $row['indentation_level'];
	$table_rows[$row['display_order']]['amount_display_type'] = $row['amount_display_type'];
	$table_rows[$row['display_order']][$row['fiscal_year']]['amount'] = $row['amount'];
	$years[$row['fiscal_year']] = 	$row['fiscal_year'];
}
rsort($years);
?>
<h3><?php echo $node->widgetConfig->table_title; ?></h3>
<a class="trends-export" href="/export/download/trends_cap_assets_stats_by_program_csv?dataUrl=/node/<?php echo $node->nid ?>">Export</a>
<table class="fy-ait">
  <tbody>
  <tr>
    <td width="250">&nbsp;</td>
    <td class="bb">Fiscal Year</td>
  </tr>
  </tbody>
</table>
<table id="table_<?php echo widget_unique_identifier($node) ?>" style='display:none' class="trendsShowOnLoad <?php echo $node->widgetConfig->html_class ?>">
    <?php
    if (isset($node->widgetConfig->caption_column)) {
        echo '<caption>' . $node->data[0][$node->widgetConfig->caption_column] . '</caption>';
    }
    else if (isset($node->widgetConfig->caption)) {
        echo '<caption>' . $node->widgetConfig->caption . '</caption>';
    }
    ?>
    <thead>
        <tr class="second-row">
            <th><br/></th>
            <?php
            foreach ($years as $year)
                echo "<th><div>&nbsp;</div></th><th class='number'><div>" . $year . "</div></th>";
            ?>
            <th>&nbsp;</th>
        </tr>
    </thead>

    <tbody>

    <?php 
    		foreach( $table_rows as $row){
    			$cat_class = "";
    			if( $row['highlight_yn'] == 'Y')
    				$cat_class = "highlight ";    			
    			$cat_class .= "level" . $row['indentation_level']; 
    			$amount_class = "";

                $amount_class .= " number";

                for($i=1;$i < 30;$i++){
                    $find = '('. $i . ')';
                    $replace = '<sup>('.$i .')</sup>';
                    $row['category'] = str_replace($find,$replace, $row['category']);
                }

                $row['category'] = (isset($row['category'])?$row['category']:'&nbsp;');
                
                
                $conditionCategory = $row['category'];
                switch($conditionCategory){
                	case "Correctional/Detention Centers<sup>(2)</sup><sup>(3)</sup>":
                		$conditionCategory = "<div class='" . $cat_class . "'>Correctional/Detention<br><span style='padding-left:10px;'>Centers<sup>(2)</sup><sup>(3)</sup><span></div>";
                		break;
                	case "Intermediate/Junior High Schools<sup>(20)</sup>":
                		$conditionCategory = "<div class='" . $cat_class ."'>Intermediate/Junior High<br><span style='padding-left:10px;'>Schools<sup>(20)</sup><span></div>";
                		break;
                	case "Vehicle Maintenance/Storage Facilities<sup>(13)</sup><sup>(22)</sup><sup>(26)</sup>":
                		$conditionCategory = "<div class='" .$cat_class . "'>Vehicle Maintenance/Storage<br><span style='padding-left:10px;'>Facilities<sup>(13)</sup><sup>(22)</sup><sup>(26)</sup><span></div>";
                		break;
                	case "Parks, Recreation, and Cultural Activities:":
                		$conditionCategory = "<div class='" .$cat_class . "'>Parks, Recreation, and<br>Cultural Activities:</div>";
                		break;
                	case "Vehicle Maintenance/Storage Facilities":
                		$conditionCategory = "<div class='" .$cat_class . "'>Vehicle Maintenance/Storage<br><span style='padding-left:10px;'>Facilities<span></div>";
                		break;
                	default:
                		$conditionCategory = "<div class='" . $cat_class . "' >" . $row['category'] . "</div>";
                		break;
                }
                
			    echo "<tr><td class='text'>" . /*$row['category']*/ $conditionCategory . "</td>";
			    foreach ($years as $year)
			        echo "<td><div>&nbsp;</div></td><td class='" . $amount_class . "'><div>" . (($row[$year]['amount'] > 0)?number_format($row[$year]['amount']):'&nbsp;') . "</div></td>";
			    echo "<td>&nbsp;</td>";
			    echo "</tr>";
    		}
    ?>

    </tbody>
</table>
<div class="footnote">
  <p>(1) In 2004, the Department of Transportation (DOT) took ownership of 16 Waterway and 17 Highway Bridges which were previously owned by the Department of Parks and Recreation.</p>
  <p>(2) These include both active and inactive facilities.</p>
  <p>(3) In 2006, the Department of Correction transferred ownership of the Bronx House of Detention building to Economic Development Corporation under the Department of Small Business Services.</p>
  <p>(4) In 2006, the Fire Department included 4 reserve fireboats for hurricane preparedness.<br/>
  <p>(5) In 2006, Icahn became the Parks Department fifth major stadium. Icahn is located on Randalls Island and serves as a track and field facility.</p>
  <p>(6) Parks fiscal year 2005 acreage count includes a reduction of 92 acres.</p>
  <p>(7) The decrease in transfer stations and increase in piers and bulkheads were due to a reclassification in fiscal year 2007.</p>
  <p>(8) In fiscal year 2007, DOT reclassified one bridge structure to a waterway bridge, and demolished three other bridge structures.</p>
  <p>(9) In fiscal year 2008, DOT added three new highway bridges as follows: Brook Avenue, SI Ferry Pedestrian Bridge and Borough Place-Ramp A. However, it also removed a Footbridge opposite East 77th Street.</p>
  <p>(10) Change resulted from reclassifying pier and bulkheads.</p>
  <p>(11) Decrease due to the sale of the Queens Plaza Garage.</p>
  <p>(12) One fireboat was sunk to contribute to a reef.</p>
  <p>(13) The Sanitation Department demolished its East 73rd Street Facility and reclassified one of its facilities to a vehicle-maintenance facility.</p>
  <p>(14) In fiscal year 2008, the American Museum of National History Section 16-Rose Terrace/Park Garage and the Rose Center Planetarium were classified as Museum Gallery Facilities.</p>
  <p>(15) DOT acquired three new state of the art Ferries in fiscal year 2008.</p>
  <p>(16) The Yankee Stadium pedestrian Bridge was demolished and a new bridge built and owned by the New York Metropolitan Transportation Authority.</p>
  <p>(17) The Fire Department put the Smith Fire Boat back into service in fiscal year 2009.</p>
  <p>(18) The Fire Department added Sunset Park Station in fiscal year 2010.</p>
  <p>(19) The Fire Department added one rapid response boat in fiscal year 2010.</p>
  <p>(20) In fiscal year 2010, we included various other facilities with active enrollment relating to Public, Intermediate and High School. This includes Minischools, transportables, leased space, etc.</p>
  <p>(21) The Sanitation Department advised that North Shore Marine Transfer Station had been demolished for fiscal year 2010.</p>
  <p>(22) In fiscal year 2010, the Sanitation Department added the Queens 14 Garage.</p>
  <p>(23) According to DOT, Aqueduct Racetrack Ramp was transferred to the Port Authority of New York and New Jersey and one tunnel was converted to a single lane one-way (northbound). </p>
  <p>(24) A Police Department Bell 412 Helicopter suffered catastrophic mechanical failure in fiscal year 2011. Litigation is presently underway.</p>
  <p>(25) The Department of Sanitation (DOS) demolished its Hamilton Avenue MTS transfer station to make room for a new one presently under construction.</p>
  <p>(26) In fiscal year 2011, DOS added the Manhattan 7 garage.</p>
  <p>(27) Yankee and Shea Stadia have been demolished. The two new Stadia, Citi Field and Yankee Stadium have leasing agreements in place with the Industrial Development Corporation.</p>
  <p>(28) In FY 2012, we included an Admin building with active enrollment</p>
  <p>(29) As of Fall 2012, CUNY started accepting students at its newest Community College called the New Community College. </p>
  <p>Sources: Various City Agencies</p>
</div>
<?php 
	widget_data_tables_add_js($node);
?>
