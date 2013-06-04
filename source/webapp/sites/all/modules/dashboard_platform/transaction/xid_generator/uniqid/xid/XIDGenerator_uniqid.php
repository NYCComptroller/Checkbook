<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class XIDGenerator_uniqid extends AbstractObject implements XIDGenerator {

    public function getFormatId() {
        return 208; // magic number ... means almost nothing ... just has to be a positive integer
    }

    public function generateGlobalTransactionId() {
        return uniqid();
    }

    public function generateBranchQualifier() {
        return uniqid();
    }
}
