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


class TextLogMessageTrimmer extends AbstractLogMessageListener {

//    public static $LOGGED_TEXT_LENGTH__MAXIMUM = 512; // NULL - logging string
    public static $LOGGED_TEXT_LENGTH__MAXIMUM = NULL; // NULL - logging string

    public function log($level, &$message) {
        if (is_string($message) && isset(self::$LOGGED_TEXT_LENGTH__MAXIMUM)) {
            $length = strlen($message);
            if ($length > self::$LOGGED_TEXT_LENGTH__MAXIMUM) {
                $message = substr($message, 0, self::$LOGGED_TEXT_LENGTH__MAXIMUM)
                    . t(' ... @trimmedCharacterLength more character(s)', array('@trimmedCharacterLength' => ($length - self::$LOGGED_TEXT_LENGTH__MAXIMUM)));
            }
        }
    }
}
