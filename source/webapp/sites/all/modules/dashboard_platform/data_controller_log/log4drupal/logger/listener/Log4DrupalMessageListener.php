<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
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
