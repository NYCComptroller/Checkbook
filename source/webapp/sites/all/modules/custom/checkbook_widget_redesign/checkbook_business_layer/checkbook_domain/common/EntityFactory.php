<?php

abstract class EntityFactory {
    protected $entity;
    abstract public function create(DatabaseStatementBase $data);
}