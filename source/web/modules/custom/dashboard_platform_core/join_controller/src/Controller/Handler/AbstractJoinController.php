<?php

namespace Drupal\join_controller\Controller\Handler;

use Drupal\checkbook_log\LogHelper;
use Drupal\data_controller\Common\Pattern\AbstractObject;
use Drupal\data_controller\Common\Performance\ExecutionPerformanceHelper;
use Drupal\join_controller\Controller\JoinController;
use Drupal\join_controller\Controller\JoinController_SourceConfiguration;

abstract class AbstractJoinController extends AbstractObject implements JoinController {

    abstract protected function joinSourceConfigurations(JoinController_SourceConfiguration $sourceConfigurationA, JoinController_SourceConfiguration $sourceConfigurationB);

    public final function join(JoinController_SourceConfiguration $sourceConfigurationA, JoinController_SourceConfiguration $sourceConfigurationB) {
        $timeStart = microtime(TRUE);
        $result = $this->joinSourceConfigurations($sourceConfigurationA, $sourceConfigurationB);
        LogHelper::log_info(t(
            '@className execution time: !executionTime',
            array('@className' => get_class($this), '!executionTime' => ExecutionPerformanceHelper::formatExecutionTime($timeStart))));

        return $result;
    }
}
