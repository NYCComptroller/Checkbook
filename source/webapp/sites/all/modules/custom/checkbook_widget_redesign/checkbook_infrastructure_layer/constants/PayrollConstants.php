<?php

abstract class PayrollLandingPage {

    const NYC_LEVEL = "nyc_landing";
    const AGENCY_LEVEL = "agency_landing";
    const TITLE_LEVEL = "title_landing";

    public static function getCurrent() {

        $urlPath = $_GET['q'];
        $ajaxPath = $_SERVER['HTTP_REFERER'];
        $page = null;

        if(preg_match('/payroll/',$urlPath) || preg_match('/payroll/', $ajaxPath)) {
            if(preg_match('/agency_landing/',$urlPath) || preg_match('/agency_landing/', $ajaxPath)) {
                $page = self::AGENCY_LEVEL;
            }
            else if(preg_match('/title_landing/',$urlPath) || preg_match('/title_landing/', $ajaxPath)) {
                $page = self::TITLE_LEVEL;
            }
            else {
                $page = self::NYC_LEVEL;
            }
        }
        return $page;
    }
}