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
            $this->createdCheck->populateFromObject($obj);
            $entities[] = $this->createdCheck;
        }

        return $entities;
    }
}

//class OgeChecksFactory extends AbstractChecksFactory
//{
//    public function create(IChecks $checks, $data)
//    {
//        $this->$createdChecks = new $checks();
//        return($this->$createdChecks->getData());
//    }
//}
//
//class MwbeChecksFactory extends AbstractChecksFactory
//{
//    public function create(IChecks $checks, $data)
//    {
//        $this->$createdChecks = new $checks();
//        return($this->$createdChecks->getData());
//    }
//}
//
//class SubVendorChecksFactory extends AbstractChecksFactory
//{
//    public function create(IChecks $checks, $data)
//    {
//        $this->$createdChecks = new $checks();
//        return($this->$createdChecks->getData());
//    }
//}