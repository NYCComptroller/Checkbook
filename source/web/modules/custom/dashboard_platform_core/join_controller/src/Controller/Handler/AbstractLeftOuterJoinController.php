<?php

namespace Drupal\join_controller\Controller\Handler;

use Drupal\join_controller\Controller\JoinController_SourceConfiguration;

abstract class AbstractLeftOuterJoinController extends AbstractColumnBasedJoinController {

    protected function preselectSourceConfiguration(JoinController_SourceConfiguration $sourceConfigurationA, JoinController_SourceConfiguration $sourceConfigurationB) {
        return isset($sourceConfigurationA->data)
            ? (isset($sourceConfigurationB->data)
                ? FALSE // we need to join the sources
                : $sourceConfigurationA)
            : new JoinController_SourceConfiguration(); // left dataset does not have any data -> result is empty list
    }

    public function joinHash(array &$result, array &$hashedSourceA, array &$hashedSourceB) {
        foreach ($hashedSourceA as $keyA => $recordsA) {
            if (isset($hashedSourceB[$keyA])) {
                $recordsB = $hashedSourceB[$keyA];
                foreach ($recordsA as $recordA) {
                    foreach ($recordsB as $recordB) {
                        $result[] = $this->mergeRecords($recordA, $recordB);
                    }
                }

                unset($hashedSourceB[$keyA]);
            }
            else {
                $result = array_merge($result, $recordsA);
            }

            unset($hashedSourceA[$keyA]);
        }
    }
}
