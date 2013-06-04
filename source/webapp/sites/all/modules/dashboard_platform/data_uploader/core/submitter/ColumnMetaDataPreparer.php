<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class ColumnMetaDataPreparer extends AbstractDataSubmitter {

    private $maximumColumnNameLength = NULL;
    private $columnPrefixName = NULL;

    public function __construct($maximumColumnNameLength, $columnPrefixName = NULL) {
        parent::__construct();
        $this->maximumColumnNameLength = $maximumColumnNameLength;
        $this->columnPrefixName = $columnPrefixName;
    }

    protected function lowercaseColumnNameParts($columnName) {
        $columnNameParts = explode('_', $columnName);
        if ($columnNameParts === FALSE) {
            return $columnName;
        }

        $updatedColumnName = '';
        foreach ($columnNameParts as $part) {
            if ($part == strtoupper($part)) {
                $part = strtolower($part);
            }

            if ($updatedColumnName != '') {
                $updatedColumnName .= '_';
            }
            $updatedColumnName .= $part;
        }

        return $updatedColumnName;
    }

    protected function replaceSpecialCharactersWithCorrespondingText($columnName) {
        return str_replace(
            array(
                '#',
                '&'),
            array(
                '_number_',
                '_and_'),
            $columnName);
    }

    protected function preserveEnglishWordCharacters($columnName) {
        // for some reason preg_replace('/\W+/', '_', $columnName) does not work with Unicode characters as expected
        $updatedColumnName = '';
        for ($i = 0, $l = strlen($columnName); $i < $l; $i++) {
            $c = $columnName[$i];
            if (($c >= '0' && $c <= '9')
                    || ($c == '_')
                    || ($c >= 'A' && $c <= 'Z')
                    || ($c >= 'a' && $c <= 'z')) {
                // acceptable character
            }
            else {
                $c = '_';
            }
            $updatedColumnName .= $c;
        }

        return $updatedColumnName;
    }

    protected function adjustColumnName($columnName) {
        if (!isset($columnName)) {
            return NULL;
        }

        // replacing well-known characters with corresponding text
        $adjustedColumnName = $this->replaceSpecialCharactersWithCorrespondingText($columnName);
        // replacing non-word characters with '_'
        $adjustedColumnName = $this->preserveEnglishWordCharacters($adjustedColumnName);
        // adjusting sections of the column name to lower case
        $adjustedColumnName = $this->lowercaseColumnNameParts($adjustedColumnName);
        // adding additional '_' between words
        $words = preg_split('/([[:upper:]][[:lower:]]*)/', $adjustedColumnName, NULL, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        $adjustedColumnName = implode('_', $words);
        // removing leading digits and '_'(s)
        $adjustedColumnName = preg_replace('/^[\d_]+/', '', $adjustedColumnName);
        // removing last '_'(s)
        $adjustedColumnName = preg_replace('/_+$/', '', $adjustedColumnName);
        // removing several '_'(s) in a row
        $adjustedColumnName = preg_replace('/_{2,}/', '_', $adjustedColumnName);

        // adding column prefix
        if (isset($this->columnPrefixName)) {
            $adjustedColumnName = $this->columnPrefixName . $adjustedColumnName;
        }

        $length = strlen($adjustedColumnName);
        if ($length > $this->maximumColumnNameLength) {
            $adjustedColumnName = ParameterNameTruncater::shortenParameterName($adjustedColumnName, $length - $this->maximumColumnNameLength);
        }

        if (strlen($adjustedColumnName) > $this->maximumColumnNameLength) {
            $adjustedColumnName = '';
        }

        return ($adjustedColumnName == '') ? NULL : strtolower($adjustedColumnName);
    }

    protected function adjustColumnPublicName($columnPublicName) {
        if (!isset($columnPublicName)) {
            return NULL;
        }

        $adjustedColumnPublicName = $columnPublicName;

        return $adjustedColumnPublicName;
    }

    public function prepareMetaDataColumn(RecordMetaData $recordMetaData, ColumnMetaData $column, $originalColumnName) {
        parent::prepareMetaDataColumn($recordMetaData, $column, $originalColumnName);

        $columnPublicName = isset($originalColumnName) ? $this->adjustColumnPublicName($originalColumnName) : NULL;
        $columnSystemName = isset($columnPublicName) ? $this->adjustColumnName($columnPublicName) : NULL;

        // if the same name already exists we can try to add numeric suffix
        if (isset($columnSystemName)) {
            $originalColumnSystemName = $columnSystemName;

            $nameIndex = 2;
            while ($recordMetaData->findColumn($columnSystemName) != NULL) {
                $columnSystemName = $originalColumnSystemName . '_' . $nameIndex;

                // if length of new name is greater than allowed we cannot use 'indexed' name
                if (strlen($columnSystemName) > $this->maximumColumnNameLength) {
                    $columnSystemName = NULL;
                    break;
                }

                $nameIndex++;
            }
        }

        if (!isset($columnSystemName) || ($recordMetaData->findColumn($columnSystemName) != NULL)) {
            $columnNumber = $column->columnIndex + 1;

            if (!isset($columnPublicName)) {
                $columnPublicName = "Column $columnNumber";
            }
            $columnSystemName = (isset($this->columnPrefixName) ? $this->columnPrefixName : 'c') . $columnNumber;

            // if length of 'hardcoded' name is greater than allowed we cannot proceed any further
            if (strlen($columnSystemName) > $this->maximumColumnNameLength) {
                throw new UnsupportedOperationException(t(
                    "System name cannot be generated for '@columnName' column. Maximum allowed length is @maximumColumnNameLength",
                    array('@columnName' => $originalColumnName, '@maximumColumnNameLength' => $this->maximumColumnNameLength)));
            }
        }

        $column->name = $columnSystemName;
        $column->publicName = $columnPublicName;
    }
}
