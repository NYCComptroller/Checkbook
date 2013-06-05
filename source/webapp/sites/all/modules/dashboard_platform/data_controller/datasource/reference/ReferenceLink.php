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


// FIXME support bidirectional link for a link and its nested links. That would simplify code in assembleConnectedDatasetSourceStatement() and other places
class ReferenceLink extends AbstractObject {

    protected static $linkIdentifierSequence = 0;
    public $linkId = NULL;

    /**
     * @var ReferenceLink[]
     */
    public $nestedLinks = NULL;

    /**
     * @var DatasetMetaData
     */
    public $dataset = NULL;
    // column names from this dataset which are used to link with parent dataset
    public $columnNames = NULL;
    // column names from parent dataset which are used to link with this dataset
    public $parentColumnNames = NULL;

    // list of references which produced this link
    public $referenceNames = NULL;

    // this reference link is either directly required (TRUE) or transitional (FALSE)
    public $required = FALSE;
    // execution weight for this link
    public $weight = NULL;
    // execution rotes the link participate in
    public $routes = NULL;

    public function __construct($dataset) {
        parent::__construct();
        // generating unique link identifier
        self::$linkIdentifierSequence++;
        $this->linkId = self::$linkIdentifierSequence;

        $this->dataset = $dataset;
    }

    public function isRoot() {
        return !isset($this->parentColumnNames);
    }

    public function registerNestedLink(ReferenceLink $nestedLink) {
        $this->nestedLinks[$nestedLink->linkId] = $nestedLink;
    }

    public function mergeNestedLink(ReferenceLink $nestedLink) {
        $suitableLink = $this->isMergeable($nestedLink);
        if ($suitableLink === FALSE) {
            $this->registerNestedLink($nestedLink);
        }
        else {
            // combining reference names
            ArrayHelper::addUniqueValues($suitableLink->referenceNames, $nestedLink->referenceNames);
            // combining nested links
            if (isset($nestedLink->nestedLinks)) {
                foreach ($nestedLink->nestedLinks as $childNeestedLink) {
                    $suitableLink->mergeNestedLink($childNeestedLink);
                }
            }
        }
    }

    /**
     * @param ReferenceLink $nestedLink
     * @return bool|ReferenceLink
     */
    protected function isMergeable(ReferenceLink $nestedLink) {
        $possiblyMatchedLinks = $this->nestedLinks;

        // checking dataset property
        if (count($possiblyMatchedLinks) > 0) {
            foreach ($possiblyMatchedLinks as $i => $matchableLink) {
                $isMatched = $matchableLink->dataset->name == $nestedLink->dataset->name;

                if (!$isMatched) {
                    unset($possiblyMatchedLinks[$i]);
                }
            }
        }

        // checking parent column names and their indexes
        if (count($possiblyMatchedLinks) > 0) {
            $parentColumnCount = count($nestedLink->parentColumnNames);
            foreach ($possiblyMatchedLinks as $i => $matchableLink) {
                $isMatched = count($matchableLink->parentColumnNames) == $parentColumnCount;

                if ($isMatched) {
                    foreach ($nestedLink->parentColumnNames as $parentColumnIndex => $parentColumnName) {
                        if (!isset($matchableLink->parentColumnNames[$parentColumnIndex])
                                || ($matchableLink->parentColumnNames[$parentColumnIndex] != $parentColumnName)) {
                            $isMatched = FALSE;
                            break;
                        }
                    }
                }

                if (!$isMatched) {
                    unset($possiblyMatchedLinks[$i]);
                }
            }
        }

        // checking if columns to join with parent are the same
        if (count($possiblyMatchedLinks) > 0) {
            $columnCount = count($nestedLink->columnNames);
            foreach ($possiblyMatchedLinks as $i => $matchableLink) {
                $isMatched = count($matchableLink->columnNames) == $columnCount;

                if ($isMatched) {
                    foreach ($nestedLink->columnNames as $columnIndex => $columnName) {
                        if (!isset($matchableLink->columnNames[$columnIndex])
                                || ($matchableLink->columnNames[$columnIndex] != $columnName)) {
                            $isMatched = FALSE;
                            break;
                        }
                    }
                }

                if (!$isMatched) {
                    unset($possiblyMatchedLinks[$i]);
                }
            }
        }

        $matchedLinkCount = count($possiblyMatchedLinks);
        if ($matchedLinkCount > 1) {
            throw new IllegalStateException(t('Found several identical nested links'));
        }
        elseif ($matchedLinkCount == 1) {
            return reset($possiblyMatchedLinks);
        }

        return FALSE;
    }

