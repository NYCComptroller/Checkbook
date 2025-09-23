<?php
namespace Drupal\checkbook_api\API;

class CheckbookAPIRepository {

    /**
     * Function to select records from the checkbook_api table
     * (API requests statistics table)
     *
     * @param $api_id
     * @return mixed
     */
    public function getCheckbookAPI($api_id){
        $api = \Drupal::database()->query(
            "SELECT
            `api_id`,
            `domain`,
            `request_xml`,
            `response_status`,
            `response_log`,
            `client_ip`,
            `city`,
            `state`,
            `country`,
            `country_code`,
            `continent`,
            `continent_code`,
            `created_date`
            FROM {checkbook_api}
            WHERE `api_id` = :api_id", array(':api_id' => $api_id));

        $api_results = $api->fetchObject();
        return $api_results;


    }

    /**
     * Function to insert records into the checkbook_api table
     *
     * @param CheckbookAPIEntity $checkbookAPI
     * @return mixed
     * @throws
     */
    public function insertCheckbookAPI(CheckbookAPIEntity $checkbookAPI) {
        // Remove any null properties
        $fields = array_filter((array) $checkbookAPI);
        $api_id = \Drupal::database()->insert('checkbook_api')->fields($fields)->execute();
        return self::getCheckbookAPI($api_id);
    }

    /**
     * Function to update records in the checkbook_api table
     *
     * @param CheckbookAPIEntity $checkbookAPI
     * @return \DatabaseStatementInterface
     */
    public function updateCheckbookAPI(CheckbookAPIEntity $checkbookAPI) {
        // Remove any null properties
        $fields = array_filter((array) $checkbookAPI);

        $db_update_query =
            \Drupal::database()->update("checkbook_api")
                ->fields($fields)
                ->condition('api_id', $checkbookAPI->api_id);
        $rows_affected = $db_update_query->execute();
        return $rows_affected;
    }
}
