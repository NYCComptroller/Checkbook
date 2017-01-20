<?php

class SqlDatasetFactory extends EntityFactory {

    /**
     * @param DatabaseStatementBase $data
     * @return array
     */
    public function create(DatabaseStatementBase $data)
    {
        $result = $data->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
}