<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class PathHelper {

    public static function simplifyPath($path) {
        $documentRoot = isset($_SERVER['DOCUMENT_ROOT'])
            ? trim(self::useCorrectSlash($_SERVER['DOCUMENT_ROOT']))
            : NULL;
        if (strlen($documentRoot) == 0) {
            $documentRoot = NULL;
        }

        return (isset($documentRoot) && (strpos($path, $documentRoot) === 0)) ? substr($path, strlen($documentRoot)) : $path;
    }

    public static function useCorrectSlash($path) {
        return str_replace(array('/', '\\'), array(DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR), $path);
    }
}
