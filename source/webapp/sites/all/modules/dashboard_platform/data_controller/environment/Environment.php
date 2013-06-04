<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
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
