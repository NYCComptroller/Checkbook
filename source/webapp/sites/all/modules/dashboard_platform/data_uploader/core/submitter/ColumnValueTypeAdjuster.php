<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
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
