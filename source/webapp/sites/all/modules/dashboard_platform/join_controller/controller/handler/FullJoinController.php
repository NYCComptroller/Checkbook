<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




class FullJoinController extends AbstractColumnBasedJoinController {

    public static $METHOD_NAME = 'Full';

    private $outerJoinController = NULL;

    public function __construct() {
        parent::__construct();
        $this->outerJoinController = new LeftOuterJoinController();
    }

    protected function preselectSourceConfiguration(JoinController_SourceConfiguration $sourceConfigurationA, JoinController_SourceConfiguration $sourceConfigurationB) {
        return isset($sourceConfigurationA->data)
            ? (isset($sourceConfigurationB->data)
                ? FALSE // we need to join the sources
                : $sourceConfigurationA)
            : (isset($sourceConfigurationB->data)
                ? $sourceConfigurationB
                : new JoinController_SourceConfiguration());
    }

    protected function joinHash(array &$result, array &$hashedSourceA, array &$hashedSourceB) {
        $this->outerJoinController->joinHash($result, $hashedSourceA, $hashedSourceB);
        $this->outerJoinController->joinHash($result, $hashedSourceB, $hashedSourceA);
    }
}
