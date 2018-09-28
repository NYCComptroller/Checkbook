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


class ReferenceLinkBuilder extends AbstractObject {

    public static $LINK_WEIGHT__to_LOOKUP = 1.0;
    public static $LINK_WEIGHT__from_LOOKUP = 2.3;
    public static $LINK_WEIGHT__PK_to_PK = 1.0;
    public static $LINK_WEIGHT__NON_PK_to_NON_PK = 2.8;

    public static function selectReferencedColumnNames(array $columnNames = NULL) {
        $referencePaths = NULL;

        if (isset($columnNames)) {
            foreach ($columnNames as $referencePath) {
                list($referencedDatasetName, $referencedColumnName) = ReferencePathHelper::splitReference($referencePath);
                if (!isset($referencedDatasetName)) {
                    continue;
                }

                $referencePaths[$referencePath] = TRUE;
            }
        }

        return $referencePaths;
    }

    public static function selectReferencedColumnNames4ReferenceLink(array $linkExecutionStack, array $columnNames = NULL) {
        if (!isset($columnNames)) {
            return NULL;
        }

        $selectedColumnNames = NULL;

        $linkExecutionStackSize = count($linkExecutionStack);

        foreach ($columnNames as $columnName) {
            $columnNameReferences = ReferencePathHelper::splitReferencePath($columnName);
            $referenceSegmentCount = count($columnNameReferences);

            for ($referenceSegmentIndex = $referenceSegmentCount - 1, $stackPreviousIndex = -1; $referenceSegmentIndex >= 0; $referenceSegmentIndex--) {
                $reference = $columnNameReferences[$referenceSegmentIndex];
                list($referencedDatasetName, $referencedColumnName) = ReferencePathHelper::splitReference($reference);

                if (isset($referencedDatasetName)) {
                    // looking for related link in stack
                    $foundInStack = FALSE;
                    for ($stackIndex = ($stackPreviousIndex + 1); ($stackIndex < $linkExecutionStackSize) && !$foundInStack; $stackIndex++) {
                        $stackLink = $linkExecutionStack[$stackIndex];
                        if ($referencedDatasetName != $stackLink->dataset->name) {
                            continue;
                        }

                        // checking parent column name
                        if (($stackIndex < $linkExecutionStackSize - 1) && ($referenceSegmentIndex > 0)) {
                            $nextStackLine = $linkExecutionStack[$stackIndex + 1];
                            // checking number of columns. It has to be single column
                            if (count($nextStackLine->parentColumnNames) > 1) {
                                break;
                            }

                            // checking if the parent column matches
                            if (array_search($referencedColumnName, $nextStackLine->parentColumnNames) === FALSE) {
                                break;
                            }
                        }

                        $stackPreviousIndex = $stackIndex;
                        $foundInStack = TRUE;
                    }
                    if (!$foundInStack) {
                        break;
                    }

                    // we did not reach end of the execution stack
                    if ($stackPreviousIndex < ($linkExecutionStackSize - 1)) {
                        continue;
                    }
                }
                else {
                    if ($linkExecutionStackSize > 1) {
                        // this column belongs to root link but we are working with nested link
                        break;
                    }
                }

                // original column name would be only the portion of the reference path
                $adjustedColumnName = ReferencePathHelper::assembleReferencePath(array_slice($columnNameReferences, $referenceSegmentIndex));
                $selectedColumnNames[$adjustedColumnName] = $referencedColumnName;
                break;
            }
        }

        return $selectedColumnNames;
    }

    public function prepareReferenceBranches($datasetName, array $referencePaths) {
        $metamodel = data_controller_get_metamodel();

        $dataset = $metamodel->getDataset($datasetName);

        $rootLink = new ReferenceLink($dataset);
        $rootLink->required = TRUE;

        // looking for all possible paths to reach referenced datasets
        foreach ($referencePaths as $referencePath => $required) {
            // each execution should have its own context
            $callcontext = new ReferenceCallContext();

            $this->prepareDatasetSequence($callcontext, $metamodel, $rootLink, $referencePath);
        }

        // optimizing linkage
        $this->optimizeLinks($rootLink);

        // selecting the shortest paths to link with all those datasets
        $weightMultiplier = $this->prepareWeightMultiplier($rootLink);
        $this->calculateLinkWeight($rootLink, $weightMultiplier);
        $this->detectRoutes($rootLink, $referencePaths);
        $this->removeUnroutedLinks($rootLink);

        return $rootLink;
    }