    public function unregisterNestedLink(ReferenceLink $nestedLink) {
        if (!isset($this->nestedLinks[$nestedLink->linkId])) {
            throw new IllegalArgumentException(t(
                'Reference @datasetName(@columnNames) had not been registerd with @parentDatasetName(@parentColumnNames)',
                array(
                     '@datasetName' => $nestedLink->dataset->publicName, '@columnNames' => ArrayHelper::printArray($nestedLink->columnNames, ',', FALSE, FALSE),
                     '@parentDatasetName' => $this->dataset->publicName, '@parentColumnNames' => ArrayHelper::printArray($nestedLink->parentColumnNames, ',', FALSE, FALSE))));
        }

        unset($this->nestedLinks[$nestedLink->linkId]);

        if (count($this->nestedLinks) === 0) {
            $this->nestedLinks = NULL;
        }
    }

    public function findNestedLinkByDatasetName($datasetName) {
        $selectedNestedLink = NULL;

        if (isset($this->nestedLinks)) {
            foreach ($this->nestedLinks as $nestedLink) {
                if ($nestedLink->dataset->name === $datasetName) {
                    if (isset($selectedNestedLink)) {
                        throw new UnsupportedOperationException(t(
                            'Found several nested reference links for the dataset: @datasetName',
                            array('@datasetName' => $datasetName)));
                    }

                    $selectedNestedLink = $nestedLink;
                }
            }
        }

        return $selectedNestedLink;
    }

    public function findNestedLinkByDatasetNameAndParentColumnNames($datasetName, $parentColumnNames) {
        $selectedNestedLink = NULL;

        if (isset($this->nestedLinks)) {
            $parentColumnCount = count($parentColumnNames);
            foreach ($this->nestedLinks as $nestedLink) {
                if ($nestedLink->dataset->name !== $datasetName) {
                    continue;
                }

                $nestedLinkParentColumnCount = count($nestedLink->parentColumnNames);
                if ($nestedLinkParentColumnCount != $parentColumnCount) {
                    continue;
                }

                foreach ($nestedLink->parentColumnNames as $parentColumnIndex => $nestedLinkParentColumnName) {
                    if (!isset($parentColumnNames[$parentColumnIndex])) {
                        continue 2;
                    }

                    if ($nestedLinkParentColumnName != $parentColumnNames[$parentColumnIndex]) {
                        continue 2;
                    }
                }

                if (isset($selectedNestedLink)) {
                    throw new UnsupportedOperationException(t(
                        "Found several nested reference links for '@datasetName' dataset by the parent columns: @parentColumns",
                        array(
                            '@datasetName' => $datasetName,
                            '@parentColumns' => ArrayHelper::printArray($parentColumnNames, ', ', TRUE, FALSE))));
                }

                $selectedNestedLink = $nestedLink;
            }
        }

        return $selectedNestedLink;
    }

    public function linkColumnWithParent($referencePointColumnIndex, $parentColumnName, $columnName) {
        if (isset($this->parentColumnNames[$referencePointColumnIndex])) {
            throw new UnsupportedOperationException(t(
                "'@datasetName' dataset had been connected with parent column: @referencePointColumnIndex-[@parentColumnNameA vs @parentColumnNameB]",
                array(
                    '@datasetName' => $this->dataset->publicName, '@referencePointColumnIndex' => $referencePointColumnIndex,
                    '@parentColumnNameA' => $this->parentColumnNames[$referencePointColumnIndex], '@parentColumnNameB' => $parentColumnName)));
        }
        if (isset($this->columnNames[$referencePointColumnIndex])) {
            throw new UnsupportedOperationException(t(
                "'@datasetName' dataset had been connected with parent using @referencePointColumnIndex-[@columnNameA vs @columnNameB] column",
                array(
                    '@datasetName' => $this->dataset->publicName, '@referencePointColumnIndex' => $referencePointColumnIndex,
                    '@columnNameA' => $this->columnNames[$referencePointColumnIndex], '@columnNameB' => $columnName)));
        }

        $this->parentColumnNames[$referencePointColumnIndex] = $parentColumnName;
        $this->columnNames[$referencePointColumnIndex] = $columnName;
    }

    protected function getConnectedNestedDatasetNames($includeTransitional) {
        $nestedDatasetNames = NULL;

        if (isset($this->nestedLinks)) {
            foreach ($this->nestedLinks as $nestedLink) {
                if ($nestedLink->required || $includeTransitional) {
                    ArrayHelper::addUniqueValue($nestedDatasetNames, $nestedLink->dataset->name);
                }

                ArrayHelper::addUniqueValues($nestedDatasetNames, $nestedLink->getConnectedNestedDatasetNames($includeTransitional));
            }
        }

        return $nestedDatasetNames;
    }

