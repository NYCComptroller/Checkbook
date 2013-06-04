<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class StreamDataProvider extends AbstractDataProvider {

    private $buffer = NULL;
    private $bufferSize = 0;
    private $index = 0;

    public function __construct($buffer) {
        parent::__construct();
        $this->setBuffer($buffer);
    }

    protected function setBuffer($buffer) {
        $this->buffer = $buffer;
        $this->bufferSize = strlen($this->buffer);
    }

    public function openResource() {
        LogHelper::log_notice(t('Parsing data from a buffer (size: @bufferSize) ...', array('@bufferSize' => $this->bufferSize)));

        $result = parent::openResource();

        if ($result) {
            $this->index = 0;
        }

        return $result;
    }

    public function readLineFromResource() {
        $this->incrementLineNumber();

        $delimiterSize = 1;

        $i = 0;

        $n = strpos($this->buffer, "\n", $this->index);
        $r = strpos($this->buffer, "\r", $this->index);
        if ($n === FALSE) {
            if ($r === FALSE) {
                // last line in the stream
                if (($this->index + 1) < $this->bufferSize) {
                    $s = substr($this->buffer, $this->index);
                    $this->index = $this->bufferSize;

                    return $s;
                }
                else {
                    return FALSE;
                }
            }
            else {
                // continue with just $r
                $i = $r;
            }
        }
        else {
            if ($r === FALSE) {
                // continue with just $n
                $i = $n;
            }
            else {
                // both delimiters are set
                if (($r + 1) == $n) {
                    // it is one delimiter \r\n
                    $delimiterSize = 2;

                    $i = $r;
                }
                else {
                    $i = MathHelper::min($r, $n);
                }
            }
        }

        $line = ($i == $this->index)
            ? ''
            : substr($this->buffer, $this->index, $i - $this->index);

        $this->index = $i + $delimiterSize;

        return $line;
    }
}
