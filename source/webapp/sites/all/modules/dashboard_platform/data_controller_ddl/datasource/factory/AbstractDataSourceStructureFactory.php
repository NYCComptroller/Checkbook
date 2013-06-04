<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


abstract class AbstractDataSourceStructureFactory extends AbstractDataSourceFactory {

    protected function getFactoryPublicNamePrefix() {
        return 'Structure';
    }

    protected function getHookName() {
        return 'dc_datasource_structure';
    }
}
