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


class Log4DrupalMessageListener extends AbstractLogMessageListener {

    public function log($level, &$message) {
        switch ($level) {
            case LogHelper::LEVEL_DEBUG:
                log_debug($message);
                break;
            case LogHelper::LEVEL_INFO:
                log_info($message);
                break;
            case LogHelper::LEVEL_NOTICE:
                log_notice($message);
                break;
            case LogHelper::LEVEL_WARNING:
                log_warn($message);
                break;
            case LogHelper::LEVEL_ERROR:
                log_error($message);
                break;
            case LogHelper::LEVEL_CRITICAL:
                log_critical($message);
                break;
            case LogHelper::LEVEL_ALERT:
                log_alert($message);
                break;
            case LogHelper::LEVEL_EMERGENCY:
                log_emergency($message);
                break;
        }
    }
}
