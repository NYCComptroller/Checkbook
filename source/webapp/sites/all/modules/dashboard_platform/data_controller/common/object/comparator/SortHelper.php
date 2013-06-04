<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




function sort_records(array &$records = NULL, $sorting_configurations) {
    if (!isset($records)) {
        return;
    }

    if (!isset($sorting_configurations)) {
        return;
    }

    $comparator = new DefaultPropertyBasedComparator();
    $comparator->registerSortingConfigurations($sorting_configurations);
    if (!usort($records, array($comparator, 'compare'))) {
        throw new Exception(t('Sort operation could not be completed'));
    }
}
