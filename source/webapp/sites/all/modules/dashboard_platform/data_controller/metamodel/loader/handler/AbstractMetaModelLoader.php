<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




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
