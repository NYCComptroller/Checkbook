<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




interface MetaModelLoader {

    function getName();

    function prepare(AbstractMetaModelFactory $factory, AbstractMetaModel $metamodel);

    /*
     * A loader can return self::LOAD_STATE__POSTPONED if it wants to wait until other loaders complete loading meta model.
     * The state can be returned as long as it is necessary and ($finalAttempt == TRUE) indicates last possible attempt to load meta model
     */
    function load(AbstractMetaModelFactory $factory, AbstractMetaModel $metamodel, array $filters = NULL, $finalAttempt);

    function finalize(AbstractMetaModelFactory $factory, AbstractMetaModel $metamodel);
}
