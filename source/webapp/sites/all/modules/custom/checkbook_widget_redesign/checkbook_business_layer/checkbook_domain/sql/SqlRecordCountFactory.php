<?php

class SqlRecordCountFactory extends EntityFactory {

    /**
     * @param DatabaseStatementBase $data
     * @return int
     */
    public function create(DatabaseStatementBase $data)
    {
        $results = $data->fetchAll();
        return $results[0]->record_count;
    }
} 