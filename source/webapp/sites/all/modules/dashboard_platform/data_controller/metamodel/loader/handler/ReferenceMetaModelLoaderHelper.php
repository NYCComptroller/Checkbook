<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class ReferenceMetaModelLoaderHelper {

    public static function adjustReferencePointColumn(MetaModelLoader $metamodelLoader, AbstractMetaModel $metamodel, DatasetReferencePointColumn $referencePointColumn) {
        if (!isset($referencePointColumn->columnName)) {
            $dataset = $metamodel->getDataset($referencePointColumn->datasetName);

            // FIXME eliminate this logic. Support configuration postprocessing
            $referencePointColumn->columnName = $dataset->getKeyColumn()->name;
        }

        if ($metamodelLoader instanceof ReferenceMetaModelLoader) {
            $metamodelLoader->adjustReferencePointColumn($metamodel, $referencePointColumn);
        }
    }
}
