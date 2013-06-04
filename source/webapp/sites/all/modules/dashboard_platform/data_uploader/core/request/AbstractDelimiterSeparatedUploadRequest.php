<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


abstract class AbstractDelimiterSeparatedUploadRequest extends AbstractUploadRequest {

    public $delimiter = ',';

    protected function initiateDataParser() {
        return new DelimiterDataParser($this->delimiter);
    }
}
