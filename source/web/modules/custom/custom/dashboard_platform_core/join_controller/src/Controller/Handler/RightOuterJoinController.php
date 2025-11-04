<?php

namespace Drupal\join_controller\Controller\Handler;

use Drupal\join_controller\Controller\JoinController_SourceConfiguration;

class RightOuterJoinController extends AbstractLeftOuterJoinController {

    public static $METHOD_NAME = 'RightOuter';

    protected function joinSourceConfigurations(JoinController_SourceConfiguration $sourceConfigurationA, JoinController_SourceConfiguration $sourceConfigurationB) {
        return parent::joinSourceConfigurations($sourceConfigurationB, $sourceConfigurationA);
    }
}
