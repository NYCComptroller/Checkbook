<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 4/8/16
 * Time: 4:10 PM
 */

class EntityFactory extends AbstractEntityFactory{

    public function create($data)
    {
        $result = $data->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
} 