    protected function prepareDatasetSequence(ReferenceCallContext $callcontext, MetaModel $metamodel, ReferenceLink $link, $referencePath) {
        $referenceParts = ReferencePathHelper::splitReference($referencePath);

        $parentColumnName = array_pop($referenceParts);
        $parentDatasetName = array_pop($referenceParts);
        $referencedColumnName = $referencedDatasetName = NULL;

        $leftReferencePartCount = count($referenceParts);

        $nestedReferencePath = $referencePath;
        if ($link->dataset->name == $parentDatasetName) {
            // dataset is there and it is link's dataset
            if ($leftReferencePartCount == 0) {
                return;
            }

            // assembling new reference path
            $nestedReferencePath = ReferencePathHelper::assembleSplitReferenceParts($referenceParts);

            $referencedColumnName = array_pop($referenceParts);
            $referencedDatasetName = array_pop($referenceParts);

            if (!isset($referencedDatasetName)) {
                throw new UnsupportedOperationException(t(
                    'Dataset name is not set in the reference path: @referencePath',
                    array('@referencePath' => $referencePath)));
            }
        }
        elseif (isset($parentDatasetName)) {
            $referencedColumnName = $parentColumnName;
            $referencedDatasetName = $parentDatasetName;
            $parentColumnName = $parentDatasetName = NULL;
        }
        else {
            if ($leftReferencePartCount > 0) {
                throw new UnsupportedOperationException(t(
                    'Dataset name is not set in the reference path: @referencePath',
                    array('@referencePath' => $referencePath)));
            }

            // it means that we just point to column in link's dataset
            return;
        }

        // checking if there any references which could be used to find required dataset
        $references = $metamodel->findReferencesByDatasetName($link->dataset->name);
        if (!isset($references)) {
            return;
        }

        // checking what references can be used to proceed and
        $selectedReferences = NULL;
        foreach ($references as $reference) {
            if (isset($callcontext->referenceNameStack[$reference->name])) {
                continue;
            }

            $selectedReferences[$reference->name] = $reference;
        }
        if (!isset($selectedReferences)) {
            return;
        }

        // maximum number of columns in direct references
        $directReferencePointColumnCount = NULL;

        // checking if there is any reference which directly link to referenced dataset
        $parentReferencePointIndex4References = $directReferencePointIndex4References = $transitionalReferencePointIndexes4References = NULL;
        foreach ($selectedReferences as $reference) {
            $referencePointColumnCount = $reference->getPointColumnCount();

            // checking if the reference can be used to link with other datasets
            $parentReferencePointIndex4Reference = $directReferencePointIndex4Reference = $transitionalReferencePointIndexes4Reference = NULL;
            for ($referencePointColumnIndex = 0; $referencePointColumnIndex < $referencePointColumnCount; $referencePointColumnIndex++) {
                $parentReferencePointIndex = $directReferencePointIndex = $transitionalReferencePointIndexes = NULL;
                foreach ($reference->points as $referencePointIndex => $referencePoint) {
                    $datasetName = $referencePoint->columns[$referencePointColumnIndex]->datasetName;

                    if (($link->dataset->name == $datasetName)
                            && (!isset($parentColumnName) || ($parentColumnName == $referencePoint->columns[$referencePointColumnIndex]->columnName))) {
                        if (isset($parentReferencePointIndex)) {
                            // Found several possible ways to start a join from the referring dataset.
                            // That happens because we do not have parent column name and possible join is ambiguous
                            // We cannot use this way to proceed
                            continue 3;
                        }
                        else {
                            $parentReferencePointIndex = $referencePointIndex;
                        }
                    }
                    elseif ($datasetName == $referencedDatasetName) {
                        if (isset($directReferencePointIndex)) {
                            // found several possible ways to join with the referenced dataset
                            continue 3;
                        }
                        else {
                            $directReferencePointIndex[$referencePointIndex] = TRUE;
                        }
                    }
                    else {
                        $transitionalReferencePointIndexes[$referencePointIndex] = FALSE;
                    }
                }

                // this reference cannot be used because none of the reference points linked with parent dataset
                if (!isset($parentReferencePointIndex)) {
                    continue 2;
                }

                if (isset($directReferencePointIndex)) {
                    // if we have direct reference we do not care about indirect ones
                    $transitionalReferencePointIndexes = NULL;
                }
                else {
                    // there is no direct or indirect ways. This reference is useless :)
                    if (!isset($transitionalReferencePointIndexes)) {
                        continue 2;
                    }
                }

                $parentReferencePointIndex4Reference[$referencePointColumnIndex] = $parentReferencePointIndex;
                if (isset($directReferencePointIndex)) {
                    $directReferencePointIndex4Reference[$referencePointColumnIndex] = $directReferencePointIndex;
                }
                if (isset($transitionalReferencePointIndexes)) {
                    $transitionalReferencePointIndexes4Reference[$referencePointColumnIndex] = $transitionalReferencePointIndexes;
                }
            }

            // we support only direct references between datasets
            // in this case we have direct reference based on some columns only. Rest columns are connected indirectly
            if (isset($directReferencePointIndex4Reference) && isset($transitionalReferencePointIndexes4Reference)) {
                continue;
            }

            $parentReferencePointIndex4References[$reference->name] = $parentReferencePointIndex4Reference;
            if (isset($directReferencePointIndex4Reference)) {
                $directReferencePointIndex4References[$reference->name] = $directReferencePointIndex4Reference;
                $directReferencePointColumnCount = MathHelper::max($directReferencePointColumnCount, $referencePointColumnCount);
            }
            if (isset($transitionalReferencePointIndexes4Reference)) {
                $transitionalReferencePointIndexes4References[$reference->name] = $transitionalReferencePointIndexes4Reference;
            }
        }
        // we could use none of the selected references
        if (!isset($parentReferencePointIndex4References)) {
            return;
        }

        // removing all useless direct and indirect references if there is a direct way
        if (isset($directReferencePointColumnCount)) {
            foreach ($parentReferencePointIndex4References as $referenceName => $parentReferencePointIndex4Reference) {
                $referencePointColumnCount = count($parentReferencePointIndex4Reference);

                if (isset($directReferencePointIndex4References[$referenceName])) {
                    // we preserve only direct ways with maximum number of columns
                    if ($referencePointColumnCount == $directReferencePointColumnCount) {
                        continue;
                    }
                }
                else {
                    // we preserve only indirect ways with more columns than in direct way
                    if ($referencePointColumnCount > $directReferencePointColumnCount) {
                        continue;
                    }
                }

                unset($parentReferencePointIndex4References[$referenceName]);
                unset($directReferencePointIndex4References[$referenceName]);
                unset($transitionalReferencePointIndexes4References[$referenceName]);
            }
        }

        foreach ($parentReferencePointIndex4References as $referenceName => $parentReferencePointIndex4Reference) {
            $reference = $selectedReferences[$referenceName];

            // registering the reference in a stack to avoid excessive calls
            // registration was moved here because we could have date[->month->quarter->year] and year columns in one dataset
            // if references related to date and year are registered before we start to process individual references
            // we will end up with nested links for date->month->quarter->year which do not contain a reference to year
            // which leads to GOVDB-1313 issue
            $callcontext->referenceNameStack[$reference->name] = TRUE;

            $referencePointColumnCount = $reference->getPointColumnCount();

            $referencePointIndexes4Reference = isset($directReferencePointIndex4References[$referenceName])
                ? $directReferencePointIndex4References[$referenceName]
                : NULL;
            $isDirectReference = isset($referencePointIndexes4Reference);

            if (!$isDirectReference) {
                $referencePointIndexes4Reference = isset($transitionalReferencePointIndexes4References[$referenceName])
                    ? $transitionalReferencePointIndexes4References[$referenceName]
                    : NULL;
            }

            // preparing dataset names for each reference point
            $referencePointDatasetNames = NULL;
            for ($referencePointColumnIndex = 0; $referencePointColumnIndex < $referencePointColumnCount; $referencePointColumnIndex++) {
                foreach ($referencePointIndexes4Reference[$referencePointColumnIndex] as $referencePointIndex => $directReferencePointFlag) {
                    $referencePointColumn = $reference->points[$referencePointIndex]->columns[$referencePointColumnIndex];
                    $datasetName = $referencePointColumn->datasetName;

                    // it is expected that dataset name is the same for all columns in a reference point
                    if (isset($referencePointDatasetNames[$referencePointIndex])) {
                        if ($referencePointDatasetNames[$referencePointIndex] != $datasetName) {
                            // Dataset name is not the same for all columns for the reference point
                            $referencePointDatasetNames[$referencePointIndex] = FALSE;
                        }
                    }
                    else {
                        $referencePointDatasetNames[$referencePointIndex] = $datasetName;
                    }
                }
            }
            // removing all reference points which we cannot support now
            foreach ($referencePointDatasetNames as $referencePointIndex => $datasetName) {
                if ($datasetName === FALSE) {
                    unset($referencePointDatasetNames[$referencePointIndex]);
                }
            }
            // if nothing left there is not need to proceed
            if (count($referencePointDatasetNames) == 0) {
                continue;
            }

            // preparing list of parent column names
            $parentColumnNames = NULL;
            for ($referencePointColumnIndex = 0; $referencePointColumnIndex < $referencePointColumnCount; $referencePointColumnIndex++) {
                $parentReferencePointIndex = $parentReferencePointIndex4Reference[$referencePointColumnIndex];
                $parentReferencePointColumnName = $reference->points[$parentReferencePointIndex]->columns[$referencePointColumnIndex]->columnName;

                $parentColumnNames[$referencePointColumnIndex] = $parentReferencePointColumnName;
            }

            $referenceCallContext = $isDirectReference ? $callcontext : (clone $callcontext);

            // adding all indirect datasets in stack to prevent recursive calls
            if (!$isDirectReference) {
                foreach ($referencePointDatasetNames as $referencePointIndex => $datasetName) {
                    if (isset($referenceCallContext->datasetNameStack[$datasetName])) {
                        unset($referencePointDatasetNames[$referencePointIndex]);
                    }
                    else {
                        $referenceCallContext->datasetNameStack[$datasetName] = TRUE;
                    }
                }
            }

            foreach ($referencePointDatasetNames as $referencePointIndex => $datasetName) {
                // looking for existing link
                $referencedLink = $link->findNestedLinkByDatasetNameAndParentColumnNames($datasetName, $parentColumnNames);
                if (!isset($referencedLink)) {
                    $dataset = $metamodel->getDataset($datasetName);

                    $referencedLink = new ReferenceLink($dataset);
                    foreach ($parentColumnNames as $referencePointColumnIndex => $parentReferencePointColumnName) {
                        $referencePointColumn = $reference->points[$referencePointIndex]->columns[$referencePointColumnIndex];
                        $referencedLink->linkColumnWithParent($referencePointColumnIndex, $parentReferencePointColumnName, $referencePointColumn->columnName);
                    }

                    $link->registerNestedLink($referencedLink);
                }
                ArrayHelper::addUniqueValue($referencedLink->referenceNames, $referenceName);

                // marking the link as required for the branch so it will not be deleted by the optimizer later
                if ($isDirectReference) {
                    $referencedLink->required = TRUE;
                }

                // because this reference path is not processed completely we need to continue scanning this branch
                if (isset($nestedReferencePath)) {
                    $referencePointCallContext = clone $referenceCallContext;
                    $this->prepareDatasetSequence($referencePointCallContext, $metamodel, $referencedLink, $nestedReferencePath);
                }
            }
        }
    }

