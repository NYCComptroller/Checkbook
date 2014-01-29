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
	$table_rows = array();
	$years = array();
	foreach( $node->data as $row){
		$table_rows[$row['display_order']]['category'] = $row['category'];
		$table_rows[$row['display_order']]['highlight_yn'] = $row['highlight_yn'];
		$table_rows[$row['display_order']]['indentation_level'] = $row['indentation_level'];
		$table_rows[$row['display_order']]['amount_display_type'] = $row['amount_display_type'];
		$table_rows[$row['display_order']][$row['fiscal_year']]['amount'] = $row['amount'];
		$years[$row['fiscal_year']] = 	$row['fiscal_year'];
	}
	rsort($years);

	$header = ',,,,,,Fiscal Year,,,,,'."\n";
    foreach ($years as $year){
    	$header .=  "," . $year ;
    }
	echo $header . "\n";

    foreach( $table_rows as $row){
        $rowString = '"'.$row['category'].'"' ;
        if(strpos($row['category'], ':')){
        	$hyphen = "";
        }else{
        	$hyphen = "-";
        }
        foreach ($years as $year){
            $rowString .= ',' . '"'. (($row[$year]['amount'] > 0)?number_format($row[$year]['amount']):$hyphen) .'"';
        }
        echo $rowString . "\n";
   	}

echo "\n"."\n".'"'."(1) In 2004, the Department of Transportation (DOT) took ownership of 16 Waterway and 17 Highway Bridges which were previously owned by the Department of Parks and Recreation.".'"'
     ."\n".'"'."(2) These include both active and inactive facilities.".'"'
     ."\n".'"'."(3) In 2006, the Department of Correction transferred ownership of the Bronx House of Detention building to Economic Development Corporation under the Department of Small Business Services.".'"'
     ."\n".'"'."(4) In 2006, the Fire Department included 4 reserve fireboats for hurricane preparedness.".'"'
     ."\n".'"'."(5) In 2006, Icahn became the Parks Department fifth major stadium. Icahn is located on Randalls Island and serves as a track and field facility.".'"'
     ."\n".'"'."(6) Parks fiscal year 2005 acreage count includes a reduction of 92 acres.".'"'
     ."\n".'"'."(7) The decrease in transfer stations and increase in piers and bulkheads were due to a reclassification in fiscal year 2007.".'"'
     ."\n".'"'."(8) In fiscal year 2007, DOT reclassified one bridge structure to a waterway bridge, and demolished three other bridge structures.".'"'
     ."\n".'"'."(9) In fiscal year 2008, DOT added three new highway bridges as follows: Brook Avenue, SI Ferry Pedestrian Bridge and Borough Place-Ramp A. However, it also removed a Footbridge opposite East 77th Street.".'"'
     ."\n".'"'."(10) Change resulted from reclassifying pier and bulkheads.".'"'
     ."\n".'"'."(11) Decrease due to the sale of the Queens Plaza Garage.".'"'
     ."\n".'"'."(12) One fireboat was sunk to contribute to a reef.".'"'
     ."\n".'"'."(13) The Sanitation Department demolished its East 73rd Street Facility and reclassified one of its facilities to a vehicle-maintenance facility.".'"'
     ."\n".'"'."(14) In fiscal year 2008, the American Museum of National History Section 16 - Rose Terrace/Park Garage and the Rose Center Planetarium were classified as Museum Gallery Facilities.".'"'
     ."\n".'"'."(15) DOT acquired three new state of the art Ferries in fiscal year 2008.".'"'
     ."\n".'"'."(16) The Yankee Stadium pedestrian Bridge was demolished and a new bridge built and owned by the New York Metropolitan Transportation Authority.".'"'
     ."\n".'"'."(17) The Fire Department put the Smith Fire Boat back into service in fiscal year 2009.".'"'
     ."\n".'"'."(18) The Fire Department added Sunset Park Station in fiscal year 2010.".'"'
     ."\n".'"'."(19) The Fire Department added one rapid response boat in fiscal year 2010.".'"'
     ."\n".'"'."(20) In fiscal year 2010, we included various other facilities with active enrollment relating to Public, Intermediate and High School. This includes Minischools, transportables, leased space, etc.".'"'
     ."\n".'"'."(21) The Sanitation Department advised that North Shore Marine Transfer Station had been demolished for fiscal year 2010.".'"'
     ."\n".'"'."(22) In fiscal year 2010, the Sanitation Department added the Queens 14 Garage.".'"'
     ."\n".'"'."(23) According to DOT, Aqueduct Racetrack Ramp was transferred to the Port Authority of New York and New Jersey and one tunnel was converted to a single lane one-way (northbound).".'"'
     ."\n".'"'."(24) A Police Department Bell 412 Helicopter suffered catastrophic mechanical failure in fiscal year 2011. Litigation is presently underway.".'"'
     ."\n".'"'."(25) The Department of Sanitation (DOS) demolished its Hamilton Avenue MTS transfer station to make room for a new one presently under construction.".'"'
     ."\n".'"'."(26) In fiscal year 2011, DOS added the Manhattan 7 garage.".'"'
     ."\n".'"'."(27) Yankee and Shea Stadia have been demolished. The two new Stadia, Citi Field and Yankee Stadium have leasing agreements in place with the Industrial Development Corporation.".'"'
     ."\n".'"'."(28) In FY 2012, we included an Admin building with active enrollment. ".'"'
     ."\n".'"'."(29) As of Fall 2012, CUNY started accepting students at its newest Community College called the New Community College ".'"'
     ."\n"."\n".'"'."Sources: Various City Agencies".'"';


?>


