<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 3/31/16
 * Time: 6:29 PM
 */

abstract class AbstractSqlModel implements ISqlModel {

    /**
     * @param SimpleXMLElement $xml
     * @return ISqlModel[]
     */
    public static function loadChildXElements(SimpleXMLElement $xml) {
        $childModels = array();
        if(isset(static::$childElements)) {
            foreach(static::$childElements as $childElement) {
                $childModels[] =  self::loadChildElements($xml,$childElement['xpath'],$childElement['class']);
            }
        }
        return $childModels;
    }

    /**
     * @param SimpleXMLElement $xml
     * @param $path
     * @param $modelClass
     * @return ISqlModel[]
     */
    private static function loadChildElements(SimpleXMLElement $xml,$path,$modelClass)
    {
        $childModels = array();
        foreach($xml->xpath($path) as $childXml) {
            $childModel = $modelClass::loadFromXml($childXml);
            $childModels[] = $childModel;
        }
        return $childModels;
    }

}