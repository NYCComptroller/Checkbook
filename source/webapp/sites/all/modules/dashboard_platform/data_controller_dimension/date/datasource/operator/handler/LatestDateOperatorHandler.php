<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class LatestDateOperatorHandler extends AbstractLatestOperatorHandler {

    public static $OPERATOR__NAME = 'date:latest';
}

class LatestMonthOperatorHandler extends AbstractLatestOperatorHandler {

    public static $OPERATOR__NAME = 'date:month:latest';
}

class LatestQuarterOperatorHandler extends AbstractLatestOperatorHandler {

    public static $OPERATOR__NAME = 'date:quarter:latest';
}

class LatestYearOperatorHandler extends AbstractLatestOperatorHandler {

    public static $OPERATOR__NAME = 'date:year:latest';
}