    public function getConnectedDatasetNames($includeRoot = TRUE, $includeTransitional = TRUE) {
        $datasetNames = NULL;

        if ($includeRoot) {
            ArrayHelper::addUniqueValue($datasetNames, $this->dataset->name);
        }

        ArrayHelper::addUniqueValues($datasetNames, $this->getConnectedNestedDatasetNames($includeTransitional));

        return $datasetNames;
    }

    public function getMaximumColumnCount() {
        $count = isset($this->columnNames) ? count($this->columnNames) : NULL;

        if (isset($this->nestedLinks)) {
            foreach ($this->nestedLinks as $nestedLink) {
                $count = MathHelper::max($count, $nestedLink->getMaximumColumnCount());
            }
        }

        return $count;
    }

    /**
     * @param $referencePath
     * @param null $parentColumnName
     * @return ReferenceRoute[]
     */
    public function prepareRoutes($referencePath, $parentColumnName = NULL) {
        $routes = NULL;

        $referenceParts = ReferencePathHelper::splitReference($referencePath);

        $columnName = array_pop($referenceParts);
        $datasetName = array_pop($referenceParts);

        // checking that dataset is the same and parent column name is the same
        $isReferenceFound = ($this->dataset->name == $datasetName)
            && (!isset($parentColumnName) || (array_search($parentColumnName, $this->parentColumnNames) !== FALSE));

        $nestedReferencePath = $isReferenceFound
            ? ReferencePathHelper::assembleSplitReferenceParts($referenceParts)
            : $referencePath; // it is possible that not full execution path is provided

        if (isset($nestedReferencePath)) {
            if (isset($this->nestedLinks)) {
                $nestedRoutes = NULL;
                foreach ($this->nestedLinks as $nestedLink) {
                    $nestedLinkRoutes = $nestedLink->prepareRoutes($nestedReferencePath, ($isReferenceFound ? $columnName : NULL));
                    if (isset($nestedLinkRoutes)) {
                        // current reference could not be found. We mark nested found reference as not direct
                        if (!$isReferenceFound && isset($nestedLinkRoutes[TRUE])) {
                            $adjustedNestedLinkRoutes = null;
                            $adjustedNestedLinkRoutes[FALSE] = $nestedLinkRoutes[TRUE];
                            $nestedRoutes[] = $adjustedNestedLinkRoutes;
                        }
                        else {
                            $nestedRoutes[] = $nestedLinkRoutes;
                        }
                    }
                }

                if (isset($nestedRoutes)) {
                    // checking if we found direct route
                    $isNestedReferenceFound = FALSE;
                    foreach ($nestedRoutes as $nestedLinkRoutes) {
                        if (isset($nestedLinkRoutes[TRUE])) {
                            $isNestedReferenceFound = TRUE;
                            break;
                        }
                    }

                    foreach ($nestedRoutes as $nestedLinkRoutes) {
                        if (!isset($nestedLinkRoutes[$isNestedReferenceFound])) {
                            continue;
                        }

                        foreach ($nestedLinkRoutes[$isNestedReferenceFound] as $nestedLinkRoute) {
                            $route = new ReferenceRoute($this->linkId, $this->weight);
                            $route->combineFrom($nestedLinkRoute);

                            $routes[$isNestedReferenceFound][] = $route;
                        }
                    }
                }
            }
        }
        elseif ($isReferenceFound) {
            $routes[$isReferenceFound][] = new ReferenceRoute($this->linkId, $this->weight);
        }

        return $routes;
    }

    public function assignRouteId(ReferenceRoute $route, $routeId, $level = 0) {
        $this->joinRoute($routeId);

        $possibleNextLevel = $level + 1;
        if (isset($route->segmentLinkIds[$possibleNextLevel])) {
            $nestedSegmentLinkId = $route->segmentLinkIds[$possibleNextLevel];
            $nestedLink = $this->nestedLinks[$nestedSegmentLinkId];
            $nestedLink->assignRouteId($route, $routeId, $possibleNextLevel);
        }
    }

    protected function joinRoute($routeId) {
        $this->routes[] = $routeId;
    }
}

class ReferenceRoute extends AbstractObject {

    public $segmentLinkIds = NULL;
    public $weight = NULL;

    public function __construct($rootLinkId, $rootWeight) {
        parent::__construct();
        $this->segmentLinkIds[] = $rootLinkId;
        $this->weight = $rootWeight;
    }

    public function __clone() {
        parent::__clone();
        $this->segmentLinkIds = ArrayHelper::cloneArray($this->segmentLinkIds);
    }

    public function combineFrom(ReferenceRoute $route) {
        ArrayHelper::mergeArrays($this->segmentLinkIds, $route->segmentLinkIds);
        $this->weight += $route->weight;
    }
}
