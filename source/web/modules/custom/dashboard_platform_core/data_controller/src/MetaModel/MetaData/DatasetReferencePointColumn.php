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
