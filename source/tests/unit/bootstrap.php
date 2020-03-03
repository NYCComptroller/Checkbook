<?php

global $conf;
$conf['CHECKBOOK_ENV'] = 'PHPUNIT';
$conf['email_from'] = 'test@example.com';
$conf['checkbook_dev_group_email'] = 'test@example.com';

/**
 *
 */
define('PHPUNIT_RUNNING', true);
/**
 *
 */
define('CUSTOM_MODULES_DIR', realpath(__DIR__ . '/../../webapp/sites/all/modules/custom/'));

include_once 'vendor/autoload.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/util/MappingUtil.php';

/**
 * @param string $name
 * @return mixed
 */
function variable_get($name = 'fake')
{
    global $mocked_variable;
    return $mocked_variable[$name];
}

/**
 * @param $name
 * @param $value
 */
function variable_set($name, $value)
{
    global $mocked_variable;
    $mocked_variable[$name] = $value;
}

/**
 * @return mixed
 */
function current_path()
{
    global $mock_current_path;
    return $mock_current_path;
}

/**
 * @param $a
 * @param $b
 * @param $c
 * @param $d
 * @param $e
 * @param int $f
 * @param int $g
 * @param int $h
 * @return bool
 */
function drupal_mail($a, $b, $c, $d, $e, $f = 1, $g = 2, $h = 3)
{
    return true;
}

/**
 * @param int $a
 * @param int $b
 * @param int $c
 * @return bool
 */
function module_load_include($a = 0, $b = 1, $c = 2)
{
    return true;
}

/**
 * Class DefaultMailSystem
 */
class DefaultMailSystem
{
}

/**
 *
 */
define('MENU_NORMAL_ITEM', true);
/**
 *
 */
define('MENU_DEFAULT_LOCAL_TASK', true);
/**
 *
 */
define('MENU_LOCAL_TASK', true);
/**
 *
 */
define('MENU_CALLBACK', true);

/**
 * @param int $a
 * @param int $b
 * @return bool
 */
function drupal_get_path($a = 1, $b = 2)
{
    return true;
}

/**
 * @param int $a
 * @return bool
 */
function node_load($a = 1)
{
    return true;
}

/**
 * @param int $a
 * @return bool
 */
function node_view($a = 1)
{
    return true;
}

/**
 * @param int $a
 * @return int
 */
function t($a = 1)
{
    return $a;
}

/**
 * @param int $a
 * @return int
 */
function drupal_get_form($a = 1)
{
    return [$a];
}

/**
 * @param array $a
 * @return array
 */
function drupal_map_assoc($a = [])
{
    return $a;
}

/**
 * Class FakeDataController
 */
class FakeDataController
{
    /**
     *
     */
    public function queryDataset()
    {
    }
}

/**
 * @param int $a
 * @return int
 */
function data_controller_get_instance($a = 1)
{
    return new FakeDataController();
}

/**
 *
 */
function log_error()
{
}

/**
 * @param $a
 */
function _get_contract_includes_subvendors_data_test($a)
{
}

/**
 *
 */
function data_controller_get_operator_factory_instance()
{
}

/**
 * Class LogHelper
 */
class LogHelper
{
    /**
     * @param $text
     */
    static function log_warn($text)
    {
        echo $text;
    }

    /**
     * @param $text
     */
    static function log_info($text)
    {
//        echo $text;
    }

    /**
     * @param $text
     */
    static function log_notice($text)
    {
//        echo $text;
    }
}

/**
 * @param $query
 * @return array
 */
function _checkbook_project_execute_sql_test($query)
{
    $return = [];
    $return['total'] = 777555333111;
    $return['acco_approved'] = 111;
    $return['acco_reviewing'] = 888;
    $return['acco_rejected'] = 999;
    $return['acco_cancelled'] = 222;
    $return['acco_submitted'] = 333;
    $return['total_gross_pay'] = 123456789000;
    $return['total_base_pay'] = 987654321000;
    $return['total_overtime_pay'] = 192837465000;

    return [$return];
}

/**
 * @param $q
 * @return mixed
 */
function drupal_get_path_alias($q)
{
    return $q;
}

/**
 * @param $a
 * @param $vars
 * @return mixed
 */
function theme($a, $vars)
{
    return $vars;
}

include_once CUSTOM_MODULES_DIR . '/checkbook_project/includes/checkbook_project.inc';
