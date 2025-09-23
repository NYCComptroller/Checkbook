<?php

namespace Drupal\checkbook_infrastructure_layer\Constants\Contract;

abstract class DocumentCodeId {

    const CT1 = 1;
    const CTA1 = 2;
    const CTA2 = 3;
    const DO1 = 4;
    const MA1 = 5;
    const MMA1 = 6;
    const RCT1 = 7;
    const CTR = 20;

    public static function isMasterAgreement($documentCodeId) {
        return $documentCodeId == self::MA1 || $documentCodeId == self::MMA1;
    }
}
