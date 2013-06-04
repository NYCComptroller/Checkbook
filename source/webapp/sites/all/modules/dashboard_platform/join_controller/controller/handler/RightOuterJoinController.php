<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




class RightOuterJoinController extends AbstractLeftOuterJoinController {

    public static $METHOD_NAME = 'RightOuter';

    protected function joinSourceConfigurations(JoinController_SourceConfiguration $sourceConfigurationA, JoinController_SourceConfiguration $sourceConfigurationB) {
        return parent::joinSourceConfigurations($sourceConfigurationB, $sourceConfigurationA);
    }
}
