<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class LogHelper extends AbstractFactory {

    const LEVEL_DEBUG = 'Debug';
    const LEVEL_INFO = 'Info';
    const LEVEL_NOTICE = 'Notice';
    const LEVEL_WARNING = 'Warning';
    const LEVEL_ERROR = 'Error';
    const LEVEL_CRITICAL = 'Critical';
    const LEVEL_ALERT = 'Alert';
    const LEVEL_EMERGENCY = 'Emergency';

    private static $factory = NULL;

    protected $messageListeners = NULL;

    protected function __construct() {
        parent::__construct();

        $this->initializeMessageListeners();
    }

    /**
     * @static
     * @return LogHelper
     */
    protected static function getInstance() {
        if (!isset(self::$factory)) {
            self::$factory = new LogHelper();
        }

        return self::$factory;
    }

    protected function initializeMessageListeners() {
        $this->messageListeners = array();

        $listenerConfigurations = module_invoke_all('dc_log_message_listener');
        foreach ($listenerConfigurations as $listenerConfiguration) {
            $classname = $listenerConfiguration['classname'];

            // recommended priority value:
            //     -10000 ..  -501: collect info
            //       -500 ..    -1: message modification
            //          0 .. 10000: logging into storage
            $priority = isset($listenerConfiguration['priority']) ? $listenerConfiguration['priority'] : 0;

            $this->messageListeners[$priority][] = new $classname();
        }

        ksort($this->messageListeners);
    }

    public static function log_emergency($message) {
        LogHelper::getInstance()->log(self::LEVEL_EMERGENCY, $message);
    }

    public static function log_alert($message) {
        LogHelper::getInstance()->log(self::LEVEL_ALERT, $message);
    }

    public static function log_critical($message) {
        LogHelper::getInstance()->log(self::LEVEL_CRITICAL, $message);
    }

    public static function log_error($message) {
        LogHelper::getInstance()->log(self::LEVEL_ERROR, $message);
    }

    public static function log_warn($message) {
        LogHelper::getInstance()->log(self::LEVEL_WARNING, $message);
    }

    public static function log_notice($message) {
        LogHelper::getInstance()->log(self::LEVEL_NOTICE, $message);
    }

    public static function log_info($message) {
        LogHelper::getInstance()->log(self::LEVEL_INFO, $message);
    }

    public static function log_debug($message) {
        LogHelper::getInstance()->log(self::LEVEL_DEBUG, $message);
    }

    protected function log($level, $message) {
        $isOriginalMessagePresent = isset($message);

        foreach ($this->messageListeners as $listeners) {
            foreach ($listeners as $listener) {
                $listener->log($level, $message);

                // checking if the message was eliminated
                if ($isOriginalMessagePresent && !isset($message)) {
                    return;
                }
            }
        }
    }
}
