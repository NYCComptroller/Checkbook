<?php

namespace Drupal\checkbook_infrastructure_layer\Constants\Common;

use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\Core\Url;

abstract class Datasource {

    const CITYWIDE = "checkbook";
    const OGE = "checkbook_oge";
    const NYCHA = "checkbook_nycha";
    const SOLR_CITYWIDE = 'citywide';
    const SOLR_EDC = 'edc';
    const SOLR_NYCHA = 'nycha';
    const EDC_TITLE = "NYCEDC";

  /**
   * @return string
   */
     public static function getCurrent(): string
     {
       $datasource = RequestUtilities::get(UrlParameter::DATASOURCE);
       return match ($datasource) {
         self::OGE => self::OGE,
         self::NYCHA => self::NYCHA,
         default => self::CITYWIDE,
       };
     }

  /**
   * @return bool
   */
    public static function isOGE(): bool
    {
        return (self::getCurrent() == Datasource::OGE || self::getCurrentSolrDatasource() == self::SOLR_EDC);
    }

  /**
   * @return bool
   */
    public static function isNYCHA(): bool
    {
        return (self::getCurrent() == Datasource::NYCHA || self::getCurrentSolrDatasource() == self::SOLR_NYCHA);
    }

    /**
     * @return string
     */
    public static function getCurrentSolrDatasource(): string
    {
      $url = Url::fromRoute("<current>")->toString();
      if (strpos($url, '/checkbook_nycha') || strpos($url, '/nycha') || self::getCurrent() == self::NYCHA) {
        return self::SOLR_NYCHA;
      }
      if (strpos($url, '/checkbook_oge') || strpos($url, '/edc') || self::getCurrent() == self::OGE) {
        return self::SOLR_EDC;
      }
      return self::SOLR_CITYWIDE;
    }

    /**
     * @return string
     */
    public static function getDatasourceMapBySolr(): string
    {
      $solr_datasource = DataSource::getCurrentSolrDatasource();
      return match ($solr_datasource) {
        self::SOLR_EDC => self::OGE,
        self::SOLR_NYCHA => self::NYCHA,
        default => self::CITYWIDE,
      };
    }

  /**
   * @return string
   */
    public static function getNYCHAUrl(): string
    {
        $nychaId = _checkbook_project_querydataset('checkbook_nycha:agency', array('agency_id'), array('agency_short_name' => 'HOUSING AUTH'));
        return (self::getCurrent() == Datasource::NYCHA) ? '/agency/' . $nychaId[0]['agency_id'] : '';
    }

  /**
   * @return mixed
   */
    public static function getNYCHAId(): mixed
    {
      $nychaId = _checkbook_project_querydataset('checkbook_nycha:agency', array('agency_id'), array('agency_short_name' => 'HOUSING AUTH'));
      return $nychaId[0]['agency_id'];
    }

  /**
   * @return mixed
   */
    public static function getEDCId(): mixed
    {
      $edcId = _checkbook_project_querydataset('checkbook_oge:agency', array('agency_id'), array('agency_short_name' => 'NYC EDC'));
      return $edcId[0]['agency_id'];
    }

  /**
   * @return mixed
   */
    public static function getEDCCode(): mixed
    {
      $edcCode = _checkbook_project_querydataset('checkbook_oge:agency', array('agency_code'), array('agency_short_name' => 'NYC EDC'));
      return $edcCode[0]['agency_code'];
    }
}
