<?php
$dataln = count($node->data);
$empty = (object)array('name' => '', 'data' => array(0));
switch ($dataln) {
    case 2:
        $first = (object)array('name' => $node->data[0]['agency_agency_agency_name'], 'data' => array($node->data[0]['check_amount_sum']));
        $all = array($empty, $empty, $empty, $empty, $first, $empty);
        $node->widgetConfig->chartConfig->series = $all;
        break;
    case 3:
        $first = (object)array('name' => $node->data[1]['agency_agency_agency_name'], 'data' => array($node->data[1]['check_amount_sum']));
        $second = (object)array('name' => $node->data[0]['agency_agency_agency_name'], 'data' => array($node->data[0]['check_amount_sum']));
        $all = array($empty, $empty, $empty, $second, $first, $empty);
        $node->widgetConfig->chartConfig->series = $all;
        break;
    case 4:
        $first = (object)array('name' => $node->data[2]['agency_agency_agency_name'], 'data' => array($node->data[2]['check_amount_sum']));
        $second = (object)array('name' => $node->data[1]['agency_agency_agency_name'], 'data' => array($node->data[1]['check_amount_sum']));
        $third = (object)array('name' => $node->data[0]['agency_agency_agency_name'], 'data' => array($node->data[0]['check_amount_sum']));
        $all = array($empty, $empty, $third, $second, $first, $empty);
        $node->widgetConfig->chartConfig->series = $all;
        break;
    case 5:
        $first = (object)array('name' => $node->data[3]['agency_agency_agency_name'], 'data' => array($node->data[3]['check_amount_sum']));
        $second = (object)array('name' => $node->data[2]['agency_agency_agency_name'], 'data' => array($node->data[2]['check_amount_sum']));
        $third = (object)array('name' => $node->data[1]['agency_agency_agency_name'], 'data' => array($node->data[1]['check_amount_sum']));
        $fourth = (object)array('name' => $node->data[0]['agency_agency_agency_name'], 'data' => array($node->data[0]['check_amount_sum']));
        $all = array($empty, $fourth, $third, $second, $first, $empty);
        $node->widgetConfig->chartConfig->series = $all;
        break;
    case 6:
        $first = (object)array('name' => $node->data[4]['agency_agency_agency_name'], 'data' => array($node->data[4]['check_amount_sum']));
        $second = (object)array('name' => $node->data[3]['agency_agency_agency_name'], 'data' => array($node->data[3]['check_amount_sum']));
        $third = (object)array('name' => $node->data[2]['agency_agency_agency_name'], 'data' => array($node->data[2]['check_amount_sum']));
        $fourth = (object)array('name' => $node->data[1]['agency_agency_agency_name'], 'data' => array($node->data[1]['check_amount_sum']));
        $fifth = (object)array('name' => $node->data[0]['agency_agency_agency_name'], 'data' => array($node->data[0]['check_amount_sum']));
        $top5sum = $node->data[4]['check_amount_sum'] + $node->data[3]['check_amount_sum'] + $node->data[2]['check_amount_sum'] + $node->data[1]['check_amount_sum'] + $node->data[0]['check_amount_sum'];
        $difference = $node->data[5]['check_amount_sum'] - $top5sum;
        $others = (object)array('name' => 'All Others', 'data' => array($difference));
        $all = array($fifth, $fourth, $third, $second, $first, $others);
        $node->widgetConfig->chartConfig->series = $all;
        break;
}