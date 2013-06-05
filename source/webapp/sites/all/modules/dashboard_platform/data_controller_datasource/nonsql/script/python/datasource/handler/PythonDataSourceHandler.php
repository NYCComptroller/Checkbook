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


class PythonDataSourceHandler extends ScriptDataSourceHandler {

    protected function validateResponse($functionName, DatasetMetaData $dataset, $responseBody) {
        $prefixes = array(
            "<!--",
            "The above is a description of an error in a Python program, formatted",
            "for a Web browser because the 'cgitb' module was enabled.  In case you",
            "are not reading this in a Web browser, here is the original traceback:",
            "Traceback (most recent call last):");

        $suffix = "-->";

        $startIndex = 0;
        // checking prefixes
        foreach ($prefixes as $prefix) {
            $prefixIndex = strpos($responseBody, $prefix, $startIndex);
            if ($prefixIndex === FALSE) {
                return TRUE;
            }
            $startIndex = $prefixIndex + strlen($prefix);
        }
        // checking suffix
        $endIndex = strpos($responseBody, $suffix, $startIndex);
        if ($endIndex === FALSE) {
            return TRUE;
        }

        // error is found. Trying to get message
        $message = substr($responseBody, $startIndex, $endIndex - $startIndex);
        $message = StringHelper::trim($message);
        $message = htmlspecialchars_decode($message);

        throw new IllegalStateException($message);
    }
}