    protected function optimizeLinks(ReferenceLink $link) {
        // removoing useless transitional links
        //   1) transitional link uses the same column to link with parent as its children use to connect with the link
        if (isset($link->nestedLinks)) {
            foreach($link->nestedLinks as $childLink) {
                if ($childLink->required) {
                    continue;
                }

                if (!isset($childLink->nestedLinks)) {
                    continue;
                }

                foreach ($childLink->nestedLinks as $grandchildLink) {
                    // checking column names. Those have to be the same in order to proceed with the optimization
                    if (count($childLink->columnNames) !== count($grandchildLink->parentColumnNames)) {
                        continue;
                    }
                    // number of columns is the same. Checking column names
                    foreach ($childLink->columnNames as $columnName) {
                        if (array_search($columnName, $grandchildLink->parentColumnNames) === FALSE) {
                            continue 2;
                        }
                    }

                    $childLink->unregisterNestedLink($grandchildLink);

                    // ---------- updating the link to move it to grand parent node
                    // setting new parent
                    $grandchildParentColumnNames = $grandchildLink->parentColumnNames;
                    $grandchildLink->parentColumnNames = $childLink->parentColumnNames;
                    // rearranging linking columns
                    $grandchildColumnNames = $grandchildLink->columnNames;
                    $grandchildLink->columnNames = NULL;
                    foreach ($childLink->columnNames as $referencePointColumnIndex => $columnName) {
                        $grandchildReferencePointColumnIndex = array_search($columnName, $grandchildParentColumnNames);
                        $grandchildLink->columnNames[$referencePointColumnIndex] = $grandchildColumnNames[$grandchildReferencePointColumnIndex];
                    }
                    // combining reference names
                    ArrayHelper::addUniqueValues($grandchildLink->referenceNames, $childLink->referenceNames);

                    $link->mergeNestedLink($grandchildLink);
                }

                $this->optimizeLinks($childLink);
            }
        }
    }

