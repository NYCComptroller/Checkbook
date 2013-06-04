<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class DatasetReference extends AbstractMetaData {

    /**
     * @var DatasetReferencePoint[]
     */
    public $points = array();

    public function __clone() {
        parent::__clone();
        $this->points = ArrayHelper::cloneArray($this->points);
    }

    public function initializeFrom($sourceReference) {
        parent::initializeFrom($sourceReference);

        $sourcePoints = ObjectHelper::getPropertyValue($sourceReference, 'points');
        if (isset($sourcePoints)) {
            $this->initializePointsFrom($sourcePoints);
        }
    }

    public function initializePointsFrom($sourceReferencePoints) {
        foreach ($sourceReferencePoints as $index => $sourceReferencePoint) {
            $this->initializePointFrom($index, $sourceReferencePoint);
        }
    }

    public function initializePointFrom($index, $sourceReferencePoint) {
        $isPointNew = !isset($this->points[$index]);

        $point = $isPointNew ? $this->initiatePoint() : $this->points[$index];
        $point->initializeFrom($sourceReferencePoint);

        if ($isPointNew) {
            $this->registerPointInstance($point);
        }

        return $point;
    }

    /**
     * @return DatasetReferencePoint
     */
    public function initiatePoint() {
        return new DatasetReferencePoint();
    }

    public function containsPoint(DatasetReferencePoint $sourceReferencePoint) {
        if (!isset($sourceReferencePoint)) {
            return FALSE;
        }

        foreach ($this->points as $point) {
            if ($point->equals($sourceReferencePoint)) {
                return TRUE;
            }
        }

        return FALSE;
    }

    public function registerPointInstance(DatasetReferencePoint $point) {
        if (!$this->containsPoint($point)) {
            if (count($this->points) > 0) {
                $existingPointColumnCount = $this->getPointColumnCount();
                $poinColumnCount = $point->getColumnCount();
                if ($poinColumnCount != $existingPointColumnCount) {
                    throw new IllegalStateException(t(
                        "'@referenceName' reference definition contains inconsistent number of columns",
                        array('@referenceName' => $this->publicName)));
                }
            }

            $this->points[] = $point;
        }
    }

    public function getPointCount() {
        return count($this->points);
    }

    public function getPointColumnCount() {
        if ($this->getPointCount() == 0) {
            return 0;
        }

        $point = reset($this->points);
        return $point->getColumnCount();
    }
}


class DatasetReferencePoint extends AbstractObject {

    /**
     * @var DatasetReferencePointColumn[]
     */
    public $columns = array();

    public function equals(DatasetReferencePoint $sourceReferencePoint) {
        if (!isset($sourceReferencePoint)) {
            return FALSE;
        }

        $columnCount = count($this->columns);
        if ($columnCount != count($sourceReferencePoint->columns)) {
            return FALSE;
        }

        for ($i = 0; $i < $columnCount; $i++) {
            $column = $this->columns[$i];
            $sourceColumn = $sourceReferencePoint->columns[$i];

            if (!$column->equals($sourceColumn)) {
                return FALSE;
            }
        }

        return TRUE;
    }

    public function initializeFrom($sourceReferencePoint) {
        if (isset($sourceReferencePoint)) {
            $sourceColumns = ObjectHelper::getPropertyValue($sourceReferencePoint, 'columns');
            if (isset($sourceColumns)) {
                foreach ($sourceColumns as $sourceColumn) {
                    $column = $this->initiateColumn();
                    $column->initializeFrom($sourceColumn);

                    $this->registerColumnInstance($column);
                }
            }
            else {
                $column = $this->initiateColumn();
                $column->datasetName = ObjectHelper::getPropertyValue($sourceReferencePoint, 'datasetName');

                $this->registerColumnInstance($column);
            }
        }
    }

    public function initiateColumn() {
        return new DatasetReferencePointColumn();
    }

    public function registerColumnInstance(DatasetReferencePointColumn $column) {
        $this->columns[] = $column;
    }

    public function getColumnCount() {
        return count($this->columns);
    }
}

class DatasetReferencePointColumn extends AbstractObject {

    public $datasetName = NULL;
    public $columnName = NULL;
    public $shared = NULL;

    public function equals(DatasetReferencePointColumn $sourceReferencePointColumn) {
        if (!isset($sourceReferencePointColumn)) {
            return FALSE;
        }

        if ($this->datasetName != $sourceReferencePointColumn->datasetName) {
            return FALSE;
        }

        if (isset($this->columnName) && isset($sourceReferencePointColumn->columnName) && ($this->columnName != $sourceReferencePointColumn->columnName)) {
            return FALSE;
        }

        return TRUE;
    }

    public function initializeFrom($sourceReferencePointColumn) {
        if (isset($sourceReferencePointColumn)) {
            $this->datasetName = ObjectHelper::getPropertyValue($sourceReferencePointColumn, 'datasetName');
            $this->columnName = ObjectHelper::getPropertyValue($sourceReferencePointColumn, 'columnName');
            $this->shared = ObjectHelper::getPropertyValue($sourceReferencePointColumn, 'shared');
        }
    }

    public function isShared() {
        return isset($this->shared) ? $this->shared : FALSE;
    }
}
