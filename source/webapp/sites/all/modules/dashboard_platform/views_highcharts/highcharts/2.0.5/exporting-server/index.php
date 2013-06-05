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

/**
 * This file is part of the exporting module for Highcharts JS.
 * www.highcharts.com/license
 *
 *
 * Available POST variables:
 *
 * $filename string The desired filename without extension
 * $type string The MIME type for export.
 * $width int The pixel width of the exported raster image. The height is calculated.
 * $svg string The SVG source code to convert.
 */


// Options
define ('BATIK_PATH', 'batik-rasterizer.jar');

///////////////////////////////////////////////////////////////////////////////

$type = $_POST['type'];
$svg = (string) $_POST['svg'];
$filename = (string) $_POST['filename'];

// prepare variables
if (!$filename) $filename = 'chart';
if (get_magic_quotes_gpc()) {
    $svg = stripslashes($svg);
}



$tempName = md5(rand());

// allow no other than predefined types
if ($type == 'image/png') {
    $typeString = '-m image/png';
    $ext = 'png';

}
elseif ($type == 'image/jpeg') {
    $typeString = '-m image/jpeg';
    $ext = 'jpg';

}
elseif ($type == 'application/pdf') {
    $typeString = '-m application/pdf';
    $ext = 'pdf';

}
elseif ($type == 'image/svg+xml') {
    $ext = 'svg';
}
$outfile = "temp/$tempName.$ext";

if ($typeString) {

    // size
    if ($_POST['width']) {
        $width = (int)$_POST['width'];
        if ($width) $width = "-w $width";
    }

    // generate the temporary file
    if (!file_put_contents("temp/$tempName.svg", $svg)) {
        die("Couldn't create temporary file. Check that the directory permissions for
            the /temp directory are set to 777.");
    }

    // do the conversion
    $output = shell_exec("java -jar ". BATIK_PATH ." $typeString -d $outfile $width temp/$tempName.svg");

    // catch error
    if (!is_file($outfile) || filesize($outfile) < 10) {
        echo "<pre>$output</pre>";
        echo "Error while converting SVG";
    }

    // stream it
    else {
        header("Content-Disposition: attachment; filename=$filename.$ext");
        header("Content-Type: $type");
        echo file_get_contents($outfile);
    }

    // delete it
    unlink("temp/$tempName.svg");
    unlink($outfile);

// SVG can be streamed directly back
}
else if ($ext == 'svg') {
    header("Content-Disposition: attachment; filename=$filename.$ext");
    header("Content-Type: $type");
    echo $svg;

}
else {
    echo "Invalid type";
}
?>