    protected function prepareWeightMultiplier(ReferenceLink $link) {
        $maximumColumnCount = $link->getMaximumColumnCount();
        // if there is no joins with other datasets $maximumColumnCount would be NULL
        if (!isset($maximumColumnCount)) {
            $maximumColumnCount = 1;
        }

        $thresholdColumnCount = 7;
        $multiplier = 10.0 * $thresholdColumnCount / $maximumColumnCount;

        return $multiplier;
    }

    protected function calculateLinkWeight(ReferenceLink $link, $weightMultiplier, ReferenceLink $parentLink = NULL) {
        if (isset($parentLink)) {
            $weight = NULL;
            foreach ($link->columnNames as $referencePointColumnIndex => $columnName) {
                $column = $link->dataset->getColumn($columnName);

                $parentColumnName = $link->parentColumnNames[$referencePointColumnIndex];
                $parentColumn = $parentLink->dataset->getColumn($parentColumnName);

                // calculating the link weight
                $columnWeight = $parentColumn->isKey()
                    ? ($column->isKey() ? self::$LINK_WEIGHT__PK_to_PK : self::$LINK_WEIGHT__from_LOOKUP)
                    : ($column->isKey() ? self::$LINK_WEIGHT__to_LOOKUP : self::$LINK_WEIGHT__NON_PK_to_NON_PK);

                $weight = MathHelper::min($weight, $columnWeight);
            }
            // calculating the link weight and adjusting the valued based on multiplier
            $link->weight = $weight / pow($weightMultiplier, count($link->columnNames) - 1);
        }

        if (isset($link->nestedLinks)) {
            foreach ($link->nestedLinks as $nestedLink) {
                $this->calculateLinkWeight($nestedLink, $weightMultiplier, $link);
            }
        }
    }

