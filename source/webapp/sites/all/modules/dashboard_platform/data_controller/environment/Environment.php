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


class Environment extends AbstractSingleton {

    public static $CONFIGURATION_SECTION_NAME = 'Dashboard Platform';

    public static $LOCALE__DEFAULT = NULL;

    private static $instance = NULL;

    private $locale = NULL;
    private $numericFormattingConfiguration = NULL;

    /**
     * @static
     * @return Environment
     */
    public static function getInstance() {
        if (!isset(self::$instance)) {
            self::$instance = new Environment();
            setlocale(LC_ALL, self::$instance->getLocale());
        }

        return self::$instance;
    }

    public function getConfigurationSection($sectionName) {
        global $conf;

        return isset($conf[self::$CONFIGURATION_SECTION_NAME][$sectionName])
            ? $conf[self::$CONFIGURATION_SECTION_NAME][$sectionName]
            : NULL;
    }

    public function isWindows() {
        return defined('PHP_WINDOWS_VERSION_MAJOR');
    }

    public function getLocale() {
        if (!isset($this->locale)) {
            if (isset(self::$LOCALE__DEFAULT)) {
                $this->locale = self::$LOCALE__DEFAULT;
            }
            else {
                global $conf;
                global $language;

                if(!isset($conf['site_default_country'])){
                    $conf['site_default_country'] = 'us';
                }

                $country = isset($conf['site_default_country']) ? trim($conf['site_default_country']) : '';
                if ($country === '') {
                    throw new IllegalStateException(t("Default country was not defined. Go to 'Configuration' | 'Regional and language' | 'Regional settings' to set default country"));
                }

                $localeSubTags = array('language' => $language->language, 'region' => $country);
                $locale = Locale::composeLocale($localeSubTags);

                if ($this->isWindows()) {
                    // the following line was not needed on Windows XP
                    // if we do not include it on Windows 7 localeconv() would return empty value for almost all properties
                    $uselessLocale = setlocale(LC_ALL, NULL);

                    // It is expected to get en-US instead of en_US on Windows
                    $locale = str_replace('_', '-', $locale);
                }

                $this->locale = $locale;
            }
        }

        return $this->locale;
    }

    public function getNumericFormattingElement($elementName, $required = TRUE) {
        if (!isset($this->numericFormattingConfiguration)) {
            $this->numericFormattingConfiguration = localeconv();
        }

        $elementValue = isset($this->numericFormattingConfiguration[$elementName])
            ? StringHelper::trim($this->numericFormattingConfiguration[$elementName])
            : NULL;
        if (!isset($elementValue) && $required) {
            throw new IllegalStateException(t(
                'Numeric formatting information does not contain value for the element: @elementName',
                array('@elementName' => $elementName)));
        }

        return $elementValue;
    }
}
