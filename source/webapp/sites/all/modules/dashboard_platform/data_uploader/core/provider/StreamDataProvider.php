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