    protected function detectRoutes(ReferenceLink $rootLink, array $referencePaths) {
        $routeId = 0;
        foreach ($referencePaths as $referencePath => $required) {
            // preparing possible routes
            $referenceRoutes = $rootLink->prepareRoutes($referencePath);

            // selecting 'the best' route
            $selectedRoute = NULL;
            if (isset($referenceRoutes)) {
                foreach ($referenceRoutes as $directReferenceFlag => $routes) {
                    foreach ($routes as $route) {
                        if (!isset($selectedRoute) || ($selectedRoute->weight > $route->weight)) {
                            $selectedRoute = $route;
                        }
                    }
                }
            }

            if (isset($selectedRoute)) {
                $rootLink->assignRouteId($selectedRoute, $routeId);
                $routeId++;
            }
            elseif ($required) {
                $metamodel = data_controller_get_metamodel();

                LogHelper::log_error(t('Could not execute reference path: @referencePath', array('@referencePath' => $referencePath)));

                list($referencedDatasetName) = ReferencePathHelper::splitReference($referencePath);
                $referencedDataset = $metamodel->getDataset($referencedDatasetName);

                throw new IllegalArgumentException(t(
                    "'@datasetNameA' and '@datasetNameB' datasets are not connected",
                    array('@datasetNameA' => $rootLink->dataset->publicName, '@datasetNameB' => $referencedDataset->publicName)));
            }
        }
    }

    protected function removeUnroutedLinks(ReferenceLink $link) {
        if (isset($link->nestedLinks)) {
            foreach ($link->nestedLinks as $nestedLinkId => $nestedLink) {
                $this->removeUnroutedLinks($nestedLink);

                if (isset($nestedLink->routes)) {
                    continue;
                }

                unset($link->nestedLinks[$nestedLinkId]);
            }
            if (count($link->nestedLinks) === 0) {
                $link->nestedLinks = NULL;
            }
        }
    }
}


class ReferenceCallContext extends AbstractCallContext {

    public $datasetNameStack = NULL;
    public $referenceNameStack = NULL;
}
