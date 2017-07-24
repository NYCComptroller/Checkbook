<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 4/1/16
 * Time: 11:47 AM
 */

interface ISqlModel {
    /**
     * @param SimpleXMLElement $xml
     * @return SqlExpressionModel
     */
    public static function loadFromXml(SimpleXMLElement $xml);

    /**
     * @param SimpleXMLElement $xml
     * @return ISqlModel[]
     */
    public static function loadChildXElements(SimpleXMLElement $xml);
}