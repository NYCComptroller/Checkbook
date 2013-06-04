<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
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
