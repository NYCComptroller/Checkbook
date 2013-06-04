<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class DynamicDateRangeOperatorHandler extends AbstractDynamicRangeOperatorHandler {

    public static $OPERATOR__NAME = 'date:range.dynamic';
}

class DynamicMonthRangeOperatorHandler extends AbstractDynamicRangeOperatorHandler {

    public static $OPERATOR__NAME = 'date:month:range.dynamic';
}

class DynamicQuarterRangeOperatorHandler extends AbstractDynamicRangeOperatorHandler {

    public static $OPERATOR__NAME = 'date:quarter:range.dynamic';
}

class DynamicYearRangeOperatorHandler extends AbstractDynamicRangeOperatorHandler {

    public static $OPERATOR__NAME = 'date:year:range.dynamic';
}
