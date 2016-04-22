<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 3/11/16
 * Time: 11:16 AM
 */

/* Abstract Factory */

abstract class AbstractCheckFactory
{
    protected $createdCheck;
    abstract public function create(ICheck $check, $data);
}