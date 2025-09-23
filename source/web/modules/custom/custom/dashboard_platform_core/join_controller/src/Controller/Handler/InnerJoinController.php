<?php

namespace Drupal\join_controller\Controller\Handler;

use Drupal\join_controller\Controller\JoinController_SourceConfiguration;

class InnerJoinController extends AbstractColumnBasedJoinController {

    public static $METHOD_NAME = 'Inner';

    protected function preselectSourceConfiguration(JoinController_SourceConfiguration $sourceConfigurationA, JoinController_SourceConfiguration $sourceConfigurationB) {
        return isset($sourceConfigurationA->data)
            ? (isset($sourceConfigurationB->data)
                ? FALSE // we need to join the sources
                : $sourceConfigurationB)
            : $sourceConfigurationA;
    }

    protected function joinHash(array &$result, array &$hashedSourceA, array &$hashedSourceB) {
        foreach ($hashedSourceA as $keyA => $recordsA) {
            // skipping the record which does not have a corresponding record in other data set
            if (!isset($hashedSourceB[$keyA])) {
                continue;
            }

            $recordsB = $hashedSourceB[$keyA];

            foreach ($recordsA as $recordA) {
                foreach ($recordsB as $recordB) {
                    $result[] = $this->mergeRecords($recordA, $recordB);
                }
            }
        }
    }
}
