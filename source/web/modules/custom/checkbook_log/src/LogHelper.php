<?php
/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (c) 2012 – 2023 New York City
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Drupal\checkbook_log;

class LogHelper {
    const LEVEL_DEBUG = 'Debug';
    const LEVEL_INFO = 'Info';
    const LEVEL_NOTICE = 'Notice';
    const LEVEL_WARNING = 'Warning';
    const LEVEL_ERROR = 'Error';
    const LEVEL_CRITICAL = 'Critical';
    const LEVEL_ALERT = 'Alert';
    const LEVEL_EMERGENCY = 'Emergency';

    public static function log_emergency($message) {
      \Drupal::logger('NYC Checkbook')->emergency($message);
    }

    public static function log_alert($message) {
      \Drupal::logger('NYC Checkbook')->alert($message);
    }

    public static function log_critical($message) {
      \Drupal::logger('NYC Checkbook')->critical($message);
    }

    public static function log_error($message) {
      \Drupal::logger('NYC Checkbook')->error($message);
    }

    public static function log_warn($message) {
      \Drupal::logger('NYC Checkbook')->warning($message);
    }

    public static function log_notice($message) {
      \Drupal::logger('NYC Checkbook')->notice('<pre><code>' . print_r($message, TRUE) . '</code></pre>');
    }

    public static function log_info($message) {
      \Drupal::logger('NYC Checkbook')->info('<pre><code>' . print_r($message, TRUE) . '</code></pre>');
    }

    public static function log_debug($message) {
      \Drupal::logger('NYC Checkbook')->debug('<pre><code>' . print_r($message, TRUE) . '</code></pre>');
    }

    public function log($level, $message, array $context = array()) {
        //$isOriginalMessagePresent = isset($message);
    }
}
