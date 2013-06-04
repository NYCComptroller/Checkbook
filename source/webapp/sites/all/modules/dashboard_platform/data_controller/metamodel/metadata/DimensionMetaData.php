<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class DimensionMetaData extends AbstractMetaData {

    /**
     * List of levels for this dimension.
     * It is not possible to have a dimension without any levels
     * @var LevelMetaData[]
     */
    public $levels = array();

    public function __clone() {
        parent::__clone();

        $this->levels = ArrayHelper::cloneArray($this->levels);
    }

    public function finalize() {
        parent::finalize();

        foreach ($this->levels as $level) {
            $level->finalize();
        }
    }

    public function isComplete() {
        $complete = parent::isComplete();

        for ($i = 0, $count = count($this->levels); $complete && ($i < $count); $i++) {
            $level = $this->levels[$i];
            $complete = $level->isComplete();
        }

        return $complete;
    }

    public function initializeFrom($sourceDimension) {
        parent::initializeFrom($sourceDimension);

        // preparing list of levels
        $sourceLevels = ObjectHelper::getPropertyValue($sourceDimension, 'levels');
        if (isset($sourceLevels)) {
            $this->initializeLevelsFrom($sourceLevels);
        }
    }

    public function initializeLevelsFrom($sourceLevels) {
        if (isset($sourceLevels)) {
            foreach ($sourceLevels as $sourceLevel) {
                $sourceLevelName = ObjectHelper::getPropertyValue($sourceLevel, 'name');

                $level = $this->findLevel($sourceLevelName);
                if (!isset($level)) {
                    $level = $this->registerLevel($sourceLevelName);
                }
                $level->initializeFrom($sourceLevel);
            }
        }
    }

    public function initiateLevel() {
        return new LevelMetaData();
    }

    public function registerLevel($levelName) {
        $level = $this->initiateLevel();
        $level->name = $levelName;

        $this->registerLevelInstance($level);

        return $level;
    }

    public function registerLevelInstance(LevelMetaData $unregisteredLevel) {
        $existingLevel = $this->findLevel($unregisteredLevel->name);
        if (isset($existingLevel)) {
            $this->errorLevelFound($existingLevel);
        }

        $this->levels[] = $unregisteredLevel;
    }

    public function unregisterLevel($levelName) {
        for ($i = 0, $count = count($this->levels); $i < $count; $i++) {
            $level = $this->levels[$i];
            if ($level->name === $levelName) {
                unset($this->levels[$i]);
                return;
            }
        }

        $this->errorLevelNotFound($levelName);
    }

    public function findLevel($levelName) {
        foreach ($this->levels as $level) {
            if ($level->name === $levelName) {
                return $level;
            }
        }

        return NULL;
    }

    public function getLevel($levelName) {
        $level = $this->findLevel($levelName);
        if (!isset($level)) {
            $this->errorLevelNotFound($levelName);
        }

        return $level;
    }

    public function findLevelIndex($levelName) {
        for ($i = 0, $count = $this->getLevelCount(); $i < $count; $i++) {
            $level = $this->levels[$i];
            if ($level->name === $levelName) {
                return $i;
            }
        }

        return NULL;
    }

    public function getLevelIndex($levelName) {
        $levelIndex = $this->findLevelIndex($levelName);
        if (!isset($levelIndex)) {
            $this->errorLevelNotFound($levelName);
        }

        return $levelIndex;
    }

    public function getLevelCount() {
        return count($this->levels);
    }

    protected function errorLevelFound($level) {
        throw new IllegalArgumentException(t(
            "Level '@levelName' has been already registered in '@dimensionName' dimension",
            array('@levelName' => $level->name, '@dimensionName' => $this->publicName)));
    }

    protected function errorLevelNotFound($levelName) {
        throw new IllegalArgumentException(t(
            "Level '@levelName' is not registered in '@dimensionName' dimension",
            array('@levelName' => $levelName, '@dimensionName' => $this->publicName)));
    }
}
