<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




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
