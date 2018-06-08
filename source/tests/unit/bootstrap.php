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