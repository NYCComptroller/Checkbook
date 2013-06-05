<?php
/**
* This file is part of the Checkbook NYC financial transparency software.
* 
* Copyright (C) 2012, 2013 New York City
* 
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Affero General Public License as
* published by the Free Software Foundation, either version 3 of the
* License, or (at your option) any later version.
* 
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU Affero General Public License for more details.
* 
* You should have received a copy of the GNU Affero General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


abstract class AbstractDataProvider extends AbstractObject {

    private $buffer = NULL;
    private $transactionBuffer = NULL;
    private $currentLineNumber = 0;

    public function openResource() {
        $this->currentLineNumber = 0;

        return TRUE;
    }

    abstract protected function readLineFromResource();

    public function startReading() {}

    public function readLine() {
        // use data from internal buffer first
        if (count($this->buffer) > 0) {
            $line = array_shift($this->buffer);
        }
        else {
            do {
                $line = $this->readLineFromResource();
                // we reached end of data
                if ($line === FALSE) {
                    return FALSE;
                }
                else {
                    $line = trim($line);
                }
            }
            while (strlen($line) === 0);
        }

        $this->transactionBuffer[] = $line;

        return $line;
    }

    public function rollbackReading() {
        ArrayHelper::mergeArrays($this->buffer, $this->transactionBuffer);
    }

    public function endReading() {
        $this->transactionBuffer = NULL;
    }

    public function closeResource() {}

    public function getCurrentLineNumber() {
        return $this->currentLineNumber;
    }

    protected function incrementLineNumber() {
        $this->currentLineNumber++;
    }
}
