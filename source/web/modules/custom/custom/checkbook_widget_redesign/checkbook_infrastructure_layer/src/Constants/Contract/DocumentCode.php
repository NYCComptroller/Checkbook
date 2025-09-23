<?php

namespace Drupal\checkbook_infrastructure_layer\Constants\Contract;

abstract class DocumentCode {

    const CT1 = "CT1";
    const CTA1 = "CTA1";
    const CTA2 = "CTA2";
    const DO1 = "DO1";
    const MA1 = "MA1";
    const MMA1 = "MMA1";
    const RCT1 = "RCT1";
    const CTR = "CTR";

    public static function isMasterAgreement($documentCode) {
        return $documentCode == self::MA1 || $documentCode == self::MMA1;
    }
}
