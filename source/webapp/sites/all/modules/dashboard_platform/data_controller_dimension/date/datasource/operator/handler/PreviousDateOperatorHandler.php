<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class PreviousDateOperatorHandler extends AbstractPreviousOperatorHandler {

    public static $OPERATOR__NAME = 'date:previous';
}

class PreviousMonthOperatorHandler extends AbstractPreviousOperatorHandler {

    public static $OPERATOR__NAME = 'date:month:previous';
}

class PreviousQuarterOperatorHandler extends AbstractPreviousOperatorHandler {

    public static $OPERATOR__NAME = 'date:quarter:previous';
}

class PreviousYearOperatorHandler extends AbstractPreviousOperatorHandler {

    public static $OPERATOR__NAME = 'date:year:previous';
}
