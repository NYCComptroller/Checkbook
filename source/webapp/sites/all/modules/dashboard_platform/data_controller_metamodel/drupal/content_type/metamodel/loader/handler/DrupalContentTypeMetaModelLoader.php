<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




class DrupalContentTypeMetaModelLoader extends AbstractMetaModelLoader {

    public static $DATASET__PREFIX = 'contentType_';

    public function load(AbstractMetaModelFactory $factory, AbstractMetaModel $metamodel, array $filters = NULL, $finalAttempt) {
        LogHelper::log_notice(t('Creating Meta Model using Drupal Content Types...'));

        $datasetCounter = 0;
        $contentTypes = content_types();
        if (isset($contentTypes)) {
            foreach ($contentTypes as $contentTypeName => $contentType) {
                // preparing list of tables which could be supported by our code
                $supportedTables = NULL;
                foreach ($contentType['fields'] as $field) {
                    $fieldName = $field['field_name'];
                    if ($field['multiple'] > 0) {
                        $message = t(
                        	'Multiple values are not supported yet: @contentTypeName.@fieldName',
                            array('@contentTypeName' => $contentTypeName, '@fieldName' => $fieldName));
                        LogHelper::log_warn($message);
                        continue; // UnsupportedOperationException
                    }

                    // preparing table name where the field is stored
                    $fieldStorage = $field['db_storage'];
                    switch ($fieldStorage) {
                        case CONTENT_DB_STORAGE_PER_CONTENT_TYPE:
                            $tableName = _content_tablename($field['type_name'], $fieldStorage);
                            break;
                        case CONTENT_DB_STORAGE_PER_FIELD:
                            break;
                            $tableName = _content_tablename($fieldName, $fieldStorage);
                        default:
                            $message = t(
                                "Unsupported storage type - '@fieldStorage' for the field: @fieldName",
                                array('@fieldStorage' => $fieldStorage, '@fieldName' => $fieldName));
                            LogHelper::log_warn($message);
                            continue; // UnsupportedOperationException
                    }

                    // calculating number of 'visible' suffixes
                    $visibleSuffixCount = 0;
                    foreach ($field['columns'] as $columnAttributes) {
                        if (isset($columnAttributes['views'])) {
                            if ($columnAttributes['views'] === TRUE) {
                                $visibleSuffixCount++;
                            }
                        }
                        else {
                            $visibleSuffixCount++;
                        }
                    }

                    // generating fields for all 'visible' suffixes
                    foreach ($field['columns'] as $columnSuffix => $columnAttributes) {
                        if (isset($columnAttributes['views']) && ($columnAttributes['views'] === FALSE)) {
                            continue;
                        }

                        $supportedField = new stdClass();
                        // required flag
                        $supportedField->required = $field->required == 1;
                        // original name of the field
                        $supportedField->original_name = $fieldName;
                        // calculating name of database column
                        $supportedField->column = $fieldName . '_' . $columnSuffix;
                        // field name
                        if ($visibleSuffixCount === 1) {
                            $supportedField->name = $fieldName;
                        }
                        else {
                            $supportedField->name = $supportedField->column;
                        }

                        if (isset($supportedTables[$tableName]->storage)) {
                            $previousStorage = $supportedTables[$tableName]->storage;
                            if ($fieldStorage != $previousStorage) {
                                $message = t(
                                	"Inconsistent storage for '@tableName' table([@fieldStorage1, @fieldStorage2]) for the field: @fieldName",
                                    array('@tableName' => $tableName, '@fieldName' => $fieldName, '@fieldStorage1' => $previousStorage, '@fieldStorage2' => $fieldStorage));
                                LogHelper::log_warn($message);
                                continue; // IllegalStateException
                            }
                        }
                        else {
                            $supportedTables[$tableName]->storage = $fieldStorage;
                        }

                        $supportedTables[$tableName]->supportedFields[$supportedField->name] = $supportedField;
                    }
                }

                // preparing dataset source
                $datasetSource = new stdClass();
                $datasetSource->assembler->type = ContentTypeDatasetSourceAssembler::$DATASET_SOURCE_ASSEMBLER__TYPE;
                $datasetSource->assembler->config->drupal = $contentType;
                if (isset($supportedTables)) {
                    $datasetSource->assembler->config->supportedTables = $supportedTables;
                }

                // preparing & registering dataset
                $dataset = new DatasetMetaData();
                $dataset->name = $this->getDatasetName($contentTypeName);
                $dataset->description = $contentType['description'];
                $dataset->datasourceName = AbstractDrupalDataSourceQueryProxy::$DATASOURCE_NAME__DEFAULT;
                $dataset->source = $datasetSource;
                // FIXME Populate list of columns and mark the dataset as complete
                $dataset->registerColumn('nid')->key = TRUE;

                $metamodel->registerDataset($dataset);
                $datasetCounter++;
            }
        }
        LogHelper::log_info(t('Processed @datasetCount datasets', array('@datasetCount' => $datasetCounter)));

        return self::LOAD_STATE__SUCCESSFUL;
    }

    public static function getDatasetName($contentTypeName) {
        // FIXME cache it somewhere
        list($namespace) = NameSpaceHelper::splitAlias(AbstractDrupalDataSourceQueryProxy::$DATASOURCE_NAME__DEFAULT);

        return NameSpaceHelper::addNameSpace($namespace, self::$DATASET__PREFIX . $contentTypeName);
    }
}
