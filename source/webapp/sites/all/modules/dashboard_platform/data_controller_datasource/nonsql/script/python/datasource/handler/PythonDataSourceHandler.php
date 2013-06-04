<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
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
