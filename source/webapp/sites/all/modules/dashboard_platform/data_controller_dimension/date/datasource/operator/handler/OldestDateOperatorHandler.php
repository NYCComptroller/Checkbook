<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class OldestDateOperatorHandler extends AbstractOldestOperatorHandler {

    public static $OPERATOR__NAME = 'date:oldest';
}

class OldestMonthOperatorHandler extends AbstractOldestOperatorHandler {

    public static $OPERATOR__NAME = 'date:month:oldest';
}

class OldestQuarterOperatorHandler extends AbstractOldestOperatorHandler {

    public static $OPERATOR__NAME = 'date:quarter:oldest';
}

class OldestYearOperatorHandler extends AbstractOldestOperatorHandler {

    public static $OPERATOR__NAME = 'date:year:oldest';
}
