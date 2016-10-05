<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 4/8/16
 * Time: 4:10 PM
 */

abstract class AbstractEntityFactory{
    protected $entity;
    abstract public function create($data);
}