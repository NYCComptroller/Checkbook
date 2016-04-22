<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 3/11/16
 * Time: 11:17 AM
 */

/* Concrete Factories */

class CheckFactory extends AbstractCheckFactory
{
    public function create(ICheck $check, $data)
    {
        $entities = array();
        while ($obj = $data->fetchObject('DBCheck')) {
            $this->createdCheck = new $check();
            $this->createdCheck->populate($obj);
            $entities[] = $this->createdCheck->asArray();
        }

        return $entities;
    }
}