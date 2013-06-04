<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class ReferencePathHelper {

    private static $SEPARATOR_REFERENCE = '@';
    private static $SEPARATOR_REFERENCE_PATH = '/';

    public static function assembleReference($resource, $name) {
        if (!isset($resource) && !isset($name)) {
            throw new IllegalArgumentException(t('Undefined resource and name for reference assembling'));
        }

        return (isset($name) ? $name : '') . (isset($resource) ? self::$SEPARATOR_REFERENCE . $resource : '');
    }

    public static function splitReference($reference) {
        $parts = NULL;

        $references = self::splitReferencePath($reference);
        foreach ($references as $reference) {
            $index = strpos($reference, self::$SEPARATOR_REFERENCE);

            if ($index === FALSE) {
                $parts[] = NULL;
                $parts[] = $reference;
            }
            else {
                $resource = trim(substr($reference, $index + 1));
                if (strlen($resource) == 0) {
                    $resource = NULL;
                }

                $name = trim(substr($reference, 0, $index));
                if (strlen($name) == 0) {
                    $name = NULL;
                }

                $parts[] = $resource;
                $parts[] = $name;
            }
        }

        return $parts;
    }

    public static function assembleSplitReferenceParts(array $parts) {
        $references = NULL;

        while (TRUE) {
            $resource = array_shift($parts);
            $name = array_shift($parts);
            if (!isset($resource) && !isset($name)) {
                break;
            }

            $references[] = self::assembleReference($resource, $name);
        }

        return isset($references) ? self::assembleReferencePath($references) : NULL;
    }

    public static function assembleReferencePath(array $references) {
        $referencePath = '';

        foreach ($references as $reference) {
            if (!isset($reference)) {
                continue;
            }

            $reference = trim($reference);
            if (strlen($reference) == 0) {
                continue;
            }

            if (strlen($referencePath) > 0) {
                $referencePath .= self::$SEPARATOR_REFERENCE_PATH;
            }

            $referencePath .= $reference;
        }

        if (strlen($referencePath) == 0) {
            throw new IllegalArgumentException(t('Assembled reference path is empty'));
        }

        return $referencePath;
    }

    public static function splitReferencePath($reference) {
        return explode(self::$SEPARATOR_REFERENCE_PATH, $reference);
    }

    public static function generateDatabaseColumnName($reference) {
        $parts = self::splitReference($reference);

        $databaseColumnName = NULL;
        for ($i = 0, $count = count($parts); $i < $count; $i += 2) {
            $resource = $parts[$i];
            $name = $parts[$i + 1];

            $columnNameSegment = $name;
            if (isset($resource)) {
                list($namespace, $resourceName) = NameSpaceHelper::splitAlias($resource);

                $columnNameSegment .= ParameterHelper::$COLUMN_NAME_DELIMITER__DATABASE;
                if (isset($namespace)) {
                    $columnNameSegment .= $namespace . ParameterHelper::$COLUMN_NAME_DELIMITER__DATABASE;
                }
                $columnNameSegment .= $resourceName;
            }

            if (isset($databaseColumnName)) {
                $databaseColumnName .= ParameterHelper::$COLUMN_NAME_DELIMITER__DATABASE;
            }
            $databaseColumnName .= $columnNameSegment;
        }

        // replacing non-word characters with ParameterHelper::$COLUMN_NAME_DELIMITER__DATABASE
        $databaseColumnName = preg_replace('/\W+/', ParameterHelper::$COLUMN_NAME_DELIMITER__DATABASE, $databaseColumnName);
        // removing several subsequent instances of ParameterHelper::$COLUMN_NAME_DELIMITER__DATABASE
        $databaseColumnName = preg_replace(
            '/' . ParameterHelper::$COLUMN_NAME_DELIMITER__DATABASE . '{2,}/',
            ParameterHelper::$COLUMN_NAME_DELIMITER__DATABASE,
            $databaseColumnName);

        $databaseColumnName = strtolower($databaseColumnName);

        return $databaseColumnName;
    }

    public static function assembleDatabaseColumnName($maximumLength, $reference) {
        $databaseColumnName = self::generateDatabaseColumnName($reference);

        return ParameterNameTruncater::truncateParameterName($databaseColumnName, $maximumLength);
    }

    public static function checkReference($reference) {
        $parts = self::splitReference($reference);

        for ($i = 0, $count = count($parts); $i < $count; $i += 2) {
            $resource = $parts[$i];
            if (isset($resource)) {
                NameSpaceHelper::checkAlias($resource);
            }

            $name = $parts[$i + 1];
            StringDataTypeHandler::checkValueAsWord($name);
        }
    }
}
