<?php

include_once 'vendor/autoload.php';

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
function drupal_mail($a, $b, $c, $d, $e, $f=1, $g=2, $h=3) {
    return true;
}

/**
 * @param int $a
 * @param int $b
 * @param int $c
 * @return bool
 */
function module_load_include($a=0, $b=1, $c=2)
{
    return true;
}

/**
 * Class DefaultMailSystem
 */
class DefaultMailSystem{}

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
function drupal_get_path($a=1, $b=2)
{
    return true;
}

/**
 * @param int $a
 * @return bool
 */
function node_load($a=1)
{
    return true;
}

/**
 * @param int $a
 * @return bool
 */
function node_view($a=1)
{
    return true;
}

/**
 * @param int $a
 * @return int
 */
function t($a=1)
{
    return $a;
}

/**
 * @param int $a
 * @return int
 */
function drupal_get_form($a=1)
{
    return [$a];
}

/**
 * @param array $a
 * @return array
 */
function drupal_map_assoc($a=[])
{
    return $a;
}