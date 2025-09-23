<?php
/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (c) 2012 – 2023 New York City
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Drupal\checkbook_services\Widget;

interface IWidgetService {

    /**
     * Returns the legacy node id
     * @return mixed
     */
    function getLegacyNodeId();

    /**
     * Returns the widget data
     * @param $parameters
     * @param $limit
     * @param $orderBy
     * @return mixed
     */
    function getWidgetData($parameters, $limit, $orderBy);

    /**
     * Returns records from specified configured datasource
     * Returns total number of records for the widget
     * @param $parameters
     * @return mixed
     */
    function getWidgetDataCount($parameters);

    /**
     * Returns count for widget header using specified datasource or default row count
     * @param $parameters
     * @return mixed
     */
    function getWidgetHeaderCount($parameters);

    /**
     * Function to be overridden by implementing class to apply customized formatting to the data
     * @param $column_name
     * @param $row
     * @return mixed
     */
    function implementDerivedColumn($column_name,$row);

    /**
     * Function will convert the order by to a SQL clause
     * @return string
     */
    function prepareOrderBy();

    /**
     * Function will populate the parameter array with values that
     * exist in both the list of validUrlParameters and the query string
     * @return array
     */
    function prepareInputParameters();
    function getParamName();

}
