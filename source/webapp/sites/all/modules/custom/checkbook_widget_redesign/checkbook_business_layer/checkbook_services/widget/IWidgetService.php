<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 4/8/16
 * Time: 3:07 PM
 */

interface IWidgetService {

//    /**
//     * Function to initialize class variables
//     * @return mixed
//     */
//    function initialize();

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