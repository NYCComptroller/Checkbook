<?php
/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (c) 2012 – 2023 New York City
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Drupal\data_controller\MetaModel\MetaData;

use Drupal\data_controller\Common\Object\Exception\IllegalStateException;
use Drupal\data_controller\Common\Object\Manipulation\ArrayHelper;
use Drupal\data_controller\Common\Object\Manipulation\ObjectHelper;

class DatasetReference extends AbstractMetaData
{

    /**
     * @var \DatasetReferencePoint
     */
    public $points = [];

    public function __clone()
    {
        parent::__clone();
        $this->points = ArrayHelper::cloneArray($this->points);
    }

    public function initializeFrom($sourceReference)
    {
        parent::initializeFrom($sourceReference);

        $sourcePoints = ObjectHelper::getPropertyValue($sourceReference, 'points');
        if (isset($sourcePoints)) {
            $this->initializePointsFrom($sourcePoints);
        }
    }

    public function initializePointsFrom($sourceReferencePoints)
    {
        foreach ($sourceReferencePoints as $index => $sourceReferencePoint) {
            $this->initializePointFrom($index, $sourceReferencePoint);
        }
    }

    public function initializePointFrom($index, $sourceReferencePoint)
    {
        $isPointNew = !isset($this->points[$index]);

        $point = $isPointNew ? $this->initiatePoint() : $this->points[$index];
        $point->initializeFrom($sourceReferencePoint);

        if ($isPointNew) {
            $this->registerPointInstance($point);
        }

        return $point;
    }

    /**
     * @return \DatasetReferencePoint
     */
    public function initiatePoint()
    {
        return new DatasetReferencePoint();
    }

    public function containsPoint(DatasetReferencePoint $sourceReferencePoint)
    {
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

    public function registerPointInstance(DatasetReferencePoint $point)
    {
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

    public function getPointCount()
    {
        return count($this->points);
    }

    public function getPointColumnCount()
    {
        if ($this->getPointCount() == 0) {
            return 0;
        }

        $point = reset($this->points);
        return $point->getColumnCount();
    }
}
