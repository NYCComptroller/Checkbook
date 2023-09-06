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

use Drupal\data_controller\Common\Object\Manipulation\ObjectHelper;
use Drupal\data_controller\Common\Pattern\AbstractObject;


class DatasetReferencePoint extends AbstractObject {

    /**
     * @var DatasetReferencePointColumn[]
     */
    public $columns = [];

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
