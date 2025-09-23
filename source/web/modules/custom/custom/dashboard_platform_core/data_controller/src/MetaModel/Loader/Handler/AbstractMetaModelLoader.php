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

namespace Drupal\data_controller\MetaModel\Loader\Handler;

use Drupal\data_controller\Common\Pattern\AbstractObject;
use Drupal\data_controller\MetaModel\Factory\AbstractMetaModelFactory;
use Drupal\data_controller\MetaModel\Handler\AbstractMetaModel;
use Drupal\data_controller\MetaModel\Loader\MetaModelLoader;
use Drupal\data_controller\MetaModel\MetaData\AbstractMetaData;


abstract class AbstractMetaModelLoader extends AbstractObject implements MetaModelLoader {

    const LOAD_STATE__SUCCESSFUL = 'Successful';
    const LOAD_STATE__SKIPPED = 'Skipped';
    const LOAD_STATE__POSTPONED = 'Postponed';

    public function getName() {
        return get_class($this);
    }

    public function prepare(AbstractMetaModelFactory $factory, AbstractMetaModel $metamodel) {}

    public function finalize(AbstractMetaModelFactory $factory, AbstractMetaModel $metamodel) {}

    protected function isMetaDataAcceptable(AbstractMetaData $metadata, array $filters = NULL) {
        $classname = get_class($metadata);
        if (isset($filters[$classname])) {
            foreach ($filters[$classname] as $propertyName => $filterValues) {
                if (isset($metadata->$propertyName)) {
                    $propertyValue = $metadata->$propertyName;
                    if (array_search($propertyValue, $filterValues) === FALSE) {
                        return FALSE;
                    }
                }
            }
        }

        return TRUE;
    }
}
