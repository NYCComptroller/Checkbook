<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class DefaultXIDFactory extends XIDFactory {

    public function newXID() {
        $generator = XIDGeneratorFactory::getInstance()->getGenerator();

        $formatId = $generator->getFormatId();
        if ($formatId == XID_FORMAT_NULL) {
            throw new IllegalArgumentException(t('Unsupported XID format identifier: @formatId', array('@formatId' => $formatId)));
        }

        $globalTransactionId = $generator->generateGlobalTransactionId();
        if (strlen($globalTransactionId) > MAXGTRIDSIZE) {
            throw new IllegalArgumentException(t(
                'Global transaction identifier is too long. Max length is @globalTransactionIdLength bytes',
                array('@globalTransactionIdLength' => MAXGTRIDSIZE)));
        }

        $branchQualifier = $generator->generateBranchQualifier();
        if (strlen($branchQualifier) > MAXBQUALSIZE) {
            throw new IllegalArgumentException(t(
                'Global transaction branch qualifier is too long. Max length is @branchQualifierLength bytes',
                array('@branchQualifierLength' => MAXBQUALSIZE)));
        }

        $xid = new XID();
        $xid->formatId = $formatId;
        $xid->globalTransactionId = $globalTransactionId;
        $xid->branchQualifier = $branchQualifier;

        return $xid;
    }
}
