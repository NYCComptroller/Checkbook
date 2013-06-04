<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class ParameterNameTruncater {

    public static $PARAMETER_NAME_AUTOMATIC_SUFFIX_DELIMITER = '_';
    protected static $parameterNameAutomaticSuffixSequence = 0;

    protected static $shortenedParameterNames = NULL; // [$shreddableCharacterCount][$originalParameterName]
    protected static $truncatedParameterNames = NULL; // [$maximumLength][$originalParameterName]

    protected static function removeVowels($parameterName, &$shreddableCharacterCount) {
        // removing vowels (except first character and words shorter than 4 characters) starting from the longest word
        $parameterNameParts = explode('_', $parameterName);

        $processableIndexes = array();
        foreach ($parameterNameParts as $index => $part) {
            $length = strlen($part);
            if ($length <= 3) {
                continue;
            }

            $processableIndexes[$index] = $length;
        }

        arsort($processableIndexes, SORT_NUMERIC);

        foreach ($processableIndexes as $processableIndex => $length) {
            $part = $parameterNameParts[$processableIndex];

            $updatedPart = NULL;
            for ($i = strlen($part) - 1; (($shreddableCharacterCount > 0) && ($i > 0 /* excluding first character*/)); $i--) {
                $char = $part[$i];
                switch ($char) {
                    case 'a':
                    case 'e':
                    case 'i':
                    case 'o':
                    case 'u':
                    case 'y': // because it is in the middle of the word it is in most cases is vowel
                        if (!isset($updatedPart)) {
                            $updatedPart = $part;
                        }
                        $updatedPart = substr_replace($updatedPart, '', $i, 1);
                        $shreddableCharacterCount--;
                        break;
                }
            }
            if (isset($updatedPart)) {
                $parameterNameParts[$processableIndex] = $updatedPart;
            }

            if ($shreddableCharacterCount == 0) {
                break;
            }
        }

        return implode('_', $parameterNameParts);
    }

    public static function shortenParameterName($parameterName, $shreddableCharacterCount) {
        $shortenedParameterName = $parameterName;

        if (isset(self::$shortenedParameterNames[$shreddableCharacterCount][$parameterName])) {
            $shortenedParameterName = self::$shortenedParameterNames[$shreddableCharacterCount][$parameterName];
        }
        else {
            $shortenedParameterName = self::removeVowels($shortenedParameterName, $shreddableCharacterCount);

            self::$shortenedParameterNames[$shreddableCharacterCount][$parameterName] = $shortenedParameterName;
        }

        return $shortenedParameterName;
    }

    public static function truncateParameterName($parameterName, $maximumLength) {
        $truncatedParameterName = $parameterName;

        if (isset(self::$truncatedParameterNames[$maximumLength][$parameterName])) {
            $truncatedParameterName = self::$truncatedParameterNames[$maximumLength][$parameterName];
        }
        elseif (strlen($truncatedParameterName) > $maximumLength) {
            $suffix = self::$parameterNameAutomaticSuffixSequence++;

            $maximumColumnPrefixLength = $maximumLength;
            $maximumColumnPrefixLength -= strlen(self::$PARAMETER_NAME_AUTOMATIC_SUFFIX_DELIMITER);
            $maximumColumnPrefixLength -= strlen($suffix);

            $shreddableCharacterCount = strlen($truncatedParameterName) - $maximumColumnPrefixLength;
            $truncatedParameterName = self::shortenParameterName($truncatedParameterName, $shreddableCharacterCount);

            if (strlen($truncatedParameterName) > $maximumColumnPrefixLength) {
                $truncatedParameterName = substr($truncatedParameterName, 0, $maximumColumnPrefixLength);
            }
            $truncatedParameterName .= self::$PARAMETER_NAME_AUTOMATIC_SUFFIX_DELIMITER . $suffix;

            self::$truncatedParameterNames[$maximumLength][$parameterName] = $truncatedParameterName;
        }

        return $truncatedParameterName;
    }
}
