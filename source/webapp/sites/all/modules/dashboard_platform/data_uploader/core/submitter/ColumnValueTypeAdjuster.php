<?php
/**
* This file is part of the Checkbook NYC financial transparency software.
* 
* Copyright (C) 2012, 2013 New York City
* 
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Affero General Public License as
* published by the Free Software Foundation, either version 3 of the
* License, or (at your option) any later version.
* 
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU Affero General Public License for more details.
* 
* You should have received a copy of the GNU Affero General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


class ColumnValueTypeAdjuster extends AbstractDataSubmitter {

    public function beforeRecordSubmit(RecordMetaData $recordMetaData, $recordNumber, array &$record) {
        $result = parent::beforeRecordSubmit($recordMetaData, $recordNumber, $record);

        if ($result) {
            $datatypeFactory = DataTypeFactory::getInstance();

            foreach ($recordMetaData->getColumns() as $column) {
                if (!isset($record[$column->columnIndex])) {
                    continue;
                }

                list($datasetName) = ReferencePathHelper::splitReference($column->type->applicationType);

                // FIXME convert data to data type of corresponding lookup dataset column
                if (isset($datasetName)) {
                    continue;
                }

                $handler = $datatypeFactory->getHandler($column->type->applicationType);
                try {
                    $record[$column->columnIndex] = $handler->castValue($record[$column->columnIndex]);
                }
                catch (Exception $e) {
                    LogHelper::log_error($e->getFile() . ':' . $e->getLine());
                    throw new IllegalArgumentException(
                        ExceptionHelper::getExceptionMessage($e) . t(' [column: @columnName]', array('@columnName' => $column->publicName)),
                        0, $e);
                }
            }
        }

        return $result;
    }
}
