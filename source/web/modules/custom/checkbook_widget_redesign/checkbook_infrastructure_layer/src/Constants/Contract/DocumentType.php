<?php

namespace Drupal\checkbook_infrastructure_layer\Constants\Contract;

abstract class DocumentType {

    const CT1 = "General Contract";
    const CTA1 = "Multiple Award Contract";
    const CTA2 = "Consortium Contract";
    const DO1 = "Delivery Order";
    const MA1 = "Master agreement";
    const MMA1 = "Multiple Award Master Agreement";
    const RCT1 = "Revenue Contract";
    const CTR = "Pending General Contract";
}
