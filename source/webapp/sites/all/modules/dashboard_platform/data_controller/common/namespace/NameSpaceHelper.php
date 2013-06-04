<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




class NameSpaceHelper {

    public static $NAME_SPACE__DEFAULT = 'common';

    private static $SEPARATOR = ':';

    public static function addNameSpace($namespace, $name) {
        return $namespace . self::$SEPARATOR . $name;
    }

    public static function resolveNameSpace($namespace, $alias) {
        list($originalNameSpace, $name) = self::splitAlias($alias);

        return isset($originalNameSpace) ? $alias : self::addNameSpace($namespace, $alias);
    }

    public static function splitAlias($alias) {
        $index = strpos($alias, self::$SEPARATOR);

        return ($index === FALSE) ? array(NULL, $alias) : array(substr($alias, 0, $index), substr($alias, $index + 1));
    }

    public static function findNameSpace($alias) {
        list($namespace, $name) = self::splitAlias($alias);

        return $namespace;
    }

    public static function removeNameSpace($alias) {
        list($namespace, $name) = self::splitAlias($alias);

        return $name;
    }

    public static function getNameSpace($alias) {
        $namespace = self::findNameSpace($alias);
        if (!isset($namespace)) {
            throw new IllegalArgumentException(t('Name space is not defined for the name: @alias', array('@alias' => $alias)));
        }

        return $namespace;
    }

    public static function checkAlias($alias) {
        list($namespace, $name) = NameSpaceHelper::splitAlias($alias);

        StringDataTypeHandler::checkValueAsWord($namespace);
        StringDataTypeHandler::checkValueAsWord($name);
    }
}
